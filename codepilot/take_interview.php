<?php 
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once "php/config/connect.php";

    $interviewId = $_GET['id'] ?? 0;
    
    $query = "SELECT i.*, l.title as lang_title, r.title as route_title 
              FROM interviews i 
              JOIN languages l ON i.language_id = l.id 
              JOIN routes r ON i.route_id = r.id
              WHERE i.id = ? AND i.user_id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ii", $interviewId, $_SESSION['user']['id']);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $interview = mysqli_fetch_assoc($result);

            if (!$interview) {
                header("Location: interview.php"); 
                exit;
            }

    $page_title = "Собеседование - " . $interview['lang_title'];
    $extra_css = "teststyle.css";
    require_once "components/header.php"; 
?>

<main class="container">
    <div class="test-wrapper">
        <div class="test-header">
            <div class="test-info">
                <span class="badge"><?php echo $interview['route_title']; ?></span>
                <span class="badge"><?php echo $interview['lang_title']; ?></span>
            </div>
            <div class="timer" id="test-timer">00:00</div>
        </div>
        
        <div id="error-box" class="error-message" style="display: none; margin-bottom: 20px;"></div>
        
        <div id="question-container" class="q-card main-card">
            <div class="loader">Загрузка вопроса...</div>
        </div>

        <div class="test-footer">
            <button class="nav-btn prev" id="prev-btn" onclick="prevQuestion()" disabled>← Назад</button>
            <div class="progress-info">
                Вопрос <span id="current-num">1</span> из <span id="total-num"><?php echo $interview['total_questions']; ?></span>
            </div>
            <button class="nav-btn next" id="next-btn" onclick="nextQuestion()">Далее →</button>
        </div>
    </div>
</main>

<script>
    const interviewId = <?php echo $interviewId; ?>;
    const totalQuestions = <?php echo $interview['total_questions']; ?>;
    let currentOrder = 1;
    let seconds = 0;

    function startTimer() {
        const timerElement = document.getElementById('test-timer');
        setInterval(() => {
            seconds++;
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            timerElement.innerText = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }, 1000);
    }

    function showError(text) {
        const errorBox = document.getElementById('error-box');
        errorBox.innerText = text;
        errorBox.style.display = 'block';
    }

    async function loadQuestion(order) {
        const container = document.getElementById('question-container');
        container.innerHTML = '<div class="loader">Загрузка вопроса...</div>';
        
        try {
            const response = await fetch(`php/auth/get_question.php?interview_id=${interviewId}&order=${order}`);
            const data = await response.json();

            if (!data.success) {
                container.innerHTML = `
                    <div style="text-align:center; padding: 50px;">
                        <h3 style="color:#D93025;">Ошибка</h3>
                        <p style="color:#8A94A6;">${data.message || 'Не удалось загрузить вопрос'}</p>
                        <a href="interview.php" style="color:#387FF5; text-decoration:none; font-weight:600;">Вернуться</a>
                    </div>`;
                return;
            }

            let diffText = data.difficulty == 1 ? 'Легко' : (data.difficulty == 2 ? 'Средне' : 'Сложно');
            let diffClass = `diff-${data.difficulty}`;

            container.innerHTML = `
                <div class="q-header">
                    <div class="q-title-row">
                        <h2>${data.question}</h2>
                        <span class="difficulty-badge ${diffClass}">${diffText}</span>
                    </div>
                    <p style="font-size:13px; color:#8A94A6;">
                        ${data.multiple ? 'Выберите один или несколько вариантов' : 'Выберите один вариант'}
                    </p>
                </div>
                <div class="answers-list">
                    ${data.answers.map(ans => `
                        <div class="answer-option ${data.multiple ? 'multi' : ''} ${data.selected.includes(Number(ans.id)) ? 'selected' : ''}" 
                             data-id="${ans.id}" 
                             onclick="selectOption(this, ${data.multiple})">
                            <div class="check-icon"></div>
                            <span>${ans.answer}</span>
                        </div>
                    `).join('')}
                </div>
            `;

            document.getElementById('current-num').innerText = order;
            document.getElementById('prev-btn').disabled = (order === 1);
            document.getElementById('next-btn').innerText = (order === totalQuestions) ? 'Завершить собеседование' : 'Далее →';
            currentOrder = order;
        } catch (e) {
            container.innerHTML = '<div class="loader" style="color:#D93025;">Ошибка связи с сервером</div>';
        }
    }

    function selectOption(el, isMultiple) {
        document.getElementById('error-box').style.display = 'none';
        if (!isMultiple) {
            el.parentElement.querySelectorAll('.answer-option').forEach(opt => opt.classList.remove('selected'));
        }
        el.classList.toggle('selected');
    }

    async function nextQuestion() {
        const selectedElements = document.querySelectorAll('.answer-option.selected');
        const selectedIds = Array.from(selectedElements).map(el => parseInt(el.dataset.id));

        if (selectedIds.length === 0) {
            showError("Пожалуйста, выберите хотя бы один вариант ответа");
            return;
        }

        const btn = document.getElementById('next-btn');
        btn.disabled = true;

        await saveAnswer(selectedIds);

        if (currentOrder < totalQuestions) {
            await loadQuestion(currentOrder + 1);
            btn.disabled = false;
        } else {
            finishInterview();
        }
    }

    async function saveAnswer(answerIds) {
        try {
            await fetch('php/auth/save_answer.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    interview_id: interviewId,
                    order: currentOrder,
                    answer_ids: answerIds
                }),
                credentials: "include"
            });
        } catch (e) { console.error(e); }
    }

    function prevQuestion() {
        if (currentOrder > 1) {
            loadQuestion(currentOrder - 1);
        }
    }

    function finishInterview() {
        window.location.href = `results.php?id=${interviewId}`;
    }

    startTimer();
    loadQuestion(currentOrder);
</script>

<?php require_once "components/footer.php"; ?>
<?php 
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once "php/config/connect.php";

    $lang_code = $_GET['id'] ?? '';

    $stmt = mysqli_prepare($conn, "SELECT id, title FROM languages WHERE code = ?");
    mysqli_stmt_bind_param($stmt, "s", $lang_code);
    mysqli_stmt_execute($stmt);
    $lang = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$lang) { header("Location: questions.php"); exit; }

    $query = "
        SELECT q.*, t.title as topic_title, t.id as t_id
        FROM questions q
        LEFT JOIN topics t ON q.topic_id = t.id
        WHERE q.language_id = ? AND q.is_active = 1
        ORDER BY t.id ASC, q.difficulty ASC
    ";
    $stmt_q = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt_q, "i", $lang['id']);
    mysqli_stmt_execute($stmt_q);
    $all_questions = mysqli_fetch_all(mysqli_stmt_get_result($stmt_q), MYSQLI_ASSOC);

    $grouped_questions = [];
    foreach ($all_questions as $q) {
        $t_name = $q['topic_title'] ?: 'Разное';
        $grouped_questions[$t_name][] = $q;
    }

    $page_title = "Вопросы по " . $lang['title'];
    $extra_css = "viewstyle.css";
    require_once "components/header.php"; 
?>

<main class="container">
    <div class="view-wrapper">
        
        <aside class="sidebar">
            <a href="questions.php" class="back-link">← Назад</a>
            <div class="sidebar-box">
                <h3>Темы</h3>
                <nav class="topic-nav" id="topic-menu">
                    <?php $i = 0; foreach ($grouped_questions as $t_name => $qs): ?>
                        <a href="javascript:void(0)" 
                           class="topic-link <?php echo ($i === 0) ? 'active' : ''; ?>" 
                           onclick="switchTopic(this, 'topic-<?php echo md5($t_name); ?>')">
                            <?php echo $t_name; ?> 
                            <span><?php echo count($qs); ?></span>
                        </a>
                    <?php $i++; endforeach; ?>
                </nav>
            </div>
        </aside>

        <div class="content-area">
            <header class="content-header">
                <div class="header-info">
                    <h1><?php echo $lang['title']; ?></h1>
                    <p>Выберите уровень сложности для фильтрации вопросов:</p>
                </div>
                
                <div class="difficulty-tabs">
                    <button class="diff-tab active" onclick="filterDifficulty(this, 'all')">Все</button>
                    <button class="diff-tab" onclick="filterDifficulty(this, '1')">Trainee</button>
                    <button class="diff-tab" onclick="filterDifficulty(this, '2')">Junior/Middle</button>
                    <button class="diff-tab" onclick="filterDifficulty(this, '3')">Senior</button>
                </div>
            </header>

            <div id="questions-viewport">
                <?php $i = 0; foreach ($grouped_questions as $t_name => $qs): ?>
                    <div class="topic-content <?php echo ($i === 0) ? 'active' : ''; ?>" id="topic-<?php echo md5($t_name); ?>">
                        <h2 class="topic-title"><?php echo $t_name; ?></h2>
                        <div class="questions-list">
                            <?php foreach ($qs as $q): ?>
                                <div class="q-card" data-difficulty="<?php echo $q['difficulty']; ?>">
                                    <div class="q-card__header" onclick="toggleAccordion(this)">
                                        <div class="q-main-info">
                                            <span class="difficulty-dot diff-<?php echo $q['difficulty']; ?>"></span>
                                            <h3><?php echo htmlspecialchars($q['question']); ?></h3>
                                        </div>
                                        <div class="q-icon">▼</div>
                                    </div>
                                    <div class="q-card__body">
                                        <div class="explanation-box">
                                            <strong>Разбор ответа:</strong>
                                            <p><?php echo nl2br(htmlspecialchars($q['explanation'])); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php $i++; endforeach; ?>
            </div>
        </div>

    </div>
</main>

<script>
function switchTopic(element, topicId) {
    document.querySelectorAll('.topic-link').forEach(link => link.classList.remove('active'));
    element.classList.add('active');

    document.querySelectorAll('.topic-content').forEach(content => {
        content.classList.remove('active');
    });
    const targetContent = document.getElementById(topicId);
    targetContent.classList.add('active');

    const allTab = document.querySelector('.diff-tab[onclick*="all"]');
    resetDifficultyFilter(targetContent, allTab);
    
    if (window.innerWidth < 1000) {
        targetContent.scrollIntoView({ behavior: 'smooth' });
    }
}

function filterDifficulty(element, diffValue) {
    document.querySelectorAll('.diff-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    element.classList.add('active');

    const activeTopic = document.querySelector('.topic-content.active');
    const cards = activeTopic.querySelectorAll('.q-card');

    cards.forEach(card => {
        const cardDiff = card.getAttribute('data-difficulty');
        
        if (diffValue === 'all' || cardDiff === String(diffValue)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function resetDifficultyFilter(topicElement, tabElement) {
    document.querySelectorAll('.diff-tab').forEach(tab => tab.classList.remove('active'));
    tabElement.classList.add('active');
    
    const cards = topicElement.querySelectorAll('.q-card');
    cards.forEach(card => card.style.display = 'block');
}

function toggleAccordion(header) {
    const card = header.parentElement;
    const body = header.nextElementSibling;

    if (card.classList.contains('open')) {
        body.style.maxHeight = null;
        card.classList.remove('open');
    } else {       
        body.style.maxHeight = body.scrollHeight + "px";
        card.classList.add('open');
    }
}
</script>

<?php require_once "components/footer.php"; ?>
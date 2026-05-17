<?php 
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once "php/config/connect.php"; 

    $counts_query = "
        SELECT l.code, COUNT(q.id) as total 
        FROM languages l 
        LEFT JOIN questions q ON l.id = q.language_id AND q.is_active = 1 
        GROUP BY l.id
    ";
    $counts_result = mysqli_query($conn, $counts_query);
    $q_counts = [];
    while ($row = mysqli_fetch_assoc($counts_result)) {
        $q_counts[$row['code']] = $row['total'];
    }

    function getCount($code, $q_counts) {
        return $q_counts[$code] ?? 0;
    }

    $page_title = "CodePilot - База вопросов";
    $extra_css = "questionsstyle.css";
    require_once "components/header.php"; 
?>

<main class="container">
  <div class="main">
    
    <section class="sectionOne">
        <div class="title_container">
            <p class="badge">
                <img src="images/favicon.svg" alt=""> 
                Идеальное место для повторения материала
            </p>
        </div>
        <div class="text">
            <h1>Вопросы с собеседований</h1>
            <h4>Изучай теорию, разбирай ответы и готовься к самым каверзным вопросам по технологиям.</h4>
        </div>
    </section>

    <section class="category-section">
        <h2 class="category-title"><img src="images/favicon.svg" alt=""> Backend</h2>
        <div class="cards-wrapper">
            <a href="language_view.php?id=python" class="card">
                <div class="card__bg" style="background-image: url('images/pythoncardbg.jpg');"></div>
                <div class="card__content">
                    <div class="card__header">
                        <img src="images/python.svg" alt="Python">
                        <h3>Python</h3>
                    </div>
                    <p>ООП, декораторы, многопоточность и работа с GIL. Всё для Backend-разработчика.</p>
                    <div class="card__footer">
                        <span class="q-badge"><?php echo getCount('python', $q_counts); ?> вопросов</span>
                    </div>
                </div>
            </a>

            <a href="language_view.php?id=cpp" class="card">
                <div class="card__bg" style="background-image: url('images/c++cardbg.jpg');"></div>
                <div class="card__content">
                    <div class="card__header">
                        <img src="images/c++.svg" alt="C++">
                        <h3>C++</h3>
                    </div>
                    <p>Управление памятью, шаблоны, STL и стандарты C++17/20.</p>
                    <div class="card__footer">
                        <span class="q-badge"><?php echo getCount('cpp', $q_counts); ?> вопросов</span>
                    </div>
                </div>
            </a>

            <a href="language_view.php?id=csharp" class="card">
                <div class="card__bg" style="background-image: url('images/сsharpcardbg.jpg');"></div>
                <div class="card__content">
                    <div class="card__header">
                        <img src="images/csharp.svg" alt="C#">
                        <h3>C#</h3>
                    </div>
                    <p>ООП, .NET, LINQ, асинхронность и разработка корпоративных приложений.</p>
                    <div class="card__footer">
                        <span class="q-badge"><?php echo getCount('csharp', $q_counts); ?> вопросов</span>
                    </div>
                </div>
            </a>
        </div>
    </section>

    <section class="category-section">
        <h2 class="category-title"><img src="images/favicon.svg" alt=""> Frontend</h2>
        <div class="cards-wrapper">
            <a href="language_view.php?id=js" class="card">
                <div class="card__bg" style="background-image: url('images/jscardbg.png');"></div>
                <div class="card__content">
                    <div class="card__header">
                        <img src="images/javascript.png" alt="JS">
                        <h3>JavaScript</h3>
                    </div>
                    <p>Замыкания, Event Loop, асинхронность и современные стандарты ES6+.</p>
                    <div class="card__footer">
                        <span class="q-badge"><?php echo getCount('js', $q_counts); ?> вопросов</span>
                    </div>
                </div>
            </a>

            <a href="language_view.php?id=html" class="card">
                <div class="card__bg" style="background-image: url('images/htmlcardbg.jpg');"></div>
                <div class="card__content">
                    <div class="card__header">
                        <img src="images/html.svg" alt="HTML">
                        <h3>HTML</h3>
                    </div>
                    <p>Семантика, структура документа, формы и основы доступности (A11y).</p>
                    <div class="card__footer">
                        <span class="q-badge"><?php echo getCount('html', $q_counts); ?> вопросов</span>
                    </div>
                </div>
            </a>

            <a href="language_view.php?id=css" class="card">
                <div class="card__bg" style="background-image: url('images/csscardbg.jpg');"></div>
                <div class="card__content">
                    <div class="card__header">
                        <img src="images/css.svg" alt="CSS">
                        <h3>CSS</h3>
                    </div>
                    <p>Flexbox, Grid, анимации, препроцессоры и методологии верстки.</p>
                    <div class="card__footer">
                        <span class="q-badge"><?php echo getCount('css', $q_counts); ?> вопросов</span>
                    </div>
                </div>
            </a>
        </div>
    </section>

    <section class="category-section">
        <h2 class="category-title"><img src="images/favicon.svg" alt=""> Database</h2>
        <div class="cards-wrapper">
            <a href="language_view.php?id=sql" class="card">
                <div class="card__bg" style="background-image: url('images/sqlcardbg.jpg');"></div>
                <div class="card__content">
                    <div class="card__header">
                        <img src="images/sql.png" alt="SQL">
                        <h3>SQL</h3>
                    </div>
                    <p>Проектирование БД, JOIN-ы, индексы, транзакции и оптимизация запросов.</p>
                    <div class="card__footer">
                        <span class="q-badge"><?php echo getCount('sql', $q_counts); ?> вопросов</span>
                    </div>
                </div>
            </a>
        </div>
    </section>

  </div>
</main>

<?php require_once "components/footer.php"; ?>
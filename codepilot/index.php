<?php 
    $page_title = "CodePilot - Главная";
    $extra_css = "homepagestyle.css";
    require_once "components/header.php"; 
?>

<main class="container">
  <div class="main">
    <section class="sectionOne">
        <div class="title_container">
            <p class="badge">
                <img src="images/favicon.svg" alt="">
                Лучшее место для подготовки к собеседованиям
            </p>
        </div>

        <div class="text">
            <h1>Готовься к собеседованию с CodePilot</h1>
            <h4>Подготовка к собесу - самое <b>верное</b> решение для получения оффера</h4>
        </div>
    </section>

    <section class="sectionTwo">
        <h2><img src="images/favicon.svg" alt="">Выбери свой путь</h2>
        
        <div class="cards-wrapper">
            <a href="coding.php" class="card small">
                <div class="card__bg" style="background-image: url('images/card1.png');"></div>
                <div class="card__content">
                    <h3>Решать задачи</h3>
                    <p>Решай задачи на своем языке программирования. Проверяй и тренируй свои навыки!</p>
                    <span class="card__btn">Подробнее →</span>
                </div>
            </a>

            <a href="interview.php" class="card featured">
                <div class="card__bg" style="background-image: url('images/card2.png');"></div>
                <div class="card__content">
                    <h3>Прохождение собеседования</h3>
                    <p>Проверь свои знания с нашим агентом и пойми, готов ли ты получить оффер!</p>
                    <span class="card__btn">Начать →</span>
                </div>
            </a>

            <a href="questions.php" class="card small">
                <div class="card__bg" style="background-image: url('images/card3.png');"></div>
                <div class="card__content">
                    <h3>Вопросы на собеседованиях</h3>
                    <p>Изучи самые частые вопросы на собеседовании. И будь всегда готов!</p>
                    <span class="card__btn">Смотреть →</span>
                </div>
            </a>
        </div>
    </section>

    <section class="reviews">
        <div class="container">
            <div class="reviews__header">
                <h2>Опыт пользователей CodePilot</h2>
                <p>Мы гордимся тем, что помогаем разработчикам получать офферы в топовые компании мира.</p>
            </div>

            <div class="reviews__grid">
                <div class="review-card">
                    <p class="review-text">«Подготовка к собеседованию стала намного проще. Задачи структурированы, а разборы помогают понять саму суть решения.»</p>
                    <div class="review-author">
                        <img src="images/reviewsavatar.png" alt="Avatar">
                        <div>
                            <strong>Артем Иванов</strong>
                            <span>Frontend Developer</span>
                        </div>
                    </div>
                </div>

                <div class="review-card">
                    <p class="review-text">«Симуляция собеседования - это киллер-фича. Перестала нервничать на реальных звонках и получила оффер через 2 недели.»</p>
                    <div class="review-author">
                        <img src="images/reviewsavatar.png" alt="Avatar">
                        <div>
                            <strong>Надежда Кузнецова</strong>
                            <span>Product Manager</span>
                        </div>
                    </div>
                </div>

                <div class="review-card">
                    <p class="review-text">«Лучшая база вопросов. Всё в одном месте, не нужно гуглить десятки статей перед интервью.»</p>
                    <div class="review-author">
                        <img src="images/reviewsavatar.png" alt="Avatar">
                        <div>
                            <strong>Михаил Петров</strong>
                            <span>Backend Engineer</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="faq">
        <div class="container faq__wrapper">
            <div class="faq__info">
                <span class="badge">FAQ</span>
                <h2>Часто задаваемые вопросы</h2>
                <p>Узнайте больше о том, как CodePilot помогает в подготовке и возможностях платформы.</p>
            </div>

            <div class="faq__list">
                <details class="faq__item" open>
                    <summary class="faq__question">Что такое CodePilot и чем он отличается от других?</summary>
                    <div class="faq__answer">
                        CodePilot — это комплексная платформа. В отличие от простого решения задач, мы предлагаем симуляцию интервью, персонализированные планы подготовки и базу вопросов от реальных интервьюеров.
                    </div>
                </details>

                <details class="faq__item">
                    <summary class="faq__question">Как работает симуляция собеседования?</summary>
                    <div class="faq__answer">
                        Наш агент задает вопросы, слушает ваши ответы и анализирует код, после чего выдает детальный фидбек по вашим Soft и Hard skills.
                    </div>
                </details>

                <details class="faq__item">
                    <summary class="faq__question">Нужно ли платить за использование платформы?</summary>
                    <div class="faq__answer">
                        Нет не нужно, наш сайт полностью бесплатный. Мы стараемся ради вас, чтобы вы, как можно быстрее получили долгожданный оффер.
                    </div>
                </details>

                <details class="faq__item">
                    <summary class="faq__question">Есть ли поддержка разных языков программирования?</summary>
                    <div class="faq__answer">
                        Да, мы поддерживаем JavaScript, Python, C++ и многие другие популярные языки.
                    </div>
                </details>
            </div>
        </div>
    </section>
</div>
</main>


<?php 
    require_once "components/footer.php"; 
?>
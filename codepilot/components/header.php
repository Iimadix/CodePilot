<!DOCTYPE html>
<html lang="ru">
<head>
    <base href="http://localhost/codepilot/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'CodePilot'; ?></title>
    
    <link rel="stylesheet" href="style.css">

    
    <?php if (isset($extra_css)): ?>
        <link rel="stylesheet" href="<?php echo $extra_css; ?>">
    <?php endif; ?>
    
    <link rel="icon" href="images/favicon.svg" type="image/x-icon">
</head>
<body>
    <header class="header">
        <nav class="header__nav">
            <a href="index.php" class="logo">
                <img src="images/logo.svg" alt="лого">
            </a>

            <ul class="nav-links">
                <li><a href="coding.php">Кодинг</a></li>
                <li><a href="interview.php">Собеседование</a></li>
                <li><a href="questions.php">Вопросы собеседований</a></li>
            </ul>

            <div class="header-right">
                <div class="header_images" id="user-section"></div>

                <button class="burger" id="burger-btn">
                    <span></span><span></span><span></span>
                </button>
            </div>

            <div class="mobile-menu" id="mobile-menu">
                <a href="coding.php">Кодинг</a>
                <a href="interview.php">Собеседование</a>
                <a href="questions.php">Вопросы собеседований</a>
            </div>
        </nav>
    </header>
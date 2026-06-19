<?php
session_start();
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodePilot - Авторизация</title>

    <link rel="stylesheet" href="authstyle.css">
    <link rel="icon" href="images/favicon.svg" type="image/x-icon">
</head>
<body>

<div class="auth-container">

    <div class="auth-logo">
        <a href="index.php">
            <img src="images/logo.svg" alt="CodePilot Logo">
        </a>
    </div>

    <div class="auth-card">

    <div id="error-box" class="error-message"></div>

    <div id="sign-up-form" class="form-section" style="display: none;">
        <h1>Создайте аккаунт</h1>
        <p class="subtitle">Придумайте уникальный логин для профиля</p>

        <form onsubmit="handleSignUp(event)" novalidate>
            <div class="input-group">
                <input type="text" id="reg-name" required placeholder=" " maxlength="20">
                <label>Логин</label>
            </div>
            <div class="input-group">
                <input type="email" id="reg-email" required placeholder=" ">
                <label>Почта</label>
            </div>
            <div class="input-group">
                <input type="password" id="reg-pass" required placeholder=" ">
                <label>Пароль</label>
            </div>
            <button type="submit" class="auth-btn">Создать аккаунт</button>
        </form>
        <p class="toggle-text">Есть аккаунт? <a href="#" onclick="toggleForms()">Войти</a></p>
    </div>

    <div id="sign-in-form" class="form-section">
        <h1>С возвращением!</h1>
        <p class="subtitle">Рады видеть вас снова</p>

        <form onsubmit="handleSignIn(event)" novalidate>
            <div class="input-group">
                <input type="text" id="login-email" required placeholder=" ">
                <label>Почта или логин</label>
            </div>
            <div class="input-group">
                <input type="password" id="login-pass" required placeholder=" ">
                <label>Пароль</label>
            </div>
            <button type="submit" class="auth-btn">Войти</button>
        </form>
        <p class="toggle-text">Впервые у нас? <a href="#" onclick="toggleForms()">Регистрация</a></p>
    </div>
</div>
</div>

<script>

    document.querySelectorAll('input').forEach(input => {
    input.addEventListener('input', () => {
        document.getElementById('error-box').style.display = 'none';
    });
    });

    function toggleForms() {
        const signUp = document.getElementById('sign-up-form');
        const signIn = document.getElementById('sign-in-form');
        const errorBox = document.getElementById('error-box');
        errorBox.style.display = 'none';

        if (signUp.style.display === 'none') {
            signUp.style.display = 'block';
            signIn.style.display = 'none';
        } else {
            signUp.style.display = 'none';
            signIn.style.display = 'block';
        }
    }

    function showError(text) {
        const errorBox = document.getElementById('error-box');
        errorBox.innerText = text;
        errorBox.style.display = 'block';
        errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    async function handleSignUp(event) {
        event.preventDefault();
        const name = document.getElementById('reg-name').value.trim();
        const email = document.getElementById('reg-email').value.trim();
        const pass = document.getElementById('reg-pass').value;

        if (!name) return showError("Придумайте логин");
        
        if (!/^[a-zA-Z0-9]+$/.test(name)) {
            return showError("Логин может содержать только английские буквы и цифры");
        }

        if (name.length < 3 || name.length > 20) {
            return showError("Логин должен быть от 3 до 20 символов");
        }
        
        if (!email || !email.includes('@')) return showError("Введите корректную почту");
        if (pass.length < 6) return showError("Пароль должен быть не менее 6 символов");

        try {
            const response = await fetch("php/auth/register.php", {
                method: "POST",
                credentials: "include",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ name, email, password: pass })
            });
            const data = await response.json();
            if (data.success) {
                window.location.href = "index.php";
            } else {
                showError(data.message);
            }
        } catch(error) { showError("Ошибка сервера"); }
    }

    async function handleSignIn(event) {
        event.preventDefault();
        const email = document.getElementById('login-email').value.trim();
        const pass = document.getElementById('login-pass').value;

        if (!email) return showError("Введите почту или логин");
        if (!pass) return showError("Введите пароль");

        try {
            const response = await fetch("php/auth/login.php", {
                method: "POST",
                credentials: "include",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ email, password: pass })
            });
            const data = await response.json();
            if (data.success) {
                window.location.href = "index.php";
            } else {
                showError(data.message);
            }
        } catch(error) { showError("Ошибка сервера"); }
    }
</script>
</body>
</html>
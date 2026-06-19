<?php 
    $page_title = "CodePilot - Настройки";
    $extra_css = "profilestyle.css";
    require_once "components/header.php"; 
?>

<main class="container">
    <div class="profile-wrapper">
        <aside class="profile-side card">
            <div class="avatar-block">
                <img id="p-avatar" src="images/avatars/1.png" alt="Avatar">
            </div>
            <h2 id="p-name">Настройки</h2>
            <p style="font-size: 14px; color: #666; margin-bottom: 20px;">Редактирование профиля</p>
            
            <a href="profile.php" class="save-btn" style="background: #EEF4FF; color: #387FF5; text-decoration: none; display: block; width: 100%; margin-bottom: 12px; text-align: center;">
                Мой профиль
            </a>
            
            <button class="logout-btn-alt" onclick="globalLogout()" style="margin-top: 0;">Выйти</button>
        </aside>

        <div class="profile-content">
            <div id="error-box" class="error-message" style="display: none; margin-bottom: 20px;"></div>

            <section class="profile-card card">
                <div class="card-header">
                    <img src="images/favicon.svg" alt="">
                    <h3>Основные данные</h3>
                </div>
                
                <form autocomplete="off" onsubmit="return false;">
                    <input autocomplete="false" name="hidden" type="text" style="display:none;">
                    
                    <div class="info-grid">
                        <div class="info-field">
                            <span>Отображаемое имя (Никнейм)</span>
                            <input type="text" id="p-nickname-input" class="profile-input" autocomplete="off" maxlength="20">
                        </div>
                        <div class="info-field">
                            <span>Логин (уникальный адрес профиля)</span>
                            <input type="text" id="p-login-input" class="profile-input disabled-input" autocomplete="off" disabled>
                        </div>
                    </div>

                    <div class="info-grid" style="margin-top:20px;">
                        <div class="info-field">
                            <span>Ваш уровень</span>
                            <select id="p-level-input" class="profile-input">
                                <option value="Стажер">Стажер</option>
                                <option value="Junior">Junior</option>
                                <option value="Middle">Middle</option>
                                <option value="Senior">Senior</option>
                                <option value="Lead">Lead</option>
                            </select>
                        </div>
                        <div class="info-field">
                            <span>Стек технологий (язык)</span>
                            <input type="text" id="p-tech-input" class="profile-input" placeholder="Например: Python, JS, C++" autocomplete="off" maxlength="50">
                        </div>
                    </div>

                    <div class="info-grid" style="margin-top:20px;">
                        <div class="info-field">
                            <span>Страна</span>
                            <select id="p-country-input" class="profile-input">
                                <option value="">Не указано</option>
                                <option value="Россия">Россия</option>
                                <option value="Казахстан">Казахстан</option>
                                <option value="Беларусь">Беларусь</option>
                                <option value="Узбекистан">Узбекистан</option>
                                <option value="Армения">Армения</option>
                                <option value="Грузия">Грузия</option>
                            </select>
                        </div>
                        <div class="info-field">
                            <span>Город</span>
                            <input type="text" id="p-city-input" class="profile-input" autocomplete="off">
                        </div>
                    </div>

                    <div class="info-field" style="margin-top:20px;">
                        <span>О себе</span>
                        <textarea id="p-bio-input" class="profile-input" style="height: 120px; resize: none;" placeholder="Расскажите о своем опыте..."></textarea>
                    </div>
                </form>

                <div style="margin-top: 30px;">
                    <button class="save-btn" onclick="saveSettings()" id="save-btn">Сохранить изменения</button>
                </div>
            </section>

            <section class="profile-card card" style="margin-top: 24px;">
                <div class="card-header">
                    <img src="images/favicon.svg" alt="">
                    <h3>Безопасность</h3>
                </div>
                
                <div class="info-grid">
                    <div class="info-field">
                        <span>Текущий пароль</span>
                        <input type="password" id="old-pass" class="profile-input" placeholder="••••••••">
                    </div>
                    <div class="info-field">
                        <span>Новый пароль</span>
                        <input type="password" id="new-pass" class="profile-input" placeholder="Минимум 6 символов">
                    </div>
                </div>

                <div style="margin-top: 30px;">
                    <button class="save-btn" style="background: #4B5162;" onclick="changePassword()" id="pass-btn">Обновить пароль</button>
                </div>
            </section>
        </div>
    </div>
</main>

<script>
function showError(text) {
    const errorBox = document.getElementById('error-box');
    errorBox.innerText = text;
    errorBox.style.display = 'block';
    errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

document.querySelectorAll('.profile-input').forEach(el => {
    el.addEventListener('input', () => {
        document.getElementById('error-box').style.display = 'none';
    });
});

async function loadSettings() {
    const response = await fetch("php/auth/check_auth.php", { credentials: "include" });
    const data = await response.json();
    if (!data.success) { window.location.href = "auth.php"; return; }

    const user = data.user;
    document.getElementById("p-avatar").src = `images/avatars/${user.image_id}.png`;
    
    document.getElementById("p-nickname-input").value = user.nickname || "";
    document.getElementById("p-login-input").value = user.login || "";
    document.getElementById("p-level-input").value = user.level || "Стажер";

    const techStack = user.tech_stack;
    document.getElementById("p-tech-input").value = (techStack === "Не указано") ? "" : techStack;

    document.getElementById("p-country-input").value = user.country || "";
    document.getElementById("p-city-input").value = user.city || "";
    document.getElementById("p-bio-input").value = user.bio || "";
}

async function saveSettings() {
    const btn = document.getElementById('save-btn');
    const nicknameInput = document.getElementById("p-nickname-input").value.trim();

    if (nicknameInput.length < 3 || nicknameInput.length > 20) {
        showError("Никнейм должен быть от 3 до 20 символов");
        return;
    }

    if (!/^[a-zA-Z0-9а-яА-ЯёЁ]+$/.test(nicknameInput)) {
        showError("Никнейм может содержать только буквы и цифры без пробелов");
        return;
    }

    const payload = {
        nickname: nicknameInput,
        level: document.getElementById("p-level-input").value,
        tech_stack: document.getElementById("p-tech-input").value,
        country: document.getElementById("p-country-input").value,
        city: document.getElementById("p-city-input").value,
        bio: document.getElementById("p-bio-input").value
    };

    btn.innerText = "Сохранение...";
    btn.disabled = true;
    
    try {
        const response = await fetch("php/auth/update_profile.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload),
            credentials: "include"
        });
        const result = await response.json();
        if (result.success) {
            btn.innerText = "Успешно сохранено!";
            btn.style.background = "#28a745";
            setTimeout(() => location.reload(), 1000);
        } else {
            showError(result.message);
            btn.innerText = "Сохранить изменения";
            btn.style.background = "";
            btn.disabled = false;
        }
    } catch (e) {
        showError("Ошибка сети.");
        btn.innerText = "Сохранить изменения";
        btn.style.background = "";
        btn.disabled = false;
    }
}


async function changePassword() {
    const oldPass = document.getElementById('old-pass').value;
    const newPass = document.getElementById('new-pass').value;
    const btn = document.getElementById('pass-btn');

    if(!oldPass || !newPass) {
        showError("Заполните оба поля пароля");
        return;
    }

    if(newPass.length < 6) {
        showError("Новый пароль должен быть не менее 6 символов");
        return;
    }

    btn.innerText = "Обновление...";
    btn.disabled = true;
    
    try {
        const response = await fetch("php/auth/change_password.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ oldPass, newPass }),
            credentials: "include"
        });

        const result = await response.json();
        if(result.success) {
            btn.innerText = "Пароль изменен!";
            btn.style.background = "#28a745";
            
            document.getElementById('old-pass').value = "";
            document.getElementById('new-pass').value = "";
            
            setTimeout(() => {
                location.reload();
            }, 1500);

        } else {
            showError(result.message);
            btn.innerText = "Обновить пароль";
            btn.disabled = false;
        }
    } catch(e) {
        showError("Ошибка сети.");
        btn.innerText = "Обновить пароль";
        btn.disabled = false;
    }
}

loadSettings();
</script>


<?php 
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once "php/config/connect.php";
    $page_title = "CodePilot - Профиль";
    $extra_css = "profilestyle.css";
    require_once "components/header.php"; 
?>

<main class="container">
    <div class="profile-wrapper">
        <aside class="profile-side card">
            <div class="avatar-block">
                <img id="p-avatar" src="images/avatars/1.png" alt="Avatar">
            </div>
            <h2 id="p-name">Загрузка...</h2>
            
            <div class="role-container">
                <div id="p-level" class="level-badge">Загрузка...</div>
                <div id="p-tech-list" class="tech-list">
                </div>
            </div>
            
            <button id="logout-btn" class="logout-btn-alt" onclick="globalLogout()" style="display: none;">
                Выйти из аккаунта
            </button>
        </aside>

        <div class="profile-content">
            <section class="profile-card card">
                <div class="card-header">
                    <img src="images/favicon.svg" alt="">
                    <h3>Личная информация</h3>
                </div>
                
                <div class="info-grid">
                    <div class="info-field">
                        <span>Логин</span>
                        <p id="p-login-val">-</p>
                    </div>
                    <div class="info-field">
                        <span>Email</span>
                        <p id="p-email-val">-</p>
                    </div>
                </div>

                <div class="info-grid" style="margin-top:20px;">
                    <div class="info-field">
                        <span>Страна</span>
                        <p id="p-country-val">Не указано</p>
                    </div>
                    <div class="info-field">
                        <span>Город</span>
                        <p id="p-city-val">Не указано</p>
                    </div>
                </div>
            </section>

            <section class="profile-card card">
                <div class="card-header">
                    <img src="images/favicon.svg" alt="">
                    <h3>О себе</h3>
                </div>
                <p id="p-bio" style="color: #4B5162; line-height: 1.8; white-space: pre-line;">Загрузка...</p>
            </section>

            <section class="profile-card card">
                <div class="card-header">
                    <img src="images/favicon.svg" alt="">
                    <h3>Прогресс в собеседованиях</h3>
                </div>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-info">
                            <span class="stat-num" id="stat-interviews">0</span>
                            <span class="stat-lab">Собеседований</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-info">
                            <span class="stat-num" id="stat-score">0%</span>
                            <span class="stat-lab">Успешность</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-info">
                            <span class="stat-num" id="stat-questions">0</span>
                            <span class="stat-lab">Верных ответов</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-info">
                            <span class="stat-num" id="stat-fav-lang">-</span>
                            <span class="stat-lab">Лучший стек</span>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>

<script>
async function loadProfile() {
    const urlParams = new URLSearchParams(window.location.search);
    const userLogin = urlParams.get('u');
    
    const apiUrl = userLogin 
        ? `php/auth/get_user.php?login=${userLogin}` 
        : "php/auth/check_auth.php";

    try {
        const response = await fetch(apiUrl, { credentials: "include" });
        const data = await response.json();

        if (!data.success) {
            window.location.href = userLogin ? "index.php" : "auth.php";
            return;
        }

        const user = data.user;
        const isMine = data.is_mine ?? true;

        document.getElementById("p-avatar").src = `images/avatars/${user.image_id}.png`;
        document.getElementById("p-name").innerText = user.nickname || user.login;
        document.getElementById("p-level").innerText = user.level || "Стажер";
        
        const techContainer = document.getElementById("p-tech-list");
        techContainer.innerHTML = "";
        if (user.tech_stack && user.tech_stack.trim() !== "" && user.tech_stack !== "Не указано") {
            const tags = user.tech_stack.split(/[\s,;]+/); 
            tags.forEach(tag => {
                if (tag.trim() !== "") {
                    const span = document.createElement("span");
                    span.className = "tech-tag";
                    span.innerText = tag;
                    techContainer.appendChild(span);
                }
            });
        } else {
            const span = document.createElement("span");
            span.className = "tech-tag";
            span.innerText = "Разработчик";
            techContainer.appendChild(span);
        }

        document.getElementById("p-bio").innerText = user.bio || "Пользователь еще ничего не рассказал о себе.";
        document.getElementById("p-login-val").innerText = "@" + user.login;
        document.getElementById("p-email-val").innerText = isMine ? user.email : "••••@••••.••";
        document.getElementById("p-country-val").innerText = user.country || "Не указано";
        document.getElementById("p-city-val").innerText = user.city || "Не указано";

        document.getElementById("stat-interviews").innerText = user.interview_count || 0;
        document.getElementById("stat-score").innerText = Math.round(user.avg_score || 0) + "%";
        document.getElementById("stat-questions").innerText = user.total_correct || 0;
        document.getElementById("stat-fav-lang").innerText = user.fav_lang || "Нет данных";

        if (isMine) {
            document.getElementById("logout-btn").style.display = "block";
        }

    } catch (error) {
        console.error("Ошибка загрузки профиля:", error);
    }
}
loadProfile();
</script>

<?php require_once "components/footer.php"; ?>
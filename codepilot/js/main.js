async function checkUser() {
    try {
        const response = await fetch("php/auth/check_auth.php", {
            credentials: "include",
            cache: "no-store" 
        });

        const data = await response.json();
        const userSection = document.getElementById("user-section");

        if (!data.success) {
            userSection.innerHTML = `
                <a href="auth.php" style="text-decoration:none;color:#387FF5;font-weight:600;margin-right:15px;">
                    Войти
                </a>
            `;
            return;
        }

        const user = data.user;

        const displayName = user.nickname || user.login;
        
        userSection.innerHTML = `
            <div class="user-menu-wrapper" id="user-menu-trigger">
                <!-- Вместо ${user.login} пишем ${displayName} -->
                <span style="font-weight:600;color:#4B5162;">${displayName}</span>
                <img src="images/avatars/${user.image_id}.png" 
                     style="width:38px;height:38px;border-radius:50%;border:2px solid #387FF5;object-fit:cover;">
                
                <div class="user-dropdown" id="user-dropdown">
                    <a href="profile.php?u=${user.login}">Мой профиль</a>
                    <a href="settings.php">Настройки</a> 
                    <hr>
                    <button class="logout-item" onclick="globalLogout()">Выйти с аккаунта</button>
                </div>
            </div>
        `;

        const trigger = document.getElementById('user-menu-trigger');
        const dropdown = document.getElementById('user-dropdown');

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('active');
        });

    } catch(error) {
        console.log("Ошибка авторизации:", error);
    }
}

async function globalLogout() {
    try {
        const response = await fetch("php/auth/logout.php", {
            method: "POST",
            credentials: "include"
        });
        const data = await response.json();
        if (data.success) {
            window.location.href = "index.php";
        }
    } catch(e) { console.log(e); }
}

const burgerBtn = document.getElementById("burger-btn");
const mobileMenu = document.getElementById("mobile-menu");

if(burgerBtn) {
    burgerBtn.addEventListener("click", () => {
        mobileMenu.classList.toggle("active");
    });
}

document.addEventListener("click", (e) => {
    const dropdown = document.getElementById('user-dropdown');
    if (dropdown && !e.target.closest('#user-menu-trigger')) {
        dropdown.classList.remove('active');
    }
    
    if (mobileMenu && !burgerBtn.contains(e.target) && !mobileMenu.contains(e.target)) {
        mobileMenu.classList.remove("active");
    }
});

checkUser();
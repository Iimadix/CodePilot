let selection = {
    route_id: null,
    language_id: null,
    level_id: null
};

let interviewIdToDelete = null;

document.addEventListener('click', function(e) {
    const card = e.target.closest('.setup-card');
    if (!card) return;

    const type = card.dataset.type;
    const id = card.dataset.id;

    if (type === 'route') {
        handleRouteSelection(card, id);
    } 
    else if (type === 'language') {
        handleLanguageSelection(card, id);
    } 
    else if (type === 'level') {
        handleLevelSelection(card, id);
    }
});

function handleRouteSelection(card, id) {
    const container = document.querySelector('#step-1 .setup-cards');
    container.querySelectorAll('.setup-card').forEach(c => c.classList.remove('selected'));
    container.classList.add('has-selected');
    card.classList.add('selected');

    selection.route_id = id;
    selection.language_id = null;
    selection.level_id = null;

    document.getElementById('step-2').classList.add('hidden');
    document.getElementById('step-3').classList.add('hidden');
    document.getElementById('start-block').classList.add('hidden');

    loadLanguages(id);
}

function handleLanguageSelection(card, id) {
    const container = document.querySelector('#step-2 .setup-cards');
    container.querySelectorAll('.setup-card').forEach(c => c.classList.remove('selected'));
    container.classList.add('has-selected');
    card.classList.add('selected');

    selection.language_id = id;
    selection.level_id = null;

    document.getElementById('start-block').classList.add('hidden');
    
    const levelStep = document.getElementById('step-3');
    const levelCardsContainer = levelStep.querySelector('.setup-cards');
    levelCardsContainer.classList.remove('has-selected');
    levelCardsContainer.querySelectorAll('.setup-card').forEach(c => c.classList.remove('selected'));

    levelStep.classList.remove('hidden');
}

function handleLevelSelection(card, id) {
    const container = document.querySelector('#step-3 .setup-cards');
    container.querySelectorAll('.setup-card').forEach(c => c.classList.remove('selected'));
    container.classList.add('has-selected');
    card.classList.add('selected');

    selection.level_id = id;
    document.getElementById('start-block').classList.remove('hidden');
}

async function loadLanguages(routeId) {
    const listContainer = document.getElementById('language-list');
    const step2 = document.getElementById('step-2');
    listContainer.classList.add('loading');

    try {
        const response = await fetch(`php/auth/get_languages_by_route.php?route_id=${routeId}`);
        const languages = await response.json();
        
        listContainer.innerHTML = ''; 
        document.querySelector('#step-2 .setup-cards').classList.remove('has-selected');

        languages.forEach(lang => {
            listContainer.innerHTML += `
                <div class="setup-card" data-type="language" data-id="${lang.id}">
                    <div class="card-icon">💻</div>
                    <h3>${lang.title}</h3>
                </div>
            `;
        });
        
        listContainer.classList.remove('loading');
        step2.classList.remove('hidden');
        
    } catch (e) { 
        console.error("Ошибка загрузки языков:", e);
        listContainer.classList.remove('loading');
    }
}

function startInterview() {
    const errorBox = document.getElementById('setup-error-box');
    
    if (!selection.route_id || !selection.language_id || !selection.level_id) {
        errorBox.innerText = "Пожалуйста, выберите все параметры";
        errorBox.style.display = 'block';
        errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }
    
    errorBox.style.display = 'none';
    window.location.href = `process_start.php?r=${selection.route_id}&l=${selection.language_id}&lv=${selection.level_id}`;
}

function deleteInterview(id) {
    interviewIdToDelete = id;
    const modal = document.getElementById('delete-modal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeDeleteModal() {
    const modal = document.getElementById('delete-modal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
    interviewIdToDelete = null;
}

const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
if (confirmDeleteBtn) {
    confirmDeleteBtn.addEventListener('click', async function() {
        if (!interviewIdToDelete) return;

        this.innerText = "Удаление...";
        this.disabled = true;

        try {
            const response = await fetch(`php/auth/delete_interview.php?id=${interviewIdToDelete}`, {
                method: "GET",
                credentials: "include"
            });
            const data = await response.json();
            
            if (data.success) {
                location.reload();
            } else {
                alert("Ошибка: " + data.message);
                closeDeleteModal();
                this.innerText = "Да, удалить";
                this.disabled = false;
            }
        } catch (e) {
            console.error("Ошибка сети:", e);
            closeDeleteModal();
            this.innerText = "Да, удалить";
            this.disabled = false;
        }
    });
}

const deleteModalOverlay = document.getElementById('delete-modal');
if (deleteModalOverlay) {
    deleteModalOverlay.addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
}
<?php 
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once "php/config/connect.php";
    
    $routes = mysqli_query($conn, "SELECT * FROM routes");
    $levels = mysqli_query($conn, "SELECT * FROM experience_levels ORDER BY level_value ASC");

    $page_title = "CodePilot - Тренажер";
    $extra_css = "interviewstyle.css";
    require_once "components/header.php"; 
?>

<main class="container">
    <div class="main">
        
        <section class="setup-section">
            <div class="text-center">
                <h1>Тренажер собеседований</h1>
                <p>Настрой параметры и проверь свои знания</p>
            </div>

            <div class="step-container" id="step-1">
                <h2 class="step-title">1. Выбери направление</h2>
                <div class="setup-cards">
                    <?php while($r = mysqli_fetch_assoc($routes)): ?>
                        <div class="setup-card" data-type="route" data-id="<?php echo $r['id']; ?>">
                            <div class="card-icon">🚀</div>
                            <h3><?php echo $r['title']; ?></h3>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="step-container hidden" id="step-2">
                <h2 class="step-title">2. Выбери язык программирования</h2>
                <div class="setup-cards" id="language-list">
                </div>
            </div>

            <div class="step-container hidden" id="step-3">
                <h2 class="step-title">3. Выбери свой уровень</h2>
                <div class="setup-cards">
                    <?php while($l = mysqli_fetch_assoc($levels)): ?>
                        <div class="setup-card" data-type="level" data-id="<?php echo $l['id']; ?>">
                            <div class="card-icon">📊</div>
                            <h3><?php echo $l['title']; ?></h3>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="start-container hidden" id="start-block">
                <div id="setup-error-box" class="error-message" style="display: none; margin-bottom: 20px;"></div>
                <button class="start-btn" onclick="startInterview()">Начать собеседование</button>
            </div>
        </section>
        
        <?php 
        $userId = $_SESSION['user']['id'] ?? 0;
        $historyCount = 0;
        if ($userId > 0) {
            $historyQuery = "SELECT i.*, l.title as lang, r.title as route, el.title as level 
                            FROM interviews i 
                            JOIN languages l ON i.language_id = l.id 
                            JOIN routes r ON i.route_id = r.id
                            JOIN experience_levels el ON i.experience_level_id = el.id
                            WHERE i.user_id = $userId 
                            ORDER BY i.created_at DESC LIMIT 10";
            $history = mysqli_query($conn, $historyQuery);
            $historyCount = mysqli_num_rows($history);
        }
        ?>

        <section class="history-section">
            <div class="section-header">
                <h3>История собеседований</h3>
                <span class="count-badge"><?php echo $historyCount; ?> / 10</span>
            </div>

            <?php if (isset($_GET['error']) && $_GET['error'] === 'limit'): ?>
                <div class="error-message" style="margin-bottom: 30px;">
                    Вы достигли лимита в 10 собеседований. Удалите старые, чтобы начать новое.
                </div>
            <?php endif; ?>

            <div class="history-list">
                <?php if ($historyCount > 0): ?>
                    <?php while($item = mysqli_fetch_assoc($history)): ?>
                        <div class="history-item">
                            <div class="history-info">
                                <div class="history-main">
                                    <strong><?php echo $item['lang']; ?></strong>
                                    <span><?php echo $item['route']; ?> • <?php echo $item['level']; ?></span>
                                </div>
                                <div class="history-date">
                                    <?php echo date('d.m.Y', strtotime($item['created_at'])); ?>
                                </div>
                            </div>

                            <div class="history-status">
                                <?php if ($item['status'] === 'completed'): ?>
                                    <span class="status-done"><?php echo round($item['score']); ?>%</span>
                                <?php else: ?>
                                    <span class="status-process">В процессе</span>
                                <?php endif; ?>
                            </div>

                            <div class="history-actions">
                                <?php if ($item['status'] === 'completed'): ?>
                                    <a href="results.php?id=<?php echo $item['id']; ?>" class="action-btn view">Результат</a>
                                <?php else: ?>
                                    <a href="take_interview.php?id=<?php echo $item['id']; ?>" class="action-btn continue">Продолжить</a>
                                <?php endif; ?>
                                
                                <button onclick="deleteInterview(<?php echo $item['id']; ?>)" class="action-btn delete">
                                    <img src="images/trash.svg" alt="Удалить" style="width:16px; height:16px;">
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-history">
                        <img src="images/empty.svg" alt="" style="width: 64px; opacity: 0.15; margin-bottom: 15px;">
                        <p>У вас еще нет пройденных собеседований. Начните свое первое испытание выше!</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</main>

<div id="delete-modal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-icon">🗑️</div>
        <h3>Удалить запись?</h3>
        <p>Вы уверены, что хотите удалить результат этого собеседования? Это действие нельзя будет отменить.</p>
        
        <div class="modal-actions">
            <button class="modal-btn cancel" onclick="closeDeleteModal()">Отмена</button>
            <button class="modal-btn confirm" id="confirm-delete-btn">Да, удалить</button>
        </div>
    </div>
</div>


<script src="js/interview.js"></script>
<?php require_once "components/footer.php"; ?>
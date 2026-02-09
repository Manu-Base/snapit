<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$title = 'Analytics | ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/dashboard_header.php';
require_once __DIR__ . '/../includes/db.php';

$user_id = current_user_id();

$attendance = (int) db()->prepare('SELECT COUNT(*) FROM time_sessions WHERE user_id = ?');
$attendance_stmt = db()->prepare('SELECT COUNT(*) FROM time_sessions WHERE user_id = ?');
$attendance_stmt->execute([$user_id]);
$total_sessions = (int) $attendance_stmt->fetchColumn();

$task_stmt = db()->prepare('SELECT COUNT(*) FROM tasks WHERE user_id = ?');
$task_stmt->execute([$user_id]);
$total_tasks = (int) $task_stmt->fetchColumn();

$completed_stmt = db()->prepare('SELECT COUNT(*) FROM tasks WHERE user_id = ? AND completed_at IS NOT NULL');
$completed_stmt->execute([$user_id]);
$completed_tasks = (int) $completed_stmt->fetchColumn();

$completion_rate = $total_tasks > 0 ? round(($completed_tasks / $total_tasks) * 100) : 0;
?>
<main class="dashboard">
  <h1>Analytics</h1>
  <div class="stats-grid">
    <div class="card">Attendance sessions: <?php echo e((string) $total_sessions); ?></div>
    <div class="card">Tasks completed: <?php echo e((string) $completed_tasks); ?> / <?php echo e((string) $total_tasks); ?></div>
    <div class="card">Completion rate: <?php echo e((string) $completion_rate); ?>%</div>
  </div>
  <p class="muted">Detailed reports can be cached in analytics_cache for performance.</p>
</main>
<?php require_once __DIR__ . '/../includes/dashboard_nav.php'; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

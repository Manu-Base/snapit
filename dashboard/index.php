<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$title = 'Dashboard | ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/dashboard_header.php';
require_once __DIR__ . '/../includes/db.php';

$user_id = current_user_id();
$stmt = db()->prepare('SELECT name FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<main class="dashboard">
  <h1>Welcome back, <?php echo e($user['name'] ?? ''); ?>!</h1>
  <p class="muted">Stay on schedule. Your timetable defines your execution windows.</p>
  <div class="card-grid">
    <div class="card">Create or update your timetable to enable time login.</div>
    <div class="card">Log in only during scheduled hours to unlock tasks.</div>
    <div class="card">Track progress through roadmaps and analytics.</div>
  </div>
</main>
<?php require_once __DIR__ . '/../includes/dashboard_nav.php'; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

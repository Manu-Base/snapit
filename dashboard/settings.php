<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$title = 'Settings | ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/dashboard_header.php';
require_once __DIR__ . '/../includes/db.php';

$user_id = current_user_id();
$stmt = db()->prepare('SELECT name, email FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<main class="dashboard">
  <h1>Settings</h1>
  <div class="card">
    <p><strong>Name:</strong> <?php echo e($user['name'] ?? ''); ?></p>
    <p><strong>Email:</strong> <?php echo e($user['email'] ?? ''); ?></p>
    <p class="muted">Account management and security options can be expanded here.</p>
  </div>
</main>
<?php require_once __DIR__ . '/../includes/dashboard_nav.php'; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

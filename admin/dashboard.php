<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$title = 'Admin Dashboard | ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$stats = [
    'total_users' => (int) db()->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'active_users' => (int) db()->query('SELECT COUNT(*) FROM users WHERE status = "active"')->fetchColumn(),
    'roadmap_count' => (int) db()->query('SELECT COUNT(*) FROM roadmaps')->fetchColumn(),
    'active_sessions' => (int) db()->query('SELECT COUNT(*) FROM time_sessions WHERE ended_at IS NULL')->fetchColumn(),
];
?>
<section class="dashboard">
  <h1>System Overview</h1>
  <div class="stats-grid">
    <div class="card">Total users: <?php echo e((string) $stats['total_users']); ?></div>
    <div class="card">Active users: <?php echo e((string) $stats['active_users']); ?></div>
    <div class="card">Roadmaps: <?php echo e((string) $stats['roadmap_count']); ?></div>
    <div class="card">Active sessions: <?php echo e((string) $stats['active_sessions']); ?></div>
  </div>
  <p class="muted">Admins can manage users and security but cannot access user tasks or roadmap content.</p>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

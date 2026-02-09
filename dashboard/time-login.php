<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$title = 'Time Login | ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/dashboard_header.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';

$user_id = current_user_id();
$today = gmdate('l');
$current_time = gmdate('H:i:s');
$error = '';
$success = '';

$stmt = db()->prepare('SELECT * FROM timetables WHERE user_id = ? AND day_of_week = ?');
$stmt->execute([$user_id, $today]);
$today_slot = $stmt->fetch();

$stmt = db()->prepare('SELECT * FROM time_sessions WHERE user_id = ? AND ended_at IS NULL');
$stmt->execute([$user_id]);
$active_session = $stmt->fetch();

$within_slot = $today_slot && (int) $today_slot['is_holiday'] === 0
    && $current_time >= $today_slot['start_time']
    && $current_time <= $today_slot['end_time'];

if ($active_session && $current_time > ($today_slot['end_time'] ?? '00:00:00')) {
    $stmt = db()->prepare('UPDATE time_sessions SET ended_at = ? WHERE id = ?');
    $stmt->execute([now_utc(), $active_session['id']]);
    $active_session = null;
}

if (is_post()) {
    if (!csrf_validate($_POST[CSRF_TOKEN_KEY] ?? null)) {
        $error = 'Invalid session token.';
    } elseif (!$today_slot) {
        $error = 'No timetable exists for today.';
    } elseif ((int) $today_slot['is_holiday'] === 1) {
        $error = 'Today is marked as a holiday.';
    } elseif (!$within_slot) {
        $error = 'You can only log in during your scheduled time.';
    } elseif ($active_session) {
        $error = 'You already have an active session.';
    } else {
        $stmt = db()->prepare('INSERT INTO time_sessions (user_id, day_of_week, start_time, end_time, started_at) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$user_id, $today, $today_slot['start_time'], $today_slot['end_time'], now_utc()]);
        $success = 'Time session started.';
        $active_session = ['started_at' => now_utc()];
    }
}
?>
<main class="dashboard">
  <h1>Time Login</h1>
  <?php if ($error): ?>
    <p class="error"><?php echo e($error); ?></p>
  <?php endif; ?>
  <?php if ($success): ?>
    <p class="success"><?php echo e($success); ?></p>
  <?php endif; ?>

  <div class="card">
    <p>Today: <strong><?php echo e($today); ?></strong></p>
    <p>Server time: <strong><?php echo e($current_time); ?></strong></p>
    <?php if (!$today_slot): ?>
      <p class="muted">Create a timetable entry to enable time login.</p>
    <?php elseif ((int) $today_slot['is_holiday'] === 1): ?>
      <p class="muted">Today is a holiday. No sessions allowed.</p>
    <?php else: ?>
      <p>Scheduled slot: <?php echo e($today_slot['start_time']); ?> - <?php echo e($today_slot['end_time']); ?></p>
    <?php endif; ?>
  </div>

  <form method="post" class="card">
    <input type="hidden" name="<?php echo CSRF_TOKEN_KEY; ?>" value="<?php echo e(csrf_token()); ?>">
    <button class="btn" type="submit" <?php echo $within_slot && !$active_session ? '' : 'disabled'; ?>>Start Session</button>
  </form>

  <?php if ($active_session): ?>
    <div class="card success">
      Active session started at <?php echo e($active_session['started_at']); ?>.
    </div>
  <?php endif; ?>
</main>
<?php require_once __DIR__ . '/../includes/dashboard_nav.php'; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$title = 'Timetable | ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/dashboard_header.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';

$user_id = current_user_id();
$error = '';
$success = '';

if (is_post()) {
    if (!csrf_validate($_POST[CSRF_TOKEN_KEY] ?? null)) {
        $error = 'Invalid session token.';
    } else {
        $day = $_POST['day'] ?? '';
        $start = $_POST['start_time'] ?? '';
        $end = $_POST['end_time'] ?? '';
        $is_holiday = isset($_POST['is_holiday']) ? 1 : 0;

        if (!in_array($day, ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'], true)) {
            $error = 'Invalid day selection.';
        } elseif (!$is_holiday && (!$start || !$end)) {
            $error = 'Provide start and end times.';
        } else {
            $stmt = db()->prepare('REPLACE INTO timetables (user_id, day_of_week, start_time, end_time, is_holiday) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$user_id, $day, $start ?: null, $end ?: null, $is_holiday]);
            $success = 'Timetable updated.';
        }
    }
}

$stmt = db()->prepare('SELECT day_of_week, start_time, end_time, is_holiday FROM timetables WHERE user_id = ?');
$stmt->execute([$user_id]);
$rows = $stmt->fetchAll();
$timetable = [];
foreach ($rows as $row) {
    $timetable[$row['day_of_week']] = $row;
}
?>
<main class="dashboard">
  <h1>Weekly Timetable</h1>
  <?php if ($error): ?>
    <p class="error"><?php echo e($error); ?></p>
  <?php endif; ?>
  <?php if ($success): ?>
    <p class="success"><?php echo e($success); ?></p>
  <?php endif; ?>

  <form method="post" class="card">
    <input type="hidden" name="<?php echo CSRF_TOKEN_KEY; ?>" value="<?php echo e(csrf_token()); ?>">
    <label>Day of Week
      <select name="day" required>
        <?php foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day): ?>
          <option value="<?php echo e($day); ?>"><?php echo e($day); ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Start Time
      <input type="time" name="start_time">
    </label>
    <label>End Time
      <input type="time" name="end_time">
    </label>
    <label class="checkbox">
      <input type="checkbox" name="is_holiday"> Mark as Holiday
    </label>
    <button class="btn" type="submit">Save Slot</button>
  </form>

  <div class="card">
    <h2>Current Schedule</h2>
    <ul class="list">
      <?php foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day):
        $row = $timetable[$day] ?? null;
      ?>
        <li>
          <strong><?php echo e($day); ?>:</strong>
          <?php if (!$row): ?>
            Not set
          <?php elseif ((int) $row['is_holiday'] === 1): ?>
            Holiday
          <?php else: ?>
            <?php echo e($row['start_time']); ?> - <?php echo e($row['end_time']); ?>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</main>
<?php require_once __DIR__ . '/../includes/dashboard_nav.php'; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$title = 'Tasks | ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/dashboard_header.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';

$user_id = current_user_id();
$today = gmdate('l');
$current_time = gmdate('H:i:s');
$error = '';
$success = '';

$stmt = db()->prepare('SELECT * FROM time_sessions WHERE user_id = ? AND ended_at IS NULL');
$stmt->execute([$user_id]);
$active_session = $stmt->fetch();

$can_execute = (bool) $active_session;

if (is_post()) {
    if (!csrf_validate($_POST[CSRF_TOKEN_KEY] ?? null)) {
        $error = 'Invalid session token.';
    } else {
        $action = $_POST['action'] ?? '';
        if ($action === 'create') {
            $title_input = trim($_POST['title'] ?? '');
            $day = $_POST['day'] ?? $today;
            if ($title_input === '') {
                $error = 'Task title required.';
            } else {
                $stmt = db()->prepare('INSERT INTO tasks (user_id, title, day_of_week, created_at) VALUES (?, ?, ?, ?)');
                $stmt->execute([$user_id, $title_input, $day, now_utc()]);
                $success = 'Task added.';
            }
        } elseif ($action === 'complete') {
            $task_id = (int) ($_POST['task_id'] ?? 0);
            if (!$can_execute) {
                $error = 'No active time session.';
            } else {
                $stmt = db()->prepare('SELECT id FROM tasks WHERE id = ? AND user_id = ?');
                $stmt->execute([$task_id, $user_id]);
                if ($stmt->fetch()) {
                    $stmt = db()->prepare('UPDATE tasks SET completed_at = ? WHERE id = ?');
                    $stmt->execute([now_utc(), $task_id]);
                    $stmt = db()->prepare('INSERT INTO task_logs (task_id, user_id, session_id, completed_at) VALUES (?, ?, ?, ?)');
                    $stmt->execute([$task_id, $user_id, $active_session['id'], now_utc()]);
                    $success = 'Task completed.';
                }
            }
        }
    }
}

$stmt = db()->prepare('SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll();
?>
<main class="dashboard">
  <h1>Tasks</h1>
  <?php if ($error): ?>
    <p class="error"><?php echo e($error); ?></p>
  <?php endif; ?>
  <?php if ($success): ?>
    <p class="success"><?php echo e($success); ?></p>
  <?php endif; ?>

  <form method="post" class="card">
    <input type="hidden" name="<?php echo CSRF_TOKEN_KEY; ?>" value="<?php echo e(csrf_token()); ?>">
    <input type="hidden" name="action" value="create">
    <label>Task Title
      <input type="text" name="title" required>
    </label>
    <label>Day of Week
      <select name="day" required>
        <?php foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day): ?>
          <option value="<?php echo e($day); ?>" <?php echo $day === $today ? 'selected' : ''; ?>><?php echo e($day); ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <button class="btn" type="submit">Add Task</button>
  </form>

  <div class="card">
    <h2>Your Tasks</h2>
    <ul class="list">
      <?php foreach ($tasks as $task): ?>
        <li>
          <div>
            <strong><?php echo e($task['title']); ?></strong>
            <span class="muted">(<?php echo e($task['day_of_week']); ?>)</span>
          </div>
          <?php if ($task['completed_at']): ?>
            <span class="success">Completed</span>
          <?php else: ?>
            <form method="post">
              <input type="hidden" name="<?php echo CSRF_TOKEN_KEY; ?>" value="<?php echo e(csrf_token()); ?>">
              <input type="hidden" name="action" value="complete">
              <input type="hidden" name="task_id" value="<?php echo e((string) $task['id']); ?>">
              <button class="btn small" type="submit" <?php echo $can_execute ? '' : 'disabled'; ?>>Complete</button>
            </form>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
    <p class="muted">Tasks can only be completed during an active time session.</p>
  </div>
</main>
<?php require_once __DIR__ . '/../includes/dashboard_nav.php'; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

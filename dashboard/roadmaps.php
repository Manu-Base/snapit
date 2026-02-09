<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$title = 'Roadmaps | ' . APP_NAME;
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
        $title_input = trim($_POST['title'] ?? '');
        if ($title_input === '') {
            $error = 'Roadmap title required.';
        } else {
            $stmt = db()->prepare('INSERT INTO roadmaps (user_id, title, created_at) VALUES (?, ?, ?)');
            $stmt->execute([$user_id, $title_input, now_utc()]);
            $roadmap_id = (int) db()->lastInsertId();
            $stmt = db()->prepare('INSERT INTO roadmap_nodes (roadmap_id, parent_id, node_type, title) VALUES (?, NULL, "goal", ?)');
            $stmt->execute([$roadmap_id, $title_input]);
            $success = 'Roadmap created.';
        }
    }
}

$stmt = db()->prepare('SELECT * FROM roadmaps WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$user_id]);
$roadmaps = $stmt->fetchAll();
?>
<main class="dashboard">
  <h1>Roadmaps</h1>
  <?php if ($error): ?>
    <p class="error"><?php echo e($error); ?></p>
  <?php endif; ?>
  <?php if ($success): ?>
    <p class="success"><?php echo e($success); ?></p>
  <?php endif; ?>

  <form method="post" class="card">
    <input type="hidden" name="<?php echo CSRF_TOKEN_KEY; ?>" value="<?php echo e(csrf_token()); ?>">
    <label>Roadmap Title
      <input type="text" name="title" required>
    </label>
    <button class="btn" type="submit">Create Roadmap</button>
  </form>

  <div class="card">
    <h2>Your Roadmaps</h2>
    <ul class="list">
      <?php foreach ($roadmaps as $roadmap): ?>
        <li>
          <strong><?php echo e($roadmap['title']); ?></strong>
          <span class="muted">Created <?php echo e($roadmap['created_at']); ?></span>
        </li>
      <?php endforeach; ?>
    </ul>
    <p class="muted">Drag & drop tree editing can be layered on top of these nodes in the frontend.</p>
  </div>
</main>
<?php require_once __DIR__ . '/../includes/dashboard_nav.php'; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$title = 'Knowledge Space | ' . APP_NAME;
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
        $content = trim($_POST['content'] ?? '');
        if ($title_input === '' || $content === '') {
            $error = 'Title and content required.';
        } else {
            $blocks = json_encode([
                ['type' => 'text', 'value' => $content],
            ], JSON_THROW_ON_ERROR);
            $stmt = db()->prepare('INSERT INTO knowledge_entries (user_id, title, blocks_json, created_at) VALUES (?, ?, ?, ?)');
            $stmt->execute([$user_id, $title_input, $blocks, now_utc()]);
            $success = 'Entry saved.';
        }
    }
}

$stmt = db()->prepare('SELECT * FROM knowledge_entries WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$user_id]);
$entries = $stmt->fetchAll();
?>
<main class="dashboard">
  <h1>Knowledge Space</h1>
  <?php if ($error): ?>
    <p class="error"><?php echo e($error); ?></p>
  <?php endif; ?>
  <?php if ($success): ?>
    <p class="success"><?php echo e($success); ?></p>
  <?php endif; ?>

  <form method="post" class="card">
    <input type="hidden" name="<?php echo CSRF_TOKEN_KEY; ?>" value="<?php echo e(csrf_token()); ?>">
    <label>Title
      <input type="text" name="title" required>
    </label>
    <label>Content
      <textarea name="content" rows="5" required></textarea>
    </label>
    <button class="btn" type="submit">Save Entry</button>
  </form>

  <div class="card">
    <h2>Entries</h2>
    <ul class="list">
      <?php foreach ($entries as $entry): ?>
        <li>
          <strong><?php echo e($entry['title']); ?></strong>
          <span class="muted"><?php echo e($entry['created_at']); ?></span>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</main>
<?php require_once __DIR__ . '/../includes/dashboard_nav.php'; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

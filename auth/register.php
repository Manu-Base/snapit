<?php
$title = 'Sign Up | ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

$error = '';

if (is_post()) {
    if (!csrf_validate($_POST[CSRF_TOKEN_KEY] ?? null)) {
        $error = 'Invalid session token.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
            $error = 'Use a valid name, email, and a password of at least 8 characters.';
        } else {
            $stmt = db()->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Account already exists for this email.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = db()->prepare('INSERT INTO users (name, email, password_hash, status, created_at) VALUES (?, ?, ?, "active", ?)');
                $stmt->execute([$name, $email, $hash, now_utc()]);
                login_user((int) db()->lastInsertId());
                redirect('/dashboard/index.php');
            }
        }
    }
}
?>
<section class="auth-card">
  <h1>Create Account</h1>
  <?php if ($error): ?>
    <p class="error"><?php echo e($error); ?></p>
  <?php endif; ?>
  <form method="post">
    <input type="hidden" name="<?php echo CSRF_TOKEN_KEY; ?>" value="<?php echo e(csrf_token()); ?>">
    <label>Full Name
      <input type="text" name="name" required>
    </label>
    <label>Email
      <input type="email" name="email" required>
    </label>
    <label>Password
      <input type="password" name="password" minlength="8" required>
    </label>
    <button class="btn" type="submit">Create Account</button>
  </form>
  <p class="muted">Already have an account? <a href="/auth/login.php">Sign in</a></p>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

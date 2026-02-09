<?php
$title = 'Sign In | ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
$error = '';

if (is_post()) {
    if (!csrf_validate($_POST[CSRF_TOKEN_KEY] ?? null)) {
        $error = 'Invalid session token.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            $error = 'Please enter a valid email and password.';
        } else {
            $stmt = db()->prepare('SELECT id, password_hash FROM users WHERE email = ? AND status = "active"');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                login_user((int) $user['id']);
                redirect('/dashboard/index.php');
            } else {
                $error = 'Invalid credentials.';
            }
        }
    }
}
?>
<section class="auth-card">
  <h1>Sign In</h1>
  <?php if ($error): ?>
    <p class="error"><?php echo e($error); ?></p>
  <?php endif; ?>
  <form method="post">
    <input type="hidden" name="<?php echo CSRF_TOKEN_KEY; ?>" value="<?php echo e(csrf_token()); ?>">
    <label>Email
      <input type="email" name="email" required>
    </label>
    <label>Password
      <input type="password" name="password" required>
    </label>
    <button class="btn" type="submit">Sign In</button>
  </form>
  <p class="muted">No account? <a href="/auth/register.php">Sign up</a></p>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

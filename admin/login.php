<?php
$title = 'Admin Login | ' . APP_NAME;
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
            $stmt = db()->prepare('SELECT id, password_hash FROM admin_users WHERE email = ? AND status = "active"');
            $stmt->execute([$email]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password_hash'])) {
                login_admin((int) $admin['id']);
                redirect('/admin/dashboard.php');
            } else {
                $error = 'Invalid credentials.';
            }
        }
    }
}
?>
<section class="auth-card">
  <h1>Admin Login</h1>
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
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

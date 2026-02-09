<?php
$title = 'Discipline-Driven Productivity | ' . APP_NAME;
require_once __DIR__ . '/includes/header.php';
?>
<header class="public-header">
  <nav class="nav-bar">
    <div class="logo">SnapIt</div>
    <div class="nav-links">
      <a href="/auth/login.php">Sign In</a>
      <a class="btn" href="/auth/register.php">Sign Up</a>
    </div>
  </nav>
</header>
<main>
  <section class="hero">
    <h1>Discipline-Driven Productivity</h1>
    <p>Time-locked execution that turns plans into honest results.</p>
    <div class="hero-actions">
      <a class="btn" href="/auth/register.php">Get Started</a>
      <a class="btn ghost" href="#about">Learn More</a>
    </div>
  </section>

  <section id="about" class="section">
    <h2>Why Time-Based Execution Matters</h2>
    <p>Normal to-do apps allow unchecked completion. SnapIt enforces time slots, attendance, and progress reporting so you build real discipline.</p>
    <p>Built for students, teams, professionals, and fitness users who need structure and accountability.</p>
  </section>

  <section class="section features">
    <h2>Core Features</h2>
    <div class="feature-grid">
      <div class="card">Timetable-based execution</div>
      <div class="card">Login/logout like attendance</div>
      <div class="card">Roadmap tree visualization</div>
      <div class="card">Analytics & discipline tracking</div>
      <div class="card">Collaboration without privacy loss</div>
    </div>
  </section>
</main>
<footer class="public-footer">
  <div>
    <strong>SnapIt</strong><br>
    Contact: support@yourdomain.com
  </div>
  <div>
    <a href="#">Terms</a> | <a href="#">Privacy</a>
  </div>
  <div>&copy; <?php echo date('Y'); ?> SnapIt</div>
</footer>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

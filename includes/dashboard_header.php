<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/csrf.php';
start_secure_session();
require_login();
$csrf_token = csrf_token();
$server_time = gmdate('H:i:s');
$server_date = gmdate('Y-m-d');
$server_day = gmdate('l');
?>
<header class="top-bar">
  <div>
    <strong>Server Time:</strong> <?php echo e($server_time); ?>
  </div>
  <div>
    <?php echo e($server_day); ?>, <?php echo e($server_date); ?>
  </div>
  <div id="session-timer" data-active="0">No active session</div>
</header>

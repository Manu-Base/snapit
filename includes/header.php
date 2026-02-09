<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/csrf.php';
start_secure_session();
$csrf_token = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#0d1b2a">
  <link rel="manifest" href="/manifest.json">
  <link rel="stylesheet" href="/assets/css/app.css">
  <title><?php echo e($title ?? APP_NAME); ?></title>
</head>
<body>

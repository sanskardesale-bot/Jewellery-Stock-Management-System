<?php
/**
 * JewelTrack — Login Page
 */
require_once __DIR__ . '/config/app.php';

// Already logged in
if (isLoggedIn()) {
    redirect('/jeweltrack/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Please enter your email and password.';
    } else {
        $result = loginUser($email, $password);
        if ($result['success']) {
            redirect('/jeweltrack/dashboard.php');
        } else {
            $error = $result['message'];
        }
    }
}

$shopName = getSetting('shop_name', 'JewelTrack');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Login to JewelTrack — Jewellery Inventory Management System">
  <title>Login — <?= e($shopName) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="/jeweltrack/assets/css/style.css">
</head>
<body class="login-page">

<div class="login-card">
  <!-- Logo -->
  <div class="logo-wrap">
    <div class="logo-icon"><i class="bi bi-gem"></i></div>
    <h1><?= e($shopName) ?></h1>
    <p class="subtitle">Jewellery Inventory &amp; Stock Management</p>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
      <i class="bi bi-x-circle-fill"></i><span><?= e($error) ?></span>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <form method="POST" action="/jeweltrack/login.php" novalidate>
    <?= csrfField() ?>

    <div class="mb-3">
      <label for="email" class="form-label">Email Address</label>
      <div class="input-group">
        <span class="input-group-text bg-white border-end-0">
          <i class="bi bi-envelope text-muted"></i>
        </span>
        <input type="email" id="email" name="email" class="form-control border-start-0"
               placeholder="admin@jeweltrack.com"
               value="<?= e($_POST['email'] ?? '') ?>" required autofocus>
      </div>
    </div>

    <div class="mb-4">
      <label for="password" class="form-label">Password</label>
      <div class="input-group">
        <span class="input-group-text bg-white border-end-0">
          <i class="bi bi-lock text-muted"></i>
        </span>
        <input type="password" id="password" name="password" class="form-control border-start-0"
               placeholder="Enter your password" required>
        <button type="button" class="input-group-text bg-white border-start-0" id="togglePass"
                title="Show/Hide password">
          <i class="bi bi-eye text-muted" id="eyeIcon"></i>
        </button>
      </div>
    </div>

    <button type="submit" class="btn btn-gold w-100 py-2 fs-6">
      <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
    </button>
  </form>

  <div class="mt-4 p-3 rounded" style="background:#f8f9fa;border:1px solid #e7e7e1;font-size:0.78rem;">
    <p class="mb-1 fw-600 text-muted">Demo Credentials</p>
    <p class="mb-1">Admin: <code>admin@jeweltrack.com</code> / <code>Admin@123</code></p>
    <p class="mb-0">Staff: <code>priya@jeweltrack.com</code> / <code>Staff@123</code></p>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById('togglePass')?.addEventListener('click', function() {
    const pwd  = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    const isHidden = pwd.type === 'password';
    pwd.type = isHidden ? 'text' : 'password';
    icon.className = isHidden ? 'bi bi-eye-slash text-muted' : 'bi bi-eye text-muted';
  });
</script>
</body>
</html>

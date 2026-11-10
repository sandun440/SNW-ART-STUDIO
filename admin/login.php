<?php
require_once __DIR__ . '/../config/config.php';
if (!empty($_SESSION['admin_id'])) {
    redirect('admin/index.php');
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $stmt = db()->prepare('SELECT * FROM admins WHERE email=?');
    $stmt->execute([trim($_POST['email'] ?? '')]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($_POST['password'] ?? '', $admin['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        redirect('admin/index.php');
    }
    $error = 'Invalid email or password.';
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Studio Portal Login · <?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body style="background: radial-gradient(circle at 50% 25%, #f9f6f0 0%, #ede6da 100%); min-height: 100vh;">

<div class="admin-login-wrap">
  <div class="admin-login-card">
    <div style="text-align: center; margin-bottom: 28px;">
      <a class="brand" href="<?= url('index.php') ?>" style="justify-content: center; margin-bottom: 16px;">
        <div class="brand-monogram" style="width: 48px; height: 48px; font-size: 19px;">SNW</div>
      </a>
      <p class="eyebrow" style="justify-content: center; margin-bottom: 4px;">Studio Administration</p>
      <h1 style="font-family: var(--font-serif); font-size: 32px; font-weight: 600; margin: 0; color: var(--ink);">Sign In</h1>
    </div>

    <?php if ($error): ?>
      <div class="alert error" style="margin-bottom: 20px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        <span><?= e($error) ?></span>
      </div>
    <?php endif; ?>

    <form method="post">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      
      <div class="field" style="margin-bottom: 18px;">
        <label>Admin Email</label>
        <input type="email" name="email" required placeholder="admin@snwartstudio.com" value="<?= old('email') ?>">
      </div>

      <div class="field" style="margin-bottom: 24px;">
        <label>Password</label>
        <input type="password" name="password" required placeholder="••••••••">
      </div>

      <button class="button gold" type="submit" style="width: 100%; padding: 15px; font-size: 14.5px;">
        <span>Access Dashboard</span>
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
      </button>
    </form>

    <div style="text-align: center; margin-top: 24px; font-size: 13px;">
      <a href="<?= url('index.php') ?>" style="color: var(--muted); display: inline-flex; align-items: center; gap: 6px;">
        <span>&larr; Back to SNW Art Studio website</span>
      </a>
    </div>
  </div>
</div>

</body>
</html>

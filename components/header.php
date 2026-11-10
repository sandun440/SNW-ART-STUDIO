<?php
$pageTitle = $pageTitle ?? APP_NAME;
$currentUri = $_SERVER['REQUEST_URI'] ?? '';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> · <?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
<header class="site-header">
  <div class="container nav-wrap">
    <a class="brand" href="<?= url('index.php') ?>" aria-label="SNW Art Studio Home">
      <div class="brand-monogram">SNW</div>
      <div class="brand-text">
        <span>SNW</span>
        <small>ART STUDIO</small>
      </div>
    </a>
    
    <button class="nav-toggle" aria-label="Toggle navigation">☰</button>
    
    <nav class="main-nav">
      <a href="<?= url('index.php') ?>" class="<?= str_contains($currentUri, 'index.php') || $currentUri === '/' || str_ends_with($currentUri, 'SNW%20ART%20STUDIO/') ? 'active' : '' ?>">Home</a>
      <a href="<?= url('gallery.php') ?>" class="<?= str_contains($currentUri, 'gallery.php') ? 'active' : '' ?>">Gallery</a>
      <a href="<?= url('about.php') ?>" class="<?= str_contains($currentUri, 'about.php') ? 'active' : '' ?>">The Artist</a>
      <a href="<?= url('contact.php') ?>" class="<?= str_contains($currentUri, 'contact.php') ? 'active' : '' ?>">Contact</a>
      <a href="<?= url('order.php') ?>" class="nav-cta">
        <span>Commission a Piece</span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
      </a>

      <?php if (!empty($_SESSION['admin_id'])): ?>
        <a href="<?= url('admin/index.php') ?>" class="admin-nav-btn is-logged" title="Studio Admin Dashboard">
          <span class="admin-dot"></span>
          <span>Dashboard</span>
        </a>
      <?php else: ?>
        <a href="<?= url('admin/login.php') ?>" class="admin-nav-btn" title="Studio Admin Login">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
          <span>Admin</span>
        </a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<?php if ($message = flash('success')): ?>
  <div class="container" style="margin-top: 24px;">
    <div class="alert success">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
      <?= e($message) ?>
    </div>
  </div>
<?php endif; ?>

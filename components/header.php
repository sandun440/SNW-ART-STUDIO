<?php
$pageTitle = $pageTitle ?? APP_NAME;
$currentUri = $_SERVER['REQUEST_URI'] ?? '';
?>
<!--
  SNW ART STUDIO — Fine Handcrafted Graphite & Portrait Art
  Designed & Engineered by Sandun Sameera
  Portfolio: https://sandunsameera.vercel.app/
-->
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="author" content="Sandun Sameera (https://sandunsameera.vercel.app/)">
<title><?= e($pageTitle) ?> · <?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="<?= url('assets/images/favicon.png') ?>?v=1.0">
<link rel="apple-touch-icon" href="<?= url('assets/images/favicon.png') ?>?v=1.0">
<link href="<?= url('assets/css/style.css') ?>?v=5.0" rel="stylesheet">
</head>
<body>
<header class="site-header">
  <div class="container nav-wrap">
    <a class="brand" href="<?= url('index.php') ?>" aria-label="SNW Art Studio Home">
      <img src="<?= url('assets/images/logo.png') ?>?v=1.0" alt="SNW ART STUDIO · Pencil Art &amp; Sketch Studio" class="brand-logo">
    </a>
    
    <button class="nav-toggle" aria-label="Toggle navigation">☰</button>
    
    <nav class="main-nav">
      <a href="<?= url('index.php') ?>" class="<?= str_contains($currentUri, 'index.php') || $currentUri === '/' || str_ends_with($currentUri, 'SNW%20ART%20STUDIO/') ? 'active' : '' ?>">Home</a>
      <a href="<?= url('gallery.php') ?>" class="<?= str_contains($currentUri, 'gallery.php') ? 'active' : '' ?>">Gallery</a>
      <a href="<?= url('about.php') ?>" class="<?= str_contains($currentUri, 'about.php') ? 'active' : '' ?>">The Artist</a>
      <a href="<?= url('track.php') ?>" class="<?= str_contains($currentUri, 'track.php') ? 'active' : '' ?>">Track Order</a>
      <a href="<?= url('contact.php') ?>" class="<?= str_contains($currentUri, 'contact.php') ? 'active' : '' ?>">Contact</a>
      <a href="<?= url('order.php') ?>" class="nav-cta">
        <span>Commission a Piece</span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
      </a>

      <?php if (!empty($_SESSION['admin_id'])): ?>
        <a href="<?= url('admin/index.php') ?>" class="admin-nav-btn is-logged" title="Studio Admin Dashboard" style="display:inline-flex !important; align-items:center !important; gap:9px !important; padding:8px 18px !important; border-radius:999px !important; background:#ffffff !important; border:1.5px solid rgba(197,160,89,0.5) !important; color:#11100e !important; text-decoration:none !important; font-weight:600 !important; font-size:13px !important; box-shadow:0 2px 8px rgba(17,16,14,0.06) !important; white-space:nowrap !important;">
          <span class="admin-dot" style="width:8px; height:8px; border-radius:50%; background:#10b981; box-shadow:0 0 0 2px rgba(16,185,129,0.25); flex-shrink:0;"></span>
          <span style="color:#11100e !important; font-weight:600 !important;">Dashboard</span>
          <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="opacity:0.45; margin-left:1px;"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </a>
      <?php else: ?>
        <a href="<?= url('admin/login.php') ?>" class="admin-nav-btn" title="Studio Admin Login" style="display:inline-flex !important; align-items:center !important; gap:8px !important; padding:8px 18px !important; border-radius:999px !important; background:#ffffff !important; border:1.5px solid rgba(197,160,89,0.4) !important; color:#11100e !important; text-decoration:none !important; font-weight:600 !important; font-size:13px !important; box-shadow:0 2px 8px rgba(17,16,14,0.06) !important; white-space:nowrap !important;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--gold-dark); flex-shrink:0;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
          <span style="color:#11100e !important; font-weight:600 !important;">Admin</span>
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

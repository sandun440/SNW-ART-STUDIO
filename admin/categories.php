<?php
require_once __DIR__ . '/../config/config.php';
admin_required();

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));

    if ($name === '') {
        $error = 'Category name is required.';
    } else {
        try {
            $s = db()->prepare('INSERT INTO categories (name, slug) VALUES (?,?)');
            $s->execute([$name, $slug]);
            flash('success', 'New category created successfully.');
            redirect('admin/categories.php');
        } catch (PDOException $e) {
            $error = 'That category already exists.';
        }
    }
}

$categories = db()->query('SELECT c.*, COUNT(a.id) artwork_count FROM categories c LEFT JOIN artworks a ON a.category_id=c.id GROUP BY c.id ORDER BY c.name')->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Artwork Categories · <?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="<?= url('assets/css/style.css') ?>?v=3.1" rel="stylesheet">
</head>
<body style="background: var(--cream);">

<header class="site-header">
  <div class="container nav-wrap">
    <a class="brand" href="<?= url('admin/index.php') ?>">
      <div class="brand-monogram">SNW</div>
      <div class="brand-text">
        <span>SNW</span>
        <small>STUDIO ADMIN</small>
      </div>
    </a>
    <div style="display: flex; gap: 12px; align-items: center;">
      <a class="button light" href="<?= url('index.php') ?>" target="_blank" style="padding: 8px 18px; font-size: 12.5px;">View Live Site &nearr;</a>
      <a class="button" href="<?= url('admin/logout.php') ?>" style="padding: 8px 18px; font-size: 12.5px;">Log Out</a>
    </div>
  </div>
</header>

<main class="container" style="padding: 40px 0 80px;">
  <div style="margin-bottom: 28px;">
    <p class="eyebrow"><a href="<?= url('admin/index.php') ?>" style="color:var(--gold-dark);">&larr; Back to Dashboard</a></p>
    <h1 style="font-family: var(--font-serif); font-size: 38px; font-weight: 600; margin: 4px 0 8px; color: var(--ink);">
      Collection Categories
    </h1>
    <p class="lead">Organize studio pencil drawings, portraits, and curated collections.</p>
  </div>

  <?php if ($message = flash('success')): ?>
    <div class="alert success" style="margin-bottom: 24px;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
      <span><?= e($message) ?></span>
    </div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="alert error" style="margin-bottom: 24px;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
      <span><?= e($error) ?></span>
    </div>
  <?php endif; ?>

  <div class="split" style="align-items: start; gap: 48px;">
    <!-- Add Category Form -->
    <div class="notice" style="background: var(--paper); border: 1.5px solid var(--line); border-radius: var(--radius-md); padding: 32px; box-shadow: var(--shadow-sm); width: 100%;">
      <h3 style="font-family: var(--font-serif); font-size: 20px; margin-bottom: 18px;">Add New Category</h3>
      <form method="post">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <div class="field" style="margin-bottom: 20px;">
          <label>Category Name *</label>
          <input type="text" name="name" required placeholder="e.g. Charcoal Sketches">
        </div>
        <button class="button gold" type="submit" style="width: 100%;">
          <span>Save Category</span>
        </button>
      </form>
    </div>

    <!-- Category List Table -->
    <div style="width: 100%;">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Category Name</th>
            <th>URL Slug</th>
            <th>Artworks</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($categories as $cat): ?>
            <tr>
              <td><strong style="font-size: 15px;"><?= e($cat['name']) ?></strong></td>
              <td><code style="background:var(--cream-subtle);padding:2px 8px;border-radius:4px;font-size:12px;"><?= e($cat['slug']) ?></code></td>
              <td><span class="badge-pill" style="margin:0;"><?= e((string)$cat['artwork_count']) ?> works</span></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
</body>
</html>

<?php
require_once __DIR__ . '/../config/config.php';
admin_required();

$counts = [
    'artworks' => db()->query('SELECT COUNT(*) FROM artworks')->fetchColumn(),
    'orders' => db()->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
    'new_orders' => db()->query("SELECT COUNT(*) FROM orders WHERE status='new'")->fetchColumn(),
    'messages' => db()->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn()
];

$orders = db()->query('SELECT * FROM orders ORDER BY created_at DESC LIMIT 10')->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Dashboard · <?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="<?= url('assets/images/favicon.png') ?>?v=1.0">
<link href="<?= url('assets/css/style.css') ?>?v=5.0" rel="stylesheet">
</head>
<body style="background: var(--cream);">

<header class="site-header">
  <div class="container nav-wrap">
    <a class="brand" href="<?= url('admin/index.php') ?>" style="display:flex; align-items:center; gap:12px;">
      <img src="<?= url('assets/images/logo.png') ?>?v=1.0" alt="SNW ART STUDIO" style="height: 44px; width: auto; mix-blend-mode: multiply;">
      <span class="badge-pill" style="font-size:10px; padding:3px 9px; letter-spacing:0.08em; text-transform:uppercase; font-weight:700; background:rgba(184,134,45,0.12); color:var(--gold-dark); border:1px solid rgba(184,134,45,0.3);">Admin</span>
    </a>
    <div style="display: flex; gap: 12px; align-items: center;">
      <a class="button light" href="<?= url('index.php') ?>" target="_blank" style="padding: 8px 18px; font-size: 12.5px;">View Live Site &nearr;</a>
      <a class="button" href="<?= url('admin/logout.php') ?>" style="padding: 8px 18px; font-size: 12.5px;">Log Out</a>
    </div>
  </div>
</header>

<main class="container" style="padding: 40px 0 80px;">
  <div style="margin-bottom: 32px;">
    <p class="eyebrow">Studio Management</p>
    <h1 style="font-family: var(--font-serif); font-size: 38px; font-weight: 600; margin: 4px 0 8px; color: var(--ink);">Welcome back, <?= e($_SESSION['admin_name'] ?? 'Administrator') ?></h1>
    <p class="lead">Here is an overview of studio works, client requests, and ongoing orders.</p>
  </div>

  <!-- Admin Nav Filters -->
  <nav class="filters" style="margin-bottom: 32px;">
    <a href="<?= url('admin/index.php') ?>" class="active">Overview</a>
    <a href="<?= url('admin/artworks.php') ?>">Artworks (<?= $counts['artworks'] ?>)</a>
    <a href="<?= url('admin/orders.php') ?>">Commissions &amp; Orders (<?= $counts['orders'] ?>)</a>
    <a href="<?= url('admin/messages.php') ?>">Messages (<?= $counts['messages'] ?>)</a>
    <a href="<?= url('admin/categories.php') ?>">Categories</a>
    <a href="<?= url('admin/settings.php') ?>">Bank &amp; Settings</a>
  </nav>

  <!-- Metric Stat Cards -->
  <div class="grid" style="grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 48px;">
    <a href="<?= url('admin/artworks.php') ?>" class="stat-card" style="text-decoration: none; color: inherit;">
      <p class="eyebrow">Active Artworks</p>
      <div class="stat-number"><?= e((string)$counts['artworks']) ?></div>
    </a>

    <a href="<?= url('admin/orders.php') ?>" class="stat-card" style="text-decoration: none; color: inherit;">
      <p class="eyebrow">Total Inquiries</p>
      <div class="stat-number"><?= e((string)$counts['orders']) ?></div>
    </a>

    <a href="<?= url('admin/orders.php?status=new') ?>" class="stat-card" style="text-decoration: none; color: inherit;">
      <p class="eyebrow" style="color: #0369a1;">New Pending</p>
      <div class="stat-number" style="color: #0369a1;"><?= e((string)$counts['new_orders']) ?></div>
    </a>

    <a href="<?= url('admin/messages.php') ?>" class="stat-card" style="text-decoration: none; color: inherit;">
      <p class="eyebrow" style="color: var(--gold-dark);">Contact Messages</p>
      <div class="stat-number"><?= e((string)$counts['messages']) ?></div>
    </a>
  </div>

  <!-- Recent Orders Table -->
  <section>
    <div class="section-head" style="margin-bottom: 20px;">
      <div>
        <p class="eyebrow">Recent Client Commissions</p>
        <h2 style="font-size: 28px;">Latest order requests</h2>
      </div>
      <a href="<?= url('admin/orders.php') ?>" class="button light" style="font-size: 13px; padding: 10px 20px;">All Orders &rarr;</a>
    </div>

    <?php if (empty($orders)): ?>
      <div class="notice" style="text-align: center; padding: 48px 20px;">
        <p style="color: var(--muted); margin: 0;">No commission requests logged yet.</p>
      </div>
    <?php else: ?>
      <div style="overflow-x: auto;">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Reference</th>
              <th>Customer</th>
              <th>Contact</th>
              <th>Type</th>
              <th>Status</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order): ?>
              <tr>
                <td>
                  <strong style="font-family: var(--font-serif); font-size: 15px; color: var(--gold-dark);">
                    <?= e($order['reference_no']) ?>
                  </strong>
                </td>
                <td><strong><?= e($order['customer_name']) ?></strong></td>
                <td>
                  <span style="display:block;font-size:13px;"><?= e($order['email']) ?></span>
                  <small style="color:var(--muted);"><?= e($order['phone']) ?></small>
                </td>
                <td><span style="text-transform: capitalize; font-weight: 500;"><?= e($order['artwork_type']) ?></span></td>
                <td>
                  <span class="status-badge status-<?= e($order['status']) ?>">
                    <?= e(ucfirst($order['status'])) ?>
                  </span>
                </td>
                <td style="font-size: 13px; color: var(--muted);"><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                <td>
                  <a href="<?= url('admin/orders.php?id=' . (int)$order['id']) ?>" class="button light" style="padding: 6px 14px; font-size: 12px;">View &rarr;</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </section>

  <footer style="margin-top: 60px; padding: 24px 0; border-top: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center; font-size: 12.5px; color: var(--muted); flex-wrap: wrap; gap: 12px;">
    <div>SNW Art Studio · Internal Management Portal v1.0</div>
    <div>
      System Engineered &amp; Maintained by <a href="https://sandunsameera.vercel.app/" target="_blank" rel="noopener" style="color: var(--gold-dark); font-weight: 600; text-decoration: none;">Sandun Sameera ↗</a>
    </div>
  </footer>
</main>
</body>
</html>

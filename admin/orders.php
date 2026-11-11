<?php
require_once __DIR__ . '/../config/config.php';
admin_required();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $status = $_POST['status'] ?? '';
    if (in_array($status, ['new', 'reviewing', 'confirmed', 'completed', 'cancelled'], true)) {
        db()->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$status, (int)$_POST['id']]);
        flash('success', 'Order status updated successfully.');
    }
}

$orders = db()->query('SELECT o.*, a.title artwork_title, (SELECT image_path FROM order_images oi WHERE oi.order_id=o.id LIMIT 1) reference_image FROM orders o LEFT JOIN artworks a ON a.id=o.artwork_id ORDER BY o.created_at DESC')->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Commission Requests &amp; Orders · <?= APP_NAME ?></title>
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
      Client Orders &amp; Commissions
    </h1>
    <p class="lead">Manage requests, view uploaded reference photographs, and update progress status.</p>
  </div>

  <!-- Admin Nav Tabs -->
  <nav class="filters" style="margin-bottom: 32px;">
    <a href="<?= url('admin/index.php') ?>">Overview</a>
    <a href="<?= url('admin/artworks.php') ?>">Artworks</a>
    <a href="<?= url('admin/orders.php') ?>" class="active">Commissions &amp; Orders</a>
    <a href="<?= url('admin/messages.php') ?>">Messages</a>
    <a href="<?= url('admin/categories.php') ?>">Categories</a>
    <a href="<?= url('admin/settings.php') ?>">Bank &amp; Settings</a>
  </nav>

  <?php if ($message = flash('success')): ?>
    <div class="alert success" style="margin-bottom: 24px;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
      <span><?= e($message) ?></span>
    </div>
  <?php endif; ?>

  <?php if (empty($orders)): ?>
    <div class="notice" style="text-align: center; padding: 60px 20px;">
      <p style="color: var(--muted); margin: 0;">No client orders or commission requests received yet.</p>
    </div>
  <?php else: ?>
    <div style="overflow-x: auto;">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Reference</th>
            <th>Client Details</th>
            <th>Type / Requested Piece</th>
            <th>Reference Image</th>
            <th>Description &amp; Story</th>
            <th>Status</th>
            <th>Date</th>
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
              <td>
                <strong><?= e($order['customer_name']) ?></strong>
                <div style="font-size: 13px; color: var(--muted); margin-top: 2px;">
                  <a href="mailto:<?= e($order['email']) ?>" style="color:inherit;text-decoration:underline;"><?= e($order['email']) ?></a>
                </div>
                <?php 
                  $cleanPhone = preg_replace('/[^0-9]/', '', $order['phone']);
                ?>
                <div style="margin-top: 4px;">
                  <a href="https://wa.me/<?= $cleanPhone ?>" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:4px;color:#16a34a;font-size:12px;font-weight:600;">
                    <span>WhatsApp: <?= e($order['phone']) ?> &nearr;</span>
                  </a>
                </div>
              </td>
              <td>
                <span class="badge-pill" style="margin:0;padding:3px 10px;font-size:10.5px;">
                  <?= e($order['artwork_title'] ?: ucfirst($order['artwork_type'])) ?>
                </span>
              </td>
              <td style="text-align: center;">
                <?php if (!empty($order['reference_image'])): ?>
                  <a href="<?= url($order['reference_image']) ?>" target="_blank" title="View reference photo">
                    <img src="<?= url($order['reference_image']) ?>" alt="Reference" style="width: 48px; height: 48px; object-fit: cover; border-radius: var(--radius-xs); border: 1px solid var(--line);">
                    <small style="display:block;font-size:10px;color:var(--gold-dark);margin-top:2px;">View Photo &nearr;</small>
                  </a>
                <?php else: ?>
                  <span style="font-size: 12px; color: var(--muted-light);">None</span>
                <?php endif; ?>
              </td>
              <td style="max-width: 280px; font-size: 13px; color: var(--muted);">
                <?= nl2br(e($order['description'])) ?>
              </td>
              <td>
                <form method="post" style="display:flex;align-items:center;gap:8px;">
                  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                  <input type="hidden" name="id" value="<?= $order['id'] ?>">
                  <select name="status" onchange="this.form.submit()" style="padding: 6px 10px; font-size: 12px; border-radius: var(--radius-sm); border: 1px solid var(--line); font-weight: 600;">
                    <?php foreach (['new', 'reviewing', 'confirmed', 'completed', 'cancelled'] as $st): ?>
                      <option value="<?= $st ?>" <?= $order['status'] === $st ? 'selected' : '' ?>>
                        <?= ucfirst($st) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </form>
              </td>
              <td style="font-size: 13px; color: var(--muted); white-space: nowrap;">
                <?= date('M d, Y', strtotime($order['created_at'])) ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</main>
</body>
</html>

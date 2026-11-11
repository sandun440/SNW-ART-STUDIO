<?php
require_once __DIR__ . '/../config/config.php';
admin_required();

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    verify_csrf();
    $delId = (int)$_POST['delete_id'];
    db()->prepare('DELETE FROM contact_messages WHERE id = ?')->execute([$delId]);
    flash('success', 'Message deleted successfully.');
    redirect('admin/messages.php');
}

$messages = db()->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Client Inquiries &amp; Messages · <?= APP_NAME ?></title>
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
      Client Contact Messages
    </h1>
    <p class="lead">Inquiries submitted through the studio contact form.</p>
  </div>

  <!-- Admin Nav Tabs -->
  <nav class="filters" style="margin-bottom: 32px;">
    <a href="<?= url('admin/index.php') ?>">Overview</a>
    <a href="<?= url('admin/artworks.php') ?>">Artworks</a>
    <a href="<?= url('admin/orders.php') ?>">Commissions &amp; Orders</a>
    <a href="<?= url('admin/messages.php') ?>" class="active">Messages (<?= count($messages) ?>)</a>
    <a href="<?= url('admin/categories.php') ?>">Categories</a>
  </nav>

  <?php if ($msg = flash('success')): ?>
    <div class="alert success" style="margin-bottom: 24px;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
      <span><?= e($msg) ?></span>
    </div>
  <?php endif; ?>

  <?php if (empty($messages)): ?>
    <div class="notice" style="text-align: center; padding: 60px 20px; background: var(--paper); border: 1px solid var(--line); border-radius: var(--radius-md);">
      <p style="color: var(--muted); font-size: 16px; margin: 0;">No contact messages received yet.</p>
    </div>
  <?php else: ?>
    <div style="display: flex; flex-direction: column; gap: 20px;">
      <?php foreach ($messages as $m): ?>
        <div style="background: var(--paper); border: 1px solid var(--line); border-radius: var(--radius-md); padding: 26px 30px; box-shadow: var(--shadow-xs);">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px; margin-bottom: 14px; border-bottom: 1px solid var(--cream-subtle); padding-bottom: 14px;">
            <div>
              <h3 style="font-size: 17px; font-weight: 700; color: var(--ink); margin-bottom: 4px;"><?= e($m['subject']) ?></h3>
              <p style="font-size: 13.5px; color: var(--muted); margin: 0;">
                From: <strong style="color: var(--ink);"><?= e($m['name']) ?></strong> &lt;<a href="mailto:<?= e($m['email']) ?>" style="color: var(--gold-dark);"><?= e($m['email']) ?></a>&gt;
              </p>
            </div>
            <div style="text-align: right;">
              <span style="font-size: 12px; color: var(--muted); display: block; margin-bottom: 8px;">
                <?= date('M d, Y · h:i A', strtotime($m['created_at'])) ?>
              </span>
              <div style="display: flex; gap: 8px; justify-content: flex-end;">
                <a href="mailto:<?= e($m['email']) ?>?subject=Re: <?= urlencode($m['subject']) ?>" class="button light" style="padding: 6px 14px; font-size: 12px; gap: 6px;">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                  <span>Reply</span>
                </a>
                <form method="POST" onsubmit="return confirm('Delete this message?');" style="display: inline;">
                  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                  <input type="hidden" name="delete_id" value="<?= (int)$m['id'] ?>">
                  <button type="submit" class="button light" style="padding: 6px 14px; font-size: 12px; color: #dc2626; border-color: #fca5a5;">
                    Delete
                  </button>
                </form>
              </div>
            </div>
          </div>
          <div style="font-size: 14.5px; line-height: 1.65; color: var(--ink-soft); background: var(--cream); border: 1px solid var(--line); border-radius: 8px; padding: 14px 18px; white-space: pre-wrap;"><?= e($m['message']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <footer style="margin-top: 60px; padding: 24px 0; border-top: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center; font-size: 12.5px; color: var(--muted); flex-wrap: wrap; gap: 12px;">
    <div>SNW Art Studio · Internal Management Portal v1.0</div>
    <div>
      System Engineered &amp; Maintained by <a href="https://sandunsameera.vercel.app/" target="_blank" rel="noopener" style="color: var(--gold-dark); font-weight: 600; text-decoration: none;">Sandun Sameera ↗</a>
    </div>
  </footer>
</main>
</body>
</html>


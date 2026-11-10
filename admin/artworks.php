<?php
require_once __DIR__ . '/../config/config.php';
admin_required();

$error = null;
$edit = null;

if (isset($_GET['edit'])) {
    $s = db()->prepare('SELECT * FROM artworks WHERE id=?');
    $s->execute([(int)$_GET['edit']]);
    $edit = $s->fetch();
}

if (isset($_GET['delete'])) {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_GET['csrf'] ?? '')) {
        http_response_code(419);
        exit('Invalid request token.');
    }
    $s = db()->prepare('DELETE FROM artworks WHERE id=?');
    $s->execute([(int)$_GET['delete']]);
    flash('success', 'Artwork removed from studio collection.');
    redirect('admin/artworks.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = (int)($_POST['category_id'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));

    if ($title === '' || $description === '' || !$category || $price < 0) {
        $error = 'Please fill out all required fields with a valid price.';
    } else {
        try {
            $path = upload_image($_FILES['image'] ?? [], 'artworks');
            if ($id) {
                $sql = 'UPDATE artworks SET category_id=?, title=?, slug=?, description=?, price=?, featured=?';
                $params = [$category, $title, $slug, $description, $price, !empty($_POST['featured']) ? 1 : 0];
                if ($path) {
                    $sql .= ', image_path=?';
                    $params[] = $path;
                }
                $sql .= ' WHERE id=?';
                $params[] = $id;
                db()->prepare($sql)->execute($params);
                flash('success', 'Artwork updated successfully.');
            } else {
                $s = db()->prepare('INSERT INTO artworks (category_id, title, slug, description, price, image_path, featured) VALUES (?,?,?,?,?,?,?)');
                $s->execute([$category, $title, $slug, $description, $price, $path, !empty($_POST['featured']) ? 1 : 0]);
                flash('success', 'New artwork added to collection.');
            }
            redirect('admin/artworks.php');
        } catch (Throwable $e) {
            $error = $e instanceof RuntimeException ? $e->getMessage() : 'Could not save artwork.';
        }
    }
}

$categories = db()->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$artworks = db()->query('SELECT a.*, c.name category_name FROM artworks a JOIN categories c ON c.id=a.category_id ORDER BY a.created_at DESC')->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Manage Artworks · <?= APP_NAME ?></title>
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
      <?= $edit ? 'Edit Artwork: ' . e($edit['title']) : 'Add New Studio Artwork' ?>
    </h1>
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

  <!-- Add / Edit Form Card -->
  <div class="notice" style="background: var(--paper); border: 1.5px solid var(--line); border-radius: var(--radius-md); padding: 36px; box-shadow: var(--shadow-sm); margin-bottom: 50px;">
    <h3 style="font-family: var(--font-serif); font-size: 22px; margin-bottom: 20px;">
      <?= $edit ? 'Update Artwork Details' : 'New Piece Details' ?>
    </h3>

    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <input type="hidden" name="id" value="<?= e((string)($edit['id'] ?? 0)) ?>">

      <div class="form-grid">
        <div class="field">
          <label>Artwork Title *</label>
          <input type="text" name="title" required placeholder="e.g. The Quiet Gaze" value="<?= e($edit['title'] ?? '') ?>">
        </div>

        <div class="field">
          <label>Category *</label>
          <select name="category_id" required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $category): ?>
              <option value="<?= $category['id'] ?>" <?= ($edit['category_id'] ?? 0) == $category['id'] ? 'selected' : '' ?>>
                <?= e($category['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label>Price (<?= e(CURRENCY_SYMBOL) ?>) *</label>
          <input type="number" step="0.01" min="0" name="price" required placeholder="180.00" value="<?= e((string)($edit['price'] ?? '')) ?>">
        </div>

        <div class="field">
          <label>Artwork Image (JPG, PNG, WEBP)</label>
          <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
          <?php if (!empty($edit['image_path'])): ?>
            <small style="color:var(--muted);margin-top:6px;display:flex;align-items:center;gap:8px;">
              <span>Current:</span>
              <img src="<?= url($edit['image_path']) ?>" style="width:32px;height:32px;object-fit:cover;border-radius:4px;">
              <span><?= e($edit['image_path']) ?></span>
            </small>
          <?php endif; ?>
        </div>

        <div class="field full">
          <label>Description &amp; Notes *</label>
          <textarea name="description" required placeholder="Medium, paper type, inspiration, and mood..."><?= e($edit['description'] ?? '') ?></textarea>
        </div>

        <div class="field full">
          <label style="display:flex;align-items:center;gap:10px;cursor:pointer;text-transform:none;font-size:14px;letter-spacing:0;">
            <input type="checkbox" name="featured" value="1" <?= !empty($edit['featured']) ? 'checked' : '' ?> style="width:auto;margin:0;">
            <strong>Feature on Homepage Showcase</strong>
          </label>
        </div>
      </div>

      <div style="display: flex; gap: 14px; margin-top: 24px;">
        <button class="button gold" type="submit">
          <span><?= $edit ? 'Save Changes' : 'Publish Artwork' ?></span>
        </button>
        <?php if ($edit): ?>
          <a class="button light" href="<?= url('admin/artworks.php') ?>">Cancel</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- Artwork Collection List -->
  <section>
    <div class="section-head" style="margin-bottom: 20px;">
      <div>
        <p class="eyebrow">Studio Inventory</p>
        <h2 style="font-size: 28px;">Current collection (<?= count($artworks) ?>)</h2>
      </div>
    </div>

    <div style="overflow-x: auto;">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Preview</th>
            <th>Title</th>
            <th>Category</th>
            <th>Price</th>
            <th>Featured</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($artworks as $art): ?>
            <tr>
              <td style="width: 70px;">
                <?php if (!empty($art['image_path'])): ?>
                  <img src="<?= url($art['image_path']) ?>" alt="<?= e($art['title']) ?>" style="width: 54px; height: 54px; object-fit: cover; border-radius: var(--radius-xs);">
                <?php else: ?>
                  <div style="width:54px;height:54px;background:#e2ddd5;border-radius:var(--radius-xs);display:flex;align-items:center;justify-content:center;font-size:10px;color:var(--muted);">No img</div>
                <?php endif; ?>
              </td>
              <td>
                <strong style="font-size: 15px;"><?= e($art['title']) ?></strong>
                <small style="display:block;color:var(--muted);max-width:320px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                  <?= e($art['description']) ?>
                </small>
              </td>
              <td><?= e($art['category_name']) ?></td>
              <td><strong style="color:var(--gold-dark);font-family:var(--font-serif);"><?= format_price($art['price']) ?></strong></td>
              <td>
                <?php if (!empty($art['featured'])): ?>
                  <span class="status-badge status-confirmed">Featured</span>
                <?php else: ?>
                  <span style="color:var(--muted);font-size:12px;">Standard</span>
                <?php endif; ?>
              </td>
              <td>
                <div style="display: flex; gap: 8px;">
                  <a href="<?= url('admin/artworks.php?edit=' . (int)$art['id']) ?>" class="button light" style="padding: 6px 12px; font-size: 12px;">Edit</a>
                  <a href="<?= url('admin/artworks.php?delete=' . (int)$art['id'] . '&csrf=' . urlencode(csrf_token())) ?>" onclick="return confirm('Are you sure you want to delete this artwork?')" class="button light" style="padding: 6px 12px; font-size: 12px; color: #b91c1c; border-color: #fca5a5;">Delete</a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>
</body>
</html>

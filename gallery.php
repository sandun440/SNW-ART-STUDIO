<?php
require_once __DIR__ . '/config/config.php';
$category = $_GET['category'] ?? '';
$sql = "SELECT a.*, c.name category_name FROM artworks a JOIN categories c ON c.id=a.category_id";
$params = [];
if ($category !== '') {
    $sql .= " WHERE c.slug = ?";
    $params[] = $category;
}
$sql .= " ORDER BY a.created_at DESC";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$artworks = $stmt->fetchAll();

$categories = db()->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$totalCount = count($artworks);
$pageTitle = 'Original Collection';
require __DIR__ . '/components/header.php';
?>
<main class="container">
  <section class="page-intro">
    <p class="eyebrow">Studio Portfolio</p>
    <h1>The Original Collection</h1>
    <p class="lead">Original graphite works and fine portrait studies available directly from the easel. Each artwork is an authentic one-of-a-kind original delivered with a signed Certificate of Authenticity.</p>
  </section>

  <!-- Filter Pills -->
  <div class="filters">
    <a class="<?= $category === '' ? 'active' : '' ?>" href="<?= url('gallery.php') ?>">All Works (<?= db()->query("SELECT COUNT(*) FROM artworks")->fetchColumn() ?>)</a>
    <?php foreach ($categories as $item): 
      $countStmt = db()->prepare("SELECT COUNT(*) FROM artworks WHERE category_id=?");
      $countStmt->execute([$item['id']]);
      $catCount = $countStmt->fetchColumn();
    ?>
      <a class="<?= $category === $item['slug'] ? 'active' : '' ?>" href="<?= url('gallery.php?category=' . urlencode($item['slug'])) ?>">
        <?= e($item['name']) ?> (<?= $catCount ?>)
      </a>
    <?php endforeach; ?>
  </div>

  <?php if (empty($artworks)): ?>
    <div class="notice" style="text-align: center; padding: 60px 20px; margin: 40px 0;">
      <p class="eyebrow" style="justify-content: center;">Collection Update</p>
      <h3 style="font-family: var(--font-serif); font-size: 24px; margin: 10px 0;">No artworks in this category currently.</h3>
      <p class="lead" style="margin: 0 auto 20px; max-width: 440px;">New pieces are frequently added as they are completed. You can also commission a custom work crafted specifically for you.</p>
      <a href="<?= url('order.php') ?>" class="button gold">Commission a custom piece</a>
    </div>
  <?php else: ?>
    <div class="grid" style="margin-bottom: 80px;">
      <?php foreach ($artworks as $art): ?>
        <article class="card">
          <a href="<?= url('artwork.php?slug=' . urlencode($art['slug'])) ?>">
            <div class="art-image">
              <span class="art-category-pill"><?= e($art['category_name']) ?></span>
              <?php if (!empty($art['image_path'])): ?>
                <img src="<?= url($art['image_path']) ?>" alt="<?= e($art['title']) ?>" loading="lazy">
              <?php endif; ?>
            </div>
            <div class="art-body">
              <p class="eyebrow"><?= e($art['category_name']) ?></p>
              <h3><?= e($art['title']) ?></h3>
              <p><?= e($art['description']) ?></p>
              <div class="card-footer">
                <div class="price"><?= format_price($art['price']) ?></div>
                <span class="art-view-link">View details &rarr;</span>
              </div>
            </div>
          </a>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- Custom Commission Banner -->
  <section class="section" style="background: var(--cream-subtle); border-radius: var(--radius-lg); padding: 60px 48px; margin-bottom: 100px; text-align: center;">
    <p class="eyebrow" style="justify-content: center;">Have a specific vision?</p>
    <h2 style="font-size: clamp(28px, 4vw, 42px); margin-bottom: 14px;">Didn't find the exact piece you're seeking?</h2>
    <p class="lead" style="margin: 0 auto 30px; max-width: 580px;">Commission a personal portrait from your favorite photograph or a custom graphite concept crafted directly by the artist.</p>
    <a href="<?= url('order.php') ?>" class="button gold">
      <span>Start a bespoke commission</span>
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
    </a>
  </section>
</main>
<?php require __DIR__ . '/components/footer.php'; ?>

<?php
require_once __DIR__ . '/config/config.php';
$slug = trim($_GET['slug'] ?? '');
$stmt = db()->prepare("SELECT a.*, c.name category_name FROM artworks a JOIN categories c ON c.id=a.category_id WHERE a.slug=?");
$stmt->execute([$slug]);
$art = $stmt->fetch();

if (!$art) {
    http_response_code(404);
    $pageTitle = 'Artwork Not Found';
    require __DIR__ . '/components/header.php';
    echo '<main class="container section" style="text-align:center;"><p class="eyebrow">404 Error</p><h2>Artwork Not Found</h2><p class="lead" style="margin:16px auto 24px;">The piece you are looking for may have been moved or acquired.</p><a href="' . url('gallery.php') . '" class="button">Browse Collection</a></main>';
    require __DIR__ . '/components/footer.php';
    exit;
}

$pageTitle = $art['title'];

// Fetch 3 related pieces
$relatedStmt = db()->prepare("SELECT a.*, c.name category_name FROM artworks a JOIN categories c ON c.id=a.category_id WHERE a.id != ? ORDER BY RAND() LIMIT 3");
$relatedStmt->execute([$art['id']]);
$related = $relatedStmt->fetchAll();

require __DIR__ . '/components/header.php';
?>
<main class="container">
  <!-- Breadcrumb -->
  <div style="padding-top: 36px; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--muted);">
    <a href="<?= url('index.php') ?>">Home</a>
    <span>&rsaquo;</span>
    <a href="<?= url('gallery.php') ?>">Gallery</a>
    <span>&rsaquo;</span>
    <span style="color: var(--ink); font-weight: 500;"><?= e($art['title']) ?></span>
  </div>

  <section class="detail">
    <!-- Left: Artwork Image Showcase -->
    <div class="detail-image">
      <?php if (!empty($art['image_path'])): ?>
        <img src="<?= url($art['image_path']) ?>" alt="<?= e($art['title']) ?>" id="artMainImage">
      <?php else: ?>
        <div style="height: 500px; display:flex; align-items:center; justify-content:center; color: var(--muted-light);">
          <span>No image available</span>
        </div>
      <?php endif; ?>
    </div>

    <!-- Right: Artwork Metadata & Inquiries -->
    <div class="detail-copy">
      <p class="eyebrow"><?= e($art['category_name']) ?></p>
      <h1><?= e($art['title']) ?></h1>
      <div class="price"><?= format_price($art['price']) ?></div>
      
      <p class="lead" style="font-size: 15.5px; margin-bottom: 24px;">
        <?= nl2br(e($art['description'])) ?>
      </p>

      <!-- Specifications Grid -->
      <div class="detail-specs">
        <div class="spec-item">
          <strong>Medium</strong>
          <span>Charcoal &amp; Graphite (2B–8B)</span>
        </div>
        <div class="spec-item">
          <strong>Paper</strong>
          <span>300gsm Archival Cotton</span>
        </div>
        <div class="spec-item">
          <strong>Authenticity</strong>
          <span>Hand-signed Certificate</span>
        </div>
        <div class="spec-item">
          <strong>Packaging</strong>
          <span>Reinforced Protective Casing</span>
        </div>
      </div>

      <div class="notice" style="margin: 20px 0 28px; padding: 18px 20px;">
        <h4 style="font-size: 14.5px; font-weight: 600; color: var(--ink); margin-bottom: 4px;">Direct Studio Purchase</h4>
        <p style="font-size: 13px; color: var(--muted); margin: 0;">Submit your request below. We will confirm reserve status and arrange custom framing and insured shipping details directly with you.</p>
      </div>

      <div class="detail-actions">
        <a class="button gold" href="<?= url('order.php?artwork=' . (int)$art['id']) ?>">
          <span>Request This Artwork</span>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </a>

        <?php 
        $waMsg = "Hello SNW Art Studio! I am interested in purchasing the artwork: '" . $art['title'] . "' (" . format_price($art['price']) . "). Is it currently available?";
        ?>
        <a class="button light" href="https://wa.me/<?= WHATSAPP_NUMBER ?>?text=<?= urlencode($waMsg) ?>" target="_blank" rel="noopener" style="display:flex;align-items:center;gap:8px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="#25d366"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.18-2.587-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.634.07-.98-.073-.679-.278-1.574-.916-2.316-1.658-.87-.87-1.428-1.89-1.688-2.339-.144-.249-.015-.386.109-.51.111-.112.253-.29.38-.435.127-.144.17-.247.255-.411.085-.164.042-.307-.021-.433-.063-.127-.571-1.378-.782-1.89-.205-.499-.413-.431-.571-.439-.147-.008-.316-.01-.485-.01s-.444.063-.677.317c-.233.254-.889.868-.889 2.117 0 1.249.91 2.455 1.037 2.625.127.17 1.79 2.733 4.336 3.832.606.262 1.079.418 1.448.535.609.193 1.163.166 1.601.1.488-.074 1.503-.614 1.714-1.207.212-.593.212-1.101.149-1.207-.064-.106-.233-.17-.487-.297z"/></svg>
          <span>Ask Artist on WhatsApp</span>
        </a>
      </div>
    </div>
  </section>

  <!-- More Pieces from Studio -->
  <?php if (!empty($related)): ?>
    <section class="section" style="border-top: 1px solid var(--line); padding-top: 70px;">
      <div class="section-head">
        <div>
          <p class="eyebrow">Studio Easel</p>
          <h2>More works you may appreciate</h2>
        </div>
        <a href="<?= url('gallery.php') ?>" class="button light">View Collection</a>
      </div>

      <div class="grid">
        <?php foreach ($related as $item): ?>
          <article class="card">
            <a href="<?= url('artwork.php?slug=' . urlencode($item['slug'])) ?>">
              <div class="art-image">
                <span class="art-category-pill"><?= e($item['category_name']) ?></span>
                <?php if (!empty($item['image_path'])): ?>
                  <img src="<?= url($item['image_path']) ?>" alt="<?= e($item['title']) ?>" loading="lazy">
                <?php endif; ?>
              </div>
              <div class="art-body">
                <p class="eyebrow"><?= e($item['category_name']) ?></p>
                <h3><?= e($item['title']) ?></h3>
                <p><?= e($item['description']) ?></p>
                <div class="card-footer">
                  <div class="price"><?= format_price($item['price']) ?></div>
                  <span class="art-view-link">View details &rarr;</span>
                </div>
              </div>
            </a>
          </article>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>
</main>
<?php require __DIR__ . '/components/footer.php'; ?>

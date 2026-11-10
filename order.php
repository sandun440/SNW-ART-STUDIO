<?php
require_once __DIR__ . '/config/config.php';
$errors = [];
$artworkId = (int)($_GET['artwork'] ?? $_POST['artwork_id'] ?? 0);
$artwork = null;

if ($artworkId) {
    $s = db()->prepare('SELECT * FROM artworks WHERE id=?');
    $s->execute([$artworkId]);
    $artwork = $s->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['customer_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $type = $_POST['artwork_type'] ?? '';
    $description = trim($_POST['description'] ?? '');

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '' || $description === '' || !in_array($type, ['existing', 'pencil', 'portrait'], true)) {
        $errors[] = 'Please fill out all required fields with a valid email address.';
    }

    if (!$errors) {
        try {
            $ref = 'SNW-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
            $s = db()->prepare('INSERT INTO orders (reference_no, artwork_id, customer_name, email, phone, artwork_type, description) VALUES (?,?,?,?,?,?,?)');
            $s->execute([$ref, $artworkId ?: null, $name, $email, $phone, $type, $description]);
            $orderId = (int)db()->lastInsertId();

            if (!empty($_FILES['reference_image']['name'])) {
                $path = upload_image($_FILES['reference_image'], 'orders');
                db()->prepare('INSERT INTO order_images (order_id, image_path) VALUES (?,?)')->execute([$orderId, $path]);
            }

            redirect('confirmation.php?ref=' . urlencode($ref));
        } catch (Throwable $e) {
            $errors[] = $e instanceof RuntimeException ? $e->getMessage() : 'Unable to submit the request right now. Please try again or message via WhatsApp.';
        }
    }
}

$pageTitle = 'Commission a Custom Artwork';
require __DIR__ . '/components/header.php';
?>
<main class="container">
  <section class="page-intro">
    <p class="eyebrow">Bespoke Commissions</p>
    <h1>Commission a Personal Piece</h1>
    <p class="lead">Share your memory, reference photograph, or artistic vision. There is no payment required at this stage — the artist personally reviews every request and follows up via WhatsApp or email with recommendations, timeline, and an exact quote.</p>
  </section>

  <div class="split" style="align-items: start; gap: 60px; margin-bottom: 90px;">
    <!-- Left: Commission Form -->
    <div class="form-wrap">
      <?php if (!empty($errors)): ?>
        <div class="alert error" style="margin-bottom: 24px;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
          <div>
            <?php foreach ($errors as $error): ?>
              <div><?= e($error) ?></div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data" id="commissionForm">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <input type="hidden" name="artwork_id" value="<?= $artworkId ?>">

        <!-- Step 1: Artwork Type Selection -->
        <div style="margin-bottom: 28px;">
          <label style="display:block;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink);margin-bottom:12px;">1. Choose Commission Type</label>
          <div class="type-selector">
            <label class="type-card <?= (!$artwork && (old('artwork_type') === 'portrait' || empty(old('artwork_type')))) ? 'active' : '' ?>">
              <input type="radio" name="artwork_type" value="portrait" <?= (!$artwork && (old('artwork_type') === 'portrait' || empty(old('artwork_type')))) ? 'checked' : '' ?>>
              <div style="font-size: 24px; margin-bottom: 6px;">🎨</div>
              <strong>Portrait Art</strong>
              <small>Intimate charcoal &amp; graphite faces</small>
            </label>

            <label class="type-card <?= (!$artwork && old('artwork_type') === 'pencil') ? 'active' : '' ?>">
              <input type="radio" name="artwork_type" value="pencil" <?= (!$artwork && old('artwork_type') === 'pencil') ? 'checked' : '' ?>>
              <div style="font-size: 24px; margin-bottom: 6px;">✏️</div>
              <strong>Pencil Drawing</strong>
              <small>Figures, gestures, memories &amp; pets</small>
            </label>

            <label class="type-card <?= ($artwork || old('artwork_type') === 'existing') ? 'active' : '' ?>">
              <input type="radio" name="artwork_type" value="existing" <?= ($artwork || old('artwork_type') === 'existing') ? 'checked' : '' ?>>
              <div style="font-size: 24px; margin-bottom: 6px;">🖼️</div>
              <strong>Existing Work</strong>
              <small><?= $artwork ? e($artwork['title']) : 'Claim an easel original' ?></small>
            </label>
          </div>
        </div>

        <!-- Step 2: Contact Info -->
        <div class="form-grid" style="margin-bottom: 24px;">
          <div class="field">
            <label>Full Name *</label>
            <input type="text" name="customer_name" required placeholder="e.g. Kasun Perera" value="<?= old('customer_name') ?>">
          </div>

          <div class="field">
            <label>Email Address *</label>
            <input type="email" name="email" required placeholder="e.g. kasun@example.com" value="<?= old('email') ?>">
          </div>

          <div class="field full">
            <label>WhatsApp / Phone Number *</label>
            <input type="tel" name="phone" required placeholder="e.g. +94 71 234 5678" value="<?= old('phone') ?>">
            <small style="color:var(--muted);font-size:12px;margin-top:4px;">We share milestone progress photos and draft previews directly via WhatsApp.</small>
          </div>
        </div>

        <!-- Step 3: Reference & Story -->
        <div class="field full" style="margin-bottom: 24px;">
          <label>Tell Us Your Story &amp; Details *</label>
          <textarea name="description" required placeholder="Who or what would you like drawn? Mention special instructions (desired paper size A4/A3/A2, background elements, lighting mood, or special dedication)."><?= old('description') ?></textarea>
        </div>

        <!-- Modern Upload Box with Live Preview -->
        <div class="field full" style="margin-bottom: 32px;">
          <label>Upload Reference Photograph (Optional)</label>
          <div class="file-dropzone" id="dropzone">
            <input type="file" name="reference_image" id="reference_image" accept="image/jpeg,image/png,image/webp">
            <div class="dropzone-icon">📷</div>
            <div class="dropzone-text">
              <strong id="dropzoneLabel">Click or drag &amp; drop your reference photo</strong>
              <small>Supports high-resolution JPG, PNG or WEBP up to 8 MB</small>
            </div>
            <div class="file-preview" id="filePreview">
              <img id="previewImg" src="" alt="Photo preview">
              <span id="previewName"></span>
            </div>
          </div>
        </div>

        <button class="button gold" type="submit" style="width: 100%; padding: 18px; font-size: 15px;">
          <span>Submit Commission Request</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </button>
      </form>
    </div>

    <!-- Right: Studio Process & Guarantees -->
    <div>
      <div class="notice" style="margin-top: 0; background: var(--paper); border: 1px solid var(--line); box-shadow: var(--shadow-sm);">
        <p class="eyebrow">Studio Transparency</p>
        <h3 style="font-family: var(--font-serif); font-size: 22px; margin-bottom: 14px;">What happens after you submit?</h3>
        
        <div style="display: flex; flex-direction: column; gap: 18px; margin-top: 18px;">
          <div style="display: flex; gap: 14px; align-items: start;">
            <div style="width: 28px; height: 28px; border-radius: var(--radius-full); background: var(--gold-light); color: var(--gold-dark); font-weight: 700; font-size: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">1</div>
            <div>
              <strong style="display: block; font-size: 14px; color: var(--ink);">Quality Review</strong>
              <span style="font-size: 13px; color: var(--muted);">We review your reference photograph for clarity, contrast, and facial details.</span>
            </div>
          </div>

          <div style="display: flex; gap: 14px; align-items: start;">
            <div style="width: 28px; height: 28px; border-radius: var(--radius-full); background: var(--gold-light); color: var(--gold-dark); font-weight: 700; font-size: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">2</div>
            <div>
              <strong style="display: block; font-size: 14px; color: var(--ink);">Consultation &amp; Quote</strong>
              <span style="font-size: 13px; color: var(--muted);">We contact you on WhatsApp with composition suggestions, sizing options, and an exact timeline and quote.</span>
            </div>
          </div>

          <div style="display: flex; gap: 14px; align-items: start;">
            <div style="width: 28px; height: 28px; border-radius: var(--radius-full); background: var(--gold-light); color: var(--gold-dark); font-weight: 700; font-size: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">3</div>
            <div>
              <strong style="display: block; font-size: 14px; color: var(--ink);">Progress Milestones</strong>
              <span style="font-size: 13px; color: var(--muted);">You receive step-by-step WIP photos. You approve the final artwork before it is sealed and shipped.</span>
            </div>
          </div>

          <div style="display: flex; gap: 14px; align-items: start;">
            <div style="width: 28px; height: 28px; border-radius: var(--radius-full); background: var(--gold-light); color: var(--gold-dark); font-weight: 700; font-size: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">4</div>
            <div>
              <strong style="display: block; font-size: 14px; color: var(--ink);">Bank Payment &amp; Confirmation</strong>
              <span style="font-size: 13px; color: var(--muted);">Direct bank transfer details are shown on the next page. A 50% advance confirms your easel booking.</span>
            </div>
          </div>
        </div>

        <div style="margin-top: 28px; padding-top: 20px; border-top: 1px solid var(--line); font-size: 13px; color: var(--muted);">
          <strong style="color: var(--ink); display: block; margin-bottom: 6px;">Need advice before submitting?</strong>
          You can chat directly with the artist right now:
          <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>" target="_blank" rel="noopener" style="color: #25d366; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; margin-top: 6px;">
            <span>Message on WhatsApp &rarr;</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</main>
<?php require __DIR__ . '/components/footer.php'; ?>

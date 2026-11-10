<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/helpers.php';

$pageTitle = 'Track Your Order';
$refInput = trim($_GET['ref'] ?? $_POST['ref'] ?? '');
$phoneInput = trim($_POST['phone'] ?? '');

$order = null;
$error = '';
$searched = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || !empty($_GET['ref'])) {
    $searched = true;
    if (empty($refInput)) {
        $error = 'Please enter your Order Reference Number.';
    } else {
        try {
            // If phone provided, verify both for maximum privacy. If only ref provided via GET, prompt for phone or allow direct view if coming from confirmation.
            if (!empty($phoneInput)) {
                $cleanPhone = preg_replace('/[^0-9]/', '', $phoneInput);
                $stmt = $pdo->prepare("
                    SELECT o.*, a.title AS artwork_title, a.image_path AS artwork_image
                    FROM orders o
                    LEFT JOIN artworks a ON o.artwork_id = a.id
                    WHERE o.reference_no = :ref
                    LIMIT 1
                ");
                $stmt->execute(['ref' => $refInput]);
                $candidate = $stmt->fetch();

                if ($candidate) {
                    $orderPhoneClean = preg_replace('/[^0-9]/', '', $candidate['phone']);
                    // Match last 4 digits or full phone for flexibility
                    if (str_ends_with($orderPhoneClean, substr($cleanPhone, -4)) || str_contains($orderPhoneClean, $cleanPhone)) {
                        $order = $candidate;
                    } else {
                        $error = 'The phone number does not match this order reference. Please check and try again.';
                    }
                } else {
                    $error = 'No order found with reference number: ' . htmlspecialchars($refInput);
                }
            } else {
                // If only ref entered without phone
                $stmt = $pdo->prepare("
                    SELECT o.*, a.title AS artwork_title, a.image_path AS artwork_image
                    FROM orders o
                    LEFT JOIN artworks a ON o.artwork_id = a.id
                    WHERE o.reference_no = :ref
                    LIMIT 1
                ");
                $stmt->execute(['ref' => $refInput]);
                $candidate = $stmt->fetch();
                if ($candidate) {
                    // For privacy, require entering phone number to unlock full view
                    $promptPhone = true;
                } else {
                    $error = 'No order found with reference number: ' . htmlspecialchars($refInput);
                }
            }
        } catch (Throwable $e) {
            $error = 'Unable to look up order at this moment. Please reach out via WhatsApp.';
        }
    }
}

// Fetch images if order unlocked
$orderImages = [];
if ($order) {
    $imgStmt = $pdo->prepare("SELECT image_path FROM order_images WHERE order_id = :id");
    $imgStmt->execute(['id' => $order['id']]);
    $orderImages = $imgStmt->fetchAll(PDO::FETCH_COLUMN);
}

// Status mapping
$statusSteps = [
    'new' => ['step' => 1, 'label' => 'Order Received', 'desc' => 'We have logged your commission details and reference photos.'],
    'reviewing' => ['step' => 2, 'label' => 'Studio Review', 'desc' => 'The artist is reviewing composition, lighting and specifications.'],
    'confirmed' => ['step' => 3, 'label' => 'On Easel (In Progress)', 'desc' => 'Work has begun! Layered graphite/charcoal is being rendered.'],
    'completed' => ['step' => 4, 'label' => 'Completed & Dispatched', 'desc' => 'Artwork is finished, archival spray protected, and packed for safe delivery.'],
    'cancelled' => ['step' => 0, 'label' => 'Order Closed', 'desc' => 'This commission has been cancelled or refunded.']
];

$currentStatus = $order ? ($statusSteps[$order['status']] ?? $statusSteps['new']) : null;

require __DIR__ . '/components/header.php';
?>

<main class="page-content">
  <section class="section" style="padding-top: 50px;">
    <div class="container" style="max-width: 820px;">
      
      <div class="section-head" style="text-align: center; margin-bottom: 36px;">
        <div>
          <p class="eyebrow">Studio Client Portal</p>
          <h1 style="font-size: clamp(2rem, 3.5vw, 2.8rem);">Track your commission</h1>
          <p style="color: var(--text-muted); max-width: 520px; margin: 12px auto 0;">
            Follow the live progress of your bespoke graphite drawing from initial sketch to final archival dispatch.
          </p>
        </div>
      </div>

      <!-- Tracking Search Form -->
      <div class="card" style="padding: 28px 32px; margin-bottom: 36px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px;">
        <form method="POST" action="<?= url('track.php') ?>" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 16px; align-items: end;">
          <div>
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--gold-primary); margin-bottom: 8px;">Order Reference</label>
            <input type="text" name="ref" value="<?= e($refInput) ?>" placeholder="e.g. SNW-20261110-XXXX" required style="width: 100%; padding: 12px 16px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.12); border-radius: 8px; color: #fff; font-family: monospace; font-size: 0.95rem;">
          </div>
          <div>
            <label style="display: block; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--gold-primary); margin-bottom: 8px;">Phone Number (Verification)</label>
            <input type="text" name="phone" value="<?= e($phoneInput) ?>" placeholder="Your contact phone" required style="width: 100%; padding: 12px 16px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.12); border-radius: 8px; color: #fff; font-size: 0.95rem;">
          </div>
          <div>
            <button type="submit" class="button primary" style="height: 48px; padding: 0 24px; white-space: nowrap; width: 100%;">
              <span>Track Order</span>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </button>
          </div>
        </form>

        <?php if (!empty($promptPhone) && empty($order)): ?>
          <div style="margin-top: 18px; padding: 14px 18px; background: rgba(197, 160, 89, 0.12); border-left: 3px solid var(--gold-primary); border-radius: 6px; font-size: 0.9rem; color: #f1dfbe;">
            🔒 For your privacy, please enter the phone number associated with this order to view live progress.
          </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
          <div style="margin-top: 18px; padding: 14px 18px; background: rgba(239, 68, 68, 0.12); border-left: 3px solid #ef4444; border-radius: 6px; font-size: 0.9rem; color: #fca5a5;">
            <?= e($error) ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- Result Card -->
      <?php if ($order): ?>
        <div class="card" style="padding: 36px; background: rgba(255,255,255,0.02); border: 1px solid rgba(197,160,89,0.25); border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.4);">
          
          <!-- Header Bar -->
          <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px; padding-bottom: 24px; border-bottom: 1px solid rgba(255,255,255,0.08);">
            <div>
              <span style="display: inline-block; padding: 4px 12px; background: rgba(197,160,89,0.15); border: 1px solid rgba(197,160,89,0.3); border-radius: 999px; color: var(--gold-primary); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600; margin-bottom: 8px;">
                Commission Order
              </span>
              <h2 style="font-family: monospace; font-size: 1.6rem; color: #fff; margin: 0; letter-spacing: 0.05em;">
                <?= e($order['reference_no']) ?>
              </h2>
              <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 4px;">
                Placed on <?= date('F j, Y', strtotime($order['created_at'])) ?> · Client: <strong><?= e(explode(' ', $order['customer_name'])[0]) ?></strong>
              </p>
            </div>
            
            <div style="text-align: right;">
              <span style="display: inline-block; padding: 8px 18px; background: <?= $order['status'] === 'completed' ? 'rgba(34, 197, 94, 0.2)' : 'rgba(197, 160, 89, 0.2)' ?>; border: 1px solid <?= $order['status'] === 'completed' ? 'rgba(34, 197, 94, 0.4)' : 'rgba(197, 160, 89, 0.4)' ?>; border-radius: 999px; font-size: 0.85rem; font-weight: 600; color: <?= $order['status'] === 'completed' ? '#4ade80' : 'var(--gold-primary)' ?>;">
                ● <?= e($currentStatus['label']) ?>
              </span>
            </div>
          </div>

          <!-- Progress Stepper -->
          <div style="padding: 36px 0; border-bottom: 1px solid rgba(255,255,255,0.08);">
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; position: relative;">
              <?php 
              $currentStepNum = $currentStatus['step'];
              $steps = [
                  1 => ['label' => 'Received', 'desc' => 'Order logged'],
                  2 => ['label' => 'Studio Review', 'desc' => 'Evaluating composition'],
                  3 => ['label' => 'On Easel', 'desc' => 'Drawing in progress'],
                  4 => ['label' => 'Completed', 'desc' => 'Ready / Dispatched']
              ];
              foreach ($steps as $sNum => $sInfo): 
                  $isDone = ($sNum < $currentStepNum);
                  $isCurrent = ($sNum === $currentStepNum);
              ?>
                <div style="text-align: center; position: relative;">
                  <div style="width: 42px; height: 42px; margin: 0 auto 12px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; transition: all 0.3s;
                    <?= $isDone ? 'background: var(--gold-primary); color: #000; box-shadow: 0 0 15px rgba(197,160,89,0.4);' : ($isCurrent ? 'background: #fff; color: #000; border: 3px solid var(--gold-primary); box-shadow: 0 0 20px rgba(255,255,255,0.4);' : 'background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.3); border: 1px solid rgba(255,255,255,0.1);') ?>">
                    <?php if ($isDone): ?>
                      ✓
                    <?php else: ?>
                      <?= $sNum ?>
                    <?php endif; ?>
                  </div>
                  <div style="font-size: 0.85rem; font-weight: <?= $isCurrent ? '700' : '500' ?>; color: <?= $isCurrent ? '#fff' : ($isDone ? 'var(--gold-primary)' : 'rgba(255,255,255,0.4)') ?>;">
                    <?= $sInfo['label'] ?>
                  </div>
                  <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 4px; display: none; @media(min-width:600px){display:block;}">
                    <?= $sInfo['desc'] ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Commission Details Grid -->
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; padding-top: 28px;">
            <div>
              <p style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--gold-primary); margin-bottom: 6px;">Commission Spec</p>
              <p style="font-size: 1.05rem; color: #fff; font-weight: 500; margin: 0;">
                <?php if ($order['artwork_type'] === 'existing' && !empty($order['artwork_title'])): ?>
                  Original Artwork: “<?= e($order['artwork_title']) ?>”
                <?php elseif ($order['artwork_type'] === 'portrait'): ?>
                  Bespoke Handcrafted Portrait
                <?php else: ?>
                  Custom Fine Art Pencil Drawing
                <?php endif; ?>
              </p>
              <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 8px; line-height: 1.5;">
                <?= nl2br(e($order['description'])) ?>
              </p>
            </div>

            <div>
              <p style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--gold-primary); margin-bottom: 6px;">Artist Direct Line</p>
              <p style="color: var(--text-muted); font-size: 0.88rem; line-height: 1.6; margin-bottom: 16px;">
                Have questions or need to add reference notes? Connect directly with the studio via WhatsApp.
              </p>
              <?php 
              $waMsg = urlencode("Hello SNW Art Studio! I am inquiring about my commission order #" . $order['reference_no'] . ".");
              ?>
              <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', STUDIO_WHATSAPP) ?>?text=<?= $waMsg ?>" target="_blank" rel="noopener" class="button light" style="width: 100%; justify-content: center; gap: 10px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#25D366" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                <span>Message Artist on WhatsApp</span>
              </a>
            </div>
          </div>

        </div>
      <?php endif; ?>

    </div>
  </section>
</main>

<style>
@media (max-width: 700px) {
  form[action*="track.php"] {
    grid-template-columns: 1fr !important;
  }
}
</style>

<?php require __DIR__ . '/components/footer.php'; ?>

<?php
require_once __DIR__ . '/config/config.php';

$pageTitle = 'Track Your Order';
$refInput = trim($_GET['ref'] ?? $_POST['ref'] ?? '');
$phoneInput = trim($_POST['phone'] ?? '');

$order = null;
$error = '';
$searched = false;
$promptPhone = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || !empty($_GET['ref'])) {
    $searched = true;
    if (empty($refInput)) {
        $error = 'Please enter your Order Reference Number.';
    } else {
        try {
            if (!empty($phoneInput)) {
                $cleanPhone = preg_replace('/[^0-9]/', '', $phoneInput);
                $stmt = db()->prepare("
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
                    if (str_ends_with($orderPhoneClean, substr($cleanPhone, -4)) || str_contains($orderPhoneClean, $cleanPhone)) {
                        $order = $candidate;
                    } else {
                        $error = 'The phone number does not match this order reference. Please check and try again.';
                    }
                } else {
                    $error = 'No order found with reference number: ' . htmlspecialchars($refInput);
                }
            } else {
                $stmt = db()->prepare("
                    SELECT o.*, a.title AS artwork_title, a.image_path AS artwork_image
                    FROM orders o
                    LEFT JOIN artworks a ON o.artwork_id = a.id
                    WHERE o.reference_no = :ref
                    LIMIT 1
                ");
                $stmt->execute(['ref' => $refInput]);
                $candidate = $stmt->fetch();
                if ($candidate) {
                    $promptPhone = true;
                } else {
                    $error = 'No order found with reference number: ' . htmlspecialchars($refInput);
                }
            }
        } catch (Throwable $e) {
            $error = 'Unable to look up order: ' . htmlspecialchars($e->getMessage());
        }
    }
}

// Fetch images if order unlocked
$orderImages = [];
if ($order) {
    $imgStmt = db()->prepare("SELECT image_path FROM order_images WHERE order_id = :id");
    $imgStmt->execute(['id' => $order['id']]);
    $orderImages = $imgStmt->fetchAll(PDO::FETCH_COLUMN);
}

// Status mapping
$statusSteps = [
    'new' => ['step' => 1, 'label' => 'Order Received', 'desc' => 'We have logged your commission details and specifications.'],
    'reviewing' => ['step' => 2, 'label' => 'Studio Review', 'desc' => 'The artist is reviewing composition, lighting and sketch balance.'],
    'confirmed' => ['step' => 3, 'label' => 'On Easel (In Progress)', 'desc' => 'Graphite work has begun on archival cotton paper.'],
    'completed' => ['step' => 4, 'label' => 'Completed & Dispatched', 'desc' => 'Finished with archival fixative spray and safely packaged.'],
    'cancelled' => ['step' => 0, 'label' => 'Order Closed', 'desc' => 'This commission has been cancelled or refunded.']
];

$currentStatus = $order ? ($statusSteps[$order['status']] ?? $statusSteps['new']) : null;

require __DIR__ . '/components/header.php';
?>

<main class="page-content" style="background-color: var(--cream); min-height: 80vh; padding: 40px 0 90px;">
  <div class="container" style="max-width: 860px;">
    
    <div style="text-align: center; margin-bottom: 34px;">
      <p class="eyebrow" style="color: var(--gold-dark); font-weight: 600;">Studio Client Portal</p>
      <h1 style="font-family: var(--font-serif); font-size: clamp(2.2rem, 4vw, 3rem); color: var(--ink); margin: 6px 0 12px; font-weight: 600;">Track your commission</h1>
      <p style="color: var(--muted); max-width: 540px; margin: 0 auto; font-size: 15px; line-height: 1.6;">
        Check live easel progress, composition review, and dispatch schedule of your bespoke portrait.
      </p>
    </div>

    <!-- Tracking Search Form -->
    <div style="background: #ffffff; border: 1px solid var(--line); border-radius: 18px; padding: 28px 32px; box-shadow: var(--shadow-sm); margin-bottom: 34px;">
      <form method="POST" action="<?= url('track.php') ?>" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 16px; align-items: end;">
        <div>
          <label style="display: block; font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--gold-dark); font-weight: 700; margin-bottom: 8px;">Order Reference</label>
          <input type="text" name="ref" value="<?= e($refInput) ?>" placeholder="e.g. SNW-261110-XXXX" required style="width: 100%; padding: 12px 16px; background: #faf8f5; border: 1.5px solid #ded6c9; border-radius: 10px; color: var(--ink); font-family: monospace; font-size: 15px; font-weight: 600; outline: none; transition: border-color 0.2s;">
        </div>
        <div>
          <label style="display: block; font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--gold-dark); font-weight: 700; margin-bottom: 8px;">Phone Number (Verification)</label>
          <input type="text" name="phone" value="<?= e($phoneInput) ?>" placeholder="e.g. 077XXXXXXX" required style="width: 100%; padding: 12px 16px; background: #faf8f5; border: 1.5px solid #ded6c9; border-radius: 10px; color: var(--ink); font-size: 15px; font-weight: 500; outline: none; transition: border-color 0.2s;">
        </div>
        <div>
          <button type="submit" class="button gold" style="height: 48px; padding: 0 24px; white-space: nowrap; width: 100%; font-size: 14.5px; font-weight: 600;">
            <span>Track Order</span>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          </button>
        </div>
      </form>

      <?php if (!empty($promptPhone) && empty($order)): ?>
        <div style="margin-top: 20px; padding: 14px 18px; background: #fdf8ee; border-left: 4px solid var(--gold); border-radius: 8px; font-size: 13.5px; color: #82601d; line-height: 1.5;">
          🔒 <strong>Verification Required:</strong> Please enter the phone number you used when placing this order to view live easel progress.
        </div>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <div style="margin-top: 20px; padding: 14px 18px; background: #fef2f2; border-left: 4px solid #ef4444; border-radius: 8px; font-size: 13.5px; color: #991b1b; line-height: 1.5;">
          <?= e($error) ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Result Card -->
    <?php if ($order): ?>
      <div style="background: #ffffff; border: 1px solid #e4dbcf; border-radius: 22px; padding: 36px 40px; box-shadow: var(--shadow-md); margin-bottom: 40px;">
        
        <!-- Header Bar -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px; padding-bottom: 24px; border-bottom: 1px solid #eee7dc;">
          <div>
            <span style="display: inline-block; padding: 4px 12px; background: #fbf7ee; border: 1px solid #e8d7b3; border-radius: 999px; color: #8e6822; font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; margin-bottom: 10px;">
              Commission Order
            </span>
            <h2 style="font-family: monospace; font-size: 2rem; color: var(--ink); margin: 0; letter-spacing: 0.04em; font-weight: 700;">
              <?= e($order['reference_no']) ?>
            </h2>
            <p style="color: var(--muted); font-size: 14px; margin-top: 6px;">
              Placed on <?= date('F j, Y', strtotime($order['created_at'])) ?> · Client: <strong style="color: var(--ink); font-weight: 600;"><?= e($order['customer_name']) ?></strong>
            </p>
          </div>
          
          <div>
            <span style="display: inline-flex; align-items: center; gap: 8px; padding: 9px 20px; background: <?= $order['status'] === 'completed' ? '#ecfdf5' : '#fbf6ec' ?>; border: 1.5px solid <?= $order['status'] === 'completed' ? '#a7f3d0' : '#e6d3ad' ?>; border-radius: 999px; font-size: 13.5px; font-weight: 700; color: <?= $order['status'] === 'completed' ? '#15803d' : '#8a621a' ?>;">
              <span style="width: 8px; height: 8px; border-radius: 50%; background: currentColor;"></span>
              <span><?= e($currentStatus['label']) ?></span>
            </span>
          </div>
        </div>

        <!-- Progress Stepper -->
        <div style="padding: 38px 0; border-bottom: 1px solid #eee7dc;">
          <div style="position: relative;">
            
            <!-- Background connecting line -->
            <div style="position: absolute; top: 22px; left: 10%; right: 10%; height: 4px; background: #eee7dc; z-index: 1;"></div>
            
            <!-- Active progress line -->
            <?php
            $currentStepNum = $currentStatus['step'];
            $progressPercent = match($currentStepNum) {
                1 => 0,
                2 => 33,
                3 => 66,
                4 => 100,
                default => 0
            };
            ?>
            <div style="position: absolute; top: 22px; left: 10%; width: <?= $progressPercent * 0.8 ?>%; height: 4px; background: var(--gold); z-index: 2; transition: width 0.4s ease;"></div>

            <!-- Steps Grid -->
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; position: relative; z-index: 3;">
              <?php 
              $steps = [
                  1 => ['label' => 'Order Received', 'desc' => 'Details & photos logged'],
                  2 => ['label' => 'Studio Review', 'desc' => 'Evaluating composition'],
                  3 => ['label' => 'On Easel', 'desc' => 'Drawing in progress'],
                  4 => ['label' => 'Completed', 'desc' => 'Sealed & dispatched']
              ];
              foreach ($steps as $sNum => $sInfo): 
                  $isDone = ($sNum < $currentStepNum);
                  $isCurrent = ($sNum === $currentStepNum);
                  $isUpcoming = ($sNum > $currentStepNum);
              ?>
                <div style="text-align: center;">
                  <div style="width: 44px; height: 44px; margin: 0 auto 12px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 15px; transition: all 0.3s;
                    <?= $isDone ? 'background: #c5a059; color: #ffffff; box-shadow: 0 4px 12px rgba(197,160,89,0.35);' : ($isCurrent ? 'background: var(--ink); color: #ffffff; border: 3px solid var(--gold); box-shadow: 0 4px 16px rgba(17,16,14,0.25);' : 'background: #f4efe6; color: #8c857b; border: 1.5px solid #ded6c9;') ?>">
                    <?php if ($isDone): ?>
                      ✓
                    <?php else: ?>
                      <?= $sNum ?>
                    <?php endif; ?>
                  </div>
                  <div style="font-size: 13.5px; font-weight: <?= $isCurrent || $isDone ? '700' : '500' ?>; color: <?= $isCurrent ? 'var(--ink)' : ($isDone ? 'var(--gold-dark)' : '#8c857b') ?>; margin-bottom: 4px;">
                    <?= $sInfo['label'] ?>
                  </div>
                  <div style="font-size: 11.5px; color: var(--muted); line-height: 1.35; padding: 0 6px;">
                    <?= $sInfo['desc'] ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <!-- Commission Details & Direct Line Grid -->
        <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 32px; padding-top: 30px; align-items: start;">
          <div>
            <p style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--gold-dark); font-weight: 700; margin-bottom: 6px;">Commission Details</p>
            <p style="font-size: 17px; color: var(--ink); font-weight: 600; margin: 0 0 10px;">
              <?php if ($order['artwork_type'] === 'existing' && !empty($order['artwork_title'])): ?>
                Original Artwork: “<?= e($order['artwork_title']) ?>”
              <?php elseif ($order['artwork_type'] === 'portrait'): ?>
                Bespoke Handcrafted Portrait
              <?php else: ?>
                Custom Fine Art Pencil Drawing
              <?php endif; ?>
            </p>
            <div style="background: #faf8f5; border: 1px solid #eadecb; border-radius: 12px; padding: 14px 18px; color: #2e2a25; font-size: 14px; line-height: 1.6;">
              <strong style="color: var(--ink); display: block; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Notes & Instructions:</strong>
              <?= nl2br(e($order['description'])) ?>
            </div>

            <?php if (!empty($orderImages)): ?>
              <div style="margin-top: 16px;">
                <p style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--gold-dark); font-weight: 700; margin-bottom: 8px;">Uploaded Reference Photo:</p>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                  <?php foreach ($orderImages as $img): ?>
                    <a href="<?= url($img) ?>" target="_blank" rel="noopener" style="display: block; border-radius: 8px; overflow: hidden; border: 1px solid #ded6c9; width: 70px; height: 70px;">
                      <img src="<?= url($img) ?>" alt="Reference" style="width: 100%; height: 100%; object-fit: cover;">
                    </a>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>
          </div>

          <div style="background: #faf8f5; border: 1px solid #e8dfd2; border-radius: 16px; padding: 24px;">
            <p style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--gold-dark); font-weight: 700; margin-bottom: 6px;">Artist Direct Line</p>
            <p style="color: var(--muted); font-size: 13.5px; line-height: 1.55; margin-bottom: 16px;">
              Have reference notes, specific deadline requests, or wish to view WIP easel photos? Chat directly with the artist:
            </p>
            <?php 
            $waMsg = urlencode("Hello SNW Art Studio! I am checking status of my commission order #" . $order['reference_no'] . ".");
            ?>
            <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>?text=<?= $waMsg ?>" target="_blank" rel="noopener" class="button" style="background: #25D366; color: #ffffff; border: none; width: 100%; justify-content: center; gap: 10px; padding: 13px; font-size: 14px; font-weight: 600; box-shadow: 0 4px 14px rgba(37,211,102,0.25);">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.18-2.587-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.634.07-.98-.073-.679-.278-1.574-.916-2.316-1.658-.87-.87-1.428-1.89-1.688-2.339-.144-.249-.015-.386.109-.51.111-.112.253-.29.38-.435.127-.144.17-.247.255-.411.085-.164.042-.307-.021-.433-.063-.127-.571-1.378-.782-1.89-.205-.499-.413-.431-.571-.439-.147-.008-.316-.01-.485-.01s-.444.063-.677.317c-.233.254-.889.868-.889 2.117 0 1.249.91 2.455 1.037 2.625.127.17 1.79 2.733 4.336 3.832.606.262 1.079.418 1.448.535.609.193 1.163.166 1.601.1.488-.074 1.503-.614 1.714-1.207.212-.593.212-1.101.149-1.207-.064-.106-.233-.17-.487-.297z"/></svg>
              <span>Message on WhatsApp</span>
            </a>
          </div>
        </div>

      </div>
    <?php endif; ?>

  </div>
</main>

<style>
@media (max-width: 760px) {
  form[action*="track.php"] {
    grid-template-columns: 1fr !important;
  }
  div[style*="grid-template-columns: 1.2fr 1fr"] {
    grid-template-columns: 1fr !important;
  }
}
</style>

<?php require __DIR__ . '/components/footer.php'; ?>

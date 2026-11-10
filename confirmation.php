<?php
require_once __DIR__ . '/config/config.php';
$ref = trim($_GET['ref'] ?? '');
$s = db()->prepare('SELECT * FROM orders WHERE reference_no=?');
$s->execute([$ref]);
$order = $s->fetch();

if (!$order) {
    http_response_code(404);
    $pageTitle = 'Request Not Found';
    require __DIR__ . '/components/header.php';
    echo '<main class="container section" style="text-align:center;"><p class="eyebrow">Not Found</p><h2>Reference Number Not Found</h2><a href="' . url('index.php') . '" class="button">Return to Studio</a></main>';
    require __DIR__ . '/components/footer.php';
    exit;
}

$pageTitle = 'Commission Request Received';
require __DIR__ . '/components/header.php';
?>
<main class="container" style="max-width: 720px; padding: 60px 0 100px;">
  <!-- Success Header -->
  <div style="text-align: center; margin-bottom: 36px;">
    <div style="width: 72px; height: 72px; border-radius: var(--radius-full); background: #e8f5ed; color: #1e6b37; display: inline-flex; align-items: center; justify-content: center; font-size: 32px; margin-bottom: 20px; box-shadow: 0 4px 16px rgba(30, 107, 55, 0.15);">
      ✓
    </div>
    <p class="eyebrow" style="justify-content: center;">Request Successfully Logged</p>
    <h1 style="font-family: var(--font-serif); font-size: clamp(34px, 5vw, 46px); margin: 8px 0 14px;">Thank you, <?= e($order['customer_name']) ?>.</h1>
    <p class="lead" style="margin: 0 auto; max-width: 540px;">Your commission request has reached the studio. Please review your tracking reference and bank transfer details below.</p>
  </div>

  <!-- Reference Card -->
  <div class="notice" style="background: var(--paper); border: 1.5px solid var(--line); border-radius: var(--radius-md); padding: 28px; box-shadow: var(--shadow-sm); text-align: center; margin-bottom: 32px;">
    <p class="eyebrow" style="justify-content: center;">Your Tracking Reference</p>
    <div style="font-family: var(--font-serif); font-size: 32px; font-weight: 700; letter-spacing: 0.08em; color: var(--gold-dark); margin: 6px 0 8px;">
      <?= e($order['reference_no']) ?>
    </div>
    <p style="font-size: 13px; color: var(--muted); margin: 0;">Please keep this reference code when transferring funds or messaging the artist.</p>
  </div>

  <!-- Bank Transfer / Payment Details Card -->
  <div style="background: var(--paper); border: 2px solid var(--gold); border-radius: var(--radius-lg); padding: 36px; box-shadow: var(--shadow-md); margin-bottom: 36px; position: relative;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; border-bottom: 1px solid var(--line); padding-bottom: 16px;">
      <div>
        <span class="badge-pill" style="margin-bottom: 4px;">Official Payment Method</span>
        <h3 style="font-family: var(--font-serif); font-size: 24px; color: var(--ink); margin: 4px 0 0;">Direct Bank Transfer</h3>
      </div>
      <div style="font-size: 28px;">🏦</div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
      <div>
        <small style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--muted); display: block; margin-bottom: 4px;">Bank Name</small>
        <strong style="font-size: 16px; color: var(--ink);"><?= e(BANK_NAME) ?></strong>
      </div>

      <div>
        <small style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--muted); display: block; margin-bottom: 4px;">Branch</small>
        <strong style="font-size: 16px; color: var(--ink);"><?= e(BANK_BRANCH) ?></strong>
      </div>

      <div style="grid-column: 1 / -1; background: var(--cream-card); padding: 18px 22px; border-radius: var(--radius-sm); border: 1px solid var(--line);">
        <small style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--muted); display: block; margin-bottom: 6px;">Account Number</small>
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
          <span id="accountNo" style="font-family: monospace; font-size: 24px; font-weight: 700; letter-spacing: 2px; color: var(--ink);">
            <?= e(BANK_ACCOUNT_NUMBER) ?>
          </span>
          <button type="button" onclick="copyAccountNo()" id="copyBtn" class="button light" style="padding: 6px 16px; font-size: 12.5px; border-radius: var(--radius-full);">
            <span>📋 Copy Number</span>
          </button>
        </div>
      </div>

      <div>
        <small style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--muted); display: block; margin-bottom: 4px;">Account Name</small>
        <strong style="font-size: 15px; color: var(--ink);"><?= e(BANK_ACCOUNT_NAME) ?></strong>
      </div>

      <div>
        <small style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--muted); display: block; margin-bottom: 4px;">SWIFT Code (International)</small>
        <strong style="font-size: 15px; color: var(--ink);"><?= e(BANK_SWIFT) ?></strong>
      </div>
    </div>

    <!-- Instructions Notice -->
    <div style="background: #fdfbf7; border-left: 4px solid var(--gold); padding: 14px 18px; font-size: 13.5px; color: var(--muted); line-height: 1.6;">
      <strong style="color: var(--ink);">Payment Instructions:</strong>
      <ol style="margin: 6px 0 0 18px; padding: 0;">
        <li>Use your Reference Number <strong>(<?= e($order['reference_no']) ?>)</strong> as the payment description or deposit slip remarks.</li>
        <li>Once transferred, take a photo or screenshot of the receipt and send it to our WhatsApp to confirm and start your piece.</li>
      </ol>
    </div>
  </div>

  <!-- Action Buttons -->
  <div style="display: flex; flex-direction: column; gap: 14px;">
    <?php
    $waText = "Hello SNW Art Studio! I have submitted order reference: " . $order['reference_no'] . " (" . $order['customer_name'] . "). Here is my bank transfer payment receipt / slip:";
    ?>
    <a class="button gold" href="https://wa.me/<?= WHATSAPP_NUMBER ?>?text=<?= urlencode($waText) ?>" target="_blank" rel="noopener" style="padding: 16px; font-size: 15px;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.18-2.587-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.634.07-.98-.073-.679-.278-1.574-.916-2.316-1.658-.87-.87-1.428-1.89-1.688-2.339-.144-.249-.015-.386.109-.51.111-.112.253-.29.38-.435.127-.144.17-.247.255-.411.085-.164.042-.307-.021-.433-.063-.127-.571-1.378-.782-1.89-.205-.499-.413-.431-.571-.439-.147-.008-.316-.01-.485-.01s-.444.063-.677.317c-.233.254-.889.868-.889 2.117 0 1.249.91 2.455 1.037 2.625.127.17 1.79 2.733 4.336 3.832.606.262 1.079.418 1.448.535.609.193 1.163.166 1.601.1.488-.074 1.503-.614 1.714-1.207.212-.593.212-1.101.149-1.207-.064-.106-.233-.17-.487-.297z"/></svg>
      <span>Send Payment Slip via WhatsApp</span>
    </a>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
      <a class="button light" href="<?= url('track.php?ref=' . urlencode($order['reference_no'])) ?>" style="padding: 14px;">Track Order Status</a>
      <a class="button light" href="<?= url('index.php') ?>" style="padding: 14px;">Return to Home</a>
    </div>
  </div>
</main>

<script>
function copyAccountNo() {
  const acc = document.getElementById('accountNo').textContent.trim();
  navigator.clipboard.writeText(acc).then(() => {
    const btn = document.getElementById('copyBtn');
    btn.innerHTML = '<span>✓ Copied!</span>';
    btn.style.borderColor = '#16a34a';
    btn.style.color = '#16a34a';
    setTimeout(() => {
      btn.innerHTML = '<span>📋 Copy Number</span>';
      btn.style.borderColor = '';
      btn.style.color = '';
    }, 2500);
  });
}
</script>

<?php require __DIR__ . '/components/footer.php'; ?>

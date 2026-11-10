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
<main class="container" style="max-width: 680px; padding: 60px 0 100px;">
  <div style="text-align: center; margin-bottom: 36px;">
    <div style="width: 72px; height: 72px; border-radius: var(--radius-full); background: #e8f5ed; color: #1e6b37; display: inline-flex; align-items: center; justify-content: center; font-size: 32px; margin-bottom: 20px; box-shadow: 0 4px 16px rgba(30, 107, 55, 0.15);">
      ✓
    </div>
    <p class="eyebrow" style="justify-content: center;">Request Successfully Logged</p>
    <h1 style="font-family: var(--font-serif); font-size: clamp(34px, 5vw, 48px); margin: 8px 0 14px;">Thank you, <?= e($order['customer_name']) ?>.</h1>
    <p class="lead" style="margin: 0 auto; max-width: 520px;">Your commission request has been directly delivered to the studio easel. The artist will reach out shortly to begin your collaborative journey.</p>
  </div>

  <div class="notice" style="background: var(--paper); border: 1.5px solid var(--line); border-radius: var(--radius-md); padding: 32px; box-shadow: var(--shadow-sm); text-align: center;">
    <p class="eyebrow" style="justify-content: center;">Your Tracking Reference</p>
    <div style="font-family: var(--font-serif); font-size: 32px; font-weight: 700; letter-spacing: 0.08em; color: var(--gold-dark); margin: 6px 0 10px;">
      <?= e($order['reference_no']) ?>
    </div>
    <p style="font-size: 13px; color: var(--muted); margin: 0;">Please keep this reference code for your records and when discussing your project with the artist.</p>
  </div>

  <div style="display: flex; flex-direction: column; gap: 14px; margin-top: 32px;">
    <?php
    $waText = "Hello SNW Art Studio! I just submitted a commission request with Reference: " . $order['reference_no'] . " (" . $order['customer_name'] . ").";
    ?>
    <a class="button gold" href="https://wa.me/<?= WHATSAPP_NUMBER ?>?text=<?= urlencode($waText) ?>" target="_blank" rel="noopener" style="padding: 16px;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.18-2.587-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.634.07-.98-.073-.679-.278-1.574-.916-2.316-1.658-.87-.87-1.428-1.89-1.688-2.339-.144-.249-.015-.386.109-.51.111-.112.253-.29.38-.435.127-.144.17-.247.255-.411.085-.164.042-.307-.021-.433-.063-.127-.571-1.378-.782-1.89-.205-.499-.413-.431-.571-.439-.147-.008-.316-.01-.485-.01s-.444.063-.677.317c-.233.254-.889.868-.889 2.117 0 1.249.91 2.455 1.037 2.625.127.17 1.79 2.733 4.336 3.832.606.262 1.079.418 1.448.535.609.193 1.163.166 1.601.1.488-.074 1.503-.614 1.714-1.207.212-.593.212-1.101.149-1.207-.064-.106-.233-.17-.487-.297z"/></svg>
      <span>Connect on WhatsApp with Reference</span>
    </a>

    <a class="button light" href="<?= url('index.php') ?>" style="padding: 14px;">Return to Studio Home</a>
  </div>
</main>
<?php require __DIR__ . '/components/footer.php'; ?>

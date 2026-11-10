<?php
require_once __DIR__ . '/config/config.php';
$pageTitle = 'Payment Methods & Bank Details';
require __DIR__ . '/components/header.php';
?>
<main class="container">
  <section class="page-intro">
    <p class="eyebrow">Studio Financials</p>
    <h1>Payment Methods &amp; Bank Details</h1>
    <p class="lead">To ensure personal collaboration and authenticity, all commissions and collection acquisitions are processed securely through direct bank transfer or verified studio channels.</p>
  </section>

  <section class="split section" style="padding-top: 10px; align-items: start; gap: 64px; margin-bottom: 90px;">
    <!-- Left: Official Bank Transfer Card -->
    <div>
      <div style="background: var(--paper); border: 2px solid var(--gold); border-radius: var(--radius-lg); padding: 36px; box-shadow: var(--shadow-md); position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; border-bottom: 1px solid var(--line); padding-bottom: 16px;">
          <div>
            <span class="badge-pill" style="margin-bottom: 4px;">Primary Method</span>
            <h3 style="font-family: var(--font-serif); font-size: 24px; color: var(--ink); margin: 4px 0 0;">Official Studio Account</h3>
          </div>
          <div style="font-size: 32px;">🏦</div>
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
            <small style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--muted); display: block; margin-bottom: 4px;">SWIFT Code</small>
            <strong style="font-size: 15px; color: var(--ink);"><?= e(BANK_SWIFT) ?></strong>
          </div>
        </div>

        <div style="background: #fdfbf7; border-left: 4px solid var(--gold); padding: 14px 18px; font-size: 13.5px; color: var(--muted); line-height: 1.6;">
          <strong style="color: var(--ink);">Verification Note:</strong>
          Please always send a screenshot or receipt photo to our official WhatsApp (<strong>+<?= e(WHATSAPP_NUMBER) ?></strong>) along with your Name or Order Reference.
        </div>
      </div>
    </div>

    <!-- Right: Payment Policy & Guide -->
    <div>
      <div class="notice" style="background: var(--paper); border: 1.5px solid var(--line); border-radius: var(--radius-md); padding: 32px; box-shadow: var(--shadow-sm); margin-top: 0;">
        <p class="eyebrow">Studio Payment Workflow</p>
        <h3 style="font-family: var(--font-serif); font-size: 22px; margin-bottom: 16px;">How Payments Work</h3>
        
        <div style="display: flex; flex-direction: column; gap: 20px; font-size: 14px; color: var(--muted); line-height: 1.65;">
          <div>
            <strong style="color: var(--ink); display: block; font-size: 15px; margin-bottom: 4px;">1. Free Initial Consultation</strong>
            Submitting a commission request via our website is completely free. No payment is required until we review your photo and confirm the quotation.
          </div>

          <div>
            <strong style="color: var(--ink); display: block; font-size: 15px; margin-bottom: 4px;">2. 50% Advance to Begin</strong>
            Once composition and sizing are agreed upon, a 50% deposit secures your easel booking and work commences.
          </div>

          <div>
            <strong style="color: var(--ink); display: block; font-size: 15px; margin-bottom: 4px;">3. 50% Final Payment Upon Approval</strong>
            When your artwork is finished, we send high-resolution photos and video previews. Once you are 100% satisfied, the final balance is settled before archival packing and dispatch.
          </div>

          <div>
            <strong style="color: var(--ink); display: block; font-size: 15px; margin-bottom: 4px;">4. International Collectors</strong>
            International clients can transfer funds via SWIFT Wire, Western Union, or direct bank transfer.
          </div>
        </div>

        <div style="margin-top: 28px; padding-top: 20px; border-top: 1px solid var(--line);">
          <a class="button gold" href="<?= url('order.php') ?>" style="width: 100%;">
            <span>Commission a Custom Piece</span>
          </a>
        </div>
      </div>
    </div>
  </section>
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

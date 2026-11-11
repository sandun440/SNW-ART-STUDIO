<?php
require_once __DIR__ . '/../config/config.php';
admin_required();

$success = flash('success');
$error = flash('error');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $fields = [
        'bank_name' => trim($_POST['bank_name'] ?? ''),
        'bank_account_name' => trim($_POST['bank_account_name'] ?? ''),
        'bank_account_number' => trim($_POST['bank_account_number'] ?? ''),
        'bank_branch' => trim($_POST['bank_branch'] ?? ''),
        'bank_swift' => trim($_POST['bank_swift'] ?? ''),
        'whatsapp_number' => preg_replace('/[^0-9]/', '', $_POST['whatsapp_number'] ?? ''),
        'studio_email' => trim($_POST['studio_email'] ?? ''),
        'studio_hours' => trim($_POST['studio_hours'] ?? ''),
    ];

    if (empty($fields['bank_name']) || empty($fields['bank_account_name']) || empty($fields['bank_account_number'])) {
        flash('error', 'Please fill in the required Bank Name, Account Name, and Account Number.');
        redirect('admin/settings.php');
    }

    try {
        $stmt = db()->prepare("
            INSERT INTO studio_settings (setting_key, setting_value)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
        ");

        foreach ($fields as $key => $val) {
            $stmt->execute([$key, $val]);
        }

        flash('success', 'Studio & Bank details updated successfully! Changes are now live across all client payment pages.');
        redirect('admin/settings.php');
    } catch (Throwable $e) {
        flash('error', 'Error saving settings: ' . $e->getMessage());
        redirect('admin/settings.php');
    }
}

// Current values from database or defaults
$bankName = studio_setting('bank_name', BANK_NAME);
$bankAccName = studio_setting('bank_account_name', BANK_ACCOUNT_NAME);
$bankAccNo = studio_setting('bank_account_number', BANK_ACCOUNT_NUMBER);
$bankBranch = studio_setting('bank_branch', BANK_BRANCH);
$bankSwift = studio_setting('bank_swift', BANK_SWIFT);

$whatsapp = studio_setting('whatsapp_number', WHATSAPP_NUMBER);
$email = studio_setting('studio_email', STUDIO_EMAIL);
$hours = studio_setting('studio_hours', STUDIO_HOURS);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Studio Bank &amp; Contact Settings · <?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="<?= url('assets/css/style.css') ?>?v=4.0" rel="stylesheet">
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
      Studio Bank &amp; Payment Settings
    </h1>
    <p class="lead">Update the studio's official bank transfer details and direct WhatsApp inquiry lines. These values update the live payment pages immediately.</p>
  </div>

  <!-- Admin Nav Tabs -->
  <nav class="filters" style="margin-bottom: 32px;">
    <a href="<?= url('admin/index.php') ?>">Overview</a>
    <a href="<?= url('admin/artworks.php') ?>">Artworks</a>
    <a href="<?= url('admin/orders.php') ?>">Commissions &amp; Orders</a>
    <a href="<?= url('admin/messages.php') ?>">Messages</a>
    <a href="<?= url('admin/categories.php') ?>">Categories</a>
    <a href="<?= url('admin/settings.php') ?>" class="active">Bank &amp; Settings</a>
  </nav>

  <?php if ($success): ?>
    <div class="alert success" style="margin-bottom: 24px;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
      <span><?= e($success) ?></span>
    </div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="alert error" style="margin-bottom: 24px;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
      <span><?= e($error) ?></span>
    </div>
  <?php endif; ?>

  <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 40px; align-items: start;">
    
    <!-- Settings Form -->
    <div style="background: var(--paper); border: 1px solid var(--line); border-radius: var(--radius-lg); padding: 36px; box-shadow: var(--shadow-sm);">
      <form method="POST" action="<?= url('admin/settings.php') ?>">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

        <!-- Section 1: Bank Transfer Details -->
        <div style="margin-bottom: 32px; padding-bottom: 28px; border-bottom: 1px solid var(--line);">
          <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 18px;">
            <span style="font-size: 22px;">🏦</span>
            <h2 style="font-family: var(--font-serif); font-size: 22px; color: var(--ink); margin: 0;">Official Bank Account</h2>
          </div>
          <p style="font-size: 13.5px; color: var(--muted); margin-bottom: 20px;">
            Clients receive these details after submitting an order and on the dedicated Payment Information page.
          </p>

          <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px;">
            <div style="grid-column: 1 / -1;">
              <label style="display: block; font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--gold-dark); font-weight: 700; margin-bottom: 6px;">Bank Name *</label>
              <input type="text" name="bank_name" id="input_bank_name" value="<?= e($bankName) ?>" required style="width: 100%; padding: 12px 16px; background: #faf8f5; border: 1.5px solid #ded6c9; border-radius: 8px; color: var(--ink); font-size: 15px; font-weight: 600;">
            </div>

            <div>
              <label style="display: block; font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--gold-dark); font-weight: 700; margin-bottom: 6px;">Account Holder Name *</label>
              <input type="text" name="bank_account_name" id="input_acc_name" value="<?= e($bankAccName) ?>" required style="width: 100%; padding: 12px 16px; background: #faf8f5; border: 1.5px solid #ded6c9; border-radius: 8px; color: var(--ink); font-size: 14.5px;">
            </div>

            <div>
              <label style="display: block; font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--gold-dark); font-weight: 700; margin-bottom: 6px;">Account Number *</label>
              <input type="text" name="bank_account_number" id="input_acc_no" value="<?= e($bankAccNo) ?>" required style="width: 100%; padding: 12px 16px; background: #faf8f5; border: 1.5px solid #ded6c9; border-radius: 8px; color: var(--ink); font-family: monospace; font-size: 17px; font-weight: 700; letter-spacing: 1px;">
            </div>

            <div>
              <label style="display: block; font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--gold-dark); font-weight: 700; margin-bottom: 6px;">Branch Name</label>
              <input type="text" name="bank_branch" id="input_branch" value="<?= e($bankBranch) ?>" placeholder="e.g. Colombo Main Branch" style="width: 100%; padding: 12px 16px; background: #faf8f5; border: 1.5px solid #ded6c9; border-radius: 8px; color: var(--ink); font-size: 14.5px;">
            </div>

            <div>
              <label style="display: block; font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--gold-dark); font-weight: 700; margin-bottom: 6px;">SWIFT / BIC (International)</label>
              <input type="text" name="bank_swift" id="input_swift" value="<?= e($bankSwift) ?>" placeholder="e.g. CCEYLKKL" style="width: 100%; padding: 12px 16px; background: #faf8f5; border: 1.5px solid #ded6c9; border-radius: 8px; color: var(--ink); font-size: 14.5px;">
            </div>
          </div>
        </div>

        <!-- Section 2: Studio Contact & WhatsApp -->
        <div style="margin-bottom: 32px;">
          <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 18px;">
            <span style="font-size: 22px;">💬</span>
            <h2 style="font-family: var(--font-serif); font-size: 22px; color: var(--ink); margin: 0;">Studio Contact &amp; Channels</h2>
          </div>

          <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px;">
            <div style="grid-column: 1 / -1;">
              <label style="display: block; font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--gold-dark); font-weight: 700; margin-bottom: 6px;">
                WhatsApp Inquiry Number (with Country Code, no + or spaces) *
              </label>
              <input type="text" name="whatsapp_number" value="<?= e($whatsapp) ?>" required placeholder="e.g. 94765237415" style="width: 100%; padding: 12px 16px; background: #faf8f5; border: 1.5px solid #ded6c9; border-radius: 8px; color: var(--ink); font-family: monospace; font-size: 15px; font-weight: 600;">
              <small style="color: var(--muted); font-size: 12px; margin-top: 4px; display: block;">Powers the floating WhatsApp button and all direct artwork inquiry buttons.</small>
            </div>

            <div>
              <label style="display: block; font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--gold-dark); font-weight: 700; margin-bottom: 6px;">Studio Email</label>
              <input type="email" name="studio_email" value="<?= e($email) ?>" style="width: 100%; padding: 12px 16px; background: #faf8f5; border: 1.5px solid #ded6c9; border-radius: 8px; color: var(--ink); font-size: 14.5px;">
            </div>

            <div>
              <label style="display: block; font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--gold-dark); font-weight: 700; margin-bottom: 6px;">Studio Working Hours</label>
              <input type="text" name="studio_hours" value="<?= e($hours) ?>" style="width: 100%; padding: 12px 16px; background: #faf8f5; border: 1.5px solid #ded6c9; border-radius: 8px; color: var(--ink); font-size: 14.5px;">
            </div>
          </div>
        </div>

        <button type="submit" class="button gold" style="padding: 14px 28px; font-size: 15px; font-weight: 600; cursor: pointer; border: none;">
          <span>💾 Save &amp; Update Live Settings</span>
        </button>
      </form>
    </div>

    <!-- Live Preview Card -->
    <div>
      <p class="eyebrow" style="color: var(--gold-dark);">Live Client Preview</p>
      <div style="background: var(--paper); border: 2px solid var(--gold); border-radius: var(--radius-lg); padding: 30px; box-shadow: var(--shadow-md); position: sticky; top: 100px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--line); padding-bottom: 14px;">
          <div>
            <span class="badge-pill" style="font-size: 10.5px; padding: 4px 10px;">Client View</span>
            <h3 style="font-family: var(--font-serif); font-size: 20px; color: var(--ink); margin: 6px 0 0;">Studio Bank Account</h3>
          </div>
          <div style="font-size: 28px;">🏦</div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 16px;">
          <div>
            <small style="font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--muted); display: block; margin-bottom: 2px;">Bank Name</small>
            <strong id="preview_bank_name" style="font-size: 16px; color: var(--ink);"><?= e($bankName) ?></strong>
          </div>

          <div>
            <small style="font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--muted); display: block; margin-bottom: 2px;">Branch</small>
            <strong id="preview_branch" style="font-size: 15px; color: var(--ink);"><?= e($bankBranch) ?></strong>
          </div>

          <div style="background: var(--cream-card); padding: 14px 18px; border-radius: 8px; border: 1px solid var(--line);">
            <small style="font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--muted); display: block; margin-bottom: 4px;">Account Number</small>
            <span id="preview_acc_no" style="font-family: monospace; font-size: 20px; font-weight: 700; letter-spacing: 1.5px; color: var(--ink); display: block;">
              <?= e($bankAccNo) ?>
            </span>
          </div>

          <div>
            <small style="font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--muted); display: block; margin-bottom: 2px;">Account Name</small>
            <strong id="preview_acc_name" style="font-size: 14.5px; color: var(--ink);"><?= e($bankAccName) ?></strong>
          </div>

          <div>
            <small style="font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--muted); display: block; margin-bottom: 2px;">SWIFT Code</small>
            <strong id="preview_swift" style="font-size: 14.5px; color: var(--ink);"><?= e($bankSwift) ?></strong>
          </div>
        </div>

        <div style="margin-top: 20px; padding: 10px 14px; background: #fdfbf7; border-left: 3px solid var(--gold); font-size: 12px; color: var(--muted);">
          ⚡ When you click "Save", these exact details appear on <a href="<?= url('payment.php') ?>" target="_blank" style="color: var(--gold-dark); text-decoration: underline;">payment.php</a> and order receipts.
        </div>
      </div>
    </div>

  </div>

  <footer style="margin-top: 60px; padding: 24px 0; border-top: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center; font-size: 12.5px; color: var(--muted); flex-wrap: wrap; gap: 12px;">
    <div>SNW Art Studio · Internal Management Portal v1.0</div>
    <div>
      System Engineered &amp; Maintained by <a href="https://sandunsameera.vercel.app/" target="_blank" rel="noopener" style="color: var(--gold-dark); font-weight: 600; text-decoration: none;">Sandun Sameera ↗</a>
    </div>
  </footer>
</main>

<script>
// Live update preview card as admin types
const bindInput = (inputId, previewId) => {
  const el = document.getElementById(inputId);
  const prev = document.getElementById(previewId);
  if (el && prev) {
    el.addEventListener('input', () => {
      prev.textContent = el.value.trim() || '—';
    });
  }
};
bindInput('input_bank_name', 'preview_bank_name');
bindInput('input_acc_name', 'preview_acc_name');
bindInput('input_acc_no', 'preview_acc_no');
bindInput('input_branch', 'preview_branch');
bindInput('input_swift', 'preview_swift');
</script>

</body>
</html>


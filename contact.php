<?php
require_once __DIR__ . '/config/config.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $subject === '' || $message === '') {
        $errors[] = 'Please fill out all fields with a valid email address.';
    }

    if (!$errors) {
        $s = db()->prepare('INSERT INTO contact_messages (name, email, subject, message) VALUES (?,?,?,?)');
        $s->execute([$name, $email, $subject, $message]);
        flash('success', 'Thank you! Your message has been safely received. The studio will get back to you shortly.');
        redirect('contact.php');
    }
}

$pageTitle = 'Contact the Studio';
require __DIR__ . '/components/header.php';
?>
<main class="container">
  <section class="page-intro">
    <p class="eyebrow">Direct Inquiries</p>
    <h1>Let’s Start a Conversation</h1>
    <p class="lead">Whether you are inquiring about an existing collection original, discussing custom sizing, or simply wishing to say hello, the studio welcomes your message.</p>
  </section>

  <section class="split section" style="padding-top: 10px; align-items: start; gap: 70px; margin-bottom: 90px;">
    <!-- Left: Studio Details & Quick Actions -->
    <div>
      <div class="notice" style="background: var(--paper); border: 1.5px solid var(--line); border-radius: var(--radius-md); padding: 32px; box-shadow: var(--shadow-sm);">
        <p class="eyebrow">Immediate Assistance</p>
        <h3 style="font-family: var(--font-serif); font-size: 22px; margin-bottom: 12px;">Prefer instant messaging?</h3>
        <p style="font-size: 14px; color: var(--muted); margin-bottom: 20px;">
          For fast quotes, photo evaluations, or questions regarding ongoing artworks, WhatsApp is our fastest channel.
        </p>

        <a class="button" href="https://wa.me/<?= WHATSAPP_NUMBER ?>" target="_blank" rel="noopener" style="background: #25d366; border-color: #25d366; color: #ffffff; width: 100%;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.18-2.587-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.634.07-.98-.073-.679-.278-1.574-.916-2.316-1.658-.87-.87-1.428-1.89-1.688-2.339-.144-.249-.015-.386.109-.51.111-.112.253-.29.38-.435.127-.144.17-.247.255-.411.085-.164.042-.307-.021-.433-.063-.127-.571-1.378-.782-1.89-.205-.499-.413-.431-.571-.439-.147-.008-.316-.01-.485-.01s-.444.063-.677.317c-.233.254-.889.868-.889 2.117 0 1.249.91 2.455 1.037 2.625.127.17 1.79 2.733 4.336 3.832.606.262 1.079.418 1.448.535.609.193 1.163.166 1.601.1.488-.074 1.503-.614 1.714-1.207.212-.593.212-1.101.149-1.207-.064-.106-.233-.17-.487-.297z"/></svg>
          <span>Chat on WhatsApp</span>
        </a>
      </div>

      <div style="margin-top: 36px; display: flex; flex-direction: column; gap: 20px;">
        <div>
          <strong style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--gold-dark); display: block; margin-bottom: 4px;">Studio Working Hours</strong>
          <p style="font-size: 14.5px; color: var(--ink);"><?= e(STUDIO_HOURS) ?></p>
        </div>

        <div>
          <strong style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--gold-dark); display: block; margin-bottom: 4px;">Direct Email</strong>
          <p style="font-size: 14.5px; color: var(--ink);"><a href="mailto:<?= e(STUDIO_EMAIL) ?>" style="color:inherit;"><?= e(STUDIO_EMAIL) ?></a></p>
        </div>

        <div>
          <strong style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--gold-dark); display: block; margin-bottom: 4px;">Worldwide Delivery</strong>
          <p style="font-size: 14.5px; color: var(--ink);">Dispatched with tracked international express air couriers in moisture-resistant rigid containers.</p>
        </div>
      </div>
    </div>

    <!-- Right: Message Form -->
    <div class="form-wrap" style="width: 100%;">
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

      <form method="post" style="background: var(--paper); border: 1.5px solid var(--line); border-radius: var(--radius-md); padding: 36px; box-shadow: var(--shadow-sm);">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        
        <div class="form-grid">
          <div class="field">
            <label>Your Name *</label>
            <input type="text" name="name" required placeholder="e.g. Ruwan Silva" value="<?= old('name') ?>">
          </div>

          <div class="field">
            <label>Email Address *</label>
            <input type="email" name="email" required placeholder="e.g. ruwan@example.com" value="<?= old('email') ?>">
          </div>

          <div class="field full">
            <label>Subject *</label>
            <input type="text" name="subject" required placeholder="e.g. Inquiry regarding Custom Family Portrait" value="<?= old('subject') ?>">
          </div>

          <div class="field full">
            <label>Message *</label>
            <textarea name="message" required placeholder="Write your message or inquiry here..."><?= old('message') ?></textarea>
          </div>
        </div>

        <button class="button gold" type="submit" style="width: 100%; margin-top: 24px; padding: 16px;">
          <span>Send Message to Studio</span>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
        </button>
      </form>
    </div>
  </section>
</main>
<?php require __DIR__ . '/components/footer.php'; ?>

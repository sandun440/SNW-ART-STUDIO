<footer class="site-footer">
  <div class="container footer-grid">
    <div class="footer-brand">
      <a class="brand" href="<?= url('index.php') ?>" style="margin-bottom: 8px;">
        <img src="<?= url('assets/images/logo.png') ?>?v=2.0" alt="SNW ART STUDIO" class="brand-logo footer-logo">
      </a>
      <p>Devoted to pure traditional fine portraiture, pencil sketches, and original fine arts handcrafted with patient mastery.</p>
    </div>

    <div class="footer-col">
      <h4>Explore</h4>
      <a href="<?= url('gallery.php') ?>">Original Gallery</a>
      <a href="<?= url('order.php') ?>">Custom Commission</a>
      <a href="<?= url('track.php') ?>">Track Order Status</a>
      <a href="<?= url('payment.php') ?>">Bank &amp; Payment Details</a>
      <a href="<?= url('about.php') ?>">The Artist Story</a>
      <a href="<?= url('contact.php') ?>">Direct Inquiry</a>
    </div>

    <div class="footer-col">
      <h4>Art Specialties</h4>
      <p>• Hand-drawn Portraits<br>• Fine Pencil Sketches<br>• Memorial & Legacy Arts<br>• Archival Framing</p>
    </div>

    <div class="footer-col">
      <h4>Studio Hours</h4>
      <p><?= e(STUDIO_HOURS) ?><br><small style="color:var(--gold-dark);font-weight:600;">Worldwide Express Delivery</small></p>
    </div>
  </div>

  <div class="container footer-bottom">
    <div>
      © <?= date('Y') ?> SNW ART STUDIO. Handcrafted with patient graphite strokes.
      <span style="opacity:0.3;margin:0 8px;">|</span>
      <span>Website Developed by <a href="https://sandunsameera.vercel.app/" target="_blank" rel="noopener" style="color:var(--gold-dark);font-weight:600;text-decoration:none;border-bottom:1px dotted var(--gold-dark);transition:all 0.2s;" onmouseover="this.style.color='var(--ink)'" onmouseout="this.style.color='var(--gold-dark)'">Sandun Sameera</a></span>
    </div>
    <div style="display:flex;gap:18px;align-items:center;">
      <a href="https://sandunsameera.vercel.app/" target="_blank" rel="noopener" style="color:var(--muted);font-size:12px;text-decoration:none;display:inline-flex;align-items:center;gap:4px;" title="Website Developer Portfolio">
        <span>Developer Portfolio</span>
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
      </a>
      <a href="<?= url('admin/login.php') ?>" style="opacity:0.6;font-size:11px;">Studio Portal</a>
    </div>
  </div>
</footer>

<!-- Modern Floating WhatsApp Button -->
<a class="whatsapp" href="https://wa.me/<?= WHATSAPP_NUMBER ?>?text=<?= urlencode('Hello SNW Art Studio! I would like to inquire about a custom portrait commission.') ?>" target="_blank" rel="noopener" aria-label="Chat with artist on WhatsApp">
  <span class="whatsapp-pulse"></span>
  <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.18-2.587-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.634.07-.98-.073-.679-.278-1.574-.916-2.316-1.658-.87-.87-1.428-1.89-1.688-2.339-.144-.249-.015-.386.109-.51.111-.112.253-.29.38-.435.127-.144.17-.247.255-.411.085-.164.042-.307-.021-.433-.063-.127-.571-1.378-.782-1.89-.205-.499-.413-.431-.571-.439-.147-.008-.316-.01-.485-.01s-.444.063-.677.317c-.233.254-.889.868-.889 2.117 0 1.249.91 2.455 1.037 2.625.127.17 1.79 2.733 4.336 3.832.606.262 1.079.418 1.448.535.609.193 1.163.166 1.601.1.488-.074 1.503-.614 1.714-1.207.212-.593.212-1.101.149-1.207-.064-.106-.233-.17-.487-.297z"/>
  </svg>
  <span>Chat with Artist</span>
</a>

<script src="<?= url('assets/js/app.js') ?>?v=4.0"></script>
</body>
</html>

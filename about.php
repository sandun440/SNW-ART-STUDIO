<?php
require_once __DIR__ . '/config/config.php';
$pageTitle = 'The Artist & Studio Practice';
require __DIR__ . '/components/header.php';
?>
<main class="container">
  <section class="page-intro">
    <p class="eyebrow">The Artist</p>
    <h1>Made slowly by hand.<br>Held with lasting feeling.</h1>
    <p class="lead">A patient practice devoted to the emotional weight of graphite, the geometry of shadows, and the sacred act of capturing human presence.</p>
  </section>

  <section class="split section" style="padding-top: 10px;">
    <!-- Visual Artist Showcase -->
    <div class="hero-card" style="box-shadow: var(--shadow-lg);">
      <div class="hero-art-img-wrap" style="height: 560px;">
        <img src="<?= url('uploads/artworks/artist-studio.jpg') ?>" alt="SNW Studio Artist in creative practice" style="width:100%;height:100%;object-fit:cover;object-position:center top;">
        <div class="hero-floating-badge">
          <span>The Artist at Work</span>
          <small>Live Creative Practice · SNW Studio</small>
        </div>
      </div>
    </div>

    <!-- Artist Narrative -->
    <div>
      <p class="eyebrow">SNW Studio Philosophy</p>
      <p class="quote">“An artwork is never merely a likeness. It is a slow, quiet act of <span>paying attention.</span>”</p>
      
      <p class="lead" style="font-size: 15.5px; margin-bottom: 16px;">
        I am the artist behind SNW ART STUDIO, an independent fine art practice devoted to fine portraiture, realistic pencil drawings, and expressive live paintings. In an era dominated by instantaneous digital filters and artificial imagery, I believe in the timeless value of handcrafted, tactile artistry.
      </p>

      <p class="lead" style="font-size: 15.5px; margin-bottom: 24px;">
        Whether detailing intricate skin textures and light with 2H–8B graphite and willow charcoal, or building vibrant, emotive scenes with acrylics and mixed media on canvas and bespoke surfaces, every stroke is executed with patient fidelity and personal devotion.
      </p>

      <div class="detail-specs" style="grid-template-columns: 1fr 1fr; margin: 28px 0;">
        <div class="spec-item">
          <strong>The Mediums</strong>
          <span>Graphite, Charcoal, Acrylic &amp; Mixed Media</span>
        </div>
        <div class="spec-item">
          <strong>The Surfaces</strong>
          <span>Stretched Canvas, 300gsm Paper &amp; Wood Panels</span>
        </div>
        <div class="spec-item">
          <strong>Creative Disciplines</strong>
          <span>Realistic Pencil Art, Portraits &amp; Live Murals</span>
        </div>
        <div class="spec-item">
          <strong>Authenticity</strong>
          <span>100% Handcrafted Originals (No AI / No Digital)</span>
        </div>
      </div>

      <a href="<?= url('order.php') ?>" class="button gold">
        <span>Commission a custom artwork with the artist</span>
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
      </a>
    </div>
  </section>
</main>
<?php require __DIR__ . '/components/footer.php'; ?>

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
      <p class="quote">“A portrait is never merely a likeness. It is a slow, quiet act of <span>paying attention.</span>”</p>
      
      <p class="lead" style="font-size: 15.5px; margin-bottom: 16px;">
        I am the artist behind SNW ART STUDIO, an independent fine art practice rooted in traditional graphite and charcoal portraiture. In an era dominated by instantaneous digital filters and artificial imagery, I believe there is irreplaceable value in the slow, tactile patience of the hand.
      </p>

      <p class="lead" style="font-size: 15.5px; margin-bottom: 24px;">
        Every artwork begins with careful contemplation of light, proportion, and gesture. Layer upon layer of graphite — from light drafting grades (2H) to the deepest, velvet blacks (8B) — are delicately applied and blended by hand on archival cotton rag paper.
      </p>

      <div class="detail-specs" style="grid-template-columns: 1fr 1fr; margin: 28px 0;">
        <div class="spec-item">
          <strong>The Medium</strong>
          <span>Graphite &amp; Willow Charcoal</span>
        </div>
        <div class="spec-item">
          <strong>The Surface</strong>
          <span>300gsm Archival Paper</span>
        </div>
        <div class="spec-item">
          <strong>The Preservation</strong>
          <span>UV Archival Fixative</span>
        </div>
        <div class="spec-item">
          <strong>The Philosophy</strong>
          <span>100% Traditional Handcraft</span>
        </div>
      </div>

      <a href="<?= url('order.php') ?>" class="button gold">
        <span>Commission a portrait with the artist</span>
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
      </a>
    </div>
  </section>
</main>
<?php require __DIR__ . '/components/footer.php'; ?>

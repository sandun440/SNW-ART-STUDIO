<?php
require_once __DIR__ . '/config/config.php';
$pageTitle = 'Fine Handcrafted Graphite & Portrait Art';
$artworks = db()->query("SELECT a.*, c.name category_name FROM artworks a JOIN categories c ON c.id=a.category_id WHERE a.featured=1 ORDER BY a.created_at DESC LIMIT 3")->fetchAll();
require __DIR__ . '/components/header.php';
?>
<main>
  <!-- Hero Section -->
  <section class="container hero">
    <div>
      <div class="badge-pill">
        <span>✦</span>
        <span>100% Handcrafted Graphite & Portraiture</span>
      </div>
      
      <h1>Drawn from <em>soul.</em><br>Crafted for eternity.</h1>
      
      <p class="lead">Original fine pencil drawings and bespoke portrait art, created slowly and thoughtfully for spaces, people and moments that deserve to be remembered forever.</p>
      
      <div class="hero-actions">
        <a class="button gold" href="<?= url('gallery.php') ?>">
          <span>Explore Collection</span>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </a>
        <a class="button light" href="<?= url('order.php') ?>">Commission a Piece</a>
      </div>

      <div class="hero-stats">
        <div class="hero-stat">
          <strong>500+</strong>
          <span>Pieces Delivered</span>
        </div>
        <div class="hero-stat">
          <strong>100%</strong>
          <span>Pure Traditional Handcraft</span>
        </div>
        <div class="hero-stat">
          <strong>5.0 ★</strong>
          <span>Client Satisfaction</span>
        </div>
      </div>
    </div>

    <!-- Hero Showcase Card -->
    <div class="hero-showcase">
      <div class="hero-satisfaction-pill">
        <span class="stars">★★★★★</span>
        <span>"Captures true human soul"</span>
      </div>

      <div class="hero-card">
        <div class="hero-art-img-wrap">
          <img src="<?= url('uploads/artworks/study-02.jpg') ?>" alt="Featured Graphite Portrait Artwork" loading="eager">
          <div class="hero-floating-badge">
            <span>“The Gaze” · Portrait Study</span>
            <small>Layered 2B-8B Charcoal & Graphite on Archival Cotton</small>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Selected Works Section -->
  <section class="section" style="padding-top: 40px;">
    <div class="container">
      <div class="section-head">
        <div>
          <p class="eyebrow">Curated Collection</p>
          <h2>Selected studio works</h2>
        </div>
        <a href="<?= url('gallery.php') ?>" class="button light">
          <span>View all works</span>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </a>
      </div>

      <div class="grid">
        <?php foreach ($artworks as $art): ?>
          <article class="card">
            <a href="<?= url('artwork.php?slug=' . urlencode($art['slug'])) ?>">
              <div class="art-image">
                <span class="art-category-pill"><?= e($art['category_name']) ?></span>
                <?php if (!empty($art['image_path'])): ?>
                  <img src="<?= url($art['image_path']) ?>" alt="<?= e($art['title']) ?>" loading="lazy">
                <?php endif; ?>
              </div>
              <div class="art-body">
                <p class="eyebrow"><?= e($art['category_name']) ?></p>
                <h3><?= e($art['title']) ?></h3>
                <p><?= e($art['description']) ?></p>
                <div class="card-footer">
                  <div class="price">$<?= number_format((float)$art['price'], 2) ?></div>
                  <span class="art-view-link">View details &rarr;</span>
                </div>
              </div>
            </a>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Studio Craft & Pillars -->
  <section class="section" style="background: var(--cream-subtle); padding: 90px 0;">
    <div class="container">
      <div style="text-align: center; max-width: 640px; margin: 0 auto 50px;">
        <p class="eyebrow" style="justify-content: center;">Uncompromising Quality</p>
        <h2>Why collectors choose SNW Studio</h2>
        <p class="lead" style="margin: 12px auto 0;">Every stroke is executed by hand with absolute devotion to likeness, tonal harmony, and timeless longevity.</p>
      </div>

      <div class="features-grid">
        <div class="feature-box">
          <div class="feature-icon-wrap">✍️</div>
          <h3>100% Traditional Handcraft</h3>
          <p>Never digitally printed or AI-generated. Pure mastery of graphite pencils from hard 2H to velvet 8B.</p>
        </div>

        <div class="feature-box">
          <div class="feature-icon-wrap">📜</div>
          <h3>Museum-Grade Cotton</h3>
          <p>Drawn on heavyweight 300gsm acid-free paper coated with UV fixative to resist fading for generations.</p>
        </div>

        <div class="feature-box">
          <div class="feature-icon-wrap">👁️</div>
          <h3>Milestone Previews</h3>
          <p>Receive WhatsApp work-in-progress photos and video clips as your artwork comes to life layer by layer.</p>
        </div>

        <div class="feature-box">
          <div class="feature-icon-wrap">🛡️</div>
          <h3>Secure Global Transit</h3>
          <p>Meticulously encased in moisture-proof wooden or reinforced hardboard casing with tracked delivery.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Dark Artist Philosophy Section -->
  <section class="section dark">
    <div class="container split">
      <div>
        <p class="eyebrow">A Personal Practice</p>
        <p class="quote">“Every face carries a quiet universe. My work is simply to make the invisible <span>visible.</span>”</p>
      </div>
      <div>
        <p class="lead">SNW ART STUDIO is an independent practice built around the intimate discipline of hand-drawn portraiture. Each commission begins with an open conversation and ends with a timeless work of art that feels unmistakably alive.</p>
        <p class="lead" style="margin-top: 14px;">No shortcuts. No mass production. Only patience, observation, and deep respect for the subject.</p>
        <a href="<?= url('about.php') ?>" class="button light" style="margin-top: 28px;">Meet the artist</a>
      </div>
    </div>
  </section>

  <!-- Step-by-Step Commission Flow -->
  <section class="section">
    <div class="container">
      <div style="text-align: center; max-width: 600px; margin: 0 auto 40px;">
        <p class="eyebrow" style="justify-content: center;">Commission Process</p>
        <h2>How a custom piece is born</h2>
        <p class="lead" style="margin: 10px auto 0;">A smooth, transparent, and collaborative experience from initial concept to your doorstep.</p>
      </div>

      <div class="steps-grid">
        <div class="step-card">
          <div class="step-num">01</div>
          <h3>Share Your Vision</h3>
          <p>Submit your preferred reference photographs and tell us the story or mood you wish to preserve.</p>
        </div>

        <div class="step-card">
          <div class="step-num">02</div>
          <h3>Personal Consultation</h3>
          <p>We analyze lighting, discuss optimal dimensions, and confirm availability and custom pricing.</p>
        </div>

        <div class="step-card">
          <div class="step-num">03</div>
          <h3>Handcrafted Creation</h3>
          <p>Drawn by hand with patient graphite layers. You receive intermediate previews for feedback.</p>
        </div>

        <div class="step-card">
          <div class="step-num">04</div>
          <h3>Sealed & Delivered</h3>
          <p>Finished with protective archival sealant, Certificate of Authenticity, and luxury packaging.</p>
        </div>
      </div>

      <div style="text-align: center; margin-top: 50px;">
        <a href="<?= url('order.php') ?>" class="button gold">
          <span>Start Your Commission</span>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </a>
      </div>
    </div>
  </section>

  <!-- Client Testimonials -->
  <section class="section" style="background: var(--cream-subtle); padding: 90px 0;">
    <div class="container">
      <div class="section-head">
        <div>
          <p class="eyebrow">Collector Words</p>
          <h2>Loved by clients worldwide</h2>
        </div>
      </div>

      <div class="reviews-grid">
        <div class="review-card">
          <div>
            <div class="review-stars">★★★★★</div>
            <p class="review-text">“When I unwrapped the portrait of my grandparents, my whole family was brought to tears. The gentle light in their eyes is captured with such sensitivity that it feels like they are right in the room.”</p>
          </div>
          <div class="reviewer">
            <div class="reviewer-avatar">DS</div>
            <div class="reviewer-info">
              <strong>Dilshan Senanayake</strong>
              <span>Custom Portrait Commission · Colombo</span>
            </div>
          </div>
        </div>

        <div class="review-card">
          <div>
            <div class="review-stars">★★★★★</div>
            <p class="review-text">“The level of detail in the pencil work is unbelievable. The shading transitions are so soft and smooth. The WhatsApp progress updates made the whole waiting process an absolute joy!”</p>
          </div>
          <div class="reviewer">
            <div class="reviewer-avatar">AN</div>
            <div class="reviewer-info">
              <strong>Amanda Nirmal</strong>
              <span>Graphite Memorial Artwork · Kandy</span>
            </div>
          </div>
        </div>

        <div class="review-card">
          <div>
            <div class="review-stars">★★★★★</div>
            <p class="review-text">“Arrived in London flawlessly packaged in a rigid wooden protector. The certificate of authenticity and the quality of the heavyweight paper show the studio’s absolute mastery.”</p>
          </div>
          <div class="reviewer">
            <div class="reviewer-avatar">RW</div>
            <div class="reviewer-info">
              <strong>Richard Williams</strong>
              <span>Fine Art Collector · London, UK</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>
<?php require __DIR__ . '/components/footer.php'; ?>

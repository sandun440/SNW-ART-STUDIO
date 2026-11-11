/**
 * SNW ART STUDIO — Modern Interactive Luxury Experience Engine
 * Crafted for fluid micro-interactions, 3D card tilts, scroll reveals, and lightbox showcases.
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Ambient Cursor Spotlight (Desktop only)
  if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
    let spotlight = document.querySelector('.ambient-spotlight');
    if (!spotlight) {
      spotlight = document.createElement('div');
      spotlight.className = 'ambient-spotlight';
      document.body.appendChild(spotlight);
    }

    let mouseX = -500;
    let mouseY = -500;
    let currentX = -500;
    let currentY = -500;

    window.addEventListener('mousemove', (e) => {
      mouseX = e.clientX;
      mouseY = e.clientY;
    });

    const updateSpotlight = () => {
      // Smooth lerp easing
      currentX += (mouseX - currentX) * 0.12;
      currentY += (mouseY - currentY) * 0.12;
      document.documentElement.style.setProperty('--mouse-x', `${currentX}px`);
      document.documentElement.style.setProperty('--mouse-y', `${currentY}px`);
      requestAnimationFrame(updateSpotlight);
    };
    requestAnimationFrame(updateSpotlight);
  }

  // 2. Interactive 3D Tilt & Specular Glare on Cards
  const tiltElements = document.querySelectorAll('.card, .hero-card, .feature-card, .step-card');
  tiltElements.forEach(card => {
    // Inject glare element if missing
    if (!card.querySelector('.card-glare')) {
      const glare = document.createElement('div');
      glare.className = 'card-glare';
      card.appendChild(glare);
    }

    if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
      card.addEventListener('mousemove', (e) => {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;

        // Max tilt angles: 6 degrees for subtle luxury feel
        const rotateX = ((y - centerY) / centerY) * -6;
        const rotateY = ((x - centerX) / centerX) * 6;

        card.style.transform = `perspective(1000px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg) translateY(-6px) scale3d(1.015, 1.015, 1.015)`;

        // Set glare coordinates
        const glareX = (x / rect.width) * 100;
        const glareY = (y / rect.height) * 100;
        card.style.setProperty('--glare-x', `${glareX}%`);
        card.style.setProperty('--glare-y', `${glareY}%`);
      });

      card.addEventListener('mouseleave', () => {
        card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateY(0) scale3d(1, 1, 1)';
        card.style.transition = 'transform 0.45s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.45s ease';
      });

      card.addEventListener('mouseenter', () => {
        card.style.transition = 'transform 0.1s ease-out, box-shadow 0.25s ease';
      });
    }
  });

  // 3. Scroll Reveal Animations (IntersectionObserver)
  const revealTargets = document.querySelectorAll(
    '.hero > div, .hero-showcase, .section-head, .grid .card, .features-grid .feature-card, .steps-grid .step-card, .reviews-grid .review-card, .split > div, .card'
  );

  revealTargets.forEach((el, index) => {
    el.classList.add('reveal-init');
    // Apply staggered delays within grids
    const parentGrid = el.closest('.grid, .features-grid, .steps-grid, .reviews-grid');
    if (parentGrid) {
      const siblings = Array.from(parentGrid.children);
      const childIndex = siblings.indexOf(el);
      const delay = (childIndex % 3) + 1;
      el.classList.add(`reveal-delay-${delay}`);
    }
  });

  const revealObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('revealed');
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.1,
    rootMargin: '0px 0px -40px 0px'
  });

  revealTargets.forEach(el => revealObserver.observe(el));

  // 4. Live Counter Animation for Studio Stats
  const statNumbers = document.querySelectorAll('.hero-stat strong');
  let countersAnimated = false;

  const animateCounters = () => {
    if (countersAnimated) return;
    countersAnimated = true;

    statNumbers.forEach(stat => {
      const text = stat.textContent.trim();
      const match = text.match(/([0-9]+(\.[0-9]+)?)/);
      if (!match) return;

      const targetValue = parseFloat(match[0]);
      const suffix = text.replace(match[0], '');
      const isFloat = match[0].includes('.');
      const duration = 1600;
      const startTime = performance.now();

      const updateCounter = (currentTime) => {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        // Easing: easeOutExpo
        const ease = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
        const current = targetValue * ease;

        stat.textContent = (isFloat ? current.toFixed(1) : Math.floor(current)) + suffix;

        if (progress < 1) {
          requestAnimationFrame(updateCounter);
        } else {
          stat.textContent = text;
        }
      };
      requestAnimationFrame(updateCounter);
    });
  };

  const statsContainer = document.querySelector('.hero-stats');
  if (statsContainer) {
    const statsObserver = new IntersectionObserver((entries) => {
      if (entries[0].isIntersecting) {
        animateCounters();
        statsObserver.disconnect();
      }
    }, { threshold: 0.5 });
    statsObserver.observe(statsContainer);
  }

  // 5. Fullscreen Luxury Art Lightbox Modal
  let lightbox = document.querySelector('.art-lightbox');
  if (!lightbox) {
    lightbox = document.createElement('div');
    lightbox.className = 'art-lightbox';
    lightbox.innerHTML = `
      <div class="art-lightbox-panel">
        <button class="art-lightbox-close" aria-label="Close Lightbox">&times;</button>
        <div class="art-lightbox-img-wrap">
          <img src="" alt="Artwork Preview" id="lightboxImg">
        </div>
        <div class="art-lightbox-body">
          <div>
            <span class="art-category-pill" id="lightboxCategory" style="position: static; display: inline-block; margin-bottom: 12px;">Category</span>
            <h2 id="lightboxTitle" style="font-family: var(--font-serif); font-size: 28px; margin-bottom: 10px; color: var(--ink);">Artwork Title</h2>
            <p id="lightboxDesc" style="color: var(--muted); font-size: 14.5px; line-height: 1.6; margin-bottom: 20px;">Artwork description details.</p>
            <div id="lightboxPrice" style="font-family: var(--font-serif); font-size: 24px; font-weight: 700; color: var(--gold-dark); margin-bottom: 24px;">Price</div>
          </div>
          <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="#" id="lightboxInquire" target="_blank" rel="noopener" class="button gold" style="flex: 1; justify-content: center;">
              <span>Inquire on WhatsApp</span>
            </a>
            <a href="#" id="lightboxDetail" class="button light">
              <span>View Full Page</span>
            </a>
          </div>
        </div>
      </div>
    `;
    document.body.appendChild(lightbox);
  }

  const lightboxImg = document.getElementById('lightboxImg');
  const lightboxTitle = document.getElementById('lightboxTitle');
  const lightboxCategory = document.getElementById('lightboxCategory');
  const lightboxDesc = document.getElementById('lightboxDesc');
  const lightboxPrice = document.getElementById('lightboxPrice');
  const lightboxInquire = document.getElementById('lightboxInquire');
  const lightboxDetail = document.getElementById('lightboxDetail');
  const lightboxClose = lightbox.querySelector('.art-lightbox-close');

  const openLightbox = (data) => {
    lightboxImg.src = data.img;
    lightboxTitle.textContent = data.title;
    lightboxCategory.textContent = data.category || 'Fine Art';
    lightboxDesc.textContent = data.desc || 'Handcrafted graphite and charcoal piece on archival cotton.';
    lightboxPrice.textContent = data.price || '';
    lightboxDetail.href = data.url || '#';
    lightboxInquire.href = `https://wa.me/94765237415?text=${encodeURIComponent('Hello SNW Art Studio! I am inquiring about the piece: "' + data.title + '".')}`;

    lightbox.classList.add('active');
    document.body.style.overflow = 'hidden';
  };

  const closeLightbox = () => {
    lightbox.classList.remove('active');
    document.body.style.overflow = '';
  };

  lightboxClose.addEventListener('click', closeLightbox);
  lightbox.addEventListener('click', (e) => {
    if (e.target === lightbox) closeLightbox();
  });
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && lightbox.classList.contains('active')) closeLightbox();
  });

  // Attach Quick Zoom button overlays to artwork cards
  document.querySelectorAll('.card').forEach(card => {
    const artImage = card.querySelector('.art-image');
    const link = card.querySelector('a');
    if (!artImage || !link) return;

    const img = artImage.querySelector('img');
    const title = card.querySelector('h3')?.textContent.trim() || 'Artwork';
    const category = card.querySelector('.art-category-pill')?.textContent.trim() || '';
    const desc = card.querySelector('p:not(.eyebrow)')?.textContent.trim() || '';
    const price = card.querySelector('.price')?.textContent.trim() || '';

    // Create zoom overlay
    const overlay = document.createElement('div');
    overlay.className = 'art-zoom-overlay';
    overlay.innerHTML = `
      <button class="art-zoom-btn" type="button">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
        <span>Quick Zoom</span>
      </button>
    `;
    artImage.appendChild(overlay);

    overlay.querySelector('.art-zoom-btn').addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      openLightbox({
        img: img ? img.src : '',
        title,
        category,
        desc,
        price,
        url: link.href
      });
    });
  });

  // 6. Magnetic Pull on Primary Action Buttons (Desktop only)
  if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
    const magneticBtns = document.querySelectorAll('.button.gold, .nav-cta');
    magneticBtns.forEach(btn => {
      btn.addEventListener('mousemove', (e) => {
        const rect = btn.getBoundingClientRect();
        const x = e.clientX - (rect.left + rect.width / 2);
        const y = e.clientY - (rect.top + rect.height / 2);
        btn.style.transform = `translate(${x * 0.18}px, ${y * 0.18}px)`;
      });
      btn.addEventListener('mouseleave', () => {
        btn.style.transform = 'translate(0, 0)';
        btn.style.transition = 'transform 0.4s cubic-bezier(0.16, 1, 0.3, 1)';
      });
      btn.addEventListener('mouseenter', () => {
        btn.style.transition = 'transform 0.1s ease-out';
      });
    });
  }

  // 7. Mobile Navigation Drawer Toggle
  const navToggle = document.querySelector('.nav-toggle');
  const mainNav = document.querySelector('.main-nav');
  if (navToggle && mainNav) {
    navToggle.addEventListener('click', () => {
      mainNav.classList.toggle('open');
    });
  }

  // 8. Commission Type Selector Active State
  document.querySelectorAll('.type-card input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', () => {
      document.querySelectorAll('.type-card').forEach(card => card.classList.remove('active'));
      if (radio.checked) {
        radio.closest('.type-card')?.classList.add('active');
      }
    });
  });

  // 9. Drag & Drop Live Preview on File Input
  const fileInput = document.getElementById('reference_image');
  const dropzone = document.getElementById('dropzone');
  const filePreview = document.getElementById('filePreview');
  const previewImg = document.getElementById('previewImg');
  const previewName = document.getElementById('previewName');
  const dropzoneLabel = document.getElementById('dropzoneLabel');

  if (fileInput) {
    const handleFile = (file) => {
      if (!file) return;
      if (previewName) previewName.textContent = file.name + ' (' + (file.size / (1024 * 1024)).toFixed(2) + ' MB)';
      if (dropzoneLabel) dropzoneLabel.textContent = 'Selected: ' + file.name;

      if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
          if (previewImg) previewImg.src = e.target.result;
          if (filePreview) filePreview.style.display = 'flex';
        };
        reader.readAsDataURL(file);
      }
    };

    fileInput.addEventListener('change', (e) => {
      if (e.target.files && e.target.files[0]) {
        handleFile(e.target.files[0]);
      }
    });

    if (dropzone) {
      ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
          e.preventDefault();
          e.stopPropagation();
          dropzone.style.borderColor = 'var(--gold)';
          dropzone.style.background = 'var(--gold-light)';
        });
      });

      ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
          e.preventDefault();
          e.stopPropagation();
          dropzone.style.borderColor = '';
          dropzone.style.background = '';
        });
      });

      dropzone.addEventListener('drop', (e) => {
        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
          fileInput.files = e.dataTransfer.files;
          handleFile(e.dataTransfer.files[0]);
        }
      });
    }
  }
});

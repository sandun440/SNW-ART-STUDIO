// Mobile Navigation Toggle
const navToggle = document.querySelector('.nav-toggle');
const mainNav = document.querySelector('.main-nav');
if (navToggle && mainNav) {
  navToggle.addEventListener('click', () => {
    mainNav.classList.toggle('open');
  });
}

// Commission Type Selector Active State
document.querySelectorAll('.type-card input[type="radio"]').forEach(radio => {
  radio.addEventListener('change', () => {
    document.querySelectorAll('.type-card').forEach(card => card.classList.remove('active'));
    if (radio.checked) {
      radio.closest('.type-card')?.classList.add('active');
    }
  });
});

// Drag and drop & Live Image Preview on File Input
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

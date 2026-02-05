document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.php-email-form').forEach(function (form) {
    const submitBtn = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function () {
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.classList.add('sending');
      }
    });

    const watchSelectors = ['.loading', '.sent-message', '.error-message'];
    const observer = new MutationObserver(function () {
      const sent = form.querySelector('.sent-message');
      const err = form.querySelector('.error-message');

      if (sent && sent.classList.contains('d-block')) {
        if (submitBtn) { submitBtn.disabled = false; submitBtn.classList.remove('sending'); }
        // auto-hide success message
        setTimeout(function () { sent.classList.remove('d-block'); }, 6000);
      }
      if (err && err.classList.contains('d-block')) {
        if (submitBtn) { submitBtn.disabled = false; submitBtn.classList.remove('sending'); }
      }
    });

    watchSelectors.forEach(function (sel) {
      const el = form.querySelector(sel);
      if (el) observer.observe(el, { attributes: true, attributeFilter: ['class'] });
    });

    // Accessibility: move focus to the first invalid field when an error appears
    const errorObserver = new MutationObserver(function () {
      const err = form.querySelector('.error-message');
      if (err && err.classList.contains('d-block')) {
        const firstInvalid = form.querySelector('[aria-invalid="true"]') || form.querySelector('.is-invalid') || form.querySelector('input,textarea');
        if (firstInvalid) firstInvalid.focus();
      }
    });
    const errEl = form.querySelector('.error-message');
    if (errEl) errorObserver.observe(errEl, { attributes: true, attributeFilter: ['class'] });
  });
});

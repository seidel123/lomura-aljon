/* ============================================================
   NOVA, slider + header + reveal (vanilla JS, no dependencies)
   - .nova-slider / .nova-slide / [data-slider-prev|next|dot]
   - fade rotation 5.5s; pause on hover/focus; idle-resume 8s;
     manual nav stops; arrows; swipe 40px; ARIA; reduced-motion;
     pageshow/pagehide lifecycle; editor fallback (stacked)
   - .nova-header.is-scrolled elevation
   - .nova-reveal IntersectionObserver
   ============================================================ */
(function () {
  'use strict';

  var AUTOPLAY_MS = 5500;
  var IDLE_RESUME_MS = 8000;
  var SWIPE_PX = 40;

  function initSlider(slider) {
    var slides = Array.prototype.slice.call(slider.querySelectorAll('.nova-slide'));
    if (slides.length < 2) { return; }

    var prev = slider.querySelector('[data-slider-prev]');
    var next = slider.querySelector('[data-slider-next]');
    var dots = Array.prototype.slice.call(slider.querySelectorAll('[data-slider-dot]'));
    var progress = slider.querySelector('[data-slider-progress]');
    var current = 0;
    var timer = null;
    var userStopped = false;
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    slider.setAttribute('role', 'region');
    slider.setAttribute('aria-roledescription', 'carousel');
    if (!slider.getAttribute('aria-label')) {
      slider.setAttribute('aria-label', 'Featured properties');
    }

    slides.forEach(function (slide, i) {
      slide.setAttribute('aria-roledescription', 'slide');
      slide.setAttribute('aria-label', (i + 1) + ' of ' + slides.length);
    });

    function restartProgress() {
      if (!progress) { return; }
      progress.style.animation = 'none';
      void progress.offsetWidth;
      if (!reduceMotion && timer) {
        progress.style.animation = 'nova-progress ' + AUTOPLAY_MS + 'ms linear forwards';
      } else {
        progress.style.width = '0%';
      }
    }

    function show(index) {
      current = (index + slides.length) % slides.length;
      slides.forEach(function (slide, i) {
        var active = i === current;
        slide.classList.toggle('is-active', active);
        if (active) { slide.removeAttribute('aria-hidden'); }
        else { slide.setAttribute('aria-hidden', 'true'); }
      });
      dots.forEach(function (dot, i) {
        if (i === current) { dot.setAttribute('aria-current', 'true'); }
        else { dot.removeAttribute('aria-current'); }
      });
      var counter = slider.querySelector('[data-slider-current]');
      if (counter) {
        var n = current + 1;
        counter.textContent = (n < 10 ? '0' : '') + n;
      }
      restartProgress();
    }

    function stopAutoplay() {
      if (timer) { window.clearInterval(timer); timer = null; }
      if (progress) { progress.style.animation = 'none'; }
    }

    function startAutoplay() {
      if (timer || userStopped || reduceMotion) { return; }
      timer = window.setInterval(function () { show(current + 1); }, AUTOPLAY_MS);
      restartProgress();
    }

    function go(index, manual) {
      show(index);
      if (manual) { userStopped = true; stopAutoplay(); }
    }

    if (prev) { prev.addEventListener('click', function () { go(current - 1, true); }); }
    if (next) { next.addEventListener('click', function () { go(current + 1, true); }); }
    dots.forEach(function (dot, i) {
      dot.addEventListener('click', function () { go(i, true); });
    });

    var hoverTimer = null;
    var isHovered = false;

    function cancelIdle() {
      if (hoverTimer) { window.clearTimeout(hoverTimer); hoverTimer = null; }
    }
    function pauseForHover() {
      stopAutoplay();
      cancelIdle();
      hoverTimer = window.setTimeout(function () {
        hoverTimer = null;
        startAutoplay();
      }, IDLE_RESUME_MS);
    }

    slider.addEventListener('pointerenter', function () {
      isHovered = true;
      pauseForHover();
    });
    slider.addEventListener('pointermove', function () {
      if (isHovered) { pauseForHover(); }
    });
    slider.addEventListener('pointerleave', function () {
      isHovered = false;
      cancelIdle();
      startAutoplay();
    });
    slider.addEventListener('focusin', stopAutoplay);
    slider.addEventListener('focusout', startAutoplay);

    slider.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowLeft') { e.preventDefault(); go(current - 1, true); }
      else if (e.key === 'ArrowRight') { e.preventDefault(); go(current + 1, true); }
    });

    var touchX = null;
    slider.addEventListener('touchstart', function (e) {
      if (e.touches.length === 1) { touchX = e.touches[0].clientX; }
    }, { passive: true });
    slider.addEventListener('touchend', function (e) {
      if (touchX === null || e.changedTouches.length !== 1) { return; }
      var dx = e.changedTouches[0].clientX - touchX;
      touchX = null;
      if (Math.abs(dx) >= SWIPE_PX) { go(current + (dx < 0 ? 1 : -1), true); }
    }, { passive: true });

    document.addEventListener('visibilitychange', function () {
      if (document.hidden) { stopAutoplay(); }
      else { startAutoplay(); }
    });

    window.addEventListener('pageshow', function (e) {
      if (!e.persisted) { return; }
      userStopped = false;
      isHovered = false;
      cancelIdle();
      stopAutoplay();
      startAutoplay();
    });
    window.addEventListener('pagehide', function () {
      isHovered = false;
      cancelIdle();
      stopAutoplay();
    });

    show(0);
    startAutoplay();
  }

  function initHeader() {
    var header = document.querySelector('.nova-header');
    if (!header) { return; }
    function onScroll() {
      header.classList.toggle('is-scrolled', window.pageYOffset > 24);
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  function initSmoothAnchors() {
    document.addEventListener('click', function (e) {
      var a = e.target.closest ? e.target.closest('a[href^="#"]') : null;
      if (!a) { return; }
      var id = a.getAttribute('href');
      if (!id || id.length < 2) { return; }
      var target = document.querySelector(id);
      if (!target) { return; }
      e.preventDefault();
      var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      target.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
      if (history.replaceState) { history.replaceState(null, '', id); }
    });
  }

  function initReveal() {
    var els = document.querySelectorAll('.nova-reveal');
    if (!els.length) { return; }
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      Array.prototype.forEach.call(els, function (el) { el.classList.add('is-visible'); });
      return;
    }
    if (!('IntersectionObserver' in window)) {
      Array.prototype.forEach.call(els, function (el) { el.classList.add('is-visible'); });
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry, idx) {
        if (entry.isIntersecting) {
          entry.target.style.transitionDelay = (idx % 3 * 80) + 'ms';
          entry.target.classList.add('is-visible');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });
    Array.prototype.forEach.call(els, function (el) { io.observe(el); });
  }

  function ready(fn) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', fn);
    } else { fn(); }
  }

  ready(function () {
    initHeader();
    initSmoothAnchors();
    initReveal();
    if (document.body.classList.contains('elementor-editor-active')) { return; }
    Array.prototype.forEach.call(document.querySelectorAll('.nova-slider'), initSlider);
  });
})();

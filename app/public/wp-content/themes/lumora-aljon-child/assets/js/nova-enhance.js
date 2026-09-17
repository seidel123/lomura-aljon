/* ============================================================
   NOVA enhance (lumora-aljon): mobile menu, scrollspy, progress,
   back-to-top, hero entrance, slider live region, AJAX form.
   Vanilla JS, decoupled from nova-slider.js via observers.
   ============================================================ */
(function () {
  'use strict';

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- Mobile menu (progressive enhancement) ---------- */
  function initMenu() {
    var header = document.querySelector('.nova-header');
    if (!header) { return; }
    var nav = header.querySelector('.nova-nav');
    var cta = header.querySelector('.nova-header-cta');
    if (!nav || header.querySelector('.nova-menu-btn')) { return; }

    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'nova-menu-btn';
    btn.setAttribute('aria-label', 'Open menu');
    btn.setAttribute('aria-expanded', 'false');
    btn.innerHTML =
      '<svg class="nova-menu-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>' +
      '<svg class="nova-menu-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>';

    if (cta && cta.parentNode) { cta.parentNode.insertBefore(btn, cta); }
    else { header.appendChild(btn); }

    function setOpen(open) {
      header.classList.toggle('menu-open', open);
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      btn.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
      document.body.style.overflow = open ? 'hidden' : '';
      if (open) {
        var first = nav.querySelector('a');
        if (first) { first.focus({ preventScroll: true }); }
      } else {
        btn.focus({ preventScroll: true });
      }
    }

    btn.addEventListener('click', function () {
      setOpen(!header.classList.contains('menu-open'));
    });
    nav.addEventListener('click', function (e) {
      if (e.target.closest && e.target.closest('a')) { setOpen(false); document.body.style.overflow = ''; header.classList.remove('menu-open'); btn.setAttribute('aria-expanded', 'false'); btn.setAttribute('aria-label', 'Open menu'); }
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && header.classList.contains('menu-open')) {
        header.classList.remove('menu-open');
        btn.setAttribute('aria-expanded', 'false');
        btn.setAttribute('aria-label', 'Open menu');
        document.body.style.overflow = '';
        btn.focus();
      }
    });
  }

  /* ---------- Scrollspy ---------- */
  function initSpy() {
    var links = Array.prototype.slice.call(document.querySelectorAll('.nova-nav a[href^="#"]'));
    if (!links.length || !('IntersectionObserver' in window)) { return; }
    var map = {};
    links.forEach(function (a) {
      var id = a.getAttribute('href').slice(1);
      var sec = document.getElementById(id);
      if (sec) { map[id] = { link: a, sec: sec }; }
    });
    var ids = Object.keys(map);
    if (!ids.length) { return; }
    function activate(id) {
      links.forEach(function (a) { a.classList.remove('is-active'); });
      if (id && map[id]) { map[id].link.classList.add('is-active'); }
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) { activate(en.target.id); }
      });
    }, { rootMargin: '-40% 0px -55% 0px', threshold: 0 });
    ids.forEach(function (id) {
      var anchor = map[id].sec;
      var section = anchor.closest ? (anchor.closest('section, footer, header') || anchor) : anchor;
      io.observe(section);
    });
  }

  /* ---------- Progress + back-to-top ---------- */
  function initChrome() {
    var bar = document.querySelector('[data-nova-progress]');
    var top = document.querySelector('[data-nova-top]');
    var ticking = false;
    function update() {
      ticking = false;
      var y = window.pageYOffset || document.documentElement.scrollTop;
      var max = document.documentElement.scrollHeight - window.innerHeight;
      if (bar) { bar.style.transform = 'scaleX(' + (max > 0 ? Math.min(1, y / max) : 0) + ')'; }
      if (top) {
        var show = y > 600;
        top.classList.toggle('is-visible', show);
        if (show) { top.removeAttribute('hidden'); }
        else { top.setAttribute('hidden', ''); }
      }
    }
    window.addEventListener('scroll', function () {
      if (!ticking) { ticking = true; window.requestAnimationFrame(update); }
    }, { passive: true });
    update();
    if (top) {
      top.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
      });
    }
  }

  /* ---------- Hero entrance (staggered, per active slide) ---------- */
  function initEntrance() {
    if (reduceMotion) { return; }
    var slides = Array.prototype.slice.call(document.querySelectorAll('.nova-slider .nova-slide'));
    if (!slides.length) { return; }
    slides.forEach(function (slide) {
      var kids = Array.prototype.slice.call(slide.children).filter(function (el) {
        return !el.classList.contains('nova-hero-img');
      });
      kids.forEach(function (el, i) {
        el.classList.add('nova-hero-enter');
        el.style.transitionDelay = Math.min(i * 90, 450) + 'ms';
      });
    });
    function paint() {
      slides.forEach(function (slide) {
        var on = slide.classList.contains('is-active');
        Array.prototype.forEach.call(slide.querySelectorAll('.nova-hero-enter'), function (el) {
          el.classList.toggle('is-in', on);
        });
      });
    }
    paint();
    if ('MutationObserver' in window) {
      var mo = new MutationObserver(function () { paint(); });
      slides.forEach(function (s) { mo.observe(s, { attributes: true, attributeFilter: ['class'] }); });
    }
  }

  /* ---------- Slider live region (no slider.js edits needed) ---------- */
  function initLiveRegion() {
    var slider = document.querySelector('.nova-slider');
    if (!slider) { return; }
    var live = document.createElement('p');
    live.className = 'nova-sr-live';
    live.setAttribute('aria-live', 'polite');
    live.setAttribute('role', 'status');
    slider.appendChild(live);
    var slides = Array.prototype.slice.call(slider.querySelectorAll('.nova-slide'));
    function titleOf(slide) {
      var h = slide.querySelector('h1, h2');
      return h ? h.textContent.trim() : '';
    }
    function announce() {
      var idx = slides.findIndex(function (s) { return s.classList.contains('is-active'); });
      if (idx < 0) { return; }
      live.textContent = 'Showing property ' + (idx + 1) + ' of ' + slides.length + ': ' + titleOf(slides[idx]);
    }
    announce();
    if ('MutationObserver' in window) {
      var mo = new MutationObserver(function (muts) {
        var hit = muts.some(function (m) { return m.attributeName === 'class'; });
        if (hit) { announce(); }
      });
      slides.forEach(function (s) { mo.observe(s, { attributes: true, attributeFilter: ['class'] }); });
    }
  }

  /* ---------- AJAX inquiry form (graceful fallback to POST) ---------- */
  function initForm() {
    var form = document.getElementById('lumora-inquiry-form');
    if (!form || typeof window.fetch === 'undefined' || !window.NOVA_ENHANCE) { return; }
    var status = document.getElementById('lumora-form-status');
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var data = new FormData(form);
      var name = (data.get('lumora_name') || '').toString().trim();
      var email = (data.get('lumora_email') || '').toString().trim();
      var msg = (data.get('lumora_message') || '').toString().trim();
      var valid = true;
      [['lumora_name', name], ['lumora_email', email], ['lumora_message', msg]].forEach(function (pair) {
        var field = form.querySelector('[name="' + pair[0] + '"]');
        var bad = !pair[1] || (pair[0] === 'lumora_email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(pair[1]));
        if (field) { field.setAttribute('aria-invalid', bad ? 'true' : 'false'); }
        if (bad) { valid = false; }
      });
      if (!valid) {
        if (status) { status.textContent = 'Please add your name, a valid email and a few words.'; }
        return;
      }
      var btn = form.querySelector('.nova-form-submit');
      if (btn) { btn.disabled = true; btn.textContent = 'Sending…'; }
      if (status) { status.textContent = 'Sending your message…'; }
      data.append('action', 'lumora_inquiry');
      data.append('nonce', window.NOVA_ENHANCE.nonce);
      window.fetch(window.NOVA_ENHANCE.ajaxUrl, { method: 'POST', body: data, credentials: 'same-origin' })
        .then(function (res) { return res.json().then(function (j) { return { ok: res.ok && j.success, j: j }; }); })
        .then(function (out) {
          if (out.ok) {
            form.outerHTML = '<p class="nova-form-success" role="status">' + out.j.data.message + ' Prefer to talk? <a href="tel:+61754408899">+61 7 5440 8899</a>.</p>';
          } else {
            throw new Error((out.j.data && out.j.data.message) || 'send failed');
          }
        })
        .catch(function () {
          if (btn) { btn.disabled = false; btn.textContent = 'Send message →'; }
          if (status) { status.textContent = 'Could not send just now. Trying the standard way…'; }
          form.submit();
        });
    });
  }

  /* ---------- Brutalist quote rotator ([data-cn-quote], prev/next) ---------- */
  function initRotator() {
    var root = document.querySelector('[data-cn-rotator]');
    if (!root) { return; }
    var quotes = Array.prototype.slice.call(root.querySelectorAll('[data-cn-quote]'));
    if (quotes.length < 2) { return; }
    var prev = root.querySelector('[data-cn-prev]');
    var next = root.querySelector('[data-cn-next]');
    var count = root.querySelector('[data-cn-count]');
    var live = document.createElement('p');
    live.className = 'nova-sr-live';
    live.setAttribute('aria-live', 'polite');
    root.appendChild(live);
    var i = 0;
    function show(n) {
      i = (n + quotes.length) % quotes.length;
      quotes.forEach(function (q, k) {
        var on = k === i;
        q.classList.toggle('is-active', on);
        if (on) { q.removeAttribute('aria-hidden'); }
        else { q.setAttribute('aria-hidden', 'true'); }
      });
      if (count) { count.textContent = (i + 1 < 10 ? '0' : '') + (i + 1) + ' / ' + (quotes.length < 10 ? '0' + quotes.length : quotes.length); }
      var who = quotes[i].querySelector('.cn-voice-name');
      live.textContent = 'Client story ' + (i + 1) + ' of ' + quotes.length + (who ? ': ' + who.textContent.trim() : '');
    }
    if (prev) { prev.addEventListener('click', function () { show(i - 1); }); }
    if (next) { next.addEventListener('click', function () { show(i + 1); }); }
    root.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowLeft') { e.preventDefault(); show(i - 1); }
      else if (e.key === 'ArrowRight') { e.preventDefault(); show(i + 1); }
    });
    show(0);
  }

  /* ---------- Break-band parallax ([data-cn-parallax]) ---------- */
  function initParallax() {
    var img = document.querySelector('[data-cn-parallax]');
    if (!img || reduceMotion || !('requestAnimationFrame' in window)) { return; }
    var ticking = false;
    function update() {
      ticking = false;
      var r = img.getBoundingClientRect();
      var vh = window.innerHeight;
      if (r.bottom < 0 || r.top > vh) { return; }
      var p = (r.top + r.height / 2 - vh / 2) / vh; /* -0.5..0.5 */
      img.style.transform = 'translateY(' + (p * -60).toFixed(1) + 'px)';
    }
    window.addEventListener('scroll', function () {
      if (!ticking) { ticking = true; window.requestAnimationFrame(update); }
    }, { passive: true });
    update();
  }

  function ready(fn) {
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', fn); }
    else { fn(); }
  }

  ready(function () {
    initMenu();
    initSpy();
    initChrome();
    initEntrance();
    initLiveRegion();
    initForm();
    initRotator();
    initParallax();
  });
})();

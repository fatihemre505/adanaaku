(function () {
  'use strict';
  var d = document, de = d.documentElement;
  de.classList.remove('no-js');
  
  // Prefers reduced motion
  var prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  var nav = d.querySelector('header.nav'),
      bar = d.getElementById('progress'),
      top = d.querySelector('.totop'),
      drawer = d.getElementById('drawer'),
      burger = d.getElementById('burger');

  // Scroll: header state, progress, back-to-top
  var tick = false;
  function onScroll() {
    var y = window.scrollY, h = de.scrollHeight - innerHeight;
    nav.classList.toggle('scrolled', y > 20);
    bar.style.width = (h > 0 ? (y / h) * 100 : 0) + '%';
    top.classList.toggle('show', y > 700);
    tick = false;
  }
  addEventListener('scroll', function () {
    if (!tick) { tick = true; requestAnimationFrame(onScroll); }
  }, { passive: true });
  onScroll();
  
  top.addEventListener('click', function () { 
    if (prefersReduced) {
      window.scrollTo({ top: 0 });
    } else {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  });

  // Mobile drawer with smooth transitions
  function setDrawer(open) {
    drawer.classList.toggle('open', open);
    burger.setAttribute('aria-expanded', open);
    d.body.style.overflow = open ? 'hidden' : '';
  }
  burger.addEventListener('click', function () { setDrawer(!drawer.classList.contains('open')); });
  drawer.addEventListener('click', function (e) {
    if (e.target.closest('a') || e.target.closest('[data-close]')) setDrawer(false);
  });
  addEventListener('keydown', function (e) { if (e.key === 'Escape') setDrawer(false); });
  addEventListener('resize', function () { if (innerWidth > 1024) setDrawer(false); });

  // Reveal on scroll with optimized timing
  var rv = d.querySelectorAll('.rv');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (es) {
      es.forEach(function (e) {
        if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
      });
    }, { threshold: .08, rootMargin: '0px 0px -50px 0px' });
    rv.forEach(function (el) { io.observe(el); });
  } else rv.forEach(function (el) { el.classList.add('in'); });

  // Active nav link with smooth tracking
  var links = [].slice.call(d.querySelectorAll('.links a'));
  if ('IntersectionObserver' in window) {
    var so = new IntersectionObserver(function (es) {
      es.forEach(function (e) {
        if (e.isIntersecting) {
          links.forEach(function (a) { a.classList.toggle('active', a.getAttribute('href') === '#' + e.target.id); });
        }
      });
    }, { rootMargin: '-44% 0px -50% 0px' });
    links.forEach(function (a) {
      var s = d.querySelector(a.getAttribute('href'));
      if (s) so.observe(s);
    });
  }

  // Count-up with easing
  var cu = d.querySelectorAll('[data-count]');
  if ('IntersectionObserver' in window) {
    var co = new IntersectionObserver(function (es) {
      es.forEach(function (e) {
        if (!e.isIntersecting) return;
        co.unobserve(e.target);
        var el = e.target, to = +el.dataset.count, suf = el.dataset.suffix || '', pre = el.dataset.prefix || '', t0 = performance.now();
        // Cubic easing for smooth count-up
        (function step(t) {
          var p = Math.min((t - t0) / 1600, 1);
          var eased = 1 - Math.pow(1 - p, 3);
          el.textContent = pre + Math.round(to * eased) + suf;
          if (p < 1) requestAnimationFrame(step);
        })(t0);
      });
    }, { threshold: .5 });
    cu.forEach(function (el) { co.observe(el); });
  }

  // Card spotlight with performance optimization
  d.querySelectorAll('.card').forEach(function (c) {
    var isHovering = false;
    c.addEventListener('pointerenter', function () { isHovering = true; });
    c.addEventListener('pointerleave', function () { isHovering = false; });
    c.addEventListener('pointermove', function (e) {
      if (!isHovering) return;
      var r = c.getBoundingClientRect();
      c.style.setProperty('--mx', (e.clientX - r.left) + 'px');
      c.style.setProperty('--my', (e.clientY - r.top) + 'px');
    }, { passive: true });
  });

  // Service card -> preselect form option
  d.querySelectorAll('[data-service]').forEach(function (a) {
    a.addEventListener('click', function () {
      var sel = d.querySelector('select[name=hizmet]');
      if (sel) sel.value = a.dataset.service;
    });
  });
  
  // Smooth scroll for anchor links
  d.querySelectorAll('a[href^="#"]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      var href = this.getAttribute('href');
      if (href === '#') return;
      var target = d.querySelector(href);
      if (target) {
        e.preventDefault();
        if (prefersReduced) {
          target.scrollIntoView();
        } else {
          target.scrollIntoView({ behavior: 'smooth' });
        }
      }
    });
  });

  // Lazy load images for better performance
  if ('IntersectionObserver' in window && 'loading' in HTMLImageElement.prototype) {
    d.querySelectorAll('img[loading="lazy"]').forEach(function (img) {
      img.addEventListener('load', function () {
        this.classList.add('loaded');
      });
    });
  }
})();

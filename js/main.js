/* Shoten — sitio institucional */
(function () {
  'use strict';

  /*
   * URL de la aplicación (fe_shoten). En local ambos proyectos viven en
   * C:\WinNMP\WWW\shoten, por eso la ruta relativa. En el hosting se cambia
   * por el dominio definitivo (p. ej. https://app.shoten.com.ar).
   */
  var APP_URL = '../fe_shoten';
  var APP_ROUTES = {
    login: '/login.php',
    register: '/register.php'
  };

  document.querySelectorAll('[data-app]').forEach(function (el) {
    var route = APP_ROUTES[el.getAttribute('data-app')];
    if (route) el.setAttribute('href', APP_URL + route);
  });

  // Header con sombra al scrollear
  var header = document.querySelector('.header');
  var onScroll = function () {
    if (header) header.classList.toggle('is-scrolled', window.scrollY > 8);
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  // Menú mobile
  var burger = document.querySelector('.burger');
  if (burger) {
    burger.addEventListener('click', function () {
      var open = document.body.classList.toggle('menu-open');
      burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  // Desplegables
  var items = document.querySelectorAll('.nav__item[data-dropdown]');
  var closeAll = function (except) {
    items.forEach(function (item) {
      if (item !== except) {
        item.classList.remove('is-open');
        item.querySelector('.nav__link').setAttribute('aria-expanded', 'false');
      }
    });
  };
  items.forEach(function (item) {
    var btn = item.querySelector('.nav__link');
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      closeAll(item);
      var open = item.classList.toggle('is-open');
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  });
  document.addEventListener('click', function () { closeAll(null); });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeAll(null);
  });
  document.querySelectorAll('.nav a').forEach(function (a) {
    a.addEventListener('click', function () { document.body.classList.remove('menu-open'); });
  });

  // Demo del hero: pestañas con rotación automática
  var demo = document.querySelector('[data-demo]');
  if (demo) {
    var tabs = demo.querySelectorAll('.demo__tab');
    var screens = demo.querySelectorAll('.screen');
    var title = demo.querySelector('[data-demo-title]');
    var current = 0;
    var timer = null;

    var show = function (i) {
      current = i;
      tabs.forEach(function (t, idx) {
        t.classList.toggle('is-active', idx === i);
        t.setAttribute('aria-selected', idx === i ? 'true' : 'false');
      });
      screens.forEach(function (s, idx) { s.classList.toggle('is-active', idx === i); });
      if (title) title.textContent = tabs[i].getAttribute('data-title') || tabs[i].textContent;
    };
    var start = function () {
      stop();
      timer = setInterval(function () { show((current + 1) % tabs.length); }, 4500);
    };
    var stop = function () { if (timer) clearInterval(timer); };

    tabs.forEach(function (t, idx) {
      t.addEventListener('click', function () { show(idx); start(); });
    });
    demo.addEventListener('mouseenter', stop);
    demo.addEventListener('mouseleave', start);

    show(0);
    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) start();
  }

  // Aparición al scrollear y contadores
  var animateCount = function (el) {
    var target = parseFloat(el.getAttribute('data-count'));
    var suffix = el.getAttribute('data-suffix') || '';
    var prefix = el.getAttribute('data-prefix') || '';
    var t0 = null;
    var step = function (ts) {
      if (!t0) t0 = ts;
      var p = Math.min((ts - t0) / 1200, 1);
      var v = Math.round(target * (1 - Math.pow(1 - p, 3)));
      el.textContent = prefix + v.toLocaleString('es-AR') + suffix;
      if (p < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  };

  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-visible');
        entry.target.querySelectorAll('[data-count]').forEach(animateCount);
        if (entry.target.hasAttribute('data-count')) animateCount(entry.target);
        io.unobserve(entry.target);
      });
    }, { threshold: 0.15 });
    document.querySelectorAll('.reveal, [data-count]').forEach(function (el) { io.observe(el); });
  } else {
    document.querySelectorAll('.reveal').forEach(function (el) { el.classList.add('is-visible'); });
  }

  // Precios: mensual / anual
  var toggle = document.querySelector('[data-billing]');
  if (toggle) {
    var buttons = toggle.querySelectorAll('button');
    buttons.forEach(function (b) {
      b.addEventListener('click', function () {
        var mode = b.getAttribute('data-mode');
        buttons.forEach(function (x) { x.classList.toggle('is-active', x === b); });
        document.querySelectorAll('[data-price-' + mode + ']').forEach(function (el) {
          el.textContent = el.getAttribute('data-price-' + mode);
        });
      });
    });
  }

  // Año del footer
  document.querySelectorAll('[data-year]').forEach(function (el) {
    el.textContent = new Date().getFullYear();
  });
})();

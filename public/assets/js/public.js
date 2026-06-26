/* ============================================================
   Keepnew — Public JS (vanilla, no dependencies)
   ============================================================ */

(function () {
  'use strict';

  /* ── FAQ Accordion ─────────────────────────────────────────── */
  function initFaq() {
    var items = document.querySelectorAll('.kn-faq__item');
    if (!items.length) return;

    items.forEach(function (item) {
      var btn = item.querySelector('.kn-faq__question');
      if (!btn) return;

      btn.addEventListener('click', function () {
        var isOpen = item.classList.contains('is-open');

        // Close all
        items.forEach(function (el) {
          el.classList.remove('is-open');
        });

        // Toggle current
        if (!isOpen) {
          item.classList.add('is-open');
        }
      });
    });
  }

  /* ── Smooth Scroll ─────────────────────────────────────────── */
  function initSmoothScroll() {
    var anchors = document.querySelectorAll('a[href^="#"]');
    anchors.forEach(function (link) {
      link.addEventListener('click', function (e) {
        var hash = link.getAttribute('href');
        if (hash === '#') return;
        var target = document.querySelector(hash);
        if (!target) return;
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        // Update URL without jumping
        if (history.pushState) {
          history.pushState(null, '', hash);
        }
      });
    });
  }

  /* ── Mobile Nav Toggle ─────────────────────────────────────── */
  function initMobileNav() {
    var toggle = document.querySelector('.kn-nav__toggle');
    var mobile = document.querySelector('.kn-nav__mobile');
    if (!toggle || !mobile) return;

    toggle.addEventListener('click', function () {
      mobile.classList.toggle('is-open');
      var expanded = mobile.classList.contains('is-open');
      toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
    });
  }

  /* ── Transparent Nav Scroll ──────────────────────────────── */
  function initNavScroll() {
    var nav = document.querySelector('.kn-nav--transparent');
    if (!nav) return;
    function onScroll() {
      if (window.scrollY > 40) {
        nav.classList.add('kn-nav--scrolled');
      } else {
        nav.classList.remove('kn-nav--scrolled');
      }
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* ── Init ──────────────────────────────────────────────────── */
  document.addEventListener('DOMContentLoaded', function () {
    initFaq();
    initSmoothScroll();
    initMobileNav();
    initNavScroll();
  });
}());

// Dropdown & mega menu toggle
document.querySelectorAll('.kn-nav__has-drop, .kn-nav__has-mega').forEach(function(el) {
  el.addEventListener('mouseenter', function() { el.classList.add('open'); });
  el.addEventListener('mouseleave', function() { el.classList.remove('open'); });
});

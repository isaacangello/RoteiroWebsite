(function () {
  'use strict';

  var BREAKPOINT     = 1024;
  var SWIPE_DISTANCE = 60;
  var EDGE_THRESHOLD = 35;

  var toggle  = document.getElementById('rta-menu-toggle');
  var drawer  = document.getElementById('rta-nav-drawer');
  var overlay = document.getElementById('rta-overlay');
  var closeBtn = document.getElementById('rta-nav-close');
  var menu    = drawer && drawer.querySelector('.rta-menu');

  if (!toggle || !drawer || !overlay || !menu) return;

  var open = false;

  // ─── utils ────────────────────────────────────────────

  function isDesktop() {
    return window.innerWidth > BREAKPOINT;
  }

  function setTranslate(x) {
    drawer.style.transform = 'translate3d(' + x + 'px, 0, 0)';
  }

  function getTranslate() {
    var style  = window.getComputedStyle(drawer);
    var matrix = style.transform.match(/matrix\(([^)]+)\)/);
    if (!matrix) return 0;
    return parseFloat(matrix[1].split(', ')[4]) || 0;
  }

  // ─── open / close ─────────────────────────────────────

  function openDrawer() {
    if (open) return;
    open = true;
    drawer.classList.add('rta-nav--open');
    setTranslate(0);
    overlay.classList.add('rta-overlay--visible');
    toggle.setAttribute('aria-expanded', 'true');
    document.body.classList.add('rta-no-scroll');
    closeBtn.focus();
  }

  function closeDrawer() {
    if (!open) return;
    open = false;
    drawer.classList.remove('rta-nav--open');
    setTranslate(-280);
    overlay.classList.remove('rta-overlay--visible');
    toggle.setAttribute('aria-expanded', 'false');
    document.body.classList.remove('rta-no-scroll');
    toggle.focus();
  }

  // ─── hamburger button ─────────────────────────────────

  toggle.addEventListener('click', function (e) {
    e.stopPropagation();
    if (open) { closeDrawer(); }
    else      { openDrawer();  }
  });

  closeBtn.addEventListener('click', closeDrawer);
  overlay.addEventListener('click', closeDrawer);

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && open) closeDrawer();
  });

  // close on resize past breakpoint
  var resizeTimer;
  window.addEventListener('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
      if (isDesktop() && open) closeDrawer();
    }, 150);
  });

  // ─── swipe / drag (touch + pointer for desktop testing) ─

  var startX = 0, startY = 0;
  var tracking = false;
  var dir = null; // 'h' | 'v'

  // ---- touch (mobile native) ----
  document.addEventListener('touchstart', function (e) {
    if (isDesktop()) return;
    var t = e.changedTouches[0];
    startX = t.clientX;
    startY = t.clientY;
    tracking = true;
    dir = null;
  }, { passive: true });

  document.addEventListener('touchmove', function (e) {
    if (!tracking || isDesktop()) return;
    var t = e.changedTouches[0];
    var dx = t.clientX - startX;
    var dy = t.clientY - startY;

    if (!dir) {
      if (Math.abs(dx) < 8 && Math.abs(dy) < 8) return;
      dir = Math.abs(dx) > Math.abs(dy) ? 'h' : 'v';
    }

    if (dir !== 'h') return;
    e.preventDefault();

    if (open) {
      var nx = Math.min(0, dx);
      setTranslate(nx);
    } else {
      if (startX < EDGE_THRESHOLD && dx > 0) {
        setTranslate(dx - 280);
      }
    }
  }, { passive: false });

  document.addEventListener('touchend', function (e) {
    if (!tracking) return;
    tracking = false;
    if (dir !== 'h') return;
    var dx = e.changedTouches[0].clientX - startX;

    if (open) {
      if (dx < -SWIPE_DISTANCE) closeDrawer();
      else openDrawer();
    } else {
      if (dx > SWIPE_DISTANCE) openDrawer();
      else setTranslate(-280);
    }
    dir = null;
  });

  // ---- pointer (desktop mouse, fallback) ----
  document.addEventListener('pointerdown', function (e) {
    if (isDesktop()) return;
    startX = e.clientX;
    startY = e.clientY;
    tracking = true;
    dir = null;
  });

  document.addEventListener('pointermove', function (e) {
    if (!tracking || isDesktop()) return;
    var dx = e.clientX - startX;
    var dy = e.clientY - startY;

    if (!dir) {
      if (Math.abs(dx) < 8 && Math.abs(dy) < 8) return;
      dir = Math.abs(dx) > Math.abs(dy) ? 'h' : 'v';
    }

    if (dir !== 'h') {
      // if movement is mostly vertical, stop tracking
      tracking = false;
      return;
    }
    e.preventDefault();

    if (open) {
      setTranslate(Math.min(0, dx));
    } else {
      if (startX < EDGE_THRESHOLD && dx > 0) {
        setTranslate(dx - 280);
      }
    }
  });

  document.addEventListener('pointerup', function (e) {
    if (!tracking) return;
    tracking = false;
    if (dir !== 'h') return;
    var dx = e.clientX - startX;

    if (open) {
      if (dx < -SWIPE_DISTANCE) closeDrawer();
      else openDrawer();
    } else {
      if (dx > SWIPE_DISTANCE) openDrawer();
      else setTranslate(-280);
    }
    dir = null;
  });

  // ─── desktop hover submenus ───────────────────────────

  var items = menu.querySelectorAll('.menu-item-has-children');

  // initial hide (only on desktop – mobile hides via CSS)
  if (isDesktop()) {
    menu.querySelectorAll('.sub-menu').forEach(function (s) {
      s.style.display = 'none';
    });
  }

  menu.addEventListener('mouseover', function (e) {
    if (!isDesktop()) return;
    var item = e.target.closest('.menu-item-has-children');
    if (item && menu.contains(item)) {
      var sub = item.querySelector('.sub-menu');
      if (sub) {
        item.classList.add('rta-menu-open');
        sub.style.display = 'block';
      }
    }
  });

  menu.addEventListener('mouseout', function (e) {
    if (!isDesktop()) return;
    var item = e.target.closest('.menu-item-has-children');
    if (!item) return;
    var related = e.relatedTarget;
    while (related) {
      if (related === item) return;
      related = related.parentNode;
    }
    var sub = item.querySelector('.sub-menu');
    if (sub) {
      item.classList.remove('rta-menu-open');
      sub.style.display = 'none';
    }
  });

  // ─── mobile submenu toggle (tap) ──────────────────────

  items.forEach(function (item) {
    var link = item.querySelector('a');
    if (!link) return;
    var sub = item.querySelector('.sub-menu');
    if (!sub) return;

    link.addEventListener('click', function (e) {
      if (isDesktop()) return;
      e.preventDefault();

      var wasOpen = item.classList.contains('rta-menu-open');

      // close siblings
      items.forEach(function (other) {
        if (other !== item) {
          other.classList.remove('rta-menu-open');
          var s = other.querySelector('.sub-menu');
          if (s) s.style.display = 'none';
        }
      });

      if (wasOpen) {
        item.classList.remove('rta-menu-open');
        sub.style.display = 'none';
      } else {
        item.classList.add('rta-menu-open');
        sub.style.display = 'block';
      }
    });
  });
})();

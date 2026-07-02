(function () {
  'use strict';

  var BREAKPOINT = 1024;
  var SWIPE_THRESHOLD = 80;
  var drawerOpen = false;

  var toggle  = document.getElementById('rta-menu-toggle');
  var drawer  = document.getElementById('rta-nav-drawer');
  var overlay = document.getElementById('rta-overlay');
  var closeBtn = document.getElementById('rta-nav-close');
  var menu    = drawer && drawer.querySelector('.rta-menu');

  if (!toggle || !drawer || !overlay || !menu) return;

  // ─── Desktop hover (only above breakpoint) ──────────────
  var submenus = menu.querySelectorAll('.sub-menu');
  var items   = menu.querySelectorAll('.menu-item-has-children');

  submenus.forEach(function (sub) {
    sub.style.display = 'none';
  });

  function isDesktop() {
    return window.innerWidth > BREAKPOINT;
  }

  // Desktop hover events
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

  // ─── Submenu toggle (mobile: tap to open/close) ──────
  items.forEach(function (item) {
    var link = item.querySelector('a');
    if (!link) return;

    link.addEventListener('click', function (e) {
      if (isDesktop()) return;
      var sub = item.querySelector('.sub-menu');
      if (!sub) return;

      e.preventDefault();

      var isOpen = item.classList.contains('rta-menu-open');

      // Close all others
      items.forEach(function (other) {
        if (other !== item) {
          other.classList.remove('rta-menu-open');
          var s = other.querySelector('.sub-menu');
          if (s) s.style.display = 'none';
        }
      });

      // Toggle this one
      if (isOpen) {
        item.classList.remove('rta-menu-open');
        sub.style.display = 'none';
      } else {
        item.classList.add('rta-menu-open');
        sub.style.display = 'block';
      }
    });
  });

  // ─── Drawer: open / close ─────────────────────────────
  function openDrawer() {
    if (drawerOpen) return;
    drawerOpen = true;
    drawer.classList.add('rta-nav--open');
    overlay.classList.add('rta-overlay--visible');
    toggle.setAttribute('aria-expanded', 'true');
    toggle.setAttribute('aria-label', 'Fechar menu');
    document.body.classList.add('rta-no-scroll');
    closeBtn.focus();
  }

  function closeDrawer() {
    if (!drawerOpen) return;
    drawerOpen = false;
    drawer.classList.remove('rta-nav--open');
    drawer.style.transform = '';
    overlay.classList.remove('rta-overlay--visible');
    toggle.setAttribute('aria-expanded', 'false');
    toggle.setAttribute('aria-label', 'Abrir menu');
    document.body.classList.remove('rta-no-scroll');
    toggle.focus();
  }

  // Hamburger click
  toggle.addEventListener('click', function () {
    if (drawerOpen) {
      closeDrawer();
    } else {
      openDrawer();
    }
  });

  // Close button
  closeBtn.addEventListener('click', closeDrawer);

  // Overlay click
  overlay.addEventListener('click', closeDrawer);

  // Escape key
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && drawerOpen) {
      closeDrawer();
    }
  });

  // Close on resize to desktop
  window.addEventListener('resize', function () {
    if (isDesktop() && drawerOpen) {
      closeDrawer();
    }
  });

  // ─── Swipe / Drag gesture (Pointer Events) ────────────
  var dragStartX = 0;
  var dragStartY = 0;
  var isDragging = false;
  var dragDirection = null; // 'horizontal' | 'vertical'

  function getTranslateX(el) {
    var style = window.getComputedStyle(el);
    var transform = style.transform;
    if (transform === 'none') return 0;
    var matrix = transform.match(/matrix\(([^)]+)\)/);
    if (matrix) {
      var values = matrix[1].split(', ');
      return parseFloat(values[4]) || 0;
    }
    return 0;
  }

  function setDrawerTranslate(x) {
    drawer.style.transform = 'translate3d(' + x + 'px, 0, 0)';
  }

  // Swipe from left edge: open drawer
  document.addEventListener('pointerdown', function (e) {
    if (isDesktop()) return;
    // Only track if starting near left edge (within 40px) or if drawer is open
    if (e.clientX < 40 || drawerOpen) {
      dragStartX = e.clientX;
      dragStartY = e.clientY;
      isDragging = true;
      dragDirection = null;
      drawer.setPointerCapture(e.pointerId);
    }
  });

  document.addEventListener('pointermove', function (e) {
    if (!isDragging || isDesktop()) return;

    var dx = e.clientX - dragStartX;
    var dy = e.clientY - dragStartY;

    // Determine direction on first significant move
    if (!dragDirection) {
      if (Math.abs(dx) > 10 || Math.abs(dy) > 10) {
        dragDirection = Math.abs(dx) > Math.abs(dy) ? 'horizontal' : 'vertical';
      }
    }

    if (dragDirection !== 'horizontal') return;

    e.preventDefault();

    if (drawerOpen) {
      // Dragging drawer closed: constrain dx to negative values
      var translate = Math.min(0, dx);
      setDrawerTranslate(translate);
    } else {
      // Dragging from left edge to open: allow only positive
      if (dx > 0) {
        setDrawerTranslate(dx - 280);
      }
    }
  });

  document.addEventListener('pointerup', function (e) {
    if (!isDragging) return;
    isDragging = false;

    if (dragDirection !== 'horizontal') return;

    var dx = e.clientX - dragStartX;

    if (drawerOpen) {
      if (dx < -SWIPE_THRESHOLD) {
        closeDrawer();
      } else {
        setDrawerTranslate(0);
        drawer.classList.add('rta-nav--open');
      }
    } else {
      if (dx > SWIPE_THRESHOLD) {
        openDrawer();
      } else {
        setDrawerTranslate(-280);
        drawer.classList.remove('rta-nav--open');
      }
    }

    dragDirection = null;
  });
})();

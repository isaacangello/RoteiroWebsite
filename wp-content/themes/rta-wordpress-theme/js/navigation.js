(function () {
  'use strict';

  function initNav() {
    var nav = document.querySelector('.rta-menu');
    if (!nav) return;

    var submenus = nav.querySelectorAll('.sub-menu');
    var items = nav.querySelectorAll('.menu-item-has-children');

    // Hide all submenus on load via inline style
    submenus.forEach(function (sub) {
      sub.style.display = 'none';
    });

    nav.addEventListener('mouseover', function (e) {
      var item = e.target.closest('.menu-item-has-children');
      if (item && nav.contains(item)) {
        var sub = item.querySelector('.sub-menu');
        if (sub) {
          item.classList.add('rta-menu-open');
          sub.style.display = 'block';
        }
      }
    });

    nav.addEventListener('mouseout', function (e) {
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

    // Mobile: click to toggle
    items.forEach(function (item) {
      var link = item.querySelector('a');
      if (!link) return;
      link.addEventListener('click', function (e) {
        if (window.innerWidth > 768) return;
        var sub = item.querySelector('.sub-menu');
        if (item.classList.contains('rta-menu-open')) return;
        e.preventDefault();
        items.forEach(function (other) {
          if (other !== item) {
            other.classList.remove('rta-menu-open');
            var s = other.querySelector('.sub-menu');
            if (s) s.style.display = 'none';
          }
        });
        item.classList.add('rta-menu-open');
        if (sub) sub.style.display = 'block';
      });
    });

    document.addEventListener('click', function (e) {
      if (!e.target.closest('.rta-menu')) {
        items.forEach(function (item) {
          item.classList.remove('rta-menu-open');
          var sub = item.querySelector('.sub-menu');
          if (sub) sub.style.display = 'none';
        });
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNav);
  } else {
    initNav();
  }
})();

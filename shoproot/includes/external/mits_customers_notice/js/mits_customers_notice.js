(function () {
  'use strict';

  function storageGet(key) {
    try { return window.localStorage.getItem(key); } catch (e) { return null; }
  }

  function storageSet(key, value) {
    try { window.localStorage.setItem(key, value); } catch (e) {}
  }

  function sessionGet(key) {
    try { return window.sessionStorage.getItem(key); } catch (e) { return null; }
  }

  function sessionSet(key, value) {
    try { window.sessionStorage.setItem(key, value); } catch (e) {}
  }

  function pad2(value) {
    value = parseInt(value, 10) || 0;
    return value < 10 ? '0' + value : String(value);
  }

  function todayKey() {
    var d = new Date();
    return d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate());
  }

  function shouldShow(item) {
    var frequency = item.getAttribute('data-frequency') || 'always';
    var key = item.getAttribute('data-storage-key') || '';
    if (!key) { return true; }
    if (frequency === 'session') {
      if (sessionGet(key) === 'shown') { return false; }
      sessionSet(key, 'shown');
    }
    if (frequency === 'day') {
      if (storageGet(key) === todayKey()) { return false; }
      storageSet(key, todayKey());
    }
    if (frequency === 'dismiss' && storageGet(key) === 'dismissed') {
      return false;
    }
    return true;
  }

  function reflowToasts() {
    var positions = ['bottom-right', 'bottom-left', 'bottom-center', 'top-right', 'top-left', 'top-center'];
    for (var p = 0; p < positions.length; p++) {
      var toasts = document.querySelectorAll('.mits-cn-toast--' + positions[p] + '.is-visible');
      var offset = 0;
      for (var i = 0; i < toasts.length; i++) {
        toasts[i].style.setProperty('--mits-cn-toast-offset', offset + 'px');
        offset += toasts[i].offsetHeight + 10;
      }
    }
  }

  function activateFloating(item) {
    var type = item.getAttribute('data-display-type') || 'notice';
    if (type !== 'modal' && type !== 'toast') { return false; }
    var floating = item.querySelector(type === 'modal' ? '[data-mits-cn-modal]' : '[data-mits-cn-toast]');
    if (floating) {
      floating.classList.add('is-visible');
      if (type === 'toast') { reflowToasts(); }
    }
    return true;
  }

  function findTarget(selector) {
    var selectors = String(selector || '').split(',');
    for (var i = 0; i < selectors.length; i++) {
      var part = selectors[i].replace(/^\s+|\s+$/g, '');
      if (!part) { continue; }
      try {
        var found = document.querySelector(part);
        if (found) { return found; }
      } catch (e) {}
    }
    return null;
  }

  function placeItem(item) {
    var type = item.getAttribute('data-display-type') || 'notice';
    if (type === 'modal' || type === 'toast') {
      document.body.appendChild(item);
      activateFloating(item);
      return;
    }
    if (type === 'topbar') {
      document.body.insertBefore(item, document.body.firstChild);
      return;
    }

    var selector = item.getAttribute('data-selector') || '#main-content, main, .content_big, .content_full, #col_right .col_right_inner, #col_right, #col_full, #contentwrap, #content, #layout_content, #main';
    var method = item.getAttribute('data-insert-method') || 'prepend';
    var target = findTarget(selector);
    if (!target) {
      document.body.insertBefore(item, document.body.firstChild);
      return;
    }
    if (method === 'before') {
      target.parentNode.insertBefore(item, target);
    } else if (method === 'after') {
      target.parentNode.insertBefore(item, target.nextSibling);
    } else if (method === 'append') {
      target.appendChild(item);
    } else {
      target.insertBefore(item, target.firstChild);
    }
  }

  function removeItem(item, remember) {
    if (remember && (item.getAttribute('data-frequency') || '') === 'dismiss') {
      var key = item.getAttribute('data-storage-key') || '';
      if (key) { storageSet(key, 'dismissed'); }
    }
    if (item.parentNode) { item.parentNode.removeChild(item); }
    reflowToasts();
  }

  function bindClose(item) {
    var buttons = item.querySelectorAll('[data-mits-cn-close]');
    for (var i = 0; i < buttons.length; i++) {
      buttons[i].addEventListener('click', function () { removeItem(item, true); });
    }
    var modal = item.querySelector('[data-mits-cn-modal]');
    if (modal && item.getAttribute('data-dismissible') === '1') {
      modal.addEventListener('click', function (event) {
        if (event.target === modal) { removeItem(item, true); }
      });
    }
  }

  function setupCountdown(item) {
    var counters = item.querySelectorAll('[data-mits-cn-countdown]');
    for (var i = 0; i < counters.length; i++) {
      (function (counter) {
        var end = parseInt(counter.getAttribute('data-end') || '0', 10) * 1000;
        if (!end) { return; }
        function update() {
          var seconds = Math.max(0, Math.floor((end - Date.now()) / 1000));
          var days = Math.floor(seconds / 86400);
          var hours = Math.floor((seconds % 86400) / 3600);
          var minutes = Math.floor((seconds % 3600) / 60);
          var secs = seconds % 60;
          var parts = [];
          if (days > 0) { parts.push(days + ' ' + (counter.getAttribute('data-days') || 'days')); }
          parts.push(pad2(hours) + ' ' + (counter.getAttribute('data-hours') || 'hours'));
          parts.push(pad2(minutes) + ' ' + (counter.getAttribute('data-minutes') || 'minutes'));
          parts.push(pad2(secs) + ' ' + (counter.getAttribute('data-seconds') || 'seconds'));
          counter.textContent = parts.join(' ');
          if (seconds > 0) { window.setTimeout(update, 1000); }
        }
        update();
      }(counters[i]));
    }
  }

  function init() {
    var items = document.querySelectorAll('[data-mits-cn-item]');
    for (var i = 0; i < items.length; i++) {
      var item = items[i];
      if (!shouldShow(item)) {
        removeItem(item, false);
        continue;
      }
      var type = item.getAttribute('data-display-type') || 'notice';
      if (type === 'modal' || type === 'toast' || item.getAttribute('data-placement') !== 'manual') {
        placeItem(item);
      } else {
        activateFloating(item);
      }
      bindClose(item);
      setupCountdown(item);
    }
    var root = document.getElementById('mits-cn-auto-root');
    if (root && root.parentNode) { root.parentNode.removeChild(root); }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
  window.addEventListener('resize', reflowToasts);
}());

(function () {
  'use strict';

  function getBaseUrl() {
    try {
      var fromPhp = (window.__PROJET_BASE_URL || '').toString();
      if (fromPhp && fromPhp !== 'BASE_URL') return fromPhp;
    } catch (e) {}

    var parts = window.location.pathname.split('/').filter(Boolean);
    var idx = parts.indexOf('projet');
    if (idx >= 0) return '/' + parts.slice(0, idx + 1).join('/');
    return '';
  }

  var baseUrl = getBaseUrl();

  function el(tag, attrs, children) {
    var node = document.createElement(tag);
    if (attrs) {
      Object.keys(attrs).forEach(function (k) {
        if (k === 'class') node.className = attrs[k];
        else if (k === 'text') node.textContent = attrs[k];
        else node.setAttribute(k, attrs[k]);
      });
    }
    (children || []).forEach(function (c) {
      if (c == null) return;
      if (typeof c === 'string') node.appendChild(document.createTextNode(c));
      else node.appendChild(c);
    });
    return node;
  }

  function debounce(fn, ms) {
    var t;
    return function () {
      var args = arguments;
      clearTimeout(t);
      t = setTimeout(function () {
        fn.apply(null, args);
      }, ms);
    };
  }

  // -------------------- Smart search (suggestions) --------------------
  function setupSearch(input, wrapper, context) {
    if (!input || !wrapper) return;

    wrapper.classList.add('ai-search-wrapper');

    var suggestions = el('div', { class: 'ai-search-suggestions', role: 'listbox' });
    suggestions.style.display = 'none';
    wrapper.appendChild(suggestions);

    var items = [];
    var activeIndex = -1;

    function hide() {
      suggestions.style.display = 'none';
      suggestions.innerHTML = '';
      items = [];
      activeIndex = -1;
    }

    function show(list) {
      suggestions.innerHTML = '';
      items = list || [];
      activeIndex = -1;

      if (!items.length) {
        hide();
        return;
      }

      items.forEach(function (r, idx) {
        var row = el('div', { class: 'ai-search-item', role: 'option' });
        row.dataset.index = String(idx);

        var title = el('div', { class: 'ai-search-title', text: r.title || '' });
        var meta = el('div', { class: 'ai-search-meta', text: (r.subtitle || '') });

        row.appendChild(title);
        if (r.subtitle) row.appendChild(meta);

        row.addEventListener('mousedown', function (e) {
          // mousedown to prevent blur before click
          e.preventDefault();
          if (r.url) window.location.href = r.url;
        });

        suggestions.appendChild(row);
      });

      suggestions.style.display = 'block';
    }

    function setActive(nextIndex) {
      if (!items.length) return;
      activeIndex = Math.max(0, Math.min(nextIndex, items.length - 1));

      var nodes = suggestions.querySelectorAll('.ai-search-item');
      nodes.forEach(function (n) {
        n.classList.remove('active');
      });
      var active = nodes[activeIndex];
      if (active) {
        active.classList.add('active');
        active.scrollIntoView({ block: 'nearest' });
      }
    }

    var fetchSuggestions = debounce(function () {
      var q = (input.value || '').trim();
      if (q.length < 2) {
        hide();
        return;
      }

      var url = baseUrl + '/api/search?q=' + encodeURIComponent(q) + '&context=' + encodeURIComponent(context);
      fetch(url, { headers: { 'Accept': 'application/json' } })
        .then(function (res) { return res.json(); })
        .then(function (data) {
          show((data && data.results) ? data.results : []);
        })
        .catch(function () {
          hide();
        });
    }, 220);

    input.addEventListener('input', fetchSuggestions);

    input.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        hide();
        return;
      }

      if (suggestions.style.display !== 'block') return;

      if (e.key === 'ArrowDown') {
        e.preventDefault();
        setActive(activeIndex + 1);
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        setActive(activeIndex - 1);
      } else if (e.key === 'Enter') {
        e.preventDefault();
        var chosen = null;
        if (activeIndex >= 0 && items[activeIndex]) chosen = items[activeIndex];
        else if (items[0]) chosen = items[0];
        if (chosen && chosen.url) window.location.href = chosen.url;
      }
    });

    input.addEventListener('blur', function () {
      // small delay so click can register
      setTimeout(hide, 120);
    });

    wrapper.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') hide();
    });
  }

  function setupAllSearch() {
    // Backoffice search box already exists
    var backInput = document.querySelector('.search-box .search-input');
    var backWrap = document.querySelector('.search-box');
    if (backInput && backWrap) {
      setupSearch(backInput, backWrap, 'admin');
    }

    // Frontoffice: optional global search bar
    var frontInput = document.querySelector('#projetGlobalSearch');
    var frontWrap = document.querySelector('#projetGlobalSearchWrap');
    if (frontInput && frontWrap) {
      setupSearch(frontInput, frontWrap, 'front');
    }
  }

  // -------------------- Help widget (AI chat) --------------------
  function setupHelpWidget() {
    var mount = document.getElementById('ai-help-widget');
    if (!mount) return;

    var context = (mount.getAttribute('data-context') || 'front');

    var toggle = el('button', { class: 'ai-help-toggle', type: 'button' }, [
      el('span', { class: 'ai-help-toggle-text', text: 'AIDE' }),
    ]);

    var panel = el('div', { class: 'ai-help-panel', 'aria-hidden': 'true' });

    var header = el('div', { class: 'ai-help-header' }, [
      el('div', { class: 'ai-help-title', text: 'Aide' }),
      el('button', { class: 'ai-help-close', type: 'button', 'aria-label': 'Fermer' }, ['×']),
    ]);

    var body = el('div', { class: 'ai-help-body' });

    var form = el('form', { class: 'ai-help-form' });
    var input = el('input', {
      class: 'ai-help-input',
      type: 'text',
      placeholder: 'Pose ta question…',
      autocomplete: 'off',
    });
    var send = el('button', { class: 'ai-help-send', type: 'submit' }, ['Envoyer']);
    form.appendChild(input);
    form.appendChild(send);

    panel.appendChild(header);
    panel.appendChild(body);
    panel.appendChild(form);

    mount.appendChild(toggle);
    mount.appendChild(panel);

    var open = false;
    var history = [];

    function setOpen(v) {
      open = !!v;
      panel.setAttribute('aria-hidden', open ? 'false' : 'true');
      panel.style.display = open ? 'flex' : 'none';
      if (open) {
        input.focus();
      }
    }

    function pushMessage(role, text) {
      history.push({ role: role, content: text });

      var bubble = el('div', { class: 'ai-help-msg ' + (role === 'user' ? 'user' : 'assistant') });
      bubble.textContent = text;
      body.appendChild(bubble);
      body.scrollTop = body.scrollHeight;
    }

    function setBusy(busy) {
      send.disabled = busy;
      input.disabled = busy;
      if (busy) send.textContent = '…';
      else send.textContent = 'Envoyer';
    }

    function initialGreeting() {
      pushMessage('assistant', context === 'admin'
        ? "Je peux t'aider sur le dashboard admin (communautés, publications, navigation). Que veux-tu faire ?"
        : "Je peux t'aider à utiliser le site (communautés, publications, navigation). Que cherches-tu ?");
    }

    function callAI() {
      var endpoint = baseUrl + '/api/ai/chat';
      return fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ messages: history }),
      }).then(function (res) {
        return res.json().then(function (data) {
          if (!res.ok) {
            var msg = (data && data.error) ? data.error : 'Erreur IA';
            throw new Error(msg);
          }
          return data;
        });
      });
    }

    toggle.addEventListener('click', function () {
      setOpen(!open);
      if (open && history.length === 0) initialGreeting();
    });

    header.querySelector('.ai-help-close').addEventListener('click', function () {
      setOpen(false);
    });

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var text = (input.value || '').trim();
      if (!text) return;

      input.value = '';
      pushMessage('user', text);

      setBusy(true);
      callAI()
        .then(function (data) {
          pushMessage('assistant', (data && data.answer) ? data.answer : '');
        })
        .catch(function (err) {
          pushMessage('assistant', (err && err.message) ? err.message : 'Erreur IA');
        })
        .finally(function () {
          setBusy(false);
        });
    });

    // Start closed
    setOpen(false);
  }

  document.addEventListener('DOMContentLoaded', function () {
    setupAllSearch();
    setupHelpWidget();
  });
})();

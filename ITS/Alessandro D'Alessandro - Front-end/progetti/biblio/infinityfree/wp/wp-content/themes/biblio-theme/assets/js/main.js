/* Bibliò — main.js */
(function () {

  /* ── Nav: highlight current link ───────────────────────────── */
  document.addEventListener('DOMContentLoaded', function () {
    var path = window.location.pathname;
    document.querySelectorAll('.nav-link').forEach(function (a) {
      try {
        var u = new URL(a.href);
        if (u.pathname !== '/' && path.indexOf(u.pathname) === 0) a.classList.add('active');
      } catch (e) {}
    });
  });

  /* ── Chat: storage ──────────────────────────────────────────── */
  var STORAGE_KEY = 'biblio_chat_history';

  /*
   * Ogni entry: { role: 'user'|'assistant', display: html, raw: plain_text }
   * - display: testo safe per innerHTML (newline → <br>, utente escaped)
   * - raw:     testo pulito da mandare all'LLM (no HTML entities, no <br>)
   */
  function loadHistory() {
    try { return JSON.parse(sessionStorage.getItem(STORAGE_KEY)) || []; }
    catch (e) { return []; }
  }
  function saveHistory(h) {
    try { sessionStorage.setItem(STORAGE_KEY, JSON.stringify(h)); }
    catch (e) {}
  }

  /* ── DOM render ─────────────────────────────────────────────── */
  function renderUser(displayHtml, container) {
    var d = document.createElement('div');
    d.style.cssText = 'text-align:right;margin-bottom:10px;';
    d.innerHTML = '<span style="background:var(--fg);color:var(--bg);padding:8px 12px;border-radius:8px;display:inline-block;font-size:14px;">' + displayHtml + '</span>';
    container.appendChild(d);
  }

  function renderBot(displayHtml, container) {
    var d = document.createElement('div');
    d.style.cssText = 'margin-bottom:10px;color:var(--fg);font-size:14px;border-left:2px solid var(--fg);padding-left:10px;line-height:1.6;';
    d.innerHTML = displayHtml;
    container.appendChild(d);
  }

  /* Converte la risposta del bot in HTML safe:
     - newline → <br>
     - nessun altro HTML (il bot non deve iniettare markup) */
  function botTextToHtml(text) {
    return escapeHtml(text).replace(/\n/g, '<br>');
  }

  /* ── Helpers ────────────────────────────────────────────────── */
  function escapeHtml(s) {
    return String(s)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function getCurrentCategorySlug() {
    var m = window.location.pathname.match(/\/(?:product-category|wp-categoria-prodotto)\/([^\/]+)/);
    if (m) return m[1];
    var active = document.querySelector('.filter-item.active[data-slug]');
    if (active) return active.dataset.slug;
    return '';
  }

  /* Ultimi N turni in formato API (raw text, no HTML) */
  function buildApiHistory(history, n) {
    return history.slice(-(n || 6)).map(function (m) {
      return { role: m.role, content: m.raw };
    });
  }

  /* ── REST call ──────────────────────────────────────────────── */
  async function inviaMessaggio(rawText) {
    var history = loadHistory();
    try {
      var res = await fetch(biblio_api.url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': biblio_api.nonce },
        body: JSON.stringify({
          message:       rawText,
          category_slug: getCurrentCategorySlug(),
          history:       buildApiHistory(history)
        })
      });
      var data = await res.json();
      return data.reply || 'Errore nella risposta.';
    } catch (e) {
      return 'MyBibliò è momentaneamente offline.';
    }
  }

  /* ── Chat UI ────────────────────────────────────────────────── */
  document.addEventListener('DOMContentLoaded', function () {
    var inputField  = document.getElementById('chat-input');
    var submitBtn   = document.getElementById('chat-submit');
    var chatContent = document.getElementById('chat-content');
    var minimizeBtn = document.getElementById('chat-minimize');

    if (!submitBtn || !inputField || !chatContent) return;

    /* Ripristina conversazione da sessionStorage */
    loadHistory().forEach(function (m) {
      if (m.role === 'user') renderUser(m.display, chatContent);
      else renderBot(m.display, chatContent);
    });
    if (loadHistory().length) chatContent.scrollTop = chatContent.scrollHeight;

    /* Quick pick buttons */
    document.querySelectorAll('[data-chat-pick]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        inputField.value = btn.dataset.chatPick;
        submitBtn.click();
      });
    });

    async function handleChat() {
      var rawText = inputField.value.trim();
      if (!rawText) return;

      var userDisplay = escapeHtml(rawText);
      renderUser(userDisplay, chatContent);
      inputField.value = '';
      chatContent.scrollTop = chatContent.scrollHeight;

      /* Salva turno utente */
      var history = loadHistory();
      history.push({ role: 'user', display: userDisplay, raw: rawText });
      saveHistory(history);

      /* Loader */
      var loader = document.createElement('div');
      loader.style.cssText = 'color:var(--fg-muted);font-size:13px;padding-left:12px;margin-bottom:8px;';
      loader.textContent = '…';
      chatContent.appendChild(loader);
      chatContent.scrollTop = chatContent.scrollHeight;

      var replyRaw = await inviaMessaggio(rawText);

      chatContent.removeChild(loader);
      var replyDisplay = botTextToHtml(replyRaw);
      renderBot(replyDisplay, chatContent);
      chatContent.scrollTop = chatContent.scrollHeight;

      /* Salva turno bot */
      history = loadHistory();
      history.push({ role: 'assistant', display: replyDisplay, raw: replyRaw });
      saveHistory(history);
    }

    submitBtn.addEventListener('click', function (e) { e.preventDefault(); handleChat(); });
    inputField.addEventListener('keypress', function (e) {
      if (e.key === 'Enter') { e.preventDefault(); handleChat(); }
    });

    /* Minimizza */
    if (minimizeBtn) {
      var inputRow = inputField.parentElement;
      minimizeBtn.addEventListener('click', function () {
        var hidden = chatContent.style.display === 'none';
        chatContent.style.display = hidden ? 'flex' : 'none';
        inputRow.style.display    = hidden ? 'flex' : 'none';
        minimizeBtn.textContent   = hidden ? '−' : '+';
      });
    }
  });

})();

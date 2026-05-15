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

  /* ── Chat: storage key ──────────────────────────────────────── */
  var STORAGE_KEY = 'biblio_chat_history';

  /* Legge la history da sessionStorage (array di {role, html}) */
  function loadHistory() {
    try { return JSON.parse(sessionStorage.getItem(STORAGE_KEY)) || []; }
    catch (e) { return []; }
  }

  /* Salva la history */
  function saveHistory(history) {
    try { sessionStorage.setItem(STORAGE_KEY, JSON.stringify(history)); }
    catch (e) {}
  }

  /* Rende un messaggio utente nel DOM */
  function renderUser(html, container) {
    var d = document.createElement('div');
    d.style.cssText = 'text-align:right;margin-bottom:10px;';
    d.innerHTML = '<span style="background:var(--fg);color:var(--bg);padding:8px 12px;border-radius:8px;display:inline-block;font-size:14px;">' + html + '</span>';
    container.appendChild(d);
  }

  /* Rende un messaggio bot nel DOM */
  function renderBot(html, container) {
    var d = document.createElement('div');
    d.style.cssText = 'margin-bottom:10px;color:var(--fg);font-size:14px;border-left:2px solid var(--fg);padding-left:10px;';
    d.innerHTML = html;
    container.appendChild(d);
  }

  /* Recupera la categoria attiva dalla pagina corrente */
  function getCurrentCategorySlug() {
    /* Prova dal path URL: /wp-categoria-prodotto/<slug>/ */
    var m = window.location.pathname.match(/\/(?:product-category|wp-categoria-prodotto)\/([^\/]+)/);
    if (m) return m[1];
    /* Prova dal filtro attivo nella sidebar */
    var active = document.querySelector('.filter-item.active');
    if (active && active.dataset.slug) return active.dataset.slug;
    return '';
  }

  /* Costruisce la lista messaggi da mandare al backend (last 6 turns) */
  function buildApiHistory(history) {
    var last = history.slice(-6);
    return last.map(function (m) {
      return { role: m.role, content: m.text };
    });
  }

  /* Chiamata REST al backend */
  async function inviaMessaggio(testo) {
    var history = loadHistory();
    var payload = {
      message: testo,
      category_slug: getCurrentCategorySlug(),
      history: buildApiHistory(history)
    };
    try {
      var res = await fetch(biblio_api.url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': biblio_api.nonce
        },
        body: JSON.stringify(payload)
      });
      var data = await res.json();
      return data.reply || 'Errore nella risposta.';
    } catch (e) {
      return 'MyBibliò è momentaneamente offline.';
    }
  }

  /* ── Chat UI ────────────────────────────────────────────────── */
  document.addEventListener('DOMContentLoaded', function () {
    var inputField   = document.getElementById('chat-input');
    var submitBtn    = document.getElementById('chat-submit');
    var chatContent  = document.getElementById('chat-content');
    var minimizeBtn  = document.getElementById('chat-minimize');
    var fullWrapper  = document.getElementById('biblio-chatbot-container');

    if (!submitBtn || !inputField || !chatContent) return;

    /* Ripristina la conversazione precedente dalla sessionStorage */
    var history = loadHistory();
    if (history.length > 0) {
      history.forEach(function (m) {
        if (m.role === 'user') renderUser(m.text, chatContent);
        else renderBot(m.text, chatContent);
      });
      chatContent.scrollTop = chatContent.scrollHeight;
    }

    /* Gestione quick pick (pulsanti suggerimento) */
    document.querySelectorAll('[data-chat-pick]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        inputField.value = btn.dataset.chatPick;
        submitBtn.click();
      });
    });

    async function handleChat() {
      var query = inputField.value.trim();
      if (!query) return;

      /* Mostra messaggio utente */
      renderUser(escapeHtml(query), chatContent);
      inputField.value = '';
      chatContent.scrollTop = chatContent.scrollHeight;

      /* Salva in history */
      var history = loadHistory();
      history.push({ role: 'user', text: escapeHtml(query) });
      saveHistory(history);

      /* Indicatore di caricamento */
      var loader = document.createElement('div');
      loader.style.cssText = 'margin-bottom:10px;color:var(--fg-muted);font-size:13px;padding-left:12px;';
      loader.textContent = '…';
      chatContent.appendChild(loader);
      chatContent.scrollTop = chatContent.scrollHeight;

      /* Chiama backend */
      var reply = await inviaMessaggio(query);

      /* Rimuovi loader e mostra risposta */
      chatContent.removeChild(loader);
      renderBot(reply, chatContent);
      chatContent.scrollTop = chatContent.scrollHeight;

      /* Aggiorna history con risposta bot */
      history = loadHistory();
      history.push({ role: 'assistant', text: reply });
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
        var isHidden = chatContent.style.display === 'none';
        chatContent.style.display    = isHidden ? 'flex'  : 'none';
        inputRow.style.display       = isHidden ? 'flex'  : 'none';
        minimizeBtn.textContent      = isHidden ? '−' : '+';
      });
    }
  });

  /* Escaping base per testo utente nel DOM */
  function escapeHtml(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

})();

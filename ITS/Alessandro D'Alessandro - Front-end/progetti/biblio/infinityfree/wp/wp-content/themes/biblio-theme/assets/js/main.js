/* Bibliò — minimal interactions */
(function(){
  // Highlight current nav link if WP didn't mark it
  document.addEventListener('DOMContentLoaded', function(){
    var path = window.location.pathname;
    document.querySelectorAll('.nav-link').forEach(function(a){
      try {
        var u = new URL(a.href);
        if (u.pathname !== '/' && path.indexOf(u.pathname) === 0) a.classList.add('active');
      } catch(e){}
    });
  });
    
})();
/* Funzione core per comunicare con il backend */
async function inviaMessaggioChat(messaggio) {
    try {
        const response = await fetch(biblio_api.url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': biblio_api.nonce
            },
            body: JSON.stringify({ message: messaggio })
        });
        const data = await response.json();
        return data.reply;
    } catch (e) {
        return "Ops! MyBibliò è momentaneamente offline.";
    }
}

/* Gestione dell'interfaccia Chat */
document.addEventListener('DOMContentLoaded', function() {
   
    const inputField = document.getElementById('chat-input');
    const submitBtn = document.getElementById('chat-submit');
    const chatContainer = document.getElementById('chat-content');
    const minimizeBtn = document.getElementById('chat-minimize');
    const fullWrapper = document.getElementById('biblio-chatbot-container');

    if (!submitBtn || !inputField) return;

    async function handleChat() {
        const query = inputField.value.trim();
        if (!query) return;

        // 1. Mostra il messaggio dell'utente nel box
        chatContainer.innerHTML += `
            <div style="text-align:right; margin-bottom:15px;">
                <span style="background:var(--fg); color:var(--bg); padding:8px 12px; border-radius:8px; display:inline-block; font-size:14px;">
                    ${query}
                </span>
            </div>`;
        
        inputField.value = '';
        chatContainer.scrollTop = chatContainer.scrollHeight;

        // 2. Chiamata al backend
        try {
            // Usiamo la funzione inviaMessaggioChat che hai già nel file
            const reply = await inviaMessaggioChat(query);
            
            // 3. Mostra la risposta di MyBibliò con lo stile delle tue card
            chatContainer.innerHTML += `
                <div style="margin-bottom:15px; color:var(--fg); font-size:14px; border-left:2px solid var(--fg); padding-left:10px;">
                    ${reply}
                </div>`;
            chatContainer.scrollTop = chatContainer.scrollHeight;
        } catch (error) {
            chatContainer.innerHTML += `<div style="color:red; font-size:12px;">Errore di invio.</div>`;
        }
    }

    // Listener per il click sul pulsante →
    submitBtn.addEventListener('click', function(e) {
        e.preventDefault();
        handleChat();
    });

    // Listener per il tasto Invio
    inputField.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            handleChat();
        }
    });

    // Gestione tasto minimizza (−)
    if (minimizeBtn) {
        minimizeBtn.addEventListener('click', function() {
            if (chatContainer.style.display === 'none') {
                chatContainer.style.display = 'block';
                this.innerText = '−';
                fullWrapper.style.height = 'auto';
            } else {
                chatContainer.style.display = 'none';
                this.innerText = '+';
                // Nasconde anche la riga di input quando minimizzato
                inputField.parentElement.style.display = 'none';
            }
        });
    }
});
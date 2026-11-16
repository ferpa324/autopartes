(() => {
    const root = document.getElementById('chatbot');
    if (!root) return;

    const endpoint = root.dataset.endpoint;
    const toggle = document.getElementById('chatbot-toggle');
    const panel = document.getElementById('chatbot-panel');
    const closeBtn = document.getElementById('chatbot-cerrar');
    const log = document.getElementById('chatlog');
    const input = document.getElementById('mensaje');
    const send = document.getElementById('enviar');
    const clear = document.getElementById('chatbot-limpiar');
    const shortcuts = document.querySelectorAll('[data-question]');

    const bienvenida = '¡Hola! 👋 Soy el asistente de AutoMarket. Preguntame por autos, repuestos, precios o cómo comprar.';
    let cargado = false;
    let ocupado = false;

    function scrollBottom() {
        log.scrollTop = log.scrollHeight;
    }

    function appendMessage(texto, clase, enlaces = []) {
        const wrap = document.createElement('div');
        wrap.className = `chat-message ${clase}`;

        const bubble = document.createElement('div');
        bubble.className = 'chat-bubble';
        bubble.textContent = texto;
        wrap.appendChild(bubble);

        if (Array.isArray(enlaces) && enlaces.length) {
            const links = document.createElement('div');
            links.className = 'chat-links';
            enlaces.forEach((item) => {
                if (!item || !item.url || !item.texto) return;
                const a = document.createElement('a');
                a.href = item.url;
                a.className = 'btn btn-sm btn-outline-primary';
                a.textContent = item.texto;
                links.appendChild(a);
            });
            wrap.appendChild(links);
        }

        log.appendChild(wrap);
        scrollBottom();
        return wrap;
    }

    async function cargarHistorial() {
        if (cargado) return;
        cargado = true;
        try {
            const response = await fetch(`${endpoint}?accion=historial`, { credentials: 'same-origin' });
            const data = await response.json();
            const historial = Array.isArray(data.historial) ? data.historial : [];
            if (!historial.length) appendMessage(bienvenida, 'bot');
            historial.forEach((m) => appendMessage(m.mensaje, m.remitente === 'usuario' ? 'user' : 'bot', m.enlaces || []));
        } catch (error) {
            appendMessage(bienvenida, 'bot');
        }
    }

    function openChat() {
        panel.classList.remove('d-none');
        toggle.classList.add('d-none');
        toggle.setAttribute('aria-expanded', 'true');
        root.classList.add('is-open');
        cargarHistorial();
        window.setTimeout(() => input.focus(), 100);
    }

    function closeChat() {
        panel.classList.add('d-none');
        toggle.classList.remove('d-none');
        toggle.setAttribute('aria-expanded', 'false');
        root.classList.remove('is-open');
    }

    async function sendMessage(texto = input.value.trim()) {
        if (!texto || ocupado) return;
        ocupado = true;
        send.disabled = true;
        shortcuts.forEach((b) => b.disabled = true);

        appendMessage(texto, 'user');
        input.value = '';

        const typing = appendMessage('Escribiendo…', 'bot typing');

        try {
            const body = new URLSearchParams({ mensaje: texto });
            const response = await fetch(endpoint, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
                body
            });

            const data = await response.json();
            typing.remove();
            appendMessage(data.texto || data.error || 'No pude responder.', 'bot', data.enlaces || []);
        } catch (error) {
            typing.remove();
            appendMessage('No pude conectarme al asistente. Revisá que Apache, PHP y la base estén funcionando.', 'bot');
        } finally {
            ocupado = false;
            send.disabled = false;
            shortcuts.forEach((b) => b.disabled = false);
            input.focus();
        }
    }

    async function clearChat() {
        try {
            await fetch(`${endpoint}?accion=limpiar`, {
                method: 'POST',
                credentials: 'same-origin'
            });
        } catch (error) {
            // Aunque falle la petición, limpiamos la vista.
        }
        log.innerHTML = '';
        appendMessage(bienvenida, 'bot');
    }

    toggle.addEventListener('click', openChat);
    closeBtn.addEventListener('click', closeChat);
    send.addEventListener('click', () => sendMessage());
    clear.addEventListener('click', clearChat);

    input.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            sendMessage();
        }
    });

    shortcuts.forEach((button) => {
        button.addEventListener('click', () => sendMessage(button.dataset.question));
    });
})();

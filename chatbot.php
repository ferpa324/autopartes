<div id="chatbot" data-endpoint="<?= e(url('backend/chatbot_backend.php')) ?>">
    <button id="chatbot-toggle" type="button" class="chatbot-toggle shadow" aria-label="Abrir asistente" aria-expanded="false">
        <span>💬</span>
        <span class="chatbot-notify">1</span>
    </button>

    <section id="chatbot-panel" class="chatbot-panel d-none" role="dialog" aria-label="Asistente de AutoMarket" aria-modal="false">
        <header class="chatbot-header">
            <div class="d-flex align-items-center gap-2">
                <div class="chatbot-avatar">🚗</div>
                <div>
                    <div class="fw-bold">Asistente AutoMarket</div>
                    <small>Preguntame por autos o repuestos</small>
                </div>
            </div>
            <button id="chatbot-cerrar" type="button" class="btn btn-sm btn-light" aria-label="Cerrar">✕</button>
        </header>

        <div class="chatbot-shortcuts" id="chatbot-shortcuts">
            <button type="button" data-question="¿Qué autos tienen disponibles?">Autos</button>
            <button type="button" data-question="¿Qué repuestos tienen?">Repuestos</button>
            <button type="button" data-question="¿Cómo compro?">¿Cómo compro?</button>
        </div>

        <div id="chatlog" class="chatlog" aria-live="polite"></div>

        <div class="chatbot-input">
            <input type="text" id="mensaje" maxlength="300" placeholder="Escribí tu pregunta..." autocomplete="off">
            <button id="enviar" type="button" aria-label="Enviar mensaje">➤</button>
        </div>
        <div class="chatbot-footer">
            <button type="button" id="chatbot-limpiar">Limpiar conversación</button>
        </div>
    </section>
</div>
<script src="<?= url('js/chatbot.js') ?>"></script>

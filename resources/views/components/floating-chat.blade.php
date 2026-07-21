<div class="floating-chat" id="floating-chat">
    <div class="floating-chat-panel" id="floating-chat-panel" hidden>
        <div class="floating-chat-header">
            <div>
                <strong>Customer Service</strong>
                <span class="text-muted" style="display:block; font-size:0.85rem;">Live • n8n-ready</span>
            </div>
            <button type="button" class="floating-chat-close" id="floating-chat-close" aria-label="Tutup chat">✕</button>
        </div>
        <div class="floating-chat-thread" id="floating-chat-thread">
            <div class="bubble assistant">Halo! Saya asisten CS. Silakan tanyakan kebutuhan Anda tentang data RUP.</div>
        </div>
        <div class="floating-chat-input">
            <input id="floating-chat-input" type="text" placeholder="Ketik pesan..." autocomplete="off" />
            <button type="button" id="floating-chat-send" class="btn-primary">Kirim</button>
        </div>
    </div>
    <button type="button" class="floating-chat-toggle" id="floating-chat-toggle" aria-label="Buka chat">
        💬
    </button>
</div>

@once
@push('scripts')
<script>
    (function () {
        const panel = document.getElementById('floating-chat-panel');
        const toggle = document.getElementById('floating-chat-toggle');
        const closeBtn = document.getElementById('floating-chat-close');
        const thread = document.getElementById('floating-chat-thread');
        const input = document.getElementById('floating-chat-input');
        const send = document.getElementById('floating-chat-send');

        if (!panel || !toggle) return;

        function openChat() {
            panel.hidden = false;
            toggle.classList.add('is-open');
            input.focus();
        }

        function closeChat() {
            panel.hidden = true;
            toggle.classList.remove('is-open');
        }

        toggle.addEventListener('click', () => {
            panel.hidden ? openChat() : closeChat();
        });

        closeBtn?.addEventListener('click', closeChat);

        function addBubble(role, text) {
            const bubble = document.createElement('div');
            bubble.className = 'bubble ' + role;
            bubble.textContent = text;
            thread.appendChild(bubble);
            thread.scrollTop = thread.scrollHeight;
        }

        function sendMessage() {
            const text = input.value.trim();
            if (!text) return;
            addBubble('user', text);
            input.value = '';

            fetch('/api/chat', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ message: text, source: 'dashboard', channel: 'web' })
            })
            .then((response) => response.json())
            .then((payload) => {
                addBubble('assistant', payload?.data?.message || 'Terima kasih, pesan Anda sudah diterima.');
            });
        }

        send?.addEventListener('click', sendMessage);
        input?.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') sendMessage();
        });
    })();
</script>
@endpush
@endonce

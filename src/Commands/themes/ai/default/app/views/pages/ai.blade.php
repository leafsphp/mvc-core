<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400..800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #fafaf9; --fg: #0c0a09; --muted: #57534e; --line: rgba(12,10,9,0.1); --bubble: #ffffff; --mine: #1c1917; --mine-fg: #fafaf9; }
        @media (prefers-color-scheme: dark) {
            :root { --bg: #0c0a09; --fg: #fafaf9; --muted: #a8a29e; --line: rgba(250,250,249,0.1); --bubble: #171412; --mine: #fafaf9; --mine-fg: #1c1917; }
        }
        * { box-sizing: border-box; margin: 0; }
        body { background: var(--bg); color: var(--fg); font-family: Inter, ui-sans-serif, system-ui, sans-serif; font-size: 15px; }
        .shell { max-width: 720px; margin: 0 auto; min-height: 100vh; display: flex; flex-direction: column; border-left: 1px solid var(--line); border-right: 1px solid var(--line); }
        header { padding: 18px 24px; border-bottom: 1px solid var(--line); font-family: "Bricolage Grotesque", sans-serif; font-weight: 700; }
        header span { color: var(--muted); font-weight: 400; font-family: "JetBrains Mono", monospace; font-size: 11px; text-transform: uppercase; letter-spacing: 0.16em; margin-left: 10px; }
        #log { flex: 1; padding: 24px; display: flex; flex-direction: column; gap: 14px; overflow-y: auto; }
        .msg { max-width: 85%; padding: 12px 15px; line-height: 1.6; white-space: pre-wrap; border: 1px solid var(--line); background: var(--bubble); }
        .msg.mine { align-self: flex-end; background: var(--mine); color: var(--mine-fg); border: none; }
        form { display: flex; gap: 10px; padding: 18px 24px; border-top: 1px solid var(--line); }
        input { flex: 1; padding: 12px 14px; border: 1px solid var(--line); background: var(--bubble); color: var(--fg); font: inherit; outline: none; }
        button { padding: 12px 20px; border: none; background: #E8753A; color: #fff; font: inherit; font-weight: 600; cursor: pointer; }
        button:disabled { opacity: 0.5; }
    </style>
</head>
<body>
    <div class="shell">
        <header>Chat <span>powered by leaf + claude</span></header>
        <div id="log"></div>
        <form id="chat">
            <input id="input" placeholder="Ask anything..." autocomplete="off" autofocus>
            <button id="send">Send</button>
        </form>
    </div>

    <script>
        const log = document.getElementById('log');
        const input = document.getElementById('input');
        const send = document.getElementById('send');
        const history = [];

        function bubble(role, text) {
            const el = document.createElement('div');
            el.className = 'msg' + (role === 'user' ? ' mine' : '');
            el.textContent = text;
            log.appendChild(el);
            log.scrollTop = log.scrollHeight;
            return el;
        }

        document.getElementById('chat').addEventListener('submit', async (e) => {
            e.preventDefault();
            const text = input.value.trim();
            if (!text) return;

            input.value = '';
            send.disabled = true;
            history.push({ role: 'user', content: text });
            bubble('user', text);
            const reply = bubble('assistant', '');

            try {
                const res = await fetch('/ai/chat', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ messages: history }),
                });

                const reader = res.body.getReader();
                const decoder = new TextDecoder();
                let buffer = '';

                while (true) {
                    const { done, value } = await reader.read();
                    if (done) break;
                    buffer += decoder.decode(value, { stream: true });

                    const lines = buffer.split('\n\n');
                    buffer = lines.pop();

                    for (const line of lines) {
                        if (!line.startsWith('data: ')) continue;
                        const payload = line.slice(6);
                        if (payload === '[DONE]') continue;
                        const data = JSON.parse(payload);
                        if (data.error) reply.textContent = 'Error: ' + data.error;
                        if (data.text) reply.textContent += data.text;
                        log.scrollTop = log.scrollHeight;
                    }
                }

                history.push({ role: 'assistant', content: reply.textContent });
            } catch (err) {
                reply.textContent = 'Something went wrong: ' + err.message;
            }

            send.disabled = false;
            input.focus();
        });
    </script>
</body>
</html>

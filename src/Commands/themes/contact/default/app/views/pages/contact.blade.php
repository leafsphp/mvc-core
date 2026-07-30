<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400..800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #fafaf9; --fg: #0c0a09; --muted: #57534e; --line: rgba(12,10,9,0.1); --card: #ffffff; }
        @media (prefers-color-scheme: dark) { :root { --bg: #0c0a09; --fg: #fafaf9; --muted: #a8a29e; --line: rgba(250,250,249,0.1); --card: #171412; } }
        * { box-sizing: border-box; margin: 0; }
        body { background: var(--bg); color: var(--fg); font-family: Inter, ui-sans-serif, system-ui, sans-serif; font-size: 15px; line-height: 1.6; }
        .shell { max-width: 560px; margin: 0 auto; padding: 64px 24px; }
        h1 { font-family: "Bricolage Grotesque", sans-serif; font-size: 32px; margin: 10px 0 8px; }
        .eyebrow { font-family: "JetBrains Mono", monospace; font-size: 11px; text-transform: uppercase; letter-spacing: 0.16em; color: var(--muted); }
        .eyebrow::before { content: '// '; opacity: 0.5; }
        .lead { color: var(--muted); margin-bottom: 32px; }
        form { display: grid; gap: 16px; }
        label { font-size: 13px; font-weight: 600; }
        input, textarea { width: 100%; margin-top: 6px; padding: 12px 14px; border: 1px solid var(--line); background: var(--card); color: var(--fg); font: inherit; outline: none; }
        input:focus, textarea:focus { border-color: #E8753A; }
        textarea { min-height: 140px; resize: vertical; }
        button { justify-self: start; padding: 12px 24px; border: none; background: #E8753A; color: #fff; font: inherit; font-weight: 600; cursor: pointer; }
        .error { color: #C0392B; font-size: 13px; margin-top: 4px; }
        .success { border: 1px solid var(--line); background: var(--card); padding: 16px 18px; margin-bottom: 28px; }
    </style>
</head>
<body>
    <div class="shell">
        <p class="eyebrow">contact</p>
        <h1>Talk to us</h1>
        <p class="lead">Questions, feedback, ideas: drop a message and we will get back to you.</p>

        @if ($sent)
            <div class="success">Message sent! We will be in touch soon. 🧡</div>
        @endif

        <form method="POST" action="/contact">
            <div>
                <label for="name">Name</label>
                <input id="name" name="name" value="{{ $old['name'] ?? '' }}" required>
                @if (isset($errors['name'])) <p class="error">{{ is_array($errors['name']) ? $errors['name'][0] : $errors['name'] }}</p> @endif
            </div>
            <div>
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ $old['email'] ?? '' }}" required>
                @if (isset($errors['email'])) <p class="error">{{ is_array($errors['email']) ? $errors['email'][0] : $errors['email'] }}</p> @endif
            </div>
            <div>
                <label for="message">Message</label>
                <textarea id="message" name="message" required>{{ $old['message'] ?? '' }}</textarea>
                @if (isset($errors['message'])) <p class="error">{{ is_array($errors['message']) ? $errors['message'][0] : $errors['message'] }}</p> @endif
            </div>
            <button>Send message</button>
        </form>
    </div>
</body>
</html>

{{-- EDIT ME: this is a starting point, not legal advice. Review every section
     and adapt it to what your app actually collects and does. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400..800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #fafaf9; --fg: #0c0a09; --muted: #57534e; --line: rgba(12,10,9,0.1); }
        @media (prefers-color-scheme: dark) { :root { --bg: #0c0a09; --fg: #fafaf9; --muted: #a8a29e; --line: rgba(250,250,249,0.1); } }
        * { box-sizing: border-box; margin: 0; }
        body { background: var(--bg); color: var(--fg); font-family: Inter, ui-sans-serif, system-ui, sans-serif; font-size: 16px; line-height: 1.7; }
        .shell { max-width: 680px; margin: 0 auto; padding: 56px 24px; }
        h1 { font-family: "Bricolage Grotesque", sans-serif; font-size: 32px; margin: 10px 0 4px; }
        h2 { font-family: "Bricolage Grotesque", sans-serif; font-size: 20px; margin: 32px 0 10px; }
        p, li { color: var(--muted); margin: 12px 0; }
        .eyebrow { font-family: "JetBrains Mono", monospace; font-size: 11px; text-transform: uppercase; letter-spacing: 0.16em; color: var(--muted); }
        .eyebrow::before { content: '// '; opacity: 0.5; }
        .updated { font-family: "JetBrains Mono", monospace; font-size: 12px; color: var(--muted); margin-bottom: 24px; display: block; }
    </style>
</head>
<body>
    <div class="shell">
        <p class="eyebrow">legal</p>
        <h1>Privacy Policy</h1>
        <span class="updated">Last updated: {{ date('F j, Y') }}</span>

        <p>{{ _env('APP_NAME', 'We') }} ("we", "us") respects your privacy. This policy explains what we collect, why, and what we do with it.</p>

        <h2>What we collect</h2>
        <p>EDIT ME: list what you actually collect. Common examples: account details (name, email), usage data, payment information processed by your payment provider, and cookies needed for sessions.</p>

        <h2>How we use it</h2>
        <p>EDIT ME: typical uses are providing and improving the service, processing payments, sending transactional email, and responding to support requests. If you use data for marketing or analytics, say so here.</p>

        <h2>What we share</h2>
        <p>EDIT ME: name your processors (hosting, payments, email, analytics) and state that you never sell personal data, if that is true.</p>

        <h2>Data retention and deletion</h2>
        <p>EDIT ME: say how long you keep data and how users can request deletion, for example by emailing {{ _env('CONTACT_EMAIL', 'support@example.com') }}.</p>

        <h2>Contact</h2>
        <p>Questions about this policy? Reach us at {{ _env('CONTACT_EMAIL', 'support@example.com') }}.</p>
    </div>
</body>
</html>

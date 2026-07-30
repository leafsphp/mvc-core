{{-- EDIT ME: this is a starting point, not legal advice. Review every section
     and adapt it to your product before launch. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service</title>
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
        <h1>Terms of Service</h1>
        <span class="updated">Last updated: {{ date('F j, Y') }}</span>

        <p>These terms govern your use of {{ _env('APP_NAME', 'this service') }}. By using the service, you agree to them.</p>

        <h2>Your account</h2>
        <p>EDIT ME: account responsibilities, accurate information, keeping credentials safe, minimum age if applicable.</p>

        <h2>Acceptable use</h2>
        <p>EDIT ME: what users may not do (abuse, illegal content, attempts to break the service, reselling without permission).</p>

        <h2>Payments and refunds</h2>
        <p>EDIT ME: billing cycle, cancellation, and your refund policy. Delete this section if the service is free.</p>

        <h2>Termination</h2>
        <p>EDIT ME: when you may suspend or close accounts, and what happens to user data afterwards.</p>

        <h2>Disclaimers and liability</h2>
        <p>EDIT ME: the service is provided "as is"; limit liability to the extent your jurisdiction allows. Have a lawyer review this section in particular.</p>

        <h2>Contact</h2>
        <p>Questions about these terms? Reach us at {{ _env('CONTACT_EMAIL', 'support@example.com') }}.</p>
    </div>
</body>
</html>

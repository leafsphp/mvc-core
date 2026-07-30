<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post['title'] }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400..800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #fafaf9; --fg: #0c0a09; --muted: #57534e; --line: rgba(12,10,9,0.1); }
        @media (prefers-color-scheme: dark) { :root { --bg: #0c0a09; --fg: #fafaf9; --muted: #a8a29e; --line: rgba(250,250,249,0.1); } }
        * { box-sizing: border-box; margin: 0; }
        body { background: var(--bg); color: var(--fg); font-family: Inter, ui-sans-serif, system-ui, sans-serif; font-size: 16px; line-height: 1.7; }
        .shell { max-width: 680px; margin: 0 auto; padding: 56px 24px; }
        h1, h2, h3 { font-family: "Bricolage Grotesque", sans-serif; letter-spacing: -0.01em; }
        a { color: inherit; }
        .eyebrow { font-family: "JetBrains Mono", monospace; font-size: 11px; text-transform: uppercase; letter-spacing: 0.16em; color: var(--muted); }
        .eyebrow::before { content: '// '; opacity: 0.5; }
        h1 { font-size: 32px; margin: 10px 0 6px; }
        .content { margin-top: 36px; }
        .content h2 { font-size: 22px; margin: 32px 0 12px; }
        .content p { margin: 16px 0; }
        .content code { font-family: "JetBrains Mono", monospace; font-size: 0.85em; background: rgba(232, 117, 58, 0.09); padding: 2px 5px; }
        .content pre { background: #171412; color: #f5f5f4; padding: 18px; overflow-x: auto; margin: 20px 0; }
        .content pre code { background: none; padding: 0; }
        .back { display: inline-block; margin-bottom: 28px; font-size: 14px; color: var(--muted); text-decoration: none; }
        .back:hover { color: #E8753A; }
        .date { font-family: "JetBrains Mono", monospace; font-size: 12px; color: var(--muted); }
    </style>
</head>
<body>
    <div class="shell">
        <a class="back" href="/blog">&larr; All posts</a>
        <p class="eyebrow">blog</p>
        <h1>{{ $post['title'] }}</h1>
        <span class="date">{{ $post['date'] }}</span>
        <div class="content">{!! $post['html'] !!}</div>
    </div>
</body>
</html>

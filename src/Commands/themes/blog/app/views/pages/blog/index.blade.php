<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
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
        h1 { font-size: 34px; margin: 10px 0 40px; }
        .post { padding: 22px 0; border-top: 1px solid var(--line); }
        .post a { text-decoration: none; }
        .post h2 { font-size: 20px; }
        .post h2:hover { color: #E8753A; }
        .post p { color: var(--muted); margin-top: 6px; }
        .date { font-family: "JetBrains Mono", monospace; font-size: 12px; color: var(--muted); }
    </style>
</head>
<body>
    <div class="shell">
        <p class="eyebrow">blog</p>
        <h1>Latest posts</h1>
        @foreach ($posts as $post)
            <article class="post">
                <a href="/blog/{{ $post['slug'] }}">
                    <h2>{{ $post['title'] }}</h2>
                </a>
                <p>{{ $post['description'] }}</p>
                <span class="date">{{ $post['date'] }}</span>
            </article>
        @endforeach
    </div>
</body>
</html>

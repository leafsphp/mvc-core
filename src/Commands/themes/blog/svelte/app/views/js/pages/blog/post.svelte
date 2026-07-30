<script>
    const { post } = $props();
</script>

<svelte:head>
    <title>{post.title}</title>
</svelte:head>

<div class="min-h-screen bg-stone-50 text-stone-950 dark:bg-stone-950 dark:text-stone-50">
    <div class="max-w-2xl mx-auto px-6 py-14 leading-relaxed">
        <a
            href="/blog"
            class="inline-block mb-7 text-sm text-stone-600 dark:text-stone-400 no-underline hover:text-[#E8753A] dark:hover:text-[#E8753A] transition-colors"
        >
            &larr; All posts
        </a>
        <p class="font-mono text-[11px] uppercase tracking-[0.16em] text-stone-600 dark:text-stone-400">
            <span class="opacity-50">//</span> blog
        </p>
        <h1 class="text-3xl font-bold mt-2 mb-1.5 tracking-tight">{post.title}</h1>
        <span class="font-mono text-xs text-stone-600 dark:text-stone-400">
            {post.date}
        </span>

        <div
            class="post-content mt-9 [&_h2]:text-[22px] [&_h2]:font-bold [&_h2]:mt-8 [&_h2]:mb-3 [&_p]:my-4 [&_code]:font-mono [&_code]:text-[0.85em] [&_code]:bg-[#E8753A]/10 [&_code]:px-1.5 [&_code]:py-0.5 [&_pre]:bg-stone-900 [&_pre]:text-stone-100 [&_pre]:p-4 [&_pre]:overflow-x-auto [&_pre]:my-5 [&_pre_code]:bg-transparent [&_pre_code]:p-0"
        >
            {@html post.html}
        </div>
    </div>
</div>

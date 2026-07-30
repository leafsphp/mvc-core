<script>
    const { posts = [] } = $props();
</script>

<svelte:head>
    <title>Blog</title>
</svelte:head>

<div class="min-h-screen bg-stone-50 text-stone-950 dark:bg-stone-950 dark:text-stone-50">
    <div class="max-w-2xl mx-auto px-6 py-14">
        <p class="font-mono text-[11px] uppercase tracking-[0.16em] text-stone-600 dark:text-stone-400">
            <span class="opacity-50">//</span> blog
        </p>
        <h1 class="text-3xl md:text-4xl font-bold mt-2 mb-10 tracking-tight">
            Latest posts
        </h1>

        {#each posts as post (post.slug)}
            <article class="py-5 border-t border-stone-950/10 dark:border-stone-50/10 group">
                <a href={`/blog/${post.slug}`} class="no-underline">
                    <h2 class="text-xl font-bold group-hover:text-[#E8753A] transition-colors">
                        {post.title}
                    </h2>
                </a>
                <p class="mt-1.5 text-stone-600 dark:text-stone-400 leading-relaxed">
                    {post.description}
                </p>
                <span class="font-mono text-xs text-stone-600 dark:text-stone-400">
                    {post.date}
                </span>
            </article>
        {/each}
    </div>
</div>

<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    post: { type: Object, required: true },
});
</script>

<template>
    <Head :title="post.title" />

    <main class="min-h-screen bg-stone-50 text-stone-950 dark:bg-stone-950 dark:text-stone-50">
        <div class="max-w-2xl mx-auto px-6 py-14 leading-relaxed">
            <Link
                href="/blog"
                class="inline-block mb-7 text-sm text-stone-600 dark:text-stone-400 hover:text-[#E8753A] dark:hover:text-[#E8753A] transition-colors"
            >
                &larr; All posts
            </Link>

            <p class="font-mono text-[11px] uppercase tracking-[0.16em] text-stone-600 dark:text-stone-400">
                <span class="opacity-50">//</span> blog
            </p>
            <h1 class="text-3xl font-bold tracking-tight mt-2 mb-1.5">{{ post.title }}</h1>
            <span class="font-mono text-xs text-stone-600 dark:text-stone-400">{{ post.date }}</span>

            <!-- post.html is pre-rendered on the server from markdown (Parsedown, safe mode) -->
            <div class="post-content mt-9" v-html="post.html"></div>
        </div>
    </main>
</template>

<style scoped>
.post-content :deep(h2) {
    font-size: 1.375rem;
    font-weight: 700;
    letter-spacing: -0.01em;
    margin: 32px 0 12px;
}

.post-content :deep(p) {
    margin: 16px 0;
}

.post-content :deep(code) {
    font-family: ui-monospace, monospace;
    font-size: 0.85em;
    background: rgba(232, 117, 58, 0.09);
    padding: 2px 5px;
}

.post-content :deep(pre) {
    background: #171412;
    color: #f5f5f4;
    padding: 18px;
    overflow-x: auto;
    margin: 20px 0;
}

.post-content :deep(pre code) {
    background: none;
    padding: 0;
}
</style>

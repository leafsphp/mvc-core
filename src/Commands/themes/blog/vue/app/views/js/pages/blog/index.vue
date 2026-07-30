<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    posts: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="Blog" />

    <main class="min-h-screen bg-stone-50 text-stone-950 dark:bg-stone-950 dark:text-stone-50">
        <div class="max-w-2xl mx-auto px-6 py-14 leading-relaxed">
            <p class="font-mono text-[11px] uppercase tracking-[0.16em] text-stone-600 dark:text-stone-400">
                <span class="opacity-50">//</span> blog
            </p>
            <h1 class="text-3xl font-bold tracking-tight mt-2 mb-10">Latest posts</h1>

            <article
                v-for="post in posts"
                :key="post.slug"
                class="py-5 border-t border-stone-950/10 dark:border-stone-50/10"
            >
                <Link :href="`/blog/${post.slug}`">
                    <h2 class="text-xl font-bold tracking-tight hover:text-[#E8753A] transition-colors">
                        {{ post.title }}
                    </h2>
                </Link>
                <p class="mt-1.5 text-stone-600 dark:text-stone-400">{{ post.description }}</p>
                <span class="font-mono text-xs text-stone-600 dark:text-stone-400">{{ post.date }}</span>
            </article>
        </div>
    </main>
</template>

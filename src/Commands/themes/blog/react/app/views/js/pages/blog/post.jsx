import { Head, Link } from "@inertiajs/react";

export default function BlogPost({ post }) {
    return (
        <>
            <Head title={post.title} />

            <main className="min-h-screen bg-stone-50 text-stone-950 dark:bg-stone-950 dark:text-stone-50">
                <div className="max-w-2xl mx-auto px-6 py-14 leading-relaxed">
                    <Link
                        href="/blog"
                        className="inline-block mb-7 text-sm text-stone-600 dark:text-stone-400 hover:text-[#E8753A] dark:hover:text-[#E8753A] transition-colors"
                    >
                        &larr; All posts
                    </Link>
                    <p className="font-mono text-[11px] uppercase tracking-[0.16em] text-stone-600 dark:text-stone-400">
                        <span className="opacity-50">{"// "}</span>blog
                    </p>
                    <h1 className="text-3xl font-bold tracking-tight mt-2 mb-1.5">
                        {post.title}
                    </h1>
                    <span className="font-mono text-xs text-stone-600 dark:text-stone-400">
                        {post.date}
                    </span>

                    {/* post.html is rendered server-side from markdown (Parsedown, safe mode) */}
                    <div
                        className="mt-9 [&_h2]:text-[22px] [&_h2]:font-semibold [&_h2]:mt-8 [&_h2]:mb-3 [&_p]:my-4 [&_code]:font-mono [&_code]:text-[0.85em] [&_code]:bg-[#E8753A]/10 [&_code]:px-1 [&_code]:py-0.5 [&_pre]:bg-stone-900 [&_pre]:text-stone-100 [&_pre]:p-4 [&_pre]:overflow-x-auto [&_pre]:my-5 [&_pre_code]:bg-transparent [&_pre_code]:p-0"
                        dangerouslySetInnerHTML={{ __html: post.html }}
                    />
                </div>
            </main>
        </>
    );
}

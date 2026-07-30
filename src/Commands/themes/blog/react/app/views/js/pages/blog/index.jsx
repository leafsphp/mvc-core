import { Head, Link } from "@inertiajs/react";

export default function BlogIndex({ posts = [] }) {
    return (
        <>
            <Head title="Blog" />

            <main className="min-h-screen bg-stone-50 text-stone-950 dark:bg-stone-950 dark:text-stone-50">
                <div className="max-w-2xl mx-auto px-6 py-14 leading-relaxed">
                    <p className="font-mono text-[11px] uppercase tracking-[0.16em] text-stone-600 dark:text-stone-400">
                        <span className="opacity-50">{"// "}</span>blog
                    </p>
                    <h1 className="text-3xl font-bold tracking-tight mt-2 mb-10">
                        Latest posts
                    </h1>

                    {posts.map((post) => (
                        <article
                            key={post.slug}
                            className="py-5 border-t border-stone-950/10 dark:border-stone-50/10"
                        >
                            <Link href={`/blog/${post.slug}`}>
                                <h2 className="text-xl font-semibold hover:text-[#E8753A] transition-colors">
                                    {post.title}
                                </h2>
                            </Link>
                            <p className="mt-1.5 text-stone-600 dark:text-stone-400">
                                {post.description}
                            </p>
                            <span className="font-mono text-xs text-stone-600 dark:text-stone-400">
                                {post.date}
                            </span>
                        </article>
                    ))}
                </div>
            </main>
        </>
    );
}

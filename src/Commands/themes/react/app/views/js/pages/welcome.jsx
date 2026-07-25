import { Head } from "@inertiajs/react";
import { useEffect, useState } from "react";

export default function Welcome({ auth, phpVersion }) {
    const lines = [
        'Your assistant sees the same project map you do.',
        'Less "where does this go in Laravel?" energy.',
        'Run leaf context anytime for a fresh export.',
    ];

    const [typed, setTyped] = useState('');

    useEffect(() => {
        let i = 0;
        let char = 0;
        let timeoutId;

        const tick = () => {
            const line = lines[i];
            if (char <= line.length) {
                setTyped(line.slice(0, char));
                char++;
                timeoutId = setTimeout(tick, char === line.length + 1 ? 2200 : 28);
            } else {
                char = 0;
                i = (i + 1) % lines.length;
                timeoutId = setTimeout(tick, 400);
            }
        };

        tick();

        return () => clearTimeout(timeoutId);
    }, []);

    return (
        <>
            <Head title="Welcome" />

            <style>{`
                @keyframes blink {
                    50% { opacity: 0; }
                }
                .cursor-blink {
                    animation: blink 1s step-end infinite;
                }
                .text-gradient {
                    background: linear-gradient(135deg, #F5B731 0%, #E8753A 35%, #D4542B 65%, #C0392B 100%);
                    background-clip: text;
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                }
            `}</style>

            <div className="min-h-screen bg-neutral-50 font-sans text-sm text-neutral-950 antialiased dark:bg-neutral-950 dark:text-neutral-50">
                <div className="mx-auto flex min-h-screen w-full max-w-[1120px] flex-col px-6">
                    <header className="flex flex-col items-start justify-between gap-4 py-5 sm:flex-row sm:items-center">
                        <a
                            href="https://leafphp.dev"
                            className="flex items-center gap-2.5 text-[15px] font-semibold tracking-tight text-neutral-950 dark:text-neutral-50"
                            aria-label="Leaf PHP"
                        >
                            <img src="https://leafphp.dev/logo-circle.png" alt="" className="h-7 w-7" />
                            <span>Leaf MVC</span>
                            <span className="rounded-md border border-neutral-200 bg-white px-1.5 py-0.5 text-[11px] font-semibold text-neutral-500 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-400">
                                v5
                            </span>
                        </a>

                        <nav className="flex flex-wrap gap-1" aria-label="Useful Leaf links">
                            <a
                                href="https://github.com/leafsphp/leaf"
                                target="_blank"
                                rel="noreferrer"
                                className="flex items-center gap-2 rounded-lg px-3 py-2 text-[13px] font-medium text-neutral-600 transition hover:bg-black/5 hover:text-neutral-950 dark:text-neutral-400 dark:hover:bg-white/5 dark:hover:text-neutral-50"
                            >
                                <svg className="size-4" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z" />
                                </svg>
                                1.3K
                            </a>
                            <a
                                href="https://leafphp.dev/docs/"
                                target="_blank"
                                rel="noreferrer"
                                className="rounded-lg px-3 py-2 text-[13px] font-medium text-neutral-600 transition hover:bg-black/5 hover:text-neutral-950 dark:text-neutral-400 dark:hover:bg-white/5 dark:hover:text-neutral-50"
                            >
                                Docs
                            </a>
                            <a
                                href="https://leafphp.dev/support"
                                target="_blank"
                                rel="noreferrer"
                                className="rounded-lg px-3 py-2 text-[13px] font-medium text-neutral-600 transition hover:bg-black/5 hover:text-neutral-950 dark:text-neutral-400 dark:hover:bg-white/5 dark:hover:text-neutral-50"
                            >
                                🧡 SUPPORT LEAF
                            </a>
                        </nav>
                    </header>

                    <main className="flex flex-1 flex-col gap-12 pb-16 pt-1">
                        <section
                            className="grid items-center gap-8 border-b border-neutral-200 py-8 dark:border-neutral-800 lg:grid-cols-2 lg:gap-10 lg:py-24"
                            aria-labelledby="welcome-title"
                        >
                            <div>
                                <p className="mb-3 text-[13px] font-medium text-neutral-500 dark:text-neutral-400">
                                    Leaf MVC v5
                                </p>
                                <h1
                                    id="welcome-title"
                                    className="max-w-lg text-4xl font-semibold leading-[1.1] tracking-tight text-neutral-950 dark:text-neutral-50 sm:text-[2.75rem]"
                                >
                                    Elegant PHP for you<br />{" "}
                                    <span className="text-gradient"> and your AI agents ⚡️</span>
                                </h1>
                                <p className="mt-4 max-w-md text-base leading-relaxed text-neutral-600 dark:text-neutral-400">
                                    Leaf maintains a project map that your AI agents can use to understand your project and help you build it.
                                </p>

                                <div className="mt-6 flex flex-wrap gap-2">
                                    <a
                                        href="https://leafphp.dev/docs/intro/first-app"
                                        target="_blank"
                                        rel="noreferrer"
                                        className="inline-flex items-center gap-2 rounded-lg bg-neutral-900 px-4 py-2.5 text-sm font-medium text-neutral-50 transition hover:bg-neutral-800 dark:bg-neutral-50 dark:text-neutral-900 dark:hover:bg-neutral-200"
                                    >
                                        Quick Start
                                        <svg
                                            className="h-4 w-4 fill-none stroke-current stroke-2"
                                            viewBox="0 0 24 24"
                                            aria-hidden="true"
                                        >
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M5 12h14m0 0-6-6m6 6-6 6" />
                                        </svg>
                                    </a>
                                    <a
                                        href="https://leafphp.dev/docs/mvc/"
                                        target="_blank"
                                        rel="noreferrer"
                                        className="inline-flex items-center rounded-lg border border-neutral-300 bg-white px-4 py-2.5 text-sm font-medium text-neutral-950 transition hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-50 dark:hover:bg-neutral-900"
                                    >
                                        Documentation
                                    </a>
                                </div>
                            </div>

                            <div
                                className="flex flex-col overflow-hidden rounded-2xl border border-neutral-500/10 font-mono text-xs shadow-md dark:border-neutral-700/10 dark:shadow-black/40 p-5 bg-neutral-900 text-neutral-100"
                                aria-label="Context workflow preview"
                            >
                                <div className="grid gap-2.5 p-4 leading-relaxed">
                                    <div className="flex flex-wrap gap-2">
                                        <span className="shrink-0 font-medium text-neutral-400">$</span>
                                        <span className="dark:text-neutral-200">leaf serve</span>
                                    </div>
                                    <div className="pl-[18px] text-[#D4542B] dark:text-[#F5B731]">
                                        → Server running at http://127.0.0.1:5500
                                    </div>
                                    <div className="flex flex-wrap gap-2">
                                        <span className="shrink-0 font-medium text-neutral-400">$</span>
                                        <span className="dark:text-neutral-200">leaf context</span>
                                    </div>
                                    <div className="pl-[18px] text-[#D4542B] dark:text-[#F5B731]">
                                        → Project map ready (routes, structure, conventions)
                                    </div>
                                    <div className="ml-3 grid gap-2 rounded-lg border border-neutral-800 bg-neutral-950/80 p-3">
                                        <p className="text-[11px] text-neutral-500"># you ask (in Cursor, ChatGPT, etc.)</p>
                                        <code className="block break-words text-[11px] leading-snug text-neutral-300">
                                            "Add a Stripe webhook"
                                        </code>
                                        <p className="text-[11px] text-neutral-500"># Leaf's map already knows</p>
                                        <code className="block break-words text-[11px] leading-snug text-neutral-300">
                                            your routes, config, naming, folder layout
                                        </code>
                                        <p className="text-[11px] text-neutral-500"># after you ship changes</p>
                                        <code className="block break-words text-[11px] leading-snug text-neutral-300">
                                            → map refreshes so context doesn't drift
                                        </code>
                                    </div>
                                    <div className="flex min-h-[1.6em] flex-wrap items-center gap-2 text-neutral-400">
                                        <span className="shrink-0 font-medium text-neutral-300">→</span>
                                        <span className="text-neutral-400">{typed}</span>
                                        <span className="inline-block h-3.5 w-1.5 cursor-blink bg-neutral-500" aria-hidden="true" />
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section aria-labelledby="overview">
                            <h2 id="overview" className="sr-only">
                                What you get with Leaf MVC
                            </h2>
                            <p className="mb-5 max-w-xl text-[15px] leading-relaxed text-neutral-600 dark:text-neutral-400">
                                <span className="font-medium text-neutral-950 dark:text-neutral-50">You build it.</span>{" "}
                                Edit files in{" "}
                                <code className="font-mono text-[13px] text-neutral-950 dark:text-neutral-200">
                                    app/views/js/pages/welcome.jsx
                                </code>
                                , run{" "}
                                <code className="font-mono text-[13px] text-neutral-950 dark:text-neutral-200">
                                    php leaf serve
                                </code>
                                .{" "}
                                <span className="font-medium text-neutral-950 dark:text-neutral-50">AI stays aligned.</span>{" "}
                                When you use an assistant, Leaf keeps an accurate project map in sync.
                            </p>

                            <div className="grid grid-cols-1 gap-px overflow-hidden rounded-2xl border border-neutral-200 bg-neutral-200 dark:border-neutral-800 dark:bg-neutral-800 sm:grid-cols-2 lg:grid-cols-4">
                                <article className="bg-white p-6 transition hover:bg-neutral-50 dark:bg-neutral-950 dark:hover:bg-neutral-900">
                                    <span
                                        className="mb-4 grid h-9 w-9 place-items-center rounded-lg border border-neutral-200 bg-neutral-50 text-neutral-600 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-400"
                                        aria-hidden="true"
                                    >
                                        <svg className="h-[18px] w-[18px] fill-none stroke-current stroke-[1.75]" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M6 4h12v6H6zM6 14h12v6H6zM12 10v4" />
                                        </svg>
                                    </span>
                                    <h3 className="text-[15px] font-semibold tracking-tight text-neutral-950 dark:text-neutral-50">
                                        MVC, where you expect it
                                    </h3>
                                    <p className="mt-1.5 text-sm leading-snug tracking-tight text-neutral-600 dark:text-neutral-400">
                                        Routes, views, controllers, and models in{" "}
                                        <code className="font-mono text-[13px] font-medium text-neutral-950 dark:text-neutral-200">
                                            app/
                                        </code>
                                        —readable code you own end to end.
                                    </p>
                                </article>

                                <article className="bg-white p-6 transition hover:bg-neutral-50 dark:bg-neutral-950 dark:hover:bg-neutral-900">
                                    <span
                                        className="mb-4 grid h-9 w-9 place-items-center rounded-lg border border-neutral-200 bg-neutral-50 text-neutral-600 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-400"
                                        aria-hidden="true"
                                    >
                                        <svg className="h-[18px] w-[18px] fill-none stroke-current stroke-[1.75]" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M4 5h16v14H4zM8 9h8M8 13h5" />
                                        </svg>
                                    </span>
                                    <h3 className="text-[15px] font-semibold tracking-tight text-neutral-950 dark:text-neutral-50">
                                        Project map built in
                                    </h3>
                                    <p className="mt-1.5 text-sm leading-snug tracking-tight text-neutral-600 dark:text-neutral-400">
                                        Every install ships with structure and conventions your assistant can read—no re-explaining the codebase.
                                    </p>
                                </article>

                                <article className="bg-white p-6 transition hover:bg-neutral-50 dark:bg-neutral-950 dark:hover:bg-neutral-900">
                                    <span
                                        className="mb-4 grid h-9 w-9 place-items-center rounded-lg border border-neutral-200 bg-neutral-50 text-neutral-600 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-400"
                                        aria-hidden="true"
                                    >
                                        <svg className="h-[18px] w-[18px] fill-none stroke-current stroke-[1.75]" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M4 7h6v6H4zM14 7h6v6h-6zM4 17h6v4H4zM14 17h6v4h-6z" />
                                        </svg>
                                    </span>
                                    <h3 className="text-[15px] font-semibold tracking-tight text-neutral-950 dark:text-neutral-50">
                                        <code className="font-mono text-[13px] font-medium">leaf context</code> on demand
                                    </h3>
                                    <p className="mt-1.5 text-sm leading-snug tracking-tight text-neutral-600 dark:text-neutral-400">
                                        Export a minified map anytime—paste into ChatGPT, Cursor, or any tool.
                                    </p>
                                </article>

                                <article className="bg-white p-6 transition hover:bg-neutral-50 dark:bg-neutral-950 dark:hover:bg-neutral-900">
                                    <span
                                        className="mb-4 grid h-9 w-9 place-items-center rounded-lg border border-neutral-200 bg-neutral-50 text-neutral-600 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-400"
                                        aria-hidden="true"
                                    >
                                        <svg className="h-[18px] w-[18px] fill-none stroke-current stroke-[1.75]" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M7 7h10v10H7zM17 7l-4 4M7 17l4-4" />
                                        </svg>
                                    </span>
                                    <h3 className="text-[15px] font-semibold tracking-tight text-neutral-950 dark:text-neutral-50">
                                        Two-way sync
                                    </h3>
                                    <p className="mt-1.5 text-sm leading-snug tracking-tight text-neutral-600 dark:text-neutral-400">
                                        Context updates as your app changes, so output matches your patterns—not invented ones.
                                    </p>
                                </article>
                            </div>
                        </section>
                    </main>

                    <footer className="flex flex-col items-start justify-between gap-2 border-t border-neutral-200 py-4 text-[13px] text-neutral-500 dark:border-neutral-800 sm:flex-row sm:items-center">
                        <span>PHP v{phpVersion || "8.2"}</span>
                        <span>Simple · Elegant · Fast · Leaf 5</span>
                    </footer>
                </div>
            </div>
        </>
    );
}

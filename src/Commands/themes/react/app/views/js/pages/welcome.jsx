import { Head } from "@inertiajs/react";

export default function Welcome({ auth, phpVersion }) {
    return (
        <>
            <Head title="Welcome">
                <link rel="preconnect" href="https://fonts.googleapis.com" />
                <link rel="preconnect" href="https://fonts.gstatic.com" crossOrigin="anonymous" />
                <link
                    href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400..800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap"
                    rel="stylesheet"
                />
            </Head>

            <style>{`
                .font-display {
                    font-family: 'Bricolage Grotesque', 'Inter', sans-serif;
                }
                .font-mono-ds {
                    font-family: 'JetBrains Mono', ui-monospace, monospace;
                }
                .eyebrow {
                    font-family: 'JetBrains Mono', ui-monospace, monospace;
                    font-size: 11px;
                    font-weight: 500;
                    letter-spacing: 0.16em;
                    text-transform: uppercase;
                }
                .eyebrow::before {
                    content: '// ';
                    opacity: 0.5;
                    letter-spacing: 0;
                }
                .text-gradient {
                    background: linear-gradient(135deg, #F5B731 0%, #E8753A 35%, #D4542B 65%, #C0392B 100%);
                    background-clip: text;
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                }
                .corner-marks::before,
                .corner-marks::after {
                    content: '';
                    position: absolute;
                    width: 5px;
                    height: 5px;
                    background: #E8753A;
                }
                .corner-marks::before { top: -1px; left: -1px; }
                .corner-marks::after { bottom: -1px; right: -1px; }
            `}</style>

            <div className="min-h-screen bg-stone-50 font-sans text-sm text-stone-950 antialiased dark:bg-stone-950 dark:text-stone-50">
                <div className="mx-auto flex min-h-screen w-full max-w-[1120px] flex-col border-x border-stone-950/10 px-8 dark:border-stone-50/10">
                    <header className="flex flex-wrap items-center justify-between gap-4 border-b border-stone-950/10 py-5 dark:border-stone-50/10">
                        <a
                            href="https://leafphp.dev"
                            className="flex items-center gap-2.5 text-[15px] font-semibold tracking-tight"
                            aria-label="Leaf PHP"
                        >
                            <img src="/favicon.ico" alt="" className="h-7 w-7" />
                            <span>Leaf MVC</span>
                            <span className="font-mono-ds border border-stone-950/10 px-1.5 py-0.5 text-[11px] font-semibold text-stone-500 dark:border-stone-50/10 dark:text-stone-400">
                                v5
                            </span>
                        </a>

                        <nav className="flex flex-wrap gap-1" aria-label="Useful Leaf links">
                            <a
                                href="https://github.com/leafsphp/leaf"
                                target="_blank"
                                rel="noreferrer"
                                className="flex items-center gap-2 px-3 py-2 text-[13px] font-medium text-stone-600 transition hover:bg-black/5 hover:text-stone-950 dark:text-stone-400 dark:hover:bg-white/5 dark:hover:text-stone-50"
                            >
                                <svg className="size-4 shrink-0" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z" />
                                </svg>
                                GitHub
                            </a>
                            <a
                                href="https://leafphp.dev/docs/"
                                target="_blank"
                                rel="noreferrer"
                                className="px-3 py-2 text-[13px] font-medium text-stone-600 transition hover:bg-black/5 hover:text-stone-950 dark:text-stone-400 dark:hover:bg-white/5 dark:hover:text-stone-50"
                            >
                                Docs
                            </a>
                            <a
                                href="https://leafphp.dev/support"
                                target="_blank"
                                rel="noreferrer"
                                className="px-3 py-2 text-[13px] font-medium text-stone-600 transition hover:bg-black/5 hover:text-stone-950 dark:text-stone-400 dark:hover:bg-white/5 dark:hover:text-stone-50"
                            >
                                🧡 SUPPORT LEAF
                            </a>
                        </nav>
                    </header>

                    <main className="flex flex-1 flex-col">
                        <section
                            className="flex flex-col items-center pb-16 pt-[72px] text-center"
                            aria-labelledby="welcome-title"
                        >
                            <p className="eyebrow text-stone-500 dark:text-stone-400">leaf mvc v5</p>
                            <h1
                                id="welcome-title"
                                className="font-display mt-4 text-[42px] font-bold leading-[1.08] tracking-tight sm:text-[54px]"
                            >
                                Elegant PHP for you
                                <br />
                                <span className="text-gradient">and your AI agents ⚡️</span>
                            </h1>
                            <p className="mx-auto mt-[18px] max-w-[30rem] text-base leading-[1.65] text-stone-600 dark:text-stone-400">
                                Full MVC structure, a console that scaffolds it, and a project map your AI assistant reads so it builds in your patterns, not invented ones.
                            </p>

                            <div
                                className="corner-marks font-mono-ds relative mx-auto mt-10 w-full max-w-[620px] border border-white/[.08] bg-[#171412] px-7 py-[26px] text-left text-[12.5px] leading-[1.9] text-stone-100"
                                aria-label="Console preview"
                            >
                                <span className="font-medium text-[#E8753A]">$</span> leaf g:controller Posts -m
                                <br />
                                <span className="text-stone-400">→ app/controllers/PostsController.php</span>
                                <br />
                                <span className="text-stone-400">→ app/models/Post.php</span>
                                <span className="-mx-7 my-4 block h-px bg-white/[.08]" aria-hidden="true" />
                                <span className="font-medium text-[#E8753A]">$</span> leaf context
                                <br />
                                <span className="text-stone-400">
                                    → <span className="text-[#F5B731]">Project map ready</span> for your AI (routes, structure, conventions)
                                </span>
                            </div>

                            <div className="mt-9 flex flex-wrap justify-center gap-2.5">
                                <a
                                    href="https://leafphp.dev/learn/mvc"
                                    target="_blank"
                                    rel="noreferrer"
                                    className="inline-flex items-center gap-2 bg-stone-900 px-[18px] py-[11px] text-sm font-medium text-stone-50 transition hover:bg-stone-800 dark:bg-stone-50 dark:text-stone-900 dark:hover:bg-stone-200"
                                >
                                    Quick Start
                                    <svg
                                        className="h-4 w-4 shrink-0 fill-none stroke-current stroke-2"
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
                                    className="inline-flex items-center border border-stone-950/10 bg-white px-[18px] py-[11px] text-sm font-medium transition hover:bg-stone-50 dark:border-stone-50/10 dark:bg-stone-950 dark:hover:bg-stone-900"
                                >
                                    Documentation
                                </a>
                            </div>
                        </section>

                        <section className="border-t border-stone-950/10 py-14 dark:border-stone-50/10" aria-labelledby="essentials">
                            <h2 id="essentials" className="sr-only">
                                The essentials, one function away
                            </h2>
                            <p className="eyebrow text-stone-500 dark:text-stone-400">Ship features, not boilerplate</p>

                            <div className="mt-6 grid grid-cols-1 gap-px border border-stone-950/10 bg-stone-950/10 dark:border-stone-50/10 dark:bg-stone-50/10 md:grid-cols-3">
                                <article className="min-w-0 bg-white p-[26px] transition hover:bg-stone-50 dark:bg-stone-950 dark:hover:bg-stone-900">
                                    <p className="font-mono-ds mb-2.5 text-[11px] text-stone-500 dark:text-stone-400">app/routes/_auth.php</p>
                                    <h3 className="font-display text-base font-semibold tracking-tight">Auth in one line</h3>
                                    <p className="mt-1.5 text-[13px] leading-[1.55] text-stone-600 dark:text-stone-400">
                                        Login, signup, sessions and tokens, from one function.
                                    </p>
                                    <pre className="font-mono-ds mt-3.5 overflow-x-auto text-[12.5px] leading-[1.7]">
                                        $email = <span className="text-[#D4542B]">request</span>()-&gt;get(<span className="text-[#7c8a4d]">'email'</span>);{"\n"}
                                        $password = <span className="text-[#D4542B]">request</span>()-&gt;get(<span className="text-[#7c8a4d]">'password'</span>);{"\n"}
                                        {"\n"}
                                        <span className="text-[#D4542B]">auth</span>()-&gt;login([{"\n"}
                                        {"  "}<span className="text-[#7c8a4d]">'email'</span> =&gt; $email,{"\n"}
                                        {"  "}<span className="text-[#7c8a4d]">'password'</span> =&gt; $password,{"\n"}
                                        ]);
                                    </pre>
                                </article>
                                <article className="min-w-0 bg-white p-[26px] transition hover:bg-stone-50 dark:bg-stone-950 dark:hover:bg-stone-900">
                                    <p className="font-mono-ds mb-2.5 text-[11px] text-stone-500 dark:text-stone-400">app/database/users.yml</p>
                                    <h3 className="font-display text-base font-semibold tracking-tight">Your database is a YAML file</h3>
                                    <p className="mt-1.5 text-[13px] leading-[1.55] text-stone-600 dark:text-stone-400">
                                        Edit it, run <code className="font-mono-ds">leaf db:migrate</code>, and Leaf diffs the changes in. Seeds included.
                                    </p>
                                    <pre className="font-mono-ds mt-3.5 overflow-x-auto text-[12.5px] leading-[1.7]">
                                        <span className="text-[#D4542B]">columns</span>:{"\n"}
                                        {"  "}<span className="text-[#D4542B]">email</span>: {"{"} <span className="text-[#D4542B]">type</span>: <span className="text-[#7c8a4d]">string</span>, <span className="text-[#D4542B]">unique</span>: <span className="text-[#7c8a4d]">true</span> {"}"}{"\n"}
                                        {"  "}<span className="text-[#D4542B]">plan</span>: <span className="text-[#7c8a4d]">string</span>{"\n"}
                                        <span className="text-[#D4542B]">seeds</span>:{"\n"}
                                        {"  "}<span className="text-[#D4542B]">count</span>: <span className="text-[#7c8a4d]">10</span>{"\n"}
                                        {"  "}<span className="text-[#D4542B]">data</span>:{"\n"}
                                        {"    "}<span className="text-[#D4542B]">email</span>: <span className="text-[#7c8a4d]">'@faker.unique.safeEmail'</span>
                                    </pre>
                                </article>
                                <article className="min-w-0 bg-white p-[26px] transition hover:bg-stone-50 dark:bg-stone-950 dark:hover:bg-stone-900">
                                    <p className="font-mono-ds mb-2.5 text-[11px] text-stone-500 dark:text-stone-400">app/controllers/SignupController.php</p>
                                    <h3 className="font-display text-base font-semibold tracking-tight">Heavy work leaves the request</h3>
                                    <p className="mt-1.5 text-[13px] leading-[1.55] text-stone-600 dark:text-stone-400">
                                        Queue the slow part, respond instantly. A worker picks it up in the background.
                                    </p>
                                    <pre className="font-mono-ds mt-3.5 overflow-x-auto text-[12.5px] leading-[1.7]">
                                        <span className="text-[#D4542B]">dispatch</span>({"\n"}  SendWelcomeEmail::<span className="text-[#D4542B]">with</span>($user-&gt;id){"\n"});{"\n"}
                                        {"\n"}
                                        <span className="text-[#D4542B]">response</span>()-&gt;json([{"\n"}
                                        {"  "}<span className="text-[#7c8a4d]">'status'</span> =&gt; <span className="text-[#7c8a4d]">'shipped'</span>,{"\n"}
                                        ]);
                                    </pre>
                                </article>
                            </div>
                            <p className="mt-5 text-[13px] text-stone-500 dark:text-stone-400">
                                Need more?{" "}
                                <code className="font-mono-ds border border-stone-950/10 px-2 py-[3px] text-stone-950 dark:border-stone-50/10 dark:text-stone-200">
                                    leaf install auth db mail billing queue
                                </code>{" "}
                                and each module wires itself up.
                            </p>
                        </section>
                    </main>

                    <footer className="font-mono-ds flex flex-wrap items-center justify-between gap-2 border-t border-stone-950/10 py-[18px] text-[13px] text-stone-500 dark:border-stone-50/10">
                        <span>php v{phpVersion || "8.2"}</span>
                        <span>simple · elegant · fast · leaf 5</span>
                    </footer>
                </div>
            </div>
        </>
    );
}

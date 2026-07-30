import { Head, useForm } from "@inertiajs/react";

const firstError = (error) => (Array.isArray(error) ? error[0] : error);

export default function Contact({ sent = false, errors = {}, old = {} }) {
    const { data, setData, post, processing } = useForm({
        name: old.name || "",
        email: old.email || "",
        message: old.message || "",
    });

    const submit = (e) => {
        e.preventDefault();

        post("/contact");
    };

    const inputClasses =
        "w-full mt-1.5 px-3.5 py-3 border border-stone-950/10 dark:border-stone-50/10 bg-white dark:bg-stone-900 text-stone-950 dark:text-stone-50 outline-none focus:border-[#E8753A]";

    return (
        <>
            <Head title="Contact" />

            <main className="min-h-screen bg-stone-50 text-stone-950 dark:bg-stone-950 dark:text-stone-50">
                <div className="max-w-xl mx-auto px-6 py-16 leading-relaxed">
                    <p className="font-mono text-[11px] uppercase tracking-[0.16em] text-stone-600 dark:text-stone-400">
                        <span className="opacity-50">{"// "}</span>contact
                    </p>
                    <h1 className="text-3xl font-bold tracking-tight mt-2 mb-2">
                        Talk to us
                    </h1>
                    <p className="text-stone-600 dark:text-stone-400 mb-8">
                        Questions, feedback, ideas: drop a message and we will
                        get back to you.
                    </p>

                    {sent && (
                        <div className="border border-stone-950/10 dark:border-stone-50/10 bg-white dark:bg-stone-900 px-4 py-4 mb-7">
                            Message sent! We will be in touch soon. 🧡
                        </div>
                    )}

                    <form onSubmit={submit} className="grid gap-4">
                        <div>
                            <label
                                htmlFor="name"
                                className="text-[13px] font-semibold"
                            >
                                Name
                            </label>
                            <input
                                id="name"
                                name="name"
                                className={inputClasses}
                                value={data.name}
                                onChange={(e) =>
                                    setData("name", e.target.value)
                                }
                                required
                            />
                            {errors.name && (
                                <p className="text-red-600 text-[13px] mt-1">
                                    {firstError(errors.name)}
                                </p>
                            )}
                        </div>

                        <div>
                            <label
                                htmlFor="email"
                                className="text-[13px] font-semibold"
                            >
                                Email
                            </label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                className={inputClasses}
                                value={data.email}
                                onChange={(e) =>
                                    setData("email", e.target.value)
                                }
                                required
                            />
                            {errors.email && (
                                <p className="text-red-600 text-[13px] mt-1">
                                    {firstError(errors.email)}
                                </p>
                            )}
                        </div>

                        <div>
                            <label
                                htmlFor="message"
                                className="text-[13px] font-semibold"
                            >
                                Message
                            </label>
                            <textarea
                                id="message"
                                name="message"
                                className={`${inputClasses} min-h-[140px] resize-y`}
                                value={data.message}
                                onChange={(e) =>
                                    setData("message", e.target.value)
                                }
                                required
                            />
                            {errors.message && (
                                <p className="text-red-600 text-[13px] mt-1">
                                    {firstError(errors.message)}
                                </p>
                            )}
                        </div>

                        <button
                            className="justify-self-start px-6 py-3 bg-[#E8753A] text-white font-semibold cursor-pointer disabled:opacity-50"
                            disabled={processing}
                        >
                            Send message
                        </button>
                    </form>
                </div>
            </main>
        </>
    );
}

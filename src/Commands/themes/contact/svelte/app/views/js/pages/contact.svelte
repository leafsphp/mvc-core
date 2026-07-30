<script>
    import { useForm } from "@inertiajs/svelte";

    const { sent = false, errors = {}, old = {} } = $props();

    const form = useForm({
        name: old.name ?? "",
        email: old.email ?? "",
        message: old.message ?? "",
    });

    const error = (field) => {
        const message = errors?.[field];
        return Array.isArray(message) ? message[0] : message;
    };

    const submit = (e) => {
        e.preventDefault();

        $form.post("/contact");
    };
</script>

<svelte:head>
    <title>Contact</title>
</svelte:head>

<div class="min-h-screen bg-stone-50 text-stone-950 dark:bg-stone-950 dark:text-stone-50">
    <div class="max-w-xl mx-auto px-6 py-16">
        <p class="font-mono text-[11px] uppercase tracking-[0.16em] text-stone-600 dark:text-stone-400">
            <span class="opacity-50">//</span> contact
        </p>
        <h1 class="text-3xl font-bold mt-2 mb-2">Talk to us</h1>
        <p class="text-stone-600 dark:text-stone-400 mb-8">
            Questions, feedback, ideas: drop a message and we will get back to
            you.
        </p>

        {#if sent}
            <div class="border border-stone-950/10 dark:border-stone-50/10 bg-white dark:bg-stone-900 px-4 py-4 mb-7">
                Message sent! We will be in touch soon. 🧡
            </div>
        {/if}

        <form onsubmit={submit} class="grid gap-4">
            <div>
                <label for="name" class="text-[13px] font-semibold">Name</label>
                <input
                    id="name"
                    class="w-full mt-1.5 px-3.5 py-3 border border-stone-950/10 dark:border-stone-50/10 bg-white dark:bg-stone-900 text-stone-950 dark:text-stone-50 outline-none focus:border-[#E8753A]"
                    value={$form.name}
                    onchange={(e) => $form.name = e.target.value}
                    required
                />
                {#if error("name")}
                    <p class="text-sm text-red-600 dark:text-red-400 mt-1">
                        {error("name")}
                    </p>
                {/if}
            </div>
            <div>
                <label for="email" class="text-[13px] font-semibold">Email</label>
                <input
                    id="email"
                    type="email"
                    class="w-full mt-1.5 px-3.5 py-3 border border-stone-950/10 dark:border-stone-50/10 bg-white dark:bg-stone-900 text-stone-950 dark:text-stone-50 outline-none focus:border-[#E8753A]"
                    value={$form.email}
                    onchange={(e) => $form.email = e.target.value}
                    required
                />
                {#if error("email")}
                    <p class="text-sm text-red-600 dark:text-red-400 mt-1">
                        {error("email")}
                    </p>
                {/if}
            </div>
            <div>
                <label for="message" class="text-[13px] font-semibold">Message</label>
                <textarea
                    id="message"
                    class="w-full mt-1.5 px-3.5 py-3 min-h-[140px] resize-y border border-stone-950/10 dark:border-stone-50/10 bg-white dark:bg-stone-900 text-stone-950 dark:text-stone-50 outline-none focus:border-[#E8753A]"
                    value={$form.message}
                    onchange={(e) => $form.message = e.target.value}
                    required
                ></textarea>
                {#if error("message")}
                    <p class="text-sm text-red-600 dark:text-red-400 mt-1">
                        {error("message")}
                    </p>
                {/if}
            </div>
            <button
                class="justify-self-start px-6 py-3 bg-[#E8753A] text-white font-semibold cursor-pointer disabled:opacity-50"
                disabled={$form.processing}
            >
                Send message
            </button>
        </form>
    </div>
</div>

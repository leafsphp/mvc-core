<!-- EDIT ME: this is a starting point, not legal advice. Review every section
     and adapt it to your product before launch. -->
<script>
    const { appName, contactEmail, updated } = $props();
</script>

<svelte:head>
    <title>Terms of Service</title>
</svelte:head>

<div class="min-h-screen bg-stone-50 text-stone-950 dark:bg-stone-950 dark:text-stone-50">
    <div class="max-w-2xl mx-auto px-6 py-14 leading-relaxed">
        <p class="font-mono text-[11px] uppercase tracking-[0.16em] text-stone-600 dark:text-stone-400">
            <span class="opacity-50">//</span> legal
        </p>
        <h1 class="text-3xl font-bold mt-2 mb-1">Terms of Service</h1>
        <span class="block font-mono text-xs text-stone-600 dark:text-stone-400 mb-6">
            Last updated: {updated}
        </span>

        <p class="my-3 text-stone-600 dark:text-stone-400">
            These terms govern your use of {appName}. By using the service, you
            agree to them.
        </p>

        <h2 class="text-xl font-bold mt-8 mb-2">Your account</h2>
        <p class="my-3 text-stone-600 dark:text-stone-400">
            EDIT ME: account responsibilities, accurate information, keeping
            credentials safe, minimum age if applicable.
        </p>

        <h2 class="text-xl font-bold mt-8 mb-2">Acceptable use</h2>
        <p class="my-3 text-stone-600 dark:text-stone-400">
            EDIT ME: what users may not do (abuse, illegal content, attempts to
            break the service, reselling without permission).
        </p>

        <h2 class="text-xl font-bold mt-8 mb-2">Payments and refunds</h2>
        <p class="my-3 text-stone-600 dark:text-stone-400">
            EDIT ME: billing cycle, cancellation, and your refund policy. Delete
            this section if the service is free.
        </p>

        <h2 class="text-xl font-bold mt-8 mb-2">Termination</h2>
        <p class="my-3 text-stone-600 dark:text-stone-400">
            EDIT ME: when you may suspend or close accounts, and what happens to
            user data afterwards.
        </p>

        <h2 class="text-xl font-bold mt-8 mb-2">Disclaimers and liability</h2>
        <p class="my-3 text-stone-600 dark:text-stone-400">
            EDIT ME: the service is provided "as is"; limit liability to the
            extent your jurisdiction allows. Have a lawyer review this section
            in particular.
        </p>

        <h2 class="text-xl font-bold mt-8 mb-2">Contact</h2>
        <p class="my-3 text-stone-600 dark:text-stone-400">
            Questions about these terms? Reach us at {contactEmail}.
        </p>
    </div>
</div>

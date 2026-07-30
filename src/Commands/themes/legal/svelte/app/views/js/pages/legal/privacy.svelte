<!-- EDIT ME: this is a starting point, not legal advice. Review every section
     and adapt it to what your app actually collects and does. -->
<script>
    const { appName, contactEmail, updated } = $props();
</script>

<svelte:head>
    <title>Privacy Policy</title>
</svelte:head>

<div class="min-h-screen bg-stone-50 text-stone-950 dark:bg-stone-950 dark:text-stone-50">
    <div class="max-w-2xl mx-auto px-6 py-14 leading-relaxed">
        <p class="font-mono text-[11px] uppercase tracking-[0.16em] text-stone-600 dark:text-stone-400">
            <span class="opacity-50">//</span> legal
        </p>
        <h1 class="text-3xl font-bold mt-2 mb-1">Privacy Policy</h1>
        <span class="block font-mono text-xs text-stone-600 dark:text-stone-400 mb-6">
            Last updated: {updated}
        </span>

        <p class="my-3 text-stone-600 dark:text-stone-400">
            {appName} ("we", "us") respects your privacy. This policy explains
            what we collect, why, and what we do with it.
        </p>

        <h2 class="text-xl font-bold mt-8 mb-2">What we collect</h2>
        <p class="my-3 text-stone-600 dark:text-stone-400">
            EDIT ME: list what you actually collect. Common examples: account
            details (name, email), usage data, payment information processed by
            your payment provider, and cookies needed for sessions.
        </p>

        <h2 class="text-xl font-bold mt-8 mb-2">How we use it</h2>
        <p class="my-3 text-stone-600 dark:text-stone-400">
            EDIT ME: typical uses are providing and improving the service,
            processing payments, sending transactional email, and responding to
            support requests. If you use data for marketing or analytics, say so
            here.
        </p>

        <h2 class="text-xl font-bold mt-8 mb-2">What we share</h2>
        <p class="my-3 text-stone-600 dark:text-stone-400">
            EDIT ME: name your processors (hosting, payments, email, analytics)
            and state that you never sell personal data, if that is true.
        </p>

        <h2 class="text-xl font-bold mt-8 mb-2">Data retention and deletion</h2>
        <p class="my-3 text-stone-600 dark:text-stone-400">
            EDIT ME: say how long you keep data and how users can request
            deletion, for example by emailing {contactEmail}.
        </p>

        <h2 class="text-xl font-bold mt-8 mb-2">Contact</h2>
        <p class="my-3 text-stone-600 dark:text-stone-400">
            Questions about this policy? Reach us at {contactEmail}.
        </p>
    </div>
</div>

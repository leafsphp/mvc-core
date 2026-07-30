<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    sent: { type: Boolean, default: false },
    errors: { type: Object, default: () => ({}) },
    old: { type: Object, default: () => ({}) },
});

const form = useForm({
    name: props.old.name ?? '',
    email: props.old.email ?? '',
    message: props.old.message ?? '',
});

const fieldError = (field) => {
    const error = props.errors?.[field] ?? form.errors?.[field];
    return Array.isArray(error) ? error[0] : error;
};

const submit = () => {
    form.post('/contact');
};
</script>

<template>
    <Head title="Contact" />

    <main class="min-h-screen bg-stone-50 text-stone-950 dark:bg-stone-950 dark:text-stone-50">
        <div class="max-w-xl mx-auto px-6 py-16 leading-relaxed">
            <p class="font-mono text-[11px] uppercase tracking-[0.16em] text-stone-600 dark:text-stone-400">
                <span class="opacity-50">//</span> contact
            </p>
            <h1 class="text-3xl font-bold tracking-tight mt-2 mb-2">Talk to us</h1>
            <p class="text-stone-600 dark:text-stone-400 mb-8">
                Questions, feedback, ideas: drop a message and we will get back to you.
            </p>

            <div
                v-if="sent"
                class="border border-stone-950/10 dark:border-stone-50/10 bg-white dark:bg-[#171412] px-4.5 py-4 mb-7"
            >
                Message sent! We will be in touch soon. 🧡
            </div>

            <form @submit.prevent="submit" class="grid gap-4">
                <div>
                    <label for="name" class="text-[13px] font-semibold">Name</label>
                    <input
                        id="name"
                        v-model="form.name"
                        required
                        class="w-full mt-1.5 px-3.5 py-3 border border-stone-950/10 dark:border-stone-50/10 bg-white dark:bg-[#171412] text-inherit outline-none focus:border-[#E8753A]"
                    />
                    <p v-if="fieldError('name')" class="text-[#C0392B] text-[13px] mt-1">
                        {{ fieldError('name') }}
                    </p>
                </div>

                <div>
                    <label for="email" class="text-[13px] font-semibold">Email</label>
                    <input
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        class="w-full mt-1.5 px-3.5 py-3 border border-stone-950/10 dark:border-stone-50/10 bg-white dark:bg-[#171412] text-inherit outline-none focus:border-[#E8753A]"
                    />
                    <p v-if="fieldError('email')" class="text-[#C0392B] text-[13px] mt-1">
                        {{ fieldError('email') }}
                    </p>
                </div>

                <div>
                    <label for="message" class="text-[13px] font-semibold">Message</label>
                    <textarea
                        id="message"
                        v-model="form.message"
                        required
                        class="w-full mt-1.5 px-3.5 py-3 min-h-[140px] resize-y border border-stone-950/10 dark:border-stone-50/10 bg-white dark:bg-[#171412] text-inherit outline-none focus:border-[#E8753A]"
                    ></textarea>
                    <p v-if="fieldError('message')" class="text-[#C0392B] text-[13px] mt-1">
                        {{ fieldError('message') }}
                    </p>
                </div>

                <button
                    :disabled="form.processing"
                    class="justify-self-start px-6 py-3 bg-[#E8753A] text-white font-semibold cursor-pointer disabled:opacity-50"
                >
                    Send message
                </button>
            </form>
        </div>
    </main>
</template>

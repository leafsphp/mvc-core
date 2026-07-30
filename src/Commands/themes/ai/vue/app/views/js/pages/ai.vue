<script setup>
import { ref, nextTick } from 'vue';
import { Head } from '@inertiajs/vue3';

const messages = ref([]);
const input = ref('');
const sending = ref(false);
const log = ref(null);
const inputEl = ref(null);

const scrollToBottom = async () => {
    await nextTick();

    if (log.value) {
        log.value.scrollTop = log.value.scrollHeight;
    }
};

const submit = async () => {
    const text = input.value.trim();

    if (!text || sending.value) {
        return;
    }

    input.value = '';
    sending.value = true;

    messages.value.push({ role: 'user', content: text });

    const reply = { role: 'assistant', content: '' };
    messages.value.push(reply);
    scrollToBottom();

    try {
        const res = await fetch('/ai/chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                messages: messages.value.slice(0, -1),
            }),
        });

        const reader = res.body.getReader();
        const decoder = new TextDecoder();
        let buffer = '';

        while (true) {
            const { done, value } = await reader.read();
            if (done) break;
            buffer += decoder.decode(value, { stream: true });

            const lines = buffer.split('\n\n');
            buffer = lines.pop();

            for (const line of lines) {
                if (!line.startsWith('data: ')) continue;
                const payload = line.slice(6);
                if (payload === '[DONE]') continue;
                const data = JSON.parse(payload);
                if (data.error) reply.content = 'Error: ' + data.error;
                if (data.text) reply.content += data.text;
                scrollToBottom();
            }
        }
    } catch (err) {
        reply.content = 'Something went wrong: ' + err.message;
    }

    sending.value = false;
    await nextTick();
    inputEl.value?.focus();
};
</script>

<template>
    <Head title="Chat" />

    <main class="min-h-screen bg-stone-50 text-stone-950 dark:bg-stone-950 dark:text-stone-50 text-[15px]">
        <div
            class="max-w-3xl mx-auto min-h-screen h-screen flex flex-col border-x border-stone-950/10 dark:border-stone-50/10">
            <header class="px-6 py-4.5 border-b border-stone-950/10 dark:border-stone-50/10 font-bold">
                Chat
                <span
                    class="ml-2.5 font-mono font-normal text-[11px] uppercase tracking-[0.16em] text-stone-600 dark:text-stone-400">
                    powered by leaf + claude
                </span>
            </header>

            <div ref="log" class="flex-1 p-6 flex flex-col gap-3.5 overflow-y-auto">
                <div
                    v-for="(message, index) in messages"
                    :key="index"
                    class="max-w-[85%] px-4 py-3 leading-relaxed whitespace-pre-wrap"
                    :class="message.role === 'user'
                        ? 'self-end bg-stone-900 text-stone-50 dark:bg-stone-50 dark:text-stone-900'
                        : 'border border-stone-950/10 dark:border-stone-50/10 bg-white dark:bg-[#171412]'"
                >{{ message.content }}</div>
            </div>

            <form @submit.prevent="submit" class="flex gap-2.5 px-6 py-4.5 border-t border-stone-950/10 dark:border-stone-50/10">
                <input
                    ref="inputEl"
                    v-model="input"
                    placeholder="Ask anything..."
                    autocomplete="off"
                    autofocus
                    class="flex-1 px-3.5 py-3 border border-stone-950/10 dark:border-stone-50/10 bg-white dark:bg-[#171412] text-inherit outline-none focus:border-[#E8753A]"
                />
                <button
                    :disabled="sending"
                    class="px-5 py-3 bg-[#E8753A] text-white font-semibold cursor-pointer disabled:opacity-50"
                >
                    Send
                </button>
            </form>
        </div>
    </main>
</template>

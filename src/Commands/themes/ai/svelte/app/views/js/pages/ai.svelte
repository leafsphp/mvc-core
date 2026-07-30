<script>
    import { tick } from "svelte";

    let messages = $state([]);
    let input = $state("");
    let sending = $state(false);
    let log;

    const scrollToBottom = async () => {
        await tick();

        if (log) {
            log.scrollTop = log.scrollHeight;
        }
    };

    const submit = async (e) => {
        e.preventDefault();

        const text = input.trim();
        if (!text || sending) return;

        input = "";
        sending = true;
        messages.push({ role: "user", content: text });
        messages.push({ role: "assistant", content: "" });
        // grab the proxied version so edits are reactive
        const reply = messages[messages.length - 1];
        scrollToBottom();

        try {
            const res = await fetch("/ai/chat", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    messages: messages
                        .slice(0, -1)
                        .map(({ role, content }) => ({ role, content })),
                }),
            });

            const reader = res.body.getReader();
            const decoder = new TextDecoder();
            let buffer = "";

            while (true) {
                const { done, value } = await reader.read();
                if (done) break;
                buffer += decoder.decode(value, { stream: true });

                const lines = buffer.split("\n\n");
                buffer = lines.pop();

                for (const line of lines) {
                    if (!line.startsWith("data: ")) continue;
                    const payload = line.slice(6);
                    if (payload === "[DONE]") continue;
                    const data = JSON.parse(payload);
                    if (data.error) reply.content = "Error: " + data.error;
                    if (data.text) reply.content += data.text;
                    scrollToBottom();
                }
            }
        } catch (err) {
            reply.content = "Something went wrong: " + err.message;
        }

        sending = false;
    };
</script>

<svelte:head>
    <title>Chat</title>
</svelte:head>

<div class="min-h-screen bg-stone-50 text-stone-950 dark:bg-stone-950 dark:text-stone-50 text-[15px]">
    <div class="max-w-[720px] mx-auto min-h-screen flex flex-col border-x border-stone-950/10 dark:border-stone-50/10">
        <header class="px-6 py-[18px] border-b border-stone-950/10 dark:border-stone-50/10 font-bold">
            Chat
            <span class="ml-2.5 font-mono font-normal text-[11px] uppercase tracking-[0.16em] text-stone-600 dark:text-stone-400">
                powered by leaf + claude
            </span>
        </header>

        <div bind:this={log} class="flex-1 p-6 flex flex-col gap-3.5 overflow-y-auto">
            {#each messages as message}
                <div
                    class={message.role === "user"
                        ? "max-w-[85%] px-[15px] py-3 leading-relaxed whitespace-pre-wrap self-end bg-stone-900 text-stone-50 dark:bg-stone-50 dark:text-stone-900"
                        : "max-w-[85%] px-[15px] py-3 leading-relaxed whitespace-pre-wrap border border-stone-950/10 dark:border-stone-50/10 bg-white dark:bg-stone-900"}
                >{message.content}</div>
            {/each}
        </div>

        <form
            onsubmit={submit}
            class="flex gap-2.5 px-6 py-[18px] border-t border-stone-950/10 dark:border-stone-50/10"
        >
            <!-- svelte-ignore a11y_autofocus -->
            <input
                class="flex-1 px-3.5 py-3 border border-stone-950/10 dark:border-stone-50/10 bg-white dark:bg-stone-900 text-stone-950 dark:text-stone-50 outline-none focus:border-[#E8753A]"
                placeholder="Ask anything..."
                autocomplete="off"
                autofocus
                bind:value={input}
            />
            <button
                class="px-5 py-3 bg-[#E8753A] text-white font-semibold cursor-pointer disabled:opacity-50"
                disabled={sending}
            >
                Send
            </button>
        </form>
    </div>
</div>

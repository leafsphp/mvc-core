import { Head } from "@inertiajs/react";
import { useEffect, useRef, useState } from "react";

export default function Chat() {
    const [messages, setMessages] = useState([]);
    const [input, setInput] = useState("");
    const [streaming, setStreaming] = useState(false);
    const logRef = useRef(null);
    const inputRef = useRef(null);

    useEffect(() => {
        if (logRef.current) {
            logRef.current.scrollTop = logRef.current.scrollHeight;
        }
    }, [messages]);

    const submit = async (e) => {
        e.preventDefault();

        const text = input.trim();

        if (!text || streaming) return;

        const history = [...messages, { role: "user", content: text }];

        setInput("");
        setStreaming(true);
        setMessages([...history, { role: "assistant", content: "" }]);

        const setReply = (updater) => {
            setMessages((current) => {
                const next = [...current];
                const last = next[next.length - 1];

                next[next.length - 1] = {
                    ...last,
                    content: updater(last.content),
                };

                return next;
            });
        };

        try {
            const res = await fetch("/ai/chat", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ messages: history }),
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
                    if (data.error) setReply(() => "Error: " + data.error);
                    if (data.text) setReply((content) => content + data.text);
                }
            }
        } catch (err) {
            setReply(() => "Something went wrong: " + err.message);
        }

        setStreaming(false);
        inputRef.current?.focus();
    };

    return (
        <>
            <Head title="Chat" />

            <main className="min-h-screen bg-stone-50 text-stone-950 dark:bg-stone-950 dark:text-stone-50 text-[15px]">
                <div className="max-w-3xl mx-auto min-h-screen flex flex-col border-x border-stone-950/10 dark:border-stone-50/10">
                    <header className="px-6 py-4 border-b border-stone-950/10 dark:border-stone-50/10 font-bold">
                        Chat{" "}
                        <span className="ml-2.5 font-mono font-normal text-[11px] uppercase tracking-[0.16em] text-stone-600 dark:text-stone-400">
                            powered by leaf + claude
                        </span>
                    </header>

                    <div
                        ref={logRef}
                        className="flex-1 p-6 flex flex-col gap-3.5 overflow-y-auto"
                    >
                        {messages.map((message, index) => (
                            <div
                                key={index}
                                className={
                                    message.role === "user"
                                        ? "max-w-[85%] px-4 py-3 leading-relaxed whitespace-pre-wrap self-end bg-stone-900 text-stone-50 dark:bg-stone-50 dark:text-stone-900"
                                        : "max-w-[85%] px-4 py-3 leading-relaxed whitespace-pre-wrap border border-stone-950/10 dark:border-stone-50/10 bg-white dark:bg-stone-900"
                                }
                            >
                                {message.content}
                            </div>
                        ))}
                    </div>

                    <form
                        onSubmit={submit}
                        className="flex gap-2.5 px-6 py-4 border-t border-stone-950/10 dark:border-stone-50/10"
                    >
                        <input
                            ref={inputRef}
                            className="flex-1 px-3.5 py-3 border border-stone-950/10 dark:border-stone-50/10 bg-white dark:bg-stone-900 text-stone-950 dark:text-stone-50 outline-none focus:border-[#E8753A]"
                            placeholder="Ask anything..."
                            autoComplete="off"
                            autoFocus
                            value={input}
                            onChange={(e) => setInput(e.target.value)}
                        />
                        <button
                            className="px-5 py-3 bg-[#E8753A] text-white font-semibold cursor-pointer disabled:opacity-50"
                            disabled={streaming}
                        >
                            Send
                        </button>
                    </form>
                </div>
            </main>
        </>
    );
}

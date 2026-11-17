import { usePage } from '@inertiajs/react';
import { Bot, Loader2, MessageCircle, Send, ShieldCheck, User, X } from 'lucide-react';
import { useCallback, useEffect, useRef, useState } from 'react';

import { HttpError, jsonRequest } from '@/lib/http';
import { cn } from '@/lib/utils';
import chat from '@/routes/chat';

interface ChatMessage {
    role: 'user' | 'assistant' | 'admin' | string;
    content: string;
}

interface ChatState {
    escalated: boolean;
    status: string | null;
    messages: ChatMessage[];
    guest_limit_reached: boolean;
}

const QUICK_PROMPTS = [
    'What pastries do you recommend?',
    'Do you have gluten-free options?',
    'How do I order a custom cake?',
    'What are your delivery hours?',
];

/** Floating pastry assistant; hands the thread to staff after a few questions or on request. */
export default function AiChatWidget() {
    const user = usePage().props.auth.user;
    const [open, setOpen] = useState(false);
    const [state, setState] = useState<ChatState>({ escalated: false, status: null, messages: [], guest_limit_reached: false });
    const [text, setText] = useState('');
    const [busy, setBusy] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [askContact, setAskContact] = useState(false);
    const [contact, setContact] = useState({ name: '', email: '' });
    const scroller = useRef<HTMLDivElement>(null);

    const scrollDown = () => requestAnimationFrame(() => scroller.current?.scrollTo({ top: scroller.current.scrollHeight, behavior: 'smooth' }));

    const refresh = useCallback(async () => {
        try {
            setState(await jsonRequest<ChatState>(chat.show().url));
        } catch {
            /* offline — keep what we have */
        }
    }, []);

    // Poll for staff replies while the panel is open on an escalated thread.
    useEffect(() => {
        if (!open || !state.escalated) return;
        const id = window.setInterval(refresh, 5000);
        return () => window.clearInterval(id);
    }, [open, state.escalated, refresh]);

    const toggle = () => {
        if (!open) refresh().then(scrollDown);
        setOpen(!open);
    };

    const send = async (message: string) => {
        const value = message.trim();
        if (!value || busy) return;
        setBusy(true);
        setError(null);
        setText('');
        setState((s) => ({ ...s, messages: [...s.messages, { role: 'user', content: value }] }));
        scrollDown();
        try {
            setState(await jsonRequest<ChatState>(chat.send().url, { method: 'POST', body: { message: value } }));
        } catch (e) {
            setError(e instanceof HttpError ? e.message : 'Sorry, I am having trouble connecting right now.');
            if (e instanceof HttpError && e.status === 429) setAskContact(true);
        } finally {
            setBusy(false);
            scrollDown();
        }
    };

    const escalate = async () => {
        if (!user && !askContact) {
            setAskContact(true);
            return;
        }
        setBusy(true);
        setError(null);
        try {
            setState(await jsonRequest<ChatState>(chat.escalate().url, { method: 'POST', body: user ? {} : contact }));
            setAskContact(false);
        } catch (e) {
            setError(e instanceof HttpError ? e.message : 'Could not reach our team. Please try again.');
        } finally {
            setBusy(false);
            scrollDown();
        }
    };

    const locked = state.guest_limit_reached && !state.escalated;

    // Sits above any page bottom bar (the product page sets --bottom-bar-h).
    return (
        <div className="fixed right-3 bottom-[calc(0.75rem+var(--bottom-bar-h,0px))] z-50 flex max-w-[calc(100vw-24px)] flex-col items-end gap-3 transition-[bottom] duration-200 sm:right-6 sm:bottom-[calc(1.5rem+var(--bottom-bar-h,0px))]">
            {open && (
                <div
                    role="dialog"
                    aria-label="Chat with ABCDips"
                    className="flex w-[calc(100vw-24px)] max-w-sm animate-in flex-col overflow-hidden rounded-3xl border border-border bg-card shadow-xl zoom-in-95 fade-in slide-in-from-bottom-4 sm:w-96"
                    style={{ height: 'min(520px, calc(100vh - 90px))' }}
                >
                    <div className="flex shrink-0 items-center justify-between bg-sidebar px-4 py-3.5 shadow-md">
                        <div className="flex items-center gap-2.5">
                            <div className="flex size-8 items-center justify-center rounded-full bg-highlight/30 text-sidebar-foreground">
                                {state.escalated ? <User className="size-4" /> : <Bot className="size-4" />}
                            </div>
                            <div>
                                <p className="text-xs leading-none font-bold text-sidebar-foreground">
                                    {state.escalated ? 'ABCDips Support Team' : 'ABCDips Pastry Assistant'}
                                </p>
                                <p className="mt-0.5 text-[10px] leading-none font-medium text-sidebar-primary">
                                    {state.escalated ? 'Connected to our team' : 'Ask me anything about our pastries!'}
                                </p>
                            </div>
                        </div>
                        <button
                            type="button"
                            onClick={() => setOpen(false)}
                            aria-label="Close chat"
                            className="rounded-lg p-1.5 text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-foreground"
                        >
                            <X className="size-4" />
                        </button>
                    </div>

                    <div ref={scroller} className="min-h-0 flex-1 space-y-3 overflow-y-auto overscroll-contain bg-card px-4 py-3" aria-live="polite">
                        {state.messages.length === 0 && (
                            <Bubble role="assistant" content="Hi! Ask me about the menu, allergens, custom cakes or delivery." />
                        )}
                        {state.messages.map((message, i) => (
                            <Bubble key={i} {...message} />
                        ))}
                        {busy && (
                            <div className="flex items-center gap-2 pl-9">
                                <Loader2 className="size-4 animate-spin text-primary" />
                            </div>
                        )}
                        {error && <p className="text-center text-xs text-destructive">{error}</p>}

                        {state.messages.length === 0 && (
                            <div className="flex flex-wrap gap-1.5 pt-1">
                                {QUICK_PROMPTS.map((prompt) => (
                                    <button
                                        key={prompt}
                                        type="button"
                                        onClick={() => send(prompt)}
                                        className="rounded-full border border-border bg-card px-3 py-1 text-[11px] font-semibold text-primary hover:bg-muted"
                                    >
                                        {prompt}
                                    </button>
                                ))}
                            </div>
                        )}

                        {!state.escalated && (
                            <div className="space-y-2 pt-2">
                                {askContact && !user && (
                                    <div className="space-y-2 rounded-2xl border border-border bg-card p-3">
                                        <p className="text-xs font-semibold text-foreground">Leave your details and our team will reply here:</p>
                                        <input
                                            value={contact.name}
                                            onChange={(e) => setContact((c) => ({ ...c, name: e.target.value }))}
                                            placeholder="Your name"
                                            aria-label="Your name"
                                            className="w-full rounded-lg border border-border bg-transparent px-2.5 py-1.5 text-xs"
                                        />
                                        <input
                                            type="email"
                                            value={contact.email}
                                            onChange={(e) => setContact((c) => ({ ...c, email: e.target.value }))}
                                            placeholder="you@email.com"
                                            aria-label="Your email"
                                            className="w-full rounded-lg border border-border bg-transparent px-2.5 py-1.5 text-xs"
                                        />
                                    </div>
                                )}
                                <div className="flex justify-center">
                                    <button
                                        type="button"
                                        onClick={escalate}
                                        disabled={busy || (askContact && !user && !contact.email)}
                                        className="flex items-center gap-1.5 rounded-full border border-border bg-background px-3.5 py-1.5 text-xs font-bold text-primary shadow-sm transition-all hover:bg-muted disabled:opacity-50"
                                    >
                                        <ShieldCheck className="size-3.5" />
                                        {askContact && !user ? 'Send to our team' : 'Talk to a person'}
                                    </button>
                                </div>
                            </div>
                        )}
                    </div>

                    <form
                        className="flex shrink-0 items-center gap-2 border-t border-border bg-card px-3 py-2.5"
                        onSubmit={(event) => {
                            event.preventDefault();
                            send(text);
                        }}
                    >
                        <input
                            value={text}
                            onChange={(e) => setText(e.target.value)}
                            disabled={locked}
                            maxLength={1000}
                            placeholder={locked ? 'Sign in to keep chatting' : state.escalated ? 'Message our team…' : 'Ask about our pastries…'}
                            aria-label="Message"
                            className="flex-1 rounded-xl border border-border bg-card px-3 py-2 text-xs text-foreground focus:border-border focus:outline-none disabled:opacity-60"
                        />
                        <button
                            type="submit"
                            disabled={busy || locked || text.trim().length < 2}
                            aria-label="Send"
                            className="flex size-9 items-center justify-center rounded-xl bg-primary text-primary-foreground disabled:opacity-40"
                        >
                            <Send className="size-4" />
                        </button>
                    </form>
                </div>
            )}

            <button
                type="button"
                onClick={toggle}
                aria-label={open ? 'Close chat' : 'Chat with us'}
                className="flex size-12 items-center justify-center rounded-full bg-primary text-sidebar-foreground shadow-xl transition-transform hover:scale-105 active:scale-95 sm:size-14"
            >
                {open ? <X className="size-6" /> : <MessageCircle className="size-6" />}
            </button>
        </div>
    );
}

function Bubble({ role, content }: ChatMessage) {
    if (role === 'user') {
        return (
            <div className="flex justify-end">
                <p className="max-w-[82%] rounded-2xl rounded-tr-sm bg-primary px-3.5 py-2.5 text-xs leading-relaxed break-words whitespace-pre-line text-sidebar-foreground">
                    {content}
                </p>
            </div>
        );
    }
    const staff = role === 'admin';
    return (
        <div className="flex items-start gap-2">
            <div
                className={cn(
                    'flex size-7 shrink-0 items-center justify-center rounded-full border',
                    staff ? 'border-success/30 bg-success/30 text-success-ink' : 'border-border bg-highlight/30 text-primary',
                )}
            >
                {staff ? <ShieldCheck className="size-4" /> : <Bot className="size-4" />}
            </div>
            <div
                className={cn(
                    'max-w-[85%] rounded-2xl rounded-tl-sm border px-3.5 py-2.5',
                    staff ? 'border-success/40 bg-success/20' : 'border-border bg-muted',
                )}
            >
                {staff && <div className="mb-0.5 text-[10px] font-bold text-success">Support Agent</div>}
                <p className="text-xs leading-relaxed break-words whitespace-pre-line text-foreground">{content.replace(/\*\*(.+?)\*\*/g, '$1')}</p>
            </div>
        </div>
    );
}

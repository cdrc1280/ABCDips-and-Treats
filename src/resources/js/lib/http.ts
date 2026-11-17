/** Same-origin JSON request with Laravel's XSRF cookie, for the few non-page endpoints (chat, lookups, quotes). */
export async function jsonRequest<T>(url: string, init: { method?: string; body?: unknown; signal?: AbortSignal } = {}): Promise<T> {
    const xsrf = document.cookie.match(/(?:^|; )XSRF-TOKEN=([^;]+)/)?.[1];
    const response = await fetch(url, {
        method: init.method ?? 'GET',
        credentials: 'same-origin',
        signal: init.signal,
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...(init.body !== undefined ? { 'Content-Type': 'application/json' } : {}),
            ...(xsrf ? { 'X-XSRF-TOKEN': decodeURIComponent(xsrf) } : {}),
        },
        body: init.body !== undefined ? JSON.stringify(init.body) : undefined,
    });

    const data = (await response.json().catch(() => ({}))) as T & { message?: string };
    if (!response.ok) throw new HttpError(data.message ?? 'Something went wrong. Please try again.', response.status);
    return data;
}

export class HttpError extends Error {
    constructor(
        message: string,
        public readonly status: number,
    ) {
        super(message);
    }
}

/**
 * Download a server-built file (report exports) without leaving the page. Errors come back as
 * a thrown HttpError carrying the server's message (e.g. "No data found"), so callers can toast it
 * instead of the browser opening a raw JSON page.
 */
export async function downloadFile(url: string, fallbackName = 'download'): Promise<void> {
    const response = await fetch(url, {
        credentials: 'same-origin',
        headers: { Accept: 'application/json, */*', 'X-Requested-With': 'XMLHttpRequest' },
    });

    if (!response.ok) {
        const data = (await response.json().catch(() => ({}))) as { message?: string };
        throw new HttpError(data.message ?? 'The file could not be generated. Please try again.', response.status);
    }

    const disposition = response.headers.get('Content-Disposition') ?? '';
    const name = decodeURIComponent(
        disposition.match(/filename\*=UTF-8''([^;]+)/i)?.[1] ?? disposition.match(/filename="?([^";]+)"?/i)?.[1] ?? fallbackName,
    );
    const href = URL.createObjectURL(await response.blob());
    const link = Object.assign(document.createElement('a'), { href, download: name });
    document.body.append(link);
    link.click();
    link.remove();
    window.setTimeout(() => URL.revokeObjectURL(href), 1000);
}

const peso = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
const number = new Intl.NumberFormat('en-US');

/** ₱1,234.50 — always 2 decimals, tabular in tables. */
export function money(value: number | string | null | undefined, symbol = '₱'): string {
    if (value === null || value === undefined || value === '') return '—';
    return `${symbol}${peso.format(Number(value))}`;
}

const compact = new Intl.NumberFormat('en-US', { notation: 'compact', maximumFractionDigits: 1 });

/** ₱12.9K — for axis ticks and big headline figures; exact values go in tooltips and tables. */
export function compactMoney(value: number, symbol = '₱'): string {
    return Math.abs(value) < 1000 ? `${symbol}${Math.round(value).toLocaleString('en-US')}` : `${symbol}${compact.format(value)}`;
}

export function formatNumber(value: number | string | null | undefined): string {
    if (value === null || value === undefined || value === '') return '—';
    return number.format(Number(value));
}

export function formatDate(value: string | null | undefined, withTime = false): string {
    if (!value) return '—';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return '—';
    return date.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        ...(withTime ? { hour: 'numeric', minute: '2-digit' } : {}),
    });
}

export function initials(name: string): string {
    return name
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase())
        .join('');
}

export function titleCase(value: string | null | undefined): string {
    if (!value) return '—';
    return value.replace(/[_-]+/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
}

import { formatDate } from '@/lib/format';

export function DateFormatted({ value, withTime = false }: { value: string | null | undefined; withTime?: boolean }) {
    if (!value) return <span className="text-muted-foreground">—</span>;
    return (
        <time dateTime={value} className="whitespace-nowrap tabular-nums">
            {formatDate(value, withTime)}
        </time>
    );
}

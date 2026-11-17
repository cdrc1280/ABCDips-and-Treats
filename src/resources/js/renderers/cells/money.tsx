import { money } from '@/lib/format';

export function Money({ value }: { value: number | string | null | undefined }) {
    return <span className="tabular-nums">{money(value)}</span>;
}

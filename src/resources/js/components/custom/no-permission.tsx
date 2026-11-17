import { ShieldAlert } from 'lucide-react';

export function NoPermission({ message = "You don't have permission to view this." }: { message?: string }) {
    return (
        <div className="flex flex-col items-center gap-2 rounded-xl border border-dashed p-10 text-center text-muted-foreground">
            <ShieldAlert className="size-8" />
            <p className="text-sm">{message}</p>
        </div>
    );
}

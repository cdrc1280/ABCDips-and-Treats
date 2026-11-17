export function AuthHeading({ title, description }: { title: string; description?: string }) {
    return (
        <div className="mb-6 grid gap-1">
            <h1 className="font-display text-[1.75rem] leading-tight tracking-tight">{title}</h1>
            {description && <p className="text-sm text-muted-foreground">{description}</p>}
        </div>
    );
}

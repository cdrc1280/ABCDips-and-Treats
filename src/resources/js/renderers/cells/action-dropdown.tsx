import { Link, router } from '@inertiajs/react';
import { Loader2, MoreHorizontal } from 'lucide-react';
import { useState, type FormEvent } from 'react';

import { ConfirmDialog } from '@/components/custom/confirm-dialog';
import { Field, FieldDescription, InputError } from '@/components/custom/field';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { usePermissions } from '@/hooks/use-permissions';
import type { RowAction } from '@/types';

/** Kebab menu for one row; permission-less and hidden() actions are left out. */
export function ActionDropdown<T>({ row, actions }: { row: T; actions: RowAction<T>[] }) {
    const { can } = usePermissions();
    const [confirming, setConfirming] = useState<RowAction<T> | null>(null);
    const [prompting, setPrompting] = useState<RowAction<T> | null>(null);
    const [promptValue, setPromptValue] = useState('');
    const [promptError, setPromptError] = useState<string | undefined>();
    const [processing, setProcessing] = useState(false);
    const visible = actions.filter((action) => can(action.permission) && !action.hidden?.(row));
    if (visible.length === 0) return <span className="text-muted-foreground">—</span>;

    const close = () => {
        setConfirming(null);
        setPrompting(null);
        setPromptValue('');
        setPromptError(undefined);
    };

    const run = (action: RowAction<T>, data: Record<string, string | number | boolean> = {}) => {
        if (action.onClick) return action.onClick(row);
        if (!action.href || !action.method) return;
        router.visit(action.href(row), {
            method: action.method,
            data,
            preserveScroll: true,
            onStart: () => setProcessing(true),
            onError: (errors) => setPromptError(action.prompt ? errors[action.prompt.field] : undefined),
            onSuccess: () => close(),
            onFinish: () => setProcessing(false),
        });
    };

    const select = (action: RowAction<T>) => {
        if (action.prompt) return setPrompting(action);
        if (action.isWithConfirmation) return setConfirming(action);
        run(action);
    };

    const regular = visible.filter((action) => !action.destructive);
    const destructive = visible.filter((action) => action.destructive);

    const item = (action: RowAction<T>) => {
        const Icon = action.icon;
        const body = (
            <>
                {Icon && <Icon />}
                {action.label}
            </>
        );
        if (action.href && !action.method && !action.isWithConfirmation && !action.prompt) {
            return (
                <DropdownMenuItem key={action.label} asChild>
                    {action.external ? <a href={action.href(row)}>{body}</a> : <Link href={action.href(row)}>{body}</Link>}
                </DropdownMenuItem>
            );
        }
        return (
            <DropdownMenuItem key={action.label} variant={action.destructive ? 'destructive' : 'default'} onSelect={() => select(action)}>
                {body}
            </DropdownMenuItem>
        );
    };

    const submitPrompt = (event: FormEvent) => {
        event.preventDefault();
        if (prompting?.prompt) run(prompting, { [prompting.prompt.field]: promptValue });
    };

    const describe = (value: string | ((row: T) => string) | undefined) => (typeof value === 'function' ? value(row) : value);

    return (
        <>
            <DropdownMenu>
                <DropdownMenuTrigger asChild>
                    <Button variant="ghost" size="icon" className="size-8" aria-label="Row actions">
                        <MoreHorizontal />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="start" className="w-52">
                    {regular.map(item)}
                    {regular.length > 0 && destructive.length > 0 && <DropdownMenuSeparator />}
                    {destructive.map(item)}
                </DropdownMenuContent>
            </DropdownMenu>

            {confirming && (
                <ConfirmDialog
                    open
                    onOpenChange={(open) => !open && close()}
                    title={confirming.confirmTitle ?? `${confirming.label}?`}
                    description={describe(confirming.confirmDescription) ?? 'This action cannot be undone.'}
                    confirmLabel={confirming.label}
                    destructive={confirming.destructive}
                    processing={processing}
                    onConfirm={() => run(confirming)}
                />
            )}

            {prompting?.prompt && (
                <Dialog open onOpenChange={(open) => !open && close()}>
                    <DialogContent className="sm:max-w-md">
                        <form onSubmit={submitPrompt} className="grid gap-4">
                            <DialogHeader>
                                <DialogTitle>{prompting.label}</DialogTitle>
                                {prompting.prompt.description && <DialogDescription>{describe(prompting.prompt.description)}</DialogDescription>}
                            </DialogHeader>
                            <Field>
                                <Label htmlFor="row-action-prompt">{prompting.prompt.label}</Label>
                                {prompting.prompt.type === 'select' ? (
                                    <SelectDropdown
                                        id="row-action-prompt"
                                        value={promptValue || null}
                                        invalid={!!promptError}
                                        options={
                                            typeof prompting.prompt.options === 'function'
                                                ? prompting.prompt.options(row)
                                                : (prompting.prompt.options ?? [])
                                        }
                                        onChange={(value) => setPromptValue(value === null ? '' : String(value))}
                                    />
                                ) : (
                                    <Input
                                        id="row-action-prompt"
                                        autoFocus
                                        type={prompting.prompt.type ?? 'text'}
                                        min={prompting.prompt.min}
                                        step={prompting.prompt.step}
                                        value={promptValue}
                                        aria-invalid={!!promptError}
                                        onChange={(event) => setPromptValue(event.target.value)}
                                    />
                                )}
                                {!promptError && <FieldDescription>Saved immediately.</FieldDescription>}
                                <InputError message={promptError} />
                            </Field>
                            <DialogFooter>
                                <Button type="button" variant="outline" onClick={close} disabled={processing}>
                                    Cancel
                                </Button>
                                <Button type="submit" disabled={processing || promptValue === ''}>
                                    {processing && <Loader2 className="animate-spin" />} {prompting.label}
                                </Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            )}
        </>
    );
}

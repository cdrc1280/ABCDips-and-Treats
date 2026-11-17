import type { CartLine } from '@/types';

/** Variation / flavor / custom-cake spec badges for one basket line. */
export function CartLineDetails({ line }: { line: CartLine }) {
    const options = line.options ?? {};

    return (
        <>
            {Array.isArray(options.flavors) && options.flavors.length > 0 ? (
                <div className="mt-1 flex flex-wrap gap-1">
                    <span className="rounded-md border border-highlight/40 bg-highlight/20 px-2 py-0.5 text-[10px] font-semibold text-foreground">
                        Assorted: {options.flavors.join(', ')}
                    </span>
                </div>
            ) : (
                options.flavor && <div className="mt-0.5 text-xs font-semibold text-primary">Flavor: {options.flavor}</div>
            )}
            {options.variation && <div className="mt-0.5 text-xs font-semibold text-primary">Option: {options.variation}</div>}
            {options.is_custom && (
                <div className="mt-1 space-y-0.5 rounded-xl border border-primary/20 bg-background p-2.5 text-[11px] text-primary">
                    <div className="font-semibold">Custom Cake Spec:</div>
                    {options.flavor_preference && (
                        <div>
                            Flavor: <strong>{options.flavor_preference}</strong>
                        </div>
                    )}
                    {options.frosting_type && (
                        <div>
                            Frosting: <strong>{options.frosting_type}</strong>
                        </div>
                    )}
                    {options.cake_inscription && <div className="text-muted-foreground italic">“{options.cake_inscription}”</div>}
                    {options.event_date && <div className="text-muted-foreground">Event: {options.event_date}</div>}
                </div>
            )}
        </>
    );
}

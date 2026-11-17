import { Cake, CircleDot, CupSoda, Layers, ShoppingBag, Wheat, type LucideProps } from 'lucide-react';

/** A lucide icon for a storefront category, picked from its slug. */
export function CategoryIcon({ slug, ...props }: { slug: string | null | undefined } & LucideProps) {
    const s = slug ?? '';
    if (/banana|bread|loaf/.test(s)) return <Wheat {...props} />;
    if (/cookie|crinkle/.test(s)) return <CircleDot {...props} />;
    if (/brownie|dip/.test(s)) return <Layers {...props} />;
    if (/cake|cheese|cupcake/.test(s)) return <Cake {...props} />;
    if (/drink|coffee|tea/.test(s)) return <CupSoda {...props} />;
    return <ShoppingBag {...props} />;
}

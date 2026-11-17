/**
 * Create/edit page grid: sticky identity aside (preview) left, form right. Below lg it is one column and the
 * preview (always the first child) moves after the form, so phones open on the fields.
 * minmax(0,…) + min-w-0 keep long content from widening the page on phones.
 */
export const tabGridClass =
    'grid min-w-0 grid-cols-[minmax(0,1fr)] items-start gap-4 max-lg:[&>*:first-child]:order-last lg:grid-cols-[20rem_minmax(0,1fr)] xl:grid-cols-[24rem_minmax(0,1fr)]';

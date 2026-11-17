import type { TablePreferences } from './data-table';

/** The query behind an index table — mirrors App\Shared\Data\FilterData (snake_case). */
export interface Filters<TSearch extends object = Record<string, unknown>> {
    search: Partial<TSearch>;
    sort_field: string | null;
    sort_dir: 'asc' | 'desc' | null;
    per_page: number;
    page: number;
}

export type FilterFieldType = 'text' | 'select' | 'date' | 'boolean';

export interface FilterField {
    name: string;
    label: string;
    type: FilterFieldType;
    placeholder?: string;
    options?: readonly { label: string; value: string | number }[];
    /** Name of a page prop holding Option[] (for server-provided select options). */
    optionsProp?: string;
}

/** Turn a `filter.ts` schema (`as const satisfies readonly FilterField[]`) into its search shape. */
export type SearchFieldsOf<T extends readonly FilterField[]> = {
    [K in T[number] as K['name']]?: K['type'] extends 'boolean' ? boolean | string : string;
};

export interface TableState {
    name: string;
    columns: TablePreferences;
    per_page_options: number[];
}

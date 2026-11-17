import { usePage } from '@inertiajs/react';

/**
 * Whether a form field is required, according to the backend Data class's rules
 * (shared by the #[FormRules] attribute on the controller method).
 */
export function useRequired() {
    const { form_rules } = usePage().props;
    const required = new Set(form_rules?.required ?? []);

    return (field: string): boolean => required.has(field);
}

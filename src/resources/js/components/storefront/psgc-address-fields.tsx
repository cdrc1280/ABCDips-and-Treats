import { useEffect, useState } from 'react';

import { SelectDropdown } from '@/components/custom/select-dropdown';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

interface PsgcPlace {
    code: string;
    name: string;
}

export interface PsgcAddress {
    region: string;
    province: string;
    city: string;
    barangay: string;
    street_address: string;
}

const API = 'https://psgc.gitlab.io/api';
const NCR = '130000000';
const memory = new Map<string, Promise<PsgcPlace[]>>();

/** Philippine Standard Geographic Code lists, cached in memory and localStorage (they rarely change). */
function loadPlaces(url: string): Promise<PsgcPlace[]> {
    const hit = memory.get(url);
    if (hit) return hit;
    const key = `psgc:${url}`;
    const promise = (async () => {
        try {
            const cached = localStorage.getItem(key);
            if (cached) return JSON.parse(cached) as PsgcPlace[];
        } catch {
            /* storage unavailable */
        }
        const res = await fetch(url);
        if (!res.ok) throw new Error('PSGC unavailable');
        const rows = ((await res.json()) as PsgcPlace[]).map(({ code, name }) => ({ code, name })).sort((a, b) => a.name.localeCompare(b.name));
        try {
            localStorage.setItem(key, JSON.stringify(rows));
        } catch {
            /* quota */
        }
        return rows;
    })();
    memory.set(url, promise);
    promise.catch(() => memory.delete(url));
    return promise;
}

function usePlaces(url: string | null) {
    const [state, setState] = useState<{ url: string | null; rows: PsgcPlace[]; failed: boolean }>({ url: null, rows: [], failed: false });

    useEffect(() => {
        if (!url) return;
        let live = true;
        loadPlaces(url)
            .then((rows) => live && setState({ url, rows, failed: false }))
            .catch(() => live && setState({ url, rows: [], failed: true }));
        return () => {
            live = false;
        };
    }, [url]);

    const ready = state.url === url;
    return { rows: ready ? state.rows : [], loading: !!url && !ready, failed: ready && state.failed };
}

const toOptions = (rows: PsgcPlace[]) => rows.map((row) => ({ value: row.name, label: row.name }));

/**
 * Region → province → city/municipality → barangay pickers backed by the public PSGC API.
 * Values are stored as names (what the order and profile keep); NCR has no provinces.
 */
export function PsgcAddressFields({
    value,
    onChange,
    errors = {},
}: {
    value: PsgcAddress;
    onChange: (next: PsgcAddress) => void;
    errors?: Partial<Record<keyof PsgcAddress, string>>;
}) {
    const regions = usePlaces(`${API}/regions.json`);
    const region = regions.rows.find((r) => r.name === value.region);
    const ncr = region?.code === NCR;
    const provinces = usePlaces(region && !ncr ? `${API}/regions/${region.code}/provinces.json` : null);
    const province = provinces.rows.find((p) => p.name === value.province);
    const cities = usePlaces(
        ncr && region
            ? `${API}/regions/${region.code}/cities-municipalities.json`
            : province
              ? `${API}/provinces/${province.code}/cities-municipalities.json`
              : null,
    );
    const city = cities.rows.find((c) => c.name === value.city);
    const barangays = usePlaces(city ? `${API}/cities-municipalities/${city.code}/barangays.json` : null);

    const set = (patch: Partial<PsgcAddress>) => onChange({ ...value, ...patch });
    const offline = regions.failed;

    if (offline) {
        // PSGC unreachable: plain text fields so checkout never blocks on a third-party API.
        return (
            <div className="grid gap-4 sm:grid-cols-2">
                {(['region', 'province', 'city', 'barangay'] as const).map((field) => (
                    <div key={field} className="space-y-1.5">
                        <Label htmlFor={`addr-${field}`} className="capitalize">
                            {field === 'city' ? 'City / Municipality' : field}
                        </Label>
                        <Input id={`addr-${field}`} value={value[field]} onChange={(e) => set({ [field]: e.target.value })} />
                        {errors[field] && <p className="text-xs text-destructive">{errors[field]}</p>}
                    </div>
                ))}
                <StreetField value={value.street_address} onChange={(street_address) => set({ street_address })} error={errors.street_address} />
            </div>
        );
    }

    return (
        <div className="grid gap-4 sm:grid-cols-2">
            <Field label="Region" error={errors.region}>
                <SelectDropdown
                    id="addr-region"
                    value={value.region || null}
                    options={toOptions(regions.rows)}
                    placeholder={regions.loading ? 'Loading…' : 'Select region'}
                    searchPlaceholder="Search region…"
                    onChange={(name) => set({ region: name ?? '', province: '', city: '', barangay: '' })}
                />
            </Field>
            {!ncr && (
                <Field label="Province" error={errors.province}>
                    <SelectDropdown
                        id="addr-province"
                        value={value.province || null}
                        options={toOptions(provinces.rows)}
                        disabled={!region}
                        placeholder={provinces.loading ? 'Loading…' : 'Select province'}
                        searchPlaceholder="Search province…"
                        onChange={(name) => set({ province: name ?? '', city: '', barangay: '' })}
                    />
                </Field>
            )}
            <Field label="City / Municipality" error={errors.city}>
                <SelectDropdown
                    id="addr-city"
                    value={value.city || null}
                    options={toOptions(cities.rows)}
                    disabled={ncr ? !region : !province}
                    placeholder={cities.loading ? 'Loading…' : 'Select city'}
                    searchPlaceholder="Search city…"
                    onChange={(name) => set({ city: name ?? '', barangay: '', ...(ncr ? { province: 'Metro Manila' } : {}) })}
                />
            </Field>
            <Field label="Barangay" error={errors.barangay}>
                <SelectDropdown
                    id="addr-barangay"
                    value={value.barangay || null}
                    options={toOptions(barangays.rows)}
                    disabled={!city}
                    placeholder={barangays.loading ? 'Loading…' : 'Select barangay'}
                    searchPlaceholder="Search barangay…"
                    onChange={(name) => set({ barangay: name ?? '' })}
                />
            </Field>
            <StreetField value={value.street_address} onChange={(street_address) => set({ street_address })} error={errors.street_address} />
        </div>
    );
}

function Field({ label, error, children }: { label: string; error?: string; children: React.ReactNode }) {
    return (
        <div className="space-y-1.5">
            <Label>{label}</Label>
            {children}
            {error && <p className="text-xs text-destructive">{error}</p>}
        </div>
    );
}

function StreetField({ value, onChange, error }: { value: string; onChange: (v: string) => void; error?: string }) {
    return (
        <div className="space-y-1.5 sm:col-span-2">
            <Label htmlFor="addr-street">House no., street, subdivision / landmark</Label>
            <Input
                id="addr-street"
                value={value}
                onChange={(e) => onChange(e.target.value)}
                maxLength={500}
                placeholder="e.g. Blk 5 Lot 12, Mabini St., near the chapel"
            />
            {error && <p className="text-xs text-destructive">{error}</p>}
        </div>
    );
}

export function formatAddress(a: PsgcAddress): string {
    return [a.street_address, a.barangay, a.city, a.province].filter(Boolean).join(', ');
}

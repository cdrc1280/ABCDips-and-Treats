import 'leaflet/dist/leaflet.css';

import { Crosshair, Loader2, MapPin } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';

import { Button } from '@/components/ui/button';

type Leaflet = typeof import('leaflet');
export interface LatLng {
    lat: number;
    lng: number;
}

const pinIcon = (L: Leaflet, color: string) =>
    L.divIcon({
        className: '',
        iconSize: [28, 28],
        iconAnchor: [14, 28],
        html: `<svg viewBox="0 0 24 24" width="28" height="28" fill="${color}" stroke="#fff" stroke-width="1.5"><path d="M12 22s7-6.2 7-12a7 7 0 1 0-14 0c0 5.8 7 12 7 12z"/><circle cx="12" cy="10" r="2.6" fill="#fff"/></svg>`,
    });

/** Drag / tap to drop the delivery pin; Leaflet loads only when the map is shown. */
export function MapPinPicker({ store, value, onChange }: { store: LatLng; value: LatLng | null; onChange: (point: LatLng) => void }) {
    const node = useRef<HTMLDivElement>(null);
    const marker = useRef<import('leaflet').Marker | null>(null);
    const map = useRef<import('leaflet').Map | null>(null);
    const latest = useRef(onChange);
    const [ready, setReady] = useState(false);
    const [locating, setLocating] = useState(false);
    const [geoError, setGeoError] = useState<string | null>(null);

    useEffect(() => {
        latest.current = onChange;
    });

    useEffect(() => {
        let disposed = false;
        import('leaflet').then((L) => {
            if (disposed || !node.current) return;
            const start = value ?? store;
            const instance = L.map(node.current, { zoomControl: true, attributionControl: true }).setView([start.lat, start.lng], value ? 16 : 13);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
            }).addTo(instance);
            L.marker([store.lat, store.lng], { icon: pinIcon(L, '#5C3A22') })
                .addTo(instance)
                .bindTooltip('ABCDips store');
            const pin = L.marker([start.lat, start.lng], { draggable: true, icon: pinIcon(L, '#C0392B') }).addTo(instance);
            pin.on('dragend', () => {
                const { lat, lng } = pin.getLatLng();
                latest.current({ lat, lng });
            });
            instance.on('click', (event) => {
                pin.setLatLng(event.latlng);
                latest.current({ lat: event.latlng.lat, lng: event.latlng.lng });
            });
            map.current = instance;
            marker.current = pin;
            setReady(true);
        });
        return () => {
            disposed = true;
            map.current?.remove();
            map.current = null;
        };
        // Map is created once; later pin moves come from the marker itself.
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    const locate = () => {
        if (!navigator.geolocation) {
            setGeoError('Location is not available in this browser.');
            return;
        }
        setLocating(true);
        setGeoError(null);
        navigator.geolocation.getCurrentPosition(
            ({ coords }) => {
                const point = { lat: coords.latitude, lng: coords.longitude };
                marker.current?.setLatLng([point.lat, point.lng]);
                map.current?.setView([point.lat, point.lng], 17);
                onChange(point);
                setLocating(false);
            },
            () => {
                setGeoError('We could not get your location. Drag the pin instead.');
                setLocating(false);
            },
            { enableHighAccuracy: true, timeout: 10000 },
        );
    };

    return (
        <div className="space-y-2">
            <div className="relative h-72 overflow-hidden rounded-2xl border border-primary/30">
                <div ref={node} className="size-full" role="application" aria-label="Delivery location map — drag the red pin to your door" />
                {!ready && (
                    <div className="absolute inset-0 flex items-center justify-center bg-background">
                        <Loader2 className="size-6 animate-spin text-primary" />
                    </div>
                )}
            </div>
            <div className="flex flex-wrap items-center justify-between gap-2">
                <p className="flex items-center gap-1.5 text-xs text-muted-foreground">
                    <MapPin className="size-3.5 text-destructive" />
                    {value ? `Pinned at ${value.lat.toFixed(5)}, ${value.lng.toFixed(5)}` : 'Tap the map or drag the red pin to your door.'}
                </p>
                <Button type="button" size="sm" variant="outline" onClick={locate} disabled={locating}>
                    {locating ? <Loader2 className="size-3.5 animate-spin" /> : <Crosshair className="size-3.5" />} Use my location
                </Button>
            </div>
            {geoError && <p className="text-xs text-destructive">{geoError}</p>}
        </div>
    );
}

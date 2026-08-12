<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />

<div
    x-data="{
        lat: $wire.entangle('data.store_lat'),
        lng: $wire.entangle('data.store_lng'),
        address: $wire.entangle('data.store_address'),
        searchQuery: '',
        searching: false,
        noResults: false,
        suggestions: [],
        map: null,
        marker: null,

        init() {
            this.$nextTick(() => {
                this.loadLeafletScript().then(() => {
                    setTimeout(() => this.initMap(), 200);
                });
            });
        },

        loadLeafletScript() {
            return new Promise((resolve) => {
                if (window.L) return resolve(window.L);

                if (!document.getElementById('leaflet-js-admin')) {
                    const script = document.createElement('script');
                    script.id = 'leaflet-js-admin';
                    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                    script.onload = () => resolve(window.L);
                    document.body.appendChild(script);
                } else {
                    const check = setInterval(() => {
                        if (window.L) {
                            clearInterval(check);
                            resolve(window.L);
                        }
                    }, 100);
                }
            });
        },

        initMap() {
            const L = window.L;
            if (!this.$refs.adminMap || !L) return;

            const startLat = parseFloat(this.lat) || 14.4597;
            const startLng = parseFloat(this.lng) || 120.9640;

            if (this.map) {
                try { this.map.remove(); } catch (e) {}
                this.map = null;
            }

            this.map = L.map(this.$refs.adminMap, {
                center: [startLat, startLng],
                zoom: 15,
                zoomControl: true,
            });

            // Fast CARTO Voyager Tiles
            const cartoLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                maxZoom: 19,
                subdomains: 'abcd',
                attribution: '© OpenStreetMap © CARTO',
            });

            const esriLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
                maxZoom: 19,
                attribution: '© Esri',
            });

            cartoLayer.on('tileerror', () => {
                if (this.map) {
                    this.map.removeLayer(cartoLayer);
                    esriLayer.addTo(this.map);
                }
            });

            cartoLayer.addTo(this.map);

            const storeIcon = L.divIcon({
                html: `<div style='background:#5C3A22; color:#FBF3E7; width:38px; height:38px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:22px; border:2px solid #D9A876; box-shadow:0 4px 12px rgba(0,0,0,0.5); cursor:grab;'>🏪</div>`,
                className: '',
                iconSize: [38, 38],
                iconAnchor: [19, 19],
            });

            this.marker = L.marker([startLat, startLng], {
                draggable: true,
                icon: storeIcon,
            }).addTo(this.map).bindPopup('<b>ABCDips Store Location</b>');

            this.marker.on('dragend', () => {
                const pos = this.marker.getLatLng();
                this.lat = pos.lat.toFixed(6);
                this.lng = pos.lng.toFixed(6);
                this.reverseGeocode(pos.lat, pos.lng);
            });

            this.map.on('click', (e) => {
                const { lat, lng } = e.latlng;
                this.marker.setLatLng([lat, lng]);
                this.lat = lat.toFixed(6);
                this.lng = lng.toFixed(6);
                this.reverseGeocode(lat, lng);
            });

            if ('ResizeObserver' in window && this.$refs.adminMap) {
                new ResizeObserver(() => {
                    if (this.map) this.map.invalidateSize();
                }).observe(this.$refs.adminMap);
            }

            setTimeout(() => {
                if (this.map) this.map.invalidateSize();
            }, 300);
        },

        async searchAddress() {
            const q = (this.searchQuery || '').trim();
            if (!q) return;

            this.searching = true;
            this.noResults = false;
            this.suggestions = [];

            try {
                const res = await fetch(`https://photon.komoot.io/api/?q=${encodeURIComponent(q + ' Philippines')}&limit=5&bbox=119.5,13.5,122.5,15.5`);
                const data = await res.json();
                if (data && data.features && data.features.length > 0) {
                    this.suggestions = data.features.map(f => {
                        const p = f.properties;
                        const name = p.name || p.street || p.district || 'Location';
                        const subtitle = [p.street, p.district, p.city, p.state].filter(Boolean).join(', ');
                        return {
                            display_name: [name, subtitle].filter(Boolean).join(', '),
                            lat: f.geometry.coordinates[1],
                            lon: f.geometry.coordinates[0],
                        };
                    });
                    this.selectSuggestion(this.suggestions[0]);
                } else {
                    this.noResults = true;
                }
            } catch {
                this.noResults = true;
            } finally {
                this.searching = false;
            }
        },

        selectSuggestion(sug) {
            const newLat = parseFloat(sug.lat);
            const newLng = parseFloat(sug.lon);
            this.lat = newLat.toFixed(6);
            this.lng = newLng.toFixed(6);
            this.address = sug.display_name;
            this.suggestions = [];
            this.searchQuery = sug.display_name;
            this.noResults = false;

            if (this.map && this.marker) {
                this.map.setView([newLat, newLng], 16);
                this.marker.setLatLng([newLat, newLng]);
                setTimeout(() => {
                    if (this.map) this.map.invalidateSize();
                }, 100);
            }
        },

        async reverseGeocode(lat, lng) {
            try {
                const res = await fetch(`https://photon.komoot.io/reverse?lat=${lat}&lon=${lng}`);
                const data = await res.json();
                if (data && data.features && data.features.length > 0) {
                    const props = data.features[0].properties;
                    const name = props.name || props.street || props.district || '';
                    const city = props.city || props.county || props.state || '';
                    const fullAddr = [name, props.street, props.district, city].filter(Boolean).join(', ');
                    if (fullAddr) {
                        this.address = fullAddr;
                        return;
                    }
                }
            } catch {}

            try {
                const res = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&countrycodes=ph`, {
                    headers: { 'Accept-Language': 'en' }
                });
                const data = await res.json();
                if (data && data.display_name) {
                    this.address = data.display_name;
                }
            } catch {}
        }
    }"
    class="space-y-3"
>
    <div class="flex items-center justify-between">
        <label class="text-sm font-semibold text-gray-700 dark:text-gray-200 flex items-center gap-2">
            <span>📍 Store Location Map Pinpoint</span>
            <span class="text-xs text-gray-400 font-normal">(Drag the 🏪 store icon or click anywhere on the map)</span>
        </label>
        <span x-text="lat && lng ? `GPS: ${lat}, ${lng}` : 'GPS: Not Set'" class="text-xs font-mono font-bold text-amber-500 bg-amber-500/10 px-2.5 py-0.5 rounded-full border border-amber-500/20"></span>
    </div>

    <!-- Search Input for Admin -->
    <div class="relative">
        <div class="flex gap-2">
            <input
                x-model="searchQuery"
                @keydown.enter.prevent="searchAddress"
                type="text"
                placeholder="Search bakery store location in Philippines (e.g. Molino Blvd, Bacoor)..."
                class="fi-input flex-1 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-xs text-gray-900 dark:text-white"
            />
            <button
                @click.prevent="searchAddress"
                type="button"
                class="fi-btn rounded-lg bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs px-4 py-2 shadow-xs transition-all flex items-center gap-1.5 flex-shrink-0"
            >
                <span x-show="!searching">🔍 Search Map</span>
                <span x-show="searching" class="flex items-center gap-1">
                    <svg class="animate-spin h-3.5 w-3.5 text-white" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    Searching...
                </span>
            </button>
        </div>

        <!-- No Results Warning -->
        <div x-show="noResults" class="mt-1 text-xs text-red-500 font-semibold px-1">
            ⚠️ No location found for "<span x-text="searchQuery"></span>". Try searching by barangay or city (e.g. "Molino Bacoor").
        </div>

        <!-- Autocomplete Suggestions List -->
        <template x-if="suggestions.length > 0">
            <div class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden z-[500] max-h-48 overflow-y-auto">
                <template x-for="(sug, i) in suggestions" :key="i">
                    <button
                        @click.prevent="selectSuggestion(sug)"
                        type="button"
                        class="w-full text-left px-3.5 py-2 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-500/10 hover:text-amber-500 border-b border-gray-100 dark:border-gray-700/50 last:border-0 truncate"
                    >
                        📍 <span x-text="sug.display_name"></span>
                    </button>
                </template>
            </div>
        </template>
    </div>

    <!-- Map Canvas Container with Explicit Inline Dimensions -->
    <div class="relative rounded-xl overflow-hidden border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 shadow-inner z-10" style="height: 300px; min-height: 300px; width: 100%;">
        <div x-ref="adminMap" style="height: 300px; min-height: 300px; width: 100%;"></div>
    </div>
</div>

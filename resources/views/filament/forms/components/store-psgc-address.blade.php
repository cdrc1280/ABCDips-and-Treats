<div
    x-data="{
        region: $wire.entangle('data.store_region'),
        province: $wire.entangle('data.store_province'),
        city: $wire.entangle('data.store_city'),
        barangay: $wire.entangle('data.store_barangay'),
        street: $wire.entangle('data.store_street_address'),
        fullAddress: $wire.entangle('data.store_address'),

        regions: [],
        provinces: [],
        cities: [],
        barangays: [],

        selectedRegionCode: '',
        selectedProvinceCode: '',
        selectedCityCode: '',
        selectedBarangayCode: '',

        searchRegion: '',
        searchProvince: '',
        searchCity: '',
        searchBarangay: '',

        openRegion: false,
        openProvince: false,
        openCity: false,
        openBarangay: false,

        loadingRegions: false,
        loadingProvinces: false,
        loadingCities: false,
        loadingBarangays: false,

        get isNcr() {
            const reg = this.regions.find(r => r.code === this.selectedRegionCode);
            return reg ? (reg.code === '130000000' || reg.regionName === 'NCR') : false;
        },

        get filteredRegions() {
            const q = (this.searchRegion || '').toLowerCase().trim();
            if (!q) return this.regions;
            return this.regions.filter(r => r.name.toLowerCase().includes(q) || (r.regionName && r.regionName.toLowerCase().includes(q)));
        },

        get filteredProvinces() {
            const q = (this.searchProvince || '').toLowerCase().trim();
            if (!q) return this.provinces;
            return this.provinces.filter(p => p.name.toLowerCase().includes(q));
        },

        get filteredCities() {
            const q = (this.searchCity || '').toLowerCase().trim();
            if (!q) return this.cities;
            return this.cities.filter(c => c.name.toLowerCase().includes(q));
        },

        get filteredBarangays() {
            const q = (this.searchBarangay || '').toLowerCase().trim();
            if (!q) return this.barangays;
            return this.barangays.filter(b => b.name.toLowerCase().includes(q));
        },

        init() {
            this.loadRegions().then(() => {
                this.syncFromExistingValues();
            });
        },

        async fetchPsgc(url, cacheKey) {
            const cached = localStorage.getItem(`psgc_admin_${cacheKey}`);
            if (cached) {
                try { return JSON.parse(cached); } catch(e) {}
            }
            try {
                const res = await fetch(url);
                const data = await res.json();
                if (Array.isArray(data)) {
                    localStorage.setItem(`psgc_admin_${cacheKey}`, JSON.stringify(data));
                }
                return data;
            } catch(e) {
                return [];
            }
        },

        async loadRegions() {
            this.loadingRegions = true;
            this.regions = await this.fetchPsgc('https://psgc.gitlab.io/api/regions.json', 'regions');
            this.loadingRegions = false;
        },

        async syncFromExistingValues() {
            if (this.region) {
                const reg = this.regions.find(r => r.name === this.region || r.regionName === this.region);
                if (reg) {
                    this.selectedRegionCode = reg.code;
                    this.searchRegion = reg.name;
                    await this.onRegionChange(true);
                }
            } else {
                const defaultReg = this.regions.find(r => r.code === '040000000') || this.regions[0];
                if (defaultReg) {
                    this.selectedRegionCode = defaultReg.code;
                    this.region = defaultReg.name;
                    this.searchRegion = defaultReg.name;
                    await this.onRegionChange(true);
                }
            }

            if (this.province && this.provinces.length > 0) {
                const prov = this.provinces.find(p => p.name === this.province);
                if (prov) {
                    this.selectedProvinceCode = prov.code;
                    this.searchProvince = prov.name;
                    await this.onProvinceChange(true);
                }
            }

            if (this.city && this.cities.length > 0) {
                const c = this.cities.find(ci => ci.name === this.city);
                if (c) {
                    this.selectedCityCode = c.code;
                    this.searchCity = c.name;
                    await this.onCityChange(true);
                }
            }

            if (this.barangay && this.barangays.length > 0) {
                const b = this.barangays.find(br => br.name === this.barangay);
                if (b) {
                    this.selectedBarangayCode = b.code;
                    this.searchBarangay = b.name;
                }
            }
        },

        async selectRegionItem(reg) {
            this.selectedRegionCode = reg.code;
            this.searchRegion = reg.name;
            this.openRegion = false;
            await this.onRegionChange(false);
        },

        async selectProvinceItem(prov) {
            this.selectedProvinceCode = prov.code;
            this.searchProvince = prov.name;
            this.openProvince = false;
            await this.onProvinceChange(false);
        },

        async selectCityItem(c) {
            this.selectedCityCode = c.code;
            this.searchCity = c.name;
            this.openCity = false;
            await this.onCityChange(false);
        },

        selectBarangayItem(b) {
            this.selectedBarangayCode = b.code;
            this.searchBarangay = b.name;
            this.openBarangay = false;
            this.onBarangayChange();
        },

        async onRegionChange(skipReset = false) {
            const reg = this.regions.find(r => r.code === this.selectedRegionCode);
            this.region = reg ? reg.name : '';

            if (!skipReset) {
                this.selectedProvinceCode = '';
                this.province = '';
                this.searchProvince = '';
                this.selectedCityCode = '';
                this.city = '';
                this.searchCity = '';
                this.selectedBarangayCode = '';
                this.barangay = '';
                this.searchBarangay = '';
            }

            this.provinces = [];
            this.cities = [];
            this.barangays = [];

            if (!this.selectedRegionCode) return;

            if (this.isNcr) {
                this.province = 'Metro Manila';
                this.searchProvince = 'Metro Manila';
                this.loadingCities = true;
                this.cities = await this.fetchPsgc(`https://psgc.gitlab.io/api/regions/${this.selectedRegionCode}/cities-municipalities.json`, 'cities_ncr');
                this.loadingCities = false;
            } else {
                this.loadingProvinces = true;
                this.provinces = await this.fetchPsgc(`https://psgc.gitlab.io/api/regions/${this.selectedRegionCode}/provinces.json`, `prov_${this.selectedRegionCode}`);
                this.loadingProvinces = false;
            }

            this.compileFullAddress();
        },

        async onProvinceChange(skipReset = false) {
            const prov = this.provinces.find(p => p.code === this.selectedProvinceCode);
            this.province = prov ? prov.name : '';

            if (!skipReset) {
                this.selectedCityCode = '';
                this.city = '';
                this.searchCity = '';
                this.selectedBarangayCode = '';
                this.barangay = '';
                this.searchBarangay = '';
            }

            this.cities = [];
            this.barangays = [];

            if (!this.selectedProvinceCode) return;

            this.loadingCities = true;
            this.cities = await this.fetchPsgc(`https://psgc.gitlab.io/api/provinces/${this.selectedProvinceCode}/cities-municipalities.json`, `cities_prov_${this.selectedProvinceCode}`);
            this.loadingCities = false;

            this.compileFullAddress();
        },

        async onCityChange(skipReset = false) {
            const c = this.cities.find(ci => ci.code === this.selectedCityCode);
            this.city = c ? c.name : '';

            if (!skipReset) {
                this.selectedBarangayCode = '';
                this.barangay = '';
                this.searchBarangay = '';
            }

            this.barangays = [];

            if (!this.selectedCityCode) return;

            this.loadingBarangays = true;
            this.barangays = await this.fetchPsgc(`https://psgc.gitlab.io/api/cities-municipalities/${this.selectedCityCode}/barangays.json`, `brgy_city_${this.selectedCityCode}`);
            this.loadingBarangays = false;

            this.compileFullAddress();
        },

        onBarangayChange() {
            const b = this.barangays.find(br => br.code === this.selectedBarangayCode);
            this.barangay = b ? b.name : '';
            this.compileFullAddress();
        },

        compileFullAddress() {
            const parts = [
                this.street,
                this.barangay,
                this.city,
                this.province,
                this.region
            ].filter(Boolean);
            this.fullAddress = parts.join(', ');
        }
    }"
    class="space-y-4 bg-gray-50 dark:bg-gray-800/80 p-4 rounded-xl border border-gray-200 dark:border-gray-700 col-span-full shadow-xs"
>
    <div class="flex items-center justify-between">
        <label class="text-xs font-bold uppercase tracking-wider text-gray-800 dark:text-amber-400 flex items-center gap-1.5">
            <span>🇵🇭</span> Store Pickup & Lalamove Origin PSGC Address
        </label>
        <span x-show="loadingRegions || loadingProvinces || loadingCities || loadingBarangays" class="text-xs text-amber-500 flex items-center gap-1">
            <svg class="animate-spin h-3.5 w-3.5 text-amber-500" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
            Loading PSGC Data...
        </span>
    </div>

    <!-- 4-Level Searchable Combobox Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- 1. Searchable Region -->
        <div class="relative" @click.outside="openRegion = false">
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-200 mb-1">Store Region *</label>
            <div class="relative">
                <input
                    type="text"
                    x-model="searchRegion"
                    @focus="openRegion = true"
                    @input="openRegion = true"
                    placeholder="Search or select Region..."
                    class="fi-input w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3.5 py-2.5 text-xs text-gray-900 dark:text-white shadow-xs focus:ring-2 focus:ring-amber-500/50"
                />
                <button type="button" @click="openRegion = !openRegion" class="absolute right-2.5 top-2.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs">
                    ▼
                </button>
            </div>

            <!-- Dropdown Options Overlay -->
            <div x-show="openRegion" x-transition class="absolute left-0 right-0 top-full mt-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl z-[600] max-h-52 overflow-y-auto">
                <template x-for="reg in filteredRegions" :key="reg.code">
                    <button
                        type="button"
                        @click="selectRegionItem(reg)"
                        class="w-full text-left px-3.5 py-2 text-xs text-gray-900 dark:text-gray-100 hover:bg-amber-500/15 hover:text-amber-600 dark:hover:text-amber-400 border-b border-gray-100 dark:border-gray-800 last:border-0"
                    >
                        <span class="font-bold" x-text="reg.name"></span>
                        <span class="text-[10px] text-gray-400 ml-1" x-text="`(${reg.regionName})`"></span>
                    </button>
                </template>
                <div x-show="filteredRegions.length === 0" class="px-3.5 py-2.5 text-xs text-gray-400 italic">No region found.</div>
            </div>
        </div>

        <!-- 2. Searchable Province -->
        <div class="relative" @click.outside="openProvince = false">
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-200 mb-1">Store Province *</label>
            <div class="relative">
                <input
                    type="text"
                    x-model="searchProvince"
                    @focus="if(!isNcr && selectedRegionCode) openProvince = true"
                    @input="if(!isNcr && selectedRegionCode) openProvince = true"
                    :disabled="!selectedRegionCode || isNcr"
                    placeholder="Search or select Province..."
                    class="fi-input w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3.5 py-2.5 text-xs text-gray-900 dark:text-white shadow-xs focus:ring-2 focus:ring-amber-500/50 disabled:opacity-60 disabled:cursor-not-allowed"
                />
                <button type="button" @click="if(!isNcr && selectedRegionCode) openProvince = !openProvince" class="absolute right-2.5 top-2.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs">
                    ▼
                </button>
            </div>

            <!-- Dropdown Options Overlay -->
            <div x-show="openProvince" x-transition class="absolute left-0 right-0 top-full mt-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl z-[600] max-h-52 overflow-y-auto">
                <template x-for="prov in filteredProvinces" :key="prov.code">
                    <button
                        type="button"
                        @click="selectProvinceItem(prov)"
                        class="w-full text-left px-3.5 py-2 text-xs text-gray-900 dark:text-gray-100 hover:bg-amber-500/15 hover:text-amber-600 dark:hover:text-amber-400 border-b border-gray-100 dark:border-gray-800 last:border-0"
                        x-text="prov.name"
                    ></button>
                </template>
                <div x-show="filteredProvinces.length === 0" class="px-3.5 py-2.5 text-xs text-gray-400 italic">No province found.</div>
            </div>
        </div>

        <!-- 3. Searchable City / Municipality -->
        <div class="relative" @click.outside="openCity = false">
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-200 mb-1">Store City / Municipality *</label>
            <div class="relative">
                <input
                    type="text"
                    x-model="searchCity"
                    @focus="if(cities.length > 0) openCity = true"
                    @input="if(cities.length > 0) openCity = true"
                    :disabled="cities.length === 0"
                    placeholder="Search or select City / Municipality..."
                    class="fi-input w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3.5 py-2.5 text-xs text-gray-900 dark:text-white shadow-xs focus:ring-2 focus:ring-amber-500/50 disabled:opacity-60 disabled:cursor-not-allowed"
                />
                <button type="button" @click="if(cities.length > 0) openCity = !openCity" class="absolute right-2.5 top-2.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs">
                    ▼
                </button>
            </div>

            <!-- Dropdown Options Overlay -->
            <div x-show="openCity" x-transition class="absolute left-0 right-0 top-full mt-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl z-[600] max-h-52 overflow-y-auto">
                <template x-for="c in filteredCities" :key="c.code">
                    <button
                        type="button"
                        @click="selectCityItem(c)"
                        class="w-full text-left px-3.5 py-2 text-xs text-gray-900 dark:text-gray-100 hover:bg-amber-500/15 hover:text-amber-600 dark:hover:text-amber-400 border-b border-gray-100 dark:border-gray-800 last:border-0"
                        x-text="c.name"
                    ></button>
                </template>
                <div x-show="filteredCities.length === 0" class="px-3.5 py-2.5 text-xs text-gray-400 italic">No city/municipality found.</div>
            </div>
        </div>

        <!-- 4. Searchable Barangay -->
        <div class="relative" @click.outside="openBarangay = false">
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-200 mb-1">Store Barangay *</label>
            <div class="relative">
                <input
                    type="text"
                    x-model="searchBarangay"
                    @focus="if(barangays.length > 0) openBarangay = true"
                    @input="if(barangays.length > 0) openBarangay = true"
                    :disabled="barangays.length === 0"
                    placeholder="Search or select Barangay..."
                    class="fi-input w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3.5 py-2.5 text-xs text-gray-900 dark:text-white shadow-xs focus:ring-2 focus:ring-amber-500/50 disabled:opacity-60 disabled:cursor-not-allowed"
                />
                <button type="button" @click="if(barangays.length > 0) openBarangay = !openBarangay" class="absolute right-2.5 top-2.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs">
                    ▼
                </button>
            </div>

            <!-- Dropdown Options Overlay -->
            <div x-show="openBarangay" x-transition class="absolute left-0 right-0 top-full mt-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl z-[600] max-h-52 overflow-y-auto">
                <template x-for="b in filteredBarangays" :key="b.code">
                    <button
                        type="button"
                        @click="selectBarangayItem(b)"
                        class="w-full text-left px-3.5 py-2 text-xs text-gray-900 dark:text-gray-100 hover:bg-amber-500/15 hover:text-amber-600 dark:hover:text-amber-400 border-b border-gray-100 dark:border-gray-800 last:border-0"
                        x-text="b.name"
                    ></button>
                </template>
                <div x-show="filteredBarangays.length === 0" class="px-3.5 py-2.5 text-xs text-gray-400 italic">No barangay found.</div>
            </div>
        </div>
    </div>

    <!-- 5. Street / Building / House # Detail Input -->
    <div>
        <label class="block text-xs font-bold text-gray-700 dark:text-gray-200 mb-1">Store Street Address / Building / House # *</label>
        <input
            x-model="street"
            @input="compileFullAddress()"
            type="text"
            placeholder="e.g. 123 Zapote Road, Phase 1"
            class="fi-input w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3.5 py-2.5 text-xs text-gray-900 dark:text-white shadow-xs"
        />
    </div>

    <!-- Compiled Full Address Display -->
    <div class="bg-amber-500/10 border border-amber-500/20 p-3 rounded-lg text-xs text-gray-800 dark:text-amber-300 flex items-start gap-2">
        <span class="text-sm shrink-0">📍</span>
        <div>
            <strong class="font-bold">Compiled Store Pickup Address:</strong>
            <p class="font-mono text-gray-900 dark:text-white mt-0.5" x-text="fullAddress || 'Not set'"></p>
        </div>
    </div>
</div>

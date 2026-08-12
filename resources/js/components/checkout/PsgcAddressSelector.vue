<template>
  <div class="space-y-4 bg-surface/60 dark:bg-[#1A120C]/60 p-4 rounded-2xl border border-brand-caramel/30">
    <div class="flex items-center justify-between">
      <h4 class="text-xs font-bold uppercase tracking-wider text-brand-choco dark:text-[#E2C08A] flex items-center gap-1.5">
        <span>🇵🇭</span> Philippine Standard Geographic Address (PSGC)
      </h4>
      <span v-if="loadingRegions || loadingProvinces || loadingCities || loadingBarangays" class="text-[11px] text-brand-caramel flex items-center gap-1">
        <span class="w-3 h-3 border-2 border-brand-choco border-t-transparent rounded-full animate-spin"></span>
        Loading locations...
      </span>
    </div>

    <!-- 4-Level Searchable Combobox Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <!-- 1. Searchable Region -->
      <div class="relative" v-click-outside="() => openRegion = false">
        <label class="block text-[11px] font-bold text-ink/70 dark:text-[#FBF3E7]/70 mb-1">
          Region <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
          <input
            type="text"
            v-model="searchRegion"
            @focus="openRegion = true"
            @input="openRegion = true"
            placeholder="Search or select Region..."
            class="w-full bg-white dark:bg-[#120B07] border border-brand-caramel/30 rounded-xl px-3.5 py-2 text-xs text-ink dark:text-[#FBF3E7] placeholder-warm-gray focus:outline-none focus:border-brand-choco focus:ring-1 focus:ring-brand-choco/30 transition-all"
          />
          <button type="button" @click="openRegion = !openRegion" class="absolute right-3 top-2.5 text-warm-gray text-xs">
            ▼
          </button>
        </div>

        <!-- Dropdown Menu -->
        <Transition
          enter-active-class="transition duration-100 ease-out"
          enter-from-class="transform scale-95 opacity-0"
          enter-to-class="transform scale-100 opacity-100"
          leave-active-class="transition duration-75 ease-in"
          leave-from-class="transform scale-100 opacity-100"
          leave-to-class="transform scale-95 opacity-0"
        >
          <div v-if="openRegion" class="absolute left-0 right-0 top-full mt-1 bg-white dark:bg-[#1E1510] border border-brand-caramel/30 rounded-xl shadow-xl z-50 max-h-52 overflow-y-auto">
            <button
              v-for="reg in filteredRegions"
              :key="reg.code"
              type="button"
              @click="selectRegionItem(reg)"
              class="w-full text-left px-3.5 py-2 text-xs text-ink dark:text-[#FBF3E7] hover:bg-brand-caramel/10 hover:text-brand-choco dark:hover:text-[#E2C08A] border-b border-brand-caramel/10 last:border-0"
            >
              <span class="font-bold">{{ reg.name }}</span>
              <span class="text-[10px] text-warm-gray ml-1">({{ reg.regionName }})</span>
            </button>
            <div v-if="filteredRegions.length === 0" class="px-3.5 py-2.5 text-xs text-warm-gray italic">No region found matching "{{ searchRegion }}"</div>
          </div>
        </Transition>
      </div>

      <!-- 2. Searchable Province -->
      <div class="relative" v-click-outside="() => openProvince = false">
        <label class="block text-[11px] font-bold text-ink/70 dark:text-[#FBF3E7]/70 mb-1">
          Province <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
          <input
            type="text"
            v-model="searchProvince"
            @focus="handleProvinceFocus"
            @input="handleProvinceFocus"
            :disabled="!selectedRegionCode || isNcr"
            :placeholder="isNcr ? 'Metro Manila (NCR)' : 'Search or select Province...'"
            class="w-full bg-white dark:bg-[#120B07] border border-brand-caramel/30 rounded-xl px-3.5 py-2 text-xs text-ink dark:text-[#FBF3E7] placeholder-warm-gray focus:outline-none focus:border-brand-choco focus:ring-1 focus:ring-brand-choco/30 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          />
          <button type="button" @click="handleProvinceToggle" class="absolute right-3 top-2.5 text-warm-gray text-xs">
            ▼
          </button>
        </div>

        <!-- Dropdown Menu -->
        <Transition
          enter-active-class="transition duration-100 ease-out"
          enter-from-class="transform scale-95 opacity-0"
          enter-to-class="transform scale-100 opacity-100"
          leave-active-class="transition duration-75 ease-in"
          leave-from-class="transform scale-100 opacity-100"
          leave-to-class="transform scale-95 opacity-0"
        >
          <div v-if="openProvince" class="absolute left-0 right-0 top-full mt-1 bg-white dark:bg-[#1E1510] border border-brand-caramel/30 rounded-xl shadow-xl z-50 max-h-52 overflow-y-auto">
            <button
              v-for="prov in filteredProvinces"
              :key="prov.code"
              type="button"
              @click="selectProvinceItem(prov)"
              class="w-full text-left px-3.5 py-2 text-xs text-ink dark:text-[#FBF3E7] hover:bg-brand-caramel/10 hover:text-brand-choco dark:hover:text-[#E2C08A] border-b border-brand-caramel/10 last:border-0"
            >
              {{ prov.name }}
            </button>
            <div v-if="filteredProvinces.length === 0" class="px-3.5 py-2.5 text-xs text-warm-gray italic">No province found matching "{{ searchProvince }}"</div>
          </div>
        </Transition>
      </div>

      <!-- 3. Searchable City / Municipality -->
      <div class="relative" v-click-outside="() => openCity = false">
        <label class="block text-[11px] font-bold text-ink/70 dark:text-[#FBF3E7]/70 mb-1">
          City / Municipality <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
          <input
            type="text"
            v-model="searchCity"
            @focus="handleCityFocus"
            @input="handleCityFocus"
            :disabled="cities.length === 0"
            placeholder="Search or select City / Municipality..."
            class="w-full bg-white dark:bg-[#120B07] border border-brand-caramel/30 rounded-xl px-3.5 py-2 text-xs text-ink dark:text-[#FBF3E7] placeholder-warm-gray focus:outline-none focus:border-brand-choco focus:ring-1 focus:ring-brand-choco/30 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          />
          <button type="button" @click="handleCityToggle" class="absolute right-3 top-2.5 text-warm-gray text-xs">
            ▼
          </button>
        </div>

        <!-- Dropdown Menu -->
        <Transition
          enter-active-class="transition duration-100 ease-out"
          enter-from-class="transform scale-95 opacity-0"
          enter-to-class="transform scale-100 opacity-100"
          leave-active-class="transition duration-75 ease-in"
          leave-from-class="transform scale-100 opacity-100"
          leave-to-class="transform scale-95 opacity-0"
        >
          <div v-if="openCity" class="absolute left-0 right-0 top-full mt-1 bg-white dark:bg-[#1E1510] border border-brand-caramel/30 rounded-xl shadow-xl z-50 max-h-52 overflow-y-auto">
            <button
              v-for="c in filteredCities"
              :key="c.code"
              type="button"
              @click="selectCityItem(c)"
              class="w-full text-left px-3.5 py-2 text-xs text-ink dark:text-[#FBF3E7] hover:bg-brand-caramel/10 hover:text-brand-choco dark:hover:text-[#E2C08A] border-b border-brand-caramel/10 last:border-0"
            >
              {{ c.name }}
            </button>
            <div v-if="filteredCities.length === 0" class="px-3.5 py-2.5 text-xs text-warm-gray italic">No city found matching "{{ searchCity }}"</div>
          </div>
        </Transition>
      </div>

      <!-- 4. Searchable Barangay -->
      <div class="relative" v-click-outside="() => openBarangay = false">
        <label class="block text-[11px] font-bold text-ink/70 dark:text-[#FBF3E7]/70 mb-1">
          Barangay <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
          <input
            type="text"
            v-model="searchBarangay"
            @focus="handleBarangayFocus"
            @input="handleBarangayFocus"
            :disabled="barangays.length === 0"
            placeholder="Search or select Barangay..."
            class="w-full bg-white dark:bg-[#120B07] border border-brand-caramel/30 rounded-xl px-3.5 py-2 text-xs text-ink dark:text-[#FBF3E7] placeholder-warm-gray focus:outline-none focus:border-brand-choco focus:ring-1 focus:ring-brand-choco/30 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          />
          <button type="button" @click="handleBarangayToggle" class="absolute right-3 top-2.5 text-warm-gray text-xs">
            ▼
          </button>
        </div>

        <!-- Dropdown Menu -->
        <Transition
          enter-active-class="transition duration-100 ease-out"
          enter-from-class="transform scale-95 opacity-0"
          enter-to-class="transform scale-100 opacity-100"
          leave-active-class="transition duration-75 ease-in"
          leave-from-class="transform scale-100 opacity-100"
          leave-to-class="transform scale-95 opacity-0"
        >
          <div v-if="openBarangay" class="absolute left-0 right-0 top-full mt-1 bg-white dark:bg-[#1E1510] border border-brand-caramel/30 rounded-xl shadow-xl z-50 max-h-52 overflow-y-auto">
            <button
              v-for="b in filteredBarangays"
              :key="b.code"
              type="button"
              @click="selectBarangayItem(b)"
              class="w-full text-left px-3.5 py-2 text-xs text-ink dark:text-[#FBF3E7] hover:bg-brand-caramel/10 hover:text-brand-choco dark:hover:text-[#E2C08A] border-b border-brand-caramel/10 last:border-0"
            >
              {{ b.name }}
            </button>
            <div v-if="filteredBarangays.length === 0" class="px-3.5 py-2.5 text-xs text-warm-gray italic">No barangay found matching "{{ searchBarangay }}"</div>
          </div>
        </Transition>
      </div>
    </div>

    <!-- 5. Street / Building / House # Detail Input -->
    <div>
      <label class="block text-[11px] font-bold text-ink/70 dark:text-[#FBF3E7]/70 mb-1">
        Street Address / House # / Building / Landmark <span class="text-rose-500">*</span>
      </label>
      <input
        v-model="streetAddressInput"
        @input="emitFullAddress"
        type="text"
        placeholder="e.g. Block 5 Lot 12 Phase 2, Molino Blvd, near Shell station"
        class="w-full bg-white dark:bg-[#120B07] border border-brand-caramel/30 rounded-xl px-3.5 py-2 text-xs text-ink dark:text-[#FBF3E7] placeholder-warm-gray focus:outline-none focus:border-brand-choco focus:ring-1 focus:ring-brand-choco/30 transition-all"
      />
    </div>

    <!-- Assembled Address Preview Bar -->
    <div v-if="compiledAddress" class="bg-amber-500/10 border border-amber-500/20 p-2.5 rounded-xl text-[11px] text-brand-choco dark:text-[#E2C08A] flex items-start gap-2">
      <span class="text-sm shrink-0">📍</span>
      <div>
        <strong class="font-bold">Full Structured Delivery Address:</strong>
        <p class="text-ink dark:text-[#FBF3E7] mt-0.5">{{ compiledAddress }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'

const props = defineProps({
  region: { type: String, default: '' },
  province: { type: String, default: '' },
  city: { type: String, default: '' },
  barangay: { type: String, default: '' },
  streetAddress: { type: String, default: '' },
  address: { type: String, default: '' },
})

const emit = defineEmits([
  'update:region',
  'update:province',
  'update:city',
  'update:barangay',
  'update:streetAddress',
  'update:address',
  'address-changed',
])

// Custom Directive for Click Outside
const vClickOutside = {
  mounted(el, binding) {
    el.clickOutsideEvent = (event) => {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value(event)
      }
    }
    document.addEventListener('click', el.clickOutsideEvent)
  },
  unmounted(el) {
    document.removeEventListener('click', el.clickOutsideEvent)
  },
}

const regions = ref([])
const provinces = ref([])
const cities = ref([])
const barangays = ref([])

const selectedRegionCode = ref('')
const selectedProvinceCode = ref('')
const selectedCityCode = ref('')
const selectedBarangayCode = ref('')
const streetAddressInput = ref(props.streetAddress || '')

const selectedRegionName = ref(props.region || '')
const selectedProvinceName = ref(props.province || '')
const selectedCityName = ref(props.city || '')
const selectedBarangayName = ref(props.barangay || '')

const searchRegion = ref('')
const searchProvince = ref('')
const searchCity = ref('')
const searchBarangay = ref('')

const openRegion = ref(false)
const openProvince = ref(false)
const openCity = ref(false)
const openBarangay = ref(false)

const loadingRegions = ref(false)
const loadingProvinces = ref(false)
const loadingCities = ref(false)
const loadingBarangays = ref(false)

function handleProvinceFocus() {
  if (!isNcr.value && selectedRegionCode.value) openProvince.value = true
}
function handleProvinceToggle() {
  if (!isNcr.value && selectedRegionCode.value) openProvince.value = !openProvince.value
}
function handleCityFocus() {
  if (cities.value.length > 0) openCity.value = true
}
function handleCityToggle() {
  if (cities.value.length > 0) openCity.value = !openCity.value
}
function handleBarangayFocus() {
  if (barangays.value.length > 0) openBarangay.value = true
}
function handleBarangayToggle() {
  if (barangays.value.length > 0) openBarangay.value = !openBarangay.value
}

const isNcr = computed(() => {
  const reg = regions.value.find(r => r.code === selectedRegionCode.value)
  return reg ? (reg.code === '130000000' || reg.regionName === 'NCR') : false
})

const filteredRegions = computed(() => {
  const q = searchRegion.value.toLowerCase().trim()
  if (!q) return regions.value
  return regions.value.filter(r => r.name.toLowerCase().includes(q) || (r.regionName && r.regionName.toLowerCase().includes(q)))
})

const filteredProvinces = computed(() => {
  const q = searchProvince.value.toLowerCase().trim()
  if (!q) return provinces.value
  return provinces.value.filter(p => p.name.toLowerCase().includes(q))
})

const filteredCities = computed(() => {
  const q = searchCity.value.toLowerCase().trim()
  if (!q) return cities.value
  return cities.value.filter(c => c.name.toLowerCase().includes(q))
})

const filteredBarangays = computed(() => {
  const q = searchBarangay.value.toLowerCase().trim()
  if (!q) return barangays.value
  return barangays.value.filter(b => b.name.toLowerCase().includes(q))
})

const compiledAddress = computed(() => {
  const parts = [
    streetAddressInput.value,
    selectedBarangayName.value,
    selectedCityName.value,
    selectedProvinceName.value,
    selectedRegionName.value,
  ].filter(Boolean)

  return parts.join(', ')
})

async function fetchPsgc(url, cacheKey) {
  const cached = localStorage.getItem(`psgc_${cacheKey}`)
  if (cached) {
    try { return JSON.parse(cached) } catch {}
  }
  try {
    const res = await fetch(url)
    const data = await res.json()
    if (Array.isArray(data)) {
      localStorage.setItem(`psgc_${cacheKey}`, JSON.stringify(data))
    }
    return data
  } catch (err) {
    console.warn('[PSGC] Error fetching', url, err)
    return []
  }
}

async function loadRegions() {
  loadingRegions.value = true
  regions.value = await fetchPsgc('https://psgc.gitlab.io/api/regions.json', 'regions')
  loadingRegions.value = false

  // Sync initial values if provided
  if (props.region) {
    const reg = regions.value.find(r => r.name === props.region || r.regionName === props.region)
    if (reg) {
      selectedRegionCode.value = reg.code
      selectedRegionName.value = reg.name
      searchRegion.value = reg.name
      await onRegionChange(true)
    }
  } else {
    const defaultReg = regions.value.find(r => r.code === '040000000' || r.regionName === 'Region IV-A') || regions.value[0]
    if (defaultReg) {
      selectedRegionCode.value = defaultReg.code
      selectedRegionName.value = defaultReg.name
      searchRegion.value = defaultReg.name
      await onRegionChange(true)
    }
  }

  if (props.province && provinces.value.length > 0) {
    const prov = provinces.value.find(p => p.name === props.province)
    if (prov) {
      selectedProvinceCode.value = prov.code
      selectedProvinceName.value = prov.name
      searchProvince.value = prov.name
      await onProvinceChange(true)
    }
  }

  if (props.city && cities.value.length > 0) {
    const c = cities.value.find(ci => ci.name === props.city)
    if (c) {
      selectedCityCode.value = c.code
      selectedCityName.value = c.name
      searchCity.value = c.name
      await onCityChange(true)
    }
  }

  if (props.barangay && barangays.value.length > 0) {
    const b = barangays.value.find(br => br.name === props.barangay)
    if (b) {
      selectedBarangayCode.value = b.code
      selectedBarangayName.value = b.name
      searchBarangay.value = b.name
    }
  }
}

async function selectRegionItem(reg) {
  selectedRegionCode.value = reg.code
  selectedRegionName.value = reg.name
  searchRegion.value = reg.name
  openRegion.value = false
  await onRegionChange(false)
}

async function selectProvinceItem(prov) {
  selectedProvinceCode.value = prov.code
  selectedProvinceName.value = prov.name
  searchProvince.value = prov.name
  openProvince.value = false
  await onProvinceChange(false)
}

async function selectCityItem(c) {
  selectedCityCode.value = c.code
  selectedCityName.value = c.name
  searchCity.value = c.name
  openCity.value = false
  await onCityChange(false)
}

function selectBarangayItem(b) {
  selectedBarangayCode.value = b.code
  selectedBarangayName.value = b.name
  searchBarangay.value = b.name
  openBarangay.value = false
  onBarangayChange()
}

async function onRegionChange(skipReset = false) {
  const reg = regions.value.find(r => r.code === selectedRegionCode.value)
  selectedRegionName.value = reg ? reg.name : ''
  if (!skipReset) {
    selectedProvinceCode.value = ''
    selectedProvinceName.value = ''
    searchProvince.value = ''
    selectedCityCode.value = ''
    selectedCityName.value = ''
    searchCity.value = ''
    selectedBarangayCode.value = ''
    selectedBarangayName.value = ''
    searchBarangay.value = ''
  }
  provinces.value = []
  cities.value = []
  barangays.value = []

  if (!selectedRegionCode.value) return

  if (isNcr.value) {
    selectedProvinceName.value = 'Metro Manila'
    searchProvince.value = 'Metro Manila'
    loadingCities.value = true
    cities.value = await fetchPsgc(`https://psgc.gitlab.io/api/regions/${selectedRegionCode.value}/cities-municipalities.json`, `cities_ncr`)
    loadingCities.value = false
    emitFullAddress()
  } else {
    loadingProvinces.value = true
    provinces.value = await fetchPsgc(`https://psgc.gitlab.io/api/regions/${selectedRegionCode.value}/provinces.json`, `prov_${selectedRegionCode.value}`)
    loadingProvinces.value = false

    const cavite = provinces.value.find(p => p.name.toLowerCase().includes('cavite'))
    if (cavite && !props.province && !skipReset) {
      selectedProvinceCode.value = cavite.code
      selectedProvinceName.value = cavite.name
      searchProvince.value = cavite.name
      await onProvinceChange(false)
    }
    emitFullAddress()
  }
}

async function onProvinceChange(skipReset = false) {
  const prov = provinces.value.find(p => p.code === selectedProvinceCode.value)
  selectedProvinceName.value = prov ? prov.name : ''
  if (!skipReset) {
    selectedCityCode.value = ''
    selectedCityName.value = ''
    searchCity.value = ''
    selectedBarangayCode.value = ''
    selectedBarangayName.value = ''
    searchBarangay.value = ''
  }
  cities.value = []
  barangays.value = []

  if (!selectedProvinceCode.value) return

  loadingCities.value = true
  cities.value = await fetchPsgc(`https://psgc.gitlab.io/api/provinces/${selectedProvinceCode.value}/cities-municipalities.json`, `cities_prov_${selectedProvinceCode.value}`)
  loadingCities.value = false

  const bacoor = cities.value.find(c => c.name.toLowerCase().includes('bacoor'))
  if (bacoor && !props.city && !skipReset) {
    selectedCityCode.value = bacoor.code
    selectedCityName.value = bacoor.name
    searchCity.value = bacoor.name
    await onCityChange(false)
  }
  emitFullAddress()
}

async function onCityChange(skipReset = false) {
  const cityObj = cities.value.find(c => c.code === selectedCityCode.value)
  selectedCityName.value = cityObj ? cityObj.name : ''
  if (!skipReset) {
    selectedBarangayCode.value = ''
    selectedBarangayName.value = ''
    searchBarangay.value = ''
  }
  barangays.value = []

  if (!selectedCityCode.value) return

  loadingBarangays.value = true
  barangays.value = await fetchPsgc(`https://psgc.gitlab.io/api/cities-municipalities/${selectedCityCode.value}/barangays.json`, `brgy_city_${selectedCityCode.value}`)
  loadingBarangays.value = false
  emitFullAddress()
}

function onBarangayChange() {
  const brgyObj = barangays.value.find(b => b.code === selectedBarangayCode.value)
  selectedBarangayName.value = brgyObj ? brgyObj.name : ''
  emitFullAddress()
}

function emitFullAddress() {
  const fullAddr = compiledAddress.value
  emit('update:region', selectedRegionName.value)
  emit('update:province', selectedProvinceName.value)
  emit('update:city', selectedCityName.value)
  emit('update:barangay', selectedBarangayName.value)
  emit('update:streetAddress', streetAddressInput.value)
  emit('update:address', fullAddr)

  emit('address-changed', {
    region: selectedRegionName.value,
    province: selectedProvinceName.value,
    city: selectedCityName.value,
    barangay: selectedBarangayName.value,
    streetAddress: streetAddressInput.value,
    fullAddress: fullAddr,
  })
}

async function syncFromIncomingProps() {
  if (regions.value.length === 0) return

  if (props.region) {
    const reg = regions.value.find(r => 
      r.name.toLowerCase().includes(props.region.toLowerCase()) || 
      (r.regionName && r.regionName.toLowerCase().includes(props.region.toLowerCase()))
    )
    if (reg && reg.code !== selectedRegionCode.value) {
      selectedRegionCode.value = reg.code
      selectedRegionName.value = reg.name
      searchRegion.value = reg.name
      await onRegionChange(true)
    }
  }

  if (props.province && provinces.value.length > 0) {
    const prov = provinces.value.find(p => p.name.toLowerCase().includes(props.province.toLowerCase()))
    if (prov && prov.code !== selectedProvinceCode.value) {
      selectedProvinceCode.value = prov.code
      selectedProvinceName.value = prov.name
      searchProvince.value = prov.name
      await onProvinceChange(true)
    }
  }

  if (props.city && cities.value.length > 0) {
    const c = cities.value.find(ci => ci.name.toLowerCase().includes(props.city.toLowerCase()))
    if (c && c.code !== selectedCityCode.value) {
      selectedCityCode.value = c.code
      selectedCityName.value = c.name
      searchCity.value = c.name
      await onCityChange(true)
    }
  }

  if (props.barangay && barangays.value.length > 0) {
    const b = barangays.value.find(br => br.name.toLowerCase().includes(props.barangay.toLowerCase()))
    if (b) {
      selectedBarangayCode.value = b.code
      selectedBarangayName.value = b.name
      searchBarangay.value = b.name
    }
  }
}

watch(() => props.streetAddress, (newVal) => {
  if (newVal !== undefined && newVal !== streetAddressInput.value) {
    streetAddressInput.value = newVal
  }
})

watch([() => props.region, () => props.province, () => props.city, () => props.barangay], () => {
  syncFromIncomingProps()
})

onMounted(async () => {
  await loadRegions()
  await syncFromIncomingProps()
})
</script>

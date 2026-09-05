<template>
  <div class="space-y-3">
    <!-- Map Toggle Button -->
    <div class="flex items-center justify-between">
      <button
        type="button"
        @click="toggleMap"
        v-tooltip="'Click to open interactive map and drag pin to your exact delivery location'"
        class="inline-flex items-center gap-2 text-xs font-bold text-brand-choco bg-surface hover:bg-brand-tan/30 px-3.5 py-2 rounded-xl border border-brand-caramel/30 transition-all shadow-xs"
      >
        <span>{{ showMap ? 'Hide Map Picker' : 'Pinpoint Location on Map' }}</span>
        <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="showMap ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>

      <span v-if="selectedCoords" class="text-[11px] text-emerald-600 dark:text-emerald-400 font-extrabold flex items-center gap-1 bg-emerald-50 dark:bg-emerald-950/60 px-2.5 py-1 rounded-lg border border-emerald-500/20">
        GPS: {{ selectedCoords.lat.toFixed(4) }}, {{ selectedCoords.lng.toFixed(4) }}
      </span>
    </div>

    <!-- Map & Search Box Panel -->
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0 max-h-0 overflow-hidden"
      enter-to-class="opacity-100 max-h-[550px] overflow-hidden"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100 max-h-[550px] overflow-hidden"
      leave-to-class="opacity-0 max-h-0 overflow-hidden"
    >
      <div v-show="showMap" class="space-y-2 bg-surface/80 dark:bg-[#1A120C]/80 p-3 rounded-2xl border border-brand-caramel/30">

        <!-- Search Bar with Autocomplete Suggestions -->
        <div class="relative">
          <div class="flex gap-2">
            <div class="relative flex-1">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search street, barangay, or landmark in Philippines..."
                class="w-full bg-white dark:bg-[#120B07] border border-brand-caramel/30 rounded-xl px-3.5 py-2 text-xs text-ink dark:text-[#FBF3E7] placeholder-warm-gray focus:outline-none focus:border-brand-choco focus:ring-1 focus:ring-brand-choco/30"
                @input="handleSearchInput"
                @keydown.enter.prevent="performSearch"
              />
              <span v-if="searching" class="absolute right-3 top-2.5 w-3.5 h-3.5 border-2 border-brand-choco border-t-transparent rounded-full animate-spin"></span>
            </div>

            <button
              type="button"
              @click="performSearch"
              v-tooltip="'Search location on map'"
              class="bg-brand-choco text-surface px-3.5 py-2 rounded-xl text-xs font-bold hover:bg-choco-600 transition-colors shrink-0 shadow-xs"
            >
              Search
            </button>
          </div>

          <!-- Autocomplete Dropdown List -->
          <div
            v-if="suggestions.length > 0"
            class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-[#1E1510] rounded-xl shadow-xl border border-brand-caramel/20 overflow-hidden z-[500] max-h-48 overflow-y-auto"
          >
            <button
              v-for="(sug, idx) in suggestions"
              :key="idx"
              type="button"
              @click="selectSuggestion(sug)"
              class="w-full text-left px-3.5 py-2 text-xs text-ink dark:text-[#FBF3E7] hover:bg-brand-tan/20 dark:hover:bg-[#2A1D16] hover:text-brand-choco border-b border-brand-caramel/10 last:border-0 truncate"
            >
              {{ sug.name }} <span class="text-[10px] text-warm-gray">({{ sug.subtitle }})</span>
            </button>
          </div>
        </div>

        <!-- Interactive Map Canvas Container -->
        <div class="relative rounded-xl overflow-hidden border border-brand-caramel/30 shadow-inner h-64 bg-amber-50 dark:bg-[#120B07]">
          <div ref="mapContainer" class="w-full h-full min-h-[256px]"></div>

          <!-- Instruction Badge Overlay -->
          <div class="absolute bottom-2 left-2 right-2 bg-white/95 dark:bg-[#1A120C]/95 backdrop-blur-xs px-3 py-1.5 rounded-lg border border-brand-caramel/20 text-[11px] text-brand-choco dark:text-[#E2C08A] flex items-center justify-between z-[400] shadow-sm">
            <span><strong>Tip:</strong> Drag the map marker or tap anywhere on the map to set your exact delivery dropoff location.</span>
            <button type="button" @click="centerOnStore" class="text-[10px] font-bold text-brand-caramel hover:underline">Recenter</button>
          </div>
        </div>

      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted, nextTick } from 'vue'

const props = defineProps({
  address: { type: String, default: '' },
  city: { type: String, default: '' },
  storeLat: { type: Number, default: 14.4597 },
  storeLng: { type: Number, default: 120.9640 },
})

const emit = defineEmits(['update:address', 'update:city', 'location-selected'])

const showMap = ref(false)
const mapContainer = ref(null)
const searchQuery = ref('')
const searching = ref(false)
const suggestions = ref([])
const selectedCoords = ref(null)

let map = null
let marker = null
let storeMarker = null
let routeLine = null
let searchTimeout = null
let resizeObserver = null

function toggleMap() {
  showMap.value = !showMap.value
}

function loadLeaflet() {
  return new Promise((resolve, reject) => {
    if (window.L) return resolve(window.L)

    if (!document.getElementById('leaflet-css-free')) {
      const link = document.createElement('link')
      link.id = 'leaflet-css-free'
      link.rel = 'stylesheet'
      link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'
      document.head.appendChild(link)
    }

    if (!document.getElementById('leaflet-js-free')) {
      const script = document.createElement('script')
      script.id = 'leaflet-js-free'
      script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
      script.onload = () => resolve(window.L)
      script.onerror = reject
      document.body.appendChild(script)
    } else {
      const check = setInterval(() => {
        if (window.L) {
          clearInterval(check)
          resolve(window.L)
        }
      }, 100)
    }
  })
}

async function initMap() {
  const L = await loadLeaflet()
  if (!mapContainer.value) return

  if (map) {
    try { map.remove() } catch {}
    map = null
  }

  const initialLat = selectedCoords.value?.lat || props.storeLat
  const initialLng = selectedCoords.value?.lng || props.storeLng

  map = L.map(mapContainer.value, {
    center: [initialLat, initialLng],
    zoom: 14,
    zoomControl: true,
  })

  // Fast, Free CARTO Voyager Tiles (High-DPI, global CDN)
  const cartoLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
    maxZoom: 19,
    subdomains: 'abcd',
    attribution: '© OpenStreetMap © CARTO',
  })

  // Esri World Street Map Fallback Tile Layer
  const esriLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
    maxZoom: 19,
    attribution: '© Esri',
  })

  cartoLayer.on('tileerror', () => {
    map.removeLayer(cartoLayer)
    esriLayer.addTo(map)
  })

  cartoLayer.addTo(map)

  // Custom Bakery Store Icon
  const storeIcon = L.divIcon({
    html: `<div style="background:#5C3A22; color:#FBF3E7; width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:20px; border:2px solid #D9A876; box-shadow:0 4px 8px rgba(0,0,0,0.35);">Store</div>`,
    className: '',
    iconSize: [34, 34],
    iconAnchor: [17, 17],
  })

  // Customer Dropoff Delivery Pin Icon
  const deliveryIcon = L.divIcon({
    html: `<div style="background:#C08E5D; color:white; width:36px; height:36px; border-radius:50% 50% 50% 0; transform:rotate(-45deg); display:flex; align-items:center; justify-content:center; font-size:18px; border:2px solid #5C3A22; box-shadow:0 4px 10px rgba(0,0,0,0.4);"><span style="transform:rotate(45deg); display:block;">Pin</span></div>`,
    className: '',
    iconSize: [36, 36],
    iconAnchor: [18, 36],
  })

  // Store Marker
  storeMarker = L.marker([props.storeLat, props.storeLng], { icon: storeIcon })
    .addTo(map)
    .bindPopup('<b>ABCDips Store Pickup Location</b>')

  // Draggable Delivery Marker
  marker = L.marker([initialLat, initialLng], {
    draggable: true,
    icon: deliveryIcon,
  }).addTo(map)

  // Drag End Event -> Reverse Geocode location
  marker.on('dragend', async () => {
    const pos = marker.getLatLng()
    selectedCoords.value = { lat: pos.lat, lng: pos.lng }
    drawRoute()
    await reverseGeocode(pos.lat, pos.lng)
  })

  // Map Click Event -> Move marker to clicked spot
  map.on('click', async (e) => {
    const { lat, lng } = e.latlng
    marker.setLatLng([lat, lng])
    selectedCoords.value = { lat, lng }
    drawRoute()
    await reverseGeocode(lat, lng)
  })

  // Observe element resizes so the map never renders blank or cut-off
  if ('ResizeObserver' in window && mapContainer.value) {
    resizeObserver = new ResizeObserver(() => {
      if (map) map.invalidateSize()
    })
    resizeObserver.observe(mapContainer.value)
  }

  drawRoute()
}

function drawRoute() {
  if (!map || !selectedCoords.value || !window.L) return
  if (routeLine) map.removeLayer(routeLine)

  const points = [
    [props.storeLat, props.storeLng],
    [selectedCoords.value.lat, selectedCoords.value.lng],
  ]

  routeLine = window.L.polyline(points, {
    color: '#C08E5D',
    weight: 3,
    dashArray: '6, 8',
    opacity: 0.85,
  }).addTo(map)
}

function centerOnStore() {
  if (map) {
    map.setView([props.storeLat, props.storeLng], 14)
  }
}

// Fast Free Reverse Geocode (Photon API with Nominatim Fallback)
async function reverseGeocode(lat, lng) {
  try {
    const res = await fetch(`https://photon.komoot.io/reverse?lat=${lat}&lon=${lng}`)
    const data = await res.json()
    if (data && data.features && data.features.length > 0) {
      const p = data.features[0].properties
      const name = p.name || p.street || ''
      const street = [p.housenumber, p.street || name].filter(Boolean).join(' ') || name || ''
      const barangay = p.district || p.suburb || p.quarter || p.neighbourhood || ''
      const city = p.city || p.municipality || p.town || p.county || ''
      const province = p.state || p.county || ''
      const region = p.state || ''

      const fullAddr = [street, barangay, city, province].filter(Boolean).join(', ')

      if (fullAddr) {
        emit('update:address', fullAddr)
        emit('update:city', city)
        emit('location-selected', {
          lat,
          lng,
          address: fullAddr,
          city,
          province,
          region,
          barangay,
          streetAddress: street,
        })
        return
      }
    }
  } catch {}

  // Fallback to Nominatim if Photon fails
  try {
    const res = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&countrycodes=ph`, {
      headers: { 'Accept-Language': 'en' }
    })
    const data = await res.json()
    if (data && data.display_name && data.address) {
      const a = data.address
      const street = [a.house_number, a.road].filter(Boolean).join(' ') || ''
      const barangay = a.quarter || a.suburb || a.village || a.neighbourhood || ''
      const city = a.city || a.town || a.municipality || a.county || ''
      const province = a.state || a.province || ''
      const region = a.region || a.state || ''
      const fullAddr = data.display_name

      emit('update:address', fullAddr)
      emit('update:city', city)
      emit('location-selected', {
        lat,
        lng,
        address: fullAddr,
        city,
        province,
        region,
        barangay,
        streetAddress: street,
      })
    }
  } catch (err) {
    console.warn('Reverse geocode fallback error', err)
  }
}

// Search Input Handler (Photon API Fast Autocomplete)
function handleSearchInput() {
  if (!searchQuery.value.trim() || searchQuery.value.length < 2) {
    suggestions.value = []
    return
  }

  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(async () => {
    searching.value = true
    try {
      const res = await fetch(`https://photon.komoot.io/api/?q=${encodeURIComponent(searchQuery.value + ' Philippines')}&limit=5&bbox=119.5,13.5,122.5,15.5`)
      const data = await res.json()
      if (data && data.features) {
        suggestions.value = data.features.map(f => {
          const p = f.properties
          const name = p.name || p.street || p.district || 'Location'
          const subtitle = [p.street, p.district, p.city, p.state].filter(Boolean).join(', ')
          return {
            name,
            subtitle,
            full_address: [name, subtitle].filter(Boolean).join(', '),
            lat: f.geometry.coordinates[1],
            lng: f.geometry.coordinates[0],
            city: p.city || p.county || p.state || 'Cavite',
          }
        })
      } else {
        suggestions.value = []
      }
    } catch {
      suggestions.value = []
    } finally {
      searching.value = false
    }
  }, 300)
}

async function performSearch() {
  if (suggestions.value.length > 0) {
    selectSuggestion(suggestions.value[0])
  } else if (searchQuery.value.trim()) {
    handleSearchInput()
  }
}

function selectSuggestion(sug) {
  const lat = parseFloat(sug.lat)
  const lng = parseFloat(sug.lng)

  selectedCoords.value = { lat, lng }
  suggestions.value = []
  searchQuery.value = sug.full_address

  if (map && marker) {
    map.setView([lat, lng], 16)
    marker.setLatLng([lat, lng])
    drawRoute()
  }

  emit('update:address', sug.full_address)
  emit('update:city', sug.city)
  emit('location-selected', { lat, lng, address: sug.full_address, city: sug.city })
}

let addressGeocodeTimeout = null
watch(() => props.address, (newAddr) => {
  if (!newAddr || newAddr.length < 5) return
  searchQuery.value = newAddr

  clearTimeout(addressGeocodeTimeout)
  addressGeocodeTimeout = setTimeout(async () => {
    try {
      const res = await fetch(`https://photon.komoot.io/api/?q=${encodeURIComponent(newAddr + ' Philippines')}&limit=1&bbox=119.5,13.5,122.5,15.5`)
      const data = await res.json()
      if (data && data.features && data.features.length > 0) {
        const coords = data.features[0].geometry.coordinates
        const lng = coords[0]
        const lat = coords[1]

        selectedCoords.value = { lat, lng }
        if (map && marker) {
          map.setView([lat, lng], 16)
          marker.setLatLng([lat, lng])
          drawRoute()
        }
      }
    } catch (e) {
      console.warn('Auto pinpoint address geocode failed', e)
    }
  }, 500)
}, { immediate: true })

watch(showMap, async (val) => {
  if (val) {
    await nextTick()
    await initMap()
    setTimeout(() => {
      if (map) map.invalidateSize()
    }, 150)
  }
})

onMounted(() => {
  if (props.address) searchQuery.value = props.address
})

onUnmounted(() => {
  if (resizeObserver) resizeObserver.disconnect()
  if (map) map.remove()
})
</script>


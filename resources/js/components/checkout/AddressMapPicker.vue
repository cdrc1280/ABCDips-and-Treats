<template>
  <div class="space-y-3">
    <!-- Map Toggle Button -->
    <div class="flex items-center justify-between">
      <button
        type="button"
        @click="showMap = !showMap"
        v-tooltip="'Click to open interactive map and drag pin to your exact delivery location'"
        class="inline-flex items-center gap-2 text-xs font-bold text-brand-choco bg-surface hover:bg-brand-tan/30 px-3.5 py-2 rounded-xl border border-brand-caramel/30 transition-all"
      >
        <span>📍 {{ showMap ? 'Hide Map Picker' : 'Pinpoint Location on Map' }}</span>
        <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="showMap ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>

      <span v-if="selectedCoords" class="text-[11px] text-success font-bold flex items-center gap-1">
        ✓ Pin Location: {{ selectedCoords.lat.toFixed(4) }}, {{ selectedCoords.lng.toFixed(4) }}
      </span>
    </div>

    <!-- Map & Search Box Panel -->
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0 max-h-0 overflow-hidden"
      enter-to-class="opacity-100 max-h-[500px] overflow-hidden"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100 max-h-[500px] overflow-hidden"
      leave-to-class="opacity-0 max-h-0 overflow-hidden"
    >
      <div v-show="showMap" class="space-y-2 bg-surface/60 p-3 rounded-2xl border border-brand-caramel/30">

        <!-- Search Bar with Autocomplete Suggestions -->
        <div class="relative">
          <div class="flex gap-2">
            <div class="relative flex-1">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search street, barangay, or landmark in PH..."
                class="w-full bg-white border border-brand-caramel/30 rounded-xl px-3.5 py-2 text-xs text-ink placeholder-warm-gray focus:outline-none focus:border-brand-choco focus:ring-1 focus:ring-brand-choco/30"
                @input="handleSearchInput"
                @keydown.enter.prevent="performSearch"
              />
              <span v-if="searching" class="absolute right-3 top-2.5 w-3.5 h-3.5 border-2 border-brand-choco border-t-transparent rounded-full animate-spin"></span>
            </div>

            <button
              type="button"
              @click="performSearch"
              v-tooltip="'Search address on map'"
              class="bg-brand-choco text-surface px-3.5 py-2 rounded-xl text-xs font-bold hover:bg-choco-600 transition-colors shrink-0"
            >
              Search
            </button>
          </div>

          <!-- Autocomplete Dropdown List -->
          <div
            v-if="suggestions.length > 0"
            class="absolute top-full left-0 right-0 mt-1 bg-white rounded-xl shadow-xl border border-brand-caramel/20 overflow-hidden z-50 max-h-48 overflow-y-auto"
          >
            <button
              v-for="(sug, idx) in suggestions"
              :key="idx"
              type="button"
              @click="selectSuggestion(sug)"
              class="w-full text-left px-3.5 py-2 text-xs text-ink hover:bg-surface hover:text-brand-choco border-b border-brand-caramel/10 last:border-0 truncate"
            >
              📍 {{ sug.display_name }}
            </button>
          </div>
        </div>

        <!-- Leaflet Map Container -->
        <div class="relative rounded-xl overflow-hidden border border-brand-caramel/30 shadow-inner h-64 bg-amber-50">
          <div ref="mapContainer" class="w-full h-full"></div>

          <!-- Instruction Badge Overlay -->
          <div class="absolute bottom-2 left-2 right-2 bg-white/90 backdrop-blur-xs px-3 py-1.5 rounded-lg border border-brand-caramel/20 text-[11px] text-brand-choco flex items-center justify-between z-400 shadow-sm">
            <span>💡 <strong>Tip:</strong> Drag the 📍 pin or tap anywhere on the map to set your exact delivery dropoff location.</span>
            <button type="button" @click="centerOnStore" class="text-[10px] font-bold text-brand-caramel underline hover:text-brand-choco">Recenter</button>
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

// Dynamically load Leaflet library if not present
function loadLeaflet() {
  return new Promise((resolve, reject) => {
    if (window.L) return resolve(window.L)

    // Load CSS
    if (!document.getElementById('leaflet-css')) {
      const link = document.createElement('link')
      link.id = 'leaflet-css'
      link.rel = 'stylesheet'
      link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'
      document.head.appendChild(link)
    }

    // Load JS
    if (!document.getElementById('leaflet-js')) {
      const script = document.createElement('script')
      script.id = 'leaflet-js'
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
  if (!mapContainer.value || map) return

  const initialLat = selectedCoords.value?.lat || props.storeLat
  const initialLng = selectedCoords.value?.lng || props.storeLng

  map = L.map(mapContainer.value, {
    center: [initialLat, initialLng],
    zoom: 14,
    zoomControl: true,
  })

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap contributors',
  }).addTo(map)

  // Custom Bakery Store Pin Icon
  const storeIcon = L.divIcon({
    html: `<div style="background:#5C3A22; color:#FBF3E7; width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-center; font-size:18px; border:2px solid #D9A876; shadow:0 4px 6px rgba(0,0,0,0.3);">🏪</div>`,
    className: '',
    iconSize: [32, 32],
    iconAnchor: [16, 16],
  })

  // Customer Dropoff Delivery Pin Icon
  const deliveryIcon = L.divIcon({
    html: `<div style="background:#C08E5D; color:#white; width:36px; height:36px; border-radius:50% 50% 50% 0; transform:rotate(-45deg); display:flex; align-items:center; justify-center; font-size:18px; border:2px solid #5C3A22; box-shadow:0 4px 10px rgba(0,0,0,0.4);"><span style="transform:rotate(45deg); display:block;">📍</span></div>`,
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
    opacity: 0.8,
  }).addTo(map)
}

function centerOnStore() {
  if (map) {
    map.setView([props.storeLat, props.storeLng], 14)
  }
}

// Reverse Geocode Coords -> Address String
async function reverseGeocode(lat, lng) {
  try {
    const res = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&countrycodes=ph`, {
      headers: { 'Accept-Language': 'en' }
    })
    const data = await res.json()
    if (data && data.display_name) {
      const fullAddress = data.display_name
      const addressParts = fullAddress.split(', ')

      // Extract city & street
      const cityPart = addressParts.find(p => p.toLowerCase().includes('city') || p.toLowerCase().includes('cavite') || p.toLowerCase().includes('manila')) || addressParts[addressParts.length - 3] || 'Cavite'
      const streetPart = addressParts.slice(0, 3).join(', ')

      emit('update:address', streetPart)
      emit('update:city', cityPart)
      emit('location-selected', { lat, lng, address: streetPart, city: cityPart })
    }
  } catch (err) {
    console.warn('Reverse geocoding failed', err)
  }
}

// Search Input Handler (Debounced Suggestions)
function handleSearchInput() {
  if (!searchQuery.value.trim() || searchQuery.value.length < 3) {
    suggestions.value = []
    return
  }

  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(async () => {
    searching.value = true
    try {
      const res = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(searchQuery.value + ', Philippines')}&format=json&countrycodes=ph&limit=5`, {
        headers: { 'Accept-Language': 'en' }
      })
      suggestions.value = await res.json()
    } catch {
      suggestions.value = []
    } finally {
      searching.value = false
    }
  }, 400)
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
  const lng = parseFloat(sug.lon)

  selectedCoords.value = { lat, lng }
  suggestions.value = []
  searchQuery.value = sug.display_name

  if (map && marker) {
    map.setView([lat, lng], 16)
    marker.setLatLng([lat, lng])
    drawRoute()
  }

  const parts = sug.display_name.split(', ')
  const cityPart = parts.find(p => p.toLowerCase().includes('city') || p.toLowerCase().includes('cavite') || p.toLowerCase().includes('manila')) || parts[parts.length - 3] || 'Cavite'
  const streetPart = parts.slice(0, 3).join(', ')

  emit('update:address', streetPart)
  emit('update:city', cityPart)
  emit('location-selected', { lat, lng, address: streetPart, city: cityPart })
}

watch(showMap, async (val) => {
  if (val) {
    await nextTick()
    await initMap()
    setTimeout(() => map?.invalidateSize(), 200)
  }
})

onMounted(() => {
  if (props.address) searchQuery.value = props.address
})

onUnmounted(() => {
  if (map) map.remove()
})
</script>

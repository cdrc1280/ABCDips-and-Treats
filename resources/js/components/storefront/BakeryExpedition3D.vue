<template>
  <div ref="expeditionRef" class="relative w-full rounded-3xl overflow-hidden bg-[#0C0704] text-[#FBF3E7] border border-[#C08E5D]/30 shadow-2xl min-h-[92vh] flex flex-col justify-between p-6 sm:p-10 lg:p-14 select-none group perspective-[1400px]">
    
    <!-- 3D Cinematic Background Atmosphere & Light Flares -->
    <div class="absolute inset-0 bg-gradient-to-b from-[#1C120A]/90 via-[#0F0804]/95 to-[#060302] pointer-events-none" />
    <div
      class="absolute -top-32 left-1/2 -translate-x-1/2 w-[52rem] h-[52rem] rounded-full bg-[radial-gradient(circle,rgba(217,168,118,0.2)_0%,rgba(192,142,93,0.08)_40%,transparent_70%)] blur-3xl pointer-events-none transition-all duration-1000"
      :style="{ transform: `translate(-50%, 0) scale(${1 + activeStageIdx * 0.08})` }"
    />
    <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-[#5C3A22]/20 rounded-full blur-3xl pointer-events-none" />
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-[#C08E5D]/15 rounded-full blur-3xl pointer-events-none" />

    <!-- 3D Interactive Canvas / Backdrop Mesh -->
    <div
      ref="cameraCanvasRef"
      @mousemove="handleCanvasMouseMove"
      @mouseleave="handleCanvasMouseLeave"
      class="absolute inset-0 transition-transform duration-500 ease-out will-change-transform flex items-center justify-center pointer-events-auto cursor-grab active:cursor-grabbing"
    >
      <!-- Cinematic Center 3D Stage Object with Dynamic Camera Lighting -->
      <div
        class="relative w-full max-w-2xl aspect-16/10 rounded-3xl overflow-hidden border border-[#C08E5D]/30 shadow-[0_20px_70px_rgba(0,0,0,0.8)] transition-all duration-700 ease-out"
        :style="{ transform: `scale(${1.0 + activeStageIdx * 0.02}) rotateY(${cameraRotateY}deg) rotateX(${cameraRotateX}deg)` }"
      >
        <img
          :src="activeStage.image"
          :alt="activeStage.title"
          class="w-full h-full object-cover rounded-2xl transform transition-transform duration-1000 ease-out group-hover:scale-105"
        />
        
        <!-- Volumetric Vignette & Sun Rays Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-[#0C0704] via-transparent to-black/40 pointer-events-none" />
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,transparent_40%,rgba(12,7,4,0.85)_100%)] pointer-events-none" />

        <!-- Floating 3D Waypoint Markers across the landscape -->
        <div
          v-for="(stg, idx) in stages"
          :key="stg.id"
          @click.stop="setStage(idx)"
          class="absolute transform -translate-x-1/2 -translate-y-1/2 cursor-pointer transition-all duration-500 group/marker"
          :style="{ left: stg.pinX, top: stg.pinY }"
        >
          <div
            :class="[
              'px-3 py-1.5 rounded-full text-xs font-mono font-bold flex items-center gap-2 border transition-all duration-300 backdrop-blur-md shadow-xl',
              activeStageIdx === idx
                ? 'bg-[#E2C08A] text-[#1C1410] border-white scale-110 shadow-[#D9A876]/40'
                : 'bg-[#1C1410]/85 text-[#E2C08A] border-[#C08E5D]/40 hover:bg-[#1C1410] hover:scale-105'
            ]"
          >
            <span class="w-2 h-2 rounded-full" :class="activeStageIdx === idx ? 'bg-[#1C1410] animate-ping' : 'bg-[#E2C08A]'" />
            <span class="hidden sm:inline">{{ stg.code }} • {{ stg.waypointName }}</span>
            <span class="sm:hidden">{{ stg.code }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Top HUD Bar: Experience Branding & Mode Controls -->
    <div class="relative z-20 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-[#C08E5D]/20 pb-6 pointer-events-auto">
      <div>
        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#D9A876]/15 border border-[#C08E5D]/30 text-[11px] font-mono font-bold text-[#E2C08A] uppercase tracking-widest backdrop-blur-md mb-1.5">
          <Sparkles class="w-3.5 h-3.5 text-[#E2C08A] animate-pulse" />
          <span>3D Cinematic Expedition • Cavite Bakery</span>
        </div>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-[#FBF3E7] tracking-tight font-sans">
          THE ARTISAN EXPEDITION
        </h1>
      </div>

      <!-- Quick Action HUD -->
      <div class="flex items-center gap-2 bg-[#1C1410]/90 p-1.5 rounded-2xl border border-[#C08E5D]/30 backdrop-blur-xl">
        <button
          type="button"
          @click="toggle3DAutoTour"
          :class="[
            'px-4 py-2 rounded-xl text-xs font-bold transition-all duration-300 flex items-center gap-2',
            isTourActive
              ? 'bg-gradient-to-r from-[#D9A876] to-[#C08E5D] text-[#1C1410] shadow-md scale-102'
              : 'text-[#E2C08A]/70 hover:text-[#FBF3E7] hover:bg-white/5'
          ]"
        >
          <RotateCw class="w-3.5 h-3.5" :class="{ 'animate-spin': isTourActive }" style="animation-duration: 5s;" />
          <span>{{ isTourActive ? '3D Tour Active' : 'Start 3D Tour' }}</span>
        </button>

        <RouterLink
          to="/shop"
          class="bg-gradient-to-r from-[#D9A876] to-[#C08E5D] text-[#1C1410] px-5 py-2 rounded-xl text-xs font-extrabold hover:opacity-95 transition-all flex items-center gap-1.5 shadow-md"
        >
          <ShoppingBag class="w-3.5 h-3.5" />
          <span>Taste Experience</span>
        </RouterLink>
      </div>
    </div>

    <!-- Center Interactive HUD Layer (Left Telemetry Card + Right Elevation Waypoint Stepper) -->
    <div class="relative z-20 grid grid-cols-1 lg:grid-cols-12 gap-6 items-center my-auto py-8 pointer-events-none">
      
      <!-- Left HUD Glassmorphic Telemetry Card (Like Everest HUD Drawer) -->
      <div class="lg:col-span-5 bg-[#140D09]/88 border border-[#C08E5D]/40 rounded-3xl p-6 sm:p-7 backdrop-blur-2xl shadow-2xl space-y-4 pointer-events-auto max-w-md transform transition-transform duration-500">
        
        <div class="flex items-center justify-between border-b border-[#C08E5D]/20 pb-3">
          <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse" />
            <span class="text-xs font-mono font-bold text-[#E2C08A] uppercase tracking-wider">
              {{ activeStage.stageLabel }}
            </span>
          </div>
          <span class="text-xs font-mono text-[#FBF3E7]/70">{{ activeStage.elevation }}</span>
        </div>

        <div class="space-y-1.5">
          <h2 class="text-2xl sm:text-3xl font-extrabold text-[#FBF3E7] leading-snug">
            {{ activeStage.title }}
          </h2>
          <p class="text-xs sm:text-sm text-[#FBF3E7]/80 leading-relaxed">
            {{ activeStage.description }}
          </p>
        </div>

        <!-- Field Data Grid -->
        <div class="grid grid-cols-2 gap-2.5 pt-2">
          <div class="p-3 rounded-xl bg-white/5 border border-[#C08E5D]/20">
            <div class="text-[10px] font-mono text-[#E2C08A] uppercase">Oven Temperature</div>
            <div class="text-sm font-mono font-extrabold text-[#FBF3E7] mt-0.5">{{ activeStage.temperature }}</div>
          </div>
          <div class="p-3 rounded-xl bg-white/5 border border-[#C08E5D]/20">
            <div class="text-[10px] font-mono text-[#E2C08A] uppercase">Hydration Index</div>
            <div class="text-sm font-mono font-extrabold text-emerald-400 mt-0.5">{{ activeStage.hydration }}</div>
          </div>
        </div>

        <!-- Action Button -->
        <div class="pt-2">
          <RouterLink
            :to="activeStage.ctaUrl"
            class="w-full bg-gradient-to-r from-[#D9A876] to-[#C08E5D] text-[#1C1410] py-3 rounded-xl font-black text-xs hover:opacity-95 transition-all flex items-center justify-center gap-2 shadow-lg"
          >
            <span>Order {{ activeStage.name }} • {{ activeStage.price }}</span>
            <ArrowRight class="w-3.5 h-3.5" />
          </RouterLink>
        </div>
      </div>

      <!-- Right Side: Vertical Elevation Stepper (Like Everest Stepper) -->
      <div class="hidden lg:flex lg:col-span-7 justify-end pointer-events-auto">
        <div class="bg-[#140D09]/85 border border-[#C08E5D]/30 p-2 rounded-2xl backdrop-blur-xl flex flex-col gap-2 shadow-xl">
          <button
            v-for="(stg, idx) in stages"
            :key="stg.id"
            @click="setStage(idx)"
            :class="[
              'w-9 h-9 rounded-xl font-mono text-xs font-bold transition-all duration-300 flex items-center justify-center',
              activeStageIdx === idx
                ? 'bg-gradient-to-r from-[#D9A876] to-[#C08E5D] text-[#1C1410] shadow-md scale-110'
                : 'text-[#E2C08A]/70 hover:text-[#FBF3E7] hover:bg-white/10'
            ]"
            :title="stg.title"
          >
            {{ idx + 1 }}
          </button>
        </div>
      </div>

    </div>

    <!-- Bottom HUD Bar: Live Telemetry Metrics (Like Everest Bottom Bar) -->
    <div class="relative z-20 border-t border-[#C08E5D]/20 pt-4 flex flex-wrap items-center justify-between gap-4 text-xs font-mono text-[#E2C08A]/80 pointer-events-auto">
      <div class="flex flex-wrap items-center gap-6 sm:gap-10">
        <div>
          <div class="text-[9px] text-[#E2C08A]/60 uppercase">OVEN HEAT</div>
          <div class="text-xs sm:text-sm font-bold text-[#FBF3E7]">350°F CONVECTION</div>
        </div>
        <div>
          <div class="text-[9px] text-[#E2C08A]/60 uppercase">BUTTER FAT</div>
          <div class="text-xs sm:text-sm font-bold text-amber-300">82% EUROPEAN</div>
        </div>
        <div>
          <div class="text-[9px] text-[#E2C08A]/60 uppercase">BATCH OUTPUT</div>
          <div class="text-xs sm:text-sm font-bold text-emerald-400">14,250+ LOAVES</div>
        </div>
        <div>
          <div class="text-[9px] text-[#E2C08A]/60 uppercase">LOCATION</div>
          <div class="text-xs sm:text-sm font-bold text-[#FBF3E7]">CAVITE, PH</div>
        </div>
      </div>

      <div class="flex items-center gap-2 text-[10px] text-[#E2C08A]/70">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" />
        <span>3D KINETIC ENGINE ACTIVE</span>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Sparkles, ShoppingBag, ArrowRight, RotateCw } from 'lucide-vue-next'

const expeditionRef = ref(null)
const cameraCanvasRef = ref(null)

const activeStageIdx = ref(0)
const isTourActive = ref(false)
const cameraRotateX = ref(0)
const cameraRotateY = ref(0)
let tourTimer = null
let rafId = null

const stages = [
  {
    id: 'stage-1',
    code: 'STAGE I',
    stageLabel: 'Stage I • Cavendish Harvest',
    waypointName: 'Cavite Banana Groves',
    elevation: 'Elev. 120m',
    title: 'Classic Cavendish Banana Bread',
    name: 'Fresh Loaf',
    price: '₱280.00',
    description: 'Slow-folded with over 1 lb of sun-ripened Cavendish bananas, roasted California walnuts, and French brown butter.',
    temperature: '350°F Golden Rise',
    hydration: '99.4% Crumb Softness',
    pinX: '28%',
    pinY: '42%',
    image: '/images/blog-banana-bread.jpg',
    ctaUrl: '/shop?search=banana'
  },
  {
    id: 'stage-2',
    code: 'STAGE II',
    stageLabel: 'Stage II • The Callebaut Crucible',
    waypointName: 'Belgian Cacao Core',
    elevation: 'Elev. 340m',
    title: 'Belgian Dark Ganache Brownies',
    name: 'Fudge Box',
    price: '₱340.00',
    description: 'Single-origin 70% Callebaut dark chocolate melted into pure creamery butter with a crackly sea-salt crown.',
    temperature: '325°F Gentle Melt',
    hydration: '98.2% Molten Core',
    pinX: '52%',
    pinY: '35%',
    image: '/images/blog-banana-bread.jpg',
    ctaUrl: '/shop?category=fudge-brownies'
  },
  {
    id: 'stage-3',
    code: 'STAGE III',
    stageLabel: 'Stage III • Basque Heritage',
    waypointName: 'Ube Cream Summit',
    elevation: 'Elev. 680m',
    title: 'Signature Basque Ube Cheesecake',
    name: 'Basque Cake',
    price: '₱680.00',
    description: 'Pure Philippine Ube Halaya baked into smooth cream cheese over a toasted coconut Graham crust.',
    temperature: '425°F Caramelized Top',
    hydration: '99.8% Silky Custard',
    pinX: '75%',
    pinY: '48%',
    image: '/images/blog-custom-cake.jpg',
    ctaUrl: '/products/signature-ube-cheesecake-6-inch'
  },
  {
    id: 'stage-4',
    code: 'STAGE IV',
    stageLabel: 'Stage IV • Laminated Brioche',
    waypointName: 'Cinnamon Spire',
    elevation: 'Elev. 290m',
    title: 'Flaky Cinnamon Brioche Rolls',
    name: 'Brioche Roll',
    price: '₱290.00',
    description: '72 layers of butter-laminated dough infused with spiced Saigon cinnamon and cream cheese vanilla drizzle.',
    temperature: '360°F Lamination Rise',
    hydration: '96.5% Flaky Matrix',
    pinX: '40%',
    pinY: '65%',
    image: '/images/blog-banana-bread.jpg',
    ctaUrl: '/shop?search=cinnamon'
  }
]

const activeStage = computed(() => stages[activeStageIdx.value])

function setStage(idx) {
  activeStageIdx.value = idx
}

function toggle3DAutoTour() {
  isTourActive.value = !isTourActive.value
  if (isTourActive.value) {
    startTour()
  } else {
    stopTour()
  }
}

function startTour() {
  if (tourTimer) clearInterval(tourTimer)
  tourTimer = setInterval(() => {
    activeStageIdx.value = (activeStageIdx.value + 1) % stages.length
  }, 4500)
}

function stopTour() {
  if (tourTimer) {
    clearInterval(tourTimer)
    tourTimer = null
  }
}

function handleCanvasMouseMove(e) {
  if (!cameraCanvasRef.value) return
  if (rafId) cancelAnimationFrame(rafId)

  rafId = requestAnimationFrame(() => {
    if (!cameraCanvasRef.value) return
    const rect = cameraCanvasRef.value.getBoundingClientRect()
    const x = e.clientX - rect.left - rect.width / 2
    const y = e.clientY - rect.top - rect.height / 2
    
    cameraRotateX.value = -(y / (rect.height / 2)) * 12
    cameraRotateY.value = (x / (rect.width / 2)) * 12
  })
}

function handleCanvasMouseLeave() {
  cameraRotateX.value = 0
  cameraRotateY.value = 0
}

onMounted(() => {
  // Start subtle ambient tour
  startTour()
  isTourActive.value = true
})

onUnmounted(() => {
  stopTour()
  if (rafId) cancelAnimationFrame(rafId)
})
</script>

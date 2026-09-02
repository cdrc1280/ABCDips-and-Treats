<template>
  <div ref="sectionRef" class="relative bg-[#140D09] text-[#FBF3E7] rounded-3xl overflow-hidden border border-[#C08E5D]/30 shadow-2xl">
    
    <!-- Ambient Studio Lights & Gradient Fog -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-[#D9A876]/15 rounded-full blur-3xl pointer-events-none transition-opacity duration-700"
         :style="{ opacity: activeStage === 0 ? 0.8 : 0.4 }" />
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-[#C08E5D]/20 rounded-full blur-3xl pointer-events-none transition-opacity duration-700"
         :style="{ opacity: activeStage === 3 ? 0.9 : 0.4 }" />
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(217,168,118,0.06)_0%,transparent_70%)] pointer-events-none" />

    <!-- Main Pin Container -->
    <div ref="pinContainerRef" class="min-h-[85vh] lg:min-h-[90vh] flex flex-col justify-between p-6 sm:p-10 lg:p-12 relative z-10">
      
      <!-- Top Bar: Studio Header & Stage Pills -->
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-b border-[#C08E5D]/20 pb-6">
        <div>
          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#D9A876]/15 border border-[#C08E5D]/30 text-xs font-bold text-[#E2C08A] tracking-wider uppercase backdrop-blur-md mb-2">
            <Sparkles class="w-3.5 h-3.5 text-[#E2C08A] animate-pulse" />
            <span>3D CGI Product Showcase • Artisanal Cavendish Loaf</span>
          </div>
          <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-[#FBF3E7] tracking-tight">
            The Anatomy of Perfection
          </h2>
        </div>

        <!-- Stage Navigation Track -->
        <div class="flex items-center gap-1.5 p-1.5 bg-[#1C1410]/80 rounded-2xl border border-[#C08E5D]/30 backdrop-blur-md overflow-x-auto max-w-full">
          <button v-for="(stage, idx) in stages" :key="stage.id"
                  @click="goToStage(idx)"
                  :class="[
                    'px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-300 whitespace-nowrap flex items-center gap-2',
                    activeStage === idx
                      ? 'bg-gradient-to-r from-[#D9A876] to-[#C08E5D] text-[#1C1410] shadow-md scale-102'
                      : 'text-[#E2C08A]/70 hover:text-[#FBF3E7] hover:bg-white/5'
                  ]">
            <span class="text-[10px] font-mono opacity-80">0{{ idx + 1 }}</span>
            <span>{{ stage.tabLabel }}</span>
          </button>
        </div>
      </div>

      <!-- Center Stage: 3D Studio Stage with Dynamic Camera & Exploded View -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center my-auto py-8">
        
        <!-- Left: Stage Narrative & Craft Metrics (Animated with GSAP) -->
        <div class="lg:col-span-5 space-y-6 text-left">
          
          <!-- Stage Badge -->
          <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-xl bg-[#C08E5D]/20 border border-[#C08E5D]/40 text-[#E2C08A] flex items-center justify-center text-xs font-mono font-bold">
              0{{ activeStage + 1 }}
            </span>
            <span class="text-xs font-bold text-[#E2C08A] uppercase tracking-widest font-mono">
              {{ stages[activeStage].subtitle }}
            </span>
          </div>

          <div class="space-y-3">
            <h3 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#FBF3E7] tracking-tight leading-tight">
              {{ stages[activeStage].title }}
            </h3>
            <p class="text-sm sm:text-base text-[#FBF3E7]/80 leading-relaxed min-h-[4.5rem]">
              {{ stages[activeStage].description }}
            </p>
          </div>

          <!-- Feature Cards for Active Stage -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
            <div v-for="(feat, fIdx) in stages[activeStage].features" :key="fIdx"
                 class="p-3.5 rounded-2xl bg-white/5 border border-[#C08E5D]/20 backdrop-blur-md flex items-start gap-3 transform transition-all duration-300 hover:bg-white/10 hover:border-[#C08E5D]/40">
              <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-[#D9A876]/30 to-[#C08E5D]/20 flex items-center justify-center text-[#E2C08A] shrink-0 border border-[#C08E5D]/30">
                <component :is="feat.icon" class="w-4 h-4" />
              </div>
              <div>
                <h4 class="text-xs font-bold text-[#FBF3E7]">{{ feat.title }}</h4>
                <p class="text-[11px] text-[#E2C08A]/70 mt-0.5">{{ feat.detail }}</p>
              </div>
            </div>
          </div>

          <!-- Action & Order Row -->
          <div class="flex flex-wrap items-center gap-4 pt-4">
            <RouterLink to="/shop?search=banana"
                        class="bg-gradient-to-r from-[#D9A876] to-[#C08E5D] text-[#1C1410] px-7 py-3.5 rounded-2xl font-extrabold text-sm hover:opacity-95 hover:shadow-lg hover:shadow-[#C08E5D]/20 transition-all duration-300 flex items-center gap-2 transform active:scale-95">
              <ShoppingBag class="w-4 h-4" />
              <span>Order Fresh Loaf • From ₱280</span>
            </RouterLink>
            <RouterLink to="/custom-orders"
                        class="px-5 py-3.5 rounded-2xl font-bold text-xs sm:text-sm text-[#E2C08A] hover:text-[#FBF3E7] hover:bg-white/5 border border-[#C08E5D]/30 transition-all duration-300 flex items-center gap-1.5">
              <span>Bulk / Custom Loaves</span>
              <ArrowRight class="w-3.5 h-3.5" />
            </RouterLink>
          </div>
        </div>

        <!-- Right: Interactive 3D Turntable / Exploded Layer Canvas -->
        <div class="lg:col-span-7 flex justify-center items-center relative perspective-[1400px]">
          
          <!-- 3D Studio Card with Interactive Mouse Gyroscope & Turntable Scrub -->
          <div ref="viewport3dRef"
               @mousemove="handleMouseMove"
               @mouseleave="handleMouseLeave"
               class="relative w-full max-w-lg aspect-4/3 sm:aspect-square rounded-3xl p-4 bg-gradient-to-b from-white/10 via-black/40 to-black/60 border border-[#C08E5D]/40 shadow-2xl backdrop-blur-2xl transition-transform duration-300 ease-out will-change-transform flex items-center justify-center overflow-hidden group">
            
            <!-- Radial Spotlight & Turntable Base Disk -->
            <div class="absolute w-80 h-80 rounded-full bg-gradient-to-tr from-[#D9A876]/10 to-transparent blur-2xl pointer-events-none" />
            <div class="absolute bottom-6 w-3/4 h-8 bg-[#D9A876]/10 rounded-full blur-xl transform scale-y-50 pointer-events-none" />

            <!-- Dynamic Product Layer: Stage 0 (Whole Studio Loaf) -->
            <div v-show="activeStage === 0" class="stage-layer relative w-full h-full flex items-center justify-center transition-all duration-500">
              <div class="relative w-full h-full rounded-2xl overflow-hidden shadow-2xl border border-[#C08E5D]/30">
                <img src="/images/blog-banana-bread.jpg" 
                     alt="Artisanal Cavendish Banana Bread Studio" 
                     class="w-full h-full object-cover rounded-2xl transform transition-transform duration-700 ease-out group-hover:scale-105" />
                <div class="absolute inset-0 bg-gradient-to-t from-[#140D09]/80 via-transparent to-black/20" />
                
                <!-- Floating 360 Turntable Badge -->
                <div class="absolute top-4 left-4 inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-[#140D09]/85 border border-[#C08E5D]/40 text-xs font-bold text-[#E2C08A] backdrop-blur-md shadow-lg">
                  <RotateCw class="w-3.5 h-3.5 text-[#E2C08A] animate-spin" style="animation-duration: 8s;" />
                  <span>360° Studio Showcase</span>
                </div>

                <div class="absolute bottom-4 left-4 right-4 p-3 rounded-xl bg-[#140D09]/85 border border-[#C08E5D]/30 backdrop-blur-md flex items-center justify-between text-xs">
                  <span class="text-[#FBF3E7] font-bold">Golden Walnut &amp; Cinnamon Glaze</span>
                  <span class="text-[#E2C08A] font-mono font-extrabold">1.2 kg Fresh Weight</span>
                </div>
              </div>
            </div>

            <!-- Dynamic Product Layer: Stage 1 (Sliced Honeycomb Crumb) -->
            <div v-show="activeStage === 1" class="stage-layer relative w-full h-full flex items-center justify-center transition-all duration-500">
              <div class="relative w-full h-full rounded-2xl overflow-hidden shadow-2xl border border-[#C08E5D]/30">
                <img src="/images/blog-banana-bread.jpg" 
                     alt="Crumb Cross-Section Anatomy" 
                     class="w-full h-full object-cover rounded-2xl transform scale-125 transition-transform duration-700 ease-out" />
                <div class="absolute inset-0 bg-gradient-to-t from-[#140D09]/90 via-[#5C3A22]/20 to-black/30" />
                
                <!-- Cross-Section Callouts -->
                <div class="absolute top-6 right-6 p-3 rounded-xl bg-[#140D09]/90 border border-[#C08E5D]/40 backdrop-blur-md text-xs space-y-1 shadow-lg max-w-[12rem]">
                  <div class="flex items-center gap-1.5 text-[#E2C08A] font-bold">
                    <Flame class="w-3.5 h-3.5" />
                    <span>Convection Crumb</span>
                  </div>
                  <p class="text-[10px] text-[#FBF3E7]/80">Honeycomb moisture retention with zero crumbly dryness.</p>
                </div>

                <div class="absolute bottom-6 left-6 p-3 rounded-xl bg-[#140D09]/90 border border-[#C08E5D]/40 backdrop-blur-md text-xs space-y-1 shadow-lg max-w-[12rem]">
                  <div class="flex items-center gap-1.5 text-[#E2C08A] font-bold">
                    <Sparkles class="w-3.5 h-3.5" />
                    <span>Cavendish Pockets</span>
                  </div>
                  <p class="text-[10px] text-[#FBF3E7]/80">Visible caramelized fruit folds throughout every slice.</p>
                </div>
              </div>
            </div>

            <!-- Dynamic Product Layer: Stage 2 (Exploded Floating Ingredients) -->
            <div v-show="activeStage === 2" class="stage-layer relative w-full h-full flex items-center justify-center p-4 transition-all duration-500">
              <div class="grid grid-cols-2 gap-4 w-full h-full">
                <div class="p-4 rounded-2xl bg-gradient-to-br from-white/10 to-white/5 border border-[#C08E5D]/30 backdrop-blur-md flex flex-col justify-between transform hover:-translate-y-1 transition-transform">
                  <div class="w-10 h-10 rounded-xl bg-[#D9A876]/20 flex items-center justify-center text-[#E2C08A] border border-[#C08E5D]/30">
                    <Sparkles class="w-5 h-5" />
                  </div>
                  <div>
                    <h4 class="font-extrabold text-sm text-[#FBF3E7]">100% Cavendish Bananas</h4>
                    <p class="text-[11px] text-[#E2C08A]/80 mt-1">> 1 lb per loaf for natural honey sweetness</p>
                  </div>
                </div>

                <div class="p-4 rounded-2xl bg-gradient-to-br from-white/10 to-white/5 border border-[#C08E5D]/30 backdrop-blur-md flex flex-col justify-between transform hover:-translate-y-1 transition-transform">
                  <div class="w-10 h-10 rounded-xl bg-[#D9A876]/20 flex items-center justify-center text-[#E2C08A] border border-[#C08E5D]/30">
                    <Heart class="w-5 h-5" />
                  </div>
                  <div>
                    <h4 class="font-extrabold text-sm text-[#FBF3E7]">Pure Creamery Butter</h4>
                    <p class="text-[11px] text-[#E2C08A]/80 mt-1">Real European unsalted butter, zero margarine</p>
                  </div>
                </div>

                <div class="p-4 rounded-2xl bg-gradient-to-br from-white/10 to-white/5 border border-[#C08E5D]/30 backdrop-blur-md flex flex-col justify-between transform hover:-translate-y-1 transition-transform">
                  <div class="w-10 h-10 rounded-xl bg-[#D9A876]/20 flex items-center justify-center text-[#E2C08A] border border-[#C08E5D]/30">
                    <Flame class="w-5 h-5" />
                  </div>
                  <div>
                    <h4 class="font-extrabold text-sm text-[#FBF3E7]">Roasted Walnuts</h4>
                    <p class="text-[11px] text-[#E2C08A]/80 mt-1">Lightly torched California walnut pieces</p>
                  </div>
                </div>

                <div class="p-4 rounded-2xl bg-gradient-to-br from-white/10 to-white/5 border border-[#C08E5D]/30 backdrop-blur-md flex flex-col justify-between transform hover:-translate-y-1 transition-transform">
                  <div class="w-10 h-10 rounded-xl bg-[#D9A876]/20 flex items-center justify-center text-[#E2C08A] border border-[#C08E5D]/30">
                    <Award class="w-5 h-5" />
                  </div>
                  <div>
                    <h4 class="font-extrabold text-sm text-[#FBF3E7]">Zero Preservatives</h4>
                    <p class="text-[11px] text-[#E2C08A]/80 mt-1">Clean-label, unbleached flour, farm eggs</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Dynamic Product Layer: Stage 3 (Velvety Chocolate Dip Pairing) -->
            <div v-show="activeStage === 3" class="stage-layer relative w-full h-full flex flex-col items-center justify-center p-6 text-center transition-all duration-500">
              <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-[#5C3A22] to-[#1C1410] flex items-center justify-center text-[#E2C08A] mb-4 border-2 border-[#C08E5D] shadow-2xl">
                <Sparkles class="w-10 h-10 text-[#E2C08A]" />
              </div>
              <h3 class="text-xl sm:text-2xl font-extrabold text-[#FBF3E7] mb-2">The Signature Chocolate Dip Pairing</h3>
              <p class="text-xs sm:text-sm text-[#FBF3E7]/80 max-w-sm mb-6 leading-relaxed">
                Dip warm banana bread slices into our artisanal dark Belgian ganache or salted caramel dip for the ultimate dessert experience.
              </p>
              
              <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#1C1410]/90 border border-[#C08E5D]/40 text-xs font-bold text-[#E2C08A] backdrop-blur-md shadow-lg">
                <Award class="w-4 h-4 text-[#E2C08A]" />
                <span>Includes 1 Complimentary Signature Dip with Every Loaf</span>
              </div>
            </div>

            <!-- Subtle Corner Frame Tech Accents -->
            <div class="absolute -top-1 -left-1 w-4 h-4 border-t-2 border-l-2 border-[#E2C08A] rounded-tl-lg" />
            <div class="absolute -top-1 -right-1 w-4 h-4 border-t-2 border-r-2 border-[#E2C08A] rounded-tr-lg" />
            <div class="absolute -bottom-1 -left-1 w-4 h-4 border-b-2 border-l-2 border-[#E2C08A] rounded-bl-lg" />
            <div class="absolute -bottom-1 -right-1 w-4 h-4 border-b-2 border-r-2 border-[#E2C08A] rounded-br-lg" />
          </div>
        </div>

      </div>

      <!-- Bottom Bar: Interactive Scroll Timeline Progress Bar -->
      <div class="border-t border-[#C08E5D]/20 pt-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3 text-xs text-[#E2C08A]/80 font-mono">
          <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping" />
          <span>SCROLL DOWN TO PROGRESS CGI STAGES</span>
        </div>

        <div class="w-full sm:w-64 h-2 bg-white/10 rounded-full overflow-hidden border border-[#C08E5D]/20">
          <div class="h-full bg-gradient-to-r from-[#D9A876] to-[#C08E5D] transition-all duration-300 rounded-full"
               :style="{ width: `${((activeStage + 1) / stages.length) * 100}%` }" />
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Sparkles, ShoppingBag, ArrowRight, Award, Flame, Heart, RotateCw, Layers } from 'lucide-vue-next'
import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

gsap.registerPlugin(ScrollTrigger)

const sectionRef = ref(null)
const pinContainerRef = ref(null)
const viewport3dRef = ref(null)
const activeStage = ref(0)
let ctx = null

const stages = [
  {
    id: 'studio',
    tabLabel: '360° Studio Loaf',
    subtitle: 'STAGE 01 • THE WHOLE LOAF',
    title: 'Golden Crust & Roasted Walnuts',
    description: 'Freshly unmolded from the oven with caramelized dark edges, fragrant cinnamon notes, and crunchy California walnuts toasted to golden perfection.',
    features: [
      { icon: Flame, title: 'Convection Baked', detail: 'Slow 55-minute bake for even caramelization' },
      { icon: Award, title: 'Artisanal Loaf', detail: 'Hand-mixed in micro batches daily' }
    ]
  },
  {
    id: 'anatomy',
    tabLabel: 'Honeycomb Crumb',
    subtitle: 'STAGE 02 • SLICED ANATOMY',
    title: 'Moist Honeycomb Crumb Structure',
    description: 'Slicing open reveals a rich, dense yet velvety crumb that retains moisture for days without ever drying out or crumbling.',
    features: [
      { icon: Sparkles, title: 'Cavendish Folds', detail: 'Swirled with natural banana sugars' },
      { icon: Heart, title: 'Zero Artificial Additives', detail: 'Naturally leavened and unbleached' }
    ]
  },
  {
    id: 'ingredients',
    tabLabel: 'Pure Ingredients',
    subtitle: 'STAGE 03 • 3D EXPLODED VIEW',
    title: 'Farm-Fresh Clean Label Ingredients',
    description: 'Over a full pound of ripened bananas, pure imported creamery butter, farm-fresh eggs, and aromatic spices in every single bake.',
    features: [
      { icon: Sparkles, title: '> 1 lb Cavendish', detail: 'Peak ripeness for deep caramel flavor' },
      { icon: Heart, title: 'Real Dairy Butter', detail: 'European cultured creamery butter' }
    ]
  },
  {
    id: 'pairing',
    tabLabel: 'Signature Dip',
    subtitle: 'STAGE 04 • VELVET DIP PAIRING',
    title: 'Rich Dark Chocolate Ganache Dip',
    description: 'Each loaf comes with our signature artisanal dipping sauce. Warm your slice for 15 seconds and dip into liquid chocolate bliss.',
    features: [
      { icon: Award, title: 'Belgian Cacao', detail: 'Velvety smooth warm ganache' },
      { icon: ShoppingBag, title: 'Free Dip Included', detail: 'Comes with every single order' }
    ]
  }
]

function goToStage(idx) {
  activeStage.value = idx
}

function handleMouseMove(e) {
  if (!viewport3dRef.value) return
  const rect = viewport3dRef.value.getBoundingClientRect()
  const x = e.clientX - rect.left - rect.width / 2
  const y = e.clientY - rect.top - rect.height / 2
  
  const rotateX = -(y / (rect.height / 2)) * 12
  const rotateY = (x / (rect.width / 2)) * 12
  
  viewport3dRef.value.style.transform = `perspective(1200px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`
}

function handleMouseLeave() {
  if (!viewport3dRef.value) return
  viewport3dRef.value.style.transform = 'perspective(1200px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)'
}

onMounted(() => {
  ctx = gsap.context(() => {
    // Pin section and scrub through the 4 CGI stages as user scrolls
    ScrollTrigger.create({
      trigger: sectionRef.value,
      start: 'top 15%',
      end: '+=180%',
      pin: true,
      scrub: 1,
      onUpdate: (self) => {
        const progress = self.progress
        const newStage = Math.min(
          stages.length - 1,
          Math.floor(progress * stages.length)
        )
        if (newStage !== activeStage.value) {
          activeStage.value = newStage
        }
      }
    })
  }, sectionRef.value)
})

onUnmounted(() => {
  if (ctx) ctx.revert()
})
</script>

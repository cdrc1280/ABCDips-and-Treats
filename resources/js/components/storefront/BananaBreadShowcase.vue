<template>
  <div ref="containerRef" class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#2D1B10] via-[#1C1410] to-[#120B07] text-surface p-6 sm:p-10 lg:p-14 border border-[#C08E5D]/30 shadow-2xl">
    
    <!-- Warm Ambient Glow Overlays -->
    <div class="absolute -top-24 -left-24 w-72 h-72 bg-[#D9A876]/15 rounded-full blur-3xl pointer-events-none" />
    <div class="absolute -bottom-24 -right-24 w-80 h-80 bg-[#C08E5D]/20 rounded-full blur-3xl pointer-events-none" />

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center relative z-10">
      
      <!-- Left: Story & Craftsmanship Milestones -->
      <div class="lg:col-span-6 space-y-6 text-left">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#D9A876]/15 border border-[#C08E5D]/30 text-xs font-bold text-[#E2C08A] tracking-wider uppercase backdrop-blur-md">
          <Sparkles class="w-3.5 h-3.5 text-[#E2C08A]" />
          <span>Signature Craft Highlight</span>
        </div>

        <div class="space-y-2">
          <h3 class="font-['Caveat'] text-[#E2C08A] text-2xl sm:text-3xl">the gold standard of baking</h3>
          <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#FBF3E7] tracking-tight leading-tight">
            Artisanal Banana Bread <br />
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#E2C08A] via-[#D9A876] to-[#C08E5D]">
              Slow-Baked to Perfection
            </span>
          </h2>
        </div>

        <p class="text-sm sm:text-base text-[#FBF3E7]/80 leading-relaxed max-w-xl">
          Every loaf is handcrafted in small batches using over a pound of naturally ripened Cavendish bananas, real creamery butter, and toasted walnuts for an impossibly moist, melt-in-your-mouth crumb.
        </p>

        <!-- Dynamic Craft Milestones (Staggered On Scroll) -->
        <div class="space-y-3 pt-2">
          <div v-for="(milestone, idx) in milestones" :key="idx" 
               class="milestone-item flex items-center gap-3.5 p-3.5 rounded-2xl bg-white/5 dark:bg-black/20 border border-[#C08E5D]/20 backdrop-blur-md hover:bg-white/10 transition-all duration-300 transform hover:translate-x-1">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#C08E5D]/40 to-[#5C3A22]/60 flex items-center justify-center text-[#E2C08A] shrink-0 shadow-inner border border-[#C08E5D]/30">
              <component :is="milestone.icon" class="w-5 h-5" />
            </div>
            <div>
              <h4 class="text-xs sm:text-sm font-bold text-[#FBF3E7]">{{ milestone.title }}</h4>
              <p class="text-[11px] text-[#E2C08A]/80">{{ milestone.desc }}</p>
            </div>
          </div>
        </div>

        <!-- Action CTA -->
        <div class="flex flex-wrap items-center gap-4 pt-4">
          <RouterLink to="/shop?search=banana" 
                      class="bg-gradient-to-r from-[#D9A876] to-[#C08E5D] text-[#1C1410] px-7 py-3.5 rounded-2xl font-extrabold text-sm hover:opacity-95 hover:shadow-lg hover:shadow-[#C08E5D]/20 transition-all duration-300 flex items-center gap-2 transform active:scale-98">
            <ShoppingBag class="w-4 h-4" />
            <span>Order Fresh Loaf • From ₱280</span>
          </RouterLink>
          <RouterLink to="/about" 
                      class="px-5 py-3.5 rounded-2xl font-bold text-xs sm:text-sm text-[#E2C08A] hover:text-[#FBF3E7] hover:bg-white/5 border border-[#C08E5D]/30 transition-all duration-300 flex items-center gap-1.5">
            <span>Read Our Baking Story</span>
            <ArrowRight class="w-3.5 h-3.5" />
          </RouterLink>
        </div>
      </div>

      <!-- Right: Interactive 3D Parallax Banana Bread Card -->
      <div class="lg:col-span-6 flex justify-center perspective-[1200px]">
        <div ref="cardRef"
             @mousemove="handleMouseMove"
             @mouseleave="handleMouseLeave"
             class="relative w-full max-w-md aspect-4/3 sm:aspect-square rounded-3xl p-3 bg-gradient-to-b from-white/10 to-white/5 border border-[#C08E5D]/40 shadow-2xl backdrop-blur-xl transition-transform duration-200 ease-out will-change-transform">
          
          <!-- Image Container with Glaze Refraction -->
          <div class="relative w-full h-full rounded-2xl overflow-hidden shadow-inner group">
            <img src="/images/blog-banana-bread.jpg" 
                 alt="Artisanal Cavendish Banana Bread" 
                 class="w-full h-full object-cover rounded-2xl transform transition-transform duration-700 ease-out group-hover:scale-108" />
            
            <div class="absolute inset-0 bg-gradient-to-t from-[#1C1410]/80 via-transparent to-black/20" />

            <!-- Floating Quality Badge -->
            <div class="absolute top-4 left-4 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#1C1410]/80 border border-[#C08E5D]/40 text-xs font-bold text-[#E2C08A] backdrop-blur-md shadow-lg">
              <Award class="w-3.5 h-3.5 text-[#E2C08A]" />
              <span>Cavite's Top Rated</span>
            </div>

            <!-- Floating Temperature / Freshness Badge -->
            <div class="absolute bottom-4 left-4 right-4 p-3.5 rounded-2xl bg-[#1C1410]/85 border border-[#C08E5D]/30 backdrop-blur-md flex items-center justify-between text-xs">
              <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping" />
                <span class="font-bold text-[#FBF3E7]">Baked Fresh Every Morning</span>
              </div>
              <span class="text-[#E2C08A] font-extrabold">100% Real Butter</span>
            </div>
          </div>

          <!-- Subtle Corner Accents -->
          <div class="absolute -top-1.5 -left-1.5 w-4 h-4 border-t-2 border-l-2 border-[#E2C08A] rounded-tl-lg" />
          <div class="absolute -top-1.5 -right-1.5 w-4 h-4 border-t-2 border-r-2 border-[#E2C08A] rounded-tr-lg" />
          <div class="absolute -bottom-1.5 -left-1.5 w-4 h-4 border-b-2 border-l-2 border-[#E2C08A] rounded-bl-lg" />
          <div class="absolute -bottom-1.5 -right-1.5 w-4 h-4 border-b-2 border-r-2 border-[#E2C08A] rounded-br-lg" />
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Sparkles, ShoppingBag, ArrowRight, Award, Flame, Clock, Heart } from 'lucide-vue-next'
import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

gsap.registerPlugin(ScrollTrigger)

const containerRef = ref(null)
const cardRef = ref(null)
let ctx = null

const milestones = [
  { icon: Flame, title: 'Over 1 lb of Pure Cavendish Bananas', desc: 'Naturally sweetened with zero artificial banana flavorings' },
  { icon: Heart, title: 'Real Imported Creamery Butter', desc: 'Rich golden crumb structure that stays moist for days' },
  { icon: Clock, title: 'Slow Convection Oven Bake', desc: 'Caramelized walnut topping with a delicate golden crust' }
]

function handleMouseMove(e) {
  if (!cardRef.value) return
  const rect = cardRef.value.getBoundingClientRect()
  const x = e.clientX - rect.left - rect.width / 2
  const y = e.clientY - rect.top - rect.height / 2
  
  const rotateX = -(y / (rect.height / 2)) * 10
  const rotateY = (x / (rect.width / 2)) * 10
  
  cardRef.value.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`
}

function handleMouseLeave() {
  if (!cardRef.value) return
  cardRef.value.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)'
}

onMounted(() => {
  ctx = gsap.context(() => {
    gsap.from('.milestone-item', {
      scrollTrigger: {
        trigger: containerRef.value,
        start: 'top 80%',
        toggleActions: 'play none none reverse'
      },
      x: -30,
      opacity: 0,
      duration: 0.7,
      stagger: 0.15,
      ease: 'power3.out'
    })

    gsap.from(cardRef.value, {
      scrollTrigger: {
        trigger: containerRef.value,
        start: 'top 80%',
        toggleActions: 'play none none reverse'
      },
      scale: 0.9,
      opacity: 0,
      duration: 0.9,
      ease: 'back.out(1.4)'
    })
  }, containerRef.value)
})

onUnmounted(() => {
  if (ctx) ctx.revert()
})
</script>

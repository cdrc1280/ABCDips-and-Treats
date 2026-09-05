<template>
  <section ref="patisserieSectionRef" class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 my-20">
    <div class="relative bg-gradient-to-br from-[#1C130D] via-[#140D08] to-[#0D0704] text-[#FBF3E7] rounded-3xl p-8 sm:p-12 lg:p-16 border border-[#C08E5D]/30 shadow-2xl overflow-hidden">
      
      <!-- Ambient French Patisserie Glows -->
      <div class="absolute -top-40 right-10 w-[32rem] h-[32rem] bg-gradient-to-br from-[#D9A876]/15 via-[#C08E5D]/10 to-transparent rounded-full blur-3xl pointer-events-none transition-all duration-700" />
      <div class="absolute -bottom-40 left-10 w-[30rem] h-[30rem] bg-gradient-to-tr from-[#5C3A22]/25 to-transparent rounded-full blur-3xl pointer-events-none" />
      <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(217,168,118,0.06)_0%,transparent_60%)] pointer-events-none" />

      <!-- Section Header -->
      <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6 border-b border-[#C08E5D]/20 pb-8 relative z-10">
        <div class="space-y-2">
          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#D9A876]/15 border border-[#C08E5D]/30 text-xs font-bold text-[#E2C08A] tracking-widest uppercase backdrop-blur-md">
            <Sparkles class="w-3.5 h-3.5 text-[#E2C08A] animate-pulse" />
            <span>La Collection Gourmande • Patisserie Tradition</span>
          </div>
          <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#FBF3E7] tracking-tight font-serif">
            L'Art de la Pâtisserie
          </h2>
        </div>

        <p class="text-xs sm:text-sm text-[#E2C08A]/80 max-w-sm leading-relaxed font-sans">
          Every creation marries slow French lamination techniques with fresh local harvest and 100% pure creamery butter.
        </p>
      </div>

      <!-- Main Showcase Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center my-auto py-10 relative z-10">
        
        <!-- Left: Interactive Pastry Selection Dial (Animated List) -->
        <div class="lg:col-span-5 space-y-3 order-2 lg:order-1">
          <div
            v-for="(item, idx) in pastries"
            :key="item.id"
            @mouseenter="selectPastry(idx)"
            @click="selectPastry(idx)"
            :class="[
              'p-4 sm:p-5 rounded-2xl border transition-all duration-500 cursor-pointer flex items-center justify-between group relative overflow-hidden',
              activeIdx === idx
                ? 'bg-gradient-to-r from-white/10 to-white/5 border-[#D9A876] shadow-xl scale-102'
                : 'bg-white/3 border-[#C08E5D]/20 hover:border-[#C08E5D]/50 hover:bg-white/5 opacity-80 hover:opacity-100'
            ]"
          >
            <!-- Active Golden Accent Line -->
            <div
              class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-[#D9A876] to-[#C08E5D] transition-opacity duration-300"
              :class="activeIdx === idx ? 'opacity-100' : 'opacity-0'"
            />

            <div class="space-y-1 pl-2">
              <div class="flex items-center gap-2">
                <span class="text-[10px] font-mono font-bold text-[#E2C08A] uppercase tracking-wider">0{{ idx + 1 }}</span>
                <h3 class="text-sm sm:text-base font-bold text-[#FBF3E7] group-hover:text-[#E2C08A] transition-colors">
                  {{ item.name }}
                </h3>
              </div>
              <p class="text-xs text-[#E2C08A]/70 line-clamp-1">
                {{ item.notes }}
              </p>
            </div>

            <div class="text-right shrink-0">
              <span class="text-xs sm:text-sm font-mono font-extrabold text-[#E2C08A] block">
                {{ item.price }}
              </span>
              <span class="text-[10px] uppercase text-[#FBF3E7]/60">
                {{ item.badge }}
              </span>
            </div>
          </div>
        </div>

        <!-- Right: Animated Pastry Stage with 3D Depth & Shimmer -->
        <div class="lg:col-span-7 flex justify-center items-center relative order-1 lg:order-2 perspective-[1200px]">
          
          <div
            ref="card3dRef"
            @mousemove="handleCardMove"
            @mouseleave="handleCardLeave"
            class="relative w-full max-w-lg aspect-4/3 sm:aspect-16/10 rounded-3xl p-6 bg-gradient-to-b from-white/10 via-[#1C1410]/80 to-[#0F0A07]/95 border border-[#C08E5D]/40 shadow-2xl backdrop-blur-2xl transition-transform duration-300 ease-out will-change-transform flex items-center justify-center overflow-hidden group"
          >
            <!-- Radial Spotlight -->
            <div class="absolute w-72 h-72 rounded-full bg-[radial-gradient(circle,rgba(217,168,118,0.25)_0%,transparent_70%)] blur-2xl pointer-events-none" />

            <!-- Dynamic Active Pastry Image -->
            <div class="relative z-10 w-full h-full rounded-2xl overflow-hidden shadow-2xl border border-[#C08E5D]/30">
              <img
                :src="activePastry.image"
                :alt="activePastry.name"
                class="w-full h-full object-cover rounded-2xl transform transition-transform duration-700 ease-out group-hover:scale-106"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-[#0F0A07]/90 via-[#1C1410]/30 to-transparent pointer-events-none" />

              <!-- Floating French Craft Tag -->
              <div class="absolute top-4 left-4 inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-[#0F0A07]/85 border border-[#C08E5D]/40 text-xs font-bold text-[#E2C08A] backdrop-blur-md shadow-lg">
                <Sparkles class="w-3.5 h-3.5 text-[#E2C08A]" />
                <span>{{ activePastry.accentTag }}</span>
              </div>

              <!-- Bottom Tasting Notes Card -->
              <div class="absolute bottom-4 left-4 right-4 p-4 rounded-xl bg-[#0F0A07]/90 border border-[#C08E5D]/40 backdrop-blur-md flex items-center justify-between">
                <div>
                  <div class="text-[10px] font-mono text-[#E2C08A] uppercase tracking-wider">Flavor Profile</div>
                  <div class="text-xs sm:text-sm font-bold text-[#FBF3E7]">{{ activePastry.flavorProfile }}</div>
                </div>
                <RouterLink
                  :to="activePastry.url"
                  class="bg-gradient-to-r from-[#D9A876] to-[#C08E5D] text-[#1C1410] px-4 py-2 rounded-xl text-xs font-extrabold hover:opacity-95 transition-all flex items-center gap-1.5 shrink-0"
                >
                  <span>Order</span>
                  <ArrowRight class="w-3.5 h-3.5" />
                </RouterLink>
              </div>
            </div>

          </div>
        </div>

      </div>

    </div>
  </section>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Sparkles, ArrowRight } from 'lucide-vue-next'

const patisserieSectionRef = ref(null)
const card3dRef = ref(null)
const activeIdx = ref(0)
let rafId = null

const pastries = [
  {
    id: 'banana-loaf',
    name: 'Cavendish Banana Bread Loaf',
    price: '₱280.00',
    notes: 'Ripe organic Cavendish, Normandy-style butter, roasted walnuts',
    badge: 'Signature',
    accentTag: '100% Real Creamery Butter',
    flavorProfile: 'Warm Honey, Caramelized Banana & Cinnamon',
    image: '/images/blog-banana-bread.jpg',
    url: '/shop?search=banana'
  },
  {
    id: 'belgian-brownies',
    name: 'Belgian Dark Ganache Brownies',
    price: '₱340.00',
    notes: '70% Callebaut single-origin cacao, flaky Maldon salt, molten fudgy core',
    badge: 'Ultra-Fudge',
    accentTag: 'Single-Origin Belgian Chocolate',
    flavorProfile: 'Bittersweet Ganache & Sea Salt Crust',
    image: '/images/blog-banana-bread.jpg',
    url: '/shop?category=fudge-brownies'
  },
  {
    id: 'ube-cheesecake',
    name: 'Signature Basque Ube Cheesecake',
    price: '₱680.00',
    notes: 'Pure Philippine Ube Halaya, velvety cream cheese, toasted coconut base',
    badge: 'Chef Special',
    accentTag: 'Fresh Daily in Limited Batches',
    flavorProfile: 'Silky Taro Cream & Burnt Caramel Crown',
    image: '/images/blog-custom-cake.jpg',
    url: '/products/signature-ube-cheesecake-6-inch'
  },
  {
    id: 'cinnamon-buns',
    name: 'Flaky Cinnamon Brioche Rolls',
    price: '₱290.00',
    notes: '72-layer laminated brioche dough, Saigon cinnamon, cream cheese drizzle',
    badge: 'Morning Fresh',
    accentTag: '72-Layer Laminated Brioche',
    flavorProfile: 'Spiced Saigon Cinnamon & Sweet Cream Glaze',
    image: '/images/blog-banana-bread.jpg',
    url: '/shop?search=cinnamon'
  }
]

const activePastry = computed(() => pastries[activeIdx.value])

function selectPastry(index) {
  activeIdx.value = index
}

function handleCardMove(e) {
  if (!card3dRef.value) return
  if (rafId) cancelAnimationFrame(rafId)

  rafId = requestAnimationFrame(() => {
    if (!card3dRef.value) return
    const rect = card3dRef.value.getBoundingClientRect()
    const x = e.clientX - rect.left - rect.width / 2
    const y = e.clientY - rect.top - rect.height / 2
    
    const rotateX = -(y / (rect.height / 2)) * 10
    const rotateY = (x / (rect.width / 2)) * 10
    
    card3dRef.value.style.transform = `perspective(1200px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`
  })
}

function handleCardLeave() {
  if (card3dRef.value) {
    card3dRef.value.style.transform = 'perspective(1200px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)'
  }
}
</script>

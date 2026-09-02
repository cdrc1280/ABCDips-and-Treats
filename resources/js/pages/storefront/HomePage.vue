<template>
    <div ref="mainHomeRef" class="space-y-16 md:space-y-28 pb-20 overflow-hidden">

        <!-- 1. Hero Section (Artisanal Luxury & Kinetic Typography) -->
        <section class="hero-section max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="relative bg-gradient-to-br from-[#2D1B10] via-[#1C1410] to-[#120B07] text-surface rounded-3xl overflow-hidden shadow-2xl p-8 sm:p-12 lg:p-16 border border-[#C08E5D]/30">
                
                <!-- Ambient Warm Bakery Diffusion -->
                <div class="absolute -top-32 -right-32 w-96 h-96 bg-[#D9A876]/15 rounded-full blur-3xl pointer-events-none" />
                <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-[#C08E5D]/15 rounded-full blur-3xl pointer-events-none" />

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center relative z-10">

                    <!-- Hero Left Column -->
                    <div class="lg:col-span-7 space-y-6 text-left hero-text-col">
                        <div class="hero-badge inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#D9A876]/15 border border-[#C08E5D]/30 text-xs font-bold text-[#E2C08A] tracking-wider uppercase backdrop-blur-md">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
                            <span>{{ homeContent.hero_badge }}</span>
                        </div>

                        <h1 class="hero-title text-4xl sm:text-5xl lg:text-6xl font-black text-[#FBF3E7] tracking-tight leading-[1.12]">
                            {{ homeContent.hero_title }} <br class="hidden sm:inline" />
                            <span class="font-['Caveat'] text-[#E2C08A] font-normal text-4xl sm:text-5xl lg:text-6xl block mt-1">
                                {{ homeContent.hero_subtitle }}
                            </span>
                        </h1>

                        <p class="hero-desc text-[#FBF3E7]/85 text-base sm:text-lg max-w-xl leading-relaxed whitespace-pre-line font-medium">
                            {{ homeContent.hero_description }}
                        </p>

                        <!-- CTA Actions -->
                        <div class="hero-cta flex flex-wrap gap-4 pt-2">
                            <RouterLink :to="homeContent.hero_btn_primary_url || '/shop'">
                                <button v-tooltip="'Explore all handcrafted treats & pastries'"
                                    class="bg-gradient-to-r from-[#D9A876] to-[#C08E5D] text-[#1C1410] px-8 py-4 rounded-2xl font-extrabold text-sm hover:opacity-95 hover:shadow-lg hover:shadow-[#C08E5D]/25 transition-all duration-300 flex items-center gap-2 transform active:scale-98">
                                    <span>{{ homeContent.hero_btn_primary_text }}</span>
                                    <ArrowRight class="w-4 h-4 text-[#1C1410]" />
                                </button>
                            </RouterLink>

                            <RouterLink :to="homeContent.hero_btn_secondary_url || '/best-sellers'">
                                <button v-tooltip="'View top customer favorite pastries'"
                                    class="bg-white/5 border border-[#C08E5D]/40 text-[#FBF3E7] px-7 py-4 rounded-2xl font-bold text-sm hover:bg-white/10 hover:border-[#E2C08A] transition-all duration-300 flex items-center gap-2">
                                    <Sparkles class="w-4 h-4 text-[#E2C08A]" />
                                    <span>{{ homeContent.hero_btn_secondary_text }}</span>
                                </button>
                            </RouterLink>
                        </div>

                        <!-- Trust Highlights -->
                        <div class="hero-bullets pt-6 border-t border-[#C08E5D]/20 flex flex-wrap gap-6 text-xs text-[#E2C08A] font-semibold">
                            <div v-if="homeContent.hero_bullet_1" class="flex items-center gap-2">
                                <CheckCircle2 class="w-4 h-4 text-emerald-400" />
                                <span>{{ homeContent.hero_bullet_1 }}</span>
                            </div>
                            <div v-if="homeContent.hero_bullet_2" class="flex items-center gap-2">
                                <CheckCircle2 class="w-4 h-4 text-emerald-400" />
                                <span>{{ homeContent.hero_bullet_2 }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Hero Right Showcase Card (3D Tilt & Light Glass) -->
                    <div class="lg:col-span-5 flex justify-center hero-card-col">
                        <div class="w-full max-w-sm bg-white/5 dark:bg-[#1C1410]/80 rounded-3xl p-5 shadow-2xl border border-[#C08E5D]/30 backdrop-blur-xl transform hover:scale-[1.02] transition-transform duration-500">
                            <div class="relative bg-black/30 rounded-2xl p-2 mb-4 overflow-hidden aspect-4/3 border border-[#C08E5D]/20">
                                <img :src="homeContent.hero_card_image || '/images/blog-banana-bread.jpg'" 
                                     :alt="homeContent.hero_card_title"
                                     class="w-full h-full object-cover rounded-xl transition-transform duration-700 ease-out hover:scale-108" />
                                
                                <div class="absolute top-4 right-4 bg-[#1C1410]/80 backdrop-blur-md px-3 py-1 rounded-full text-[11px] font-extrabold text-[#E2C08A] border border-[#C08E5D]/30">
                                    Bestseller
                                </div>
                            </div>
                            
                            <div class="bg-gradient-to-r from-[#2A1C13] to-[#1C1410] text-surface p-4 rounded-2xl border border-[#C08E5D]/30 shadow-inner">
                                <div class="text-[11px] font-extrabold text-[#E2C08A] uppercase tracking-wider">
                                    {{ homeContent.hero_card_badge }}
                                </div>
                                <div class="text-base font-extrabold text-[#FBF3E7] mt-0.5">{{ homeContent.hero_card_title }}</div>
                                <div class="text-xs text-[#E2C08A]/80 mt-0.5">{{ homeContent.hero_card_subtitle }}</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- 2. Categories Pill Bar (Balanced Centered Grid) -->
        <section class="category-section max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
                <div>
                    <span class="font-['Caveat'] text-[#C08E5D] dark:text-[#E2C08A] text-xl sm:text-2xl">explore by category</span>
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-[#1C1410] dark:text-[#FBF3E7] tracking-tight">Freshly Baked Menu</h2>
                </div>
                <RouterLink to="/shop" class="group text-xs sm:text-sm font-bold text-[#5C3A22] dark:text-[#E2C08A] hover:underline flex items-center gap-1.5 transition-all">
                    <span>View All Collections</span>
                    <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                </RouterLink>
            </div>

            <!-- Perfectly Aligned Responsive 4-Column / Multi-Column Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                <RouterLink v-for="cat in categories" :key="cat.id" :to="`/shop?category=${cat.slug}`"
                    class="category-item group relative bg-white dark:bg-[#1E1510] p-5 sm:p-6 rounded-3xl border border-[#C08E5D]/25 dark:border-[#C08E5D]/30 hover:border-[#C08E5D] dark:hover:border-[#E2C08A] text-center shadow-xs hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col items-center justify-between overflow-hidden">
                    
                    <!-- Ambient Hover Glow -->
                    <span class="absolute inset-0 bg-gradient-to-b from-[#D9A876]/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none" />

                    <!-- Distinctive Category Vector Icon -->
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-[#D9A876]/15 dark:bg-[#2A1C13] mb-3.5 flex items-center justify-center text-[#5C3A22] dark:text-[#E2C08A] group-hover:scale-110 group-hover:bg-[#D9A876]/30 dark:group-hover:bg-[#3B281B] transition-all duration-300 overflow-hidden border border-[#C08E5D]/20 shadow-xs shrink-0">
                        <img v-if="cat.image_url" :src="cat.image_url" :alt="cat.name" class="w-full h-full object-cover rounded-xl" />
                        <component v-else :is="getCategoryIcon(cat.slug)" class="w-7 h-7 sm:w-8 sm:h-8 text-[#C08E5D] dark:text-[#E2C08A] transition-colors" />
                    </div>

                    <!-- Category Title (Full text, no awkward truncation) -->
                    <h3 class="font-extrabold text-sm sm:text-base text-[#1C1410] dark:text-[#FBF3E7] group-hover:text-[#5C3A22] dark:group-hover:text-[#E2C08A] text-center leading-snug transition-colors line-clamp-2 min-h-[2.5rem] flex items-center justify-center">
                        {{ cat.name }}
                    </h3>

                    <!-- Item Count Pill -->
                    <div class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full bg-[#F5E8D0] dark:bg-[#2A1C13] border border-[#C08E5D]/20 text-[11px] font-bold text-[#8C7A68] dark:text-[#C5B4A4]">
                        {{ cat.products_count || 0 }} items
                    </div>
                </RouterLink>
            </div>
        </section>

        <!-- 3. Bespoke Artisanal Banana Bread Interactive Feature (The Showstopper) -->
        <section class="banana-bread-section max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <BananaBreadShowcase />
        </section>

        <!-- 4. Featured Pastries Grid (Scroll Stagger) -->
        <section class="featured-section max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <span class="font-['Caveat'] text-[#C08E5D] dark:text-[#E2C08A] text-xl">baker's recommendation</span>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-[#1C1410] dark:text-[#FBF3E7] tracking-tight">Featured Pastry Collection</h2>
                </div>
                <RouterLink to="/featured" class="text-xs font-bold text-[#5C3A22] dark:text-[#E2C08A] hover:underline flex items-center gap-1">
                    <span>See All Featured</span>
                    <ArrowRight class="w-3.5 h-3.5" />
                </RouterLink>
            </div>

            <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <SkeletonCard v-for="n in 4" :key="n" />
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div v-for="product in featuredProducts" :key="product.id" class="featured-product-card">
                    <ProductCard :product="product" />
                </div>
            </div>
        </section>

        <!-- Account Perks Banner (Guests Only) -->
        <AccountPerksDrawer v-if="!authStore.isAuthenticated" />

        <!-- 5. Baker's Spotlight Banner (Parallax & Depth) -->
        <section class="spotlight-section max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative bg-gradient-to-r from-[#2A1C13] via-[#1C1410] to-[#20150E] rounded-3xl p-8 md:p-14 text-surface overflow-hidden shadow-2xl border border-[#C08E5D]/30">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-12 items-center relative z-10">
                    <div class="md:col-span-7 space-y-5 text-left">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#D9A876]/15 border border-[#C08E5D]/30 text-xs font-bold text-[#E2C08A]">
                            <Sparkles class="w-3.5 h-3.5" />
                            <span>{{ homeContent.spotlight_tagline }}</span>
                        </div>
                        <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-[#FBF3E7] tracking-tight leading-tight">
                            {{ homeContent.spotlight_title }}
                        </h2>
                        <p class="text-[#FBF3E7]/80 text-sm md:text-base leading-relaxed whitespace-pre-line max-w-xl">
                            {{ homeContent.spotlight_description }}
                        </p>
                        <div class="pt-2">
                            <RouterLink :to="homeContent.spotlight_btn_url || '/shop'">
                                <button class="bg-gradient-to-r from-[#D9A876] to-[#C08E5D] text-[#1C1410] px-7 py-3.5 rounded-2xl font-extrabold text-sm hover:opacity-95 shadow-lg transition-all duration-300 flex items-center gap-2">
                                    <ShoppingBag class="w-4 h-4 text-[#1C1410]" />
                                    <span>{{ homeContent.spotlight_btn_text }}</span>
                                </button>
                            </RouterLink>
                        </div>
                    </div>

                    <div class="md:col-span-5 flex justify-center">
                        <div class="w-64 h-64 md:w-80 md:h-80 rounded-3xl bg-black/40 border border-[#C08E5D]/30 p-3 shadow-2xl overflow-hidden group">
                            <img :src="homeContent.spotlight_image || '/images/blog-custom-cake.jpg'" 
                                 :alt="homeContent.spotlight_title"
                                 class="w-full h-full object-cover rounded-2xl shadow-md transition-transform duration-700 ease-out group-hover:scale-108" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. Best Sellers Grid (Staggered On Scroll) -->
        <section class="bestsellers-section max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <span class="font-['Caveat'] text-[#C08E5D] dark:text-[#E2C08A] text-xl">most popular choice</span>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-[#1C1410] dark:text-[#FBF3E7] tracking-tight">Best Selling Favorites</h2>
                </div>
                <RouterLink to="/best-sellers" class="text-xs font-bold text-[#5C3A22] dark:text-[#E2C08A] hover:underline flex items-center gap-1">
                    <span>View All Best Sellers</span>
                    <ArrowRight class="w-3.5 h-3.5" />
                </RouterLink>
            </div>

            <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <SkeletonCard v-for="n in 4" :key="n" />
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div v-for="product in bestSellers" :key="product.id" class="bestseller-product-card">
                    <ProductCard :product="product" />
                </div>
            </div>
        </section>

        <SectionDivider />

        <!-- 7. Verified Store Reviews & Customer Voice -->
        <section class="reviews-section max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <StoreReviews />
        </section>

    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, inject, nextTick } from 'vue'
import { ArrowRight, Sparkles, CheckCircle2, ShoppingBag, Cake } from 'lucide-vue-next'
import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import SectionDivider from '@/components/ui/SectionDivider.vue'
import SkeletonCard from '@/components/ui/SkeletonCard.vue'
import ProductCard from '@/components/storefront/ProductCard.vue'
import StoreReviews from '@/components/storefront/StoreReviews.vue'
import AccountPerksDrawer from '@/components/storefront/AccountPerksDrawer.vue'
import BananaBreadShowcase from '@/components/storefront/BananaBreadShowcase.vue'
import { useAuthStore } from '@/stores/auth'

gsap.registerPlugin(ScrollTrigger)

const authStore = useAuthStore()
const axios = inject('axios')
const categories = ref([])
const featuredProducts = ref([])
const bestSellers = ref([])
const loading = ref(true)
const mainHomeRef = ref(null)
let gsapCtx = null

const homeContent = ref({
    hero_badge: 'OVEN FRESH TODAY IN CAVITE',
    hero_title: 'Handcrafted Pastries',
    hero_subtitle: 'baked with love & real butter',
    hero_description: 'From our famous Classic Banana Bread Loaves and ultra-fudgy Belgian chocolate brownies to cheesecakes and fresh cinnamon rolls.',
    hero_btn_primary_text: 'Browse Full Menu',
    hero_btn_primary_url: '/shop',
    hero_btn_secondary_text: 'Explore Best Sellers',
    hero_btn_secondary_url: '/best-sellers',
    hero_bullet_1: 'Same-day & Scheduled Delivery',
    hero_bullet_2: '100% Real Creamery Butter',
    hero_card_badge: 'Signature Treat',
    hero_card_title: 'Classic Banana Bread',
    hero_card_subtitle: 'Starts at ₱280.00',
    hero_card_image: null,

    spotlight_tagline: 'weekly special spotlight',
    spotlight_title: 'Signature Ube Cheesecake',
    spotlight_description: 'Real Philippine Ube Halaya folded into silky baked cream cheese set over a coconut Graham crust. Baked fresh in limited batches.',
    spotlight_btn_text: 'Order Spotlight Treat — ₱680.00',
    spotlight_btn_url: '/products/signature-ube-cheesecake-6-inch',
    spotlight_image: null,
})

function getCategoryIcon(slug) {
    const s = (slug || '').toLowerCase()
    if (s.includes('banana') || s.includes('bread') || s.includes('loaf')) return Wheat
    if (s.includes('cookie')) return CircleDot
    if (s.includes('brownie') || s.includes('dip')) return Layers
    if (s.includes('cake') || s.includes('cheese')) return Cake
    return ShoppingBag
}

function initScrollAnimations() {
    if (gsapCtx) gsapCtx.revert()
    
    gsapCtx = gsap.context(() => {
        // Hero Kinetic Reveal
        const heroTl = gsap.timeline({ defaults: { ease: 'power3.out' } })
        heroTl.from('.hero-badge', { y: 20, opacity: 0, duration: 0.6, delay: 0.1 })
              .from('.hero-title', { y: 30, opacity: 0, duration: 0.8 }, '-=0.4')
              .from('.hero-desc', { y: 20, opacity: 0, duration: 0.6 }, '-=0.5')
              .from('.hero-cta', { y: 20, opacity: 0, duration: 0.6 }, '-=0.4')
              .from('.hero-bullets', { opacity: 0, duration: 0.5 }, '-=0.3')
              .from('.hero-card-col', { scale: 0.92, opacity: 0, duration: 0.9, ease: 'back.out(1.2)' }, '-=0.7')

        // Category Cards Stagger
        gsap.from('.category-item', {
            scrollTrigger: {
                trigger: '.category-section',
                start: 'top 88%',
                once: true
            },
            y: 30,
            opacity: 0,
            duration: 0.6,
            stagger: 0.08,
            ease: 'power3.out',
            clearProps: 'all'
        })

        // Featured Products Stagger
        gsap.from('.featured-product-card', {
            scrollTrigger: {
                trigger: '.featured-section',
                start: 'top 80%',
                toggleActions: 'play none none reverse'
            },
            y: 40,
            opacity: 0,
            duration: 0.7,
            stagger: 0.12,
            ease: 'power3.out'
        })

        // Spotlight Section Reveal
        gsap.from('.spotlight-section', {
            scrollTrigger: {
                trigger: '.spotlight-section',
                start: 'top 80%',
                toggleActions: 'play none none reverse'
            },
            y: 40,
            opacity: 0,
            duration: 0.8,
            ease: 'power3.out'
        })

        // Best Sellers Stagger
        gsap.from('.bestseller-product-card', {
            scrollTrigger: {
                trigger: '.bestsellers-section',
                start: 'top 80%',
                toggleActions: 'play none none reverse'
            },
            y: 40,
            opacity: 0,
            duration: 0.7,
            stagger: 0.12,
            ease: 'power3.out'
        })
    }, mainHomeRef.value)
}

async function fetchHomeContent() {
    try {
        const { data } = await axios.get('/api/home-content')
        if (data) {
            homeContent.value = { ...homeContent.value, ...data }
        }
    } catch (err) {
        console.warn('Using default home banner settings', err)
    }
}

async function loadData() {
    loading.value = true
    try {
        const [catRes, featRes, bestRes] = await Promise.all([
            axios.get('/api/categories'),
            axios.get('/api/products/featured?limit=4'),
            axios.get('/api/products/best-sellers?limit=4')
        ])
        categories.value = catRes.data.data
        featuredProducts.value = featRes.data.data
        bestSellers.value = bestRes.data.data
    } catch (err) {
        console.warn('Failed to load home catalog data', err)
    } finally {
        loading.value = false
        await nextTick()
        initScrollAnimations()
    }
}

onMounted(() => {
    fetchHomeContent()
    loadData()
})

onUnmounted(() => {
    if (gsapCtx) gsapCtx.revert()
})
</script>

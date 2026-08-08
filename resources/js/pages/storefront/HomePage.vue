<template>
    <div class="space-y-12 md:space-y-20 pb-16">

        <!-- 1. Hero Section (Clean, Warm & High-Contrast) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-brand-choco text-surface rounded-3xl overflow-hidden shadow-xl p-8 sm:p-12 lg:p-16 relative">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">

                    <!-- Hero Left Column -->
                    <div class="lg:col-span-7 space-y-6 text-left">
                        <div
                            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-tan/20 border border-brand-tan/30 text-xs font-bold text-brand-tan">
                            <span class="w-2.5 h-2.5 rounded-full bg-success animate-pulse" />
                            <span>{{ homeContent.hero_badge }}</span>
                        </div>

                        <h1
                            class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-[1.15]">
                            {{ homeContent.hero_title }} <br class="hidden sm:inline" />
                            <span
                                class="font-['Caveat'] text-brand-tan font-normal text-4xl sm:text-5xl lg:text-6xl block mt-1">
                                {{ homeContent.hero_subtitle }}
                            </span>
                        </h1>

                        <p class="text-surface/90 text-base sm:text-lg max-w-xl leading-relaxed whitespace-pre-line">
                            {{ homeContent.hero_description }}
                        </p>

                        <div class="flex flex-wrap gap-4 pt-2">
                            <RouterLink :to="homeContent.hero_btn_primary_url || '/shop'">
                                <button v-tooltip="'Explore all handcrafted treats & pastries'"
                                    class="bg-brand-tan text-ink px-7 py-3.5 rounded-2xl font-bold text-sm hover:bg-brand-caramel transition-colors shadow-md flex items-center gap-2">
                                    {{ homeContent.hero_btn_primary_text }}
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </button>
                            </RouterLink>

                            <RouterLink :to="homeContent.hero_btn_secondary_url || '/best-sellers'">
                                <button v-tooltip="'View top customer favorite pastries'"
                                    class="bg-transparent border border-brand-tan/60 text-surface px-7 py-3.5 rounded-2xl font-semibold text-sm hover:bg-brand-tan/20 transition-colors">
                                    {{ homeContent.hero_btn_secondary_text }}
                                </button>
                            </RouterLink>
                        </div>

                        <!-- Trust Highlights -->
                        <div
                            class="pt-6 border-t border-brand-tan/20 flex flex-wrap gap-6 text-xs text-surface/90 font-medium">
                            <div v-if="homeContent.hero_bullet_1" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-brand-tan" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>{{ homeContent.hero_bullet_1 }}</span>
                            </div>
                            <div v-if="homeContent.hero_bullet_2" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-brand-tan" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>{{ homeContent.hero_bullet_2 }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Hero Right Showcase Card -->
                    <div class="lg:col-span-5 flex justify-center">
                        <div
                            class="w-full max-w-sm bg-surface dark:bg-[#2A1C13] hero-showcase-card rounded-3xl p-6 shadow-2xl text-center border border-brand-caramel/30">
                            <div
                                class="bg-white dark:bg-[#1E130B] hero-showcase-inner rounded-2xl p-2 mb-4 shadow-inner flex items-center justify-center overflow-hidden aspect-4/3">
                                <img :src="homeContent.hero_card_image" :alt="homeContent.hero_card_title"
                                    class="w-full h-full object-cover rounded-xl transition-transform duration-500 hover:scale-105" />
                            </div>
                            <div
                                class="bg-brand-choco text-surface p-4 rounded-2xl text-left shadow-sm border border-brand-tan/30">
                                <div class="text-[11px] font-bold text-brand-tan uppercase tracking-wider">
                                    {{ homeContent.hero_card_badge }}
                                </div>
                                <div class="text-base font-bold text-white">{{ homeContent.hero_card_title }}</div>
                                <div class="text-xs text-surface/80 mt-0.5">{{ homeContent.hero_card_subtitle }}</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- 2. Categories Pill Bar -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <span class="font-['Caveat'] text-brand-caramel text-xl">explore by category</span>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-ink">Freshly Baked Categories</h2>
                </div>
                <RouterLink to="/shop"
                    class="text-xs font-bold text-brand-choco hover:underline flex items-center gap-1">
                    View All →
                </RouterLink>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                <RouterLink v-for="cat in categories" :key="cat.id" :to="`/shop?category=${cat.slug}`"
                    class="group bg-white p-4 rounded-2xl border border-brand-caramel/20 text-center hover:shadow-md hover:border-brand-choco transition-all duration-200">
                    <div
                        class="w-12 h-12 rounded-xl bg-brand-tan/20 category-card-icon mx-auto mb-3 flex items-center justify-center text-brand-choco group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-xs text-ink group-hover:text-brand-choco line-clamp-1 mb-0.5">{{
                        cat.name }}</h3>
                    <span class="text-[10px] text-warm-gray">{{ cat.products_count || 0 }} items</span>
                </RouterLink>
            </div>
        </section>

        <SectionDivider />

        <!-- 3. Featured Products Grid -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <span class="font-['Caveat'] text-brand-caramel text-xl">baker's recommendation</span>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-ink">Featured Pastry Collection</h2>
                </div>
                <RouterLink to="/featured" class="text-xs font-bold text-brand-choco hover:underline">
                    See All Featured →
                </RouterLink>
            </div>

            <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <SkeletonCard v-for="n in 4" :key="n" />
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <ProductCard v-for="product in featuredProducts" :key="product.id" :product="product" />
            </div>
        </section>

        <!-- Account Perks Banner (Guests Only) -->
        <AccountPerksDrawer v-if="!authStore.isAuthenticated" />

        <!-- 4. Baker's Spotlight Banner -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div
                class="bg-brand-choco rounded-3xl p-8 md:p-12 text-surface relative overflow-hidden shadow-xl border border-brand-caramel/30">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center relative z-10">
                    <div class="space-y-4">
                        <span class="font-['Caveat'] text-brand-tan text-2xl">{{ homeContent.spotlight_tagline }}</span>
                        <h2 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight">
                            {{ homeContent.spotlight_title }}
                        </h2>
                        <p class="text-surface/90 text-sm md:text-base leading-relaxed whitespace-pre-line">
                            {{ homeContent.spotlight_description }}
                        </p>
                        <div class="pt-2">
                            <RouterLink :to="homeContent.spotlight_btn_url || '/shop'">
                                <button
                                    class="bg-brand-tan text-ink px-6 py-3.5 rounded-2xl font-bold text-sm hover:bg-brand-caramel transition-colors shadow-md">
                                    {{ homeContent.spotlight_btn_text }}
                                </button>
                            </RouterLink>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <div
                            class="w-64 h-64 md:w-80 md:h-80 rounded-2xl bg-white dark:bg-[#120B07] border border-brand-tan/40 p-3 flex items-center justify-center shadow-lg overflow-hidden">
                            <img :src="homeContent.spotlight_image" :alt="homeContent.spotlight_title"
                                class="w-full h-full object-cover rounded-xl shadow-md transition-transform duration-500 hover:scale-105" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. Best Sellers Section -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <span class="font-['Caveat'] text-brand-caramel text-xl">most popular choice</span>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-ink">Best Selling Favorites</h2>
                </div>
                <RouterLink to="/best-sellers" class="text-xs font-bold text-brand-choco hover:underline">
                    View All Best Sellers →
                </RouterLink>
            </div>

            <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <SkeletonCard v-for="n in 4" :key="n" />
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <ProductCard v-for="product in bestSellers" :key="product.id" :product="product" />
            </div>
        </section>

        <SectionDivider />

        <!-- 6. Store & Service Customer Reviews -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <StoreReviews />
        </section>

    </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import SectionDivider from '@/components/ui/SectionDivider.vue'
import SkeletonCard from '@/components/ui/SkeletonCard.vue'
import ProductCard from '@/components/storefront/ProductCard.vue'
import StoreReviews from '@/components/storefront/StoreReviews.vue'
import AccountPerksDrawer from '@/components/storefront/AccountPerksDrawer.vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const axios = inject('axios')
const categories = ref([])
const featuredProducts = ref([])
const bestSellers = ref([])
const loading = ref(true)

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
    hero_card_image: '/images/blog-banana-bread.jpg',

    spotlight_tagline: 'weekly special spotlight',
    spotlight_title: 'Signature Ube Cheesecake',
    spotlight_description: 'Real Philippine Ube Halaya folded into silky baked cream cheese set over a coconut Graham crust. Baked fresh in limited batches.',
    spotlight_btn_text: 'Order Spotlight Treat — ₱680.00',
    spotlight_btn_url: '/products/signature-ube-cheesecake-6-inch',
    spotlight_image: '/images/blog-custom-cake.jpg',
})

async function fetchHomeContent() {
    try {
        const { data } = await axios.get('/api/home-content')
        if (data) {
            homeContent.value = { ...homeContent.value, ...data }
        }
    } catch (err) {
        console.error('Failed to fetch home banner settings', err)
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
        console.error('Failed to load home page data', err)
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    fetchHomeContent()
    loadData()
})
</script>

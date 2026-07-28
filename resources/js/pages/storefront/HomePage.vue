<template>
    <div class="space-y-12 md:space-y-20 pb-16">

        <!-- 1. Hero Section (Clean, Warm & High-Contrast) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-[#5C3A22] text-[#FBF3E7] rounded-3xl overflow-hidden shadow-xl p-8 sm:p-12 lg:p-16 relative">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">

                    <!-- Hero Left Column -->
                    <div class="lg:col-span-7 space-y-6 text-left">
                        <div
                            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#D9A876]/20 border border-[#D9A876]/30 text-xs font-bold text-[#D9A876]">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#6B8F5E] animate-pulse" />
                            <span>OVEN FRESH TODAY IN CAVITE</span>
                        </div>

                        <h1
                            class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-[1.15]">
                            Handcrafted Pastries <br class="hidden sm:inline" />
                            <span
                                class="font-['Caveat'] text-[#D9A876] font-normal text-4xl sm:text-5xl lg:text-6xl block mt-1">baked
                                with love &amp; real butter</span>
                        </h1>

                        <p class="text-[#FBF3E7]/90 text-base sm:text-lg max-w-xl leading-relaxed">
                            From our famous Classic Banana Bread Loaves and ultra-fudgy Belgian chocolate brownies to
                            artisanal cheesecakes and fresh cinnamon rolls.
                        </p>

                        <div class="flex flex-wrap gap-4 pt-2">
                            <RouterLink to="/shop">
                                <button
                                    class="bg-[#D9A876] text-[#1C1410] px-7 py-3.5 rounded-2xl font-bold text-sm hover:bg-[#C08E5D] transition-colors shadow-md flex items-center gap-2">
                                    Browse Full Menu
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </button>
                            </RouterLink>

                            <RouterLink to="/best-sellers">
                                <button
                                    class="bg-transparent border border-[#D9A876]/60 text-[#FBF3E7] px-7 py-3.5 rounded-2xl font-semibold text-sm hover:bg-[#D9A876]/20 transition-colors">
                                    Explore Best Sellers
                                </button>
                            </RouterLink>
                        </div>

                        <!-- Trust Highlights -->
                        <div
                            class="pt-6 border-t border-[#D9A876]/20 flex flex-wrap gap-6 text-xs text-[#FBF3E7]/90 font-medium">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#D9A876]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Same-day &amp; Scheduled Delivery</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#D9A876]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>100% Real Creamery Butter</span>
                            </div>
                        </div>
                    </div>

                    <!-- Hero Right Showcase Card -->
                    <div class="lg:col-span-5 flex justify-center">
                        <div
                            class="w-full max-w-sm bg-[#FBF3E7] rounded-3xl p-6 shadow-2xl text-center border border-[#C08E5D]/30">
                            <div class="bg-white rounded-2xl p-6 mb-4 shadow-inner flex items-center justify-center">
                                <img src="/images/logo.png" alt="ABCDips & Treats Signature Pastries"
                                    class="h-44 sm:h-52 w-auto object-contain" />
                            </div>
                            <div class="bg-[#5C3A22] text-[#FBF3E7] p-4 rounded-2xl text-left shadow-sm">
                                <div class="text-[11px] font-bold text-[#D9A876] uppercase tracking-wider">Signature
                                    Treat</div>
                                <div class="text-base font-bold text-white">Classic Banana Bread</div>
                                <div class="text-xs text-[#FBF3E7]/80 mt-0.5">Starts at ₱280.00</div>
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
                    <span class="font-['Caveat'] text-[#C08E5D] text-xl">explore by category</span>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-[#1C1410]">Freshly Baked Categories</h2>
                </div>
                <RouterLink to="/shop" class="text-xs font-bold text-[#5C3A22] hover:underline flex items-center gap-1">
                    View All →
                </RouterLink>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                <RouterLink v-for="cat in categories" :key="cat.id" :to="`/shop?category=${cat.slug}`"
                    class="group bg-white p-4 rounded-2xl border border-[#C08E5D]/20 text-center hover:shadow-md hover:border-[#5C3A22] transition-all duration-200">
                    <div
                        class="w-12 h-12 rounded-xl bg-[#D9A876]/20 mx-auto mb-3 flex items-center justify-center text-[#5C3A22] group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-xs text-[#1C1410] group-hover:text-[#5C3A22] line-clamp-1 mb-0.5">{{
                        cat.name }}</h3>
                    <span class="text-[10px] text-[#8C7A68]">{{ cat.products_count || 0 }} items</span>
                </RouterLink>
            </div>
        </section>

        <SectionDivider />

        <!-- 3. Featured Products Grid -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <span class="font-['Caveat'] text-[#C08E5D] text-xl">baker's recommendation</span>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-[#1C1410]">Featured Pastry Collection</h2>
                </div>
                <RouterLink to="/featured" class="text-xs font-bold text-[#5C3A22] hover:underline">
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

        <!-- 4. Baker's Spotlight Banner -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div
                class="bg-[#1C1410] rounded-3xl p-8 md:p-12 text-[#FBF3E7] relative overflow-hidden shadow-xl border border-[#C08E5D]/30">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center relative z-10">
                    <div class="space-y-4">
                        <span class="font-['Caveat'] text-[#D9A876] text-2xl">weekly special spotlight</span>
                        <h2 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight">Signature Ube
                            Cheesecake</h2>
                        <p class="text-[#FBF3E7]/90 text-sm md:text-base leading-relaxed">
                            Real Philippine Ube Halaya folded into silky baked cream cheese set over a coconut Graham
                            crust. Baked fresh in limited batches.
                        </p>
                        <div class="pt-2">
                            <RouterLink to="/products/signature-ube-cheesecake-6-inch">
                                <button
                                    class="bg-[#D9A876] text-[#1C1410] px-6 py-3.5 rounded-2xl font-bold text-sm hover:bg-[#C08E5D] transition-colors shadow-md">
                                    Order Spotlight Treat — ₱680.00
                                </button>
                            </RouterLink>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <div
                            class="w-64 h-64 md:w-80 md:h-80 rounded-2xl bg-[#FBF3E7] border border-[#D9A876]/40 p-6 flex items-center justify-center shadow-lg">
                            <img src="/images/logo.png" alt="Signature Ube Cheesecake"
                                class="max-h-full object-contain" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. Best Sellers Section -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <span class="font-['Caveat'] text-[#C08E5D] text-xl">most popular choice</span>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-[#1C1410]">Best Selling Favorites</h2>
                </div>
                <RouterLink to="/best-sellers" class="text-xs font-bold text-[#5C3A22] hover:underline">
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

    </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import SectionDivider from '@/components/ui/SectionDivider.vue'
import SkeletonCard from '@/components/ui/SkeletonCard.vue'
import ProductCard from '@/components/storefront/ProductCard.vue'

const axios = inject('axios')
const categories = ref([])
const featuredProducts = ref([])
const bestSellers = ref([])
const loading = ref(true)

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

onMounted(() => loadData())
</script>

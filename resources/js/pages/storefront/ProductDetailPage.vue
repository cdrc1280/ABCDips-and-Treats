<template>
    <div class="page-container py-10 md:py-16">
        <!-- Skeleton Loading -->
        <div v-if="loading" class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <SkeletonBlock height="450px" radius="1.5rem" />
            <div class="space-y-6">
                <SkeletonBlock height="2rem" width="40%" radius="0.5rem" />
                <SkeletonBlock height="3rem" width="80%" radius="0.5rem" />
                <SkeletonBlock height="1.5rem" width="30%" radius="0.5rem" />
                <SkeletonText :lines="4" />
                <SkeletonBlock height="3.5rem" radius="1rem" />
            </div>
        </div>

        <!-- Product Not Found -->
        <div v-else-if="!product" class="py-16 text-center">
            <EmptyState title="Product Not Found"
                description="The pastry you are looking for might have been moved or is no longer available.">
                <template #action>
                    <BaseButton variant="primary" @click="$router.push('/shop')">
                        Return to Shop
                    </BaseButton>
                </template>
            </EmptyState>
        </div>

        <!-- Main Product View -->
        <div v-else class="space-y-16">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-xs font-semibold text-warm-gray">
                <RouterLink to="/" class="hover:text-brand-choco">Home</RouterLink>
                <span>/</span>
                <RouterLink to="/shop" class="hover:text-brand-choco">Shop</RouterLink>
                <span>/</span>
                <span class="text-brand-choco">{{ product.category?.name }}</span>
            </nav>

            <!-- Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-14 items-start">

                <!-- Left Image Showcase -->
                <div class="space-y-4">
                    <div
                        class="aspect-square rounded-3xl overflow-hidden bg-white border border-brand-caramel/20 shadow-md relative select-none"
                        @touchstart="onTouchStart"
                        @touchmove="onTouchMove"
                        @touchend="onTouchEnd"
                    >
                        <div
                            class="flex h-full transition-transform duration-300 ease-out"
                            :style="{ transform: `translateX(-${allImages.length ? (activeIndex * 100) / allImages.length : 0}%)`, width: `${allImages.length * 100}%` }"
                        >
                            <div
                                v-for="(img, idx) in allImages"
                                :key="idx"
                                class="h-full shrink-0"
                                :style="{ width: `${100 / allImages.length}%` }"
                            >
                                <img
                                    :src="img"
                                    :alt="`${product.name} - Image ${idx + 1}`"
                                    class="w-full h-full object-cover object-center"
                                />
                            </div>
                        </div>

                        <!-- Left Arrow -->
                        <button
                            v-if="allImages.length > 1 && activeIndex > 0"
                            type="button"
                            class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center transition-all cursor-pointer shadow-md"
                            @click="prevImage"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" /></svg>
                        </button>

                        <!-- Right Arrow -->
                        <button
                            v-if="allImages.length > 1 && activeIndex < allImages.length - 1"
                            type="button"
                            class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center transition-all cursor-pointer shadow-md"
                            @click="nextImage"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg>
                        </button>

                        <!-- Dot Indicators -->
                        <div v-if="allImages.length > 1" class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-1.5 z-20">
                            <button
                                v-for="(_, idx) in allImages"
                                :key="idx"
                                type="button"
                                class="rounded-full transition-all cursor-pointer"
                                :class="activeIndex === idx ? 'w-6 h-2 bg-white shadow-sm' : 'w-2 h-2 bg-white/50 hover:bg-white/80'"
                                @click="activeIndex = idx"
                            />
                        </div>

                        <div class="absolute top-4 left-4 flex flex-col gap-2 z-10">
                            <BaseBadge v-if="product.is_best_seller" variant="brand">Best Seller</BaseBadge>
                            <BaseBadge v-else-if="product.is_highly_rated" variant="warning">⭐ Highly Rated</BaseBadge>
                            <BaseBadge v-if="product.is_on_sale" variant="error">Sale</BaseBadge>
                            <BaseBadge v-if="product.is_seasonal" variant="warning">Seasonal</BaseBadge>
                        </div>
                    </div>

                    <!-- Gallery Thumbnails -->
                    <div v-if="allImages.length > 1"
                        class="flex items-center gap-3 overflow-x-auto pb-2">
                        <button v-for="(img, idx) in allImages" :key="idx" type="button"
                            v-tooltip="`View photo ${idx + 1}`"
                            class="w-20 h-20 rounded-xl overflow-hidden border-2 transition-all shrink-0 cursor-pointer"
                            :class="activeIndex === idx ? 'border-brand-choco scale-95 shadow-md' : 'border-brand-caramel/20 opacity-70 hover:opacity-100'"
                            @click="activeIndex = idx">
                            <img :src="img" class="w-full h-full object-cover" />
                        </button>
                    </div>
                </div>

                <!-- Right Product Info Column -->
                <div class="space-y-6">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <span class="text-xs font-bold uppercase tracking-wider text-brand-caramel">
                                {{ product.category?.name }}
                            </span>
                            <a v-if="product.reviews_count && product.reviews_count > 0" href="#reviews"
                                v-tooltip="`Read all ${product.reviews_count} verified customer reviews`"
                                class="inline-flex items-center gap-1 bg-surface border border-brand-caramel/30 px-2.5 py-0.5 rounded-full text-xs font-extrabold text-brand-choco hover:bg-brand-tan/30 transition-colors">
                                <span>⭐ {{ product.avg_rating }}</span>
                                <span class="text-warm-gray font-normal">({{ product.reviews_count }} reviews)</span>
                            </a>
                        </div>

                        <h1 class="text-3xl md:text-4xl font-extrabold text-ink tracking-tight mb-3">
                            {{ product.name }}
                        </h1>

                        <!-- Price Row -->
                        <div class="flex flex-col gap-3 mb-4">
                            <div class="flex items-baseline gap-3">
                                <span class="text-3xl font-extrabold text-brand-choco">
                                    ₱{{ (product.is_on_sale && !isExpired ? product.sale_price : product.price).toFixed(2) }}
                                </span>
                                <span v-if="product.is_on_sale && !isExpired" class="text-lg text-warm-gray line-through">
                                    ₱{{ product.price.toFixed(2) }}
                                </span>
                                <BaseBadge v-if="product.is_on_sale && !isExpired" variant="error">Save ₱{{ (product.price - product.sale_price).toFixed(2) }}</BaseBadge>
                            </div>
                            
                            <!-- Full Countdown Display -->
                            <div v-if="product.is_on_sale && product.sale_ends_at && !isExpired"
                                 class="bg-surface border border-brand-caramel/20 p-4 rounded-2xl w-fit transition-all duration-300"
                                 :class="isNearExpiry ? 'animate-[pulse_1s_ease-in-out_infinite] border-red-300 shadow-[0_0_15px_rgba(239,68,68,0.3)]' : ''">
                                <div class="text-xs font-bold uppercase tracking-wider mb-2 flex items-center gap-1"
                                     :class="isNearExpiry ? 'text-red-600' : 'text-brand-choco'">
                                    <span>🔥</span> Sale ends in:
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex flex-col items-center bg-[#2A1C13] text-white rounded-lg px-3 py-1.5 min-w-[50px]">
                                        <span class="text-lg font-black leading-none">{{ String(days).padStart(2, '0') }}</span>
                                        <span class="text-[9px] text-brand-tan uppercase mt-1">days</span>
                                    </div>
                                    <span class="text-[#2A1C13] font-bold">:</span>
                                    <div class="flex flex-col items-center bg-[#2A1C13] text-white rounded-lg px-3 py-1.5 min-w-[50px]">
                                        <span class="text-lg font-black leading-none">{{ String(hours).padStart(2, '0') }}</span>
                                        <span class="text-[9px] text-brand-tan uppercase mt-1">hrs</span>
                                    </div>
                                    <span class="text-[#2A1C13] font-bold">:</span>
                                    <div class="flex flex-col items-center bg-[#2A1C13] text-white rounded-lg px-3 py-1.5 min-w-[50px]">
                                        <span class="text-lg font-black leading-none">{{ String(minutes).padStart(2, '0') }}</span>
                                        <span class="text-[9px] text-brand-tan uppercase mt-1">min</span>
                                    </div>
                                    <span class="text-[#2A1C13] font-bold">:</span>
                                    <div class="flex flex-col items-center bg-[#2A1C13] text-white rounded-lg px-3 py-1.5 min-w-[50px]">
                                        <span class="text-lg font-black leading-none">{{ String(seconds).padStart(2, '0') }}</span>
                                        <span class="text-[9px] text-brand-tan uppercase mt-1">sec</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="text-warm-gray text-base leading-relaxed">
                            {{ product.short_description }}
                        </p>
                    </div>

                    <!-- Quick Stats / Prep info -->
                    <div class="grid grid-cols-2 gap-4 py-4 border-y border-brand-caramel/20">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-brand-tan/20 flex items-center justify-center text-brand-choco">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-warm-gray">Baking Prep Time</div>
                                <div class="text-sm font-bold text-ink">{{ product.prep_time_minutes }} minutes
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-brand-tan/20 flex items-center justify-center text-brand-choco">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 8h14M5 8a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1H5zm0 0v9a2 2 0 002 2h10a2 2 0 002-2V8M9 12h6" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-warm-gray">Stock Status</div>
                                <div class="text-sm font-bold"
                                    :class="product.is_in_stock ? 'text-success' : 'text-error'">
                                    {{ product.is_in_stock ? 'Oven Fresh In Stock' : 'Out of Stock' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Allergen Chips -->
                    <div v-if="product.allergens && product.allergens.length > 0" class="space-y-2">
                        <span class="block text-xs font-semibold uppercase tracking-wider text-brand-choco">Allergen
                            Information</span>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="(alg, idx) in product.allergens" :key="idx"
                                v-tooltip="`May contain ${alg.name} allergen — ${alg.type}. Please inform us of any dietary restrictions before ordering.`"
                                class="px-2.5 py-1 rounded-lg bg-warning/10 text-warning text-xs font-semibold border border-warning/20 cursor-help">
                                ⚠️ {{ alg.name }} ({{ alg.type }})
                            </span>
                        </div>
                    </div>

                    <!-- Quantity Selector & Add to Cart -->
                    <div class="space-y-4 pt-2">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center border border-brand-caramel/30 rounded-xl bg-white p-1">
                                <button type="button" v-tooltip="'Decrease quantity'"
                                    class="w-9 h-9 rounded-lg flex items-center justify-center text-brand-choco hover:bg-surface font-bold text-lg disabled:opacity-30"
                                    :disabled="quantity <= 1" @click="quantity--">
                                    -
                                </button>
                                <span v-tooltip="`Ordering ${quantity} of this item`"
                                    class="w-10 text-center font-bold text-ink cursor-help">{{ quantity }}</span>
                                <button type="button" v-tooltip="'Increase quantity'"
                                    class="w-9 h-9 rounded-lg flex items-center justify-center text-brand-choco hover:bg-surface font-bold text-lg"
                                    @click="quantity++">
                                    +
                                </button>
                            </div>

                            <div class="flex-1">
                                <BaseButton variant="primary" full-width size="lg" :loading="adding"
                                    :disabled="!product.is_in_stock"
                                    v-tooltip="product.is_in_stock ? 'Add to your bakery basket — review before checkout' : 'Currently out of stock — check back soon!'"
                                    @click="addToCart">
                                    Add {{ quantity }} to Order • ₱{{ ((product.is_on_sale && !isExpired ? product.sale_price : product.price) * quantity).toFixed(2) }}
                                </BaseButton>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Tabbed Section: Full Description & Nutrition Facts -->
            <div class="bg-white rounded-3xl p-6 md:p-10 border border-brand-caramel/20 shadow-sm space-y-8">
                <!-- Tabs Bar -->
                <div class="flex border-b border-brand-caramel/20 gap-8">
                    <button type="button" v-tooltip="'Full product backstory, ingredients &amp; baking details'"
                        class="pb-4 text-base font-bold transition-all border-b-2"
                        :class="activeTab === 'description' ? 'border-brand-choco text-brand-choco' : 'border-transparent text-warm-gray hover:text-brand-choco'"
                        @click="activeTab = 'description'">
                        Product Story &amp; Details
                    </button>

                    <button v-if="product.nutrition" type="button"
                        v-tooltip="'Calories, macros, serving size &amp; dietary info'"
                        class="pb-4 text-base font-bold transition-all border-b-2"
                        :class="activeTab === 'nutrition' ? 'border-brand-choco text-brand-choco' : 'border-transparent text-warm-gray hover:text-brand-choco'"
                        @click="activeTab = 'nutrition'">
                        Nutrition Facts
                    </button>
                </div>

                <!-- Description Tab Content -->
                <div v-if="activeTab === 'description'"
                    class="prose max-w-none text-ink/90 leading-relaxed space-y-4">
                    <div v-html="product.description || product.short_description"></div>
                </div>

                <!-- Nutrition Tab Content -->
                <div v-else-if="activeTab === 'nutrition' && product.nutrition" class="max-w-md space-y-4">
                    <div class="border border-ink p-4 rounded-xl space-y-2 text-ink">
                        <h3 class="font-extrabold text-2xl border-b-4 border-ink pb-1">Nutrition Facts</h3>
                        <p class="text-xs font-semibold">Serving Size: {{ product.nutrition.serving_size }}</p>
                        <div
                            class="border-t-8 border-ink my-2 pt-2 flex justify-between items-baseline font-extrabold text-lg">
                            <span>Amount Per Serving</span>
                            <span>Calories {{ product.nutrition.calories }}</span>
                        </div>
                        <div class="border-t border-ink pt-1 text-xs space-y-1">
                            <div class="flex justify-between font-bold">
                                <span>Total Fat</span>
                                <span>{{ product.nutrition.fat_g }}g</span>
                            </div>
                            <div class="flex justify-between font-bold">
                                <span>Total Carbohydrates</span>
                                <span>{{ product.nutrition.carbs_g }}g</span>
                            </div>
                            <div class="flex justify-between pl-4 text-warm-gray">
                                <span>Sugars</span>
                                <span>{{ product.nutrition.sugar_g }}g</span>
                            </div>
                            <div class="flex justify-between font-bold">
                                <span>Protein</span>
                                <span>{{ product.nutrition.protein_g }}g</span>
                            </div>
                            <div class="flex justify-between font-bold">
                                <span>Sodium</span>
                                <span>{{ product.nutrition.sodium_mg }}mg</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Reviews Section -->
            <div class="bg-white rounded-3xl p-6 md:p-10 border border-brand-caramel/20 shadow-sm">
                <ProductReviews :product-id="product.id" />
            </div>

        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, inject, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { useToast } from '@/composables/useToast'
import { useSaleCountdown } from '@/composables/useSaleCountdown'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseBadge from '@/components/ui/BaseBadge.vue'
import SkeletonBlock from '@/components/ui/SkeletonBlock.vue'
import SkeletonText from '@/components/ui/SkeletonText.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import ProductReviews from '@/components/storefront/ProductReviews.vue'

import { useProductModalStore } from '@/stores/productModal'

const axios = inject('axios')
const route = useRoute()
const cartStore = useCartStore()
const productModal = useProductModalStore()
const toast = useToast()

const product = ref(null)
const loading = ref(true)
const quantity = ref(1)
const activeIndex = ref(0)
const activeTab = ref('description')
const adding = ref(false)
const touchStartX = ref(0)
const touchCurrentX = ref(0)

const allImages = computed(() => {
    if (!product.value) return ['/images/placeholder-bakery.png']
    const p = product.value
    const list = []
    if (p.primary_image_url) list.push(p.primary_image_url)

    const gallery = p.gallery_images || p.gallery_image_urls || p.secondary_images_urls || p.images || []
    if (Array.isArray(gallery)) {
        gallery.forEach(img => {
            const url = typeof img === 'string' ? img : (img?.url || img?.src)
            if (url && !list.includes(url)) list.push(url)
        })
    }
    return list.length ? list : ['/images/placeholder-bakery.png']
})

function prevImage() {
    if (activeIndex.value > 0) activeIndex.value--
}

function nextImage() {
    if (activeIndex.value < allImages.value.length - 1) activeIndex.value++
}

function onTouchStart(e) {
    touchStartX.value = e.touches[0].clientX
    touchCurrentX.value = e.touches[0].clientX
}

function onTouchMove(e) {
    touchCurrentX.value = e.touches[0].clientX
}

function onTouchEnd() {
    if (!touchStartX.value || !touchCurrentX.value) return
    const diff = touchStartX.value - touchCurrentX.value
    if (Math.abs(diff) > 30) {
        if (diff > 0) nextImage()
        else prevImage()
    }
    touchStartX.value = 0
    touchCurrentX.value = 0
}

const { days, hours, minutes, seconds, isExpired, isNearExpiry } = useSaleCountdown(computed(() => product.value?.sale_ends_at))

async function fetchProduct() {
    loading.value = true
    try {
        const slug = route.params.slug
        const { data } = await axios.get(`/api/products/${slug}`)
        product.value = data.data
        activeIndex.value = 0
    } catch (err) {
        console.error('Failed to load product detail', err)
    } finally {
        loading.value = false
    }
}

async function addToCart() {
    if (!product.value) return

    if ((product.value.variation_type && product.value.variation_type !== 'none' && product.value.variations?.length > 0) || (product.value.flavors && product.value.flavors.length > 0)) {
        productModal.openModal(product.value)
        return
    }

    adding.value = true
    const options = product.value.flavor ? { flavor: product.value.flavor } : {}
    const res = await cartStore.addItem(product.value.id, quantity.value, options)
    adding.value = false
    if (res.success) {
        toast.success(`Added ${quantity.value}x ${product.value.name} to your basket!`, 'Pastry Added')
    }
}

onMounted(() => {
    fetchProduct()
})
</script>

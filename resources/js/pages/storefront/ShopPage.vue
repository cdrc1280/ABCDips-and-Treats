<template>
    <div class="page-container py-10 md:py-16">
        <!-- Header -->
        <PageHeader tagline="baked fresh daily" title="Explore Our Bakery Collection"
            subtitle="From classic banana bread loaves to artisanal cheesecakes and chewy handcrafted cookies." />

        <!-- Sticky Filter & Search Controls Bar -->
        <div class="sticky top-20 z-30 mb-8 p-4 md:p-5 rounded-3xl bg-white/90 dark:bg-[#1C1410]/90 backdrop-blur-xl border border-brand-caramel/25 dark:border-[#C08E5D]/25 shadow-lg shadow-brand-choco/5 transition-all">
            <div class="flex flex-col lg:flex-row gap-4 justify-between items-center">
                <!-- Search Input -->
                <div class="w-full lg:w-80 relative">
                    <BaseInput v-model="searchQuery" placeholder="Search pastries, cookies, cakes..."
                        @input="debounceSearch">
                        <template #icon-left>
                            <Search class="w-4 h-4 text-warm-gray dark:text-[#C5B4A4]" />
                        </template>
                    </BaseInput>
                    <button v-if="searchQuery"
                        class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-warm-gray hover:text-brand-choco dark:hover:text-[#E2C08A] transition-colors"
                        @click="clearSearch">
                        <X class="w-3.5 h-3.5" />
                    </button>
                </div>

                <!-- Category Pills (Smooth Horizontal Scroll on mobile/tablet) -->
                <div class="flex items-center gap-2 overflow-x-auto w-full lg:w-auto pb-1 lg:pb-0 no-scrollbar">
                    <button
                        class="px-4 py-2 rounded-2xl text-xs font-bold transition-all whitespace-nowrap shrink-0 flex items-center gap-1.5"
                        :class="!selectedCategory
                            ? 'bg-brand-choco text-surface dark:bg-[#E2C08A] dark:text-[#1C1410] shadow-sm'
                            : 'bg-surface/80 dark:bg-[#2A1C13] text-brand-choco dark:text-[#FBF3E7] hover:bg-brand-tan/25 dark:hover:bg-[#3D291D] border border-brand-caramel/20 dark:border-[#C08E5D]/25'"
                        @click="selectCategory('')">
                        <Sparkles class="w-3.5 h-3.5" />
                        <span>All Pastries</span>
                    </button>

                    <button v-for="cat in categories" :key="cat.id"
                        class="px-3.5 py-2 rounded-2xl text-xs font-semibold transition-all whitespace-nowrap shrink-0 flex items-center gap-2"
                        :class="selectedCategory === cat.slug
                            ? 'bg-brand-choco text-surface dark:bg-[#E2C08A] dark:text-[#1C1410] font-bold shadow-sm'
                            : 'bg-surface/80 dark:bg-[#2A1C13] text-brand-choco dark:text-[#FBF3E7] hover:bg-brand-tan/25 dark:hover:bg-[#3D291D] border border-brand-caramel/20 dark:border-[#C08E5D]/25'"
                        @click="selectCategory(cat.slug)">
                        <img v-if="cat.image_url" :src="cat.image_url" :alt="cat.name" class="w-4 h-4 rounded-full object-cover shrink-0" />
                        <component v-else :is="getCategoryIcon(cat.slug)" class="w-3.5 h-3.5 text-brand-caramel dark:text-[#E2C08A]" />
                        <span>{{ cat.name }}</span>
                    </button>
                </div>

                <!-- Sort Dropdown -->
                <div class="w-full lg:w-56 shrink-0">
                    <BaseSelect v-model="sortBy" :options="sortOptions" @update:model-value="() => fetchProducts(1)" />
                </div>
            </div>
        </div>

        <!-- Product Grid or Skeleton Loading with Smooth Crossfade -->
        <Transition name="fade" mode="out-in">
            <div v-if="loading" key="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <SkeletonCard v-for="n in 8" :key="n" />
            </div>

            <div v-else-if="products.length === 0" key="empty">
                <EmptyState title="No Pastries Found"
                    description="We couldn't find any treats matching your filter options. Try adjusting your search query or choosing another category.">
                    <template #action>
                        <BaseButton variant="secondary" @click="resetFilters">Reset All Filters</BaseButton>
                    </template>
                </EmptyState>
            </div>

            <div v-else key="products" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <ProductCard v-for="product in products" :key="product.id" :product="product" />
            </div>
        </Transition>

        <!-- Pagination Controls -->
        <div v-if="pagination.last_page > 1" class="mt-14 flex justify-center items-center gap-3">
            <BaseButton size="sm" variant="outline" :disabled="pagination.current_page === 1"
                @click="changePage(pagination.current_page - 1)">
                ← Previous
            </BaseButton>

            <span class="text-xs font-bold text-brand-choco dark:text-[#FBF3E7] px-4 py-2 rounded-xl bg-surface/60 dark:bg-[#1E1510] border border-brand-caramel/20 font-mono tabular-nums">
                Page {{ pagination.current_page }} of {{ pagination.last_page }}
            </span>

            <BaseButton size="sm" variant="outline" :disabled="pagination.current_page === pagination.last_page"
                @click="changePage(pagination.current_page + 1)">
                Next →
            </BaseButton>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, inject, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Search, X, Sparkles, Wheat, CircleDot, Layers, Cake, ShoppingBag } from 'lucide-vue-next'
import PageHeader from '@/components/ui/PageHeader.vue'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseSelect from '@/components/ui/BaseSelect.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import SkeletonCard from '@/components/ui/SkeletonCard.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import ProductCard from '@/components/storefront/ProductCard.vue'

const axios = inject('axios')
const route = useRoute()
const router = useRouter()

const products = ref([])
const categories = ref([])
const loading = ref(true)
const searchQuery = ref('')
const selectedCategory = ref(route.query.category || '')
const sortBy = ref('latest')
const pagination = ref({ current_page: 1, last_page: 1 })

// Client-Side In-Memory Cache (keyed by query params)
const shopCache = new Map()
const CACHE_TTL = 120000 // 2 minutes

let debounceTimer = null

const sortOptions = [
    { value: 'latest', label: 'Latest Additions' },
    { value: 'rating_high', label: 'Customer Rating (High to Low)' },
    { value: 'price_low', label: 'Price: Low to High' },
    { value: 'price_high', label: 'Price: High to Low' },
    { value: 'name_asc', label: 'Alphabetical (A-Z)' }
]

function getCategoryIcon(slug) {
    const s = (slug || '').toLowerCase()
    if (s.includes('banana') || s.includes('bread') || s.includes('loaf')) return Wheat
    if (s.includes('cookie')) return CircleDot
    if (s.includes('brownie') || s.includes('dip')) return Layers
    if (s.includes('cake') || s.includes('cheese')) return Cake
    return ShoppingBag
}

async function fetchCategories() {
    try {
        const { data } = await axios.get('/api/categories')
        categories.value = data.data
    } catch (err) {
        console.error('Failed to load categories', err)
    }
}

function getCacheKey(page) {
    return `${page}_${searchQuery.value.trim()}_${selectedCategory.value}_${sortBy.value}`
}

async function fetchProducts(page = 1) {
    const cacheKey = getCacheKey(page)
    const cached = shopCache.get(cacheKey)
    const now = Date.now()

    if (cached && (now - cached.timestamp < CACHE_TTL)) {
        products.value = cached.products
        pagination.value = cached.pagination
        loading.value = false
        return
    }

    loading.value = true
    try {
        const params = {
            page,
            search: searchQuery.value.trim(),
            category: selectedCategory.value,
            sort: sortBy.value
        }
        const { data } = await axios.get('/api/products', { params })
        products.value = data.data
        pagination.value = data.meta || { current_page: 1, last_page: 1 }

        shopCache.set(cacheKey, {
            products: data.data,
            pagination: pagination.value,
            timestamp: now
        })
    } catch (err) {
        console.error('Failed to fetch products', err)
    } finally {
        loading.value = false
    }
}

function selectCategory(catSlug) {
    selectedCategory.value = catSlug
    router.replace({
        query: {
            ...route.query,
            category: catSlug || undefined
        }
    })
    fetchProducts(1)
}

function debounceSearch() {
    clearTimeout(debounceTimer)
    debounceTimer = setTimeout(() => {
        fetchProducts(1)
    }, 350)
}

function clearSearch() {
    searchQuery.value = ''
    fetchProducts(1)
}

function changePage(page) {
    fetchProducts(page)
    window.scrollTo({ top: 160, behavior: 'smooth' })
}

function resetFilters() {
    searchQuery.value = ''
    selectedCategory.value = ''
    sortBy.value = 'latest'
    router.replace({ query: {} })
    fetchProducts(1)
}

watch(() => route.query.category, (newCat) => {
    if (newCat !== undefined && newCat !== selectedCategory.value) {
        selectedCategory.value = newCat || ''
        fetchProducts(1)
    }
})

onMounted(() => {
    fetchCategories()
    fetchProducts()
})
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}

.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>

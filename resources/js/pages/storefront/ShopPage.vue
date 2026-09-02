<template>
    <div class="page-container py-10 md:py-16">
        <!-- Header -->
        <PageHeader tagline="baked fresh daily" title="Explore Our Bakery Collection"
            subtitle="From classic banana bread loaves to cheesecakes and chewy handcrafted cookies." />

        <!-- Filter & Search Controls Bar -->
        <div class="bg-white rounded-2xl p-4 md:p-6 border border-brand-caramel/20 mb-8 space-y-4 shadow-sm">
            <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
                <!-- Search Input -->
                <div class="w-full md:w-80">
                    <BaseInput v-model="searchQuery" placeholder="Search banana bread, cookies, cakes..."
                        @input="debounceSearch">
                        <template #icon-left>
                            <svg class="w-4 h-4 text-warm-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </template>
                    </BaseInput>
                </div>

                <!-- Category Pills (Horizontal scroll on mobile) -->
                <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-1 md:pb-0 no-scrollbar">
                    <button
                        class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap shrink-0"
                        :class="!selectedCategory ? 'bg-brand-choco text-surface dark:bg-[#E2C08A] dark:text-[#1C1410]' : 'bg-brand-tan/20 text-brand-choco hover:bg-brand-tan/35 dark:bg-[#2A1C13] dark:text-[#FBF3E7] dark:hover:bg-[#3D291D] dark:border dark:border-[#C08E5D]/30'"
                        @click="selectCategory('')">
                        All Items
                    </button>

                    <button v-for="cat in categories" :key="cat.id"
                        class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all whitespace-nowrap shrink-0 flex items-center gap-2"
                        :class="selectedCategory === cat.slug ? 'bg-brand-choco text-surface dark:bg-[#E2C08A] dark:text-[#1C1410] font-bold' : 'bg-brand-tan/20 text-brand-choco hover:bg-brand-tan/35 dark:bg-[#2A1C13] dark:text-[#FBF3E7] dark:hover:bg-[#3D291D] dark:border dark:border-[#C08E5D]/30'"
                        @click="selectCategory(cat.slug)">
                        <img v-if="cat.image_url" :src="cat.image_url" :alt="cat.name" class="w-4 h-4 rounded-full object-cover shrink-0" />
                        <span>{{ cat.name }}</span>
                    </button>
                </div>

                <!-- Sort Select -->
                <div class="w-full md:w-56">
                    <BaseSelect v-model="sortBy" :options="sortOptions" @update:model-value="fetchProducts" />
                </div>
            </div>
        </div>

        <!-- Product Grid or Skeleton Loading -->
        <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <SkeletonCard v-for="n in 8" :key="n" />
        </div>

        <div v-else-if="products.length === 0">
            <EmptyState title="No Pastries Found"
                description="We couldn't find any treats matching your filter options. Try adjusting your search query or choosing another category.">
                <template #action>
                    <BaseButton variant="secondary" @click="resetFilters">Reset All Filters</BaseButton>
                </template>
            </EmptyState>
        </div>

        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <ProductCard v-for="product in products" :key="product.id" :product="product" />
        </div>

        <!-- Pagination -->
        <div v-if="pagination.last_page > 1" class="mt-12 flex justify-center items-center gap-2">
            <BaseButton size="sm" variant="outline" :disabled="pagination.current_page === 1"
                @click="changePage(pagination.current_page - 1)">
                ← Previous
            </BaseButton>

            <span class="text-sm font-semibold text-brand-choco dark:text-[#FBF3E7] px-4">
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
import { ref, onMounted, inject } from 'vue'
import { useRoute, useRouter } from 'vue-router'
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

let debounceTimer = null

const sortOptions = [
    { value: 'latest', label: 'Latest Additions' },
    { value: 'rating_high', label: 'Customer Rating (High to Low)' },
    { value: 'price_low', label: 'Price: Low to High' },
    { value: 'price_high', label: 'Price: High to Low' },
    { value: 'name_asc', label: 'Alphabetical (A-Z)' }
]

async function fetchCategories() {
    try {
        const { data } = await axios.get('/api/categories')
        categories.value = data.data
    } catch (err) {
        console.error('Failed to load categories', err)
    }
}

async function fetchProducts(page = 1) {
    loading.value = true
    try {
        const params = {
            page,
            search: searchQuery.value,
            category: selectedCategory.value,
            sort: sortBy.value
        }
        const { data } = await axios.get('/api/products', { params })
        products.value = data.data
        pagination.value = data.meta || { current_page: 1, last_page: 1 }
    } catch (err) {
        console.error('Failed to fetch products', err)
    } finally {
        loading.value = false
    }
}

function selectCategory(catSlug) {
    selectedCategory.value = catSlug
    fetchProducts(1)
}

function debounceSearch() {
    clearTimeout(debounceTimer)
    debounceTimer = setTimeout(() => {
        fetchProducts(1)
    }, 350)
}

function changePage(page) {
    fetchProducts(page)
    window.scrollTo({ top: 200, behavior: 'smooth' })
}

function resetFilters() {
    searchQuery.value = ''
    selectedCategory.value = ''
    sortBy.value = 'latest'
    fetchProducts(1)
}

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
</style>

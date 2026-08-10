<template>
  <div class="page-container py-10 md:py-16">
    <!-- Category header skeleton -->
    <div v-if="loading" class="mb-10">
      <div class="h-4 bg-brand-tan/30 rounded w-24 mb-3 animate-pulse" />
      <div class="h-10 bg-brand-tan/30 rounded-xl w-64 mb-4 animate-pulse" />
      <div class="h-4 bg-brand-tan/20 rounded w-96 animate-pulse" />
    </div>

    <!-- Category Header -->
    <div v-else-if="category" class="mb-10 flex flex-col md:flex-row gap-6 items-start md:items-center justify-between bg-white dark:bg-[#1E1510] p-6 sm:p-8 rounded-3xl border border-brand-caramel/20 dark:border-[#C08E5D]/20 shadow-sm">
      <div>
        <span class="font-['Caveat'] text-brand-caramel dark:text-[#E2C08A] text-xl">browse category</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-ink dark:text-[#FBF3E7] tracking-tight mt-1">{{ category.name }}</h1>
        <p v-if="category.description" class="text-warm-gray dark:text-[#C5B4A4] text-sm sm:text-base mt-2 max-w-xl leading-relaxed">{{ category.description }}</p>
        <div class="flex items-center gap-3 mt-4">
          <span class="inline-flex items-center gap-1.5 bg-brand-tan/20 text-brand-choco dark:text-[#E2C08A] text-xs font-bold px-3 py-1.5 rounded-full">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" /></svg>
            {{ products.length }} products
          </span>
          <RouterLink to="/shop" class="text-xs text-warm-gray hover:text-brand-choco transition-colors">← All Categories</RouterLink>
        </div>
      </div>
      <div v-if="category.image_url" class="w-24 h-24 sm:w-32 sm:h-32 rounded-2xl overflow-hidden shrink-0 border border-brand-tan/30 shadow-md">
        <img :src="category.image_url" :alt="category.name" class="w-full h-full object-cover" />
      </div>
    </div>

    <!-- Product Grid Skeleton -->
    <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <SkeletonCard v-for="n in 8" :key="n" />
    </div>

    <!-- Empty State -->
    <EmptyState
      v-else-if="products.length === 0"
      title="No Products Yet"
      description="We're baking up something special for this category. Check back soon!"
    />

    <!-- Products Grid -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <ProductCard v-for="product in products" :key="product.id" :product="product" />
    </div>
  </div>
</template>

<script setup>
import { ref, watch, inject } from 'vue'
import { useRoute } from 'vue-router'
import SkeletonCard from '@/components/ui/SkeletonCard.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import ProductCard from '@/components/storefront/ProductCard.vue'

const route = useRoute()
const axios = inject('axios')

const category = ref(null)
const products = ref([])
const loading = ref(true)

async function load(slug) {
  loading.value = true
  try {
    const [catRes, prodRes] = await Promise.all([
      axios.get(`/api/categories/${slug}`),
      axios.get(`/api/products?category=${slug}&per_page=50`)
    ])
    category.value = catRes.data.data
    products.value = prodRes.data.data
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

watch(() => route.params.slug, (slug) => slug && load(slug), { immediate: true })
</script>

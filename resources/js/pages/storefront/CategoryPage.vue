<template>
  <div class="page-container py-10 md:py-16">
    <!-- Category header skeleton -->
    <div v-if="loading" class="mb-10">
      <div class="h-4 bg-[#D9A876]/30 rounded w-24 mb-3 animate-pulse" />
      <div class="h-10 bg-[#D9A876]/30 rounded-xl w-64 mb-4 animate-pulse" />
      <div class="h-4 bg-[#D9A876]/20 rounded w-96 animate-pulse" />
    </div>

    <!-- Category Header -->
    <div v-else-if="category" class="mb-10">
      <span class="font-['Caveat'] text-[#C08E5D] text-xl">browse category</span>
      <h1 class="text-4xl font-extrabold text-[#1C1410] tracking-tight mt-1">{{ category.name }}</h1>
      <p v-if="category.description" class="text-[#8C7A68] mt-2 max-w-xl">{{ category.description }}</p>
      <div class="flex items-center gap-3 mt-4">
        <span class="inline-flex items-center gap-1.5 bg-[#D9A876]/20 text-[#5C3A22] text-xs font-bold px-3 py-1.5 rounded-full">
          <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" /></svg>
          {{ products.length }} products
        </span>
        <RouterLink to="/shop" class="text-xs text-[#8C7A68] hover:text-[#5C3A22] transition-colors">← All Categories</RouterLink>
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

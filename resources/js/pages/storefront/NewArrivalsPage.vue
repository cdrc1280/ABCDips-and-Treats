<template>
  <div class="page-container py-10 md:py-16">
    <PageHeader
      tagline="fresh out of the oven"
      title="New Pastry Arrivals"
      subtitle="Discover our latest seasonal additions and new recipe creations."
    />

    <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <SkeletonCard v-for="n in 8" :key="n" />
    </div>

    <div v-else-if="products.length === 0">
      <EmptyState title="No New Arrivals Found" description="Our bakers are crafting new recipes — check back soon!" />
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <ProductCard
        v-for="product in products"
        :key="product.id"
        :product="product"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import PageHeader from '@/components/ui/PageHeader.vue'
import SkeletonCard from '@/components/ui/SkeletonCard.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import ProductCard from '@/components/storefront/ProductCard.vue'

const axios = inject('axios')
const products = ref([])
const loading = ref(true)

async function fetchNewArrivals() {
  loading.value = true
  try {
    const { data } = await axios.get('/api/products/new-arrivals?limit=12')
    products.value = data.data
  } catch (err) {
    console.error('Failed to load new arrivals', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => fetchNewArrivals())
</script>

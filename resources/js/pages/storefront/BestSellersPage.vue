<template>
  <div class="page-container py-10 md:py-16">
    <PageHeader
      tagline="customer favorites"
      title="Best Selling Pastries"
      subtitle="Our most loved, oven-baked signature items ordered again and again."
    />

    <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <SkeletonCard v-for="n in 8" :key="n" />
    </div>

    <div v-else-if="products.length === 0">
      <EmptyState title="No Best Sellers Found" description="Check back soon for customer favorites." />
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

async function fetchBestSellers() {
  loading.value = true
  try {
    const { data } = await axios.get('/api/products/best-sellers?limit=12')
    products.value = data.data
  } catch (err) {
    console.error('Failed to load best sellers', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => fetchBestSellers())
</script>

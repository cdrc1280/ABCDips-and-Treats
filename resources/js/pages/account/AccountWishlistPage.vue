<template>
  <div class="space-y-8">
    <PageHeader
      tagline="saved treats"
      title="My Wishlist"
      subtitle="Keep track of your favorite ABCDips pastries and add them to your basket whenever you crave them."
    />

    <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <SkeletonCard v-for="n in 3" :key="n" />
    </div>

    <div v-else-if="products.length === 0">
      <EmptyState
        title="Your Wishlist is Empty"
        description="Browse our shop and click the heart icon to save your favorite treats!"
      >
        <template #action>
          <RouterLink to="/shop"><BaseButton variant="primary">Browse Menu</BaseButton></RouterLink>
        </template>
      </EmptyState>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
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
import BaseButton from '@/components/ui/BaseButton.vue'
import SkeletonCard from '@/components/ui/SkeletonCard.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import ProductCard from '@/components/storefront/ProductCard.vue'

const axios = inject('axios')
const products = ref([])
const loading = ref(true)

async function fetchWishlist() {
  loading.value = true
  try {
    const { data } = await axios.get('/api/wishlist')
    products.value = data.data
  } catch (err) {
    console.error('Failed to load wishlist', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => fetchWishlist())
</script>

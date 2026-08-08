<template>
  <div class="space-y-8">
    <PageHeader
      tagline="saved treats"
      title="My Wishlist"
      subtitle="Keep track of your favorite ABCDips pastries and add them to your basket whenever you crave them."
    />

    <div v-if="wishlistStore.loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <SkeletonCard v-for="n in 3" :key="n" />
    </div>

    <div v-else-if="wishlistStore.items.length === 0">
      <EmptyState
        title="Your Wishlist is Empty"
        description="Browse our shop and click the heart icon on any pastry to save your favorite treats!"
      >
        <template #action>
          <RouterLink to="/shop"><BaseButton variant="primary">Browse Menu</BaseButton></RouterLink>
        </template>
      </EmptyState>
    </div>

    <div v-else class="space-y-6">
      <div class="flex justify-between items-center text-xs font-bold text-warm-gray">
        <span>Showing {{ wishlistStore.items.length }} saved item{{ wishlistStore.items.length > 1 ? 's' : '' }}</span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <ProductCard
          v-for="product in wishlistStore.items"
          :key="product.id"
          :product="product"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useWishlistStore } from '@/stores/wishlist'
import PageHeader from '@/components/ui/PageHeader.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import SkeletonCard from '@/components/ui/SkeletonCard.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import ProductCard from '@/components/storefront/ProductCard.vue'

const wishlistStore = useWishlistStore()

onMounted(() => {
  wishlistStore.fetchWishlist()
})
</script>

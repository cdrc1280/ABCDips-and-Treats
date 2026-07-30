<template>
  <div
    class="group bg-white rounded-2xl border border-[#C08E5D]/20 overflow-hidden flex flex-col transition-all duration-300 hover:shadow-lg hover:-translate-y-1 cursor-pointer"
    style="box-shadow: var(--shadow-sm);"
    @click="openModal"
  >
    <!-- Image & Badge Overlay -->
    <div class="relative aspect-square overflow-hidden bg-[#FBF3E7]/60">
      <img
        :src="product.primary_image_url || '/images/placeholder-bakery.png'"
        :alt="product.name"
        class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500"
        loading="lazy"
      />

      <!-- Badges -->
      <div class="absolute top-3 left-3 flex flex-col gap-1.5 items-start z-10">
        <BaseBadge v-if="product.is_best_seller" variant="brand">Best Seller</BaseBadge>
        <BaseBadge v-else-if="product.is_highly_rated" variant="warning">⭐ Highly Rated</BaseBadge>
        <BaseBadge v-else-if="product.is_featured" variant="neutral">Featured</BaseBadge>
        <BaseBadge v-else-if="product.is_new_arrival" variant="success">New</BaseBadge>
        <BaseBadge v-if="product.is_on_sale" variant="error">Sale</BaseBadge>
      </div>

      <!-- Wishlist Heart Button -->
      <button
        type="button"
        class="absolute top-3 right-3 z-20 w-8 h-8 rounded-full bg-white/90 backdrop-blur-md shadow-sm flex items-center justify-center transition-all hover:scale-110"
        :class="wishlistStore.isInWishlist(product.id) ? 'text-red-500 bg-red-50' : 'text-[#8C7A68] hover:text-red-500'"
        v-tooltip="wishlistStore.isInWishlist(product.id) ? 'Remove from saved wishlist' : 'Save to wishlist for later'"
        @click.stop.prevent="wishlistStore.toggleWishlist(product)"
      >
        <svg class="w-4 h-4" :fill="wishlistStore.isInWishlist(product.id) ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
      </button>

      <!-- Quick Add / View Overlay Button -->
      <div class="absolute inset-x-3 bottom-3 opacity-90 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-200 z-10">
        <BaseButton
          variant="primary"
          full-width
          size="sm"
          v-tooltip="'View options & add to basket'"
          @click.stop.prevent="openModal"
        >
          View &amp; Add to Order • ₱{{ (product.sale_price || product.price).toFixed(2) }}
        </BaseButton>
      </div>
    </div>

    <!-- Product Details -->
    <div class="p-5 flex-1 flex flex-col justify-between">
      <div>
        <div class="flex items-center justify-between mb-1">
          <span class="text-xs font-semibold text-[#C08E5D] uppercase tracking-wider">
            {{ product.category?.name || 'Pastries' }}
          </span>

          <!-- Rating Score Pill -->
          <div
            v-if="product.reviews_count && product.reviews_count > 0"
            v-tooltip="`Rated ${product.avg_rating} stars based on ${product.reviews_count} verified review${product.reviews_count > 1 ? 's' : ''}`"
            class="text-[11px] font-extrabold text-[#5C3A22] flex items-center gap-1 bg-[#FBF3E7] px-2 py-0.5 rounded-full border border-[#C08E5D]/20 cursor-pointer"
            @click.stop="openModal"
          >
            <span class="text-amber-500">⭐</span>
            <span>{{ product.avg_rating }}</span>
            <span class="text-[#8C7A68] font-normal">({{ product.reviews_count }})</span>
          </div>
        </div>

        <h3 class="font-bold text-[#1C1410] text-base leading-snug line-clamp-1 mb-1.5 group-hover:text-[#5C3A22] transition-colors">
          {{ product.name }}
        </h3>

        <p class="text-xs text-[#8C7A68] line-clamp-2 mb-4 leading-relaxed">
          {{ product.short_description }}
        </p>
      </div>

      <!-- Price & Stock Row -->
      <div class="pt-3 border-t border-[#C08E5D]/10 flex items-center justify-between">
        <div class="flex items-baseline gap-2">
          <span class="font-extrabold text-[#5C3A22] text-lg">
            ₱{{ (product.sale_price || product.price).toFixed(2) }}
          </span>
          <span v-if="product.is_on_sale" class="text-xs text-[#8C7A68] line-through">
            ₱{{ product.price.toFixed(2) }}
          </span>
        </div>

        <div
          v-tooltip="'Estimated baking &amp; preparation time before dispatch'"
          class="flex items-center gap-1 text-xs text-[#8C7A68] cursor-help"
        >
          <svg class="w-3.5 h-3.5 text-[#C08E5D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          <span>{{ product.prep_time_minutes }}m prep</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useWishlistStore } from '@/stores/wishlist'
import { useProductModalStore } from '@/stores/productModal'
import BaseBadge from '@/components/ui/BaseBadge.vue'
import BaseButton from '@/components/ui/BaseButton.vue'

const props = defineProps({
  product: { type: Object, required: true }
})

const wishlistStore = useWishlistStore()
const productModalStore = useProductModalStore()

function openModal() {
  productModalStore.openModal(props.product)
}
</script>

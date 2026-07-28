<template>
  <div
    class="group bg-white rounded-2xl border border-[#C08E5D]/20 overflow-hidden flex flex-col transition-all duration-300 hover:shadow-lg hover:-translate-y-1"
    style="box-shadow: var(--shadow-sm);"
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
      <div class="absolute top-3 left-3 flex flex-col gap-1.5 items-start">
        <BaseBadge v-if="product.is_best_seller" variant="brand">Best Seller</BaseBadge>
        <BaseBadge v-else-if="product.is_featured" variant="neutral">Featured</BaseBadge>
        <BaseBadge v-else-if="product.is_new_arrival" variant="success">New</BaseBadge>
        <BaseBadge v-if="product.is_on_sale" variant="error">Sale</BaseBadge>
      </div>

      <!-- Quick Add Overlay Button -->
      <div class="absolute inset-x-3 bottom-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
        <BaseButton
          variant="primary"
          full-width
          size="sm"
          :loading="adding"
          @click.prevent="addToCart"
        >
          Add to Order • ₱{{ (product.sale_price || product.price).toFixed(2) }}
        </BaseButton>
      </div>
    </div>

    <!-- Product Details -->
    <div class="p-5 flex-1 flex flex-col justify-between">
      <div>
        <div class="text-xs font-semibold text-[#C08E5D] uppercase tracking-wider mb-1">
          {{ product.category?.name || 'Pastries' }}
        </div>

        <RouterLink :to="`/products/${product.slug}`" class="group-hover:text-[#5C3A22] transition-colors">
          <h3 class="font-bold text-[#1C1410] text-base leading-snug line-clamp-1 mb-1.5">
            {{ product.name }}
          </h3>
        </RouterLink>

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

        <div class="flex items-center gap-1 text-xs text-[#8C7A68]">
          <svg class="w-3.5 h-3.5 text-[#C08E5D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          <span>{{ product.prep_time_minutes }}m prep</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useCartStore } from '@/stores/cart'
import { useToast } from '@/composables/useToast'
import BaseBadge from '@/components/ui/BaseBadge.vue'
import BaseButton from '@/components/ui/BaseButton.vue'

const props = defineProps({
  product: { type: Object, required: true }
})

const cartStore = useCartStore()
const toast = useToast()
const adding = ref(false)

async function addToCart() {
  adding.value = true
  const res = await cartStore.addItem(props.product.id, 1)
  adding.value = false
  if (res.success) {
    toast.success(`Added ${props.product.name} to your basket!`, 'Freshly Baked')
  }
}
</script>

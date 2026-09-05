<template>
  <div
    ref="cardRef"
    class="group bg-white dark:bg-[#1E1510] rounded-2xl border border-brand-caramel/20 dark:border-[#C08E5D]/20 overflow-hidden flex flex-col cursor-pointer"
    style="box-shadow: var(--shadow-sm); transition: box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1);"
    @click="openModal"
    @mouseenter="isHovered = true"
    @mouseleave="isHovered = false"
  >
    <!-- Image & Badge Overlay -->
    <div class="relative aspect-square overflow-hidden bg-surface/60 dark:bg-[#140D09]">
      <!-- Progressive Blur-Up Image -->
      <div ref="imgWrapperRef" class="w-full h-full">
        <img
          :src="product.primary_image_url || '/images/placeholder-bakery.png'"
          :alt="product.name"
          class="w-full h-full object-cover object-center transition-all duration-700 ease-out"
          :class="[
            imgLoaded ? 'blur-0 scale-100' : 'blur-md scale-105',
            isHovered ? 'scale-108' : ''
          ]"
          loading="lazy"
          @load="imgLoaded = true"
        />
      </div>

      <!-- Badges -->
      <div class="absolute top-2.5 left-2.5 sm:top-3 sm:left-3 flex flex-col gap-1 sm:gap-1.5 items-start z-10">
        <BaseBadge v-if="product.is_best_seller" variant="brand">Best Seller</BaseBadge>
        <BaseBadge v-else-if="product.is_highly_rated" variant="warning"><span class="flex items-center gap-1"><Star class="w-3 h-3 fill-current" /><span>Highly Rated</span></span></BaseBadge>
        <BaseBadge v-else-if="product.is_featured" variant="neutral">Featured</BaseBadge>
        <BaseBadge v-else-if="product.is_new_arrival" variant="success">New</BaseBadge>
        <BaseBadge v-if="product.is_on_sale && !isExpired" variant="error">Sale</BaseBadge>
      </div>

      <!-- Wishlist Heart Button with Spring Animation -->
      <button
        type="button"
        class="absolute top-2.5 right-2.5 sm:top-3 sm:right-3 z-20 w-8 h-8 rounded-full bg-white/90 dark:bg-[#1C1410]/90 backdrop-blur-md shadow-sm flex items-center justify-center transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-brand-caramel"
        :class="[
          wishlistStore.isInWishlist(product.id)
            ? 'text-red-500 bg-red-50 dark:bg-red-950/40'
            : 'text-warm-gray dark:text-[#C5B4A4] hover:text-red-500',
          wishlistAnimating ? 'spring-bounce' : ''
        ]"
        v-tooltip="wishlistStore.isInWishlist(product.id) ? 'Remove from saved wishlist' : 'Save to wishlist for later'"
        @click.stop.prevent="handleWishlistToggle"
      >
        <Heart
          class="w-4 h-4 transition-transform duration-200"
          :fill="wishlistStore.isInWishlist(product.id) ? 'currentColor' : 'none'"
          :stroke-width="2"
        />
      </button>

      <!-- Quick Add / View Overlay Button -->
      <div class="absolute inset-x-2.5 sm:inset-x-3 bottom-2.5 sm:bottom-3 opacity-90 md:opacity-0 md:group-hover:opacity-100 transition-all duration-300 md:translate-y-2 md:group-hover:translate-y-0 z-10">
        <BaseButton
          variant="primary"
          full-width
          size="sm"
          v-tooltip="'View options & add to basket'"
          @click.stop.prevent="openModal"
        >
          View &amp; Add<template v-if="hasPrice"> • ₱{{ (product.is_on_sale && !isExpired ? product.sale_price : product.price).toFixed(2) }}</template>
        </BaseButton>
      </div>
    </div>

    <!-- Product Details -->
    <div class="p-3.5 sm:p-5 flex-1 flex flex-col justify-between">
      <div>
        <div class="flex items-center justify-between mb-1">
          <span class="text-[10px] sm:text-xs font-semibold text-brand-caramel dark:text-[#E2C08A] uppercase tracking-wider">
            {{ product.category?.name || 'Pastries' }}
          </span>

          <!-- Rating Score Pill -->
          <div
            v-if="product.reviews_count && product.reviews_count > 0"
            v-tooltip="`Rated ${product.avg_rating} stars based on ${product.reviews_count} verified review${product.reviews_count > 1 ? 's' : ''}`"
            class="text-[10px] sm:text-[11px] font-extrabold text-brand-choco dark:text-[#E2C08A] flex items-center gap-1 bg-surface dark:bg-[#140D09] px-2 py-0.5 rounded-full border border-brand-caramel/20 dark:border-[#C08E5D]/30 cursor-pointer"
            @click.stop="openModal"
          >
            <Star class="w-3.5 h-3.5 fill-amber-400 text-amber-500 inline shrink-0" />
            <span class="font-mono">{{ product.avg_rating }}</span>
            <span class="text-warm-gray dark:text-[#A89686] font-normal">({{ product.reviews_count }})</span>
          </div>
        </div>

        <h3 class="font-bold text-ink dark:text-[#FBF3E7] text-sm sm:text-base leading-snug line-clamp-1 mb-1 group-hover:text-brand-choco dark:group-hover:text-[#E2C08A] transition-colors duration-200">
          {{ product.name }}
        </h3>

        <p class="text-xs text-warm-gray dark:text-[#C5B4A4] line-clamp-2 mb-3 sm:mb-4 leading-relaxed">
          {{ product.short_description }}
        </p>
      </div>

      <!-- Price & Stock Row -->
      <div class="pt-2.5 sm:pt-3 border-t border-brand-caramel/10 dark:border-[#C08E5D]/20 flex flex-col gap-1.5 sm:gap-2">
        <div class="flex items-center justify-between">
          <div class="flex items-baseline gap-1.5 sm:gap-2">
            <template v-if="hasPrice">
              <span class="font-extrabold text-brand-choco dark:text-[#E2C08A] text-base sm:text-lg font-mono tabular-nums">
                ₱{{ (product.is_on_sale && !isExpired ? product.sale_price : product.price).toFixed(2) }}
              </span>
              <span v-if="product.is_on_sale && !isExpired" class="text-xs text-warm-gray dark:text-[#A89686] line-through font-mono tabular-nums">
                ₱{{ product.price.toFixed(2) }}
              </span>
            </template>
            <template v-else>
              <span class="text-sm font-semibold text-warm-gray dark:text-[#C5B4A4] italic">Price on Request</span>
            </template>
          </div>

          <div
            v-tooltip="'Estimated baking & preparation time before dispatch'"
            class="flex items-center gap-1 text-xs text-warm-gray dark:text-[#C5B4A4] cursor-help"
          >
            <Clock class="w-3.5 h-3.5 text-brand-caramel dark:text-[#E2C08A]" />
            <span class="text-[11px] sm:text-xs font-mono tabular-nums">{{ product.prep_time_minutes }}m</span>
          </div>
        </div>

        <div v-if="product.is_on_sale && product.sale_ends_at && !isExpired"
             class="inline-flex items-center self-start gap-1 text-[10px] font-bold px-2 py-0.5 rounded border"
             :class="isNearExpiry ? 'bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 border-red-200 dark:border-red-800 animate-pulse' : 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800'">
          <Clock class="w-3 h-3" />
          <span>Ends in {{ days > 0 ? `${days}d ${hours}h` : `${hours}h ${minutes}m` }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useWishlistStore } from '@/stores/wishlist'
import { useProductModalStore } from '@/stores/productModal'
import { useSaleCountdown } from '@/composables/useSaleCountdown'
import { useTiltEffect } from '@/composables/useTiltEffect'
import BaseBadge from '@/components/ui/BaseBadge.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import { computed, ref } from 'vue'
import { Star, Heart, Clock } from 'lucide-vue-next'

const props = defineProps({
  product: { type: Object, required: true }
})

const wishlistStore = useWishlistStore()
const productModalStore = useProductModalStore()
const { days, hours, minutes, seconds, isExpired, isNearExpiry } = useSaleCountdown(computed(() => props.product.sale_ends_at))

const cardRef = ref(null)
const imgWrapperRef = ref(null)
const imgLoaded = ref(false)
const isHovered = ref(false)
const wishlistAnimating = ref(false)

// 3D tilt effect on desktop hover
useTiltEffect(cardRef, { maxTilt: 6, scale: 1.015, speed: 600, glare: false })

const hasPrice = computed(() => {
  return props.product.price && parseFloat(props.product.price) > 0
})

function handleWishlistToggle() {
  wishlistAnimating.value = true
  wishlistStore.toggleWishlist(props.product)
  setTimeout(() => { wishlistAnimating.value = false }, 450)
}

function openModal() {
  productModalStore.openModal(props.product)
}
</script>

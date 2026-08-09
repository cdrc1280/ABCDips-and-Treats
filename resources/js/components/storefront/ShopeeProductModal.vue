<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="modalStore.isOpen && modalStore.product"
        class="fixed inset-0 z-50 overflow-y-auto bg-ink/60 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4 md:p-6"
        @click.self="modalStore.closeModal"
      >
        <Transition
          enter-active-class="transition-all duration-300 ease-out"
          enter-from-class="opacity-0 scale-95 translate-y-4"
          enter-to-class="opacity-100 scale-100 translate-y-0"
          leave-active-class="transition-all duration-200 ease-in"
          leave-from-class="opacity-100 scale-100 translate-y-0"
          leave-to-class="opacity-0 scale-95 translate-y-4"
        >
          <div
            v-if="modalStore.isOpen && modalStore.product"
            class="bg-surface rounded-2xl sm:rounded-3xl shadow-2xl border border-brand-caramel/30 overflow-hidden w-full max-w-5xl relative max-h-[92vh] flex flex-col text-ink"
          >
            <!-- Close Button -->
            <button
              type="button"
              class="absolute top-3 right-3 z-30 w-9 h-9 rounded-full bg-white/90 hover:bg-white text-brand-choco shadow-sm flex items-center justify-center transition-all cursor-pointer border border-brand-caramel/20 font-bold"
              @click="modalStore.closeModal"
            >
              ✕
            </button>

            <!-- Scrollable Container -->
            <div class="overflow-y-auto p-4 sm:p-6 md:p-8 space-y-6">

              <!-- Top Breadcrumb Bar -->
              <nav class="flex items-center gap-1.5 text-xs text-warm-gray flex-wrap pb-1 font-semibold">
                <span class="hover:text-brand-choco cursor-pointer" @click="$router.push('/')">Home</span>
                <span>/</span>
                <span class="hover:text-brand-choco cursor-pointer" @click="$router.push('/shop')">Shop</span>
                <template v-if="modalStore.product.category?.name">
                  <span>/</span>
                  <span class="text-brand-choco font-bold">{{ modalStore.product.category.name }}</span>
                </template>
                <span>/</span>
                <span class="text-brand-choco font-bold truncate max-w-xs">{{ modalStore.product.name }}</span>
              </nav>

              <!-- Main Product Card Layout (2-Column Grid) -->
              <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-brand-caramel/20 grid grid-cols-1 md:grid-cols-12 gap-6 md:gap-8 items-start">

                <!-- Left Column: Main Image Showcase + Thumbnails -->
                <div class="md:col-span-5 space-y-4">
                  <!-- Swipeable Main Image Carousel -->
                  <div
                    class="relative aspect-square rounded-2xl overflow-hidden bg-surface/50 border border-brand-caramel/20 select-none"
                    @touchstart="onTouchStart"
                    @touchmove="onTouchMove"
                    @touchend="onTouchEnd"
                  >
                    <!-- Image Slides -->
                    <div
                      class="flex h-full transition-transform duration-300 ease-out"
                      :style="{ transform: `translateX(-${allImages.length ? (activeIndex * 100) / allImages.length : 0}%)`, width: `${allImages.length * 100}%` }"
                    >
                      <div
                        v-for="(img, idx) in allImages"
                        :key="idx"
                        class="h-full shrink-0"
                        :style="{ width: `${100 / allImages.length}%` }"
                      >
                        <img
                          :src="img"
                          :alt="`${modalStore.product.name} - Image ${idx + 1}`"
                          class="w-full h-full object-cover object-center"
                        />
                      </div>
                    </div>

                    <!-- Left Arrow -->
                    <button
                      v-if="allImages.length > 1 && activeIndex > 0"
                      type="button"
                      class="absolute left-2 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center transition-all cursor-pointer"
                      @click="prevImage"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" /></svg>
                    </button>

                    <!-- Right Arrow -->
                    <button
                      v-if="allImages.length > 1 && activeIndex < allImages.length - 1"
                      type="button"
                      class="absolute right-2 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center transition-all cursor-pointer"
                      @click="nextImage"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg>
                    </button>

                    <!-- Dot Indicators -->
                    <div v-if="allImages.length > 1" class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 z-20">
                      <button
                        v-for="(_, idx) in allImages"
                        :key="idx"
                        type="button"
                        class="rounded-full transition-all cursor-pointer"
                        :class="activeIndex === idx ? 'w-5 h-2 bg-white' : 'w-2 h-2 bg-white/50 hover:bg-white/80'"
                        @click="activeIndex = idx"
                      />
                    </div>

                    <!-- Main Image Badge (first image only) -->
                    <div v-if="activeIndex === 0" class="absolute top-3 right-3 z-20 bg-brand-choco/80 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                      Main
                    </div>

                    <!-- Product Badges -->
                    <div class="absolute top-3 left-3 flex flex-col gap-1.5 items-start z-10">
                      <BaseBadge v-if="modalStore.product.is_best_seller" variant="brand">Best Seller</BaseBadge>
                      <BaseBadge v-else-if="modalStore.product.is_highly_rated" variant="warning">⭐ Highly Rated</BaseBadge>
                      <BaseBadge v-else-if="modalStore.product.is_featured" variant="neutral">Featured</BaseBadge>
                      <BaseBadge v-else-if="modalStore.product.is_new_arrival" variant="success">New</BaseBadge>
                      <BaseBadge v-if="modalStore.product.is_on_sale" variant="error">Sale</BaseBadge>
                    </div>
                  </div>

                  <!-- Thumbnail Strip -->
                  <div v-if="allImages.length > 1" class="flex gap-2 overflow-x-auto no-scrollbar px-1 pb-1">
                    <button
                      v-for="(img, idx) in allImages"
                      :key="idx"
                      type="button"
                      class="w-16 h-16 rounded-xl border-2 shrink-0 overflow-hidden cursor-pointer transition-all relative"
                      :class="activeIndex === idx ? 'border-brand-choco scale-95 shadow-md' : 'border-brand-caramel/20 opacity-60 hover:opacity-100'"
                      @click="activeIndex = idx"
                    >
                      <img :src="img" :alt="`Thumb ${idx + 1}`" class="w-full h-full object-cover" />
                      <!-- Main image indicator badge on thumbnail -->
                      <div v-if="idx === 0" class="absolute bottom-0.5 left-0.5 bg-brand-choco text-white text-[8px] font-bold px-1 rounded leading-tight">
                        Main
                      </div>
                    </button>
                  </div>

                  <!-- Wishlist Button -->
                  <div class="flex items-center justify-end pt-2 border-t border-brand-caramel/15 text-xs">
                    <button
                      type="button"
                      class="flex items-center gap-1.5 cursor-pointer transition-colors font-semibold"
                      :class="wishlistStore.isInWishlist(modalStore.product.id) ? 'text-red-500 font-bold' : 'text-warm-gray hover:text-red-500'"
                      @click="wishlistStore.toggleWishlist(modalStore.product)"
                    >
                      <svg class="w-4 h-4" :fill="wishlistStore.isInWishlist(modalStore.product.id) ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                      </svg>
                      <span>{{ wishlistStore.isInWishlist(modalStore.product.id) ? 'Saved to Wishlist' : 'Save to Wishlist' }}</span>
                    </button>
                  </div>
                </div>

                <!-- Right Column: Product Info, Price, Quantity & Add to Cart -->
                <div class="md:col-span-7 space-y-5">
                  <div>
                    <div class="flex items-center gap-3 mb-1">
                      <span v-if="modalStore.product.category?.name" class="text-xs font-bold uppercase tracking-wider text-brand-caramel">
                        {{ modalStore.product.category.name }}
                      </span>

                      <span
                        v-if="modalStore.product.reviews_count && modalStore.product.reviews_count > 0"
                        class="inline-flex items-center gap-1 bg-surface border border-brand-caramel/30 px-2.5 py-0.5 rounded-full text-xs font-extrabold text-brand-choco"
                      >
                        <span>⭐ {{ modalStore.product.avg_rating }}</span>
                        <span class="text-warm-gray font-normal">({{ modalStore.product.reviews_count }} reviews)</span>
                      </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight leading-snug">
                      {{ modalStore.product.name }}
                    </h1>
                  </div>

                  <!-- Price Container -->
                  <div class="bg-surface p-4 rounded-xl border border-brand-caramel/20 dark:border-[#C08E5D]/20 flex flex-wrap items-baseline gap-3">
                    <template v-if="hasPrice">
                      <span class="text-3xl font-extrabold text-brand-choco dark:text-[#E2C08A]">
                        ₱{{ effectivePrice.toFixed(2) }}
                      </span>
                      <span v-if="modalStore.product.is_on_sale && !selectedVariation" class="text-base text-warm-gray line-through">
                        ₱{{ modalStore.product.price.toFixed(2) }}
                      </span>
                      <BaseBadge v-if="modalStore.product.is_on_sale && !selectedVariation" variant="error">
                        Save ₱{{ (modalStore.product.price - modalStore.product.sale_price).toFixed(2) }}
                      </BaseBadge>
                    </template>
                    <template v-else>
                      <span class="text-lg font-semibold text-warm-gray dark:text-[#C5B4A4] italic">Price on Request</span>
                      <span class="text-xs text-warm-gray dark:text-[#A89686]">Contact us for pricing</span>
                    </template>
                  </div>

                  <p class="text-xs sm:text-sm text-warm-gray leading-relaxed">
                    {{ modalStore.product.short_description }}
                  </p>

                  <!-- Prep & Stock Quick Stats -->
                  <div class="grid grid-cols-2 gap-4 py-3 border-y border-brand-caramel/20">
                    <div class="flex items-center gap-3">
                      <div class="w-9 h-9 rounded-xl bg-brand-tan/20 flex items-center justify-center text-brand-choco">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                      </div>
                      <div>
                        <div class="text-[11px] text-warm-gray">Baking Prep Time</div>
                        <div class="text-xs font-bold text-ink">{{ modalStore.product.prep_time_minutes || 20 }} minutes</div>
                      </div>
                    </div>

                    <div class="flex items-center gap-3">
                      <div class="w-9 h-9 rounded-xl bg-brand-tan/20 flex items-center justify-center text-brand-choco">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1H5zm0 0v9a2 2 0 002 2h10a2 2 0 002-2V8M9 12h6" /></svg>
                      </div>
                      <div>
                        <div class="text-[11px] text-warm-gray">Stock Status</div>
                        <div class="text-xs font-bold" :class="modalStore.product.is_in_stock ? 'text-success' : 'text-error'">
                          {{ modalStore.product.is_in_stock ? 'In Stock' : 'Out of Stock' }}
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Allergen Chips (Only if product has allergens) -->
                  <div v-if="modalStore.product.allergens && modalStore.product.allergens.length > 0" class="space-y-1.5">
                    <span class="block text-xs font-bold uppercase tracking-wider text-brand-choco dark:text-[#E2C08A]">Allergen Information</span>
                    <div class="flex flex-wrap gap-2">
                      <span
                        v-for="(alg, idx) in modalStore.product.allergens"
                        :key="idx"
                        class="px-2.5 py-1 rounded-lg bg-warning/10 text-warning text-xs font-semibold border border-warning/20"
                      >
                        ⚠️ {{ alg.name }} ({{ alg.type }})
                      </span>
                    </div>
                  </div>

                  <!-- Flavor Selector (if product has multiple encoded flavors) -->
                  <div v-if="modalStore.product.flavors && modalStore.product.flavors.length > 0" class="space-y-2">
                    <span class="block text-xs font-bold uppercase tracking-wider text-brand-choco dark:text-[#E2C08A]">
                      Select Flavor <span class="text-[10px] font-normal text-warm-gray dark:text-[#A89686] lowercase">(optional)</span>
                    </span>
                    <div class="flex flex-wrap gap-2">
                      <button
                        v-for="(flv, idx) in modalStore.product.flavors"
                        :key="idx"
                        type="button"
                        class="px-3.5 py-1.5 rounded-xl border-2 text-xs font-bold transition-all cursor-pointer"
                        :class="selectedFlavorIdx === idx
                          ? 'border-amber-600 bg-amber-600 text-white dark:border-amber-400 dark:bg-amber-400 dark:text-[#1C1410]'
                          : 'border-amber-200 dark:border-amber-900/50 text-amber-900 dark:text-amber-200 bg-amber-50/50 dark:bg-amber-950/30 hover:border-amber-500'"
                        @click="selectedFlavorIdx = selectedFlavorIdx === idx ? null : idx"
                      >
                        {{ flv.name }}
                        <span v-if="flv.price_modifier && flv.price_modifier !== 0" class="ml-1 font-normal opacity-80">
                          {{ flv.price_modifier > 0 ? '+' : '' }}₱{{ Number(flv.price_modifier).toFixed(2) }}
                        </span>
                      </button>
                    </div>
                  </div>

                  <!-- Single Flavor Badge (if product has static single flavor string) -->
                  <div v-else-if="modalStore.product.flavor" class="space-y-1.5">
                    <span class="block text-xs font-bold uppercase tracking-wider text-brand-choco dark:text-[#E2C08A]">Flavor Profile</span>
                    <div class="px-3.5 py-2 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 font-bold text-xs border border-amber-200 dark:border-amber-800/50 flex items-center gap-2">
                      <span>✨ {{ modalStore.product.flavor }}</span>
                    </div>
                  </div>

                  <!-- Variation Selector (if product has variations like grams, pieces, size) -->
                  <div v-if="modalStore.product.variation_type && modalStore.product.variation_type !== 'none' && modalStore.product.variations && modalStore.product.variations.length > 0" class="space-y-2">
                    <span class="block text-xs font-bold uppercase tracking-wider text-brand-choco dark:text-[#E2C08A]">
                      {{ variationLabel }}
                    </span>
                    <div class="flex flex-wrap gap-2">
                      <button
                        v-for="(v, idx) in modalStore.product.variations"
                        :key="idx"
                        type="button"
                        class="px-3.5 py-1.5 rounded-xl border-2 text-xs font-bold transition-all cursor-pointer"
                        :class="selectedVariationIdx === idx
                          ? 'border-brand-choco bg-brand-choco text-white dark:border-[#E2C08A] dark:bg-[#E2C08A] dark:text-[#1C1410]'
                          : 'border-brand-caramel/30 dark:border-[#C08E5D]/30 text-brand-choco dark:text-[#E2C08A] hover:border-brand-choco dark:hover:border-[#E2C08A]'"
                        @click="selectedVariationIdx = idx"
                      >
                        {{ v.label }}
                        <span v-if="v.price_modifier && v.price_modifier !== 0" class="ml-1 font-normal opacity-80">
                          {{ v.price_modifier > 0 ? '+' : '' }}₱{{ Number(v.price_modifier).toFixed(2) }}
                        </span>
                      </button>
                    </div>
                  </div>

                  <!-- Quantity Selector & Action Buttons -->
                  <div class="space-y-4 pt-1">
                    <div class="flex items-center gap-3">
                      <span class="text-xs text-warm-gray dark:text-[#C5B4A4] font-bold uppercase tracking-wider">Quantity:</span>
                      <div class="flex items-center border border-brand-caramel/30 dark:border-[#C08E5D]/30 rounded-xl bg-white dark:bg-[#140D09] p-0.5">
                        <button
                          type="button"
                          class="w-8 h-8 rounded-lg flex items-center justify-center text-brand-choco dark:text-[#E2C08A] hover:bg-surface dark:hover:bg-[#2A1C13] font-extrabold text-sm disabled:opacity-30 cursor-pointer"
                          :disabled="quantity <= 1"
                          @click="quantity--"
                        >
                          -
                        </button>
                        <span class="w-10 text-center font-bold text-ink dark:text-[#FBF3E7] text-sm">{{ quantity }}</span>
                        <button
                          type="button"
                          class="w-8 h-8 rounded-lg flex items-center justify-center text-brand-choco dark:text-[#E2C08A] hover:bg-surface dark:hover:bg-[#2A1C13] font-extrabold text-sm cursor-pointer"
                          @click="quantity++"
                        >
                          +
                        </button>
                      </div>
                      <span v-if="modalStore.product.stock_qty" class="text-xs text-warm-gray dark:text-[#C5B4A4]">
                        ({{ modalStore.product.stock_qty }} available)
                      </span>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-3">
                      <button
                        type="button"
                        class="w-full sm:flex-1 px-5 py-3 rounded-xl border-2 border-brand-choco dark:border-[#C08E5D] bg-surface dark:bg-[#140D09] hover:bg-brand-tan/20 text-brand-choco dark:text-[#E2C08A] font-bold text-sm flex items-center justify-center gap-2 transition-all cursor-pointer shadow-xs disabled:opacity-50"
                        :disabled="adding || !modalStore.product.is_in_stock"
                        @click="handleAddToCart"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 0a2 2 0 100 4 2 2 0 000-4z" /></svg>
                        <span>Add To Cart</span>
                      </button>

                      <button
                        type="button"
                        class="w-full sm:flex-1 px-6 py-3 rounded-xl bg-brand-choco hover:bg-[#442917] dark:bg-[#C08E5D] dark:hover:bg-[#A07245] text-surface dark:text-[#1C1410] font-extrabold text-sm flex items-center justify-center transition-all cursor-pointer shadow-sm disabled:opacity-50"
                        :disabled="adding || !modalStore.product.is_in_stock"
                        @click="handleBuyNow"
                      >
                        Buy Now<template v-if="hasPrice"> • ₱{{ (effectivePrice * quantity).toFixed(2) }}</template>
                      </button>
                    </div>
                  </div>

                </div>

              </div>

              <!-- Product Details / Description Section -->
              <div
                v-if="modalStore.product.description || modalStore.product.nutrition"
                class="bg-white dark:bg-[#1E1510] rounded-2xl p-4 sm:p-6 shadow-sm border border-brand-caramel/20 dark:border-[#C08E5D]/20 space-y-4"
              >
                <div class="flex border-b border-brand-caramel/20 dark:border-[#C08E5D]/20 gap-6">
                  <button
                    type="button"
                    class="pb-3 text-sm font-bold transition-all border-b-2"
                    :class="activeTab === 'description' ? 'border-brand-choco dark:border-[#E2C08A] text-brand-choco dark:text-[#E2C08A]' : 'border-transparent text-warm-gray dark:text-[#C5B4A4] hover:text-brand-choco dark:hover:text-[#E2C08A]'"
                    @click="activeTab = 'description'"
                  >
                    Product Description
                  </button>

                  <button
                    v-if="modalStore.product.nutrition"
                    type="button"
                    class="pb-3 text-sm font-bold transition-all border-b-2"
                    :class="activeTab === 'nutrition' ? 'border-brand-choco dark:border-[#E2C08A] text-brand-choco dark:text-[#E2C08A]' : 'border-transparent text-warm-gray dark:text-[#C5B4A4] hover:text-brand-choco dark:hover:text-[#E2C08A]'"
                    @click="activeTab = 'nutrition'"
                  >
                    Nutrition Facts
                  </button>
                </div>

                <!-- Description Tab Content -->
                <div v-if="activeTab === 'description'" class="text-xs sm:text-sm text-ink/90 dark:text-[#FBF3E7]/90 leading-relaxed space-y-2">
                  <div v-html="modalStore.product.description || modalStore.product.short_description"></div>
                </div>

                <!-- Nutrition Tab Content -->
                <div v-else-if="activeTab === 'nutrition' && modalStore.product.nutrition" class="max-w-xs">
                  <div class="border border-ink dark:border-[#E2C08A] p-3 rounded-xl space-y-1.5 text-ink dark:text-[#FBF3E7] text-xs">
                    <h4 class="font-extrabold text-base border-b-2 border-ink dark:border-[#E2C08A] pb-1">Nutrition Facts</h4>
                    <p class="text-[11px] font-semibold">Serving Size: {{ modalStore.product.nutrition.serving_size }}</p>
                    <div class="border-t-4 border-ink dark:border-[#E2C08A] my-1 pt-1 flex justify-between font-extrabold text-sm">
                      <span>Calories</span>
                      <span>{{ modalStore.product.nutrition.calories }}</span>
                    </div>
                    <div class="border-t border-ink dark:border-[#E2C08A] pt-1 text-[11px] space-y-1">
                      <div class="flex justify-between font-semibold">
                        <span>Total Fat</span>
                        <span>{{ modalStore.product.nutrition.fat_g }}g</span>
                      </div>
                      <div class="flex justify-between font-semibold">
                        <span>Carbohydrates</span>
                        <span>{{ modalStore.product.nutrition.carbs_g }}g</span>
                      </div>
                      <div class="flex justify-between font-semibold">
                        <span>Protein</span>
                        <span>{{ modalStore.product.nutrition.protein_g }}g</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Ratings & Customer Reviews Component -->
              <div class="bg-white dark:bg-[#1E1510] rounded-2xl p-4 sm:p-6 shadow-sm border border-brand-caramel/20 dark:border-[#C08E5D]/20">
                <ProductReviews :product-id="modalStore.product.id" />
              </div>

            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useProductModalStore } from '@/stores/productModal'
import { useCartStore } from '@/stores/cart'
import { useWishlistStore } from '@/stores/wishlist'
import { useToast } from '@/composables/useToast'
import BaseBadge from '@/components/ui/BaseBadge.vue'
import ProductReviews from '@/components/storefront/ProductReviews.vue'

const modalStore = useProductModalStore()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()
const toast = useToast()
const router = useRouter()

const activeIndex = ref(0)
const activeTab = ref('description')
const quantity = ref(1)
const adding = ref(false)

// Touch swipe state
const touchStartX = ref(0)
const touchCurrentX = ref(0)
const selectedVariationIdx = ref(null)

const allImages = computed(() => {
  if (!modalStore.product) return []
  const list = []
  if (modalStore.product.primary_image_url) {
    list.push(modalStore.product.primary_image_url)
  }
  if (modalStore.product.gallery_images && Array.isArray(modalStore.product.gallery_images)) {
    modalStore.product.gallery_images.forEach(img => {
      if (!list.includes(img)) list.push(img)
    })
  }
  return list.length > 0 ? list : ['/images/placeholder-bakery.png']
})

const selectedFlavorIdx = ref(null)

const variationLabel = computed(() => {
  const type = modalStore.product?.variation_type
  if (!type || type === 'none') return 'Select Option'

  const lower = type.toLowerCase().trim()
  if (lower === 'flavor') return 'Select Option / Quantity'

  if (lower.startsWith('select ')) {
    return type
  }

  const known = {
    weight: 'Select Weight / Grams',
    pieces: 'Select Quantity (Pieces)',
    size: 'Select Size',
    packaging: 'Select Packaging',
    bundle: 'Select Bundle',
  }

  if (known[lower]) {
    return known[lower]
  }

  const capitalized = type.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ')
  return `Select ${capitalized}`
})

const selectedVariation = computed(() => {
  if (selectedVariationIdx.value === null) return null
  return modalStore.product?.variations?.[selectedVariationIdx.value] ?? null
})

const selectedFlavor = computed(() => {
  if (selectedFlavorIdx.value === null) return null
  return modalStore.product?.flavors?.[selectedFlavorIdx.value] ?? null
})

const effectivePrice = computed(() => {
  const base = modalStore.product?.sale_price || modalStore.product?.price || 0
  const varMod = selectedVariation.value?.price_modifier ?? 0
  const flvMod = selectedFlavor.value?.price_modifier ?? 0
  return parseFloat(base) + parseFloat(varMod) + parseFloat(flvMod)
})

const hasPrice = computed(() => {
  return modalStore.product?.price && parseFloat(modalStore.product.price) > 0
})

watch(() => modalStore.product, (newVal) => {
  if (newVal) {
    activeIndex.value = 0
    quantity.value = 1
    activeTab.value = 'description'
    selectedVariationIdx.value = null
    selectedFlavorIdx.value = null
  }
}, { immediate: true })

function prevImage() {
  if (activeIndex.value > 0) activeIndex.value--
}

function nextImage() {
  if (activeIndex.value < allImages.value.length - 1) activeIndex.value++
}

function onTouchStart(e) {
  touchStartX.value = e.touches[0].clientX
  touchCurrentX.value = e.touches[0].clientX
}

function onTouchMove(e) {
  touchCurrentX.value = e.touches[0].clientX
}

function onTouchEnd() {
  const diff = touchStartX.value - touchCurrentX.value
  if (Math.abs(diff) > 40) {
    if (diff > 0) nextImage()
    else prevImage()
  }
}

async function handleAddToCart() {
  if (!modalStore.product) return
  adding.value = true
  const chosenFlavor = selectedFlavor.value ? selectedFlavor.value.name : (modalStore.product.flavor || null)
  const options = {
    ...(chosenFlavor ? { flavor: chosenFlavor } : {}),
    ...(selectedFlavor.value?.price_modifier ? { flavor_price_modifier: parseFloat(selectedFlavor.value.price_modifier) } : {}),
    ...(selectedVariation.value ? {
      variation: selectedVariation.value.label,
      price_modifier: parseFloat(selectedVariation.value.price_modifier || 0),
    } : {}),
    unit_price: parseFloat(effectivePrice.value)
  }
  const res = await cartStore.addItem(modalStore.product.id, quantity.value, options)
  adding.value = false

  if (res.success) {
    toast.success(`Added ${quantity.value}x ${modalStore.product.name} to your basket!`, 'Freshly Baked')
    modalStore.closeModal()
  }
}

async function handleBuyNow() {
  if (!modalStore.product) return
  adding.value = true
  const chosenFlavor = selectedFlavor.value ? selectedFlavor.value.name : (modalStore.product.flavor || null)
  const options = {
    ...(chosenFlavor ? { flavor: chosenFlavor } : {}),
    ...(selectedFlavor.value?.price_modifier ? { flavor_price_modifier: parseFloat(selectedFlavor.value.price_modifier) } : {}),
    ...(selectedVariation.value ? {
      variation: selectedVariation.value.label,
      price_modifier: parseFloat(selectedVariation.value.price_modifier || 0),
    } : {}),
    unit_price: parseFloat(effectivePrice.value)
  }
  const res = await cartStore.addItem(modalStore.product.id, quantity.value, options)
  adding.value = false

  if (res.success) {
    toast.success(`Added to basket! Redirecting to checkout...`, 'Instant Purchase')
    modalStore.closeModal()
    router.push('/checkout')
  }
}
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

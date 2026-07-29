<template>
  <div class="page-container py-10 md:py-16">
    <!-- Skeleton Loading -->
    <div v-if="loading" class="grid grid-cols-1 lg:grid-cols-2 gap-12">
      <SkeletonBlock height="450px" radius="1.5rem" />
      <div class="space-y-6">
        <SkeletonBlock height="2rem" width="40%" radius="0.5rem" />
        <SkeletonBlock height="3rem" width="80%" radius="0.5rem" />
        <SkeletonBlock height="1.5rem" width="30%" radius="0.5rem" />
        <SkeletonText :lines="4" />
        <SkeletonBlock height="3.5rem" radius="1rem" />
      </div>
    </div>

    <!-- Product Not Found -->
    <div v-else-if="!product" class="py-16 text-center">
      <EmptyState
        title="Product Not Found"
        description="The pastry you are looking for might have been moved or is no longer available."
      >
        <template #action>
          <BaseButton variant="primary" @click="$router.push('/shop')">
            Return to Shop
          </BaseButton>
        </template>
      </EmptyState>
    </div>

    <!-- Main Product View -->
    <div v-else class="space-y-16">
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-xs font-semibold text-[#8C7A68]">
        <RouterLink to="/" class="hover:text-[#5C3A22]">Home</RouterLink>
        <span>/</span>
        <RouterLink to="/shop" class="hover:text-[#5C3A22]">Shop</RouterLink>
        <span>/</span>
        <span class="text-[#5C3A22]">{{ product.category?.name }}</span>
      </nav>

      <!-- Grid Layout -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-14 items-start">

        <!-- Left Image Showcase -->
        <div class="space-y-4">
          <div class="aspect-square rounded-3xl overflow-hidden bg-white border border-[#C08E5D]/20 shadow-md relative">
            <img
              :src="activeImage || product.primary_image_url"
              :alt="product.name"
              class="w-full h-full object-cover object-center transition-all duration-300"
            />
            <div class="absolute top-4 left-4 flex flex-col gap-2">
              <BaseBadge v-if="product.is_best_seller" variant="brand">Best Seller</BaseBadge>
              <BaseBadge v-else-if="product.is_highly_rated" variant="warning">⭐ Highly Rated</BaseBadge>
              <BaseBadge v-if="product.is_on_sale" variant="error">Sale</BaseBadge>
              <BaseBadge v-if="product.is_seasonal" variant="warning">Seasonal</BaseBadge>
            </div>
          </div>

          <!-- Gallery Thumbnails -->
          <div v-if="product.gallery_images && product.gallery_images.length > 0" class="flex items-center gap-3 overflow-x-auto pb-2">
            <button
              type="button"
              v-tooltip="'View main product photo'"
              class="w-20 h-20 rounded-xl overflow-hidden border-2 transition-all flex-shrink-0"
              :class="activeImage === product.primary_image_url ? 'border-[#5C3A22] scale-95' : 'border-[#C08E5D]/20 opacity-70 hover:opacity-100'"
              @click="activeImage = product.primary_image_url"
            >
              <img :src="product.primary_image_url" class="w-full h-full object-cover" />
            </button>
            <button
              v-for="(img, idx) in product.gallery_images"
              :key="idx"
              type="button"
              v-tooltip="`View gallery photo ${idx + 2}`"
              class="w-20 h-20 rounded-xl overflow-hidden border-2 transition-all flex-shrink-0"
              :class="activeImage === img ? 'border-[#5C3A22] scale-95' : 'border-[#C08E5D]/20 opacity-70 hover:opacity-100'"
              @click="activeImage = img"
            >
              <img :src="img" class="w-full h-full object-cover" />
            </button>
          </div>
        </div>

        <!-- Right Product Info Column -->
        <div class="space-y-6">
          <div>
            <div class="flex items-center gap-3 mb-1">
              <span class="text-xs font-bold uppercase tracking-wider text-[#C08E5D]">
                {{ product.category?.name }}
              </span>
              <a
                v-if="product.reviews_count && product.reviews_count > 0"
                href="#reviews"
                v-tooltip="`Read all ${product.reviews_count} verified customer reviews`"
                class="inline-flex items-center gap-1 bg-[#FBF3E7] border border-[#C08E5D]/30 px-2.5 py-0.5 rounded-full text-xs font-extrabold text-[#5C3A22] hover:bg-[#D9A876]/30 transition-colors"
              >
                <span>⭐ {{ product.avg_rating }}</span>
                <span class="text-[#8C7A68] font-normal">({{ product.reviews_count }} reviews)</span>
              </a>
            </div>

            <h1 class="text-3xl md:text-4xl font-extrabold text-[#1C1410] tracking-tight mb-3">
              {{ product.name }}
            </h1>

            <!-- Price Row -->
            <div class="flex items-baseline gap-3 mb-4">
              <span class="text-3xl font-extrabold text-[#5C3A22]">
                ₱{{ (product.sale_price || product.price).toFixed(2) }}
              </span>
              <span v-if="product.is_on_sale" class="text-lg text-[#8C7A68] line-through">
                ₱{{ product.price.toFixed(2) }}
              </span>
              <BaseBadge v-if="product.is_on_sale" variant="error">Save ₱{{ (product.price - product.sale_price).toFixed(2) }}</BaseBadge>
            </div>

            <p class="text-[#8C7A68] text-base leading-relaxed">
              {{ product.short_description }}
            </p>
          </div>

          <!-- Quick Stats / Prep info -->
          <div class="grid grid-cols-2 gap-4 py-4 border-y border-[#C08E5D]/20">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-[#D9A876]/20 flex items-center justify-center text-[#5C3A22]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              </div>
              <div>
                <div class="text-xs text-[#8C7A68]">Baking Prep Time</div>
                <div class="text-sm font-bold text-[#1C1410]">{{ product.prep_time_minutes }} minutes</div>
              </div>
            </div>

            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-[#D9A876]/20 flex items-center justify-center text-[#5C3A22]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1H5zm0 0v9a2 2 0 002 2h10a2 2 0 002-2V8M9 12h6" /></svg>
              </div>
              <div>
                <div class="text-xs text-[#8C7A68]">Stock Status</div>
                <div class="text-sm font-bold" :class="product.is_in_stock ? 'text-[#6B8F5E]' : 'text-[#B84C3C]'">
                  {{ product.is_in_stock ? 'Oven Fresh In Stock' : 'Out of Stock' }}
                </div>
              </div>
            </div>
          </div>

          <!-- Allergen Chips -->
          <div v-if="product.allergens && product.allergens.length > 0" class="space-y-2">
            <span class="block text-xs font-semibold uppercase tracking-wider text-[#5C3A22]">Allergen Information</span>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="(alg, idx) in product.allergens"
                :key="idx"
                v-tooltip="`May contain ${alg.name} allergen — ${alg.type}. Please inform us of any dietary restrictions before ordering.`"
                class="px-2.5 py-1 rounded-lg bg-[#C98A3A]/10 text-[#C98A3A] text-xs font-semibold border border-[#C98A3A]/20 cursor-help"
              >
                ⚠️ {{ alg.name }} ({{ alg.type }})
              </span>
            </div>
          </div>

          <!-- Quantity Selector & Add to Cart -->
          <div class="space-y-4 pt-2">
            <div class="flex items-center gap-4">
              <div class="flex items-center border border-[#C08E5D]/30 rounded-xl bg-white p-1">
                <button
                  type="button"
                  v-tooltip="'Decrease quantity'"
                  class="w-9 h-9 rounded-lg flex items-center justify-center text-[#5C3A22] hover:bg-[#FBF3E7] font-bold text-lg disabled:opacity-30"
                  :disabled="quantity <= 1"
                  @click="quantity--"
                >
                  -
                </button>
                <span v-tooltip="`Ordering ${quantity} of this item`" class="w-10 text-center font-bold text-[#1C1410] cursor-help">{{ quantity }}</span>
                <button
                  type="button"
                  v-tooltip="'Increase quantity'"
                  class="w-9 h-9 rounded-lg flex items-center justify-center text-[#5C3A22] hover:bg-[#FBF3E7] font-bold text-lg"
                  @click="quantity++"
                >
                  +
                </button>
              </div>

              <div class="flex-1">
                <BaseButton
                  variant="primary"
                  full-width
                  size="lg"
                  :loading="adding"
                  :disabled="!product.is_in_stock"
                  v-tooltip="product.is_in_stock ? 'Add to your bakery basket — review before checkout' : 'Currently out of stock — check back soon!'"
                  @click="addToCart"
                >
                  Add {{ quantity }} to Order • ₱{{ ((product.sale_price || product.price) * quantity).toFixed(2) }}
                </BaseButton>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Tabbed Section: Full Description & Nutrition Facts -->
      <div class="bg-white rounded-3xl p-6 md:p-10 border border-[#C08E5D]/20 shadow-sm space-y-8">
        <!-- Tabs Bar -->
        <div class="flex border-b border-[#C08E5D]/20 gap-8">
          <button
            type="button"
            v-tooltip="'Full product backstory, ingredients &amp; artisan baking details'"
            class="pb-4 text-base font-bold transition-all border-b-2"
            :class="activeTab === 'description' ? 'border-[#5C3A22] text-[#5C3A22]' : 'border-transparent text-[#8C7A68] hover:text-[#5C3A22]'"
            @click="activeTab = 'description'"
          >
            Product Story &amp; Details
          </button>

          <button
            v-if="product.nutrition"
            type="button"
            v-tooltip="'Calories, macros, serving size &amp; dietary info'"
            class="pb-4 text-base font-bold transition-all border-b-2"
            :class="activeTab === 'nutrition' ? 'border-[#5C3A22] text-[#5C3A22]' : 'border-transparent text-[#8C7A68] hover:text-[#5C3A22]'"
            @click="activeTab = 'nutrition'"
          >
            Nutrition Facts
          </button>
        </div>

        <!-- Description Tab Content -->
        <div v-if="activeTab === 'description'" class="prose max-w-none text-[#1C1410]/90 leading-relaxed space-y-4">
          <div v-html="product.description || product.short_description"></div>
        </div>

        <!-- Nutrition Tab Content -->
        <div v-else-if="activeTab === 'nutrition' && product.nutrition" class="max-w-md space-y-4">
          <div class="border border-[#1C1410] p-4 rounded-xl space-y-2 text-[#1C1410]">
            <h3 class="font-extrabold text-2xl border-b-4 border-[#1C1410] pb-1">Nutrition Facts</h3>
            <p class="text-xs font-semibold">Serving Size: {{ product.nutrition.serving_size }}</p>
            <div class="border-t-8 border-[#1C1410] my-2 pt-2 flex justify-between items-baseline font-extrabold text-lg">
              <span>Amount Per Serving</span>
              <span>Calories {{ product.nutrition.calories }}</span>
            </div>
            <div class="border-t border-[#1C1410] pt-1 text-xs space-y-1">
              <div class="flex justify-between font-bold">
                <span>Total Fat</span>
                <span>{{ product.nutrition.fat_g }}g</span>
              </div>
              <div class="flex justify-between font-bold">
                <span>Total Carbohydrates</span>
                <span>{{ product.nutrition.carbs_g }}g</span>
              </div>
              <div class="flex justify-between pl-4 text-[#8C7A68]">
                <span>Sugars</span>
                <span>{{ product.nutrition.sugar_g }}g</span>
              </div>
              <div class="flex justify-between font-bold">
                <span>Protein</span>
                <span>{{ product.nutrition.protein_g }}g</span>
              </div>
              <div class="flex justify-between font-bold">
                <span>Sodium</span>
                <span>{{ product.nutrition.sodium_mg }}mg</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Customer Reviews Section -->
      <div class="bg-white rounded-3xl p-6 md:p-10 border border-[#C08E5D]/20 shadow-sm">
        <ProductReviews :product-id="product.id" />
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import { useRoute } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { useToast } from '@/composables/useToast'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseBadge from '@/components/ui/BaseBadge.vue'
import SkeletonBlock from '@/components/ui/SkeletonBlock.vue'
import SkeletonText from '@/components/ui/SkeletonText.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import ProductReviews from '@/components/storefront/ProductReviews.vue'

const axios = inject('axios')
const route = useRoute()
const cartStore = useCartStore()
const toast = useToast()

const product = ref(null)
const loading = ref(true)
const quantity = ref(1)
const activeImage = ref('')
const activeTab = ref('description')
const adding = ref(false)

async function fetchProduct() {
  loading.value = true
  try {
    const slug = route.params.slug
    const { data } = await axios.get(`/api/products/${slug}`)
    product.value = data.data
    activeImage.value = product.value.primary_image_url
  } catch (err) {
    console.error('Failed to load product detail', err)
  } finally {
    loading.value = false
  }
}

async function addToCart() {
  if (!product.value) return
  adding.value = true
  const res = await cartStore.addItem(product.value.id, quantity.value)
  adding.value = false
  if (res.success) {
    toast.success(`Added ${quantity.value}x ${product.value.name} to your basket!`, 'Pastry Added')
  }
}

onMounted(() => {
  fetchProduct()
})
</script>

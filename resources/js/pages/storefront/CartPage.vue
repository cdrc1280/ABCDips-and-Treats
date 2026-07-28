<template>
  <div class="page-container py-10 md:py-16">
    <PageHeader
      tagline="your selection"
      title="Shopping Basket"
      subtitle="Review your order details and apply any coupon codes before proceeding to checkout."
    />

    <!-- Empty State -->
    <div v-if="cartStore.items.length === 0" class="bg-white rounded-3xl p-12 border border-[#C08E5D]/20 shadow-sm text-center">
      <EmptyState
        title="Your Pastry Basket is Empty"
        description="You haven't added any fresh baked treats or custom cakes to your order yet."
      >
        <template #action>
          <RouterLink to="/shop">
            <BaseButton variant="primary" size="lg">Explore Menu</BaseButton>
          </RouterLink>
        </template>
      </EmptyState>
    </div>

    <!-- Active Cart View -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

      <!-- Items List Left Column -->
      <div class="lg:col-span-8 space-y-4">
        <!-- Undo Restoration Toast Banner -->
        <Transition name="fade">
          <div v-if="lastRemovedItem" class="bg-[#FBF3E7] border border-[#C08E5D]/30 p-4 rounded-2xl flex items-center justify-between">
            <span class="text-xs font-semibold text-[#5C3A22]">
              Removed "{{ lastRemovedItem.name }}" from your basket.
            </span>
            <button
              class="text-xs font-bold text-[#5C3A22] underline hover:text-[#4A2D1A]"
              @click="undoRemove"
            >
              Undo &amp; Restore
            </button>
          </div>
        </Transition>

        <div class="bg-white rounded-3xl p-6 border border-[#C08E5D]/20 shadow-sm divide-y divide-[#C08E5D]/15">
          <div
            v-for="item in cartStore.items"
            :key="item.id"
            class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
          >
            <div class="flex items-start gap-4">
              <img
                :src="item.image_url || '/images/placeholder-bakery.png'"
                :alt="item.name"
                class="w-20 h-20 rounded-2xl object-cover border border-[#C08E5D]/20 flex-shrink-0"
              />
              <div>
                <h3 class="font-bold text-base text-[#1C1410]">
                  {{ item.options?.is_custom ? item.options.custom_title : item.name }}
                </h3>

                <!-- Custom Spec Box -->
                <div v-if="item.options?.is_custom" class="mt-1 bg-[#FBF3E7] p-3 rounded-xl text-xs text-[#5C3A22] border border-[#C08E5D]/20 space-y-1">
                  <div class="font-extrabold text-[#5C3A22]">🎂 Custom Cake Configuration:</div>
                  <div>Flavor: <strong>{{ item.options.flavor_preference }}</strong></div>
                  <div>Frosting: <strong>{{ item.options.frosting_type }}</strong></div>
                  <div v-if="item.options.budget_range_min" class="text-[#8C7A68]">
                    Preferred Budget: <strong>₱{{ item.options.budget_range_min }} - ₱{{ item.options.budget_range_max }}</strong>
                  </div>
                  <div v-if="item.options.cake_inscription" class="italic text-[#8C7A68]">"{{ item.options.cake_inscription }}"</div>
                  <div v-if="item.options.event_date" class="text-[#8C7A68]">Event Date: {{ item.options.event_date }}</div>
                </div>

                <div v-else class="text-xs text-[#8C7A68] mt-0.5">SKU: {{ item.sku }}</div>
                <div class="text-sm font-semibold text-[#5C3A22] mt-1">₱{{ item.unit_price.toFixed(2) }} each</div>
              </div>
            </div>

            <div class="flex items-center justify-between sm:justify-end gap-6 pt-2 sm:pt-0">
              <!-- Quantity Controls -->
              <div class="flex items-center border border-[#C08E5D]/30 rounded-xl bg-[#FBF3E7]/40 p-1">
                <button
                  class="w-8 h-8 rounded-lg flex items-center justify-center text-[#5C3A22] font-bold hover:bg-[#FBF3E7]"
                  @click="cartStore.updateItem(item.id, item.qty - 1)"
                >
                  -
                </button>
                <span class="w-8 text-center font-bold text-sm text-[#1C1410]">{{ item.qty }}</span>
                <button
                  class="w-8 h-8 rounded-lg flex items-center justify-center text-[#5C3A22] font-bold hover:bg-[#FBF3E7]"
                  @click="cartStore.updateItem(item.id, item.qty + 1)"
                >
                  +
                </button>
              </div>

              <div class="text-right">
                <div class="font-extrabold text-base text-[#5C3A22]">₱{{ item.subtotal.toFixed(2) }}</div>
                <button
                  class="text-xs text-[#B84C3C] hover:underline mt-0.5"
                  @click="handleRemove(item)"
                >
                  Remove
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Order Summary Right Column -->
      <div class="lg:col-span-4 space-y-6">
        <div class="bg-white rounded-3xl p-6 border border-[#C08E5D]/20 shadow-sm space-y-6">
          <h3 class="font-extrabold text-xl text-[#1C1410] border-b border-[#C08E5D]/20 pb-4">
            Order Summary
          </h3>

          <!-- Coupon Form -->
          <div class="space-y-2">
            <label class="block text-xs font-semibold uppercase tracking-wider text-[#5C3A22]">Promo / Coupon Code</label>
            <div v-if="cartStore.couponCode" class="flex items-center justify-between bg-[#6B8F5E]/15 border border-[#6B8F5E]/30 p-3 rounded-xl">
              <div>
                <span class="font-bold text-xs text-[#2D4525]">{{ cartStore.couponCode }}</span>
                <span class="text-xs text-[#6B8F5E] block">Coupon Applied!</span>
              </div>
              <button class="text-xs text-[#B84C3C] font-bold hover:underline" @click="cartStore.removeCoupon">Remove</button>
            </div>

            <div v-else class="flex gap-2">
              <BaseInput
                v-model="couponCode"
                placeholder="Enter coupon code..."
                size="sm"
              />
              <BaseButton size="sm" variant="secondary" :loading="applyingCoupon" @click="applyCoupon">
                Apply
              </BaseButton>
            </div>
          </div>

          <!-- Price Breakdown -->
          <div class="space-y-3 text-sm border-t border-[#C08E5D]/20 pt-4">
            <div class="flex justify-between text-[#8C7A68]">
              <span>Subtotal</span>
              <span class="font-semibold text-[#1C1410]">₱{{ cartStore.subtotal.toFixed(2) }}</span>
            </div>

            <div v-if="cartStore.discount > 0" class="flex justify-between text-[#6B8F5E]">
              <span>Discount ({{ cartStore.couponCode }})</span>
              <span class="font-semibold">-₱{{ cartStore.discount.toFixed(2) }}</span>
            </div>

            <div class="flex justify-between text-[#8C7A68]">
              <span>Estimated Delivery Fee</span>
              <span class="text-xs text-[#5C3A22]">Calculated at Checkout</span>
            </div>

            <div class="flex justify-between text-lg font-extrabold text-[#5C3A22] border-t border-[#C08E5D]/20 pt-3">
              <span>Total Amount</span>
              <span>₱{{ cartStore.total.toFixed(2) }}</span>
            </div>
          </div>

          <RouterLink to="/checkout" class="block">
            <BaseButton variant="primary" full-width size="lg">
              Proceed to Checkout →
            </BaseButton>
          </RouterLink>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useCartStore } from '@/stores/cart'
import PageHeader from '@/components/ui/PageHeader.vue'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import EmptyState from '@/components/ui/EmptyState.vue'

const cartStore = useCartStore()
const couponCode = ref('')
const applyingCoupon = ref(false)
const lastRemovedItem = ref(null)

async function handleRemove(item) {
  lastRemovedItem.value = item
  await cartStore.removeItem(item.id)
}

async function undoRemove() {
  if (lastRemovedItem.value) {
    await cartStore.restoreItem(lastRemovedItem.value.id)
    lastRemovedItem.value = null
  }
}

async function applyCoupon() {
  if (!couponCode.value.trim()) return
  applyingCoupon.value = true
  await cartStore.applyCoupon(couponCode.value)
  applyingCoupon.value = false
  couponCode.value = ''
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>

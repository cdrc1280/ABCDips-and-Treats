<template>
  <div class="page-container py-10 md:py-16">
    <PageHeader
      tagline="your selection"
      title="Shopping Basket"
      subtitle="Review your order details and apply any coupon codes before proceeding to checkout."
    />

    <!-- Empty State -->
    <div v-if="cartStore.items.length === 0" class="bg-white rounded-3xl p-12 border border-brand-caramel/20 shadow-sm text-center">
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
          <div v-if="lastRemovedItem" class="bg-surface border border-brand-caramel/30 p-4 rounded-2xl flex items-center justify-between">
            <span class="text-xs font-semibold text-brand-choco">
              Removed "{{ lastRemovedItem.name }}" from your basket.
            </span>
            <button
              v-tooltip="'Restore this item back to your basket'"
              class="text-xs font-bold text-brand-choco underline hover:text-choco-600"
              @click="undoRemove"
            >
              Undo &amp; Restore
            </button>
          </div>
        </Transition>

        <div class="bg-white rounded-3xl p-6 border border-brand-caramel/20 shadow-sm divide-y divide-brand-caramel/15">
          <div
            v-for="item in cartStore.items"
            :key="item.id"
            class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
          >
            <div class="flex items-start gap-4">
              <img
                :src="item.image_url || '/images/placeholder-bakery.png'"
                :alt="item.name"
                class="w-20 h-20 rounded-2xl object-cover border border-brand-caramel/20 shrink-0"
              />
              <div>
                <h3 class="font-bold text-base text-ink">
                  {{ item.options?.is_custom ? item.options.custom_title : item.name }}
                </h3>

                <!-- Flavor Option Badge -->
                <div v-if="item.options?.flavor" class="text-xs font-semibold text-amber-700 dark:text-amber-300 mt-0.5">
                  Flavor: {{ item.options.flavor }}
                </div>

                <!-- Variation Option Badge -->
                <div v-if="item.options?.variation" class="text-xs font-semibold text-brand-caramel dark:text-[#E2C08A] mt-0.5">
                  Option: {{ item.options.variation }}
                </div>

                <!-- Custom Spec Box -->
                <div v-if="item.options?.is_custom" class="mt-1 bg-surface p-3 rounded-xl text-xs text-brand-choco border border-brand-caramel/20 space-y-1">
                  <div class="font-extrabold text-brand-choco">🎂 Custom Cake Configuration:</div>
                  <div>Flavor: <strong>{{ item.options.flavor_preference }}</strong></div>
                  <div>Frosting: <strong>{{ item.options.frosting_type }}</strong></div>
                  <div v-if="item.options.budget_range_min" class="text-warm-gray">
                    Preferred Budget: <strong>₱{{ item.options.budget_range_min }} - ₱{{ item.options.budget_range_max }}</strong>
                  </div>
                  <div v-if="item.options.cake_inscription" class="italic text-warm-gray">"{{ item.options.cake_inscription }}"</div>
                  <div v-if="item.options.event_date" class="text-warm-gray">Event Date: {{ item.options.event_date }}</div>
                </div>

                <div v-else class="text-xs text-warm-gray mt-0.5">SKU: {{ item.sku }}</div>
                <div class="text-sm font-semibold text-brand-choco mt-1">₱{{ item.unit_price.toFixed(2) }} each</div>
              </div>
            </div>

            <div class="flex items-center justify-between sm:justify-end gap-6 pt-2 sm:pt-0">
              <!-- Quantity Controls -->
              <div class="flex items-center border border-brand-caramel/30 rounded-xl bg-surface/40 p-1">
                <button
                  v-tooltip="'Decrease quantity'"
                  class="w-8 h-8 rounded-lg flex items-center justify-center text-brand-choco font-bold hover:bg-surface"
                  @click="cartStore.updateItem(item.id, item.qty - 1)"
                >
                  -
                </button>
                <span v-tooltip="`${item.qty} item${item.qty > 1 ? 's' : ''} selected`" class="w-8 text-center font-bold text-sm text-ink cursor-help">{{ item.qty }}</span>
                <button
                  v-tooltip="'Increase quantity'"
                  class="w-8 h-8 rounded-lg flex items-center justify-center text-brand-choco font-bold hover:bg-surface"
                  @click="cartStore.updateItem(item.id, item.qty + 1)"
                >
                  +
                </button>
              </div>

              <div class="text-right">
                <div class="font-extrabold text-base text-brand-choco">₱{{ item.subtotal.toFixed(2) }}</div>
                <button
                  v-tooltip="'Remove from basket (you can undo this right after)'"
                  class="text-xs text-error hover:underline mt-0.5"
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
        <div class="bg-white rounded-3xl p-6 border border-brand-caramel/20 shadow-sm space-y-6">
          <h3 class="font-extrabold text-xl text-ink border-b border-brand-caramel/20 pb-4">
            Order Summary
          </h3>

          <!-- Coupon Form -->
          <div class="space-y-2">
            <label class="block text-xs font-semibold uppercase tracking-wider text-brand-choco">Promo / Coupon Code</label>
            <div v-if="cartStore.couponCode" class="flex items-center justify-between bg-success/15 border border-success/30 p-3 rounded-xl">
              <div>
                <span class="font-bold text-xs text-[#2D4525]">{{ cartStore.couponCode }}</span>
                <span class="text-xs text-success block">Coupon Applied!</span>
              </div>
              <button v-tooltip="'Remove discount coupon from this order'" class="text-xs text-error font-bold hover:underline" @click="cartStore.removeCoupon">Remove</button>
            </div>

            <div v-else class="flex gap-2">
              <BaseInput
                v-model="couponCode"
                placeholder="Enter coupon code..."
                size="sm"
                v-tooltip="'Enter a valid discount or promo code'"
              />
              <BaseButton size="sm" variant="secondary" :loading="applyingCoupon" v-tooltip="'Validate and apply coupon to order total'" @click="applyCoupon">
                Apply
              </BaseButton>
            </div>
          </div>

          <!-- Price Breakdown -->
          <div class="space-y-3 text-sm border-t border-brand-caramel/20 pt-4">
            <div class="flex justify-between text-warm-gray">
              <span>Subtotal</span>
              <span class="font-semibold text-ink">₱{{ cartStore.subtotal.toFixed(2) }}</span>
            </div>

            <div v-if="cartStore.discount > 0" class="flex justify-between text-success">
              <span>Discount ({{ cartStore.couponCode }})</span>
              <span class="font-semibold">-₱{{ cartStore.discount.toFixed(2) }}</span>
            </div>

            <div class="flex justify-between text-warm-gray">
              <span v-tooltip="'Standard delivery fee for Metro areas. Free pickup option available at checkout.'" class="cursor-help">Estimated Delivery Fee</span>
              <span class="text-xs text-brand-choco">Calculated at Checkout</span>
            </div>

            <div class="flex justify-between text-lg font-extrabold text-brand-choco border-t border-brand-caramel/20 pt-3">
              <span>Total Amount</span>
              <span>₱{{ cartStore.total.toFixed(2) }}</span>
            </div>
          </div>

          <RouterLink to="/checkout" class="block" v-tooltip="'Finalize your order: shipping, payment &amp; confirmation'">
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
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'
import PageHeader from '@/components/ui/PageHeader.vue'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import EmptyState from '@/components/ui/EmptyState.vue'

const cartStore = useCartStore()
const authStore = useAuthStore()
const router = useRouter()

const couponCode = ref('')
const applyingCoupon = ref(false)
const lastRemovedItem = ref(null)

onMounted(() => {
  if (!authStore.isAuthenticated) {
    router.push({ name: 'login', query: { redirect: '/cart' } })
  }
})

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

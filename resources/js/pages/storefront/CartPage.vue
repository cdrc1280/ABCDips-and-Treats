<template>
  <div class="page-container py-10 md:py-16">
    <PageHeader
      tagline="your selection"
      title="Shopping Basket"
      subtitle="Review your order details, select items for bulk operations, and apply coupon codes before checkout."
    />

    <!-- Empty State -->
    <div v-if="cartStore.items.length === 0" class="bg-white dark:bg-[#1E1510] rounded-3xl p-12 border border-brand-caramel/20 dark:border-[#C08E5D]/20 shadow-sm text-center">
      <EmptyState
        title="Your Pastry Basket is Empty"
        description="You haven't added any fresh baked treats or custom cakes to your order yet."
      >
        <template #action>
          <RouterLink to="/shop">
            <BaseButton variant="primary" size="lg">Explore Bakery Menu</BaseButton>
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
          <div v-if="lastRemovedItem" class="bg-surface dark:bg-[#20140D] border border-brand-caramel/30 dark:border-[#C08E5D]/30 p-4 rounded-2xl flex items-center justify-between shadow-xs">
            <span class="text-xs font-semibold text-brand-choco dark:text-[#E2C08A]">
              Removed "{{ lastRemovedItem.name }}" from your basket.
            </span>
            <button
              v-tooltip="'Restore this item back to your basket'"
              class="text-xs font-bold text-brand-choco dark:text-[#E2C08A] underline hover:text-choco-600 dark:hover:text-[#FBF3E7] transition-colors"
              @click="undoRemove"
            >
              Undo &amp; Restore
            </button>
          </div>
        </Transition>

        <!-- Bulk Selection Bar -->
        <div class="bg-white dark:bg-[#1E1510] rounded-2xl p-4 border border-brand-caramel/20 dark:border-[#C08E5D]/20 shadow-sm flex flex-wrap items-center justify-between gap-4">
          <label class="flex items-center gap-2 text-xs font-bold text-ink dark:text-[#FBF3E7] cursor-pointer select-none">
            <input type="checkbox" :checked="isAllSelected" @change="toggleSelectAll" class="rounded text-brand-choco focus:ring-brand-choco w-4 h-4 accent-brand-choco cursor-pointer" />
            <span>Select All ({{ cartStore.items.length }} Items)</span>
          </label>

          <div v-if="selectedItemIds.length > 0" class="flex items-center gap-2 sm:gap-3">
            <span class="text-xs text-warm-gray dark:text-[#C5B4A4] font-semibold font-mono tabular-nums">{{ selectedItemIds.length }} selected</span>
            <BaseButton size="sm" variant="secondary" v-tooltip="'Edit variations & quantity for selected items'" @click="openBulkEdit">
              Bulk Edit ({{ selectedItemIds.length }})
            </BaseButton>
            <BaseButton size="sm" variant="danger" v-tooltip="'Remove all selected items from basket'" @click="promptRemoveBulk">
              Delete ({{ selectedItemIds.length }})
            </BaseButton>
          </div>
        </div>

        <div class="bg-white dark:bg-[#1E1510] rounded-3xl p-6 border border-brand-caramel/20 dark:border-[#C08E5D]/20 shadow-sm divide-y divide-brand-caramel/15 dark:divide-[#C08E5D]/15">
          <div
            v-for="item in cartStore.items"
            :key="item.id"
            class="py-4.5 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
          >
            <div class="flex items-start gap-3 sm:gap-4 flex-1 min-w-0">
              <input type="checkbox" :value="item.id" v-model="selectedItemIds" class="mt-2 rounded text-brand-choco focus:ring-brand-choco w-4 h-4 accent-brand-choco shrink-0 cursor-pointer" />
              <img
                :src="item.image_url || '/images/placeholder-bakery.png'"
                :alt="item.name"
                class="w-20 h-20 rounded-2xl object-cover border border-brand-caramel/20 dark:border-[#C08E5D]/20 bg-surface/60 shrink-0"
              />
              <div class="flex-1 min-w-0">
                <h3 class="font-bold text-base text-ink dark:text-[#FBF3E7] truncate">
                  {{ item.options?.is_custom ? item.options.custom_title : item.name }}
                </h3>

                <!-- Flavor Option Badge -->
                <div v-if="item.options?.flavors && Array.isArray(item.options.flavors)" class="flex flex-wrap gap-1 mt-1">
                  <span class="text-xs font-bold text-amber-900 dark:text-amber-100 bg-amber-100/90 dark:bg-amber-950/60 px-2 py-0.5 rounded-md border border-amber-200 dark:border-amber-800">
                    Assorted: {{ item.options.flavors.join(', ') }}
                  </span>
                </div>
                <div v-else-if="item.options?.flavor" class="text-xs font-semibold text-amber-700 dark:text-amber-300 mt-0.5">
                  Flavor: {{ item.options.flavor }}
                </div>

                <!-- Variation Option Badge -->
                <div v-if="item.options?.variation" class="text-xs font-semibold text-brand-caramel dark:text-[#E2C08A] mt-0.5">
                  Option: {{ item.options.variation }}
                </div>

                <!-- Custom Spec Box -->
                <div v-if="item.options?.is_custom" class="mt-1.5 bg-surface dark:bg-[#140D09] p-3 rounded-xl text-xs text-brand-choco dark:text-[#E2C08A] border border-brand-caramel/20 dark:border-[#C08E5D]/20 space-y-1">
                  <div class="font-extrabold text-brand-choco dark:text-[#E2C08A] flex items-center gap-1.5"><Cake class="w-4 h-4" /><span>Custom Cake Configuration:</span></div>
                  <div>Flavor: <strong>{{ item.options.flavor_preference }}</strong></div>
                  <div>Frosting: <strong>{{ item.options.frosting_type }}</strong></div>
                  <div v-if="item.options.budget_range_min" class="text-warm-gray dark:text-[#C5B4A4]">
                    Preferred Budget: <strong>₱{{ item.options.budget_range_min }} - ₱{{ item.options.budget_range_max }}</strong>
                  </div>
                  <div v-if="item.options.cake_inscription" class="italic text-warm-gray dark:text-[#C5B4A4]">"{{ item.options.cake_inscription }}"</div>
                  <div v-if="item.options.event_date" class="text-warm-gray dark:text-[#C5B4A4]">Event Date: {{ item.options.event_date }}</div>
                </div>

                <div v-else class="text-xs text-warm-gray dark:text-[#C5B4A4] mt-0.5">SKU: {{ item.sku || 'ABCDIPS-TREAT' }}</div>
                <div class="text-sm font-semibold text-brand-choco dark:text-[#E2C08A] mt-1 font-mono tabular-nums">₱{{ item.unit_price.toFixed(2) }} each</div>
              </div>
            </div>

            <div class="flex items-center justify-between sm:justify-end gap-6 pt-2 sm:pt-0 shrink-0">
              <!-- Quantity Controls -->
              <div class="flex items-center border border-brand-caramel/30 dark:border-[#C08E5D]/30 rounded-xl bg-surface/40 dark:bg-[#140D09] p-1">
                <button
                  v-tooltip="'Decrease quantity'"
                  class="w-8 h-8 rounded-lg flex items-center justify-center text-brand-choco dark:text-[#E2C08A] hover:bg-surface dark:hover:bg-[#1E1510] transition-colors"
                  @click="cartStore.updateItem(item.id, item.qty - 1)"
                >
                  <Minus class="w-3.5 h-3.5" />
                </button>
                <span v-tooltip="`${item.qty} item${item.qty > 1 ? 's' : ''} selected`" class="w-8 text-center font-bold text-sm text-ink dark:text-[#FBF3E7] font-mono tabular-nums">{{ item.qty }}</span>
                <button
                  v-tooltip="'Increase quantity'"
                  class="w-8 h-8 rounded-lg flex items-center justify-center text-brand-choco dark:text-[#E2C08A] hover:bg-surface dark:hover:bg-[#1E1510] transition-colors"
                  @click="cartStore.updateItem(item.id, item.qty + 1)"
                >
                  <Plus class="w-3.5 h-3.5" />
                </button>
              </div>

              <div class="text-right flex flex-col items-end gap-1">
                <div class="font-extrabold text-base text-brand-choco dark:text-[#E2C08A] font-mono tabular-nums">₱{{ item.subtotal.toFixed(2) }}</div>
                <div class="flex items-center gap-3">
                  <button v-if="!item.options?.is_custom"
                    v-tooltip="'Edit variation or flavor options'"
                    class="text-xs text-brand-choco dark:text-[#E2C08A] hover:underline font-semibold flex items-center gap-1"
                    @click="handleEditItem(item)">
                    Edit
                  </button>
                  <button
                    v-tooltip="'Remove from basket (you can undo this right after)'"
                    class="text-xs text-error hover:underline transition-colors"
                    @click="promptRemoveSingle(item)"
                  >
                    Remove
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Order Summary Right Column -->
      <div class="lg:col-span-4 space-y-6">
        <div class="bg-white dark:bg-[#1E1510] rounded-3xl p-6 border border-brand-caramel/20 dark:border-[#C08E5D]/20 shadow-sm space-y-6 sticky top-24">
          <h3 class="font-extrabold text-xl text-ink dark:text-[#FBF3E7] border-b border-brand-caramel/20 dark:border-[#C08E5D]/20 pb-4">
            Order Summary
          </h3>

          <!-- Coupon Form -->
          <div class="space-y-2">
            <label class="block text-xs font-semibold uppercase tracking-wider text-brand-choco dark:text-[#E2C08A]">Promo / Coupon Code</label>
            <div v-if="cartStore.couponCode" class="flex items-center justify-between bg-success/15 border border-success/30 p-3 rounded-xl">
              <div>
                <span class="font-bold text-xs text-[#2D4525] dark:text-emerald-400 uppercase tracking-wide">{{ cartStore.couponCode }}</span>
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
          <div class="space-y-3 text-sm border-t border-brand-caramel/20 dark:border-[#C08E5D]/20 pt-4">
            <div class="flex justify-between text-warm-gray dark:text-[#C5B4A4]">
              <span>Subtotal</span>
              <span class="font-semibold text-ink dark:text-[#FBF3E7] font-mono tabular-nums">₱{{ cartStore.subtotal.toFixed(2) }}</span>
            </div>

            <div v-if="cartStore.discount > 0" class="flex justify-between text-success">
              <span>Discount ({{ cartStore.couponCode }})</span>
              <span class="font-semibold font-mono tabular-nums">-₱{{ cartStore.discount.toFixed(2) }}</span>
            </div>

            <div class="flex justify-between text-warm-gray dark:text-[#C5B4A4]">
              <span v-tooltip="'Standard delivery fee for Metro areas. Free pickup option available at checkout.'" class="cursor-help">Delivery Fee</span>
              <span class="text-xs text-brand-choco dark:text-[#E2C08A] font-semibold">Calculated at Checkout</span>
            </div>

            <div class="flex justify-between text-lg font-extrabold text-brand-choco dark:text-[#E2C08A] border-t border-brand-caramel/20 dark:border-[#C08E5D]/20 pt-3">
              <span>Total Amount</span>
              <span class="font-mono tabular-nums">₱{{ cartStore.total.toFixed(2) }}</span>
            </div>
          </div>

          <RouterLink to="/checkout" class="block" v-tooltip="'Finalize your order: shipping, payment &amp; confirmation'">
            <BaseButton variant="primary" full-width size="lg">
              Proceed to Checkout • ₱{{ cartStore.total.toFixed(2) }}
            </BaseButton>
          </RouterLink>
        </div>
      </div>

    </div>

    <!-- Bulk Edit Modal for Cart Page -->
    <BulkEditCartModal
      :is-open="showBulkEditModal"
      :selected-items="selectedItemsList"
      @close="showBulkEditModal = false"
      @saved="selectedItemIds = []"
    />

    <!-- Confirmation Removal Modal -->
    <ConfirmRemoveModal
      :is-open="showConfirmModal"
      :item-to-remove="pendingRemoveItem"
      :is-bulk-delete="isBulkRemoveAction"
      :bulk-count="selectedItemIds.length"
      :loading="deleting"
      @cancel="showConfirmModal = false"
      @confirm="confirmRemoveAction"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { Plus, Minus, Cake, Trash2 } from 'lucide-vue-next'
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'
import { useProductModalStore } from '@/stores/productModal'
import { useToast } from '@/composables/useToast'
import PageHeader from '@/components/ui/PageHeader.vue'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import BulkEditCartModal from '@/components/storefront/BulkEditCartModal.vue'
import ConfirmRemoveModal from '@/components/storefront/ConfirmRemoveModal.vue'

const cartStore = useCartStore()
const authStore = useAuthStore()
const productModal = useProductModalStore()
const toast = useToast()
const router = useRouter()

const couponCode = ref('')
const applyingCoupon = ref(false)
const lastRemovedItem = ref(null)

const selectedItemIds = ref([])
const showBulkEditModal = ref(false)
const showConfirmModal = ref(false)
const pendingRemoveItem = ref(null)
const isBulkRemoveAction = ref(false)
const deleting = ref(false)

const isAllSelected = computed(() => {
  return cartStore.items.length > 0 && selectedItemIds.value.length === cartStore.items.length
})

const selectedItemsList = computed(() => {
  return cartStore.items.filter(i => selectedItemIds.value.includes(i.id))
})

function toggleSelectAll() {
  if (isAllSelected.value) {
    selectedItemIds.value = []
  } else {
    selectedItemIds.value = cartStore.items.map(i => i.id)
  }
}

function openBulkEdit() {
  if (selectedItemIds.value.length === 0) return
  showBulkEditModal.value = true
}

function promptRemoveSingle(item) {
  pendingRemoveItem.value = item
  isBulkRemoveAction.value = false
  showConfirmModal.value = true
}

function promptRemoveBulk() {
  if (selectedItemIds.value.length === 0) return
  pendingRemoveItem.value = null
  isBulkRemoveAction.value = true
  showConfirmModal.value = true
}

async function confirmRemoveAction() {
  deleting.value = true
  if (isBulkRemoveAction.value) {
    const operations = selectedItemIds.value.map(id => ({ type: 'remove', item_id: id }))
    const count = selectedItemIds.value.length
    const res = await cartStore.batch(operations)
    selectedItemIds.value = []
    deleting.value = false
    showConfirmModal.value = false
    if (res.success) {
      toast.success(`Removed ${count} item${count > 1 ? 's' : ''} from your basket!`, 'Bulk Delete Complete')
    }
  } else if (pendingRemoveItem.value) {
    const item = pendingRemoveItem.value
    lastRemovedItem.value = item
    await cartStore.removeItem(item.id)
    deleting.value = false
    showConfirmModal.value = false
    pendingRemoveItem.value = null
    toast.success(`"${item.name}" has been removed from your basket.`, 'Item Removed')
  }
}

function handleEditItem(item) {
  productModal.openModalForEdit(item)
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
  const res = await cartStore.applyCoupon(couponCode.value)
  applyingCoupon.value = false
  if (res.success) {
    toast.success('Discount coupon applied!', 'Voucher Applied')
    couponCode.value = ''
  } else {
    toast.error(res.error || 'Invalid or expired coupon code.', 'Coupon Error')
  }
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>

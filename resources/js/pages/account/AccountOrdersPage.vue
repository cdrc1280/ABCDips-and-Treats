<template>
  <div class="space-y-8">
    <PageHeader
      tagline="order history"
      title="My Complete Order History"
      subtitle="Track active pastry orders, view kitchen progress, review past purchases, or re-order your favorite treats."
    />

    <!-- Filter Tabs -->
    <div class="flex flex-wrap items-center gap-2 border-b border-brand-caramel/20 pb-4">
      <button
        v-for="tab in filterTabs"
        :key="tab.id"
        @click="activeFilter = tab.id"
        :class="[
          'px-4 py-2 rounded-2xl text-xs font-extrabold transition-all duration-200 flex items-center gap-2',
          activeFilter === tab.id
            ? 'bg-brand-choco text-surface dark:bg-[#C08E5D] dark:text-[#1C1410] shadow-sm'
            : 'bg-white dark:bg-[#1E1510] text-ink dark:text-[#FBF3E7] border border-brand-caramel/20 dark:border-[#C08E5D]/20 hover:bg-surface dark:hover:bg-[#140D09]'
        ]"
      >
        <span>{{ tab.icon }}</span>
        <span>{{ tab.label }}</span>
        <span
          :class="[
            'px-2 py-0.5 rounded-full text-[10px] font-black',
            activeFilter === tab.id ? 'bg-brand-tan text-ink' : 'bg-surface dark:bg-[#140D09] text-brand-choco dark:text-[#E2C08A]'
          ]"
        >
          {{ getFilteredCount(tab.id) }}
        </span>
      </button>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="loading" class="space-y-4">
      <SkeletonCard v-for="n in 3" :key="n" />
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredOrders.length === 0">
      <EmptyState
        title="No Orders Found"
        description="We couldn't find any orders matching the selected filter option."
      >
        <template #action>
          <RouterLink to="/shop"><BaseButton variant="primary">Explore Pastry Menu 🥐</BaseButton></RouterLink>
        </template>
      </EmptyState>
    </div>

    <!-- Orders List -->
    <div v-else class="space-y-6">
      <div
        v-for="order in filteredOrders"
        :key="order.id"
        class="bg-white dark:bg-[#1E1510] rounded-3xl p-6 border border-brand-caramel/20 dark:border-[#C08E5D]/20 shadow-sm space-y-6 transition-all"
      >
        <!-- Header Row -->
        <div class="flex flex-wrap justify-between items-center gap-3 pb-4 border-b border-brand-caramel/15 dark:border-[#C08E5D]/20">
          <div>
            <div class="flex items-center gap-2">
              <span class="font-extrabold text-ink dark:text-[#FBF3E7] text-xl">Order #{{ order.order_number }}</span>
              <BaseBadge :variant="getStatusVariant(order.status)" size="sm">
                {{ order.status_label || order.status }}
              </BaseBadge>
            </div>
            <span class="text-xs text-warm-gray dark:text-[#C5B4A4] block mt-1">
              📅 Placed on {{ formatDate(order.created_at) }} • {{ order.fulfillment_type === 'pickup' ? '🏪 Store Pickup' : '🛵 Doorstep Delivery' }}
            </span>
          </div>

          <div class="flex flex-wrap items-center gap-2">
            <!-- Invoice Button -->
            <BaseButton
              size="sm"
              variant="secondary"
              v-tooltip="'View & download printable official bakery invoice'"
              @click="openInvoiceModal(order)"
            >
              📄 Invoice
            </BaseButton>

            <!-- Re-order button -->
            <BaseButton
              size="sm"
              variant="outline"
              v-tooltip="'Add all items from this order directly back into your shopping cart'"
              @click="reorderItems(order)"
            >
              🔄 Re-order Treats
            </BaseButton>

            <!-- Cancel Button (Only available when status is Pending) -->
            <BaseButton
              v-if="order.status === 'pending' || order.status === 'Pending'"
              size="sm"
              variant="outline"
              v-tooltip="'Cancel pending order'"
              class="!text-error !border-error/40 hover:!bg-red-50"
              @click="openCancelModal(order)"
            >
              🚫 Cancel Order
            </BaseButton>

            <!-- Settle Payment Button if Pooling Settled or Pending Payment -->
            <template v-if="order.delivery_mode === 'pooling' && order.pooling_status !== 'settled'">
              <button type="button" disabled class="px-3 py-1 rounded-full text-[11px] font-extrabold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border border-amber-300 dark:border-amber-800 cursor-not-allowed">
                ⏳ Pending Admin Pool
              </button>
            </template>
            <template v-else-if="order.payment_status !== 'paid' && !['cancelled', 'Cancelled'].includes(order.status)">
              <BaseButton size="sm" variant="primary" @click="openPaymentModal(order)">
                💳 Settle Payment
              </BaseButton>
            </template>

            <!-- Track Order Link -->
            <RouterLink :to="`/orders/track/${order.tracking_token || order.order_number}`" v-tooltip="'Watch live kitchen baking & delivery status'">
              <BaseButton size="sm" variant="outline">Track Order →</BaseButton>
            </RouterLink>
          </div>
        </div>

        <!-- Sleek Compact Status Summary (Non-redundant) -->
        <div v-if="!['cancelled', 'Cancelled', 'refunded', 'Refunded'].includes(order.status)" class="bg-surface/80 dark:bg-[#1E130B] rounded-2xl px-4 py-3 border border-brand-caramel/30 dark:border-brand-caramel/40 flex items-center justify-between flex-wrap gap-2 text-xs">
          <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full" :class="order.status === 'completed' || order.status === 'Completed' ? 'bg-success' : 'bg-brand-tan animate-pulse'"></span>
            <span class="font-extrabold text-brand-choco dark:text-surface-400">Kitchen &amp; Delivery Status:</span>
            <span class="font-semibold text-ink dark:text-surface">{{ getProgressStepText(order.status) }}</span>
          </div>
          <RouterLink :to="`/orders/track/${order.tracking_token || order.order_number}`" class="text-xs font-extrabold text-brand-caramel hover:text-brand-choco dark:hover:text-surface transition-colors flex items-center gap-1">
            <span>View Full Timeline</span>
            <span>→</span>
          </RouterLink>
        </div>

        <!-- Items Breakdown -->
        <div class="divide-y divide-brand-caramel/10">
          <div
            v-for="item in order.items"
            :key="item.id"
            class="py-3 flex justify-between items-center text-xs"
          >
            <div class="flex items-center gap-3">
              <img
                v-if="item.image_url"
                :src="item.image_url"
                :alt="item.product_name"
                class="w-11 h-11 rounded-xl object-cover shrink-0 border border-brand-caramel/20 shadow-xs"
              />
              <div>
                <span class="font-extrabold text-ink text-sm block">{{ item.product_name }}</span>
                <span class="text-warm-gray text-xs">Qty: {{ item.qty }} × ₱{{ (item.price || item.unit_price || 0).toFixed(2) }}</span>
                <div v-if="item.product_slug" class="mt-1">
                  <RouterLink
                    :to="`/products/${item.product_slug}#reviews`"
                    v-tooltip="'Share your review &amp; rating for this treat'"
                    class="inline-flex items-center gap-1 text-[11px] font-bold text-brand-caramel hover:text-brand-choco transition-colors"
                  >
                    ⭐ Write Review
                  </RouterLink>
                </div>
              </div>
            </div>
            <span class="font-black text-brand-choco text-sm">₱{{ item.subtotal.toFixed(2) }}</span>
          </div>
        </div>

        <!-- Details Accordion / Footer -->
        <div class="pt-3 border-t border-brand-caramel/15 flex flex-wrap justify-between items-center gap-3">
          <button
            type="button"
            @click="toggleDetails(order.id)"
            class="text-xs font-bold text-brand-caramel hover:text-brand-choco flex items-center gap-1 transition-colors"
          >
            <span>{{ expandedOrders.includes(order.id) ? 'Hide Order Details ▲' : 'View Full Details & Address ▼' }}</span>
          </button>

          <div class="text-right">
            <span class="text-xs text-warm-gray block">Total Amount Paid</span>
            <span class="text-lg font-black text-brand-choco">₱{{ order.total.toFixed(2) }}</span>
          </div>
        </div>

        <!-- Expanded Details Drawer -->
        <Transition
          enter-active-class="transition-all duration-200 ease-out"
          enter-from-class="opacity-0 max-h-0"
          enter-to-class="opacity-100 max-h-[400px]"
          leave-active-class="transition-all duration-150 ease-in"
          leave-from-class="opacity-100 max-h-[400px]"
          leave-to-class="opacity-0 max-h-0"
        >
          <div v-if="expandedOrders.includes(order.id)" class="bg-surface/60 rounded-2xl p-4 border border-brand-caramel/20 space-y-3 text-xs">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <div>
                <span class="text-warm-gray font-bold block mb-0.5">Contact Details:</span>
                <p class="font-bold text-ink">{{ order.customer_name }}</p>
                <p class="text-warm-gray">{{ order.customer_email }} • {{ order.customer_phone }}</p>
              </div>

              <div>
                <span class="text-warm-gray font-bold block mb-0.5">Fulfillment &amp; Payment:</span>
                <p class="font-bold text-ink capitalize">{{ order.fulfillment_type || 'delivery' }} ({{ order.payment_method?.toUpperCase() || 'GCASH' }})</p>
                <p class="text-warm-gray truncate">{{ order.delivery_address || 'Store Pickup' }}</p>
              </div>
            </div>

            <div class="pt-2 border-t border-brand-caramel/15 flex justify-between items-center text-warm-gray">
              <span>Items Subtotal:</span>
              <span class="font-bold text-ink">₱{{ (order.subtotal || order.total - (order.delivery_fee || 0)).toFixed(2) }}</span>
            </div>
            <div class="flex justify-between items-center text-warm-gray">
              <span>Delivery Shipping Fee:</span>
              <span class="font-bold text-ink">₱{{ (order.delivery_fee || 0).toFixed(2) }}</span>
            </div>
            <div v-if="order.notes" class="pt-1 text-warm-gray">
              <span class="font-bold">Order Note:</span> {{ order.notes }}
            </div>
          </div>
        </Transition>

      </div>
    </div>

    <!-- Invoice Modal -->
    <InvoiceModal v-model="showInvoiceModal" :order="activeInvoiceOrder" />

    <!-- Cancellation Modal -->
    <BaseModal
      v-model="showModal"
      :title="selectedOrder ? `Cancel Order #${selectedOrder.order_number}` : 'Cancel Order'"
      subtitle="Are you sure you want to cancel this pending bakery order?"
    >
      <template #icon>
        <div class="w-8 h-8 rounded-full bg-red-100 text-error flex items-center justify-center text-base font-bold">
          ⚠️
        </div>
      </template>

      <div v-if="selectedOrder" class="space-y-4">
        <div class="p-4 rounded-2xl bg-red-50 border border-red-200/60 text-xs text-error leading-relaxed space-y-1">
          <p class="font-extrabold text-sm">Notice:</p>
          <p>Order cancellation cannot be undone once confirmed. Reserved stock items will be automatically returned to our bakery inventory.</p>
        </div>

        <div class="bg-surface/70 p-4 rounded-2xl border border-brand-caramel/20 space-y-2.5 text-xs">
          <div class="flex justify-between items-center">
            <span class="text-warm-gray">Order Reference:</span>
            <span class="font-bold text-ink">{{ selectedOrder.order_number }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-warm-gray">Total Amount:</span>
            <span class="font-black text-brand-choco">₱{{ selectedOrder.total.toFixed(2) }}</span>
          </div>
        </div>
      </div>

      <template #footer>
        <div class="flex items-center justify-end gap-3">
          <BaseButton variant="outline" size="md" :disabled="cancelling" @click="showModal = false">
            Keep My Order
          </BaseButton>
          <BaseButton
            variant="primary"
            size="md"
            :loading="cancelling"
            class="!bg-error hover:!bg-[#963B2E]"
            @click="confirmCancelOrder"
          >
            Confirm Cancellation
          </BaseButton>
        </div>
      </template>
    </BaseModal>

    <!-- Order Payment Modal -->
    <OrderPaymentModal
      :show="showPaymentModal"
      :order="paymentOrder"
      :store-info="storeInfo"
      @close="showPaymentModal = false"
      @payment-success="fetchOrders"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import { useCartStore } from '@/stores/cart'
import { useToast } from '@/composables/useToast'
import PageHeader from '@/components/ui/PageHeader.vue'
import BaseBadge from '@/components/ui/BaseBadge.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseModal from '@/components/ui/BaseModal.vue'
import SkeletonCard from '@/components/ui/SkeletonCard.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import InvoiceModal from '@/components/storefront/InvoiceModal.vue'
import OrderPaymentModal from '@/components/checkout/OrderPaymentModal.vue'

const axios = inject('axios')
const toast = useToast()
const cartStore = useCartStore()

const orders = ref([])
const loading = ref(true)
const activeFilter = ref('all')
const expandedOrders = ref([])
const showModal = ref(false)
const showInvoiceModal = ref(false)
const showPaymentModal = ref(false)
const paymentOrder = ref(null)
const storeInfo = ref({})
const activeInvoiceOrder = ref(null)
const selectedOrder = ref(null)
const cancelling = ref(false)

function openInvoiceModal(order) {
  activeInvoiceOrder.value = order
  showInvoiceModal.value = true
}

function openPaymentModal(order) {
  paymentOrder.value = order
  showPaymentModal.value = true
}

async function fetchStoreSettings() {
  try {
    const { data } = await axios.get('/api/settings/store')
    storeInfo.value = data || {}
  } catch {}
}

const filterTabs = [
  { id: 'all', label: 'All Orders', icon: '📋' },
  { id: 'active', label: 'Active', icon: '🧁' },
  { id: 'completed', label: 'Completed', icon: '✅' },
  { id: 'cancelled', label: 'Cancelled', icon: '🚫' },
  { id: 'refunded', label: 'Refunded', icon: '💸' },
]

const pipelineSteps = [
  { key: 'confirmed', label: 'Order Placed', desc: 'Received & queued' },
  { key: 'preparing', label: 'Baking', desc: 'Active in kitchen' },
  { key: 'packaging', label: 'Packaging', desc: 'Sealed & boxed' },
  { key: 'transit', label: 'Out for Delivery / Pickup', desc: 'On the way' },
  { key: 'completed', label: 'Completed', desc: 'Delivered' }
]

const statusOrder = ['pending', 'confirmed', 'preparing', 'packaging', 'out_for_delivery', 'ready_for_pickup', 'completed']

const filteredOrders = computed(() => {
  if (activeFilter.value === 'all') return orders.value
  if (activeFilter.value === 'active') {
    return orders.value.filter(o => !['completed', 'cancelled', 'refunded', 'Completed', 'Cancelled', 'Refunded'].includes(o.status))
  }
  if (activeFilter.value === 'completed') {
    return orders.value.filter(o => ['completed', 'Completed'].includes(o.status))
  }
  if (activeFilter.value === 'cancelled') {
    return orders.value.filter(o => ['cancelled', 'Cancelled'].includes(o.status))
  }
  if (activeFilter.value === 'refunded') {
    return orders.value.filter(o => ['refunded', 'Refunded'].includes(o.status))
  }
  return orders.value
})

function getFilteredCount(filterId) {
  if (filterId === 'all') return orders.value.length
  if (filterId === 'active') {
    return orders.value.filter(o => !['completed', 'cancelled', 'refunded', 'Completed', 'Cancelled', 'Refunded'].includes(o.status)).length
  }
  if (filterId === 'completed') {
    return orders.value.filter(o => ['completed', 'Completed'].includes(o.status)).length
  }
  if (filterId === 'cancelled') {
    return orders.value.filter(o => ['cancelled', 'Cancelled'].includes(o.status)).length
  }
  if (filterId === 'refunded') {
    return orders.value.filter(o => ['refunded', 'Refunded'].includes(o.status)).length
  }
  return 0
}

function formatDate(dateStr) {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

function getStatusVariant(status) {
  switch (status?.toLowerCase()) {
    case 'completed': return 'success'
    case 'pending': return 'warning'
    case 'confirmed': case 'preparing': case 'out_for_delivery': return 'brand'
    case 'cancelled': case 'refunded': return 'error'
    default: return 'neutral'
  }
}

function getProgressStepText(status) {
  switch (status?.toLowerCase()) {
    case 'pending': return 'Order placed & awaiting confirmation'
    case 'confirmed': return 'Payment verified & order queued in kitchen'
    case 'preparing': return 'Freshly baking in our oven 🧁'
    case 'packaging': return 'Pastries packaged & sealed 🎁'
    case 'out_for_delivery': return 'On its way to your doorstep 🛵'
    case 'ready_for_pickup': return 'Ready for store pickup 🏪'
    case 'completed': return 'Delivered & completed 🎉'
    case 'refunded': return 'Order payment refunded 💸'
    case 'cancelled': return 'Order cancelled 🚫'
    default: return 'Processing'
  }
}

function isStepComplete(status, stepKey) {
  const s = status?.toLowerCase()
  if (s === 'completed') return true
  const currentIdx = statusOrder.indexOf(s)
  if (stepKey === 'confirmed') return currentIdx >= 1
  if (stepKey === 'preparing') return currentIdx >= 2
  if (stepKey === 'packaging') return currentIdx >= 3
  if (stepKey === 'transit') return currentIdx >= 4
  return false
}

function isStepActive(status, stepKey) {
  const s = status?.toLowerCase()
  if (stepKey === 'confirmed' && (s === 'confirmed' || s === 'pending')) return true
  if (stepKey === 'preparing' && s === 'preparing') return true
  if (stepKey === 'packaging' && s === 'packaging') return true
  if (stepKey === 'transit' && (s === 'out_for_delivery' || s === 'ready_for_pickup')) return true
  if (stepKey === 'completed' && s === 'completed') return true
  return false
}

function toggleDetails(orderId) {
  if (expandedOrders.value.includes(orderId)) {
    expandedOrders.value = expandedOrders.value.filter(id => id !== orderId)
  } else {
    expandedOrders.value.push(orderId)
  }
}

function reorderItems(order) {
  if (!order.items || order.items.length === 0) return
  
  order.items.forEach(async item => {
    const productId = item.product_id || item.product?.id || item.id
    if (productId) {
      await cartStore.addItem(productId, item.qty || 1)
    }
  })

  toast.success(`Re-added ${order.items.length} pastry treats to your shopping basket!`, 'Basket Updated 🧺')
  cartStore.openDrawer = true
}

async function fetchOrders() {
  loading.value = true
  try {
    const { data } = await axios.get('/api/customer/orders')
    orders.value = data.data || []
  } catch (err) {
    console.error('Failed to fetch customer orders', err)
  } finally {
    loading.value = false
  }
}

function openCancelModal(order) {
  selectedOrder.value = order
  showModal.value = true
}

async function confirmCancelOrder() {
  if (!selectedOrder.value) return

  cancelling.value = true
  try {
    const { data } = await axios.post(`/api/customer/orders/${selectedOrder.value.id}/cancel`)
    toast.success(data.message || 'Order cancelled successfully.', 'Order Cancelled')
    showModal.value = false
    selectedOrder.value = null
    await fetchOrders()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Could not cancel order. Please refresh.', 'Cancellation Error')
  } finally {
    cancelling.value = false
  }
}

onMounted(() => {
  fetchStoreSettings()
  fetchOrders()
})
</script>

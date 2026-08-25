<template>
  <div class="page-container py-10 md:py-16">
    <div v-if="loading" class="max-w-3xl mx-auto space-y-6">
      <SkeletonBlock height="250px" radius="1.5rem" />
      <SkeletonBlock height="400px" radius="1.5rem" />
    </div>

    <div v-else-if="!order" class="max-w-md mx-auto text-center py-16">
      <EmptyState title="Order Tracking Not Found" description="The tracking token link appears to be invalid or expired.">
        <template #action>
          <RouterLink to="/shop"><BaseButton variant="primary">Browse Menu</BaseButton></RouterLink>
        </template>
      </EmptyState>
    </div>

    <div v-else class="max-w-4xl mx-auto space-y-8">
      <!-- Header Bar -->
      <div class="bg-white rounded-3xl p-6 md:p-8 border border-brand-caramel/20 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
          <span class="script-accent text-brand-caramel text-lg">live order status</span>
          <h1 class="text-2xl md:text-3xl font-extrabold text-ink mt-0.5">
            Order #{{ order.order_number }}
          </h1>
          <p class="text-xs text-warm-gray">Placed on {{ new Date(order.created_at).toLocaleString() }}</p>
        </div>

        <div class="flex items-center gap-3">
          <BaseBadge :variant="getStatusVariant(order.status)" size="md" dot>
            {{ order.status_label || order.status }}
          </BaseBadge>
          <BaseButton size="sm" variant="outline" @click="fetchOrder">
            ↻ Refresh Status
          </BaseButton>
        </div>
      </div>

      <!-- Delivery Mode & Pooling Status Banner -->
      <div v-if="order.delivery_mode === 'pooling'" class="rounded-3xl p-6 border shadow-sm space-y-4 transition-all"
        :class="order.pooling_status === 'settled' ? 'bg-emerald-50/90 dark:bg-[#1A2E1A] border-emerald-300 dark:border-emerald-800' : 'bg-amber-50/90 dark:bg-[#2A1C13] border-amber-300 dark:border-amber-800'">
        <div class="flex items-start gap-4">
          <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shrink-0 shadow-xs"
            :class="order.pooling_status === 'settled' ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800' : 'bg-amber-100 dark:bg-amber-950/60 text-amber-800'">
            {{ order.pooling_status === 'settled' ? '🎉' : '🤝' }}
          </div>
          <div class="flex-1 space-y-2">
            <div class="flex items-center justify-between flex-wrap gap-2">
              <h3 class="text-lg font-extrabold" :class="order.pooling_status === 'settled' ? 'text-emerald-950 dark:text-emerald-200' : 'text-amber-950 dark:text-amber-200'">
                Group Delivery Pooling — {{ order.pooling_status === 'settled' ? 'Rate Settled by Admin!' : 'Awaiting Admin Rate Assignment' }}
              </h3>
              <span class="px-3 py-1 rounded-full text-xs font-bold uppercase shadow-2xs"
                :class="order.pooling_status === 'settled' ? 'bg-emerald-700 text-white' : 'bg-amber-700 text-white'">
                {{ order.pooling_status === 'settled' ? '✓ Settled' : '⏳ Pending Admin Rate' }}
              </span>
            </div>

            <p class="text-xs leading-relaxed" :class="order.pooling_status === 'settled' ? 'text-emerald-900 dark:text-emerald-300' : 'text-amber-900 dark:text-amber-300'">
              <template v-if="order.pooling_status === 'settled'">
                Admin has assigned your order to Delivery Pool Batch <strong>#{{ order.delivery_pool?.pool_code || 'POOL' }}</strong>. Your final assigned delivery fee is <strong>₱{{ (order.shipping_fee || order.delivery_fee || 0).toFixed(2) }}</strong>. Final Order Total: <strong>₱{{ (order.total || 0).toFixed(2) }}</strong>.
              </template>
              <template v-else>
                Your order is currently waiting for our admin to group it with nearby orders in <strong>{{ order.city }}</strong> and assign your discounted delivery fee. Payment cannot be settled until the admin confirms your pooled shipping rate.
              </template>
            </p>

            <!-- Settle Payment Button inside Pooling Banner -->
            <div class="pt-2">
              <template v-if="order.pooling_status === 'settled'">
                <div v-if="order.payment_status === 'paid'" class="flex items-center gap-2 text-xs font-extrabold text-emerald-800 dark:text-emerald-300">
                  <span>✓ Payment Settled &amp; Confirmed!</span>
                </div>
                <BaseButton v-else variant="primary" size="sm" @click="showPaymentModal = true">
                  💳 Settle Payment Now (₱{{ (order.total || 0).toFixed(2) }}) →
                </BaseButton>
              </template>
              <template v-else>
                <button type="button" disabled class="px-4 py-2 rounded-xl text-xs font-extrabold bg-gray-200 dark:bg-gray-800 text-gray-500 cursor-not-allowed border border-gray-300 dark:border-gray-700">
                  ⏳ Payment Locked (Awaiting Admin Pooling Settlement)
                </button>
              </template>
            </div>
          </div>
        </div>
      </div>

      <!-- Live Pipeline Progress Tracker Bar -->
      <div class="bg-white rounded-3xl p-6 md:p-10 border border-brand-caramel/20 shadow-sm space-y-8">
        <h3 class="font-extrabold text-xl text-ink border-b border-brand-caramel/20 pb-4">
          Kitchen &amp; Delivery Progress
        </h3>

        <!-- Pipeline Steps Grid -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 relative">
          <div
            v-for="(step, idx) in pipelineSteps"
            :key="step.key"
            class="flex flex-col items-center text-center space-y-2 relative z-10"
          >
            <!-- Step Circle Icon -->
            <div
              class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300"
              :class="isStepComplete(step.key)
                ? 'bg-success text-white shadow-md scale-105'
                : isStepActive(step.key)
                ? 'bg-brand-tan dark:bg-surface-400 text-ink ring-4 ring-brand-tan/30 animate-bounce'
                : 'bg-surface dark:bg-[#2A1C13] text-warm-gray dark:text-[#C5B4A4] border border-brand-caramel/30'"
            >
              <svg v-if="isStepComplete(step.key)" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
              <span v-else>{{ idx + 1 }}</span>
            </div>

            <div class="font-bold text-xs text-ink dark:text-surface">{{ step.label }}</div>
            <div class="text-[10px] text-warm-gray dark:text-[#C5B4A4] line-clamp-1">{{ step.desc }}</div>
          </div>
        </div>
      </div>

      <!-- Customer Details & Order Items Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Customer Info Box -->
        <div class="bg-white rounded-3xl p-6 border border-brand-caramel/20 shadow-sm space-y-4">
          <h3 class="font-extrabold text-lg text-ink border-b border-brand-caramel/20 pb-2">
            Fulfillment Details
          </h3>

          <div class="space-y-2 text-xs">
            <div class="flex justify-between"><span class="text-warm-gray">Customer:</span><span class="font-semibold text-ink">{{ order.customer_name }}</span></div>
            <div class="flex justify-between"><span class="text-warm-gray">Contact:</span><span class="font-semibold text-ink">{{ order.customer_phone }}</span></div>
            <div class="flex justify-between"><span class="text-warm-gray">Fulfillment:</span><span class="font-semibold uppercase text-brand-choco">{{ order.fulfillment_type }}</span></div>
            <div class="flex justify-between"><span class="text-warm-gray">Delivery Mode:</span>
              <span class="font-bold text-ink flex items-center gap-1">
                <template v-if="order.delivery_mode === 'pooling'">
                  <span>🤝 Delivery Pooling</span>
                  <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold uppercase">{{ order.pooling_status }}</span>
                </template>
                <template v-else>
                  <span>⚡ Priority Express</span>
                </template>
              </span>
            </div>
            <div v-if="order.delivery_pool" class="flex justify-between text-emerald-700 font-semibold">
              <span>Assigned Pool Batch:</span>
              <span>#{{ order.delivery_pool.pool_code }}</span>
            </div>
            <div v-if="order.delivery_address" class="pt-2 border-t border-brand-caramel/15">
              <span class="text-warm-gray block mb-1">Delivery Address:</span>
              <span class="font-medium text-ink block">{{ order.delivery_address }}, {{ order.city }}</span>
            </div>
          </div>
        </div>

        <!-- Items Box -->
        <div class="bg-white rounded-3xl p-6 border border-brand-caramel/20 shadow-sm space-y-4">
          <h3 class="font-extrabold text-lg text-ink border-b border-brand-caramel/20 pb-2">
            Items Ordered
          </h3>

          <div class="divide-y divide-brand-caramel/15 max-h-48 overflow-y-auto pr-1">
            <div v-for="item in order.items" :key="item.id" class="py-2 flex justify-between items-center text-xs">
              <div>
                <span class="font-bold text-ink block">{{ item.product_name }}</span>
                <span v-if="item.options?.flavors && Array.isArray(item.options.flavors)" class="text-[11px] font-semibold text-amber-700 block">
                  Assorted: {{ item.options.flavors.join(', ') }}
                </span>
                <span v-else-if="item.options?.flavor" class="text-[11px] font-semibold text-amber-700 block">
                  Flavor: {{ item.options.flavor }}
                </span>
                <span v-if="item.options?.variation" class="text-[11px] font-semibold text-brand-caramel block">
                  Option: {{ item.options.variation }}
                </span>
                <span class="text-warm-gray block">Qty: {{ item.qty }}</span>
              </div>
              <span class="font-bold text-brand-choco">₱{{ (item.subtotal || 0).toFixed(2) }}</span>
            </div>
          </div>

          <div class="pt-2 border-t border-brand-caramel/20 flex justify-between items-baseline font-extrabold text-base text-brand-choco">
            <span>Total Paid</span>
            <span>₱{{ (order.total || 0).toFixed(2) }}</span>
          </div>
        </div>
      </div>

      <!-- Order Payment Modal -->
      <OrderPaymentModal
        :show="showPaymentModal"
        :order="order"
        :store-info="storeInfo"
        @close="showPaymentModal = false"
        @payment-success="fetchOrder"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, inject, computed } from 'vue'
import { useRoute } from 'vue-router'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseBadge from '@/components/ui/BaseBadge.vue'
import SkeletonBlock from '@/components/ui/SkeletonBlock.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import OrderPaymentModal from '@/components/checkout/OrderPaymentModal.vue'

const axios = inject('axios')
const route = useRoute()
const order = ref(null)
const loading = ref(true)
const showPaymentModal = ref(false)
const storeInfo = ref({})

async function fetchStoreSettings() {
  try {
    const { data } = await axios.get('/api/settings/store')
    storeInfo.value = data || {}
  } catch {}
}

const pipelineSteps = computed(() => {
  if (order.value?.delivery_mode === 'pooling') {
    return [
      { key: 'pooling_assignment', label: 'Batch Assignment', desc: order.value?.pooling_status === 'settled' ? 'Fee Settled' : 'Awaiting Admin Rate' },
      { key: 'confirmed', label: 'Order Confirmed', desc: 'Payment settled' },
      { key: 'kitchen_prep', label: 'Kitchen Baking', desc: 'Baking & packaging' },
      { key: 'transit', label: 'Shared Delivery', desc: 'Group route en route' },
      { key: 'completed', label: 'Completed', desc: 'Delivered' }
    ]
  }

  return [
    { key: 'placed', label: 'Order Placed', desc: 'Received & queued' },
    { key: 'confirmed', label: 'Order Confirmed', desc: 'Payment verified' },
    { key: 'kitchen_prep', label: 'Kitchen Baking', desc: 'Baking & packaging' },
    { key: 'transit', label: 'Out for Delivery', desc: 'En route / ready' },
    { key: 'completed', label: 'Completed', desc: 'Delivered' }
  ]
})

const statusOrder = ['pending', 'confirmed', 'preparing', 'packaging', 'out_for_delivery', 'ready_for_pickup', 'completed']

function getStatusVariant(status) {
  switch (status?.toLowerCase()) {
    case 'completed':
      return 'success'
    case 'pending':
    case 'preparing':
    case 'packaging':
      return 'warning'
    case 'out_for_delivery':
    case 'ready_for_pickup':
    case 'confirmed':
      return 'brand'
    case 'cancelled':
    case 'refunded':
    case 'failed':
      return 'error'
    default:
      return 'neutral'
  }
}

function isStepComplete(stepKey) {
  if (!order.value) return false
  if (order.value.status === 'completed') return true

  if (stepKey === 'pooling_assignment') {
    return order.value.pooling_status === 'settled'
  }

  const currentIdx = statusOrder.indexOf(order.value.status)
  if (stepKey === 'placed') return true
  if (stepKey === 'confirmed') return currentIdx >= 1 && (order.value.delivery_mode !== 'pooling' || order.value.pooling_status === 'settled')
  if (stepKey === 'kitchen_prep') return currentIdx >= 4
  if (stepKey === 'transit') return currentIdx >= 6
  return false
}

function isStepActive(stepKey) {
  if (!order.value) return false

  if (stepKey === 'pooling_assignment') {
    return order.value.delivery_mode === 'pooling' && order.value.pooling_status !== 'settled'
  }

  if (stepKey === 'placed' && order.value.status === 'pending' && order.value.delivery_mode !== 'pooling') {
    return true
  }

  if (stepKey === 'confirmed' && (order.value.status === 'confirmed' || (order.value.status === 'pending' && order.value.delivery_mode === 'pooling' && order.value.pooling_status === 'settled'))) {
    return true
  }

  if (stepKey === 'kitchen_prep' && (order.value.status === 'preparing' || order.value.status === 'packaging')) return true
  if (stepKey === 'transit' && (order.value.status === 'out_for_delivery' || order.value.status === 'ready_for_pickup')) return true
  if (stepKey === 'completed' && order.value.status === 'completed') return true
  return false
}

async function fetchOrder() {
  loading.value = true
  try {
    const token = route.params.token || route.query.token
    if (token) {
      const { data } = await axios.get(`/api/orders/track/${token}`)
      order.value = data.data
    }
  } catch (err) {
    console.error('Failed to track order', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchStoreSettings()
  fetchOrder()
})
</script>

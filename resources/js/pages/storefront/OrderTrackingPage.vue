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
            <div class="flex justify-between"><span class="text-warm-gray">Method:</span><span class="font-semibold uppercase text-brand-choco">{{ order.fulfillment_type }}</span></div>
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
                <span class="font-bold text-ink">{{ item.product_name }}</span>
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

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import { useRoute } from 'vue-router'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseBadge from '@/components/ui/BaseBadge.vue'
import SkeletonBlock from '@/components/ui/SkeletonBlock.vue'
import EmptyState from '@/components/ui/EmptyState.vue'

const axios = inject('axios')
const route = useRoute()
const order = ref(null)
const loading = ref(true)

const pipelineSteps = [
  { key: 'confirmed', label: 'Order Placed', desc: 'Received & queued' },
  { key: 'preparing', label: 'Baking', desc: 'Active in kitchen' },
  { key: 'packaging', label: 'Packaging', desc: 'Sealed & boxed' },
  { key: 'transit', label: 'Out for Delivery / Pickup', desc: 'On the way' },
  { key: 'completed', label: 'Completed', desc: 'Delivered' }
]

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

  const currentIdx = statusOrder.indexOf(order.value.status)
  if (stepKey === 'confirmed') return currentIdx >= 1
  if (stepKey === 'preparing') return currentIdx >= 2
  if (stepKey === 'packaging') return currentIdx >= 3
  if (stepKey === 'transit') return currentIdx >= 4
  return false
}

function isStepActive(stepKey) {
  if (!order.value) return false
  if (stepKey === 'confirmed' && (order.value.status === 'confirmed' || order.value.status === 'pending')) return true
  if (stepKey === 'preparing' && order.value.status === 'preparing') return true
  if (stepKey === 'packaging' && order.value.status === 'packaging') return true
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

onMounted(() => fetchOrder())
</script>

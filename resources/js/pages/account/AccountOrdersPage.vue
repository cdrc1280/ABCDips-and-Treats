<template>
  <div class="space-y-8">
    <PageHeader
      tagline="customer account"
      title="My Order History"
      subtitle="Track your current orders, cancel pending requests, leave product reviews, and view receipts for previous bakery orders."
    />

    <div v-if="loading">
      <SkeletonBlock height="250px" radius="1.5rem" />
    </div>

    <div v-else-if="orders.length === 0">
      <EmptyState
        title="No Orders Placed Yet"
        description="You haven't placed any pastry orders with this account yet."
      >
        <template #action>
          <RouterLink to="/shop">
            <BaseButton variant="primary">Start Shopping</BaseButton>
          </RouterLink>
        </template>
      </EmptyState>
    </div>

    <div v-else class="space-y-4">
      <div
        v-for="order in orders"
        :key="order.id"
        class="bg-white rounded-3xl p-6 border border-[#C08E5D]/20 shadow-sm space-y-4"
      >
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-[#C08E5D]/15 pb-4">
          <div>
            <div class="font-extrabold text-lg text-[#1C1410]">{{ order.order_number }}</div>
            <div class="text-xs text-[#8C7A68]">Placed on {{ new Date(order.created_at).toLocaleDateString() }}</div>
          </div>

          <div class="flex items-center gap-2 flex-wrap">
            <BaseBadge :variant="getStatusVariant(order.status)" dot>
              {{ order.status_label || order.status }}
            </BaseBadge>

            <!-- Cancel Order Button (ONLY visible when status is pending) -->
            <BaseButton
              v-if="order.status?.toLowerCase() === 'pending'"
              size="sm"
              variant="outline"
              class="!text-[#B84C3C] !border-[#B84C3C]/40 hover:!bg-red-50"
              @click="openCancelModal(order)"
            >
              🚫 Cancel Order
            </BaseButton>

            <RouterLink :to="`/orders/track/${order.tracking_token}`">
              <BaseButton size="sm" variant="outline">Track Order →</BaseButton>
            </RouterLink>
          </div>
        </div>

        <!-- Order Items List -->
        <div class="divide-y divide-[#C08E5D]/10">
          <div
            v-for="item in order.items"
            :key="item.id"
            class="py-2.5 flex justify-between items-center text-xs"
          >
            <div class="flex items-center gap-3">
              <img
                v-if="item.image_url"
                :src="item.image_url"
                class="w-9 h-9 rounded-xl object-cover flex-shrink-0 border border-[#C08E5D]/20"
              />
              <div>
                <span class="font-bold text-[#1C1410]">{{ item.qty }}x {{ item.product_name }}</span>
                <div v-if="item.product_slug" class="mt-0.5">
                  <RouterLink
                    :to="`/products/${item.product_slug}#reviews`"
                    class="inline-flex items-center gap-1 text-[11px] font-bold text-[#C08E5D] hover:text-[#5C3A22] transition-colors"
                  >
                    ⭐ Write Review
                  </RouterLink>
                </div>
              </div>
            </div>
            <span class="font-bold text-[#5C3A22]">₱{{ item.subtotal.toFixed(2) }}</span>
          </div>
        </div>

        <div class="pt-2 border-t border-[#C08E5D]/15 flex justify-between items-center text-sm font-extrabold text-[#5C3A22]">
          <span>Total</span>
          <span>₱{{ order.total.toFixed(2) }}</span>
        </div>
      </div>
    </div>

    <!-- Uniform Premium Order Cancellation Modal -->
    <BaseModal
      v-model="showModal"
      :title="selectedOrder ? `Cancel Order #${selectedOrder.order_number}` : 'Cancel Order'"
      subtitle="Are you sure you want to cancel this pending bakery order?"
    >
      <template #icon>
        <div class="w-8 h-8 rounded-full bg-red-100 text-[#B84C3C] flex items-center justify-center text-base font-bold">
          ⚠️
        </div>
      </template>

      <div v-if="selectedOrder" class="space-y-4">
        <!-- Alert Box -->
        <div class="p-4 rounded-2xl bg-red-50 border border-red-200/60 text-xs text-[#B84C3C] leading-relaxed space-y-1">
          <div class="font-extrabold flex items-center gap-1.5 text-sm">
            <span>Notice:</span>
          </div>
          <p>Order cancellation cannot be undone once confirmed. Reserved stock items will be automatically returned to our bakery inventory.</p>
        </div>

        <!-- Order Summary Detail Box -->
        <div class="bg-[#FBF3E7]/70 p-4 rounded-2xl border border-[#C08E5D]/20 space-y-2.5">
          <div class="flex justify-between items-center text-xs text-[#8C7A68]">
            <span>Order Reference:</span>
            <span class="font-bold text-[#1C1410]">{{ selectedOrder.order_number }}</span>
          </div>

          <div class="flex justify-between items-center text-xs text-[#8C7A68]">
            <span>Fulfillment Type:</span>
            <span class="font-bold text-[#1C1410] capitalize">{{ selectedOrder.fulfillment_type || 'Delivery' }}</span>
          </div>

          <div class="flex justify-between items-center text-xs text-[#8C7A68]">
            <span>Total Amount:</span>
            <span class="font-extrabold text-[#5C3A22] text-sm">₱{{ selectedOrder.total.toFixed(2) }}</span>
          </div>

          <div class="pt-2 border-t border-[#C08E5D]/15 text-xs text-[#8C7A68]">
            <span class="font-semibold text-[#5C3A22] block mb-1">Items to be cancelled:</span>
            <ul class="space-y-1 list-disc list-inside">
              <li v-for="item in selectedOrder.items" :key="item.id" class="truncate">
                {{ item.qty }}x {{ item.product_name }}
              </li>
            </ul>
          </div>
        </div>
      </div>

      <template #footer>
        <BaseButton variant="ghost" @click="showModal = false">
          Keep My Order
        </BaseButton>
        <BaseButton
          variant="primary"
          class="!bg-[#B84C3C] hover:!bg-[#963C2F] !text-white"
          :loading="submitting"
          @click="confirmCancelOrder"
        >
          Confirm Cancellation
        </BaseButton>
      </template>
    </BaseModal>
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import PageHeader from '@/components/ui/PageHeader.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseBadge from '@/components/ui/BaseBadge.vue'
import BaseModal from '@/components/ui/BaseModal.vue'
import SkeletonBlock from '@/components/ui/SkeletonBlock.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import { useToast } from '@/composables/useToast'

const axios = inject('axios')
const toast = useToast()

const orders = ref([])
const loading = ref(true)
const showModal = ref(false)
const selectedOrder = ref(null)
const submitting = ref(false)

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

async function fetchOrders() {
  loading.value = true
  try {
    const { data } = await axios.get('/api/customer/orders')
    orders.value = data.data
  } catch (err) {
    console.error('Failed to load orders', err)
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

  submitting.value = true
  try {
    const orderId = selectedOrder.value.id
    const orderNum = selectedOrder.value.order_number
    const { data } = await axios.post(`/api/customer/orders/${orderId}/cancel`)

    toast.success(data.message || `Order #${orderNum} cancelled successfully.`, 'Order Cancelled')
    showModal.value = false
    selectedOrder.value = null
    fetchOrders()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Failed to cancel order.', 'Cancellation Error')
  } finally {
    submitting.value = false
  }
}

onMounted(() => fetchOrders())
</script>

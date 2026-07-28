<template>
  <div class="space-y-8">
    <PageHeader
      tagline="order history"
      title="My Orders"
      subtitle="Track your active pastry orders in real-time or review past purchases."
    />

    <div v-if="loading" class="space-y-4">
      <SkeletonCard v-for="n in 3" :key="n" />
    </div>

    <div v-else-if="orders.length === 0">
      <EmptyState
        title="No Orders Yet"
        description="When you place your first bakery order, you will be able to track its progress here."
      >
        <template #action>
          <RouterLink to="/shop"><BaseButton variant="primary">Explore Menu</BaseButton></RouterLink>
        </template>
      </EmptyState>
    </div>

    <div v-else class="space-y-6">
      <div
        v-for="order in orders"
        :key="order.id"
        class="bg-white rounded-3xl p-6 border border-[#C08E5D]/20 shadow-sm space-y-4"
      >
        <!-- Header Row -->
        <div class="flex flex-wrap justify-between items-center gap-3 pb-3 border-b border-[#C08E5D]/15">
          <div>
            <span class="font-extrabold text-[#1C1410] text-lg">Order #{{ order.order_number }}</span>
            <span class="text-xs text-[#8C7A68] block">Placed on {{ new Date(order.created_at).toLocaleDateString() }}</span>
          </div>

          <div class="flex items-center gap-3">
            <BaseBadge :variant="getStatusVariant(order.status)" size="sm">
              {{ order.status_label || order.status }}
            </BaseBadge>

            <!-- Cancel Action Button (Restricted strictly to Pending status) -->
            <BaseButton
              v-if="order.status === 'pending' || order.status === 'Pending'"
              size="sm"
              variant="outline"
              v-tooltip="'Cancel order (only available while status is Pending)'"
              class="!text-[#B84C3C] !border-[#B84C3C]/40 hover:!bg-red-50"
              @click="openCancelModal(order)"
            >
              🚫 Cancel Order
            </BaseButton>

            <RouterLink :to="`/orders/track/${order.tracking_token}`" v-tooltip="'Watch live kitchen baking &amp; delivery progress'">
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
                    v-tooltip="'Share your review &amp; feedback for this pastry'"
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
            <span class="font-black text-[#5C3A22]">₱{{ selectedOrder.total.toFixed(2) }}</span>
          </div>

          <div class="pt-2 border-t border-[#C08E5D]/15 space-y-1">
            <span class="text-[11px] font-bold text-[#8C7A68] uppercase tracking-wider block mb-1">Items to be cancelled:</span>
            <div
              v-for="item in selectedOrder.items"
              :key="item.id"
              class="flex justify-between text-xs text-[#1C1410]"
            >
              <span>{{ item.qty }}x {{ item.product_name }}</span>
              <span class="font-semibold text-[#5C3A22]">₱{{ item.subtotal.toFixed(2) }}</span>
            </div>
          </div>
        </div>
      </div>

      <template #footer>
        <div class="flex items-center justify-end gap-3">
          <BaseButton
            variant="outline"
            size="md"
            :disabled="cancelling"
            @click="showModal = false"
          >
            Keep My Order
          </BaseButton>

          <BaseButton
            variant="primary"
            size="md"
            :loading="cancelling"
            class="!bg-[#B84C3C] hover:!bg-[#963B2E]"
            @click="confirmCancelOrder"
          >
            Confirm Cancellation
          </BaseButton>
        </div>
      </template>
    </BaseModal>
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import { useToast } from '@/composables/useToast'
import PageHeader from '@/components/ui/PageHeader.vue'
import BaseBadge from '@/components/ui/BaseBadge.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseModal from '@/components/ui/BaseModal.vue'
import SkeletonCard from '@/components/ui/SkeletonCard.vue'
import EmptyState from '@/components/ui/EmptyState.vue'

const axios = inject('axios')
const toast = useToast()

const orders = ref([])
const loading = ref(true)
const showModal = ref(false)
const selectedOrder = ref(null)
const cancelling = ref(false)

function getStatusVariant(status) {
  switch (status?.toLowerCase()) {
    case 'completed': return 'success'
    case 'pending': return 'warning'
    case 'confirmed': case 'preparing': case 'out_for_delivery': return 'brand'
    case 'cancelled': return 'error'
    default: return 'neutral'
  }
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

onMounted(() => fetchOrders())
</script>

<template>
  <div class="space-y-8">
    <PageHeader
      tagline="customer account"
      title="My Order History"
      subtitle="Track your current orders and view receipts for previous bakery orders."
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
          <RouterLink to="/shop"><BaseButton variant="primary">Start Shopping</BaseButton></RouterLink>
        </template>
      </EmptyState>
    </div>

    <div v-else class="space-y-4">
      <div
        v-for="order in orders"
        :key="order.id"
        class="bg-white rounded-3xl p-6 border border-[#C08E5D]/20 shadow-sm space-y-4"
      >
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-[#C08E5D]/15 pb-4">
          <div>
            <div class="font-extrabold text-lg text-[#1C1410]">{{ order.order_number }}</div>
            <div class="text-xs text-[#8C7A68]">Placed on {{ new Date(order.created_at).toLocaleDateString() }}</div>
          </div>

          <div class="flex items-center gap-3">
            <BaseBadge variant="brand">{{ order.status_label }}</BaseBadge>
            <RouterLink :to="`/orders/track/${order.tracking_token}`">
              <BaseButton size="sm" variant="outline">Track Order →</BaseButton>
            </RouterLink>
          </div>
        </div>

        <div class="divide-y divide-[#C08E5D]/10">
          <div v-for="item in order.items" :key="item.id" class="py-2 flex justify-between items-center text-xs">
            <span class="font-semibold text-[#1C1410]">{{ item.qty }}x {{ item.product_name }}</span>
            <span class="font-bold text-[#5C3A22]">₱{{ item.subtotal.toFixed(2) }}</span>
          </div>
        </div>

        <div class="pt-2 border-t border-[#C08E5D]/15 flex justify-between items-center text-sm font-extrabold text-[#5C3A22]">
          <span>Total</span>
          <span>₱{{ order.total.toFixed(2) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import PageHeader from '@/components/ui/PageHeader.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseBadge from '@/components/ui/BaseBadge.vue'
import SkeletonBlock from '@/components/ui/SkeletonBlock.vue'
import EmptyState from '@/components/ui/EmptyState.vue'

const axios = inject('axios')
const orders = ref([])
const loading = ref(true)

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

onMounted(() => fetchOrders())
</script>

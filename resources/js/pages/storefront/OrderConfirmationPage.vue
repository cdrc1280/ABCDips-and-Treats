<template>
  <div class="page-container py-12 md:py-20">
    <div v-if="loading" class="max-w-2xl mx-auto space-y-6">
      <SkeletonBlock height="200px" radius="1.5rem" />
      <SkeletonBlock height="300px" radius="1.5rem" />
    </div>

    <div v-else-if="!order" class="max-w-lg mx-auto text-center py-16">
      <EmptyState title="Order Details Not Found" description="We couldn't retrieve details for this order.">
        <template #action>
          <RouterLink to="/"><BaseButton variant="primary">Return Home</BaseButton></RouterLink>
        </template>
      </EmptyState>
    </div>

    <div v-else class="max-w-3xl mx-auto space-y-8">
      <!-- Order Placed Success Banner -->
      <div class="bg-white rounded-3xl p-8 border border-[#C08E5D]/20 shadow-md text-center space-y-4">
        <div class="w-16 h-16 rounded-full bg-[#6B8F5E]/20 text-[#6B8F5E] flex items-center justify-center mx-auto">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
        </div>

        <span class="script-accent text-[#C08E5D] text-xl block">sweetness is on the way</span>
        <h1 class="text-3xl md:text-4xl font-extrabold text-[#1C1410]">Order Confirmed!</h1>
        <p class="text-[#8C7A68] text-base max-w-md mx-auto">
          Thank you, <strong class="text-[#1C1410]">{{ order.customer_name }}</strong>! We have received your pastry order <strong class="text-[#5C3A22]">{{ order.order_number }}</strong>.
        </p>

        <!-- Live Track Order Button -->
        <div class="pt-4 flex flex-wrap gap-4 justify-center">
          <RouterLink :to="`/orders/track/${order.tracking_token}`">
            <BaseButton variant="primary" size="lg">
              Track Order Progress Live →
            </BaseButton>
          </RouterLink>
        </div>
      </div>

      <!-- Payment Method Notice -->
      <BaseAlert v-if="order.payment_method === 'bank_transfer'" variant="info" title="BDO Bank Transfer Instructions">
        Please transfer <strong>₱{{ order.total.toFixed(2) }}</strong> to BDO Account <strong>0012-3456-7890 (ABCDips &amp; Treats)</strong>. Reference number: <strong>{{ order.order_number }}</strong>.
      </BaseAlert>

      <BaseAlert v-else-if="order.payment_method === 'gcash' || order.payment_method === 'maya'" variant="success" title="E-Wallet Payment Received">
        Payment reference: <strong>{{ order.payment_reference || 'SANDBOX-SUCCESS' }}</strong>. Your order is queued for baking!
      </BaseAlert>

      <!-- Order Items Breakdown -->
      <div class="bg-white rounded-3xl p-6 md:p-8 border border-[#C08E5D]/20 shadow-sm space-y-4">
        <h3 class="font-extrabold text-xl text-[#1C1410] border-b border-[#C08E5D]/20 pb-3">
          Order Summary ({{ order.order_number }})
        </h3>

        <div class="divide-y divide-[#C08E5D]/15">
          <div v-for="item in order.items" :key="item.id" class="py-3 flex justify-between items-center text-sm">
            <div>
              <div class="font-bold text-[#1C1410]">{{ item.product_name }}</div>
              <div class="text-xs text-[#8C7A68]">Qty: {{ item.qty }} × ₱{{ item.unit_price.toFixed(2) }}</div>
            </div>
            <div class="font-extrabold text-[#5C3A22]">₱{{ item.subtotal.toFixed(2) }}</div>
          </div>
        </div>

        <div class="border-t border-[#C08E5D]/20 pt-4 space-y-2 text-sm">
          <div class="flex justify-between text-[#8C7A68]">
            <span>Subtotal</span>
            <span>₱{{ order.subtotal.toFixed(2) }}</span>
          </div>
          <div v-if="order.discount_amount > 0" class="flex justify-between text-[#6B8F5E]">
            <span>Discount</span>
            <span>-₱{{ order.discount_amount.toFixed(2) }}</span>
          </div>
          <div class="flex justify-between text-[#8C7A68]">
            <span>Delivery Fee</span>
            <span>₱{{ order.delivery_fee.toFixed(2) }}</span>
          </div>
          <div class="flex justify-between text-xl font-extrabold text-[#5C3A22] border-t border-[#C08E5D]/20 pt-3">
            <span>Total Paid</span>
            <span>₱{{ order.total.toFixed(2) }}</span>
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
import BaseAlert from '@/components/ui/BaseAlert.vue'
import SkeletonBlock from '@/components/ui/SkeletonBlock.vue'
import EmptyState from '@/components/ui/EmptyState.vue'

const axios = inject('axios')
const route = useRoute()
const order = ref(null)
const loading = ref(true)

async function fetchOrder() {
  loading.value = true
  try {
    const token = route.query.token
    if (token) {
      const { data } = await axios.get(`/api/orders/track/${token}`)
      order.value = data.data
    }
  } catch (err) {
    console.error('Failed to load order confirmation', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => fetchOrder())
</script>

<template>
  <div class="page-container py-16 text-center max-w-xl mx-auto">
    <div v-if="loading" class="space-y-6 py-12">
      <div class="w-16 h-16 border-4 border-brand-caramel border-t-transparent rounded-full animate-spin mx-auto"></div>
      <h2 class="text-2xl font-bold text-ink">Verifying Your Payment...</h2>
      <p class="text-warm-gray">Please wait while we confirm your transaction with PayMongo.</p>
    </div>

    <div v-else-if="success" class="bg-white rounded-3xl p-8 border border-brand-caramel/20 shadow-xl space-y-6">
      <div class="w-20 h-20 bg-success/15 rounded-full flex items-center justify-center mx-auto text-4xl">
        🎉
      </div>
      <div>
        <span class="script-accent text-lg text-brand-caramel">payment received</span>
        <h1 class="text-3xl font-extrabold text-ink tracking-tight mt-1">Order Confirmed!</h1>
        <p class="text-warm-gray text-sm mt-2">
          Thank you for your purchase! Your payment has been successfully processed and your fresh treats are being queued for baking.
        </p>
      </div>

      <div v-if="orderDetails" class="bg-surface p-4 rounded-2xl border border-brand-caramel/20 text-left space-y-2 text-xs">
        <div class="flex justify-between">
          <span class="text-warm-gray">Order Number:</span>
          <span class="font-bold text-brand-choco">{{ orderDetails.order_number }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-warm-gray">Payment Status:</span>
          <span class="font-bold text-success uppercase">{{ orderDetails.payment_status }}</span>
        </div>
      </div>

      <div class="flex flex-col sm:flex-row gap-3 pt-2">
        <RouterLink :to="`/orders/track/${orderDetails?.tracking_token || orderDetails?.order_number}`" class="flex-1">
          <BaseButton variant="primary" full-width v-tooltip="'Track live status of your order'">
            Track Order Status
          </BaseButton>
        </RouterLink>
        <RouterLink to="/account/orders" class="flex-1">
          <BaseButton variant="outline" full-width v-tooltip="'View all your past & active orders'">
            My Orders
          </BaseButton>
        </RouterLink>
      </div>
    </div>

    <div v-else class="bg-white rounded-3xl p-8 border border-error/20 shadow-xl space-y-6">
      <div class="w-20 h-20 bg-error/15 rounded-full flex items-center justify-center mx-auto text-4xl">
        ⚠️
      </div>
      <div>
        <h1 class="text-2xl font-extrabold text-ink">Payment Verification Issue</h1>
        <p class="text-warm-gray text-sm mt-2">
          We could not verify your payment. Don't worry — if your card/wallet was charged, your order status will be updated automatically shortly.
        </p>
      </div>
      <RouterLink to="/account/orders">
        <BaseButton variant="primary">View My Orders</BaseButton>
      </RouterLink>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import { useRoute } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import BaseButton from '@/components/ui/BaseButton.vue'

const route = useRoute()
const axios = inject('axios')
const cartStore = useCartStore()

const loading = ref(true)
const success = ref(false)
const orderDetails = ref(null)

onMounted(async () => {
  cartStore.clearLocalCart()
  const orderId = route.query.order
  const sourceId = route.query.source_id

  try {
    const { data } = await axios.get('/api/payments/success', {
      params: { order: orderId, source_id: sourceId }
    })
    if (data.success) {
      success.value = true
      orderDetails.value = data
    }
  } catch (err) {
    console.error('Payment verification failed', err)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="page-container py-12 md:py-20">
    <div v-if="loading" class="max-w-2xl mx-auto space-y-6">
      <SkeletonBlock height="200px" radius="1.5rem" />
      <SkeletonBlock height="300px" radius="1.5rem" />
    </div>

    <div v-else-if="!order" class="max-w-lg mx-auto text-center py-16">
      <EmptyState title="Order Details Not Found" description="We couldn't retrieve details for this order.">
        <template #action>
          <RouterLink to="/account/orders"><BaseButton variant="primary">View My Orders</BaseButton></RouterLink>
        </template>
      </EmptyState>
    </div>

    <div v-else class="max-w-3xl mx-auto space-y-8">
      <!-- Order Placed Success Banner -->
      <div class="bg-white rounded-3xl p-8 border border-brand-caramel/20 shadow-md text-center space-y-4">
        <div class="w-16 h-16 rounded-full bg-success/20 text-success flex items-center justify-center mx-auto">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
        </div>

        <span class="script-accent text-brand-caramel text-xl block">sweetness is on the way</span>
        <h1 class="text-3xl md:text-4xl font-extrabold text-ink">Order Confirmed!</h1>
        <p class="text-warm-gray text-base max-w-md mx-auto">
          Thank you, <strong class="text-ink">{{ order.customer_name }}</strong>! We have received your pastry order <strong class="text-brand-choco">{{ order.order_number }}</strong>.
        </p>

        <!-- Live Track Order Button, Invoice & History Links -->
        <div class="pt-4 flex flex-wrap gap-4 justify-center">
          <RouterLink :to="`/track/${order.tracking_token}`">
            <BaseButton variant="primary" size="lg">
              Track Order Progress Live →
            </BaseButton>
          </RouterLink>

          <BaseButton variant="secondary" size="lg" @click="showInvoiceModal = true">
            📄 View / Download Invoice
          </BaseButton>

          <RouterLink to="/account/orders">
            <BaseButton variant="outline" size="lg">
              View Order History
            </BaseButton>
          </RouterLink>
        </div>
      </div>

      <!-- Payment Method Notice -->
      <BaseAlert v-if="order.payment_method === 'bank_transfer'" variant="info" title="BDO Bank Transfer Instructions">
        Please transfer <strong>₱{{ order.total.toFixed(2) }}</strong> to <strong>BDO Unibank</strong> Account <strong>{{ storeInfo.bdo_account_number || '0012-3456-7890' }} ({{ storeInfo.bdo_account_name || 'ABCDips & Treats' }})</strong>. Reference number to present: <strong>{{ order.order_number }}</strong>.
        <span v-if="storeInfo.bdo_instructions" class="block mt-1 text-xs opacity-90">{{ storeInfo.bdo_instructions }}</span>
      </BaseAlert>

      <BaseAlert v-else-if="order.payment_method === 'gcash' || order.payment_method === 'maya'" variant="success" title="E-Wallet Payment Received">
        Payment reference: <strong>{{ order.payment_reference || 'SANDBOX-SUCCESS' }}</strong>. Your order is queued for baking!
      </BaseAlert>

      <!-- Order Items Breakdown -->
      <div class="bg-white rounded-3xl p-6 md:p-8 border border-brand-caramel/20 shadow-sm space-y-4">
        <h3 class="font-extrabold text-xl text-ink border-b border-brand-caramel/20 pb-3 flex justify-between items-center">
          <span>Order Summary ({{ order.order_number }})</span>
          <button
            type="button"
            class="text-xs text-brand-choco font-bold hover:underline cursor-pointer flex items-center gap-1"
            @click="showInvoiceModal = true"
          >
            <span>📄 Official Invoice</span>
          </button>
        </h3>

        <div class="divide-y divide-brand-caramel/15">
          <div v-for="item in order.items" :key="item.id" class="py-3 flex justify-between items-center text-sm">
            <div>
              <div class="font-bold text-ink">{{ item.product_name }}</div>
              <div v-if="item.options?.flavor" class="text-xs font-semibold text-amber-700 dark:text-amber-300">
                Flavor: {{ item.options.flavor }}
              </div>
              <div v-if="item.options?.variation" class="text-xs font-semibold text-brand-caramel dark:text-[#E2C08A]">
                Option: {{ item.options.variation }}
              </div>
              <div class="text-xs text-warm-gray">Qty: {{ item.qty }} × ₱{{ item.unit_price.toFixed(2) }}</div>
            </div>
            <div class="font-extrabold text-brand-choco">₱{{ item.subtotal.toFixed(2) }}</div>
          </div>
        </div>

        <div class="border-t border-brand-caramel/20 pt-4 space-y-2 text-sm">
          <div class="flex justify-between text-warm-gray">
            <span>Subtotal</span>
            <span>₱{{ order.subtotal.toFixed(2) }}</span>
          </div>
          <div v-if="order.discount_amount > 0" class="flex justify-between text-success">
            <span>Discount</span>
            <span>-₱{{ order.discount_amount.toFixed(2) }}</span>
          </div>
          <div class="flex justify-between text-warm-gray">
            <span>Delivery Fee</span>
            <span>₱{{ order.delivery_fee.toFixed(2) }}</span>
          </div>
          <div class="flex justify-between text-xl font-extrabold text-brand-choco border-t border-brand-caramel/20 pt-3">
            <span>Total Paid</span>
            <span>₱{{ order.total.toFixed(2) }}</span>
          </div>
        </div>
      </div>

      <!-- Invoice Modal -->
      <InvoiceModal v-model="showInvoiceModal" :order="order" />

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
import InvoiceModal from '@/components/storefront/InvoiceModal.vue'

const axios = inject('axios')
const route = useRoute()
const order = ref(null)
const storeInfo = ref({})
const loading = ref(true)
const showInvoiceModal = ref(false)

async function fetchOrder() {
  loading.value = true
  try {
    const token = route.params.token || route.query.token
    const [ordRes, setRes] = await Promise.all([
      token ? axios.get(`/api/orders/track/${token}`) : Promise.resolve(null),
      axios.get('/api/settings/store')
    ])
    if (ordRes) order.value = ordRes.data.data
    storeInfo.value = setRes.data || {}
  } catch (err) {
    console.error('Failed to load order confirmation', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => fetchOrder())
</script>

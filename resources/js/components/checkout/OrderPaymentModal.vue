<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 flex items-center justify-center">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-ink/70 dark:bg-black/80 backdrop-blur-xs" @click="$emit('close')"></div>

        <!-- Modal Card -->
        <div class="relative bg-white dark:bg-[#1E1510] rounded-3xl border border-brand-caramel/30 shadow-2xl max-w-lg w-full p-6 md:p-8 space-y-6 z-10 text-ink dark:text-[#FBF3E7]">
          <div class="flex items-center justify-between border-b border-brand-caramel/20 pb-4">
            <div>
              <span class="text-xs font-bold uppercase tracking-wider text-brand-caramel dark:text-[#E2C08A]">Settle Order Payment</span>
              <h3 class="text-xl font-extrabold text-ink dark:text-white">Order #{{ order?.order_number }}</h3>
            </div>
            <button type="button" @click="$emit('close')" class="w-8 h-8 rounded-full bg-surface dark:bg-[#2A1C13] flex items-center justify-center text-warm-gray hover:text-ink transition-colors">
              
            </button>
          </div>

          <!-- Order Summary Badge -->
          <div class="bg-amber-50 dark:bg-[#2A1C13] p-4 rounded-2xl border border-amber-200 dark:border-amber-900/40 space-y-2.5 text-xs">
            <div class="flex justify-between text-warm-gray">
              <span>Items Subtotal:</span>
              <span class="font-bold text-ink dark:text-white">₱{{ (order?.subtotal || 0).toFixed(2) }}</span>
            </div>

            <!-- Pooling Specific Details Badge -->
            <div v-if="order?.delivery_mode === 'pooling'" class="p-3 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/70 border border-emerald-300 dark:border-emerald-800 space-y-1.5 text-[11px]">
              <div class="flex items-center justify-between text-emerald-950 dark:text-emerald-200 font-extrabold pb-1 border-b border-emerald-300/50">
                <span class="flex items-center gap-1"><Users class="w-4 h-4" /><span>Group Delivery Pooling Details</span></span>
                <span class="bg-emerald-700 text-white px-2 py-0.5 rounded-full text-[10px] uppercase font-bold tracking-wide">
                  {{ order?.pooling_status === 'settled' ? 'Rate Settled' : 'Pending Rate' }}
                </span>
              </div>
              <div v-if="order?.delivery_pool?.pool_code" class="text-emerald-900 dark:text-emerald-300 flex justify-between">
                <span class="text-emerald-800 dark:text-emerald-400">Batch Pool Code:</span>
                <span class="font-mono font-bold">#{{ order.delivery_pool.pool_code }}</span>
              </div>
              <div v-if="order?.barangay || order?.city" class="text-emerald-900 dark:text-emerald-300 flex justify-between">
                <span class="text-emerald-800 dark:text-emerald-400">Pooled Zone Location:</span>
                <span class="font-bold text-right">{{ [order?.barangay, order?.city].filter(Boolean).join(', ') }}</span>
              </div>
              <div class="text-emerald-900 dark:text-emerald-300 flex justify-between pt-1 border-t border-emerald-300/50">
                <span class="font-bold">Assigned Group Shipping Fee:</span>
                <span class="font-black text-emerald-900 dark:text-emerald-200">₱{{ (order?.shipping_fee || order?.delivery_fee || 0).toFixed(2) }}</span>
              </div>
            </div>

            <!-- Priority / Standard Delivery Fee -->
            <div v-else class="flex justify-between text-emerald-700 dark:text-emerald-400">
              <span>Priority Delivery Fee:</span>
              <span class="font-bold">₱{{ (order?.shipping_fee || order?.delivery_fee || 0).toFixed(2) }}</span>
            </div>

            <div v-if="order?.discount" class="flex justify-between text-rose-600">
              <span>Voucher Discount:</span>
              <span class="font-bold">-₱{{ (order?.discount || 0).toFixed(2) }}</span>
            </div>
            <div class="pt-2 border-t border-amber-200 dark:border-amber-900/40 flex justify-between items-baseline font-black text-base text-brand-choco dark:text-[#E2C08A]">
              <span>Final Total to Settle:</span>
              <span>₱{{ (order?.total || 0).toFixed(2) }}</span>
            </div>
          </div>

          <!-- Select Payment Gateway -->
          <div class="space-y-3">
            <label class="block text-xs font-extrabold uppercase tracking-wider text-brand-choco dark:text-[#E2C08A]">Select Payment Gateway</label>

            <!-- GCash -->
            <label class="border-2 rounded-2xl p-3.5 cursor-pointer flex items-center gap-3 transition-all"
              :class="selectedMethod === 'gcash' ? 'border-brand-choco bg-surface dark:bg-[#2A1C13]' : 'border-brand-caramel/20 bg-white dark:bg-[#1E1510]'">
              <input type="radio" v-model="selectedMethod" value="gcash" class="sr-only" />
              <span class="text-xs font-bold text-blue-600 bg-blue-100 px-2 py-1 rounded-lg">GCash</span>
              <div class="flex-1">
                <span class="font-extrabold text-xs block">GCash E-Wallet</span>
                <span class="text-[11px] text-warm-gray block">Instant online payment via GCash app</span>
              </div>
            </label>

            <!-- Maya -->
            <label class="border-2 rounded-2xl p-3.5 cursor-pointer flex items-center gap-3 transition-all"
              :class="selectedMethod === 'maya' ? 'border-brand-choco bg-surface dark:bg-[#2A1C13]' : 'border-brand-caramel/20 bg-white dark:bg-[#1E1510]'">
              <input type="radio" v-model="selectedMethod" value="maya" class="sr-only" />
              <span class="text-xs font-bold text-emerald-600 bg-emerald-100 px-2 py-1 rounded-lg">Maya</span>
              <div class="flex-1">
                <span class="font-extrabold text-xs block">Maya E-Wallet</span>
                <span class="text-[11px] text-warm-gray block">Pay instantly using Maya wallet</span>
              </div>
            </label>

            <!-- QR Ph -->
            <label v-if="enableQrph" class="border-2 rounded-2xl p-3.5 cursor-pointer flex items-center gap-3 transition-all"
              :class="selectedMethod === 'qrph' ? 'border-brand-choco bg-surface dark:bg-[#2A1C13]' : 'border-brand-caramel/20 bg-white dark:bg-[#1E1510]'">
              <input type="radio" v-model="selectedMethod" value="qrph" class="sr-only" />
              <span class="text-xs font-bold text-violet-600 bg-violet-100 px-2 py-1 rounded-lg">QR</span>
              <div class="flex-1">
                <span class="font-extrabold text-xs block">QR Ph (Scan & Pay)</span>
                <span class="text-[11px] text-warm-gray block">Scan QR code using GCash, Maya, ShopeePay, BDO, BPI, UnionBank</span>
              </div>
            </label>

            <!-- BDO Bank Transfer -->
            <label class="border-2 rounded-2xl p-3.5 cursor-pointer flex items-center gap-3 transition-all"
              :class="selectedMethod === 'bank_transfer' ? 'border-brand-choco bg-surface dark:bg-[#2A1C13]' : 'border-brand-caramel/20 bg-white dark:bg-[#1E1510]'">
              <input type="radio" v-model="selectedMethod" value="bank_transfer" class="sr-only" />
              <span class="text-xs font-bold text-amber-800 bg-amber-100 px-2 py-1 rounded-lg">Bank</span>
              <div class="flex-1">
                <span class="font-extrabold text-xs block">BDO Bank Transfer / OTC</span>
                <span class="text-[11px] text-warm-gray block">Manual online bank transfer or Over-the-Counter deposit</span>
              </div>
            </label>
          </div>

          <!-- BDO Instructions Box if Bank Transfer Selected -->
          <div v-if="selectedMethod === 'bank_transfer'" class="p-4 rounded-2xl bg-amber-50/90 dark:bg-[#2A1C13] border border-amber-300 dark:border-amber-800 space-y-2 text-xs">
            <h4 class="font-bold text-amber-950 dark:text-amber-200">BDO Deposit Details</h4>
            <div class="space-y-1 text-amber-900 dark:text-amber-300 font-mono">
              <p>Account Name: <strong>{{ storeInfo.bank_account_name || 'ABCDips & Treats' }}</strong></p>
              <p>Account Number: <strong>{{ storeInfo.bank_account_number || '0012-3456-7890' }}</strong></p>
              <p>Transfer Amount: <strong>₱{{ (order?.total || 0).toFixed(2) }}</strong></p>
              <p>Reference Note: <strong>{{ order?.order_number }}</strong></p>
            </div>
            <p class="text-[11px] text-amber-800 dark:text-amber-400 italic">Please use your order number in your transfer notes so kitchen staff can verify your payment.</p>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-3 pt-2">
            <BaseButton type="button" variant="outline" full-width @click="$emit('close')">
              Cancel
            </BaseButton>

            <BaseButton type="button" variant="primary" full-width :loading="submitting" @click="processPayment">
              Confirm Payment (₱{{ (order?.total || 0).toFixed(2) }})
            </BaseButton>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, inject } from 'vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import { useToast } from '@/composables/useToast'

const props = defineProps({
  show: { type: Boolean, default: false },
  order: { type: Object, default: () => ({}) },
  storeInfo: { type: Object, default: () => ({}) },
  enableQrph: { type: Boolean, default: true }
})

const emit = defineEmits(['close', 'payment-success'])
const axios = inject('axios')
const toast = useToast()

const selectedMethod = ref('gcash')
const submitting = ref(false)

async function processPayment() {
  if (!props.order?.id) return

  submitting.value = true
  try {
    if (selectedMethod.value === 'bank_transfer') {
      toast.success('BDO payment details saved! Please upload or present deposit slip.', 'Bank Transfer Pending')
      emit('payment-success', { method: 'bank_transfer' })
      emit('close')
      return
    }

    const { data } = await axios.post('/api/payments/create-source', {
      order_id: props.order.id,
      method: selectedMethod.value
    })

    if (data.checkout_url) {
      window.location.href = data.checkout_url
    } else {
      toast.success('Payment initiated successfully!', 'Processing')
      emit('payment-success', { method: selectedMethod.value })
      emit('close')
    }
  } catch (err) {
    toast.error(err.response?.data?.message || 'Failed to initiate payment. Please try again.', 'Payment Error')
  } finally {
    submitting.value = false
  }
}
</script>

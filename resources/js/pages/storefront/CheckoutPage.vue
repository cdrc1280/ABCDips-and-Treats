<template>
    <div class="page-container py-10 md:py-16">
        <PageHeader tagline="almost there" title="Secure Checkout"
            subtitle="Complete your contact details, choose delivery or store pickup, and select your payment method." />

        <div v-if="cartStore.items.length === 0"
            class="py-12 text-center bg-white rounded-3xl border border-[#C08E5D]/20">
            <EmptyState title="Your Basket is Empty" description="Add items to your basket before checking out.">
                <template #action>
                    <RouterLink to="/shop">
                        <BaseButton variant="primary">Return to Shop</BaseButton>
                    </RouterLink>
                </template>
            </EmptyState>
        </div>

        <form v-else @submit.prevent="handleCheckout" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- Form Column Left -->
            <div class="lg:col-span-7 space-y-6">

                <!-- 1. Fulfillment Option -->
                <div class="bg-white rounded-3xl p-6 md:p-8 border border-[#C08E5D]/20 shadow-sm space-y-4">
                    <h3 class="font-extrabold text-xl text-[#1C1410] border-b border-[#C08E5D]/20 pb-3">
                        1. Fulfillment Method
                    </h3>

                    <div class="grid grid-cols-2 gap-4">
                        <label
                            class="border-2 rounded-2xl p-4 cursor-pointer flex flex-col items-center justify-center text-center transition-all"
                            :class="form.fulfillment_type === 'delivery' ? 'border-[#5C3A22] bg-[#FBF3E7]' : 'border-[#C08E5D]/20 bg-white opacity-70 hover:opacity-100'">
                            <input type="radio" v-model="form.fulfillment_type" value="delivery" class="sr-only" />
                            <div
                                class="w-10 h-10 rounded-full bg-[#D9A876]/30 flex items-center justify-center text-[#5C3A22] mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="font-bold text-sm text-[#1C1410]">Doorstep Delivery</div>
                            <div class="text-xs text-[#8C7A68] mt-0.5">₱120.00 Flat Rate</div>
                        </label>

                        <label
                            class="border-2 rounded-2xl p-4 cursor-pointer flex flex-col items-center justify-center text-center transition-all"
                            :class="form.fulfillment_type === 'pickup' ? 'border-[#5C3A22] bg-[#FBF3E7]' : 'border-[#C08E5D]/20 bg-white opacity-70 hover:opacity-100'">
                            <input type="radio" v-model="form.fulfillment_type" value="pickup" class="sr-only" />
                            <div
                                class="w-10 h-10 rounded-full bg-[#D9A876]/30 flex items-center justify-center text-[#5C3A22] mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="font-bold text-sm text-[#1C1410]">Store Pickup</div>
                            <div class="text-xs text-[#6B8F5E] font-semibold mt-0.5">FREE</div>
                        </label>
                    </div>
                </div>

                <!-- 2. Customer & Address Details -->
                <div class="bg-white rounded-3xl p-6 md:p-8 border border-[#C08E5D]/20 shadow-sm space-y-4">
                    <h3 class="font-extrabold text-xl text-[#1C1410] border-b border-[#C08E5D]/20 pb-3">
                        2. Customer Details
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <BaseInput v-model="form.customer_name" label="Full Name" placeholder="e.g. Maria Santos"
                            required :error="errors.customer_name?.[0]" />
                        <BaseInput v-model="form.customer_email" type="email" label="Email Address"
                            placeholder="maria@example.com" required :error="errors.customer_email?.[0]" />
                        <BaseInput v-model="form.customer_phone" label="Mobile Number" placeholder="0917 123 4567"
                            required :error="errors.customer_phone?.[0]" />
                        <BaseInput v-model="form.city" label="City / District" placeholder="e.g. Quezon City, Manila" />
                    </div>

                    <div v-if="form.fulfillment_type === 'delivery'" class="space-y-4 pt-2">
                        <BaseTextarea v-model="form.delivery_address" label="Complete Delivery Address"
                            placeholder="House/Unit #, Street Name, Barangay, Landmark" rows="3" required
                            :error="errors.delivery_address?.[0]" />
                    </div>

                    <BaseTextarea v-model="form.notes" label="Special Delivery / Baking Instructions (Optional)"
                        placeholder="e.g. Please leave with security guard, or birthday candle count..." rows="2" />
                </div>

                <!-- 3. Payment Method Selection -->
                <div class="bg-white rounded-3xl p-6 md:p-8 border border-[#C08E5D]/20 shadow-sm space-y-4">
                    <h3 class="font-extrabold text-xl text-[#1C1410] border-b border-[#C08E5D]/20 pb-3">
                        3. Select Payment Method
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- GCash -->
                        <label class="border-2 rounded-2xl p-4 cursor-pointer flex items-center gap-3 transition-all"
                            :class="form.payment_method === 'gcash' ? 'border-[#5C3A22] bg-[#FBF3E7]' : 'border-[#C08E5D]/20 bg-white opacity-70 hover:opacity-100'">
                            <input type="radio" v-model="form.payment_method" value="gcash" class="sr-only" />
                            <div
                                class="w-10 h-10 rounded-xl bg-blue-500 text-white font-bold text-xs flex items-center justify-center flex-shrink-0">
                                GCash
                            </div>
                            <div>
                                <div class="font-bold text-sm text-[#1C1410]">GCash E-Wallet</div>
                                <div class="text-[11px] text-[#8C7A68]">Instant payment via QR</div>
                            </div>
                        </label>

                        <!-- Maya -->
                        <label class="border-2 rounded-2xl p-4 cursor-pointer flex items-center gap-3 transition-all"
                            :class="form.payment_method === 'maya' ? 'border-[#5C3A22] bg-[#FBF3E7]' : 'border-[#C08E5D]/20 bg-white opacity-70 hover:opacity-100'">
                            <input type="radio" v-model="form.payment_method" value="maya" class="sr-only" />
                            <div
                                class="w-10 h-10 rounded-xl bg-emerald-600 text-white font-bold text-xs flex items-center justify-center flex-shrink-0">
                                Maya
                            </div>
                            <div>
                                <div class="font-bold text-sm text-[#1C1410]">Maya Wallet / Card</div>
                                <div class="text-[11px] text-[#8C7A68]">Pay via Maya App</div>
                            </div>
                        </label>

                        <!-- Bank Transfer -->
                        <label class="border-2 rounded-2xl p-4 cursor-pointer flex items-center gap-3 transition-all"
                            :class="form.payment_method === 'bank_transfer' ? 'border-[#5C3A22] bg-[#FBF3E7]' : 'border-[#C08E5D]/20 bg-white opacity-70 hover:opacity-100'">
                            <input type="radio" v-model="form.payment_method" value="bank_transfer" class="sr-only" />
                            <div
                                class="w-10 h-10 rounded-xl bg-[#5C3A22] text-[#FBF3E7] font-bold text-xs flex items-center justify-center flex-shrink-0">
                                BDO
                            </div>
                            <div>
                                <div class="font-bold text-sm text-[#1C1410]">Bank Transfer (BDO)</div>
                                <div class="text-[11px] text-[#8C7A68]">Manual bank deposit</div>
                            </div>
                        </label>

                        <!-- Cash on Delivery / Pickup -->
                        <label class="border-2 rounded-2xl p-4 cursor-pointer flex items-center gap-3 transition-all"
                            :class="form.payment_method === 'cod' ? 'border-[#5C3A22] bg-[#FBF3E7]' : 'border-[#C08E5D]/20 bg-white opacity-70 hover:opacity-100'">
                            <input type="radio" v-model="form.payment_method" value="cod" class="sr-only" />
                            <div
                                class="w-10 h-10 rounded-xl bg-amber-700 text-white font-bold text-xs flex items-center justify-center flex-shrink-0">
                                COD
                            </div>
                            <div>
                                <div class="font-bold text-sm text-[#1C1410]">Cash on Delivery</div>
                                <div class="text-[11px] text-[#8C7A68]">Pay upon arrival</div>
                            </div>
                        </label>
                    </div>
                </div>

            </div>

            <!-- Summary Right Column -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white rounded-3xl p-6 md:p-8 border border-[#C08E5D]/20 shadow-sm space-y-6">
                    <h3 class="font-extrabold text-xl text-[#1C1410] border-b border-[#C08E5D]/20 pb-3">
                        Order Items ({{ cartStore.itemCount }})
                    </h3>

                    <!-- Items Mini List -->
                    <div class="max-h-60 overflow-y-auto space-y-3 pr-1">
                        <div v-for="item in cartStore.items" :key="item.id"
                            class="flex items-center justify-between text-xs py-1">
                            <div class="flex items-center gap-3 min-w-0">
                                <img :src="item.image_url" class="w-10 h-10 rounded-lg object-cover flex-shrink-0" />
                                <div class="truncate">
                                    <div class="font-bold text-[#1C1410] truncate">{{ item.name }}</div>
                                    <div class="text-[#8C7A68]">Qty: {{ item.qty }}</div>
                                </div>
                            </div>
                            <span class="font-bold text-[#5C3A22] flex-shrink-0">₱{{ (item.subtotal || 0).toFixed(2)
                                }}</span>
                        </div>
                    </div>

                    <!-- Price Calculation -->
                    <div class="space-y-3 text-sm border-t border-[#C08E5D]/20 pt-4">
                        <div class="flex justify-between text-[#8C7A68]">
                            <span>Subtotal</span>
                            <span class="font-semibold text-[#1C1410]">₱{{ cartStore.subtotal.toFixed(2) }}</span>
                        </div>

                        <div v-if="cartStore.discount > 0" class="flex justify-between text-[#6B8F5E]">
                            <span>Discount</span>
                            <span>-₱{{ cartStore.discount.toFixed(2) }}</span>
                        </div>

                        <div class="flex justify-between text-[#8C7A68]">
                            <span>Delivery Fee</span>
                            <span>₱{{ deliveryFee.toFixed(2) }}</span>
                        </div>

                        <div
                            class="flex justify-between text-xl font-extrabold text-[#5C3A22] border-t border-[#C08E5D]/20 pt-3">
                            <span>Final Total</span>
                            <span>₱{{ grandTotal.toFixed(2) }}</span>
                        </div>
                    </div>

                    <BaseButton type="submit" variant="primary" full-width size="lg" :loading="submitting">
                        Confirm &amp; Place Order • ₱{{ grandTotal.toFixed(2) }}
                    </BaseButton>
                </div>
            </div>

        </form>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, inject } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'
import { useToast } from '@/composables/useToast'
import PageHeader from '@/components/ui/PageHeader.vue'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseTextarea from '@/components/ui/BaseTextarea.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import EmptyState from '@/components/ui/EmptyState.vue'

const axios = inject('axios')
const router = useRouter()
const cartStore = useCartStore()
const authStore = useAuthStore()
const toast = useToast()

const submitting = ref(false)
const errors = ref({})

const form = ref({
    customer_name: '',
    customer_email: '',
    customer_phone: '',
    fulfillment_type: 'delivery',
    delivery_address: '',
    city: 'Cavite',
    postal_code: '',
    notes: '',
    payment_method: 'gcash'
})

function populateUserData() {
    if (authStore.user) {
        if (!form.value.customer_name) form.value.customer_name = authStore.user.name || ''
        if (!form.value.customer_email) form.value.customer_email = authStore.user.email || ''
        if (!form.value.customer_phone) form.value.customer_phone = authStore.user.phone || ''
        if (!form.value.delivery_address) form.value.delivery_address = authStore.user.address || ''
    }
}

watch(() => authStore.user, populateUserData, { immediate: true })

onMounted(async () => {
    if (!authStore.isAuthenticated) {
        toast.warning('Please sign in to place your order.', 'Sign In Required')
        router.push({ name: 'login', query: { redirect: '/checkout' } })
        return
    }
    populateUserData()
    await cartStore.fetchCart()
})

const deliveryFee = computed(() => form.value.fulfillment_type === 'delivery' ? 120.00 : 0.00)
const grandTotal = computed(() => Math.max(0, cartStore.total + deliveryFee.value))

async function handleCheckout() {
    if (!authStore.isAuthenticated) {
        toast.warning('Please sign in to place your order.', 'Sign In Required')
        router.push({ name: 'login', query: { redirect: '/checkout' } })
        return
    }

    submitting.value = true
    errors.value = {}

    try {
        const { data } = await axios.post('/api/checkout', form.value, {
            headers: { 'X-Cart-Token': cartStore.cartToken }
        })

        toast.success('Order placed successfully!', 'Thank you!')
        cartStore.clearLocalCart()

        const trackingToken = data.data?.tracking_token || data.data?.trackingToken
        router.push({ name: 'order-confirmation', params: { token: trackingToken } })
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors || {}
            toast.error('Please check the form for missing or invalid details.', 'Checkout Error')
        } else {
            toast.error(err.response?.data?.message || 'Failed to place order. Please try again.', 'Checkout Error')
        }
    } finally {
        submitting.value = false
    }
}
</script>

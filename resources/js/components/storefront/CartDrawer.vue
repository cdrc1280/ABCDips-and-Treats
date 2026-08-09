<template>
    <Teleport to="body">
        <!-- Backdrop -->
        <Transition name="fade">
            <div v-if="cartStore.openDrawer" class="fixed inset-0 z-80 bg-ink/50 backdrop-blur-xs"
                @click="cartStore.openDrawer = false" />
        </Transition>

        <!-- Slide-over Drawer -->
        <Transition name="slide">
            <div v-if="cartStore.openDrawer"
                class="fixed inset-y-0 right-0 z-90 w-full max-w-md bg-white dark:bg-[#1E1510] shadow-2xl flex flex-col justify-between border-l border-brand-caramel/20 dark:border-[#C08E5D]/20">
                <!-- Header -->
                <div class="p-6 border-b border-brand-caramel/20 dark:border-[#C08E5D]/20 flex items-center justify-between bg-surface dark:bg-[#140D09]">
                    <div>
                        <h2 class="text-xl font-bold text-ink dark:text-[#FBF3E7]">Your Pastry Basket</h2>
                        <p class="text-xs text-warm-gray dark:text-[#C5B4A4]">{{ cartStore.itemCount }} {{ cartStore.itemCount === 1 ?
                            'item' : 'items' }}</p>
                    </div>
                    <button v-tooltip="'Close basket drawer'"
                        class="p-2 rounded-xl text-warm-gray hover:text-brand-choco hover:bg-brand-tan/20 transition-all"
                        @click="cartStore.openDrawer = false">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Items List -->
                <div class="flex-1 overflow-y-auto p-6 space-y-4">
                    <div v-if="cartStore.items.length === 0" class="py-12 text-center">
                        <EmptyState title="Your Basket is Empty"
                            description="Add some fresh banana bread, cookies, brownies, or build a custom cake.">
                            <template #action>
                                <RouterLink to="/shop" @click="cartStore.openDrawer = false">
                                    <BaseButton variant="primary">Browse Menu</BaseButton>
                                </RouterLink>
                            </template>
                        </EmptyState>
                    </div>

                    <div v-for="item in cartStore.items" :key="item.id"
                        class="flex items-start gap-4 py-3 border-b border-brand-caramel/15">
                        <img :src="item.image_url || '/images/placeholder-bakery.png'" :alt="item.name"
                            class="w-16 h-16 rounded-xl object-cover border border-brand-caramel/20 shrink-0" />
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-sm text-ink truncate">
                                {{ item.options?.is_custom ? item.options.custom_title : item.name }}
                            </h4>

                            <!-- Variation Option Badge -->
                            <div v-if="item.options?.variation" class="text-xs font-semibold text-brand-caramel dark:text-[#E2C08A] mt-0.5">
                                Option: {{ item.options.variation }}
                            </div>

                            <!-- Custom Cake Spec Pill -->
                            <div v-if="item.options?.is_custom"
                                class="mt-1 bg-surface p-2.5 rounded-xl text-[11px] text-brand-choco border border-brand-caramel/20 space-y-0.5">
                                <div class="font-extrabold flex items-center gap-1 text-brand-choco">
                                    🎂 Custom Cake Spec:
                                </div>
                                <div>Flavor: <strong>{{ item.options.flavor_preference }}</strong></div>
                                <div>Frosting: <strong>{{ item.options.frosting_type }}</strong></div>
                                <div v-if="item.options.budget_range_min" class="text-warm-gray">
                                    Budget: <strong>₱{{ item.options.budget_range_min }} - ₱{{
                                        item.options.budget_range_max }}</strong>
                                </div>
                                <div v-if="item.options.cake_inscription" class="italic text-warm-gray">"{{
                                    item.options.cake_inscription
                                    }}"</div>
                                <div v-if="item.options.event_date" class="text-warm-gray">Event: {{
                                    item.options.event_date }}</div>
                            </div>

                            <div class="text-xs text-warm-gray mt-1">₱{{ item.unit_price.toFixed(2) }} each</div>

                            <!-- Quantity selector -->
                            <div class="flex items-center gap-2 mt-2">
                                <button v-tooltip="'Decrease quantity'"
                                    class="w-6 h-6 rounded bg-surface text-brand-choco font-bold text-xs flex items-center justify-center hover:bg-brand-tan/30"
                                    @click="cartStore.updateItem(item.id, item.qty - 1)">
                                    -
                                </button>
                                <span class="text-xs font-bold w-6 text-center text-ink">{{ item.qty }}</span>
                                <button v-tooltip="'Increase quantity'"
                                    class="w-6 h-6 rounded bg-surface text-brand-choco font-bold text-xs flex items-center justify-center hover:bg-brand-tan/30"
                                    @click="cartStore.updateItem(item.id, item.qty + 1)">
                                    +
                                </button>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <div class="font-extrabold text-sm text-brand-choco">₱{{ item.subtotal.toFixed(2) }}</div>
                            <button v-tooltip="'Remove this item from your basket'"
                                class="text-xs text-error hover:underline mt-1 block"
                                @click="cartStore.removeItem(item.id)">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Footer Summary -->
                <div v-if="cartStore.items.length > 0"
                    class="p-6 border-t border-brand-caramel/20 bg-surface dark:bg-[#1E130B] space-y-4">
                    <!-- Coupon Box inside Drawer -->
                    <div class="space-y-2">
                        <div v-if="cartStore.couponCode"
                            class="flex items-center justify-between bg-success/15 border border-success/30 p-2 rounded-xl text-xs">
                            <div class="flex items-center gap-1">
                                <span>🎟️</span>
                                <span class="font-bold text-[#2D4525] dark:text-surface-400">{{ cartStore.couponCode
                                    }}</span>
                            </div>
                            <button type="button" class="text-xs text-error font-bold hover:underline"
                                @click="cartStore.removeCoupon">Remove</button>
                        </div>
                        <div v-else class="flex gap-2">
                            <input v-model="drawerCouponCode" type="text" placeholder="Voucher code..."
                                class="flex-1 px-3 py-1.5 text-xs rounded-xl border border-brand-caramel/30 bg-white dark:bg-[#271C15] text-ink dark:text-surface focus:outline-none"
                                @keyup.enter="applyDrawerCoupon" />
                            <BaseButton size="sm" variant="secondary" :loading="applyingDrawerCoupon"
                                @click="applyDrawerCoupon">Apply
                            </BaseButton>
                        </div>
                    </div>

                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between text-warm-gray dark:text-[#C5B4A4]">
                            <span>Subtotal</span>
                            <span>₱{{ cartStore.subtotal.toFixed(2) }}</span>
                        </div>
                        <div v-if="cartStore.discount > 0" class="flex justify-between text-success">
                            <span>Discount</span>
                            <span>-₱{{ cartStore.discount.toFixed(2) }}</span>
                        </div>
                        <div
                            class="flex justify-between text-base font-extrabold text-brand-choco dark:text-surface-400 pt-2 border-t border-brand-caramel/20">
                            <span>Estimated Total</span>
                            <span>₱{{ cartStore.total.toFixed(2) }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <RouterLink to="/cart" @click="cartStore.openDrawer = false"
                            v-tooltip="'See full cart with pricing, coupon codes &amp; order notes'">
                            <BaseButton variant="outline" full-width>View Cart</BaseButton>
                        </RouterLink>
                        <RouterLink to="/checkout" @click="cartStore.openDrawer = false"
                            v-tooltip="'Proceed to payment &amp; shipping details'">
                            <BaseButton variant="primary" full-width>Checkout</BaseButton>
                        </RouterLink>
                    </div>
                </div>

            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { ref } from 'vue'
import { useCartStore } from '@/stores/cart'
import { useToast } from '@/composables/useToast'
import BaseButton from '@/components/ui/BaseButton.vue'
import EmptyState from '@/components/ui/EmptyState.vue'

const cartStore = useCartStore()
const toast = useToast()

const drawerCouponCode = ref('')
const applyingDrawerCoupon = ref(false)

async function applyDrawerCoupon() {
    if (!drawerCouponCode.value.trim()) return
    applyingDrawerCoupon.value = true
    const res = await cartStore.applyCoupon(drawerCouponCode.value)
    applyingDrawerCoupon.value = false
    if (res.success) {
        toast.success('Discount coupon applied!', 'Voucher Applied 🎟️')
        drawerCouponCode.value = ''
    } else {
        toast.error(res.error || 'Invalid or expired coupon code.', 'Coupon Error')
    }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.slide-enter-active,
.slide-leave-active {
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.slide-enter-from,
.slide-leave-to {
    transform: translateX(100%);
}
</style>

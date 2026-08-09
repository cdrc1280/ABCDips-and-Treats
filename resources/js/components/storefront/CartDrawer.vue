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

                <!-- Bulk Selection Controls Bar (when items exist) -->
                <div v-if="cartStore.items.length > 0" class="px-6 py-2.5 bg-surface dark:bg-[#1E130B] border-b border-brand-caramel/15 flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 font-bold text-ink dark:text-[#FBF3E7] cursor-pointer">
                        <input type="checkbox" :checked="isAllSelected" @change="toggleSelectAll" class="rounded text-brand-choco focus:ring-brand-choco w-3.5 h-3.5" />
                        <span>Select All</span>
                    </label>
                    <div v-if="selectedItemIds.length > 0" class="flex items-center gap-2">
                        <button class="px-2.5 py-1 rounded-lg bg-brand-tan/20 text-brand-choco dark:text-[#E2C08A] font-bold hover:bg-brand-tan/40 transition-colors" @click="openBulkEdit">
                            Edit ({{ selectedItemIds.length }})
                        </button>
                        <button class="px-2.5 py-1 rounded-lg bg-error/10 text-error font-bold hover:bg-error/20 transition-colors" @click="promptRemoveBulk">
                            Delete ({{ selectedItemIds.length }})
                        </button>
                    </div>
                </div>

                <!-- Items List -->
                <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4">
                    <div v-if="cartStore.items.length === 0" class="py-12 text-center">
                        <EmptyState title="Your Basket is Empty 🧁"
                            description="You haven't added any treats yet! Explore our oven-fresh pastries and custom cakes.">
                            <template #action>
                                <RouterLink to="/shop" @click="cartStore.openDrawer = false">
                                    <BaseButton variant="primary">Browse Bakery Menu</BaseButton>
                                </RouterLink>
                            </template>
                        </EmptyState>
                    </div>

                    <div v-for="item in cartStore.items" :key="item.id"
                        class="flex items-start gap-3 py-3 border-b border-brand-caramel/15">
                        <input type="checkbox" :value="item.id" v-model="selectedItemIds" class="mt-2 rounded text-brand-choco focus:ring-brand-choco w-3.5 h-3.5 shrink-0 cursor-pointer" />
                        <img :src="item.image_url || '/images/placeholder-bakery.png'" :alt="item.name"
                            class="w-14 h-14 rounded-xl object-cover border border-brand-caramel/20 shrink-0" />
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-sm text-ink truncate">
                                {{ item.options?.is_custom ? item.options.custom_title : item.name }}
                            </h4>

                            <!-- Flavor Option Badge -->
                            <div v-if="item.options?.flavor" class="text-xs font-semibold text-amber-700 dark:text-amber-300 mt-0.5">
                                Flavor: {{ item.options.flavor }}
                            </div>

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
                            <div class="font-extrabold text-sm text-brand-choco dark:text-[#E2C08A]">₱{{ item.subtotal.toFixed(2) }}</div>
                            <button v-if="!item.options?.is_custom"
                                v-tooltip="'Edit variation or flavor options'"
                                class="text-xs text-brand-choco dark:text-[#E2C08A] hover:underline font-semibold flex items-center justify-end gap-1 mt-1 ml-auto"
                                @click="handleEditItem(item)">
                                Edit
                            </button>
                            <button v-tooltip="'Remove this item from your basket'"
                                class="text-xs text-error hover:underline mt-1 block ml-auto"
                                @click="promptRemoveSingle(item)">
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
                            <button class="text-error font-bold hover:underline" @click="cartStore.removeCoupon">Remove</button>
                        </div>
                        <div v-else class="flex gap-2">
                            <input v-model="drawerCouponCode" type="text" placeholder="Promo code"
                                class="flex-1 px-3 py-1.5 rounded-xl border border-brand-caramel/30 dark:border-[#C08E5D]/30 text-xs bg-white dark:bg-[#120B07] text-ink dark:text-[#FBF3E7] uppercase font-bold focus:outline-none focus:border-brand-choco" />
                            <BaseButton size="sm" variant="secondary" :disabled="applyingDrawerCoupon" @click="applyDrawerCoupon">
                                Apply
                            </BaseButton>
                        </div>
                    </div>

                    <div class="space-y-1.5 text-xs text-warm-gray">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span class="font-bold text-ink">₱{{ cartStore.subtotal.toFixed(2) }}</span>
                        </div>
                        <div v-if="cartStore.discount > 0" class="flex justify-between text-success">
                            <span>Voucher Discount</span>
                            <span class="font-bold">-₱{{ cartStore.discount.toFixed(2) }}</span>
                        </div>
                        <div v-if="cartStore.fees > 0" class="flex justify-between">
                            <span>Handling Fee</span>
                            <span class="font-bold text-ink">₱{{ cartStore.fees.toFixed(2) }}</span>
                        </div>
                        <div
                            class="flex justify-between text-base font-extrabold text-brand-choco dark:text-[#E2C08A] pt-2 border-t border-brand-caramel/20">
                            <span>Estimated Total</span>
                            <span>₱{{ cartStore.total.toFixed(2) }}</span>
                        </div>
                    </div>

                    <div class="space-y-2 pt-2">
                        <RouterLink to="/checkout" @click="cartStore.openDrawer = false">
                            <BaseButton variant="primary" full-width size="lg" v-tooltip="'Proceed to fast checkout &amp; order placement'">
                                Checkout • ₱{{ cartStore.total.toFixed(2) }}
                            </BaseButton>
                        </RouterLink>
                        <RouterLink to="/cart" @click="cartStore.openDrawer = false">
                            <BaseButton variant="outline" full-width size="sm" v-tooltip="'View full shopping bag details &amp; notes'">
                                View Full Cart Page
                            </BaseButton>
                        </RouterLink>
                    </div>
                </div>

            </div>
        </Transition>
    </Teleport>

    <!-- Bulk Edit Modal for Cart Drawer -->
    <BulkEditCartModal
        :is-open="showBulkEditModal"
        :selected-items="selectedItemsList"
        @close="showBulkEditModal = false"
        @saved="selectedItemIds = []"
    />

    <!-- Confirmation Removal Modal -->
    <ConfirmRemoveModal
        :is-open="showConfirmModal"
        :item-to-remove="pendingRemoveItem"
        :is-bulk-delete="isBulkRemoveAction"
        :bulk-count="selectedItemIds.length"
        :loading="deleting"
        @cancel="showConfirmModal = false"
        @confirm="confirmRemoveAction"
    />
</template>

<script setup>
import { ref, computed } from 'vue'
import { useCartStore } from '@/stores/cart'
import { useProductModalStore } from '@/stores/productModal'
import { useToast } from '@/composables/useToast'
import BaseButton from '@/components/ui/BaseButton.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import BulkEditCartModal from '@/components/storefront/BulkEditCartModal.vue'
import ConfirmRemoveModal from '@/components/storefront/ConfirmRemoveModal.vue'

const cartStore = useCartStore()
const productModal = useProductModalStore()
const toast = useToast()

const drawerCouponCode = ref('')
const applyingDrawerCoupon = ref(false)
const selectedItemIds = ref([])
const showBulkEditModal = ref(false)

const showConfirmModal = ref(false)
const pendingRemoveItem = ref(null)
const isBulkRemoveAction = ref(false)
const deleting = ref(false)

const isAllSelected = computed(() => {
    return cartStore.items.length > 0 && selectedItemIds.value.length === cartStore.items.length
})

const selectedItemsList = computed(() => {
    return cartStore.items.filter(i => selectedItemIds.value.includes(i.id))
})

function toggleSelectAll() {
    if (isAllSelected.value) {
        selectedItemIds.value = []
    } else {
        selectedItemIds.value = cartStore.items.map(i => i.id)
    }
}

function openBulkEdit() {
    if (selectedItemIds.value.length === 0) return
    cartStore.openDrawer = false
    showBulkEditModal.value = true
}

function promptRemoveSingle(item) {
    pendingRemoveItem.value = item
    isBulkRemoveAction.value = false
    showConfirmModal.value = true
}

function promptRemoveBulk() {
    if (selectedItemIds.value.length === 0) return
    pendingRemoveItem.value = null
    isBulkRemoveAction.value = true
    showConfirmModal.value = true
}

async function confirmRemoveAction() {
    deleting.value = true
    if (isBulkRemoveAction.value) {
        const operations = selectedItemIds.value.map(id => ({ type: 'remove', item_id: id }))
        const count = selectedItemIds.value.length
        const res = await cartStore.batch(operations)
        selectedItemIds.value = []
        deleting.value = false
        showConfirmModal.value = false
        if (res.success) {
            toast.success(`Removed ${count} item${count > 1 ? 's' : ''} from your basket!`, 'Bulk Delete Complete')
        }
    } else if (pendingRemoveItem.value) {
        const item = pendingRemoveItem.value
        await cartStore.removeItem(item.id)
        deleting.value = false
        showConfirmModal.value = false
        pendingRemoveItem.value = null
    }
}

function handleEditItem(item) {
    cartStore.openDrawer = false
    productModal.openModalForEdit(item)
}

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

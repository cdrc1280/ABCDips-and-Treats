<template>
  <Teleport to="body">
    <!-- Backdrop -->
    <Transition name="fade">
      <div
        v-if="cartStore.openDrawer"
        class="fixed inset-0 z-[80] bg-[#1C1410]/50 backdrop-blur-xs"
        @click="cartStore.openDrawer = false"
      />
    </Transition>

    <!-- Slide-over Drawer -->
    <Transition name="slide">
      <div
        v-if="cartStore.openDrawer"
        class="fixed inset-y-0 right-0 z-[90] w-full max-w-md bg-white shadow-2xl flex flex-col justify-between border-l border-[#C08E5D]/20"
      >
        <!-- Header -->
        <div class="p-6 border-b border-[#C08E5D]/20 flex items-center justify-between bg-[#FBF3E7]">
          <div>
            <h2 class="text-xl font-bold text-[#1C1410]">Your Pastry Basket</h2>
            <p class="text-xs text-[#8C7A68]">{{ cartStore.itemCount }} {{ cartStore.itemCount === 1 ? 'item' : 'items' }}</p>
          </div>
          <button
            class="p-2 rounded-xl text-[#8C7A68] hover:text-[#5C3A22] hover:bg-[#D9A876]/20 transition-all"
            @click="cartStore.openDrawer = false"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <!-- Items List -->
        <div class="flex-1 overflow-y-auto p-6 space-y-4">
          <div v-if="cartStore.items.length === 0" class="py-12 text-center">
            <EmptyState
              title="Your Basket is Empty"
              description="Add some fresh banana bread, cookies, or brownies to get started."
            >
              <template #action>
                <RouterLink to="/shop" @click="cartStore.openDrawer = false">
                  <BaseButton variant="primary">Browse Menu</BaseButton>
                </RouterLink>
              </template>
            </EmptyState>
          </div>

          <div
            v-for="item in cartStore.items"
            :key="item.id"
            class="flex items-center gap-4 py-3 border-b border-[#C08E5D]/15"
          >
            <img
              :src="item.image_url || '/images/placeholder-bakery.png'"
              :alt="item.name"
              class="w-16 h-16 rounded-xl object-cover border border-[#C08E5D]/20 flex-shrink-0"
            />
            <div class="flex-1 min-w-0">
              <h4 class="font-bold text-sm text-[#1C1410] truncate">{{ item.name }}</h4>
              <div class="text-xs text-[#8C7A68] mt-0.5">₱{{ item.unit_price.toFixed(2) }} each</div>

              <!-- Quantity selector -->
              <div class="flex items-center gap-2 mt-2">
                <button
                  class="w-6 h-6 rounded bg-[#FBF3E7] text-[#5C3A22] font-bold text-xs flex items-center justify-center hover:bg-[#D9A876]/30"
                  @click="cartStore.updateItem(item.id, item.qty - 1)"
                >
                  -
                </button>
                <span class="text-xs font-bold w-6 text-center text-[#1C1410]">{{ item.qty }}</span>
                <button
                  class="w-6 h-6 rounded bg-[#FBF3E7] text-[#5C3A22] font-bold text-xs flex items-center justify-center hover:bg-[#D9A876]/30"
                  @click="cartStore.updateItem(item.id, item.qty + 1)"
                >
                  +
                </button>
              </div>
            </div>

            <div class="text-right flex-shrink-0">
              <div class="font-extrabold text-sm text-[#5C3A22]">₱{{ item.subtotal.toFixed(2) }}</div>
              <button
                class="text-xs text-[#B84C3C] hover:underline mt-1 block"
                @click="cartStore.removeItem(item.id)"
              >
                Remove
              </button>
            </div>
          </div>
        </div>

        <!-- Footer Summary -->
        <div v-if="cartStore.items.length > 0" class="p-6 border-t border-[#C08E5D]/20 bg-[#FBF3E7] space-y-4">
          <div class="space-y-1 text-sm">
            <div class="flex justify-between text-[#8C7A68]">
              <span>Subtotal</span>
              <span>₱{{ cartStore.subtotal.toFixed(2) }}</span>
            </div>
            <div v-if="cartStore.discount > 0" class="flex justify-between text-[#6B8F5E]">
              <span>Discount</span>
              <span>-₱{{ cartStore.discount.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-base font-extrabold text-[#5C3A22] pt-2 border-t border-[#C08E5D]/20">
              <span>Estimated Total</span>
              <span>₱{{ cartStore.total.toFixed(2) }}</span>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <RouterLink to="/cart" @click="cartStore.openDrawer = false">
              <BaseButton variant="outline" full-width>View Cart</BaseButton>
            </RouterLink>
            <RouterLink to="/checkout" @click="cartStore.openDrawer = false">
              <BaseButton variant="primary" full-width>Checkout</BaseButton>
            </RouterLink>
          </div>
        </div>

      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { useCartStore } from '@/stores/cart'
import BaseButton from '@/components/ui/BaseButton.vue'
import EmptyState from '@/components/ui/EmptyState.vue'

const cartStore = useCartStore()
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.slide-enter-active, .slide-leave-active { transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
.slide-enter-from, .slide-leave-to { transform: translateX(100%); }
</style>

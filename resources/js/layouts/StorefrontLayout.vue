<template>
  <div class="min-h-screen bg-surface flex flex-col relative selection:bg-[#D9A876]/30">
    <!-- Navigation Header (STATIONARY - NEVER RELOADS) -->
    <StorefrontNav />

    <!-- Dynamic Main Page Viewport with Silky Smooth Crossfade & Subtle Vertical Glide -->
    <main class="flex-1 relative z-10">
      <RouterView v-slot="{ Component, route }">
        <Transition name="page-smooth" mode="out-in" appear>
          <component :is="Component" :key="route.name || route.path" />
        </Transition>
      </RouterView>
    </main>

    <!-- Footer (STATIONARY - NEVER RELOADS) -->
    <StorefrontFooter />

    <!-- Global Persistent Modals & Drawers -->
    <CartDrawer />
    <ShopeeProductModal />
    <ToastContainer />
    <AiChatWidget />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import StorefrontNav from '@/components/storefront/StorefrontNav.vue'
import StorefrontFooter from '@/components/storefront/StorefrontFooter.vue'
import CartDrawer from '@/components/storefront/CartDrawer.vue'
import ShopeeProductModal from '@/components/storefront/ShopeeProductModal.vue'
import ToastContainer from '@/components/ui/ToastContainer.vue'
import AiChatWidget from '@/components/storefront/AiChatWidget.vue'
import { useCartStore } from '@/stores/cart'

const cartStore = useCartStore()

onMounted(() => {
  cartStore.fetchCart()
})
</script>

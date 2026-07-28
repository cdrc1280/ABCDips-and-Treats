<template>
  <div class="min-h-screen bg-[#FBF3E7] flex flex-col relative">
    <!-- Navigation Header -->
    <StorefrontNav />

    <!-- Page Content -->
    <main class="flex-1 relative z-10">
      <RouterView v-slot="{ Component }">
        <Transition
          mode="out-in"
          enter-active-class="transition-opacity duration-200 ease-out"
          enter-from-class="opacity-0"
          enter-to-class="opacity-100"
          leave-active-class="transition-opacity duration-150 ease-in"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <component :is="Component" />
        </Transition>
      </RouterView>
    </main>

    <!-- Footer -->
    <StorefrontFooter />

    <!-- Cart Drawer -->
    <CartDrawer />

    <!-- Toast Notifications -->
    <ToastContainer />

    <!-- Customer AI Chat Widget -->
    <AiChatWidget />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import StorefrontNav from '@/components/storefront/StorefrontNav.vue'
import StorefrontFooter from '@/components/storefront/StorefrontFooter.vue'
import CartDrawer from '@/components/storefront/CartDrawer.vue'
import ToastContainer from '@/components/ui/ToastContainer.vue'
import AiChatWidget from '@/components/storefront/AiChatWidget.vue'
import { useCartStore } from '@/stores/cart'

const cartStore = useCartStore()
onMounted(() => cartStore.fetchCart())
</script>

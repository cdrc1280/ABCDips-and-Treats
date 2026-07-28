<template>
  <div class="min-h-screen bg-[#FBF3E7] flex flex-col relative">
    <!-- Header Nav -->
    <StorefrontNav />

    <!-- Main Account Area -->
    <main class="flex-1 page-container py-8 md:py-12 relative z-10">

      <!-- Back Button & Account Navigation Bar -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8 bg-white p-4 sm:p-5 rounded-2xl border border-[#C08E5D]/20 shadow-sm">
        
        <!-- Back to Store Button -->
        <RouterLink
          to="/"
          class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-[#5C3A22] bg-[#D9A876]/20 hover:bg-[#D9A876]/35 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
          Back to Store
        </RouterLink>

        <!-- Account Tab Links -->
        <div class="flex items-center gap-1.5 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0">
          <RouterLink
            v-for="tab in accountTabs"
            :key="tab.to"
            :to="tab.to"
            :class="[
              'px-3.5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-1.5',
              route.path === tab.to
                ? 'bg-[#5C3A22] text-[#FBF3E7] shadow-sm'
                : 'text-[#1C1410] hover:bg-[#D9A876]/20 hover:text-[#5C3A22]'
            ]"
          >
            <span v-html="tab.icon" />
            {{ tab.label }}
          </RouterLink>
        </div>
      </div>

      <!-- Account Page View -->
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

    <!-- Footer & Overlays -->
    <StorefrontFooter />
    <CartDrawer />
    <ToastContainer />
    <AiChatWidget />
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router'
import StorefrontNav from '@/components/storefront/StorefrontNav.vue'
import StorefrontFooter from '@/components/storefront/StorefrontFooter.vue'
import CartDrawer from '@/components/storefront/CartDrawer.vue'
import ToastContainer from '@/components/ui/ToastContainer.vue'
import AiChatWidget from '@/components/storefront/AiChatWidget.vue'

const route = useRoute()

const accountTabs = [
  { to: '/account/orders', label: 'My Orders', icon: '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>' },
  { to: '/account/wishlist', label: 'My Wishlist', icon: '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>' },
  { to: '/account/profile', label: 'My Profile', icon: '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>' },
]
</script>

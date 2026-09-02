<template>
  <div class="min-h-screen bg-surface dark:bg-[#140D09] text-ink dark:text-[#FBF3E7] flex flex-col relative">
    <!-- Header Nav -->
    <StorefrontNav />

    <!-- Main Account Area -->
    <main class="flex-1 page-container py-8 md:py-12 relative z-10">

      <!-- Back Button & Account Navigation Bar -->
      <div
        class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8 bg-white dark:bg-[#1E1510] p-4 sm:p-5 rounded-2xl border border-brand-caramel/20 dark:border-[#C08E5D]/20 shadow-sm">

        <!-- Back to Store Button -->
        <RouterLink to="/"
          class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-brand-choco dark:text-[#E2C08A] bg-brand-tan/20 hover:bg-brand-tan/35 transition-colors">
          <ArrowLeft class="w-4 h-4" />
          <span>Back to Store</span>
        </RouterLink>

        <!-- Account Tab Links -->
        <div class="flex items-center gap-1.5 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0">
          <RouterLink v-for="tab in accountTabs" :key="tab.to" :to="tab.to" :class="[
            'px-3.5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-2',
            route.path === tab.to
              ? 'bg-brand-choco text-surface dark:bg-[#C08E5D] dark:text-[#1C1410] shadow-sm'
              : 'text-ink dark:text-[#FBF3E7] hover:bg-brand-tan/20 hover:text-brand-choco dark:hover:text-[#E2C08A]'
          ]">
            <component :is="tab.icon" class="w-3.5 h-3.5" />
            <span>{{ tab.label }}</span>
          </RouterLink>
        </div>
      </div>

      <!-- Account Page View -->
      <RouterView v-slot="{ Component }">
        <Transition mode="out-in" enter-active-class="transition-opacity duration-200 ease-out"
          enter-from-class="opacity-0" enter-to-class="opacity-100"
          leave-active-class="transition-opacity duration-150 ease-in" leave-from-class="opacity-100"
          leave-to-class="opacity-0">
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
import { ArrowLeft, Package, Heart, User } from 'lucide-vue-next'
import StorefrontNav from '@/components/storefront/StorefrontNav.vue'
import StorefrontFooter from '@/components/storefront/StorefrontFooter.vue'
import CartDrawer from '@/components/storefront/CartDrawer.vue'
import ToastContainer from '@/components/ui/ToastContainer.vue'
import AiChatWidget from '@/components/storefront/AiChatWidget.vue'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const authStore = useAuthStore()

const accountTabs = [
  { to: '/account/orders', label: 'My Orders', icon: Package },
  { to: '/account/wishlist', label: 'My Wishlist', icon: Heart },
  { to: '/account/profile', label: 'My Profile', icon: User },
]
</script>

<template>
  <div v-if="isVisible" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-12">
    <div class="bg-gradient-to-r from-[#2A1C13] to-brand-choco rounded-3xl p-6 sm:p-8 lg:p-10 shadow-2xl border border-brand-caramel/30 relative overflow-hidden">
      <button @click="dismiss" v-tooltip="'Dismiss account perks banner'" class="absolute top-4 right-4 text-surface/60 hover:text-white transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <div class="text-center mb-8">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white mb-2">Join Our Sweet Community</h2>
        <p class="text-brand-tan text-sm sm:text-base max-w-2xl mx-auto">Create a free account to unlock exclusive perks and make your ABCDips & Treats experience even better.</p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-5 border border-white/10 text-center">
          <div class="text-3xl mb-3">📦</div>
          <h3 class="text-white font-bold text-sm mb-1">Order Tracking</h3>
          <p class="text-surface/70 text-xs">Real-time delivery tracking</p>
        </div>
        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-5 border border-white/10 text-center">
          <div class="text-3xl mb-3">🎂</div>
          <h3 class="text-white font-bold text-sm mb-1">Birthday Discount</h3>
          <p class="text-surface/70 text-xs">Special treat on your birthday month</p>
        </div>
        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-5 border border-white/10 text-center">
          <div class="text-3xl mb-3">🏅</div>
          <h3 class="text-white font-bold text-sm mb-1">Loyalty Rewards</h3>
          <p class="text-surface/70 text-xs">Earn points with every purchase</p>
        </div>
        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-5 border border-white/10 text-center">
          <div class="text-3xl mb-3">🏷️</div>
          <h3 class="text-white font-bold text-sm mb-1">Members-Only Deals</h3>
          <p class="text-surface/70 text-xs">Exclusive flash sales</p>
        </div>
      </div>

      <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
        <RouterLink to="/auth/register" class="w-full sm:w-auto">
          <button v-tooltip="'Sign up for discounts, rewards & order tracking'" class="w-full bg-brand-tan text-ink px-8 py-3.5 rounded-2xl font-bold text-sm hover:bg-brand-caramel transition-colors shadow-md">
            Create Free Account
          </button>
        </RouterLink>
        <RouterLink to="/auth/login" class="w-full sm:w-auto">
          <button v-tooltip="'Sign in to your existing account'" class="w-full bg-transparent border border-brand-tan/60 text-white px-8 py-3.5 rounded-2xl font-semibold text-sm hover:bg-brand-tan/20 transition-colors">
            Log In
          </button>
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const isVisible = ref(false)

onMounted(() => {
  const dismissed = localStorage.getItem('abcdips_perks_dismissed')
  if (!dismissed && !authStore.isAuthenticated) {
    isVisible.value = true
  }
})

function dismiss() {
  localStorage.setItem('abcdips_perks_dismissed', 'true')
  isVisible.value = false
}
</script>

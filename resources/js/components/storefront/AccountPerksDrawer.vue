<template>
  <div v-if="isVisible" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-12">
    <div class="relative bg-gradient-to-r from-[#2A1C13] via-[#1C1410] to-[#2D1B10] rounded-3xl p-6 sm:p-8 lg:p-10 shadow-2xl border border-[#C08E5D]/30 overflow-hidden">
      <button @click="dismiss" v-tooltip="'Dismiss account perks banner'" class="absolute top-4 right-4 text-surface/60 hover:text-white transition-colors p-1 rounded-lg hover:bg-white/10">
        <X class="w-5 h-5" />
      </button>

      <div class="text-center mb-8">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#D9A876]/15 border border-[#C08E5D]/30 text-xs font-bold text-[#E2C08A] mb-2 uppercase tracking-wider">
          <Sparkles class="w-3.5 h-3.5" />
          <span>Exclusive Member Privileges</span>
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-[#FBF3E7] mb-2">Join Our Sweet Community</h2>
        <p class="text-[#E2C08A]/80 text-sm sm:text-base max-w-2xl mx-auto">Create a free account to unlock exclusive discounts, save delivery addresses, and enjoy sweet rewards.</p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white/5 backdrop-blur-md rounded-2xl p-5 border border-[#C08E5D]/20 text-center hover:bg-white/10 transition-colors">
          <div class="w-12 h-12 rounded-2xl bg-[#D9A876]/20 mx-auto mb-3 flex items-center justify-center text-[#E2C08A] border border-[#C08E5D]/30">
            <Truck class="w-6 h-6" />
          </div>
          <h3 class="text-[#FBF3E7] font-bold text-sm mb-1">Order Tracking</h3>
          <p class="text-[#E2C08A]/70 text-xs">Real-time live kitchen &amp; delivery tracking</p>
        </div>
        <div class="bg-white/5 backdrop-blur-md rounded-2xl p-5 border border-[#C08E5D]/20 text-center hover:bg-white/10 transition-colors">
          <div class="w-12 h-12 rounded-2xl bg-[#D9A876]/20 mx-auto mb-3 flex items-center justify-center text-[#E2C08A] border border-[#C08E5D]/30">
            <Gift class="w-6 h-6" />
          </div>
          <h3 class="text-[#FBF3E7] font-bold text-sm mb-1">Birthday Discount</h3>
          <p class="text-[#E2C08A]/70 text-xs">Special celebration treat on your birthday month</p>
        </div>
        <div class="bg-white/5 backdrop-blur-md rounded-2xl p-5 border border-[#C08E5D]/20 text-center hover:bg-white/10 transition-colors">
          <div class="w-12 h-12 rounded-2xl bg-[#D9A876]/20 mx-auto mb-3 flex items-center justify-center text-[#E2C08A] border border-[#C08E5D]/30">
            <Award class="w-6 h-6" />
          </div>
          <h3 class="text-[#FBF3E7] font-bold text-sm mb-1">Loyalty Rewards</h3>
          <p class="text-[#E2C08A]/70 text-xs">Earn points and redeem rewards with every purchase</p>
        </div>
        <div class="bg-white/5 backdrop-blur-md rounded-2xl p-5 border border-[#C08E5D]/20 text-center hover:bg-white/10 transition-colors">
          <div class="w-12 h-12 rounded-2xl bg-[#D9A876]/20 mx-auto mb-3 flex items-center justify-center text-[#E2C08A] border border-[#C08E5D]/30">
            <Tag class="w-6 h-6" />
          </div>
          <h3 class="text-[#FBF3E7] font-bold text-sm mb-1">Members-Only Deals</h3>
          <p class="text-[#E2C08A]/70 text-xs">Early access to seasonal pastries and flash sales</p>
        </div>
      </div>

      <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
        <RouterLink to="/auth/register" class="w-full sm:w-auto">
          <button v-tooltip="'Sign up for discounts, rewards & order tracking'" class="w-full bg-gradient-to-r from-[#D9A876] to-[#C08E5D] text-[#1C1410] px-8 py-3.5 rounded-2xl font-extrabold text-sm hover:opacity-95 transition-opacity shadow-md">
            Create Free Account
          </button>
        </RouterLink>
        <RouterLink to="/auth/login" class="w-full sm:w-auto">
          <button v-tooltip="'Sign in to your existing account'" class="w-full bg-white/5 border border-[#C08E5D]/50 text-[#FBF3E7] px-8 py-3.5 rounded-2xl font-bold text-sm hover:bg-white/10 transition-colors">
            Log In
          </button>
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { X, Sparkles, Truck, Gift, Award, Tag } from 'lucide-vue-next'
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

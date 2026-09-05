<template>
  <div class="min-h-[70vh] flex items-center justify-center px-4 sm:px-6 lg:px-8 py-16">
    <div class="max-w-2xl w-full text-center space-y-8 bg-white dark:bg-[#1E1510] rounded-3xl p-8 sm:p-12 border border-[#C08E5D]/30 shadow-2xl relative overflow-hidden">
      
      <!-- Subtle Ambient Glow -->
      <div class="absolute -top-24 -right-24 w-60 h-60 bg-[#D9A876]/10 rounded-full blur-3xl pointer-events-none" />

      <!-- Status Pill -->
      <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#F5E8D0] dark:bg-[#2A1C13] border border-[#C08E5D]/30 text-xs font-bold text-[#5C3A22] dark:text-[#E2C08A] shadow-xs">
        <AlertCircle class="w-4 h-4 text-[#C08E5D]" />
        <span>404 — PAGE NOT FOUND</span>
      </div>

      <!-- Headline -->
      <div class="space-y-3">
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#1C1410] dark:text-[#FBF3E7] tracking-tight leading-tight">
          Oops! This Pastry Page <br class="hidden sm:inline" />
          <span class="font-['Caveat'] text-[#C08E5D] dark:text-[#E2C08A] text-4xl sm:text-5xl font-normal">doesn't exist or was moved</span>
        </h1>
        <p class="text-sm sm:text-base text-[#8C7A68] dark:text-[#C5B4A4] max-w-lg mx-auto leading-relaxed font-medium">
          The link you followed might be broken or currently being fresh-baked in our Cavite ovens. Plenty of sweet treats are waiting for you!
        </p>
      </div>

      <!-- Action Buttons -->
      <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
        <RouterLink to="/" class="w-full sm:w-auto">
          <button v-tooltip="'Return to ABCDips homepage'" class="w-full bg-[#5C3A22] dark:bg-[#C08E5D] text-[#FBF3E7] dark:text-[#1C1410] px-7 py-3.5 rounded-2xl font-bold text-sm hover:bg-[#4A2D1A] transition-all shadow-md flex items-center justify-center gap-2">
            <Home class="w-4 h-4" />
            <span>Return to Homepage</span>
          </button>
        </RouterLink>
        <RouterLink to="/shop" class="w-full sm:w-auto">
          <button v-tooltip="'Explore our full bakery menu'" class="w-full bg-[#F5E8D0] dark:bg-[#2A1C13] border border-[#C08E5D]/40 text-[#5C3A22] dark:text-[#E2C08A] px-7 py-3.5 rounded-2xl font-bold text-sm hover:bg-[#E2C08A]/30 transition-all flex items-center justify-center gap-2">
            <ShoppingBag class="w-4 h-4" />
            <span>Browse Full Bakery Shop</span>
          </button>
        </RouterLink>
      </div>

      <!-- Categories Quick Access -->
      <div class="pt-6 border-t border-[#C08E5D]/20 space-y-3">
        <p class="text-xs font-bold uppercase tracking-wider text-[#8C7A68] dark:text-[#C5B4A4]">Or explore popular categories:</p>
        <div class="flex flex-wrap items-center justify-center gap-2">
          <RouterLink to="/shop?category=banana-breads" class="text-xs bg-[#F5E8D0]/50 dark:bg-[#2A1C13] hover:bg-[#C08E5D]/20 text-[#5C3A22] dark:text-[#E2C08A] font-semibold px-3.5 py-1.5 rounded-xl border border-[#C08E5D]/20 transition-all">
            Banana Breads
          </RouterLink>
          <RouterLink to="/shop?category=cheesecakes" class="text-xs bg-[#F5E8D0]/50 dark:bg-[#2A1C13] hover:bg-[#C08E5D]/20 text-[#5C3A22] dark:text-[#E2C08A] font-semibold px-3.5 py-1.5 rounded-xl border border-[#C08E5D]/20 transition-all">
            Cheesecakes
          </RouterLink>
          <RouterLink to="/shop?category=brownies-dips" class="text-xs bg-[#F5E8D0]/50 dark:bg-[#2A1C13] hover:bg-[#C08E5D]/20 text-[#5C3A22] dark:text-[#E2C08A] font-semibold px-3.5 py-1.5 rounded-xl border border-[#C08E5D]/20 transition-all">
            Brownies &amp; Dips
          </RouterLink>
          <RouterLink to="/custom-orders" class="text-xs bg-[#F5E8D0]/50 dark:bg-[#2A1C13] hover:bg-[#C08E5D]/20 text-[#5C3A22] dark:text-[#E2C08A] font-semibold px-3.5 py-1.5 rounded-xl border border-[#C08E5D]/20 transition-all">
            Custom Celebration Cakes
          </RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Home, ShoppingBag, AlertCircle } from 'lucide-vue-next'
</script>

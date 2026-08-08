<template>
  <div class="min-h-[70vh] flex items-center justify-center px-4 sm:px-6 lg:px-8 py-16">
    <div class="max-w-2xl w-full text-center space-y-8 bg-white dark:bg-[#1E1510] rounded-3xl p-8 sm:p-12 border border-[#C08E5D]/30 shadow-2xl relative overflow-hidden">
      <!-- Decorative background accent -->
      <div class="absolute -right-12 -top-12 opacity-5 pointer-events-none text-9xl">🧁</div>
      <div class="absolute -left-12 -bottom-12 opacity-5 pointer-events-none text-9xl">🥖</div>

      <!-- Playful Bakery Badge -->
      <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#F5E8D0] dark:bg-[#2A1C13] border border-[#C08E5D]/30 text-xs font-bold text-[#5C3A22] dark:text-[#E2C08A] shadow-xs">
        <span class="text-base">🥯</span>
        <span>404 — PAGE NOT FOUND</span>
      </div>

      <!-- Main Headline -->
      <div class="space-y-3">
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#1C1410] dark:text-[#FBF3E7] tracking-tight leading-tight">
          Oops! This Pastry Page <br class="hidden sm:inline" />
          <span class="font-['Caveat'] text-[#C08E5D] dark:text-[#E2C08A] text-4xl sm:text-5xl font-normal">doesn't exist or was eaten!</span>
        </h1>
        <p class="text-sm sm:text-base text-[#8C7A68] dark:text-[#C5B4A4] max-w-lg mx-auto leading-relaxed">
          The link you followed might be broken, renamed, or currently being fresh-baked in our Cavite ovens. Don't worry, there are plenty of sweet treats waiting for you!
        </p>
      </div>

      <!-- Quick Action Buttons -->
      <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
        <RouterLink to="/" class="w-full sm:w-auto">
          <button v-tooltip="'Return to ABCDips homepage'" class="w-full bg-[#5C3A22] dark:bg-[#C08E5D] text-[#FBF3E7] dark:text-[#1C1410] px-7 py-3.5 rounded-2xl font-bold text-sm hover:bg-[#4A2D1A] transition-all shadow-md flex items-center justify-center gap-2">
            <span>🏠</span> Return to Homepage
          </button>
        </RouterLink>
        <RouterLink to="/shop" class="w-full sm:w-auto">
          <button v-tooltip="'Explore our full bakery menu'" class="w-full bg-[#F5E8D0] dark:bg-[#2A1C13] border border-[#C08E5D]/40 text-[#5C3A22] dark:text-[#E2C08A] px-7 py-3.5 rounded-2xl font-bold text-sm hover:bg-[#E2C08A]/30 transition-all flex items-center justify-center gap-2">
            <span>🧁</span> Browse Full Bakery Shop
          </button>
        </RouterLink>
      </div>

      <!-- Popular Categories Quick Access -->
      <div class="pt-6 border-t border-[#C08E5D]/20 space-y-3">
        <p class="text-xs font-bold uppercase tracking-wider text-[#8C7A68] dark:text-[#C5B4A4]">Or explore popular categories:</p>
        <div class="flex flex-wrap items-center justify-center gap-2">
          <RouterLink to="/shop" v-tooltip="'Explore all treats'" class="text-xs bg-[#F5E8D0]/50 dark:bg-[#2A1C13] hover:bg-[#C08E5D]/20 text-[#5C3A22] dark:text-[#E2C08A] font-semibold px-3 py-1.5 rounded-xl border border-[#C08E5D]/20 transition-all">
            🍌 Banana Breads
          </RouterLink>
          <RouterLink to="/shop" v-tooltip="'Explore cheesecakes'" class="text-xs bg-[#F5E8D0]/50 dark:bg-[#2A1C13] hover:bg-[#C08E5D]/20 text-[#5C3A22] dark:text-[#E2C08A] font-semibold px-3 py-1.5 rounded-xl border border-[#C08E5D]/20 transition-all">
            🍰 Cheesecakes
          </RouterLink>
          <RouterLink to="/shop" v-tooltip="'Explore brownies'" class="text-xs bg-[#F5E8D0]/50 dark:bg-[#2A1C13] hover:bg-[#C08E5D]/20 text-[#5C3A22] dark:text-[#E2C08A] font-semibold px-3 py-1.5 rounded-xl border border-[#C08E5D]/20 transition-all">
            🍫 Brownies & Dips
          </RouterLink>
          <RouterLink to="/custom-orders" v-tooltip="'Order custom cake'" class="text-xs bg-[#F5E8D0]/50 dark:bg-[#2A1C13] hover:bg-[#C08E5D]/20 text-[#5C3A22] dark:text-[#E2C08A] font-semibold px-3 py-1.5 rounded-xl border border-[#C08E5D]/20 transition-all">
            🎂 Custom Cakes
          </RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Bakery Fallback 404 Page
</script>

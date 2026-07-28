<template>
  <div class="min-h-screen bg-surface flex flex-col items-center justify-center px-4">
    <!-- Logo -->
    <RouterLink to="/" class="mb-8 block">
      <img src="/images/logo.png" alt="ABCDips & Treats" class="h-20 w-auto" />
    </RouterLink>

    <!-- Auth Card -->
    <div class="w-full max-w-md bg-white rounded-2xl p-8"
         style="box-shadow: var(--shadow-lg);">
      <RouterView />
    </div>

    <!-- Back to shop -->
    <RouterLink to="/"
      class="mt-6 text-sm text-warm-gray hover:text-brand-choco transition-colors duration-200 flex items-center gap-1.5">
      ← Back to shop
    </RouterLink>
  </div>
</template>

<script setup>
import ToastContainer from '@/components/ui/ToastContainer.vue'
</script>

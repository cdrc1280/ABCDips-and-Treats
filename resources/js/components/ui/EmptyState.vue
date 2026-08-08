<template>
  <div class="py-16 px-4 text-center flex flex-col items-center justify-center max-w-md mx-auto">
    <!-- Cookie doodle hand-drawn line-art SVG motif -->
    <div class="w-24 h-24 rounded-full bg-brand-tan/20 flex items-center justify-center mb-6 text-brand-caramel">
      <slot name="icon">
        <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 48 48">
          <!-- Cookie outline with bite -->
          <path stroke-linecap="round" stroke-linejoin="round" d="M24 6C14.059 6 6 14.059 6 24c0 9.941 8.059 18 18 18 9.941 0 18-8.059 18-18 0-1.87-.285-3.673-.815-5.367a7.5 7.5 0 01-5.818-5.818A17.915 17.915 0 0024 6z" />
          <!-- Chocolate chips / dots -->
          <circle cx="18" cy="18" r="2" fill="currentColor" opacity="0.6" />
          <circle cx="28" cy="16" r="1.5" fill="currentColor" opacity="0.6" />
          <circle cx="30" cy="28" r="2" fill="currentColor" opacity="0.6" />
          <circle cx="17" cy="30" r="1.5" fill="currentColor" opacity="0.6" />
          <circle cx="24" cy="34" r="1.5" fill="currentColor" opacity="0.6" />
        </svg>
      </slot>
    </div>

    <h3 class="text-xl font-bold text-ink mb-2">{{ title }}</h3>
    <p class="text-sm text-warm-gray mb-6 leading-relaxed">{{ description }}</p>

    <div v-if="$slots.action" class="flex justify-center">
      <slot name="action" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: { type: String, default: 'Nothing here yet' },
  description: { type: String, default: 'We could not find any items matching your request.' }
})
</script>

<template>
  <div class="mb-8 md:mb-12">
    <p v-if="tagline" class="script-accent text-brand-caramel text-lg md:text-xl mb-1">
      {{ tagline }}
    </p>
    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-ink tracking-tight mb-3">
      {{ title }}
    </h1>
    <p v-if="subtitle" class="text-warm-gray text-base md:text-lg max-w-2xl">
      {{ subtitle }}
    </p>
  </div>
</template>

<script setup>
defineProps({
  title: { type: String, required: true },
  tagline: { type: String, default: '' },
  subtitle: { type: String, default: '' }
})
</script>

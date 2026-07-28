<template>
  <div class="space-y-2 w-full">
    <div
      v-for="i in lines"
      :key="i"
      class="skeleton"
      :style="{
        height: height,
        width: i === lines && lines > 1 ? lastLineWidth : '100%',
        borderRadius: '0.375rem'
      }"
    />
  </div>
</template>

<script setup>
defineProps({
  lines: { type: Number, default: 3 },
  height: { type: String, default: '0.875rem' },
  lastLineWidth: { type: String, default: '70%' }
})
</script>

<template>
  <div
    class="skeleton"
    :style="{
      width: width,
      height: height,
      borderRadius: radius
    }"
  />
</template>

<script setup>
defineProps({
  width: { type: String, default: '100%' },
  height: { type: String, default: '1rem' },
  radius: { type: String, default: '0.75rem' }
})
</script>

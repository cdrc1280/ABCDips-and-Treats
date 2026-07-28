<template>
  <span
    :class="[
      'inline-flex items-center gap-1 font-semibold rounded-full select-none transition-colors duration-150',
      sizeClasses,
      variantClasses
    ]"
  >
    <span v-if="dot" class="w-1.5 h-1.5 rounded-full bg-current" />
    <slot />
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'neutral', // neutral, brand, success, warning, error, outline
    validator: (v) => ['neutral', 'brand', 'success', 'warning', 'error', 'outline'].includes(v)
  },
  size: {
    type: String,
    default: 'md', // sm, md
    validator: (v) => ['sm', 'md'].includes(v)
  },
  dot: { type: Boolean, default: false }
})

const sizeClasses = computed(() => {
  return props.size === 'sm' ? 'px-2 py-0.5 text-[10px]' : 'px-2.5 py-1 text-xs'
})

const variantClasses = computed(() => {
  switch (props.variant) {
    case 'brand':
      return 'bg-[#5C3A22] text-[#FBF3E7]'
    case 'success':
      return 'bg-[#6B8F5E]/15 text-[#37522D]'
    case 'warning':
      return 'bg-[#C98A3A]/15 text-[#6E4612]'
    case 'error':
      return 'bg-[#B84C3C]/15 text-[#73261C]'
    case 'outline':
      return 'border border-[#C08E5D]/40 text-[#5C3A22] bg-transparent'
    case 'neutral':
    default:
      return 'bg-[#D9A876]/25 text-[#5C3A22]'
  }
})
</script>

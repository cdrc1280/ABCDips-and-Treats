<template>
  <span
    :class="[
      'inline-flex items-center gap-1 font-extrabold rounded-full select-none transition-all duration-300 transform hover:scale-105 shadow-xs',
      sizeClasses,
      variantClasses
    ]"
  >
    <span v-if="dot" class="w-1.5 h-1.5 rounded-full bg-white animate-pulse" />
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
  return props.size === 'sm' ? 'px-2.5 py-0.5 text-[11px]' : 'px-3 py-1 text-xs'
})

const variantClasses = computed(() => {
  switch (props.variant) {
    case 'brand':
      return 'bg-gradient-to-r from-brand-choco to-[#8C522B] text-white shadow-md shadow-brand-choco/25 tracking-wide ring-1 ring-white/20'
    case 'success':
      return 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-500/25 tracking-wide ring-1 ring-white/20'
    case 'warning':
      return 'bg-gradient-to-r from-amber-500 to-yellow-600 text-white shadow-md shadow-amber-500/25 tracking-wide ring-1 ring-white/20'
    case 'error':
      return 'bg-gradient-to-r from-red-600 to-rose-600 text-white shadow-md shadow-red-500/25 tracking-wide ring-1 ring-white/20'
    case 'outline':
      return 'border-2 border-brand-choco text-brand-choco dark:border-surface-400 dark:text-surface-400 bg-transparent font-bold'
    case 'neutral':
    default:
      return 'bg-gradient-to-r from-warm-gray to-brand-choco text-white shadow-md tracking-wide'
  }
})
</script>

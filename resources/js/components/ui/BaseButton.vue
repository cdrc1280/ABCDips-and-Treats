<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="[
      'inline-flex items-center justify-center font-semibold rounded-xl transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed select-none',
      sizeClasses,
      variantClasses,
      fullWidth ? 'w-full' : ''
    ]"
    @click="$emit('click', $event)"
  >
    <svg
      v-if="loading"
      class="animate-spin -ml-1 mr-2 h-4 w-4 text-current"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle
        class="opacity-25"
        cx="12"
        cy="12"
        r="10"
        stroke="currentColor"
        stroke-width="4"
      ></circle>
      <path
        class="opacity-75"
        fill="currentColor"
        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
      ></path>
    </svg>
    <slot name="icon-left" />
    <span><slot /></span>
    <slot name="icon-right" />
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  type: { type: String, default: 'button' },
  variant: {
    type: String,
    default: 'primary', // primary, secondary, ghost, danger, outline
    validator: (v) => ['primary', 'secondary', 'ghost', 'danger', 'outline'].includes(v)
  },
  size: {
    type: String,
    default: 'md', // sm, md, lg
    validator: (v) => ['sm', 'md', 'lg'].includes(v)
  },
  disabled: { type: Boolean, default: false },
  loading: { type: Boolean, default: false },
  fullWidth: { type: Boolean, default: false }
})

defineEmits(['click'])

const sizeClasses = computed(() => {
  switch (props.size) {
    case 'sm': return 'px-3 py-1.5 text-xs gap-1.5'
    case 'lg': return 'px-6 py-3 text-base gap-2.5'
    default:   return 'px-4 py-2.5 text-sm gap-2'
  }
})

const variantClasses = computed(() => {
  switch (props.variant) {
    case 'secondary':
      return 'bg-[#D9A876] text-[#1C1410] hover:bg-[#C08E5D] hover:text-white focus-visible:ring-[#C08E5D] shadow-sm'
    case 'outline':
      return 'border border-[#C08E5D]/40 text-[#5C3A22] bg-transparent hover:bg-[#D9A876]/20 focus-visible:ring-[#5C3A22]'
    case 'ghost':
      return 'text-[#5C3A22] bg-transparent hover:bg-[#D9A876]/20 focus-visible:ring-[#5C3A22]'
    case 'danger':
      return 'bg-[#B84C3C] text-white hover:bg-[#a03f30] focus-visible:ring-[#B84C3C] shadow-sm'
    case 'primary':
    default:
      return 'bg-[#5C3A22] text-[#FBF3E7] hover:bg-[#4A2D1A] focus-visible:ring-[#5C3A22] shadow-sm hover:shadow-md'
  }
})
</script>

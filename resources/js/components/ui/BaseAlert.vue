<template>
  <div
    :class="[
      'rounded-xl p-4 border flex items-start gap-3 transition-all duration-200',
      variantClasses
    ]"
  >
    <div class="flex-shrink-0 mt-0.5">
      <slot name="icon">
        <svg v-if="variant === 'success'" class="w-5 h-5 text-[#6B8F5E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <svg v-else-if="variant === 'error'" class="w-5 h-5 text-[#B84C3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <svg v-else-if="variant === 'warning'" class="w-5 h-5 text-[#C98A3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
        <svg v-else class="w-5 h-5 text-[#5C3A22]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
      </slot>
    </div>

    <div class="flex-1 text-sm">
      <h4 v-if="title" class="font-bold mb-0.5 text-current">{{ title }}</h4>
      <div class="text-current/90"><slot /></div>
    </div>

    <button
      v-if="dismissible"
      class="flex-shrink-0 text-current/60 hover:text-current p-1 rounded-lg transition-colors"
      @click="$emit('dismiss')"
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'info', // info, success, warning, error
    validator: (v) => ['info', 'success', 'warning', 'error'].includes(v)
  },
  title: { type: String, default: '' },
  dismissible: { type: Boolean, default: false }
})

defineEmits(['dismiss'])

const variantClasses = computed(() => {
  switch (props.variant) {
    case 'success':
      return 'bg-[#6B8F5E]/10 border-[#6B8F5E]/30 text-[#2D4525]'
    case 'error':
      return 'bg-[#B84C3C]/10 border-[#B84C3C]/30 text-[#692117]'
    case 'warning':
      return 'bg-[#C98A3A]/10 border-[#C98A3A]/30 text-[#59390D]'
    case 'info':
    default:
      return 'bg-[#D9A876]/20 border-[#C08E5D]/40 text-[#5C3A22]'
  }
})
</script>

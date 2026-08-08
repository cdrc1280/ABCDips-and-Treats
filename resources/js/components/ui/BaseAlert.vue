<template>
  <div
    :class="[
      'rounded-2xl p-3.5 sm:p-4 border flex items-center gap-3 transition-all duration-300 shadow-xl backdrop-blur-md relative overflow-hidden group w-full',
      containerClasses
    ]"
  >
    <!-- Left Accent Status Indicator Bar -->
    <div :class="['absolute left-0 top-0 bottom-0 w-1.5', accentBarClass]" />

    <!-- Status Icon Bubble -->
    <div :class="['w-8 h-8 sm:w-9 sm:h-9 rounded-xl flex items-center justify-center shrink-0 transition-transform duration-300 group-hover:scale-105 shadow-xs', iconBubbleClass]">
      <slot name="icon">
        <!-- Success Icon -->
        <svg v-if="variant === 'success'" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
        </svg>
        <!-- Error Icon -->
        <svg v-else-if="variant === 'error'" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
        </svg>
        <!-- Warning Icon -->
        <svg v-else-if="variant === 'warning'" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <!-- Info Icon -->
        <svg v-else class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </slot>
    </div>

    <!-- Notification Message Text -->
    <div class="flex-1 min-w-0 pr-1">
      <h4 v-if="title" class="font-extrabold text-xs sm:text-sm text-ink dark:text-[#FBF3E7] tracking-wide mb-0.5 leading-tight">
        {{ title }}
      </h4>
      <div class="text-[11px] sm:text-xs font-medium text-warm-gray dark:text-[#C5B4A4] leading-relaxed break-words">
        <slot />
      </div>
    </div>

    <!-- Dismiss Button -->
    <button
      v-if="dismissible"
      type="button"
      class="shrink-0 text-warm-gray hover:text-ink dark:text-[#C5B4A4] dark:hover:text-white p-1.5 sm:p-2 rounded-xl transition-all duration-200 hover:bg-brand-tan/20 active:scale-95 cursor-pointer touch-manipulation"
      @click="$emit('dismiss')"
      aria-label="Close notification"
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
      </svg>
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

const containerClasses = computed(() => {
  return 'bg-white/95 dark:bg-[#1E1510]/95 border-brand-caramel/25 dark:border-brand-caramel/40 shadow-2xl shadow-brand-choco/10 pl-4 sm:pl-5'
})

const accentBarClass = computed(() => {
  switch (props.variant) {
    case 'success':
      return 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]'
    case 'error':
      return 'bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.5)]'
    case 'warning':
      return 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]'
    case 'info':
    default:
      return 'bg-brand-choco dark:bg-[#C08E5D]'
  }
})

const iconBubbleClass = computed(() => {
  switch (props.variant) {
    case 'success':
      return 'bg-emerald-500/15 text-emerald-600 dark:bg-emerald-500/25 dark:text-emerald-400'
    case 'error':
      return 'bg-rose-500/15 text-rose-600 dark:bg-rose-500/25 dark:text-rose-400'
    case 'warning':
      return 'bg-amber-500/15 text-amber-600 dark:bg-amber-500/25 dark:text-amber-400'
    case 'info':
    default:
      return 'bg-brand-choco/15 text-brand-choco dark:bg-[#C08E5D]/25 dark:text-[#E2C08A]'
  }
})
</script>

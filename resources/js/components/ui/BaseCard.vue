<template>
  <div
    :class="[
      'bg-white dark:bg-[#1E1510] text-ink dark:text-[#FBF3E7] rounded-2xl transition-all duration-200 overflow-hidden border border-brand-caramel/15 dark:border-[#C08E5D]/20',
      hover ? 'hover:shadow-md hover:-translate-y-0.5' : '',
      paddingClasses
    ]"
    :style="cardStyle"
  >
    <div v-if="$slots.header || title" class="border-b border-brand-caramel/15 dark:border-[#C08E5D]/20 pb-4 mb-4 flex items-center justify-between">
      <slot name="header">
        <div>
          <h3 v-if="title" class="text-lg font-bold text-ink dark:text-[#FBF3E7]">{{ title }}</h3>
          <p v-if="subtitle" class="text-xs text-warm-gray dark:text-[#C5B4A4] mt-0.5">{{ subtitle }}</p>
        </div>
      </slot>
      <slot name="header-action" />
    </div>

    <div class="relative">
      <slot />
    </div>

    <div v-if="$slots.footer" class="border-t border-brand-caramel/15 dark:border-[#C08E5D]/20 pt-4 mt-4">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: { type: String, default: '' },
  subtitle: { type: String, default: '' },
  hover: { type: Boolean, default: false },
  padding: {
    type: String,
    default: 'normal', // none, sm, normal, lg
    validator: (v) => ['none', 'sm', 'normal', 'lg'].includes(v)
  },
  shadow: { type: Boolean, default: true }
})

const paddingClasses = computed(() => {
  switch (props.padding) {
    case 'none': return 'p-0'
    case 'sm':   return 'p-4'
    case 'lg':   return 'p-8'
    default:     return 'p-6'
  }
})

const cardStyle = computed(() => {
  return props.shadow ? { boxShadow: 'var(--shadow-md)' } : {}
})
</script>

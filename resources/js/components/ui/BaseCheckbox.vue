<template>
  <label
    :class="[
      'inline-flex items-start gap-2.5 cursor-pointer select-none',
      disabled ? 'opacity-50 cursor-not-allowed' : ''
    ]"
  >
    <div class="relative flex items-center mt-0.5">
      <input
        type="checkbox"
        :checked="modelValue"
        :disabled="disabled"
        class="sr-only peer"
        @change="$emit('update:modelValue', $event.target.checked)"
      />
      <div
        class="w-5 h-5 rounded-md border border-brand-caramel/40 bg-white transition-all duration-200 peer-checked:bg-brand-choco peer-checked:border-brand-choco peer-focus-visible:ring-2 peer-focus-visible:ring-brand-choco/30 flex items-center justify-center text-white"
      >
        <svg
          v-if="modelValue"
          class="w-3.5 h-3.5 stroke-current stroke-[2.5]"
          fill="none"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
      </div>
    </div>

    <span v-if="label || $slots.default" class="text-sm text-ink">
      <slot>{{ label }}</slot>
    </span>
  </label>
</template>

<script setup>
defineProps({
  modelValue: { type: Boolean, default: false },
  label: { type: String, default: '' },
  disabled: { type: Boolean, default: false }
})

defineEmits(['update:modelValue'])
</script>

<template>
  <label
    :class="[
      'inline-flex items-center gap-2.5 cursor-pointer select-none',
      disabled ? 'opacity-50 cursor-not-allowed' : ''
    ]"
  >
    <div class="relative flex items-center">
      <input
        type="radio"
        :name="name"
        :value="value"
        :checked="modelValue === value"
        :disabled="disabled"
        class="sr-only peer"
        @change="$emit('update:modelValue', value)"
      />
      <div
        class="w-5 h-5 rounded-full border border-brand-caramel/40 bg-white transition-all duration-200 peer-checked:border-brand-choco peer-focus-visible:ring-2 peer-focus-visible:ring-brand-choco/30 flex items-center justify-center"
      >
        <div
          v-if="modelValue === value"
          class="w-2.5 h-2.5 rounded-full bg-brand-choco"
        />
      </div>
    </div>

    <span v-if="label || $slots.default" class="text-sm text-ink">
      <slot>{{ label }}</slot>
    </span>
  </label>
</template>

<script setup>
defineProps({
  modelValue: { type: [String, Number, Boolean], default: null },
  value: { type: [String, Number, Boolean], required: true },
  name: { type: String, default: '' },
  label: { type: String, default: '' },
  disabled: { type: Boolean, default: false }
})

defineEmits(['update:modelValue'])
</script>

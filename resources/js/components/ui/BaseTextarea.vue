<template>
  <div class="w-full">
    <label v-if="label" :for="id" class="block text-xs font-semibold uppercase tracking-wider text-brand-choco mb-1.5">
      {{ label }}
      <span v-if="required" class="text-error">*</span>
    </label>

    <textarea
      :id="id"
      :value="modelValue"
      :placeholder="placeholder"
      :rows="rows"
      :disabled="disabled"
      :required="required"
      :class="[
        'w-full rounded-xl bg-white border text-sm text-ink p-3.5 placeholder-warm-gray/60 transition-all duration-200 focus:outline-none focus:ring-2 disabled:bg-surface/50 disabled:text-warm-gray disabled:cursor-not-allowed resize-y',
        error
          ? 'border-error focus:border-error focus:ring-error/20'
          : 'border-brand-caramel/30 hover:border-brand-caramel focus:border-brand-choco focus:ring-brand-choco/20'
      ]"
      @input="$emit('update:modelValue', $event.target.value)"
    ></textarea>

    <p v-if="error" class="mt-1.5 text-xs text-error font-medium">{{ error }}</p>
    <p v-else-if="hint" class="mt-1.5 text-xs text-warm-gray">{{ hint }}</p>
  </div>
</template>

<script setup>
defineProps({
  modelValue: { type: String, default: '' },
  id: { type: String, default: () => `textarea-${Math.random().toString(36).substring(2, 9)}` },
  label: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  rows: { type: [Number, String], default: 4 },
  error: { type: String, default: '' },
  hint: { type: String, default: '' },
  disabled: { type: Boolean, default: false },
  required: { type: Boolean, default: false }
})

defineEmits(['update:modelValue'])
</script>

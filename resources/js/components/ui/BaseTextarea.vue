<template>
  <div class="w-full">
    <label v-if="label" :for="id" class="block text-xs font-semibold uppercase tracking-wider text-brand-choco dark:text-[#E2C08A] mb-1.5">
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
        'w-full rounded-xl bg-white dark:bg-[#1E1510] border text-sm text-ink dark:text-[#FBF3E7] p-3.5 placeholder-warm-gray/60 dark:placeholder-[#C5B4A4]/50 transition-all duration-200 focus:outline-none focus:ring-2 disabled:bg-surface/50 dark:disabled:bg-[#140D09] disabled:text-warm-gray disabled:cursor-not-allowed resize-y',
        error
          ? 'border-error focus:border-error focus:ring-error/20'
          : 'border-brand-caramel/30 dark:border-[#C08E5D]/30 hover:border-brand-caramel dark:hover:border-[#C08E5D] focus:border-brand-choco dark:focus:border-[#E2C08A] focus:ring-brand-choco/20 dark:focus:ring-[#E2C08A]/20'
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

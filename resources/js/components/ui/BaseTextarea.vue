<template>
  <div class="w-full">
    <label v-if="label" :for="id" class="block text-xs font-semibold uppercase tracking-wider text-[#5C3A22] mb-1.5">
      {{ label }}
      <span v-if="required" class="text-[#B84C3C]">*</span>
    </label>

    <textarea
      :id="id"
      :value="modelValue"
      :placeholder="placeholder"
      :rows="rows"
      :disabled="disabled"
      :required="required"
      :class="[
        'w-full rounded-xl bg-white border text-sm text-[#1C1410] p-3.5 placeholder-[#8C7A68]/60 transition-all duration-200 focus:outline-none focus:ring-2 disabled:bg-[#FBF3E7]/50 disabled:text-[#8C7A68] disabled:cursor-not-allowed resize-y',
        error
          ? 'border-[#B84C3C] focus:border-[#B84C3C] focus:ring-[#B84C3C]/20'
          : 'border-[#C08E5D]/30 hover:border-[#C08E5D] focus:border-[#5C3A22] focus:ring-[#5C3A22]/20'
      ]"
      @input="$emit('update:modelValue', $event.target.value)"
    ></textarea>

    <p v-if="error" class="mt-1.5 text-xs text-[#B84C3C] font-medium">{{ error }}</p>
    <p v-else-if="hint" class="mt-1.5 text-xs text-[#8C7A68]">{{ hint }}</p>
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

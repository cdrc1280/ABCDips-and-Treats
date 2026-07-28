<template>
  <div class="w-full">
    <label v-if="label" :for="id" class="block text-xs font-semibold uppercase tracking-wider text-[#5C3A22] mb-1.5">
      {{ label }}
      <span v-if="required" class="text-[#B84C3C]">*</span>
    </label>

    <div class="relative rounded-xl">
      <select
        :id="id"
        :value="modelValue"
        :disabled="disabled"
        :required="required"
        :class="[
          'w-full rounded-xl bg-white border text-sm text-[#1C1410] pl-3.5 pr-10 py-2.5 appearance-none transition-all duration-200 focus:outline-none focus:ring-2 disabled:bg-[#FBF3E7]/50 disabled:text-[#8C7A68] disabled:cursor-not-allowed',
          error
            ? 'border-[#B84C3C] focus:border-[#B84C3C] focus:ring-[#B84C3C]/20'
            : 'border-[#C08E5D]/30 hover:border-[#C08E5D] focus:border-[#5C3A22] focus:ring-[#5C3A22]/20'
        ]"
        @change="$emit('update:modelValue', $event.target.value)"
      >
        <option v-if="placeholder" value="" disabled selected>{{ placeholder }}</option>
        <option
          v-for="option in options"
          :key="getOptionValue(option)"
          :value="getOptionValue(option)"
        >
          {{ getOptionLabel(option) }}
        </option>
      </select>

      <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-[#8C7A68]">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
      </div>
    </div>

    <p v-if="error" class="mt-1.5 text-xs text-[#B84C3C] font-medium">{{ error }}</p>
    <p v-else-if="hint" class="mt-1.5 text-xs text-[#8C7A68]">{{ hint }}</p>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: { type: [String, Number], default: '' },
  id: { type: String, default: () => `select-${Math.random().toString(36).substring(2, 9)}` },
  label: { type: String, default: '' },
  options: { type: Array, default: () => [] },
  placeholder: { type: String, default: '' },
  error: { type: String, default: '' },
  hint: { type: String, default: '' },
  disabled: { type: Boolean, default: false },
  required: { type: Boolean, default: false },
  valueKey: { type: String, default: 'value' },
  labelKey: { type: String, default: 'label' }
})

defineEmits(['update:modelValue'])

function getOptionValue(opt) {
  if (typeof opt === 'object' && opt !== null) {
    return opt[props.valueKey] ?? opt.id ?? opt.value
  }
  return opt
}

function getOptionLabel(opt) {
  if (typeof opt === 'object' && opt !== null) {
    return opt[props.labelKey] ?? opt.name ?? opt.label
  }
  return opt
}
</script>

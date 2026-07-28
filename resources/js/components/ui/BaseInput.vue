<template>
  <div class="w-full">
    <label v-if="label" :for="id" class="block text-xs font-semibold uppercase tracking-wider text-[#5C3A22] mb-1.5">
      {{ label }}
      <span v-if="required" class="text-[#B84C3C]">*</span>
    </label>

    <div class="relative rounded-xl">
      <!-- Icon Left Slot -->
      <div v-if="$slots['icon-left']" class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#8C7A68]">
        <slot name="icon-left" />
      </div>

      <input
        :id="id"
        :type="inputType"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
        :required="required"
        :class="[
          'w-full rounded-xl bg-white border text-sm text-[#1C1410] placeholder-[#8C7A68]/60 transition-all duration-200 focus:outline-none focus:ring-2 disabled:bg-[#FBF3E7]/50 disabled:text-[#8C7A68] disabled:cursor-not-allowed',
          $slots['icon-left'] ? 'pl-10' : 'pl-3.5',
          hasRightContent ? 'pr-10' : 'pr-3.5',
          error
            ? 'border-[#B84C3C] focus:border-[#B84C3C] focus:ring-[#B84C3C]/20'
            : success
            ? 'border-[#6B8F5E] focus:border-[#6B8F5E] focus:ring-[#6B8F5E]/20'
            : 'border-[#C08E5D]/30 hover:border-[#C08E5D] focus:border-[#5C3A22] focus:ring-[#5C3A22]/20',
          sizeClass
        ]"
        @input="$emit('update:modelValue', $event.target.value)"
        @blur="$emit('blur', $event)"
        @focus="$emit('focus', $event)"
      />

      <!-- Custom Icon Right Slot -->
      <div v-if="$slots['icon-right']" class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-[#8C7A68]">
        <slot name="icon-right" />
      </div>

      <!-- Password Hide / Show Toggle -->
      <button
        v-else-if="type === 'password'"
        type="button"
        @click="showPassword = !showPassword"
        tabindex="-1"
        aria-label="Toggle Password Visibility"
        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#8C7A68] hover:text-[#5C3A22] transition-colors focus:outline-none"
      >
        <!-- Eye Icon (Password Hidden) -->
        <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>

        <!-- Eye Slash Icon (Password Visible) -->
        <svg v-else class="w-4 h-4 text-[#5C3A22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 014.122-.976c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18" />
        </svg>
      </button>
    </div>

    <!-- Error Message -->
    <p v-if="error" class="mt-1.5 text-xs text-[#B84C3C] font-medium flex items-center gap-1">
      <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
      {{ error }}
    </p>
    <p v-else-if="hint" class="mt-1.5 text-xs text-[#8C7A68]">
      {{ hint }}
    </p>
  </div>
</template>

<script setup>
import { ref, computed, useSlots } from 'vue'

const props = defineProps({
  modelValue: { type: [String, Number], default: '' },
  id: { type: String, default: () => `input-${Math.random().toString(36).substring(2, 9)}` },
  type: { type: String, default: 'text' },
  label: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  error: { type: String, default: '' },
  hint: { type: String, default: '' },
  success: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  readonly: { type: Boolean, default: false },
  required: { type: Boolean, default: false },
  size: { type: String, default: 'md' }
})

defineEmits(['update:modelValue', 'blur', 'focus'])

const slots = useSlots()
const showPassword = ref(false)

const inputType = computed(() => {
  if (props.type === 'password') {
    return showPassword.value ? 'text' : 'password'
  }
  return props.type
})

const hasRightContent = computed(() => {
  return !!slots['icon-right'] || props.type === 'password'
})

const sizeClass = computed(() => {
  switch (props.size) {
    case 'sm': return 'py-1.5 text-xs'
    case 'lg': return 'py-3 text-base'
    default:   return 'py-2.5 text-sm'
  }
})
</script>

<template>
  <div class="w-full">
    <label v-if="label" :for="id" class="block text-xs font-semibold uppercase tracking-wider text-brand-choco mb-1.5">
      {{ label }}
      <span v-if="required" class="text-error">*</span>
    </label>

    <div class="relative rounded-xl">
      <!-- Icon Left Slot -->
      <div v-if="$slots['icon-left']" class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-warm-gray">
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
        :min="min"
        :max="max"
        :step="step"
        :maxlength="maxlength"
        :inputmode="computedInputMode"
        :autocomplete="autocomplete"
        :class="[
          'w-full rounded-xl bg-white border text-sm text-ink placeholder-warm-gray/60 transition-all duration-200 focus:outline-none focus:ring-2 disabled:bg-surface/50 disabled:text-warm-gray disabled:cursor-not-allowed',
          $slots['icon-left'] ? 'pl-10' : 'pl-3.5',
          hasRightContent ? 'pr-10' : 'pr-3.5',
          error
            ? 'border-error focus:border-error focus:ring-error/20'
            : success
            ? 'border-success focus:border-success focus:ring-success/20'
            : 'border-brand-caramel/30 hover:border-brand-caramel focus:border-brand-choco focus:ring-brand-choco/20',
          sizeClass
        ]"
        @keydown="handleKeydown"
        @input="handleInput"
        @paste="handlePaste"
        @blur="handleBlur"
        @focus="$emit('focus', $event)"
      />

      <!-- Custom Icon Right Slot -->
      <div v-if="$slots['icon-right']" class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-warm-gray">
        <slot name="icon-right" />
      </div>

      <!-- Password Hide / Show Toggle -->
      <button
        v-else-if="type === 'password'"
        type="button"
        @click="showPassword = !showPassword"
        tabindex="-1"
        aria-label="Toggle Password Visibility"
        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-warm-gray hover:text-brand-choco transition-colors focus:outline-none"
      >
        <!-- Eye Icon (Password Hidden) -->
        <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>

        <!-- Eye Slash Icon (Password Visible) -->
        <svg v-else class="w-4 h-4 text-brand-choco" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 014.122-.976c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18" />
        </svg>
      </button>
    </div>

    <!-- Local / Dynamic Error Message -->
    <p v-if="error || internalError" class="mt-1.5 text-xs text-error font-medium flex items-center gap-1">
      <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
      {{ error || internalError }}
    </p>
    <p v-else-if="hint" class="mt-1.5 text-xs text-warm-gray">
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
  size: { type: String, default: 'md' },
  min: { type: [Number, String], default: undefined },
  max: { type: [Number, String], default: undefined },
  step: { type: [Number, String], default: undefined },
  maxlength: { type: [Number, String], default: undefined },
  numericOnly: { type: Boolean, default: false },
  allowDecimal: { type: Boolean, default: false },
  autocomplete: { type: String, default: undefined },
})

const emit = defineEmits(['update:modelValue', 'blur', 'focus'])

const slots = useSlots()
const showPassword = ref(false)
const internalError = ref('')

const isNumericType = computed(() => {
  return props.type === 'number' || props.type === 'tel' || props.numericOnly
})

const computedInputMode = computed(() => {
  if (props.type === 'email') return 'email'
  if (props.type === 'tel') return 'tel'
  if (props.type === 'number' || props.numericOnly) return props.allowDecimal ? 'decimal' : 'numeric'
  return undefined
})

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

function handleKeydown(event) {
  // Disallow spaces in email inputs
  if (props.type === 'email' && event.key === ' ') {
    event.preventDefault()
    return
  }

  // Keydown numeric filtering for number, tel, or numericOnly inputs
  if (isNumericType.value) {
    // Allow navigation/control keys
    const allowedControlKeys = [
      'Backspace', 'Delete', 'Tab', 'Escape', 'Enter',
      'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown',
      'Home', 'End'
    ]
    if (allowedControlKeys.includes(event.key) || event.ctrlKey || event.metaKey) {
      return
    }

    // Allow decimal point if configured
    if ((props.allowDecimal || props.step) && event.key === '.') {
      const currentVal = String(props.modelValue ?? '')
      if (!currentVal.includes('.')) {
        return
      }
    }

    // Allow leading '+' for phone numbers if at start
    if (props.type === 'tel' && event.key === '+') {
      const currentVal = String(props.modelValue ?? '')
      if (!currentVal.includes('+') && event.target.selectionStart === 0) {
        return
      }
    }

    // Reject non-digit keys
    if (!/^\d$/.test(event.key)) {
      event.preventDefault()
    }
  }
}

function handleInput(event) {
  let value = event.target.value

  if (isNumericType.value) {
    if (props.type === 'tel') {
      value = value.replace(/(?!^\+)[^\d]/g, '')
    } else if (props.allowDecimal || props.step) {
      value = value.replace(/[^\d.]/g, '')
      const parts = value.split('.')
      if (parts.length > 2) {
        value = parts[0] + '.' + parts.slice(1).join('')
      }
    } else {
      value = value.replace(/\D/g, '')
    }
    event.target.value = value
  }

  internalError.value = ''
  emit('update:modelValue', value)
}

function handlePaste(event) {
  if (isNumericType.value) {
    const pastedText = (event.clipboardData || window.clipboardData).getData('text')
    if (props.type === 'tel') {
      if (!/^\+?\d+$/.test(pastedText.trim())) {
        event.preventDefault()
      }
    } else if (props.allowDecimal) {
      if (!/^\d*\.?\d*$/.test(pastedText.trim())) {
        event.preventDefault()
      }
    } else {
      if (!/^\d+$/.test(pastedText.trim())) {
        event.preventDefault()
      }
    }
  }
}

function handleBlur(event) {
  const val = String(props.modelValue ?? '').trim()
  internalError.value = ''

  if (props.type === 'email' && val) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailRegex.test(val)) {
      internalError.value = 'Please enter a valid email address.'
    }
  }

  if (props.min !== undefined && val !== '' && !isNaN(val)) {
    if (Number(val) < Number(props.min)) {
      internalError.value = `Value must be at least ${props.min}.`
    }
  }

  if (props.max !== undefined && val !== '' && !isNaN(val)) {
    if (Number(val) > Number(props.max)) {
      internalError.value = `Value must not exceed ${props.max}.`
    }
  }

  emit('blur', event)
}
</script>

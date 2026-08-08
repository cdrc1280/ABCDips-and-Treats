<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="modelValue"
        class="fixed inset-0 z-50 overflow-y-auto bg-ink/60 backdrop-blur-sm flex items-center justify-center p-4 sm:p-6"
        @click.self="handleBackdropClick"
      >
        <Transition
          enter-active-class="transition-all duration-300 ease-out"
          enter-from-class="opacity-0 scale-95 translate-y-4"
          enter-to-class="opacity-100 scale-100 translate-y-0"
          leave-active-class="transition-all duration-200 ease-in"
          leave-from-class="opacity-100 scale-100 translate-y-0"
          leave-to-class="opacity-0 scale-95 translate-y-4"
        >
          <div
            v-if="modelValue"
            class="bg-white rounded-3xl shadow-2xl border border-brand-caramel/20 overflow-hidden w-full max-w-lg relative"
            style="box-shadow: var(--shadow-xl, 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1));"
          >
            <!-- Modal Header -->
            <div class="px-6 pt-6 pb-4 flex items-center justify-between border-b border-brand-caramel/15">
              <div>
                <h3 class="font-extrabold text-xl text-ink flex items-center gap-2">
                  <slot name="icon" />
                  <span>{{ title }}</span>
                </h3>
                <p v-if="subtitle" class="text-xs text-warm-gray mt-0.5">{{ subtitle }}</p>
              </div>

              <button
                type="button"
                class="w-8 h-8 rounded-full bg-surface hover:bg-brand-tan/30 text-brand-choco flex items-center justify-center transition-colors"
                @click="close"
              >
                ✕
              </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4">
              <slot />
            </div>

            <!-- Modal Footer -->
            <div v-if="$slots.footer" class="px-6 py-4 bg-surface/60 border-t border-brand-caramel/15 flex items-center justify-end gap-3">
              <slot name="footer" />
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
defineProps({
  modelValue: { type: Boolean, required: true },
  title: { type: String, default: 'Confirm Action' },
  subtitle: { type: String, default: '' },
  closeOnBackdrop: { type: Boolean, default: true }
})

const emit = defineEmits(['update:modelValue', 'close'])

function close() {
  emit('update:modelValue', false)
  emit('close')
}

function handleBackdropClick() {
  if (closeOnBackdrop) {
    close()
  }
}
</script>

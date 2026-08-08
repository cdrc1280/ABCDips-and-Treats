<template>
  <Teleport to="body">
    <div
      id="toast-container"
      class="fixed top-3 left-1/2 -translate-x-1/2 sm:translate-x-0 sm:left-auto sm:right-6 sm:top-6 z-[9999] flex flex-col gap-2.5 w-[calc(100vw-24px)] sm:w-full sm:max-w-sm pointer-events-none transition-all duration-300"
    >
      <TransitionGroup name="toast">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          class="pointer-events-auto shadow-2xl rounded-2xl overflow-hidden w-full transition-all duration-300 transform"
        >
          <BaseAlert
            :variant="toast.type"
            :title="toast.title"
            dismissible
            @dismiss="remove(toast.id)"
          >
            {{ toast.message }}
          </BaseAlert>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup>
import { useToast } from '@/composables/useToast'
import BaseAlert from './BaseAlert.vue'

const { toasts, remove } = useToast()
</script>

<style scoped>
.toast-enter-active {
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.toast-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.toast-enter-from {
  opacity: 0;
  transform: translateY(-24px) scale(0.92);
}
.toast-leave-to {
  opacity: 0;
  transform: translateX(120px) scale(0.9);
}

@media (max-width: 639px) {
  .toast-leave-to {
    transform: translateY(-30px) scale(0.9);
  }
}
</style>

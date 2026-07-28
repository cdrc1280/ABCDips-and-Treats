<template>
  <Teleport to="body">
    <div id="toast-container" class="fixed top-6 right-6 z-[100] flex flex-col gap-3 max-w-sm w-full pointer-events-none px-4">
      <TransitionGroup name="toast">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          class="pointer-events-auto shadow-lg rounded-xl overflow-hidden"
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
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.toast-enter-from {
  opacity: 0;
  transform: translateY(-20px) scale(0.95);
}
.toast-leave-to {
  opacity: 0;
  transform: translateX(100px);
}
</style>

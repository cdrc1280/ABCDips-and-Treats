<template>
  <BaseModal :model-value="isOpen" :title="title" @close="handleCancel">
    <div class="space-y-3">
      <p class="text-sm text-ink/90 dark:text-[#FBF3E7]/90 leading-relaxed">
        {{ message }}
      </p>
      <div v-if="itemName" class="p-3.5 rounded-2xl bg-surface dark:bg-[#140D09] border border-brand-caramel/20 dark:border-[#C08E5D]/20 text-xs text-brand-choco dark:text-[#E2C08A] font-bold">
        Item: {{ itemName }}
      </div>
    </div>

    <template #footer>
      <BaseButton variant="ghost" size="sm" @click="handleCancel">Cancel</BaseButton>
      <BaseButton variant="danger" size="sm" :loading="loading" @click="handleConfirm">
        {{ confirmText }}
      </BaseButton>
    </template>
  </BaseModal>
</template>

<script setup>
import { computed } from 'vue'
import BaseModal from '@/components/ui/BaseModal.vue'
import BaseButton from '@/components/ui/BaseButton.vue'

const props = defineProps({
  isOpen: { type: Boolean, default: false },
  itemToRemove: { type: Object, default: null },
  isBulkDelete: { type: Boolean, default: false },
  bulkCount: { type: Number, default: 0 },
  loading: { type: Boolean, default: false },
})

const emit = defineEmits(['cancel', 'confirm'])

const title = computed(() => {
  if (props.isBulkDelete) return 'Delete Selected Items?'
  return 'Remove Item from Basket?'
})

const itemName = computed(() => {
  if (props.isBulkDelete || !props.itemToRemove) return null
  return props.itemToRemove.options?.is_custom
    ? props.itemToRemove.options.custom_title
    : props.itemToRemove.name
})

const message = computed(() => {
  if (props.isBulkDelete) {
    return `Are you sure you want to remove ${props.bulkCount} selected item${props.bulkCount > 1 ? 's' : ''} from your basket?`
  }
  if (props.itemToRemove) {
    return `Are you sure you want to remove this pastry from your basket? You can re-add it from the menu at any time.`
  }
  return 'Are you sure you want to perform this removal?'
})

const confirmText = computed(() => {
  if (props.isBulkDelete) return 'Confirm Delete'
  return 'Confirm Remove'
})

function handleCancel() {
  emit('cancel')
}

function handleConfirm() {
  emit('confirm')
}
</script>

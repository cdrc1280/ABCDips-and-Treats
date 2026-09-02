<template>
  <Teleport to="body">
    <Transition name="fade">
      <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto bg-black/60 backdrop-blur-xs">
        <div class="bg-white dark:bg-[#1E1510] text-ink dark:text-[#FBF3E7] w-full max-w-2xl rounded-3xl p-6 md:p-8 shadow-2xl border border-brand-caramel/30 dark:border-[#C08E5D]/30 space-y-6 max-h-[90vh] flex flex-col">
          
          <!-- Modal Header -->
          <div class="flex items-center justify-between border-b border-brand-caramel/20 dark:border-[#C08E5D]/20 pb-4 shrink-0">
            <div>
              <div class="text-xs font-bold uppercase tracking-wider text-brand-caramel dark:text-[#E2C08A]">Bulk Basket Editor</div>
              <h3 class="text-xl font-extrabold text-ink dark:text-[#FBF3E7]">Edit {{ editItems.length }} Selected Pastry Item{{ editItems.length > 1 ? 's' : '' }}</h3>
            </div>
            <button @click="closeModal" class="w-8 h-8 rounded-full bg-surface dark:bg-[#140D09] text-warm-gray hover:text-ink dark:hover:text-[#FBF3E7] font-bold flex items-center justify-center transition-colors"><X class="w-4 h-4" /></button>
          </div>

          <!-- Items List -->
          <div class="overflow-y-auto space-y-5 pr-1 flex-1">
            <div v-for="item in editItems" :key="item.id" class="p-4 rounded-2xl bg-surface dark:bg-[#140D09] border border-brand-caramel/20 dark:border-[#C08E5D]/20 space-y-3">
              <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                  <img :src="formatImgUrl(item.product?.primary_image_url || item.image_url)" :alt="item.name" @error="(e) => e.target.src = '/images/placeholder-bakery.png'" class="w-12 h-12 rounded-xl object-cover border border-brand-caramel/20 shrink-0" />
                  <div>
                    <h4 class="font-bold text-sm text-ink dark:text-[#FBF3E7]">{{ item.name }}</h4>
                    <span class="text-xs text-brand-choco dark:text-[#E2C08A] font-semibold">₱{{ getItemUnitPrice(item).toFixed(2) }} each</span>
                  </div>
                </div>

                <!-- Quantity Stepper -->
                <div class="flex items-center border border-brand-caramel/30 dark:border-[#C08E5D]/30 rounded-xl bg-white dark:bg-[#1E1510] p-1 shrink-0">
                  <button type="button" class="w-7 h-7 rounded-lg text-brand-choco dark:text-[#E2C08A] font-bold text-xs hover:bg-surface dark:hover:bg-[#140D09] disabled:opacity-30" :disabled="item.draftQty <= 1" @click="item.draftQty--">-</button>
                  <span class="w-8 text-center text-xs font-bold text-ink dark:text-[#FBF3E7]">{{ item.draftQty }}</span>
                  <button type="button" class="w-7 h-7 rounded-lg text-brand-choco dark:text-[#E2C08A] font-bold text-xs hover:bg-surface dark:hover:bg-[#140D09]" @click="item.draftQty++">+</button>
                </div>
              </div>

              <!-- Options Selection -->
              <div class="space-y-3 pt-2 border-t border-brand-caramel/15 dark:border-[#C08E5D]/15 text-xs">
                <!-- Flavor Selector -->
                <div v-if="item.product?.flavors?.length" class="space-y-1.5">
                  <span class="font-bold uppercase text-brand-choco dark:text-[#E2C08A]">Select Flavor:</span>
                  <div class="flex flex-wrap gap-1.5">
                    <button
                      v-for="(flv, fIdx) in item.product.flavors"
                      :key="fIdx"
                      type="button"
                      class="px-3 py-1 rounded-lg border text-xs font-semibold transition-all"
                      :class="item.draftFlavor === flv.name ? 'bg-brand-choco text-white dark:bg-[#E2C08A] dark:text-[#1C1410] border-brand-choco dark:border-[#E2C08A]' : 'bg-white dark:bg-[#1E1510] text-brand-choco dark:text-[#E2C08A] border-brand-caramel/30 dark:border-[#C08E5D]/30'"
                      @click="item.draftFlavor = item.draftFlavor === flv.name ? null : flv.name"
                    >
                      {{ flv.name }} <span v-if="flv.price_modifier && parseFloat(flv.price_modifier) !== 0" class="opacity-80">(+₱{{ Number(flv.price_modifier).toFixed(2) }})</span>
                    </button>
                  </div>
                </div>

                <!-- Variation Selector -->
                <div v-if="item.product?.variations?.length" class="space-y-1.5">
                  <span class="font-bold uppercase text-brand-choco dark:text-[#E2C08A]">Select {{ getVariationLabel(item.product.variation_type) }}:</span>
                  <div class="flex flex-wrap gap-1.5">
                    <button
                      v-for="(v, vIdx) in item.product.variations"
                      :key="vIdx"
                      type="button"
                      class="px-3 py-1 rounded-lg border text-xs font-semibold transition-all"
                      :class="item.draftVariation === v.label ? 'bg-brand-choco text-white dark:bg-[#E2C08A] dark:text-[#1C1410] border-brand-choco dark:border-[#E2C08A]' : 'bg-white dark:bg-[#1E1510] text-brand-choco dark:text-[#E2C08A] border-brand-caramel/30 dark:border-[#C08E5D]/30'"
                      @click="item.draftVariation = v.label"
                    >
                      {{ v.label }} <span v-if="v.price_modifier && parseFloat(v.price_modifier) !== 0" class="opacity-80">(+₱{{ Number(v.price_modifier).toFixed(2) }})</span>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Subtotal -->
              <div class="flex justify-between items-center text-xs pt-1">
                <span class="text-warm-gray dark:text-[#C5B4A4]">Item Subtotal:</span>
                <span class="font-extrabold text-sm text-brand-choco dark:text-[#E2C08A]">₱{{ (getItemUnitPrice(item) * item.draftQty).toFixed(2) }}</span>
              </div>
            </div>
          </div>

          <!-- Footer Actions -->
          <div class="flex justify-end gap-3 pt-4 border-t border-brand-caramel/20 dark:border-[#C08E5D]/20 shrink-0">
            <BaseButton variant="ghost" @click="closeModal">Cancel</BaseButton>
            <BaseButton variant="primary" :loading="saving" @click="saveAllChanges">Save All Changes</BaseButton>
          </div>

        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useCartStore } from '@/stores/cart'
import { useToast } from '@/composables/useToast'
import BaseButton from '@/components/ui/BaseButton.vue'

const props = defineProps({
  isOpen: { type: Boolean, default: false },
  selectedItems: { type: Array, default: () => [] }
})

const emit = defineEmits(['close', 'saved'])

const cartStore = useCartStore()
const toast = useToast()
const editItems = ref([])
const saving = ref(false)

watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    editItems.value = props.selectedItems.map(item => ({
      ...item,
      draftQty: item.qty || 1,
      draftFlavor: item.options?.flavor || null,
      draftVariation: item.options?.variation || (item.product?.variations?.[0]?.label ?? null),
    }))
  }
}, { immediate: true })

function getVariationLabel(type) {
  if (!type || type === 'none') return 'Option'
  const known = { weight: 'Weight', pieces: 'Quantity', size: 'Size', packaging: 'Packaging' }
  return known[type.toLowerCase()] || type
}

function formatImgUrl(url) {
  if (!url) return '/images/placeholder-bakery.png'
  if (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('/images/')) return url
  if (url.startsWith('/storage/')) return url
  if (url.startsWith('storage/')) return `/${url}`
  return `/storage/${url.replace(/^\/+/, '')}`
}

function getItemUnitPrice(item) {
  const base = item.product?.sale_price || item.product?.price || item.unit_price || 0
  let varMod = 0
  let flvMod = 0

  if (item.draftVariation && item.product?.variations) {
    const foundVar = item.product.variations.find(v => v.label === item.draftVariation)
    if (foundVar?.price_modifier) varMod = parseFloat(foundVar.price_modifier)
  }

  if (item.draftFlavor && item.product?.flavors) {
    const foundFlv = item.product.flavors.find(f => f.name === item.draftFlavor)
    if (foundFlv?.price_modifier) flvMod = parseFloat(foundFlv.price_modifier)
  }

  return parseFloat(base) + varMod + flvMod
}

function closeModal() {
  emit('close')
}

async function saveAllChanges() {
  saving.value = true
  try {
    const operations = editItems.value.map(item => {
      const unitPrice = getItemUnitPrice(item)
      const options = {
        ...(item.draftFlavor ? { flavor: item.draftFlavor } : {}),
        ...(item.draftVariation ? { variation: item.draftVariation } : {}),
        unit_price: unitPrice,
      }
      return {
        type: 'update',
        item_id: item.id,
        qty: item.draftQty,
        options,
      }
    })

    const res = await cartStore.batch(operations)
    saving.value = false

    if (res.success) {
      toast.success(`Updated ${editItems.value.length} item${editItems.value.length > 1 ? 's' : ''} in your basket!`, 'Bulk Update Complete')
      emit('saved')
      closeModal()
    } else {
      toast.error('Failed to update selected items.', 'Update Error')
    }
  } catch (err) {
    saving.value = false
    toast.error('Could not save item updates.', 'Error')
  }
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>

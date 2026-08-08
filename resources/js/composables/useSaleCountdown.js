import { ref, computed, onMounted, onUnmounted, unref } from 'vue'

export function useSaleCountdown(saleEndsAtRef) {
  const now = ref(Date.now())
  let timer = null

  onMounted(() => { timer = setInterval(() => { now.value = Date.now() }, 1000) })
  onUnmounted(() => { if (timer) clearInterval(timer) })

  const endsAtMs = computed(() => {
    const val = unref(saleEndsAtRef)
    return val ? new Date(val).getTime() : null
  })
  const diff = computed(() => endsAtMs.value ? Math.max(0, endsAtMs.value - now.value) : null)
  const isExpired = computed(() => diff.value !== null && diff.value <= 0)
  const days = computed(() => diff.value !== null ? Math.floor(diff.value / 86400000) : 0)
  const hours = computed(() => diff.value !== null ? Math.floor((diff.value % 86400000) / 3600000) : 0)
  const minutes = computed(() => diff.value !== null ? Math.floor((diff.value % 3600000) / 60000) : 0)
  const seconds = computed(() => diff.value !== null ? Math.floor((diff.value % 60000) / 1000) : 0)
  const isNearExpiry = computed(() => diff.value !== null && diff.value > 0 && diff.value < 3600000) // < 1 hour

  return { days, hours, minutes, seconds, isExpired, isNearExpiry, diff }
}

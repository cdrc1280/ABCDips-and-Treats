import { ref, onMounted, onUnmounted, watch, computed } from 'vue'

/**
 * Blur-up progressive image loading composable.
 * Shows a blurred low-quality placeholder, then crossfades to the full image.
 * Uses IntersectionObserver for lazy loading.
 *
 * @param {import('vue').Ref<string>|import('vue').ComputedRef<string>} srcRef - Reactive image source URL
 * @param {Object} options - { placeholder, rootMargin, threshold, blurAmount }
 * @returns {{ imgRef, isLoaded, isVisible, imgSrc, onLoad }}
 */
export function useImageLoader(srcRef, options = {}) {
  const {
    placeholder = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="300"%3E%3Crect fill="%23F5E8D0" width="400" height="300"/%3E%3C/svg%3E',
    rootMargin = '200px',
    threshold = 0.01,
    blurAmount = 20,
  } = options

  const imgRef = ref(null)
  const isLoaded = ref(false)
  const isVisible = ref(false)
  const hasError = ref(false)
  let observer = null

  const imgSrc = computed(() => {
    if (!isVisible.value) return placeholder
    return srcRef?.value || placeholder
  })

  const blurStyle = computed(() => {
    if (!isVisible.value || !isLoaded.value) {
      return {
        filter: `blur(${blurAmount}px)`,
        transform: 'scale(1.1)',
        transition: 'filter 0.5s cubic-bezier(0.16, 1, 0.3, 1), transform 0.5s cubic-bezier(0.16, 1, 0.3, 1)',
      }
    }
    return {
      filter: 'blur(0px)',
      transform: 'scale(1)',
      transition: 'filter 0.5s cubic-bezier(0.16, 1, 0.3, 1), transform 0.5s cubic-bezier(0.16, 1, 0.3, 1)',
    }
  })

  function onLoad() {
    isLoaded.value = true
  }

  function onError() {
    hasError.value = true
    isLoaded.value = true
  }

  function setupObserver() {
    if (!imgRef.value || typeof IntersectionObserver === 'undefined') {
      isVisible.value = true
      return
    }

    observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            isVisible.value = true
            observer?.unobserve(entry.target)
          }
        })
      },
      { rootMargin, threshold }
    )

    observer.observe(imgRef.value)
  }

  // Reset loaded state when src changes
  watch(() => srcRef?.value, () => {
    isLoaded.value = false
    hasError.value = false
  })

  onMounted(setupObserver)

  onUnmounted(() => {
    observer?.disconnect()
    observer = null
  })

  return {
    imgRef,
    isLoaded,
    isVisible,
    hasError,
    imgSrc,
    blurStyle,
    onLoad,
    onError,
  }
}

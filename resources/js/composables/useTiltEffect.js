import { onMounted, onUnmounted } from 'vue'

/**
 * Lightweight 3D tilt effect composable using CSS perspective transforms.
 * Applies subtle rotateX/rotateY on mousemove for a premium card interaction.
 * Uses requestAnimationFrame for 60fps performance. Auto-cleanup on unmount.
 *
 * @param {import('vue').Ref<HTMLElement|null>} elementRef - Vue ref to the target element
 * @param {Object} options - { maxTilt, scale, speed, glare }
 */
export function useTiltEffect(elementRef, options = {}) {
  const {
    maxTilt = 8,
    scale = 1.02,
    speed = 400,
    glare = false,
  } = options

  let rafId = null
  let targetRotateX = 0
  let targetRotateY = 0
  let currentRotateX = 0
  let currentRotateY = 0
  let isHovering = false
  let glareEl = null

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches

  function lerp(a, b, t) {
    return a + (b - a) * t
  }

  function updateTransform() {
    if (!elementRef.value) return

    const factor = 0.08 // Interpolation smoothness
    currentRotateX = lerp(currentRotateX, targetRotateX, factor)
    currentRotateY = lerp(currentRotateY, targetRotateY, factor)

    const transform = isHovering
      ? `perspective(${speed}px) rotateX(${currentRotateX}deg) rotateY(${currentRotateY}deg) scale3d(${scale}, ${scale}, ${scale})`
      : `perspective(${speed}px) rotateX(${currentRotateX}deg) rotateY(${currentRotateY}deg) scale3d(1, 1, 1)`

    elementRef.value.style.transform = transform
    elementRef.value.style.willChange = isHovering ? 'transform' : ''

    if (glare && glareEl) {
      const glareAngle = Math.atan2(currentRotateX, currentRotateY) * (180 / Math.PI)
      const glareIntensity = Math.sqrt(currentRotateX ** 2 + currentRotateY ** 2) / maxTilt
      glareEl.style.background = `linear-gradient(${glareAngle + 180}deg, rgba(255,255,255,${0.2 * glareIntensity}) 0%, transparent 80%)`
    }

    if (isHovering || Math.abs(currentRotateX) > 0.05 || Math.abs(currentRotateY) > 0.05) {
      rafId = requestAnimationFrame(updateTransform)
    } else {
      elementRef.value.style.transform = ''
      elementRef.value.style.willChange = ''
    }
  }

  function handleMouseMove(e) {
    if (!elementRef.value) return

    const rect = elementRef.value.getBoundingClientRect()
    const centerX = rect.left + rect.width / 2
    const centerY = rect.top + rect.height / 2

    const percentX = (e.clientX - centerX) / (rect.width / 2)
    const percentY = (e.clientY - centerY) / (rect.height / 2)

    targetRotateX = -percentY * maxTilt
    targetRotateY = percentX * maxTilt
  }

  function handleMouseEnter() {
    isHovering = true
    if (rafId) cancelAnimationFrame(rafId)
    rafId = requestAnimationFrame(updateTransform)
  }

  function handleMouseLeave() {
    isHovering = false
    targetRotateX = 0
    targetRotateY = 0
    // Animation loop continues until values settle to 0
  }

  function setupGlare() {
    if (!glare || !elementRef.value) return
    glareEl = document.createElement('div')
    glareEl.style.cssText = `
      position: absolute; inset: 0; pointer-events: none;
      border-radius: inherit; z-index: 10;
    `
    elementRef.value.style.position = 'relative'
    elementRef.value.appendChild(glareEl)
  }

  onMounted(() => {
    if (prefersReducedMotion || !elementRef.value) return

    elementRef.value.style.transformStyle = 'preserve-3d'
    elementRef.value.style.transition = `transform ${speed}ms cubic-bezier(0.16, 1, 0.3, 1)`

    elementRef.value.addEventListener('mousemove', handleMouseMove, { passive: true })
    elementRef.value.addEventListener('mouseenter', handleMouseEnter, { passive: true })
    elementRef.value.addEventListener('mouseleave', handleMouseLeave, { passive: true })

    setupGlare()
  })

  onUnmounted(() => {
    if (rafId) cancelAnimationFrame(rafId)
    if (elementRef.value) {
      elementRef.value.removeEventListener('mousemove', handleMouseMove)
      elementRef.value.removeEventListener('mouseenter', handleMouseEnter)
      elementRef.value.removeEventListener('mouseleave', handleMouseLeave)
      elementRef.value.style.transform = ''
      elementRef.value.style.willChange = ''
    }
    if (glareEl?.parentNode) {
      glareEl.parentNode.removeChild(glareEl)
    }
  })
}

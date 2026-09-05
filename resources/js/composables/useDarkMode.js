import { ref, onMounted } from 'vue'

const isDark = ref(false)

export function useDarkMode() {
  function initDarkMode() {
    const saved = localStorage.getItem('theme')
    if (saved === 'dark' || (!saved && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      isDark.value = true
      document.documentElement.classList.add('dark')
    } else {
      isDark.value = false
      document.documentElement.classList.remove('dark')
    }
  }

  function applyTheme(targetDark) {
    isDark.value = targetDark
    if (targetDark) {
      document.documentElement.classList.add('dark')
      localStorage.setItem('theme', 'dark')
    } else {
      document.documentElement.classList.remove('dark')
      localStorage.setItem('theme', 'light')
    }
  }

  /**
   * Premium circular radial wave theme transition radiating from the toggle button
   * @param {MouseEvent|PointerEvent} [event]
   */
  function toggleDarkMode(event) {
    const nextDark = !isDark.value

    // If browser doesn't support View Transitions or user prefers reduced motion, fallback gracefully
    if (
      !document.startViewTransition ||
      window.matchMedia('(prefers-reduced-motion: reduce)').matches
    ) {
      applyTheme(nextDark)
      return
    }

    // Capture exact click origin or default to top-right corner
    let x = window.innerWidth - 60
    let y = 40

    if (event?.clientX !== undefined && event?.clientY !== undefined) {
      x = event.clientX
      y = event.clientY
    } else if (event?.currentTarget) {
      const rect = event.currentTarget.getBoundingClientRect()
      x = rect.left + rect.width / 2
      y = rect.top + rect.height / 2
    }

    // Compute maximum distance to the furthest corner of viewport
    const endRadius = Math.hypot(
      Math.max(x, window.innerWidth - x),
      Math.max(y, window.innerHeight - y)
    )

    const transition = document.startViewTransition(() => {
      applyTheme(nextDark)
    })

    transition.ready.then(() => {
      const clipPath = [
        `circle(0px at ${x}px ${y}px)`,
        `circle(${endRadius}px at ${x}px ${y}px)`
      ]

      document.documentElement.animate(
        {
          clipPath: clipPath
        },
        {
          duration: 700,
          easing: 'cubic-bezier(0.19, 1, 0.22, 1)',
          pseudoElement: '::view-transition-new(root)'
        }
      )
    })
  }

  onMounted(() => {
    initDarkMode()
  })

  return {
    isDark,
    toggleDarkMode,
    initDarkMode
  }
}

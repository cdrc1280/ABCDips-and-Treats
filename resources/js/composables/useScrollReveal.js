import { onMounted, onUnmounted, ref } from 'vue'
import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

gsap.registerPlugin(ScrollTrigger)

/**
 * Reusable scroll-triggered reveal composable.
 * Wraps GSAP ScrollTrigger patterns for consistent, DRY animation setup across pages.
 *
 * @param {import('vue').Ref<HTMLElement|null>} containerRef - Vue ref for the GSAP context scope
 * @returns {{ revealFadeUp, revealStagger, revealParallax, cleanup }}
 */
export function useScrollReveal(containerRef) {
  let ctx = null
  const prefersReducedMotion = ref(false)

  function checkMotionPreference() {
    prefersReducedMotion.value = window.matchMedia('(prefers-reduced-motion: reduce)').matches
  }

  function initContext() {
    if (ctx) ctx.revert()
    if (!containerRef.value) return
    ctx = gsap.context(() => {}, containerRef.value)
  }

  /**
   * Fade-up reveal on scroll into view.
   * @param {string} selector - CSS selector for target elements
   * @param {Object} options - { trigger, y, duration, delay, ease, start, once }
   */
  function revealFadeUp(selector, options = {}) {
    if (prefersReducedMotion.value) return
    if (!containerRef.value || !document.querySelector(selector)) return

    const {
      trigger = selector,
      y = 40,
      duration = 0.8,
      delay = 0,
      ease = 'power3.out',
      start = 'top 85%',
      once = true,
    } = options

    if (ctx) {
      ctx.add(() => {
        gsap.from(selector, {
          scrollTrigger: {
            trigger,
            start,
            toggleActions: once ? 'play none none none' : 'play none none reverse',
          },
          y,
          opacity: 0,
          duration,
          delay,
          ease,
          clearProps: once ? 'all' : undefined,
        })
      })
    }
  }

  /**
   * Staggered reveal for a group of elements.
   * @param {string} selector - CSS selector for child elements
   * @param {Object} options - { trigger, y, stagger, duration, ease, start, once }
   */
  function revealStagger(selector, options = {}) {
    if (prefersReducedMotion.value) return
    if (!containerRef.value || !document.querySelector(selector)) return

    const {
      trigger = selector,
      y = 30,
      stagger = 0.08,
      duration = 0.6,
      ease = 'power3.out',
      start = 'top 85%',
      once = true,
    } = options

    if (ctx) {
      ctx.add(() => {
        gsap.from(selector, {
          scrollTrigger: {
            trigger,
            start,
            toggleActions: once ? 'play none none none' : 'play none none reverse',
          },
          y,
          opacity: 0,
          duration,
          stagger,
          ease,
          clearProps: once ? 'all' : undefined,
        })
      })
    }
  }

  /**
   * Parallax scroll effect (element moves slower or faster than scroll).
   * @param {string} selector - CSS selector
   * @param {Object} options - { trigger, yPercent, start, end, scrub }
   */
  function revealParallax(selector, options = {}) {
    if (prefersReducedMotion.value) return
    if (!containerRef.value || !document.querySelector(selector)) return

    const {
      trigger = selector,
      yPercent = -20,
      start = 'top bottom',
      end = 'bottom top',
      scrub = 1,
    } = options

    if (ctx) {
      ctx.add(() => {
        gsap.to(selector, {
          scrollTrigger: { trigger, start, end, scrub },
          yPercent,
          ease: 'none',
        })
      })
    }
  }

  /**
   * Hero entrance timeline with staggered content reveals.
   * @param {Array<string>} selectors - Ordered array of CSS selectors to reveal sequentially
   * @param {Object} options - { y, duration, stagger, ease, delay }
   */
  function revealHeroTimeline(selectors, options = {}) {
    if (prefersReducedMotion.value) return
    if (!containerRef.value) return

    const {
      y = 30,
      duration = 0.7,
      stagger = 0.12,
      ease = 'power3.out',
      delay = 0.15,
    } = options

    if (ctx) {
      ctx.add(() => {
        const validSelectors = selectors.filter(s => document.querySelector(s))
        if (!validSelectors.length) return

        const tl = gsap.timeline({ defaults: { ease } })
        validSelectors.forEach((sel, i) => {
          tl.from(sel, {
            y,
            opacity: 0,
            duration,
          }, i === 0 ? `+=${delay}` : `-=${duration * 0.55}`)
        })
      })
    }
  }

  function cleanup() {
    if (ctx) {
      ctx.revert()
      ctx = null
    }
  }

  onMounted(() => {
    checkMotionPreference()
    initContext()
  })

  onUnmounted(cleanup)

  return {
    revealFadeUp,
    revealStagger,
    revealParallax,
    revealHeroTimeline,
    cleanup,
    prefersReducedMotion,
    initContext,
  }
}

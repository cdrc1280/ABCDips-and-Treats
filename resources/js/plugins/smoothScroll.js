import Lenis from 'lenis'
import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

let lenisInstance = null

export function initSmoothScroll() {
  if (typeof window === 'undefined') return null
  if (lenisInstance) return lenisInstance

  gsap.registerPlugin(ScrollTrigger)

  lenisInstance = new Lenis({
    duration: 1.1,
    easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
    smoothWheel: true,
    touchMultiplier: 1.5,
  })

  lenisInstance.on('scroll', ScrollTrigger.update)

  gsap.ticker.add((time) => {
    lenisInstance.raf(time * 1000)
  })

  gsap.ticker.lagSmoothing(0)

  window.__lenis = lenisInstance
  return lenisInstance
}

export function getLenis() {
  return lenisInstance
}

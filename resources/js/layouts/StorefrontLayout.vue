<template>
  <div class="min-h-screen bg-surface flex flex-col relative selection:bg-[#D9A876]/30">
    <!-- Navigation Header (STATIONARY - NEVER RELOADS) -->
    <StorefrontNav />

    <!-- Dynamic Main Page Viewport with Spring-Physics Page Transitions -->
    <main class="flex-1 relative z-10">
      <RouterView v-slot="{ Component, route }">
        <Transition
          name="page-smooth"
          mode="out-in"
          appear
          @before-enter="onBeforeEnter"
          @enter="onEnter"
          @after-enter="onAfterEnter"
        >
          <component :is="Component" :key="route.name || route.path" />
        </Transition>
      </RouterView>
    </main>

    <!-- Footer (STATIONARY - NEVER RELOADS) -->
    <StorefrontFooter />

    <!-- Global Persistent Modals & Drawers -->
    <CartDrawer />
    <ShopeeProductModal />
    <ToastContainer />
    <AiChatWidget />
  </div>
</template>

<script setup>
import { onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import StorefrontNav from '@/components/storefront/StorefrontNav.vue'
import StorefrontFooter from '@/components/storefront/StorefrontFooter.vue'
import CartDrawer from '@/components/storefront/CartDrawer.vue'
import ShopeeProductModal from '@/components/storefront/ShopeeProductModal.vue'
import ToastContainer from '@/components/ui/ToastContainer.vue'
import AiChatWidget from '@/components/storefront/AiChatWidget.vue'
import { useCartStore } from '@/stores/cart'
import Lenis from 'lenis'

const cartStore = useCartStore()
const router = useRouter()
let lenis = null
let rafId = null

function initLenis() {
  if (typeof window === 'undefined') return

  lenis = new Lenis({
    duration: 1.1,
    easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
    orientation: 'vertical',
    smoothWheel: true,
    touchMultiplier: 1.5,
  })

  // Expose globally for router afterEach scroll reset
  window.__lenis = lenis

  function raf(time) {
    lenis.raf(time)
    rafId = requestAnimationFrame(raf)
  }

  rafId = requestAnimationFrame(raf)
}

function destroyLenis() {
  if (rafId) cancelAnimationFrame(rafId)
  if (lenis) {
    lenis.destroy()
    lenis = null
    window.__lenis = null
  }
}

// Page transition hooks — add subtle will-change for GPU compositing
function onBeforeEnter(el) {
  el.style.willChange = 'opacity, transform'
}

function onEnter(el, done) {
  // Let CSS transition handle the animation, just ensure cleanup
  el.addEventListener('transitionend', () => {
    done()
  }, { once: true })

  // Fallback timeout in case transitionend doesn't fire
  setTimeout(done, 500)
}

function onAfterEnter(el) {
  el.style.willChange = ''
}

// Scroll to top on route change (smooth via Lenis)
router.afterEach(() => {
  if (lenis) {
    lenis.scrollTo(0, { immediate: true })
  } else {
    window.scrollTo(0, 0)
  }
})

onMounted(() => {
  cartStore.fetchCart()
  initLenis()
})

onUnmounted(destroyLenis)
</script>

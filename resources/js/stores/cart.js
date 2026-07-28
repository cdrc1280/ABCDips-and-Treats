import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import router from '@/router'
import { useAuthStore } from '@/stores/auth'
import { useToast } from '@/composables/useToast'

export const useCartStore = defineStore('cart', () => {
  // ─── State ───────────────────────────────────────────────────
  const cart = ref(null)
  const loading = ref(false)
  const adding = ref(false)
  const error = ref(null)

  // ─── Getters ─────────────────────────────────────────────────
  const items       = computed(() => cart.value?.items ?? [])
  const itemCount   = computed(() => items.value.reduce((sum, i) => sum + i.qty, 0))
  const subtotal    = computed(() => cart.value?.subtotal ?? 0)
  const discount    = computed(() => cart.value?.discount_amount ?? 0)
  const fees        = computed(() => cart.value?.fee_amount ?? 0)
  const total       = computed(() => cart.value?.total ?? 0)
  const couponCode  = computed(() => cart.value?.coupon_code ?? null)
  const isEmpty     = computed(() => items.value.length === 0)
  const cartToken   = computed(() => cart.value?.cart_token ?? localStorage.getItem('cart_token'))

  // ─── Actions ─────────────────────────────────────────────────
  async function fetchCart() {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get('/api/cart')
      cart.value = data.data
      if (data.data?.cart_token) {
        localStorage.setItem('cart_token', data.data.cart_token)
      }
    } catch (err) {
      error.value = err.response?.data?.message ?? 'Could not load cart.'
    } finally {
      loading.value = false
    }
  }

  async function addItem(productId, qty = 1, options = {}) {
    const authStore = useAuthStore()
    const toast = useToast()

    // Enforce authentication for adding to cart
    if (!authStore.isAuthenticated) {
      toast.warning('Please sign in to add items to your basket and place orders.', 'Sign In Required')
      router.push({ name: 'login', query: { redirect: router.currentRoute.value.fullPath } })
      return { success: false, requiresAuth: true }
    }

    adding.value = true
    error.value = null
    try {
      const { data } = await axios.post('/api/cart/items', { product_id: productId, qty, options })
      cart.value = data.data
      return { success: true }
    } catch (err) {
      error.value = err.response?.data?.message ?? 'Could not add item.'
      return { success: false }
    } finally {
      adding.value = false
    }
  }

  async function updateItem(itemId, qty) {
    try {
      const { data } = await axios.put(`/api/cart/items/${itemId}`, { qty })
      cart.value = data.data
    } catch (err) {
      error.value = err.response?.data?.message
    }
  }

  async function removeItem(itemId) {
    try {
      const { data } = await axios.delete(`/api/cart/items/${itemId}`)
      cart.value = data.data
    } catch (err) {
      error.value = err.response?.data?.message
    }
  }

  async function restoreItem(itemId) {
    try {
      const { data } = await axios.post(`/api/cart/items/${itemId}/restore`)
      cart.value = data.data
    } catch (err) {
      error.value = err.response?.data?.message
    }
  }

  async function applyCoupon(code) {
    try {
      const { data } = await axios.post('/api/cart/coupon', { code })
      cart.value = data.data
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message ?? 'Invalid coupon.' }
    }
  }

  async function removeCoupon() {
    try {
      const { data } = await axios.delete('/api/cart/coupon')
      cart.value = data.data
    } catch { /* silent */ }
  }

  async function batch(operations) {
    try {
      const { data } = await axios.post('/api/cart/batch', { operations })
      cart.value = data.data
      return { success: true }
    } catch (err) {
      error.value = err.response?.data?.message
      return { success: false }
    }
  }

  function clearCart() {
    cart.value = null
  }

  function clearLocalCart() {
    if (cart.value) {
      cart.value.items = []
      cart.value.subtotal = 0
      cart.value.total = 0
    }
  }

  return {
    cart, loading, adding, error, cartToken,
    items, itemCount, subtotal, discount, fees, total, couponCode, isEmpty,
    fetchCart, addItem, updateItem, removeItem, restoreItem,
    applyCoupon, removeCoupon, batch, clearCart, clearLocalCart,
  }
})

import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

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

  // ─── Actions ─────────────────────────────────────────────────
  async function fetchCart() {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get('/api/cart')
      cart.value = data.data
    } catch (err) {
      error.value = err.response?.data?.message ?? 'Could not load cart.'
    } finally {
      loading.value = false
    }
  }

  async function addItem(productId, qty = 1, options = {}) {
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

  return {
    cart, loading, adding, error,
    items, itemCount, subtotal, discount, fees, total, couponCode, isEmpty,
    fetchCart, addItem, updateItem, removeItem, restoreItem,
    applyCoupon, removeCoupon, batch, clearCart,
  }
})

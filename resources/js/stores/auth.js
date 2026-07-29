import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useAuthStore = defineStore('auth', () => {
  // ─── State ──────────────────────────────────────────────────
  const user = ref(null)
  const loading = ref(false)
  const error = ref(null)

  // ─── Getters ─────────────────────────────────────────────────
  const isAuthenticated = computed(() => !!user.value)
  const isAdmin         = computed(() => user.value?.roles?.includes('admin') || user.value?.roles?.includes('super_admin'))
  const userName        = computed(() => user.value?.name ?? '')
  const userEmail       = computed(() => user.value?.email ?? '')

  // ─── Actions ─────────────────────────────────────────────────
  async function fetchUser() {
    const token = localStorage.getItem('auth_token')
    if (token) {
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
    }
    try {
      loading.value = true
      const { data } = await axios.get('/api/me')
      user.value = data.data
      const { useCartStore } = await import('@/stores/cart')
      const cartStore = useCartStore()
      await cartStore.fetchCart()
    } catch {
      user.value = null
      localStorage.removeItem('auth_token')
      localStorage.removeItem('cart_token')
      delete axios.defaults.headers.common['Authorization']
    } finally {
      loading.value = false
    }
  }

  async function login(credentials) {
    error.value = null
    loading.value = true
    try {
      await axios.get('/sanctum/csrf-cookie')
      const { data } = await axios.post('/api/auth/login', credentials)
      user.value = data.data.user
      
      if (data.data?.token) {
        localStorage.setItem('auth_token', data.data.token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${data.data.token}`
      }

      await mergeCart()
      const { useCartStore } = await import('@/stores/cart')
      const cartStore = useCartStore()
      await cartStore.fetchCart()
      return data.data
    } catch (err) {
      error.value = err.response?.data?.message ?? 'Login failed. Please try again.'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function register(payload) {
    error.value = null
    loading.value = true
    try {
      await axios.get('/sanctum/csrf-cookie')
      const { data } = await axios.post('/api/auth/register', payload)
      user.value = data.data.user

      if (data.data?.token) {
        localStorage.setItem('auth_token', data.data.token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${data.data.token}`
      }

      const { useCartStore } = await import('@/stores/cart')
      const cartStore = useCartStore()
      await cartStore.fetchCart()

      return data.data
    } catch (err) {
      error.value = err.response?.data?.message ?? 'Registration failed.'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    loading.value = true
    try {
      await axios.post('/api/auth/logout')
    } catch {
      // Ignore network errors during logout
    } finally {
      user.value = null
      localStorage.removeItem('auth_token')
      localStorage.removeItem('cart_token')
      delete axios.defaults.headers.common['Authorization']
      const { useCartStore } = await import('@/stores/cart')
      const cartStore = useCartStore()
      cartStore.clearCart()
      loading.value = false
    }
  }

  async function mergeCart() {
    const cartToken = localStorage.getItem('cart_token')
    if (!cartToken) return
    try {
      await axios.post('/api/cart/merge', { guest_cart_token: cartToken })
    } catch {
      // Merge failure is silent
    }
  }

  function clearUser() {
    user.value = null
    localStorage.removeItem('auth_token')
    delete axios.defaults.headers.common['Authorization']
  }

  return {
    user, loading, error,
    isAuthenticated, isAdmin, userName, userEmail,
    fetchUser, login, register, logout, clearUser,
  }
})

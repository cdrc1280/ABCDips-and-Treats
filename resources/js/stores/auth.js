import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useAuthStore = defineStore('auth', () => {
  // ─── State ──────────────────────────────────────────────────
  const user = ref(null)
  const loading = ref(false)
  const initialized = ref(false)
  const error = ref(null)

  // ─── Getters ─────────────────────────────────────────────────
  const isAuthenticated = computed(() => !!user.value)
  const isAdmin = computed(() => user.value?.roles?.includes('admin') || user.value?.roles?.includes('super_admin'))
  const userName = computed(() => user.value?.name ?? '')
  const userEmail = computed(() => user.value?.email ?? '')

  // ─── Actions ─────────────────────────────────────────────────
  async function fetchUser(force = false) {
    if (initialized.value && !force) return

    const authToken = localStorage.getItem('auth_token')

    // No token — we know the user is not logged in, skip the API call entirely.
    // This prevents 401 errors from spamming the console on every navigation.
    if (!authToken) {
      user.value = null
      initialized.value = true
      loading.value = false
      return
    }

    axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`

    try {
      loading.value = true
      const { data } = await axios.get('/api/me')
      user.value = data.data
    } catch {
      user.value = null
      localStorage.removeItem('auth_token')
      delete axios.defaults.headers.common['Authorization']
    } finally {
      loading.value = false
      initialized.value = true
    }

    try {
      const { useCartStore } = await import('@/stores/cart')
      const cartStore = useCartStore()
      await cartStore.fetchCart()
    } catch {
      // Cart fetch failure shouldn't crash auth initialization or router navigation
    }
  }

  async function login(credentials) {
    error.value = null
    loading.value = true
    try {
      await axios.get('/sanctum/csrf-cookie')
      const { data } = await axios.post('/api/auth/login', credentials)
      user.value = data.data.user

      if (data.data.token) {
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

      if (data.data.token) {
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
      initialized.value = false  // Force re-check on next navigation / bfcache restore
      localStorage.removeItem('auth_token')
      sessionStorage.setItem('logged_out', '1') // Signal for bfcache detection
      delete axios.defaults.headers.common['Authorization']
      localStorage.removeItem('cart_token')
      delete axios.defaults.headers.common['X-Cart-Token']
      const { useCartStore } = await import('@/stores/cart')
      const cartStore = useCartStore()
      cartStore.clearCart()
      loading.value = false
    }
  }

  // Call this on every route navigation to detect token mismatch
  // (e.g. user logged out in another tab, or bfcache restored stale state)
  function syncFromStorage() {
    const storedToken = localStorage.getItem('auth_token')
    const loggedOut = sessionStorage.getItem('logged_out')

    // If sessionStorage says logged out, trust it and clear state
    if (loggedOut === '1') {
      sessionStorage.removeItem('logged_out')
      if (user.value !== null || initialized.value) {
        user.value = null
        initialized.value = false
        delete axios.defaults.headers.common['Authorization']
      }
      return false // not authenticated
    }

    // If Pinia says authenticated but localStorage token is gone → clear
    if (user.value && !storedToken) {
      user.value = null
      initialized.value = false
      delete axios.defaults.headers.common['Authorization']
      return false
    }

    return !!storedToken
  }

  async function mergeCart() {
    const cartToken = localStorage.getItem('cart_token')
    if (!cartToken) return
    try {
      await axios.post('/api/cart/merge', { guest_cart_token: cartToken })
    } catch {
      // Merge failure is silent
    } finally {
      localStorage.removeItem('cart_token')
      delete axios.defaults.headers.common['X-Cart-Token']
    }
  }

  function clearUser() {
    user.value = null
    localStorage.removeItem('auth_token')
    delete axios.defaults.headers.common['Authorization']
    delete axios.defaults.headers.common['X-Cart-Token']
  }

  return {
    user, loading, initialized, error,
    isAuthenticated, isAdmin, userName, userEmail,
    fetchUser, login, register, logout, clearUser, syncFromStorage,
  }
})

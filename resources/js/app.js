import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from '@/router'
import App from '@/App.vue'
import axios from 'axios'
import { vTooltip } from '@/directives/vTooltip'

// ─── Axios global config ─────────────────────────────────────
axios.defaults.withCredentials = true
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

// CSRF token from meta tag
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
if (csrfToken) {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken
}

// Bearer token for Sanctum API auth
const authToken = localStorage.getItem('auth_token')
if (authToken) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`
}

// Cart token for guest cart (CoCart-style)
const cartToken = localStorage.getItem('cart_token')
if (cartToken) {
  axios.defaults.headers.common['X-Cart-Token'] = cartToken
}

// Base URL
axios.defaults.baseURL = window.location.origin

axios.interceptors.request.use((config) => {
  if (config.method && ['post', 'put', 'patch', 'delete'].includes(config.method.toLowerCase())) {
    config.headers = config.headers || {}
    config.headers['X-Requested-With'] = 'XMLHttpRequest'
  }
  return config
}, (error) => Promise.reject(error))

// Response interceptor — capture cart token + handle expired sessions silently
axios.interceptors.response.use(
  (response) => {
    const newCartToken = response.headers['x-cart-token']
    if (newCartToken) {
      localStorage.setItem('cart_token', newCartToken)
      axios.defaults.headers.common['X-Cart-Token'] = newCartToken
    }
    return response
  },
  (error) => {
    // Silently handle token expiry — clear stale credentials and let the
    // router guard redirect to login. This prevents 401s from appearing in
    // the browser console for normal unauthenticated visitors.
    if (error.response?.status === 401) {
      const isApiMe = error.config?.url?.includes('/api/me')
      if (!isApiMe) {
        // For non-/api/me 401s, clear the stale token immediately.
        // (fetchUser handles /api/me 401 itself with its own catch block)
        localStorage.removeItem('auth_token')
        delete axios.defaults.headers.common['Authorization']
      }
    }
    return Promise.reject(error)
  }
)

// ─── Bootstrap app ───────────────────────────────────────────
const pinia = createPinia()
const app = createApp(App)

// ─── Production hardening ────────────────────────────────────
if (import.meta.env.PROD) {
  app.config.devtools = false       // Disable Vue Devtools panel
  app.config.performance = false    // No performance tracing
  app.config.warnHandler = () => {} // Silence Vue runtime warnings
}

app.use(pinia)
app.use(router)

app.directive('tooltip', vTooltip)

app.config.globalProperties.$axios = axios
app.provide('axios', axios)

app.mount('#app')

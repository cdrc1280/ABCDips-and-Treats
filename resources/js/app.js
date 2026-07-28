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

// Restore Auth token from localStorage if present
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

// Response interceptor — capture cart token from server headers
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
    // If 401 Unauthorized, clear auth_token header and state
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token')
      delete axios.defaults.headers.common['Authorization']
    }
    return Promise.reject(error)
  }
)

// ─── Bootstrap app ───────────────────────────────────────────
const pinia = createPinia()
const app = createApp(App)

app.use(pinia)
app.use(router)

app.directive('tooltip', vTooltip)

app.config.globalProperties.$axios = axios
app.provide('axios', axios)

app.mount('#app')

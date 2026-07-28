import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import router from '@/router'
import { useAuthStore } from '@/stores/auth'
import { useToast } from '@/composables/useToast'

export const useWishlistStore = defineStore('wishlist', () => {
  const items = ref([])
  const wishlistIds = ref(new Set())
  const loading = ref(false)
  const togglingId = ref(null)

  const count = computed(() => items.value.length)

  async function fetchWishlist() {
    const authStore = useAuthStore()
    if (!authStore.isAuthenticated) {
      items.value = []
      wishlistIds.value = new Set()
      return
    }

    loading.value = true
    try {
      const { data } = await axios.get('/api/wishlist')
      items.value = data.data || []
      wishlistIds.value = new Set(items.value.map(p => p.id))
    } catch (err) {
      console.error('Failed to load wishlist', err)
    } finally {
      loading.value = false
    }
  }

  function isInWishlist(productId) {
    return wishlistIds.value.has(productId)
  }

  async function toggleWishlist(product) {
    const authStore = useAuthStore()
    const toast = useToast()

    if (!authStore.isAuthenticated) {
      toast.warning('Please sign in to save items to your wishlist.', 'Sign In Required')
      router.push({ name: 'login', query: { redirect: router.currentRoute.value.fullPath } })
      return { success: false, requiresAuth: true }
    }

    const productId = typeof product === 'object' ? product.id : product
    const productName = typeof product === 'object' ? product.name : 'Item'

    togglingId.value = productId
    try {
      const { data } = await axios.post(`/api/wishlist/${productId}`)
      if (data.added) {
        wishlistIds.value.add(productId)
        if (typeof product === 'object' && !items.value.some(p => p.id === productId)) {
          items.value.push(product)
        }
        toast.success(`Saved ${productName} to your wishlist!`, 'Wishlist Saved')
      } else {
        wishlistIds.value.delete(productId)
        items.value = items.value.filter(p => p.id !== productId)
        toast.info(`Removed ${productName} from your wishlist.`, 'Wishlist Updated')
      }
      return { success: true, added: data.added }
    } catch (err) {
      toast.error(err.response?.data?.message || 'Could not update wishlist.', 'Wishlist Error')
      return { success: false }
    } finally {
      togglingId.value = null
    }
  }

  return {
    items,
    wishlistIds,
    loading,
    togglingId,
    count,
    fetchWishlist,
    isInWishlist,
    toggleWishlist
  }
})

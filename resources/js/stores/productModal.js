import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'

export const useProductModalStore = defineStore('productModal', () => {
  const isOpen = ref(false)
  const product = ref(null)
  const loading = ref(false)

  async function openModal(productOrSlug) {
    if (!productOrSlug) return

    isOpen.value = true

    if (typeof productOrSlug === 'object') {
      product.value = productOrSlug
      // Fetch fresh complete details (gallery, reviews, allergens) if slug is available
      if (productOrSlug.slug) {
        fetchProductDetails(productOrSlug.slug)
      }
    } else if (typeof productOrSlug === 'string') {
      loading.value = true
      await fetchProductDetails(productOrSlug)
      loading.value = false
    }
  }

  async function fetchProductDetails(slug) {
    try {
      const { data } = await axios.get(`/api/products/${slug}`)
      if (data.data) {
        product.value = data.data
      }
    } catch (err) {
      console.error('Failed to load product modal details', err)
    }
  }

  function closeModal() {
    isOpen.value = false
    product.value = null
    loading.value = false
  }

  return {
    isOpen,
    product,
    loading,
    openModal,
    closeModal,
  }
})

import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'

export const useProductModalStore = defineStore('productModal', () => {
  const isOpen = ref(false)
  const product = ref(null)
  const editingCartItem = ref(null)
  const loading = ref(false)

  async function openModal(productOrSlug) {
    if (!productOrSlug) return

    isOpen.value = true

    if (typeof productOrSlug === 'object') {
      product.value = productOrSlug
      if (productOrSlug.slug) {
        fetchProductDetails(productOrSlug.slug)
      }
    } else if (typeof productOrSlug === 'string') {
      loading.value = true
      await fetchProductDetails(productOrSlug)
      loading.value = false
    }
  }

  async function openModalForEdit(cartItem) {
    if (!cartItem) return
    editingCartItem.value = cartItem
    const prod = cartItem.product || { id: cartItem.product_id, name: cartItem.name, price: cartItem.unit_price }
    await openModal(prod)
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
    editingCartItem.value = null
    loading.value = false
  }

  return {
    isOpen,
    product,
    editingCartItem,
    loading,
    openModal,
    openModalForEdit,
    closeModal,
  }
})

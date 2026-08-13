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
    editingCartItem.value = null

    if (typeof productOrSlug === 'object') {
      product.value = productOrSlug
      if (productOrSlug.slug || productOrSlug.id) {
        const idOrSlug = productOrSlug.slug || productOrSlug.id
        await fetchProductDetails(idOrSlug)
      }
    } else if (typeof productOrSlug === 'string' || typeof productOrSlug === 'number') {
      loading.value = true
      await fetchProductDetails(productOrSlug)
      loading.value = false
    }
  }

  async function openModalForEdit(cartItem) {
    if (!cartItem) return
    editingCartItem.value = cartItem
    isOpen.value = true
    loading.value = true

    const identifier = cartItem.slug || cartItem.product?.slug || cartItem.product_slug || cartItem.product_id

    if (identifier) {
      await fetchProductDetails(identifier)
    } else if (cartItem.product) {
      product.value = cartItem.product
    } else {
      product.value = { id: cartItem.product_id, name: cartItem.name, price: cartItem.unit_price }
    }

    loading.value = false
  }

  async function fetchProductDetails(identifier) {
    try {
      const { data } = await axios.get(`/api/products/${identifier}`)
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

import { ref } from 'vue'

const toasts = ref([])

export function useToast() {
  function add({ message, type = 'info', title = '', timeout = 4000 }) {
    const id = Math.random().toString(36).substring(2, 9)
    const toast = { id, message, type, title }
    toasts.value.push(toast)

    if (timeout > 0) {
      setTimeout(() => {
        remove(id)
      }, timeout)
    }

    return id
  }

  function remove(id) {
    const index = toasts.value.findIndex(t => t.id === id)
    if (index !== -1) {
      toasts.value.splice(index, 1)
    }
  }

  function success(message, title = 'Success') {
    return add({ message, title, type: 'success' })
  }

  function error(message, title = 'Error') {
    return add({ message, title, type: 'error' })
  }

  function warning(message, title = 'Warning') {
    return add({ message, title, type: 'warning' })
  }

  function info(message, title = 'Notice') {
    return add({ message, title, type: 'info' })
  }

  return {
    toasts,
    add,
    remove,
    success,
    error,
    warning,
    info
  }
}

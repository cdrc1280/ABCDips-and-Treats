export const vTooltip = {
  mounted(el, binding) {
    if (!binding.value) return

    const text = typeof binding.value === 'object' ? binding.value.text : binding.value
    const position = (typeof binding.value === 'object' && binding.value.position) ? binding.value.position : 'top'

    if (!text) return

    el.setAttribute('title', text)

    let tooltipEl = null

    function createTooltip() {
      tooltipEl = document.createElement('div')
      tooltipEl.className = 'fixed z-99999 pointer-events-none transition-all duration-200 opacity-0 scale-95 px-3 py-1.5 bg-ink/95 text-surface text-[11px] font-semibold rounded-xl border border-brand-caramel/30 shadow-2xl backdrop-blur-md max-w-xs text-center leading-snug tracking-wide'
      tooltipEl.textContent = text
      document.body.appendChild(tooltipEl)
    }

    function positionTooltip() {
      if (!tooltipEl) return
      const rect = el.getBoundingClientRect()
      const tipRect = tooltipEl.getBoundingClientRect()

      let top = 0
      let left = 0

      if (position === 'top') {
        top = rect.top - tipRect.height - 8
        left = rect.left + (rect.width - tipRect.width) / 2
      } else if (position === 'bottom') {
        top = rect.bottom + 8
        left = rect.left + (rect.width - tipRect.width) / 2
      } else if (position === 'left') {
        top = rect.top + (rect.height - tipRect.height) / 2
        left = rect.left - tipRect.width - 8
      } else if (position === 'right') {
        top = rect.top + (rect.height - tipRect.height) / 2
        left = rect.right + 8
      }

      // Viewport bounds check
      if (left < 10) left = 10
      if (left + tipRect.width > window.innerWidth - 10) {
        left = window.innerWidth - tipRect.width - 10
      }
      if (top < 10) top = rect.bottom + 8

      tooltipEl.style.top = `${top}px`
      tooltipEl.style.left = `${left}px`
    }

    function show() {
      if (!tooltipEl) createTooltip()
      positionTooltip()
      requestAnimationFrame(() => {
        if (tooltipEl) {
          tooltipEl.classList.remove('opacity-0', 'scale-95')
          tooltipEl.classList.add('opacity-100', 'scale-100')
        }
      })
    }

    function hide() {
      if (tooltipEl) {
        tooltipEl.classList.remove('opacity-100', 'scale-100')
        tooltipEl.classList.add('opacity-0', 'scale-95')
        setTimeout(() => {
          if (tooltipEl && tooltipEl.parentNode) {
            tooltipEl.parentNode.removeChild(tooltipEl)
            tooltipEl = null
          }
        }, 200)
      }
    }

    el._tooltipShow = show
    el._tooltipHide = hide

    el.addEventListener('mouseenter', show)
    el.addEventListener('mouseleave', hide)
    el.addEventListener('focus', show)
    el.addEventListener('blur', hide)
  },
  updated(el, binding) {
    if (binding.value !== binding.oldValue) {
      const text = typeof binding.value === 'object' ? binding.value.text : binding.value
      if (text) {
        el.setAttribute('title', text)
      }
    }
  },
  unmounted(el) {
    if (el._tooltipHide) {
      el._tooltipHide()
    }
    el.removeEventListener('mouseenter', el._tooltipShow)
    el.removeEventListener('mouseleave', el._tooltipHide)
    el.removeEventListener('focus', el._tooltipShow)
    el.removeEventListener('blur', el._tooltipHide)
  }
}

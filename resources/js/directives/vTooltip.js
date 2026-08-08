export const vTooltip = {
  mounted(el, binding) {
    el._tooltipValue = binding.value
    if (el.hasAttribute('title')) el.removeAttribute('title')

    let tooltipEl = null

    function getTooltipText() {
      const val = el._tooltipValue
      if (!val) return ''
      return typeof val === 'object' ? val.text : String(val)
    }

    function getTooltipPosition() {
      const val = el._tooltipValue
      return (typeof val === 'object' && val.position) ? val.position : 'top'
    }

    function createTooltip() {
      tooltipEl = document.createElement('div')
      const isDark = document.documentElement.classList.contains('dark')
      tooltipEl.className = isDark
        ? 'fixed z-[9999] pointer-events-none transition-all duration-200 opacity-0 scale-95 px-3 py-1.5 bg-[#2A1C13]/95 text-[#E2C08A] text-[11px] font-semibold rounded-xl border border-[#C08E5D]/40 shadow-2xl backdrop-blur-md max-w-xs text-center leading-snug tracking-wide'
        : 'fixed z-[9999] pointer-events-none transition-all duration-200 opacity-0 scale-95 px-3 py-1.5 bg-[#1C1410]/95 text-[#FBF3E7] text-[11px] font-semibold rounded-xl border border-[#C08E5D]/30 shadow-2xl backdrop-blur-md max-w-xs text-center leading-snug tracking-wide'
      
      document.body.appendChild(tooltipEl)
    }

    function positionTooltip() {
      if (!tooltipEl) return
      const rect = el.getBoundingClientRect()
      const tipRect = tooltipEl.getBoundingClientRect()
      const position = getTooltipPosition()

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

      // Viewport overflow boundary guards
      if (left < 10) left = 10
      if (left + tipRect.width > window.innerWidth - 10) {
        left = window.innerWidth - tipRect.width - 10
      }
      if (top < 10) top = rect.bottom + 8

      tooltipEl.style.top = `${top}px`
      tooltipEl.style.left = `${left}px`
    }

    function show() {
      const text = getTooltipText()
      if (!text) return

      if (!tooltipEl) createTooltip()

      tooltipEl.textContent = text
      
      const isDark = document.documentElement.classList.contains('dark')
      tooltipEl.className = isDark
        ? 'fixed z-[9999] pointer-events-none transition-all duration-200 opacity-0 scale-95 px-3 py-1.5 bg-[#2A1C13]/95 text-[#E2C08A] text-[11px] font-semibold rounded-xl border border-[#C08E5D]/40 shadow-2xl backdrop-blur-md max-w-xs text-center leading-snug tracking-wide'
        : 'fixed z-[9999] pointer-events-none transition-all duration-200 opacity-0 scale-95 px-3 py-1.5 bg-[#1C1410]/95 text-[#FBF3E7] text-[11px] font-semibold rounded-xl border border-[#C08E5D]/30 shadow-2xl backdrop-blur-md max-w-xs text-center leading-snug tracking-wide'

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
    el._tooltipValue = binding.value
    if (el.hasAttribute('title')) el.removeAttribute('title')
    if (el._tooltipShow && document.activeElement === el) {
      el._tooltipShow()
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

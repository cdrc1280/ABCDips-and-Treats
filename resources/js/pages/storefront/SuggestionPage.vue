<template>
  <div class="max-w-4xl mx-auto px-6 py-16">
    <div class="text-center mb-12">
      <span class="font-['Caveat'] text-brand-caramel dark:text-[#E2C08A] text-2xl block mb-2">we're listening</span>
      <h1 class="text-4xl font-extrabold text-ink dark:text-[#FBF3E7] tracking-tight">Share Your Ideas</h1>
      <p class="text-warm-gray dark:text-[#C5B4A4] mt-3">Help us make ABCDips & Treats even better</p>
    </div>

    <div v-if="success" class="bg-white dark:bg-[#1E1510] rounded-3xl p-12 text-center border border-brand-caramel/20 dark:border-[#C08E5D]/20 shadow-lg animate-[pulse_1s_ease-in-out]">
      <div class="w-16 h-16 rounded-3xl bg-emerald-500/20 text-emerald-500 mx-auto flex items-center justify-center mb-6 border border-emerald-500/30"><CheckCircle2 class="w-10 h-10" /></div>
      <h2 class="text-2xl font-extrabold text-ink dark:text-[#FBF3E7] mb-2">Thank you for your suggestion!</h2>
      <p class="text-warm-gray dark:text-[#C5B4A4] mb-8">We appreciate your feedback and will review your ideas carefully.</p>
      <button @click="resetForm" class="bg-brand-choco dark:bg-[#C08E5D] text-white dark:text-[#1C1410] px-6 py-2.5 rounded-xl font-bold hover:bg-[#3D2515] dark:hover:bg-[#E2C08A] transition-colors">
        Submit Another Idea
      </button>
    </div>

    <form v-else @submit.prevent="submit" class="bg-white dark:bg-[#1E1510] rounded-3xl p-8 border border-brand-caramel/20 dark:border-[#C08E5D]/20 shadow-md space-y-8">
      
      <!-- Category Selection -->
      <div>
        <label class="block text-sm font-extrabold text-ink dark:text-[#FBF3E7] mb-3">What kind of suggestion is this? *</label>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div v-for="cat in categories" :key="cat.id" 
               @click="form.category = cat.id"
               v-tooltip="`Select category: ${cat.label}`"
               class="cursor-pointer rounded-2xl p-4 border-2 transition-all flex items-start gap-4"
               :class="form.category === cat.id ? 'border-brand-choco dark:border-[#E2C08A] bg-brand-tan/10 dark:bg-[#C08E5D]/10 shadow-sm' : 'border-brand-caramel/20 dark:border-[#C08E5D]/20 hover:border-brand-choco dark:hover:border-[#E2C08A] hover:bg-surface/50 dark:hover:bg-[#140D09]/50'">
            <div class="w-10 h-10 rounded-xl bg-[#D9A876]/20 flex items-center justify-center text-[#5C3A22] dark:text-[#E2C08A] shrink-0 border border-[#C08E5D]/30"><component :is="cat.icon" class="w-5 h-5" /></div>
            <div>
              <h3 class="font-bold text-ink dark:text-[#FBF3E7] text-sm">{{ cat.label }}</h3>
              <p class="text-xs text-warm-gray dark:text-[#C5B4A4] mt-1 leading-relaxed">{{ cat.desc }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- User Info -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-xs font-bold text-ink dark:text-[#FBF3E7] mb-1.5">Your Name *</label>
          <input v-model="form.name" required :readonly="authStore.isAuthenticated"
                 class="w-full bg-surface dark:bg-[#140D09] border border-brand-caramel/30 dark:border-[#C08E5D]/30 rounded-xl px-4 py-3 text-sm text-ink dark:text-[#FBF3E7] focus:outline-none focus:border-brand-choco dark:focus:border-[#E2C08A] transition-all disabled:opacity-70" 
                 :class="authStore.isAuthenticated ? 'bg-surface/60 dark:bg-[#140D09]/60 cursor-not-allowed' : ''" />
        </div>
        <div>
          <label class="block text-xs font-bold text-ink dark:text-[#FBF3E7] mb-1.5">Email Address *</label>
          <input v-model="form.email" type="email" required :readonly="authStore.isAuthenticated"
                 class="w-full bg-surface dark:bg-[#140D09] border border-brand-caramel/30 dark:border-[#C08E5D]/30 rounded-xl px-4 py-3 text-sm text-ink dark:text-[#FBF3E7] focus:outline-none focus:border-brand-choco dark:focus:border-[#E2C08A] transition-all disabled:opacity-70"
                 :class="authStore.isAuthenticated ? 'bg-surface/60 dark:bg-[#140D09]/60 cursor-not-allowed' : ''" />
        </div>
      </div>

      <!-- Suggestion Content -->
      <div>
        <label class="block text-xs font-bold text-ink mb-1.5">Subject *</label>
        <input v-model="form.subject" required placeholder="Brief summary of your idea"
               class="w-full bg-surface border border-brand-caramel/30 rounded-xl px-4 py-3 text-sm text-ink focus:outline-none focus:border-brand-choco transition-all" />
      </div>

      <div>
        <label class="block text-xs font-bold text-ink mb-1.5">Your Message *</label>
        <textarea v-model="form.message" required rows="6" minlength="10" placeholder="Please describe your suggestion or feedback in detail..."
                  class="w-full bg-surface border border-brand-caramel/30 rounded-xl px-4 py-3 text-sm text-ink focus:outline-none focus:border-brand-choco transition-all resize-none"></textarea>
        <p class="text-[11px] text-warm-gray mt-1 text-right">{{ form.message.length }} / minimum 10 characters</p>
      </div>

      <!-- Submit -->
      <div class="pt-2">
        <button type="submit" :disabled="submitting || form.message.length < 10 || !form.category"
                v-tooltip="'Submit your suggestion directly to ABCDips team'"
                class="w-full bg-brand-choco text-white py-4 rounded-xl font-bold text-base hover:bg-[#3D2515] disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-md">
          {{ submitting ? 'Sending Suggestion...' : 'Submit Suggestion' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, inject, onMounted, watch } from 'vue'
import { CheckCircle2, Lightbulb, Star, Wrench, MessageSquare } from 'lucide-vue-next'
import { useAuthStore } from '@/stores/auth'
import { useToast } from '@/composables/useToast'

const axios = inject('axios')
const authStore = useAuthStore()
const toast = useToast()

const submitting = ref(false)
const success = ref(false)

const categories = [
  { id: 'product', icon: Lightbulb, label: 'Product Idea', desc: 'Suggest a new product or flavor' },
  { id: 'service', icon: Star, label: 'Service Feedback', desc: 'Tell us about your experience' },
  { id: 'feature', icon: Wrench, label: 'Feature Request', desc: 'App or website improvements' },
  { id: 'other', icon: MessageSquare, label: 'Other', desc: 'Anything else on your mind' }
]

const form = ref({
  category: '',
  name: '',
  email: '',
  subject: '',
  message: ''
})

function populateUserData() {
  if (authStore.user) {
    if (!form.value.name) form.value.name = authStore.user.name || ''
    if (!form.value.email) form.value.email = authStore.user.email || ''
  }
}

watch(() => authStore.user, populateUserData, { immediate: true })
onMounted(populateUserData)

async function submit() {
  if (form.value.message.length < 10 || !form.value.category) return
  submitting.value = true
  try {
    await axios.post('/api/suggestions', form.value)
    success.value = true
    toast.success('Your suggestion has been submitted! Thank you.', 'Feedback Received')
  } catch (err) {
    console.error('Failed to submit suggestion', err)
    toast.error(err.response?.data?.message || 'Failed to submit suggestion. Please try again.', 'Submission Error')
  } finally {
    submitting.value = false
  }
}

function resetForm() {
  success.value = false
  form.value.subject = ''
  form.value.message = ''
  form.value.category = ''
}
</script>

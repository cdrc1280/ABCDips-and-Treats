<template>
  <div class="fixed bottom-3 right-3 sm:bottom-6 sm:right-6 z-50 flex flex-col items-end gap-2.5 sm:gap-3 max-w-[calc(100vw-24px)]">
    <!-- Chat Panel -->
    <Transition enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0 scale-95 translate-y-4" enter-to-class="opacity-100 scale-100 translate-y-0"
      leave-active-class="transition-all duration-200 ease-in" leave-from-class="opacity-100 scale-100 translate-y-0"
      leave-to-class="opacity-0 scale-95 translate-y-4">
      <div v-if="isOpen"
        class="w-[calc(100vw-24px)] sm:w-88 md:w-96 max-w-sm bg-[#FDFBF7] dark:bg-[#1E1510] backdrop-blur-xl rounded-3xl shadow-2xl border border-[#C08E5D]/30 overflow-hidden flex flex-col"
        style="height: min(520px, calc(100vh - 90px));">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-[#4A2D1A] via-[#5C3A22] to-[#362215] px-4 py-3.5 flex items-center justify-between shrink-0 shadow-md">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full bg-[#D9A876]/30 flex items-center justify-center text-lg shadow-inner">
              <User v-if="escalationSuccess" class="w-4 h-4 text-[#FBF3E7]" /><Bot v-else class="w-4 h-4 text-[#FBF3E7]" />
            </div>
            <div>
              <p class="text-xs font-bold text-[#FBF3E7] leading-none">
                {{ escalationSuccess ? 'ABCDips Support Team' : 'ABCDips Pastry Assistant' }}
              </p>
              <p class="text-[10px] text-[#E2C08A] leading-none mt-0.5 font-medium">
                {{ escalationSuccess ? 'Connected to support team' : 'Ask me anything about our pastries!' }}
              </p>
            </div>
          </div>
          <button @click="isOpen = false" class="text-[#FBF3E7]/70 hover:text-[#FBF3E7] transition-colors p-1.5 rounded-lg hover:bg-white/10"
            v-tooltip="'Close assistant'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Messages -->
        <div ref="messagesEl" class="flex-1 overflow-y-auto px-3.5 sm:px-4 py-3 space-y-3 scroll-smooth relative bg-[#FDFBF7] dark:bg-[#1A120C]">
          <!-- Welcome message -->
          <div v-if="messages.length === 0" class="flex gap-2 items-start">
            <div class="w-7 h-7 rounded-full bg-[#D9A876]/30 flex items-center justify-center text-[#5C3A22] dark:text-[#E2C08A] shrink-0 border border-[#C08E5D]/20"><Bot class="w-4 h-4" /></div>
            <div class="bg-[#F5E8D0] dark:bg-[#2A1C13] rounded-2xl rounded-tl-sm px-3.5 py-2.5 max-w-[85%] border border-[#C08E5D]/20 shadow-xs">
              <p class="text-xs text-[#1C1410] dark:text-[#FBF3E7] leading-relaxed break-words">Hello! I can help with our pastry menu,
                allergen badges, custom orders, delivery hours, and more. What can I get for you today?</p>
            </div>
          </div>

          <div v-for="(msg, i) in messages" :key="i"
            :class="msg.role === 'user' ? 'flex justify-end' : 'flex gap-2 items-start'">
            <div v-if="msg.role === 'assistant'" class="w-7 h-7 rounded-full bg-[#D9A876]/30 flex items-center justify-center text-sm shrink-0 border border-[#C08E5D]/20"><Bot class="w-4 h-4 text-[#5C3A22] dark:text-[#E2C08A]" /></div>
            <div v-else-if="msg.role === 'admin'"
              class="w-7 h-7 rounded-full bg-[#6B8F5E]/30 flex items-center justify-center text-emerald-700 dark:text-emerald-400 shrink-0 border border-[#6B8F5E]/30"><ShieldCheck class="w-4 h-4" />
            </div>
            
            <div :class="[
              'rounded-2xl px-3.5 py-2.5 max-w-[85%] sm:max-w-[82%] shadow-xs border break-words',
              msg.role === 'user'
                ? 'bg-[#5C3A22] text-[#FBF3E7] border-[#5C3A22] rounded-tr-sm dark:bg-[#C08E5D] dark:text-[#1C1410]'
                : (msg.role === 'admin' 
                    ? 'bg-[#6B8F5E]/20 text-[#1C1410] dark:text-[#FBF3E7] border-[#6B8F5E]/40 rounded-tl-sm font-medium'
                    : 'bg-[#F5E8D0] text-[#1C1410] border-[#C08E5D]/20 rounded-tl-sm dark:bg-[#2A1C13] dark:text-[#FBF3E7]')
            ]">
              <div v-if="msg.role === 'admin'" class="text-[10px] font-bold text-[#6B8F5E] dark:text-[#A4C997] mb-0.5">
                Support Agent
              </div>
              <p class="text-xs leading-relaxed whitespace-pre-line break-words">{{ msg.content }}</p>
            </div>
          </div>

          <!-- Loading dots -->
          <div v-if="loading" class="flex gap-2 items-start">
            <div class="w-7 h-7 rounded-full bg-[#D9A876]/30 flex items-center justify-center text-[#5C3A22] dark:text-[#E2C08A] shrink-0 border border-[#C08E5D]/20"><Bot class="w-4 h-4" /></div>
            <div class="bg-[#F5E8D0] dark:bg-[#2A1C13] rounded-2xl rounded-tl-sm px-4 py-3 border border-[#C08E5D]/20">
              <div class="flex gap-1">
                <span class="w-1.5 h-1.5 bg-[#C08E5D] rounded-full animate-bounce" style="animation-delay:0ms" />
                <span class="w-1.5 h-1.5 bg-[#C08E5D] rounded-full animate-bounce" style="animation-delay:150ms" />
                <span class="w-1.5 h-1.5 bg-[#C08E5D] rounded-full animate-bounce" style="animation-delay:300ms" />
              </div>
            </div>
          </div>

          <!-- Talk to a human button -->
          <div v-if="!escalationSuccess && !isGuestLimitReached" class="flex justify-center mt-3">
            <button 
              @click="autoEscalate" 
              :disabled="escalating"
              class="text-xs bg-[#FBF3E7] dark:bg-[#2A1C13] border border-[#C08E5D]/40 px-3.5 py-1.5 rounded-full shadow-sm text-[#5C3A22] dark:text-[#E2C08A] font-bold hover:bg-[#E2C08A]/20 transition-all transform hover:scale-105 disabled:opacity-50 flex items-center gap-1.5"
              v-tooltip="'Escalate this conversation to our support team'"
            >
              <span v-if="escalating" class="w-3 h-3 border-2 border-[#5C3A22] dark:border-[#C08E5D] border-t-transparent rounded-full animate-spin"></span>
              <span>{{ escalating ? 'Escalating...' : 'Talk to a Human Agent' }}</span>
            </button>
          </div>

          <!-- Escalation Success Banner -->
          <div v-if="escalationSuccess" class="bg-[#6B8F5E]/15 border border-[#6B8F5E]/30 text-[#6B8F5E] dark:text-[#A4C997] text-xs p-3 rounded-xl text-center mt-3 font-semibold">
            Chat escalated to human support! Our team has received your conversation history and will respond shortly.
          </div>

          <!-- Guest Limit Reached Banner -->
          <div v-if="isGuestLimitReached" class="bg-[#F5E8D0] dark:bg-[#2A1C13] border border-[#C08E5D]/40 text-[#5C3A22] dark:text-[#E2C08A] text-xs p-3.5 rounded-2xl text-center mt-3 shadow-xs space-y-2">
            <p class="font-bold">Free Guest Chat Limit Reached (5/5)</p>
            <p class="text-[11px] opacity-90 leading-normal">Create a free account or log in to enjoy unlimited AI chat, order tracking, and exclusive discounts!</p>
            <div class="flex items-center justify-center gap-2 pt-1">
              <RouterLink to="/auth/register" @click="isOpen = false">
                <button class="bg-[#5C3A22] text-[#FBF3E7] dark:bg-[#C08E5D] dark:text-[#1C1410] px-3 py-1.5 rounded-xl font-bold text-xs hover:opacity-90 transition-opacity shadow-xs">
                  Create Account
                </button>
              </RouterLink>
              <RouterLink to="/auth/login" @click="isOpen = false">
                <button class="bg-[#FBF3E7] dark:bg-[#1C1410] border border-[#C08E5D]/40 text-[#5C3A22] dark:text-[#E2C08A] px-3 py-1.5 rounded-xl font-bold text-xs hover:bg-[#E2C08A]/20 transition-colors">
                  Log In
                </button>
              </RouterLink>
            </div>
          </div>
        </div>

        <!-- Quick prompts -->
        <div v-if="messages.length === 0 && !isGuestLimitReached && !escalationSuccess" class="px-3 pb-2 pt-1 flex flex-wrap gap-1.5 bg-[#FDFBF7] dark:bg-[#1A120C] shrink-0">
          <button v-for="q in quickPrompts" :key="q" @click="sendMessage(q)"
            class="text-[10px] bg-[#F5E8D0] dark:bg-[#2A1C13] border border-[#C08E5D]/30 text-[#5C3A22] dark:text-[#E2C08A] font-semibold px-2.5 py-1 rounded-full hover:bg-[#E2C08A]/30 transition-all text-left transform hover:scale-[1.02] shadow-2xs">{{
            q }}</button>
        </div>

        <!-- Input -->
        <div class="px-3 pb-3 pt-2 border-t border-[#C08E5D]/20 shrink-0 bg-[#FDFBF7] dark:bg-[#180E09]">
          <form @submit.prevent="sendMessage()" class="flex gap-2">
            <input v-model="inputText" :disabled="loading || isGuestLimitReached" 
              :placeholder="isGuestLimitReached ? 'Guest limit reached — log in to chat' : (escalationSuccess ? 'Send message to human support...' : 'Ask about our pastries...')"
              class="flex-1 bg-[#F5E8D0]/60 dark:bg-[#2A1C13] border border-[#C08E5D]/30 rounded-xl px-3 py-2 text-xs text-[#1C1410] dark:text-[#FBF3E7] placeholder-[#8C7A68] dark:placeholder-[#A89686] focus:outline-none focus:border-[#5C3A22] focus:ring-1 focus:ring-[#5C3A22]/30 transition-all disabled:opacity-50 disabled:cursor-not-allowed" />
            <button type="submit" :disabled="!inputText.trim() || loading || isGuestLimitReached"
              class="bg-[#5C3A22] text-[#FBF3E7] dark:bg-[#C08E5D] dark:text-[#1C1410] rounded-xl px-3.5 py-2 disabled:opacity-40 hover:bg-[#4A2D1A] transition-colors shrink-0 shadow-xs flex items-center justify-center"
              v-tooltip="isGuestLimitReached ? 'Guest limit reached — please log in' : 'Send message'">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
              </svg>
            </button>
          </form>
        </div>
      </div>
    </Transition>

    <!-- FAB Button -->
    <button @click="isOpen = !isOpen"
      v-tooltip="'Ask Dips AI about menu items, allergens &amp; custom orders'"
      class="w-13 h-13 sm:w-14 sm:h-14 rounded-full bg-[#5C3A22] dark:bg-[#C08E5D] text-[#FBF3E7] dark:text-[#1C1410] shadow-xl hover:scale-110 transition-all duration-300 flex items-center justify-center relative group border border-[#C08E5D]/30 select-none">
      <Transition mode="out-in" enter-active-class="transition-all duration-200"
        enter-from-class="opacity-0 rotate-90 scale-50" enter-to-class="opacity-100 rotate-0 scale-100"
        leave-active-class="transition-all duration-150" leave-from-class="opacity-100 rotate-0 scale-100"
        leave-to-class="opacity-0 rotate-90 scale-50">
        <Bot v-if="!isOpen" class="w-6 h-6" />
        <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </Transition>
      <!-- Unread indicator -->
      <span v-if="!isOpen"
        class="absolute -top-0.5 -right-0.5 w-3.5 h-3.5 bg-[#D9A876] rounded-full border-2 border-white dark:border-[#140D09] animate-pulse" />
    </button>
  </div>
</template>

<script setup>
import { ref, nextTick, inject, computed, onMounted, onUnmounted, watch } from 'vue'
import { Bot, User, ShieldCheck, MessageCircle, Lock, CheckCircle2, AlertTriangle, Send } from 'lucide-vue-next'
import { useAuthStore } from '@/stores/auth'

const axios = inject('axios')
const authStore = useAuthStore()
const isOpen = ref(false)
const loading = ref(false)
const inputText = ref('')
const messagesEl = ref(null)
const messages = ref([])

const GUEST_MESSAGE_LIMIT = 5

const escalationSuccess = ref(false)
const escalating = ref(false)
const hasAutoEscalated = ref(false)
let pollTimer = null

const userDetails = ref({
  name: localStorage.getItem('guest_name') || '',
  email: localStorage.getItem('guest_email') || ''
})

const userMessageCount = computed(() => messages.value.filter(m => m.role === 'user').length)
const isGuestLimitReached = computed(() => !authStore.isAuthenticated && userMessageCount.value >= GUEST_MESSAGE_LIMIT)

const quickPrompts = [
  'What pastries do you recommend?',
  'Do you have gluten-free options?',
  'How do I order a custom cake?',
  'What are your delivery hours?'
]

async function scrollToBottom() {
  await nextTick()
  if (messagesEl.value) messagesEl.value.scrollTop = messagesEl.value.scrollHeight
}

async function pollClientMessages() {
  try {
    const emailParam = userDetails.value.email ? `?guest_email=${encodeURIComponent(userDetails.value.email)}` : ''
    const { data } = await axios.get(`/api/chat/escalate/messages${emailParam}`)

    if (data && data.conversation && data.conversation.length > 0) {
      // Check if conversation has new messages or admin replies
      if (data.conversation.length > messages.value.length) {
        messages.value = data.conversation.map(m => ({ role: m.role, content: m.content }))
        await scrollToBottom()
      }
      if (data.status && data.status !== 'resolved') {
        escalationSuccess.value = true
        hasAutoEscalated.value = true
      }
    }
  } catch (err) {
    // Ignore polling errors
  }
}

async function autoEscalate() {
  if (hasAutoEscalated.value || escalating.value) return
  escalating.value = true
  try {
    const name = userDetails.value.name || (authStore.user?.name ? authStore.user.name : 'Guest Customer')
    const email = userDetails.value.email || (authStore.user?.email ? authStore.user.email : null)
    
    const convPayload = messages.value.length 
      ? messages.value.map(m => ({ role: m.role, content: m.content }))
      : [{ role: 'user', content: 'Customer requested human support' }]

    await axios.post('/api/chat/escalate', {
      guest_name: name,
      guest_email: email,
      conversation: convPayload
    })
    
    hasAutoEscalated.value = true
    escalationSuccess.value = true
    
    messages.value.push({ 
      role: 'assistant', 
      content: 'Your request has been escalated to our human support team! We have received your conversation history and will follow up shortly.' 
    })
    await scrollToBottom()
  } catch (err) {
    const errMsg = err.response?.data?.message || 'Failed to submit escalation. Please try again.'
    messages.value.push({ role: 'assistant', content: errMsg })
    await scrollToBottom()
  } finally {
    escalating.value = false
  }
}

async function sendMessage(text) {
  if (isGuestLimitReached.value) return

  const q = (text || inputText.value).trim()
  if (!q) return

  messages.value.push({ role: 'user', content: q })
  inputText.value = ''

  // If chat is already escalated to human support, stop AI bot and forward directly to support team
  if (hasAutoEscalated.value || escalationSuccess.value) {
    await scrollToBottom()
    try {
      const name = userDetails.value.name || (authStore.user?.name ? authStore.user.name : 'Guest Customer')
      const email = userDetails.value.email || (authStore.user?.email ? authStore.user.email : null)
      await axios.post('/api/chat/escalate', {
        guest_name: name,
        guest_email: email,
        conversation: messages.value.map(m => ({ role: m.role, content: m.content }))
      })
    } catch (err) {
      // Ignore background sync errors
    }
    return
  }

  // Otherwise, query AI assistant
  loading.value = true
  await scrollToBottom()
  try {
    const { data } = await axios.post('/api/ai/query', {
      prompt: q,
      query: q,
      context: 'customer',
      history: messages.value.slice(-6)
    })
    messages.value.push({ role: 'assistant', content: data.response || data.data?.response || data.message || 'Sorry, I couldn\'t get a response. Please try again!' })
  } catch (err) {
    const errMsg = err.response?.data?.message || 'Sorry, I am having trouble connecting right now. Please contact us directly or try again shortly.'
    messages.value.push({ role: 'assistant', content: errMsg })
  } finally {
    loading.value = false
    await scrollToBottom()

    // Auto escalate when 3 messages sent
    if (userMessageCount.value >= 3 && !hasAutoEscalated.value) {
      await autoEscalate()
    }
  }
}

watch(isOpen, (newVal) => {
  if (newVal) {
    pollClientMessages()
  }
})

onMounted(() => {
  pollClientMessages()
  pollTimer = setInterval(() => {
    if (isOpen.value && (escalationSuccess.value || hasAutoEscalated.value)) {
      pollClientMessages()
    }
  }, 4000)
})

onUnmounted(() => {
  if (pollTimer) clearInterval(pollTimer)
})
</script>

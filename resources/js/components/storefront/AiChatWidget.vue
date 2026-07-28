<template>
  <div class="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-3">
    <!-- Chat Panel -->
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0 scale-95 translate-y-4"
      enter-to-class="opacity-100 scale-100 translate-y-0"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100 scale-100 translate-y-0"
      leave-to-class="opacity-0 scale-95 translate-y-4"
    >
      <div
        v-if="isOpen"
        class="w-80 bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-[#C08E5D]/20 overflow-hidden flex flex-col"
        style="height: 420px;"
      >
        <!-- Header -->
        <div class="bg-[#5C3A22] px-4 py-3.5 flex items-center justify-between flex-shrink-0">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full bg-[#D9A876]/30 flex items-center justify-center text-lg">🧁</div>
            <div>
              <p class="text-xs font-bold text-[#FBF3E7] leading-none">Dips, our AI Assistant</p>
              <p class="text-[10px] text-[#D9A876] leading-none mt-0.5">Ask me anything about our pastries!</p>
            </div>
          </div>
          <button @click="isOpen = false" class="text-[#FBF3E7]/60 hover:text-[#FBF3E7] transition-colors p-1" v-tooltip="'Close assistant'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <!-- Messages -->
        <div ref="messagesEl" class="flex-1 overflow-y-auto px-4 py-3 space-y-3 scroll-smooth">
          <!-- Welcome message -->
          <div v-if="messages.length === 0" class="flex gap-2 items-start">
            <div class="w-7 h-7 rounded-full bg-[#D9A876]/20 flex items-center justify-center text-sm flex-shrink-0">🧁</div>
            <div class="bg-[#FBF3E7] rounded-2xl rounded-tl-sm px-3 py-2.5 max-w-[85%]">
              <p class="text-xs text-[#1C1410] leading-relaxed">Hi! I'm Dips 🧁 I can help with our pastry menu, ingredients, allergens, custom orders, and more. What can I get for you today?</p>
            </div>
          </div>

          <div v-for="(msg, i) in messages" :key="i" :class="msg.role === 'user' ? 'flex justify-end' : 'flex gap-2 items-start'">
            <div v-if="msg.role === 'assistant'" class="w-7 h-7 rounded-full bg-[#D9A876]/20 flex items-center justify-center text-sm flex-shrink-0">🧁</div>
            <div
              :class="[
                'rounded-2xl px-3 py-2.5 max-w-[85%]',
                msg.role === 'user'
                  ? 'bg-[#5C3A22] text-[#FBF3E7] rounded-tr-sm'
                  : 'bg-[#FBF3E7] text-[#1C1410] rounded-tl-sm'
              ]"
            >
              <p class="text-xs leading-relaxed whitespace-pre-line">{{ msg.content }}</p>
            </div>
          </div>

          <!-- Loading dots -->
          <div v-if="loading" class="flex gap-2 items-start">
            <div class="w-7 h-7 rounded-full bg-[#D9A876]/20 flex items-center justify-center text-sm flex-shrink-0">🧁</div>
            <div class="bg-[#FBF3E7] rounded-2xl rounded-tl-sm px-4 py-3">
              <div class="flex gap-1">
                <span class="w-1.5 h-1.5 bg-[#C08E5D] rounded-full animate-bounce" style="animation-delay:0ms" />
                <span class="w-1.5 h-1.5 bg-[#C08E5D] rounded-full animate-bounce" style="animation-delay:150ms" />
                <span class="w-1.5 h-1.5 bg-[#C08E5D] rounded-full animate-bounce" style="animation-delay:300ms" />
              </div>
            </div>
          </div>
        </div>

        <!-- Quick prompts -->
        <div v-if="messages.length === 0" class="px-4 pb-2 flex flex-wrap gap-1.5">
          <button
            v-for="q in quickPrompts"
            :key="q"
            @click="sendMessage(q)"
            class="text-[10px] bg-[#FBF3E7] border border-[#C08E5D]/30 text-[#5C3A22] px-2.5 py-1 rounded-full hover:bg-[#D9A876]/20 transition-colors"
          >{{ q }}</button>
        </div>

        <!-- Input -->
        <div class="px-3 pb-3 pt-2 border-t border-[#C08E5D]/20 flex-shrink-0">
          <form @submit.prevent="sendMessage()" class="flex gap-2">
            <input
              v-model="inputText"
              :disabled="loading"
              placeholder="Ask about our pastries..."
              class="flex-1 bg-[#FBF3E7] border border-[#C08E5D]/30 rounded-xl px-3 py-2 text-xs text-[#1C1410] placeholder-[#8C7A68] focus:outline-none focus:border-[#5C3A22] focus:ring-1 focus:ring-[#5C3A22]/30 transition-all"
            />
            <button
              type="submit"
              :disabled="!inputText.trim() || loading"
              class="bg-[#5C3A22] text-white rounded-xl px-3 py-2 disabled:opacity-40 hover:bg-[#4A2D1A] transition-colors flex-shrink-0"
              v-tooltip="'Send message to AI assistant'"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
            </button>
          </form>
        </div>
      </div>
    </Transition>

    <!-- FAB Button -->
    <button
      @click="isOpen = !isOpen"
      v-tooltip="'Ask Dips AI about menu items, ingredients, allergens &amp; custom orders'"
      class="w-14 h-14 rounded-full bg-[#5C3A22] text-white shadow-xl hover:bg-[#4A2D1A] hover:scale-110 transition-all duration-300 flex items-center justify-center relative group"
    >
      <Transition mode="out-in" enter-active-class="transition-all duration-200" enter-from-class="opacity-0 rotate-90 scale-50" enter-to-class="opacity-100 rotate-0 scale-100" leave-active-class="transition-all duration-150" leave-from-class="opacity-100 rotate-0 scale-100" leave-to-class="opacity-0 rotate-90 scale-50">
        <span v-if="!isOpen" class="text-2xl">🧁</span>
        <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
      </Transition>
      <!-- Unread indicator -->
      <span v-if="!isOpen" class="absolute -top-0.5 -right-0.5 w-3.5 h-3.5 bg-[#D9A876] rounded-full border-2 border-white animate-pulse" />
    </button>
  </div>
</template>

<script setup>
import { ref, nextTick, inject } from 'vue'

const axios = inject('axios')
const isOpen = ref(false)
const loading = ref(false)
const inputText = ref('')
const messagesEl = ref(null)
const messages = ref([])

const quickPrompts = ['What are your best sellers?', 'Do you have gluten-free options?', 'How do I place a custom cake order?']

async function scrollToBottom() {
  await nextTick()
  if (messagesEl.value) messagesEl.value.scrollTop = messagesEl.value.scrollHeight
}

async function sendMessage(text) {
  const q = (text || inputText.value).trim()
  if (!q) return
  messages.value.push({ role: 'user', content: q })
  inputText.value = ''
  loading.value = true
  await scrollToBottom()
  try {
    const { data } = await axios.post('/api/admin/ai/query', {
      query: q,
      context: 'customer',
      history: messages.value.slice(-6)
    })
    messages.value.push({ role: 'assistant', content: data.response || data.data?.response || 'Sorry, I couldn\'t get a response. Please try again!' })
  } catch {
    messages.value.push({ role: 'assistant', content: 'Sorry, I am having trouble connecting right now. Please contact us directly or try again shortly.' })
  } finally {
    loading.value = false
    await scrollToBottom()
  }
}
</script>

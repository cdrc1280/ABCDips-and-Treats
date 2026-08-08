<template>
  <div class="page-container py-10 md:py-16">
    <PageHeader
      tagline="intelligent operations"
      title="Antigravity AI Bakery Advisor"
      subtitle="Ask operational questions regarding raw ingredient reorder levels, recipe gross margin pricing, and real-time sales trends."
    />

    <div class="max-w-4xl mx-auto bg-white dark:bg-[#1E1510] text-ink dark:text-[#FBF3E7] rounded-3xl p-6 md:p-8 border border-brand-caramel/20 dark:border-[#C08E5D]/20 shadow-md space-y-6">

      <!-- AI Prompt Preset Quick Chips -->
      <div class="space-y-2">
        <label class="block text-xs font-bold uppercase text-brand-choco dark:text-[#E2C08A]">Suggested Operational Queries:</label>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="chip in presetChips"
            :key="chip"
            class="px-3.5 py-1.5 rounded-xl bg-surface dark:bg-[#140D09] hover:bg-brand-tan/30 text-brand-choco dark:text-[#E2C08A] text-xs font-semibold border border-brand-caramel/30 dark:border-[#C08E5D]/30 transition-all text-left"
            @click="promptInput = chip; askAi()"
          >
            💬 {{ chip }}
          </button>
        </div>
      </div>

      <!-- Chat History Area -->
      <div class="bg-surface/60 dark:bg-[#140D09]/60 rounded-2xl p-4 md:p-6 border border-brand-caramel/20 dark:border-[#C08E5D]/20 space-y-4 max-h-[450px] overflow-y-auto">
        <div v-if="chatMessages.length === 0" class="text-center py-10 space-y-2 text-warm-gray dark:text-[#C5B4A4]">
          <div class="w-12 h-12 rounded-full bg-brand-choco dark:bg-[#C08E5D] text-brand-tan dark:text-[#1C1410] font-bold text-xl flex items-center justify-center mx-auto">AI</div>
          <p class="text-sm font-bold text-ink dark:text-[#FBF3E7]">Welcome to Antigravity AI Bakery Assistant!</p>
          <p class="text-xs">Select a suggested query above or type your operational question below.</p>
        </div>

        <div
          v-for="(msg, i) in chatMessages"
          :key="i"
          class="flex gap-3 text-xs md:text-sm"
          :class="msg.role === 'user' ? 'justify-end' : 'justify-start'"
        >
          <div
            v-if="msg.role === 'assistant'"
            class="w-8 h-8 rounded-full bg-brand-choco dark:bg-[#C08E5D] text-brand-tan dark:text-[#1C1410] font-extrabold flex items-center justify-center shrink-0 text-xs shadow-xs"
          >
            AI
          </div>

          <div
            class="max-w-[85%] rounded-2xl p-4 leading-relaxed shadow-xs"
            :class="msg.role === 'user' ? 'bg-brand-choco text-surface font-medium dark:bg-[#C08E5D] dark:text-[#1C1410]' : 'bg-white dark:bg-[#1E1510] text-ink dark:text-[#FBF3E7] border border-brand-caramel/20 dark:border-[#C08E5D]/20'"
          >
            <div class="whitespace-pre-line">{{ msg.text }}</div>
            <div v-if="msg.source" class="mt-2 text-[10px] opacity-70 border-t border-current/20 pt-1 text-right">
              Powered by {{ msg.source }}
            </div>
          </div>
        </div>

        <div v-if="thinking" class="flex items-center gap-2 text-xs text-warm-gray dark:text-[#C5B4A4] italic">
          <span class="animate-pulse">🤖 Antigravity AI is analyzing bakery inventory &amp; recipe metrics...</span>
        </div>
      </div>

      <!-- Input Form -->
      <form @submit.prevent="askAi" class="flex flex-col sm:flex-row gap-3">
        <input
          v-model="promptInput"
          type="text"
          placeholder="e.g. Which ingredients need reordering from suppliers?"
          class="flex-1 bg-white dark:bg-[#140D09] border border-brand-caramel/30 dark:border-[#C08E5D]/30 rounded-2xl px-4 py-3 text-sm text-ink dark:text-[#FBF3E7] placeholder-warm-gray dark:placeholder-[#C5B4A4]/50 focus:outline-none focus:border-brand-choco dark:focus:border-[#E2C08A]"
          :disabled="thinking"
        />
        <BaseButton type="submit" variant="primary" :loading="thinking">
          Ask AI Advisor
        </BaseButton>
      </form>

    </div>
  </div>
</template>

<script setup>
import { ref, inject } from 'vue'
import PageHeader from '@/components/ui/PageHeader.vue'
import BaseButton from '@/components/ui/BaseButton.vue'

const axios = inject('axios')

const promptInput = ref('')
const thinking = ref(false)

const chatMessages = ref([])

const presetChips = [
  'Which raw ingredients are low in stock and need reordering?',
  'What price should I charge for 65% gross margin?',
  'Summarize our top-selling pastries and sales performance.'
]

async function askAi() {
  if (!promptInput.value.trim() || thinking.value) return

  const userQuery = promptInput.value
  promptInput.value = ''

  chatMessages.value.push({ role: 'user', text: userQuery })
  thinking.value = true

  try {
    const { data } = await axios.post('/api/admin/ai/query', { prompt: userQuery })
    chatMessages.value.push({
      role: 'assistant',
      text: data.data.response,
      source: data.data.source
    })
  } catch (err) {
    chatMessages.value.push({
      role: 'assistant',
      text: 'Sorry, I encountered an issue connecting to the AI engine. Please try again.',
      source: 'System Alert'
    })
  } finally {
    thinking.value = false
  }
}
</script>

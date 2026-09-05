<template>
  <div>
    <!-- Hero -->
    <section class="bg-ink dark:bg-[#140D09] relative overflow-hidden">
      <div class="max-w-5xl mx-auto px-6 py-20 md:py-28 text-center text-surface dark:text-[#FBF3E7]">
        <span class="font-['Caveat'] text-brand-tan dark:text-[#E2C08A] text-2xl block mb-3">{{ content.hero_tagline }}</span>
        <h1 class="text-5xl md:text-6xl font-extrabold tracking-tight mb-6 whitespace-pre-line leading-tight">
          {{ content.hero_title }}
        </h1>
        <p class="text-surface/70 dark:text-[#C5B4A4] text-lg max-w-2xl mx-auto leading-relaxed">
          {{ content.hero_subtitle }}
        </p>
      </div>
      <!-- Decorative circles -->
      <div class="absolute top-0 right-0 w-64 h-64 bg-brand-tan/5 rounded-full -translate-y-1/2 translate-x-1/2 pointer-events-none" />
      <div class="absolute bottom-0 left-0 w-48 h-48 bg-brand-caramel/5 rounded-full translate-y-1/2 -translate-x-1/2 pointer-events-none" />
    </section>

    <!-- Timeline -->
    <section class="max-w-4xl mx-auto px-6 py-16">
      <div class="text-center mb-12">
        <span class="font-['Caveat'] text-brand-caramel dark:text-[#E2C08A] text-xl">{{ content.timeline_tagline }}</span>
        <h2 class="text-3xl font-bold text-ink dark:text-[#FBF3E7] mt-1">{{ content.timeline_title }}</h2>
      </div>
      <div class="relative">
        <div class="absolute left-1/2 -translate-x-0.5 h-full w-0.5 bg-brand-caramel/20 dark:bg-[#C08E5D]/20 hidden md:block" />
        <div
          v-for="(item, i) in content.timeline"
          :key="i"
          class="relative flex flex-col md:flex-row gap-6 mb-12"
          :class="i % 2 === 0 ? 'md:flex-row' : 'md:flex-row-reverse'"
        >
          <div class="md:w-1/2" :class="i % 2 === 0 ? 'md:text-right md:pr-12' : 'md:text-left md:pl-12'">
            <div class="bg-white dark:bg-[#1E1510] rounded-2xl p-6 shadow-sm border border-brand-caramel/20 dark:border-[#C08E5D]/20">
              <div class="text-3xl mb-3">{{ item.emoji }}</div>
              <div class="text-xs font-bold text-brand-caramel dark:text-[#E2C08A] uppercase tracking-wider mb-1">{{ item.year }}</div>
              <h3 class="text-lg font-bold text-ink dark:text-[#FBF3E7] mb-2">{{ item.title }}</h3>
              <p class="text-sm text-warm-gray dark:text-[#C5B4A4] leading-relaxed">{{ item.desc }}</p>
            </div>
          </div>
          <div class="hidden md:flex absolute left-1/2 -translate-x-1/2 w-10 h-10 bg-brand-choco dark:bg-[#C08E5D] rounded-full items-center justify-center text-white dark:text-[#1C1410] text-sm font-bold top-4 shadow-xs">
            {{ i + 1 }}
          </div>
          <div class="md:w-1/2" />
        </div>
      </div>
    </section>

    <!-- Values -->
    <section class="bg-surface dark:bg-[#140D09] py-16">
      <div class="max-w-5xl mx-auto px-6">
        <div class="text-center mb-12">
          <span class="font-['Caveat'] text-brand-caramel dark:text-[#E2C08A] text-xl">{{ content.values_tagline }}</span>
          <h2 class="text-3xl font-bold text-ink dark:text-[#FBF3E7] mt-1">{{ content.values_title }}</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div
            v-for="val in content.values"
            :key="val.title"
            class="bg-white dark:bg-[#1E1510] rounded-2xl p-8 text-center shadow-sm border border-brand-caramel/20 dark:border-[#C08E5D]/20 hover:shadow-md hover:-translate-y-1 transition-all duration-300"
          >
            <div class="text-4xl mb-4">{{ val.emoji }}</div>
            <h3 class="font-bold text-ink dark:text-[#FBF3E7] text-lg mb-2">{{ val.title }}</h3>
            <p class="text-sm text-warm-gray dark:text-[#C5B4A4] leading-relaxed">{{ val.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Dynamic Real Store Statistics -->
    <section class="bg-brand-choco dark:bg-[#1E1510] py-14 border-y border-brand-caramel/20 dark:border-[#C08E5D]/20">
      <div class="max-w-4xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
        <div v-for="stat in stats" :key="stat.label">
          <div class="font-['Caveat'] text-4xl font-bold text-brand-tan dark:text-[#E2C08A]">{{ stat.value }}</div>
          <div class="text-xs text-surface/70 dark:text-[#C5B4A4] font-medium mt-1">{{ stat.label }}</div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="max-w-2xl mx-auto px-6 py-20 text-center">
      <span class="font-['Caveat'] text-brand-caramel dark:text-[#E2C08A] text-2xl block mb-3">{{ content.cta_tagline }}</span>
      <h2 class="text-3xl font-bold text-ink dark:text-[#FBF3E7] mb-4">{{ content.cta_title }}</h2>
      <p class="text-warm-gray dark:text-[#C5B4A4] mb-8">{{ content.cta_subtitle }}</p>
      <RouterLink
        :to="content.cta_button_url || '/shop'"
        class="inline-flex items-center gap-2 bg-brand-choco dark:bg-[#C08E5D] text-surface dark:text-[#1C1410] px-8 py-4 rounded-2xl font-bold text-sm hover:bg-choco-600 transition-colors shadow-lg hover:shadow-xl"
      >
        {{ content.cta_button_text }}
      </RouterLink>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import { Sparkles, Heart, Coffee, Star, Home, Award } from 'lucide-vue-next'

const axios = inject('axios')

const content = ref({
  hero_tagline: 'our story',
  hero_title: 'Baked with Heart,\nserved with love',
  hero_subtitle: 'ABCDips & Treats began as a small home bakery with a simple dream: to share the joy of freshly baked, handcrafted pastries with every Filipino household.',
  timeline_tagline: 'the journey',
  timeline_title: 'The ABCDips Story',
  timeline: [
    { year: '2020', iconKey: 'home', title: 'Home Kitchen Beginnings', desc: 'ABCDips & Treats started in a small home kitchen, baking banana bread and cookies for friends and family.' },
    { year: '2021', iconKey: 'heart', title: 'First Online Orders', desc: 'Word spread and we started taking online orders through social media, quickly selling out every weekend.' },
    { year: '2023', iconKey: 'sparkles', title: 'Full Menu & Delivery', desc: 'Expanded to our full pastry menu including custom cakes, cheesecakes, and cinnamon rolls with city-wide delivery.' },
  ],
  values_tagline: 'what drives us',
  values_title: 'Our Core Values',
  values: [
    { iconKey: 'coffee', title: 'Quality Ingredients', desc: 'We use only real creamery butter, imported Belgian chocolate, and fresh farm eggs. No shortcuts, ever.' },
    { iconKey: 'heart', title: 'Made with Love', desc: 'Every pastry is handcrafted in small batches by our dedicated bakers who pour passion into every bite.' },
    { iconKey: 'star', title: 'Community First', desc: 'We believe in building relationships, supporting local suppliers, and making people smile one pastry at a time.' },
  ],
  cta_tagline: 'ready to indulge?',
  cta_title: 'Order Your Favorites Today',
  cta_subtitle: 'Same-day delivery available in Cavite. Fresh from our oven to your door.',
  cta_button_text: 'Browse Full Menu →',
  cta_button_url: '/shop',
})

const stats = ref([
  { value: '...', label: 'Happy Customers' },
  { value: '...', label: 'Signature Recipes' },
  { value: '100%', label: 'Real Butter' },
  { value: '...', label: 'Average Rating' },
])

async function fetchAboutContent() {
  try {
    const { data } = await axios.get('/api/about-content')
    if (data) {
      content.value = { ...content.value, ...data }
    }
  } catch (err) {
    console.error('Failed to load about content settings', err)
  }
}

async function fetchStats() {
  try {
    const { data } = await axios.get('/api/about-stats')
    stats.value = [
      { value: `${data.happy_customers}${data.happy_customers > 0 ? '+' : ''}`, label: 'Happy Customers' },
      { value: `${data.signature_recipes}${data.signature_recipes > 0 ? '+' : ''}`, label: 'Signature Recipes' },
      { value: '100%', label: 'Real Butter' },
      { value: `${data.average_rating} / 5.0`, label: 'Average Rating' },
    ]
  } catch (err) {
    console.error('Failed to load live about stats', err)
  }
}

onMounted(() => {
  fetchAboutContent()
  fetchStats()
})
</script>

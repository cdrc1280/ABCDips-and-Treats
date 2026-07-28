<template>
  <div>
    <!-- Hero -->
    <section class="bg-[#1C1410] relative overflow-hidden">
      <div class="max-w-5xl mx-auto px-6 py-20 md:py-28 text-center text-[#FBF3E7]">
        <span class="font-['Caveat'] text-[#D9A876] text-2xl block mb-3">our story</span>
        <h1 class="text-5xl md:text-6xl font-extrabold tracking-tight mb-6">
          Baked with Heart,<br />
          <span class="font-['Caveat'] font-normal text-[#D9A876]">served with love</span>
        </h1>
        <p class="text-[#FBF3E7]/70 text-lg max-w-2xl mx-auto leading-relaxed">
          ABCDips &amp; Treats began as a small home bakery with a simple dream: to share the joy of freshly baked, handcrafted pastries with every Filipino household.
        </p>
      </div>
      <!-- Decorative circles -->
      <div class="absolute top-0 right-0 w-64 h-64 bg-[#D9A876]/5 rounded-full -translate-y-1/2 translate-x-1/2" />
      <div class="absolute bottom-0 left-0 w-48 h-48 bg-[#C08E5D]/5 rounded-full translate-y-1/2 -translate-x-1/2" />
    </section>

    <!-- Timeline -->
    <section class="max-w-4xl mx-auto px-6 py-16">
      <div class="text-center mb-12">
        <span class="font-['Caveat'] text-[#C08E5D] text-xl">the journey</span>
        <h2 class="text-3xl font-bold text-[#1C1410] mt-1">The ABCDips Story</h2>
      </div>
      <div class="relative">
        <div class="absolute left-1/2 -translate-x-0.5 h-full w-0.5 bg-[#C08E5D]/20 hidden md:block" />
        <div
          v-for="(item, i) in timeline"
          :key="i"
          class="relative flex flex-col md:flex-row gap-6 mb-12"
          :class="i % 2 === 0 ? 'md:flex-row' : 'md:flex-row-reverse'"
        >
          <div class="md:w-1/2" :class="i % 2 === 0 ? 'md:text-right md:pr-12' : 'md:text-left md:pl-12'">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#C08E5D]/20">
              <div class="text-3xl mb-3">{{ item.emoji }}</div>
              <div class="text-xs font-bold text-[#C08E5D] uppercase tracking-wider mb-1">{{ item.year }}</div>
              <h3 class="text-lg font-bold text-[#1C1410] mb-2">{{ item.title }}</h3>
              <p class="text-sm text-[#8C7A68] leading-relaxed">{{ item.desc }}</p>
            </div>
          </div>
          <div class="hidden md:flex absolute left-1/2 -translate-x-1/2 w-10 h-10 bg-[#5C3A22] rounded-full items-center justify-center text-white text-sm font-bold top-4">
            {{ i + 1 }}
          </div>
          <div class="md:w-1/2" />
        </div>
      </div>
    </section>

    <!-- Values -->
    <section class="bg-[#FBF3E7] py-16">
      <div class="max-w-5xl mx-auto px-6">
        <div class="text-center mb-12">
          <span class="font-['Caveat'] text-[#C08E5D] text-xl">what drives us</span>
          <h2 class="text-3xl font-bold text-[#1C1410] mt-1">Our Core Values</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div
            v-for="val in values"
            :key="val.title"
            class="bg-white rounded-2xl p-8 text-center shadow-sm border border-[#C08E5D]/20 hover:shadow-md hover:-translate-y-1 transition-all duration-300"
          >
            <div class="text-4xl mb-4">{{ val.emoji }}</div>
            <h3 class="font-bold text-[#1C1410] text-lg mb-2">{{ val.title }}</h3>
            <p class="text-sm text-[#8C7A68] leading-relaxed">{{ val.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Dynamic Real Store Statistics -->
    <section class="bg-[#5C3A22] py-14">
      <div class="max-w-4xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
        <div v-for="stat in stats" :key="stat.label">
          <div class="font-['Caveat'] text-4xl font-bold text-[#D9A876]">{{ stat.value }}</div>
          <div class="text-xs text-[#FBF3E7]/70 font-medium mt-1">{{ stat.label }}</div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="max-w-2xl mx-auto px-6 py-20 text-center">
      <span class="font-['Caveat'] text-[#C08E5D] text-2xl block mb-3">ready to indulge?</span>
      <h2 class="text-3xl font-bold text-[#1C1410] mb-4">Order Your Favorites Today</h2>
      <p class="text-[#8C7A68] mb-8">Same-day delivery available in Cavite. Fresh from our oven to your door.</p>
      <RouterLink
        to="/shop"
        class="inline-flex items-center gap-2 bg-[#5C3A22] text-[#FBF3E7] px-8 py-4 rounded-2xl font-bold text-sm hover:bg-[#4A2D1A] transition-colors shadow-lg hover:shadow-xl"
      >
        Browse Full Menu →
      </RouterLink>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'

const axios = inject('axios')

const timeline = [
  { year: '2020', emoji: '🏠', title: 'Home Kitchen Beginnings', desc: 'ABCDips & Treats started in a small home kitchen, baking banana bread and cookies for friends and family.' },
  { year: '2021', emoji: '❤️', title: 'First Online Orders', desc: 'Word spread and we started taking online orders through social media, quickly selling out every weekend.' },
  { year: '2023', emoji: '🥰', title: 'Full Menu & Delivery', desc: 'Expanded to our full pastry menu including custom cakes, cheesecakes, and cinnamon rolls with city-wide delivery.' },
]

const values = [
  { emoji: '🫖', title: 'Quality Ingredients', desc: 'We use only real creamery butter, imported Belgian chocolate, and fresh farm eggs. No shortcuts, ever.' },
  { emoji: '❤️', title: 'Made with Love', desc: 'Every pastry is handcrafted in small batches by our dedicated bakers who pour passion into every bite.' },
  { emoji: '🌟', title: 'Community First', desc: 'We believe in building relationships, supporting local suppliers, and making people smile one pastry at a time.' },
]

const stats = ref([
  { value: '...', label: 'Happy Customers' },
  { value: '...', label: 'Signature Recipes' },
  { value: '100%', label: 'Real Butter' },
  { value: '...', label: 'Average Rating' },
])

async function fetchStats() {
  try {
    const { data } = await axios.get('/api/about-stats')
    stats.value = [
      { value: `${data.happy_customers}${data.happy_customers > 0 ? '+' : ''}`, label: 'Happy Customers' },
      { value: `${data.signature_recipes}${data.signature_recipes > 0 ? '+' : ''}`, label: 'Signature Recipes' },
      { value: '100%', label: 'Real Butter' },
      { value: `${data.average_rating}★`, label: 'Average Rating' },
    ]
  } catch (err) {
    console.error('Failed to load live about stats', err)
  }
}

onMounted(() => {
  fetchStats()
})
</script>

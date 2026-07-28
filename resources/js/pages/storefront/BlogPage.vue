<template>
  <div class="max-w-6xl mx-auto px-6 py-16">
    <div class="text-center mb-12">
      <span class="font-['Caveat'] text-[#C08E5D] text-2xl block mb-2">fresh from the kitchen</span>
      <h1 class="text-4xl font-extrabold text-[#1C1410] tracking-tight">The ABCDips Journal</h1>
      <p class="text-[#8C7A68] mt-3">Recipes, stories, and sweet inspiration from our bakery</p>
    </div>

    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="n in 6" :key="n" class="bg-white rounded-2xl overflow-hidden border border-[#C08E5D]/20">
        <div class="h-44 bg-[#D9A876]/20 animate-pulse" />
        <div class="p-5 space-y-3">
          <div class="h-3 bg-[#D9A876]/20 rounded animate-pulse w-16" />
          <div class="h-5 bg-[#D9A876]/20 rounded animate-pulse" />
          <div class="h-3 bg-[#D9A876]/20 rounded animate-pulse w-3/4" />
        </div>
      </div>
    </div>

    <div v-else-if="posts.length === 0" class="text-center py-20">
      <div class="text-5xl mb-4">📝</div>
      <h2 class="text-xl font-bold text-[#1C1410] mb-2">No posts yet</h2>
      <p class="text-[#8C7A68]">Our bakers are crafting some delicious content. Check back soon!</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <RouterLink
        v-for="post in posts"
        :key="post.id"
        :to="`/blog/${post.slug}`"
        class="group bg-white rounded-2xl overflow-hidden border border-[#C08E5D]/20 hover:shadow-lg hover:-translate-y-1 transition-all duration-300"
      >
        <div class="h-44 bg-[#D9A876]/20 flex items-center justify-center overflow-hidden">
          <img v-if="post.cover_image" :src="post.cover_image" :alt="post.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
          <span v-else class="text-4xl">🧁</span>
        </div>
        <div class="p-5">
          <span class="text-[10px] font-bold text-[#C08E5D] uppercase tracking-wider bg-[#D9A876]/20 px-2 py-0.5 rounded-full">{{ post.category }}</span>
          <h2 class="font-bold text-[#1C1410] mt-2 mb-2 line-clamp-2 group-hover:text-[#5C3A22] transition-colors">{{ post.title }}</h2>
          <p class="text-xs text-[#8C7A68] line-clamp-2 mb-3">{{ post.excerpt }}</p>
          <div class="flex items-center justify-between text-xs text-[#8C7A68]">
            <span>{{ post.author?.name || 'ABCDips Team' }}</span>
            <span>{{ formatDate(post.published_at) }}</span>
          </div>
        </div>
      </RouterLink>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
const axios = inject('axios')
const posts = ref([])
const loading = ref(true)
async function fetchPosts() {
  try {
    const { data } = await axios.get('/api/blog/posts')
    posts.value = data.data || []
  } catch { posts.value = [] } finally { loading.value = false }
}
function formatDate(d) {
  if (!d) return ''
  return new Date(d).toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' })
}
onMounted(() => fetchPosts())
</script>

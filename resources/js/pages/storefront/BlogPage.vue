<template>
  <div class="max-w-6xl mx-auto px-6 py-16">
    <div class="text-center mb-12">
      <span class="font-['Caveat'] text-brand-caramel text-2xl block mb-2">fresh from the kitchen</span>
      <h1 class="text-4xl font-extrabold text-ink tracking-tight">The ABCDips Journal &amp; Vlog</h1>
      <p class="text-warm-gray mt-3">Recipes, baking guides, custom cake vlogs, and sweet inspiration</p>
    </div>

    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <div v-for="n in 6" :key="n" class="bg-white rounded-3xl overflow-hidden border border-brand-caramel/20 shadow-sm">
        <div class="aspect-video bg-brand-tan/20 animate-pulse" />
        <div class="p-6 space-y-3">
          <div class="h-3 bg-brand-tan/20 rounded animate-pulse w-20" />
          <div class="h-5 bg-brand-tan/20 rounded animate-pulse" />
          <div class="h-3 bg-brand-tan/20 rounded animate-pulse w-3/4" />
        </div>
      </div>
    </div>

    <div v-else-if="posts.length === 0" class="text-center py-20">
      <div class="text-5xl mb-4">📝</div>
      <h2 class="text-xl font-bold text-ink mb-2">No journal posts yet</h2>
      <p class="text-warm-gray">Our bakers are crafting new recipes and vlogs. Check back soon!</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <RouterLink
        v-for="post in posts"
        :key="post.id"
        :to="`/blog/${post.slug}`"
        class="group bg-white rounded-3xl overflow-hidden border border-brand-caramel/20 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between"
      >
        <div>
          <!-- Aspect-video Image Container -->
          <div class="relative aspect-video w-full overflow-hidden bg-surface">
            <img
              :src="post.cover_image || '/images/blog-banana-bread.jpg'"
              :alt="post.title"
              class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500"
              loading="lazy"
              @error="handleImgError"
            />
            <div class="absolute top-3 left-3">
              <span class="text-[10px] font-extrabold text-brand-choco uppercase tracking-wider bg-white/90 backdrop-blur-md px-3 py-1 rounded-full shadow-sm border border-brand-caramel/20">
                {{ post.category || 'Bakery Story' }}
              </span>
            </div>
          </div>

          <div class="p-6">
            <h2 class="font-bold text-ink text-lg leading-snug mb-2.5 group-hover:text-brand-choco transition-colors line-clamp-2">
              {{ post.title }}
            </h2>
            <p class="text-xs text-warm-gray line-clamp-2 leading-relaxed">
              {{ post.excerpt }}
            </p>
          </div>
        </div>

        <div class="px-6 pb-6 pt-2 border-t border-brand-caramel/10 flex items-center justify-between text-xs text-warm-gray">
          <span class="font-semibold text-brand-choco">{{ post.author?.name || 'ABCDips Team' }}</span>
          <span>{{ formatDate(post.published_at) }}</span>
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
  } catch {
    posts.value = []
  } finally {
    loading.value = false
  }
}

function handleImgError(e) {
  e.target.src = '/images/blog-banana-bread.jpg'
}

function formatDate(d) {
  if (!d) return ''
  return new Date(d).toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' })
}

onMounted(() => fetchPosts())
</script>

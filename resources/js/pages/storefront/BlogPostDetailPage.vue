<template>
  <div class="page-container py-10 md:py-16">
    <div v-if="loading" class="max-w-3xl mx-auto space-y-6">
      <SkeletonBlock height="400px" radius="1.5rem" />
      <SkeletonBlock height="200px" radius="1.5rem" />
    </div>

    <div v-else-if="!post" class="max-w-lg mx-auto text-center py-16">
      <EmptyState title="Article Not Found" description="The blog post or vlog update you requested could not be found.">
        <template #action>
          <RouterLink to="/blog"><BaseButton variant="primary">Return to Journal</BaseButton></RouterLink>
        </template>
      </EmptyState>
    </div>

    <article v-else class="max-w-4xl mx-auto space-y-8">
      <!-- Top Navigation -->
      <div class="flex items-center justify-between">
        <RouterLink to="/blog" class="text-xs font-bold text-[#5C3A22] hover:underline flex items-center gap-1">
          ← Back to Bakery Journal &amp; Vlog
        </RouterLink>
        <span class="text-[11px] font-extrabold text-[#C08E5D] uppercase tracking-wider bg-[#FBF3E7] px-3 py-1 rounded-full border border-[#C08E5D]/20">
          {{ post.category || 'Bakery Story' }}
        </span>
      </div>

      <!-- Post Header -->
      <div class="space-y-4 text-center max-w-3xl mx-auto">
        <h1 class="text-3xl md:text-5xl font-extrabold text-[#1C1410] leading-tight">
          {{ post.title }}
        </h1>
        <div class="flex items-center justify-center gap-4 text-xs text-[#8C7A68]">
          <span>By <strong class="text-[#1C1410]">{{ post.author?.name || 'ABCDips Team' }}</strong></span>
          <span>•</span>
          <span>{{ formatDate(post.published_at) }}</span>
        </div>
      </div>

      <!-- Cover Image Container -->
      <div class="relative w-full aspect-video max-h-[460px] rounded-3xl overflow-hidden shadow-lg border border-[#C08E5D]/20 bg-[#FBF3E7]">
        <img
          :src="post.cover_image || '/images/blog-banana-bread.jpg'"
          :alt="post.title"
          class="w-full h-full object-cover object-center"
          @error="handleImgError"
        />
      </div>

      <!-- Article Content Box -->
      <div class="bg-white rounded-3xl p-8 md:p-12 border border-[#C08E5D]/20 shadow-sm space-y-6 text-[#1C1410] text-base leading-relaxed max-w-3xl mx-auto">
        <p class="text-lg font-semibold text-[#5C3A22] leading-relaxed italic border-l-4 border-[#C08E5D] pl-4">
          {{ post.excerpt }}
        </p>
        <div class="whitespace-pre-line space-y-4 text-[#1C1410]">
          {{ post.content }}
        </div>
      </div>

      <!-- Footer CTA -->
      <div class="bg-[#FBF3E7] rounded-3xl p-8 border border-[#C08E5D]/20 text-center space-y-4 max-w-3xl mx-auto">
        <span class="script-accent text-[#C08E5D] text-xl block">baked fresh daily</span>
        <h3 class="text-2xl font-bold text-[#1C1410]">Craving freshly baked treats?</h3>
        <p class="text-xs text-[#8C7A68] max-w-md mx-auto">
          Explore our signature banana bread, handcrafted cookies, and custom cake options delivered warm to your doorstep.
        </p>
        <div class="pt-2">
          <RouterLink to="/shop"><BaseButton variant="primary">Browse Bakery Menu →</BaseButton></RouterLink>
        </div>
      </div>
    </article>
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import { useRoute } from 'vue-router'
import BaseButton from '@/components/ui/BaseButton.vue'
import SkeletonBlock from '@/components/ui/SkeletonBlock.vue'
import EmptyState from '@/components/ui/EmptyState.vue'

const axios = inject('axios')
const route = useRoute()

const post = ref(null)
const loading = ref(true)

async function fetchPost() {
  loading.value = true
  try {
    const slug = route.params.slug
    const { data } = await axios.get(`/api/blog/posts/${slug}`)
    post.value = data.data
  } catch (err) {
    console.error('Failed to load blog post', err)
  } finally {
    loading.value = false
  }
}

function handleImgError(e) {
  e.target.src = '/images/blog-banana-bread.jpg'
}

function formatDate(d) {
  if (!d) return ''
  return new Date(d).toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' })
}

onMounted(() => fetchPost())
</script>

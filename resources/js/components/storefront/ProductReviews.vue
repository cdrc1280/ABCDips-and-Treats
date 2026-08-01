<template>
  <div id="reviews" class="space-y-10">
    <!-- Header & Summary Row -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border-b border-[#C08E5D]/20 pb-8">
      <div>
        <span class="script-accent text-[#C08E5D] text-lg">customer feedback</span>
        <h3 class="text-2xl md:text-3xl font-extrabold text-[#1C1410] tracking-tight mt-0.5">
          Pastry &amp; Taste Reviews
        </h3>
        <p class="text-xs text-[#8C7A68]">Real ratings and reviews from verified ABCDips &amp; Treats customers.</p>
      </div>

      <BaseButton variant="primary" size="lg" @click="handleToggleForm">
        {{ showForm ? 'Cancel Review' : '✍️ Write a Review' }}
      </BaseButton>
    </div>

    <!-- Rating Breakdown Summary Stats Banner -->
    <div v-if="reviews.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-[#FBF3E7]/60 p-6 md:p-8 rounded-3xl border border-[#C08E5D]/25 items-center">
      <!-- Big Score -->
      <div class="text-center border-b md:border-b-0 md:border-r border-[#C08E5D]/20 pb-4 md:pb-0 md:pr-6 space-y-1">
        <div class="text-5xl font-black text-[#5C3A22]">{{ averageRating }}</div>
        <div class="flex justify-center text-amber-500 text-lg">
          <span v-for="s in 5" :key="s">{{ s <= Math.round(averageRating) ? '⭐' : '☆' }}</span>
        </div>
        <div class="text-xs font-semibold text-[#8C7A68]">Based on {{ reviews.length }} reviews</div>
      </div>

      <!-- Rating Bar Breakdown -->
      <div class="space-y-1.5 md:col-span-2">
        <div v-for="star in [5, 4, 3, 2, 1]" :key="star" class="flex items-center gap-3 text-xs">
          <span class="font-bold text-[#5C3A22] w-12">{{ star }} Stars</span>
          <div class="flex-1 bg-[#D9A876]/20 h-2.5 rounded-full overflow-hidden">
            <div
              class="bg-amber-400 h-full rounded-full transition-all duration-500"
              :style="{ width: getRatingPercentage(star) + '%' }"
            />
          </div>
          <span class="text-[#8C7A68] w-8 text-right font-semibold">{{ getRatingCount(star) }}</span>
        </div>
      </div>
    </div>

    <!-- Review Submission Form -->
    <Transition name="fade">
      <form v-if="showForm" @submit.prevent="submitReview" class="bg-[#FBF3E7] p-6 md:p-8 rounded-3xl border border-[#C08E5D]/40 shadow-md space-y-5">
        <div>
          <h4 class="font-extrabold text-xl text-[#1C1410]">Share Your Tasting Experience</h4>
          <p class="text-xs text-[#8C7A68]">How was the flavor, texture, and delivery of this pastry?</p>
        </div>

        <!-- Quick Pre-made Review Templates -->
        <div class="space-y-1.5 bg-white/70 p-4 rounded-2xl border border-[#C08E5D]/20">
          <label class="block text-xs font-bold uppercase text-[#5C3A22]">⚡ Quick Pre-made Review Messages (Click to instant fill)</label>
          <div class="flex flex-wrap gap-2 pt-1">
            <button
              v-for="(tpl, idx) in productReviewTemplates"
              :key="idx"
              type="button"
              class="text-xs bg-white border border-[#C08E5D]/30 hover:border-[#5C3A22] px-3 py-1.5 rounded-xl text-[#5C3A22] font-semibold transition-all hover:bg-[#D9A876]/20 shadow-2xs"
              @click="applyTemplate(tpl)"
            >
              {{ tpl.chipLabel }}
            </button>
          </div>
        </div>

        <!-- Star Rating Interactive Selector -->
        <div class="space-y-1.5">
          <label class="block text-xs font-bold uppercase text-[#5C3A22]">Overall Star Rating</label>
          <div class="flex items-center gap-3">
            <div class="flex items-center gap-1">
              <button
                v-for="star in 5"
                :key="star"
                type="button"
                class="text-3xl transition-transform hover:scale-125 focus:outline-none"
                @click="form.rating = star"
              >
                {{ star <= form.rating ? '⭐' : '☆' }}
              </button>
            </div>
            <span class="text-xs font-extrabold text-[#5C3A22] bg-white px-3 py-1 rounded-full border border-[#C08E5D]/30">
              {{ form.rating }} / 5 Stars
            </span>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <BaseInput v-model="form.reviewer_name" label="Your Name" placeholder="e.g. Maria S." :disabled="form.is_anonymous" :required="!form.is_anonymous" />
          <BaseInput v-model="form.reviewer_email" type="email" label="Your Email" placeholder="maria@example.com" required />
        </div>

        <label class="flex items-center gap-2 text-xs font-bold text-[#5C3A22] cursor-pointer py-1">
          <input type="checkbox" v-model="form.is_anonymous" class="rounded text-[#5C3A22] focus:ring-[#5C3A22] w-4 h-4" />
          <span>Post review anonymously (Your name will appear as "Anonymous" publicly)</span>
        </label>

        <BaseInput v-model="form.title" label="Headline / Review Title" placeholder="e.g. Incredibly moist and perfectly sweetened!" />

        <BaseTextarea v-model="form.comment" label="Detailed Review" placeholder="Tell us what you loved about the pastry flavor, freshness, and packaging..." rows="4" required />

        <div class="flex justify-end gap-3 pt-2">
          <BaseButton type="button" variant="ghost" @click="showForm = false">Cancel</BaseButton>
          <BaseButton type="submit" variant="primary" :loading="submitting">Submit Review</BaseButton>
        </div>
      </form>
    </Transition>

    <!-- Rating Filters with Counts -->
    <div v-if="reviews.length > 0" class="flex items-center justify-between gap-4 border-b border-[#C08E5D]/15 pb-4">
      <div class="text-xs font-bold text-[#8C7A68]">Filter by score:</div>
      <div class="flex items-center gap-1.5 overflow-x-auto">
        <button
          v-for="filter in [0, 5, 4, 3, 2, 1]"
          :key="filter"
          type="button"
          class="px-3 py-1 rounded-xl text-xs font-bold transition-all flex-shrink-0"
          :class="selectedFilter === filter ? 'bg-[#5C3A22] text-white shadow-xs' : 'bg-white text-[#5C3A22] border border-[#C08E5D]/20 hover:bg-[#FBF3E7]'"
          @click="selectedFilter = filter"
        >
          {{ filter === 0 ? `All (${reviews.length})` : `${filter}★ (${getRatingCount(filter)})` }}
        </button>
      </div>
    </div>

    <!-- Reviews List -->
    <div v-if="loading" class="space-y-4">
      <SkeletonRow v-for="n in 3" :key="n" />
    </div>

    <div v-else-if="filteredReviews.length === 0" class="text-center py-12 bg-[#FBF3E7]/40 rounded-3xl border border-dashed border-[#C08E5D]/30 p-8">
      <p class="text-base font-bold text-[#1C1410] mb-1">No reviews for this filter yet.</p>
      <p class="text-xs text-[#8C7A68] mb-4">Be the first to share your review for this pastry!</p>
      <BaseButton size="sm" variant="outline" @click="handleToggleForm">✍️ Write a Review</BaseButton>
    </div>

    <div v-else class="space-y-4">
      <div
        v-for="review in filteredReviews"
        :key="review.id"
        class="bg-white p-6 rounded-3xl border border-[#C08E5D]/20 shadow-sm hover:shadow-md transition-all space-y-3"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#5C3A22] text-[#FBF3E7] font-bold text-sm flex items-center justify-center">
              {{ review.reviewer_name?.charAt(0).toUpperCase() || 'A' }}
            </div>
            <div>
              <div class="font-extrabold text-sm text-[#1C1410] flex items-center gap-2">
                {{ review.reviewer_name }}
                <BaseBadge v-if="review.is_verified_buyer" variant="success" size="sm">Verified Buyer</BaseBadge>
              </div>
              <div class="text-[11px] text-[#8C7A68]">Reviewed on {{ new Date(review.created_at).toLocaleDateString() }}</div>
            </div>
          </div>

          <!-- Rating Stars -->
          <div class="flex items-center gap-1 text-sm text-amber-500">
            <span v-for="s in review.rating" :key="s">⭐</span>
          </div>
        </div>

        <h5 v-if="review.title" class="font-extrabold text-base text-[#1C1410] pt-1">{{ review.title }}</h5>

        <p class="text-xs text-[#8C7A68] leading-relaxed">{{ review.comment }}</p>

        <!-- Helpful Vote Button -->
        <div class="pt-3 border-t border-[#C08E5D]/15 flex items-center justify-between text-xs text-[#8C7A68]">
          <span>Was this review helpful?</span>
          <button
            class="px-3.5 py-1.5 rounded-xl bg-[#FBF3E7] hover:bg-[#D9A876]/30 text-[#5C3A22] font-semibold flex items-center gap-1.5 transition-all"
            @click="voteHelpful(review.id)"
          >
            👍 Helpful ({{ review.helpful_votes }})
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, inject } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToast } from '@/composables/useToast'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseTextarea from '@/components/ui/BaseTextarea.vue'
import BaseBadge from '@/components/ui/BaseBadge.vue'
import SkeletonRow from '@/components/ui/SkeletonRow.vue'

const props = defineProps({
  productId: { type: Number, required: true }
})

const axios = inject('axios')
const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const toast = useToast()

const reviews = ref([])
const loading = ref(true)
const showForm = ref(false)
const submitting = ref(false)
const selectedFilter = ref(0)

const form = ref({
  rating: 5,
  reviewer_name: '',
  reviewer_email: '',
  title: '',
  comment: '',
  is_anonymous: false
})

const productReviewTemplates = [
  {
    chipLabel: '😋 Moist & Perfectly Sweetened',
    rating: 5,
    title: 'Incredibly moist & perfectly sweetened!',
    comment: 'Super fresh and soft! The texture and balance of flavor are top-notch. Pairs amazingly with morning coffee!'
  },
  {
    chipLabel: '📦 Warm Delivery & Great Packaging',
    rating: 5,
    title: 'Arrived oven-fresh and beautifully packaged!',
    comment: 'The delivery was fast, and the pastry was sealed tight and fresh. Everyone in our family loved it!'
  },
  {
    chipLabel: '⭐ Best Pastry Quality',
    rating: 5,
    title: '10/10 Quality — Highly Recommended!',
    comment: 'Easily one of the best pastries I have tasted in town. Premium ingredients and rich butter aroma. Will order again!'
  },
  {
    chipLabel: '🎉 Family Gathering Favorite',
    rating: 5,
    title: 'Huge hit at our family gathering!',
    comment: 'Served this treat for dessert during our weekend family dinner and it was finished in minutes. 100% reordering!'
  }
]

function applyTemplate(template) {
  form.value.rating = template.rating
  form.value.title = template.title
  form.value.comment = template.comment
  toast.info('Template review loaded! Feel free to customize.', 'Pre-made Message Set')
}

function populateUserData() {
  if (authStore.user) {
    if (!form.value.reviewer_name) form.value.reviewer_name = authStore.user.name || ''
    if (!form.value.reviewer_email) form.value.reviewer_email = authStore.user.email || ''
  }
}

watch(() => authStore.user, populateUserData, { immediate: true })

function handleToggleForm() {
  if (!authStore.isAuthenticated) {
    toast.warning('Please sign in to write a review.', 'Sign In Required')
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }
  populateUserData()
  showForm.value = !showForm.value
}

const averageRating = computed(() => {
  if (reviews.value.length === 0) return '5.0'
  const sum = reviews.value.reduce((acc, r) => acc + r.rating, 0)
  return (sum / reviews.value.length).toFixed(1)
})

function getRatingCount(star) {
  return reviews.value.filter(r => r.rating === star).length
}

function getRatingPercentage(star) {
  if (reviews.value.length === 0) return 0
  return Math.round((getRatingCount(star) / reviews.value.length) * 100)
}

const filteredReviews = computed(() => {
  if (selectedFilter.value === 0) return reviews.value
  return reviews.value.filter(r => r.rating === selectedFilter.value)
})

async function fetchReviews() {
  loading.value = true
  try {
    const { data } = await axios.get(`/api/products/${props.productId}/reviews`)
    reviews.value = data.data
  } catch (err) {
    console.error('Failed to load reviews', err)
  } finally {
    loading.value = false
  }
}

async function submitReview() {
  if (!authStore.isAuthenticated) {
    toast.warning('Please sign in to write a review.', 'Sign In Required')
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }

  submitting.value = true
  try {
    await axios.post(`/api/products/${props.productId}/reviews`, {
      ...form.value,
      product_id: props.productId
    })
    toast.success('Thank you! Your review has been submitted and posted.', 'Review Published')
    showForm.value = false
    form.value = { rating: 5, reviewer_name: authStore.user?.name || '', reviewer_email: authStore.user?.email || '', title: '', comment: '' }
    fetchReviews()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Failed to submit review. Please fill out all fields.', 'Submission Error')
  } finally {
    submitting.value = false
  }
}

async function voteHelpful(reviewId) {
  try {
    const { data } = await axios.post(`/api/reviews/${reviewId}/vote`)
    toast.success(data.message)
    fetchReviews()
  } catch (err) {
    toast.info(err.response?.data?.message || 'Already voted.')
  }
}

onMounted(() => fetchReviews())
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>

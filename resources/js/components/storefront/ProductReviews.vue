<template>
  <div id="reviews" class="space-y-10">
    <!-- Header & Summary Row -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border-b border-brand-caramel/20 pb-8">
      <div>
        <span class="script-accent text-brand-caramel text-lg">customer feedback</span>
        <h3 class="text-2xl md:text-3xl font-extrabold text-ink tracking-tight mt-0.5">
          Pastry &amp; Taste Reviews
        </h3>
        <p class="text-xs text-warm-gray">Real ratings and reviews from verified ABCDips &amp; Treats customers.</p>
      </div>

      <BaseButton variant="primary" size="lg" :v-tooltip="showForm ? 'Close review form' : 'Share your rating & feedback for this pastry'" @click="handleToggleForm">
        {{ showForm ? 'Cancel Review' : '✍️ Write a Review' }}
      </BaseButton>
    </div>

    <!-- Rating Breakdown Summary Stats Banner -->
    <div v-if="reviews.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-surface/60 p-6 md:p-8 rounded-3xl border border-brand-caramel/25 items-center">
      <!-- Big Score -->
      <div class="text-center border-b md:border-b-0 md:border-r border-brand-caramel/20 pb-4 md:pb-0 md:pr-6 space-y-1">
        <div class="text-5xl font-black text-brand-choco">{{ averageRating }}</div>
        <div class="flex justify-center text-amber-500 text-lg">
          <span v-for="s in 5" :key="s">{{ s <= Math.round(averageRating) ? '⭐' : '☆' }}</span>
        </div>
        <div class="text-xs font-semibold text-warm-gray">Based on {{ reviews.length }} reviews</div>
      </div>

      <!-- Rating Bar Breakdown -->
      <div class="space-y-1.5 md:col-span-2">
        <div v-for="star in [5, 4, 3, 2, 1]" :key="star" class="flex items-center gap-3 text-xs">
          <span class="font-bold text-brand-choco w-12">{{ star }} Stars</span>
          <div class="flex-1 bg-brand-tan/20 h-2.5 rounded-full overflow-hidden">
            <div
              class="bg-amber-400 h-full rounded-full transition-all duration-500"
              :style="{ width: getRatingPercentage(star) + '%' }"
            />
          </div>
          <span class="text-warm-gray w-8 text-right font-semibold">{{ getRatingCount(star) }}</span>
        </div>
      </div>
    </div>

    <!-- Review Submission Form -->
    <Transition name="fade">
      <form v-if="showForm" @submit.prevent="submitReview" class="bg-surface p-6 md:p-8 rounded-3xl border border-brand-caramel/40 shadow-md space-y-5">
        <div>
          <h4 class="font-extrabold text-xl text-ink">Share Your Tasting Experience</h4>
          <p class="text-xs text-warm-gray">How was the flavor, texture, and delivery of this pastry?</p>
        </div>

        <!-- Quick Pre-made Review Templates -->
        <div class="space-y-1.5 bg-white/70 dark:bg-[#1A120C]/70 p-4 rounded-2xl border border-brand-caramel/20 dark:border-[#C08E5D]/20">
          <label class="block text-xs font-bold uppercase text-brand-choco dark:text-[#E2C08A]">⚡ Quick Pre-made Review Messages (Click to instant fill)</label>
          <div class="flex flex-wrap gap-2 pt-1">
            <button
              v-for="(tpl, idx) in productReviewTemplates"
              :key="idx"
              type="button"
              v-tooltip="`Click to fill: '${tpl.chipLabel}'`"
              class="text-xs bg-white dark:bg-[#1E1510] border border-brand-caramel/30 dark:border-[#C08E5D]/30 hover:border-brand-choco text-brand-choco dark:text-[#E2C08A] font-semibold transition-all hover:bg-brand-tan/20 shadow-2xs"
              @click="applyTemplate(tpl)"
            >
              {{ tpl.chipLabel }}
            </button>
          </div>
        </div>

        <!-- Star Rating Interactive Selector -->
        <div class="space-y-1.5">
          <label class="block text-xs font-bold uppercase text-brand-choco dark:text-[#E2C08A]">Overall Star Rating</label>
          <div class="flex items-center gap-3">
            <div class="flex items-center gap-1">
              <button
                v-for="star in 5"
                :key="star"
                type="button"
                v-tooltip="`Rate ${star} star${star > 1 ? 's' : ''}`"
                class="text-3xl transition-transform hover:scale-125 focus:outline-none"
                @click="form.rating = star"
              >
                {{ star <= form.rating ? '⭐' : '☆' }}
              </button>
            </div>
            <span class="text-xs font-extrabold text-brand-choco dark:text-[#E2C08A] bg-white dark:bg-[#1E1510] px-3 py-1 rounded-full border border-brand-caramel/30 dark:border-[#C08E5D]/30">
              {{ form.rating }} / 5 Stars
            </span>
          </div>
        </div>

        <div v-if="!authStore.isAuthenticated" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <BaseInput v-model="form.reviewer_name" label="Your Name" placeholder="e.g. Maria S." :disabled="form.is_anonymous" :required="!form.is_anonymous" />
          <BaseInput v-model="form.reviewer_email" type="email" label="Your Email" placeholder="maria@example.com" required />
        </div>

        <label class="flex items-center gap-2 text-xs font-bold text-brand-choco dark:text-[#E2C08A] cursor-pointer py-1">
          <input type="checkbox" v-model="form.is_anonymous" class="rounded text-brand-choco focus:ring-brand-choco w-4 h-4" />
          <span>Post review anonymously (Your name will appear as "Anonymous" publicly)</span>
        </label>

        <BaseInput v-model="form.title" label="Headline / Review Title" placeholder="e.g. Incredibly moist and perfectly sweetened!" />

        <BaseTextarea v-model="form.comment" label="Detailed Review" placeholder="Tell us what you loved about the pastry flavor, freshness, and packaging..." rows="4" required />

        <div v-if="!authStore.isAuthenticated" class="text-xs text-brand-choco/80 dark:text-[#E2C08A]/80 bg-brand-tan/10 p-3 rounded-xl border border-brand-tan/20 flex items-center gap-2 mt-2">
          💡 Tip: <RouterLink to="/auth/register" class="font-bold underline hover:text-brand-choco dark:hover:text-[#E2C08A] transition-colors">Create a free account</RouterLink> to be recognized as a Verified Buyer!
        </div>

        <div class="flex justify-end gap-3 pt-2">
          <BaseButton type="button" variant="ghost" v-tooltip="'Cancel writing review'" @click="showForm = false">Cancel</BaseButton>
          <BaseButton type="submit" variant="primary" :loading="submitting" v-tooltip="'Post your review for this pastry'">Submit Review</BaseButton>
        </div>
      </form>
    </Transition>

    <!-- Rating Filters with Counts -->
    <div v-if="reviews.length > 0" class="flex items-center justify-between gap-4 border-b border-brand-caramel/15 dark:border-[#C08E5D]/20 pb-4">
      <div class="text-xs font-bold text-warm-gray dark:text-[#C5B4A4]">Filter by score:</div>
      <div class="flex items-center gap-1.5 overflow-x-auto">
        <button
          v-for="filter in [0, 5, 4, 3, 2, 1]"
          :key="filter"
          type="button"
          v-tooltip="filter === 0 ? 'Show all reviews' : `Show only ${filter}-star reviews`"
          class="px-3 py-1 rounded-xl text-xs font-bold transition-all shrink-0"
          :class="selectedFilter === filter ? 'bg-brand-choco text-white dark:bg-[#C08E5D] dark:text-[#1C1410] shadow-xs' : 'bg-white dark:bg-[#1E1510] text-brand-choco dark:text-[#E2C08A] border border-brand-caramel/20 dark:border-[#C08E5D]/20 hover:bg-surface dark:hover:bg-[#140D09]'"
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

    <div v-else-if="filteredReviews.length === 0" class="text-center py-12 bg-surface/40 dark:bg-[#140D09]/40 rounded-3xl border border-dashed border-brand-caramel/30 dark:border-[#C08E5D]/30 p-8">
      <p class="text-base font-bold text-ink dark:text-[#FBF3E7] mb-1">No reviews for this filter yet.</p>
      <p class="text-xs text-warm-gray dark:text-[#C5B4A4] mb-4">Be the first to share your review for this pastry!</p>
      <BaseButton size="sm" variant="outline" v-tooltip="'Share your rating & feedback'" @click="handleToggleForm">✍️ Write a Review</BaseButton>
    </div>

    <div v-else class="space-y-4">
      <div
        v-for="review in filteredReviews"
        :key="review.id"
        class="bg-white dark:bg-[#1E1510] p-6 rounded-3xl border border-brand-caramel/20 dark:border-[#C08E5D]/20 shadow-sm hover:shadow-md transition-all space-y-3"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-brand-choco text-surface font-bold text-sm flex items-center justify-center">
              {{ review.reviewer_name?.charAt(0).toUpperCase() || 'A' }}
            </div>
            <div>
              <div class="font-extrabold text-sm text-ink flex items-center gap-2">
                {{ review.reviewer_name }}
                <BaseBadge v-if="review.is_verified_buyer" variant="success" size="sm">Verified Buyer</BaseBadge>
              </div>
              <div class="text-[11px] text-warm-gray">Reviewed on {{ new Date(review.created_at).toLocaleDateString() }}</div>
            </div>
          </div>

          <!-- Rating Stars -->
          <div class="flex items-center gap-1 text-sm text-amber-500">
            <span v-for="s in review.rating" :key="s">⭐</span>
          </div>
        </div>

        <h5 v-if="review.title" class="font-extrabold text-base text-ink pt-1">{{ review.title }}</h5>

        <p class="text-xs text-warm-gray leading-relaxed">{{ review.comment }}</p>

        <!-- Helpful Vote Button -->
        <div class="pt-3 border-t border-brand-caramel/15 flex items-center justify-between text-xs text-warm-gray">
          <span>Was this review helpful?</span>
          <button
            v-tooltip="'Mark this review as helpful'"
            class="px-3.5 py-1.5 rounded-xl bg-surface hover:bg-brand-tan/30 text-brand-choco font-semibold flex items-center gap-1.5 transition-all"
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

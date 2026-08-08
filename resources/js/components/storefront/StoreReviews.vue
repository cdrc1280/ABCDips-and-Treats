<template>
  <div class="space-y-12">
    <!-- Store & Service Rating Overview Card (Only shown if store reviews exist) -->
    <div v-if="stats.total_reviews && stats.total_reviews > 0" class="bg-gradient-to-br from-brand-choco to-[#3D2515] text-surface rounded-3xl p-8 md:p-12 shadow-xl border border-brand-caramel/30 relative overflow-hidden">
      <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none text-9xl">🧁</div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-center relative z-10">
        <!-- Main Score -->
        <div class="text-center lg:text-left space-y-2">
          <div class="script-accent text-brand-tan text-xl">bakery experience &amp; service</div>
          <div class="flex items-baseline justify-center lg:justify-start gap-3">
            <span class="text-6xl font-black tracking-tight">{{ stats.avg_rating }}</span>
            <span class="text-2xl text-brand-tan">/ 5.0</span>
          </div>
          <div class="flex items-center justify-center lg:justify-start gap-1 text-2xl text-amber-400">
            <span v-for="s in 5" :key="s">{{ s <= Math.round(stats.avg_rating || 5) ? '⭐' : '☆' }}</span>
          </div>
          <p class="text-xs text-brand-tan">Based on {{ stats.total_reviews }} verified customer review{{ stats.total_reviews > 1 ? 's' : '' }}</p>
        </div>

        <!-- Service Pillar Scores -->
        <div v-if="stats.service_scores" class="space-y-3 bg-white/10 backdrop-blur-md p-6 rounded-2xl border border-white/10">
          <div class="space-y-1 text-xs">
            <div class="flex justify-between font-bold">
              <span>🥐 Taste &amp; Freshness</span>
              <span class="text-amber-300">{{ stats.service_scores.taste_freshness }} ★</span>
            </div>
            <div class="w-full bg-white/20 h-2 rounded-full overflow-hidden">
              <div class="bg-amber-400 h-full rounded-full" :style="{ width: ((stats.service_scores.taste_freshness / 5) * 100) + '%' }" />
            </div>
          </div>

          <div class="space-y-1 text-xs">
            <div class="flex justify-between font-bold">
              <span>🚚 Delivery Speed &amp; Care</span>
              <span class="text-amber-300">{{ stats.service_scores.delivery_speed }} ★</span>
            </div>
            <div class="w-full bg-white/20 h-2 rounded-full overflow-hidden">
              <div class="bg-amber-400 h-full rounded-full" :style="{ width: ((stats.service_scores.delivery_speed / 5) * 100) + '%' }" />
            </div>
          </div>

          <div class="space-y-1 text-xs">
            <div class="flex justify-between font-bold">
              <span>💬 Customer Support &amp; Hospitality</span>
              <span class="text-amber-300">{{ stats.service_scores.customer_service }} ★</span>
            </div>
            <div class="w-full bg-white/20 h-2 rounded-full overflow-hidden">
              <div class="bg-amber-400 h-full rounded-full" :style="{ width: ((stats.service_scores.customer_service / 5) * 100) + '%' }" />
            </div>
          </div>
        </div>

        <!-- Write Review Callout -->
        <div class="text-center lg:text-right space-y-4">
          <h4 class="font-extrabold text-xl">How was your bakery experience?</h4>
          <p class="text-xs text-surface/80 max-w-xs ml-auto">Share your feedback on our pastries, delivery, or custom orders to help us serve you better!</p>
          <BaseButton variant="secondary" size="lg" :v-tooltip="showForm ? 'Close store review form' : 'Share your overall bakery & delivery experience'" @click="handleToggleForm">
            {{ showForm ? 'Close Review Form' : '✍️ Write a Store Review' }}
          </BaseButton>
        </div>
      </div>
    </div>

    <!-- Banner callout when 0 store reviews exist -->
    <div v-else class="bg-brand-choco text-surface rounded-3xl p-8 text-center space-y-4 shadow-lg border border-brand-caramel/30">
      <div class="text-4xl">🧁</div>
      <h3 class="text-2xl font-extrabold text-white">Bakery &amp; Store Service Reviews</h3>
      <p class="text-xs text-surface/80 max-w-md mx-auto">Have you ordered from ABCDips &amp; Treats? Share your experience with our bakery service, delivery speed, and customer care!</p>
      <BaseButton variant="secondary" size="lg" :v-tooltip="showForm ? 'Close store review form' : 'Share your overall bakery & delivery experience'" @click="handleToggleForm">
        {{ showForm ? 'Close Review Form' : '✍️ Write the First Store Review' }}
      </BaseButton>
    </div>

    <!-- Review Submission Form -->
    <Transition name="fade">
      <form v-if="showForm" @submit.prevent="submitStoreReview" class="bg-white p-8 rounded-3xl border border-brand-caramel/30 shadow-md space-y-6">
        <div>
          <h4 class="font-extrabold text-xl text-ink">Write a Bakery Service Review</h4>
          <p class="text-xs text-warm-gray">Rate your overall experience with ABCDips &amp; Treats store and delivery service.</p>
        </div>

        <!-- Quick Pre-made Store Review Templates -->
        <div class="space-y-1.5 bg-surface/70 p-4 rounded-2xl border border-brand-caramel/20">
          <label class="block text-xs font-bold uppercase text-brand-choco">⚡ Quick Pre-made Store Messages (Click to instant fill)</label>
          <div class="flex flex-wrap gap-2 pt-1">
            <button
              v-for="(tpl, idx) in storeReviewTemplates"
              :key="idx"
              type="button"
              v-tooltip="`Click to fill: '${tpl.chipLabel}'`"
              class="text-xs bg-white border border-brand-caramel/30 hover:border-brand-choco px-3 py-1.5 rounded-xl text-brand-choco font-semibold transition-all hover:bg-brand-tan/20 shadow-2xs"
              @click="applyTemplate(tpl)"
            >
              {{ tpl.chipLabel }}
            </button>
          </div>
        </div>

        <!-- Rating Selector -->
        <div class="space-y-2">
          <label class="block text-xs font-bold uppercase text-brand-choco">Overall Service Rating</label>
          <div class="flex items-center gap-3">
            <div class="flex gap-1">
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
            <span class="text-sm font-extrabold text-brand-choco bg-surface px-3 py-1 rounded-full border border-brand-caramel/30">
              {{ form.rating }} / 5 Stars
            </span>
          </div>
        </div>

        <div v-if="!authStore.isAuthenticated" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <BaseInput v-model="form.reviewer_name" label="Your Name" placeholder="e.g. Juan Dela Cruz" :disabled="form.is_anonymous" :required="!form.is_anonymous" />
          <BaseInput v-model="form.reviewer_email" type="email" label="Your Email" placeholder="juan@example.com" required />
        </div>

        <label class="flex items-center gap-2 text-xs font-bold text-brand-choco cursor-pointer py-1">
          <input type="checkbox" v-model="form.is_anonymous" class="rounded text-brand-choco focus:ring-brand-choco w-4 h-4" />
          <span>Post review anonymously (Your name will appear as "Anonymous" publicly)</span>
        </label>

        <BaseInput v-model="form.title" label="Headline" placeholder="e.g. Always fresh, super fast delivery!" />

        <BaseTextarea v-model="form.comment" label="Service & Bakery Review" placeholder="Tell us about the delivery speed, packaging, customer service, or overall experience..." rows="4" required />

        <div v-if="!authStore.isAuthenticated" class="text-xs text-brand-choco/80 bg-brand-tan/10 p-3 rounded-xl border border-brand-tan/20 flex items-center gap-2 mt-2">
          💡 Tip: <RouterLink to="/auth/register" class="font-bold underline hover:text-brand-choco transition-colors">Create a free account</RouterLink> to be recognized as a Verified Buyer!
        </div>

        <div class="flex justify-end gap-3">
          <BaseButton type="button" variant="ghost" v-tooltip="'Cancel writing review'" @click="showForm = false">Cancel</BaseButton>
          <BaseButton type="submit" variant="primary" :loading="submitting" v-tooltip="'Post your store service review'">Submit Store Review</BaseButton>
        </div>
      </form>
    </Transition>

    <!-- Rating Filters with Counts -->
    <div v-if="reviews.length > 0" class="flex flex-wrap items-center justify-between gap-4 border-b border-brand-caramel/20 pb-4">
      <h3 class="font-extrabold text-2xl text-ink">Verified Store Reviews</h3>

      <div class="flex items-center gap-2 overflow-x-auto">
        <button
          v-for="filter in [0, 5, 4, 3, 2, 1]"
          :key="filter"
          type="button"
          v-tooltip="filter === 0 ? 'Show all store reviews' : `Show only ${filter}-star store reviews`"
          class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0"
          :class="selectedRating === filter ? 'bg-brand-choco text-white shadow-sm' : 'bg-white text-brand-choco border border-brand-caramel/20 hover:bg-surface'"
          @click="selectedRating = filter"
        >
          {{ filter === 0 ? `All (${reviews.length})` : `${filter} Stars ⭐ (${getStarCount(filter)})` }}
        </button>
      </div>
    </div>

    <!-- Reviews Grid -->
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <SkeletonRow v-for="n in 4" :key="n" />
    </div>

    <div v-else-if="filteredReviews.length === 0" class="text-center py-12 bg-white rounded-3xl border border-dashed border-brand-caramel/30 p-8">
      <p class="text-base font-bold text-ink">No store reviews found for this star rating.</p>
      <p class="text-xs text-warm-gray mt-1">Try switching rating filters or leave a review!</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div
        v-for="review in filteredReviews"
        :key="review.id"
        class="bg-white rounded-3xl p-6 border border-brand-caramel/20 shadow-sm hover:shadow-md transition-all space-y-4 flex flex-col justify-between"
      >
        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-brand-choco text-surface font-bold text-sm flex items-center justify-center">
                {{ review.reviewer_name?.charAt(0).toUpperCase() || 'A' }}
              </div>
              <div>
                <div class="font-extrabold text-sm text-ink">{{ review.reviewer_name }}</div>
                <div class="text-[11px] text-warm-gray">Verified Store Customer</div>
              </div>
            </div>
            <div class="text-xs text-warm-gray">{{ new Date(review.created_at).toLocaleDateString() }}</div>
          </div>

          <div class="flex items-center gap-1 text-sm text-amber-500">
            <span v-for="s in review.rating" :key="s">⭐</span>
          </div>

          <h5 v-if="review.title" class="font-bold text-base text-ink">{{ review.title }}</h5>

          <p class="text-xs text-warm-gray leading-relaxed">{{ review.comment }}</p>

          <div v-if="review.product_name" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-surface text-[11px] font-semibold text-brand-choco">
            🛍️ Reviewed item: {{ review.product_name }}
          </div>
        </div>

        <div class="pt-3 border-t border-brand-caramel/15 flex items-center justify-between text-xs text-warm-gray">
          <span>Was this review helpful?</span>
          <button
            v-tooltip="'Mark this store review as helpful'"
            class="px-3 py-1 rounded-lg bg-surface hover:bg-brand-tan/30 text-brand-choco font-semibold flex items-center gap-1 transition-all"
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
import SkeletonRow from '@/components/ui/SkeletonRow.vue'

const axios = inject('axios')
const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const toast = useToast()

const stats = ref({ avg_rating: null, total_reviews: 0, service_scores: null })
const reviews = ref([])
const loading = ref(true)
const showForm = ref(false)
const submitting = ref(false)
const selectedRating = ref(0)

const form = ref({
  rating: 5,
  reviewer_name: '',
  reviewer_email: '',
  title: '',
  comment: '',
  is_anonymous: false
})

const storeReviewTemplates = [
  {
    chipLabel: '🚚 Fast Delivery & Oven Fresh',
    rating: 5,
    title: 'Lightning fast delivery and fresh pastries!',
    comment: 'Ordered online and received my pastries earlier than expected, still warm and carefully handled by the rider!'
  },
  {
    chipLabel: '💖 Friendly Bakery Customer Support',
    rating: 5,
    title: 'Super accommodating & friendly owner and staff!',
    comment: 'The team was so polite and accommodating when I inquired about custom orders and delivery times. Top-tier service!'
  },
  {
    chipLabel: '✨ Premium Quality & Clean Packaging',
    rating: 5,
    title: 'High quality bakery experience!',
    comment: 'The packaging is clean, aesthetic, and sturdy, and every treat is baked with real quality. My go-to local bakery!'
  },
  {
    chipLabel: '🎂 Perfect Custom Order Execution',
    rating: 5,
    title: 'Custom cake inquiry & order was seamless!',
    comment: 'Submitted a custom bake inquiry online and the team executed the theme description beyond expectations!'
  }
]

function getStarCount(star) {
  if (star === 0) return reviews.value.length
  return reviews.value.filter(r => r.rating === star).length
}

function applyTemplate(template) {
  form.value.rating = template.rating
  form.value.title = template.title
  form.value.comment = template.comment
  toast.info('Store review template loaded! Feel free to customize.', 'Pre-made Message Set')
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

const filteredReviews = computed(() => {
  if (selectedRating.value === 0) return reviews.value
  return reviews.value.filter(r => r.rating === selectedRating.value)
})

async function fetchStoreReviews() {
  loading.value = true
  try {
    const { data } = await axios.get('/api/store/reviews')
    stats.value = data.stats || { avg_rating: null, total_reviews: 0, service_scores: null }
    reviews.value = data.reviews.data || []
  } catch (err) {
    console.error('Failed to load store reviews', err)
  } finally {
    loading.value = false
  }
}

async function submitStoreReview() {

  submitting.value = true
  try {
    await axios.post('/api/reviews/store-service', {
      ...form.value
    })
    toast.success('Thank you! Your store review has been posted.', 'Review Submitted')
    showForm.value = false
    form.value = { rating: 5, reviewer_name: authStore.user?.name || '', reviewer_email: authStore.user?.email || '', title: '', comment: '' }
    fetchStoreReviews()
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
    fetchStoreReviews()
  } catch (err) {
    toast.info(err.response?.data?.message || 'Already voted.')
  }
}

onMounted(() => fetchStoreReviews())
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>

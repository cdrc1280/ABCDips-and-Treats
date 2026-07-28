<template>
  <div class="space-y-8">
    <!-- Header & Summary Row -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-[#C08E5D]/20 pb-6">
      <div>
        <h3 class="text-2xl font-bold text-[#1C1410]">Customer Reviews</h3>
        <p class="text-xs text-[#8C7A68]">Real reviews from verified ABCDips &amp; Treats buyers.</p>
      </div>

      <BaseButton variant="primary" @click="showForm = !showForm">
        {{ showForm ? 'Cancel Review' : '✍️ Write a Review' }}
      </BaseButton>
    </div>

    <!-- Review Submission Form -->
    <Transition name="fade">
      <form v-if="showForm" @submit.prevent="submitReview" class="bg-[#FBF3E7] p-6 rounded-3xl border border-[#C08E5D]/30 space-y-4">
        <h4 class="font-bold text-lg text-[#1C1410]">Share Your Pastry Experience</h4>

        <!-- Star Rating Input -->
        <div class="space-y-1">
          <label class="block text-xs font-semibold uppercase text-[#5C3A22]">Overall Rating</label>
          <div class="flex items-center gap-2">
            <button
              v-for="star in 5"
              :key="star"
              type="button"
              class="text-2xl transition-transform hover:scale-125 focus:outline-none"
              @click="form.rating = star"
            >
              {{ star <= form.rating ? '⭐' : '☆' }}
            </button>
            <span class="text-xs font-bold text-[#5C3A22] ml-2">{{ form.rating }} / 5 Stars</span>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <BaseInput v-model="form.reviewer_name" label="Your Name" placeholder="e.g. Maria S." required />
          <BaseInput v-model="form.reviewer_email" type="email" label="Your Email" placeholder="maria@example.com" required />
        </div>

        <BaseInput v-model="form.title" label="Headline / Review Title" placeholder="e.g. Incredibly moist and rich!" />

        <BaseTextarea v-model="form.comment" label="Detailed Review" placeholder="Tell us what you loved about the flavor, texture, and packaging..." rows="3" required />

        <div class="flex justify-end gap-3 pt-2">
          <BaseButton type="button" variant="ghost" @click="showForm = false">Cancel</BaseButton>
          <BaseButton type="submit" variant="primary" :loading="submitting">Post Review</BaseButton>
        </div>
      </form>
    </Transition>

    <!-- Reviews List -->
    <div v-if="loading" class="space-y-4">
      <SkeletonRow v-for="n in 3" :key="n" />
    </div>

    <div v-else-if="reviews.length === 0" class="text-center py-8 text-[#8C7A68]">
      <p class="text-sm">No reviews yet for this pastry. Be the first to leave a review!</p>
    </div>

    <div v-else class="space-y-4">
      <div
        v-for="review in reviews"
        :key="review.id"
        class="bg-white p-6 rounded-2xl border border-[#C08E5D]/15 shadow-xs space-y-3"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <div class="font-bold text-sm text-[#1C1410]">{{ review.reviewer_name }}</div>
            <BaseBadge v-if="review.is_verified_buyer" variant="success" size="sm">Verified Buyer</BaseBadge>
          </div>
          <div class="text-xs text-[#8C7A68]">{{ new Date(review.created_at).toLocaleDateString() }}</div>
        </div>

        <!-- Rating Stars -->
        <div class="flex items-center gap-1 text-sm text-amber-500">
          <span v-for="s in review.rating" :key="s">⭐</span>
        </div>

        <h5 v-if="review.title" class="font-bold text-base text-[#1C1410]">{{ review.title }}</h5>

        <p class="text-sm text-[#8C7A68] leading-relaxed">{{ review.comment }}</p>

        <!-- Helpful Vote Button -->
        <div class="pt-2 flex items-center justify-between text-xs text-[#8C7A68]">
          <span>Was this review helpful?</span>
          <button
            class="px-3 py-1 rounded-lg bg-[#FBF3E7] hover:bg-[#D9A876]/30 text-[#5C3A22] font-semibold flex items-center gap-1 transition-all"
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
import { ref, onMounted, inject } from 'vue'
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
const toast = useToast()

const reviews = ref([])
const loading = ref(true)
const showForm = ref(false)
const submitting = ref(false)

const form = ref({
  rating: 5,
  reviewer_name: '',
  reviewer_email: '',
  title: '',
  comment: ''
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
    toast.success('Thank you! Your review has been posted.', 'Review Submitted')
    showForm.value = false
    form.value = { rating: 5, reviewer_name: '', reviewer_email: '', title: '', comment: '' }
    fetchReviews()
  } catch (err) {
    toast.error('Failed to submit review. Please fill out all fields.', 'Submission Error')
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

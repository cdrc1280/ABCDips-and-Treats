<template>
  <div class="page-container py-10 md:py-16">
    <PageHeader
      tagline="bespoke baking"
      title="Custom Cake &amp; Pastry Builder"
      subtitle="Planning a wedding, birthday, or milestone celebration? Customize your cake online, add to basket, or submit an inquiry for a formal quote."
    />

    <!-- Inquiry Success Card -->
    <div v-if="submittedOrder" class="max-w-2xl mx-auto bg-white rounded-3xl p-8 border border-brand-caramel/20 shadow-md text-center space-y-4">
      <div class="w-16 h-16 rounded-full bg-success/20 text-success flex items-center justify-center mx-auto">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
      </div>

      <span class="script-accent text-brand-caramel text-xl block">inquiry received</span>
      <h2 class="text-3xl font-extrabold text-ink">Custom Order Reference #{{ submittedOrder.reference_number }}</h2>
      <p class="text-warm-gray text-sm leading-relaxed">
        Thank you, <strong>{{ submittedOrder.customer_name }}</strong>! Our head pastry chef will review your theme description and reference details, and contact you via <strong>{{ submittedOrder.customer_phone }}</strong> with a formal quote within 24 hours.
      </p>

      <div class="pt-4 flex justify-center gap-4">
        <RouterLink to="/shop"><BaseButton variant="primary">Explore Menu</BaseButton></RouterLink>
        <BaseButton variant="outline" @click="submittedOrder = null">Submit Another Inquiry</BaseButton>
      </div>
    </div>

    <!-- Custom Order Form & Live Price Calculator -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start max-w-5xl mx-auto">

      <!-- Builder Form Left Column -->
      <form @submit.prevent="submitInquiry" class="lg:col-span-8 bg-white rounded-3xl p-6 md:p-10 border border-brand-caramel/20 shadow-sm space-y-8">

        <!-- Section 1: Customer Details -->
        <div class="space-y-4">
          <h3 class="font-extrabold text-xl text-ink border-b border-brand-caramel/20 pb-3 flex items-center gap-2">
            <span>1. Contact Details</span>
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <BaseInput v-model="form.customer_name" label="Full Name" placeholder="e.g. Maria Santos" required :error="errors.customer_name?.[0]" />
            <BaseInput v-model="form.customer_email" type="email" label="Email Address" placeholder="maria@example.com" required :error="errors.customer_email?.[0]" />
            <BaseInput v-model="form.customer_phone" type="tel" numeric-only maxlength="13" label="Mobile Number" placeholder="09171234567" required :error="errors.customer_phone?.[0]" />
            <BaseInput v-model="form.event_date" type="date" label="Event Date" required :error="errors.event_date?.[0]" />
          </div>
        </div>

        <!-- Section 2: Cake Specs -->
        <div class="space-y-4">
          <h3 class="font-extrabold text-xl text-ink border-b border-brand-caramel/20 pb-3 flex items-center gap-2">
            <span>2. Custom Cake Specifications</span>
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <BaseSelect
              v-model="form.servings_count"
              label="Estimated Guest Servings"
              :options="[
                { value: 15, label: '15-20 Guests (Intimate Party)' },
                { value: 30, label: '30-40 Guests (Medium Celebration)' },
                { value: 60, label: '50-60 Guests (Large Gathering)' },
                { value: 100, label: '100+ Guests (Grand Event / Wedding)' }
              ]"
              required
              :error="errors.servings_count?.[0]"
            />

            <BaseSelect
              v-model="form.tiers_count"
              label="Tier Count"
              :options="[
                { value: 1, label: '1 Tier (Standard)' },
                { value: 2, label: '2 Tiers (Celebration)' },
                { value: 3, label: '3 Tiers (Grand / Wedding)' },
                { value: 4, label: '4+ Tiers Custom Deluxe' }
              ]"
              required
              :error="errors.tiers_count?.[0]"
            />

            <BaseSelect
              v-model="form.flavor_preference"
              label="Flavor Preference"
              :options="['Dark Belgian Chocolate', 'Signature Ube Halaya', 'Red Velvet', 'Classic Vanilla Bean', 'Buko Pandan', 'Salted Caramel Chocolate']"
            />

            <BaseSelect
              v-model="form.frosting_type"
              label="Frosting / Finish Style"
              :options="['Silky Cream Cheese', 'Swiss Meringue Buttercream', 'Semi-Naked Rustic', 'Smooth Fondant Artistry']"
            />
          </div>

          <BaseInput
            v-model="form.cake_inscription"
            label="Cake Inscription / Greeting Message"
            placeholder="e.g. Happy 30th Birthday Sophia!"
          />
        </div>

        <!-- Section 3: Theme Description -->
        <div class="space-y-4">
          <h3 class="font-extrabold text-xl text-ink border-b border-brand-caramel/20 pb-3 flex items-center gap-2">
            <span>3. Theme &amp; Design Vision</span>
          </h3>

          <BaseTextarea
            v-model="form.theme_description"
            label="Describe Theme, Colors &amp; Special Instructions"
            placeholder="e.g. Pastel floral theme with edible gold leaf accents and pearl piping for a 30th birthday..."
            rows="4"
            required
            :error="errors.theme_description?.[0]"
          />
        </div>

        <!-- Section 4: Preferred Budget Range -->
        <div class="space-y-4">
          <h3 class="font-extrabold text-xl text-ink border-b border-brand-caramel/20 pb-3 flex items-center gap-2">
            <span>4. Preferred Budget Range (₱)</span>
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <BaseInput
              v-model.number="form.budget_range_min"
              type="number"
              numeric-only
              min="0"
              label="Minimum Preferred Budget (₱)"
              placeholder="e.g. 2000"
              required
              :error="errors.budget_range_min?.[0]"
            />
            <BaseInput
              v-model.number="form.budget_range_max"
              type="number"
              numeric-only
              min="0"
              label="Maximum Preferred Budget (₱)"
              placeholder="e.g. 4500"
              required
              :error="errors.budget_range_max?.[0]"
            />
          </div>
        </div>

      </form>

      <!-- Live Summary & Action Box Right Column -->
      <div class="lg:col-span-4 space-y-6">
        <div class="bg-white rounded-3xl p-6 border border-brand-caramel/20 shadow-md space-y-6 sticky top-24">
          <div>
            <span class="script-accent text-brand-caramel text-lg">live quote estimate</span>
            <h3 class="font-extrabold text-2xl text-ink">Custom Cake Summary</h3>
          </div>

          <!-- Specs List -->
          <div class="bg-surface/70 p-4 rounded-2xl border border-brand-caramel/20 space-y-2 text-xs text-warm-gray">
            <div class="flex justify-between font-bold text-ink">
              <span>Tiers &amp; Servings:</span>
              <span>{{ form.tiers_count }} Tier ({{ form.servings_count }} Guests)</span>
            </div>
            <div class="flex justify-between">
              <span>Flavor:</span>
              <span class="font-semibold text-brand-choco dark:text-[#E2C08A]">{{ form.flavor_preference }}</span>
            </div>
            <div class="flex justify-between">
              <span>Frosting:</span>
              <span class="font-semibold text-brand-choco dark:text-[#E2C08A]">{{ form.frosting_type }}</span>
            </div>
            <div class="flex justify-between border-t border-brand-caramel/15 pt-1.5">
              <span>Preferred Budget:</span>
              <span class="font-bold text-brand-choco dark:text-[#E2C08A]">₱{{ form.budget_range_min }} - ₱{{ form.budget_range_max }}</span>
            </div>
            <div v-if="form.cake_inscription" class="pt-1 font-semibold text-brand-choco dark:text-[#E2C08A] italic">
              "{{ form.cake_inscription }}"
            </div>
          </div>

          <!-- Estimated Price Display -->
          <div class="border-t border-brand-caramel/20 pt-4 flex justify-between items-baseline">
            <span class="text-sm font-bold text-warm-gray dark:text-[#C5B4A4]">Estimated Price:</span>
            <span class="text-3xl font-black text-brand-choco dark:text-[#E2C08A]">₱{{ estimatedPrice.toFixed(2) }}</span>
          </div>

          <!-- Dual Action Buttons -->
          <div class="space-y-3 pt-2">
            <BaseButton
              variant="primary"
              full-width
              size="lg"
              :loading="addingToCart"
              @click="addToCart"
            >
              🎂 Add Custom Cake to Basket
            </BaseButton>

            <BaseButton
              variant="outline"
              full-width
              size="md"
              :loading="submitting"
              @click="submitInquiry"
            >
              📨 Submit Custom Quote Inquiry
            </BaseButton>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, inject } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import { useToast } from '@/composables/useToast'
import PageHeader from '@/components/ui/PageHeader.vue'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseSelect from '@/components/ui/BaseSelect.vue'
import BaseTextarea from '@/components/ui/BaseTextarea.vue'
import BaseButton from '@/components/ui/BaseButton.vue'

const axios = inject('axios')
const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()
const toast = useToast()

const submitting = ref(false)
const addingToCart = ref(false)
const submittedOrder = ref(null)
const errors = ref({})
const defaultCakeProductId = ref(null)

const today = new Date().toISOString().split('T')[0]

const form = ref({
  customer_name: '',
  customer_email: '',
  customer_phone: '',
  event_date: today,
  servings_count: 30,
  tiers_count: 2,
  flavor_preference: 'Signature Ube Halaya',
  frosting_type: 'Silky Cream Cheese',
  cake_inscription: 'Happy Birthday!',
  theme_description: 'Floral pastel design with gold leaf accents.',
  budget_range_min: 2000,
  budget_range_max: 4500
})

const estimatedPrice = computed(() => {
  let base = 1500
  const tiers = Number(form.value.tiers_count)
  const servings = Number(form.value.servings_count)

  if (tiers === 2) base = 2800
  else if (tiers === 3) base = 4500
  else if (tiers >= 4) base = 6500

  if (servings >= 60) base += 800
  if (servings >= 100) base += 1500

  return base
})

function populateUserData() {
  const savedPhone = localStorage.getItem('customer_phone') || authStore.user?.phone || ''
  if (savedPhone) {
    form.value.customer_phone = savedPhone
  }
  if (authStore.user) {
    if (!form.value.customer_name) form.value.customer_name = authStore.user.name || ''
    if (!form.value.customer_email) form.value.customer_email = authStore.user.email || ''
    if (!form.value.customer_phone && authStore.user.phone) form.value.customer_phone = authStore.user.phone
  }
}

watch(() => form.value.customer_phone, (newPhone) => {
  if (newPhone) {
    localStorage.setItem('customer_phone', newPhone)
  }
})

watch(() => authStore.user, populateUserData, { immediate: true })

async function fetchCakeProduct() {
  try {
    const { data } = await axios.get('/api/products')
    const cakeProduct = data.data.find(p => p.category?.slug === 'cheesecakes-cakes') || data.data[0]
    if (cakeProduct) {
      defaultCakeProductId.value = cakeProduct.id
    }
  } catch (err) {
    console.error('Failed to load cake product', err)
  }
}

async function addToCart() {
  if (!authStore.isAuthenticated) {
    toast.warning('Please sign in to add your custom cake to your basket.', 'Sign In Required')
    router.push({ name: 'login', query: { redirect: '/custom-orders' } })
    return
  }

  if (!defaultCakeProductId.value) {
    await fetchCakeProduct()
  }

  addingToCart.value = true
  try {
    const customOptions = {
      is_custom: true,
      custom_title: `Custom ${form.value.tiers_count}-Tier ${form.value.flavor_preference} Cake`,
      tiers_count: form.value.tiers_count,
      servings_count: form.value.servings_count,
      flavor_preference: form.value.flavor_preference,
      frosting_type: form.value.frosting_type,
      cake_inscription: form.value.cake_inscription,
      event_date: form.value.event_date,
      theme_description: form.value.theme_description,
      budget_range_min: form.value.budget_range_min,
      budget_range_max: form.value.budget_range_max,
      unit_price: estimatedPrice.value
    }

    const res = await cartStore.addItem(defaultCakeProductId.value, 1, customOptions)
    if (res.success) {
      toast.success(`Custom ${form.value.tiers_count}-Tier ${form.value.flavor_preference} Cake added to your basket!`, 'Custom Bake Added 🎂')
      cartStore.openDrawer = true
    }
  } catch (err) {
    toast.error('Failed to add custom cake to basket.', 'Basket Error')
  } finally {
    addingToCart.value = false
  }
}

async function submitInquiry() {
  if (!authStore.isAuthenticated) {
    toast.warning('Please sign in to submit a custom bakery inquiry.', 'Sign In Required')
    router.push({ name: 'login', query: { redirect: '/custom-orders' } })
    return
  }

  submitting.value = true
  errors.value = {}

  try {
    const { data } = await axios.post('/api/custom-orders', {
      ...form.value,
      budget_range_min: form.value.budget_range_min || estimatedPrice.value,
      budget_range_max: form.value.budget_range_max || (estimatedPrice.value + 1000)
    })
    submittedOrder.value = data.data
    toast.success('Custom inquiry submitted!', 'Inquiry Received')
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {}
      toast.error('Please check the form for missing or invalid details.', 'Validation Error')
    } else {
      toast.error(err.response?.data?.message || 'Failed to submit inquiry. Please try again.', 'Form Error')
    }
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  populateUserData()
  fetchCakeProduct()
})
</script>

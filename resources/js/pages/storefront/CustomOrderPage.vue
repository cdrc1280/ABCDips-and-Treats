<template>
  <div class="page-container py-10 md:py-16">
    <PageHeader
      tagline="bespoke baking"
      title="Custom Cake &amp; Pastry Orders"
      subtitle="Planning a wedding, birthday, or milestone celebration? Let our head pastry chef bring your vision to life."
    />

    <!-- Inquiry Success Card -->
    <div v-if="submittedOrder" class="max-w-2xl mx-auto bg-white rounded-3xl p-8 border border-[#C08E5D]/20 shadow-md text-center space-y-4">
      <div class="w-16 h-16 rounded-full bg-[#6B8F5E]/20 text-[#6B8F5E] flex items-center justify-center mx-auto">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
      </div>

      <span class="script-accent text-[#C08E5D] text-xl block">inquiry received</span>
      <h2 class="text-3xl font-extrabold text-[#1C1410]">Custom Order Reference #{{ submittedOrder.reference_number }}</h2>
      <p class="text-[#8C7A68] text-sm leading-relaxed">
        Thank you, <strong>{{ submittedOrder.customer_name }}</strong>! Our head pastry chef will review your theme description and reference details, and contact you via <strong>{{ submittedOrder.customer_phone }}</strong> with a formal quote within 24 hours.
      </p>

      <div class="pt-4 flex justify-center gap-4">
        <RouterLink to="/shop"><BaseButton variant="primary">Explore Menu</BaseButton></RouterLink>
        <BaseButton variant="outline" @click="submittedOrder = null">Submit Another Inquiry</BaseButton>
      </div>
    </div>

    <!-- Custom Order Form -->
    <form v-else @submit.prevent="submitInquiry" class="max-w-3xl mx-auto bg-white rounded-3xl p-6 md:p-10 border border-[#C08E5D]/20 shadow-sm space-y-8">

      <!-- Section 1: Customer Info -->
      <div class="space-y-4">
        <h3 class="font-extrabold text-xl text-[#1C1410] border-b border-[#C08E5D]/20 pb-3">
          1. Contact Details
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <BaseInput v-model="form.customer_name" label="Full Name" placeholder="e.g. Maria Santos" required />
          <BaseInput v-model="form.customer_email" type="email" label="Email Address" placeholder="maria@example.com" required />
          <BaseInput v-model="form.customer_phone" label="Mobile Number" placeholder="0917 123 4567" required />
          <BaseInput v-model="form.event_date" type="date" label="Event Date" required />
        </div>
      </div>

      <!-- Section 2: Cake & Pastry Specifications -->
      <div class="space-y-4">
        <h3 class="font-extrabold text-xl text-[#1C1410] border-b border-[#C08E5D]/20 pb-3">
          2. Cake &amp; Servings Specs
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <BaseSelect
            v-model="form.servings_count"
            label="Estimated Guest Servings"
            :options="[
              { value: 15, label: '15-20 Guests (Intimate)' },
              { value: 30, label: '30-40 Guests (Medium Party)' },
              { value: 60, label: '50-60 Guests (Large Gathering)' },
              { value: 100, label: '100+ Guests (Grand Event / Wedding)' }
            ]"
            required
          />

          <BaseSelect
            v-model="form.tiers_count"
            label="Tier Count"
            :options="[
              { value: 1, label: '1 Tier' },
              { value: 2, label: '2 Tiers' },
              { value: 3, label: '3 Tiers' },
              { value: 4, label: '4+ Tiers Custom' }
            ]"
            required
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
      </div>

      <!-- Section 3: Theme Description & Budget -->
      <div class="space-y-4">
        <h3 class="font-extrabold text-xl text-[#1C1410] border-b border-[#C08E5D]/20 pb-3">
          3. Theme &amp; Vision
        </h3>

        <BaseTextarea
          v-model="form.theme_description"
          label="Describe your Theme, Colors, and Specific Decor Ideas"
          placeholder="e.g. Floral pastel theme with gold leaf accents for a 30th birthday celebration..."
          rows="4"
          required
        />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <BaseInput v-model="form.budget_range_min" type="number" label="Minimum Budget (₱)" placeholder="e.g. 1500" />
          <BaseInput v-model="form.budget_range_max" type="number" label="Maximum Budget (₱)" placeholder="e.g. 3500" />
        </div>
      </div>

      <div class="pt-4">
        <BaseButton type="submit" variant="primary" full-width size="lg" :loading="submitting">
          Submit Custom Bakery Inquiry →
        </BaseButton>
      </div>

    </form>
  </div>
</template>

<script setup>
import { ref, inject } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useToast } from '@/composables/useToast'
import PageHeader from '@/components/ui/PageHeader.vue'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseSelect from '@/components/ui/BaseSelect.vue'
import BaseTextarea from '@/components/ui/BaseTextarea.vue'
import BaseButton from '@/components/ui/BaseButton.vue'

const axios = inject('axios')
const authStore = useAuthStore()
const toast = useToast()

const submitting = ref(false)
const submittedOrder = ref(null)

const form = ref({
  customer_name: authStore.userName || '',
  customer_email: authStore.user?.email || '',
  customer_phone: authStore.user?.phone || '',
  event_date: '',
  servings_count: 30,
  tiers_count: 2,
  flavor_preference: 'Signature Ube Halaya',
  frosting_type: 'Silky Cream Cheese',
  theme_description: '',
  budget_range_min: 2000,
  budget_range_max: 4500
})

async function submitInquiry() {
  submitting.value = true
  try {
    const { data } = await axios.post('/api/custom-orders', form.value)
    submittedOrder.value = data.data
    toast.success('Custom inquiry submitted!', 'Inquiry Received')
  } catch (err) {
    toast.error('Failed to submit inquiry. Please check the required fields.', 'Form Error')
  } finally {
    submitting.value = false
  }
}
</script>

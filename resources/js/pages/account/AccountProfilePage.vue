<template>
  <div class="space-y-8">
    <!-- Back Navigation Links -->
    <div class="flex items-center justify-between">
      <RouterLink
        to="/account/orders"
        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-brand-choco dark:text-[#E2C08A] bg-white dark:bg-[#1E1510] border border-brand-caramel/25 dark:border-[#C08E5D]/25 hover:bg-brand-tan/20 transition-all shadow-sm"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Back to My Account
      </RouterLink>

      <RouterLink
        to="/"
        class="text-xs font-bold text-warm-gray dark:text-[#C5B4A4] hover:text-brand-choco dark:hover:text-[#E2C08A] hover:underline"
      >
        Go to Storefront →
      </RouterLink>
    </div>

    <PageHeader
      tagline="account management"
      title="Profile &amp; Settings"
      subtitle="Update your personal details, default delivery address, and security password."
    />

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

      <!-- Profile Form Left -->
      <form @submit.prevent="updateProfile" class="bg-white dark:bg-[#1E1510] rounded-3xl p-6 md:p-8 border border-brand-caramel/20 dark:border-[#C08E5D]/20 shadow-sm space-y-4">
        <h3 class="font-extrabold text-xl text-ink dark:text-[#FBF3E7] border-b border-brand-caramel/20 dark:border-[#C08E5D]/20 pb-3">
          Personal Details
        </h3>

        <BaseInput
          v-model="profileForm.name"
          label="Full Name"
          required
        />

        <div class="space-y-1">
          <div class="flex items-center justify-between mb-1">
            <label class="text-sm font-semibold text-ink dark:text-[#E2C08A]">Email Address (Account ID)</label>
            <span
              v-if="authStore.user?.email_verified_at"
              class="text-xs font-bold text-success bg-success/15 px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
              <CheckCircle2 class="w-3 h-3 text-emerald-600" />
              <span>Verified</span>
            </span>
            <span
              v-else
              class="text-xs font-bold text-amber-700 bg-amber-100 px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
              <AlertTriangle class="w-3 h-3 text-amber-600" />
              <span>Unverified</span>
            </span>
          </div>

          <input
            v-model="profileForm.email"
            type="email"
            disabled
            class="w-full bg-gray-100 border border-brand-caramel/20 rounded-xl px-3.5 py-2.5 text-sm text-warm-gray cursor-not-allowed opacity-80"
          />

          <div v-if="!authStore.user?.email_verified_at" class="pt-1.5">
            <button
              type="button"
              @click="sendVerificationEmail"
              :disabled="sendingEmail"
              class="text-xs font-bold text-brand-choco bg-surface hover:bg-brand-tan/30 px-3.5 py-2 rounded-lg border border-brand-caramel/30 transition-all flex items-center gap-1.5"
            >
              <span v-if="sendingEmail" class="w-3 h-3 border-2 border-brand-choco border-t-transparent rounded-full animate-spin"></span>
              <span class="flex items-center gap-1.5"><Mail class="w-3.5 h-3.5" /><span>Send Verification Email</span></span>
            </button>
          </div>
        </div>

        <BaseInput
          v-model="profileForm.phone"
          type="tel"
          numeric-only
          maxlength="13"
          label="Mobile Phone Number"
          placeholder="09171234567"
        />

        <PsgcAddressSelector
          v-model:region="profileForm.region"
          v-model:province="profileForm.province"
          v-model:city="profileForm.city"
          v-model:barangay="profileForm.barangay"
          v-model:streetAddress="profileForm.street_address"
          v-model:address="profileForm.address"
        />

        <!-- Interactive Address Map Picker -->
        <AddressMapPicker
          v-model:address="profileForm.address"
          v-model:city="profileForm.city"
          :store-lat="parseFloat(storeInfo.store_lat) || 14.4597"
          :store-lng="parseFloat(storeInfo.store_lng) || 120.9640"
          @location-selected="handleLocationSelected"
        />

        <div class="pt-2">
          <BaseButton type="submit" variant="primary" :loading="updatingProfile">
            Save Profile Changes
          </BaseButton>
        </div>
      </form>

      <!-- Password Change Right -->
      <form @submit.prevent="changePassword" class="bg-white rounded-3xl p-6 md:p-8 border border-brand-caramel/20 shadow-sm space-y-4">
        <h3 class="font-extrabold text-xl text-ink border-b border-brand-caramel/20 pb-3">
          Security &amp; Password
        </h3>

        <BaseInput
          v-model="passwordForm.current_password"
          type="password"
          label="Current Password"
          required
          :error="passwordErrors.current_password?.[0]"
        />

        <BaseInput
          v-model="passwordForm.new_password"
          type="password"
          label="New Password"
          placeholder="Min 8 chars, A-Z, a-z, 0-9, special char"
          required
          :error="passwordErrors.new_password?.[0]"
        />

        <!-- Password Requirement Rules Checklist -->
        <div v-if="passwordForm.new_password" class="bg-surface p-3 rounded-xl border border-brand-caramel/20 space-y-1 text-xs">
          <p class="font-semibold text-brand-choco mb-1">New Password Requirements:</p>
          <div class="grid grid-cols-2 gap-1">
            <div :class="rules.minLength ? 'text-success font-semibold' : 'text-warm-gray'" class="flex items-center gap-1.5">
              <CheckCircle2 v-if="rules.minLength" class="w-3.5 h-3.5 text-emerald-500 inline mr-1" /><Circle v-else class="w-3.5 h-3.5 text-stone-400 inline mr-1" /> At least 8 characters
            </div>
            <div :class="rules.hasUpper ? 'text-success font-semibold' : 'text-warm-gray'" class="flex items-center gap-1.5">
              <CheckCircle2 v-if="rules.hasUpper" class="w-3.5 h-3.5 text-emerald-500 inline mr-1" /><Circle v-else class="w-3.5 h-3.5 text-stone-400 inline mr-1" /> Uppercase letter (A-Z)
            </div>
            <div :class="rules.hasLower ? 'text-success font-semibold' : 'text-warm-gray'" class="flex items-center gap-1.5">
              <CheckCircle2 v-if="rules.hasLower" class="w-3.5 h-3.5 text-emerald-500 inline mr-1" /><Circle v-else class="w-3.5 h-3.5 text-stone-400 inline mr-1" /> Lowercase letter (a-z)
            </div>
            <div :class="rules.hasNumber ? 'text-success font-semibold' : 'text-warm-gray'" class="flex items-center gap-1.5">
              <CheckCircle2 v-if="rules.hasNumber" class="w-3.5 h-3.5 text-emerald-500 inline mr-1" /><Circle v-else class="w-3.5 h-3.5 text-stone-400 inline mr-1" /> Number (0-9)
            </div>
            <div :class="rules.hasSpecial ? 'text-success font-semibold' : 'text-warm-gray'" class="flex items-center gap-1.5 col-span-2">
              <CheckCircle2 v-if="rules.hasSpecial" class="w-3.5 h-3.5 text-emerald-500 inline mr-1" /><Circle v-else class="w-3.5 h-3.5 text-stone-400 inline mr-1" /> Special character (!@#$%^&* etc.)
            </div>
          </div>
        </div>

        <BaseInput
          v-model="passwordForm.new_password_confirmation"
          type="password"
          label="Confirm New Password"
          required
          :error="passwordForm.new_password_confirmation && passwordForm.new_password !== passwordForm.new_password_confirmation ? 'Passwords do not match' : ''"
        />

        <div class="pt-2">
          <BaseButton type="submit" variant="secondary" :loading="changingPassword">
            Update Password
          </BaseButton>
        </div>
      </form>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useToast } from '@/composables/useToast'
import PageHeader from '@/components/ui/PageHeader.vue'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseTextarea from '@/components/ui/BaseTextarea.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import AddressMapPicker from '@/components/checkout/AddressMapPicker.vue'
import PsgcAddressSelector from '@/components/checkout/PsgcAddressSelector.vue'

const axios = inject('axios')
const authStore = useAuthStore()
const toast = useToast()
const storeInfo = ref({})

const profileForm = ref({
  name: authStore.user?.name || '',
  email: authStore.user?.email || '',
  phone: authStore.user?.phone || '',
  address: authStore.user?.address || '',
  city: authStore.user?.city || '',
  region: authStore.user?.region || '',
  province: authStore.user?.province || '',
  barangay: authStore.user?.barangay || '',
  street_address: authStore.user?.street_address || '',
})

async function fetchStoreSettings() {
  try {
    const { data } = await axios.get('/api/settings/store')
    storeInfo.value = data || {}
  } catch (err) {
    console.warn('Failed to load store settings', err)
  }
}

function handleLocationSelected(loc) {
  if (!loc) return
  if (loc.address) profileForm.value.address = loc.address
  if (loc.city) profileForm.value.city = loc.city
  if (loc.province) profileForm.value.province = loc.province
  if (loc.region) profileForm.value.region = loc.region
  if (loc.barangay) profileForm.value.barangay = loc.barangay
  if (loc.streetAddress) profileForm.value.street_address = loc.streetAddress
}

onMounted(() => {
  fetchStoreSettings()
})

const passwordForm = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})

const updatingProfile = ref(false)
const changingPassword = ref(false)
const verifyingEmail = ref(false)
const sendingEmail = ref(false)
const passwordErrors = ref({})

async function sendVerificationEmail() {
  sendingEmail.value = true
  try {
    const { data } = await axios.post('/api/customer/send-verification-email')
    toast.success(data.message || 'Verification email sent to your inbox!', 'Verification Email Sent')
  } catch (err) {
    toast.error('Failed to send verification email.', 'Error')
  } finally {
    sendingEmail.value = false
  }
}

async function verifyEmail() {
  verifyingEmail.value = true
  try {
    const { data } = await axios.post('/api/customer/verify-email')
    authStore.user = data.data
    toast.success('Your account email has been verified successfully!', 'Account Verified')
  } catch (err) {
    toast.error('Failed to verify account email.', 'Verification Error')
  } finally {
    verifyingEmail.value = false
  }
}

const rules = computed(() => {
  const p = passwordForm.value.new_password || ''
  return {
    minLength: p.length >= 8,
    hasUpper: /[A-Z]/.test(p),
    hasLower: /[a-z]/.test(p),
    hasNumber: /[0-9]/.test(p),
    hasSpecial: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]/.test(p),
  }
})

const isNewPasswordValid = computed(() => {
  return rules.value.minLength &&
         rules.value.hasUpper &&
         rules.value.hasLower &&
         rules.value.hasNumber &&
         rules.value.hasSpecial
})

async function updateProfile() {
  updatingProfile.value = true
  try {
    const { data } = await axios.put('/api/customer/profile', profileForm.value)
    authStore.user = data.data
    toast.success('Profile updated successfully!', 'Account')
  } catch (err) {
    toast.error('Failed to update profile details.', 'Account Error')
  } finally {
    updatingProfile.value = false
  }
}

async function changePassword() {
  passwordErrors.value = {}

  if (!isNewPasswordValid.value) {
    passwordErrors.value = {
      new_password: ['Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.']
    }
    return
  }

  if (passwordForm.value.new_password !== passwordForm.value.new_password_confirmation) {
    toast.error('New password and confirmation do not match.', 'Validation Error')
    return
  }

  changingPassword.value = true

  try {
    await axios.post('/api/customer/password', passwordForm.value)
    toast.success('Password changed successfully!', 'Security')
    passwordForm.value = { current_password: '', new_password: '', new_password_confirmation: '' }
  } catch (err) {
    if (err.response?.status === 422) {
      passwordErrors.value = err.response.data.errors || {}
    } else {
      toast.error('Failed to change password.', 'Security Error')
    }
  } finally {
    changingPassword.value = false
  }
}

onMounted(async () => {
  if (!authStore.user) {
    await authStore.fetchUser()
  }
  if (authStore.user) {
    profileForm.value.name = authStore.user.name || ''
    profileForm.value.email = authStore.user.email || ''
    profileForm.value.phone = authStore.user.phone || ''
    profileForm.value.address = authStore.user.address || ''
    profileForm.value.city = authStore.user.city || ''
    profileForm.value.region = authStore.user.region || ''
    profileForm.value.province = authStore.user.province || ''
    profileForm.value.barangay = authStore.user.barangay || ''
    profileForm.value.street_address = authStore.user.street_address || ''
  }
})
</script>

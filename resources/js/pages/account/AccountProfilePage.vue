<template>
  <div class="space-y-8">
    <!-- Back Navigation Links -->
    <div class="flex items-center justify-between">
      <RouterLink
        to="/account/orders"
        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-[#5C3A22] bg-white border border-[#C08E5D]/25 hover:bg-[#D9A876]/20 transition-all shadow-sm"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Back to My Account
      </RouterLink>

      <RouterLink
        to="/"
        class="text-xs font-bold text-[#8C7A68] hover:text-[#5C3A22] hover:underline"
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
      <form @submit.prevent="updateProfile" class="bg-white rounded-3xl p-6 md:p-8 border border-[#C08E5D]/20 shadow-sm space-y-4">
        <h3 class="font-extrabold text-xl text-[#1C1410] border-b border-[#C08E5D]/20 pb-3">
          Personal Details
        </h3>

        <BaseInput
          v-model="profileForm.name"
          label="Full Name"
          required
        />

        <BaseInput
          v-model="profileForm.email"
          type="email"
          label="Email Address (Account ID)"
          disabled
          hint="Email address cannot be changed."
        />

        <BaseInput
          v-model="profileForm.phone"
          label="Mobile Phone Number"
          placeholder="0917 123 4567"
        />

        <BaseTextarea
          v-model="profileForm.address"
          label="Default Delivery Address"
          rows="3"
          placeholder="Enter your primary delivery address..."
        />

        <div class="pt-2">
          <BaseButton type="submit" variant="primary" :loading="updatingProfile">
            Save Profile Changes
          </BaseButton>
        </div>
      </form>

      <!-- Password Change Right -->
      <form @submit.prevent="changePassword" class="bg-white rounded-3xl p-6 md:p-8 border border-[#C08E5D]/20 shadow-sm space-y-4">
        <h3 class="font-extrabold text-xl text-[#1C1410] border-b border-[#C08E5D]/20 pb-3">
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
        <div v-if="passwordForm.new_password" class="bg-[#FBF3E7] p-3 rounded-xl border border-[#C08E5D]/20 space-y-1 text-xs">
          <p class="font-semibold text-[#5C3A22] mb-1">New Password Requirements:</p>
          <div class="grid grid-cols-2 gap-1">
            <div :class="rules.minLength ? 'text-[#6B8F5E] font-semibold' : 'text-[#8C7A68]'" class="flex items-center gap-1.5">
              <span>{{ rules.minLength ? '✓' : '○' }}</span> At least 8 characters
            </div>
            <div :class="rules.hasUpper ? 'text-[#6B8F5E] font-semibold' : 'text-[#8C7A68]'" class="flex items-center gap-1.5">
              <span>{{ rules.hasUpper ? '✓' : '○' }}</span> Uppercase letter (A-Z)
            </div>
            <div :class="rules.hasLower ? 'text-[#6B8F5E] font-semibold' : 'text-[#8C7A68]'" class="flex items-center gap-1.5">
              <span>{{ rules.hasLower ? '✓' : '○' }}</span> Lowercase letter (a-z)
            </div>
            <div :class="rules.hasNumber ? 'text-[#6B8F5E] font-semibold' : 'text-[#8C7A68]'" class="flex items-center gap-1.5">
              <span>{{ rules.hasNumber ? '✓' : '○' }}</span> Number (0-9)
            </div>
            <div :class="rules.hasSpecial ? 'text-[#6B8F5E] font-semibold' : 'text-[#8C7A68]'" class="flex items-center gap-1.5 col-span-2">
              <span>{{ rules.hasSpecial ? '✓' : '○' }}</span> Special character (!@#$%^&* etc.)
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

const axios = inject('axios')
const authStore = useAuthStore()
const toast = useToast()

const profileForm = ref({
  name: authStore.user?.name || '',
  email: authStore.user?.email || '',
  phone: authStore.user?.phone || '',
  address: authStore.user?.address || ''
})

const passwordForm = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})

const updatingProfile = ref(false)
const changingPassword = ref(false)
const passwordErrors = ref({})

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
  }
})
</script>

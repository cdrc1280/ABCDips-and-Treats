<template>
  <div class="space-y-6">
    <div>
      <h1 class="text-2xl font-bold text-ink mb-1">Create account</h1>
      <p class="script-accent text-brand-caramel text-lg">join the ABCDips family</p>
    </div>

    <!-- Error Alert -->
    <BaseAlert v-if="errorMessage" variant="danger">
      {{ errorMessage }}
    </BaseAlert>

    <!-- Register Form -->
    <form @submit.prevent="handleRegister" class="space-y-4">
      <BaseInput
        v-model="form.name"
        label="Full Name"
        placeholder="e.g. Maria Santos"
        required
        :error="errors.name?.[0]"
      />

      <BaseInput
        v-model="form.email"
        type="email"
        label="Email Address"
        placeholder="you@example.com"
        required
        :error="errors.email?.[0]"
      />

      <BaseInput
        v-model="form.phone"
        type="tel"
        numeric-only
        maxlength="13"
        label="Mobile Phone Number"
        placeholder="09171234567"
        :error="errors.phone?.[0]"
      />

      <BaseInput
        v-model="form.password"
        type="password"
        label="Password"
        placeholder="Min 8 chars, A-Z, a-z, 0-9, special char"
        required
        :error="errors.password?.[0]"
      />

      <!-- Real-time Password Rules Checklist -->
      <div v-if="form.password" class="bg-surface p-3 rounded-xl border border-brand-caramel/20 space-y-1 text-xs">
        <p class="font-semibold text-brand-choco mb-1">Password Requirements:</p>
        <div class="grid grid-cols-2 gap-1">
          <div :class="rules.minLength ? 'text-success font-semibold' : 'text-warm-gray'" class="flex items-center gap-1.5">
            <span>{{ rules.minLength ? '✓' : '○' }}</span> At least 8 characters
          </div>
          <div :class="rules.hasUpper ? 'text-success font-semibold' : 'text-warm-gray'" class="flex items-center gap-1.5">
            <span>{{ rules.hasUpper ? '✓' : '○' }}</span> Uppercase letter (A-Z)
          </div>
          <div :class="rules.hasLower ? 'text-success font-semibold' : 'text-warm-gray'" class="flex items-center gap-1.5">
            <span>{{ rules.hasLower ? '✓' : '○' }}</span> Lowercase letter (a-z)
          </div>
          <div :class="rules.hasNumber ? 'text-success font-semibold' : 'text-warm-gray'" class="flex items-center gap-1.5">
            <span>{{ rules.hasNumber ? '✓' : '○' }}</span> Number (0-9)
          </div>
          <div :class="rules.hasSpecial ? 'text-success font-semibold' : 'text-warm-gray'" class="flex items-center gap-1.5 col-span-2">
            <span>{{ rules.hasSpecial ? '✓' : '○' }}</span> Special character (!@#$%^&* etc.)
          </div>
        </div>
      </div>

      <BaseInput
        v-model="form.password_confirmation"
        type="password"
        label="Confirm Password"
        placeholder="Re-enter password"
        required
        :error="form.password_confirmation && form.password !== form.password_confirmation ? 'Passwords do not match' : ''"
      />

      <div class="pt-2">
        <BaseButton type="submit" variant="primary" full-width size="lg" :loading="loading">
          Create Account →
        </BaseButton>
      </div>
    </form>

    <div class="text-center pt-2 border-t border-brand-caramel/20">
      <p class="text-sm text-warm-gray">
        Already have an account?
        <RouterLink to="/auth/login" class="font-bold text-brand-choco hover:underline">
          Sign in
        </RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToast } from '@/composables/useToast'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseAlert from '@/components/ui/BaseAlert.vue'

const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()

const form = ref({
  name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: ''
})

const loading = ref(false)
const errorMessage = ref('')
const errors = ref({})

// Real-time password requirement rules
const rules = computed(() => {
  const p = form.value.password || ''
  return {
    minLength: p.length >= 8,
    hasUpper: /[A-Z]/.test(p),
    hasLower: /[a-z]/.test(p),
    hasNumber: /[0-9]/.test(p),
    hasSpecial: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]/.test(p),
  }
})

const isPasswordValid = computed(() => {
  return rules.value.minLength &&
         rules.value.hasUpper &&
         rules.value.hasLower &&
         rules.value.hasNumber &&
         rules.value.hasSpecial
})

async function handleRegister() {
  errorMessage.value = ''
  errors.value = {}

  if (!isPasswordValid.value) {
    errors.value = {
      password: ['Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.']
    }
    return
  }

  if (form.value.password !== form.value.password_confirmation) {
    errors.value = {
      password_confirmation: ['Passwords do not match.']
    }
    return
  }

  loading.value = true

  try {
    await authStore.register(form.value)
    toast.success('Account created successfully! Welcome to ABCDips & Treats.', 'Account Created')
    router.push('/account')
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {}
      errorMessage.value = err.response.data.message || 'Please fix the errors in the form.'
    } else {
      errorMessage.value = err.response?.data?.message || 'Registration failed. Please try again.'
    }
  } finally {
    loading.value = false
  }
}
</script>

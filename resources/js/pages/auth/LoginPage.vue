<template>
  <div class="space-y-6">
    <div>
      <h1 class="text-2xl font-bold text-[#1C1410] mb-1">Welcome back</h1>
      <p class="script-accent text-[#C08E5D] text-lg">sign in to your account</p>
    </div>

    <!-- Error Alert -->
    <BaseAlert v-if="errorMessage" variant="danger">
      {{ errorMessage }}
    </BaseAlert>

    <!-- Login Form -->
    <form @submit.prevent="handleLogin" class="space-y-4">
      <BaseInput
        v-model="form.email"
        type="email"
        label="Email Address"
        placeholder="you@example.com"
        required
        :error="errors.email?.[0]"
      />

      <BaseInput
        v-model="form.password"
        type="password"
        label="Password"
        placeholder="••••••••"
        required
        :error="errors.password?.[0]"
      />

      <div class="flex items-center justify-between pt-1">
        <BaseCheckbox v-model="form.remember" label="Remember me" />
      </div>

      <div class="pt-2">
        <BaseButton type="submit" variant="primary" full-width size="lg" :loading="loading">
          Sign In →
        </BaseButton>
      </div>
    </form>

    <div class="text-center pt-2 border-t border-[#C08E5D]/20">
      <p class="text-sm text-[#8C7A68]">
        Don't have an account yet?
        <RouterLink to="/auth/register" class="font-bold text-[#5C3A22] hover:underline">
          Create an account
        </RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToast } from '@/composables/useToast'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseCheckbox from '@/components/ui/BaseCheckbox.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseAlert from '@/components/ui/BaseAlert.vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const toast = useToast()

const form = ref({
  email: '',
  password: '',
  remember: false
})

const loading = ref(false)
const errorMessage = ref('')
const errors = ref({})

async function handleLogin() {
  loading.value = true
  errorMessage.value = ''
  errors.value = {}

  try {
    await authStore.login(form.value)
    toast.success('Welcome back to ABCDips & Treats!', 'Signed In')
    const redirectPath = route.query.redirect || '/account'
    router.push(redirectPath)
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {}
      errorMessage.value = err.response.data.message || 'Invalid email or password.'
    } else {
      errorMessage.value = err.response?.data?.message || 'Login failed. Please check your credentials.'
    }
  } finally {
    loading.value = false
  }
}
</script>

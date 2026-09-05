<template>
    <header :class="[
        'fixed top-0 left-0 right-0 z-50 transition-all duration-300',
        scrolled
            ? 'glass-surface shadow-lg border-b border-[#C08E5D]/40'
            : 'bg-[#FBF3E7]/60 dark:bg-[#140D09]/60 backdrop-blur-md border-b border-[#C08E5D]/10'
    ]">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 flex items-center justify-between h-18">

            <!-- Brand Logo & Wordmark -->
            <RouterLink to="/" class="flex items-center gap-2.5 group shrink-0"
                v-tooltip="'ABCDips &amp; Treats — Home'">
                <img src="/images/logo.png" alt="ABCDips &amp; Treats"
                    class="h-10 sm:h-11 w-auto transition-transform duration-300 group-hover:scale-105" />
                <div class="hidden xl:block">
                    <span
                        class="font-extrabold text-base text-ink dark:text-surface tracking-tight block leading-none">ABCDips
                        &amp;
                        Treats</span>
                    <span class="font-['Caveat'] text-brand-caramel text-xs leading-none">Bake wth love</span>
                </div>
            </RouterLink>

            <!-- Desktop Navigation Links (Single Line, Never Wrap) -->
            <nav class="hidden lg:flex items-center gap-2 xl:gap-4 flex-1 justify-center max-w-3xl px-1">
                <RouterLink v-for="link in navLinks" :key="link.to" :to="link.to" :class="[
                    'relative px-1 py-1.5 text-[11px] lg:text-xs xl:text-sm whitespace-nowrap transition-colors duration-200 shrink-0 group',
                    isLinkActive(link.to)
                        ? 'text-brand-choco dark:text-[#E2C08A] font-extrabold'
                        : 'text-ink dark:text-surface hover:text-brand-choco dark:hover:text-[#E2C08A] font-bold'
                ]">
                    {{ link.label }}
                    <!-- Animated Underline -->
                    <span :class="[
                        'absolute bottom-0 left-0 w-full h-[2px] bg-brand-choco dark:bg-[#E2C08A] transition-all duration-300 ease-out',
                        isLinkActive(link.to) ? 'scale-x-100 opacity-100' : 'scale-x-0 opacity-0 group-hover:scale-x-100 group-hover:opacity-100 origin-center'
                    ]"></span>
                </RouterLink>
            </nav>

            <!-- Right Actions (User Account, Cart, Theme Toggle, Mobile Menu) -->
            <div class="flex items-center gap-1.5 sm:gap-2.5 shrink-0">

                <!-- Signed-in User Menu Dropdown -->
                <div v-if="authStore.isAuthenticated" class="relative" ref="userMenuRef">
                    <button @click="userMenuOpen = !userMenuOpen" v-tooltip="'Manage profile, orders &amp; wishlist'"
                        class="flex items-center gap-1.5 px-2.5 py-1.5 sm:px-3 sm:py-2 rounded-xl text-xs sm:text-sm font-bold text-brand-choco dark:text-[#E2C08A] bg-brand-tan/20 hover:bg-brand-tan/35 transition-colors">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span
                            class="hidden sm:inline max-w-[85px] md:max-w-[120px] xl:max-w-[150px] truncate whitespace-nowrap">{{
                                authStore.userName || 'Account' }}</span>
                        <svg class="w-3 h-3 shrink-0 transition-transform duration-300"
                            :class="userMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Account Dropdown Panel -->
                    <Transition enter-active-class="transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]"
                        enter-from-class="opacity-0 scale-95 translate-y-2"
                        enter-to-class="opacity-100 scale-100 translate-y-0"
                        leave-active-class="transition-all duration-200 ease-in"
                        leave-from-class="opacity-100 scale-100 translate-y-0"
                        leave-to-class="opacity-0 scale-95 translate-y-1">
                        <div v-if="userMenuOpen"
                            class="absolute right-0 top-full mt-3 w-56 glass-surface rounded-2xl shadow-xl border border-brand-caramel/20 overflow-hidden z-50">
                            <!-- User Profile Header -->
                            <div class="px-4 py-3 border-b border-brand-caramel/20 bg-surface/50 dark:bg-[#140D09]/50">
                                <p class="text-xs text-warm-gray">Signed in as</p>
                                <p class="text-sm font-bold text-ink dark:text-surface truncate">{{
                                    authStore.userEmail }}</p>
                            </div>

                            <!-- Links -->
                            <div class="py-1">
                                <RouterLink v-for="item in accountLinks" :key="item.to" :to="item.to"
                                    @click="userMenuOpen = false"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-ink dark:text-surface hover:bg-brand-tan/20 hover:text-brand-choco dark:hover:text-[#E2C08A] transition-colors">
                                    <component :is="item.icon" class="w-4 h-4 text-brand-caramel" />
                                    {{ item.label }}
                                </RouterLink>
                            </div>

                            <!-- Sign Out Action -->
                            <div class="border-t border-brand-caramel/20 py-1">
                                <button @click="handleLogout" :disabled="loggingOut"
                                    class="flex items-center gap-3 w-full px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors disabled:opacity-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    {{ loggingOut ? 'Signing out...' : 'Sign Out' }}
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>

                <!-- Guest Sign In Link -->
                <RouterLink v-else to="/auth/login" v-tooltip="'Sign in to view orders &amp; wishlist'"
                    class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl text-xs sm:text-sm font-bold text-brand-choco dark:text-[#E2C08A] hover:bg-brand-tan/20 transition-all whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Sign In
                </RouterLink>

                <!-- Dark Mode Toggle Button -->
                <button @click="toggleDarkMode($event)" v-tooltip="isDark ? 'Switch to Warm Daylight Mode' : 'Switch to Midnight Dark Mode'"
                    class="group relative w-10 h-10 rounded-2xl flex items-center justify-center p-0 text-brand-choco dark:text-[#E2C08A] bg-brand-tan/20 hover:bg-brand-tan/35 dark:bg-[#2A1C13] dark:hover:bg-[#3B281B] border border-brand-caramel/30 dark:border-brand-caramel/40 theme-toggle-btn shadow-sm hover:shadow-md cursor-pointer select-none shrink-0 overflow-hidden transition-all duration-300 active:scale-90"
                    aria-label="Toggle Dark Mode">
                    
                    <!-- Ambient Glow Flare -->
                    <span class="absolute inset-0 rounded-2xl bg-gradient-to-tr from-brand-tan/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none" />

                    <Transition mode="out-in"
                        enter-active-class="transition-all duration-350 ease-[cubic-bezier(0.16,1,0.3,1)]"
                        enter-from-class="opacity-0 rotate-180 scale-50"
                        enter-to-class="opacity-100 rotate-0 scale-100"
                        leave-active-class="transition-all duration-200 ease-in"
                        leave-from-class="opacity-100 rotate-0 scale-100"
                        leave-to-class="opacity-0 -rotate-180 scale-50">
                        <Sun v-if="isDark" class="w-5 h-5 text-[#E2C08A] group-hover:rotate-45 transition-transform duration-300" />
                        <Moon v-else class="w-5 h-5 text-brand-choco group-hover:-rotate-12 transition-transform duration-300" />
                    </Transition>
                </button>

                <!-- Basket Button (Only shown when authenticated) -->
                <button v-if="authStore.isAuthenticated" @click="cartStore.openDrawer = true"
                    v-tooltip="'View your selected treats &amp; cart subtotal'"
                    class="relative flex items-center gap-1.5 bg-brand-choco text-surface dark:bg-[#E2C08A] dark:text-[#1C1410] dark:hover:bg-brand-tan px-2.5 py-1.5 sm:px-3.5 sm:py-2 rounded-xl hover:bg-[#4a2e1b] transition-all duration-200 shadow-sm shrink-0 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span class="hidden sm:inline text-xs font-bold">Basket</span>
                    <span v-if="cartStore.itemCount > 0" :key="cartStore.itemCount"
                        :class="[
                            'w-5 h-5 rounded-full bg-brand-tan text-ink dark:bg-[#1C1410] dark:text-[#E2C08A] font-extrabold text-[10px] flex items-center justify-center -mr-0.5',
                            triggerBounce ? 'spring-bounce' : ''
                        ]">{{
                            cartStore.itemCount }}</span>
                </button>

                <!-- Mobile Menu Hamburger -->
                <button @click="mobileOpen = true" v-tooltip="'Toggle navigation menu'"
                    class="lg:hidden p-2 rounded-xl hover:bg-brand-tan/20 transition-colors"
                    aria-label="Open Navigation">
                    <svg class="w-5 h-5 text-ink dark:text-surface" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Full-Screen Mobile Overlay Navigation -->
        <Transition enter-active-class="transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]"
            enter-from-class="opacity-0" enter-to-class="opacity-100"
            leave-active-class="transition-all duration-200 ease-in" leave-from-class="opacity-100"
            leave-to-class="opacity-0">
            <div v-if="mobileOpen"
                class="fixed inset-0 z-[60] bg-ink/95 backdrop-blur-xl flex flex-col pt-20 px-6 pb-12 overflow-y-auto">
                <!-- Close Button -->
                <button @click="mobileOpen = false" class="absolute top-5 right-5 p-2 rounded-full bg-surface/10 text-surface hover:bg-surface/20 transition-colors" aria-label="Close menu">
                    <X class="w-6 h-6" />
                </button>
                
                <div class="flex flex-col gap-6 w-full max-w-sm mx-auto flex-1 justify-center">
                    <TransitionGroup enter-active-class="transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]"
                        enter-from-class="opacity-0 translate-x-8" enter-to-class="opacity-100 translate-x-0">
                        <RouterLink v-for="(link, index) in navLinks" :key="link.to" :to="link.to" @click="mobileOpen = false"
                            :style="{ transitionDelay: `${index * 50}ms` }"
                            :class="[
                                'text-2xl font-bold transition-colors w-max',
                                isLinkActive(link.to)
                                    ? 'text-[#E2C08A]'
                                    : 'text-surface hover:text-[#E2C08A]'
                            ]">{{ link.label }}</RouterLink>

                        <!-- Mobile Account Links -->
                        <div key="account-actions" class="border-t border-[#C08E5D]/30 mt-6 pt-6 flex flex-col gap-4" :style="{ transitionDelay: `${navLinks.length * 50}ms` }">
                            <RouterLink v-if="!authStore.isAuthenticated" to="/auth/login" @click="mobileOpen = false"
                                class="text-xl font-bold text-[#E2C08A] hover:text-white transition-colors">Sign In /
                                Create Account
                            </RouterLink>
                            <template v-else>
                                <RouterLink v-for="(item, i) in accountLinks" :key="item.to" :to="item.to"
                                    @click="mobileOpen = false"
                                    :style="{ transitionDelay: `${(navLinks.length + 1 + i) * 50}ms` }"
                                    class="text-lg font-medium text-surface/80 hover:text-[#E2C08A] transition-colors flex items-center gap-3">
                                    <component :is="item.icon" class="w-5 h-5 text-[#E2C08A]" />
                                    {{ item.label }}
                                </RouterLink>
                                <button @click="handleLogout"
                                    :style="{ transitionDelay: `${(navLinks.length + 1 + accountLinks.length) * 50}ms` }"
                                    class="text-left mt-2 text-lg font-semibold text-red-400 hover:text-red-300 transition-colors flex items-center gap-3 w-max">
                                    <LogOut class="w-5 h-5" />
                                    Sign Out
                                </button>
                            </template>
                        </div>
                    </TransitionGroup>
                </div>
            </div>
        </Transition>
    </header>

    <!-- Offset spacer for fixed navbar -->
    <div class="h-18" />
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { Sun, Moon, Package, Heart, User, ShoppingBag, LogOut, LogIn, Menu, X, ChevronDown } from 'lucide-vue-next'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import { useWishlistStore } from '@/stores/wishlist'
import { useToast } from '@/composables/useToast'
import { useDarkMode } from '@/composables/useDarkMode'

const authStore = useAuthStore()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()
const router = useRouter()
const route = useRoute()
const toast = useToast()
const { isDark, toggleDarkMode } = useDarkMode()

const scrolled = ref(false)
const userMenuOpen = ref(false)
const mobileOpen = ref(false)
const loggingOut = ref(false)
const userMenuRef = ref(null)
const triggerBounce = ref(false)

// Watch cart items to trigger bounce animation
watch(() => cartStore.itemCount, (newVal, oldVal) => {
    if (newVal !== oldVal) {
        triggerBounce.value = false
        // small timeout to restart animation
        setTimeout(() => {
            triggerBounce.value = true
        }, 50)
    }
})

const navLinks = [
    { to: '/', label: 'Home' },
    { to: '/shop', label: 'Shop' },
    { to: '/best-sellers', label: 'Best Sellers' },
    { to: '/new-arrivals', label: 'New Arrivals' },
    { to: '/custom-orders', label: 'Custom Cakes' },
    { to: '/about', label: 'About' },
    { to: '/blog', label: 'Blog & Vlog' },
]

function isLinkActive(toPath) {
    if (toPath === '/') return route.path === '/'
    return route.path.startsWith(toPath)
}

const accountLinks = computed(() => [
    {
        to: '/account/orders',
        label: 'My Orders',
        icon: Package
    },
    {
        to: '/account/wishlist',
        label: wishlistStore.count > 0 ? `My Wishlist (${wishlistStore.count})` : 'My Wishlist',
        icon: Heart
    },
    {
        to: '/account/profile',
        label: 'My Profile',
        icon: User
    },
])

async function handleLogout() {
    loggingOut.value = true
    userMenuOpen.value = false
    mobileOpen.value = false
    try {
        await authStore.logout()
        toast.success('You have been signed out. See you soon!', 'Signed Out')
        router.push({ name: 'home' })
    } catch {
        toast.error('Logout failed. Please try again.', 'Auth Error')
    } finally {
        loggingOut.value = false
    }
}

const onScroll = () => { scrolled.value = window.scrollY > 10 }
const onClickOutside = (e) => {
    if (userMenuRef.value && !userMenuRef.value.contains(e.target)) {
        userMenuOpen.value = false
    }
}

onMounted(() => {
    window.addEventListener('scroll', onScroll, { passive: true })
    document.addEventListener('click', onClickOutside)
})
onUnmounted(() => {
    window.removeEventListener('scroll', onScroll)
    document.removeEventListener('click', onClickOutside)
})
</script>

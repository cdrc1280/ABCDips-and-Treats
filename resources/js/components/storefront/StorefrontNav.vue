<template>
    <header :class="[
        'fixed top-0 left-0 right-0 z-50 transition-all duration-300',
        scrolled
            ? 'bg-[#FBF3E7]/95 dark:bg-[#140D09]/95 backdrop-blur-xl shadow-md border-b border-[#C08E5D]/30'
            : 'bg-[#FBF3E7]/85 dark:bg-[#140D09]/85 backdrop-blur-md border-b border-[#C08E5D]/15'
    ]">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 flex items-center justify-between h-18">

            <!-- Brand Logo & Wordmark -->
            <RouterLink to="/" class="flex items-center gap-2.5 group shrink-0"
                v-tooltip="'ABCDips &amp; Treats — Home'">
                <img src="/images/logo.png" alt="ABCDips &amp; Treats"
                    class="h-10 sm:h-11 w-auto transition-transform duration-300 group-hover:scale-105" />
                <div class="hidden xl:block">
                    <span
                        class="font-extrabold text-base text-[#1C1410] dark:text-[#FBF3E7] tracking-tight block leading-none">ABCDips
                        &amp;
                        Treats</span>
                    <span class="font-['Caveat'] text-[#C08E5D] text-xs leading-none">Bake wth love</span>
                </div>
            </RouterLink>

            <!-- Desktop Navigation Links (Single Line, Never Wrap) -->
            <nav class="hidden lg:flex items-center gap-0.5 xl:gap-1.5 flex-1 justify-center max-w-3xl px-1">
                <RouterLink v-for="link in navLinks" :key="link.to" :to="link.to" :class="[
                    'px-2 py-1.5 lg:px-2.5 xl:px-3.5 xl:py-2 rounded-xl text-[11px] lg:text-xs xl:text-sm font-bold whitespace-nowrap transition-all duration-200 shrink-0',
                    isLinkActive(link.to)
                        ? 'bg-[#5C3A22] text-[#FBF3E7] shadow-xs dark:bg-[#C08E5D] dark:text-[#1C1410]'
                        : 'text-[#1C1410] dark:text-[#FBF3E7] hover:text-[#5C3A22] dark:hover:text-[#E2C08A] hover:bg-[#D9A876]/20'
                ]">
                    {{ link.label }}
                </RouterLink>
            </nav>

            <!-- Right Actions (User Account, Cart, Theme Toggle, Mobile Menu) -->
            <div class="flex items-center gap-1.5 sm:gap-2.5 shrink-0">

                <!-- Signed-in User Menu Dropdown -->
                <div v-if="authStore.isAuthenticated" class="relative" ref="userMenuRef">
                    <button @click="userMenuOpen = !userMenuOpen" v-tooltip="'Manage profile, orders &amp; wishlist'"
                        class="flex items-center gap-1.5 px-2.5 py-1.5 sm:px-3 sm:py-2 rounded-xl text-xs sm:text-sm font-bold text-[#5C3A22] dark:text-[#E2C08A] bg-[#D9A876]/20 hover:bg-[#D9A876]/35 transition-colors">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span
                            class="hidden sm:inline max-w-[85px] md:max-w-[120px] xl:max-w-[150px] truncate whitespace-nowrap">{{
                                authStore.userName || 'Account' }}</span>
                        <svg class="w-3 h-3 shrink-0 transition-transform duration-200"
                            :class="userMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Account Dropdown Panel -->
                    <Transition enter-active-class="transition-all duration-150 ease-out"
                        enter-from-class="opacity-0 scale-95 translate-y-1"
                        enter-to-class="opacity-100 scale-100 translate-y-0"
                        leave-active-class="transition-all duration-100 ease-in"
                        leave-from-class="opacity-100 scale-100 translate-y-0"
                        leave-to-class="opacity-0 scale-95 translate-y-1">
                        <div v-if="userMenuOpen"
                            class="absolute right-0 top-full mt-2 w-56 bg-white dark:bg-[#1E1510] rounded-2xl shadow-2xl border border-[#C08E5D]/20 overflow-hidden z-50">
                            <!-- User Profile Header -->
                            <div class="px-4 py-3 bg-[#FBF3E7] dark:bg-[#140D09] border-b border-[#C08E5D]/20">
                                <p class="text-xs text-[#8C7A68]">Signed in as</p>
                                <p class="text-sm font-bold text-[#1C1410] dark:text-[#FBF3E7] truncate">{{
                                    authStore.userEmail }}</p>
                            </div>

                            <!-- Links -->
                            <div class="py-1">
                                <RouterLink v-for="item in accountLinks" :key="item.to" :to="item.to"
                                    @click="userMenuOpen = false"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-[#1C1410] dark:text-[#FBF3E7] hover:bg-[#D9A876]/20 hover:text-[#5C3A22] dark:hover:text-[#E2C08A] transition-colors">
                                    <span class="text-[#C08E5D]" v-html="item.icon" />
                                    {{ item.label }}
                                </RouterLink>
                            </div>

                            <!-- Sign Out Action -->
                            <div class="border-t border-[#C08E5D]/20 py-1">
                                <button @click="handleLogout" :disabled="loggingOut"
                                    class="flex items-center gap-3 w-full px-4 py-2.5 text-sm font-semibold text-error hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors disabled:opacity-50">
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
                    class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl text-xs sm:text-sm font-bold text-[#5C3A22] dark:text-[#E2C08A] hover:bg-[#D9A876]/20 transition-all whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Sign In
                </RouterLink>

                <!-- Dark Mode Toggle Button -->
                <button @click="toggleDarkMode" v-tooltip="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
                    class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center p-0 text-brand-choco dark:text-surface-400 bg-brand-tan/20 hover:bg-brand-tan/35 theme-toggle-btn shadow-xs cursor-pointer select-none shrink-0"
                    aria-label="Toggle Dark Mode">
                    <span v-if="isDark"
                        class="text-sm sm:text-base leading-none flex items-center justify-center transform hover:rotate-90 transition-transform duration-500">☀️</span>
                    <span v-else
                        class="text-sm sm:text-base leading-none flex items-center justify-center transform hover:-rotate-45 transition-transform duration-500">🌙</span>
                </button>

                <!-- Basket Button (Only shown when authenticated) -->
                <button v-if="authStore.isAuthenticated" @click="cartStore.openDrawer = true"
                    v-tooltip="'View your selected treats &amp; cart subtotal'"
                    class="relative flex items-center gap-1.5 bg-brand-choco text-surface dark:bg-[#E2C08A] dark:text-[#1C1410] dark:hover:bg-[#D9A876] px-2.5 py-1.5 sm:px-3.5 sm:py-2 rounded-xl hover:bg-choco-600 transition-all duration-200 shadow-sm shrink-0 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span class="hidden sm:inline text-xs font-bold">Basket</span>
                    <span v-if="cartStore.itemCount > 0"
                        class="w-5 h-5 rounded-full bg-brand-tan text-ink dark:bg-[#1C1410] dark:text-[#E2C08A] font-extrabold text-[10px] flex items-center justify-center -mr-0.5">{{
                            cartStore.itemCount }}</span>
                </button>

                <!-- Mobile Menu Hamburger -->
                <button @click="mobileOpen = !mobileOpen" v-tooltip="'Toggle navigation menu'"
                    class="lg:hidden p-2 rounded-xl hover:bg-brand-tan/20 transition-colors"
                    aria-label="Toggle Navigation">
                    <svg class="w-5 h-5 text-[#1C1410] dark:text-[#FBF3E7]" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path v-if="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile & Tablet Drawer Navigation -->
        <Transition enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition-all duration-150 ease-in" leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-2">
            <div v-if="mobileOpen"
                class="lg:hidden border-t border-[#C08E5D]/20 bg-[#FBF3E7]/98 dark:bg-[#140D09]/98 pb-4 shadow-2xl backdrop-blur-xl">
                <div class="max-w-7xl mx-auto px-4 pt-3 flex flex-col gap-1">
                    <RouterLink v-for="link in navLinks" :key="link.to" :to="link.to" @click="mobileOpen = false"
                        :class="[
                            'px-4 py-3 rounded-xl text-sm font-semibold transition-colors',
                            isLinkActive(link.to)
                                ? 'bg-[#5C3A22] text-[#FBF3E7] font-bold dark:bg-[#C08E5D] dark:text-[#1C1410] shadow-xs'
                                : 'text-[#1C1410] dark:text-[#FBF3E7] hover:text-[#5C3A22] dark:hover:text-[#E2C08A] hover:bg-[#D9A876]/20'
                        ]">{{ link.label }}</RouterLink>

                    <div class="border-t border-[#C08E5D]/20 mt-2 pt-2">
                        <RouterLink v-if="!authStore.isAuthenticated" to="/auth/login" @click="mobileOpen = false"
                            class="block px-4 py-3 text-sm font-bold text-[#5C3A22] dark:text-[#E2C08A] hover:bg-[#D9A876]/20 rounded-xl transition-colors">Sign In /
                            Create Account
                        </RouterLink>
                        <template v-else>
                            <RouterLink v-for="item in accountLinks" :key="item.to" :to="item.to"
                                @click="mobileOpen = false"
                                class="block px-4 py-3 text-sm font-medium text-[#1C1410] dark:text-[#FBF3E7] hover:text-[#5C3A22] dark:hover:text-[#E2C08A] hover:bg-[#D9A876]/20 rounded-xl transition-colors">
                                {{ item.label }}</RouterLink>
                            <button @click="handleLogout"
                                class="block w-full text-left px-4 py-3 text-sm font-semibold text-error hover:bg-red-50 dark:hover:bg-red-950/30 rounded-xl transition-colors">Sign
                                Out</button>
                        </template>
                    </div>
                </div>
            </div>
        </Transition>
    </header>

    <!-- Offset spacer for fixed navbar -->
    <div class="h-18" />
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
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
        icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>'
    },
    {
        to: '/account/wishlist',
        label: wishlistStore.count > 0 ? `My Wishlist (${wishlistStore.count})` : 'My Wishlist',
        icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>'
    },
    {
        to: '/account/profile',
        label: 'My Profile',
        icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>'
    },
])

async function handleLogout() {
    loggingOut.value = true
    userMenuOpen.value = false
    mobileOpen.value = false
    try {
        await authStore.logout()
        toast.success('You have been signed out. See you soon! 🍞', 'Signed Out')
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

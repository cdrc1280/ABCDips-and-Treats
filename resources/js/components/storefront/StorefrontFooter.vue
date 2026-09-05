<template>
    <footer class="bg-[#1C1410] border-t border-[#C08E5D]/15 mt-auto">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-10 lg:gap-12 mb-12 text-center sm:text-left">
                
                <!-- Brand Story (Left, 5 cols on lg) -->
                <div class="md:col-span-6 lg:col-span-5 flex flex-col items-center sm:items-start">
                    <div class="flex items-center gap-3 mb-6">
                        <img src="/images/logo.png" alt="ABCDips & Treats" class="h-10 w-auto brightness-200" />
                        <div class="text-left">
                            <span class="font-extrabold text-sm text-[#FBF3E7] block tracking-wide">ABCDips &amp; Treats</span>
                            <span class="font-['Caveat'] text-[#E2C08A] text-lg leading-none block mt-0.5">bake with love</span>
                        </div>
                    </div>
                    <p class="text-sm text-[#8C7A68] leading-relaxed mb-8 max-w-sm">
                        Handcrafted pastries baked with 100% real butter and a whole lot of love. Based in Cavite.
                    </p>
                    <div class="flex items-center gap-3 justify-center sm:justify-start">
                        <a href="https://www.facebook.com/abcdipsandtreats" target="_blank" rel="noopener noreferrer"
                            v-tooltip="'Visit our official Facebook page'"
                            class="w-9 h-9 rounded-xl bg-[#2A1C13] border border-[#C08E5D]/20 flex items-center justify-center text-[#C5B4A4] hover:text-[#E2C08A] hover:bg-[#3D291D] transition-all shadow-sm">
                            <Facebook class="w-4 h-4" />
                        </a>
                        <a href="https://www.instagram.com/abcdips_treats" target="_blank" rel="noopener noreferrer"
                            v-tooltip="'Follow @abcdips_treats on Instagram'"
                            class="w-9 h-9 rounded-xl bg-[#2A1C13] border border-[#C08E5D]/20 flex items-center justify-center text-[#C5B4A4] hover:text-[#E2C08A] hover:bg-[#3D291D] transition-all shadow-sm">
                            <Instagram class="w-4 h-4" />
                        </a>
                    </div>
                </div>

                <!-- Links (Middle, 4 cols on lg) -->
                <div class="md:col-span-6 lg:col-span-4 grid grid-cols-2 gap-8">
                    <!-- Quick Links -->
                    <div class="text-center sm:text-left">
                        <h3 class="text-[#E2C08A] font-bold uppercase text-xs tracking-widest mb-6">Shop</h3>
                        <ul class="space-y-3">
                            <li v-for="link in shopLinks" :key="link.to">
                                <RouterLink :to="link.to"
                                    class="text-[#C5B4A4] hover:text-[#E2C08A] transition-colors text-sm">
                                    {{ link.label }}
                                </RouterLink>
                            </li>
                        </ul>
                    </div>

                    <!-- Company Links -->
                    <div class="text-center sm:text-left">
                        <h3 class="text-[#E2C08A] font-bold uppercase text-xs tracking-widest mb-6">Company</h3>
                        <ul class="space-y-3">
                            <li v-for="link in companyLinks" :key="link.to">
                                <RouterLink :to="link.to"
                                    class="text-[#C5B4A4] hover:text-[#E2C08A] transition-colors text-sm">
                                    {{ link.label }}
                                </RouterLink>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Newsletter (Right, 3 cols on lg) -->
                <div class="md:col-span-12 lg:col-span-3 flex justify-center sm:justify-start lg:justify-end">
                    <div class="bg-[#1E1510] border border-[#C08E5D]/15 rounded-3xl p-6 shadow-lg w-full max-w-md sm:max-w-none">
                        <h3 class="text-[#E2C08A] font-bold uppercase text-xs tracking-widest mb-3">Stay in the Loop</h3>
                        <p class="text-sm text-[#8C7A68] mb-5 leading-relaxed">
                            Get fresh pastry updates, promos, and seasonal specials in your inbox.
                        </p>
                        <form @submit.prevent="subscribe" class="flex flex-col gap-3">
                            <div class="relative">
                                <input v-model="email" type="email" placeholder="your@email.com"
                                    class="w-full bg-[#140D09] border border-[#3D291D] rounded-xl pl-4 pr-10 py-3 text-sm text-[#FBF3E7] placeholder-[#8C7A68] focus:outline-none focus:border-[#C08E5D] focus:ring-1 focus:ring-[#C08E5D] transition-all" />
                                <button type="submit" :disabled="subscribed || subscribing"
                                    v-tooltip="'Subscribe to updates & exclusive promos'"
                                    class="absolute right-1.5 top-1.5 bottom-1.5 bg-[#C08E5D] text-[#1C1410] rounded-lg px-3 flex items-center justify-center text-sm font-bold hover:bg-[#E2C08A] disabled:opacity-50 transition-colors shrink-0">
                                    <Check v-if="subscribed" class="w-4 h-4" />
                                    <Send v-else class="w-4 h-4" />
                                </button>
                            </div>
                        </form>
                        <p v-if="subscribed" class="text-xs text-[#E2C08A] font-bold mt-3 flex items-center gap-1.5 justify-center sm:justify-start">
                            <Check class="w-3.5 h-3.5" /> Successfully subscribed!
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Copyright bar -->
        <div class="border-t border-[#C08E5D]/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-[#8C7A68]">
                    &copy; {{ new Date().getFullYear() }} ABCDips &amp; Treats. All rights reserved.
                </p>
                <div class="flex gap-6">
                    <RouterLink to="/privacy" class="text-sm text-[#8C7A68] hover:text-[#E2C08A] transition-colors">
                        Privacy Policy
                    </RouterLink>
                    <RouterLink to="/terms" class="text-sm text-[#8C7A68] hover:text-[#E2C08A] transition-colors">
                        Terms of Service
                    </RouterLink>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { ref } from 'vue'
import { Check, Facebook, Instagram, Send, ArrowRight } from 'lucide-vue-next'

const email = ref('')
const subscribed = ref(false)
const subscribing = ref(false)

const shopLinks = [
    { to: '/shop', label: 'All Products' },
    { to: '/best-sellers', label: 'Best Sellers' },
    { to: '/featured', label: 'Featured' },
    { to: '/new-arrivals', label: 'New Arrivals' },
    { to: '/custom-orders', label: 'Custom Cakes' },
]

const companyLinks = [
    { to: '/about', label: 'About Us' },
    { to: '/blog', label: 'Blog & Vlog' },
    { to: '/contact', label: 'Contact' },
    { to: '/faq', label: 'FAQ' },
    { to: '/privacy', label: 'Privacy Policy' },
    { to: '/terms', label: 'Terms of Service' },
    { to: '/suggestions', label: 'Suggest a Feature' },
]

async function subscribe() {
    if (!email.value) return
    subscribing.value = true
    await new Promise(r => setTimeout(r, 800))
    subscribed.value = true
    subscribing.value = false
}
</script>

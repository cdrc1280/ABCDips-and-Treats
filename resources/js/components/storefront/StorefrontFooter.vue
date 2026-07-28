<template>
    <footer class="bg-[#1C1410] text-[#FBF3E7] mt-auto">
        <div class="max-w-7xl mx-auto px-6 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">

                <!-- Brand -->
                <div class="lg:col-span-1">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="/images/logo.png" alt="ABCDips & Treats" class="h-10 w-auto brightness-200" />
                        <div>
                            <span class="font-extrabold text-sm text-[#FBF3E7] block">ABCDips & Treats</span>
                            <span class="font-['Caveat'] text-[#D9A876] text-xs">lovely bakery</span>
                        </div>
                    </div>
                    <p class="text-xs text-[#FBF3E7]/50 leading-relaxed mb-5">Handcrafted pastries baked with 100% real
                        butter and a whole lot of love. Based in Cavite.</p>
                    <div class="flex gap-3">
                        <a href="#"
                            class="w-8 h-8 bg-[#FBF3E7]/10 rounded-lg flex items-center justify-center hover:bg-[#D9A876]/30 transition-colors text-xs">📘</a>
                        <a href="#"
                            class="w-8 h-8 bg-[#FBF3E7]/10 rounded-lg flex items-center justify-center hover:bg-[#D9A876]/30 transition-colors text-xs">📸</a>
                    </div>
                </div>

                <!-- Shop Links -->
                <div>
                    <h3 class="text-xs font-bold text-[#D9A876] uppercase tracking-wider mb-4">Shop</h3>
                    <ul class="space-y-2.5">
                        <li v-for="link in shopLinks" :key="link.to">
                            <RouterLink :to="link.to"
                                class="text-sm text-[#FBF3E7]/60 hover:text-[#D9A876] transition-colors">{{ link.label
                                }}</RouterLink>
                        </li>
                    </ul>
                </div>

                <!-- Company Links -->
                <div>
                    <h3 class="text-xs font-bold text-[#D9A876] uppercase tracking-wider mb-4">Company</h3>
                    <ul class="space-y-2.5">
                        <li v-for="link in companyLinks" :key="link.to">
                            <RouterLink :to="link.to"
                                class="text-sm text-[#FBF3E7]/60 hover:text-[#D9A876] transition-colors">{{ link.label
                                }}</RouterLink>
                        </li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div>
                    <h3 class="text-xs font-bold text-[#D9A876] uppercase tracking-wider mb-4">Stay in the Loop</h3>
                    <p class="text-xs text-[#FBF3E7]/50 mb-4">Get fresh pastry updates, promos, and seasonal specials in
                        your inbox.</p>
                    <form @submit.prevent="subscribe" class="flex gap-2">
                        <input v-model="email" type="email" placeholder="your@email.com"
                            class="flex-1 bg-[#FBF3E7]/10 border border-[#FBF3E7]/10 rounded-xl px-3 py-2.5 text-xs text-[#FBF3E7] placeholder-[#FBF3E7]/30 focus:outline-none focus:border-[#D9A876]/50 transition-colors" />
                        <button type="submit" :disabled="subscribed || subscribing"
                            class="bg-[#D9A876] text-[#1C1410] rounded-xl px-3 py-2.5 text-xs font-bold hover:bg-[#C08E5D] disabled:opacity-50 transition-colors flex-shrink-0">
                            {{ subscribed ? '✓' : 'Join' }}
                        </button>
                    </form>
                    <p v-if="subscribed" class="text-xs text-[#6B8F5E] mt-2">✓ You're subscribed!</p>
                </div>
            </div>
        </div>

        <!-- Copyright bar -->
        <div class="border-t border-[#FBF3E7]/10">
            <div class="max-w-7xl mx-auto px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-2">
                <p class="text-xs text-[#FBF3E7]/30">&copy; {{ new Date().getFullYear() }} ABCDips & Treats. All rights
                    reserved.</p>
                <div class="flex gap-4">
                    <RouterLink to="/privacy" class="text-xs text-[#FBF3E7]/30 hover:text-[#D9A876] transition-colors">
                        Privacy Policy</RouterLink>
                    <RouterLink to="/terms" class="text-xs text-[#FBF3E7]/30 hover:text-[#D9A876] transition-colors">
                        Terms of Service</RouterLink>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { ref } from 'vue'
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
    { to: '/blog', label: 'Blog' },
    { to: '/contact', label: 'Contact' },
    { to: '/faq', label: 'FAQ' },
    { to: '/privacy', label: 'Privacy Policy' },
    { to: '/terms', label: 'Terms of Service' },
]
async function subscribe() {
    if (!email.value) return
    subscribing.value = true
    await new Promise(r => setTimeout(r, 800))
    subscribed.value = true
    subscribing.value = false
}
</script>

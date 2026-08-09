<template>
    <footer class="bg-[#1C1410] dark:bg-[#120B07] text-[#FBF3E7] border-t border-[#C08E5D]/20 mt-auto">
        <div class="max-w-7xl mx-auto px-6 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">

                <!-- Brand -->
                <div class="lg:col-span-1">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="/images/logo.png" alt="ABCDips & Treats" class="h-10 w-auto brightness-200" />
                        <div>
                            <span class="font-extrabold text-sm text-[#FBF3E7] block">ABCDips &amp; Treats</span>
                            <span class="font-['Caveat'] text-[#E2C08A] text-xs">bake with love</span>
                        </div>
                    </div>
                    <p class="text-xs text-[#FBF3E7]/70 leading-relaxed mb-5">Handcrafted pastries baked with 100% real
                        butter and a whole lot of love. Based in Cavite.</p>
                    <div class="flex gap-3">
                        <a href="https://www.facebook.com/abcdipsandtreats" target="_blank" rel="noopener noreferrer"
                            v-tooltip="'Visit our official Facebook page'"
                            class="w-9 h-9 bg-[#FBF3E7]/10 hover:bg-[#C08E5D]/30 rounded-xl flex items-center justify-center transition-colors text-xs text-[#FBF3E7] hover:text-[#E2C08A]">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="https://www.instagram.com/abcdips_treats" target="_blank" rel="noopener noreferrer"
                            v-tooltip="'Follow @abcdips_treats on Instagram'"
                            class="w-9 h-9 bg-[#FBF3E7]/10 hover:bg-[#C08E5D]/30 rounded-xl flex items-center justify-center transition-colors text-xs text-[#FBF3E7] hover:text-[#E2C08A]">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Shop Links -->
                <div>
                    <h3 class="text-xs font-bold text-[#E2C08A] uppercase tracking-wider mb-4">Shop</h3>
                    <ul class="space-y-2.5">
                        <li v-for="link in shopLinks" :key="link.to">
                            <RouterLink :to="link.to"
                                class="text-sm text-[#FBF3E7]/70 hover:text-[#E2C08A] transition-colors">{{ link.label
                                }}</RouterLink>
                        </li>
                    </ul>
                </div>

                <!-- Company Links -->
                <div>
                    <h3 class="text-xs font-bold text-[#E2C08A] uppercase tracking-wider mb-4">Company</h3>
                    <ul class="space-y-2.5">
                        <li v-for="link in companyLinks" :key="link.to">
                            <RouterLink :to="link.to"
                                class="text-sm text-[#FBF3E7]/70 hover:text-[#E2C08A] transition-colors">{{ link.label
                                }}</RouterLink>
                        </li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div>
                    <h3 class="text-xs font-bold text-[#E2C08A] uppercase tracking-wider mb-4">Stay in the Loop</h3>
                    <p class="text-xs text-[#FBF3E7]/70 mb-4">Get fresh pastry updates, promos, and seasonal specials in
                        your inbox.</p>
                    <form @submit.prevent="subscribe" class="flex gap-2">
                        <input v-model="email" type="email" placeholder="your@email.com"
                            class="flex-1 bg-[#FBF3E7]/10 border border-[#FBF3E7]/15 rounded-xl px-3 py-2.5 text-xs text-[#FBF3E7] placeholder-[#FBF3E7]/40 focus:outline-none focus:border-[#E2C08A] transition-colors" />
                        <button type="submit" :disabled="subscribed || subscribing"
                            v-tooltip="'Subscribe to updates & exclusive promos'"
                            class="bg-[#C08E5D] text-[#1C1410] rounded-xl px-3.5 py-2.5 text-xs font-bold hover:bg-[#E2C08A] disabled:opacity-50 transition-colors shrink-0">
                            {{ subscribed ? '✓' : 'Join' }}
                        </button>
                    </form>
                    <p v-if="subscribed" class="text-xs text-[#6B8F5E] font-bold mt-2">✓ You're subscribed!</p>
                </div>
            </div>
        </div>

        <!-- Copyright bar -->
        <div class="border-t border-[#FBF3E7]/10">
            <div class="max-w-7xl mx-auto px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-2">
                <p class="text-xs text-[#FBF3E7]/70 dark:text-[#FBF3E7]/80">&copy; {{ new Date().getFullYear() }} ABCDips &amp; Treats. All
                    rights
                    reserved.</p>
                <div class="flex gap-4">
                    <RouterLink to="/privacy" class="text-xs text-[#FBF3E7]/70 dark:text-[#FBF3E7]/80 hover:text-[#E2C08A] transition-colors">
                        Privacy Policy</RouterLink>
                    <RouterLink to="/terms" class="text-xs text-[#FBF3E7]/70 dark:text-[#FBF3E7]/80 hover:text-[#E2C08A] transition-colors">
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
    { to: '/blog', label: 'Blog & Vlog' },
    { to: '/contact', label: 'Contact' },
    { to: '/faq', label: 'FAQ' },
    { to: '/privacy', label: 'Privacy Policy' },
    { to: '/terms', label: 'Terms of Service' },
    { to: '/suggestions', label: '💡 Suggest a Feature' },
]
async function subscribe() {
    if (!email.value) return
    subscribing.value = true
    await new Promise(r => setTimeout(r, 800))
    subscribed.value = true
    subscribing.value = false
}
</script>

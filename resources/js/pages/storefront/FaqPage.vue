<template>
    <div class="max-w-3xl mx-auto px-6 py-16">
        <div class="text-center mb-12">
            <span class="font-['Caveat'] text-brand-caramel dark:text-[#E2C08A] text-2xl block mb-2">got questions?</span>
            <h1 class="text-4xl font-extrabold text-ink dark:text-[#FBF3E7] tracking-tight">Frequently Asked Questions</h1>
            <p class="text-warm-gray dark:text-[#C5B4A4] mt-3 max-w-lg mx-auto leading-relaxed">
                Everything you need to know about ABCDips &amp; Treats, our baking craft, delivery, and custom cakes.
            </p>
        </div>

        <!-- Category Tabs -->
        <div class="flex flex-wrap gap-2 justify-center mb-10">
            <button v-for="cat in categories" :key="cat" @click="activeCategory = cat" :class="[
                'px-4 py-2 rounded-2xl text-xs font-bold transition-all',
                activeCategory === cat
                    ? 'bg-brand-choco text-surface dark:bg-[#E2C08A] dark:text-[#1C1410] shadow-sm'
                    : 'bg-surface dark:bg-[#1E1510] text-brand-choco dark:text-[#FBF3E7] border border-brand-caramel/20 dark:border-[#C08E5D]/20 hover:bg-brand-tan/20 dark:hover:bg-[#2A1C13]'
            ]">{{ cat }}</button>
        </div>

        <!-- Accordion with Smooth Expansion -->
        <div class="space-y-3">
            <div v-for="(faq, i) in filteredFaqs" :key="i"
                class="bg-white dark:bg-[#1E1510] rounded-2xl border border-brand-caramel/20 dark:border-[#C08E5D]/20 overflow-hidden shadow-xs hover:border-brand-caramel/40 transition-colors">
                <button @click="openIndex = openIndex === i ? null : i"
                    class="w-full flex items-center justify-between px-6 py-4 text-left cursor-pointer select-none">
                    <span class="font-bold text-ink dark:text-[#FBF3E7] text-sm pr-4">{{ faq.q }}</span>
                    <ChevronDown class="w-4 h-4 text-brand-caramel dark:text-[#E2C08A] shrink-0 transition-transform duration-300"
                        :class="openIndex === i ? 'rotate-180' : ''" />
                </button>
                <Transition enter-active-class="transition-all duration-300 ease-out"
                    enter-from-class="max-h-0 opacity-0" enter-to-class="max-h-96 opacity-100"
                    leave-active-class="transition-all duration-200 ease-in" leave-from-class="max-h-96 opacity-100"
                    leave-to-class="max-h-0 opacity-0">
                    <div v-if="openIndex === i" class="px-6 pb-5 border-t border-brand-caramel/10 dark:border-[#C08E5D]/15">
                        <p class="text-sm text-warm-gray dark:text-[#C5B4A4] leading-relaxed pt-3.5">{{ faq.a }}</p>
                    </div>
                </Transition>
            </div>
        </div>

        <!-- Contact CTA Box -->
        <div class="mt-14 bg-brand-choco dark:bg-[#140D09] rounded-3xl p-8 text-center text-surface border border-brand-caramel/20 dark:border-[#C08E5D]/20 shadow-md">
            <div class="w-12 h-12 rounded-2xl bg-[#D9A876]/20 mx-auto flex items-center justify-center mb-3 text-[#E2C08A] border border-[#C08E5D]/30">
                <HelpCircle class="w-6 h-6" />
            </div>
            <h2 class="text-xl font-bold mb-2">Still have questions?</h2>
            <p class="text-surface/70 dark:text-[#C5B4A4] text-sm mb-6 max-w-md mx-auto leading-relaxed">
                Our pastry chef team is happy to help. Send us a message and we'll get back to you shortly.
            </p>
            <RouterLink to="/contact"
                class="inline-flex items-center gap-2 bg-brand-tan text-ink px-6 py-3 rounded-xl font-bold text-sm hover:bg-brand-caramel transition-colors shadow-sm">
                Contact Us →
            </RouterLink>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { HelpCircle, ChevronDown } from 'lucide-vue-next'

const openIndex = ref(null)
const activeCategory = ref('All')
const categories = ['All', 'Orders', 'Delivery', 'Products', 'Payments', 'Custom Orders']

const faqs = [
    { cat: 'Orders', q: 'How do I place an order?', a: 'Simply browse our Shop page, add items to your basket, then proceed to Checkout. You can order as a guest or create an account for faster future orders.' },
    { cat: 'Orders', q: 'Can I modify or cancel my order?', a: 'You can modify or cancel your order within 1 hour of placing it. Contact us immediately via the Contact page or message us on Facebook.' },
    { cat: 'Delivery', q: 'Where do you deliver?', a: 'We currently deliver within Cavite. Same-day delivery is available for orders placed before 10:00 AM. Orders placed after may be scheduled for next-day delivery.' },
    { cat: 'Delivery', q: 'How much is the delivery fee?', a: 'Delivery fees vary depending on your location within Cavite. The exact fee will be calculated in real-time via Lalamove during checkout.' },
    { cat: 'Products', q: 'Are your products freshly baked?', a: 'Yes! Everything is baked fresh the same day or evening of delivery. We never sell day-old pastries. Our products use 100% real creamery butter and premium ingredients.' },
    { cat: 'Products', q: 'Do you have allergen information?', a: 'Yes. Each product page lists all allergens including gluten, dairy, eggs, nuts, and soy. If you have a specific allergy, please contact us before ordering.' },
    { cat: 'Payments', q: 'What payment methods do you accept?', a: 'We accept GCash, Maya (PayMaya), QR Ph from any Philippine bank, and manual BDO Online Transfer.' },
    { cat: 'Payments', q: 'Is my payment information secure?', a: 'Absolutely. All transactions are processed through secure PayMongo payment channels. We never store your payment card details on our servers.' },
    { cat: 'Custom Orders', q: 'How do I order a custom cake?', a: 'Visit our Custom Orders page and fill out the inquiry form with your requirements (size, flavor, design theme). We\'ll get back to you within 24 hours with a quote.' },
    { cat: 'Custom Orders', q: 'How far in advance should I order a custom cake?', a: 'We recommend placing custom cake orders at least 5-7 days in advance. For large or complex designs, 2 weeks notice ensures the best result.' },
]

const filteredFaqs = computed(() =>
    activeCategory.value === 'All' ? faqs : faqs.filter(f => f.cat === activeCategory.value)
)
</script>

<template>
    <div class="max-w-3xl mx-auto px-6 py-16">
        <div class="text-center mb-12">
            <span class="font-['Caveat'] text-brand-caramel text-2xl block mb-2">got questions?</span>
            <h1 class="text-4xl font-extrabold text-ink tracking-tight">Frequently Asked Questions</h1>
            <p class="text-warm-gray mt-3">Everything you need to know about ABCDips & Treats</p>
        </div>

        <!-- Category Tabs -->
        <div class="flex flex-wrap gap-2 justify-center mb-10">
            <button v-for="cat in categories" :key="cat" @click="activeCategory = cat" :class="[
                'px-4 py-2 rounded-full text-sm font-semibold transition-all',
                activeCategory === cat
                    ? 'bg-brand-choco text-surface'
                    : 'bg-brand-tan/20 text-brand-choco hover:bg-brand-tan/40'
            ]">{{ cat }}</button>
        </div>

        <!-- Accordion -->
        <div class="space-y-3">
            <div v-for="(faq, i) in filteredFaqs" :key="i"
                class="bg-white rounded-2xl border border-brand-caramel/20 overflow-hidden shadow-sm">
                <button @click="openIndex = openIndex === i ? null : i"
                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                    <span class="font-semibold text-ink text-sm pr-4">{{ faq.q }}</span>
                    <svg class="w-5 h-5 text-brand-caramel shrink-0 transition-transform duration-300"
                        :class="openIndex === i ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <Transition enter-active-class="transition-all duration-300 ease-out"
                    enter-from-class="max-h-0 opacity-0" enter-to-class="max-h-96 opacity-100"
                    leave-active-class="transition-all duration-200 ease-in" leave-from-class="max-h-96 opacity-100"
                    leave-to-class="max-h-0 opacity-0">
                    <div v-if="openIndex === i" class="px-6 pb-5 border-t border-brand-caramel/10">
                        <p class="text-sm text-warm-gray leading-relaxed pt-4">{{ faq.a }}</p>
                    </div>
                </Transition>
            </div>
        </div>

        <!-- Contact CTA -->
        <div class="mt-14 bg-brand-choco rounded-3xl p-8 text-center text-surface">
            <div class="w-12 h-12 rounded-2xl bg-[#D9A876]/20 mx-auto flex items-center justify-center mb-3 text-[#C08E5D] border border-[#C08E5D]/30"><HelpCircle class="w-6 h-6" /></div>
            <h2 class="text-xl font-bold mb-2">Still have questions?</h2>
            <p class="text-surface/70 text-sm mb-6">Our team is happy to help. Send us a message and we'll get back to
                you shortly.</p>
            <RouterLink to="/contact"
                class="inline-flex items-center gap-2 bg-brand-tan text-ink px-6 py-3 rounded-xl font-bold text-sm hover:bg-brand-caramel transition-colors">
                Contact Us →</RouterLink>
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
    { cat: 'Delivery', q: 'How much is the delivery fee?', a: 'Delivery fees vary depending on your location within Cavite. The exact fee will be shown at checkout before you confirm your order.' },
    { cat: 'Products', q: 'Are your products freshly baked?', a: 'Yes! Everything is baked fresh the same day or evening of delivery. We never sell day-old pastries. Our products use 100% real creamery butter and premium ingredients.' },
    { cat: 'Products', q: 'Do you have allergen information?', a: 'Yes. Each product page lists all allergens including gluten, dairy, eggs, nuts, and soy. If you have a specific allergy, please contact us before ordering.' },
    { cat: 'Payments', q: 'What payment methods do you accept?', a: 'We accept GCash, Maya (PayMaya), Bank Transfer (BDO, BPI, Metrobank, UnionBank, etc.), and Cash on Delivery.' },
    { cat: 'Payments', q: 'Is my payment information secure?', a: 'Absolutely. All transactions are processed through secure payment channels. We never store your payment card details on our servers.' },
    { cat: 'Custom Orders', q: 'How do I order a custom cake?', a: 'Visit our Custom Orders page and fill out the inquiry form with your requirements (size, flavor, design theme). We\'ll get back to you within 24 hours with a quote.' },
    { cat: 'Custom Orders', q: 'How far in advance should I order a custom cake?', a: 'We recommend placing custom cake orders at least 5-7 days in advance. For large or complex designs, 2 weeks\'s notice ensures the best result.' },
]

const filteredFaqs = computed(() =>
    activeCategory.value === 'All' ? faqs : faqs.filter(f => f.cat === activeCategory.value)
)
</script>

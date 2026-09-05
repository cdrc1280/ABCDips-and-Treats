<template>
    <div class="max-w-6xl mx-auto px-6 py-16">
        <div class="text-center mb-12">
            <span class="font-['Caveat'] text-brand-caramel dark:text-[#E2C08A] text-2xl block mb-2">get in touch</span>
            <h1 class="text-4xl font-extrabold text-ink dark:text-[#FBF3E7] tracking-tight">Contact Us</h1>
            <p class="text-warm-gray dark:text-[#C5B4A4] mt-3 max-w-lg mx-auto leading-relaxed">
                We'd love to hear from you. Send us a message and our Cavite bakery team will respond within 24 hours.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
            <!-- Contact Info Cards -->
            <div class="lg:col-span-2 space-y-4">
                <component :is="info.link ? 'a' : 'div'" v-for="info in contactInfo" :key="info.label"
                    :href="info.link" :target="info.link ? '_blank' : undefined" :rel="info.link ? 'noopener noreferrer' : undefined"
                    v-tooltip="info.link ? `Open ${info.label}` : undefined"
                    class="bg-white dark:bg-[#1E1510] rounded-2xl p-5 border border-brand-caramel/20 dark:border-[#C08E5D]/20 flex items-start gap-4 shadow-sm hover:shadow-md transition-all"
                    :class="{ 'cursor-pointer hover:border-brand-choco dark:hover:border-[#E2C08A]': info.link }">
                    <div
                        class="w-10 h-10 rounded-xl bg-brand-tan/20 dark:bg-[#2A1C13] border border-brand-caramel/20 dark:border-[#C08E5D]/30 flex items-center justify-center text-brand-choco dark:text-[#E2C08A] shrink-0">
                        <component :is="info.icon" class="w-5 h-5" />
                    </div>
                    <div>
                        <p class="text-xs font-bold text-brand-caramel dark:text-[#E2C08A] uppercase tracking-wider mb-0.5">{{ info.label }}</p>
                        <p class="text-sm font-semibold text-ink dark:text-[#FBF3E7]">{{ info.value }}</p>
                        <p v-if="info.sub" class="text-xs text-warm-gray dark:text-[#C5B4A4] mt-0.5">{{ info.sub }}</p>
                    </div>
                </component>

                <!-- Social Links Card -->
                <div class="bg-brand-choco dark:bg-[#140D09] rounded-2xl p-5 text-surface border border-brand-caramel/20 dark:border-[#C08E5D]/20">
                    <p class="text-xs font-bold text-brand-tan dark:text-[#E2C08A] uppercase tracking-wider mb-3">Follow Us</p>
                    <div class="flex gap-3">
                        <a href="https://www.facebook.com/abcdipsandtreats" target="_blank" rel="noopener noreferrer"
                            v-tooltip="'Visit our official Facebook page'"
                            class="w-9 h-9 bg-surface/10 hover:bg-brand-tan/30 rounded-xl flex items-center justify-center transition-colors text-surface">
                            <Facebook class="w-4 h-4" />
                        </a>
                        <a href="https://www.instagram.com/abcdips_treats" target="_blank" rel="noopener noreferrer"
                            v-tooltip="'Follow @abcdips_treats on Instagram'"
                            class="w-9 h-9 bg-surface/10 hover:bg-brand-tan/30 rounded-xl flex items-center justify-center transition-colors text-surface">
                            <Instagram class="w-4 h-4" />
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="lg:col-span-3">
                <!-- Success State -->
                <div v-if="sent" class="bg-white dark:bg-[#1E1510] rounded-3xl p-12 text-center border border-brand-caramel/20 dark:border-[#C08E5D]/20 shadow-sm">
                    <div class="w-16 h-16 rounded-3xl bg-[#D9A876]/20 mx-auto flex items-center justify-center mb-4 text-brand-caramel dark:text-[#E2C08A] border border-[#C08E5D]/30">
                        <CheckCircle2 class="w-8 h-8 text-emerald-500" />
                    </div>
                    <h2 class="text-2xl font-bold text-ink dark:text-[#FBF3E7] mb-2">Message Sent!</h2>
                    <p class="text-warm-gray dark:text-[#C5B4A4]">Thank you for reaching out. We'll get back to you within 24 hours.</p>
                    <button @click="sent = false; form = { name: '', email: '', phone: '', subject: '', message: '' }"
                        v-tooltip="'Send another message'"
                        class="mt-6 text-sm font-semibold text-brand-choco dark:text-[#E2C08A] hover:underline">
                        Send another message
                    </button>
                </div>

                <!-- Form -->
                <form v-else @submit.prevent="submit"
                    class="bg-white dark:bg-[#1E1510] rounded-3xl p-8 border border-brand-caramel/20 dark:border-[#C08E5D]/20 shadow-sm space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-ink dark:text-[#FBF3E7] mb-1.5">Full Name *</label>
                            <input v-model="form.name" required placeholder="Your name"
                                class="w-full bg-surface dark:bg-[#140D09] border border-brand-caramel/30 dark:border-[#C08E5D]/30 rounded-xl px-4 py-3 text-sm text-ink dark:text-[#FBF3E7] focus:outline-none focus:border-brand-choco focus:ring-1 focus:ring-brand-choco/20 transition-all" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-ink dark:text-[#FBF3E7] mb-1.5">Email Address *</label>
                            <input v-model="form.email" type="email" inputmode="email" required placeholder="your@email.com"
                                @keydown.space.prevent
                                class="w-full bg-surface dark:bg-[#140D09] border border-brand-caramel/30 dark:border-[#C08E5D]/30 rounded-xl px-4 py-3 text-sm text-ink dark:text-[#FBF3E7] focus:outline-none focus:border-brand-choco focus:ring-1 focus:ring-brand-choco/20 transition-all" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-ink dark:text-[#FBF3E7] mb-1.5">Phone Number</label>
                            <input v-model="form.phone" type="tel" inputmode="tel" maxlength="13" placeholder="09171234567"
                                @keydown="onNumericKeydown"
                                @input="form.phone = $event.target.value.replace(/(?!^\+)[^\d]/g, '')"
                                class="w-full bg-surface dark:bg-[#140D09] border border-brand-caramel/30 dark:border-[#C08E5D]/30 rounded-xl px-4 py-3 text-sm text-ink dark:text-[#FBF3E7] focus:outline-none focus:border-brand-choco focus:ring-1 focus:ring-brand-choco/20 transition-all" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-ink dark:text-[#FBF3E7] mb-1.5">Subject *</label>
                            <select v-model="form.subject" required
                                class="w-full bg-surface dark:bg-[#140D09] border border-brand-caramel/30 dark:border-[#C08E5D]/30 rounded-xl px-4 py-3 text-sm text-ink dark:text-[#FBF3E7] focus:outline-none focus:border-brand-choco transition-all">
                                <option value="">Select a subject</option>
                                <option>General Inquiry</option>
                                <option>Order Issue</option>
                                <option>Custom Cake Inquiry</option>
                                <option>Delivery Question</option>
                                <option>Feedback</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-ink dark:text-[#FBF3E7] mb-1.5">Message *</label>
                        <textarea v-model="form.message" required rows="5" placeholder="Tell us how we can help..."
                            class="w-full bg-surface dark:bg-[#140D09] border border-brand-caramel/30 dark:border-[#C08E5D]/30 rounded-xl px-4 py-3 text-sm text-ink dark:text-[#FBF3E7] focus:outline-none focus:border-brand-choco focus:ring-1 focus:ring-brand-choco/20 transition-all resize-none" />
                    </div>
                    <div v-if="errors"
                        class="bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-xl px-4 py-3 text-sm">
                        {{ errors }}
                    </div>
                    <button type="submit" :disabled="submitting"
                        v-tooltip="'Send message to ABCDips team'"
                        class="w-full bg-brand-choco dark:bg-[#C08E5D] text-surface dark:text-[#1C1410] py-3.5 rounded-xl font-bold text-sm hover:bg-choco-600 dark:hover:bg-[#D9A876] disabled:opacity-50 transition-all shadow-sm hover:shadow-md">
                        {{ submitting ? 'Sending...' : 'Send Message →' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, inject } from 'vue'
import { MapPin, Clock, Mail, MessageSquare, Facebook, Instagram, CheckCircle2 } from 'lucide-vue-next'

const axios = inject('axios')
const sent = ref(false)
const submitting = ref(false)
const errors = ref('')
const form = ref({ name: '', email: '', phone: '', subject: '', message: '' })

const contactInfo = [
    { icon: MapPin, label: 'Location', value: 'Cavite, Philippines', sub: 'Delivery within Cavite' },
    { icon: Clock, label: 'Business Hours', value: 'Mon–Sat: 8:00 AM – 6:00 PM', sub: 'Sunday: 9:00 AM – 4:00 PM' },
    { icon: Mail, label: 'Email', value: 'hello@abcdips.com', sub: 'We reply within 24 hours', link: 'mailto:hello@abcdips.com' },
    { icon: MessageSquare, label: 'Facebook', value: 'ABCDips & Treats', sub: 'Message us on Facebook', link: 'https://www.facebook.com/abcdipsandtreats' },
]

async function submit() {
    submitting.value = true
    errors.value = ''
    try {
        await axios.post('/api/contact', form.value)
        sent.value = true
    } catch (err) {
        errors.value = err.response?.data?.message || 'Failed to send. Please try again.'
    } finally {
        submitting.value = false
    }
}

function onNumericKeydown(event) {
    const allowedControlKeys = ['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Home', 'End']
    if (allowedControlKeys.includes(event.key) || event.ctrlKey || event.metaKey) return
    if (!/^\d$/.test(event.key) && event.key !== '+') event.preventDefault()
}
</script>

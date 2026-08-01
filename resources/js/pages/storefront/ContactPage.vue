<template>
    <div class="max-w-6xl mx-auto px-6 py-16">
        <div class="text-center mb-12">
            <span class="font-['Caveat'] text-[#C08E5D] text-2xl block mb-2">get in touch</span>
            <h1 class="text-4xl font-extrabold text-[#1C1410] tracking-tight">Contact Us</h1>
            <p class="text-[#8C7A68] mt-3">We'd love to hear from you. Send us a message and we'll respond within 24
                hours.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
            <!-- Contact Info -->
            <div class="lg:col-span-2 space-y-4">
                <div v-for="info in contactInfo" :key="info.label"
                    class="bg-white rounded-2xl p-5 border border-[#C08E5D]/20 flex items-start gap-4 shadow-sm hover:shadow-md transition-shadow">
                    <div
                        class="w-10 h-10 rounded-xl bg-[#D9A876]/20 flex items-center justify-center text-xl flex-shrink-0">
                        {{ info.emoji }}</div>
                    <div>
                        <p class="text-xs font-bold text-[#C08E5D] uppercase tracking-wider mb-0.5">{{ info.label }}</p>
                        <p class="text-sm font-semibold text-[#1C1410]">{{ info.value }}</p>
                        <p v-if="info.sub" class="text-xs text-[#8C7A68] mt-0.5">{{ info.sub }}</p>
                    </div>
                </div>

                <!-- Social Links -->
                <div class="bg-[#5C3A22] rounded-2xl p-5 text-[#FBF3E7]">
                    <p class="text-xs font-bold text-[#D9A876] uppercase tracking-wider mb-3">Follow Us</p>
                    <div class="flex gap-3">
                        <a href="https://facebook.com" target="_blank"
                            class="w-9 h-9 bg-[#FBF3E7]/10 rounded-xl flex items-center justify-center hover:bg-[#D9A876]/30 transition-colors text-sm">📘</a>
                        <a href="https://instagram.com" target="_blank"
                            class="w-9 h-9 bg-[#FBF3E7]/10 rounded-xl flex items-center justify-center hover:bg-[#D9A876]/30 transition-colors text-sm">📸</a>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="lg:col-span-3">
                <!-- Success State -->
                <div v-if="sent" class="bg-white rounded-3xl p-12 text-center border border-[#C08E5D]/20 shadow-sm">
                    <div class="text-5xl mb-4">🍞</div>
                    <h2 class="text-2xl font-bold text-[#1C1410] mb-2">Message Sent!</h2>
                    <p class="text-[#8C7A68]">Thank you for reaching out. We'll get back to you within 24 hours.</p>
                    <button @click="sent = false; form = { name: '', email: '', phone: '', subject: '', message: '' }"
                        class="mt-6 text-sm font-semibold text-[#5C3A22] hover:underline">Send another message</button>
                </div>

                <!-- Form -->
                <form v-else @submit.prevent="submit"
                    class="bg-white rounded-3xl p-8 border border-[#C08E5D]/20 shadow-sm space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-[#1C1410] mb-1.5">Full Name *</label>
                            <input v-model="form.name" required placeholder="Your name"
                                class="w-full bg-[#FBF3E7] border border-[#C08E5D]/30 rounded-xl px-4 py-3 text-sm text-[#1C1410] focus:outline-none focus:border-[#5C3A22] focus:ring-1 focus:ring-[#5C3A22]/20 transition-all" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#1C1410] mb-1.5">Email Address *</label>
                            <input v-model="form.email" type="email" inputmode="email" required placeholder="your@email.com"
                                @keydown.space.prevent
                                class="w-full bg-[#FBF3E7] border border-[#C08E5D]/30 rounded-xl px-4 py-3 text-sm text-[#1C1410] focus:outline-none focus:border-[#5C3A22] focus:ring-1 focus:ring-[#5C3A22]/20 transition-all" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-[#1C1410] mb-1.5">Phone Number</label>
                            <input v-model="form.phone" type="tel" inputmode="tel" maxlength="13" placeholder="09171234567"
                                @keydown="onNumericKeydown"
                                @input="form.phone = $event.target.value.replace(/(?!^\+)[^\d]/g, '')"
                                class="w-full bg-[#FBF3E7] border border-[#C08E5D]/30 rounded-xl px-4 py-3 text-sm text-[#1C1410] focus:outline-none focus:border-[#5C3A22] focus:ring-1 focus:ring-[#5C3A22]/20 transition-all" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#1C1410] mb-1.5">Subject *</label>
                            <select v-model="form.subject" required
                                class="w-full bg-[#FBF3E7] border border-[#C08E5D]/30 rounded-xl px-4 py-3 text-sm text-[#1C1410] focus:outline-none focus:border-[#5C3A22] transition-all">
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
                        <label class="block text-xs font-bold text-[#1C1410] mb-1.5">Message *</label>
                        <textarea v-model="form.message" required rows="5" placeholder="Tell us how we can help..."
                            class="w-full bg-[#FBF3E7] border border-[#C08E5D]/30 rounded-xl px-4 py-3 text-sm text-[#1C1410] focus:outline-none focus:border-[#5C3A22] focus:ring-1 focus:ring-[#5C3A22]/20 transition-all resize-none" />
                    </div>
                    <div v-if="errors"
                        class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">{{ errors }}
                    </div>
                    <button type="submit" :disabled="submitting"
                        class="w-full bg-[#5C3A22] text-[#FBF3E7] py-3.5 rounded-xl font-bold text-sm hover:bg-[#4A2D1A] disabled:opacity-50 transition-all shadow-sm hover:shadow-md">{{
                            submitting ? 'Sending...' : 'Send Message →' }}</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, inject } from 'vue'
const axios = inject('axios')
const sent = ref(false)
const submitting = ref(false)
const errors = ref('')
const form = ref({ name: '', email: '', phone: '', subject: '', message: '' })
const contactInfo = [
    { emoji: '📍', label: 'Location', value: 'Cavite, Philippines', sub: 'Delivery within Cavite' },
    { emoji: '⏰', label: 'Business Hours', value: 'Mon–Sat: 8:00 AM – 6:00 PM', sub: 'Sunday: 9:00 AM – 4:00 PM' },
    { emoji: '📧', label: 'Email', value: 'hello@abcdips.com', sub: 'We reply within 24 hours' },
    { emoji: '📱', label: 'Facebook', value: 'ABCDips & Treats', sub: 'Message us on Facebook' },
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

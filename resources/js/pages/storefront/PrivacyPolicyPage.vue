<template>
  <div class="max-w-3xl mx-auto px-6 py-16">
    <div class="mb-10">
      <h1 class="text-4xl font-extrabold text-ink tracking-tight">Privacy Policy</h1>
      <p class="text-warm-gray mt-2 text-sm">Last updated: July 2026</p>
    </div>
    <div class="prose-custom space-y-8">
      <section v-for="section in sections" :key="section.title" class="bg-white rounded-2xl p-6 border border-brand-caramel/20">
        <h2 class="text-lg font-bold text-ink mb-3">{{ section.title }}</h2>
        <p class="text-sm text-warm-gray leading-relaxed">{{ section.content }}</p>
      </section>
    </div>
    <div class="mt-10 bg-surface rounded-2xl p-6 border border-brand-caramel/20 text-sm text-warm-gray">
      <strong class="text-ink">Questions about this policy?</strong> Contact us at <a href="mailto:hello@abcdips.com" class="text-brand-choco hover:underline">hello@abcdips.com</a>
    </div>
  </div>
</template>
<script setup>
const sections = [
  { title: '1. Information We Collect', content: 'We collect information you provide directly, such as your name, email address, phone number, and delivery address when you create an account or place an order. We also collect data automatically, including IP address, browser type, and pages visited.' },
  { title: '2. How We Use Your Information', content: 'We use your information to process orders, send order confirmations and updates, provide customer support, improve our services, and send promotional communications (with your consent).' },
  { title: '3. Information Sharing', content: 'We do not sell, trade, or rent your personal information to third parties. We may share data with trusted service providers who assist in operating our website, subject to strict confidentiality agreements.' },
  { title: '4. Cookies', content: 'We use cookies to enhance your browsing experience, remember your cart items, and analyze site traffic. You can control cookie settings through your browser preferences.' },
  { title: '5. Data Security', content: 'We implement industry-standard security measures including SSL encryption, secure servers, and regular security audits to protect your personal information.' },
  { title: '6. Your Rights', content: 'You have the right to access, correct, or delete your personal information at any time. You may also opt out of marketing communications by clicking "unsubscribe" in any email or contacting us directly.' },
]
</script>

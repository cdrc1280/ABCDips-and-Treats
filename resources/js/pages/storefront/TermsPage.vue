<template>
  <div class="max-w-3xl mx-auto px-6 py-16">
    <div class="mb-10">
      <h1 class="text-4xl font-extrabold text-ink tracking-tight">Terms of Service</h1>
      <p class="text-warm-gray mt-2 text-sm">Last updated: July 2026</p>
    </div>
    <div class="space-y-8">
      <section v-for="section in sections" :key="section.title" class="bg-white rounded-2xl p-6 border border-brand-caramel/20">
        <h2 class="text-lg font-bold text-ink mb-3">{{ section.title }}</h2>
        <p class="text-sm text-warm-gray leading-relaxed">{{ section.content }}</p>
      </section>
    </div>
  </div>
</template>
<script setup>
const sections = [
  { title: '1. Acceptance of Terms', content: 'By accessing and using ABCDips & Treats website and ordering services, you accept and agree to be bound by these Terms of Service. If you do not agree, please do not use our services.' },
  { title: '2. Orders and Payment', content: 'All orders are subject to product availability. We reserve the right to refuse or cancel any order. Payment must be received before orders are processed. Prices are in Philippine Peso (PHP) and are subject to change.' },
  { title: '3. Delivery', content: 'Delivery times are estimates and may vary based on location and demand. We are not liable for delays caused by external factors beyond our control. Delivery addresses must be within our service area.' },
  { title: '4. Cancellations and Refunds', content: 'Orders may be cancelled within 1 hour of placement. Refunds for qualifying cancellations will be processed within 3-5 business days. Custom orders are non-refundable once production has begun.' },
  { title: '5. Product Information', content: 'We strive to ensure all product descriptions, prices, and allergen information are accurate. We are not responsible for typographical errors. Please contact us if you have specific dietary concerns before ordering.' },
  { title: '6. Intellectual Property', content: 'All content on this website, including images, text, logos, and recipes, is the intellectual property of ABCDips & Treats. Unauthorized use is strictly prohibited.' },
  { title: '7. Limitation of Liability', content: 'ABCDips & Treats shall not be liable for any indirect, incidental, or consequential damages arising from use of our services or products beyond the value of the transaction in question.' },
]
</script>

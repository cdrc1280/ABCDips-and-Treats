<template>
    <Teleport to="body">
        <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0"
            enter-to-class="opacity-100" leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="modelValue && order"
                class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-3 sm:p-6"
                @click.self="close">
                <Transition enter-active-class="transition-all duration-300 ease-out"
                    enter-from-class="opacity-0 scale-95 translate-y-4"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                    leave-active-class="transition-all duration-200 ease-in"
                    leave-from-class="opacity-100 scale-100 translate-y-0"
                    leave-to-class="opacity-0 scale-95 translate-y-4">
                    <div v-if="modelValue && order"
                        class="bg-white rounded-3xl shadow-2xl border border-brand-caramel/30 overflow-hidden w-full max-w-3xl relative max-h-[90vh] flex flex-col text-ink">
                        <!-- Header Bar -->
                        <div
                            class="px-6 py-4 bg-brand-choco text-surface flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center gap-2">
                                <span class="text-xl">📄</span>
                                <div>
                                    <h3 class="font-extrabold text-lg leading-tight uppercase tracking-wider">INVOICE
                                    </h3>
                                    <p class="text-xs text-brand-tan">ABCDips &amp; Treats Bakery</p>
                                </div>
                            </div>

                            <div v-if="order?.delivery_mode !== 'pooling' || order?.pooling_status === 'settled'" class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                                <button type="button"
                                    class="px-3 py-1.5 rounded-xl bg-brand-tan text-ink font-bold text-xs hover:bg-brand-caramel transition-all flex items-center gap-1 cursor-pointer"
                                    @click="downloadInvoice('a4')">
                                    <span>📄 A4 PDF</span>
                                </button>
                                <button type="button"
                                    class="px-3 py-1.5 rounded-xl bg-amber-200 text-amber-950 font-bold text-xs hover:bg-amber-300 transition-all flex items-center gap-1 cursor-pointer"
                                    @click="downloadInvoice('pos')">
                                    <span>🧾 80mm POS</span>
                                </button>
                                <button type="button"
                                    class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/40 text-white flex items-center justify-center text-sm font-bold transition-all cursor-pointer"
                                    @click="close">
                                    ✕
                                </button>
                            </div>
                            <div v-else>
                                <button type="button"
                                    class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/40 text-white flex items-center justify-center text-sm font-bold transition-all cursor-pointer"
                                    @click="close">
                                    ✕
                                </button>
                            </div>
                        </div>

                        <!-- Invoice Pending Pool Settlement Alert -->
                        <div v-if="order?.delivery_mode === 'pooling' && order?.pooling_status !== 'settled'" class="p-8 text-center space-y-4 bg-amber-50/90 dark:bg-[#2A1C13] rounded-b-3xl">
                            <span class="text-5xl block">⏳</span>
                            <div class="space-y-1">
                                <h3 class="font-extrabold text-lg text-amber-950 dark:text-amber-200">Invoice Pending Admin Settlement</h3>
                                <p class="text-xs text-amber-900/90 dark:text-amber-300/90 max-w-md mx-auto leading-relaxed">
                                    Your order is currently queued for Group Delivery Pooling. Official invoices and receipts are only generated after our admin accepts your delivery batch and assigns your shared delivery rate.
                                </p>
                            </div>
                            <button type="button" @click="close" class="px-5 py-2.5 rounded-xl bg-amber-800 text-white font-bold text-xs hover:bg-amber-900 transition-all cursor-pointer">
                                Got It
                            </button>
                        </div>

                        <!-- Invoice Content Body -->
                        <div v-else class="p-6 overflow-y-auto space-y-6 text-xs sm:text-sm bg-[#FFFFFF]">
                            <!-- Title & Brand Header -->
                            <div class="border-l-4 border-brand-choco pl-3">
                                <h1 class="text-3xl font-black text-ink uppercase tracking-wider">INVOICE</h1>
                                <p class="text-xs text-brand-choco font-bold">ABCDips &amp; Treats &bull; Bake with Love
                                    &amp; Specialty Pastries</p>
                            </div>

                            <!-- Metadata Grid (Reference Layout: Left: Invoice No & Date, Right: Issued to) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-[#E2D2C4] pt-4">
                                <div class="space-y-3">
                                    <div>
                                        <span class="text-xs text-warm-gray block">Invoice No:</span>
                                        <span class="font-mono font-bold text-sm text-ink">#{{ order.order_number
                                            }}</span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-warm-gray block">Date Issued:</span>
                                        <span class="font-mono font-bold text-sm text-ink">{{
                                            formatDate(order.created_at) }}</span>
                                    </div>
                                </div>

                                <div class="space-y-1">
                                    <span class="text-xs text-warm-gray block">Issued to:</span>
                                    <span class="font-extrabold text-base text-ink block">{{ order.customer_name
                                        }}</span>
                                    <span class="text-xs text-warm-gray block">{{ order.customer_email }} &bull; {{
                                        order.customer_phone }}</span>
                                    <span class="text-xs text-brand-choco font-semibold block capitalize">
                                        {{ order.fulfillment_type }}
                                        <template v-if="order.delivery_mode === 'pooling'">
                                            &bull; 🤝 Group Delivery Pooling (Batch #{{ order.delivery_pool?.pool_code || 'POOL' }})
                                        </template>
                                        &bull; {{ order.delivery_address || 'Store Pickup' }} {{ order.city }}
                                    </span>
                                </div>
                            </div>

                            <!-- Itemized Table (Matching reference layout) -->
                            <div class="border border-[#D9C5B5] overflow-hidden rounded-t-sm shadow-xs">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr
                                            class="bg-[#EADCD3] text-brand-choco text-xs font-extrabold uppercase tracking-wider border-b border-[#D9C5B5]">
                                            <th class="p-2.5 text-center w-12 border-r border-[#D9C5B5]">NO</th>
                                            <th class="p-2.5 border-r border-[#D9C5B5]">DESCRIPTION</th>
                                            <th class="p-2.5 text-center w-16 border-r border-[#D9C5B5]">QTY</th>
                                            <th class="p-2.5 text-right w-24 border-r border-[#D9C5B5]">PRICE</th>
                                            <th class="p-2.5 text-right w-28">SUBTOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#E5D5C5]">
                                        <tr v-for="(item, idx) in order.items" :key="item.id"
                                            class="hover:bg-surface/40 text-xs">
                                            <td class="p-2.5 text-center font-mono border-r border-[#E5D5C5]">{{ idx + 1
                                                }}</td>
                                            <td class="p-2.5 font-semibold text-ink border-r border-[#E5D5C5]">
                                                {{ item.product_name }}
                                                <span v-if="item.options?.flavor" class="block text-[10px] font-semibold text-amber-700">Flavor: {{ item.options.flavor }}</span>
                                                <span v-if="item.options?.variation" class="block text-[10px] font-semibold text-brand-choco">Option: {{ item.options.variation }}</span>
                                            </td>
                                            <td class="p-2.5 text-center font-mono border-r border-[#E5D5C5]">{{
                                                item.qty }}</td>
                                            <td class="p-2.5 text-right font-mono border-r border-[#E5D5C5]">&#8369;{{
                                                Number(item.unit_price).toFixed(2) }}</td>
                                            <td class="p-2.5 text-right font-mono font-bold text-ink">&#8369;{{
                                                Number(item.subtotal).toFixed(2) }}</td>
                                        </tr>
                                        <tr v-if="order.delivery_fee > 0" class="hover:bg-surface/40 text-xs">
                                            <td class="p-2.5 text-center font-mono border-r border-[#E5D5C5]">{{
                                                (order.items?.length || 0) + 1 }}</td>
                                            <td class="p-2.5 border-r border-[#E5D5C5]">
                                                <template v-if="order.delivery_mode === 'pooling'">
                                                    <span class="font-bold text-emerald-800 dark:text-emerald-400">🤝 Group Delivery Pooling Shared Shipping Rate</span>
                                                    <span v-if="order.delivery_pool?.pool_code" class="block text-[10px] font-mono text-emerald-700 font-bold">Batch: #{{ order.delivery_pool.pool_code }}</span>
                                                </template>
                                                <template v-else>
                                                    <span class="text-warm-gray italic">Delivery &amp; Shipping Fee</span>
                                                </template>
                                            </td>
                                            <td class="p-2.5 text-center font-mono border-r border-[#E5D5C5]">1</td>
                                            <td class="p-2.5 text-right font-mono border-r border-[#E5D5C5]">&#8369;{{
                                                Number(order.delivery_fee).toFixed(2) }}</td>
                                            <td class="p-2.5 text-right font-mono font-bold text-ink">&#8369;{{
                                                Number(order.delivery_fee).toFixed(2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Grand Total Bar (Reference layout: Full width primary bar) -->
                            <div
                                class="bg-brand-choco text-white px-4 py-3 font-extrabold flex justify-between items-center text-sm rounded-b-md shadow-xs">
                                <span class="tracking-widest">GRAND TOTAL</span>
                                <span class="font-mono text-lg">&#8369;{{ Number(order.total || 0).toFixed(2) }}</span>
                            </div>

                            <!-- Payment Information Box -->
                            <div class="pt-2 text-xs space-y-1">
                                <span class="font-bold text-warm-gray uppercase tracking-wider block">Payment
                                    Information</span>
                                <p><strong>Method:</strong> {{ (order.payment_method || 'COD').toUpperCase() }} &bull;
                                    <strong>Status:</strong> <span class="text-success font-bold">{{
                                        (order.payment_status || 'PAID').toUpperCase() }}</span></p>
                                <p class="text-warm-gray">Reference Token: {{ order.payment_reference || 'N/A' }}</p>
                                <p class="text-[11px] text-warm-gray border-t border-dashed border-[#E2D2C4] pt-2 mt-2">
                                    Store Payment Contacts &bull; GCash: 09064177614 &bull; Unionbank: 109430339968
                                </p>
                            </div>
                        </div>

                        <!-- Footer Actions Bar -->
                        <div
                            class="px-6 py-4 bg-surface/80 border-t border-brand-caramel/20 flex items-center justify-between">
                            <span class="text-xs text-warm-gray">Stored transaction receipt &bull; ABCDips Bakery
                                System</span>
                            <div class="flex gap-2 flex-wrap sm:flex-nowrap">
                                <button type="button"
                                    class="px-3.5 py-2 rounded-xl border border-brand-choco text-brand-choco hover:bg-brand-tan/20 font-bold text-xs transition-all cursor-pointer"
                                    @click="close">
                                    Close
                                </button>
                                <template v-if="order?.delivery_mode !== 'pooling' || order?.pooling_status === 'settled'">
                                    <button type="button"
                                        class="px-4 py-2 rounded-xl bg-brand-choco text-surface hover:bg-[#442917] font-bold text-xs transition-all cursor-pointer shadow-xs"
                                        @click="downloadInvoice('a4')">
                                        📄 Download A4 Invoice
                                    </button>
                                    <button type="button"
                                        class="px-4 py-2 rounded-xl bg-amber-700 text-white hover:bg-amber-800 font-bold text-xs transition-all cursor-pointer shadow-xs"
                                        @click="downloadInvoice('pos')">
                                        🧾 Download POS 80mm
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
const props = defineProps({
    modelValue: { type: Boolean, required: true },
    order: { type: Object, default: null }
})

const emit = defineEmits(['update:modelValue', 'close'])

function close() {
    emit('update:modelValue', false)
    emit('close')
}

function formatDate(val) {
    if (!val) return ''
    return new Date(val).toLocaleString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric',
        hour: 'numeric', minute: '2-digit', hour12: true
    })
}

function capitalize(val) {
    if (!val) return ''
    return val.charAt(0).toUpperCase() + val.slice(1)
}

function downloadInvoice(paper = 'a4') {
    if (!props.order?.id) return
    const token = props.order.tracking_token ? `&token=${props.order.tracking_token}` : ''
    const downloadUrl = `/order-invoice/${props.order.id}/download?paper=${paper}${token}`

    const paperLabel = paper === 'pos' ? 'POS_Receipt' : 'Invoice'
    const link = document.createElement('a')
    link.href = downloadUrl
    link.setAttribute('download', `ABCDips_${paperLabel}_${props.order.order_number || props.order.id}.pdf`)
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
}
</script>

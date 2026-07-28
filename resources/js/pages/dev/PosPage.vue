<template>
  <div class="min-h-screen bg-[#1C1410] text-[#FBF3E7] p-4 md:p-6 flex flex-col">
    <!-- Top POS Header -->
    <header class="bg-[#2D211A] rounded-2xl p-4 mb-4 border border-[#C08E5D]/30 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-[#5C3A22] flex items-center justify-center font-bold text-lg text-[#D9A876]">
          POS
        </div>
        <div>
          <h1 class="font-extrabold text-lg text-[#FBF3E7]">ABCDips &amp; Treats Walk-in Register</h1>
          <p class="text-xs text-[#D9A876]">Counter Terminal #1 • {{ currentUser }}</p>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <RouterLink to="/">
          <BaseButton variant="outline" size="sm">Exit to Storefront</BaseButton>
        </RouterLink>
        <RouterLink to="/admin">
          <BaseButton variant="secondary" size="sm">Back-Office Admin</BaseButton>
        </RouterLink>
      </div>
    </header>

    <!-- Main POS Workspace (Split Screen) -->
    <div class="flex-1 grid grid-cols-1 lg:grid-cols-12 gap-4">

      <!-- Left Column: Product Selection Grid -->
      <div class="lg:col-span-7 flex flex-col space-y-4">
        <!-- Search & Filter Bar -->
        <div class="bg-[#2D211A] p-4 rounded-2xl border border-[#C08E5D]/20 flex flex-col sm:flex-row gap-3 items-center justify-between">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search pastry by name or SKU..."
            class="w-full sm:w-72 bg-[#1C1410] border border-[#C08E5D]/30 rounded-xl px-4 py-2 text-sm text-[#FBF3E7] placeholder-[#8C7A68] focus:outline-none focus:border-[#D9A876]"
          />

          <div class="flex gap-2 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0">
            <button
              class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all whitespace-nowrap"
              :class="!selectedCat ? 'bg-[#D9A876] text-[#1C1410]' : 'bg-[#1C1410] text-[#D9A876] hover:bg-[#5C3A22]'"
              @click="selectedCat = ''"
            >
              All
            </button>
            <button
              v-for="cat in categories"
              :key="cat"
              class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all whitespace-nowrap"
              :class="selectedCat === cat ? 'bg-[#D9A876] text-[#1C1410]' : 'bg-[#1C1410] text-[#D9A876] hover:bg-[#5C3A22]'"
              @click="selectedCat = cat"
            >
              {{ cat }}
            </button>
          </div>
        </div>

        <!-- Product Grid -->
        <div class="flex-1 bg-[#2D211A] p-4 rounded-2xl border border-[#C08E5D]/20 overflow-y-auto max-h-[600px]">
          <div v-if="loading" class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <SkeletonCard v-for="n in 6" :key="n" />
          </div>

          <div v-else class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div
              v-for="p in filteredProducts"
              :key="p.id"
              class="bg-[#1C1410] rounded-xl p-3 border border-[#C08E5D]/20 hover:border-[#D9A876] cursor-pointer transition-all flex flex-col justify-between group"
              @click="addToRegister(p)"
            >
              <div>
                <img :src="p.primary_image_url" class="w-full h-24 object-cover rounded-lg mb-2 group-hover:scale-105 transition-transform" />
                <h4 class="font-bold text-xs text-[#FBF3E7] line-clamp-1">{{ p.name }}</h4>
                <div class="text-[10px] text-[#8C7A68]">SKU: {{ p.sku }}</div>
              </div>

              <div class="flex items-center justify-between mt-2 pt-2 border-t border-[#C08E5D]/15">
                <span class="font-extrabold text-xs text-[#D9A876]">₱{{ p.effective_price.toFixed(2) }}</span>
                <span class="text-[10px] px-1.5 py-0.5 rounded bg-[#5C3A22] text-white font-semibold">
                  {{ p.stock_qty }} left
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: POS Register Ticket & Payment Calculator -->
      <div class="lg:col-span-5 bg-[#2D211A] rounded-2xl p-5 border border-[#C08E5D]/30 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between border-b border-[#C08E5D]/20 pb-3 mb-4">
            <h3 class="font-extrabold text-base text-[#FBF3E7]">Current Order Ticket</h3>
            <button class="text-xs text-[#B84C3C] hover:underline" @click="registerItems = []">Clear Ticket</button>
          </div>

          <!-- Walk-in Customer Field -->
          <div class="mb-4">
            <input
              v-model="customerName"
              type="text"
              placeholder="Walk-in Guest Name (Optional)"
              class="w-full bg-[#1C1410] border border-[#C08E5D]/30 rounded-xl px-3 py-1.5 text-xs text-[#FBF3E7] placeholder-[#8C7A68]"
            />
          </div>

          <!-- Items Ticket List -->
          <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
            <div v-if="registerItems.length === 0" class="py-8 text-center text-xs text-[#8C7A68]">
              Tap products on the left to add to order.
            </div>

            <div
              v-for="item in registerItems"
              :key="item.id"
              class="bg-[#1C1410] p-2.5 rounded-xl flex items-center justify-between text-xs"
            >
              <div class="min-w-0 pr-2">
                <div class="font-bold text-[#FBF3E7] truncate">{{ item.name }}</div>
                <div class="text-[10px] text-[#8C7A68]">₱{{ item.unit_price.toFixed(2) }} each</div>
              </div>

              <div class="flex items-center gap-2 flex-shrink-0">
                <button class="w-5 h-5 rounded bg-[#5C3A22] text-white font-bold" @click="updateQty(item.id, -1)">-</button>
                <span class="font-bold text-xs w-4 text-center">{{ item.qty }}</span>
                <button class="w-5 h-5 rounded bg-[#5C3A22] text-white font-bold" @click="updateQty(item.id, 1)">+</button>
                <span class="font-extrabold text-xs text-[#D9A876] ml-2 w-14 text-right">₱{{ item.subtotal.toFixed(2) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment & Change Calculation -->
        <div class="border-t border-[#C08E5D]/20 pt-4 space-y-4">
          <!-- Totals Breakdown -->
          <div class="space-y-1 text-xs">
            <div class="flex justify-between text-[#8C7A68]"><span>Subtotal</span><span>₱{{ subtotal.toFixed(2) }}</span></div>
            <div class="flex justify-between text-lg font-extrabold text-[#D9A876] pt-1"><span>Total Due</span><span>₱{{ subtotal.toFixed(2) }}</span></div>
          </div>

          <!-- Payment Method Tabs -->
          <div class="grid grid-cols-4 gap-2">
            <button
              v-for="m in ['cash', 'gcash', 'maya', 'card']"
              :key="m"
              class="py-2 rounded-xl text-xs font-bold uppercase transition-all"
              :class="paymentMethod === m ? 'bg-[#D9A876] text-[#1C1410]' : 'bg-[#1C1410] text-[#D9A876] hover:bg-[#5C3A22]'"
              @click="paymentMethod = m"
            >
              {{ m }}
            </button>
          </div>

          <!-- Cash Tendered & Change Calculator -->
          <div v-if="paymentMethod === 'cash'" class="bg-[#1C1410] p-3 rounded-xl space-y-2">
            <div class="flex items-center justify-between text-xs">
              <span class="text-[#D9A876] font-bold">Cash Tendered:</span>
              <input
                v-model.number="cashTendered"
                type="number"
                step="50"
                class="w-28 bg-[#2D211A] border border-[#D9A876]/40 rounded-lg px-2 py-1 text-right font-extrabold text-sm text-[#FBF3E7]"
              />
            </div>
            <div class="flex justify-between items-center text-xs font-bold pt-1 border-t border-[#C08E5D]/20">
              <span class="text-[#8C7A68]">Change Due:</span>
              <span class="text-sm font-extrabold text-[#6B8F5E]">₱{{ changeDue.toFixed(2) }}</span>
            </div>
          </div>

          <BaseButton
            variant="primary"
            full-width
            size="lg"
            :disabled="registerItems.length === 0"
            :loading="processing"
            @click="completeCheckout"
          >
            Complete Sale &amp; Print Receipt →
          </BaseButton>
        </div>
      </div>

    </div>

    <!-- Receipt Print Modal -->
    <Teleport to="body">
      <div v-if="receiptOrder" class="fixed inset-0 z-[100] bg-black/80 flex items-center justify-center p-4">
        <div class="bg-white text-black p-6 rounded-2xl max-w-sm w-full font-mono text-xs space-y-4 shadow-2xl">
          <div class="text-center space-y-1">
            <h2 class="font-extrabold text-base">ABCDips &amp; Treats</h2>
            <p>Artisan Pastry Counter</p>
            <p class="text-[10px] text-gray-500">Order: {{ receiptOrder.order_number }}</p>
            <p class="text-[10px] text-gray-500">{{ new Date(receiptOrder.created_at).toLocaleString() }}</p>
          </div>

          <div class="border-t border-b border-dashed border-gray-400 py-2 space-y-1">
            <div v-for="item in receiptOrder.items" :key="item.id" class="flex justify-between">
              <span>{{ item.qty }}x {{ item.product_name }}</span>
              <span>₱{{ item.subtotal.toFixed(2) }}</span>
            </div>
          </div>

          <div class="space-y-1 text-right font-bold">
            <div class="flex justify-between"><span>TOTAL:</span><span>₱{{ receiptOrder.total.toFixed(2) }}</span></div>
            <div class="flex justify-between text-gray-600"><span>METHOD:</span><span>{{ receiptOrder.payment_method.toUpperCase() }}</span></div>
            <div v-if="receiptOrder.payment_method === 'cash'" class="flex justify-between text-gray-600"><span>CASH:</span><span>₱{{ lastCashTendered.toFixed(2) }}</span></div>
            <div v-if="receiptOrder.payment_method === 'cash'" class="flex justify-between text-emerald-700"><span>CHANGE:</span><span>₱{{ lastChangeDue.toFixed(2) }}</span></div>
          </div>

          <div class="text-center pt-2 text-[10px] text-gray-500">
            Thank you for buying fresh baked treats!
          </div>

          <div class="flex gap-2 pt-2">
            <BaseButton variant="primary" full-width size="sm" @click="window.print()">Print</BaseButton>
            <BaseButton variant="ghost" full-width size="sm" @click="receiptOrder = null">Close</BaseButton>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import SkeletonCard from '@/components/ui/SkeletonCard.vue'

const axios = inject('axios')
const products = ref([])
const loading = ref(true)
const searchQuery = ref('')
const selectedCat = ref('')
const currentUser = 'Head Baker'

const registerItems = ref([])
const customerName = ref('')
const paymentMethod = ref('cash')
const cashTendered = ref(500)
const processing = ref(false)

const receiptOrder = ref(null)
const lastCashTendered = ref(0)
const lastChangeDue = ref(0)

const categories = computed(() => {
  const set = new Set(products.value.map(p => p.category?.name).filter(Boolean))
  return Array.from(set)
})

const filteredProducts = computed(() => {
  return products.value.filter(p => {
    const matchCat = !selectedCat.value || p.category?.name === selectedCat.value
    const matchQuery = !searchQuery.value || p.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || p.sku.toLowerCase().includes(searchQuery.value.toLowerCase())
    return matchCat && matchQuery
  })
})

const subtotal = computed(() => registerItems.value.reduce((acc, i) => acc + i.subtotal, 0))
const changeDue = computed(() => Math.max(0, (cashTendered.value || subtotal.value) - subtotal.value))

function addToRegister(product) {
  const existing = registerItems.value.find(i => i.id === product.id)
  if (existing) {
    existing.qty++
    existing.subtotal = existing.qty * existing.unit_price
  } else {
    registerItems.value.push({
      id: product.id,
      name: product.name,
      unit_price: product.effective_price,
      qty: 1,
      subtotal: product.effective_price
    })
  }
}

function updateQty(id, delta) {
  const item = registerItems.value.find(i => i.id === id)
  if (!item) return
  item.qty += delta
  if (item.qty <= 0) {
    registerItems.value = registerItems.value.filter(i => i.id !== id)
  } else {
    item.subtotal = item.qty * item.unit_price
  }
}

async function fetchProducts() {
  loading.value = true
  try {
    const { data } = await axios.get('/api/pos/products')
    products.value = data.data
  } catch (err) {
    console.error('Failed to load POS products', err)
  } finally {
    loading.value = false
  }
}

async function completeCheckout() {
  if (registerItems.value.length === 0) return
  processing.value = true
  try {
    const payload = {
      customer_name: customerName.value,
      payment_method: paymentMethod.value,
      cash_tendered: cashTendered.value,
      items: registerItems.value.map(i => ({ id: i.id, qty: i.qty }))
    }

    const { data } = await axios.post('/api/pos/checkout', payload)

    lastCashTendered.value = cashTendered.value
    lastChangeDue.value = changeDue.value
    receiptOrder.value = data.data

    // Clear register & reload stock
    registerItems.value = []
    customerName.value = ''
    fetchProducts()
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to complete POS sale.')
  } finally {
    processing.value = false
  }
}

onMounted(() => fetchProducts())
</script>

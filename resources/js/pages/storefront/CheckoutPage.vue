<template>
  <div class="page-container py-10 md:py-16">
    <PageHeader tagline="almost there" title="Secure Checkout"
      subtitle="Complete your contact details, choose delivery or store pickup, and select your payment method." />

    <!-- Empty Basket State -->
    <div v-if="cartStore.items.length === 0"
      class="py-12 text-center bg-white rounded-3xl border border-brand-caramel/20 shadow-sm">
      <EmptyState title="Your Basket is Empty" description="Add items to your basket before checking out.">
        <template #action>
          <RouterLink to="/shop">
            <BaseButton variant="primary">Return to Shop</BaseButton>
          </RouterLink>
        </template>
      </EmptyState>
    </div>

    <!-- Active Checkout Form -->
    <!-- Account Verification Required Box -->
    <div v-if="authStore.user && !authStore.user.email_verified_at" class="max-w-7xl mx-auto mb-8">
      <div class="bg-amber-50/90 border-2 border-amber-300 rounded-3xl p-6 md:p-8 shadow-md space-y-6">
        <div class="flex items-start gap-4">
          <div
            class="w-12 h-12 rounded-2xl bg-amber-100 border border-amber-300 flex items-center justify-center text-2xl shrink-0">
            🔐
          </div>
          <div>
            <h3 class="text-xl font-extrabold text-amber-950">Account Verification Required Before Checkout</h3>
            <p class="text-sm text-amber-900 mt-1">
              To ensure order security, please verify your account using either your <strong>Email Address</strong> or
              <strong>Mobile Phone Number</strong>.
            </p>
          </div>
        </div>

        <!-- Verification Method Tabs -->
        <div class="flex items-center gap-2 border-b border-amber-200 pb-3">
          <button type="button" @click="verificationTab = 'email'" :class="[
            'px-4 py-2 rounded-xl text-xs font-bold transition-all',
            verificationTab === 'email'
              ? 'bg-brand-choco text-surface shadow-xs'
              : 'bg-white/80 text-amber-900 hover:bg-white border border-amber-200'
          ]">✉️ Verify via Email</button>

          <button type="button" @click="verificationTab = 'phone'" :class="[
            'px-4 py-2 rounded-xl text-xs font-bold transition-all',
            verificationTab === 'phone'
              ? 'bg-brand-choco text-surface shadow-xs'
              : 'bg-white/80 text-amber-900 hover:bg-white border border-amber-200'
          ]">📱 Verify via Mobile Number (SMS Code)</button>
        </div>

        <!-- Tab 1: Email Verification -->
        <div v-if="verificationTab === 'email'"
          class="space-y-4 bg-white/80 rounded-2xl p-5 border border-amber-200/80">
          <p class="text-sm text-ink">
            We will send a secure verification link to <strong>{{ authStore.user.email }}</strong>.
          </p>
          <div class="flex items-center gap-3 flex-wrap">
            <BaseButton type="button" variant="primary" :loading="sendingEmail" @click="sendVerificationEmail">
              ✉️ Send Verification Link to Email
            </BaseButton>
            <span v-if="emailSent" class="text-xs font-bold text-success">✓ Link Sent! Please check your email
              inbox.</span>
          </div>
        </div>

        <!-- Tab 2: Mobile Phone Verification -->
        <div v-else-if="verificationTab === 'phone'"
          class="space-y-4 bg-white/80 rounded-2xl p-5 border border-amber-200/80">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-ink mb-1">Mobile Phone Number</label>
              <div class="flex gap-2">
                <input v-model="otpPhone" type="tel" inputmode="tel" placeholder="09171234567"
                  @keydown="onNumericKeydown" @input="otpPhone = $event.target.value.replace(/(?!^\+)[^\d]/g, '')"
                  class="flex-1 bg-white border border-brand-caramel/30 rounded-xl px-3.5 py-2 text-sm text-ink focus:ring-2 focus:ring-brand-choco" />
                <button type="button" @click="sendPhoneOtp" :disabled="sendingOtp || !otpPhone"
                  class="px-3.5 py-2 bg-brand-choco text-white text-xs font-bold rounded-xl hover:bg-choco-600 disabled:opacity-50 transition-all shrink-0">
                  {{ sendingOtp ? 'Sending...' : 'Send SMS Code' }}
                </button>
              </div>
              <p v-if="otpSent" class="text-[11px] text-success font-semibold mt-1">✓ SMS Code sent! Please check your
                mobile device for the verification code.</p>
            </div>

            <div v-if="otpSent">
              <label class="block text-xs font-bold text-ink mb-1">Enter 6-Digit SMS Code</label>
              <div class="flex gap-2">
                <input v-model="otpCode" type="text" inputmode="numeric" maxlength="6" placeholder="Enter 6-digit code"
                  @keydown="onNumericKeydown" @input="otpCode = $event.target.value.replace(/\D/g, '')"
                  class="w-36 bg-white border border-brand-caramel/30 rounded-xl px-3.5 py-2 text-sm font-extrabold text-center tracking-widest text-ink focus:ring-2 focus:ring-brand-choco" />
                <button type="button" @click="verifyPhoneOtp" :disabled="verifyingOtp || otpCode.length !== 6"
                  class="px-4 py-2 bg-success text-white text-xs font-bold rounded-xl hover:bg-green-700 disabled:opacity-50 transition-all shrink-0">
                  {{ verifyingOtp ? 'Verifying...' : 'Verify & Unlock Checkout' }}
                </button>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <form v-else-if="cartStore.items.length > 0" @submit.prevent="handleCheckout"
      class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

      <!-- Form Column Left -->
      <div class="lg:col-span-7 space-y-6">

        <!-- 1. Fulfillment Method -->
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-brand-caramel/20 shadow-sm space-y-4">
          <h3
            class="font-extrabold text-xl text-ink border-b border-brand-caramel/20 pb-3 flex items-center justify-between">
            <span>1. Fulfillment Method</span>
            <span v-if="storeInfo.store_address" class="text-xs font-normal text-warm-gray">
              📍 Store: {{ storeInfo.store_address }}
            </span>
          </h3>

          <div class="grid grid-cols-2 gap-4">
            <!-- Doorstep Delivery -->
            <label v-tooltip="'Real-time delivery quote calculated via Lalamove based on your location'"
              class="border-2 rounded-2xl p-4 cursor-pointer flex flex-col items-center justify-center text-center transition-all"
              :class="form.fulfillment_type === 'delivery' ? 'border-brand-choco bg-surface' : 'border-brand-caramel/20 bg-white opacity-70 hover:opacity-100'">
              <input type="radio" v-model="form.fulfillment_type" value="delivery" class="sr-only" />
              <div class="w-10 h-10 rounded-full bg-brand-tan/30 flex items-center justify-center text-brand-choco mb-2">
                🛵
              </div>
              <div class="font-bold text-sm text-ink">Doorstep Delivery</div>
              <div class="text-xs text-warm-gray mt-0.5">
                <span v-if="quotingDelivery" class="animate-pulse text-brand-caramel">Quoting...</span>
                <span v-else-if="quotedFee !== null">₱{{ quotedFee.toFixed(2) }} ({{ quoteProvider }})</span>
                <span v-else>Lalamove Real-Time Quote</span>
              </div>
            </label>

            <!-- Store Pickup -->
            <label v-tooltip="'Pick up your fresh pastries directly at our store — 100% FREE!'"
              class="border-2 rounded-2xl p-4 cursor-pointer flex flex-col items-center justify-center text-center transition-all"
              :class="form.fulfillment_type === 'pickup' ? 'border-brand-choco bg-surface' : 'border-brand-caramel/20 bg-white opacity-70 hover:opacity-100'">
              <input type="radio" v-model="form.fulfillment_type" value="pickup" class="sr-only" />
              <div class="w-10 h-10 rounded-full bg-brand-tan/30 flex items-center justify-center text-brand-choco mb-2">
                🏪
              </div>
              <div class="font-bold text-sm text-ink">Store Pickup</div>
              <div class="text-xs text-success font-semibold mt-0.5">FREE</div>
              <div class="text-[11px] text-warm-gray mt-1 max-w-[200px] truncate"
                :title="storeInfo.store_address || 'Bacoor, Cavite, Philippines'">
                📍 Store: {{ storeInfo.store_address || 'Bacoor, Cavite, Philippines' }}
              </div>
            </label>
          </div>
        </div>

        <!-- 2. Customer & Address Details -->
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-brand-caramel/20 shadow-sm space-y-4">
          <h3 class="font-extrabold text-xl text-ink border-b border-brand-caramel/20 pb-3">
            2. Customer Details
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <BaseInput v-model="form.customer_name" label="Full Name" placeholder="e.g. Maria Santos" required
              :error="errors.customer_name?.[0]" />
            <BaseInput v-model="form.customer_email" type="email" label="Email Address" placeholder="maria@example.com"
              required :error="errors.customer_email?.[0]" />
            <BaseInput v-model="form.customer_phone" type="tel" numeric-only maxlength="13" label="Mobile Number"
              placeholder="09171234567" required :error="errors.customer_phone?.[0]" />
            <BaseInput v-model="form.city" label="City / District" placeholder="e.g. Bacoor, Cavite" required />
          </div>

          <!-- Delivery Address Fields & Lalamove Live Quote Status -->
          <div v-if="form.fulfillment_type === 'delivery'" class="space-y-3 pt-2">
            <BaseTextarea v-model="form.delivery_address" label="Complete Delivery Address"
              placeholder="House/Unit #, Street Name, Barangay, Landmark (e.g. 123 Zapote Road, Brgy. Molino III)"
              rows="3" required :error="errors.delivery_address?.[0]" />

            <!-- Interactive Pinpoint Map Picker -->
            <AddressMapPicker v-model:address="form.delivery_address" v-model:city="form.city"
              :store-lat="parseFloat(storeInfo.store_lat) || 14.4597"
              :store-lng="parseFloat(storeInfo.store_lng) || 120.9640" @location-selected="handleLocationPinpointed" />

            <!-- Live Lalamove Quote Status Box -->
            <div class="p-3.5 rounded-2xl text-xs transition-all border" :class="[
              quotingDelivery ? 'bg-surface border-brand-caramel/30 text-brand-choco' :
                quoteError ? 'bg-red-50 border-red-200 text-error' :
                  quotedFee !== null ? 'bg-emerald-50 border-emerald-200 text-[#2D4525]' : 'bg-gray-50 border-gray-200 text-gray-600'
            ]">
              <div v-if="quotingDelivery" class="flex items-center gap-2">
                <span class="w-4 h-4 border-2 border-brand-choco border-t-transparent rounded-full animate-spin"></span>
                <span>Calculating Lalamove delivery rate for your location...</span>
              </div>
              <div v-else-if="quotedFee !== null" class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <span class="text-base">🛵</span>
                  <div>
                    <span class="font-bold text-sm">{{ quoteProvider }} Motorcycle Rate: ₱{{ quotedFee.toFixed(2)
                      }}</span>
                    <span v-if="quoteNote" class="block text-[11px] opacity-80 mt-0.5">{{ quoteNote }}</span>
                  </div>
                </div>
                <span class="bg-emerald-600 text-white px-2 py-0.5 rounded-full font-bold text-[10px]">Calculated</span>
              </div>
              <div v-else-if="quoteError" class="flex items-center gap-2">
                <span>⚠️</span>
                <span>{{ quoteError }}</span>
              </div>
              <div v-else class="text-warm-gray">
                💡 Type your complete street and barangay address above to auto-calculate your Lalamove delivery rate.
              </div>
            </div>
          </div>

          <BaseTextarea v-model="form.notes" label="Special Instructions (Optional)"
            placeholder="e.g. Please call upon arrival, birthday candle count, etc." rows="2" />
        </div>

        <!-- 3. Payment Method Selection -->
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-brand-caramel/20 shadow-sm space-y-4">
          <h3 class="font-extrabold text-xl text-ink border-b border-brand-caramel/20 pb-3">
            3. Select Payment Method
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- GCash -->
            <label v-tooltip="'Pay securely via GCash E-Wallet redirect'"
              class="border-2 rounded-2xl p-4 cursor-pointer flex items-center gap-3 transition-all"
              :class="form.payment_method === 'gcash' ? 'border-brand-choco bg-surface' : 'border-brand-caramel/20 bg-white opacity-70 hover:opacity-100'">
              <input type="radio" v-model="form.payment_method" value="gcash" class="sr-only" />
              <div
                class="w-10 h-10 rounded-xl bg-blue-500 text-white font-bold text-xs flex items-center justify-center shrink-0">
                GCash
              </div>
              <div>
                <div class="font-bold text-sm text-ink">GCash E-Wallet</div>
                <div class="text-[11px] text-warm-gray">Instant payment via PayMongo</div>
              </div>
            </label>

            <!-- Maya -->
            <label v-tooltip="'Pay securely via Maya App / Card redirect'"
              class="border-2 rounded-2xl p-4 cursor-pointer flex items-center gap-3 transition-all"
              :class="form.payment_method === 'maya' ? 'border-brand-choco bg-surface' : 'border-brand-caramel/20 bg-white opacity-70 hover:opacity-100'">
              <input type="radio" v-model="form.payment_method" value="maya" class="sr-only" />
              <div
                class="w-10 h-10 rounded-xl bg-emerald-600 text-white font-bold text-xs flex items-center justify-center shrink-0">
                Maya
              </div>
              <div>
                <div class="font-bold text-sm text-ink">Maya Wallet / Card</div>
                <div class="text-[11px] text-warm-gray">Pay via Maya App / Card</div>
              </div>
            </label>

            <!-- Bank Transfer (Static BDO) -->
            <label v-tooltip="'Manual BDO Online Transfer / Bank Deposit'"
              class="border-2 rounded-2xl p-4 cursor-pointer flex items-center gap-3 transition-all"
              :class="form.payment_method === 'bank_transfer' ? 'border-brand-choco bg-surface' : 'border-brand-caramel/20 bg-white opacity-70 hover:opacity-100'">
              <input type="radio" v-model="form.payment_method" value="bank_transfer" class="sr-only" />
              <div
                class="w-10 h-10 rounded-xl bg-[#003366] text-white font-black text-xs flex items-center justify-center shrink-0">
                BDO
              </div>
              <div>
                <div class="font-bold text-sm text-ink">BDO Bank Transfer</div>
                <div class="text-[11px] text-warm-gray">Manual BDO online transfer / deposit</div>
              </div>
            </label>

            <!-- Cash on Delivery -->
            <label v-tooltip="'Pay cash directly upon delivery / store pickup'"
              class="border-2 rounded-2xl p-4 cursor-pointer flex items-center gap-3 transition-all"
              :class="form.payment_method === 'cod' ? 'border-brand-choco bg-surface' : 'border-brand-caramel/20 bg-white opacity-70 hover:opacity-100'">
              <input type="radio" v-model="form.payment_method" value="cod" class="sr-only" />
              <div
                class="w-10 h-10 rounded-xl bg-amber-700 text-white font-bold text-xs flex items-center justify-center shrink-0">
                COD
              </div>
              <div>
                <div class="font-bold text-sm text-ink">Cash on Delivery</div>
                <div class="text-[11px] text-warm-gray">Pay cash upon arrival</div>
              </div>
            </label>
          </div>

          <!-- Dynamic BDO Account Info Box (Configured via Store Settings) -->
          <div v-if="form.payment_method === 'bank_transfer'"
            class="p-4 bg-surface border border-brand-caramel/30 rounded-2xl text-xs space-y-1 text-ink">
            <p class="font-bold text-sm text-brand-choco flex items-center gap-1.5">
              <span>🏦 BDO Unibank Account Details</span>
            </p>
            <p><strong>Bank Name:</strong> BDO Unibank (Banco de Oro)</p>
            <p><strong>Account Name:</strong> {{ storeInfo.bdo_account_name || 'ABCDips & Treats' }}</p>
            <p><strong>Account Number:</strong> <span class="font-mono font-bold text-brand-choco">{{
              storeInfo.bdo_account_number || '0012-3456-7890' }}</span></p>
            <p class="text-[11px] text-warm-gray pt-1 border-t border-brand-caramel/20 mt-1">
              {{ storeInfo.bdo_instructions || 'Please transfer your payment to our BDO Account and present reference code upon delivery or pickup.' }}
            </p>
          </div>
        </div>

      </div>

      <!-- Order Summary Right Column -->
      <div class="lg:col-span-5 space-y-6">
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-brand-caramel/20 shadow-sm space-y-6">
          <h3 class="font-extrabold text-xl text-ink border-b border-brand-caramel/20 pb-3">
            Order Items ({{ cartStore.itemCount }})
          </h3>

          <!-- Items Mini List -->
          <div class="max-h-60 overflow-y-auto space-y-3 pr-1">
            <div v-for="item in cartStore.items" :key="item.id" class="flex items-center justify-between text-xs py-1">
              <div class="flex items-center gap-3 min-w-0">
                <img :src="item.image_url || '/images/placeholder-bakery.png'"
                  class="w-10 h-10 rounded-lg object-cover shrink-0" />
                <div class="truncate">
                  <div class="font-bold text-ink truncate">{{ item.options?.is_custom ? item.options.custom_title
                    :
                    item.name }}</div>

                  <div class="flex items-center gap-2 mt-1">
                    <div class="flex items-center border border-brand-caramel/30 rounded-lg overflow-hidden bg-surface">
                      <button type="button" @click.prevent="adjustItemQty(item, -1)" v-tooltip="'Decrease quantity'"
                        class="px-2 py-0.5 text-xs font-bold text-brand-choco hover:bg-brand-tan/40 transition-colors">-</button>
                      <span class="px-2 text-[11px] font-bold text-ink">{{ item.qty }}</span>
                      <button type="button" @click.prevent="adjustItemQty(item, 1)" v-tooltip="'Increase quantity'"
                        class="px-2 py-0.5 text-xs font-bold text-brand-choco hover:bg-brand-tan/40 transition-colors">+</button>
                    </div>
                    <button type="button" @click.prevent="cartStore.removeItem(item.id)" v-tooltip="'Remove item'"
                      class="text-[10px] text-red-500 hover:underline">Remove</button>
                  </div>

                </div>
              </div>
              <span class="font-bold text-brand-choco shrink-0">₱{{ (item.subtotal || 0).toFixed(2) }}</span>
            </div>
          </div>

          <!-- Promo Coupon / Voucher Box -->
          <div class="border-t border-b border-brand-caramel/20 py-3 space-y-2">
            <div v-if="cartStore.couponCode"
              class="flex items-center justify-between bg-success/15 border border-success/30 p-2.5 rounded-xl text-xs">
              <div class="flex items-center gap-1.5">
                <span>🎟️</span>
                <span class="font-bold text-[#2D4525]">{{ cartStore.couponCode }}</span>
                <span class="text-[10px] text-success">(Applied)</span>
              </div>
              <button type="button" class="text-xs text-error font-bold hover:underline"
                @click="cartStore.removeCoupon">Remove</button>
            </div>
            <div v-else class="flex gap-2">
              <input v-model="couponCode" type="text" placeholder="Coupon / Voucher code..."
                class="flex-1 px-3 py-1.5 text-xs rounded-xl border border-brand-caramel/30 bg-surface/50 text-ink focus:outline-none focus:border-brand-choco"
                @keyup.enter="handleApplyCoupon" />
              <BaseButton size="sm" variant="secondary" :loading="applyingCoupon" @click="handleApplyCoupon">Apply
              </BaseButton>
            </div>
          </div>

          <!-- Price Calculation -->
          <div class="space-y-3 text-sm pt-2">
            <div class="flex justify-between text-warm-gray">
              <span>Subtotal</span>
              <span class="font-semibold text-ink">₱{{ cartStore.subtotal.toFixed(2) }}</span>
            </div>

            <div v-if="cartStore.discount > 0" class="flex justify-between text-success">
              <span>Discount</span>
              <span>-₱{{ cartStore.discount.toFixed(2) }}</span>
            </div>

            <div class="flex justify-between text-warm-gray">
              <span
                v-tooltip="form.fulfillment_type === 'pickup' ? 'No delivery fee for store pickup' : 'Lalamove dynamic delivery quote'"
                class="cursor-help">
                Delivery Fee ({{ form.fulfillment_type === 'pickup' ? 'Pickup' : quoteProvider }})
              </span>
              <span class="font-semibold text-ink">
                <span v-if="quotingDelivery" class="text-xs text-brand-caramel">Quoting...</span>
                <span v-else>₱{{ deliveryFee.toFixed(2) }}</span>
              </span>
            </div>

            <div class="flex justify-between text-xl font-extrabold text-brand-choco border-t border-brand-caramel/20 pt-3">
              <span>Final Total</span>
              <span>₱{{ grandTotal.toFixed(2) }}</span>
            </div>
          </div>

          <BaseButton type="submit" variant="primary" full-width size="lg" :loading="submitting"
            v-tooltip="'Place order and proceed to payment'">
            Confirm &amp; Place Order • ₱{{ grandTotal.toFixed(2) }}
          </BaseButton>
        </div>
      </div>

    </form>

    <!-- Bank Transfer Instructions Modal -->
    <Teleport to="body">
      <Transition name="fade">
        <div v-if="bdoModalOpen"
          class="fixed inset-0 z-100 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
          <div class="bg-white rounded-3xl p-6 md:p-8 max-w-md w-full border border-brand-caramel/20 shadow-2xl space-y-6">
            <div class="text-center space-y-2">
              <div
                class="w-16 h-16 bg-brand-choco text-surface font-bold text-lg rounded-2xl flex items-center justify-center mx-auto shadow-md uppercase px-1">
                {{ (bdoDetails.bank_name || storeInfo.bank_name || 'BANK').slice(0, 5) }}
              </div>
              <h3 class="text-2xl font-extrabold text-ink">{{ bdoDetails.bank_name || storeInfo.bank_name ||
                'Bank' }}
                Transfer</h3>
              <p class="text-xs text-warm-gray">Please complete your transfer to finalize your order.</p>
            </div>

            <div class="bg-surface p-4 rounded-2xl border border-brand-caramel/30 space-y-3 text-xs">
              <div class="flex justify-between border-b border-brand-caramel/20 pb-2">
                <span class="text-warm-gray">Bank:</span>
                <span class="font-bold text-ink">{{ bdoDetails.bank_name || storeInfo.bank_name || 'Bank Transfer'
                  }}</span>
              </div>
              <div class="flex justify-between border-b border-brand-caramel/20 pb-2">
                <span class="text-warm-gray">Account Name:</span>
                <span class="font-bold text-ink">{{ bdoDetails.account_name || storeInfo.bank_account_name || 'ABCDips & Treats' }}</span>
              </div>
              <div class="flex justify-between items-center border-b border-brand-caramel/20 pb-2">
                <span class="text-warm-gray">Account Number:</span>
                <div class="flex items-center gap-2">
                  <span class="font-extrabold text-brand-choco text-sm">{{ bdoDetails.account_number ||
                    storeInfo.bank_account_number || 'Not configured' }}</span>
                  <button type="button"
                    @click="copyToClipboard(bdoDetails.account_number || storeInfo.bank_account_number || '')"
                    class="text-[10px] bg-brand-choco text-white px-2 py-0.5 rounded hover:bg-choco-600">Copy</button>
                </div>
              </div>
              <div class="flex justify-between border-b border-brand-caramel/20 pb-2">
                <span class="text-warm-gray">Transfer Amount:</span>
                <span class="font-extrabold text-sm text-brand-choco">₱{{ (bdoDetails.transfer_amount ||
                  grandTotal).toFixed(2) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-warm-gray">Payment Reference:</span>
                <span class="font-bold text-brand-choco">{{ bdoDetails.reference_note || createdOrderNumber }}</span>
              </div>
            </div>

            <div class="text-[11px] text-warm-gray space-y-1">
              <p>📌 <strong>Important Instructions:</strong></p>
              <ul class="list-disc pl-4 space-y-0.5">
                <li>Transfer exact amount ₱{{ grandTotal.toFixed(2) }} via {{ storeInfo.bank_name || 'Bank' }} Online or
                  OTC.</li>
                <li>Use reference <strong>{{ createdOrderNumber }}</strong> in your transfer note.</li>
                <li v-if="storeInfo.bank_instructions">{{ storeInfo.bank_instructions }}</li>
                <li>Your order state is set to <strong>Pending</strong> until bank receipt verification.</li>
              </ul>
            </div>

            <div class="flex gap-3">
              <RouterLink :to="`/orders/track/${createdOrderToken || createdOrderNumber}`" class="flex-1">
                <BaseButton variant="primary" full-width>Track Order</BaseButton>
              </RouterLink>
              <RouterLink to="/account/orders" class="flex-1">
                <BaseButton variant="outline" full-width>My Orders</BaseButton>
              </RouterLink>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, inject } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'
import { useToast } from '@/composables/useToast'
import PageHeader from '@/components/ui/PageHeader.vue'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseTextarea from '@/components/ui/BaseTextarea.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseAlert from '@/components/ui/BaseAlert.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import AddressMapPicker from '@/components/checkout/AddressMapPicker.vue'

const axios = inject('axios')
const router = useRouter()
const cartStore = useCartStore()
const authStore = useAuthStore()
const toast = useToast()

const submitting = ref(false)
const verifyingEmail = ref(false)
const sendingEmail = ref(false)
const emailSent = ref(false)
const verificationTab = ref('email')
const otpPhone = ref(authStore.user?.phone || '')
const otpCode = ref('')
const couponCode = ref('')
const applyingCoupon = ref(false)

async function handleApplyCoupon() {
  if (!couponCode.value.trim()) return
  applyingCoupon.value = true
  const res = await cartStore.applyCoupon(couponCode.value)
  applyingCoupon.value = false
  if (res.success) {
    toast.success('Discount coupon applied successfully!', 'Voucher Applied 🎟️')
    couponCode.value = ''
  } else {
    toast.error(res.error || 'Invalid or expired coupon code.', 'Coupon Error')
  }
}
const sendingOtp = ref(false)
const otpSent = ref(false)
const verifyingOtp = ref(false)
const errors = ref({})

async function sendVerificationEmail() {
  sendingEmail.value = true
  try {
    const { data } = await axios.post('/api/customer/send-verification-email')
    emailSent.value = true
    toast.success(data.message || 'Verification email sent to your inbox!', 'Verification Email Sent ✉️')
  } catch (err) {
    toast.error('Failed to send verification email.', 'Error')
  } finally {
    sendingEmail.value = false
  }
}

async function sendPhoneOtp() {
  if (!otpPhone.value) {
    toast.error('Please enter a valid mobile phone number.', 'Validation Error')
    return
  }
  sendingOtp.value = true
  try {
    const { data } = await axios.post('/api/customer/send-phone-otp', { phone: otpPhone.value })
    otpSent.value = true
    toast.success(data.message || 'Verification code sent! Please check your mobile device.', 'SMS Code Sent 📱')
  } catch (err) {
    toast.error(err.response?.data?.message || 'Failed to send SMS code.', 'Error')
  } finally {
    sendingOtp.value = false
  }
}

async function verifyPhoneOtp() {
  if (otpCode.value.length !== 6) {
    toast.error('Please enter a 6-digit verification code.', 'Validation Error')
    return
  }
  verifyingOtp.value = true
  try {
    const { data } = await axios.post('/api/customer/verify-phone-otp', { otp: otpCode.value })
    authStore.user = data.data
    toast.success('Your account has been verified successfully via mobile number!', 'Account Verified 🎉')
  } catch (err) {
    toast.error(err.response?.data?.message || 'Invalid verification code. Please try again.', 'Verification Error')
  } finally {
    verifyingOtp.value = false
  }
}

async function verifyEmail() {
  verifyingEmail.value = true
  try {
    const { data } = await axios.post('/api/customer/verify-email')
    authStore.user = data.data
    toast.success('Your account email has been verified successfully!', 'Account Verified')
  } catch (err) {
    toast.error('Failed to verify account email.', 'Verification Error')
  } finally {
    verifyingEmail.value = false
  }
}

// Store settings & quote state
const storeInfo = ref({})
const quotingDelivery = ref(false)
const quotedFee = ref(null)
const quoteProvider = ref('Lalamove')
const quoteNote = ref('')
const quoteError = ref('')

// BDO Modal state
const bdoModalOpen = ref(false)
const bdoDetails = ref({})
const createdOrderNumber = ref('')
const createdOrderToken = ref('')

const form = ref({
  customer_name: '',
  customer_email: '',
  customer_phone: '',
  fulfillment_type: 'delivery',
  delivery_address: '',
  city: 'Bacoor, Cavite',
  postal_code: '',
  notes: '',
  payment_method: 'gcash'
})

// Debounced delivery quote fetcher
let quoteTimeout = null
function fetchDeliveryQuote() {
  if (form.value.fulfillment_type !== 'delivery') {
    quotedFee.value = null
    return
  }

  const fullAddr = (form.value.delivery_address + ' ' + form.value.city).trim()
  if (fullAddr.length < 8) {
    quotedFee.value = null
    quoteError.value = ''
    return
  }

  if (quoteTimeout) clearTimeout(quoteTimeout)
  quoteTimeout = setTimeout(async () => {
    quotingDelivery.value = true
    quoteError.value = ''
    try {
      const { data } = await axios.post('/api/delivery/quote', { address: fullAddr })
      if (data.success && data.fee !== null) {
        quotedFee.value = data.fee
        quoteProvider.value = data.provider_label || 'Lalamove'
        quoteNote.value = data.note || ''
      } else {
        quotedFee.value = 120.00
        quoteProvider.value = 'Standard Rate'
        quoteError.value = data.error || 'Address not matched, using standard flat rate.'
      }
    } catch {
      quotedFee.value = 120.00
      quoteProvider.value = 'Standard Rate'
      quoteError.value = 'Unable to connect to Lalamove, applied default rate ₱120.'
    } finally {
      quotingDelivery.value = false
    }
  }, 600)
}

function populateUserData() {
  if (authStore.user) {
    form.value.customer_name = authStore.user.name || ''
    form.value.customer_email = authStore.user.email || ''
    form.value.customer_phone = authStore.user.phone || ''
    form.value.delivery_address = authStore.user.address || ''
  } else {
    form.value.customer_name = ''
    form.value.customer_email = ''
    form.value.customer_phone = ''
    form.value.delivery_address = ''
  }
}

watch(() => authStore.user, (user) => {
  if (user) {
    populateUserData()
    if (form.value.delivery_address && form.value.fulfillment_type === 'delivery') {
      fetchDeliveryQuote()
    }
  }
}, { immediate: true })

function adjustItemQty(item, delta) {
  const newQty = (item.qty || 1) + delta
  if (newQty <= 0) {
    cartStore.removeItem(item.id)
  } else {
    cartStore.updateItemQty(item.id, newQty)
  }
}

function handleLocationPinpointed(loc) {
  if (loc.address) form.value.delivery_address = loc.address
  if (loc.city) form.value.city = loc.city
  fetchDeliveryQuote()
}

watch(() => [form.value.delivery_address, form.value.city, form.value.fulfillment_type], () => {
  if (form.value.fulfillment_type === 'delivery') {
    fetchDeliveryQuote()
  } else {
    quotedFee.value = null
    quoteError.value = ''
  }
})

onMounted(async () => {
  if (!authStore.isAuthenticated) {
    toast.warning('Please sign in to place your order.', 'Sign In Required')
    router.push({ name: 'login', query: { redirect: '/checkout' } })
    return
  }
  populateUserData()
  await cartStore.fetchCart()

  try {
    const { data } = await axios.get('/api/settings/store')
    storeInfo.value = data || {}
    if (data.store_address && (form.value.city === 'Bacoor, Cavite' || !form.value.city)) {
      form.value.city = data.store_address
    }
  } catch { }

  if (form.value.delivery_address) {
    fetchDeliveryQuote()
  }
})

const deliveryFee = computed(() => {
  if (form.value.fulfillment_type === 'pickup') return 0.00
  return quotedFee.value !== null ? quotedFee.value : 120.00
})

const grandTotal = computed(() => Math.max(0, cartStore.total + deliveryFee.value))

function copyToClipboard(text) {
  navigator.clipboard.writeText(text)
  toast.success('Account number copied to clipboard!', 'Copied')
}

async function handleCheckout() {
  if (!authStore.isAuthenticated) {
    toast.warning('Please sign in to place your order.', 'Sign In Required')
    router.push({ name: 'login', query: { redirect: '/checkout' } })
    return
  }

  submitting.value = true
  errors.value = {}

  try {
    // 1. Create order
    const payload = {
      ...form.value,
      shipping_fee: deliveryFee.value,
    }

    const { data } = await axios.post('/api/checkout', payload, {
      headers: { 'X-Cart-Token': cartStore.cartToken }
    })

    const order = data.data
    const orderId = order.id
    createdOrderNumber.value = order.order_number || ''
    createdOrderToken.value = order.tracking_token || order.trackingToken || ''

    cartStore.clearLocalCart()

    // 2. Handle Payment Method Routing
    if (form.value.payment_method === 'gcash' || form.value.payment_method === 'maya') {
      toast.info(`Redirecting to ${form.value.payment_method.toUpperCase()} payment gateway...`, 'Processing Payment')
      try {
        const payRes = await axios.post('/api/payments/create-source', {
          order_id: orderId,
          method: form.value.payment_method
        })

        if (payRes.data?.checkout_url) {
          window.location.href = payRes.data.checkout_url
          return
        }
      } catch (payErr) {
        console.warn('PayMongo source creation fallback', payErr)
      }

      // Fallback sandbox redirect if PayMongo keys not set
      router.push({
        name: 'payment-success',
        query: { order: order.order_number, method: form.value.payment_method }
      })
      return
    }

    if (form.value.payment_method === 'bank_transfer') {
      bdoDetails.value = {
        bank_name: storeInfo.value.bank_name || 'BDO',
        account_name: storeInfo.value.bank_account_name || 'ABCDips & Treats',
        account_number: storeInfo.value.bank_account_number || '0012-3456-7890',
        transfer_amount: grandTotal.value,
        reference_note: order.order_number,
      }
      bdoModalOpen.value = true
      toast.success('Order placed! Please complete your bank transfer.', 'Order Created')
      return
    }

    // COD / Store Pickup
    toast.success('Order placed successfully! State: Pending confirmation.', 'Thank you!')
    router.push({ name: 'order-confirmation', params: { token: createdOrderToken.value || createdOrderNumber.value } })

  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {}
      toast.error('Please check the form for missing or invalid details.', 'Checkout Error')
    } else {
      toast.error(err.response?.data?.message || 'Failed to place order. Please try again.', 'Checkout Error')
    }
  } finally {
    submitting.value = false
  }
}

function onNumericKeydown(event) {
  const allowedControlKeys = [
    'Backspace', 'Delete', 'Tab', 'Escape', 'Enter',
    'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown',
    'Home', 'End'
  ]
  if (allowedControlKeys.includes(event.key) || event.ctrlKey || event.metaKey) {
    return
  }
  if (!/^\d$/.test(event.key) && event.key !== '+') {
    event.preventDefault()
  }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>

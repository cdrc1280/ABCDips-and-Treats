<template>
  <div class="min-h-screen bg-surface py-12 px-4 md:px-8">
    <div class="max-w-6xl mx-auto space-y-12">

      <!-- Header -->
      <div class="border-b border-brand-caramel/30 pb-6 flex items-center justify-between">
        <div>
          <span class="script-accent text-brand-caramel text-xl">design system &amp; component kit</span>
          <h1 class="text-3xl md:text-4xl font-bold text-ink mt-1">ABCDips &amp; Treats Component Showcase</h1>
        </div>
        <BaseButton variant="outline" size="sm" @click="triggerToast">
          Test Toast Notification
        </BaseButton>
      </div>

      <!-- 1. Buttons -->
      <section class="space-y-4">
        <h2 class="text-xl font-bold text-brand-choco border-b border-brand-caramel/20 pb-2">1. Buttons &amp; Variants</h2>
        <div class="flex flex-wrap gap-4 items-center">
          <BaseButton variant="primary">Primary Button</BaseButton>
          <BaseButton variant="secondary">Secondary Button</BaseButton>
          <BaseButton variant="outline">Outline Button</BaseButton>
          <BaseButton variant="ghost">Ghost Button</BaseButton>
          <BaseButton variant="danger">Danger Button</BaseButton>
        </div>

        <div class="flex flex-wrap gap-4 items-center pt-2">
          <BaseButton size="sm">Small</BaseButton>
          <BaseButton size="md">Medium</BaseButton>
          <BaseButton size="lg">Large</BaseButton>
          <BaseButton loading>Loading State</BaseButton>
          <BaseButton disabled>Disabled State</BaseButton>
        </div>
      </section>

      <!-- 2. Badges & Chips -->
      <section class="space-y-4">
        <h2 class="text-xl font-bold text-brand-choco border-b border-brand-caramel/20 pb-2">2. Badges &amp; Status Chips</h2>
        <div class="flex flex-wrap gap-3 items-center">
          <BaseBadge variant="brand" dot>Brand Badge</BaseBadge>
          <BaseBadge variant="neutral" dot>Neutral Status</BaseBadge>
          <BaseBadge variant="success" dot>Available / Fresh</BaseBadge>
          <BaseBadge variant="warning" dot>Low Stock</BaseBadge>
          <BaseBadge variant="error" dot>Out of Stock</BaseBadge>
          <BaseBadge variant="outline">Outline Chip</BaseBadge>
        </div>
      </section>

      <!-- 3. Form Inputs -->
      <section class="space-y-4">
        <h2 class="text-xl font-bold text-brand-choco border-b border-brand-caramel/20 pb-2">3. Form Controls &amp; Inputs</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <BaseInput
            label="Product Name"
            placeholder="e.g. Classic Banana Bread"
            hint="Enter the official bakery product name"
          />
          <BaseInput
            label="Email Address (Success)"
            model-value="order@abcdips.ph"
            success
            hint="Valid email format"
          />
          <BaseInput
            label="SKU Code (Error)"
            model-value="INVALID-SKU"
            error="SKU format must be BAKERY-XXX-000"
          />

          <BaseSelect
            label="Product Category"
            placeholder="Select a category..."
            :options="['Banana Bread', 'Cookies', 'Brownies', 'Cheesecakes', 'Cakes', 'Cupcakes']"
          />

          <div class="space-y-3 pt-2">
            <span class="block text-xs font-semibold uppercase tracking-wider text-brand-choco">Checkboxes &amp; Radios</span>
            <BaseCheckbox v-model="checkboxVal" label="Include allergy warning label" />
            <div class="flex gap-4">
              <BaseRadio v-model="radioVal" value="delivery" label="Delivery" />
              <BaseRadio v-model="radioVal" value="pickup" label="Store Pickup" />
            </div>
          </div>

          <BaseTextarea
            label="Order Notes / Custom Requests"
            placeholder="Add special instructions for your order..."
            rows="3"
          />
        </div>
      </section>

      <!-- 4. Alerts & Notifications -->
      <section class="space-y-4">
        <h2 class="text-xl font-bold text-brand-choco border-b border-brand-caramel/20 pb-2">4. Alerts &amp; Notifications</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <BaseAlert variant="info" title="Fresh Batch Ready!">
            Our signature Ube Cheesecakes just came out of the oven.
          </BaseAlert>
          <BaseAlert variant="success" title="Order Confirmed">
            Order #ABCD-8921 has been sent to the kitchen.
          </BaseAlert>
          <BaseAlert variant="warning" title="Limited Ingredient Stock">
            Butter inventory is below the minimum threshold (15 kg remaining).
          </BaseAlert>
          <BaseAlert variant="error" title="Payment Failed" dismissible>
            GCash checkout timed out. Please try placing your order again.
          </BaseAlert>
        </div>
      </section>

      <!-- 5. Cards & Containers -->
      <section class="space-y-4">
        <h2 class="text-xl font-bold text-brand-choco border-b border-brand-caramel/20 pb-2">5. Cards &amp; Content Containers</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <BaseCard title="Banana Bread Loaf" subtitle="Best Seller • ₱280" hover>
            <p class="text-sm text-warm-gray">Rich, moist loaf made with ripe Cavendish bananas and pure premium butter.</p>
            <template #footer>
              <div class="flex justify-between items-center">
                <BaseBadge variant="success">In Stock</BaseBadge>
                <BaseButton size="sm">Add to Cart</BaseButton>
              </div>
            </template>
          </BaseCard>

          <BaseCard title="Box of 9 Fudgy Brownies" subtitle="Signature Treat • ₱320" hover>
            <p class="text-sm text-warm-gray">Decadent Belgian dark chocolate brownies with crackly tops and fudgy centers.</p>
            <template #footer>
              <div class="flex justify-between items-center">
                <BaseBadge variant="brand">Pre-Order</BaseBadge>
                <BaseButton size="sm" variant="secondary">View Details</BaseButton>
              </div>
            </template>
          </BaseCard>

          <BaseCard title="Custom Celebration Cake" subtitle="Cakes • Starts ₱900">
            <p class="text-sm text-warm-gray">Personalized tiered cakes tailored to your theme, flavors, and frosting choice.</p>
            <template #footer>
              <BaseButton size="sm" variant="outline" full-width>Customize Cake</BaseButton>
            </template>
          </BaseCard>
        </div>
      </section>

      <!-- 6. Table & Skeleton Loading States -->
      <section class="space-y-4">
        <h2 class="text-xl font-bold text-brand-choco border-b border-brand-caramel/20 pb-2">6. Tables &amp; Skeleton Shimmer Loading States</h2>

        <div class="flex items-center gap-4 mb-4">
          <BaseButton size="sm" :variant="tableLoading ? 'primary' : 'outline'" @click="tableLoading = !tableLoading">
            Toggle Table Loading State
          </BaseButton>
        </div>

        <BaseTable
          :columns="[
            { key: 'sku', label: 'SKU' },
            { key: 'name', label: 'Product Name' },
            { key: 'category', label: 'Category' },
            { key: 'price', label: 'Price', align: 'right' },
            { key: 'status', label: 'Status' }
          ]"
          :items="[
            { id: 1, sku: 'BB-001', name: 'Banana Bread Loaf', category: 'Loaves', price: '₱280.00', status: 'In Stock' },
            { id: 2, sku: 'CC-004', name: 'Chocolate Chip Cookies (6 pcs)', category: 'Cookies', price: '₱180.00', status: 'In Stock' },
            { id: 3, sku: 'BR-009', name: 'Fudgy Brownies (Box of 9)', category: 'Brownies', price: '₱320.00', status: 'Low Stock' },
            { id: 4, sku: 'CS-006', name: 'Classic Cheesecake 6-inch', category: 'Cheesecakes', price: '₱650.00', status: 'In Stock' }
          ]"
          :loading="tableLoading"
        >
          <template #cell-status="{ value }">
            <BaseBadge :variant="value === 'Low Stock' ? 'warning' : 'success'" dot>
              {{ value }}
            </BaseBadge>
          </template>
        </BaseTable>
      </section>

      <!-- 7. Skeleton Component Showcase -->
      <section class="space-y-4">
        <h2 class="text-xl font-bold text-brand-choco border-b border-brand-caramel/20 pb-2">7. Layout-Matching Skeleton Shimmer Cards</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <SkeletonCard />
          <SkeletonCard />
          <SkeletonCard />
        </div>
      </section>

      <!-- 8. Empty State Motif -->
      <section class="space-y-4">
        <h2 class="text-xl font-bold text-brand-choco border-b border-brand-caramel/20 pb-2">8. Cookie-Doodle Empty State</h2>
        <div class="bg-white rounded-2xl border border-brand-caramel/15">
          <EmptyState
            title="Your Pastry Basket is Empty"
            description="You haven't added any fresh baked treats to your order yet. Explore our freshly oven-baked collection!"
          >
            <template #action>
              <BaseButton variant="primary">Explore Menu</BaseButton>
            </template>
          </EmptyState>
        </div>
      </section>

      <SectionDivider />

    </div>
    <ToastContainer />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseCard from '@/components/ui/BaseCard.vue'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseSelect from '@/components/ui/BaseSelect.vue'
import BaseTextarea from '@/components/ui/BaseTextarea.vue'
import BaseCheckbox from '@/components/ui/BaseCheckbox.vue'
import BaseRadio from '@/components/ui/BaseRadio.vue'
import BaseAlert from '@/components/ui/BaseAlert.vue'
import BaseBadge from '@/components/ui/BaseBadge.vue'
import BaseTable from '@/components/ui/BaseTable.vue'
import SkeletonCard from '@/components/ui/SkeletonCard.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import SectionDivider from '@/components/ui/SectionDivider.vue'
import ToastContainer from '@/components/ui/ToastContainer.vue'
import { useToast } from '@/composables/useToast'

const toast = useToast()
const checkboxVal = ref(true)
const radioVal = ref('delivery')
const tableLoading = ref(false)

function triggerToast() {
  toast.success('Components showcase initialized!', 'ABCDips UI System')
}
</script>

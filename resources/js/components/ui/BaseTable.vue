<template>
  <div class="w-full bg-white rounded-2xl border border-brand-caramel/15 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm text-ink">
        <!-- Table Header -->
        <thead class="bg-surface border-b border-brand-caramel/20 text-xs font-semibold uppercase tracking-wider text-brand-choco">
          <tr>
            <th
              v-for="col in columns"
              :key="col.key"
              class="px-6 py-4"
              :class="col.align === 'right' ? 'text-right' : col.align === 'center' ? 'text-center' : 'text-left'"
            >
              {{ col.label }}
            </th>
          </tr>
        </thead>

        <!-- Table Body -->
        <tbody class="divide-y divide-brand-caramel/10">
          <template v-if="loading">
            <tr v-for="i in skeletonRows" :key="i">
              <td v-for="col in columns" :key="col.key" class="px-6 py-4">
                <SkeletonBlock height="1.25rem" :width="col.skeletonWidth || '70%'" radius="0.375rem" />
              </td>
            </tr>
          </template>

          <template v-else-if="items.length === 0">
            <tr>
              <td :colspan="columns.length" class="px-6 py-8 text-center text-warm-gray">
                <slot name="empty">
                  <EmptyState title="No records found" description="There are no items to display in this table." />
                </slot>
              </td>
            </tr>
          </template>

          <template v-else>
            <tr
              v-for="(item, index) in items"
              :key="item.id || index"
              class="hover:bg-surface/40 transition-colors duration-150"
            >
              <td
                v-for="col in columns"
                :key="col.key"
                class="px-6 py-4"
                :class="col.align === 'right' ? 'text-right' : col.align === 'center' ? 'text-center' : 'text-left'"
              >
                <slot :name="`cell-${col.key}`" :item="item" :index="index" :value="item[col.key]">
                  {{ item[col.key] }}
                </slot>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <!-- Pagination slot / footer -->
    <div v-if="$slots.pagination" class="border-t border-brand-caramel/15 px-6 py-4 bg-surface/30">
      <slot name="pagination" />
    </div>
  </div>
</template>

<script setup>
import SkeletonBlock from './SkeletonBlock.vue'
import EmptyState from './EmptyState.vue'

defineProps({
  columns: { type: Array, required: true }, // Array of { key, label, align?, skeletonWidth? }
  items: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
  skeletonRows: { type: Number, default: 4 }
})
</script>

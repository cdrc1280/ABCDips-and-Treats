<template>
  <div class="flex items-center gap-4 py-3 px-4 border-b border-brand-caramel/10">
    <SkeletonBlock height="2.5rem" width="2.5rem" radius="0.5rem" class="shrink-0" />
    <div class="flex-1 space-y-2">
      <SkeletonBlock height="1rem" width="60%" radius="0.375rem" />
      <SkeletonBlock height="0.75rem" width="35%" radius="0.375rem" />
    </div>
    <SkeletonBlock height="1.5rem" width="80px" radius="0.5rem" class="shrink-0" />
  </div>
</template>

<script setup>
import SkeletonBlock from './SkeletonBlock.vue'
</script>

<template>
  <div class="bg-white rounded-2xl p-4 border border-[#C08E5D]/15 space-y-4" style="box-shadow: var(--shadow-sm);">
    <!-- Aspect ratio image block -->
    <SkeletonBlock height="180px" radius="1rem" />
    
    <!-- Title + price -->
    <div class="space-y-2">
      <SkeletonBlock height="1.25rem" width="80%" radius="0.5rem" />
      <SkeletonBlock height="0.875rem" width="40%" radius="0.375rem" />
    </div>

    <!-- Description -->
    <SkeletonText :lines="2" height="0.75rem" />

    <!-- Action button placeholder -->
    <SkeletonBlock height="2.5rem" radius="0.75rem" />
  </div>
</template>

<script setup>
import SkeletonBlock from './SkeletonBlock.vue'
import SkeletonText from './SkeletonText.vue'
</script>

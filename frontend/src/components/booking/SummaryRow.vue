<template>
  <div class="flex justify-between items-center py-2.5 border-b border-slate-200 last:border-0">
    <span class="text-xs text-slate-400">{{ label }}</span>
    <span :class="['text-sm font-semibold', highlighted ? 'text-emerald' : 'text-ink']">{{ value }}</span>
  </div>
</template>

<script setup lang="ts">
defineProps<{ label: string; value: string; highlighted?: boolean }>()
</script>

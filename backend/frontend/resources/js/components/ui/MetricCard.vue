<template>
  <div
    class="bg-white dark:bg-ink-2 border border-slate-200 dark:border-white/10 rounded-[14px] p-5 shadow-sm relative overflow-hidden hover:shadow transition-all duration-200 hover:-translate-y-px"
  >
    <!-- Top accent bar -->
    <div :class="['absolute top-0 left-0 right-0 h-[3px] rounded-t-[14px]', accentClass]"></div>

    <!-- Icon -->
    <div :class="['w-10 h-10 rounded-[10px] flex items-center justify-center text-lg mb-3.5', iconBg]">
      {{ icon }}
    </div>

    <div class="text-[26px] font-bold text-ink dark:text-white tracking-tight leading-none">
      {{ formattedValue }}
    </div>
    <div class="text-xs text-slate-400 dark:text-white/40 mt-1.5">{{ label }}</div>
    <div :class="['text-[11px] font-semibold mt-2.5 flex items-center gap-1', deltaClass]">
      {{ delta }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  value: number | string
  label: string
  delta?: string
  color?: 'green' | 'blue' | 'amber' | 'red'
  icon?: string
}>()

const COLOR_MAP = {
  green: { accent: 'bg-emerald',           iconBg: 'bg-emerald-4 dark:bg-emerald/20 text-emerald', delta: 'text-emerald' },
  blue:  { accent: 'bg-brand-sapphire',    iconBg: 'bg-blue-50 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400',   delta: 'text-blue-600 dark:text-blue-400' },
  amber: { accent: 'bg-brand-amber',       iconBg: 'bg-amber-50 dark:bg-amber-500/20 text-amber-500',  delta: 'text-amber-600 dark:text-amber-400' },
  red:   { accent: 'bg-brand-coral',       iconBg: 'bg-red-50 dark:bg-red-500/20 text-red-600 dark:text-red-400',    delta: 'text-red-600 dark:text-red-400' },
}

const cfg = computed(() => COLOR_MAP[props.color ?? 'green'])
const accentClass = computed(() => cfg.value.accent)
const iconBg      = computed(() => cfg.value.iconBg)
const deltaClass  = computed(() => cfg.value.delta)

const formattedValue = computed(() => {
  if (typeof props.value === 'number' && props.value >= 1000) {
    return (props.value / 1000).toFixed(1) + 'k'
  }
  return props.value
})
</script>

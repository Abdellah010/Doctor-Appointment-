<template>
  <span :class="pillClass">{{ label }}</span>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{ status: string }>()

const CONFIG: Record<string, { cls: string; label: string }> = {
  confirmed: { cls: 'bg-emerald-50 text-emerald-800 border border-emerald-200',  label: 'Confirmed'  },
  completed: { cls: 'bg-slate-100 text-slate-600 border border-slate-200',       label: 'Completed'  },
  pending:   { cls: 'bg-amber-50 text-amber-800 border border-amber-200',        label: 'Pending'    },
  cancelled: { cls: 'bg-red-50 text-red-700 border border-red-200',             label: 'Cancelled'  },
  declined:  { cls: 'bg-red-50 text-red-700 border border-red-200',             label: 'Declined'   },
  verified:  { cls: 'bg-emerald-50 text-emerald-800 border border-emerald-200',  label: '✓ Verified' },
  rejected:  { cls: 'bg-red-50 text-red-700 border border-red-200',             label: '✗ Rejected' },
}

const cfg = computed(() => CONFIG[props.status] ?? { cls: 'bg-slate-100 text-slate-500', label: props.status })
const pillClass = computed(() => `inline-flex items-center text-[11px] font-bold px-2.5 py-1 rounded-full ${cfg.value.cls}`)
const label     = computed(() => cfg.value.label)
</script>

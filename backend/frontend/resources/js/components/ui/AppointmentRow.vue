<template>
  <div class="flex gap-4 items-start px-5 py-4 border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors group">
    <!-- Time column -->
    <div class="text-right min-w-[52px] flex-shrink-0">
      <div class="text-[14px] font-bold text-ink">{{ time }}</div>
      <div class="text-[10px] text-slate-400 mt-0.5">{{ dateLabel }}</div>
    </div>

    <!-- Color line -->
    <div :class="['w-[3px] rounded-full self-stretch mt-0.5 flex-shrink-0', lineColor]"></div>

    <!-- Body -->
    <div class="flex-1 min-w-0">
      <div class="text-[14px] font-semibold text-ink">{{ appointment.doctor.user.name }}</div>
      <div class="text-xs text-slate-400 mt-0.5 truncate">
        {{ appointment.doctor.specialty }} · {{ appointment.reason ?? 'No reason specified' }}
      </div>

      <!-- Actions -->
      <div class="flex gap-2 mt-2 opacity-0 group-hover:opacity-100 transition-opacity">
        <button
          class="text-[11px] font-semibold px-2.5 py-1 rounded-md border border-slate-200 bg-white text-slate-600 hover:border-slate-300 transition-colors"
          @click="$emit('reschedule', appointment)"
        >
          Reschedule
        </button>
        <button
          class="text-[11px] font-semibold px-2.5 py-1 rounded-md border border-red-100 bg-white text-red-600 hover:bg-red-50 transition-colors"
          @click="$emit('cancel', appointment)"
        >
          Cancel
        </button>
      </div>
    </div>

    <!-- Status pill -->
    <StatusPill :status="appointment.status" />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import StatusPill from './StatusPill.vue'
import type { Appointment } from '@/types'

const props = defineProps<{ appointment: Appointment }>()
defineEmits<{ cancel: [a: Appointment]; reschedule: [a: Appointment] }>()

const dt        = computed(() => new Date(props.appointment.scheduled_at))
const time      = computed(() => dt.value.toLocaleTimeString('en', { hour: '2-digit', minute: '2-digit' }))
const dateLabel = computed(() => dt.value.toLocaleString('en', { month: 'short', day: 'numeric' }))

const LINE_COLORS: Record<string, string> = {
  confirmed: 'bg-emerald',
  pending:   'bg-amber-400',
  completed: 'bg-slate-300',
  cancelled: 'bg-red-400',
  declined:  'bg-red-400',
}
const lineColor = computed(() => LINE_COLORS[props.appointment.status] ?? 'bg-slate-300')
</script>

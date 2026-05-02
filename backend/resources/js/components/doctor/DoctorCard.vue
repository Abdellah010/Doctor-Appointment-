<template>
  <div
    class="bg-white dark:bg-ink-2 border border-slate-200 dark:border-white/10 rounded-[14px] p-3 md:p-5 shadow-sm hover:border-emerald dark:hover:border-emerald/50 hover:shadow transition-all duration-200 cursor-pointer group relative flex flex-row gap-3 md:gap-5 items-center"
    :class="{ 'opacity-70': isPending }"
    @click="handleClick"
  >
    <!-- Avatar -->
    <img
      :src="doctor.id % 2 === 0 ? '/images/doctor_male.png' : '/images/hero_doctor.png'"
      alt="Doctor Avatar"
      class="w-12 h-12 md:w-20 md:h-20 rounded-full object-cover border-2 border-slate-100 dark:border-white/10 flex-shrink-0"
    />

    <!-- Info & Tags -->
    <div class="flex-1 min-w-0">
      <div class="flex flex-col md:flex-row md:items-center justify-between mb-1">
        <div class="truncate">
          <div class="font-bold text-[15px] md:text-[18px] text-ink dark:text-white tracking-tight truncate">{{ doctor.user.name }}</div>
          <div class="flex items-center gap-2 mt-1.5">
            <span class="text-[10px] md:text-[11px] font-black text-emerald bg-emerald/5 px-2 py-0.5 rounded-md uppercase tracking-[0.1em]">{{ doctor.specialty }}</span>
            <span class="text-slate-300 dark:text-white/10">•</span>
            <span class="text-[10px] md:text-[11px] font-black text-slate-500 dark:text-white/40 uppercase tracking-[0.1em]">{{ doctor.city }}</span>
          </div>
        </div>
        
        <div class="mt-1 md:mt-0 flex items-center gap-1 text-[13px] md:text-[15px] font-semibold text-ink dark:text-white">
          <span class="text-amber-400 text-[10px] md:text-sm">★</span>
          <span>{{ doctor.rating.toFixed(1) }}</span>
          <span class="text-slate-400 font-normal text-[11px] md:text-sm">({{ doctor.rating_count }})</span>
        </div>
      </div>

      <div class="flex flex-wrap items-center gap-1 md:gap-2 mt-2">
        <span
          v-if="doctor.status === 'verified'"
          class="inline-flex items-center gap-1 text-[9px] md:text-xs font-bold bg-emerald-4 dark:bg-emerald/20 text-[#065F46] dark:text-emerald border border-emerald-3 dark:border-emerald/30 px-2 py-0.5 md:px-2.5 md:py-1 rounded-full"
        >
          ✓ Verified
        </span>
        <span v-if="doctor.accepts_cnss" class="text-[9px] md:text-xs font-semibold bg-blue-50 dark:bg-blue-500/20 text-blue-800 dark:text-blue-400 border border-blue-200 dark:border-blue-500/30 px-2 py-0.5 md:px-2.5 md:py-1 rounded-full">CNSS</span>
      </div>
    </div>

    <!-- Action & Price (Side on mobile) -->
    <div class="flex flex-col items-end gap-1 flex-shrink-0">
      <div class="text-[13px] md:text-[15px] font-bold text-ink dark:text-white">
        {{ isPending ? '—' : `${doctor.consultation_fee} MAD` }}
      </div>
      <button
        class="hidden md:block bg-emerald text-white text-sm font-bold px-6 py-2.5 rounded-lg hover:bg-emerald-2 transition-colors shadow-sm"
        @click.stop="handleClick"
      >
        Book
      </button>
      <!-- Arrow for mobile -->
      <div class="md:hidden text-slate-300">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { Doctor } from '@/types'

const props = defineProps<{ doctor: Doctor }>()
const emit = defineEmits<{ book: [doctor: Doctor] }>()

const isPending = computed(() => props.doctor.status === 'pending')

function handleClick() {
  if (!isPending.value) {
    emit('book', props.doctor)
  }
}

const AVATAR_COLORS = [
  { bg: '#ECFDF5', color: '#065F46' },
  { bg: '#EFF6FF', color: '#1E3A8A' },
  { bg: '#FDF2F8', color: '#9D174D' },
  { bg: '#FFFBEB', color: '#92400E' },
  { bg: '#F0FDF4', color: '#14532D' },
]

const avatarStyle = computed(() => {
  const idx = props.doctor.id % AVATAR_COLORS.length
  return AVATAR_COLORS[idx]
})

const avatarBg    = computed(() => avatarStyle.value.bg)
const avatarColor = computed(() => avatarStyle.value.color)

const initials = computed(() =>
  props.doctor.user.name
    .replace('Dr. ', '')
    .split(' ')
    .map(w => w[0])
    .join('')
    .slice(0, 2)
    .toUpperCase()
)
</script>

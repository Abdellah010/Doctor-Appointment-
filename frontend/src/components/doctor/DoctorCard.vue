<template>
  <div
    class="bg-white border border-slate-200 rounded-[14px] p-5 shadow-sm hover:border-emerald hover:shadow transition-all duration-200 cursor-pointer group relative"
    :class="{ 'opacity-70': !doctor.isVerified }"
    @click="!isPending && $emit('book', doctor)"
  >
    <!-- Book button on hover -->
    <button
      v-if="!isPending"
      class="absolute top-4 right-4 bg-emerald text-white text-xs font-bold px-3 py-1.5 rounded-md opacity-0 group-hover:opacity-100 transition-opacity"
      @click.stop="$emit('book', doctor)"
    >
      Book →
    </button>

    <!-- Avatar -->
    <div
      class="w-13 h-13 rounded-full flex items-center justify-center text-lg font-bold mb-3.5 border-2 border-slate-100"
      :style="{ background: avatarBg, color: avatarColor }"
    >
      {{ initials }}
    </div>

    <!-- Info -->
    <div class="font-bold text-[15px] text-ink tracking-tight">{{ doctor.user.name }}</div>
    <div class="text-sm text-slate-400 mt-0.5">{{ doctor.specialty }} · {{ doctor.city }}</div>

    <!-- Tags -->
    <div class="flex gap-1.5 flex-wrap mt-3">
      <span
        v-if="doctor.status === 'verified'"
        class="inline-flex items-center gap-1 text-xs font-bold bg-emerald-3 text-[#065F46] border border-emerald-3 px-2.5 py-1 rounded-full"
      >
        ✓ Verified
      </span>
      <span
        v-else
        class="text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 px-2.5 py-1 rounded-full"
      >
        Under review
      </span>
      <span v-if="doctor.accepts_cnss" class="text-xs font-semibold bg-blue-50 text-blue-800 px-2 py-1 rounded-full">CNSS</span>
      <span v-if="doctor.accepts_ramed" class="text-xs font-semibold bg-blue-50 text-blue-800 px-2 py-1 rounded-full">RAMED</span>
    </div>

    <!-- Footer -->
    <div class="flex items-center justify-between mt-4 pt-3.5 border-t border-slate-100">
      <div class="flex items-center gap-1.5 text-sm font-semibold text-ink">
        <span class="text-amber-400 text-xs">★★★★★</span>
        <span>{{ doctor.rating.toFixed(1) }}</span>
        <span class="text-slate-400 font-normal">({{ doctor.rating_count }})</span>
      </div>
      <div class="text-sm font-bold text-emerald">
        {{ isPending ? '—' : `${doctor.consultation_fee} MAD` }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { Doctor } from '@/types'

const props = defineProps<{ doctor: Doctor }>()
defineEmits<{ book: [doctor: Doctor] }>()

const isPending = computed(() => props.doctor.status === 'pending')

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

<template>
  <AppLayout>
    <!-- Hero -->
    <section class="bg-ink rounded-[28px] mx-6 mt-6 px-10 py-10 flex gap-8 items-end overflow-hidden relative">
      <!-- Background glow -->
      <div class="absolute top-[-80px] right-[180px] w-[300px] h-[300px] rounded-full"
           style="background: radial-gradient(circle, rgba(5,150,105,.18) 0%, transparent 70%); pointer-events:none;"></div>

      <!-- Text -->
      <div class="flex-1 pb-2 relative z-10">
        <div class="inline-flex items-center gap-2 text-[11px] font-bold text-emerald bg-emerald/10 border border-emerald/25 px-3 py-1.5 rounded-full mb-5 uppercase tracking-wider">
          <span class="w-2 h-2 bg-emerald rounded-full"></span>
          Trusted by 3,200+ patients in Morocco
        </div>
        <h1 class="font-serif text-[30px] text-white italic leading-[1.2] mb-3">
          Healthcare, delivered<br>with precision.
        </h1>
        <p class="text-[13px] text-white/45 leading-[1.7] max-w-[360px]">
          Connect with board-certified specialists, book appointments in seconds, and take control of your health journey — all in one trusted platform.
        </p>
        <div class="flex gap-3 mt-6">
          <Link
            href="/doctors"
            class="px-6 py-2.5 bg-emerald text-white text-[13px] font-bold rounded-[10px] hover:bg-emerald-2 transition-all shadow-[0_4px_16px_rgba(5,150,105,.35)] hover:shadow-[0_6px_20px_rgba(5,150,105,.45)] hover:-translate-y-px"
          >
            Find a doctor
          </Link>
          <Link
            href="/register"
            class="px-6 py-2.5 bg-white/8 text-white/80 text-[13px] font-bold rounded-[10px] border border-white/15 hover:bg-white/14 transition-all"
          >
            Sign up free →
          </Link>
        </div>
      </div>

      <!-- Hero card -->
      <div class="w-[210px] flex-shrink-0 relative z-10">
        <div class="bg-white/7 border border-white/10 backdrop-blur rounded-t-[14px] p-4 flex flex-col gap-3">
          <div class="bg-emerald/18 border border-emerald/30 rounded-[10px] p-3">
            <div class="text-[10px] font-bold text-emerald uppercase tracking-wider mb-1.5">Next appointment</div>
            <div class="text-[13px] font-bold text-white">Dr. Sara Alaoui</div>
            <div class="text-[11px] text-white/40 mt-0.5">Today · 11:00 AM · Cardiology</div>
          </div>
          <div class="bg-white/6 border border-white/8 rounded-[10px] p-3">
            <div class="text-[10px] text-white/30">Cardiologist · Casablanca</div>
            <div class="text-[12px] text-white font-medium mt-1">Routine checkup + ECG</div>
          </div>
          <div class="bg-white/6 border border-white/8 rounded-[10px] p-3">
            <div class="text-[10px] text-white/30 mb-1.5">Appointment status</div>
            <span class="inline-flex items-center gap-1 text-[11px] font-bold bg-emerald-3 text-[#065F46] px-2.5 py-1 rounded-full">
              ✓ Confirmed
            </span>
          </div>
        </div>
      </div>
    </section>

    <!-- Trust stats -->
    <section class="grid grid-cols-3 gap-4 mx-6 mt-5">
      <div
        v-for="stat in trustStats"
        :key="stat.label"
        class="bg-white border border-slate-200 rounded-[14px] px-5 py-4 shadow-sm flex items-center gap-4"
      >
        <div :class="['w-11 h-11 rounded-[10px] flex items-center justify-center text-xl flex-shrink-0', stat.iconBg]">
          {{ stat.icon }}
        </div>
        <div>
          <div class="text-[22px] font-bold text-ink tracking-tight leading-none">{{ stat.value }}</div>
          <div class="text-[11px] text-slate-400 mt-1">{{ stat.label }}</div>
        </div>
      </div>
    </section>

    <!-- Why DocAppoint -->
    <section class="mx-6 mt-8 mb-12">
      <h2 class="text-[13px] font-bold text-ink mb-4">Why DocAppoint?</h2>
      <div class="grid grid-cols-3 gap-4">
        <div
          v-for="feat in features"
          :key="feat.title"
          class="bg-white border border-slate-200 rounded-[14px] p-5 shadow-sm hover:shadow hover:-translate-y-0.5 transition-all duration-200"
        >
          <div :class="['w-10 h-10 rounded-[10px] flex items-center justify-center text-xl mb-3.5', feat.iconBg]">
            {{ feat.icon }}
          </div>
          <div class="text-[14px] font-bold text-ink mb-1.5">{{ feat.title }}</div>
          <div class="text-[12px] text-slate-500 leading-[1.65]">{{ feat.desc }}</div>
          <div class="mt-3.5">
            <span :class="['text-[11px] font-bold px-2.5 py-1 rounded-full', feat.pillClass]">
              {{ feat.pill }}
            </span>
          </div>
        </div>
      </div>
    </section>
  </AppLayout>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/components/layout/AppLayout.vue'

const trustStats = [
  { icon: '🏥', iconBg: 'bg-emerald-4', value: '124',  label: 'Board-certified doctors' },
  { icon: '👥', iconBg: 'bg-blue-50',   value: '3.2k', label: 'Patients served'         },
  { icon: '⭐', iconBg: 'bg-amber-50',  value: '98%',  label: 'Satisfaction rate'       },
]

const features = [
  {
    icon: '🛡️', iconBg: 'bg-emerald-4',
    title: 'Verified doctors only',
    desc:  'Every doctor is manually reviewed and credentialed by our team before they can accept appointments. Zero compromises on trust.',
    pill: 'Medical-grade vetting', pillClass: 'bg-emerald-3 text-[#065F46]',
  },
  {
    icon: '⚡', iconBg: 'bg-blue-50',
    title: 'Book in under 2 minutes',
    desc:  'See real availability, pick your slot, confirm — and receive SMS + email reminders automatically. No waiting, no back-and-forth.',
    pill: 'Instant confirmation', pillClass: 'bg-blue-50 text-blue-800',
  },
  {
    icon: '📊', iconBg: 'bg-amber-50',
    title: 'Complete health tracking',
    desc:  'Your full appointment history, doctor notes, and upcoming bookings in one timeline. Always know where your health stands.',
    pill: 'Health dashboard', pillClass: 'bg-amber-50 text-amber-800',
  },
]
</script>

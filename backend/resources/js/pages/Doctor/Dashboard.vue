<template>
  <AppLayout>
    <div class="py-2">
      <!-- Metrics -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-7">
        <MetricCard icon="📋" label="Today's appointments" :value="stats.today_count"        delta="↑ 2 pending review"       color="green" />
        <MetricCard icon="📈" label="This month"           :value="stats.month_count"        delta="↑ +12% vs last month"    color="blue"  />
        <MetricCard icon="⭐" label="Patient rating"       :value="doctor.rating.toFixed(1)" delta="Top 5% of doctors"       color="amber" />
        <MetricCard icon="💰" label="Revenue (MAD)"        :value="stats.revenue"            :delta="`${stats.month_count} × ${doctor.consultation_fee} MAD`" color="green" />
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-[1fr_280px] gap-6 items-start">
        <div>
          <!-- Weekly schedule -->
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-[13px] font-bold text-ink dark:text-white uppercase tracking-wider opacity-60">Weekly schedule</h2>
            <span class="text-xs text-slate-400 dark:text-white/40">{{ weekLabel }}</span>
          </div>
          <div class="bg-white dark:bg-ink-2 border border-slate-200 dark:border-white/10 rounded-[14px] shadow-sm overflow-x-auto mb-6">
            <div class="min-w-[800px]">
              <!-- Calendar header -->
              <div class="grid bg-slate-50 dark:bg-white/5 border-b border-slate-200 dark:border-white/10" :style="gridStyle">
                <div class="px-3 py-2.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-r border-slate-200 dark:border-white/10"></div>
                <div
                  v-for="day in weekDays"
                  :key="day.label"
                  class="px-2 py-2.5 text-center text-[11px] font-bold text-slate-400 uppercase tracking-wide border-r border-slate-200 dark:border-white/10 last:border-0"
                >
                  {{ day.label }}
                </div>
              </div>

              <!-- Time rows -->
              <div
                v-for="hour in workHours"
                :key="hour"
                class="grid border-b border-slate-100 dark:border-white/5 last:border-0"
                :style="gridStyle"
              >
                <div class="px-3 py-2 text-[10px] text-slate-400 font-semibold text-right border-r border-slate-100 dark:border-white/5">{{ hour }}</div>
                <div
                  v-for="day in weekDays"
                  :key="day.date"
                  class="p-1.5 border-r border-slate-100 dark:border-white/5 last:border-0 min-h-[48px]"
                >
                  <div
                    v-for="appt in getAppts(day.date, hour)"
                    :key="appt.id"
                    :class="['text-[11px] font-bold rounded-[6px] px-2 py-1.5 cursor-pointer transition-colors',
                      appt.status === 'confirmed'
                        ? 'bg-emerald-4 dark:bg-emerald/20 border border-emerald-3 dark:border-emerald/30 text-[#065F46] dark:text-emerald hover:bg-emerald-3'
                        : 'bg-blue-50 dark:bg-blue-500/20 border border-blue-100 dark:border-blue-500/30 text-blue-800 dark:text-blue-400']"
                  >
                    {{ appt.patient.name.split(' ')[0] }} {{ appt.patient.name.split(' ')[1]?.[0] }}.
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Pending requests -->
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-[13px] font-bold text-ink dark:text-white uppercase tracking-wider opacity-60">Pending requests</h2>
            <span v-if="pending.length" class="text-xs font-bold bg-amber-50 dark:bg-amber-500/20 text-amber-800 dark:text-amber-400 border border-amber-200 dark:border-amber-500/30 px-2.5 py-1 rounded-full">
              {{ pending.length }} waiting
            </span>
          </div>
          <div class="bg-white dark:bg-ink-2 border border-slate-200 dark:border-white/10 rounded-[14px] shadow-sm overflow-hidden mb-8 lg:mb-0">
            <div v-if="pending.length === 0" class="py-10 text-center text-sm text-slate-400 dark:text-white/30">
              No pending requests — you're all caught up ✓
            </div>
            <div
              v-for="appt in pending"
              :key="appt.id"
              class="flex items-center gap-4 px-5 py-4 border-b border-slate-100 dark:border-white/5 last:border-0 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors"
            >
              <div class="text-right min-w-[52px]">
                <div class="text-sm font-bold text-ink dark:text-white">{{ formatTime(appt.scheduled_at) }}</div>
                <div class="text-[10px] text-slate-400 dark:text-white/30">{{ formatShortDate(appt.scheduled_at) }}</div>
              </div>
              <div class="flex-1">
                <div class="text-sm font-semibold text-ink dark:text-white">{{ appt.patient.name }}</div>
                <div class="text-xs text-slate-400 dark:text-white/40 mt-0.5">{{ appt.reason ?? 'No reason specified' }}</div>
              </div>
              <div class="flex gap-2">
                <button
                  @click="accept(appt.id)"
                  class="text-[11px] font-bold px-3 py-1.5 rounded-md bg-emerald-4 dark:bg-emerald/20 border border-emerald-3 dark:border-emerald/30 text-[#065F46] dark:text-emerald hover:bg-emerald-3 transition-colors"
                >Accept</button>
                <button
                  @click="decline(appt.id)"
                  class="text-[11px] font-bold px-3 py-1.5 rounded-md bg-red-50 dark:bg-red-500/20 border border-red-100 dark:border-red-500/30 text-red-700 dark:text-red-400 hover:bg-red-100 transition-colors"
                >Decline</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Right sidebar -->
        <div class="space-y-5">
          <!-- Activity chart -->
          <div>
            <div class="flex items-center justify-between mb-3">
              <h2 class="text-[13px] font-bold text-ink dark:text-white uppercase tracking-wider opacity-60">Weekly activity</h2>
              <span class="text-base font-extrabold text-emerald">+23%</span>
            </div>
            <div class="bg-white dark:bg-ink-2 border border-slate-200 dark:border-white/10 rounded-[14px] shadow-sm p-5">
              <div class="text-xs text-slate-400 dark:text-white/40 mb-3.5">Appointments / day</div>
              <div class="flex items-end gap-1.5 h-14">
                <div
                  v-for="(bar, i) in weeklyActivity"
                  :key="i"
                  :class="['flex-1 rounded-sm transition-all', bar.hi ? 'bg-emerald' : 'bg-emerald-4 dark:bg-emerald/20']"
                  :style="{ height: bar.pct + '%' }"
                ></div>
              </div>
              <div class="flex justify-between mt-1.5">
                <span class="text-[10px] text-slate-400 dark:text-white/30">Mon</span>
                <span class="text-[10px] text-slate-400 dark:text-white/30">Sun</span>
              </div>
            </div>
          </div>

          <!-- Availability settings -->
          <div>
            <h2 class="text-[13px] font-bold text-ink dark:text-white mb-3 uppercase tracking-wider opacity-60">Availability</h2>
            <div class="bg-white dark:bg-ink-2 border border-slate-200 dark:border-white/10 rounded-[14px] shadow-sm p-5">
              <div class="form-group">
                <label class="form-label">Consultation fee</label>
                <div class="relative">
                  <input v-model="settingsForm.fee" class="form-input pr-14" type="number">
                  <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400 dark:text-white/40">MAD</span>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Available days</label>
                <div class="flex flex-wrap gap-1.5 mt-1">
                  <button
                    v-for="day in allDays"
                    :key="day.key"
                    @click="toggleDay(day.key)"
                    :class="['text-[11px] font-bold px-2.5 py-1.5 rounded-md border transition-all',
                      settingsForm.days.includes(day.key)
                        ? 'bg-emerald border-emerald text-white'
                        : 'bg-slate-50 dark:bg-white/5 border-slate-200 dark:border-white/10 text-slate-500 dark:text-white/40 hover:border-slate-300']"
                  >
                    {{ day.label }}
                  </button>
                </div>
              </div>
              <button
                @click="saveSettings"
                class="w-full py-3 bg-emerald text-white text-sm font-bold rounded-[10px] hover:bg-emerald-2 transition-colors shadow-sm mt-2"
              >
                Save schedule
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { computed, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/components/layout/AppLayout.vue'
import MetricCard from '@/components/ui/MetricCard.vue'
import type { Appointment, Doctor } from '@/types'

const props = defineProps<{
  doctor: Doctor
  appointments: Appointment[]
  pending: Appointment[]
  stats: { today_count: number; month_count: number; revenue: number }
  weeklyActivity: { label: string; count: number }[]
}>()

const settingsForm = reactive({
  fee:  props.doctor.consultation_fee,
  days: [...props.doctor.available_days],
})

const allDays = [
  { key: 'mon', label: 'Mon' }, { key: 'tue', label: 'Tue' },
  { key: 'wed', label: 'Wed' }, { key: 'thu', label: 'Thu' },
  { key: 'fri', label: 'Fri' }, { key: 'sat', label: 'Sat' },
]

const workHours = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00']
const gridStyle = 'grid-template-columns: 56px repeat(5, 1fr)'

const weekDays = computed(() => {
  const days = []
  for (let i = 0; i < 5; i++) {
    const d = new Date()
    d.setDate(d.getDate() - d.getDay() + 1 + i)
    days.push({
      label: d.toLocaleString('en', { weekday: 'short' }) + ' ' + d.getDate(),
      date:  d.toISOString().slice(0, 10),
    })
  }
  return days
})

const weekLabel = computed(() => {
  const start = weekDays.value[0]
  const end   = weekDays.value[4]
  return `${new Date(start.date).toLocaleString('en', { month: 'short', day: 'numeric' })} – ${new Date(end.date).toLocaleString('en', { month: 'short', day: 'numeric' })}`
})

const weeklyActivity = computed(() =>
  props.weeklyActivity.map((d, i, arr) => {
    const max = Math.max(...arr.map(x => x.count), 1)
    return { ...d, pct: Math.max((d.count / max) * 100, 8), hi: d.count === max }
  })
)

function getAppts(date: string, hour: string) {
  return props.appointments.filter(a => {
    const dt = new Date(a.scheduled_at)
    return dt.toISOString().slice(0, 10) === date &&
           `${String(dt.getHours()).padStart(2,'0')}:00` === hour
  })
}

function formatTime(dt: string) {
  return new Date(dt).toLocaleTimeString('en', { hour: '2-digit', minute: '2-digit' })
}

function formatShortDate(dt: string) {
  return new Date(dt).toLocaleString('en', { month: 'short', day: 'numeric' })
}

function toggleDay(key: string) {
  const idx = settingsForm.days.indexOf(key)
  idx > -1 ? settingsForm.days.splice(idx, 1) : settingsForm.days.push(key)
}

function accept(id: number)  { router.patch(`/appointments/${id}/accept`) }
function decline(id: number) { router.patch(`/appointments/${id}/decline`) }

function saveSettings() {
  router.patch('/doctor/availability', {
    consultation_fee: settingsForm.fee,
    available_days:   settingsForm.days,
  })
}
</script>

<style scoped>
.form-group { @apply mb-4; }
.form-label { @apply block text-xs font-semibold text-slate-500 mb-1.5; }
.form-input { @apply w-full border border-slate-200 rounded-[10px] px-3 py-2.5 text-sm text-ink outline-none focus:border-emerald focus:ring-2 focus:ring-emerald/10 transition-all; }
</style>

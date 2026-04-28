<template>
  <AppLayout>
    <div class="py-2">

      <!-- Alert banner -->
      <Link
        v-if="stats.pending_verifications > 0"
        href="/admin/verifications?status=pending"
        class="flex items-center gap-4 bg-amber-50 border border-amber-200 rounded-[14px] px-5 py-4 mb-7 hover:shadow transition-shadow group"
      >
        <span class="text-2xl flex-shrink-0">⚠️</span>
        <div class="flex-1">
          <div class="text-sm font-bold text-amber-800">
            {{ stats.pending_verifications }} doctor{{ stats.pending_verifications > 1 ? 's' : '' }} pending verification
          </div>
          <div class="text-xs text-amber-600 mt-0.5">Oldest submission: 1 week ago — review required</div>
        </div>
        <span class="text-amber-600 text-lg group-hover:translate-x-0.5 transition-transform">→</span>
      </Link>

      <!-- Metrics -->
      <div class="grid grid-cols-4 gap-4 mb-7">
        <MetricCard icon="👥" label="Total patients"      :value="stats.total_patients"        delta="↑ +12% this month"  color="blue"  />
        <MetricCard icon="🩺" label="Active doctors"      :value="stats.active_doctors"        delta="↑ +3 this week"     color="green" />
        <MetricCard icon="📅" label="Appointments"        :value="stats.total_appointments"    delta="↑ +8% vs last week" color="green" />
        <MetricCard icon="⏳" label="Pending verifications" :value="stats.pending_verifications" delta="⚠ Needs attention"  color="amber" />
      </div>

      <div class="grid grid-cols-[1fr_300px] gap-6 items-start">
        <!-- Recent appointments table -->
        <div>
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-[13px] font-bold text-ink">Recent appointments</h2>
            <Link href="/admin/verifications" class="text-xs text-emerald font-semibold hover:underline">View verifications →</Link>
          </div>
          <div class="bg-white border border-slate-200 rounded-[14px] shadow-sm overflow-hidden">
            <table class="w-full text-sm">
              <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                  <th class="tbl-th">Patient</th>
                  <th class="tbl-th">Doctor</th>
                  <th class="tbl-th">Date & time</th>
                  <th class="tbl-th">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="appt in recent_appointments"
                  :key="appt.id"
                  class="border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors"
                >
                  <td class="tbl-td font-semibold">{{ appt.patient.name }}</td>
                  <td class="tbl-td text-slate-500">{{ appt.doctor.user.name }}</td>
                  <td class="tbl-td text-slate-500">{{ formatDt(appt.scheduled_at) }}</td>
                  <td class="tbl-td"><StatusPill :status="appt.status" /></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Right sidebar -->
        <div class="space-y-5">
          <!-- Activity chart -->
          <div>
            <div class="flex items-center justify-between mb-3">
              <h2 class="text-[13px] font-bold text-ink">Platform activity</h2>
              <span class="text-base font-extrabold text-emerald">+23%</span>
            </div>
            <div class="bg-white border border-slate-200 rounded-[14px] shadow-sm p-5">
              <div class="text-xs text-slate-400 mb-3.5">Appointments / day (this week)</div>
              <div class="flex items-end gap-1.5 h-16">
                <div
                  v-for="(bar, i) in activityBars"
                  :key="i"
                  :class="['flex-1 rounded-sm transition-all', bar.hi ? 'bg-emerald' : 'bg-emerald-4']"
                  :style="{ height: bar.pct + '%' }"
                ></div>
              </div>
              <div class="flex justify-between mt-1.5">
                <span class="text-[10px] text-slate-400">Mon</span>
                <span class="text-[10px] text-slate-400">Sun</span>
              </div>
            </div>
          </div>

          <!-- Doctor status breakdown -->
          <div>
            <h2 class="text-[13px] font-bold text-ink mb-3">Doctor breakdown</h2>
            <div class="bg-white border border-slate-200 rounded-[14px] shadow-sm p-5 space-y-3.5">
              <div class="flex items-center gap-3">
                <div class="w-2.5 h-2.5 rounded-full bg-emerald flex-shrink-0"></div>
                <div class="flex-1 text-sm text-ink">Verified &amp; active</div>
                <div class="text-[15px] font-extrabold text-ink">{{ stats.active_doctors }}</div>
              </div>
              <div class="flex items-center gap-3">
                <div class="w-2.5 h-2.5 rounded-full bg-amber-400 flex-shrink-0"></div>
                <div class="flex-1 text-sm text-ink">Pending verification</div>
                <div class="text-[15px] font-extrabold text-amber-600">{{ stats.pending_verifications }}</div>
              </div>
              <div class="flex items-center gap-3">
                <div class="w-2.5 h-2.5 rounded-full bg-red-400 flex-shrink-0"></div>
                <div class="flex-1 text-sm text-ink">Rejected</div>
                <div class="text-[15px] font-extrabold text-red-600">3</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/components/layout/AppLayout.vue'
import MetricCard from '@/components/ui/MetricCard.vue'
import StatusPill from '@/components/ui/StatusPill.vue'
import type { AdminDashboardProps } from '@/types'

const props = defineProps<AdminDashboardProps>()

const activityBars = computed(() => {
  const max = Math.max(...props.weekly_activity.map(d => d.count), 1)
  return props.weekly_activity.map(d => ({
    ...d,
    pct: Math.max((d.count / max) * 100, 8),
    hi:  d.count === max,
  }))
})

function formatDt(dt: string) {
  return new Date(dt).toLocaleString('en', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })
}
</script>

<style scoped>
.tbl-th { @apply text-left px-4 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider; }
.tbl-td { @apply px-4 py-3.5 text-slate-700; }
</style>

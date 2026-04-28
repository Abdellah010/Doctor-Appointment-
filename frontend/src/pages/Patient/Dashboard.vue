<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-6 py-8">

      <!-- Patient header card -->
      <div class="bg-white border border-slate-200 rounded-[20px] px-6 py-5 shadow-sm flex items-center gap-5 mb-7">
        <div class="w-14 h-14 rounded-full bg-blue-50 text-blue-800 flex items-center justify-center text-xl font-extrabold border-2 border-slate-100 flex-shrink-0">
          {{ initials }}
        </div>
        <div>
          <div class="text-[17px] font-bold text-ink tracking-tight">{{ user.name }}</div>
          <div class="text-sm text-slate-400 mt-0.5">
            Patient since {{ joinedSince }} · {{ user.insurance_type.toUpperCase() }} insured
          </div>
        </div>
        <div class="ml-auto flex gap-2">
          <span class="pill-green">{{ stats.upcoming_count }} upcoming</span>
          <span class="pill-blue">{{ stats.completed_count }} completed</span>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-3 gap-4 mb-7">
        <MetricCard
          v-for="metric in metrics"
          :key="metric.label"
          :value="metric.value"
          :label="metric.label"
          :delta="metric.delta"
          :color="metric.color"
          :icon="metric.icon"
        />
      </div>

      <div class="grid grid-cols-[1fr_300px] gap-6 items-start">
        <div>
          <!-- Upcoming appointments -->
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-[13px] font-bold text-ink tracking-tight">Upcoming appointments</h2>
            <Link href="/doctors" class="text-xs text-emerald font-semibold hover:underline">+ Book new</Link>
          </div>
          <div class="bg-white border border-slate-200 rounded-[14px] shadow-sm overflow-hidden mb-5">
            <div v-if="upcoming.length === 0" class="py-12 text-center text-slate-400 text-sm">
              No upcoming appointments.
              <Link href="/doctors" class="text-emerald font-semibold hover:underline ml-1">Book one now →</Link>
            </div>
            <AppointmentRow
              v-for="appt in upcoming"
              :key="appt.id"
              :appointment="appt"
              @cancel="cancelAppt"
            />
          </div>

          <!-- History -->
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-[13px] font-bold text-ink">Appointment history</h2>
          </div>
          <div class="bg-white border border-slate-200 rounded-[14px] shadow-sm overflow-hidden">
            <table class="w-full text-sm">
              <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                  <th class="tbl-th">Doctor</th>
                  <th class="tbl-th">Specialty</th>
                  <th class="tbl-th">Date</th>
                  <th class="tbl-th">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="appt in past"
                  :key="appt.id"
                  class="border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors"
                >
                  <td class="tbl-td font-semibold">{{ appt.doctor.user.name }}</td>
                  <td class="tbl-td text-slate-500">{{ appt.doctor.specialty }}</td>
                  <td class="tbl-td text-slate-500">{{ formatDate(appt.scheduled_at) }}</td>
                  <td class="tbl-td"><StatusPill :status="appt.status" /></td>
                </tr>
                <tr v-if="past.length === 0">
                  <td colspan="4" class="tbl-td text-center text-slate-400">No past appointments yet</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Profile sidebar -->
        <div>
          <h2 class="text-[13px] font-bold text-ink mb-3">My profile</h2>
          <div class="bg-white border border-slate-200 rounded-[14px] shadow-sm p-5">
            <form @submit.prevent="saveProfile">
              <div class="form-group">
                <label class="form-label">Full name</label>
                <input v-model="form.name" class="form-input" type="text">
              </div>
              <div class="form-group">
                <label class="form-label">Phone number</label>
                <input v-model="form.phone" class="form-input" type="tel">
              </div>
              <div class="form-group">
                <label class="form-label">Insurance</label>
                <select v-model="form.insurance_type" class="form-input">
                  <option value="cnss">CNSS (Public)</option>
                  <option value="ramed">RAMED</option>
                  <option value="private">Private insurance</option>
                  <option value="none">None</option>
                </select>
              </div>
              <button type="submit" class="btn-primary w-full py-2.5 text-sm">Save changes</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { computed, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/components/layout/AppLayout.vue'
import MetricCard from '@/components/ui/MetricCard.vue'
import AppointmentRow from '@/components/ui/AppointmentRow.vue'
import StatusPill from '@/components/ui/StatusPill.vue'
import type { Appointment, PatientDashboardProps, User } from '@/types'

const props = defineProps<PatientDashboardProps>()

const form = reactive({
  name:           props.user.name,
  phone:          props.user.phone ?? '',
  insurance_type: props.user.insurance_type,
})

const initials = computed(() =>
  props.user.name.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase()
)

const joinedSince = computed(() =>
  new Date(props.user.created_at).toLocaleString('en', { month: 'short', year: 'numeric' })
)

const metrics = computed(() => [
  { label: 'Upcoming',         value: props.stats.upcoming_count,  delta: 'Next: soon',  color: 'green', icon: '📅' },
  { label: 'Total consultations', value: props.stats.completed_count, delta: 'All time',    color: 'blue',  icon: '✅' },
  { label: 'Doctors visited',  value: props.stats.doctors_visited, delta: 'Across specialties', color: 'amber', icon: '🩺' },
])

function formatDate(dt: string) {
  return new Date(dt).toLocaleString('en', { month: 'short', day: 'numeric', year: 'numeric' })
}

function cancelAppt(appt: Appointment) {
  if (!confirm('Cancel this appointment?')) return
  router.patch(`/appointments/${appt.id}/cancel`)
}

function saveProfile() {
  router.patch('/patient/profile', form)
}
</script>

<style scoped>
.pill-green { @apply text-xs font-bold bg-emerald-3 text-[#065F46] border border-emerald-3 px-2.5 py-1 rounded-full; }
.pill-blue  { @apply text-xs font-bold bg-blue-50 text-blue-800 px-2.5 py-1 rounded-full; }
.tbl-th     { @apply text-left px-4 py-2.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider; }
.tbl-td     { @apply px-4 py-3.5 text-slate-700; }
.form-group { @apply mb-4; }
.form-label { @apply block text-xs font-semibold text-slate-500 mb-1.5; }
.form-input { @apply w-full border border-slate-200 rounded-[10px] px-3 py-2.5 text-sm text-ink focus:outline-none focus:border-emerald focus:ring-2 focus:ring-emerald/10 transition-all; }
.btn-primary { @apply bg-emerald text-white rounded-[10px] font-semibold hover:bg-emerald-2 transition-all shadow-sm; }
</style>

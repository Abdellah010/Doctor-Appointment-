<template>
  <AppLayout>
    <div class="py-2">
      <!-- Filter tabs -->
      <div class="flex gap-2 mb-6">
        <Link
          v-for="tab in tabs"
          :key="tab.key"
          :href="`/admin/verifications?status=${tab.key}`"
          :class="['filter-tab', status === tab.key ? 'on' : '']"
        >
          {{ tab.label }}
          <span :class="['ml-1.5 text-xs font-bold', status === tab.key ? 'text-white/70' : 'text-slate-400']">
            {{ counts[tab.key as keyof typeof counts] }}
          </span>
        </Link>
      </div>

      <!-- Doctors table -->
      <div class="bg-white dark:bg-ink-2 border border-slate-200 dark:border-white/10 rounded-[14px] shadow-sm overflow-hidden mb-6">
        <!-- Table header (Hidden on mobile) -->
        <div class="hidden md:grid grid-cols-[2fr_1.2fr_0.8fr_110px_160px] gap-3 px-5 py-3 bg-slate-50 dark:bg-white/5 border-b border-slate-200 dark:border-white/10">
          <span class="tbl-hdr">Doctor</span>
          <span class="tbl-hdr">Specialty</span>
          <span class="tbl-hdr">Submitted</span>
          <span class="tbl-hdr">Status</span>
          <span class="tbl-hdr">Actions</span>
        </div>

        <!-- Rows -->
        <div
          v-for="doctor in doctors.data"
          :key="doctor.id"
          class="flex flex-col md:grid md:grid-cols-[2fr_1.2fr_0.8fr_110px_160px] gap-4 md:gap-3 px-5 py-5 md:py-4 border-b border-slate-100 dark:border-white/5 last:border-0 items-start md:items-center hover:bg-slate-50 dark:hover:bg-white/5 transition-colors"
        >
          <!-- Doctor info -->
          <div class="flex items-center gap-3 w-full md:w-auto">
            <div
              class="w-10 h-10 md:w-9 md:h-9 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
              :style="{ background: '#EFF6FF', color: '#1E3A8A' }"
            >
              {{ initials(doctor) }}
            </div>
            <div class="flex-1">
              <div class="text-sm font-bold text-ink dark:text-white">{{ doctor.user.name }}</div>
              <div class="text-xs text-slate-400 mt-0.5">{{ doctor.specialty }} · {{ doctor.city }}</div>
            </div>
            <!-- Mobile status indicator -->
            <div class="md:hidden">
              <StatusPill :status="doctor.status" />
            </div>
          </div>

          <!-- Mobile Labels (Only visible on small screens) -->
          <div class="md:hidden w-full flex justify-between items-center py-2 border-y border-slate-50 dark:border-white/5">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Specialty</span>
            <span class="text-sm font-medium text-ink dark:text-white">{{ doctor.specialty }}</span>
          </div>

          <span class="hidden md:block text-sm font-medium text-ink dark:text-white">{{ doctor.specialty }}</span>

          <div class="w-full md:w-auto flex justify-between md:block">
            <span class="md:hidden text-[10px] font-bold text-slate-400 uppercase tracking-widest">Submitted</span>
            <span class="text-xs text-slate-400">{{ timeAgo(doctor.created_at) }}</span>
          </div>

          <div class="hidden md:block">
            <StatusPill :status="doctor.status" />
          </div>

          <!-- Actions -->
          <div class="w-full md:w-auto pt-2 md:pt-0">
            <div v-if="doctor.status === 'pending'" class="flex gap-2 w-full md:w-auto">
              <button
                @click="approve(doctor.id)"
                class="flex-1 md:flex-none text-[11px] font-bold px-4 py-2 md:py-1.5 rounded-md bg-emerald-4 border border-emerald-3 text-[#065F46] hover:bg-emerald-3 transition-colors text-center"
              >
                Approve
              </button>
              <button
                @click="openReject(doctor)"
                class="flex-1 md:flex-none text-[11px] font-bold px-4 py-2 md:py-1.5 rounded-md bg-red-50 border border-red-100 text-red-700 hover:bg-red-100 transition-colors text-center"
              >
                Reject
              </button>
            </div>
            <div v-else class="text-xs text-slate-400 font-semibold md:text-right">
              {{ doctor.status === 'verified' ? 'Approved ✓' : 'Rejected ✗' }}
            </div>
          </div>
        </div>

        <div v-if="doctors.data.length === 0" class="py-16 text-center text-slate-400 dark:text-white/20">
          No doctors in this category.
        </div>
      </div>

      <!-- Reject modal -->
      <Teleport to="body">
        <div
          v-if="rejectModal.open"
          class="fixed inset-0 z-50 flex items-center justify-center p-6"
          style="background: rgba(0,0,0,0.45);"
          @click.self="rejectModal.open = false"
        >
          <div class="bg-white rounded-[20px] shadow-lg w-full max-w-md p-6">
            <h2 class="text-lg font-bold text-ink mb-4">Reject with reason</h2>

            <!-- Subject -->
            <div class="flex items-center gap-3 bg-slate-50 rounded-[10px] px-4 py-3 mb-5">
              <div class="w-10 h-10 rounded-full bg-amber-50 text-amber-800 flex items-center justify-center text-sm font-bold flex-shrink-0">
                {{ rejectModal.doctor ? initials(rejectModal.doctor) : '' }}
              </div>
              <div>
                <div class="text-sm font-bold text-ink">{{ rejectModal.doctor?.user.name }}</div>
                <div class="text-xs text-slate-400 mt-0.5">License #: {{ rejectModal.doctor?.license_number }}</div>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Rejection reason</label>
              <select v-model="rejectForm.reason" class="form-input">
                <option value="incomplete_credentials">Incomplete credentials</option>
                <option value="license_not_verifiable">License not verifiable</option>
                <option value="missing_documents">Missing documents</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Additional notes (sent to doctor)</label>
              <input v-model="rejectForm.notes" class="form-input" placeholder="e.g. Please resubmit your license scan...">
            </div>

            <div class="flex gap-3 mt-2">
              <button @click="submitReject" class="px-5 py-2.5 bg-red-600 text-white text-sm font-bold rounded-[10px] hover:bg-red-700 transition-colors">
                Send rejection
              </button>
              <button @click="rejectModal.open = false" class="px-5 py-2.5 border border-slate-200 text-slate-600 text-sm font-semibold rounded-[10px] hover:bg-slate-50 transition-colors">
                Cancel
              </button>
            </div>
          </div>
        </div>
      </Teleport>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/components/layout/AppLayout.vue'
import StatusPill from '@/components/ui/StatusPill.vue'
import type { AdminVerificationsProps, Doctor } from '@/types'

const props = defineProps<AdminVerificationsProps>()

const tabs = [
  { key: 'all',      label: 'All' },
  { key: 'pending',  label: '⏳ Pending' },
  { key: 'verified', label: '✓ Verified' },
  { key: 'rejected', label: '✗ Rejected' },
]

const rejectModal = reactive<{ open: boolean; doctor: Doctor | null }>({
  open: false,
  doctor: null,
})

const rejectForm = reactive({ reason: 'incomplete_credentials', notes: '' })

function initials(doctor: Doctor): string {
  return doctor.user.name
    .replace('Dr. ', '')
    .split(' ')
    .map((w: string) => w[0])
    .join('')
    .slice(0, 2)
    .toUpperCase()
}

function timeAgo(dateStr: string): string {
  const diff = Date.now() - new Date(dateStr).getTime()
  const days = Math.floor(diff / 86400000)
  if (days === 0) return 'Today'
  if (days === 1) return '1 day ago'
  if (days < 7)  return `${days} days ago`
  return `${Math.floor(days / 7)} week${Math.floor(days / 7) > 1 ? 's' : ''} ago`
}

function approve(doctorId: number) {
  router.patch(`/admin/doctors/${doctorId}/approve`)
}

function openReject(doctor: Doctor) {
  rejectModal.doctor = doctor
  rejectModal.open = true
}

function submitReject() {
  if (!rejectModal.doctor) return
  router.patch(`/admin/doctors/${rejectModal.doctor.id}/reject`, rejectForm, {
    onSuccess: () => { rejectModal.open = false },
  })
}
</script>

<style scoped>
.tbl-hdr      { @apply text-[11px] font-bold text-slate-400 uppercase tracking-wider; }
.filter-tab   { @apply text-sm font-semibold px-4 py-2 rounded-full border border-slate-200 bg-white text-slate-600 hover:border-slate-300 transition-colors cursor-pointer; }
.filter-tab.on{ @apply bg-ink border-ink text-white; }
.pill-amber   { @apply text-xs font-bold bg-amber-50 border border-amber-200 text-amber-800 px-3 py-1.5 rounded-full; }
.form-group   { @apply mb-4; }
.form-label   { @apply block text-xs font-semibold text-slate-500 mb-1.5; }
.form-input   { @apply w-full border border-slate-200 rounded-[10px] px-3 py-2.5 text-sm text-ink focus:outline-none focus:border-emerald focus:ring-2 focus:ring-emerald/10 transition-all; }
</style>

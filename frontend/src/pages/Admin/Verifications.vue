<template>
  <AppLayout>
    <div class="max-w-6xl mx-auto px-6 py-8">

      <div class="flex items-center justify-between mb-6">
        <div>
          <h1 class="text-2xl font-bold text-ink tracking-tight">Verification Panel</h1>
          <p class="text-sm text-slate-400 mt-1">Review and approve doctor credentials</p>
        </div>
        <span v-if="counts.pending > 0" class="pill-amber">
          ⚠ {{ counts.pending }} pending review
        </span>
      </div>

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
      <div class="bg-white border border-slate-200 rounded-[14px] shadow-sm overflow-hidden mb-6">
        <!-- Table header -->
        <div class="grid grid-cols-[2fr_1.2fr_0.8fr_110px_160px] gap-3 px-5 py-3 bg-slate-50 border-b border-slate-200">
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
          class="grid grid-cols-[2fr_1.2fr_0.8fr_110px_160px] gap-3 px-5 py-4 border-b border-slate-100 last:border-0 items-center hover:bg-slate-50 transition-colors"
        >
          <!-- Doctor info -->
          <div class="flex items-center gap-3">
            <div
              class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
              :style="{ background: '#EFF6FF', color: '#1E3A8A' }"
            >
              {{ initials(doctor) }}
            </div>
            <div>
              <div class="text-sm font-bold text-ink">{{ doctor.user.name }}</div>
              <div class="text-xs text-slate-400 mt-0.5">{{ doctor.specialty }} · {{ doctor.city }}</div>
            </div>
          </div>

          <span class="text-sm font-medium text-ink">{{ doctor.specialty }}</span>

          <span class="text-xs text-slate-400">{{ timeAgo(doctor.created_at) }}</span>

          <StatusPill :status="doctor.status" />

          <!-- Actions -->
          <div v-if="doctor.status === 'pending'" class="flex gap-2">
            <button
              @click="approve(doctor.id)"
              class="text-[11px] font-bold px-3 py-1.5 rounded-md bg-emerald-4 border border-emerald-3 text-[#065F46] hover:bg-emerald-3 transition-colors"
            >
              Approve
            </button>
            <button
              @click="openReject(doctor)"
              class="text-[11px] font-bold px-3 py-1.5 rounded-md bg-red-50 border border-red-100 text-red-700 hover:bg-red-100 transition-colors"
            >
              Reject
            </button>
          </div>
          <div v-else class="text-xs text-slate-400 font-semibold">
            {{ doctor.status === 'verified' ? 'Approved ✓' : 'Rejected ✗' }}
          </div>
        </div>

        <div v-if="doctors.data.length === 0" class="py-16 text-center text-slate-400">
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

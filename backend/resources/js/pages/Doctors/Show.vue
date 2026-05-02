<template>
  <AppLayout>
    <div class="py-2">

      <!-- Stepper -->
      <div class="bg-white dark:bg-ink-2 border border-slate-200 dark:border-white/10 rounded-[14px] shadow-sm px-4 md:px-7 py-4 md:py-5 flex items-center gap-0 mb-6 md:mb-7">
        <template v-for="(step, idx) in steps" :key="step.label">
          <div class="flex items-center flex-1 min-w-0">
            <div :class="['w-7 h-7 md:w-8 md:h-8 rounded-full flex items-center justify-center text-xs font-bold border-2 flex-shrink-0 transition-all',
              step.done   ? 'bg-emerald border-emerald text-white' :
              step.active ? 'border-emerald text-emerald shadow-[0_0_0_4px_rgba(5,150,105,.12)]' :
                            'border-slate-200 text-slate-400 bg-white dark:bg-ink-3 dark:border-white/5']">
              {{ step.done ? '✓' : idx + 1 }}
            </div>
            <div class="ml-2 md:ml-3 overflow-hidden">
              <div :class="['text-[10px] md:text-xs font-bold truncate', step.done || step.active ? 'text-ink dark:text-white' : 'text-slate-400']">
                {{ step.label }}
              </div>
              <div :class="['text-[9px] md:text-[10px] mt-0.5 truncate hidden sm:block', step.active ? 'text-emerald font-semibold' : 'text-slate-400']">
                {{ step.sub }}
              </div>
            </div>
          </div>
          <div v-if="idx < steps.length - 1" :class="['flex-1 h-[2px] mx-2 md:mx-3 max-w-[40px] md:max-w-[80px]', step.done ? 'bg-emerald' : 'bg-slate-100 dark:bg-white/5']"></div>
        </template>
      </div>

      <div class="flex flex-col lg:grid lg:grid-cols-[1fr_320px] gap-6 items-start">

        <!-- Left: Doctor + Calendar + Slots + Patient info -->
        <div class="space-y-6 w-full">
          <!-- Doctor info -->
          <div class="bg-white dark:bg-ink-2 border border-slate-200 dark:border-white/10 rounded-[14px] shadow-sm p-5 md:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 pb-5 mb-5 border-b border-slate-100 dark:border-white/5">
              <div class="w-14 h-14 rounded-full bg-emerald-4 dark:bg-emerald/20 text-[#065F46] dark:text-emerald flex items-center justify-center text-lg font-bold border-2 border-slate-100 dark:border-white/10 shadow-sm">
                {{ initials }}
              </div>
              <div>
                <div class="text-[17px] font-bold text-ink dark:text-white">{{ doctor.user.name }}</div>
                <div class="text-[14px] text-slate-500 dark:text-white/50 mt-0.5">{{ doctor.specialty }} · {{ doctor.city }}</div>
                <div class="flex items-center gap-2 mt-2">
                  <span class="inline-flex items-center gap-1 text-[11px] font-bold bg-emerald-3 dark:bg-emerald/30 text-[#065F46] dark:text-emerald px-2.5 py-1 rounded-full">✓ Verified</span>
                  <span class="text-[13px] text-amber-500 font-bold">★ {{ doctor.rating.toFixed(1) }}</span>
                </div>
              </div>
            </div>

            <!-- Calendar -->
            <div class="bg-slate-50 dark:bg-white/5 p-4 rounded-xl border border-slate-100 dark:border-white/5">
              <CalendarPicker
                v-model="selectedDate"
                :dates-with-slots="datesWithSlots"
                @month-change="onMonthChange"
              />
            </div>

            <!-- Time slots -->
            <div v-if="selectedDate" class="mt-6">
              <div class="text-xs font-bold text-slate-500 dark:text-white/40 mb-4 uppercase tracking-widest">
                Available slots — {{ formatDate(selectedDate) }}
              </div>
              <div v-if="loadingSlots" class="text-center py-8 text-sm text-slate-400">Loading slots…</div>
              <div v-else-if="slots.length === 0" class="text-center py-8 text-sm text-slate-400 bg-slate-50 dark:bg-white/5 rounded-xl border border-dashed border-slate-200 dark:border-white/10">
                No available slots on this date.
              </div>
              <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-3 gap-2.5">
                <button
                  v-for="slot in slots"
                  :key="slot.id"
                  type="button"
                  :disabled="slot.is_booked"
                  @click="selectSlot(slot)"
                  :class="['py-3 rounded-xl border text-sm font-bold transition-all',
                    slot.is_booked
                      ? 'opacity-40 cursor-not-allowed border-slate-100 dark:border-white/5 text-slate-400 line-through'
                      : (selectedSlot && selectedSlot.id === slot.id)
                        ? 'bg-emerald border-emerald text-white shadow-lg shadow-emerald/20'
                        : 'bg-white dark:bg-ink-3 border-slate-200 dark:border-white/10 text-ink dark:text-white hover:border-emerald hover:text-emerald dark:hover:text-emerald dark:hover:bg-emerald/10']"
                >
                  {{ slot.start_time.slice(0,5) }}
                </button>
              </div>
            </div>
            <div v-else class="mt-6 py-10 text-center text-sm text-slate-400 border border-dashed border-slate-200 dark:border-white/10 rounded-xl">
              <div class="text-2xl mb-2">📅</div>
              Select a date to see available time slots
            </div>
          </div>

          <!-- Patient details form -->
          <div class="bg-white dark:bg-ink-2 border border-slate-200 dark:border-white/10 rounded-[14px] shadow-sm p-5 md:p-6">
            <div class="text-[15px] font-bold text-ink dark:text-white mb-5 flex items-center gap-2">
              <span class="w-1 h-4 bg-emerald rounded-full"></span>
              Patient information
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
              <div class="form-group">
                <label class="form-label">Full name</label>
                <input v-model="form.name" class="form-input dark:bg-ink-3 dark:border-white/5 dark:text-white" type="text" placeholder="Your full name">
              </div>
              <div class="form-group">
                <label class="form-label">Phone number</label>
                <input v-model="form.phone" class="form-input dark:bg-ink-3 dark:border-white/5 dark:text-white" type="tel" placeholder="+212 6xx xxx xxx">
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Reason for visit</label>
              <input v-model="form.reason" class="form-input dark:bg-ink-3 dark:border-white/5 dark:text-white" placeholder="e.g. Routine checkup, chest pain follow-up…">
            </div>

            <div class="form-group">
              <label class="form-label">Insurance type</label>
              <select v-model="form.insurance_type" class="form-input dark:bg-ink-3 dark:border-white/5 dark:text-white appearance-none">
                <option value="cnss">CNSS (Public)</option>
                <option value="ramed">RAMED</option>
                <option value="private">Private insurance</option>
                <option value="none">None / Self-pay</option>
              </select>
            </div>

            <!-- Reviews Section -->
            <div class="bg-white dark:bg-ink-2 border border-slate-200 dark:border-white/10 rounded-[14px] shadow-sm p-5 md:p-6 mt-6">
              <div class="text-[15px] font-bold text-ink dark:text-white mb-5 flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <span class="w-1 h-4 bg-amber-400 rounded-full"></span>
                  Patient Reviews
                </div>
                <div v-if="doctor.rating_count > 0" class="text-xs text-slate-400 font-normal">
                  Based on {{ doctor.rating_count }} consultations
                </div>
              </div>

              <div v-if="!doctor.appointments || doctor.appointments.length === 0" class="py-8 text-center text-sm text-slate-400 border border-dashed border-slate-200 dark:border-white/10 rounded-xl">
                No reviews yet. Be the first to leave one after your visit!
              </div>

              <div v-else class="space-y-4">
                <div 
                  v-for="review in doctor.appointments" 
                  :key="review.id"
                  class="p-4 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-100 dark:border-white/5"
                >
                  <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center gap-2">
                      <div class="w-7 h-7 rounded-full bg-slate-200 dark:bg-white/10 flex items-center justify-center text-[10px] font-bold">
                        {{ review.patient.name.charAt(0) }}
                      </div>
                      <div>
                        <div class="text-xs font-bold text-ink dark:text-white">{{ review.patient.name }}</div>
                        <div class="text-[9px] text-slate-400 uppercase tracking-tighter">Verified Patient</div>
                      </div>
                    </div>
                    <div class="flex text-amber-400 text-[10px]">
                      <span v-for="i in 5" :key="i" :class="i <= review.patient_rating ? 'opacity-100' : 'opacity-20'">★</span>
                    </div>
                  </div>
                  <p class="text-[13px] text-slate-600 dark:text-white/70 italic leading-relaxed">
                    "{{ review.patient_review }}"
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Summary + CTA -->
        <div class="lg:sticky lg:top-24 w-full">
          <div class="bg-white dark:bg-ink-2 border border-slate-200 dark:border-white/10 rounded-[14px] shadow-lg p-5 md:p-6">
            <div class="text-[15px] font-bold text-ink dark:text-white mb-5 flex items-center gap-2">
              <span class="w-1 h-4 bg-emerald rounded-full"></span>
              Appointment summary
            </div>

            <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-5 mb-6 space-y-4">
              <SummaryRow label="Doctor"    :value="doctor.user.name" />
              <SummaryRow label="Specialty" :value="doctor.specialty" />
              <SummaryRow label="Date"      :value="selectedDate ? formatDate(selectedDate) : '—'" highlighted />
              <SummaryRow label="Time"      :value="selectedSlot ? selectedSlot.start_time.slice(0,5) : '—'" highlighted />
              <SummaryRow label="Duration"  :value="`${doctor.slot_duration} minutes`" />

              <div class="flex justify-between items-center pt-4 mt-2 border-t border-slate-200 dark:border-white/10">
                <span class="text-[14px] font-bold text-slate-500 dark:text-white/40">Total fee</span>
                <span class="text-[22px] font-extrabold text-ink dark:text-white tracking-tight">{{ doctor.consultation_fee }} MAD</span>
              </div>
            </div>

            <!-- CTA button -->
            <button
              @click="confirmBooking"
              :disabled="!canBook || submitting"
              :class="['w-full py-4 rounded-xl text-[15px] font-bold transition-all duration-300',
                canBook && !submitting
                  ? 'bg-emerald text-white shadow-lg shadow-emerald/20 hover:bg-emerald-2 hover:shadow-emerald/30 hover:-translate-y-px active:translate-y-0'
                  : 'bg-slate-100 dark:bg-white/5 text-slate-400 dark:text-white/20 cursor-not-allowed']"
            >
              {{ submitting ? 'Booking…' : 'Confirm appointment →' }}
            </button>

            <div class="text-center text-[11px] text-slate-400 mt-4 flex items-center justify-center gap-2">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
              SMS confirmation sent to your phone
            </div>

            <!-- Security badge -->
            <div class="mt-6 p-4 bg-emerald-4/30 dark:bg-emerald/10 border border-emerald-3/30 dark:border-emerald/20 rounded-xl">
              <div class="text-[11px] font-bold text-[#065F46] dark:text-emerald mb-1 uppercase tracking-widest flex items-center gap-1.5">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                Secure booking
              </div>
              <div class="text-[11px] text-[#047857] dark:text-emerald/60 leading-relaxed">
                Your data is protected and only shared with your selected doctor.
              </div>
            </div>

            <!-- Warning if not all fields filled -->
            <div
              v-if="!canBook"
              class="mt-4 text-[11px] text-red-500/70 font-medium text-center"
            >
              {{ bookingBlocker }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, reactive, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import AppLayout from '@/components/layout/AppLayout.vue'
import CalendarPicker from '@/components/booking/CalendarPicker.vue'
import SummaryRow from '@/components/booking/SummaryRow.vue'
import type { Doctor, Slot } from '@/types'

const props = defineProps<{
  doctor: Doctor
  datesWithSlots: string[]
  month: string
}>()

const selectedDate = ref('')
const selectedSlot = ref<Slot | null>(null)
const slots        = ref<Slot[]>([])
const loadingSlots = ref(false)
const submitting   = ref(false)

const form = reactive({
  name:           '',
  phone:          '',
  reason:         '',
  insurance_type: 'cnss',
})

const initials = computed(() => {
  return props.doctor.user.name
    .replace('Dr. ', '')
    .split(' ')
    .map(w => w[0])
    .join('')
    .slice(0, 2)
    .toUpperCase()
})

const steps = computed(() => [
  { label: 'Select doctor', sub: props.doctor.user.name,                    done: true,  active: false },
  { label: 'Choose date',   sub: selectedDate.value || 'Pick from calendar', done: !!selectedSlot.value, active: !selectedSlot.value && !!selectedDate.value },
  { label: 'Your details',  sub: 'Fill in info',                             done: false, active: !!selectedSlot.value },
  { label: 'Confirm',       sub: 'Review & pay',                             done: false, active: false },
])

const canBook = computed(() =>
  !!selectedDate.value &&
  !!selectedSlot.value &&
  form.name.trim().length > 2 &&
  form.phone.trim().length > 5
)

const bookingBlocker = computed(() => {
  if (!selectedDate.value) return 'Please select a date'
  if (!selectedSlot.value) return 'Please select a time slot'
  if (!form.name.trim())   return 'Please enter your name'
  if (!form.phone.trim())  return 'Please enter your phone number'
  return ''
})

watch(selectedDate, async (date) => {
  selectedSlot.value = null
  if (!date) return
  loadingSlots.value = true
  try {
    const res = await axios.get(`/doctors/${props.doctor.id}/slots`, { params: { date } })
    slots.value = res.data
  } finally {
    loadingSlots.value = false
  }
})

function selectSlot(slot: Slot) {
  selectedSlot.value = slot
}

function onMonthChange(month: string) {
  router.get(
    `/doctors/${props.doctor.id}`,
    { month },
    { preserveState: true, replace: true, only: ['datesWithSlots', 'month'] }
  )
}

function formatDate(d: string) {
  return new Date(d).toLocaleString('en', { month: 'long', day: 'numeric', year: 'numeric' })
}

function confirmBooking() {
  if (!canBook.value || !selectedSlot.value) return
  submitting.value = true
  router.post('/appointments', {
    doctor_id:      props.doctor.id,
    slot_id:        selectedSlot.value.id,
    reason:         form.reason,
    insurance_type: form.insurance_type,
  }, {
    onSuccess: () => {
      // The controller will redirect, but we can clear local state
      submitting.value = false
    },
    onError: (errors) => {
      submitting.value = false
      // Most errors will be handled by the form validation in Laravel
      // and displayed via flash or individual field errors if we had them.
    },
    onFinish: () => { 
      submitting.value = false 
    },
  })
}
</script>

<style scoped>
.form-group { @apply mb-4; }
.form-label { @apply block text-xs font-semibold text-slate-500 mb-1.5; }
.form-input { @apply w-full border border-slate-200 rounded-[10px] px-3 py-2.5 text-sm text-ink outline-none focus:border-emerald focus:ring-2 focus:ring-emerald/10 transition-all; }
</style>

<template>
  <AppLayout>
    <div class="max-w-6xl mx-auto px-6 py-8">

      <!-- Stepper -->
      <div class="bg-white border border-slate-200 rounded-[14px] shadow-sm px-7 py-5 flex items-center gap-0 mb-7">
        <template v-for="(step, idx) in steps" :key="step.label">
          <div class="flex items-center flex-1">
            <div :class="['w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2 flex-shrink-0 transition-all',
              step.done   ? 'bg-emerald border-emerald text-white' :
              step.active ? 'border-emerald text-emerald shadow-[0_0_0_4px_rgba(5,150,105,.12)]' :
                            'border-slate-200 text-slate-400 bg-white']">
              {{ step.done ? '✓' : idx + 1 }}
            </div>
            <div class="ml-3">
              <div :class="['text-xs font-semibold', step.done || step.active ? 'text-ink' : 'text-slate-400']">
                {{ step.label }}
              </div>
              <div :class="['text-[10px] mt-0.5', step.active ? 'text-emerald font-semibold' : 'text-slate-400']">
                {{ step.sub }}
              </div>
            </div>
          </div>
          <div v-if="idx < steps.length - 1" :class="['flex-1 h-[2px] mx-3 max-w-[80px]', step.done ? 'bg-emerald' : 'bg-slate-200']"></div>
        </template>
      </div>

      <div class="grid grid-cols-[1fr_300px] gap-6 items-start">

        <!-- Left: Doctor + Calendar + Slots + Patient info -->
        <div class="space-y-5">
          <!-- Doctor info -->
          <div class="bg-white border border-slate-200 rounded-[14px] shadow-sm p-5">
            <div class="flex items-center gap-4 pb-4 mb-4 border-b border-slate-100">
              <div class="w-12 h-12 rounded-full bg-emerald-4 text-[#065F46] flex items-center justify-center text-base font-bold border-2 border-slate-100">
                SA
              </div>
              <div>
                <div class="text-[15px] font-bold text-ink">{{ doctor.user.name }}</div>
                <div class="text-sm text-slate-400 mt-0.5">{{ doctor.specialty }} · {{ doctor.city }}</div>
                <div class="flex items-center gap-2 mt-1.5">
                  <span class="inline-flex items-center gap-1 text-[11px] font-bold bg-emerald-3 text-[#065F46] px-2.5 py-1 rounded-full">✓ Verified</span>
                  <span class="text-xs text-amber-500 font-bold">★ {{ doctor.rating.toFixed(1) }}</span>
                </div>
              </div>
            </div>

            <!-- Calendar -->
            <CalendarPicker
              v-model="selectedDate"
              :dates-with-slots="datesWithSlots"
              @month-change="onMonthChange"
            />

            <!-- Time slots -->
            <div v-if="selectedDate" class="mt-5">
              <div class="text-xs font-bold text-slate-500 mb-3 uppercase tracking-wider">
                Available slots — {{ formatDate(selectedDate) }}
              </div>
              <div v-if="loadingSlots" class="text-center py-6 text-sm text-slate-400">Loading slots…</div>
              <div v-else-if="slots.length === 0" class="text-center py-6 text-sm text-slate-400">
                No available slots on this date.
              </div>
              <div v-else class="grid grid-cols-3 gap-2">
                <button
                  v-for="slot in slots"
                  :key="slot.id"
                  :disabled="slot.is_booked"
                  @click="selectSlot(slot)"
                  :class="['py-2.5 rounded-[10px] border text-sm font-semibold transition-all',
                    slot.is_booked
                      ? 'opacity-40 cursor-not-allowed border-slate-100 text-slate-400 line-through'
                      : selectedSlot?.id === slot.id
                        ? 'bg-emerald border-emerald text-white shadow-sm'
                        : 'border-slate-200 text-ink hover:border-emerald hover:text-emerald hover:bg-emerald-4']"
                >
                  {{ slot.start_time.slice(0,5) }}
                </button>
              </div>
            </div>
            <div v-else class="mt-5 py-6 text-center text-sm text-slate-400 border border-dashed border-slate-200 rounded-[10px]">
              Select a date to see available time slots
            </div>
          </div>

          <!-- Patient details form -->
          <div class="bg-white border border-slate-200 rounded-[14px] shadow-sm p-5">
            <div class="text-[13px] font-bold text-ink mb-4">Patient information</div>
            <div class="grid grid-cols-2 gap-4">
              <div class="form-group">
                <label class="form-label">Full name</label>
                <input v-model="form.name" class="form-input" type="text" placeholder="Your full name">
              </div>
              <div class="form-group">
                <label class="form-label">Phone number</label>
                <input v-model="form.phone" class="form-input" type="tel" placeholder="+212 6xx xxx xxx">
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Reason for visit</label>
              <input v-model="form.reason" class="form-input" placeholder="e.g. Routine checkup, chest pain follow-up…">
            </div>
            <div class="form-group">
              <label class="form-label">Insurance type</label>
              <select v-model="form.insurance_type" class="form-input">
                <option value="cnss">CNSS (Public)</option>
                <option value="ramed">RAMED</option>
                <option value="private">Private insurance</option>
                <option value="none">None / Self-pay</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Right: Summary + CTA -->
        <div class="sticky top-24">
          <div class="bg-white border border-slate-200 rounded-[14px] shadow-sm p-5">
            <div class="text-[13px] font-bold text-ink mb-4">Appointment summary</div>

            <div class="bg-slate-50 rounded-[10px] p-4 mb-4 space-y-0">
              <SummaryRow label="Doctor"    :value="doctor.user.name" />
              <SummaryRow label="Specialty" :value="doctor.specialty" />
              <SummaryRow label="Date"      :value="selectedDate ? formatDate(selectedDate) : '—'" highlighted />
              <SummaryRow label="Time"      :value="selectedSlot ? selectedSlot.start_time.slice(0,5) : '—'" highlighted />
              <SummaryRow label="Duration"  :value="`${doctor.slot_duration} minutes`" />

              <div class="flex justify-between items-center pt-3.5 mt-0.5 border-t border-slate-200">
                <span class="text-[14px] font-bold text-ink">Total fee</span>
                <span class="text-[20px] font-extrabold text-ink tracking-tight">{{ doctor.consultation_fee }} MAD</span>
              </div>
            </div>

            <!-- CTA button -->
            <button
              @click="confirmBooking"
              :disabled="!canBook || submitting"
              :class="['w-full py-3.5 rounded-[10px] text-[15px] font-bold transition-all',
                canBook && !submitting
                  ? 'bg-emerald text-white shadow-[0_2px_8px_rgba(5,150,105,.3)] hover:bg-emerald-2 hover:shadow-[0_4px_14px_rgba(5,150,105,.4)] hover:-translate-y-px'
                  : 'bg-slate-100 text-slate-400 cursor-not-allowed']"
            >
              {{ submitting ? 'Booking…' : 'Confirm appointment →' }}
            </button>

            <div class="text-center text-[11px] text-slate-400 mt-2.5">
              📱 SMS confirmation sent to your phone
            </div>

            <!-- Security badge -->
            <div class="mt-4 p-3.5 bg-emerald-4 border border-emerald-3 rounded-[10px]">
              <div class="text-[11px] font-bold text-[#065F46] mb-1">🔒 Secure booking</div>
              <div class="text-[11px] text-[#047857] leading-relaxed">
                Your data is protected and only shared with your selected doctor.
              </div>
            </div>

            <!-- Warning if not all fields filled -->
            <div
              v-if="!canBook"
              class="mt-3 text-[11px] text-slate-400 text-center"
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
    onFinish: () => { submitting.value = false },
  })
}
</script>

<style scoped>
.form-group { @apply mb-4; }
.form-label { @apply block text-xs font-semibold text-slate-500 mb-1.5; }
.form-input { @apply w-full border border-slate-200 rounded-[10px] px-3 py-2.5 text-sm text-ink outline-none focus:border-emerald focus:ring-2 focus:ring-emerald/10 transition-all; }
</style>

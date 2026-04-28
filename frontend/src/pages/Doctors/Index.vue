<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-6 py-8">

      <!-- Search bar -->
      <div class="bg-white border border-slate-200 rounded-[14px] shadow-sm px-4 py-3 flex items-center gap-3 mb-5">
        <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 16 16">
          <path d="M7 13A6 6 0 107 1a6 6 0 000 12zM13 13l2 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        <input
          v-model="search"
          @input="debouncedSearch"
          class="flex-1 text-sm text-ink outline-none placeholder:text-slate-400"
          placeholder="Search by name, specialty, condition..."
        />
        <div class="h-5 w-px bg-slate-200"></div>
        <select v-model="filters.specialty" @change="applyFilters" class="text-sm text-slate-500 outline-none bg-transparent cursor-pointer">
          <option value="">All specialties</option>
          <option v-for="s in specialties" :key="s">{{ s }}</option>
        </select>
        <div class="h-5 w-px bg-slate-200"></div>
        <select v-model="filters.city" @change="applyFilters" class="text-sm text-slate-500 outline-none bg-transparent cursor-pointer">
          <option value="">Any location</option>
          <option v-for="c in cities" :key="c">{{ c }}</option>
        </select>
        <button @click="applyFilters" class="bg-emerald text-white text-sm font-semibold px-4 py-2 rounded-[8px] hover:bg-emerald-2 transition-colors flex-shrink-0">
          Search
        </button>
      </div>

      <!-- Filter pills -->
      <div class="flex gap-2 flex-wrap mb-5">
        <button
          v-for="pill in filterPills"
          :key="pill.key"
          @click="togglePill(pill.key)"
          :class="['text-sm font-semibold px-4 py-2 rounded-full border transition-all duration-150',
            activePills.includes(pill.key)
              ? 'bg-ink border-ink text-white'
              : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300']"
        >
          {{ pill.label }}
        </button>
      </div>

      <!-- Results count -->
      <div class="text-xs text-slate-400 mb-4">
        Showing <strong class="text-ink">{{ doctors.total }}</strong> verified doctors in Morocco
      </div>

      <!-- Doctor grid -->
      <div class="grid grid-cols-3 gap-4 mb-8">
        <DoctorCard
          v-for="doctor in doctors.data"
          :key="doctor.id"
          :doctor="doctor"
          @book="goToBook"
        />
      </div>

      <!-- Pagination -->
      <div v-if="doctors.last_page > 1" class="flex justify-center gap-2">
        <button
          v-for="page in doctors.last_page"
          :key="page"
          @click="goToPage(page)"
          :class="['w-9 h-9 rounded-lg text-sm font-semibold transition-colors',
            page === doctors.current_page
              ? 'bg-emerald text-white'
              : 'bg-white border border-slate-200 text-slate-600 hover:border-slate-300']"
        >
          {{ page }}
        </button>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/components/layout/AppLayout.vue'
import DoctorCard from '@/components/doctor/DoctorCard.vue'
import type { Doctor, DoctorsIndexProps } from '@/types'

const props = defineProps<DoctorsIndexProps>()

const search = ref(props.filters.search ?? '')
const filters = reactive({
  specialty: props.filters.specialty ?? '',
  city:      props.filters.city      ?? '',
  insurance: props.filters.insurance ?? '',
})
const activePills = ref<string[]>([])

const filterPills = [
  { key: 'available_today', label: 'Available today' },
  { key: 'top_rated',       label: 'Top rated'       },
  { key: 'cnss',            label: 'Accepts CNSS'    },
  { key: 'ramed',           label: 'Accepts RAMED'   },
]

let searchTimer: ReturnType<typeof setTimeout>

function debouncedSearch() {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(applyFilters, 400)
}

function togglePill(key: string) {
  const idx = activePills.value.indexOf(key)
  if (idx > -1) activePills.value.splice(idx, 1)
  else activePills.value.push(key)
  applyFilters()
}

function applyFilters() {
  const query: Record<string, string> = {}
  if (search.value)        query.search    = search.value
  if (filters.specialty)   query.specialty = filters.specialty
  if (filters.city)        query.city      = filters.city
  if (activePills.value.includes('cnss'))            query.insurance       = 'cnss'
  if (activePills.value.includes('ramed'))           query.insurance       = 'ramed'
  if (activePills.value.includes('available_today')) query.available_today = '1'
  if (activePills.value.includes('top_rated'))       query.top_rated       = '1'

  router.get('/doctors', query, { preserveState: true, replace: true })
}

function goToBook(doctor: Doctor) {
  router.visit(`/doctors/${doctor.id}`)
}

function goToPage(page: number) {
  router.get('/doctors', { ...filters, search: search.value, page }, { preserveState: true })
}
</script>

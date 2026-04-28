<template>
  <AppLayout>
    <div class="py-2">

      <!-- Search bar -->
      <div class="bg-white dark:bg-ink-2 border border-slate-200 dark:border-white/10 rounded-[14px] shadow-sm p-4 mb-5">
        <div class="flex flex-col lg:flex-row gap-4 items-stretch lg:items-center">
          <!-- Search Input -->
          <div class="flex-1 flex items-center gap-3 bg-slate-50 dark:bg-white/5 px-4 py-2.5 rounded-xl border border-transparent focus-within:border-emerald/30 focus-within:bg-white dark:focus-within:bg-ink-3 transition-all">
            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 16 16">
              <path d="M7 13A6 6 0 107 1a6 6 0 000 12zM13 13l2 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <input
              v-model="search"
              @input="debouncedSearch"
              class="flex-1 text-sm text-ink dark:text-white bg-transparent outline-none placeholder:text-slate-400"
              placeholder="Search by name, specialty, condition..."
            />
          </div>

          <!-- Specialty Select -->
          <div class="lg:w-64">
            <SpecialtySelect
              v-model="filters.specialty"
              :specialties="specialties"
              @change="applyFilters"
              placeholder="All specialties"
              allow-clear
            />
          </div>

          <!-- City Select -->
          <div class="lg:w-64">
            <LocationSelect
              v-model="filters.city"
              :items="cities"
              @change="applyFilters"
              placeholder="Any location"
              allow-clear
            />
          </div>

          <!-- Search Button -->
          <button @click="applyFilters" class="bg-emerald text-white text-sm font-bold px-6 py-2.5 rounded-xl hover:bg-emerald-2 transition-all shadow-md shadow-emerald/10 flex-shrink-0">
            Search
          </button>
        </div>
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

      <!-- Doctor list -->
      <div v-if="doctors.data.length > 0" class="flex flex-col gap-4 mb-8">
        <DoctorCard
          v-for="doctor in doctors.data"
          :key="doctor.id"
          :doctor="doctor"
          @book="goToBook"
        />
      </div>
      
      <!-- Empty state -->
      <div v-else class="flex flex-col items-center justify-center py-20 px-4 text-center bg-white dark:bg-ink-2 border border-slate-200 dark:border-white/10 rounded-[14px] shadow-sm mb-8">
        <div class="w-16 h-16 bg-slate-50 dark:bg-white/5 rounded-full flex items-center justify-center mb-4">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="text-slate-400 dark:text-white/30" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            <line x1="11" y1="8" x2="11" y2="14"></line>
            <line x1="8" y1="11" x2="14" y2="11"></line>
          </svg>
        </div>
        <h3 class="text-lg font-bold text-ink dark:text-white mb-2">No doctors found</h3>
        <p class="text-slate-500 dark:text-white/50 max-w-md mx-auto">
          We couldn't find any verified doctors matching your current filters. Try adjusting your search criteria or removing some filters.
        </p>
        <button @click="clearFilters" class="mt-6 text-sm font-bold text-emerald hover:text-emerald-2 transition-colors">
          Clear all filters
        </button>
      </div>

      <!-- Pagination: Premium Design -->
      <div v-if="doctors.last_page > 1" class="flex items-center justify-center gap-3 mt-12 pb-10">
        <!-- Prev -->
        <button
          @click="doctors.current_page > 1 && goToPage(doctors.current_page - 1)"
          :disabled="doctors.current_page === 1"
          class="w-10 h-10 rounded-xl bg-white dark:bg-ink-2 border border-slate-200 dark:border-white/10 flex items-center justify-center text-slate-400 hover:text-emerald hover:border-emerald transition-all disabled:opacity-30 disabled:hover:border-slate-200"
        >
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg>
        </button>

        <!-- Numbers -->
        <div class="flex items-center gap-2">
          <button
            v-for="page in doctors.last_page"
            :key="page"
            @click="goToPage(page)"
            :class="['w-10 h-10 rounded-xl text-[14px] font-black transition-all border-2',
              page === doctors.current_page
                ? 'bg-emerald border-emerald text-white shadow-lg shadow-emerald/20 scale-110'
                : 'bg-white dark:bg-ink-2 border-slate-50 dark:border-white/5 text-slate-400 hover:text-ink dark:hover:text-white hover:border-slate-200 dark:hover:border-white/10']"
          >
            {{ page }}
          </button>
        </div>

        <!-- Next -->
        <button
          @click="doctors.current_page < doctors.last_page && goToPage(doctors.current_page + 1)"
          :disabled="doctors.current_page === doctors.last_page"
          class="w-10 h-10 rounded-xl bg-white dark:bg-ink-2 border border-slate-200 dark:border-white/10 flex items-center justify-center text-slate-400 hover:text-emerald hover:border-emerald transition-all disabled:opacity-30 disabled:hover:border-slate-200"
        >
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
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
import SpecialtySelect from '@/components/ui/SpecialtySelect.vue'
import LocationSelect from '@/components/ui/LocationSelect.vue'
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

function clearFilters() {
  search.value = ''
  filters.specialty = ''
  filters.city = ''
  filters.insurance = ''
  activePills.value = []
  applyFilters()
}

function goToBook(doctor: Doctor) {
  router.visit(`/doctors/${doctor.id}`)
}

function goToPage(page: number) {
  router.get('/doctors', { ...filters, search: search.value, page }, { preserveState: true })
}
</script>

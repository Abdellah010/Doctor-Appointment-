<template>
  <div class="select-none">
    <!-- Month header -->
    <div class="flex items-center justify-between mb-3.5">
      <span class="text-sm font-bold text-ink dark:text-white">{{ monthLabel }}</span>
      <div class="flex gap-1">
        <button @click="prevMonth" class="cal-nav-btn">‹</button>
        <button @click="nextMonth" class="cal-nav-btn">›</button>
      </div>
    </div>

    <!-- Day headers -->
    <div class="grid grid-cols-7 gap-1 mb-1">
      <div
        v-for="d in ['Su','Mo','Tu','We','Th','Fr','Sa']"
        :key="d"
        class="text-center text-[10px] font-bold text-slate-400 uppercase tracking-wide py-1"
      >{{ d }}</div>
    </div>

    <!-- Days grid -->
    <div class="grid grid-cols-7 gap-1">
      <div
        v-for="cell in calendarCells"
        :key="cell.key"
        @click="cell.selectable && selectDay(cell.date)"
        :class="[
          'text-center text-[12px] md:text-[13px] py-1.5 md:py-2 rounded-lg font-medium transition-all duration-100 cursor-pointer',
          cell.dim        ? 'text-slate-300 cursor-default'            : '',
          cell.selectable && cell.hasSlots ? 'text-emerald bg-emerald-4 dark:bg-emerald/20 font-bold' : '',
          cell.selectable && !cell.hasSlots && !cell.dim ? 'text-slate-600 dark:text-white/60 hover:bg-slate-100 dark:hover:bg-white/5' : '',
          cell.selected   ? '!bg-emerald !text-white shadow-sm'        : '',
          !cell.selectable && !cell.dim ? 'text-slate-300 dark:text-white/10 cursor-not-allowed' : '',
        ]"
      >
        {{ cell.day }}
      </div>
    </div>

    <!-- Legend -->
    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mt-4 pt-4 border-t border-slate-100 dark:border-white/5">
      <div class="flex items-center gap-1.5 text-[10px] md:text-xs text-slate-400">
        <div class="w-2 h-2 rounded-full bg-emerald"></div>
        Available
      </div>
      <div class="flex items-center gap-1.5 text-[10px] md:text-xs text-slate-400">
        <div class="w-2 h-2 rounded-full bg-slate-200 dark:bg-white/10"></div>
        No slots
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

interface CalendarCell {
  key: string
  day: number | ''
  date: string
  dim: boolean
  selected: boolean
  hasSlots: boolean
  selectable: boolean
}

const props = defineProps<{
  modelValue: string  // YYYY-MM-DD
  datesWithSlots: string[]
}>()

const emit = defineEmits<{
  'update:modelValue': [date: string]
  'monthChange': [month: string]  // YYYY-MM
}>()

const today = new Date()
const viewYear  = ref(today.getFullYear())
const viewMonth = ref(today.getMonth())

const monthLabel = computed(() => {
  return new Date(viewYear.value, viewMonth.value).toLocaleString('en', {
    month: 'long', year: 'numeric'
  })
})

const calendarCells = computed((): CalendarCell[] => {
  const year  = viewYear.value
  const month = viewMonth.value

  const firstDay = new Date(year, month, 1).getDay()
  const lastDate = new Date(year, month + 1, 0).getDate()
  const cells: CalendarCell[] = []

  // Leading empty cells
  for (let i = 0; i < firstDay; i++) {
    const prevDate = new Date(year, month, -firstDay + i + 1)
    cells.push({
      key: `prev-${i}`,
      day: prevDate.getDate(),
      date: '',
      dim: true,
      selected: false,
      hasSlots: false,
      selectable: false,
    })
  }

  for (let d = 1; d <= lastDate; d++) {
    const dateStr = `${year}-${String(month + 1).padStart(2,'0')}-${String(d).padStart(2,'0')}`
    const dateObj = new Date(dateStr)
    const todayObj = new Date(today.toDateString())
    const isPast  = dateObj < todayObj

    cells.push({
      key: dateStr,
      day: d,
      date: dateStr,
      dim: false,
      selected: props.modelValue === dateStr,
      hasSlots: props.datesWithSlots.includes(dateStr),
      selectable: !isPast,
    })
  }

  return cells
})

function selectDay(date: string) {
  if (!date) return
  emit('update:modelValue', date)
}

function prevMonth() {
  if (viewMonth.value === 0) { viewMonth.value = 11; viewYear.value-- }
  else viewMonth.value--
  emitMonthChange()
}

function nextMonth() {
  if (viewMonth.value === 11) { viewMonth.value = 0; viewYear.value++ }
  else viewMonth.value++
  emitMonthChange()
}

function emitMonthChange() {
  emit('monthChange', `${viewYear.value}-${String(viewMonth.value + 1).padStart(2,'0')}`)
}
</script>

<style scoped>
.cal-nav-btn {
  @apply w-7 h-7 flex items-center justify-center rounded-md border border-slate-200 dark:border-white/10 text-slate-400 dark:text-white/40 hover:border-emerald hover:text-emerald dark:hover:text-emerald transition-colors text-sm;
}
</style>

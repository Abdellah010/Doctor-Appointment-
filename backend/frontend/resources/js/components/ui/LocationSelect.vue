<template>
  <div class="relative" ref="containerRef">
    <button
      type="button"
      @click="isOpen = !isOpen"
      class="w-full flex items-center justify-between gap-3 text-sm text-ink dark:text-white bg-slate-50 dark:bg-white/5 border-2 border-transparent hover:border-slate-200 dark:hover:border-white/10 px-4 py-2.5 rounded-xl outline-none transition-all"
      :class="{ 'ring-2 ring-emerald/20 border-emerald/30': isOpen }"
    >
      <div class="flex items-center gap-2.5 truncate">
        <span class="truncate font-semibold" :class="{ 'text-slate-400': !selectedItem }">
          {{ selectedItem || placeholder || 'Select location' }}
        </span>
      </div>
      <svg width="10" height="10" viewBox="0 0 10 10" fill="none" class="transition-transform duration-200 flex-shrink-0 opacity-40" :class="{ 'rotate-180': isOpen }">
        <path d="M2 3L5 6L8 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>

    <Transition name="pop">
      <div v-if="isOpen" class="absolute z-[100] mt-2 w-full min-w-[240px] bg-white dark:bg-ink-2 border border-slate-200 dark:border-white/10 rounded-2xl shadow-2xl overflow-hidden py-2 animate-in fade-in zoom-in-95 duration-200">
        <!-- Search within select -->
        <div class="px-3 pb-2 mb-2 border-b border-slate-50 dark:border-white/5">
          <div class="flex items-center gap-2 bg-slate-50 dark:bg-white/5 px-3 py-1.5 rounded-lg border border-transparent focus-within:border-emerald/30 transition-all">
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input 
              v-model="searchTerm" 
              class="bg-transparent border-none outline-none text-xs w-full text-ink dark:text-white placeholder:text-slate-400" 
              placeholder="Search city..."
              @click.stop
            />
          </div>
        </div>

        <div class="max-h-[320px] overflow-y-auto custom-scrollbar">
          <button
            v-if="allowClear"
            @click="select('')"
            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-slate-500 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors"
          >
            <span class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-white/5 flex items-center justify-center text-[10px]">✕</span>
            <span class="font-bold uppercase tracking-widest text-[10px]">Any location</span>
          </button>

          <button
            v-for="c in filteredItems"
            :key="c"
            @click="select(c)"
            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-ink dark:text-white hover:bg-slate-50 dark:hover:bg-white/5 transition-all group"
            :class="{ 'bg-emerald/5 text-emerald': modelValue === c }"
          >
            <span class="font-bold tracking-tight truncate">{{ c }}</span>
          </button>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { onClickOutside } from '@vueuse/core'

const props = defineProps<{
  modelValue: string
  items: string[]
  placeholder?: string
  allowClear?: boolean
}>()

const emit = defineEmits(['update:modelValue', 'change'])

const isOpen = ref(false)
const searchTerm = ref('')
const containerRef = ref<HTMLElement>()

onClickOutside(containerRef, () => isOpen.value = false)

const selectedItem = computed(() => props.modelValue)

const filteredItems = computed(() => {
  if (!searchTerm.value) return props.items
  return props.items.filter(i => 
    i.toLowerCase().includes(searchTerm.value.toLowerCase())
  )
})

function select(c: string) {
  emit('update:modelValue', c)
  emit('change', c)
  isOpen.value = false
  searchTerm.value = ''
}
</script>

<style scoped>
.pop-enter-active, .pop-leave-active { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); transform-origin: top; }
.pop-enter-from, .pop-leave-to { opacity: 0; transform: scale(0.95) translateY(-10px); }

.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { @apply bg-transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { @apply bg-slate-200 dark:bg-white/10 rounded-full; }
</style>

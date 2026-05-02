<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink/60 backdrop-blur-sm animate-in fade-in duration-300">
    <div class="bg-white dark:bg-ink-2 w-full max-w-md rounded-[24px] shadow-2xl overflow-hidden border border-white/10 transform animate-in slide-in-from-bottom-8 duration-500">
      <div class="px-6 pt-8 pb-6 text-center">
        <!-- Icon -->
        <div class="w-16 h-16 bg-emerald-4 dark:bg-emerald/20 text-emerald rounded-full flex items-center justify-center mx-auto mb-4 text-2xl shadow-inner">
          ⭐
        </div>
        
        <h3 class="text-xl font-bold text-ink dark:text-white mb-2">How was your visit?</h3>
        <p class="text-sm text-slate-500 dark:text-white/40 mb-8 px-4">
          Your feedback helps <strong>Dr. {{ doctorName }}</strong> and other patients.
        </p>

        <!-- Star Rating -->
        <div class="flex justify-center gap-3 mb-8">
          <button 
            v-for="star in 5" 
            :key="star"
            @click="rating = star"
            @mouseenter="hoverRating = star"
            @mouseleave="hoverRating = 0"
            type="button"
            class="text-3xl transition-all transform hover:scale-125 focus:outline-none"
            :class="[star <= (hoverRating || rating) ? 'text-amber-400 drop-shadow-md' : 'text-slate-200 dark:text-white/10']"
          >
            ★
          </button>
        </div>

        <!-- Review Text -->
        <div class="text-left mb-6">
          <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Your testimonial</label>
          <textarea 
            v-model="review"
            rows="4"
            class="w-full bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10 rounded-[16px] px-4 py-3 text-sm text-ink dark:text-white placeholder:text-slate-400 focus:ring-2 focus:ring-emerald/20 focus:border-emerald outline-none transition-all resize-none"
            placeholder="Tell us about the consultation, the clinic, and the care received..."
          ></textarea>
        </div>

        <!-- Actions -->
        <div class="flex gap-3">
          <button @click="$emit('close')" class="flex-1 py-3.5 rounded-[14px] text-sm font-bold text-slate-500 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
            Maybe later
          </button>
          <button 
            @click="submit"
            :disabled="!rating || processing"
            class="flex-1 py-3.5 rounded-[14px] text-sm font-bold bg-emerald text-white shadow-lg shadow-emerald/20 hover:bg-emerald-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
          >
            {{ processing ? 'Submitting...' : 'Submit Review' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps<{
  isOpen: boolean
  appointmentId: number
  doctorName: string
}>()

const emit = defineEmits(['close', 'success'])

const rating = ref(0)
const hoverRating = ref(0)
const review = ref('')
const processing = ref(false)

function submit() {
  if (!rating.value) return
  
  processing.value = true
  router.post(`/appointments/${props.appointmentId}/complete`, {
    rating: rating.value,
    review: review.value
  }, {
    onSuccess: () => {
      processing.value = false
      emit('success')
      emit('close')
    },
    onError: () => {
      processing.value = false
    }
  })
}
</script>

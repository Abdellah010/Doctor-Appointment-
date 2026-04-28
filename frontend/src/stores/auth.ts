import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import type { User } from '@/types'

export const useAuthStore = defineStore('auth', () => {
  const page = usePage()

  const user = computed<User | null>(() => page.props.auth?.user ?? null)
  const isLoggedIn = computed(() => user.value !== null)
  const isPatient = computed(() => user.value?.role === 'patient')
  const isDoctor  = computed(() => user.value?.role === 'doctor')
  const isAdmin   = computed(() => user.value?.role === 'admin')

  return { user, isLoggedIn, isPatient, isDoctor, isAdmin }
})

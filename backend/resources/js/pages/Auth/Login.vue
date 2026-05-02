<template>
  <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2 font-sans bg-slate-50 dark:bg-ink overflow-hidden transition-colors duration-500">
    
    <!-- Left panel: Visual Hero -->
    <div class="hidden lg:flex flex-col p-16 relative overflow-hidden bg-ink">
      <!-- Photographic Background -->
      <img src="/images/moroccan_doctor_smile.png" alt="Friendly Moroccan Doctor" class="absolute inset-0 w-full h-full object-cover opacity-50 z-0 transition-transform duration-[20s] hover:scale-110" />
      
      <!-- Dark Gradient Overlay for Text Legibility -->
      <div class="absolute inset-0 bg-gradient-to-b from-ink/90 via-ink/50 to-ink z-0"></div>

      <!-- Static Color Accents (Optimized for Performance) -->
      <div class="absolute top-0 left-0 w-full h-full opacity-30 pointer-events-none z-0" style="background: radial-gradient(circle at 10% 10%, rgba(5,150,105,0.4) 0%, transparent 50%), radial-gradient(circle at 90% 90%, rgba(59,130,246,0.3) 0%, transparent 50%);">
      </div>

      <!-- Logo -->
      <div class="flex items-center gap-4 mb-24 relative z-10 group cursor-pointer" @click="router.get('/')">
        <div class="w-12 h-12 bg-emerald border border-emerald-3 rounded-[16px] flex items-center justify-center shadow-2xl shadow-emerald/40 group-hover:rotate-[10deg] transition-all duration-500">
          <svg width="24" height="24" viewBox="0 0 16 16" fill="none">
            <path d="M8 2v12M2 8h12" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="flex flex-col">
          <span class="text-[22px] text-white font-black tracking-tight leading-none uppercase">DocAppoint</span>
          <span class="text-[10px] text-emerald font-bold tracking-[0.2em] uppercase mt-1.5 opacity-80">Healthcare Redefined</span>
        </div>
      </div>

      <div class="relative z-10 mt-auto max-w-md">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald/10 border border-emerald/20 text-emerald text-[10px] font-bold uppercase tracking-widest mb-6">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald animate-ping"></span>
          Now Live in Casablanca & Rabat
        </div>
        <h2 class="text-[48px] text-white font-black leading-[1.05] mb-8 tracking-tighter">
          The future of<br/>healthcare is<br/><span class="text-emerald">precision.</span>
        </h2>
        <p class="text-[16px] text-slate-400 leading-[1.7] mb-12 font-medium">
          Morocco's most advanced platform for specialist care. Seamlessly connecting patients with the nation's top-tier medical practitioners.
        </p>

        <!-- Trust Signals -->
        <div class="grid grid-cols-1 gap-5">
          <div v-for="sig in tm('auth.trust_signals')" :key="sig" class="flex items-center gap-4 text-slate-300 group">
            <div class="w-6 h-6 rounded-lg bg-emerald/10 border border-emerald/20 flex items-center justify-center group-hover:bg-emerald group-hover:border-emerald transition-all duration-300">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" class="text-emerald group-hover:text-white transition-colors">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>
            <span class="text-[14px] font-semibold tracking-tight">{{ rt(sig) }}</span>
          </div>
        </div>
      </div>

      <!-- Footer credit -->
      <div class="mt-auto pt-12 text-[11px] text-slate-500 font-bold uppercase tracking-widest relative z-10 border-t border-white/5 flex items-center justify-between">
        <span>&copy; 2026 DocAppoint Systems</span>
        <span class="flex items-center gap-1.5">
          <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          Encrypted
        </span>
      </div>
    </div>

    <!-- Right panel: Form -->
    <div class="flex items-center justify-center p-6 sm:p-12 lg:p-24 relative bg-white dark:bg-ink-2">
      
      <!-- Mobile Logo -->
      <div class="absolute top-8 left-8 flex lg:hidden items-center gap-3">
        <div class="w-9 h-9 bg-emerald rounded-xl flex items-center justify-center shadow-lg shadow-emerald/20">
          <svg width="18" height="18" viewBox="0 0 16 16" fill="none"><path d="M8 2v12M2 8h12" stroke="white" stroke-width="2.5" stroke-linecap="round"/></svg>
        </div>
        <span class="text-ink dark:text-white font-black text-[18px] uppercase tracking-tight">DocAppoint</span>
      </div>

      <div class="w-full max-w-[420px] mt-16 lg:mt-0">
        <div class="mb-12">
          <h1 class="text-[36px] font-black text-ink dark:text-white tracking-tighter mb-3 leading-none">
            {{ currentTitle }}
          </h1>
          <p class="text-[16px] text-slate-500 dark:text-white/40 font-medium">
            {{ isLogin ? $t('auth.desc_login') : $t('auth.desc_register') }}
          </p>
        </div>

        <!-- Role Tabs (Only for public login) -->
        <div v-if="!role" class="grid grid-cols-3 gap-2 bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/5 rounded-2xl p-1.5 mb-10">
          <button
            v-for="r in roles"
            :key="r.value"
            @click="selectedRole = r.value"
            :class="['py-3 text-[11px] font-black rounded-xl transition-all duration-300 uppercase tracking-widest',
              selectedRole === r.value
                ? 'bg-white dark:bg-emerald text-ink dark:text-white shadow-xl'
                : 'text-slate-400 hover:text-slate-600 dark:text-white/20 dark:hover:text-white/50']"
          >
            {{ $t(r.value) }}
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="submit" class="space-y-6" autocomplete="off">
          
          <div v-if="!isLogin" class="animate-in slide-in-from-top-2 duration-300">
            <div class="form-group">
              <label class="form-label">Legal Full Name</label>
              <div class="relative group">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald transition-colors">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
                <input v-model="form.name" class="form-input-premium" type="text" placeholder="Dr. Sara Benali" required>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">{{ isLogin ? $t('auth.identity') : $t('auth.identity') }}</label>
            <div class="relative group">
              <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald transition-colors">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
              </div>
              <input v-model="form.identity" class="form-input-premium" :type="isLogin ? 'text' : 'email'" :placeholder="isLogin ? 'Email or phone number' : 'contact@hospital.com'" required autocomplete="off">
            </div>
          </div>

          <div v-if="!isLogin" class="form-group animate-in slide-in-from-top-2 duration-400">
            <label class="form-label">{{ $t('auth.phone') }}</label>
            <div class="relative group">
              <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald transition-colors">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
              </div>
              <input v-model="form.phone" class="form-input-premium" type="tel" placeholder="06XXXXXXXX" required>
            </div>
          </div>

          <div v-if="!isLogin && selectedRole === 'doctor'" class="grid grid-cols-2 gap-4 animate-in slide-in-from-top-4 duration-500">
            <div class="form-group">
              <label class="form-label">{{ $t('auth.specialty') }}</label>
              <SpecialtySelect
                v-model="form.specialty"
                :specialties="specialtiesList"
                :placeholder="$t('auth.specialty')"
              />
            </div>
            <div class="form-group">
              <label class="form-label">{{ $t('auth.city') }}</label>
              <LocationSelect
                v-model="form.city"
                :items="citiesList"
                :placeholder="$t('auth.city')"
              />
            </div>
          </div>

          <div class="form-group">
            <div class="flex justify-between mb-2">
              <label class="form-label mb-0">{{ $t('auth.password') }}</label>
              <button v-if="isLogin" type="button" class="text-[11px] font-black text-emerald uppercase tracking-widest hover:opacity-70 transition-opacity">{{ $t('auth.forgot') }}</button>
            </div>
            <div class="relative group">
              <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald transition-colors">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
              </div>
              <input v-model="form.password" class="form-input-premium pr-12" :type="showPassword ? 'text' : 'password'" placeholder="••••••••" required autocomplete="new-password">
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 hover:text-emerald transition-colors"
              >
                <svg v-if="showPassword" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
              </button>
            </div>
          </div>

          <div v-if="isLogin" class="flex items-center gap-3">
            <div class="relative flex items-center">
              <input id="remember" type="checkbox" v-model="form.remember" class="w-5 h-5 rounded-lg border-slate-200 dark:border-white/10 text-emerald focus:ring-emerald/10 transition-all cursor-pointer">
            </div>
            <label for="remember" class="text-[14px] text-slate-500 dark:text-white/40 font-bold cursor-pointer select-none tracking-tight">{{ $t('auth.remember') }}</label>
          </div>

          <!-- Errors -->
          <div v-if="errors.length" class="bg-red-500/5 border border-red-500/10 rounded-2xl px-6 py-5 space-y-2 animate-in fade-in duration-300">
            <div v-for="e in errors" :key="e" class="flex items-start gap-3 text-[13px] text-red-600 dark:text-red-400 font-bold leading-tight">
              <span class="w-2 h-2 rounded-full bg-red-500 mt-1 flex-shrink-0"></span> {{ e }}
            </div>
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full py-5 bg-emerald text-white font-black rounded-2xl text-[18px] hover:bg-emerald-2 transition-all shadow-2xl shadow-emerald/20 disabled:opacity-50 disabled:cursor-not-allowed hover:-translate-y-1 active:translate-y-0 flex items-center justify-center gap-3 group uppercase tracking-widest"
          >
            <span>{{ loading ? 'Securing...' : isLogin ? $t('login') : $t('sign_up') }}</span>
            <svg v-if="!loading" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="group-hover:translate-x-1.5 transition-transform"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
          </button>
        </form>

        <div class="mt-12 pt-12 border-t border-slate-100 dark:border-white/5 text-center">
          <p class="text-[15px] text-slate-500 dark:text-white/40 font-bold">
            {{ isLogin ? (role === 'admin' ? '' : $t('auth.no_account')) : $t('auth.has_account') }}
            <button
              v-if="!(isLogin && role === 'admin')"
              @click="toggle"
              class="text-emerald font-black hover:underline ml-2 uppercase tracking-tighter"
            >
              {{ isLogin ? (role === 'doctor' ? $t('auth.join_now') : $t('auth.join_now')) : $t('auth.sign_in') }}
            </button>
          </p>
        </div>

        <!-- Google Auth -->
        <div class="mt-8">
          <button
            @click="googleLogin"
            class="w-full py-4 border-2 border-slate-100 dark:border-white/5 rounded-2xl flex items-center justify-center gap-4 hover:bg-slate-50 dark:hover:bg-white/5 transition-all text-[15px] font-black text-slate-600 dark:text-white/60 uppercase tracking-widest"
          >
            <svg width="22" height="22" viewBox="0 0 48 48"><path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"></path><path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"></path><path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"></path><path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"></path></svg>
            {{ $t('auth.google') }}
          </button>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import SpecialtySelect from '@/components/ui/SpecialtySelect.vue'
import LocationSelect from '@/components/ui/LocationSelect.vue'

const { t, tm, rt } = useI18n()

const props = defineProps<{
  role?: 'patient' | 'doctor' | 'admin'
  isRegister?: boolean
}>()

const isLogin      = ref(!props.isRegister)
const loading      = ref(false)
const showPassword = ref(false)
const errors       = ref<string[]>([])
const selectedRole = ref(props.role || 'patient')

const currentTitle = computed(() => {
  const r = props.role ?? selectedRole.value
  const mode = isLogin.value ? 'login' : 'register'
  return t(`auth.${r}.${mode}`)
})

const roles = [
  { value: 'patient', label: 'patient' },
  { value: 'doctor',  label: 'doctor'  },
  { value: 'admin',   label: 'admin'   },
]

const specialtiesList = [
  'Cardiology', 'Dermatology', 'Endocrinology', 'Gastroenterology', 
  'General Practice', 'Gynecology', 'Neurology', 'Oncology', 
  'Ophthalmology', 'Orthopedics', 'Pediatrics', 'Psychiatry', 
  'Pulmonology', 'Radiology', 'Rheumatology', 'Urology', 'Dentistry'
].sort()

const citiesList = [
  'Casablanca', 'Rabat', 'Marrakech', 'Fes', 'Tangier', 
  'Agadir', 'Meknes', 'Oujda', 'Kenitra', 'Tetouan', 
  'Safi', 'Mohammedia', 'Khouribga', 'El Jadida', 'Beni Mellal',
  'Nador', 'Taza', 'Settat'
].sort()

const form = reactive({
  name:      '',
  identity:  '', 
  password:  '',
  phone:     '',
  specialty: '',
  city:      '',
  remember:  false,
})

function toggle() {
  isLogin.value = !isLogin.value
  errors.value  = []
}

function submit() {
  loading.value = true
  errors.value  = []

  const url = isLogin.value ? '/login' : '/register'
  const data = {
    ...form,
    role: selectedRole.value,
    email: isLogin.value ? null : form.identity 
  }

  router.post(url, data, {
    onError:  (errs) => { errors.value = Object.values(errs).flat() },
    onFinish: () => { loading.value = false },
  })
}

function googleLogin() {
  errors.value = ["Google Authentication is currently being optimized for your region. Please use your secure credentials for now."]
}
</script>

<style scoped>
.form-label { @apply block text-[11px] font-black text-slate-400 dark:text-white/20 mb-2 uppercase tracking-[0.1em]; }
.form-input-premium { 
  @apply w-full bg-slate-50 dark:bg-ink-3 border-2 border-slate-100 dark:border-white/5 rounded-2xl px-12 py-4 text-[15px] font-bold text-ink dark:text-white outline-none focus:border-emerald focus:bg-white dark:focus:bg-ink-2 focus:ring-8 focus:ring-emerald/5 transition-all placeholder:text-slate-300 dark:placeholder:text-white/5; 
}
/* Custom Select Arrow Fix */
select.form-input-premium {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 1.2rem center;
  background-size: 1.2em;
}
</style>

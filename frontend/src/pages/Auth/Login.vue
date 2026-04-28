<template>
  <div class="min-h-screen grid grid-cols-2 font-sans">
    <!-- Left panel -->
    <div class="bg-ink flex flex-col p-11 relative overflow-hidden">
      <!-- Glow -->
      <div class="absolute bottom-[-100px] right-[-100px] w-[350px] h-[350px] rounded-full pointer-events-none"
           style="background: radial-gradient(circle, rgba(5,150,105,.15) 0%, transparent 65%);"></div>

      <!-- Logo -->
      <div class="flex items-center gap-2.5 mb-10">
        <div class="w-8 h-8 bg-emerald/25 border border-emerald/40 rounded-[9px] flex items-center justify-center">
          <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
            <path d="M7 1v12M1 7h12" stroke="#059669" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </div>
        <span class="font-serif text-[16px] text-white">DocAppoint</span>
      </div>

      <h2 class="font-serif text-[26px] text-white italic leading-[1.25] mb-3">
        Your health journey<br>starts here.
      </h2>
      <p class="text-[13px] text-white/40 leading-[1.7] max-w-[280px]">
        Connect with verified specialists, manage your appointments, and take control of your healthcare experience.
      </p>

      <!-- Trust signals -->
      <div class="flex flex-col gap-3 mt-8">
        <div v-for="signal in trustSignals" :key="signal" class="flex items-center gap-2.5">
          <div class="w-7 h-7 rounded-full bg-emerald/15 flex items-center justify-center flex-shrink-0 text-emerald text-xs font-bold">✓</div>
          <span class="text-[12px] text-white/40">{{ signal }}</span>
        </div>
      </div>

      <!-- Testimonial -->
      <div class="mt-auto bg-white/5 border border-white/9 rounded-[14px] p-4.5 relative z-10">
        <div class="flex items-center gap-2.5 mb-3">
          <div class="w-9 h-9 rounded-full bg-emerald/25 border border-emerald/30 flex items-center justify-center text-xs font-bold text-emerald">SA</div>
          <div>
            <div class="text-[13px] font-bold text-white">Dr. Sara Alaoui</div>
            <div class="text-[11px] text-white/35">Cardiologist · ★ 4.9</div>
          </div>
          <div class="ml-auto">
            <span class="inline-flex items-center gap-1 text-[11px] font-bold bg-emerald-3 text-[#065F46] px-2 py-0.5 rounded-full">✓ Verified</span>
          </div>
        </div>
        <p class="text-[12px] text-white/35 italic leading-[1.65]">
          "DocAppoint transformed how I manage my practice. My schedule stays full and patients love the experience."
        </p>
      </div>
    </div>

    <!-- Right panel: form -->
    <div class="bg-white flex items-center justify-center p-11">
      <div class="w-full max-w-[340px]">
        <h1 class="text-[22px] font-bold text-ink tracking-tight mb-1.5">
          {{ isLogin ? 'Welcome back' : 'Create account' }}
        </h1>
        <p class="text-[13px] text-slate-400 mb-7">
          {{ isLogin ? 'Sign in to your DocAppoint account' : 'Join DocAppoint — it\'s free' }}
        </p>

        <!-- Role tabs -->
        <div class="flex gap-0.5 bg-slate-100 border border-slate-200 rounded-[10px] p-1 mb-6">
          <button
            v-for="role in roles"
            :key="role.value"
            @click="selectedRole = role.value"
            :class="['flex-1 py-2 text-xs font-semibold rounded-[8px] transition-all',
              selectedRole === role.value
                ? 'bg-white text-ink shadow-sm'
                : 'text-slate-500 hover:text-slate-700']"
          >
            {{ role.label }}
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="submit" class="space-y-4">
          <div v-if="!isLogin" class="form-group">
            <label class="form-label">Full name</label>
            <input v-model="form.name" class="form-input" type="text" placeholder="Dr. Sara Alaoui" required>
          </div>

          <div class="form-group">
            <label class="form-label">Email address</label>
            <input v-model="form.email" class="form-input" type="email" placeholder="name@example.com" required>
          </div>

          <div v-if="!isLogin && selectedRole === 'doctor'" class="grid grid-cols-2 gap-3">
            <div class="form-group">
              <label class="form-label">Specialty</label>
              <input v-model="form.specialty" class="form-input" type="text" placeholder="Cardiology" required>
            </div>
            <div class="form-group">
              <label class="form-label">City</label>
              <input v-model="form.city" class="form-input" type="text" placeholder="Casablanca" required>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Password</label>
            <input v-model="form.password" class="form-input" type="password" placeholder="••••••••" required>
          </div>

          <div v-if="isLogin" class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-xs text-slate-500 cursor-pointer">
              <input type="checkbox" class="rounded"> Remember me
            </label>
            <a href="#" class="text-xs text-emerald font-semibold hover:underline">Forgot password?</a>
          </div>

          <!-- Errors -->
          <div v-if="errors.length" class="text-xs text-red-600 bg-red-50 border border-red-200 rounded-[8px] px-3 py-2.5 space-y-0.5">
            <div v-for="e in errors" :key="e">{{ e }}</div>
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full py-3 bg-emerald text-white font-bold rounded-[10px] text-sm hover:bg-emerald-2 transition-all shadow-[0_2px_8px_rgba(5,150,105,.3)] disabled:opacity-60 disabled:cursor-not-allowed"
          >
            {{ loading ? 'Please wait…' : isLogin ? 'Sign in to DocAppoint' : 'Create account' }}
          </button>
        </form>

        <div class="relative my-5 flex items-center">
          <div class="flex-1 h-px bg-slate-200"></div>
          <span class="px-3 text-xs text-slate-400">or continue with</span>
          <div class="flex-1 h-px bg-slate-200"></div>
        </div>

        <button class="w-full py-2.5 border border-slate-200 rounded-[10px] text-sm text-slate-600 font-medium hover:bg-slate-50 transition-colors flex items-center justify-center gap-2">
          <svg width="16" height="16" viewBox="0 0 16 16"><circle cx="8" cy="8" r="7" fill="#4285F4"/><text x="8" y="12" text-anchor="middle" fill="white" font-size="10" font-weight="bold">G</text></svg>
          Sign in with Google
        </button>

        <p class="text-center text-xs text-slate-400 mt-5">
          {{ isLogin ? "No account?" : 'Already have an account?' }}
          <button @click="toggle" class="text-emerald font-bold hover:underline ml-1">
            {{ isLogin ? 'Create one free' : 'Sign in' }}
          </button>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const isLogin      = ref(true)
const loading      = ref(false)
const errors       = ref<string[]>([])
const selectedRole = ref('patient')

const roles = [
  { value: 'patient', label: 'Patient' },
  { value: 'doctor',  label: 'Doctor'  },
  { value: 'admin',   label: 'Admin'   },
]

const trustSignals = [
  'CNSS & RAMED insurance accepted',
  'All doctors board-certified & verified',
  'Data secured to medical-grade standards',
]

const form = reactive({
  name:      '',
  email:     '',
  password:  '',
  specialty: '',
  city:      '',
  role:      computed(() => selectedRole.value),
})

function toggle() {
  isLogin.value = !isLogin.value
  errors.value  = []
}

function submit() {
  loading.value = true
  errors.value  = []

  const url  = isLogin.value ? '/login'    : '/register'
  const data = isLogin.value
    ? { email: form.email, password: form.password, role: selectedRole.value }
    : { ...form }

  router.post(url, data, {
    onError:  (errs) => { errors.value = Object.values(errs).flat() },
    onFinish: () => { loading.value = false },
  })
}
</script>

<style scoped>
.form-group  { }
.form-label  { @apply block text-xs font-semibold text-slate-500 mb-1.5; }
.form-input  { @apply w-full border border-slate-200 rounded-[10px] px-3 py-2.5 text-sm text-ink outline-none focus:border-emerald focus:ring-2 focus:ring-emerald/10 transition-all; }
</style>

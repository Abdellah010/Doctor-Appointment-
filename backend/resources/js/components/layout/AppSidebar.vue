<template>
  <aside class="sidebar w-[240px] bg-ink flex flex-col relative overflow-hidden h-screen flex-shrink-0">
    <!-- Glow effect -->
    <div class="absolute -top-[60px] -right-[60px] w-[200px] h-[200px] pointer-events-none"
         style="background: radial-gradient(circle, rgba(5,150,105,.18) 0%, transparent 70%);"></div>

    <!-- Brand -->
    <div class="sb-brand px-5 py-6 flex items-center gap-3 border-b border-white/5">
      <div class="sb-logo w-9 h-9 bg-emerald rounded-[10px] flex items-center justify-center shadow-[0_0_0_4px_rgba(5,150,105,.2)]">
        <svg width="18" height="18" viewBox="0 0 16 16" fill="none">
          <path d="M8 2v12M2 8h12" stroke="white" stroke-width="2.2" stroke-linecap="round"/>
        </svg>
      </div>
      <div>
        <div class="text-[17px] text-white font-bold tracking-tight">DocAppoint</div>
        <div class="text-[10px] text-white/30 tracking-widest uppercase">v2.4.0</div>
      </div>
    </div>

    <!-- Navigation -->
    <div class="flex-1 overflow-y-auto py-5 px-3">
      <!-- Common Links -->
      <div class="mb-6">
        <div class="text-[9px] font-bold text-white/25 uppercase tracking-widest px-2 mb-2">{{ $t('nav.main_menu') }}</div>
        <nav class="space-y-0.5">
          <Link href="/" class="sb-item" :class="{ active: $page.url === '/' }">
            <span class="sb-icon">🏠</span>
            <span class="sb-txt">{{ $t('nav.home') }}</span>
          </Link>
          <Link href="/doctors" class="sb-item" :class="{ active: $page.url === '/doctors' }">
            <span class="sb-icon">🔍</span>
            <span class="sb-txt">{{ $t('nav.find_doctors') }}</span>
          </Link>
        </nav>
      </div>

      <!-- Patient Links -->
      <div v-if="auth.isPatient" class="mb-6">
        <div class="text-[9px] font-bold text-white/25 uppercase tracking-widest px-2 mb-2">{{ $t('nav.patient_portal') }}</div>
        <nav class="space-y-0.5">
          <Link href="/dashboard" class="sb-item" :class="{ active: $page.url === '/dashboard' }">
            <span class="sb-icon">📊</span>
            <span class="sb-txt">{{ $t('nav.my_dashboard') }}</span>
          </Link>
          <Link href="/dashboard" class="sb-item" :class="{ active: $page.url === '/dashboard' }">
            <span class="sb-icon">📅</span>
            <span class="sb-txt">{{ $t('nav.my_appointments') }}</span>
          </Link>
        </nav>
      </div>

      <!-- Doctor Links -->
      <div v-if="auth.isDoctor" class="mb-6">
        <div class="text-[9px] font-bold text-white/25 uppercase tracking-widest px-2 mb-2">{{ $t('nav.doctor_panel') }}</div>
        <nav class="space-y-0.5">
          <Link href="/doctor/dashboard" class="sb-item" :class="{ active: $page.url === '/doctor/dashboard' }">
            <span class="sb-icon">🩺</span>
            <span class="sb-txt">{{ $t('nav.dashboard') }}</span>
          </Link>
          <Link href="/doctor/dashboard" class="sb-item" :class="{ active: $page.url === '/doctor/dashboard' }">
            <span class="sb-icon">📅</span>
            <span class="sb-txt">{{ $t('nav.manage_schedule') }}</span>
          </Link>
        </nav>
      </div>

      <!-- Admin Links -->
      <div v-if="auth.isAdmin" class="mb-6">
        <div class="text-[9px] font-bold text-white/25 uppercase tracking-widest px-2 mb-2">{{ $t('nav.administration') }}</div>
        <nav class="space-y-0.5">
          <Link href="/admin-system-secure" class="sb-item" :class="{ active: $page.url === '/admin-system-secure' }">
            <span class="sb-icon">🛡️</span>
            <span class="sb-txt">{{ $t('nav.platform_overview') }}</span>
          </Link>
          <Link href="/admin-system-secure/verifications" class="sb-item" :class="{ active: $page.url === '/admin-system-secure/verifications' }">
            <span class="sb-icon">✅</span>
            <span class="sb-txt">{{ $t('nav.verifications') }}</span>
            <span v-if="pendingCount > 0" class="sb-badge">{{ pendingCount }}</span>
          </Link>
        </nav>
      </div>
    </div>

    <!-- Footer -->
    <div class="p-5 border-t border-white/5">
      <div class="text-[10px] text-white/20 leading-relaxed">
        &copy; 2024 DocAppoint.<br>Healthcare with precision.
      </div>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const page = usePage()

const pendingCount = computed(() =>
  (page.props.pending_verifications_count as number) ?? 0
)
</script>

<style scoped>
.sb-item {
  @apply flex items-center gap-2.5 p-2.5 rounded-[10px] cursor-pointer transition-all duration-150 relative;
}
.sb-item:hover {
  @apply bg-white/5;
}
.sb-item.active {
  @apply bg-emerald/15;
}
.sb-item.active::before {
  content: '';
  @apply absolute left-0 top-1/2 -translate-y-1/2 w-0.5 h-4 bg-emerald rounded-r-sm;
}
.sb-icon {
  @apply w-[18px] h-[18px] flex items-center justify-center opacity-40 transition-opacity;
}
.sb-item:hover .sb-icon, .sb-item.active .sb-icon {
  @apply opacity-100;
}
.sb-txt {
  @apply text-[13px] text-white/50 transition-colors;
}
.sb-item:hover .sb-txt, .sb-item.active .sb-txt {
  @apply text-white font-medium;
}
.sb-badge {
  @apply ml-auto text-[10px] bg-emerald text-white px-1.5 py-0.5 rounded-full font-bold animate-pulse;
}
</style>

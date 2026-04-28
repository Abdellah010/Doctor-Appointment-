<template>
  <div class="min-h-screen bg-[#F0F4F8] font-sans">
    <!-- Top navigation bar -->
    <header class="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-6 h-16 flex items-center gap-4">
        <!-- Logo -->
        <Link href="/" class="flex items-center gap-2.5 mr-6">
          <div class="w-8 h-8 bg-emerald rounded-lg flex items-center justify-center shadow-sm">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
              <path d="M8 2v12M2 8h12" stroke="white" stroke-width="2.2" stroke-linecap="round"/>
            </svg>
          </div>
          <span class="font-serif text-[17px] text-ink">DocAppoint</span>
        </Link>

        <!-- Navigation links -->
        <nav class="flex items-center gap-1">
          <Link href="/doctors" class="nav-link" :class="{ active: isActive('/doctors') }">
            Find Doctors
          </Link>
          <Link v-if="auth.isPatient" href="/dashboard" class="nav-link" :class="{ active: isActive('/dashboard') }">
            My Dashboard
          </Link>
          <Link v-if="auth.isDoctor" href="/doctor/dashboard" class="nav-link" :class="{ active: isActive('/doctor') }">
            Dashboard
          </Link>
          <Link v-if="auth.isAdmin" href="/admin" class="nav-link" :class="{ active: isActive('/admin') }">
            Admin
          </Link>
        </nav>

        <div class="ml-auto flex items-center gap-3">
          <!-- Verification alert badge -->
          <Link
            v-if="auth.isAdmin && pendingCount > 0"
            href="/admin/verifications"
            class="flex items-center gap-1.5 bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold px-3 py-1.5 rounded-full hover:bg-amber-100 transition-colors"
          >
            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
            {{ pendingCount }} pending
          </Link>

          <!-- Auth buttons -->
          <template v-if="!auth.isLoggedIn">
            <Link href="/login" class="text-sm text-slate-600 font-medium hover:text-ink transition-colors">
              Log in
            </Link>
            <Link href="/register" class="btn-primary text-sm px-4 py-2">
              Get started free
            </Link>
          </template>

          <!-- User avatar dropdown -->
          <div v-else class="relative" ref="avatarRef">
            <button
              @click="menuOpen = !menuOpen"
              class="w-9 h-9 rounded-full bg-ink flex items-center justify-center text-white text-sm font-bold border-2 border-transparent hover:border-emerald transition-colors"
            >
              {{ initials }}
            </button>
            <div
              v-if="menuOpen"
              class="absolute right-0 top-12 w-52 bg-white border border-slate-200 rounded-lg shadow-lg py-1 z-50"
            >
              <div class="px-4 py-3 border-b border-slate-100">
                <div class="text-sm font-semibold text-ink">{{ auth.user?.name }}</div>
                <div class="text-xs text-slate-400 mt-0.5">{{ auth.user?.email }}</div>
              </div>
              <Link
                :href="dashboardUrl"
                class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors"
                @click="menuOpen = false"
              >
                Dashboard
              </Link>
              <button
                @click="logout"
                class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors"
              >
                Sign out
              </button>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Flash messages -->
    <Transition name="flash">
      <div
        v-if="flash.success"
        class="fixed top-20 right-6 z-50 bg-emerald-4 border border-emerald-3 text-emerald-2 text-sm font-semibold px-4 py-3 rounded-lg shadow-lg flex items-center gap-2"
      >
        <span class="text-base">✓</span> {{ flash.success }}
      </div>
    </Transition>

    <!-- Page content -->
    <main>
      <slot />
    </main>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import { onClickOutside } from '@vueuse/core'
import { useAuthStore } from '@/stores/auth'

const auth  = useAuthStore()
const page  = usePage()
const menuOpen = ref(false)
const avatarRef = ref<HTMLElement>()

onClickOutside(avatarRef, () => { menuOpen.value = false })

const flash = computed(() => page.props.flash as Record<string, string> ?? {})

const pendingCount = computed(() =>
  (page.props.pending_verifications_count as number) ?? 0
)

const initials = computed(() => {
  const name = auth.user?.name ?? ''
  return name.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase()
})

const dashboardUrl = computed(() => {
  if (auth.isAdmin)   return '/admin'
  if (auth.isDoctor)  return '/doctor/dashboard'
  return '/dashboard'
})

function isActive(path: string): boolean {
  return page.url.startsWith(path)
}

function logout() {
  menuOpen.value = false
  router.post('/logout')
}
</script>

<style scoped>
.nav-link {
  @apply px-3 py-2 rounded-lg text-sm text-slate-600 font-medium hover:text-ink hover:bg-slate-50 transition-colors;
}
.nav-link.active {
  @apply text-emerald bg-emerald-4;
}
.btn-primary {
  @apply bg-emerald text-white rounded-[10px] font-semibold hover:bg-emerald-2 transition-all shadow-sm;
}
.flash-enter-active, .flash-leave-active { transition: all .3s ease; }
.flash-enter-from, .flash-leave-to { opacity: 0; transform: translateY(-8px); }
</style>

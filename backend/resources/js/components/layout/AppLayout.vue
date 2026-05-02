<template>
  <Head :title="pageTitle">
    <meta name="description" :content="pageSubTitle">
  </Head>

  <div class="root-layout min-h-screen bg-[#F0F4F8] dark:bg-ink font-sans transition-colors duration-300 md:flex" :dir="isRTL ? 'rtl' : 'ltr'">
    
    <!-- Mobile overlay -->
    <div
      v-if="auth.isLoggedIn && mobileMenuOpen"
      class="fixed inset-0 bg-ink/60 z-[100] md:hidden backdrop-blur-sm animate-in fade-in duration-300"
      @click="mobileMenuOpen = false"
    ></div>

    <!-- Sidebar: Fixed overlay on mobile, relative panel on desktop -->
    <div
      v-if="auth.isLoggedIn"
      class="sidebar-container fixed md:relative inset-y-0 left-0 z-[110] transition-transform duration-300 ease-out md:translate-x-0"
      :class="[mobileMenuOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full md:translate-x-0']"
    >
      <AppSidebar class="w-[260px] h-full" />
    </div>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-h-screen min-w-0 w-full overflow-x-hidden">
      <!-- Top navigation bar -->
      <header class="h-[58px] bg-white dark:bg-ink-2 border-b border-slate-200 dark:border-white/10 shadow-sm flex items-center px-4 md:px-7 flex-shrink-0 z-50">
        
        <!-- Mobile hamburger -->
        <button
          v-if="auth.isLoggedIn"
          @click="mobileMenuOpen = true"
          class="md:hidden w-10 h-10 -ml-2 flex items-center justify-center text-slate-500 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 rounded-full transition-colors"
        >
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
          </svg>
        </button>

        <!-- Logo (Only for guests) -->
        <Link v-if="!auth.isLoggedIn" href="/" class="flex items-center gap-2.5 mr-auto md:mr-8">
          <div class="w-8 h-8 bg-emerald rounded-lg flex items-center justify-center shadow-sm">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
              <path d="M8 2v12M2 8h12" stroke="white" stroke-width="2.2" stroke-linecap="round"/>
            </svg>
          </div>
          <span class="text-[17px] text-ink dark:text-white font-bold tracking-tight hidden sm:block">DocAppoint</span>
        </Link>

        <!-- Page Identity -->
        <div v-else class="flex flex-col truncate ml-2 md:ml-0">
          <div class="text-[15px] font-bold text-ink dark:text-white leading-tight truncate">{{ pageTitle }}</div>
          <div class="text-[11px] text-slate-400 dark:text-white/40 mt-0.5 truncate hidden sm:block">{{ pageSubTitle }}</div>
        </div>

        <div class="ml-auto flex items-center gap-2 md:gap-4">
          <!-- Theme & Language Toggles -->
          <div class="flex items-center gap-1.5">
            <!-- Theme Toggle -->
            <div class="relative" ref="themeRef">
              <button @click="themeOpen = !themeOpen" class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-white/10 transition-all text-slate-600 dark:text-white/70 border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5">
                <svg v-if="mode === 'light'" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                <svg v-else-if="mode === 'dark'" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
              </button>
              <Transition name="pop">
                <div v-if="themeOpen" class="absolute right-0 top-11 w-40 bg-white dark:bg-ink border border-slate-200 dark:border-white/10 rounded-xl shadow-2xl py-1.5 z-[120]">
                  <button @click="mode = 'light'; themeOpen = false" :class="['w-full flex items-center gap-3 px-4 py-2.5 text-sm', mode === 'light' ? 'text-emerald font-bold bg-emerald/5' : 'text-slate-600 dark:text-white/60 hover:bg-slate-50 dark:hover:bg-white/5']">☀️ Light</button>
                  <button @click="mode = 'dark'; themeOpen = false" :class="['w-full flex items-center gap-3 px-4 py-2.5 text-sm', mode === 'dark' ? 'text-emerald font-bold bg-emerald/5' : 'text-slate-600 dark:text-white/60 hover:bg-slate-50 dark:hover:bg-white/5']">🌙 Dark</button>
                  <button @click="mode = 'auto'; themeOpen = false" :class="['w-full flex items-center gap-3 px-4 py-2.5 text-sm', mode === 'auto' ? 'text-emerald font-bold bg-emerald/5' : 'text-slate-600 dark:text-white/60 hover:bg-slate-50 dark:hover:bg-white/5']">💻 System</button>
                </div>
              </Transition>
            </div>

            <!-- Language Toggle -->
            <div class="relative" ref="langRef">
              <button @click="langOpen = !langOpen" class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-white/10 transition-all text-lg border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5">
                {{ currentFlag }}
              </button>
              <Transition name="pop">
                <div v-if="langOpen" class="absolute right-0 top-11 w-44 bg-white dark:bg-ink border border-slate-200 dark:border-white/10 rounded-xl shadow-2xl py-1.5 z-[120]">
                  <button @click="setLang('en')" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-slate-600 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5">🇺🇸 English</button>
                  <button @click="setLang('fr')" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-slate-600 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5">🇫🇷 Français</button>
                  <button @click="setLang('ar')" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-slate-600 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 border-t border-slate-100 dark:border-white/5">🇲🇦 العربية</button>
                </div>
              </Transition>
            </div>
          </div>

          <!-- User Avatar -->
          <div v-if="auth.isLoggedIn" class="relative" ref="avatarRef">
            <button @click="menuOpen = !menuOpen" class="w-9 h-9 rounded-full bg-emerald text-white text-xs font-bold border-2 border-white dark:border-white/10 shadow-sm hover:scale-105 transition-transform">
              {{ initials }}
            </button>
            <Transition name="pop">
              <div v-if="menuOpen" class="absolute right-0 top-11 w-56 bg-white dark:bg-ink border border-slate-200 dark:border-white/10 rounded-2xl shadow-2xl py-2 z-[120]">
                <div class="px-4 py-3 border-b border-slate-50 dark:border-white/5 mb-2">
                  <div class="text-sm font-bold text-ink dark:text-white">{{ auth.user?.name }}</div>
                  <div class="text-[11px] text-slate-400 mt-1 truncate">{{ auth.user?.email }}</div>
                </div>
                <Link :href="dashboardUrl" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5">📊 {{ $t('dashboard') }}</Link>
                <button @click="logout" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10">🚪 {{ $t('logout') }}</button>
              </div>
            </Transition>
          </div>
        </div>
      </header>

      <!-- Main Body -->
      <main class="flex-1 overflow-y-auto">
        <div class="p-4 md:p-8 max-w-[1600px] mx-auto w-full">
          <slot />
        </div>
        <AppFooter />
      </main>

      <!-- Global Notifications -->
      <div class="fixed bottom-6 right-6 flex flex-col gap-3 z-[150]">
        <Transition name="slide-up">
          <div v-if="flash.success" class="bg-emerald text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 backdrop-blur-md">
            <span class="text-lg">✓</span> {{ flash.success }}
          </div>
        </Transition>
        <Transition name="slide-up">
          <div v-if="flash.error" class="bg-red-600 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 backdrop-blur-md">
            <span class="text-lg">!</span> {{ flash.error }}
          </div>
        </Transition>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { Link, Head, usePage, router } from '@inertiajs/vue3'
import { onClickOutside, useColorMode } from '@vueuse/core'
import { useAuthStore } from '@/stores/auth'
import { useI18n } from 'vue-i18n'
import AppSidebar from './AppSidebar.vue'
import AppFooter from './AppFooter.vue'

const auth = useAuthStore()
const page = usePage()
const { t, locale } = useI18n()

const menuOpen = ref(false)
const langOpen = ref(false)
const themeOpen = ref(false)
const mobileMenuOpen = ref(false)
const avatarRef = ref<HTMLElement>()
const langRef = ref<HTMLElement>()
const themeRef = ref<HTMLElement>()

onClickOutside(avatarRef, () => { menuOpen.value = false })
onClickOutside(langRef, () => { langOpen.value = false })
onClickOutside(themeRef, () => { themeOpen.value = false })

router.on('navigate', () => { mobileMenuOpen.value = false })

const flash = computed(() => page.props.flash as Record<string, string> ?? {})
const mode = useColorMode({
  selector: 'html',
  attribute: 'class',
  modes: { dark: 'dark', light: '' },
})

const isRTL = computed(() => locale.value === 'ar')
const currentFlag = computed(() => {
  if (locale.value === 'ar') return '🇲🇦'
  if (locale.value === 'fr') return '🇫🇷'
  return '🇺🇸'
})

function setLang(lang: string) {
  locale.value = lang
  localStorage.setItem('locale', lang)
  langOpen.value = false
  document.documentElement.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr')
}

const initials = computed(() => (auth.user?.name ?? '').split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase())
const dashboardUrl = computed(() => {
  if (auth.isAdmin) return '/admin-system-secure'
  if (auth.isDoctor) return '/doctor/dashboard'
  return '/dashboard'
})

const pageTitle = computed(() => {
  const url = page.url
  if (url.includes('admin')) return t('nav.administration')
  if (url.includes('doctor')) return t('nav.dashboard')
  if (url.includes('dashboard')) return t('nav.my_dashboard')
  return 'DocAppoint'
})

function logout() {
  router.post('/logout')
}
</script>

<style scoped>
.pop-enter-active, .pop-leave-active { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); transform-origin: top right; }
.pop-enter-from, .pop-leave-to { opacity: 0; transform: scale(0.9) translateY(-10px); }

.slide-up-enter-active, .slide-up-leave-active { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.slide-up-enter-from, .slide-up-leave-to { opacity: 0; transform: translateY(20px); }

@keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
.fade-in { animation: fade-in 0.3s ease-out; }
</style>

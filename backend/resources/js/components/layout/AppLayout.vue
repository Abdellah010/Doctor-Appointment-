<template>
  <Head :title="pageTitle">
    <meta name="description" :content="pageSubTitle">
  </Head>

  <div class="min-h-screen bg-[#F0F4F8] dark:bg-ink font-sans flex overflow-x-hidden transition-colors duration-300" :dir="isRTL ? 'rtl' : 'ltr'">
    <!-- Mobile overlay -->
    <div
      v-if="auth.isLoggedIn && mobileMenuOpen"
      class="fixed inset-0 bg-ink/50 dark:bg-black/60 z-[60] md:hidden backdrop-blur-sm"
      @click="mobileMenuOpen = false"
    ></div>

    <!-- Sidebar (Only for logged in users) -->
    <AppSidebar
      v-if="auth.isLoggedIn"
      class="z-[70] transition-all duration-300 fixed inset-y-0 left-0 md:relative md:translate-x-0"
      :class="[
        mobileMenuOpen 
          ? 'w-[240px] translate-x-0 shadow-2xl visible pointer-events-auto' 
          : 'w-0 md:w-[240px] -translate-x-full invisible pointer-events-none md:translate-x-0 md:visible md:pointer-events-auto'
      ]"
    />

    <div class="flex-1 flex flex-col h-[100dvh] overflow-hidden w-full">
      <!-- Top navigation bar -->
      <header class="h-[54px] md:h-[58px] bg-white dark:bg-ink-2 border-b border-slate-200 dark:border-white/10 shadow-sm flex items-center px-3 md:px-7 flex-shrink-0 z-50 transition-colors duration-300">
        
        <!-- Mobile hamburger (Logged in only) -->
        <button
          v-if="auth.isLoggedIn"
          @click="mobileMenuOpen = true"
          class="md:hidden w-8 h-8 flex items-center justify-center mr-3 text-slate-500 dark:text-white/70"
        >
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
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

        <!-- Crumbs / Title -->
        <div v-else class="flex flex-col truncate">
          <div class="text-[14px] font-bold text-ink dark:text-white leading-tight truncate">{{ pageTitle }}</div>
          <div class="text-[11px] text-slate-400 dark:text-white/40 mt-0.5 truncate hidden sm:block">{{ pageSubTitle }}</div>
        </div>

        <div class="ml-auto flex items-center gap-1.5 md:gap-4">
          
          <!-- Theme Toggle (3-State) -->
          <div class="relative" ref="themeRef">
            <button 
              @click="themeOpen = !themeOpen" 
              class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-white/10 transition-all text-slate-600 dark:text-white/70 shadow-sm border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5"
            >
              <!-- Show current mode icon -->
              <svg v-if="mode === 'light'" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
              <svg v-else-if="mode === 'dark'" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
              <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
            </button>

            <Transition name="lang-pop">
              <div v-if="themeOpen" class="absolute right-0 top-11 w-36 bg-white dark:bg-ink border border-slate-200 dark:border-white/10 rounded-xl shadow-xl py-1.5 z-50 overflow-hidden">
                <button @click="mode = 'light'; themeOpen = false" :class="['w-full flex items-center gap-3 px-4 py-2 text-sm transition-colors', mode === 'light' ? 'text-emerald font-bold bg-emerald-4 dark:bg-emerald/10' : 'text-slate-700 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5']">
                  <span class="text-base">☀️</span> Light
                </button>
                <button @click="mode = 'dark'; themeOpen = false" :class="['w-full flex items-center gap-3 px-4 py-2 text-sm transition-colors', mode === 'dark' ? 'text-emerald font-bold bg-emerald-4 dark:bg-emerald/10' : 'text-slate-700 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5']">
                  <span class="text-base">🌙</span> Dark
                </button>
                <button @click="mode = 'auto'; themeOpen = false" :class="['w-full flex items-center gap-3 px-4 py-2 text-sm transition-colors', mode === 'auto' ? 'text-emerald font-bold bg-emerald-4 dark:bg-emerald/10' : 'text-slate-700 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5']">
                  <span class="text-base">💻</span> System
                </button>
              </div>
            </Transition>
          </div>

          <!-- Language Dropdown -->
          <div class="relative" ref="langRef">
            <button @click="langOpen = !langOpen" class="flex items-center gap-2 text-sm font-bold text-slate-600 dark:text-white/70 hover:text-ink dark:hover:text-white transition-all bg-slate-100 dark:bg-white/5 px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-white/10 shadow-sm">
              <span class="text-base">{{ currentFlag }}</span>
              <span class="hidden md:inline uppercase text-[11px] tracking-widest">{{ currentLang }}</span>
              <svg width="10" height="10" viewBox="0 0 10 10" fill="none" class="opacity-50"><path d="M2 3L5 6L8 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <Transition name="lang-pop">
              <div v-if="langOpen" class="absolute right-0 top-11 w-40 bg-white dark:bg-ink border border-slate-200 dark:border-white/10 rounded-xl shadow-xl py-1.5 z-50 overflow-hidden">
                <button @click="setLang('en')" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group">
                  <span class="text-lg">🇺🇸</span>
                  <div class="flex flex-col">
                    <span class="font-bold text-[13px] group-hover:text-ink dark:group-hover:text-white">English</span>
                    <span class="text-[10px] opacity-40 uppercase tracking-tighter">USA / UK</span>
                  </div>
                </button>
                <button @click="setLang('fr')" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group">
                  <span class="text-lg">🇫🇷</span>
                  <div class="flex flex-col">
                    <span class="font-bold text-[13px] group-hover:text-ink dark:group-hover:text-white">Français</span>
                    <span class="text-[10px] opacity-40 uppercase tracking-tighter">France</span>
                  </div>
                </button>
                <button @click="setLang('ar')" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 dark:text-white/70 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group border-t border-slate-50 dark:border-white/5 mt-1 pt-2">
                  <span class="text-lg">🇲🇦</span>
                  <div class="flex flex-col items-end w-full">
                    <span class="font-bold text-[13px] group-hover:text-ink dark:group-hover:text-white">العربية</span>
                    <span class="text-[10px] opacity-40 uppercase tracking-tighter">المغرب</span>
                  </div>
                </button>
              </div>
            </Transition>
          </div>

          <!-- Guest Nav -->
          <nav v-if="!auth.isLoggedIn" class="hidden md:flex items-center gap-2 mr-2">
            <Link href="/doctors" class="text-sm text-slate-600 dark:text-white/70 font-medium hover:text-ink dark:hover:text-white px-3 py-2 rounded-lg hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
              {{ $t('find_doctor') }}
            </Link>
          </nav>

          <!-- Auth buttons -->
          <template v-if="!auth.isLoggedIn">
            <Link href="/login" class="hidden sm:block text-sm text-slate-600 dark:text-white/70 font-medium hover:text-ink dark:hover:text-white transition-colors mr-2">
              {{ $t('login') }}
            </Link>
            <Link href="/register" class="btn-primary text-xs md:text-sm px-3 md:px-5 py-2">
              {{ $t('sign_up') }}
            </Link>
          </template>

          <!-- User avatar dropdown -->
          <div v-else class="relative" ref="avatarRef">
            <button
              @click="menuOpen = !menuOpen"
              class="w-[34px] h-[34px] rounded-full bg-ink dark:bg-white/10 flex items-center justify-center text-white text-[11px] font-bold border-2 border-slate-200 dark:border-white/20 hover:border-emerald transition-all"
            >
              {{ initials }}
            </button>
            <div
              v-if="menuOpen"
              class="absolute right-0 top-11 w-52 bg-white dark:bg-ink border border-slate-200 dark:border-white/10 rounded-xl shadow-lg py-1 z-50 overflow-hidden"
            >
              <div class="px-4 py-3 border-b border-slate-50 dark:border-white/5 bg-slate-50/50 dark:bg-white/5">
                <div class="text-sm font-bold text-ink dark:text-white leading-none">{{ auth.user?.name }}</div>
                <div class="text-[11px] text-slate-400 dark:text-white/40 mt-1.5 truncate">{{ auth.user?.email }}</div>
              </div>
              <div class="p-1">
                <Link
                  :href="dashboardUrl"
                  class="flex items-center gap-2 px-3 py-2 text-sm text-slate-600 dark:text-white/70 hover:text-ink dark:hover:text-white hover:bg-slate-50 dark:hover:bg-white/5 rounded-lg transition-colors"
                  @click="menuOpen = false"
                >
                  <span class="text-base opacity-50">📊</span> {{ $t('dashboard') }}
                </Link>
                <button
                  @click="logout"
                  class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors"
                >
                  <span class="text-base opacity-50">🚪</span> {{ $t('logout') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Scroll body -->
      <main class="flex-1 overflow-y-auto p-3 md:p-7 relative">
        <!-- Flash messages -->
        <Transition name="flash">
          <div
            v-if="flash.success"
            class="fixed bottom-6 right-6 md:bottom-8 md:right-8 z-[100] bg-emerald-4 dark:bg-emerald/90 border border-emerald-3 dark:border-emerald/30 text-[#065F46] dark:text-white text-sm font-bold px-5 py-4 rounded-2xl shadow-2xl flex items-center gap-3 backdrop-blur-md"
          >
            <div class="w-6 h-6 rounded-full bg-emerald text-white flex items-center justify-center text-xs">✓</div>
            <span>{{ flash.success }}</span>
            <button @click="flash.success = null" class="ml-2 opacity-50 hover:opacity-100">✕</button>
          </div>
        </Transition>

        <Transition name="flash">
          <div
            v-if="flash.error"
            class="fixed bottom-6 right-6 md:bottom-8 md:right-8 z-[100] bg-red-50 dark:bg-red-500/90 border border-red-200 dark:border-red-500/30 text-red-700 dark:text-white text-sm font-bold px-5 py-4 rounded-2xl shadow-2xl flex items-center gap-3 backdrop-blur-md"
          >
            <div class="w-6 h-6 rounded-full bg-red-600 text-white flex items-center justify-center text-xs">!</div>
            <span>{{ flash.error }}</span>
            <button @click="flash.error = null" class="ml-2 opacity-50 hover:opacity-100">✕</button>
          </div>
        </Transition>

        <slot />
        <AppFooter />
      </main>
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

const auth  = useAuthStore()
const page  = usePage()
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

// Close mobile menu on navigate
router.on('navigate', () => { mobileMenuOpen.value = false })

const flash = computed(() => page.props.flash as Record<string, string> ?? {})

// Theme — useColorMode handles light, dark, and auto (system)
const mode = useColorMode({
  selector: 'html',
  attribute: 'class',
  modes: {
    dark: 'dark',
    light: '',
  },
  storageKey: 'vueuse-color-scheme',
})

// Safety sync for dark mode class
watch(mode, (val) => {
  const isDark = val === 'dark' || (val === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches)
  if (isDark) document.documentElement.classList.add('dark')
  else       document.documentElement.classList.remove('dark')
}, { immediate: true })

// I18n logic
const currentLang = computed(() => locale.value)
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
  // Force document direction
  document.documentElement.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr')
  document.documentElement.setAttribute('lang', lang)
}

const initials = computed(() => {
  const name = auth.user?.name ?? ''
  return name.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase()
})

const dashboardUrl = computed(() => {
  if (auth.isAdmin)   return '/admin-system-secure'
  if (auth.isDoctor)  return '/doctor/dashboard'
  return '/dashboard'
})

const pageTitle = computed(() => {
  const url = page.url
  if (url === '/admin') return t('nav.platform_overview')
  if (url === '/admin/verifications') return t('nav.verifications')
  if (url.startsWith('/doctor/dashboard')) return t('nav.dashboard')
  if (url.startsWith('/dashboard')) return t('nav.my_dashboard')
  if (url.startsWith('/doctors')) return t('nav.find_doctors')
  return 'DocAppoint'
})

const pageSubTitle = computed(() => {
  const url = page.url
  if (url === '/admin') return t('nav.administration')
  if (url === '/admin/verifications') return t('nav.verifications')
  if (url.startsWith('/doctor/dashboard')) return t('nav.manage_schedule')
  if (url.startsWith('/dashboard')) return t('nav.patient_portal')
  if (url.startsWith('/doctors')) return t('nav.find_doctors')
  return 'Healthcare delivered with precision'
})

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

.lang-pop-enter-active, .lang-pop-leave-active { transition: all .2s cubic-bezier(0.4, 0, 0.2, 1); transform-origin: top right; }
.lang-pop-enter-from, .lang-pop-leave-to { opacity: 0; transform: scale(0.95) translateY(-10px); }
</style>

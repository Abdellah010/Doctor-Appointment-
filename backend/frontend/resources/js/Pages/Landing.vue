<template>
  <AppLayout>
    <!-- Premium Hero -->
    <section class="mx-4 md:mx-6 mt-6 relative group">
      <div class="bg-white dark:bg-ink-2 border border-slate-100 dark:border-white/5 rounded-[32px] md:rounded-[40px] overflow-hidden min-h-[600px] flex flex-col lg:flex-row items-center relative transition-colors duration-500 shadow-sm">
        <!-- Background Accents -->
        <div class="absolute top-0 right-0 w-full h-full opacity-30 dark:opacity-20 pointer-events-none">
          <div class="absolute top-[-20%] right-[-10%] w-[60%] h-[80%] bg-emerald/10 dark:bg-emerald/20 blur-[120px] rounded-full"></div>
          <div class="absolute bottom-[-10%] left-[-10%] w-[40%] h-[60%] bg-blue-500/5 dark:bg-blue-500/10 blur-[100px] rounded-full"></div>
        </div>

        <!-- Hero Content -->
        <div class="flex-1 p-8 md:p-16 lg:p-24 relative z-10 text-center lg:text-left">
          <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-emerald/10 border border-emerald/20 text-emerald text-[11px] font-black uppercase tracking-[0.2em] mb-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
            <span class="w-2 h-2 rounded-full bg-emerald animate-pulse"></span>
            {{ $t('trusted_by') }}
          </div>
          
          <h1 class="text-[40px] md:text-[56px] lg:text-[72px] text-ink dark:text-white font-black leading-[0.95] mb-8 tracking-tighter animate-in fade-in slide-in-from-bottom-6 duration-1000" v-html="$t('hero_title')"></h1>
          
          <p class="text-[16px] md:text-[18px] text-slate-500 dark:text-slate-400 leading-[1.6] max-w-[480px] mb-12 mx-auto lg:mx-0 animate-in fade-in slide-in-from-bottom-8 duration-1000 font-medium">
            {{ $t('hero_desc') }}
          </p>

          <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto animate-in fade-in slide-in-from-bottom-10 duration-1000">
            <Link
              href="/doctors"
              class="px-10 py-5 bg-emerald text-white text-[16px] font-black rounded-2xl hover:bg-emerald-2 transition-all shadow-2xl shadow-emerald/20 hover:-translate-y-1 active:translate-y-0 flex items-center justify-center gap-3 uppercase tracking-widest group"
            >
              {{ $t('find_doctor') }}
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="group-hover:translate-x-1.5 transition-transform"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </Link>
            <Link
              href="/register"
              class="px-10 py-5 bg-slate-50 dark:bg-white/5 text-slate-600 dark:text-white font-black rounded-2xl border-2 border-slate-100 dark:border-white/10 hover:bg-slate-100 dark:hover:bg-white/10 transition-all hover:-translate-y-1 active:translate-y-0 flex items-center justify-center uppercase tracking-widest"
            >
              {{ $t('sign_up') }}
            </Link>
          </div>
        </div>

        <!-- Hero Visual -->
        <div class="flex-1 relative w-full lg:h-full min-h-[400px] lg:min-h-0 overflow-hidden">
          <div class="absolute inset-0 bg-gradient-to-t lg:bg-gradient-to-l from-white dark:from-ink via-transparent to-transparent z-10"></div>
          <img 
            src="/images/hero_doctor_premium.png" 
            alt="Healthcare Visual" 
            class="w-full h-full object-cover object-center lg:object-right-top opacity-80 dark:opacity-60 group-hover:scale-105 transition-transform duration-[2000ms]"
          >
        </div>
      </div>
    </section>

    <!-- Trust Stats: Modern Grid -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mx-4 md:mx-6 mt-8">
      <div
        v-for="(stat, idx) in trustStats"
        :key="stat.label"
        :style="{ transitionDelay: `${idx * 100}ms` }"
        class="bg-white dark:bg-ink border-2 border-slate-50 dark:border-white/5 rounded-[32px] p-8 flex items-center gap-6 hover:border-emerald/30 hover:shadow-2xl hover:shadow-emerald/5 transition-all group animate-in zoom-in-95 duration-700"
      >
        <div :class="['w-16 h-16 rounded-2xl flex items-center justify-center text-3xl shadow-inner group-hover:scale-110 transition-transform duration-500', stat.iconBg]">
          {{ stat.icon }}
        </div>
        <div>
          <div class="text-[32px] font-black text-ink dark:text-white tracking-tighter leading-none mb-2">{{ stat.value }}</div>
          <div class="text-[12px] text-slate-400 dark:text-white/40 font-black uppercase tracking-[0.15em]">{{ stat.label }}</div>
        </div>
      </div>
    </section>

    <!-- Features: World Class Design -->
    <section class="mx-4 md:mx-6 mt-20 mb-32">
      <div class="text-center mb-16">
        <div class="text-emerald font-black text-[11px] uppercase tracking-[0.3em] mb-4">DocAppoint</div>
        <h2 class="text-[32px] md:text-[44px] font-black text-ink dark:text-white tracking-tighter leading-none">{{ $t('features.title') }}</h2>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div
          v-for="(feat, idx) in features"
          :key="feat.title"
          :style="{ transitionDelay: `${idx * 150}ms` }"
          class="group relative bg-white dark:bg-ink-2 border-2 border-slate-50 dark:border-white/5 rounded-[40px] p-10 hover:border-emerald/30 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 overflow-hidden"
        >
          <!-- Corner Glow -->
          <div class="absolute top-0 right-0 w-32 h-32 bg-emerald/5 blur-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>

          <div :class="['w-16 h-16 rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:rotate-[10deg] transition-transform duration-500 shadow-sm', feat.iconBg]">
            {{ feat.icon }}
          </div>
          
          <h3 class="text-[22px] font-black text-ink dark:text-white mb-4 tracking-tight leading-tight">{{ feat.title }}</h3>
          <p class="text-[15px] text-slate-500 dark:text-white/40 leading-[1.7] mb-8 font-medium">{{ feat.desc }}</p>
          
          <div class="mt-auto flex items-center justify-between">
            <span :class="['text-[11px] font-black px-5 py-2 rounded-full uppercase tracking-widest', feat.pillClass]">
              {{ feat.pill }}
            </span>
            <div class="w-10 h-10 rounded-full border border-slate-100 dark:border-white/10 flex items-center justify-center text-slate-300 dark:text-white/20 group-hover:bg-emerald group-hover:border-emerald group-hover:text-white transition-all duration-300">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Professional CTA -->
    <section class="mx-4 md:mx-6 mb-12">
      <div class="bg-emerald dark:bg-emerald/90 rounded-[40px] p-12 md:p-20 text-center relative overflow-hidden group shadow-2xl shadow-emerald/20">
        <!-- Abstract patterns -->
        <div class="absolute top-0 left-0 w-full h-full opacity-10">
          <div class="absolute top-[-50%] left-[-20%] w-full h-full border-[100px] border-white rounded-full"></div>
        </div>

        <div class="relative z-10 max-w-2xl mx-auto">
          <h2 class="text-[32px] md:text-[48px] text-white font-black leading-none tracking-tighter mb-8">{{ $t('landing.cta_title') }}</h2>
          <p class="text-white/80 text-[16px] md:text-[18px] font-medium mb-12">{{ $t('landing.cta_desc') }}</p>
          
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <Link href="/register" class="px-10 py-5 bg-ink text-white font-black rounded-2xl hover:scale-105 transition-transform uppercase tracking-widest shadow-xl">{{ $t('landing.cta_get_started') }}</Link>
            <Link href="/doctor-portal-secure/register" class="px-10 py-5 bg-white text-emerald font-black rounded-2xl hover:scale-105 transition-transform uppercase tracking-widest shadow-xl">Join as Medical Professional</Link>
          </div>
        </div>
      </div>
    </section>
  </AppLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/components/layout/AppLayout.vue'

const { t } = useI18n()

const trustStats = computed(() => [
  { icon: '🏥', iconBg: 'bg-emerald-50 dark:bg-emerald/10 text-emerald', value: '500+',  label: t('stats.verified') },
  { icon: '👥', iconBg: 'bg-blue-50 dark:bg-blue-500/10 text-blue-600',   value: '15k+', label: t('stats.bookings') },
  { icon: '⭐', iconBg: 'bg-amber-50 dark:bg-amber-500/10 text-amber-500',  value: '99%',  label: t('stats.satisfaction') },
])

const features = computed(() => [
  {
    icon: '🛡️', iconBg: 'bg-emerald-50 dark:bg-emerald/10 text-emerald',
    title: t('features.feat1_title'),
    desc:  t('features.feat1_desc'),
    pill: t('features.feat1_pill'), pillClass: 'bg-emerald/10 text-emerald',
  },
  {
    icon: '⚡', iconBg: 'bg-blue-50 dark:bg-blue-500/10 text-blue-600',
    title: t('features.feat2_title'),
    desc:  t('features.feat2_desc'),
    pill: t('features.feat2_pill'), pillClass: 'bg-blue-500/10 text-blue-500',
  },
  {
    icon: '📊', iconBg: 'bg-amber-50 dark:bg-amber-500/10 text-amber-500',
    title: t('features.feat3_title'),
    desc:  t('features.feat3_desc'),
    pill: t('features.feat3_pill'), pillClass: 'bg-amber-500/10 text-amber-500',
  },
])
</script>

<style scoped>
@keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
@keyframes slide-in-bottom { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
@keyframes zoom-in { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }

.animate-in { animation-fill-mode: both; }
.fade-in { animation-name: fade-in; }
.slide-in-from-bottom-4 { animation-name: slide-in-bottom; }
.slide-in-from-bottom-6 { animation-name: slide-in-bottom; }
.slide-in-from-bottom-8 { animation-name: slide-in-bottom; }
.slide-in-from-bottom-10 { animation-name: slide-in-bottom; }
.zoom-in-95 { animation-name: zoom-in; }

.duration-700 { animation-duration: 700ms; }
.duration-1000 { animation-duration: 1000ms; }
</style>

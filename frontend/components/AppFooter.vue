<template>
    <!--
    =====================================================================
    | Template principal du footer
    |---------------------------------------------------------------------
    | Structure :
    | - Effet de glassmorphisme
    | - Copyright et lien
    =====================================================================
    -->
    <footer class="relative bg-gradient-to-r from-white via-gray-50 to-white border-t border-gray-200 py-6 mt-auto">
        
        <!-- Effet de glassmorphisme subtil -->
        <div class="absolute inset-0 bg-gradient-to-r from-[#0097b2]/5 via-transparent to-[#00b4d8]/5 pointer-events-none"></div>
        
        <div class="relative container mx-auto px-4">
          <div class="flex flex-row items-center gap-2">
            <p class="absolute left-1/2 -translate-x-1/2 text-sm text-gray-600 leading-relaxed text-center w-max max-w-full px-2">
              {{ t('copyright') || '© 2025 NeoStart.tech' }} 
              <a 
                href="https://neostart.tech/" 
                target="_blank" 
                rel="noopener noreferrer"
                class="text-[#0097b2] hover:text-[#00b4d8] transition-all duration-300 font-medium hover:underline decoration-2 underline-offset-2"
              >
                {{ t('company') || 'NeoStart.tech' }}
              </a> 
              - {{ t('rights') || 'Tous droits réservés.' }}
            </p>
            <div class="flex-1"></div>
            <div class="flex items-center justify-end w-1/6 min-w-[120px]">
              <div class="relative w-full max-w-[160px]">
                <button
                  class="flex items-center w-full px-3 py-2 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-[#0097b2] transition-all duration-200 group"
                  @click="toggleLangMenu"
                  @keydown.enter.prevent="toggleLangMenu"
                  @keydown.space.prevent="toggleLangMenu"
                  :aria-expanded="open"
                  :aria-haspopup="true"
                  type="button"
                  ref="langBtnRef"
                >
                  <span class="mr-2 flex items-center justify-center w-5 h-5 text-lg">
                    {{ langs.find((l: typeof langs[0]) => l.value === $i18n.locale)?.icon }}
                  </span>
                  <span class="text-xs font-medium text-gray-700">{{ langs.find((l: typeof langs[0]) => l.value === $i18n.locale)?.label }}</span>
                  <svg class="ml-auto w-4 h-4 text-gray-400 group-hover:text-[#0097b2] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div v-if="open" class="absolute z-10 mb-2 w-full bottom-full bg-white border border-gray-200 rounded-lg shadow-lg animate-fade-in-up" @keydown.esc="closeLangMenu" tabindex="-1" ref="langMenuRef">
                  <ul>
                    <li v-for="lang in langs" :key="lang.value">
                      <button
                        class="flex items-center w-full px-3 py-2 hover:bg-[#f0f9fa] focus:bg-[#e0f7fa] rounded transition"
                        @click="selectLang(lang.value)"
                        @keydown.enter.prevent="selectLang(lang.value)"
                        @keydown.space.prevent="selectLang(lang.value)"
                        :aria-current="locale === lang.value ? 'true' : 'false'"
                        type="button"
                      >
                        <span class="mr-2 flex items-center justify-center w-5 h-5 text-lg">
                          {{ lang.icon }}
                        </span>
                        <span class="text-xs font-medium text-gray-700">{{ lang.label }}</span>
                      </button>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
    </footer>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'
const open = ref(false)
const langBtnRef = ref<HTMLElement | null>(null)
const langMenuRef = ref<HTMLElement | null>(null)

const langs = [
  { value: 'fr', label: 'Français', icon: '🇫🇷' },
  { value: 'en', label: 'English', icon: '🇺🇸' },
  { value: 'zh', label: '中文', icon: '🇨🇳' }
]

const { t, locale } = useI18n()

function toggleLangMenu() {
  open.value = !open.value
  if (open.value) {
    setTimeout(() => langMenuRef.value?.focus(), 0)
  }
}
function closeLangMenu() {
  open.value = false
  langBtnRef.value?.focus()
}
function selectLang(val: string) {
  locale.value = val as typeof locale.value
  closeLangMenu()
}
function handleClickOutside(event: MouseEvent) {
  if (!open.value) return
  const menu = langMenuRef.value
  const btn = langBtnRef.value
  if (menu && !menu.contains(event.target as Node) && btn && !btn.contains(event.target as Node)) {
    closeLangMenu()
  }
}
onMounted(() => {
  document.addEventListener('mousedown', handleClickOutside)
})
onBeforeUnmount(() => {
  document.removeEventListener('mousedown', handleClickOutside)
})
</script>

<style scoped>
    /*
    | Styles spécifiques au composant AppFooter
    | Menu déroulant moderne et épuré pour la sélection de langue
    */
    .animate-fade-in-up {
      animation: fadeInUp 0.18s cubic-bezier(.4,0,.2,1);
    }
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
</style>



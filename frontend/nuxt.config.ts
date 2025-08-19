export default defineNuxtConfig({
  compatibilityDate: '2025-05-15',
  devtools: {
    enabled: true,
  },
  css: ['@/assets/css/tailwind.css'],
  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt',
    '@nuxtjs/i18n'
  ],
  vue: {
    compilerOptions: {
      isCustomElement: (tag) => false
    }
  },
  vite: {
    vue: {
      template: {
        compilerOptions: {
          isCustomElement: (tag) => false
        }
      }
    },
    define: {
      __VUE_OPTIONS_API__: true,
      __VUE_PROD_DEVTOOLS__: false
    }
  },
  i18n: {
    locales: [
      { code: 'fr', name: 'Français', file: 'fr.json' },
      { code: 'en', name: 'English', file: 'en.json' },
      { code: 'zh', name: '中文', file: 'zh.json' }
    ],
    langDir: 'locales/',
    defaultLocale: 'fr',
    strategy: 'no_prefix',
    compilation: {
      strictMessage: false,
      escapeHtml: false
    }
  },
  imports: {
    dirs: ['stores']
  },
  runtimeConfig: {
    public: {
      apiBase: 'http://localhost:8000/api', // backend-client
      nhsBase: 'http://localhost:8000/api/nhs'  // proxy NHS via backend-client
    }
  },
  ssr: false // Pour simplifier l'authentification côté client
})


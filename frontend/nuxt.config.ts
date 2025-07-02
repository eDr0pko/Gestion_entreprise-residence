export default defineNuxtConfig({
  compatibilityDate: '2025-05-15',
  devtools: {
    enabled: true,
  },
  css: ['@/assets/css/tailwind.css'],
  postcss: {
    plugins: {
      tailwindcss: {},
      autoprefixer: {},
    },
  },
  modules: [
    '@pinia/nuxt'
  ],
  imports: {
    dirs: ['stores'],
    presets: [
      {
        from: '~/stores/auth',
        imports: ['useAuthStore']
      }
    ]
  },
  runtimeConfig: {
    public: {
      apiBase: 'http://127.0.0.1:8000/api'
    }
  },
  ssr: false // Pour simplifier l'authentification côté client
})


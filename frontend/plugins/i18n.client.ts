export default defineNuxtPlugin(() => {
  const { $i18n, $localePath } = useNuxtApp()
  
  // Force loading of translation files
  if (process.client) {
    const loadLocaleMessages = async () => {
      try {
        const fr = await import('~/locales/fr.json')
        const en = await import('~/locales/en.json')
        const zh = await import('~/locales/zh.json')
        
        $i18n.setLocaleMessage('fr', fr.default || fr)
        $i18n.setLocaleMessage('en', en.default || en)
        $i18n.setLocaleMessage('zh', zh.default || zh)
        
        console.log('✅ i18n translations loaded successfully')
      } catch (error) {
        console.error('❌ Error loading i18n translations:', error)
      }
    }
    
    loadLocaleMessages()
  }
})



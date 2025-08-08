import { ref, computed, readonly } from 'vue'

// État global du thème
const isDark = ref(false)

export const useTheme = () => {
  const currentTheme = computed(() => isDark.value ? 'dark' : 'light')
  
  const toggleTheme = () => {
    isDark.value = !isDark.value
    updateDocument()
    saveToStorage()
  }
  
  const setTheme = (theme) => {
    isDark.value = theme === 'dark'
    updateDocument()
    saveToStorage()
  }
  
  const updateDocument = () => {
    if (process.client) {
      if (isDark.value) {
        document.documentElement.classList.add('dark')
      } else {
        document.documentElement.classList.remove('dark')
      }
    }
  }
  
  const saveToStorage = () => {
    if (process.client) {
      localStorage.setItem('theme', currentTheme.value)
    }
  }
  
  const initTheme = () => {
    if (process.client) {
      const savedTheme = localStorage.getItem('theme')
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
      
      if (savedTheme) {
        isDark.value = savedTheme === 'dark'
      } else {
        isDark.value = prefersDark
      }
      
      updateDocument()
    }
  }
  
  return {
    isDark: readonly(isDark),
    currentTheme,
    toggleTheme,
    setTheme,
    initTheme
  }
}



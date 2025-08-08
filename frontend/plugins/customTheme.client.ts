import { useAppSettings } from '~/composables/useAppSettings'

export default defineNuxtPlugin(() => {
  // Fonction pour appliquer les couleurs personnalisées
  const applyCustomColors = (primaryColor: string, secondaryColor: string) => {
    const root = document.documentElement

    // Convertir hex en RGB pour les transparences
    const hexToRgb = (hex: string) => {
      const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex)
      return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
      } : null
    }

    const primaryRgb = hexToRgb(primaryColor)
    const secondaryRgb = hexToRgb(secondaryColor)

    if (primaryRgb && secondaryRgb) {
      // Variables CSS personnalisées
      root.style.setProperty('--color-primary', primaryColor)
      root.style.setProperty('--color-secondary', secondaryColor)
      root.style.setProperty('--color-primary-rgb', `${primaryRgb.r}, ${primaryRgb.g}, ${primaryRgb.b}`)
      root.style.setProperty('--color-secondary-rgb', `${secondaryRgb.r}, ${secondaryRgb.g}, ${secondaryRgb.b}`)
      
      // Variantes pour les hovers et transparences
      root.style.setProperty('--color-primary-hover', `${primaryColor}dd`)
      root.style.setProperty('--color-secondary-hover', `${secondaryColor}dd`)
      root.style.setProperty('--color-primary-light', `${primaryColor}20`)
      root.style.setProperty('--color-secondary-light', `${secondaryColor}20`)
    }
  }

  // Appliquer au démarrage si disponible
  const { settings } = useAppSettings()
  
  watch(() => settings.value, (newSettings) => {
    if (newSettings.app_primary_color && newSettings.app_secondary_color) {
      applyCustomColors(newSettings.app_primary_color, newSettings.app_secondary_color)
    }
  }, { immediate: true })

  // Rendre la fonction disponible globalement
  return {
    provide: {
      applyCustomColors
    }
  }
})

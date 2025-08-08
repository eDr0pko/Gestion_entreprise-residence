import { computed } from 'vue'

/**
 * Composable pour gérer la personnalisation de l'application
 */
export const useAppTheme = () => {
  const { settings } = useAppSettings()

  /**
   * CSS variables calculées à partir des paramètres
   */
  const themeVariables = computed(() => {
    if (!settings.value) return {}

    return {
      '--color-primary': settings.value.primary_color || '#3B82F6',
      '--color-secondary': settings.value.secondary_color || '#10B981',
      '--color-accent': settings.value.accent_color || '#F59E0B',
      '--color-background': settings.value.background_color || '#F8FAFC',
      '--app-name': `"${settings.value.app_name || 'Gestion Résidence'}"`,
      '--company-name': `"${settings.value.company_name || 'Ma Société'}"`
    }
  })

  /**
   * Style object pour les composants Vue
   */
  const themeStyles = computed(() => {
    const vars = themeVariables.value
    const styleObj: Record<string, string> = {}
    
    Object.entries(vars).forEach(([key, value]) => {
      styleObj[key] = value
    })
    
    return styleObj
  })

  /**
   * Applique les variables CSS au document
   */
  const applyTheme = () => {
    if (process.client) {
      const root = document.documentElement
      Object.entries(themeVariables.value).forEach(([property, value]) => {
        root.style.setProperty(property, value)
      })
    }
  }

  /**
   * Classe CSS pour les couleurs du thème
   */
  const themeClasses = computed(() => {
    if (!settings.value) return ''
    
    const classes = []
    
    // Mode sombre activé
    if (settings.value.enable_dark_mode) {
      classes.push('theme-dark-enabled')
    }
    
    return classes.join(' ')
  })

  /**
   * Styles inline pour les éléments avec couleurs personnalisées
   */
  const primaryButtonStyle = computed(() => ({
    backgroundColor: settings.value?.primary_color || '#3B82F6',
    borderColor: settings.value?.primary_color || '#3B82F6'
  }))

  const secondaryButtonStyle = computed(() => ({
    backgroundColor: settings.value?.secondary_color || '#10B981',
    borderColor: settings.value?.secondary_color || '#10B981'
  }))

  const accentStyle = computed(() => ({
    color: settings.value?.accent_color || '#F59E0B'
  }))

  /**
   * Configuration du logo
   */
  const logoConfig = computed(() => ({
    src: settings.value?.app_logo || '/default-logo.png',
    alt: settings.value?.app_name || 'Logo'
  }))

  /**
   * Informations de l'application
   */
  const appInfo = computed(() => ({
    name: settings.value?.app_name || 'Gestion Résidence',
    tagline: settings.value?.app_tagline || 'Gérez votre résidence facilement',
    companyName: settings.value?.company_name || 'Ma Société'
  }))

  /**
   * Contenu des pages
   */
  const pageContent = computed(() => ({
    welcome: {
      title: settings.value?.welcome_title || 'Bienvenue',
      message: settings.value?.welcome_message || 'Bienvenue dans votre espace de gestion'
    },
    login: {
      title: settings.value?.login_title || 'Connexion',
      subtitle: settings.value?.login_subtitle || 'Accédez à votre espace'
    },
    register: {
      title: settings.value?.register_title || 'Inscription',
      subtitle: settings.value?.register_subtitle || 'Créez votre compte'
    }
  }))

  /**
   * Configuration des fonctionnalités
   */
  const features = computed(() => ({
    registrationEnabled: settings.value?.enable_registration ?? true,
    darkModeEnabled: settings.value?.enable_dark_mode ?? false,
    footerEnabled: settings.value?.show_footer ?? true
  }))

  /**
   * Informations de contact
   */
  const contactInfo = computed(() => ({
    email: settings.value?.contact_email || '',
    phone: settings.value?.contact_phone || '',
    address: settings.value?.contact_address || '',
    footerText: settings.value?.footer_text || '© 2024 Tous droits réservés'
  }))

  return {
    // État
    themeVariables,
    themeStyles,
    themeClasses,
    
    // Actions
    applyTheme,
    
    // Styles
    primaryButtonStyle,
    secondaryButtonStyle,
    accentStyle,
    
    // Configuration
    logoConfig,
    appInfo,
    pageContent,
    features,
    contactInfo
  }
}



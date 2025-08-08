<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
      <!-- Header Section -->
      <div class="text-center mb-12">
        <h1 class="text-5xl font-black text-gray-900 mb-4 tracking-tight">
          {{ $t('adminSettings.title') }}
        </h1>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">
          {{ $t('adminSettings.subtitle') }}
        </p>
      </div>

      <!-- Main Settings Card -->
      <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 overflow-hidden">
        <div class="p-8 sm:p-12">
          <form @submit.prevent="onSave" class="space-y-12">
            
            <!-- Identité Section -->
            <div class="space-y-8">
              <div class="flex items-center gap-4 pb-4 border-b border-gray-200">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-2 0H7m5 0v2a2 2 0 01-2 2H7a2 2 0 01-2-2v-2m7 0V9a2 2 0 00-2-2H7a2 2 0 00-2 2v6.5" />
                  </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $t('adminSettings.identity') }}</h2>
              </div>
              
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Nom du site -->
                <div class="space-y-3">
                  <label class="block text-sm font-semibold text-gray-700 uppercase tracking-wide">{{ $t('adminSettings.siteName') }}</label>
                  <input 
                    v-model="form.appName" 
                    type="text" 
                    class="w-full px-4 py-4 rounded-2xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 bg-gray-50 focus:bg-white text-lg font-semibold placeholder-gray-400" 
                    placeholder="Nom de votre plateforme" 
                    required 
                  />
                </div>

                <!-- Nom de société -->
                <div class="space-y-3">
                  <label class="block text-sm font-semibold text-gray-700 uppercase tracking-wide">{{ $t('adminSettings.companyName') }}</label>
                  <input 
                    v-model="form.companyName" 
                    type="text" 
                    class="w-full px-4 py-4 rounded-2xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 bg-gray-50 focus:bg-white text-lg font-semibold placeholder-gray-400" 
                    placeholder="Nom de votre société" 
                  />
                </div>
              </div>

              <!-- Tagline -->
              <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700 uppercase tracking-wide">{{ $t('adminSettings.tagline') }}</label>
                <input 
                  v-model="form.appTagline" 
                  type="text" 
                  class="w-full px-4 py-4 rounded-2xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 bg-gray-50 focus:bg-white text-lg font-semibold placeholder-gray-400" 
                  placeholder="Décrivez votre plateforme en quelques mots" 
                />
              </div>

              <!-- Logo Upload Zone -->
              <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700 uppercase tracking-wide">{{ $t('adminSettings.logo') }}</label>
                <div 
                  class="relative group cursor-pointer"
                  @dragover.prevent 
                  @drop.prevent="onDrop" 
                  @click="triggerFileInput"
                >
                  <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="onFileChange" />
                  
                  <div class="border-3 border-dashed border-blue-300 rounded-3xl bg-gradient-to-br from-blue-50 to-indigo-50 hover:from-blue-100 hover:to-indigo-100 transition-all duration-300 p-8 text-center group-hover:border-blue-400">
                    <div v-if="form.logoUrl" class="space-y-4">
                      <div class="flex justify-center">
                        <img 
                          :src="getLogoUrl(form.logoUrl)" 
                          alt="Logo" 
                          class="h-24 w-24 rounded-2xl object-contain border-2 border-white shadow-xl" 
                        />
                      </div>
                      <div class="space-y-2">
                        <p class="text-sm font-semibold text-blue-600">{{ $t('adminSettings.currentLogo') }}</p>
                        <p class="text-xs text-gray-500">{{ $t('adminSettings.replaceLogo') }}</p>
                      </div>
                    </div>
                    
                    <div v-else class="space-y-4">
                      <div class="flex justify-center">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-400 to-purple-500 rounded-2xl flex items-center justify-center">
                          <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                          </svg>
                        </div>
                      </div>
                      <div class="space-y-2">
                        <p class="text-lg font-semibold text-gray-700">{{ $t('adminSettings.addLogo') }}</p>
                        <p class="text-sm text-gray-500">{{ $t('adminSettings.dropOrBrowse') }}</p>
                        <p class="text-xs text-gray-400">{{ $t('adminSettings.logoFormats') }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Couleurs Section -->
            <div class="space-y-8">
              <div class="flex items-center gap-4 pb-4 border-b border-gray-200">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z" />
                  </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $t('adminSettings.colors') }}</h2>
              </div>
              
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="space-y-3">
                  <label class="block text-sm font-semibold text-gray-700 uppercase tracking-wide">{{ $t('adminSettings.primaryColor') }}</label>
                  <div class="flex items-center gap-3">
                    <input 
                      v-model="form.primaryColor" 
                      type="color" 
                      class="w-12 h-12 rounded-xl border-2 border-gray-200 cursor-pointer shadow-lg hover:shadow-xl transition-all duration-300"
                    />
                    <input 
                      v-model="form.primaryColor" 
                      type="text" 
                      class="flex-1 px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 font-mono text-sm"
                      placeholder="#3B82F6"
                    />
                  </div>
                </div>

                <div class="space-y-3">
                  <label class="block text-sm font-semibold text-gray-700 uppercase tracking-wide">{{ $t('adminSettings.secondaryColor') }}</label>
                  <div class="flex items-center gap-3">
                    <input 
                      v-model="form.secondaryColor" 
                      type="color" 
                      class="w-12 h-12 rounded-xl border-2 border-gray-200 cursor-pointer shadow-lg hover:shadow-xl transition-all duration-300"
                    />
                    <input 
                      v-model="form.secondaryColor" 
                      type="text" 
                      class="flex-1 px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 font-mono text-sm"
                      placeholder="#10B981"
                    />
                  </div>
                </div>

                <div class="space-y-3">
                  <label class="block text-sm font-semibold text-gray-700 uppercase tracking-wide">{{ $t('adminSettings.accentColor') }}</label>
                  <div class="flex items-center gap-3">
                    <input 
                      v-model="form.accentColor" 
                      type="color" 
                      class="w-12 h-12 rounded-xl border-2 border-gray-200 cursor-pointer shadow-lg hover:shadow-xl transition-all duration-300"
                    />
                    <input 
                      v-model="form.accentColor" 
                      type="text" 
                      class="flex-1 px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 font-mono text-sm"
                      placeholder="#F59E0B"
                    />
                  </div>
                </div>

                <div class="space-y-3">
                  <label class="block text-sm font-semibold text-gray-700 uppercase tracking-wide">{{ $t('adminSettings.backgroundColor') }}</label>
                  <div class="flex items-center gap-3">
                    <input 
                      v-model="form.backgroundColor" 
                      type="color" 
                      class="w-12 h-12 rounded-xl border-2 border-gray-200 cursor-pointer shadow-lg hover:shadow-xl transition-all duration-300"
                    />
                    <input 
                      v-model="form.backgroundColor" 
                      type="text" 
                      class="flex-1 px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 font-mono text-sm"
                      placeholder="#F8FAFC"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Contenu textuel Section -->
            <div class="space-y-8">
              <div class="flex items-center gap-4 pb-4 border-b border-gray-200">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                  </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $t('adminSettings.textContent') }}</h2>
              </div>
              
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Page d'accueil -->
                <div class="space-y-4">
                  <h3 class="text-lg font-semibold text-gray-800">{{ $t('adminSettings.homePage') }}</h3>
                  <div class="space-y-3">
                    <div>
                      <label class="block text-sm font-semibold text-gray-700 uppercase tracking-wide">{{ $t('adminSettings.welcomeTitle') }}</label>
                      <input 
                        v-model="form.welcomeTitle" 
                        type="text" 
                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 bg-gray-50 focus:bg-white font-semibold placeholder-gray-400" 
                        :placeholder="$t('adminSettings.welcomeTitlePlaceholder')" 
                      />
                    </div>
                    <div>
                      <label class="block text-sm font-semibold text-gray-700 uppercase tracking-wide">{{ $t('adminSettings.welcomeMessage') }}</label>
                      <textarea 
                        v-model="form.welcomeMessage" 
                        rows="3"
                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 bg-gray-50 focus:bg-white font-semibold placeholder-gray-400" 
                        :placeholder="$t('adminSettings.welcomeMessagePlaceholder')" 
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-end pt-8 border-t border-gray-200">
              <button 
                type="button"
                class="px-8 py-4 rounded-2xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition-all duration-300 text-lg"
                @click="resetForm"
              >
                {{ $t('adminSettings.reset') }}
              </button>
              <button 
                type="submit" 
                :disabled="loading"
                class="px-8 py-4 rounded-2xl bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold shadow-2xl hover:shadow-3xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 text-lg disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span v-if="loading" class="flex items-center gap-2">
                  <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>
                    <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" class="opacity-75"></path>
                  </svg>
                  {{ $t('button.saving') }}
                </span>
                <span v-else>{{ $t('adminSettings.saveChanges') }}</span>
              </button>
            </div>

            <!-- Feedback Messages -->
            <div v-if="error" class="bg-red-50 border-2 border-red-200 rounded-2xl p-6">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <p class="text-red-800 font-semibold">{{ $t('adminSettings.error') }}: {{ error }}</p>
              </div>
            </div>

            <div v-if="success" class="bg-green-50 border-2 border-green-200 rounded-2xl p-6">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <p class="text-green-800 font-semibold">{{ $t('adminSettings.success') }}</p>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>


<script setup lang="ts">
const { fetchSettings } = useAppSettings()

const loading = ref(false)
const error = ref('')
const success = ref(false)
const fileInput = ref<HTMLInputElement>()

const form = ref({
  appName: '',
  logoUrl: null as string | null,
  companyName: '',
  appTagline: '',
  primaryColor: '#3B82F6',
  secondaryColor: '#10B981',
  accentColor: '#F59E0B',
  backgroundColor: '#F8FAFC',
  enableRegistration: true,
  enableDarkMode: false,
  showFooter: true,
  welcomeTitle: '',
  welcomeMessage: '',
  loginTitle: '',
  loginSubtitle: '',
  registerTitle: '',
  registerSubtitle: '',
  contactEmail: '',
  contactPhone: '',
  contactAddress: '',
  footerText: ''
})

const getLogoUrl = (logoUrl: string | null) => {
  if (!logoUrl) return ''
  return logoUrl.startsWith('/') ? logoUrl : '/logos/' + logoUrl
}

const triggerFileInput = () => {
  fileInput.value?.click()
}

const onFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    handleFile(target.files[0])
  }
}

const onDrop = (event: DragEvent) => {
  const files = event.dataTransfer?.files
  if (files && files[0]) {
    handleFile(files[0])
  }
}

const handleFile = async (file: File) => {
  if (!file.type.startsWith('image/')) {
    error.value = 'Veuillez sélectionner un fichier image'
    return
  }

  if (file.size > 2 * 1024 * 1024) {
    error.value = 'Le fichier doit faire moins de 2MB'
    return
  }

  try {
    const formData = new FormData()
    formData.append('logo', file)

    const response = await $fetch<{ logoUrl: string }>('/api/settings/upload-logo', {
      method: 'POST',
      body: formData,
      baseURL: 'http://localhost:8000'
    })

    if (response.logoUrl) {
      form.value.logoUrl = response.logoUrl
      error.value = ''
    }
  } catch (err) {
    console.error('Erreur upload logo:', err)
    error.value = 'Erreur lors du téléchargement du logo'
  }
}

const resetForm = async () => {
  try {
    await loadSettings()
    success.value = false
    error.value = ''
  } catch (err) {
    error.value = 'Erreur lors du rechargement des paramètres'
  }
}

const loadSettings = async () => {
  try {
    const settings = await $fetch<any>('/api/settings', {
      baseURL: 'http://localhost:8000'
    })
    
    if (settings) {
      form.value = {
        appName: settings.app_name || '',
        logoUrl: settings.logo_url || null,
        companyName: settings.company_name || '',
        appTagline: settings.app_tagline || '',
        primaryColor: settings.primary_color || '#3B82F6',
        secondaryColor: settings.secondary_color || '#10B981',
        accentColor: settings.accent_color || '#F59E0B',
        backgroundColor: settings.background_color || '#F8FAFC',
        enableRegistration: settings.enable_registration ?? true,
        enableDarkMode: settings.enable_dark_mode ?? false,
        showFooter: settings.show_footer ?? true,
        welcomeTitle: settings.welcome_title || '',
        welcomeMessage: settings.welcome_message || '',
        loginTitle: settings.login_title || '',
        loginSubtitle: settings.login_subtitle || '',
        registerTitle: settings.register_title || '',
        registerSubtitle: settings.register_subtitle || '',
        contactEmail: settings.contact_email || '',
        contactPhone: settings.contact_phone || '',
        contactAddress: settings.contact_address || '',
        footerText: settings.footer_text || ''
      }
    }
  } catch (err) {
    console.error('Erreur chargement paramètres:', err)
    error.value = 'Erreur lors du chargement des paramètres'
  }
}

const onSave = async () => {
  loading.value = true
  error.value = ''
  success.value = false
  
  try {
    await $fetch('/api/settings', {
      method: 'POST',
      body: {
        app_name: form.value.appName || '',
        logo_url: form.value.logoUrl || '',
        company_name: form.value.companyName || '',
        app_tagline: form.value.appTagline || '',
        primary_color: form.value.primaryColor || '#3B82F6',
        secondary_color: form.value.secondaryColor || '#10B981',
        accent_color: form.value.accentColor || '#F59E0B',
        background_color: form.value.backgroundColor || '#F8FAFC',
        enable_registration: !!form.value.enableRegistration,
        enable_dark_mode: !!form.value.enableDarkMode,
        show_footer: !!form.value.showFooter,
        welcome_title: form.value.welcomeTitle || '',
        welcome_message: form.value.welcomeMessage || '',
        login_title: form.value.loginTitle || '',
        login_subtitle: form.value.loginSubtitle || '',
        register_title: form.value.registerTitle || '',
        register_subtitle: form.value.registerSubtitle || '',
        contact_email: form.value.contactEmail && form.value.contactEmail.includes('@') ? form.value.contactEmail : '',
        contact_phone: form.value.contactPhone || '',
        contact_address: form.value.contactAddress || '',
        footer_text: form.value.footerText || ''
      },
      baseURL: 'http://localhost:8000'
    })
    
    success.value = true
    setTimeout(() => {
      success.value = false
    }, 3000)
    
    await fetchSettings()
  } catch (err) {
    console.error('Erreur sauvegarde:', err)
    error.value = 'Erreur lors de la sauvegarde'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadSettings()
})
</script>



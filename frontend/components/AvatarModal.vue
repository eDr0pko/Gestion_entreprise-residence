<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl">
      <h3 class="text-xl font-bold text-gray-900 mb-6 text-center">Photo de profil</h3>
      
      <!-- Avatar actuel -->
      <div class="flex justify-center mb-6">
        <div class="relative">
          <div v-if="currentAvatarUrl" class="w-24 h-24 rounded-full overflow-hidden border-4 border-gray-200 shadow-lg">
            <img 
              :src="currentAvatarUrl" 
              :alt="`Photo de profil`"
              class="w-full h-full object-cover"
              @error="avatarError = true"
            />
          </div>
          <div v-else class="w-24 h-24 bg-gradient-to-br from-[#0097b2] to-[#008699] rounded-full flex items-center justify-center shadow-lg">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
          </div>
        </div>
      </div>

      <!-- Zone de drag & drop ou sélection de fichier -->
      <div class="mb-6">
        <div 
          class="border-2 border-dashed rounded-xl p-6 text-center transition-colors"
          :class="[
            dragover ? 'border-[#0097b2] bg-[#0097b2]/5' : 'border-gray-300',
            'hover:border-[#0097b2] hover:bg-[#0097b2]/5 cursor-pointer'
          ]"
          @dragover.prevent="dragover = true"
          @dragleave.prevent="dragover = false"
          @drop.prevent="handleDrop"
          @click="() => fileInput?.click()"
        >
          <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
          </svg>
          <p class="text-gray-600 mb-2">
            <span class="font-medium text-[#0097b2]">Cliquez pour parcourir</span>
            ou glissez une image ici
          </p>
          <p class="text-xs text-gray-500">JPG, PNG, GIF jusqu'à 2MB</p>
        </div>
        
        <input
          ref="fileInput"
          type="file"
          accept="image/*"
          class="hidden"
          @change="handleFileSelect"
        />
      </div>

      <!-- Aperçu de la nouvelle image -->
      <div v-if="selectedFile" class="mb-6">
        <h4 class="text-sm font-medium text-gray-700 mb-2">Aperçu :</h4>
        <div class="flex justify-center">
          <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-[#0097b2] shadow-md">
            <img 
              :src="previewUrl || undefined" 
              alt="Aperçu"
              class="w-full h-full object-cover"
            />
          </div>
        </div>
      </div>

      <!-- Message d'erreur -->
      <div v-if="error" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-sm text-red-600">{{ error }}</p>
      </div>

      <!-- Actions -->
      <div class="space-y-3">
        <!-- Upload -->
        <button
          v-if="selectedFile"
          @click="uploadAvatar"
          :disabled="uploading"
          class="w-full px-4 py-3 bg-gradient-to-r from-[#0097b2] to-[#008699] text-white font-semibold rounded-xl hover:from-[#008699] hover:to-[#007580] transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="uploading" class="flex items-center justify-center">
            <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Envoi en cours...
          </span>
          <span v-else>Modifier la photo</span>
        </button>

        <!-- Supprimer -->
        <button
          v-if="currentAvatarUrl && !selectedFile"
          @click="deleteAvatar"
          :disabled="deleting"
          class="w-full px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="deleting" class="flex items-center justify-center">
            <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Suppression...
          </span>
          <span v-else>Supprimer la photo</span>
        </button>

        <!-- Fermer -->
        <button
          @click="close"
          class="w-full px-4 py-3 text-gray-700 bg-gray-200 rounded-xl hover:bg-gray-300 transition-colors"
        >
          {{ selectedFile ? 'Annuler' : 'Fermer' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  currentAvatarUrl?: string | null
}

const props = defineProps<Props>()

const emit = defineEmits<{
  close: []
  success: [avatarUrl: string | null]
}>()

const authStore = useAuthStore()
const config = useRuntimeConfig()

// Référence au input file
const fileInput = ref<HTMLInputElement>()

// État réactif
const dragover = ref(false)
const selectedFile = ref<File | null>(null)
const previewUrl = ref<string | null>(null)
const uploading = ref(false)
const deleting = ref(false)
const error = ref('')
const avatarError = ref(false)

// Gestion des fichiers
const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (file) {
    processFile(file)
  }
}

const handleDrop = (event: DragEvent) => {
  dragover.value = false
  const file = event.dataTransfer?.files[0]
  if (file) {
    processFile(file)
  }
}

const processFile = (file: File) => {
  error.value = ''
  
  // Validation du fichier
  if (!file.type.startsWith('image/')) {
    error.value = 'Veuillez sélectionner une image'
    return
  }
  
  if (file.size > 2 * 1024 * 1024) { // 2MB
    error.value = 'L\'image ne doit pas dépasser 2MB'
    return
  }
  
  selectedFile.value = file
  
  // Créer l'aperçu
  const reader = new FileReader()
  reader.onload = (e) => {
    previewUrl.value = e.target?.result as string
  }
  reader.readAsDataURL(file)
}

// Upload de l'avatar
const uploadAvatar = async () => {
  if (!selectedFile.value) return
  
  try {
    uploading.value = true
    error.value = ''
    
    const formData = new FormData()
    formData.append('avatar', selectedFile.value)
    
    const response = await fetch(`${config.public.apiBase}/profile/avatar`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${authStore.token}`
      },
      body: formData
    })
    
    const data = await response.json()
    
    if (data.success) {
      // Mettre à jour l'utilisateur dans le store
      if (authStore.user) {
        (authStore.user as any).photo_profil = data.data.photo_profil
      }
      
      emit('success', data.data.avatar_url)
      close()
    } else {
      error.value = data.message || 'Erreur lors de l\'upload'
    }
  } catch (err: any) {
    console.error('Erreur lors de l\'upload d\'avatar:', err)
    error.value = 'Erreur lors de l\'upload de l\'image'
  } finally {
    uploading.value = false
  }
}

// Suppression de l'avatar
const deleteAvatar = async () => {
  try {
    deleting.value = true
    error.value = ''
    
    const response = await fetch(`${config.public.apiBase}/profile/avatar`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${authStore.token}`
      }
    })
    
    const data = await response.json()
    
    if (data.success) {
      // Mettre à jour l'utilisateur dans le store
      if (authStore.user) {
        (authStore.user as any).photo_profil = null
      }
      
      emit('success', null)
      close()
    } else {
      error.value = data.message || 'Erreur lors de la suppression'
    }
  } catch (err: any) {
    console.error('Erreur lors de la suppression d\'avatar:', err)
    error.value = 'Erreur lors de la suppression de l\'image'
  } finally {
    deleting.value = false
  }
}

const close = () => {
  // Nettoyer l'aperçu
  if (previewUrl.value) {
    URL.revokeObjectURL(previewUrl.value)
  }
  emit('close')
}

// Nettoyage lors de la destruction du composant
onUnmounted(() => {
  if (previewUrl.value) {
    URL.revokeObjectURL(previewUrl.value)
  }
})
</script>

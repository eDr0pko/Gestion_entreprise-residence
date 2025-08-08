/*
==========================================================================
| Composant AvatarModal
|--------------------------------------------------------------------------
| Permet à l'utilisateur d'ajouter, modifier ou supprimer sa photo de profil
| - Affiche l'avatar actuel ou un avatar par défaut
| - Permet l'upload par sélection, drag & drop ou prise directe via caméra
| - Affiche un aperçu avant validation
| - Gère les erreurs et la suppression
| - Utilise le store d'authentification pour la synchronisation
|--------------------------------------------------------------------------
| Auteur : milei
| Date   : 2025-07-22
==========================================================================
*/

<template>
  <!--
  =====================================================================
  | Template principal du modal d'avatar
  |---------------------------------------------------------------------
  | Structure :
  | - Affichage avatar actuel
  | - Zone drag & drop / sélection / prise photo
  | - Aperçu de la nouvelle image
  | - Messages d'erreur
  | - Actions (upload, suppression, fermeture)
  =====================================================================
  -->

  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl">
      <h3 class="text-xl font-bold text-gray-900 mb-6 text-center">{{ $t('components.avatarModal.title') }}</h3>
      
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
            <span class="font-medium text-[#0097b2]">{{ $t('components.avatarModal.upload') }}</span>
            {{ $t('components.avatarModal.orDragDrop') }}
          </p>
          <p class="text-xs text-gray-500">{{ $t('components.avatarModal.fileInfo') }}</p>
        </div>
        
        <input
          ref="fileInput"
          type="file"
          accept="image/*"
          class="hidden"
          @change="handleFileSelect"
        />

      <!-- Option pour prendre une photo directement -->
      <div class="mb-4 flex justify-center space-x-3">
        <button
          type="button"
          class="px-4 py-2 bg-gradient-to-r from-[#0097b2] to-[#008699] text-white rounded-xl shadow hover:from-[#008699] hover:to-[#007580] transition-all duration-300 flex items-center"
          @click="openCameraModal('environment')"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
          {{ $t('components.avatarModal.takePhoto') }}
        </button>
      </div>
      </div>

      <!-- Aperçu de la nouvelle image -->
      <div v-if="selectedFile" class="mb-6">
        <h4 class="text-sm font-medium text-gray-700 mb-2">{{ $t('components.avatarModal.preview') }}</h4>
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
        <p class="text-sm text-red-600">{{ $t(error) }}</p>
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
            {{ $t('components.avatarModal.saving') }}
          </span>
          <span v-else>{{ $t('components.avatarModal.change') }}</span>
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
            {{ $t('components.avatarModal.deleting') }}
          </span>
          <span v-else>{{ $t('components.avatarModal.remove') }}</span>
        </button>

        <!-- Fermer -->
        <button
          @click="close"
          class="w-full px-4 py-3 text-gray-700 bg-gray-200 rounded-xl hover:bg-gray-300 transition-colors"
        >
          {{ selectedFile ? $t('components.avatarModal.cancel') : $t('components.avatarModal.close') }}
        </button>
      </div>
    </div>
    
    <!-- Modal de caméra -->
    <div v-if="showCameraModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-60 p-4">
      <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl">
        <h3 class="text-xl font-bold text-gray-900 mb-4 text-center">
          {{ cameraMode === 'user' ? 'Selfie' : 'Prendre une photo' }}
        </h3>
        
        <!-- Zone de prévisualisation caméra -->
        <div class="relative mb-4">
          <video
            ref="videoElement"
            v-show="!capturedPhoto"
            class="w-full h-64 object-cover rounded-lg bg-gray-900"
            autoplay
            muted
            playsinline
          ></video>
          
          <!-- Photo capturée -->
          <div v-if="capturedPhoto" class="w-full h-64 flex items-center justify-center bg-gray-100 rounded-lg">
            <img 
              :src="capturedPhoto" 
              alt="Photo capturée"
              class="max-w-full max-h-full object-contain rounded-lg"
            />
          </div>
          
          <!-- Canvas caché pour la capture -->
          <canvas ref="canvasElement" class="hidden"></canvas>
        </div>

        <!-- Message d'erreur caméra -->
        <div v-if="cameraError" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-sm text-red-600">{{ cameraError }}</p>
        </div>

        <!-- Contrôles de la caméra -->
        <div class="flex justify-center space-x-3 mb-4">
          <button
            v-if="!capturedPhoto"
            @click="capturePhoto"
            :disabled="!cameraStream"
            class="px-6 py-3 bg-gradient-to-r from-[#0097b2] to-[#008699] text-white rounded-xl shadow hover:from-[#008699] hover:to-[#007580] transition-all duration-300 disabled:opacity-50 flex items-center"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            Capturer
          </button>
          
          <button
            v-if="capturedPhoto"
            @click="retakePhoto"
            class="px-4 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-xl shadow hover:from-gray-600 hover:to-gray-700 transition-all duration-300 flex items-center"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Reprendre
          </button>
          
          <button
            v-if="capturedPhoto"
            @click="useCapturedPhoto"
            class="px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl shadow hover:from-green-600 hover:to-green-700 transition-all duration-300 flex items-center"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Utiliser
          </button>
        </div>

        <!-- Fermer le modal caméra -->
        <button
          @click="closeCameraModal"
          class="w-full px-4 py-3 text-gray-700 bg-gray-200 rounded-xl hover:bg-gray-300 transition-colors"
        >
          Annuler
        </button>
      </div>
    </div>
  </div>
</template>


<script setup lang="ts">
  /*
  ==========================================================================
  | Script principal du composant AvatarModal
  |--------------------------------------------------------------------------
  | - Gestion de l'upload, suppression et prise de photo
  | - Synchronisation avec le store utilisateur
  | - Validation et aperçu de l'image
  | - Nettoyage des ressources
  ==========================================================================
  */

  // ----------------------------------------------------------------------
  // Gestion de la caméra moderne avec getUserMedia
  // ----------------------------------------------------------------------

  // Ouvre le modal de caméra avec le mode spécifié
  const openCameraModal = async (facingMode: 'environment' | 'user') => {
    cameraMode.value = facingMode
    showCameraModal.value = true
    cameraError.value = ''
    capturedPhoto.value = null
    
    // Attendre que le DOM soit mis à jour
    await nextTick()
    
    try {
      // Demander l'accès à la caméra
      const constraints: MediaStreamConstraints = {
        video: {
          facingMode: facingMode,
          width: { ideal: 640 },
          height: { ideal: 480 }
        },
        audio: false
      }

      const stream = await navigator.mediaDevices.getUserMedia(constraints)
      cameraStream.value = stream
      
      if (videoElement.value) {
        videoElement.value.srcObject = stream
      }
    } catch (err: any) {
      console.error('Erreur accès caméra:', err)
      cameraError.value = 'Impossible d\'accéder à la caméra. Vérifiez les permissions.'
      // Fallback vers input file avec capture pour mobile
      if (isMobile()) {
        closeCameraModal()
        createCameraInput(facingMode)
      }
    }
  }

  // Capture une photo depuis le stream vidéo
  const capturePhoto = () => {
    if (!videoElement.value || !canvasElement.value || !cameraStream.value) return
    
    const video = videoElement.value
    const canvas = canvasElement.value
    const context = canvas.getContext('2d')
    
    if (!context) return
    
    // Ajuster la taille du canvas à la vidéo
    canvas.width = video.videoWidth
    canvas.height = video.videoHeight
    
    // Dessiner l'image sur le canvas
    context.drawImage(video, 0, 0)
    
    // Convertir en data URL
    capturedPhoto.value = canvas.toDataURL('image/jpeg', 0.8)
  }

  // Reprendre une photo
  const retakePhoto = () => {
    capturedPhoto.value = null
  }

  // Utiliser la photo capturée
  const useCapturedPhoto = () => {
    if (!capturedPhoto.value) return
    
    // Convertir data URL en blob puis en File
    fetch(capturedPhoto.value)
      .then(res => res.blob())
      .then(blob => {
        const file = new File([blob], 'camera-capture.jpg', { type: 'image/jpeg' })
        processFile(file)
        closeCameraModal()
      })
      .catch(err => {
        console.error('Erreur conversion photo:', err)
        cameraError.value = 'Erreur lors du traitement de la photo'
      })
  }

  // Ferme le modal de caméra et arrête le stream
  const closeCameraModal = () => {
    if (cameraStream.value) {
      cameraStream.value.getTracks().forEach(track => track.stop())
      cameraStream.value = null
    }
    showCameraModal.value = false
    capturedPhoto.value = null
    cameraError.value = ''
  }

  // Détecte si on est sur mobile
  const isMobile = () => {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
  }

  // ----------------------------------------------------------------------
  // Fonction : createCameraInput
  // Crée un input file avec capture camera selon le type demandé
  // ----------------------------------------------------------------------

  function createCameraInput(facingMode: 'environment' | 'user') {
    // Supprime l'ancien input
    if (fileInput.value) {
      fileInput.value.value = ''
      fileInput.value.remove()
    }
    // Crée un nouvel input avec capture
    const input = document.createElement('input')
    input.type = 'file'
    input.accept = 'image/*'
    
    // Définit le type de caméra selon le mode
    if (facingMode === 'user') {
      input.setAttribute('capture', 'user') // Caméra frontale pour selfie
    } else {
      input.setAttribute('capture', 'environment') // Caméra arrière
    }
    
    input.className = 'hidden'
    input.onchange = (event) => handleFileSelect(event)
    
    // Ajoute au DOM
    fileInput.value = input as HTMLInputElement
    document.body.appendChild(input)
    input.click()
    
    // Nettoie après sélection
    input.addEventListener('change', () => {
      setTimeout(() => {
        input.remove()
      }, 1000)
    })
  }

  // ----------------------------------------------------------------------
  // Imports et initialisations
  // ----------------------------------------------------------------------

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

  // Référence au input file et éléments caméra
  const fileInput = ref<HTMLInputElement>()
  const videoElement = ref<HTMLVideoElement>()
  const canvasElement = ref<HTMLCanvasElement>()

  // État réactif
  const dragover = ref(false)
  const selectedFile = ref<File | null>(null)
  const previewUrl = ref<string | null>(null)
  const uploading = ref(false)
  const deleting = ref(false)
  const error = ref('')
  const avatarError = ref(false)
  
  // État de la caméra
  const showCameraModal = ref(false)
  const cameraMode = ref<'environment' | 'user'>('environment')
  const cameraStream = ref<MediaStream | null>(null)
  const capturedPhoto = ref<string | null>(null)
  const cameraError = ref('')

  // ----------------------------------------------------------------------
  // Fonction : handleFileSelect
  // Gère la sélection d'un fichier image
  // ----------------------------------------------------------------------

  const handleFileSelect = (event: Event) => {
    const target = event.target as HTMLInputElement
    const file = target.files?.[0]
    if (file) {
      processFile(file)
    }
  }

  // ----------------------------------------------------------------------
  // Fonction : handleDrop
  // Gère le drag & drop d'un fichier image
  // ----------------------------------------------------------------------

  const handleDrop = (event: DragEvent) => {
    dragover.value = false
    const file = event.dataTransfer?.files[0]
    if (file) {
      processFile(file)
    }
  }

  // ----------------------------------------------------------------------
  // Fonction : processFile
  // Valide et génère l'aperçu de l'image sélectionnée
  // ----------------------------------------------------------------------

  const processFile = (file: File) => {
    error.value = ''
    
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

  // ----------------------------------------------------------------------
  // Fonction : uploadAvatar
  // Upload l'image sélectionnée vers le backend
  // ----------------------------------------------------------------------

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
          authStore.updateUser({ photo_profil: data.data.photo_profil })
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

  // ----------------------------------------------------------------------
  // Fonction : deleteAvatar
  // Supprime l'avatar utilisateur côté backend
  // ----------------------------------------------------------------------

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
          authStore.updateUser({ photo_profil: null })
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

  // ----------------------------------------------------------------------
  // Fonction : close
  // Ferme le modal et nettoie toutes les ressources
  // ----------------------------------------------------------------------

  const close = () => {
    if (previewUrl.value) {
      URL.revokeObjectURL(previewUrl.value)
    }
    closeCameraModal() // Nettoie aussi la caméra
    emit('close')
  }

  // ----------------------------------------------------------------------
  // Hook : onUnmounted
  // Nettoie toutes les ressources à la destruction du composant
  // ----------------------------------------------------------------------

  onUnmounted(() => {
    if (previewUrl.value) {
      URL.revokeObjectURL(previewUrl.value)
    }
    closeCameraModal()
  })

</script>



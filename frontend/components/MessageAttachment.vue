<template>
  <div class="mt-2 space-y-2">
    <div v-if="fichiers.length === 0" class="text-xs text-gray-500 italic">
      Aucun fichier attaché
    </div>
    
    <div
      v-for="fichier in fichiers"
      :key="fichier.id_fichier"
      class="relative group"
    >
      <!-- Images : affichage en tant qu'image -->
      <div v-if="isImage(fichier)" class="relative">
        <div 
          class="relative rounded-lg overflow-hidden bg-gray-100 transition-all duration-200 hover:shadow-lg cursor-pointer image-container"
          :class="{ 'max-w-sm': true }"
          @click="openImageModal(fichier)"
          tabindex="0"
          @keydown.enter="openImageModal(fichier)"
          @keydown.space.prevent="openImageModal(fichier)"
        >
          <!-- Loading state -->
          <div 
            v-if="!imageDataUrls[fichier.id_fichier] && !imageErrors[fichier.id_fichier]"
            class="aspect-video bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center min-h-[120px]"
          >
            <div class="text-center p-4">
              <svg class="w-6 h-6 mx-auto mb-2 animate-spin text-gray-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <p class="text-xs text-gray-500">Chargement...</p>
            </div>
          </div>

          <!-- Image loaded -->
          <div v-else-if="imageDataUrls[fichier.id_fichier]" class="relative group">
            <img 
              :src="imageDataUrls[fichier.id_fichier]"
              :alt="fichier.nom_original"
              class="w-full h-auto max-w-full object-contain rounded-lg image-shadow transition-transform"
              :style="{ maxHeight: '300px' }"
              loading="lazy"
              @load="onImageLoad(fichier.id_fichier)"
              @error="onImageError(fichier.id_fichier)"
            />
            
            <!-- Overlay au hover -->
            <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-all duration-200 flex items-center justify-center rounded-lg">
              <div class="bg-white/90 backdrop-blur-sm text-gray-900 px-4 py-2 rounded-full text-sm font-medium shadow-lg transform scale-95 group-hover:scale-100 transition-transform">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                </svg>
                Agrandir
              </div>
            </div>
            
            <!-- Nom du fichier overlay en bas -->
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent p-3 rounded-b-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200">
              <p class="text-white text-sm font-medium truncate">
                {{ fichier.nom_original }}
              </p>
              <p class="text-white/80 text-xs">
                {{ formatFileSize(fichier.taille_fichier) }}
              </p>
            </div>
          </div>

          <!-- Error state -->
          <div 
            v-else
            class="min-h-[120px] bg-gradient-to-br from-red-50 to-red-100 flex items-center justify-center cursor-pointer rounded-lg border border-red-200"
          >
            <div class="text-center p-4">
              <svg class="w-8 h-8 mx-auto mb-2 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              <p class="text-sm font-medium text-red-700 mb-1">
                {{ fichier.nom_original }}
              </p>
              <p class="text-xs text-red-600 mb-3">
                Erreur de chargement
              </p>
              <div class="space-y-2">
                <button 
                  @click="retryImageLoad(fichier.id_fichier)"
                  class="text-xs bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition-colors mr-2"
                >
                  🔄 Réessayer
                </button>
                <button 
                  @click="forceCanvasConversion(fichier.id_fichier)"
                  class="text-xs bg-purple-500 text-white px-3 py-1 rounded hover:bg-purple-600 transition-colors mr-2"
                >
                  🎨 Conversion forcée
                </button>
                <button 
                  @click="downloadImageFallback(fichier.id_fichier)"
                  class="text-xs bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition-colors mr-2"
                >
                  💾 Télécharger fichier
                </button>
                <button 
                  @click="$emit('download-file', fichier.id_fichier)"
                  class="text-xs bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition-colors"
                >
                  📥 Télécharger (émit)
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- PDFs : affichage spécial -->
      <div v-else-if="isPDF(fichier)" 
        @click="$emit('download-file', fichier.id_fichier)"
        class="flex items-center p-3 rounded-lg border-2 border-dashed cursor-pointer transition-colors"
        :class="isCurrentUser 
          ? 'border-white/30 bg-white/10 hover:bg-white/20' 
          : 'border-red-200 bg-red-50 hover:bg-red-100'"
      >
        <div class="flex-shrink-0 mr-3">
          <div class="w-10 h-12 bg-red-500 rounded flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
            </svg>
          </div>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium" :class="isCurrentUser ? 'text-white' : 'text-gray-900'">
            {{ fichier.nom_original }}
          </p>
          <p class="text-xs" :class="isCurrentUser ? 'text-white/70' : 'text-gray-500'">
            PDF • {{ formatFileSize(fichier.taille_fichier) }}
          </p>
        </div>
        <svg class="w-4 h-4 flex-shrink-0 ml-2" :class="isCurrentUser ? 'text-white/70' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
        </svg>
      </div>

      <!-- Documents Office -->
      <div v-else-if="isOfficeDoc(fichier)" 
        @click="$emit('download-file', fichier.id_fichier)"
        class="flex items-center p-3 rounded-lg border cursor-pointer transition-colors"
        :class="isCurrentUser 
          ? 'border-white/30 bg-white/10 hover:bg-white/20' 
          : 'border-blue-200 bg-blue-50 hover:bg-blue-100'"
      >
        <div class="flex-shrink-0 mr-3">
          <div class="w-8 h-10 bg-blue-500 rounded flex items-center justify-center">
            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
            </svg>
          </div>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium truncate" :class="isCurrentUser ? 'text-white' : 'text-gray-900'">
            {{ fichier.nom_original }}
          </p>
          <p class="text-xs" :class="isCurrentUser ? 'text-white/70' : 'text-gray-500'">
            {{ getFileTypeLabel(fichier) }} • {{ formatFileSize(fichier.taille_fichier) }}
          </p>
        </div>
        <svg class="w-4 h-4 flex-shrink-0 ml-2" :class="isCurrentUser ? 'text-white/70' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
        </svg>
      </div>

      <!-- Archives -->
      <div v-else-if="isArchive(fichier)" 
        @click="$emit('download-file', fichier.id_fichier)"
        class="flex items-center p-3 rounded-lg border cursor-pointer transition-colors"
        :class="isCurrentUser 
          ? 'border-white/30 bg-white/10 hover:bg-white/20' 
          : 'border-purple-200 bg-purple-50 hover:bg-purple-100'"
      >
        <div class="flex-shrink-0 mr-3">
          <div class="w-8 h-8 bg-purple-500 rounded flex items-center justify-center">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium truncate" :class="isCurrentUser ? 'text-white' : 'text-gray-900'">
            {{ fichier.nom_original }}
          </p>
          <p class="text-xs" :class="isCurrentUser ? 'text-white/70' : 'text-gray-500'">
            Archive • {{ formatFileSize(fichier.taille_fichier) }}
          </p>
        </div>
        <svg class="w-4 h-4 flex-shrink-0 ml-2" :class="isCurrentUser ? 'text-white/70' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
        </svg>
      </div>

      <!-- Autres fichiers -->
      <div v-else 
        @click="$emit('download-file', fichier.id_fichier)"
        class="flex items-center p-2 rounded cursor-pointer transition-colors"
        :class="isCurrentUser 
          ? 'bg-white/20 hover:bg-white/30' 
          : 'bg-gray-50 hover:bg-gray-100'"
      >
        <div class="flex-shrink-0 mr-2">
          <svg class="w-5 h-5" :class="isCurrentUser ? 'text-white' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium truncate" :class="isCurrentUser ? 'text-white' : 'text-gray-900'">
            {{ fichier.nom_original }}
          </p>
          <p class="text-xs" :class="isCurrentUser ? 'text-white/70' : 'text-gray-500'">
            {{ formatFileSize(fichier.taille_fichier) }}
          </p>
        </div>
        <svg class="w-4 h-4 flex-shrink-0 ml-2" :class="isCurrentUser ? 'text-white' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
        </svg>
      </div>
    </div>

  </div>

  <!-- Modal pour afficher l'image en grand -->
  <Teleport to="body">
    <Transition name="fade">
      <div
        v-if="showImageModal && modalImage"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4"
        @click="showImageModal = false"
      >
        <div class="relative max-w-[95vw] max-h-[95vh] flex flex-col" @click.stop>
          <!-- Header avec informations et actions -->
          <div class="flex items-center justify-between mb-4 text-white">
            <div class="flex-1 min-w-0">
              <h3 class="text-lg font-medium truncate">{{ modalImage.nom_original }}</h3>
              <p class="text-sm text-gray-300">{{ formatFileSize(modalImage.taille_fichier) }}</p>
            </div>
            <div class="flex items-center space-x-2 ml-4">
              <!-- Bouton télécharger -->
              <button
                @click="$emit('download-file', modalImage.id_fichier)"
                class="p-2 text-white hover:text-gray-300 transition-colors bg-black/50 rounded-full hover:bg-black/70"
                title="Télécharger"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m4 2.5V19a2 2 0 01-2 2H5a2 2 0 01-2-2v-2.5"></path>
                </svg>
              </button>
              
              <!-- Bouton fermer -->
              <button
                @click="showImageModal = false"
                class="p-2 text-white hover:text-gray-300 transition-colors bg-black/50 rounded-full hover:bg-black/70"
                title="Fermer"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
          </div>
          
          <!-- Container de l'image -->
          <div class="flex-1 flex items-center justify-center min-h-0">
            <!-- Image en grand -->
            <img
              v-if="imageDataUrls[modalImage.id_fichier]"
              :src="imageDataUrls[modalImage.id_fichier]"
              :alt="modalImage.nom_original"
              class="max-w-full max-h-full object-contain rounded-lg shadow-2xl"
              style="max-height: calc(95vh - 120px);"
            />
            
            <!-- Fallback si l'image n'est pas chargée -->
            <div v-else class="bg-gray-800 p-8 rounded-lg text-white text-center">
              <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              <p class="text-lg font-medium mb-2">Image non disponible</p>
              <p class="text-gray-400 mb-4">Impossible de charger l'image</p>
              <button
                @click="$emit('download-file', modalImage.id_fichier)"
                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 rounded text-white transition-colors"
              >
                Télécharger quand même
              </button>
            </div>
          </div>
        </div>
        
        <!-- Instructions en bas -->
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-white/70 text-sm text-center">
          <p>Cliquez en dehors de l'image ou appuyez sur Échap pour fermer</p>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
  interface FichierMessage {
    id_fichier: number
    nom_original: string
    type_fichier: string
    taille_fichier: number
  }

  interface Props {
    fichiers: FichierMessage[]
    isCurrentUser: boolean
  }

  const props = defineProps<Props>()

  const emit = defineEmits<{
    'download-file': [fichierId: number]
  }>()

  // Configuration
  const config = useRuntimeConfig()
  const authStore = useAuthStore()

  // État pour la modal d'image
  const showImageModal = ref(false)
  const modalImage = ref<FichierMessage | null>(null)

  // Cache des images chargées
  const imageDataUrls = ref<Record<number, string>>({})
  const imageErrors = ref<Record<number, boolean>>({})

  // Fonction pour tester la connectivité de l'API
  const testApiConnectivity = async () => {
    try {
      const response = await fetch(`${config.public.apiBase}/conversations`, {
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Accept': 'application/json'
        }
      })
      return response.ok
    } catch (error) {
      return false
    }
  }

  // Fonction pour charger une image avec authentification
  const loadImageWithAuth = async (fichierId: number) => {
    if (imageDataUrls.value[fichierId] || imageErrors.value[fichierId]) {
      return // Déjà chargée ou en erreur
    }
    
    // Test de connectivité avant de charger l'image
    const apiOk = await testApiConnectivity()
    if (!apiOk) {
      imageErrors.value[fichierId] = true
      return
    }
    
    try {
      // Essayer plusieurs URLs/méthodes en cas d'échec
      const attempts = [
        { url: `${config.public.apiBase}/files/${fichierId}?inline=1`, method: 'inline' },
        { url: `${config.public.apiBase}/files/${fichierId}`, method: 'direct' },
        { url: `${config.public.apiBase}/files/${fichierId}?format=jpeg`, method: 'format' }
      ]
      
      for (const attempt of attempts) {
        try {
          const headers = {
            'Authorization': `Bearer ${authStore.token}`,
            'Accept': 'image/*,*/*'
          }
          
          const response = await fetch(attempt.url, { headers })

          if (!response.ok) {
            continue // Essayer la méthode suivante
          }

          const blob = await response.blob()
          
          if (blob.size === 0) {
            continue
          }

          // Diagnostic approfondi du blob
          await diagnoseBlobContent(fichierId, blob)
          
          // Tenter de réparer l'image si la signature est corrompue
          const repairedBlob = await attemptImageRepair(fichierId, blob)
          const finalBlob = repairedBlob || blob
          
          const dataUrl = URL.createObjectURL(finalBlob)
          imageDataUrls.value[fichierId] = dataUrl
          
          // Test immédiat de l'URL créée
          const isValid = await testImageValidity(fichierId, dataUrl)
          if (isValid) {
            return // Succès complet
          } else {
            URL.revokeObjectURL(dataUrl)
            delete imageDataUrls.value[fichierId]
            
            // Si c'est la dernière tentative, essayer la conversion Canvas
            if (attempt === attempts[attempts.length - 1]) {
              const canvasBlob = await convertImageViaCanvas(fichierId, finalBlob)
              if (canvasBlob) {
                const canvasDataUrl = URL.createObjectURL(canvasBlob)
                const canvasValid = await testImageValidity(fichierId, canvasDataUrl)
                if (canvasValid) {
                  imageDataUrls.value[fichierId] = canvasDataUrl
                  return
                } else {
                  URL.revokeObjectURL(canvasDataUrl)
                }
              }
            }
            
            continue // Essayer la méthode suivante
          }
          
        } catch (attemptError: any) {
          // Continuer avec la méthode suivante
        }
      }
      
      // Toutes les tentatives ont échoué
      throw new Error('Toutes les méthodes de chargement ont échoué')
    } catch (error: any) {
      imageErrors.value[fichierId] = true
    }
  }

  // Fonction pour réessayer de charger une image avec diagnostic avancé
  const retryImageLoad = async (fichierId: number) => {
    // Nettoyer l'ancienne URL pour éviter les fuites mémoire
    if (imageDataUrls.value[fichierId]) {
      URL.revokeObjectURL(imageDataUrls.value[fichierId])
      delete imageDataUrls.value[fichierId]
    }
    
    // Supprimer l'erreur précédente
    delete imageErrors.value[fichierId]
    
    try {
      await loadImageWithAuth(fichierId)
      
      // Si succès, vérifier que l'image est réellement valide
      const isValid = await testImageValidity(fichierId, imageDataUrls.value[fichierId])
      if (!isValid) {
        throw new Error('Image chargée mais invalide pour le navigateur')
      }
    } catch (error) {
      imageErrors.value[fichierId] = true
    }
  }

  // Fonction de téléchargement de secours
  const downloadImageFallback = async (fichierId: number) => {
    try {
      // Utiliser directement l'API de téléchargement sans inline
      const response = await fetch(`${config.public.apiBase}/files/${fichierId}`, {
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Accept': '*/*'
        }
      })
      
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`)
      }
      
      const blob = await response.blob()
      const url = URL.createObjectURL(blob)
      
      // Créer un lien de téléchargement temporaire
      const a = document.createElement('a')
      a.href = url
      a.download = `image_${fichierId}.jpg`
      document.body.appendChild(a)
      a.click()
      document.body.removeChild(a)
      URL.revokeObjectURL(url)
    } catch (error) {
      // Échec silencieux
    }
  }

  // Fonction de conversion canvas pour images très corrompues
  const convertImageViaCanvas = async (fichierId: number, blob: Blob): Promise<Blob | null> => {
    return new Promise((resolve) => {
      try {
        const img = new Image()
        const canvas = document.createElement('canvas')
        const ctx = canvas.getContext('2d')
        
        if (!ctx) {
          resolve(null)
          return
        }
        
        img.onload = () => {
          try {
            canvas.width = img.naturalWidth
            canvas.height = img.naturalHeight
            
            // Dessiner l'image sur le canvas
            ctx.drawImage(img, 0, 0)
            
            // Convertir en nouveau blob JPEG
            canvas.toBlob((newBlob) => {
              URL.revokeObjectURL(img.src)
              resolve(newBlob)
            }, 'image/jpeg', 0.9)
          } catch (canvasError) {
            URL.revokeObjectURL(img.src)
            resolve(null)
          }
        }
        
        img.onerror = () => {
          URL.revokeObjectURL(img.src)
          resolve(null)
        }
        
        // Timeout de sécurité
        setTimeout(() => {
          URL.revokeObjectURL(img.src)
          resolve(null)
        }, 10000)
        
        img.src = URL.createObjectURL(blob)
      } catch (error) {
        resolve(null)
      }
    })
  }

  // Fonction pour forcer la conversion Canvas depuis l'interface
  const forceCanvasConversion = async (fichierId: number) => {
    try {
      // Obtenir d'abord le blob original
      const response = await fetch(`${config.public.apiBase}/files/${fichierId}?inline=1`, {
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Accept': 'image/*,*/*'
        }
      })
      
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`)
      }
      
      const blob = await response.blob()
      
      // Essayer d'abord la réparation
      const repairedBlob = await attemptImageRepair(fichierId, blob)
      const sourceBlob = repairedBlob || blob
      
      // Puis conversion Canvas
      const canvasBlob = await convertImageViaCanvas(fichierId, sourceBlob)
      
      if (canvasBlob) {
        // Nettoyer l'ancienne URL
        if (imageDataUrls.value[fichierId]) {
          URL.revokeObjectURL(imageDataUrls.value[fichierId])
        }
        
        const dataUrl = URL.createObjectURL(canvasBlob)
        imageDataUrls.value[fichierId] = dataUrl
        imageErrors.value[fichierId] = false
      } else {
        throw new Error('La conversion Canvas a échoué')
      }
    } catch (error) {
      imageErrors.value[fichierId] = true
    }
  }

  // Fonction appelée quand une image se charge avec succès
  const onImageLoad = (fichierId: number) => {
    // Image chargée avec succès
  }

  // Fonction appelée en cas d'erreur de chargement d'image
  const onImageError = (fichierId: number) => {
    imageErrors.value[fichierId] = true
    // Nettoyer l'URL si elle existe
    if (imageDataUrls.value[fichierId]) {
      URL.revokeObjectURL(imageDataUrls.value[fichierId])
      delete imageDataUrls.value[fichierId]
    }
  }

  // Fonction pour ouvrir la modal d'image
  const openImageModal = (fichier: FichierMessage) => {
    modalImage.value = fichier
    showImageModal.value = true
  }

  // Fonctions de diagnostic des images
  const diagnoseBlobContent = async (fichierId: number, blob: Blob) => {
    // Vérifier les premiers bytes du fichier pour détecter la corruption
    try {
      const arrayBuffer = await blob.slice(0, 32).arrayBuffer()
      const bytes = new Uint8Array(arrayBuffer)
      
      // Vérifier les signatures de fichiers
      if (blob.type === 'image/jpeg' && bytes.length >= 3) {
        const isValidJpeg = bytes[0] === 0xFF && bytes[1] === 0xD8 && bytes[2] === 0xFF
        if (!isValidJpeg) {
          // Signature JPEG invalide détectée
        }
      } else if (blob.type === 'image/png' && bytes.length >= 8) {
        const pngSignature = [0x89, 0x50, 0x4E, 0x47, 0x0D, 0x0A, 0x1A, 0x0A]
        const isValidPng = pngSignature.every((byte, i) => bytes[i] === byte)
        if (!isValidPng) {
          // Signature PNG invalide détectée
        }
      }

      // Test de lecture complète du blob pour détecter la corruption
      try {
        await blob.arrayBuffer()
      } catch (readError) {
        // Erreur de lecture
      }
    } catch (debugError) {
      // Impossible de debugger le blob
    }
  }

  // Fonction pour tenter de réparer une image corrompue
  const attemptImageRepair = async (fichierId: number, blob: Blob): Promise<Blob | null> => {
    try {
      // Lire les premiers 64 bytes pour analyser la corruption
      const headerBuffer = await blob.slice(0, 64).arrayBuffer()
      const headerBytes = new Uint8Array(headerBuffer)
      
      // Rechercher la vraie signature JPEG dans les premiers bytes
      if (blob.type === 'image/jpeg') {
        const jpegSignature = [0xFF, 0xD8, 0xFF]
        
        // Chercher où commence réellement le JPEG
        let jpegStart = -1
        for (let i = 0; i <= headerBytes.length - 3; i++) {
          if (headerBytes[i] === jpegSignature[0] && 
              headerBytes[i + 1] === jpegSignature[1] && 
              headerBytes[i + 2] === jpegSignature[2]) {
            jpegStart = i
            break
          }
        }
        
        if (jpegStart > 0) {
          // Créer un nouveau blob sans les bytes corrompus au début
          const repairedBlob = blob.slice(jpegStart)
          
          // Vérifier que la réparation a fonctionné
          const testBuffer = await repairedBlob.slice(0, 16).arrayBuffer()
          const testBytes = new Uint8Array(testBuffer)
          const isValidAfterRepair = testBytes[0] === 0xFF && testBytes[1] === 0xD8 && testBytes[2] === 0xFF
          
          if (isValidAfterRepair) {
            return repairedBlob
          }
        }
      }
      
      // Tentative de réparation pour PNG
      else if (blob.type === 'image/png') {
        const pngSignature = [0x89, 0x50, 0x4E, 0x47, 0x0D, 0x0A, 0x1A, 0x0A]
        
        // Chercher où commence réellement le PNG
        let pngStart = -1
        for (let i = 0; i <= headerBytes.length - 8; i++) {
          let matches = 0
          for (let j = 0; j < 8; j++) {
            if (headerBytes[i + j] === pngSignature[j]) {
              matches++
            }
          }
          if (matches === 8) {
            pngStart = i
            break
          }
        }
        
        if (pngStart > 0) {
          const repairedBlob = blob.slice(pngStart)
          return repairedBlob
        }
      }
      
      return null // Aucune réparation possible
    } catch (error) {
      return null
    }
  }

  const testImageValidity = async (fichierId: number, dataUrl: string): Promise<boolean> => {
    return new Promise((resolve) => {
      const testImg = new Image()
      
      testImg.onload = () => {
        resolve(true)
      }
      
      testImg.onerror = (error) => {
        resolve(false)
      }
      
      // Timeout pour éviter les blocages
      setTimeout(() => {
        resolve(false)
      }, 5000)
      
      testImg.src = dataUrl
    })
  }

  // Fonctions utilitaires pour déterminer le type de fichier
  const isImage = (fichier: FichierMessage) => {
    return fichier.type_fichier?.startsWith('image/') || 
          fichier.nom_original?.match(/\.(jpg|jpeg|png|gif|webp|svg)$/i)
  }

  const isPDF = (fichier: FichierMessage) => {
    return fichier.type_fichier === 'application/pdf' || 
          fichier.nom_original?.endsWith('.pdf')
  }

  const isOfficeDoc = (fichier: FichierMessage) => {
    const officeTypes = [
      'application/msword',
      'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
      'application/vnd.ms-excel',
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      'application/vnd.ms-powerpoint',
      'application/vnd.openxmlformats-officedocument.presentationml.presentation'
    ]
    
    return officeTypes.includes(fichier.type_fichier) ||
          fichier.nom_original?.match(/\.(doc|docx|xls|xlsx|ppt|pptx)$/i)
  }

  const isArchive = (fichier: FichierMessage) => {
    const archiveTypes = [
      'application/zip',
      'application/x-rar-compressed',
      'application/x-7z-compressed',
      'application/gzip'
    ]
    
    return archiveTypes.includes(fichier.type_fichier) ||
          fichier.nom_original?.match(/\.(zip|rar|7z|gz|tar)$/i)
  }

  const getFileTypeLabel = (fichier: FichierMessage) => {
    if (fichier.nom_original?.match(/\.(doc|docx)$/i)) return 'Word'
    if (fichier.nom_original?.match(/\.(xls|xlsx)$/i)) return 'Excel'
    if (fichier.nom_original?.match(/\.(ppt|pptx)$/i)) return 'PowerPoint'
    if (fichier.type_fichier?.includes('text')) return 'Texte'
    return 'Document'
  }

  const formatFileSize = (bytes: number): string => {
    if (bytes === 0) return '0 B'
    const k = 1024
    const sizes = ['B', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i]
  }

  // Surveiller les fichiers
  watch(() => props.fichiers, (newFichiers) => {
    // Charger les images automatiquement
    newFichiers.forEach(fichier => {
      if (isImage(fichier)) {
        loadImageWithAuth(fichier.id_fichier)
      }
    })
  }, { immediate: true })

  // Gestion de la touche Échap pour fermer la modal
  const handleKeyDown = (event: KeyboardEvent) => {
    if (event.key === 'Escape' && showImageModal.value) {
      showImageModal.value = false
    }
  }

  // Charger les images au montage du composant
  onMounted(() => {
    props.fichiers.forEach(fichier => {
      if (isImage(fichier)) {
        loadImageWithAuth(fichier.id_fichier)
      }
    })
    
    // Ajouter l'écouteur d'événement pour la touche Échap
    document.addEventListener('keydown', handleKeyDown)
  })

  // Nettoyage des URLs d'objets pour éviter les fuites mémoire
  onUnmounted(() => {
    Object.values(imageDataUrls.value).forEach(url => {
      if (url.startsWith('blob:')) {
        URL.revokeObjectURL(url)
      }
    })
    
    // Retirer l'écouteur d'événement
    document.removeEventListener('keydown', handleKeyDown)
  })
</script>

<style scoped>
  /* Transitions pour la modal */
  .fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
  }

  .fade-enter-from, .fade-leave-to {
    opacity: 0;
  }

  /* Animation pour l'overlay au hover */
  .group:hover .group-hover\:opacity-100 {
    opacity: 1 !important;
  }

  .group:hover .group-hover\:scale-100 {
    transform: scale(1) !important;
  }

  /* Amélioration du scrollbar pour la modal si nécessaire */
  .modal-content {
    scrollbar-width: thin;
    scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
  }

  .modal-content::-webkit-scrollbar {
    width: 6px;
  }

  .modal-content::-webkit-scrollbar-track {
    background: transparent;
  }

  .modal-content::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 3px;
  }

  .modal-content::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
  }

  /* Effet de focus pour l'accessibilité */
  .image-container:focus-within {
    outline: 2px solid #0097b2;
    outline-offset: 2px;
  }

  /* Animation pour le loading spinner */
  @keyframes spin {
    from {
      transform: rotate(0deg);
    }
    to {
      transform: rotate(360deg);
    }
  }

  .animate-spin {
    animation: spin 1s linear infinite;
  }

  /* Transition fluide pour le scale au hover */
  .transition-transform {
    transition: transform 0.2s ease-in-out;
  }

  /* Ombre portée progressive pour les images */
  .image-shadow {
    box-shadow: 
      0 1px 3px rgba(0, 0, 0, 0.12),
      0 1px 2px rgba(0, 0, 0, 0.24);
    transition: box-shadow 0.3s ease;
  }

  .image-shadow:hover {
    box-shadow: 
      0 4px 6px rgba(0, 0, 0, 0.12),
      0 2px 4px rgba(0, 0, 0, 0.06),
      0 8px 25px rgba(0, 0, 0, 0.12);
  }
</style>



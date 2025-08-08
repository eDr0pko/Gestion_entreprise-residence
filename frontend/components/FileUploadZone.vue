<template>
  <div class="file-upload-zone">
    <!-- Zone de drag & drop -->
    <div
      ref="dropZone"
      @drop="handleDrop"
      @dragover="handleDragOver"
      @dragenter="handleDragEnter"
      @dragleave="handleDragLeave"
      @click="triggerFileInput"
      class="border-2 border-dashed rounded-lg p-4 transition-all duration-200 cursor-pointer"
      :class="[
        isDragging ? 'border-[#0097b2] bg-blue-50' : 'border-gray-300 hover:border-gray-400',
        files.length > 0 ? 'bg-gray-50' : ''
      ]"
    >
      <input
        ref="fileInput"
        type="file"
        multiple
        @change="handleFileSelect"
        accept="image/*,.pdf,.doc,.docx,.txt,.zip,.rar"
        class="hidden"
      />
      
      <div v-if="files.length === 0" class="text-center py-2">
        <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
        </svg>
        <p class="text-sm text-gray-600">
          Glissez vos fichiers ici ou 
          <span class="text-[#0097b2] hover:text-[#007a94] underline">cliquez pour parcourir</span>
        </p>
        <p class="text-xs text-gray-500 mt-1">
          Images, PDF, Documents (max 10MB chacun)
        </p>
      </div>

      <!-- Liste des fichiers sélectionnés -->
      <div v-else class="space-y-2">
        <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
          <span>{{ files.length }} fichier{{ files.length > 1 ? 's' : '' }} sélectionné{{ files.length > 1 ? 's' : '' }}</span>
          <button
            @click.stop="clearFiles"
            class="text-red-500 hover:text-red-700 text-xs"
          >
            Tout supprimer
          </button>
        </div>
        
        <div
          v-for="(file, index) in files"
          :key="index"
          class="flex items-center justify-between p-2 bg-white rounded border"
        >
          <div class="flex items-center space-x-2 flex-1 min-w-0">
            <!-- Icône selon le type de fichier -->
            <div class="flex-shrink-0">
              <svg v-if="isImage(file)" class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              <svg v-else-if="isPDF(file)" class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <svg v-else class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 truncate">{{ file.name }}</p>
              <p class="text-xs text-gray-500">{{ formatFileSize(file.size) }}</p>
            </div>
          </div>
          
          <button
            @click.stop="removeFile(index)"
            class="p-1 text-red-500 hover:text-red-700 transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Erreurs -->
    <div v-if="errors.length > 0" class="mt-2 space-y-1">
      <div
        v-for="(error, index) in errors"
        :key="index"
        class="text-sm text-red-600 bg-red-50 p-2 rounded"
      >
        {{ error }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  interface Props {
    maxFiles?: number
    maxFileSize?: number // en MB
    acceptedTypes?: string[]
  }

  const props = withDefaults(defineProps<Props>(), {
    maxFiles: 5,
    maxFileSize: 10,
    acceptedTypes: () => ['image/*', '.pdf', '.doc', '.docx', '.txt', '.zip', '.rar']
  })

  const emit = defineEmits<{
    filesChanged: [files: File[]]
  }>()

  // État réactif
  const files = ref<File[]>([])
  const errors = ref<string[]>([])
  const isDragging = ref(false)
  const dropZone = ref<HTMLElement>()
  const fileInput = ref<HTMLInputElement>()

  // Méthodes utilitaires
  const isImage = (file: File) => file.type.startsWith('image/')
  const isPDF = (file: File) => file.type === 'application/pdf'

  const formatFileSize = (bytes: number): string => {
    if (bytes === 0) return '0 B'
    const k = 1024
    const sizes = ['B', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i]
  }

  // Validation des fichiers
  const validateFile = (file: File): string | null => {
    // Vérifier la taille
    if (file.size > props.maxFileSize * 1024 * 1024) {
      return `${file.name} dépasse la taille maximale de ${props.maxFileSize}MB`
    }
    
    // Vérifier le type (simplifié)
    const isValidType = props.acceptedTypes.some(type => {
      if (type.startsWith('.')) {
        return file.name.toLowerCase().endsWith(type.toLowerCase())
      } else if (type.includes('/*')) {
        const mainType = type.split('/')[0]
        return file.type.startsWith(mainType)
      } else {
        return file.type === type
      }
    })
    
    if (!isValidType) {
      return `${file.name} n'est pas un type de fichier autorisé`
    }
    
    return null
  }

  // Ajouter des fichiers
  const addFiles = (newFiles: FileList | File[]) => {
    errors.value = []
    const filesToAdd: File[] = []
    
    Array.from(newFiles).forEach(file => {
      // Vérifier si le fichier n'est pas déjà ajouté
      if (files.value.some(f => f.name === file.name && f.size === file.size)) {
        errors.value.push(`${file.name} est déjà sélectionné`)
        return
      }
      
      // Valider le fichier
      const error = validateFile(file)
      if (error) {
        errors.value.push(error)
        return
      }
      
      filesToAdd.push(file)
    })
    
    // Vérifier le nombre total de fichiers
    if (files.value.length + filesToAdd.length > props.maxFiles) {
      errors.value.push(`Maximum ${props.maxFiles} fichiers autorisés`)
      return
    }
    
    // Ajouter les fichiers valides
    files.value.push(...filesToAdd)
    emit('filesChanged', files.value)
  }

  // Gestionnaires d'événements
  const handleFileSelect = (event: Event) => {
    const target = event.target as HTMLInputElement
    if (target.files) {
      addFiles(target.files)
    }
  }

  const handleDrop = (event: DragEvent) => {
    event.preventDefault()
    isDragging.value = false
    
    if (event.dataTransfer?.files) {
      addFiles(event.dataTransfer.files)
    }
  }

  const handleDragOver = (event: DragEvent) => {
    event.preventDefault()
  }

  const handleDragEnter = (event: DragEvent) => {
    event.preventDefault()
    isDragging.value = true
  }

  const handleDragLeave = (event: DragEvent) => {
    event.preventDefault()
    // Vérifier si on sort vraiment de la zone
    if (!dropZone.value?.contains(event.relatedTarget as Node)) {
      isDragging.value = false
    }
  }

  const triggerFileInput = () => {
    fileInput.value?.click()
  }

  const removeFile = (index: number) => {
    files.value.splice(index, 1)
    emit('filesChanged', files.value)
  }

  const clearFiles = () => {
    files.value = []
    errors.value = []
    emit('filesChanged', files.value)
  }

  // Méthode publique pour obtenir les fichiers
  const getFiles = () => files.value

  defineExpose({
    getFiles,
    clearFiles
  })
</script>

<style scoped>
  .file-upload-zone {
    width: 100%;
  }
</style>



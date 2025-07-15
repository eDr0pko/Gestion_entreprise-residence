
<template>
  <div class="bg-white border-t border-gray-200 p-3 lg:p-4 flex-shrink-0">
    <!-- Zone de citation (réponse) -->
    <div v-if="replyToMessage" class="mb-2 flex items-center bg-blue-50 border-l-4 border-[#0097b2] px-3 py-2 rounded relative">
      <div class="flex-1">
        <div class="text-xs text-gray-500 mb-1">
          Réponse à <span class="font-semibold">{{ replyToMessage.auteur_nom }}</span>
        </div>
        <div class="text-sm text-gray-700 truncate max-w-xs">
          {{ replyToMessage.contenu_message }}
        </div>
      </div>
      <button @click="$emit('cancel-reply')" class="ml-2 text-gray-400 hover:text-red-500" title="Annuler la réponse">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Zone d'upload si fichiers sélectionnés -->
    <div v-if="showFileUpload" class="mb-3">
      <FileUploadZone
        ref="fileUploadRef"
        :max-files="5"
        :max-file-size="10"
        @files-changed="handleFilesChanged"
      />
    </div>

    <!-- Affichage compact des fichiers sélectionnés -->
    <div v-if="!showFileUpload && selectedFiles.length > 0" class="mb-2">
      <div class="flex flex-wrap gap-2">
        <div 
          v-for="(file, index) in selectedFiles" 
          :key="index"
          class="flex items-center space-x-1 px-2 py-1 bg-blue-50 text-[#0097b2] rounded text-xs border"
        >
          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
          </svg>
          <span class="truncate max-w-20">{{ file.name }}</span>
          <button 
            @click="removeFile(index)"
            class="text-red-500 hover:text-red-700 ml-1"
          >
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <form @submit.prevent="$emit('send-message')" class="flex items-end space-x-2 lg:space-x-3">
      <div class="flex-1">
        <textarea
          v-model="message"
          ref="messageInput"
          placeholder="Tapez votre message..."
          rows="1"
          class="w-full px-3 lg:px-4 py-2 text-sm lg:text-base border border-gray-200 rounded-lg resize-none focus:ring-1 focus:ring-[#0097b2] focus:border-[#0097b2] transition-colors"
          @keydown="handleKeydown"
          style="max-height: 120px;"
          :disabled="sending"
        ></textarea>
      </div>
      
      <!-- Bouton fichiers -->
      <button
        type="button"
        @click="toggleFileUpload"
        class="relative p-2 text-gray-500 hover:text-[#0097b2] rounded-lg transition-colors flex-shrink-0"
        :class="{ 'text-[#0097b2] bg-blue-50': showFileUpload || selectedFiles.length > 0 }"
        :title="selectedFiles.length > 0 ? `${selectedFiles.length} fichier(s) sélectionné(s)` : 'Joindre des fichiers'"
      >
        <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
        </svg>
        <!-- Badge du nombre de fichiers -->
        <span 
          v-if="selectedFiles.length > 0" 
          class="absolute -top-1 -right-1 bg-[#0097b2] text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"
        >
          {{ selectedFiles.length }}
        </span>
      </button>
      
      <!-- Bouton envoyer -->
      <button
        type="submit"
        :disabled="(!message.trim() && selectedFiles.length === 0) || sending"
        class="p-2 bg-[#0097b2] text-white rounded-lg hover:bg-[#007a94] disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex-shrink-0"
        title="Envoyer le message"
      >
        <svg v-if="sending" class="w-4 h-4 lg:w-5 lg:h-5 animate-spin" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <svg v-else class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
        </svg>
      </button>
    </form>
  </div>
</template>

<script setup lang="ts">

import FileUploadZone from './FileUploadZone.vue'
import type { Message } from '~/types'

interface Props {
  sending: boolean
  replyToMessage?: Message | null
}

const props = defineProps<Props>()

const emit = defineEmits<{
  'send-message': []
  'cancel-reply': []
}>()

const message = defineModel<string>('message', { required: true })
const selectedFiles = defineModel<File[]>('selectedFiles', { required: true })

const messageInput = ref<HTMLTextAreaElement | null>(null)
const fileUploadRef = ref()
const showFileUpload = ref(false)

const replyToMessage = computed(() => props.replyToMessage)

const toggleFileUpload = () => {
  showFileUpload.value = !showFileUpload.value
  // Ne vider les fichiers que si on ferme la zone et qu'il n'y a pas de fichiers
  if (!showFileUpload.value && selectedFiles.value.length === 0) {
    fileUploadRef.value?.clearFiles()
  }
}

const handleFilesChanged = (files: File[]) => {
  selectedFiles.value = files
}

const removeFile = (index: number) => {
  selectedFiles.value.splice(index, 1)
}

const handleKeydown = (event: KeyboardEvent) => {
  if (event.key === 'Enter' && !event.shiftKey) {
    event.preventDefault()
    emit('send-message')
  }
}

// Watcher pour auto-resize du textarea
watch(message, () => {
  nextTick(() => {
    if (messageInput.value) {
      messageInput.value.style.height = 'auto'
      messageInput.value.style.height = Math.min(messageInput.value.scrollHeight, 120) + 'px'
    }
  })
})

// Exposer les méthodes nécessaires
defineExpose({
  clearFiles: () => {
    selectedFiles.value = []
    showFileUpload.value = false
    fileUploadRef.value?.clearFiles()
  },
  focusInput: () => {
    if (messageInput.value) messageInput.value.focus()
  }
})
</script>

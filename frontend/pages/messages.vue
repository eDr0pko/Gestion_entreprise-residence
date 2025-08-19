<template>
  <div class="h-screen bg-white flex flex-col">
    <!-- Header responsive -->
    <AppHeader 
      title=""
      :error="error"
      :loading-conversations="loadingConversations"
    >
      <template #default>
        <div class="w-full flex justify-center items-center">
          <span class="text-xl md:text-2xl font-extrabold bg-gradient-to-r from-[#0097b2] via-[#00b4d8] to-[#43e6ff] bg-clip-text text-transparent tracking-tight drop-shadow animate-fadein">
            {{ t('messages.title') }}
          </span>
        </div>
      </template>
      <template #actions>
        <div class="flex items-center">
          <!-- Bouton créer conversation (visible seulement sur la liste) -->
          <button
            v-if="!selectedConversation || !isMobile"
            @click="showCreateModal = true"
            class="p-2 text-gray-500 hover:text-[#0097b2] hover:bg-gray-100 rounded-lg transition-all duration-200 mr-2"
            :title="t('messages.newConversation')"
          >
            <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
          </button>
          <!-- Le slot actions est à gauche du statut de connexion -->
        </div>
      </template>
    </AppHeader>
    
    <!-- Notification d'erreur -->
    <Transition name="slide-down">
      <div 
        v-if="error" 
        class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 max-w-md w-full mx-4"
      >
        <div class="bg-red-500 text-white px-4 py-3 rounded-lg shadow-lg flex items-center justify-between">
          <div class="flex items-center">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm">{{ t(error) }}</span>
          </div>
          <button 
            @click="clearError"
            class="ml-2 text-white hover:text-gray-200 transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>
    </Transition>

    <!-- Contenu principal responsive -->
    <div class="flex-1 flex overflow-hidden">
      <!-- Liste des conversations -->
      <ConversationsList
        :conversations="filteredConversations"
        :selected-conversation="selectedConversation"
        :loading="loadingConversations"
        :error="error"
        :search-query="searchQuery"
        :total-messages-non-lus="totalMessagesNonLus"
        :is-mobile="isMobile"
        @select-conversation="selectConversation"
        @update-search="searchQuery = $event"
        @create-conversation="showCreateModal = true"
        @retry-load="loadConversations"
        @debug-auth="debugAuth"
        @refresh-conversations="refreshConversations"
      />

      <!-- Zone de conversation (responsive) -->
      <div 
        class="flex-1 flex flex-col min-h-0 transition-all duration-300"
        :class="[
          'lg:flex',
          isMobile && selectedConversation ? 'flex' : '',
          isMobile && !selectedConversation ? 'hidden' : ''
        ]"
      >
        <!-- État vide (pas de conversation sélectionnée) -->
        <div v-if="!selectedConversation" class="flex-1 flex items-center justify-center bg-gray-50 p-4">
          <div class="text-center">
            <svg class="w-12 h-12 lg:w-16 lg:h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <h3 class="text-base lg:text-lg font-medium text-gray-900 mb-2">{{ t('messages.selectConversationTitle') }}</h3>
            <p class="text-sm text-gray-500">{{ t('messages.selectConversationHint') }}</p>
          </div>
        </div>

        <!-- Conversation active -->
        <div v-else class="flex-1 flex flex-col min-h-0">
          <!-- Header de la conversation -->
          <ConversationHeader
            :conversation="selectedConversation"
            @show-members="showMembersModal = true"
          />

          <!-- Messages Area -->
          <MessagesArea
            ref="messagesAreaRef"
            :messages="messages"
            :loading="loadingMessages"
            :current-user-email="(currentUser as any)?.email"
            :is-at-bottom="isAtBottom"
            @scroll-to-bottom="scrollToBottom"
            @reaction-toggled="handleReactionToggled"
            @download-file="downloadFile"
            @scroll="handleMessagesScroll"
            @reply="onReplyMessage"
          />

          <!-- Bandeau de réponse (citation) -->
          <Transition name="fade">
            <div v-if="replyingTo" class="px-3 lg:px-4 py-2 bg-[#0097b2]/5 border-l-4 border-[#0097b2] mx-3 lg:mx-4 mt-2 rounded relative">
              <div class="flex items-start gap-2">
                <div class="flex-1 min-w-0">
                  <div class="text-xs font-semibold text-[#0097b2] truncate">{{ replyingTo.auteur_nom }}</div>
                  <div class="text-xs text-gray-600 truncate">{{ replyingTo.contenu_message }}</div>
                </div>
                <button class="text-gray-400 hover:text-gray-600 transition-colors" @click="cancelReply" :title="t('components.messageReply.cancel')">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
          </Transition>

          <!-- Message Composer -->
          <MessageComposer
            ref="messageComposerRef"
            v-model:message="newMessage"
            v-model:selected-files="selectedFiles"
            :sending="sendingMessage"
            @send-message="sendMessage"
          />
        </div>
      </div>
    </div>

    <!-- Footer (caché sur mobile quand conversation ouverte) -->
    <AppFooter v-show="!isMobile || !selectedConversation" />

    <!-- Modal de création de conversation -->
    <CreateConversationModal 
      :show="showCreateModal"
      @close="showCreateModal = false"
      @created="onConversationCreated"
    />

    <!-- Modal des membres du groupe -->
    <GroupMembersModal 
      :show="showMembersModal"
      :conversation="selectedConversation"
      @close="showMembersModal = false"
      @members-updated="onMembersUpdated"
    />
  </div>
</template>

<script setup lang="ts">
  // Middleware pour vérifier l'authentification
  definePageMeta({
    middleware: 'auth'
  })

  const { t } = useI18n()

  useHead({
    title: computed(() => t('messages.pageTitle'))
  })

  // Import des composables
  import type { Conversation, Message, FichierMessage, ApiResponse } from '~/types'
  import { useI18n } from 'vue-i18n'

  // Configuration et composables
  const config = useRuntimeConfig()
  const authStore = useAuthStore()

  // Device detection
  const isMobile = computed(() => {
    if (process.client) {
      return window.innerWidth < 1024
    }
    return false
  })

  // État de l'utilisateur connecté
  const currentUser = computed(() => authStore.user)

  // Headers d'authentification
  const getAuthHeaders = () => {
    const headers: Record<string, string> = {
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    }
    
    if (authStore.token) {
      headers['Authorization'] = `Bearer ${authStore.token}`
    }
    
    return headers
  }

  // État réactif
  const searchQuery = ref('')
  const selectedConversation = ref<Conversation | null>(null)
  const newMessage = ref('')
  const error = ref('')
  // Réponse (citation)
  const replyingTo = ref<Message | null>(null)

  // Fonction pour effacer l'erreur
  const clearError = () => {
    error.value = ''
  }

  // États de chargement
  const loadingConversations = ref(true)
  const loadingMessages = ref(false)
  const sendingMessage = ref(false)

  // Données
  const conversations = ref<Conversation[]>([])
  const messages = ref<Message[]>([])
  const selectedFiles = ref<File[]>([])

  // Référence au composant MessagesArea
  const messagesAreaRef = ref()

  // Référence au composant MessageComposer
  const messageComposerRef = ref()

  // État du défilement
  const isAtBottom = ref(true)

  // Modales
  const showCreateModal = ref(false)
  const showMembersModal = ref(false)

  // Computed
  const filteredConversations = computed(() => {
    if (!searchQuery.value.trim()) return conversations.value
    return conversations.value.filter(conv => 
      conv.nom_groupe?.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  })

  const totalMessagesNonLus = computed(() => {
    return conversations.value.reduce((total, conv) => total + conv.messages_non_lus, 0)
  })

  // Navigation mobile
  const goBackToConversations = () => {
    selectedConversation.value = null
  }

  const toggleMobileMenu = () => {
    // Pour la compatibilité avec AppHeader
  }


  // Charger les conversations
  const loadConversations = async () => {
    try {
      error.value = ''
      loadingConversations.value = true
      
      const response = await fetch(`${config.public.apiBase}/conversations`, {
        headers: getAuthHeaders(),
      })
      
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`)
      }
      
      const data = await response.json() as ApiResponse
      
      if (data.success && Array.isArray(data.conversations)) {
        conversations.value = data.conversations
      } else {
        throw new Error(data.error || 'Réponse API invalide')
      }
      
    } catch (err: any) {
      console.error('Erreur lors du chargement des conversations:', err)
      
      if (err.status === 401) {
        if (authStore.isAuthenticated) {
          authStore.clearAuth()
        }
        await navigateTo('/')
        return
      }
      
      error.value = 'messages.errors.loadConversations'
      conversations.value = []
    } finally {
      loadingConversations.value = false
    }
  }

  // Rafraîchir manuellement les conversations
  const refreshConversations = async () => {
    await loadConversations()
  }

  // Charger les messages d'un groupe
  const loadMessages = async (groupId: number) => {
    try {
      loadingMessages.value = true
      
      const response = await fetch(`${config.public.apiBase}/messages/${groupId}`, {
        headers: getAuthHeaders()
      })
      
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`)
      }
      
      const data = await response.json() as ApiResponse
      
      if (data.success && data.messages) {
        messages.value = data.messages
        
        // Scroll vers le bas après chargement
        await nextTick()
        setTimeout(() => {
          scrollToBottom(false)
          isAtBottom.value = true
        }, 50)
      } else {
        throw new Error(data.error || 'Erreur lors du chargement des messages')
      }
      
    } catch (err: any) {
      console.error('Erreur lors du chargement des messages:', err)
      
      if (err.status === 401) {
        if (authStore.isAuthenticated) {
          authStore.clearAuth()
        }
        await navigateTo('/')
        return
      }
      
      error.value = 'messages.errors.loadMessages'
      messages.value = []
    } finally {
      loadingMessages.value = false
    }
  }

  // Sélectionner une conversation
  const selectConversation = async (conversation: Conversation) => {
    selectedConversation.value = conversation
    
    // Charger les messages
    await loadMessages(conversation.id_groupe_message)
    
    // Mettre à jour localement le compteur de messages non lus
    const index = conversations.value.findIndex(c => c.id_groupe_message === conversation.id_groupe_message)
    if (index !== -1) {
      conversations.value[index].messages_non_lus = 0
    }
  }

  // Envoyer un message
  const sendMessage = async () => {
    if ((!newMessage.value.trim() && selectedFiles.value.length === 0) || !selectedConversation.value || sendingMessage.value) return
    
    // Vérification des prérequis
    if (!authStore.token) {
      error.value = 'messages.errors.missingToken'
      return
    }
    
    if (!selectedConversation.value?.id_groupe_message) {
      error.value = 'messages.errors.noConversationSelected'
      return
    }
    
    try {
      sendingMessage.value = true
      
      // Créer un message temporaire pour l'affichage immédiat
      const tempMessage: Message = {
        id_message: Date.now(), // ID temporaire
        contenu_message: newMessage.value.trim(),
        date_envoi: new Date().toISOString(),
        email_auteur: (currentUser.value as any)?.email || '',
        auteur_nom: (currentUser.value as any)?.nom || 'Vous',
        is_current_user: true,
        statut_lecture: 'sending', // Statut temporaire pour indiquer l'envoi
        fichiers: selectedFiles.value.map((file, index) => ({
          id_fichier: -(index + 1), // ID négatif temporaire
          nom_original: file.name,
          type_fichier: file.type,
          taille_fichier: file.size
        })),
        reactions: {},
        reply_to: replyingTo.value
          ? {
              id_message: replyingTo.value.id_message,
              auteur_nom: replyingTo.value.auteur_nom,
              excerpt: replyingTo.value.contenu_message?.slice(0, 120) || ''
            }
          : null
      }
      
      // Sauvegarder les valeurs actuelles
      const currentMessage = newMessage.value.trim()
      const currentFiles = [...selectedFiles.value]
      
      // Ajouter immédiatement le message à la liste pour un affichage fluide
      messages.value.push(tempMessage)
      
      // Vider les champs immédiatement
      newMessage.value = ''
      selectedFiles.value = []
      
      // Scroll vers le bas immédiatement
      await nextTick()
      setTimeout(() => scrollToBottom(true), 50)
      
      const formData = new FormData()
      formData.append('contenu', currentMessage)
      if (replyingTo.value?.id_message) {
        formData.append('reply_to', String(replyingTo.value.id_message))
      }
      
      // Ajouter les fichiers
      currentFiles.forEach((file) => {
        formData.append('fichiers[]', file)
      })
      
      const response = await fetch(`${config.public.apiBase}/messages/${selectedConversation.value.id_groupe_message}`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${authStore.token}`
        },
        body: formData
      })
      
      if (!response.ok) {
        let errorText = ''
        let errorData = null
        
        try {
          errorText = await response.text()
          // Essayer de parser en JSON pour obtenir plus de détails
          try {
            errorData = JSON.parse(errorText)
          } catch {
            // Si ce n'est pas du JSON, garder le texte brut
          }
        } catch (textErr) {
          errorText = 'Impossible de lire la réponse d\'erreur'
        }
        
        console.error('Erreur HTTP:', {
          status: response.status,
          statusText: response.statusText,
          body: errorText,
          parsedData: errorData
        })
        
        // Utiliser le message d'erreur du serveur s'il est disponible
        const errorMessage = errorData?.message || errorData?.error || errorText || response.statusText
        throw new Error(`HTTP ${response.status}: ${errorMessage}`)
      }
      
      let data: ApiResponse
      try {
        data = await response.json() as ApiResponse
      } catch (jsonErr) {
        console.error('Erreur lors du parsing JSON:', jsonErr)
        throw new Error('Réponse du serveur invalide (JSON malformé)')
      }
      
      if (data.success) {
        let newMessage: Message | null = null
        
        // Le backend peut retourner soit 'message' (envoi) soit 'messages' (rechargement)
        if (data.message && typeof data.message === 'object' && data.message !== null) {
          newMessage = data.message as Message
        } else if (data.messages && Array.isArray(data.messages) && data.messages.length > 0) {
          newMessage = data.messages[data.messages.length - 1] as Message
        }
        
        if (newMessage && newMessage.id_message) {
          // Remplacer le message temporaire par la vraie réponse du serveur
          const tempMessageIndex = messages.value.findIndex(msg => msg.id_message === tempMessage.id_message)
          if (tempMessageIndex !== -1) {
            // Conserver la position de scroll
            const wasAtBottom = isAtBottom.value
            messages.value[tempMessageIndex] = newMessage
            
            // Si on était en bas, rester en bas après la mise à jour
            if (wasAtBottom) {
              await nextTick()
              setTimeout(() => scrollToBottom(false), 10)
            }
          } else {
            messages.value.push(newMessage)
            await nextTick()
            setTimeout(() => scrollToBottom(false), 10)
          }
          
          // Mettre à jour la conversation dans la liste pour refléter le nouveau dernier message
          const conversationIndex = conversations.value.findIndex(c => c.id_groupe_message === selectedConversation.value?.id_groupe_message)
          if (conversationIndex !== -1) {
            const updatedConversation = { ...conversations.value[conversationIndex] }
            updatedConversation.dernier_contenu = newMessage.contenu_message || 'Fichier envoyé'
            updatedConversation.dernier_auteur = newMessage.auteur_nom
            updatedConversation.derniere_activite = newMessage.date_envoi
            
            // Déplacer la conversation en haut de la liste
            conversations.value.splice(conversationIndex, 1)
            conversations.value.unshift(updatedConversation)
            
            // Mettre à jour la référence de la conversation sélectionnée
            selectedConversation.value = updatedConversation
          }
          
          // Fermer le panel de sélection de fichiers s'il y en avait
          if (currentFiles.length > 0 && messageComposerRef.value?.clearFiles) {
            messageComposerRef.value.clearFiles()
          }
          // Réinitialiser la citation après envoi réussi
          replyingTo.value = null
        } else {
          // En cas d'absence de message dans la réponse, retirer le message temporaire
          const tempMessageIndex = messages.value.findIndex(msg => msg.id_message === tempMessage.id_message)
          if (tempMessageIndex !== -1) {
            messages.value.splice(tempMessageIndex, 1)
          }
          throw new Error('Le serveur n\'a pas retourné de message valide')
        }
      } else {
        throw new Error(data.error || (typeof data.message === 'string' ? data.message : '') || 'Erreur lors de l\'envoi du message')
      }
      
    } catch (err: any) {
      console.error('Erreur lors de l\'envoi du message:', err)
      
      // En cas d'erreur, retirer le message temporaire et restaurer les champs
      const tempMessageIndex = messages.value.findIndex(msg => msg.statut_lecture === 'sending')
      if (tempMessageIndex !== -1) {
        const failedMessage = messages.value[tempMessageIndex]
        messages.value.splice(tempMessageIndex, 1)
        
        // Restaurer le contenu en cas d'échec
        newMessage.value = failedMessage.contenu_message || ''
        // Note: Les fichiers ne peuvent pas être restaurés pour des raisons de sécurité
      }
      
      error.value = 'messages.errors.sendMessage'
    } finally {
      sendingMessage.value = false
    }
  }

  // Démarrer une réponse à un message
  const onReplyMessage = (message: Message) => {
    replyingTo.value = message
    // Essayer de focaliser le champ de saisie si le composant expose une méthode
    try { messageComposerRef.value?.focus?.() } catch {}
  }

  // Annuler la réponse
  const cancelReply = () => {
    replyingTo.value = null
  }

  // Si on change de conversation, annuler une éventuelle citation
  watch(selectedConversation, () => {
    replyingTo.value = null
  })

  // Scroll vers le bas
  const scrollToBottom = (smooth: boolean = true) => {
    if (messagesAreaRef.value?.messagesContainer) {
      const container = messagesAreaRef.value.messagesContainer
      container.scrollTo({
        top: container.scrollHeight,
        behavior: smooth ? 'smooth' : 'auto'
      })
    }
  }

  // Gestion du défilement pour déterminer si on est en bas
  const handleMessagesScroll = () => {
    if (messagesAreaRef.value?.messagesContainer) {
      const container = messagesAreaRef.value.messagesContainer
      const scrollTop = container.scrollTop
      const scrollHeight = container.scrollHeight
      const clientHeight = container.clientHeight
      
      // Considérer qu'on est en bas si on est à moins de 100px du bas
      isAtBottom.value = scrollHeight - scrollTop - clientHeight < 100
    }
  }

  // Gestion des réactions
  const handleReactionToggled = async (messageId: number, emoji: string) => {
    try {
      const messageIndex = messages.value.findIndex(msg => msg.id_message === messageId)
      if (messageIndex === -1) return
      
      const message = messages.value[messageIndex]
      const currentUserEmail = (currentUser.value as any)?.email
      const currentUserNom = (currentUser.value as any)?.nom || 'Vous'
      
      if (!currentUserEmail) return
      
      // Mise à jour optimiste locale
      const originalReactions = JSON.parse(JSON.stringify(message.reactions || {}))
      
      if (!message.reactions) {
        message.reactions = {}
      }
      
      if (!message.reactions[emoji]) {
        message.reactions[emoji] = { count: 0, users: [] }
      }
      
      const userAlreadyReacted = message.reactions[emoji].users.some(user => user.email === currentUserEmail)
      
      if (userAlreadyReacted) {
        // Retirer la réaction
        message.reactions[emoji].users = message.reactions[emoji].users.filter(user => user.email !== currentUserEmail)
        message.reactions[emoji].count = message.reactions[emoji].users.length
        
        // Supprimer l'emoji s'il n'y a plus d'utilisateurs
        if (message.reactions[emoji].count === 0) {
          delete message.reactions[emoji]
        }
      } else {
        // Ajouter la réaction
        message.reactions[emoji].users.push({
          email: currentUserEmail,
          nom: currentUserNom
        })
        message.reactions[emoji].count = message.reactions[emoji].users.length
      }
      
      // Déclencher la réactivité
      messages.value[messageIndex] = { ...message }
      
      const response = await fetch(`${config.public.apiBase}/messages/${messageId}/reactions`, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${authStore.token}`
        },
        body: JSON.stringify({ emoji })
      })
      
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`)
      }
      
      const data = await response.json()
      
      if (!data.success) {
        // En cas d'erreur du serveur, restaurer l'état original
        messages.value[messageIndex].reactions = originalReactions
        throw new Error(data.message || 'Erreur lors de la gestion de la réaction')
      }
      
      // Optionnel: Mettre à jour avec la réponse du serveur pour s'assurer de la cohérence
      if (data.reactions) {
        messages.value[messageIndex].reactions = data.reactions
      }
      
    } catch (err: any) {
      console.error('Erreur lors de la gestion de la réaction:', err)
      error.value = 'messages.errors.reaction'
      
      // En cas d'erreur réseau, recharger les messages pour récupérer l'état correct
      if (selectedConversation.value) {
        await loadMessages(selectedConversation.value.id_groupe_message)
      }
    }
  }

  // Téléchargement de fichier
  const downloadFile = async (fichierId: number) => {
    try {
      const response = await fetch(`${config.public.apiBase}/files/${fichierId}`, {
        headers: getAuthHeaders()
      })
      
      if (response.ok) {
        const blob = await response.blob()
        const url = window.URL.createObjectURL(blob)
        const a = document.createElement('a')
        a.href = url
        a.download = `file_${fichierId}`
        document.body.appendChild(a)
        a.click()
        window.URL.revokeObjectURL(url)
        document.body.removeChild(a)
      }
    } catch (err) {
      console.error('Erreur lors du téléchargement:', err)
      error.value = 'messages.errors.downloadFile'
    }
  }

  // Événements des modales
  const onConversationCreated = (conversation: Conversation) => {
    conversations.value.unshift(conversation)
    selectConversation(conversation)
  }

  const onMembersUpdated = () => {
    // Recharger les informations de la conversation
    if (selectedConversation.value) {
      loadConversations()
    }
  }

  // Fonction utilitaire pour tester la connexion au backend
  const testBackendConnection = async () => {
    try {
      const response = await fetch(`${config.public.apiBase}/conversations`, {
        headers: getAuthHeaders(),
      })
      
      if (response.status === 401) {
        return false
      }
      
      return response.ok
    } catch (err) {
      return false
    }
  }

  // Debug de l'authentification
  const debugAuth = async () => {
    console.log('=== DEBUG AUTHENTIFICATION ===')
    console.log('Store auth:', authStore)
    console.log('Utilisateur:', authStore.user)
    console.log('Token:', authStore.token)
    console.log('Is authenticated:', authStore.isAuthenticated)
    console.log('API Base:', config.public.apiBase)
    console.log('Headers:', getAuthHeaders())
    
    // Test de connexion au backend
    const backendOk = await testBackendConnection()
    console.log('Backend accessible:', backendOk)
    
    alert('Logs de debug affichés dans la console du navigateur (F12)')
  }

  // Formatage du temps et taille de fichier
  const formatTime = (dateString: string) => {
    // Fonction utilitaire pour formater l'heure
    const date = new Date(dateString)
    const now = new Date()
    const diffInHours = (now.getTime() - date.getTime()) / (1000 * 60 * 60)
    
    if (diffInHours < 1) {
      return 'À l\'instant'
    } else if (diffInHours < 24) {
      return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
    } else if (diffInHours < 48) {
      return 'Hier'
    } else {
      return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' })
    }
  }

  const formatFileSize = (bytes: number) => {
    if (bytes === 0) return '0 B'
    const k = 1024
    const sizes = ['B', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
  }

  // Fonction utilitaire pour gérer les erreurs de réseau
  const handleNetworkError = (err: any, fallbackMessage: string) => {
    if (err.name === 'AbortError') {
      return 'Opération annulée'
    } else if (err.code === 'NETWORK_ERROR' || err.message?.includes('fetch') || err.message?.includes('NetworkError')) {
      return 'Erreur de connexion au serveur. Vérifiez votre connexion internet.'
    } else if (err.status === 429) {
      return 'Trop de tentatives. Veuillez patienter quelques instants.'
    } else if (err.status === 413) {
      return 'Fichier trop volumineux. Veuillez réduire la taille du fichier.'
    } else {
      return err.data?.message || err.message || fallbackMessage
    }
  }

  // Lifecycle
  onMounted(async () => {
    await loadConversations()
  })

  onUnmounted(() => {
    // Cleanup si nécessaire
  })

  // Watcher pour scroller automatiquement quand de nouveaux messages arrivent
  watch(messages, async (newMessages, oldMessages) => {
    // Si on a de nouveaux messages et qu'on était déjà en bas, scroller automatiquement
    if (newMessages.length > oldMessages.length && isAtBottom.value) {
      await nextTick()
      setTimeout(() => scrollToBottom(true), 50)
    }
  }, { flush: 'post' })
</script>

<style scoped>
  /* Animation pour les notifications */
  .slide-down-enter-active, .slide-down-leave-active {
    transition: all 0.3s ease;
  }

  .slide-down-enter-from {
    opacity: 0;
    transform: translateX(-50%) translateY(-20px);
  }

  .slide-down-leave-to {
    opacity: 0;
    transform: translateX(-50%) translateY(-20px);
  }

  /* Effet de transition fluide pour le changement de vue mobile */
  .transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  }
</style>


<template>
  <div class="flex-1 relative bg-gray-50 min-h-0">
    <div v-if="loading" class="absolute inset-0 flex items-center justify-center">
      <div class="flex items-center space-x-2 text-gray-500">
        <svg class="w-4 h-4 lg:w-5 lg:h-5 animate-spin" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-sm">Chargement...</span>
      </div>
    </div>
    
    <div 
      v-else 
      ref="messagesContainer" 
      class="h-full overflow-y-auto custom-scrollbar px-3 lg:px-4 py-3 lg:py-4"
      @scroll="handleScroll"
    >
      <div v-if="messages.length === 0" class="h-full flex items-center justify-center">
        <div class="text-center text-gray-500">
          <svg class="w-10 h-10 lg:w-12 lg:h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
          </svg>
          <p class="text-sm">Aucun message dans cette conversation</p>
          <p class="text-xs mt-1">Soyez le premier à envoyer un message !</p>
        </div>
      </div>
      
      <!-- Conteneur des messages avec espacement responsive -->
      <div class="space-y-3 lg:space-y-4 pb-4">
        <div
          v-for="(message, index) in messages"
          :key="message.id_message"
          class="flex"
          :class="{ 'justify-end': message.is_current_user }"
        >
          <div class="max-w-[85%] sm:max-w-xs lg:max-w-md">
            <!-- Message cité (reply/quote) -->
            <div v-if="message.reply_to" class="mb-1 ml-1 px-2 py-1 border-l-4 border-[#0097b2] bg-blue-50 rounded text-xs text-gray-700 max-w-xs truncate">
              <span class="font-semibold">{{ message.reply_to.auteur_nom }}</span>
              <span v-if="message.reply_to.contenu_message">: {{ message.reply_to.contenu_message }}</span>
            </div>
            <!-- Nom de l'expéditeur -->
            <div 
              v-if="!message.is_current_user && (index === 0 || messages[index - 1].email_auteur !== message.email_auteur)" 
              class="text-xs text-gray-500 mb-1 ml-1"
            >
              {{ message.auteur_nom }}
            </div>
            
            <!-- Bulle de message -->
            <div
              class="px-3 lg:px-4 py-2 rounded-lg shadow-sm transition-all duration-200 hover:shadow-md relative group"
              :class="[
                message.is_current_user 
                  ? 'bg-[#0097b2] text-white rounded-br-sm' 
                  : 'bg-white text-gray-900 rounded-bl-sm border border-gray-200',
                message.statut_lecture === 'sending' ? 'opacity-75' : ''
              ]"
            >
              <!-- Bouton Répondre déplacé sous la bulle, à côté des réactions -->
              <!-- Indicateur d'envoi en cours -->
              <div 
                v-if="message.statut_lecture === 'sending'" 
                class="absolute -top-1 -right-1 w-4 h-4 bg-yellow-500 rounded-full flex items-center justify-center"
                title="Envoi en cours..."
              >
                <svg class="w-2 h-2 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </div>
              <!-- Contenu texte -->
              <p v-if="message.contenu_message" class="text-sm lg:text-base whitespace-pre-wrap break-words">
                {{ message.contenu_message }}
              </p>

              <!-- Fichiers attachés -->
              <MessageAttachment
                v-if="message.fichiers && message.fichiers.length > 0"
                :fichiers="message.fichiers"
                :is-current-user="message.is_current_user"
                @download-file="$emit('download-file', $event)"
              />

              <!-- Informations du message au bas de la bulle -->
              <div class="flex items-center justify-end mt-2 space-x-1">
                <span class="text-xs opacity-70">
                  {{ formatTime(message.date_envoi) }}
                </span>
                
                <!-- Statut de lecture avancé -->
                <div v-if="message.is_current_user" class="flex items-center space-x-1">
                  <!-- En cours d'envoi -->
                  <template v-if="message.statut_lecture === 'sending'">
                    <svg class="w-3 h-3 animate-spin opacity-70" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                  </template>
                  
                  <!-- Envoyé -->
                  <template v-else>
                    <svg class="w-3 h-3 opacity-70" fill="currentColor" viewBox="0 0 20 20" title="Envoyé">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    
                    <!-- Reçu/Lu -->
                    <svg 
                      v-if="message.statut_lecture === 'lu'"
                      class="w-3 h-3 text-blue-400" 
                      fill="currentColor" 
                      viewBox="0 0 20 20"
                      title="Lu"
                    >
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <svg 
                      v-else
                      class="w-3 h-3 opacity-50" 
                      fill="currentColor" 
                      viewBox="0 0 20 20"
                      title="Reçu"
                    >
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                  </template>
                </div>
              </div>
            </div>

            <!-- RÉACTIONS SOUS LA BULLE -->
            <div class="flex items-center gap-2 mt-1 ml-2">
              <MessageReactions
                :message-id="message.id_message"
                :reactions="transformReactions(message.reactions || {})"
                :current-user-email="currentUserEmail"
                @reaction-toggled="$emit('reaction-toggled', message.id_message, $event)"
              />
              <button
                class="flex items-center text-xs text-gray-500 hover:text-[#0097b2] px-2 py-1 rounded transition-colors"
                title="Répondre en citant"
                @click.stop="$emit('reply-to-message', message)"
                style="align-items: center; margin-top: 0;"
              >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7a1 1 0 011-1h8m-9 10l-5-5m0 0l5-5" />
                </svg>
                Répondre
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bouton pour descendre en bas -->
    <Transition name="fade">
      <button
        v-if="!isAtBottom && messages.length > 0"
        @click="$emit('scroll-to-bottom')"
        class="absolute bottom-3 right-3 lg:bottom-4 lg:right-4 w-8 h-8 lg:w-10 lg:h-10 bg-[#0097b2] text-white rounded-full shadow-lg hover:bg-[#007a94] transition-all duration-200 flex items-center justify-center"
        title="Aller en bas"
      >
        <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
        </svg>
      </button>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import MessageReactions from './MessageReactions.vue'
import MessageAttachment from './MessageAttachment.vue'
import type { Message, FichierMessage } from '~/types'

interface Props {
  messages: Message[]
  loading: boolean
  isAtBottom: boolean
  currentUserEmail: string
}

const props = defineProps<Props>()

const emit = defineEmits<{
  'download-file': [fichierId: number]
  'reaction-toggled': [messageId: number, emoji: string]
  'scroll-to-bottom': []
  'scroll': []
  'reply-to-message': [message: Message]
}>()

const messagesContainer = ref<HTMLElement | null>(null)

const handleScroll = () => {
  emit('scroll')
}

// Transformer les réactions du format API vers le format attendu par le composant
const transformReactions = (reactions: Record<string, any>) => {
  // Les réactions arrivent déjà dans le bon format depuis le backend
  // Format: { "👍": { count: 2, users: [{ id_personne: 1, email: "user@test.com", nom: "John Doe" }] } }
  if (!reactions || typeof reactions !== 'object') {
    return {}
  }
  
  const transformed: Record<string, { count: number; users: { email: string; nom: string; }[] }> = {}
  
  for (const [emoji, reactionData] of Object.entries(reactions)) {
    if (reactionData && typeof reactionData === 'object' && reactionData.users) {
      transformed[emoji] = {
        count: reactionData.count || reactionData.users.length,
        users: reactionData.users.map((user: any) => ({
          email: user.email,
          nom: user.nom
        }))
      }
    }
  }
  
  return transformed
}

// Formater l'heure
const formatTime = (dateString: string): string => {
  if (!dateString) return ''
  
  try {
    const date = new Date(dateString)
    const now = new Date()
    const diff = now.getTime() - date.getTime()
    const days = Math.floor(diff / (1000 * 60 * 60 * 24))
    
    if (days === 0) {
      return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
    } else if (days === 1) {
      return 'Hier'
    } else if (days < 7) {
      return date.toLocaleDateString('fr-FR', { weekday: 'short' })
    } else {
      return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' })
    }
  } catch (error) {
    console.error('Erreur formatage date:', error)
    return ''
  }
}

// Exposer la référence du container pour le scroll
defineExpose({ messagesContainer })
</script>

<style scoped>
/* Scrollbar personnalisée */
.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e0 #f7fafc;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #f7fafc;
  border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e0;
  border-radius: 3px;
  transition: background-color 0.2s;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #a0aec0;
}

/* Animation pour le bouton de retour en bas */
.fade-enter-active, .fade-leave-active {
  transition: all 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
  transform: translateY(10px) scale(0.9);
}
</style>

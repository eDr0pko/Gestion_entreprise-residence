
<template>
  <div 
    class="bg-white border-r border-gray-200 flex flex-col transition-all duration-300"
    :class="[
      'hidden lg:flex lg:w-80',
      isMobile && !selectedConversation ? 'flex w-full' : '',
      isMobile && selectedConversation ? 'hidden' : ''
    ]"
  >
    <!-- Barre de recherche et bouton création -->
    <div class="p-3 lg:p-4 border-b border-gray-100">
      <!-- Section Messages avec badge -->
      <div class="flex items-center justify-center mb-4">
        <div class="flex items-center space-x-3">
          <h2 class="text-lg lg:text-xl font-semibold text-gray-900">{{ t('conversationsList.title') }}</h2>
          <Transition name="badge">
            <div 
              v-if="totalMessagesNonLus > 0"
              class="bg-red-500 text-white text-xs lg:text-sm font-bold rounded-full min-w-[20px] lg:min-w-[24px] h-5 lg:h-6 flex items-center justify-center px-1 lg:px-2"
              :title="t('conversationsList.unreadBadge', { count: totalMessagesNonLus })"
            >
              {{ totalMessagesNonLus > 99 ? '99+' : totalMessagesNonLus }}
            </div>
          </Transition>
        </div>
      </div>

      <div class="flex items-center space-x-2 mb-3">
        <div class="relative flex-1">
          <input
            :value="searchQuery"
            @input="searchQuery = ($event.target as HTMLInputElement).value"
            type="text"
            :placeholder="t('conversationsList.searchPlaceholder')"
            class="w-full pl-10 pr-4 py-2 text-sm lg:text-base border border-gray-200 rounded-lg focus:ring-1 focus:ring-[#0097b2] focus:border-[#0097b2] transition-colors"
          />
          <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>
        
        <!-- Boutons nouvelle conversation et refresh (version mobile) -->
        <button
          @click="$emit('refresh-conversations')"
          :disabled="loading"
          class="lg:hidden p-2 text-gray-500 hover:text-[#0097b2] hover:bg-gray-100 rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed border border-gray-200"
          :title="t('conversationsList.refresh')"
        >
          <svg 
            class="w-5 h-5 transition-transform duration-200"
            :class="{ 'animate-spin': loading }"
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
        </button>
        
        <button
          @click="$emit('create-conversation')"
          class="lg:hidden p-2 bg-[#0097b2] text-white rounded-lg hover:bg-[#007a94] transition-colors"
          :title="t('conversationsList.newConversation')"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
        </button>
      </div>

      <!-- Boutons nouvelle conversation et refresh (version desktop) -->
      <div class="hidden lg:flex space-x-2">
        <button
          @click="$emit('create-conversation')"
          class="flex-1 flex items-center justify-center px-4 py-2 bg-[#0097b2] text-white rounded-lg hover:bg-[#007a94] transition-colors"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          {{ t('conversationsList.newConversation') }}
        </button>
        
        <button
          @click="$emit('refresh-conversations')"
          :disabled="loading"
          class="px-3 py-2 text-gray-500 hover:text-[#0097b2] hover:bg-gray-100 rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed border border-gray-200"
          :title="t('conversationsList.refresh')"
        >
          <svg 
            class="w-4 h-4 transition-transform duration-200"
            :class="{ 'animate-spin': loading }"
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
        </button>
      </div>
    </div>

    <!-- Loading des conversations -->
    <div v-if="loading" class="flex-1 flex items-center justify-center p-4">
      <div class="text-gray-500 text-sm">{{ t('conversationsList.loading') }}</div>
    </div>

    <!-- Message d'erreur -->
    <div v-else-if="error" class="flex-1 flex items-center justify-center p-4">
      <div class="text-center">
        <div class="text-red-500 mb-4">
          <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <p class="text-sm mb-2 max-w-xs">{{ error }}</p>
        </div>
        <div class="space-y-2">
          <button 
            @click="$emit('retry-load')" 
            class="block w-full px-4 py-2 text-sm bg-[#0097b2] text-white rounded-lg hover:bg-[#007a94] transition-colors"
          >
            {{ t('conversationsList.retry') }}
          </button>
          <button 
            @click="$emit('debug-auth')" 
            class="block w-full px-4 py-2 text-sm bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
          >
            {{ t('conversationsList.debugAuth') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Liste des conversations -->
    <div v-else class="flex-1 overflow-y-auto custom-scrollbar">
      <div v-if="filteredConversations.length === 0" class="flex-1 flex items-center justify-center p-4">
        <div class="text-center text-gray-500">
          <p class="text-sm">{{ t('conversationsList.noConversation') }}</p>
        </div>
      </div>
      
      <div
        v-for="conversation in filteredConversations"
        :key="conversation.id_groupe_message"
        @click="$emit('select-conversation', conversation)"
        class="flex items-center px-3 lg:px-4 py-3 hover:bg-gray-50 cursor-pointer transition-colors border-b border-gray-100 relative active:bg-gray-100"
        :class="{ 'bg-[#e6f7fb]': selectedConversation?.id_groupe_message === conversation.id_groupe_message }"
      >
        <!-- Avatar du groupe -->
        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-[#0097b2] rounded-full flex items-center justify-center flex-shrink-0 relative">
          <svg class="w-5 h-5 lg:w-6 lg:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
          
          <!-- Badge de messages non lus -->
          <Transition name="badge">
            <div 
              v-if="conversation.messages_non_lus > 0"
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full min-w-[18px] lg:min-w-[20px] h-4 lg:h-5 flex items-center justify-center px-1 shadow-lg"
              :title="t('conversationsList.unreadBadge', { count: conversation.messages_non_lus })"
            >
              {{ conversation.messages_non_lus > 99 ? '99+' : conversation.messages_non_lus }}
            </div>
          </Transition>
        </div>

        <div class="ml-3 flex-1 min-w-0">
          <div class="flex items-center justify-between">
            <h3 class="text-sm lg:text-base font-medium text-gray-900 truncate"
                :class="{ 'font-bold': conversation.messages_non_lus > 0 }">
              {{ conversation.nom_groupe }}
            </h3>
            <div class="flex items-center space-x-1 lg:space-x-2">
              <span class="text-xs text-gray-500">
                {{ formatTime(conversation.derniere_activite || conversation.date_creation) }}
              </span>
              <!-- Indicateur de statut de lecture -->
              <div 
                v-if="conversation.messages_non_lus > 0"
                class="w-1.5 h-1.5 lg:w-2 lg:h-2 bg-red-500 rounded-full"
                :title="t('conversationsList.unread')"
              ></div>
            </div>
          </div>
          
          <p class="text-xs lg:text-sm text-gray-500 truncate mt-1"
             :class="{ 'font-medium text-gray-700': conversation.messages_non_lus > 0 }">
            <span v-if="conversation.dernier_auteur" class="font-medium">{{ conversation.dernier_auteur }}:</span>
            {{ conversation.dernier_contenu || t('conversationsList.noMessage') }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { useI18n } from 'vue-i18n'
  import { computed } from 'vue'
  const { t } = useI18n()

  interface Conversation {
    id_groupe_message: number
    nom_groupe: string
    date_creation: string
    derniere_activite?: string
    messages_non_lus: number
    dernier_contenu?: string
    dernier_auteur?: string
    nombre_membres?: number
  }

  interface Props {
    conversations: Conversation[]
    selectedConversation: Conversation | null
    loading: boolean
    error: string
    searchQuery: string
    totalMessagesNonLus: number
    isMobile: boolean
  }

  const props = defineProps<Props>()

  const emit = defineEmits<{
    'create-conversation': []
    'select-conversation': [conversation: Conversation]
    'retry-load': []
    'debug-auth': []
    'refresh-conversations': []
    'update-search': [searchQuery: string]
  }>()

  const searchQuery = computed({
    get: () => props.searchQuery,
    set: (value: string) => emit('update-search', value)
  })

  const filteredConversations = computed(() => {
    if (!searchQuery.value) return props.conversations
    return props.conversations.filter((conv: Conversation) => 
      conv.nom_groupe?.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  })

  const totalMessagesNonLus = computed(() => props.totalMessagesNonLus)

  const selectedConversation = computed(() => props.selectedConversation)
  const isMobile = computed(() => props.isMobile)

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
        return t('conversationsList.yesterday')
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

  /* Animation pour le badge */
  .badge-enter-active, .badge-leave-active {
    transition: all 0.3s ease;
  }

  .badge-enter-from {
    opacity: 0;
    transform: scale(0.3);
  }

  .badge-leave-to {
    opacity: 0;
    transform: scale(0.3);
  }
</style>



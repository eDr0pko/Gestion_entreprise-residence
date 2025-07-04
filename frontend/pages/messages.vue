<template>
  <div class="h-screen bg-white flex flex-col">
    <!-- Header responsive -->
    <AppHeader 
      title="Retour" 
      back-to="/planning"
      :show-back-button="isMobile && !!selectedConversation"
      :show-mobile-menu="!selectedConversation"
      @go-back="goBackToConversations"
      @toggle-mobile-menu="toggleMobileMenu"
    >
      <template #actions>
        <!-- Indicateur de statut de connexion -->
        <div class="flex items-center space-x-2 mr-2">
          <div 
            class="w-2 h-2 rounded-full"
            :class="error ? 'bg-red-500' : loadingConversations ? 'bg-yellow-500 animate-pulse' : 'bg-green-500'"
            :title="error ? 'Erreur de connexion' : loadingConversations ? 'Chargement...' : 'Connecté'"
          ></div>
          <span v-if="error" class="text-xs text-red-500 hidden lg:inline">Hors ligne</span>
          <span v-else-if="loadingConversations" class="text-xs text-yellow-600 hidden lg:inline">Chargement...</span>
          <span v-else class="text-xs text-green-600 hidden lg:inline">En ligne</span>
        </div>
        
        <!-- Bouton créer conversation (visible seulement sur la liste) -->
        <button
          v-if="!selectedConversation || !isMobile"
          @click="showCreateModal = true"
          class="p-2 text-gray-500 hover:text-[#0097b2] hover:bg-gray-100 rounded-lg transition-all duration-200"
          title="Nouvelle conversation"
        >
          <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
        </button>
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
            <span class="text-sm">{{ error }}</span>
          </div>
          <button 
            @click="error = ''"
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
      <!-- Sidebar - Liste des conversations (responsive) -->
      <div 
        class="bg-white border-r border-gray-200 flex flex-col transition-all duration-300"
        :class=" [
          // Desktop: toujours visible avec largeur fixe
          'hidden lg:flex lg:w-80',
          // Mobile: pleine largeur ou cachée selon sélection
          isMobile && !selectedConversation ? 'flex w-full' : '',
          isMobile && selectedConversation ? 'hidden' : ''
        ]"
      >
        <!-- Barre de recherche et bouton création -->
        <div class="p-3 lg:p-4 border-b border-gray-100">
          <!-- Section Messages avec badge - Centrée horizontalement -->
          <div class="flex items-center justify-center mb-4">
            <div class="flex items-center space-x-3">
              <h2 class="text-lg lg:text-xl font-semibold text-gray-900">Vos conversations</h2>
              <Transition name="badge">
                <div 
                  v-if="totalMessagesNonLus > 0"
                  class="bg-red-500 text-white text-xs lg:text-sm font-bold rounded-full min-w-[20px] lg:min-w-[24px] h-5 lg:h-6 flex items-center justify-center px-1 lg:px-2"
                  :title="`${totalMessagesNonLus} message${totalMessagesNonLus > 1 ? 's' : ''} non lu${totalMessagesNonLus > 1 ? 's' : ''} au total`"
                >
                  {{ totalMessagesNonLus > 99 ? '99+' : totalMessagesNonLus }}
                </div>
              </Transition>
            </div>
          </div>

          <div class="flex items-center space-x-2 mb-3">
            <div class="relative flex-1">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Rechercher une conversation..."
                class="w-full pl-10 pr-4 py-2 text-sm lg:text-base border border-gray-200 rounded-lg focus:ring-1 focus:ring-[#0097b2] focus:border-[#0097b2] transition-colors"
              />
              <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
            
            <!-- Bouton nouvelle conversation (version mobile) -->
            <button
              @click="showCreateModal = true"
              class="lg:hidden p-2 bg-[#0097b2] text-white rounded-lg hover:bg-[#007a94] transition-colors"
              title="Nouvelle conversation"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
            </button>
          </div>

          <!-- Bouton nouvelle conversation (version desktop) -->
          <button
            @click="showCreateModal = true"
            class="hidden lg:flex w-full items-center justify-center px-4 py-2 bg-[#0097b2] text-white rounded-lg hover:bg-[#007a94] transition-colors"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Nouvelle conversation
          </button>
        </div>

        <!-- Loading des conversations -->
        <div v-if="loadingConversations" class="flex-1 flex items-center justify-center p-4">
          <div class="text-gray-500 text-sm">Chargement...</div>
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
                @click="loadConversations" 
                class="block w-full px-4 py-2 text-sm bg-[#0097b2] text-white rounded-lg hover:bg-[#007a94] transition-colors"
              >
                Réessayer
              </button>
              <button 
                @click="debugAuth" 
                class="block w-full px-4 py-2 text-sm bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
              >
                Debug connexion
              </button>
            </div>
          </div>
        </div>

        <!-- Liste des conversations avec défilement -->
        <div v-else class="flex-1 overflow-y-auto custom-scrollbar">
          <div v-if="conversations.length === 0" class="flex-1 flex items-center justify-center p-4">
            <div class="text-center text-gray-500">
              <p class="text-sm">Aucune conversation trouvée</p>
            </div>
          </div>
          
          <div
            v-for="conversation in filteredConversations"
            :key="conversation.id_groupe_message"
            @click="selectConversation(conversation)"
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
                  :title="`${conversation.messages_non_lus} message${conversation.messages_non_lus > 1 ? 's' : ''} non lu${conversation.messages_non_lus > 1 ? 's' : ''}`"
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
                    title="Messages non lus"
                  ></div>
                </div>
              </div>
              
              <p class="text-xs lg:text-sm text-gray-500 truncate mt-1"
                 :class="{ 'font-medium text-gray-700': conversation.messages_non_lus > 0 }">
                <span v-if="conversation.dernier_auteur" class="font-medium">{{ conversation.dernier_auteur }}:</span>
                {{ conversation.dernier_contenu || 'Aucun message' }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Zone de conversation (responsive) -->
      <div 
        class="flex-1 flex flex-col min-h-0 transition-all duration-300"
        :class=" [
          // Desktop: toujours visible
          'lg:flex',
          // Mobile: visible seulement si conversation sélectionnée
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
            <h3 class="text-base lg:text-lg font-medium text-gray-900 mb-2">Sélectionnez une conversation</h3>
            <p class="text-sm text-gray-500">Choisissez une conversation dans la liste pour commencer à discuter</p>
          </div>
        </div>

        <!-- Conversation active -->
        <div v-else class="flex-1 flex flex-col min-h-0">
          <!-- Header de la conversation (CLIQUABLE pour voir les membres) -->
          <div 
            @click="showMembersModal = true"
            class="hidden lg:flex bg-white border-b border-gray-200 px-4 lg:px-6 py-3 lg:py-4 items-center justify-between flex-shrink-0 cursor-pointer hover:bg-gray-50 transition-colors"
            title="Voir les membres de la conversation"
          >
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 lg:w-10 lg:h-10 bg-[#0097b2] rounded-full flex items-center justify-center">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
              </div>
              <div>
                <h2 class="text-sm lg:text-base font-semibold text-gray-900">{{ selectedConversation.nom_groupe }}</h2>
                <p class="text-xs lg:text-sm text-gray-500">{{ selectedConversation.nombre_membres || 0 }} membre{{ (selectedConversation.nombre_membres || 0) > 1 ? 's' : '' }}</p>
              </div>
            </div>
            <!-- Icône pour indiquer que c'est cliquable -->
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </div>

          <!-- Messages avec défilement optimisé -->
          <div class="flex-1 relative bg-gray-50 min-h-0">
            <div v-if="loadingMessages" class="absolute inset-0 flex items-center justify-center">
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
                      :class="message.is_current_user 
                        ? 'bg-[#0097b2] text-white rounded-br-sm' 
                        : 'bg-white text-gray-900 rounded-bl-sm border border-gray-200'"
                    >
                      <!-- Contenu texte -->
                      <p v-if="message.contenu_message" class="text-sm lg:text-base whitespace-pre-wrap break-words">
                        {{ message.contenu_message }}
                      </p>

                      <!-- Fichiers attachés -->
                      <div v-if="message.fichiers && message.fichiers.length > 0" class="space-y-2 mt-2">
                        <div
                          v-for="fichier in message.fichiers"
                          :key="fichier.id_fichier"
                          @click="downloadFile(fichier.id_fichier)"
                          class="flex items-center p-2 rounded cursor-pointer transition-colors"
                          :class="message.is_current_user 
                            ? 'bg-white/20 hover:bg-white/30' 
                            : 'bg-gray-50 hover:bg-gray-100'"
                        >
                          <!-- Icône de fichier -->
                          <div class="flex-shrink-0 mr-2">
                            <svg v-if="fichier.type_fichier.startsWith('image/')" class="w-5 h-5" :class="message.is_current_user ? 'text-white' : 'text-green-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <svg v-else-if="fichier.type_fichier === 'application/pdf'" class="w-5 h-5" :class="message.is_current_user ? 'text-white' : 'text-red-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <svg v-else class="w-5 h-5" :class="message.is_current_user ? 'text-white' : 'text-blue-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                          </div>
                          
                          <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate" :class="message.is_current_user ? 'text-white' : 'text-gray-900'">
                              {{ fichier.nom_original }}
                            </p>
                            <p class="text-xs" :class="message.is_current_user ? 'text-white/70' : 'text-gray-500'">
                              {{ formatFileSize(fichier.taille_fichier) }}
                            </p>
                          </div>
                          
                          <svg class="w-4 h-4 flex-shrink-0 ml-2" :class="message.is_current_user ? 'text-white' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                          </svg>
                        </div>
                      </div>

                      <!-- Informations du message au bas de la bulle -->
                      <div class="flex items-center justify-end mt-2 space-x-1">
                        <span class="text-xs opacity-70">
                          {{ formatTime(message.date_envoi) }}
                        </span>
                        
                        <!-- Statut de lecture avancé -->
                        <div v-if="message.is_current_user" class="flex items-center space-x-1">
                          <!-- Envoyé -->
                          <svg class="w-3 h-3 opacity-70" fill="currentColor" viewBox="0 0 20 20">
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
                        </div>
                      </div>
                    </div>

                    <!-- RÉACTIONS SOUS LA BULLE (plus dans la bulle) -->
                    <MessageReactions
                      :message-id="message.id_message"
                      :reactions="transformReactions(message.reactions || {})"
                      :current-user-email="(currentUser as any)?.email || ''"
                      @reaction-toggled="handleReactionToggled(message.id_message, $event)"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Bouton pour descendre en bas (responsive) -->
            <Transition name="fade">
              <button
                v-if="!isAtBottom && messages.length > 0"
                @click="() => scrollToBottom()"
                class="absolute bottom-3 right-3 lg:bottom-4 lg:right-4 w-8 h-8 lg:w-10 lg:h-10 bg-[#0097b2] text-white rounded-full shadow-lg hover:bg-[#007a94] transition-all duration-200 flex items-center justify-center"
                title="Aller en bas"
              >
                <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
              </button>
            </Transition>
          </div>

          <!-- Zone de saisie avec upload de fichiers -->
          <div class="bg-white border-t border-gray-200 p-3 lg:p-4 flex-shrink-0">
            <!-- Zone d'upload si fichiers sélectionnés -->
            <div v-if="showFileUpload" class="mb-3">
              <FileUploadZone
                ref="fileUploadRef"
                :max-files="5"
                :max-file-size="10"
                @files-changed="handleFilesChanged"
              />
            </div>

            <form @submit.prevent="sendMessage" class="flex items-end space-x-2 lg:space-x-3">
              <div class="flex-1">
                <textarea
                  v-model="newMessage"
                  ref="messageInput"
                  placeholder="Tapez votre message..."
                  rows="1"
                  class="w-full px-3 lg:px-4 py-2 text-sm lg:text-base border border-gray-200 rounded-lg resize-none focus:ring-1 focus:ring-[#0097b2] focus:border-[#0097b2] transition-colors"
                  @keydown="handleKeydown"
                  style="max-height: 120px;"
                  :disabled="sendingMessage"
                ></textarea>
              </div>
              
              <!-- Bouton fichiers -->
              <button
                type="button"
                @click="toggleFileUpload"
                class="p-2 text-gray-500 hover:text-[#0097b2] rounded-lg transition-colors flex-shrink-0"
                :class="{ 'text-[#0097b2] bg-blue-50': showFileUpload }"
                title="Joindre des fichiers"
              >
                <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                </svg>
              </button>
              
              <!-- Bouton envoyer -->
              <button
                type="submit"
                :disabled="(!newMessage.trim() && selectedFiles.length === 0) || sendingMessage"
                class="p-2 bg-[#0097b2] text-white rounded-lg hover:bg-[#007a94] disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex-shrink-0"
                title="Envoyer le message"
              >
                <svg v-if="sendingMessage" class="w-4 h-4 lg:w-5 lg:h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
              </button>
            </form>
          </div>
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

// Types pour TypeScript
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

interface Message {
  id_message: number
  contenu_message?: string
  date_envoi: string
  email_auteur: string
  auteur_nom: string
  is_current_user: boolean
  statut_lecture?: string
  fichiers?: FichierMessage[]
  reactions?: Record<string, string[]>
}

interface FichierMessage {
  id_fichier: number
  nom_original: string
  type_fichier: string
  taille_fichier: number
}

interface ApiResponse {
  success: boolean
  error?: string
  conversations?: Conversation[]
  messages?: Message[]
  message?: Message
  reactions?: Record<string, string[]>
}

useHead({
  title: 'Messages - Gestion Entreprise de Résidence'
})

// AJOUTER CES IMPORTS EXPLICITES
import MessageReactions from '~/components/MessageReactions.vue'
import FileUploadZone from '~/components/FileUploadZone.vue'

const authStore = useAuthStore()
const config = useRuntimeConfig()

// Fonction pour récupérer l'utilisateur actuel (unifié via Pinia store)
const getCurrentUser = () => {
  if (authStore.isAuthenticated && authStore.user) {
    return authStore.user
  }
  return null
}

// Computed pour l'utilisateur actuel
const currentUser = computed(() => getCurrentUser())

// Fonction pour récupérer les headers d'authentification
const getAuthHeaders = (isFormData = false) => {
  const headers: any = {
    'Accept': 'application/json'
  }
  
  // Pour FormData, ne pas définir Content-Type (le navigateur le fera)
  if (!isFormData) {
    headers['Content-Type'] = 'application/json'
  }
  
  // Ajouter le token d'authentification (membre ou invité)
  if (authStore.token) {
    headers['Authorization'] = `Bearer ${authStore.token}`
  }
  
  return headers
}

// État réactif
const searchQuery = ref('')
const selectedConversation = ref<Conversation | null>(null)
const newMessage = ref('')
const messagesContainer = ref<HTMLElement | null>(null)
const messageInput = ref<HTMLTextAreaElement | null>(null)
const error = ref('')

// États de chargement
const loadingConversations = ref(true)
const loadingMessages = ref(false)
const sendingMessage = ref(false)

// Données
const conversations = ref<Conversation[]>([])
const messages = ref<Message[]>([])

// Nouveau state pour le défilement
const isAtBottom = ref(true)

// Nouveau state pour gérer la séparation des messages
const premierMessageNonLuIndex = ref<number | null>(null)
const ancienneDerniereConnexion = ref<string | null>(null)

// Variables pour le rafraîchissement automatique
const autoRefreshEnabled = ref(true)
const autoRefreshInterval = ref<NodeJS.Timeout | null>(null)
const lastConversationsUpdate = ref<string>('')
const lastMessagesUpdate = ref<string>('')
const isRefreshing = ref(false)
const refreshIntervalMs = 10000 // 10 secondes pour éviter le rate limiting
const consecutiveErrors = ref(0)
const maxErrors = 3

// Hashes pour détecter les changements
const conversationsHash = ref<string>('')
const messagesHash = ref<string>('')

// Fonction pour calculer l'intervalle avec backoff exponentiel
const getRefreshInterval = () => {
  if (consecutiveErrors.value === 0) return refreshIntervalMs
  // Augmenter l'intervalle exponentiellement en cas d'erreurs
  return refreshIntervalMs * Math.pow(2, Math.min(consecutiveErrors.value, 4))
}

// Fonctions de polling automatique
const checkConversationsChanges = async (): Promise<boolean> => {
  try {
    if (!authStore.token) return false

    const response = await $fetch<any>(`${config.public.apiBase}/conversations/check-changes`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json',
      },
      timeout: 5000 // Timeout de 5 secondes
    })

    if (response.success && response.hash !== conversationsHash.value) {
      conversationsHash.value = response.hash
      console.log('🔄 Changements détectés dans les conversations')
      consecutiveErrors.value = 0 // Reset des erreurs en cas de succès
      return true
    }
    consecutiveErrors.value = 0 // Reset des erreurs en cas de succès
    return false
  } catch (error: any) {
    consecutiveErrors.value++
    console.error('❌ Erreur lors de la vérification des conversations:', error)
    
    // Si trop d'erreurs consécutives, désactiver temporairement
    if (consecutiveErrors.value >= maxErrors) {
      console.warn(`⚠️ ${maxErrors} erreurs consécutives, augmentation de l'intervalle`)
    }
    
    // Si erreur 429 (Too Many Requests), augmenter l'intervalle
    if (error.status === 429) {
      console.warn('⚠️ Rate limit atteint, augmentation de l\'intervalle')
      consecutiveErrors.value += 2 // Pénalité supplémentaire pour rate limiting
    }
    
    return false
  }
}

const checkMessagesChanges = async (): Promise<boolean> => {
  try {
    if (!authStore.token || !selectedConversation.value) return false

    const response = await $fetch<any>(`${config.public.apiBase}/messages/${selectedConversation.value.id_groupe_message}/check-changes`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json',
      },
      timeout: 5000 // Timeout de 5 secondes
    })

    if (response.success && response.hash !== messagesHash.value) {
      messagesHash.value = response.hash
      console.log('🔄 Changements détectés dans les messages')
      return true
    }
    return false
  } catch (error: any) {
    console.error('❌ Erreur lors de la vérification des messages:', error)
    
    // Si erreur 429 (Too Many Requests), augmenter l'intervalle
    if (error.status === 429) {
      console.warn('⚠️ Rate limit atteint pour les messages')
      consecutiveErrors.value += 1
    }
    
    return false
  }
}

const refreshConversationsIfNeeded = async () => {
  const hasChanges = await checkConversationsChanges()
  if (hasChanges) {
    console.log('🔄 Rafraîchissement des conversations...')
    await loadConversations()
  }
}

const refreshMessagesIfNeeded = async () => {
  const hasChanges = await checkMessagesChanges()
  if (hasChanges) {
    console.log('🔄 Rafraîchissement des messages...')
    const shouldScrollToBottom = isAtBottom.value
    await loadMessages(selectedConversation.value!.id_groupe_message)
    if (shouldScrollToBottom) {
      await nextTick()
      scrollToBottom()
    }
  }
}

const performAutoRefresh = async () => {
  if (!autoRefreshEnabled.value || isRefreshing.value) return

  isRefreshing.value = true
  try {
    // Vérifier et rafraîchir les conversations
    await refreshConversationsIfNeeded()
    
    // Vérifier et rafraîchir les messages de la conversation active
    if (selectedConversation.value) {
      await refreshMessagesIfNeeded()
    }
  } catch (error) {
    console.error('❌ Erreur lors du rafraîchissement automatique:', error)
  } finally {
    isRefreshing.value = false
  }
}

const startAutoRefresh = () => {
  if (autoRefreshInterval.value) {
    clearInterval(autoRefreshInterval.value)
  }
  
  if (autoRefreshEnabled.value) {
    const interval = getRefreshInterval()
    console.log(`🚀 Démarrage du rafraîchissement automatique (toutes les ${interval/1000} secondes)`)
    
    autoRefreshInterval.value = setInterval(async () => {
      await performAutoRefresh()
      
      // Redémarrer avec un nouvel intervalle si nécessaire (en cas d'erreurs)
      const newInterval = getRefreshInterval()
      if (newInterval !== interval) {
        console.log(`🔄 Changement d'intervalle: ${newInterval/1000} secondes`)
        startAutoRefresh() // Redémarrer avec le nouvel intervalle
      }
    }, interval)
  }
}

const stopAutoRefresh = () => {
  if (autoRefreshInterval.value) {
    console.log('⏹️ Arrêt du rafraîchissement automatique')
    clearInterval(autoRefreshInterval.value)
    autoRefreshInterval.value = null
  }
}

const toggleAutoRefresh = () => {
  autoRefreshEnabled.value = !autoRefreshEnabled.value
  if (autoRefreshEnabled.value) {
    startAutoRefresh()
  } else {
    stopAutoRefresh()
  }
}

// Conversations filtrées
const filteredConversations = computed(() => {
  if (!searchQuery.value) return conversations.value
  
  return conversations.value.filter((conv: Conversation) => 
    conv.nom_groupe?.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

// Computed pour le total de messages non lus
const totalMessagesNonLus = computed(() => {
  return conversations.value.reduce((total, conv) => total + conv.messages_non_lus, 0)
})

// Gérer le défilement
const handleScroll = () => {
  if (messagesContainer.value) {
    const { scrollTop, scrollHeight, clientHeight } = messagesContainer.value
    // Considérer qu'on est en bas si on est à moins de 50px du bas
    isAtBottom.value = scrollHeight - scrollTop - clientHeight < 50
  }
}

// Scroll vers le bas avec animation fluide
const scrollToBottom = (smooth: boolean = true) => {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTo({
      top: messagesContainer.value.scrollHeight,
      behavior: smooth ? 'smooth' : 'auto'
    })
  }
}

// Charger les conversations avec debug
const loadConversations = async () => {
  try {
    error.value = ''
    loadingConversations.value = true
    
    console.log('=== CHARGEMENT DES CONVERSATIONS ===')
    console.log('Utilisateur:', (currentUser.value as any)?.email)
    console.log('Type:', authStore.isAuthenticated ? 'member' : 'guest')
    console.log('Token disponible:', !!authStore.token)
    
    console.log('URL:', `${config.public.apiBase}/conversations`)
    
    // Test de connectivité de base
    try {
      console.log('🔍 Test de connectivité vers:', config.public.apiBase)
      const testResponse = await fetch(`${config.public.apiBase.replace('/api', '')}`)
      console.log('Status de connectivité:', testResponse.status)
    } catch (connectError) {
      console.error('❌ Erreur de connectivité:', connectError)
      throw new Error('Impossible de se connecter au serveur. Vérifiez que le backend est démarré.')
    }
    
    const response = await $fetch<ApiResponse>(`${config.public.apiBase}/conversations`, {
      headers: getAuthHeaders(),
      timeout: 10000 // Timeout de 10 secondes
    })
    
    console.log('✅ Response from API:', response)
    
    if (response.success && Array.isArray(response.conversations)) {
      conversations.value = response.conversations
      console.log('✅ Conversations chargées:', conversations.value.length)
    } else {
      console.error('❌ Réponse API non valide:', response)
      throw new Error(response.error || 'Réponse API invalide')
    }
    
  } catch (err: any) {
    console.error('❌ Erreur lors du chargement des conversations:', err)
    console.error('Status:', err.status)
    console.error('Data:', err.data)
    console.error('Message:', err.message)
    
    // Si erreur 401, rediriger vers l'accueil
    if (err.status === 401) {
      console.log('🔄 Erreur 401, redirection vers accueil')
      if (authStore.isAuthenticated) {
        authStore.clearAuth()
      }
      await navigateTo('/')
      return
    }
    
    // Si erreur de réseau
    if (err.code === 'NETWORK_ERROR' || err.message?.includes('fetch')) {
      error.value = 'Erreur de connexion au serveur. Vérifiez que le backend est démarré.'
    } else {
      error.value = err.data?.message || err.message || 'Impossible de charger les conversations'
    }
    conversations.value = []
  } finally {
    loadingConversations.value = false
  }
}

// Charger les messages d'un groupe
const loadMessages = async (groupId: number) => {
  try {
    loadingMessages.value = true
    
    console.log('=== CHARGEMENT DES MESSAGES ===')
    console.log('Groupe ID:', groupId)
    console.log('Token:', !!authStore.token)
    
    const response = await $fetch<ApiResponse>(`${config.public.apiBase}/messages/${groupId}`, {
      headers: getAuthHeaders()
    })
    
    console.log('✅ Messages response:', response)
    
    if (response.success && response.messages) {
      messages.value = response.messages
      
      // Scroll vers le bas après chargement des messages avec un délai
      await nextTick()
      setTimeout(() => {
        scrollToBottom(false) // Scroll instantané
        isAtBottom.value = true
      }, 100) // Petit délai pour s'assurer que le DOM est rendu
    } else {
      throw new Error(response.error || 'Erreur lors du chargement des messages')
    }
    
  } catch (err: any) {
    console.error('❌ Erreur lors du chargement des messages:', err)
    console.error('Status:', err.status)
    console.error('Data:', err.data)
    console.error('Message:', err.message)
    
    if (err.status === 401) {
      console.log('🔄 Erreur 401, redirection vers accueil')
      if (authStore.isAuthenticated) {
        authStore.clearAuth()
      }
      await navigateTo('/')
      return
    }
    
    let errorMessage = 'Impossible de charger les messages'
    if (err.data?.message) {
      errorMessage = err.data.message
    } else if (err.message) {
      errorMessage = err.message
    }
    
    error.value = errorMessage
    messages.value = []
  } finally {
    loadingMessages.value = false
  }
}

// Sélectionner une conversation et marquer comme lue
const selectConversation = async (conversation: Conversation) => {
  selectedConversation.value = conversation
  
  // Réinitialiser le hash des messages pour cette nouvelle conversation
  messagesHash.value = ''
  
  // Charger les messages (cela met automatiquement à jour la derniere_connexion)
  await loadMessages(conversation.id_groupe_message)
  
  // Mettre à jour localement le compteur de messages non lus
  const index = conversations.value.findIndex(c => c.id_groupe_message === conversation.id_groupe_message)
  if (index !== -1) {
    conversations.value[index].messages_non_lus = 0
  }
}

// Fonction pour marquer manuellement comme lu (si besoin)
const markConversationAsRead = async (groupId: number) => {
  try {
    await $fetch(`${config.public.apiBase}/conversations/${groupId}/mark-read`, {
      method: 'POST',
      headers: getAuthHeaders()
    })
    
    // Mettre à jour localement
    const index = conversations.value.findIndex(c => c.id_groupe_message === groupId)
    if (index !== -1) {
      conversations.value[index].messages_non_lus = 0
    }
  } catch (error) {
    console.error('Erreur lors du marquage comme lu:', error)
  }
}

// Envoyer un message et mettre à jour les compteurs
const sendMessage = async () => {
  if ((!newMessage.value.trim() && selectedFiles.value.length === 0) || !selectedConversation.value || sendingMessage.value) return
  
  const wasAtBottom = isAtBottom.value
  
  try {
    sendingMessage.value = true
    
    console.log('=== ENVOI DE MESSAGE ===')
    console.log('Conversation:', selectedConversation.value.id_groupe_message)
    console.log('Contenu:', newMessage.value.trim())
    console.log('Fichiers:', selectedFiles.value.length)
    console.log('Token:', !!authStore.token)
    
    const formData = new FormData()
    if (newMessage.value.trim()) {
      formData.append('contenu', newMessage.value.trim())
    }
    
    selectedFiles.value.forEach((file, index) => {
      formData.append(`fichiers[${index}]`, file)
    })
    
    console.log('FormData entries:')
    for (let [key, value] of formData.entries()) {
      console.log(key, value)
    }
    
    const response = await $fetch<ApiResponse>(`${config.public.apiBase}/messages/${selectedConversation.value.id_groupe_message}`, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Authorization': authStore.token ? `Bearer ${authStore.token}` : ''
        // Ne pas définir Content-Type pour FormData, le navigateur le fera automatiquement
      },
      body: formData
    })
    
    console.log('✅ Response from sendMessage:', response)
    
    if (response.success && response.message) {
      messages.value.push(response.message)
      newMessage.value = ''
      selectedFiles.value = []
      showFileUpload.value = false
      fileUploadRef.value?.clearFiles()
      
      // Mettre à jour la conversation dans la liste
      const conversationIndex = conversations.value.findIndex(
        c => c.id_groupe_message === selectedConversation.value?.id_groupe_message
      )
      if (conversationIndex !== -1) {
        conversations.value[conversationIndex].dernier_contenu = response.message.contenu_message || '[Fichier partagé]'
        conversations.value[conversationIndex].dernier_auteur = response.message.auteur_nom
        conversations.value[conversationIndex].derniere_activite = response.message.date_envoi
      }
      
      // Scroll vers le bas seulement si l'utilisateur était déjà en bas
      nextTick(() => {
        if (wasAtBottom) {
          scrollToBottom(true)
        }
      })
    } else {
      throw new Error(response.error || 'Erreur lors de l\'envoi du message')
    }
    
  } catch (err: any) {
    console.error('❌ Erreur lors de l\'envoi du message:', err)
    console.error('Status:', err.status)
    console.error('Data:', err.data)
    console.error('Message:', err.message)
    
    if (err.status === 401) {
      console.log('🔄 Erreur 401, redirection vers accueil')
      if (authStore.isAuthenticated) {
        authStore.clearAuth()
      }
      await navigateTo('/')
      return
    }
    
    // Afficher l'erreur à l'utilisateur
    let errorMessage = 'Erreur lors de l\'envoi du message'
    if (err.data?.message) {
      errorMessage = err.data.message
    } else if (err.message) {
      errorMessage = err.message
    }
    
    // TODO: Afficher une notification d'erreur à l'utilisateur
    console.error('Erreur à afficher:', errorMessage)
    error.value = errorMessage
    
    // Effacer l'erreur après 5 secondes
    setTimeout(() => {
      error.value = ''
    }, 5000)
  } finally {
    sendingMessage.value = false
  }
}

// Gérer les touches du clavier
const handleKeydown = (event: KeyboardEvent) => {
  if (event.key === 'Enter' && !event.shiftKey) {
    event.preventDefault()
    sendMessage()
  }
}

// Transformer les réactions du format API vers le format attendu par le composant
const transformReactions = (reactions: Record<string, string[]>) => {
  const transformed: Record<string, { count: number; users: { email: string; nom: string; }[] }> = {}
  
  for (const [emoji, users] of Object.entries(reactions)) {
    transformed[emoji] = {
      count: users.length,
      users: users.map(email => ({ email, nom: email.split('@')[0] }))
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

// Watcher pour auto-resize du textarea
watch(newMessage, () => {
  nextTick(() => {
    if (messageInput.value) {
      messageInput.value.style.height = 'auto'
      messageInput.value.style.height = Math.min(messageInput.value.scrollHeight, 120) + 'px'
    }
  })
})

// Nouveau state pour mobile
const isMobile = ref(false)

// Fonction pour gérer le retour sur mobile
const goBackToConversations = () => {
  if (isMobile.value) {
    selectedConversation.value = null
  }
}

// Fonction pour le menu mobile
const toggleMobileMenu = () => {
  console.log('Toggle mobile menu')
}

// Détecter la taille d'écran
const updateScreenSize = () => {
  isMobile.value = window.innerWidth < 1024
}

// Initialiser au montage
onMounted(async () => {
  console.log('=== MESSAGES PAGE MOUNTED ===')
  console.log('process.client:', process.client)
  
  // S'assurer qu'on est côté client
  if (!process.client) {
    console.log('⚠️ Pas côté client, attendre...')
    return
  }
  
  // Détecter la taille d'écran
  updateScreenSize()
  window.addEventListener('resize', updateScreenSize)
  
  // Initialiser l'auth depuis localStorage
  authStore.initAuth()
  
  console.log('Auth state après init:', {
    authenticated: authStore.isAuthenticated,
    user: (currentUser.value as any)?.email,
    token: !!authStore.token
  })
  
  // Délai pour s'assurer que le localStorage est accessible et synchronisé
  console.log('⏱️ Attente synchronisation localStorage...')
  await new Promise(resolve => setTimeout(resolve, 200))
  // Initialiser l'authentification
  authStore.initAuth()
  
  // Vérifier qu'un utilisateur est authentifié
  const currentUserData = getCurrentUser()
  
  if (!currentUserData) {
    console.log('❌ Aucun utilisateur authentifié, redirection vers index')
    await navigateTo('/')
    return
  }
  
  console.log('✅ Utilisateur authentifié trouvé:', {
    email: (currentUserData as any).email,
    role: (currentUserData as any).role
  })
  
  // Vérifier la validité du token
  if (authStore.token) {
    try {
      const isValid = await authStore.checkAuth()
      if (!isValid) {
        console.log('❌ Token invalide, redirection vers index')
        await navigateTo('/')
        return
      }
    } catch (error) {
      console.error('❌ Erreur vérification auth:', error)
      await navigateTo('/')
      return
    }
  }
  
  console.log('✅ Auth valide, chargement des conversations')
  
  try {
    await loadConversations()
    // Démarrer le rafraîchissement automatique après le premier chargement
    startAutoRefresh()
  } catch (err) {
    console.error('❌ Erreur lors du chargement:', err)
    error.value = 'Impossible de charger les conversations. Veuillez rafraîchir la page.'
  }
})

onUnmounted(() => {
  window.removeEventListener('resize', updateScreenSize)
  // Arrêter le rafraîchissement automatique
  stopAutoRefresh()
})

// Nouvel état pour la modal
const showCreateModal = ref(false)

// Nouvelle fonction pour gérer la création de conversation
const onConversationCreated = (newConversation: Conversation) => {
  console.log('Nouvelle conversation créée:', newConversation)
  
  // Ajouter la nouvelle conversation au début de la liste
  conversations.value.unshift(newConversation)
  
  // Sélectionner automatiquement la nouvelle conversation
  selectConversation(newConversation)
  
  // Fermer la modal
  showCreateModal.value = false
}

// Nouvel état pour la modal des membres
const showMembersModal = ref(false)

// Nouvelle fonction pour gérer la mise à jour des membres
const onMembersUpdated = (updatedMembers: any[]) => {
  console.log('Membres mis à jour:', updatedMembers)
  
  // Mettre à jour le nombre de membres dans la conversation sélectionnée
  if (selectedConversation.value) {
    selectedConversation.value.nombre_membres = updatedMembers.length
    
    // Mettre à jour aussi dans la liste des conversations
    const index = conversations.value.findIndex(c => c.id_groupe_message === selectedConversation.value?.id_groupe_message)
    if (index !== -1) {
      conversations.value[index].nombre_membres = updatedMembers.length
    }
  }
}

// Nouvelles variables pour les fichiers
const showFileUpload = ref(false)
const selectedFiles = ref<File[]>([])
const fileUploadRef = ref()

// Nouvelles fonctions
const toggleFileUpload = () => {
  showFileUpload.value = !showFileUpload.value
  if (!showFileUpload.value) {
    selectedFiles.value = []
    fileUploadRef.value?.clearFiles()
  }
}

const handleFilesChanged = (files: File[]) => {
  selectedFiles.value = files
}

const formatFileSize = (bytes: number): string => {
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i]
}

const downloadFile = async (fichierId: number) => {
  try {
    const authHeaders = getAuthHeaders()
    const response = await fetch(`${config.public.apiBase}/files/${fichierId}`, {
      headers: authHeaders
    })
    
    if (response.ok) {
      const blob = await response.blob()
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = 'fichier'
      document.body.appendChild(a)
      a.click()
      window.URL.revokeObjectURL(url)
      document.body.removeChild(a)
    }
  } catch (error) {
    console.error('Erreur lors du téléchargement:', error)
  }
}

const handleReactionToggled = async (messageId: number, emoji: string) => {
  try {
    const response = await $fetch<{
      success: boolean
      reactions: Record<string, string[]>
    }>(`${config.public.apiBase}/messages/${messageId}/reactions`, {
      method: 'POST',
      headers: getAuthHeaders(),
      body: { emoji }
    })
    
    console.log('✅ Reaction response:', response)
    
    if (response.success) {
      // Mettre à jour les réactions du message
      const messageIndex = messages.value.findIndex(m => m.id_message === messageId)
      if (messageIndex !== -1) {
        messages.value[messageIndex].reactions = response.reactions
        console.log('✅ Message reactions updated:', messages.value[messageIndex].reactions)
      }
    }
  } catch (error) {
    console.error('❌ Erreur lors de la réaction:', error)
  }
}

// Fonction de debug pour diagnostiquer les problèmes
const debugAuth = async () => {
  console.log('=== DEBUG AUTHENTIFICATION ===')
  console.log('Auth Store State:', {
    user: currentUser.value,
    token: authStore.token,
    isAuthenticated: authStore.isAuthenticated,
    isLoggedIn: authStore.isLoggedIn
  })
  
  console.log('LocalStorage:', {
    token: process.client ? localStorage.getItem('auth_token') : 'N/A',
    user: process.client ? localStorage.getItem('user') : 'N/A'
  })
  
  console.log('Runtime Config:', {
    apiBase: config.public.apiBase
  })
  
  // Test de connectivité
  try {
    console.log('🔍 Test de connectivité basique...')
    const response = await fetch(`${config.public.apiBase.replace('/api', '')}`)
    console.log('Connectivité OK:', response.status)
  } catch (err) {
    console.error('❌ Erreur de connectivité:', err)
    alert('Erreur de connectivité au serveur. Vérifiez que le backend Laravel est démarré sur http://127.0.0.1:8000')
    return
  }
  
  // Test de l'endpoint auth
  if (authStore.token) {
    try {
      console.log('🔍 Test de l\'endpoint auth...')
      const response = await $fetch(`${config.public.apiBase}/check`, {
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Accept': 'application/json'
        }
      })
      console.log('Auth check OK:', response)
    } catch (err) {
      console.error('❌ Erreur auth check:', err)
    }
  }
  
  alert('Logs de debug affichés dans la console du navigateur (F12)')
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

.custom-scrollbar::-webkit-scrollbar-corner {
  background: #f7fafc;
}

/* Animation pour le bouton de retour en bas */
.fade-enter-active, .fade-leave-active {
  transition: all 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
  transform: translateY(10px) scale(0.9);
}

/* Animation pour le badge de messages non lus */
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

/* Pulse animation pour attirer l'attention */
@keyframes pulse-red {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

.animate-pulse-red {
  animation: pulse-red 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Style pour les conversations avec messages non lus */
.conversation-unread {
  background: linear-gradient(90deg, #fef2f2 0%, #ffffff 10%);
  border-left: 3px solid #ef4444;
}

/* Améliorations responsive */
@media (max-width: 1024px) {
  .custom-scrollbar::-webkit-scrollbar {
    width: 4px;
  }
  
  /* Améliorer les interactions tactiles */
  button, .cursor-pointer {
    -webkit-tap-highlight-color: transparent;
  }
  
  /* Optimiser les animations pour mobile */
  .transition-all {
    transition-duration: 200ms;
  }
}

/* Styles pour les petits écrans */
@media (max-width: 640px) {
  .space-y-3 > :not([hidden]) ~ :not([hidden]) {
    margin-top: 0.5rem;
  }
}

/* Effet de transition fluide pour le changement de vue mobile */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>



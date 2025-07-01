<template>
  <div class="h-screen bg-white flex flex-col">
    <!-- Header responsive -->
    <AppHeader 
      title="Messages" 
      back-to="/principale"
      :show-back-button="isMobile && selectedConversation"
      :show-mobile-menu="!selectedConversation"
      :badge="{ count: totalMessagesNonLus, title: `${totalMessagesNonLus} message${totalMessagesNonLus > 1 ? 's' : ''} non lu${totalMessagesNonLus > 1 ? 's' : ''} au total` }"
      @go-back="goBackToConversations"
      @toggle-mobile-menu="toggleMobileMenu"
    >
      <template #actions>
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
          <div class="text-center text-red-500">
            <p class="text-sm mb-2">{{ error }}</p>
            <button @click="loadConversations" class="text-sm text-[#0097b2] hover:text-[#007a94]">
              Réessayer
            </button>
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
                      :reactions="message.reactions || {}"
                      :current-user-email="authStore.user?.email || ''"
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
                @click="scrollToBottom"
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
// filepath: c:\wamp64V2\www\Gestion_entreprise-residence\frontend\pages\messages.vue
// Types pour TypeScript (garder les interfaces existantes)
// ...existing interfaces...

definePageMeta({
  middleware: 'auth'
})

useHead({
  title: 'Messages - Gestion Entreprise de Résidence'
})

// AJOUTER CES IMPORTS EXPLICITES
import MessageReactions from '~/components/MessageReactions.vue'
import FileUploadZone from '~/components/FileUploadZone.vue'

const authStore = useAuthStore()
const config = useRuntimeConfig()

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
    console.log('Token disponible:', !!authStore.token)
    console.log('Token value:', authStore.token?.substring(0, 20) + '...')
    console.log('Utilisateur:', authStore.user?.email)
    console.log('Authenticated:', authStore.isAuthenticated)
    
    if (!authStore.token) {
      console.error('❌ Token manquant')
      throw new Error('Token d\'authentification manquant')
    }
    
    const headers = {
      'Authorization': `Bearer ${authStore.token}`,
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    }
    
    console.log('Headers:', headers)
    console.log('URL:', `${config.public.apiBase}/conversations`)
    
    const response = await $fetch<ApiResponse>(`${config.public.apiBase}/conversations`, {
      headers
    })
    
    console.log('✅ Response from API:', response)
    
    if (response.success && response.conversations) {
      conversations.value = response.conversations
      console.log('✅ Conversations chargées:', conversations.value.length)
    } else {
      console.error('❌ Réponse API non valide:', response)
      throw new Error(response.error || 'Erreur lors du chargement des conversations')
    }
    
  } catch (err: any) {
    console.error('❌ Erreur lors du chargement des conversations:', err)
    console.error('Status:', err.status)
    console.error('Data:', err.data)
    
    // Si erreur 401, rediriger vers login
    if (err.status === 401) {
      console.log('🔄 Erreur 401, redirection vers login')
      authStore.clearAuth()
      await navigateTo('/login')
      return
    }
    
    error.value = err.data?.message || err.message || 'Impossible de charger les conversations'
    conversations.value = []
  } finally {
    loadingConversations.value = false
  }
}

// Charger les messages d'un groupe
const loadMessages = async (groupId: number) => {
  try {
    loadingMessages.value = true
    
    const response = await $fetch<ApiResponse>(`${config.public.apiBase}/messages/${groupId}`, {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json'
      }
    })
    
    console.log('Messages response:', response)
    
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
    console.error('Erreur lors du chargement des messages:', err)
    
    if (err.status === 401) {
      authStore.clearAuth()
      await navigateTo('/login')
      return
    }
    
    error.value = err.data?.message || err.message || 'Impossible de charger les messages'
    messages.value = []
  } finally {
    loadingMessages.value = false
  }
}

// Sélectionner une conversation et marquer comme lue
const selectConversation = async (conversation: Conversation) => {
  selectedConversation.value = conversation
  
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
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json'
      }
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
    
    const formData = new FormData()
    if (newMessage.value.trim()) {
      formData.append('contenu', newMessage.value.trim())
    }
    
    selectedFiles.value.forEach((file, index) => {
      formData.append(`fichiers[${index}]`, file)
    })
    
    const response = await $fetch<ApiResponse>(`${config.public.apiBase}/messages/${selectedConversation.value.id_groupe_message}`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json'
      },
      body: formData
    })
    
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
    console.error('Erreur lors de l\'envoi du message:', err)
    
    if (err.status === 401) {
      authStore.clearAuth()
      await navigateTo('/login')
      return
    }
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
  
  // Détecter la taille d'écran
  updateScreenSize()
  window.addEventListener('resize', updateScreenSize)
  
  // Initialiser l'auth depuis localStorage
  authStore.initAuth()
  
  console.log('Auth state après init:', {
    authenticated: authStore.isAuthenticated,
    user: authStore.user?.email,
    token: !!authStore.token
  })
  
  // Vérifier encore une fois
  if (!authStore.isAuthenticated || !authStore.token) {
    console.log('❌ Pas d\'authentification, redirection vers login')
    await navigateTo('/login')
    return
  }
  
  // Tester la validité du token
  try {
    const isValid = await authStore.checkAuth()
    if (!isValid) {
      console.log('❌ Token invalide, redirection vers login')
      await navigateTo('/login')
      return
    }
  } catch (error) {
    console.error('Erreur vérification auth:', error)
    await navigateTo('/login')
    return
  }
  
  console.log('✅ Auth valide, chargement des conversations')
  await loadConversations()
})

onUnmounted(() => {
  window.removeEventListener('resize', updateScreenSize)
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
    const response = await fetch(`${config.public.apiBase}/files/${fichierId}`, {
      headers: {
        'Authorization': `Bearer ${authStore.token}`
      }
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
    const response = await $fetch(`${config.public.apiBase}/messages/${messageId}/reactions`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Content-Type': 'application/json'
      },
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



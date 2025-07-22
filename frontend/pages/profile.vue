<template>
  <div class="min-h-screen bg-gradient-to-br from-[#0097b2]/5 via-white to-[#0097b2]/10">
    <!-- Header -->
    <AppHeader :title="''" />

    <!-- Contenu principal -->
    <div class="max-w-5xl mx-auto px-4 py-8 lg:py-12">
      <!-- Hero Section avec informations utilisateur -->
      <div class="relative overflow-hidden bg-white/70 backdrop-blur-sm rounded-3xl shadow-xl border border-// Modales
const showEditModal = ref(false)
const showPasswordModal = ref(false)
const showCurrentPasswordModal = ref(false)
const showAvatarModal = ref(false)/50 p-8 mb-8">
        <!-- Gradient décoratif -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-[#0097b2]/20 to-transparent rounded-full -translate-y-32 translate-x-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr from-[#0097b2]/10 to-transparent rounded-full translate-y-24 -translate-x-24"></div>
        
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:space-x-8">
          <!-- Avatar avec effet moderne -->
          <div class="flex justify-center lg:justify-start mb-6 lg:mb-0">
            <div class="relative group">
              <div v-if="avatarUrl" class="w-28 h-28 lg:w-32 lg:h-32 rounded-full overflow-hidden transform transition-all duration-300 border-4 border-white shadow-lg">
                <img 
                  :src="avatarUrl" 
                  :alt="`Photo de profil de ${user?.prenom} ${user?.nom}`"
                  class="w-full h-full object-cover"
                  @error="handleAvatarError"
                />
              </div>
              <div v-else class="w-28 h-28 lg:w-32 lg:h-32 bg-gradient-to-br from-[#0097b2] to-[#008699] rounded-full flex items-center justify-center">
                <svg class="w-14 h-14 lg:w-16 lg:h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              <!-- Indicateur en ligne -->
              <div class="absolute bottom-2 right-2 w-6 h-6 bg-green-400 border-4 border-white rounded-full shadow-lg"></div>
            </div>
          </div>

          <!-- Informations avec design moderne -->
          <div class="text-center lg:text-left flex-1">
            <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-gray-900 to-[#0097b2] bg-clip-text text-transparent mb-3">
              {{ `${user?.prenom} ${user?.nom}` || 'Utilisateur' }}
            </h1>
            <div class="space-y-2 mb-4">
              <p class="text-gray-600 flex items-center justify-center lg:justify-start">
                <svg class="w-4 h-4 mr-2 text-[#0097b2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                </svg>
                {{ user?.email }}
              </p>
              <p class="text-gray-600 flex items-center justify-center lg:justify-start">
                <svg class="w-4 h-4 mr-2 text-[#0097b2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
                {{ formatPhoneNumber(user?.numero_telephone || '') }}
              </p>
            </div>
            <div class="flex flex-wrap justify-center lg:justify-start gap-2">
              <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold shadow-lg"
                    :class="[roleColors.background, roleColors.text]">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     :class="roleColors.icon">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ userRole }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Sections modernes -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Paramètres du compte avec design moderne -->
        <div class="group relative overflow-hidden bg-white/80 backdrop-blur-sm rounded-3xl shadow-xl border border-white/50 p-8 hover:shadow-2xl transition-all duration-500 hover:-translate-y-1">
          <!-- Gradient décoratif -->
          <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-[#0097b2]/10 to-transparent rounded-full -translate-y-16 translate-x-16"></div>
          
          <!-- Message de succès avec design moderne -->
          <Transition name="success" mode="out-in">
            <div v-if="successMessage" class="relative mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200/50 rounded-2xl flex items-center backdrop-blur-sm">
              <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center mr-3 shadow-lg">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
              </div>
              <span class="text-green-800 font-medium">{{ successMessage }}</span>
            </div>
          </Transition>
          
          <div class="relative">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
              <div class="w-10 h-10 bg-gradient-to-br from-[#0097b2] to-[#008699] rounded-xl flex items-center justify-center mr-3 shadow-lg">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              Paramètres du compte
            </h3>
            
            <div class="space-y-4">
              <button @click="openEditModal" class="w-full group/btn p-4 rounded-2xl hover:bg-gradient-to-r hover:from-[#0097b2]/5 hover:to-[#0097b2]/10 transition-all duration-300 flex items-center justify-between border border-transparent hover:border-[#0097b2]/20">
                <div class="flex items-center">
                  <div class="w-8 h-8 bg-gradient-to-br from-[#0097b2]/20 to-[#0097b2]/30 rounded-lg flex items-center justify-center mr-3 group-hover/btn:from-[#0097b2] group-hover/btn:to-[#008699] transition-all duration-300">
                    <svg class="w-4 h-4 text-[#0097b2] group-hover/btn:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </div>
                  <span class="text-gray-700 font-medium group-hover/btn:text-[#0097b2] transition-colors duration-300">Modifier les informations</span>
                </div>
                <svg class="w-5 h-5 text-gray-400 group-hover/btn:text-[#0097b2] group-hover/btn:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
              
              <button @click="openPasswordModal" class="w-full group/btn p-4 rounded-2xl hover:bg-gradient-to-r hover:from-[#0097b2]/5 hover:to-[#0097b2]/10 transition-all duration-300 flex items-center justify-between border border-transparent hover:border-[#0097b2]/20">
                <div class="flex items-center">
                  <div class="w-8 h-8 bg-gradient-to-br from-[#0097b2]/20 to-[#0097b2]/30 rounded-lg flex items-center justify-center mr-3 group-hover/btn:from-[#0097b2] group-hover/btn:to-[#008699] transition-all duration-300">
                    <svg class="w-4 h-4 text-[#0097b2] group-hover/btn:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                  </div>
                  <span class="text-gray-700 font-medium group-hover/btn:text-[#0097b2] transition-colors duration-300">Changer le mot de passe</span>
                </div>
                <svg class="w-5 h-5 text-gray-400 group-hover/btn:text-[#0097b2] group-hover/btn:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
              
              <button @click="openAvatarModal" class="w-full group/btn p-4 rounded-2xl hover:bg-gradient-to-r hover:from-[#0097b2]/5 hover:to-[#0097b2]/10 transition-all duration-300 flex items-center justify-between border border-transparent hover:border-[#0097b2]/20">
                <div class="flex items-center">
                  <!-- Aperçu miniature de l'avatar -->
                  <div class="w-8 h-8 rounded-lg overflow-hidden mr-3 flex-shrink-0">
                    <div v-if="avatarUrl" class="w-full h-full">
                      <img 
                        :src="avatarUrl" 
                        :alt="'Aperçu avatar'"
                        class="w-full h-full object-cover"
                        @error="handleAvatarError"
                      />
                    </div>
                    <div v-else class="w-full h-full bg-gradient-to-br from-[#0097b2]/20 to-[#0097b2]/30 group-hover/btn:from-[#0097b2] group-hover/btn:to-[#008699] transition-all duration-300 flex items-center justify-center">
                      <svg class="w-4 h-4 text-[#0097b2] group-hover/btn:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      </svg>
                    </div>
                  </div>
                  <div class="flex flex-col items-start flex-1">
                    <div class="flex items-center">
                      <span class="text-gray-700 font-medium group-hover/btn:text-[#0097b2] transition-colors duration-300">Photo de profil</span>
                      <span v-if="avatarUrl" class="ml-2 text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full">Configurée</span>
                      <span v-else class="ml-2 text-xs text-orange-600 bg-orange-100 px-2 py-1 rounded-full">Aucune photo</span>
                    </div>
                    <span class="text-xs text-gray-500 mt-1">{{ avatarUrl ? 'Modifier ou supprimer' : 'Ajouter une photo de profil' }}</span>
                  </div>
                </div>
                <svg class="w-5 h-5 text-gray-400 group-hover/btn:text-[#0097b2] group-hover/btn:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Statistiques avec design moderne -->
        <div class="group relative overflow-hidden bg-white/80 backdrop-blur-sm rounded-3xl shadow-xl border border-white/50 p-8 hover:shadow-2xl transition-all duration-500 hover:-translate-y-1">
          <!-- Gradient décoratif -->
          <div class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-[#0097b2]/10 to-transparent rounded-full translate-y-16 -translate-x-16"></div>
          
          <div class="relative">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
              <div class="w-10 h-10 bg-gradient-to-br from-[#0097b2] to-[#008699] rounded-xl flex items-center justify-center mr-3 shadow-lg">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
              </div>
              Statistiques
            </h3>
            
            <div class="grid grid-cols-1 gap-4">
              <!-- Statistiques principales -->
              <div class="grid grid-cols-2 gap-4">
                <div class="group/stat p-4 bg-gradient-to-br from-[#0097b2]/5 to-[#0097b2]/10 rounded-2xl border border-[#0097b2]/10 hover:from-[#0097b2]/10 hover:to-[#0097b2]/20 transition-all duration-300">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-sm text-gray-600 mb-1">Messages</p>
                      <p class="text-2xl font-bold text-[#0097b2]">{{ stats?.messages_envoyes || 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-br from-[#0097b2]/20 to-[#0097b2]/30 rounded-xl flex items-center justify-center group-hover/stat:from-[#0097b2] group-hover/stat:to-[#008699] transition-all duration-300">
                      <svg class="w-5 h-5 text-[#0097b2] group-hover/stat:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                      </svg>
                    </div>
                  </div>
                </div>
                
                <div class="group/stat p-4 bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl border border-orange-200/50 hover:from-orange-100 hover:to-orange-200 transition-all duration-300">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-sm text-gray-600 mb-1">Non lus</p>
                      <p class="text-2xl font-bold text-orange-600">{{ stats?.messages_non_lus || 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-br from-orange-200 to-orange-300 rounded-xl flex items-center justify-center group-hover/stat:from-orange-500 group-hover/stat:to-orange-600 transition-all duration-300">
                      <svg class="w-5 h-5 text-orange-600 group-hover/stat:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Autres statistiques -->
              <div class="space-y-3">
                <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-2xl border border-gray-100 hover:shadow-md transition-all duration-300">
                  <div class="flex justify-between items-center">
                    <span class="text-gray-700 font-medium">Réactions données</span>
                    <span class="font-bold text-[#0097b2] bg-[#0097b2]/10 px-3 py-1 rounded-full">{{ stats?.reactions_donnees || 0 }}</span>
                  </div>
                </div>
                
                <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-2xl border border-gray-100 hover:shadow-md transition-all duration-300">
                  <div class="flex justify-between items-center">
                    <span class="text-gray-700 font-medium">Groupes rejoints</span>
                    <span class="font-bold text-[#0097b2] bg-[#0097b2]/10 px-3 py-1 rounded-full">{{ stats?.groupes_participes || 0 }}</span>
                  </div>
                </div>
                
                <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-100">
                  <div class="text-sm text-gray-600 mb-1">Membre depuis</div>
                  <div class="font-semibold text-gray-900">{{ formatDate(stats?.date_inscription) }}</div>
                </div>
                
                <div class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl border border-green-100">
                  <div class="text-sm text-gray-600 mb-1">Dernière connexion</div>
                  <div class="font-semibold text-gray-900">{{ formatDate(stats?.derniere_connexion) }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Action de déconnexion avec design moderne -->
      <div class="mt-12">
        <div class="relative overflow-hidden bg-white/80 backdrop-blur-sm rounded-3xl shadow-xl border border-white/50 p-8">
          <!-- Gradient décoratif -->
          <div class="absolute top-0 left-1/2 w-48 h-48 bg-gradient-to-br from-red-100/30 to-transparent rounded-full -translate-y-24 -translate-x-24"></div>
          
          <div class="relative text-center">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Zone de déconnexion</h3>
            <p class="text-gray-600 mb-6">Déconnectez-vous en toute sécurité de votre session</p>
            <button 
              @click="logout"
              class="group inline-flex items-center px-8 py-4 bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold rounded-2xl hover:from-red-600 hover:to-red-700 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl"
            >
              <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
              </svg>
              Se déconnecter
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Modifier les informations -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-lg font-semibold mb-4">Modifier les informations</h3>
        
        <form @submit.prevent="updateProfile" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
            <input
              v-model="editForm.prenom"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0097b2] focus:border-transparent"
              @input="profileError = ''"
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
            <input
              v-model="editForm.nom"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0097b2] focus:border-transparent"
              @input="profileError = ''"
            />
          </div>
          
          <div>
            <PhoneInput
              v-model="editForm.numero_telephone"
              label="Téléphone"
              :error="profileError"
              required
              @update:model-value="profileError = ''"
            />
          </div>
          
          <div class="flex gap-3 pt-4">
            <button
              type="button"
              @click="closeEditModal"
              class="flex-1 px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors"
            >
              Annuler
            </button>
            <button
              type="submit"
              :disabled="updatingProfile"
              class="flex-1 px-4 py-2 bg-[#0097b2] text-white rounded-lg hover:bg-[#008699] transition-colors disabled:opacity-50"
            >
              {{ updatingProfile ? 'Mise à jour...' : 'Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Vérification mot de passe actuel -->
    <div v-if="showCurrentPasswordModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-lg font-semibold mb-4">Vérification du mot de passe</h3>
        
        <form @submit.prevent="verifyCurrentPassword" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe actuel</label>
            <input
              v-model="currentPasswordForm.current_password"
              type="password"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0097b2] focus:border-transparent"
              :class="{ 'border-red-500 focus:ring-red-500': passwordError }"
              placeholder="Entrez votre mot de passe actuel"
              @input="passwordError = ''"
            />
            <p v-if="passwordError" class="mt-1 text-sm text-red-600">{{ passwordError }}</p>
          </div>
          
          <div class="flex gap-3 pt-4">
            <button
              type="button"
              @click="closeCurrentPasswordModal"
              class="flex-1 px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors"
            >
              Annuler
            </button>
            <button
              type="submit"
              :disabled="updatingPassword"
              class="flex-1 px-4 py-2 bg-[#0097b2] text-white rounded-lg hover:bg-[#008699] transition-colors disabled:opacity-50"
            >
              {{ updatingPassword ? 'Vérification...' : 'Continuer' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Nouveau mot de passe -->
    <div v-if="showPasswordModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-lg font-semibold mb-4">Nouveau mot de passe</h3>
        
        <form @submit.prevent="updatePassword" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe</label>
            <input
              v-model="passwordForm.new_password"
              type="password"
              required
              minlength="8"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0097b2] focus:border-transparent"
              placeholder="Minimum 8 caractères"
              @input="newPasswordError = ''"
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirmer le nouveau mot de passe</label>
            <input
              v-model="passwordForm.new_password_confirmation"
              type="password"
              required
              minlength="8"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0097b2] focus:border-transparent"
              :class="{ 'border-red-500 focus:ring-red-500': newPasswordError }"
              placeholder="Répétez le nouveau mot de passe"
              @input="newPasswordError = ''"
            />
            <p v-if="newPasswordError" class="mt-1 text-sm text-red-600">{{ newPasswordError }}</p>
          </div>
          
          <div class="flex gap-3 pt-4">
            <button
              type="button"
              @click="closePasswordModal"
              class="flex-1 px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors"
            >
              Annuler
            </button>
            <button
              type="submit"
              :disabled="updatingPassword"
              class="flex-1 px-4 py-2 bg-[#0097b2] text-white rounded-lg hover:bg-[#008699] transition-colors disabled:opacity-50"
            >
              {{ updatingPassword ? 'Mise à jour...' : 'Changer' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Avatar -->
    <AvatarModal 
      v-if="showAvatarModal"
      :current-avatar-url="avatarUrl"
      @close="closeAvatarModal"
      @success="handleAvatarSuccess"
    />

    <!-- Footer -->
    <AppFooter />
  </div>
</template>

<script setup lang="ts">
// Formate le numéro selon l'indicatif international
function formatPhoneNumber(num: string) {
  if (!num) return ''
  if (num.startsWith('+')) {
    const match = num.match(/^(\+\d{1,3})(\d+)$/)
    if (match) {
      const indicatif = match[1]
      const reste = match[2]
      if (indicatif === '+33' && reste.length === 9) {
        return `${indicatif}  ${reste[0]} ${reste.slice(1,3)} ${reste.slice(3,5)} ${reste.slice(5,7)} ${reste.slice(7,9)}`
      }
      if (indicatif === '+228' && reste.length === 8) {
        return `${indicatif}  ${reste.slice(0,2)} ${reste.slice(2,4)} ${reste.slice(4,6)} ${reste.slice(6,8)}`
      }
      return indicatif + '  ' + reste.replace(/(\d{2})/g, '$1 ').trim()
    }
  }
  if (num.startsWith('0') && num.length === 10) {
    return `${num.slice(0,2)} ${num.slice(2,4)} ${num.slice(4,6)} ${num.slice(6,8)} ${num.slice(8,10)}`
  }
  return num
}
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: 'Profil - Gestion Entreprise de Résidence'
})

const authStore = useAuthStore()
const config = useRuntimeConfig()

// Types
interface User {
  nom: string
  prenom: string
  email: string
  numero_telephone: string
  photo_profil?: string | null
  type?: string
  user_type?: string
  role?: string
  [key: string]: any // Pour les autres propriétés éventuelles
}

interface Stats {
  role: string
  messages_envoyes: number
  reactions_donnees: number
  groupes_participes: number
  messages_non_lus: number
  date_inscription: string
  derniere_connexion: string
}

interface ApiResponse {
  success: boolean
  data: any
}

// État réactif
const user = computed(() => authStore.user as User | null)
const stats = ref<Stats | null>(null)
const loading = ref(false)

// Computed pour déterminer l'URL de l'avatar
const avatarUrl = computed(() => {
  if (!user.value?.photo_profil) return null
  
  // Si c'est déjà une URL complète, la retourner telle quelle
  if (user.value.photo_profil.startsWith('http')) {
    return user.value.photo_profil
  }
  
  // Sinon, construire l'URL avec l'API
  return `${config.public.apiBase}/avatars/${user.value.photo_profil.split('/').pop()}`
})

// Computed pour déterminer le rôle correct de l'utilisateur
const userRole = computed(() => {
  // D'abord essayer d'utiliser le rôle des stats (qui vient de l'API)
  if (stats.value?.role) {
    return stats.value.role
  }
  
  // Sinon, utiliser le rôle stocké dans l'utilisateur (défini lors de la connexion)
  if (user.value?.role) {
    switch (user.value.role) {
      case 'admin': 
        return 'Administrateur'
      case 'gardien': 
        return 'Gardien'
      case 'resident': 
        return 'Résident'
      case 'invite': 
        return 'Invité'
      default: 
        return user.value.role
    }
  }
  
  // Vérifier si l'utilisateur a des propriétés spécifiques aux invités
  if (user.value) {
    if (user.value.type === 'invite' || user.value.user_type === 'invite') {
      return 'Invité'
    }
  }
  
  // Fallback par défaut
  return 'Utilisateur'
})

// Computed pour les couleurs du rôle
const roleColors = computed(() => {
  const role = userRole.value.toLowerCase()
  
  switch (role) {
    case 'administrateur':
    case 'admin':
      return {
        background: 'bg-gradient-to-r from-red-500 to-red-600',
        text: 'text-white',
        icon: 'text-white'
      }
    case 'gardien':
      return {
        background: 'bg-gradient-to-r from-orange-500 to-orange-600',
        text: 'text-white',
        icon: 'text-white'
      }
    case 'résident':
    case 'resident':
      return {
        background: 'bg-gradient-to-r from-[#0097b2] to-[#008699]',
        text: 'text-white',
        icon: 'text-white'
      }
    case 'invité':
    case 'invite':
      return {
        background: 'bg-gradient-to-r from-emerald-500 to-teal-500',
        text: 'text-white',
        icon: 'text-white'
      }
    default:
      return {
        background: 'bg-gradient-to-r from-gray-500 to-gray-600',
        text: 'text-white',
        icon: 'text-white'
      }
  }
})

// Modals
const showEditModal = ref(false)
const showPasswordModal = ref(false)
const showCurrentPasswordModal = ref(false)
const showAvatarModal = ref(false)

// États des formulaires
const updatingProfile = ref(false)
const updatingPassword = ref(false)
const passwordError = ref('')
const newPasswordError = ref('')
const profileError = ref('')
const successMessage = ref('')

// Données des formulaires
const editForm = ref({
  nom: '',
  prenom: '',
  numero_telephone: ''
})

const currentPasswordForm = ref({
  current_password: ''
})

const passwordForm = ref({
  new_password: '',
  new_password_confirmation: ''
})

// Charger les statistiques au montage
onMounted(async () => {
  await loadStats()
  initEditForm()
})

// Charger les statistiques
const loadStats = async () => {
  try {
    loading.value = true
    
    const response = await $fetch(`${config.public.apiBase}/profile/stats`, {
      headers: {
        'Authorization': `Bearer ${authStore.token}`
      }
    }) as ApiResponse
    
    if (response.success) {
      stats.value = response.data
    }
  } catch (error) {
    console.error('❌ [PROFILE] Erreur lors du chargement des statistiques:', error)
  } finally {
    loading.value = false
  }
}

// Initialiser le formulaire d'édition
const initEditForm = () => {
  if (user.value) {
    editForm.value = {
      nom: user.value.nom || '',
      prenom: user.value.prenom || '',
      numero_telephone: user.value.numero_telephone || ''
    }
  }
}

// Modals
const openEditModal = () => {
  initEditForm()
  profileError.value = ''
  successMessage.value = ''
  showEditModal.value = true
}

const closeEditModal = () => {
  showEditModal.value = false
  profileError.value = ''
  initEditForm()
}

const openPasswordModal = () => {
  currentPasswordForm.value = {
    current_password: ''
  }
  passwordError.value = ''
  newPasswordError.value = ''
  successMessage.value = ''
  showCurrentPasswordModal.value = true
}

const closePasswordModal = () => {
  showPasswordModal.value = false
  showCurrentPasswordModal.value = false
  passwordForm.value = {
    new_password: '',
    new_password_confirmation: ''
  }
  currentPasswordForm.value = {
    current_password: ''
  }
  passwordError.value = ''
  newPasswordError.value = ''
}

const closeCurrentPasswordModal = () => {
  showCurrentPasswordModal.value = false
  currentPasswordForm.value = {
    current_password: ''
  }
  passwordError.value = ''
  newPasswordError.value = ''
}

// Fonction pour afficher un message de succès temporaire
const showSuccessMessage = (message: string) => {
  successMessage.value = message
  setTimeout(() => {
    successMessage.value = ''
  }, 5000) // Disparaît après 5 secondes
}

const openAvatarModal = () => {
  showAvatarModal.value = true
}

const closeAvatarModal = () => {
  showAvatarModal.value = false
}

const handleAvatarSuccess = (newAvatarUrl: string | null) => {
  showSuccessMessage(newAvatarUrl ? 'Photo de profil mise à jour avec succès' : 'Photo de profil supprimée avec succès')
}

const handleAvatarError = () => {
  console.error('Erreur lors du chargement de l\'avatar')
}

// Mettre à jour le profil
const updateProfile = async () => {
  try {
    updatingProfile.value = true
    profileError.value = ''
    
    // Validation côté client pour le format international du téléphone
    const cleanPhone = editForm.value.numero_telephone.replace(/\s/g, '') // Retirer les espaces
    if (!/^\+\d{1,4}\d{6,15}$/.test(cleanPhone)) {
      profileError.value = 'Le numéro de téléphone doit être au format international (+33123456789)'
      return
    }
    
    const response = await $fetch(`${config.public.apiBase}/profile/update`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Content-Type': 'application/json'
      },
      body: editForm.value
    }) as ApiResponse
    
    if (response.success) {
      // Mettre à jour les données utilisateur
      authStore.updateUser(response.data)
      closeEditModal()
      showSuccessMessage('Informations mises à jour avec succès')
    }
  } catch (error: any) {
    console.error('Erreur lors de la mise à jour du profil:', error)
    
    if (error.response?.status === 422) {
      // Vérifier si l'erreur est spécifiquement liée au format du téléphone
      if (error.data?.errors?.numero_telephone) {
        profileError.value = Array.isArray(error.data.errors.numero_telephone) 
          ? error.data.errors.numero_telephone[0] 
          : error.data.errors.numero_telephone
      } else {
        profileError.value = 'Les données saisies ne sont pas valides'
      }
    } else {
      profileError.value = 'Erreur lors de la mise à jour du profil'
    }
  } finally {
    updatingProfile.value = false
  }
}

// Vérifier le mot de passe actuel (vraie vérification)
const verifyCurrentPassword = async () => {
  if (!currentPasswordForm.value.current_password) {
    passwordError.value = 'Veuillez saisir votre mot de passe actuel'
    return
  }

  try {
    updatingPassword.value = true
    passwordError.value = ''
    
    console.log('🔐 Vérification du mot de passe actuel:', currentPasswordForm.value.current_password ? `[${currentPasswordForm.value.current_password.length} caractères]` : '[VIDE]')
    
    // Faire une vraie vérification du mot de passe actuel
    const response = await $fetch(`${config.public.apiBase}/profile/verify-password`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Content-Type': 'application/json'
      },
      body: {
        current_password: currentPasswordForm.value.current_password
      }
    }) as ApiResponse
    
    if (response.success) {
      console.log('✅ Mot de passe actuel vérifié avec succès')
      // Passer à l'étape suivante
      showCurrentPasswordModal.value = false
      passwordForm.value = {
        new_password: '',
        new_password_confirmation: ''
      }
      showPasswordModal.value = true
    }
  } catch (error: any) {
    console.error('❌ Erreur lors de la vérification du mot de passe:', error)
    
    if (error.response?.status === 422 || error.response?.status === 401) {
      passwordError.value = 'Mot de passe actuel incorrect'
    } else {
      passwordError.value = 'Erreur lors de la vérification du mot de passe'
    }
  } finally {
    updatingPassword.value = false
  }
}

// Changer le mot de passe
const updatePassword = async () => {
  // Validation côté client
  if (passwordForm.value.new_password !== passwordForm.value.new_password_confirmation) {
    newPasswordError.value = 'Les mots de passe ne correspondent pas'
    return
  }
  
  if (passwordForm.value.new_password.length < 8) {
    newPasswordError.value = 'Le mot de passe doit contenir au moins 8 caractères'
    return
  }
  
  try {
    updatingPassword.value = true
    newPasswordError.value = ''
    
    const requestBody = {
      current_password: currentPasswordForm.value.current_password,
      new_password: passwordForm.value.new_password,
      new_password_confirmation: passwordForm.value.new_password_confirmation
    }
    
    console.log('🔐 Changement de mot de passe - Données envoyées:', {
      current_password: currentPasswordForm.value.current_password ? `[${currentPasswordForm.value.current_password.length} caractères]` : '[VIDE]',
      new_password: passwordForm.value.new_password ? `[${passwordForm.value.new_password.length} caractères]` : '[VIDE]',
      new_password_confirmation: passwordForm.value.new_password_confirmation ? `[${passwordForm.value.new_password_confirmation.length} caractères]` : '[VIDE]'
    })
    
    console.log('🔐 Vérification avant envoi:')
    console.log('  - current_password rempli:', !!currentPasswordForm.value.current_password)
    console.log('  - new_password rempli:', !!passwordForm.value.new_password)
    console.log('  - new_password_confirmation rempli:', !!passwordForm.value.new_password_confirmation)
    console.log('  - Les mots de passe correspondent:', passwordForm.value.new_password === passwordForm.value.new_password_confirmation)
    
    console.log('🔐 URL de l\'API:', `${config.public.apiBase}/profile/password`)
    console.log('🔐 Token présent:', !!authStore.token)
    
    const response = await $fetch(`${config.public.apiBase}/profile/password`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Content-Type': 'application/json'
      },
      body: requestBody
    }) as ApiResponse
    
    if (response.success) {
      console.log('✅ Changement de mot de passe réussi')
      closePasswordModal()
      showSuccessMessage('Mot de passe mis à jour avec succès')
      // Reset forms
      currentPasswordForm.value = { current_password: '' }
      passwordForm.value = { new_password: '', new_password_confirmation: '' }
    }
  } catch (error: any) {
    console.error('❌ Erreur lors du changement de mot de passe:', error)
    console.error('❌ Status d\'erreur:', error.response?.status)
    console.error('❌ Headers de réponse:', error.response?.headers)
    console.error('❌ Données d\'erreur complètes:', error.response?._data || error.data)
    
    if (error.response?.status === 422) {
      // Analyser l'erreur plus précisément
      const errorData = error.response._data || error.data
      console.log('🔍 Analyse des erreurs 422:', {
        errorData,
        hasErrors: !!errorData?.errors,
        hasMessage: !!errorData?.message,
        errorsKeys: errorData?.errors ? Object.keys(errorData.errors) : [],
        message: errorData?.message
      })
      
      if (errorData?.errors) {
        const errors = errorData.errors
        console.log('🔍 Erreurs spécifiques:', errors)
        
        if (errors.current_password) {
          console.log('❌ Erreur sur current_password:', errors.current_password)
          // Retourner au premier modal pour corriger le mot de passe actuel
          showPasswordModal.value = false
          passwordError.value = Array.isArray(errors.current_password) ? errors.current_password[0] : errors.current_password
          showCurrentPasswordModal.value = true
        } else if (errors.new_password) {
          console.log('❌ Erreur sur new_password:', errors.new_password)
          newPasswordError.value = Array.isArray(errors.new_password) ? errors.new_password[0] : errors.new_password
        } else if (errors.new_password_confirmation) {
          console.log('❌ Erreur sur new_password_confirmation:', errors.new_password_confirmation)
          newPasswordError.value = Array.isArray(errors.new_password_confirmation) ? errors.new_password_confirmation[0] : errors.new_password_confirmation
        } else {
          console.log('❌ Erreur générale de validation')
          newPasswordError.value = 'Les données saisies ne sont pas valides'
        }
      } else if (errorData?.message) {
        console.log('❌ Message d\'erreur direct:', errorData.message)
        if (errorData.message.includes('actuel incorrect') || errorData.message.includes('current password')) {
          // Retourner au premier modal pour corriger le mot de passe actuel
          showPasswordModal.value = false
          passwordError.value = errorData.message
          showCurrentPasswordModal.value = true
        } else {
          newPasswordError.value = errorData.message
        }
      } else {
        console.log('❌ Erreur 422 sans structure reconnue')
        newPasswordError.value = 'Erreur de validation des données'
      }
    } else {
      console.log('❌ Erreur non-422:', error.response?.status)
      newPasswordError.value = 'Erreur lors du changement de mot de passe'
    }
  } finally {
    updatingPassword.value = false
  }
}

// Utilitaires
const formatDate = (dateString: string | undefined) => {
  if (!dateString) return 'Non disponible'
  
  try {
    const date = new Date(dateString)
    return date.toLocaleDateString('fr-FR', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  } catch {
    return 'Date invalide'
  }
}

const toggleMobileMenu = () => {
  console.log('Toggle mobile menu')
}

const logout = async () => {
  try {
    await authStore.logout()
    // La redirection est maintenant gérée par le store
  } catch (error) {
    console.error('Erreur lors de la déconnexion:', error)
    // En cas d'erreur, forcer la redirection
    await navigateTo('/')
  }
}
</script>



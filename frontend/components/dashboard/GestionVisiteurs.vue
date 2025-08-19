<template>
  <div class="space-y-8 animate-fadeInUp">
    <!-- Header moderne -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 card-shadow">
      <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
        <div class="animate-slideInLeft">
          <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $t('gestionVisiteurs.title') }}</h2>
          <p class="text-gray-500">Gérez les visiteurs et leurs accès en toute sécurité</p>
        </div>
        <div class="flex items-center gap-3">
          <button @click="toggleView" class="px-4 py-2.5 rounded-xl bg-gray-50 text-gray-700 border border-gray-200 hover:bg-gray-100 hover:border-gray-300 transition-all duration-200 font-medium hover:card-shadow">
            {{ isListView ? $t('gestionVisiteurs.cardView') : $t('gestionVisiteurs.listView') }}
          </button>
          <button @click="openAddGuestModal" class="px-6 py-2.5 rounded-xl gradient-modern text-white hover:opacity-90 transition-all duration-200 font-medium shadow-lg hover:card-shadow-hover">
            Ajouter un invité
          </button>
        </div>
      </div>
      
      <!-- Filtres modernes -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">{{ $t('gestionVisiteurs.globalSearch') }}</label>
          <div class="relative">
            <input v-model="searchQuery" @input="filterPersons" type="text" :placeholder="$t('gestionVisiteurs.searchPlaceholder')" 
              class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white" />
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </div>
        </div>
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">{{ $t('gestionVisiteurs.filterActif') }}</label>
          <select v-model="filterActif" @change="filterPersons" 
            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white">
            <option value="">{{ $t('gestionVisiteurs.allStatus') }}</option>
            <option value="true">{{ $t('gestionVisiteurs.active') }}</option>
            <option value="false">{{ $t('gestionVisiteurs.banned') }}</option>
          </select>
        </div>
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">{{ $t('gestionVisiteurs.filterDate') }}</label>
          <input v-model="filterDate" @input="filterPersons" type="date" 
            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white" />
        </div>
      </div>
    </div>
    <div v-if="loading" class="flex items-center justify-center py-16">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
        <p class="text-gray-500 font-medium">{{ $t('gestionVisiteurs.loading') }}</p>
      </div>
    </div>
    <div v-else class="space-y-8">
      <!-- Visiteurs actifs -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8">
        <div class="flex items-center gap-3 mb-6">
          <h3 class="text-2xl font-bold text-gray-900">{{ $t('gestionVisiteurs.visitorsTitle') }}</h3>
          <div class="h-px flex-1 bg-gradient-to-r from-gray-200 to-transparent"></div>
          <div class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">
            {{ (persons ?? []).length }} visiteur{{ (persons ?? []).length > 1 ? 's' : '' }}
          </div>
        </div>
        
        <div v-if="persons && persons.length === 0" class="text-center py-12">
          <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
          </svg>
          <p class="text-gray-500 text-lg font-medium">{{ $t('gestionVisiteurs.noVisitor') }}</p>
        </div>
        
        <div v-if="!isListView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="person in (persons ?? [])" :key="person.id" 
            class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-gray-200 transition-all duration-300 p-6 relative overflow-hidden card-shadow hover:card-shadow-hover animate-fadeInUp">
            <!-- Indicateur de statut -->
            <div class="absolute top-0 right-0 w-20 h-20 opacity-5">
              <div :class="person.actif ? 'gradient-success' : 'gradient-danger'" 
                class="w-full h-full rotate-45 transform translate-x-8 -translate-y-8"></div>
            </div>
            
            <!-- Header de la carte -->
            <div class="flex items-start gap-4 mb-4">
              <div class="relative">
                <img v-if="person.photo_profil" :src="getAvatarUrl(person.photo_profil) || ''" 
                  class="w-16 h-16 rounded-2xl object-cover border-2 border-gray-100 group-hover:border-blue-200 transition-colors duration-300" alt="avatar" />
                <div v-else class="w-16 h-16 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-xl font-bold text-gray-600 border-2 border-gray-100 group-hover:border-blue-200 transition-colors duration-300">
                  {{ person.prenom[0] }}{{ person.nom[0] }}
                </div>
                <div :class="person.actif ? 'bg-green-500' : 'bg-red-500'" class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full border-2 border-white"></div>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                  <h4 class="font-bold text-xl text-gray-900 truncate">{{ person.prenom }} {{ person.nom }}</h4>
                  <span v-if="!person.actif" class="px-2 py-0.5 rounded-lg bg-red-100 text-red-700 text-xs font-semibold">Banni</span>
                </div>
                <p class="text-sm text-gray-500 mb-2 truncate">{{ person.email }}</p>
                <div class="flex flex-wrap gap-1.5">
                  <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-purple-100 text-purple-700 border border-purple-200">
                    {{ $t('gestionVisiteurs.visitor') }}
                  </span>
                  <span :class="person.actif ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200'" 
                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium border">
                    {{ person.actif ? $t('gestionVisiteurs.active') : $t('gestionVisiteurs.banned') }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Informations détaillées -->
            <div class="space-y-3 mb-6">
              <div class="flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <span class="text-gray-600">{{ formatPhoneNumber(person.numero_telephone) }}</span>
              </div>
              
              <div v-if="person.date_inscription" class="flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-gray-600">Inscrit le {{ formatDate(person.date_inscription ?? '') }}</span>
              </div>
              
              <div v-if="person.date_expiration" class="flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-gray-600">Expire le {{ formatDate(person.date_expiration ?? '') }}</span>
              </div>
              
              <div v-if="person.invite_par && getInviterName(person.invite_par)" class="flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-gray-600">Invité par <span class="font-semibold text-green-600">{{ getInviterName(person.invite_par) }}</span></span>
              </div>
              
              <div v-if="person.commentaire" class="flex items-start gap-2 text-sm">
                <svg class="w-4 h-4 text-purple-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
                <span class="text-gray-600">{{ person.commentaire }}</span>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
              <template v-if="person.actif">
                <button @click="banPerson(person)" 
                  class="flex-1 px-4 py-2.5 rounded-xl bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 hover:border-red-300 transition-all duration-200 font-medium text-sm">
                  {{ $t('gestionVisiteurs.ban') }}
                </button>
                <button @click="editPerson(person)" 
                  class="flex-1 px-4 py-2.5 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 hover:border-blue-300 transition-all duration-200 font-medium text-sm">
                  {{ $t('gestionVisiteurs.edit') }}
                </button>
                <button @click="deletePerson(person)" 
                  class="flex-1 px-4 py-2.5 rounded-xl bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 hover:border-red-300 transition-all duration-200 font-medium text-sm">
                  Supprimer
                </button>
              </template>
              <template v-else>
                <button @click="unbanPerson(person)" 
                  class="flex-1 px-4 py-2.5 rounded-xl bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 hover:border-green-300 transition-all duration-200 font-medium text-sm">
                  {{ $t('gestionVisiteurs.unban') }}
                </button>
              </template>
            </div>
          </div>
        </div>
        <!-- Vue liste moderne -->
        <div v-else class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-x-auto">
          <table class="min-w-full w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-900 min-w-[120px]">{{ $t('gestionVisiteurs.lastName') }}</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-900 min-w-[120px]">{{ $t('gestionVisiteurs.firstName') }}</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-900 min-w-[180px]">{{ $t('gestionVisiteurs.email') }}</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-900 min-w-[140px]">{{ $t('gestionVisiteurs.phone') }}</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-900 min-w-[100px]">{{ $t('gestionVisiteurs.activeLabel') }}</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-900 min-w-[140px]">{{ $t('gestionVisiteurs.registeredOn') }}</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-900 min-w-[140px]">{{ $t('gestionVisiteurs.invitedBy') }}</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-900 min-w-[120px]">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
              <tr v-for="person in (persons ?? [])" :key="person.id" class="hover:bg-gray-50 transition-colors duration-200">
                <td class="px-4 py-3 font-medium text-gray-900 break-words truncate min-w-[120px]">{{ person.nom }}</td>
                <td class="px-4 py-3 text-gray-700 break-words truncate min-w-[120px]">{{ person.prenom }}</td>
                <td class="px-4 py-3 text-gray-700 break-words truncate min-w-[180px]">{{ person.email }}</td>
                <td class="px-4 py-3 text-gray-700 break-words truncate min-w-[140px]">{{ person.numero_telephone }}</td>
                <td class="px-4 py-3">
                  <span :class="person.actif ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200'" 
                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium border">
                    {{ person.actif ? $t('gestionVisiteurs.yes') : $t('gestionVisiteurs.no') }}
                  </span>
                </td>
                <td class="px-4 py-3 text-gray-700 break-words truncate min-w-[140px]">{{ formatDate(person.date_inscription ?? '') }}</td>
                <td class="px-4 py-3 text-gray-700 break-words truncate min-w-[140px]">{{ getInviterName(person.invite_par) }}</td>
                <td class="px-4 py-3 min-w-[120px]">
                  <div class="flex gap-2 flex-wrap">
                    <template v-if="person.actif">
                      <button @click="banPerson(person)" 
                        class="px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 hover:border-red-300 text-xs font-medium transition-all duration-200">
                        {{ $t('gestionVisiteurs.ban') }}
                      </button>
                      <button @click="editPerson(person)" 
                        class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 hover:border-blue-300 text-xs font-medium transition-all duration-200">
                        {{ $t('gestionVisiteurs.edit') }}
                      </button>
                      <button @click="deletePerson(person)" 
                        class="px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 hover:border-red-300 text-xs font-medium transition-all duration-200">
                        Supprimer
                      </button>
                    </template>
                    <template v-else>
                      <button @click="unbanPerson(person)" 
                        class="px-3 py-1.5 rounded-lg bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 hover:border-green-300 text-xs font-medium transition-all duration-200">
                        {{ $t('gestionVisiteurs.unban') }}
                      </button>
                    </template>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Visiteurs bannis -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8">
        <div class="flex items-center gap-3 mb-6">
          <h3 class="text-2xl font-bold text-red-700">{{ $t('gestionVisiteurs.bannedVisitorsTitle') }}</h3>
          <div class="h-px flex-1 bg-gradient-to-r from-gray-200 to-transparent"></div>
          <div class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-medium">
            {{ bannedPersons.length }} banni{{ bannedPersons.length > 1 ? 's' : '' }}
          </div>
        </div>
        
        <div v-if="bannedPersons.length === 0" class="text-center py-12">
          <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
          </svg>
          <p class="text-gray-500 text-lg font-medium">{{ $t('gestionVisiteurs.noBannedVisitor') }}</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="person in bannedPersons" :key="person.id" 
            class="group bg-gradient-to-br from-gray-50 to-red-50 rounded-2xl border border-red-100 shadow-sm p-6 relative overflow-hidden card-shadow animate-fadeInUp">
            <!-- Indicateur de bannissement -->
            <div class="absolute top-0 right-0 w-20 h-20 opacity-10">
              <div class="w-full h-full gradient-danger rotate-45 transform translate-x-8 -translate-y-8"></div>
            </div>
            
            <!-- Header de la carte -->
            <div class="flex items-start gap-4 mb-4">
              <div class="relative">
                <img v-if="person.photo_profil" :src="getAvatarUrl(person.photo_profil) || ''" 
                  class="w-16 h-16 rounded-2xl object-cover border-2 border-red-100 grayscale" alt="avatar" />
                <div v-else class="w-16 h-16 rounded-2xl bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center text-xl font-bold text-gray-500 border-2 border-red-100">
                  {{ person.prenom[0] }}{{ person.nom[0] }}
                </div>
                <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-red-500 rounded-full border-2 border-white">
                  <svg class="w-3 h-3 text-white absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <h4 class="font-bold text-xl text-gray-900 mb-1 truncate">{{ person.prenom }} {{ person.nom }}</h4>
                <p class="text-sm text-gray-500 mb-2 truncate">{{ person.email }}</p>
                <div class="flex flex-wrap gap-1.5">
                  <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-700 border border-red-200">
                    {{ $t('gestionVisiteurs.banned') }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Informations -->
            <div class="space-y-3 mb-6">
              <div class="flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <span class="text-gray-600">{{ person.numero_telephone }}</span>
              </div>
              
              <div v-if="person.date_inscription" class="flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-gray-600">Inscrit le {{ formatDate(person.date_inscription ?? '') }}</span>
              </div>
              
              <div v-if="person.date_expiration" class="flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-gray-600">Expirait le {{ formatDate(person.date_expiration ?? '') }}</span>
              </div>
              
              <div v-if="person.invite_par" class="flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-gray-600">Invité par {{ person.invite_par }}</span>
              </div>
              
              <div v-if="person.commentaire" class="flex items-start gap-2 text-sm">
                <svg class="w-4 h-4 text-purple-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
                <span class="text-gray-600">{{ person.commentaire }}</span>
              </div>
            </div>

            <!-- État -->
            <div class="flex gap-2">
              <button @click="unbanPerson(person)" 
                class="flex-1 px-4 py-2.5 rounded-xl bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 hover:border-green-300 transition-all duration-200 font-medium text-sm">
                {{ $t('gestionVisiteurs.unban') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Place le modal ici, AVANT la fermeture du template -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-6 w-full max-w-lg shadow-lg relative">
        <h3 class="text-xl font-bold mb-4">{{ $t('gestionVisiteurs.editVisitorTitle') }}</h3>
        <form @submit.prevent="saveEditPerson">
          <div class="mb-3">
            <label class="block text-sm font-medium mb-1">{{ $t('gestionVisiteurs.lastName') }}</label>
            <input v-model="editForm.nom" class="input w-full" required />
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium mb-1">{{ $t('gestionVisiteurs.firstName') }}</label>
            <input v-model="editForm.prenom" class="input w-full" required />
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium mb-1">{{ $t('gestionVisiteurs.email') }}</label>
            <input v-model="editForm.email" class="input w-full" required type="email" />
          </div>
          <div class="mb-3">
            <PhoneInput 
              :model-value="editForm.numero_telephone || ''"
              @update:model-value="(value: string) => editForm.numero_telephone = value"
              :label="$t('gestionVisiteurs.phone')"
              :placeholder="$t('gestionVisiteurs.phonePlaceholder')"
            />
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium mb-1">{{ $t('gestionVisiteurs.comment') }}</label>
            <textarea v-model="editForm.commentaire" class="input w-full" rows="2"></textarea>
          </div>
          <div class="flex gap-2 justify-end mt-4">
            <button type="button" @click="closeEditModal" class="px-4 py-1 rounded bg-gray-100 text-gray-600 border border-gray-300 hover:bg-gray-200">{{ $t('gestionVisiteurs.cancel') }}</button>
            <button type="submit" class="px-4 py-1 rounded bg-blue-600 text-white hover:bg-blue-700">{{ $t('gestionVisiteurs.save') }}</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal d'ajout d'invité -->
    <div v-if="showAddGuestModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-6 w-full max-w-lg shadow-lg relative">
        <h3 class="text-xl font-bold mb-4">Ajouter un invité</h3>
        <form @submit.prevent="saveAddGuest">
          <div class="mb-3">
            <label class="block text-sm font-medium mb-1">{{ $t('gestionVisiteurs.lastName') }}</label>
            <input v-model="addGuestForm.nom" class="input w-full" required />
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium mb-1">{{ $t('gestionVisiteurs.firstName') }}</label>
            <input v-model="addGuestForm.prenom" class="input w-full" required />
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium mb-1">{{ $t('gestionVisiteurs.email') }}</label>
            <input v-model="addGuestForm.email" class="input w-full" required type="email" />
          </div>
          <div class="mb-3">
            <PhoneInput 
              v-model="addGuestForm.numero_telephone"
              :label="$t('gestionVisiteurs.phone')"
              :placeholder="$t('gestionVisiteurs.phonePlaceholder')"
            />
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium mb-1">{{ $t('gestionVisiteurs.comment') }}</label>
            <textarea v-model="addGuestForm.commentaire" class="input w-full" rows="2"></textarea>
          </div>
          <div class="flex gap-2 justify-end mt-4">
            <button type="button" @click="closeAddGuestModal" class="px-4 py-1 rounded bg-gray-100 text-gray-600 border border-gray-300 hover:bg-gray-200">{{ $t('gestionVisiteurs.cancel') }}</button>
            <button type="submit" class="px-4 py-1 rounded bg-green-600 text-white hover:bg-green-700">Ajouter</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  // Filtres avancés
  import { computed } from 'vue'
  const filterActif = ref('')
  const filterMail = ref('')
  const filterDate = ref('')
  // Removed mailsList as it is no longer needed
  // Formate le numéro selon l'indicatif international
  function formatPhoneNumber(num: string) {
    // Si le numéro commence par +
    if (num.startsWith('+')) {
      // Sépare l'indicatif (ex: +33, +228, etc.)
      const match = num.match(/^(\+\d{1,3})(\d+)$/)
      if (match) {
        const indicatif = match[1]
        const reste = match[2]
        // Format FR (+33)
        if (indicatif === '+33' && reste.length === 9) {
          return `${indicatif}  ${reste[0]} ${reste.slice(1,3)} ${reste.slice(3,5)} ${reste.slice(5,7)} ${reste.slice(7,9)}`
        }
        // Format Togo (+228)
        if (indicatif === '+228' && reste.length === 8) {
          return `${indicatif}  ${reste.slice(0,2)} ${reste.slice(2,4)} ${reste.slice(4,6)} ${reste.slice(6,8)}`
        }
        // Format générique (groupe de 2)
        return indicatif + '  ' + reste.replace(/(\d{2})/g, '$1 ').trim()
      }
    }
    // Format FR local (0X XX XX XX XX)
    if (num.startsWith('0') && num.length === 10) {
      return `${num.slice(0,2)} ${num.slice(2,4)} ${num.slice(4,6)} ${num.slice(6,8)} ${num.slice(8,10)}`
    }
    return num
  }

  const isListView = ref(false)
  function toggleView() {
    isListView.value = !isListView.value
  }

  // Fonction utilitaire pour afficher le nom complet de l'invitant à partir de son id (exposée au template)
  function getInviterName(inviteParId: string | number | null | undefined) {
    if (!inviteParId) return null
    // On tente d'abord de trouver par id exact (number ou string)
    let inviter = allPersons.value.find(p => String(p.id) === String(inviteParId))
    if (!inviter) {
      // Certains backends renvoient l'id sous forme de string ou number, on tente les deux
      inviter = allPersons.value.find(p => p.id == inviteParId)
    }
    if (inviter) return `${inviter.prenom} ${inviter.nom}`
    return null
  }

  import { ref, onMounted } from 'vue'
  import type { InvitePerson } from '~/types/invitePerson'
  import { useAuthStore } from '~/stores/auth'

  // Utilisation du nouveau composable
  const {
    guests: rawGuests,
    loadingGuests: loading,
    errorGuests: error,
    fetchGuests
  } = useAdminData()

  const authStore = useAuthStore()
  const apiBase = useRuntimeConfig().public.apiBase
  const searchQuery = ref('')
  const allPersons = ref<InvitePerson[]>([])
  const persons = ref<InvitePerson[]>([])
  const bannedPersons = ref<InvitePerson[]>([])

  // Remplace fonction locale par composable centralisé
  import { useAvatarUrl } from '@/composables/useAvatarUrl'
  const { build: getAvatarUrl } = useAvatarUrl()

  function formatDate(date: string) {
    if (!date) return ''
    return new Date(date).toLocaleDateString('fr-FR')
  }

  async function fetchPersons() {
    await fetchGuests()
    // Mapping des données pour correspondre au format attendu
    if (rawGuests.value && Array.isArray(rawGuests.value)) {
      const mapped = rawGuests.value.map((p: any) => ({
        id: p.id_personne,
        email: p.email,
        nom: p.nom,
        prenom: p.prenom,
        numero_telephone: p.numero_telephone,
        photo_profil: p.photo_profil,
        actif: p.actif ?? false,
        date_inscription: p.date_inscription ?? '',
        date_expiration: p.date_expiration ?? '',
        invite_par: p.invite_par ?? '',
        commentaire: p.commentaire ?? '',
        created_at: p.created_at ?? '',
        updated_at: p.updated_at ?? ''
      }))
      allPersons.value = mapped
      filterPersons()
      bannedPersons.value = mapped.filter(p => !p.actif)
    } else {
      allPersons.value = []
      persons.value = []
      bannedPersons.value = []
    }
  }

  function filterPersons() {
    const q = searchQuery.value.trim().toLowerCase()
    persons.value = allPersons.value.filter(p => {
      let match = true
      // Recherche globale
      if (q && !(
        p.nom.toLowerCase().includes(q) ||
        p.prenom.toLowerCase().includes(q) ||
        p.email.toLowerCase().includes(q) ||
        p.numero_telephone.toLowerCase().includes(q) ||
        (p.commentaire && p.commentaire.toLowerCase().includes(q))
      )) match = false

      // Filtre actif
      if (filterActif.value === 'true' && !p.actif) match = false
      if (filterActif.value === 'false' && p.actif) match = false

      // Filtre date inscription
      if (filterDate.value) {
        const dateStr = p.date_inscription?.slice(0, 10)
        if (dateStr !== filterDate.value) match = false
      }

      // Filtre mail
      if (filterMail.value && p.email !== filterMail.value) match = false

      return match
    })
  }

  async function banPerson(person: InvitePerson) {
    if (!confirm(`Bannir ${person.prenom} ${person.nom} ?`)) return
    try {
      await $fetch(`${apiBase}/admin/guests/${person.id}/ban`, {
        method: 'POST',
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      await fetchPersons()
    } catch (e) {
      console.error('Erreur lors du bannissement:', e)
      alert('Erreur lors du bannissement')
    }
  }

  async function unbanPerson(person: InvitePerson) {
    if (!confirm(`Débannir ${person.prenom} ${person.nom} ?`)) return
    try {
      await $fetch(`${apiBase}/admin/guests/${person.id}/unban`, {
        method: 'POST',
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      await fetchPersons()
    } catch (e) {
      console.error('Erreur lors du débannissement:', e)
      alert('Erreur lors du débannissement')
    }
  }

  // --- MODAL MODIFICATION ---
  const showEditModal = ref(false)
  const editForm = ref<Partial<InvitePerson & {id?: number}>>({})
  function editPerson(person: InvitePerson) {
    editForm.value = { 
      ...person,
      numero_telephone: person.numero_telephone || ''
    }
    showEditModal.value = true
  }

  // --- MODAL AJOUT D'INVITÉ ---
  const showAddGuestModal = ref(false)
  const addGuestForm = ref({
    nom: '',
    prenom: '',
    email: '',
    numero_telephone: '',
    commentaire: ''
  })

  function openAddGuestModal() {
    addGuestForm.value = {
      nom: '',
      prenom: '',
      email: '',
      numero_telephone: '',
      commentaire: ''
    }
    showAddGuestModal.value = true
  }

  function closeAddGuestModal() {
    showAddGuestModal.value = false
  }

  async function saveAddGuest() {
    try {
      await $fetch(`${apiBase}/admin/guests`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(addGuestForm.value)
      })
      showAddGuestModal.value = false
      await fetchPersons()
    } catch (e: any) {
      console.error('Erreur lors de l\'ajout de l\'invité:', e)
      alert('Erreur lors de l\'ajout de l\'invité')
    }
  }

  async function saveEditPerson() {
    if (!editForm.value.id) return
    try {
      await $fetch(`${apiBase}/admin/guests/${editForm.value.id}`, {
        method: 'PUT',
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          nom: editForm.value.nom,
          prenom: editForm.value.prenom,
          email: editForm.value.email,
          numero_telephone: editForm.value.numero_telephone,
          commentaire: editForm.value.commentaire,
          // Ajoutez ici d'autres champs si besoin (date_expiration, actif...)
        })
      })
      showEditModal.value = false
      await fetchPersons()
    } catch (e: any) {
      if (e && e.data) {
        console.error('Erreur de validation backend:', e.data)
        alert('Erreur: ' + JSON.stringify(e.data))
      } else {
        console.error('Erreur inconnue lors de la mise à jour du visiteur', e)
        alert('Erreur inconnue lors de la mise à jour du visiteur')
      }
    }
  }
  function closeEditModal() {
    showEditModal.value = false
  }

  // --- SUPPRESSION D'INVITÉ ---
  async function deletePerson(person: InvitePerson) {
    if (!confirm(`Supprimer définitivement ${person.prenom} ${person.nom} ?`)) return
    try {
      await $fetch(`${apiBase}/admin/guests/${person.id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      await fetchPersons()
    } catch (e) {
      console.error('Erreur lors de la suppression:', e)
      alert('Erreur lors de la suppression')
    }
  }

  onMounted(fetchPersons)
</script>

<style scoped>
  .input {
    border: 1px solid #e0e7ef;
    border-radius: 0.5rem;
    padding: 0.5rem 0.75rem;
    font-size: 1rem;
    outline: none;
    transition: border-color 0.2s, background-color 0.2s;
    background-color: #f9fafb;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  }
  .input:focus {
    border-color: #60a5fa;
    background-color: white;
  }
  .card {
    border-radius: 1rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    border: 1px solid #f3f4f6;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    position: relative;
    transition: box-shadow 0.2s;
    background: linear-gradient(135deg, #f8fafc 0%, #e0e7ef 100%);
  }
  .card:hover {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  }
</style>



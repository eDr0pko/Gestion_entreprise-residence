<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
        
        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
          <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
              <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-[#0097b2] rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">{{ conversation?.nom_groupe }}</h3>
                  <p class="text-sm text-gray-500">{{ t('components.groupMembersModal.memberCount', { count: members.length }) }}</p>
                </div>
              </div>
              <button 
                @click="closeModal"
                class="p-1 text-gray-400 hover:text-gray-600 rounded-lg transition-colors"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>

            <!-- Contenu -->
            <div class="flex-1 min-h-0 flex flex-col">
              <!-- Bouton ajouter des membres -->
              <div class="p-4 border-b border-gray-100">
                <button
                  @click="showAddMembers = true"
                  class="w-full flex items-center justify-center px-4 py-2 bg-[#0097b2] text-white rounded-lg hover:bg-[#007a94] transition-colors"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                  {{ t('components.groupMembersModal.addMembers') }}
                </button>
              </div>

              <!-- Liste des membres -->
              <div class="flex-1 overflow-y-auto">
                <div v-if="loadingMembers" class="p-4 text-center text-gray-500">
                  <svg class="w-5 h-5 animate-spin mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span class="text-sm">{{ t('components.groupMembersModal.loading') }}</span>
                </div>

                <div v-else-if="error" class="p-4 text-center text-red-500">
                  <p class="text-sm">{{ error }}</p>
                  <button @click="loadMembers" class="text-xs text-[#0097b2] hover:text-[#007a94] mt-2">
                    {{ t('components.groupMembersModal.retry') }}
                  </button>
                </div>

                <div v-else class="space-y-1 p-2">
                  <div
                    v-for="member in members"
                    :key="member.email"
                    class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors"
                  >
                    <!-- Avatar -->
                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                      <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                    </div>

                    <!-- Informations membre -->
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900 truncate">{{ member.nom_complet }}</p>
                      <p class="text-xs text-gray-500 truncate">{{ member.role }}</p>
                    </div>

                    <!-- Badge "Vous" si c'est l'utilisateur actuel -->
                    <div v-if="member.is_current_user" class="ml-2">
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ t('components.groupMembersModal.you') }}
                      </span>
                    </div>
                  </div>

                  <div v-if="members.length === 0" class="p-4 text-center text-gray-500">
                    <p class="text-sm">{{ t('components.groupMembersModal.noMembers') }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="p-4 border-t border-gray-200 flex justify-end">
              <button
                @click="closeModal"
                class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
              >
                {{ t('components.groupMembersModal.close') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>

  <!-- Modal d'ajout de membres -->
  <AddMembersModal 
    :show="showAddMembers"
    :conversation="conversation"
    :existing-members="members"
    @close="showAddMembers = false"
    @members-added="onMembersAdded"
  />
</template>

<script setup lang="ts">
  import { useI18n } from 'vue-i18n'
  const { t } = useI18n()
  import type { Member, Conversation } from '~/types'

  interface Props {
    show: boolean
    conversation: Conversation | null
  }

  const props = defineProps<Props>()

  const emit = defineEmits<{
    close: []
    membersUpdated: [members: Member[]]
  }>()

  const authStore = useAuthStore()
  const config = useRuntimeConfig()

  // État réactif
  const members = ref<Member[]>([])
  const loadingMembers = ref(false)
  const error = ref('')
  const showAddMembers = ref(false)

  // Charger les membres du groupe
  const loadMembers = async () => {
    if (!props.conversation) return
    if (loadingMembers.value) return
    const groupId = props.conversation.id_groupe_message
    try {
      loadingMembers.value = true
      error.value = ''
      console.debug('[GroupMembersModal] Loading members for group', groupId)
      const url = `${config.public.apiBase}/conversations/${groupId}/members`
      let response: any = await $fetch<any>(url, {
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Accept': 'application/json'
        }
      }).catch((e: any) => { console.error('[GroupMembersModal] Network/parse error', e); throw e })

      // Diagnostic
      console.debug('[GroupMembersModal] Raw response (type):', typeof response)
      if (typeof response === 'string') {
        try {
          response = JSON.parse(response)
          console.debug('[GroupMembersModal] Parsed string JSON response')
        } catch (parseErr) {
          console.warn('[GroupMembersModal] Unable to parse string response as JSON')
        }
      }
      console.debug('[GroupMembersModal] Response keys:', Object.keys(response || {}))

      // Normalisation: certains endpoints anciens renvoient directement {members:[...]} sans success
      const hasMembersArray = response && Array.isArray(response.members)
      const hasUsersArray = response && Array.isArray(response.users)
      const successFlag = (typeof response?.success === 'boolean') ? response.success : (hasMembersArray || hasUsersArray)

      if (!successFlag) {
        error.value = response?.error || 'Erreur lors du chargement des membres'
        return
      }

      const list = hasMembersArray ? response.members : (hasUsersArray ? response.users : [])
      members.value = list
      console.debug('[GroupMembersModal] Final members count', members.value.length)

    } catch (err: any) {
      console.error('[GroupMembersModal] Exception while loading members', err)
      error.value = err?.data?.message || err?.message || 'Impossible de charger les membres'
    } finally {
      loadingMembers.value = false
    }
  }

  // Gérer l'ajout de nouveaux membres
  const onMembersAdded = (newMembers: Member[]) => {
    // Ajouter les nouveaux membres à la liste
    members.value.push(...newMembers)
    
    // Émettre l'événement pour mettre à jour le parent
    emit('membersUpdated', members.value)
    
    // Fermer la modal d'ajout
    showAddMembers.value = false
  }

  // Fermer la modal
  const closeModal = () => {
    showAddMembers.value = false
    emit('close')
  }

  // Charger les membres à l'ouverture
  watch(() => props.show, (newShow) => {
    if (newShow && props.conversation) {
      loadMembers()
    }
  })
</script>

<style scoped>
  .modal-enter-active, .modal-leave-active {
    transition: all 0.3s ease;
  }

  .modal-enter-from, .modal-leave-to {
    opacity: 0;
    transform: scale(0.9);
  }
</style>



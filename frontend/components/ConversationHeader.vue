<template>
  <div 
    @click="$emit('show-members')"
    class="flex bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 lg:px-6 py-3 lg:py-4 items-center justify-between flex-shrink-0 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
    title="Voir les membres de la conversation"
  >
    <div class="flex items-center space-x-3">
      <div class="w-8 h-8 lg:w-10 lg:h-10 bg-[#0097b2] dark:bg-[#007a94] rounded-full flex items-center justify-center">
        <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
      </div>
      <div>
        <h2 class="text-sm lg:text-base font-semibold text-gray-900 dark:text-white">{{ conversation?.nom_groupe }}</h2>
        <p class="text-xs lg:text-sm text-gray-500 dark:text-gray-400">
          {{ t('conversationHeader.member', { count: conversation?.nombre_membres || 0 }) }}
        </p>
      </div>
    </div>
    <!-- Icône pour indiquer que c'est cliquable -->
    <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
    </svg>
  </div>
</template>

<script setup lang="ts">
  import { onMounted } from 'vue'
  import { useI18n } from 'vue-i18n'

// Import du système de thème
  const { initTheme } = useTheme()

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
    conversation: Conversation | null
  }

  const props = defineProps<Props>()

  const emit = defineEmits<{
    'show-members': []
  }>()

  // Initialisation du thème
  onMounted(() => {
    initTheme()
  })
</script>



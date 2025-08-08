<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-2xl font-bold">{{ t('adminStatsAdvanced.title') }}</h2>
      <button @click="fetchStats" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
        {{ t('adminStatsAdvanced.refresh') }}
      </button>
    </div>
    
    <div v-if="loadingStats" class="text-center py-8 text-gray-500">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <p class="mt-2">{{ t('adminStatsAdvanced.loading') }}</p>
    </div>
    <div v-else-if="errorStats" class="text-center py-8 text-red-500">
      <p>{{ t('adminStatsAdvanced.error') }} : {{ errorStats }}</p>
    </div>
    
    <div v-else class="space-y-8">
      <!-- Vue d'ensemble rapide -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Utilisateurs -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl border border-blue-200">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-sm font-medium text-blue-600">{{ t('adminStatsAdvanced.totalUsers') }}</h3>
              <p class="text-3xl font-bold text-blue-900">{{ stats.users?.total || 0 }}</p>
              <div class="text-xs text-blue-700 mt-1">
                <span>{{ stats.users?.admins || 0 }} {{ t('adminStatsAdvanced.admins') }}</span> •
                <span>{{ stats.users?.gardiens || 0 }} {{ t('adminStatsAdvanced.gardiens') }}</span> •
                <span>{{ stats.users?.residents || 0 }} {{ t('adminStatsAdvanced.residents') }}</span>
              </div>
            </div>
            <div class="p-3 bg-blue-200 rounded-full">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
            </div>
          </div>
        </div>

        <!-- Messages -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl border border-green-200">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-sm font-medium text-green-600">{{ t('adminStatsAdvanced.messagesExchanged') }}</h3>
              <p class="text-3xl font-bold text-green-900">{{ stats.messages?.total || 0 }}</p>
              <div class="text-xs text-green-700 mt-1">
                <span>{{ stats.groups?.total || 0 }} {{ t('adminStatsAdvanced.groups') }}</span>
              </div>
            </div>
            <div class="p-3 bg-green-200 rounded-full">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
              </svg>
            </div>
          </div>
        </div>

        <!-- Visites -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-xl border border-purple-200">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-sm font-medium text-purple-600">{{ t('adminStatsAdvanced.visits') }}</h3>
              <p class="text-3xl font-bold text-purple-900">{{ getTotalVisits() }}</p>
              <div class="text-xs text-purple-700 mt-1">
                <span>{{ stats.invites?.active || 0 }} {{ t('adminStatsAdvanced.activeGuests') }}</span>
              </div>
            </div>
            <div class="p-3 bg-purple-200 rounded-full">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.196-2.121l.196.121zm-6 0v-2a3 3 0 013-3 3 3 0 013 3v2h-6zM9 12a4 4 0 118 0 4 4 0 01-8 0zm8-2a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
          </div>
        </div>

        <!-- Réactions -->
        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-6 rounded-xl border border-yellow-200">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-sm font-medium text-yellow-600">{{ t('adminStatsAdvanced.reactions') }}</h3>
              <p class="text-3xl font-bold text-yellow-900">{{ stats.reactions?.total || 0 }}</p>
              <div class="text-xs text-yellow-700 mt-1">
                <span>{{ stats.files?.total || 0 }} {{ t('adminStatsAdvanced.sharedFiles') }}</span>
              </div>
            </div>
            <div class="p-3 bg-yellow-200 rounded-full">
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.01M15 10h1.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Détails par sections -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Utilisateurs les plus actifs -->
        <div class="bg-white p-6 rounded-xl border border-gray-200">
          <h3 class="text-lg font-semibold mb-4">{{ t('adminStatsAdvanced.mostActiveUsers') }}</h3>
          <div class="space-y-3">
            <div v-for="(user, index) in stats.messages?.most_active_users?.slice(0, 5)" :key="user.id_auteur" class="flex items-center justify-between">
              <div class="flex items-center">
                <span class="text-sm font-medium text-gray-900">{{ t('adminStatsAdvanced.user') }} #{{ user.id_auteur }}</span>
                <span v-if="index === 0" class="ml-2 text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded">👑 {{ t('adminStatsAdvanced.top') }}</span>
              </div>
              <span class="text-sm text-gray-600">{{ user.count }} {{ t('adminStatsAdvanced.messages') }}</span>
            </div>
          </div>
        </div>

        <!-- Groupes les plus actifs -->
        <div class="bg-white p-6 rounded-xl border border-gray-200">
          <h3 class="text-lg font-semibold mb-4">{{ t('adminStatsAdvanced.mostActiveGroups') }}</h3>
          <div class="space-y-3">
            <div v-for="(group, index) in stats.groups?.most_active?.slice(0, 5)" :key="group.id_groupe_message" class="flex items-center justify-between">
              <div class="flex items-center">
                <span class="text-sm font-medium text-gray-900">{{ t('adminStatsAdvanced.group') }} #{{ group.id_groupe_message }}</span>
                <span v-if="index === 0" class="ml-2 text-xs bg-green-100 text-green-700 px-2 py-1 rounded">🔥 {{ t('adminStatsAdvanced.hot') }}</span>
              </div>
              <span class="text-sm text-gray-600">{{ group.count }} {{ t('adminStatsAdvanced.messages') }}</span>
            </div>
          </div>
        </div>

        <!-- Émojis les plus utilisés -->
        <div class="bg-white p-6 rounded-xl border border-gray-200">
          <h3 class="text-lg font-semibold mb-4">{{ t('adminStatsAdvanced.favoriteEmojis') }}</h3>
          <div class="space-y-3">
            <div v-for="emoji in stats.reactions?.top_emojis?.slice(0, 5)" :key="emoji.emoji" class="flex items-center justify-between">
              <div class="flex items-center">
                <span class="text-lg mr-2">{{ emoji.emoji }}</span>
                <span class="text-sm font-medium text-gray-900">{{ emoji.emoji }}</span>
              </div>
              <span class="text-sm text-gray-600">{{ emoji.count }} {{ t('adminStatsAdvanced.times') }}</span>
            </div>
          </div>
        </div>

        <!-- Statut des visites -->
        <div class="bg-white p-6 rounded-xl border border-gray-200">
          <h3 class="text-lg font-semibold mb-4">{{ t('adminStatsAdvanced.visitStatus') }}</h3>
          <div class="space-y-3">
            <div v-for="visit in stats.visits?.by_status" :key="visit.statut_visite" class="flex items-center justify-between">
              <div class="flex items-center">
                <span class="w-3 h-3 rounded-full mr-2" :class="getVisitStatusColor(visit.statut_visite)"></span>
                <span class="text-sm font-medium text-gray-900">{{ getVisitStatusLabel(visit.statut_visite) }}</span>
              </div>
              <span class="text-sm text-gray-600">{{ visit.count }}</span>
            </div>
          </div>
        </div>

        <!-- Messages récents -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 lg:col-span-2">
          <h3 class="text-lg font-semibold mb-4">{{ t('adminStatsAdvanced.recentMessages') }}</h3>
          <div class="space-y-3 max-h-64 overflow-y-auto">
            <div v-for="message in stats.messages?.recent?.slice(0, 10)" :key="message.id_message" class="border-l-4 border-blue-200 pl-4">
              <div class="flex items-center justify-between mb-1">
                <span class="text-sm font-medium text-gray-900">{{ t('adminStatsAdvanced.user') }} #{{ message.id_auteur }}</span>
                <span class="text-xs text-gray-500">{{ formatDate(message.date_envoi) }}</span>
              </div>
              <p class="text-sm text-gray-700 line-clamp-2">{{ message.contenu_message }}</p>
            </div>
          </div>
        </div>

        <!-- Logs récents -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 lg:col-span-2">
          <h3 class="text-lg font-semibold mb-4">{{ t('adminStatsAdvanced.recentActivity') }}</h3>
          <div class="space-y-3 max-h-64 overflow-y-auto">
            <div v-for="log in stats.logs?.recent?.slice(0, 10)" :key="log.id" class="flex items-center justify-between">
              <div class="flex items-center">
                <span class="w-2 h-2 rounded-full bg-blue-500 mr-3"></span>
                <div>
                  <span class="text-sm font-medium text-gray-900">{{ log.action }}</span>
                  <p class="text-xs text-gray-600">{{ log.message }}</p>
                </div>
              </div>
              <span class="text-xs text-gray-500">{{ formatDate(log.created_at) }}</span>
            </div>
          </div>
        </div>

        <!-- Fichiers les plus volumineux -->
        <div class="bg-white p-6 rounded-xl border border-gray-200">
          <h3 class="text-lg font-semibold mb-4">{{ t('adminStatsAdvanced.largestFiles') }}</h3>
          <div class="space-y-3">
            <div v-for="file in stats.files?.largest" :key="file.nom_fichier" class="flex items-center justify-between">
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">{{ file.nom_fichier }}</p>
                <p class="text-xs text-gray-600">{{ formatDate(file.date_upload) }}</p>
              </div>
              <span class="text-sm text-gray-600 ml-2">{{ formatFileSize(file.taille_fichier) }}</span>
            </div>
          </div>
        </div>

        <!-- Actions populaires -->
        <div class="bg-white p-6 rounded-xl border border-gray-200">
          <h3 class="text-lg font-semibold mb-4">{{ t('adminStatsAdvanced.popularActions') }}</h3>
          <div class="space-y-3">
            <div v-for="action in stats.logs?.top_actions" :key="action.action" class="flex items-center justify-between">
              <span class="text-sm font-medium text-gray-900">{{ getActionLabel(action.action) }}</span>
              <span class="text-sm text-gray-600">{{ action.count }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Bannissements -->
      <div v-if="stats.bans?.total > 0" class="bg-red-50 p-6 rounded-xl border border-red-200">
        <h3 class="text-lg font-semibold text-red-800 mb-4">⚠️ {{ t('adminStatsAdvanced.bans') }} ({{ stats.bans.total }})</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div v-for="ban in stats.bans.motifs" :key="ban.motif || 'no-reason'" class="bg-white p-4 rounded-lg">
            <div class="font-medium text-gray-900">{{ ban.motif || t('adminStatsAdvanced.noReason') }}</div>
            <div class="text-sm text-gray-600">{{ ban.count }} {{ t('adminStatsAdvanced.cases') }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">

import { computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'


const { t } = useI18n()
// Utilisation du composable unifié
const {
  stats,
  loadingStats,
  errorStats,
  fetchStats
} = useAdminData()

// Fonctions utilitaires
function getTotalVisits() {
  if (!stats.value.visits?.by_status) return 0
  return stats.value.visits.by_status.reduce((total: number, visit: any) => total + visit.count, 0)
}

function getVisitStatusColor(status: string) {
  switch (status) {
    case 'programmee': return 'bg-blue-500'
    case 'en_cours': return 'bg-green-500'
    case 'terminee': return 'bg-gray-500'
    case 'annulee': return 'bg-red-500'
    default: return 'bg-gray-300'
  }
}

function getVisitStatusLabel(status: string) {
  switch (status) {
    case 'programmee': return t('adminStatsAdvanced.statusScheduled')
    case 'en_cours': return t('adminStatsAdvanced.statusOngoing')
    case 'terminee': return t('adminStatsAdvanced.statusFinished')
    case 'annulee': return t('adminStatsAdvanced.statusCancelled')
    default: return status
  }
}

function getActionLabel(action: string) {
  switch (action) {
    case 'login': return t('adminStatsAdvanced.actionLogin')
    case 'login_attempt': return t('adminStatsAdvanced.actionLoginAttempt')
    case 'read_messages': return t('adminStatsAdvanced.actionReadMessages')
    case 'add_reaction': return t('adminStatsAdvanced.actionAddReaction')
    default: return action
  }
}

function formatDate(dateString: string | null | undefined) {
  if (!dateString) return t('adminStatsAdvanced.notAvailable')
  try {
    return new Date(dateString).toLocaleString('fr-FR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  } catch {
    return t('adminStatsAdvanced.notAvailable')
  }
}

function formatFileSize(bytes: number) {
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

onMounted(() => {
  fetchStats()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>

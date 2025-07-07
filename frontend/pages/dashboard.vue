<template>
  <div class="min-h-screen flex flex-col bg-gradient-to-br from-[#e6f6fa] via-[#f3f8fa] to-white">
    <AppHeader :title="''" />
    <div class="flex flex-1">
      <DashboardSidebar
        :sections="sidebarSections"
        :selectedSection="selectedSection"
        @select="selectedSection = $event"
      >
        <template #top>
          <div class="mb-8 flex flex-col items-center">
            <div class="text-2xl font-extrabold bg-gradient-to-r from-[#0097b2] to-[#00b4d8] bg-clip-text text-transparent tracking-tight drop-shadow-lg mb-2 animate-fadein">Tableau de bord</div>
            <div class="h-1 w-12 rounded-full bg-gradient-to-r from-[#0097b2] to-[#00b4d8] opacity-60 mb-2"></div>
          </div>
        </template>
      </DashboardSidebar>
      <main class="flex-1 px-4 sm:px-8 py-8 lg:py-14 max-w-5xl mx-auto w-full dashboard-main">
        <transition name="fade" mode="out-in">
          <div :key="selectedSection">
            <component :is="sectionComponent" />
          </div>
        </transition>
      </main>
    </div>
    <AppFooter />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, defineAsyncComponent } from 'vue'
import { useAuthStore } from '~/stores/auth'
import AppHeader from '~/components/AppHeader.vue'
import AppFooter from '~/components/AppFooter.vue'
import DashboardSidebar from '~/components/DashboardSidebar.vue'

// Icônes Heroicons (outline)
const UserIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/></svg>'
}
const GroupIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-4a4 4 0 10-8 0 4 4 0 008 0zm6 6v2a2 2 0 01-2 2H7a2 2 0 01-2-2v-2a2 2 0 012-2h10a2 2 0 012 2z"/></svg>'
}
const HomeIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/></svg>'
}
const FileIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7V4a2 2 0 012-2h6a2 2 0 012 2v3M7 7v10a2 2 0 002 2h6a2 2 0 002-2V7M7 7h10"/></svg>'
}
const ChartIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 11V7a1 1 0 112 0v4a1 1 0 01-2 0zm-4 4v-2a1 1 0 112 0v2a1 1 0 01-2 0zm8 0v-4a1 1 0 112 0v4a1 1 0 01-2 0z"/></svg>'
}
const CogIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-1.14 1.603-1.14 1.902 0a1.724 1.724 0 002.573 1.066c.94-1.543 3.31-.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.14.3 1.14 1.603 0 1.902a1.724 1.724 0 00-1.066 2.573c1.543.94.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.3 1.14-1.603 1.14-1.902 0a1.724 1.724 0 00-2.573-1.066c-.94 1.543-.826-3.31 2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.14-.3-1.14-1.603 0-1.902a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>'
}
const BanIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9l-6 6"/></svg>'
}
const VisitIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>'
}

const authStore = useAuthStore()
const role = authStore.userRole

// Définition des sections selon le rôle
const adminSections = [
  { key: 'users', label: 'Utilisateurs', icon: UserIcon },
  { key: 'groupes', label: 'Groupes & Messages', icon: GroupIcon },
  { key: 'logements', label: 'Logements & Bans', icon: HomeIcon },
  { key: 'visites', label: 'Visites & Invités', icon: VisitIcon },
  { key: 'stats', label: 'Statistiques & Rapports', icon: ChartIcon },
  { key: 'settings', label: 'Paramètres', icon: CogIcon },
]
const gardienSections = [
  { key: 'incidents', label: 'Incidents', icon: BanIcon },
  { key: 'messages', label: 'Messages & Groupes', icon: GroupIcon },
  { key: 'visites', label: 'Visites', icon: VisitIcon },
  { key: 'fichiers', label: 'Fichiers & Documents', icon: FileIcon },
]

const sidebarSections = computed(() => {
  if (role === 'admin') return adminSections
  if (role === 'gardien') return gardienSections
  return []
})

const selectedSection = ref(sidebarSections.value[0]?.key || '')

// Composants de contenu central (à personnaliser selon la section)
const sectionComponent = computed(() => {
  if (role === 'admin') {
    switch (selectedSection.value) {
      case 'users': return defineAsyncComponent(() => import('~/components/dashboard/AdminUsers.vue'))
      case 'groupes': return defineAsyncComponent(() => import('~/components/dashboard/AdminGroupes.vue'))
      case 'logements': return defineAsyncComponent(() => import('~/components/dashboard/AdminLogements.vue'))
      case 'visites': return defineAsyncComponent(() => import('~/components/dashboard/AdminVisites.vue'))
      case 'stats': return defineAsyncComponent(() => import('~/components/dashboard/AdminStats.vue'))
      case 'settings': return defineAsyncComponent(() => import('~/components/dashboard/AdminSettings.vue'))
      default: return { template: '<div class="text-gray-500">Sélectionnez une section</div>' }
    }
  }
  if (role === 'gardien') {
    switch (selectedSection.value) {
      case 'incidents': return defineAsyncComponent(() => import('~/components/dashboard/GardienIncidents.vue'))
      case 'messages': return defineAsyncComponent(() => import('~/components/dashboard/GardienMessages.vue'))
      case 'visites': return defineAsyncComponent(() => import('~/components/dashboard/GardienVisites.vue'))
      case 'fichiers': return defineAsyncComponent(() => import('~/components/dashboard/GardienFichiers.vue'))
      default: return { template: '<div class="text-gray-500">Sélectionnez une section</div>' }
    }
  }
  return { template: '<div class="text-gray-500">Vous n\'avez pas accès à ce tableau de bord.</div>' }
})
</script>

<style scoped>
.dashboard-main {
  background: rgba(255,255,255,0.92);
  border-radius: 2rem;
  box-shadow: 0 8px 32px 0 rgba(0,151,178,0.10), 0 1.5px 4px 0 rgba(0,0,0,0.04);
  min-height: 70vh;
  margin-top: 2.5rem;
  margin-bottom: 2.5rem;
  transition: box-shadow 0.3s cubic-bezier(.4,0,.2,1), background 0.3s cubic-bezier(.4,0,.2,1);
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s cubic-bezier(.4,0,.2,1);
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

body {
  background: linear-gradient(135deg, #e6f6fa 0%, #f3f8fa 100%);
}

/* Sidebar glassmorphisme et animation */
.dashboard-sidebar {
  background: rgba(255,255,255,0.85);
  border-radius: 2rem 0 0 2rem;
  box-shadow: 0 8px 32px 0 rgba(0,151,178,0.10), 0 1.5px 4px 0 rgba(0,0,0,0.04);
  border-right: 1.5px solid #e0e7ef;
  min-width: 260px;
  max-width: 300px;
  padding-top: 2.5rem;
  padding-bottom: 2.5rem;
  margin-top: 2.5rem;
  margin-bottom: 2.5rem;
  transition: box-shadow 0.3s cubic-bezier(.4,0,.2,1), background 0.3s cubic-bezier(.4,0,.2,1);
  z-index: 10;
}

.dashboard-sidebar button {
  font-size: 1.08rem;
  letter-spacing: 0.01em;
  font-weight: 600;
  border: none;
  outline: none;
  background: none;
  cursor: pointer;
  margin-bottom: 0.25rem;
  padding: 0.85rem 1.2rem;
  border-radius: 1.2rem;
  transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.dashboard-sidebar button:hover, .dashboard-sidebar button:focus {
  background: linear-gradient(90deg, #0097b2 0%, #00b4d8 100%);
  color: #fff;
  box-shadow: 0 2px 8px 0 rgba(0,151,178,0.10);
}
.dashboard-sidebar .bg-gradient-to-r {
  background-clip: text;
}

@media (max-width: 1024px) {
  .dashboard-sidebar {
    min-width: 70px;
    max-width: 90px;
    padding-left: 0.5rem;
    padding-right: 0.5rem;
  }
  .dashboard-main {
    margin-top: 1rem;
    margin-bottom: 1rem;
    border-radius: 1rem;
  }
}

@keyframes fadein {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadein {
  animation: fadein 0.7s cubic-bezier(.4,0,.2,1);
}
</style>

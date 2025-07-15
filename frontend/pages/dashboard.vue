<template>
  <div class="min-h-screen flex flex-col bg-gradient-to-br from-[#e6f6fa] via-[#f3f8fa] to-white relative overflow-x-hidden">
    <header class="fixed top-0 left-0 w-full z-50 bg-gradient-to-br from-[#e6f6fa] via-[#f3f8fa] to-white shadow-sm">
      <AppHeader :title="''" />
    </header>
    <div style="height:64px;"></div> <!-- Décalage pour le header sticky -->
    <div class="flex flex-1 z-10 relative">
      <aside class="dashboard-sidebar-flat">
        <DashboardSidebar
          :sections="sidebarSections"
          :selectedSection="selectedSection"
          @select="selectedSection = $event"
        >
          <template #top>
            <div class="mb-10 flex flex-col items-center">
              <div class="text-3xl font-extrabold bg-gradient-to-r from-[#0097b2] to-[#00b4d8] bg-clip-text text-transparent tracking-tight drop-shadow-xl mb-3 animate-fadein">Tableau de bord</div>
              <div class="h-1 w-16 rounded-full bg-gradient-to-r from-[#0097b2] to-[#00b4d8] opacity-70 mb-2"></div>
            </div>
          </template>
        </DashboardSidebar>
      </aside>
      <main class="dashboard-main-flat flex-1 px-2 sm:px-8 py-8 lg:py-14 w-full">
        <transition name="fade" mode="out-in">
          <div :key="selectedSection" class="relative z-10">
            <component :is="sectionComponent" />
          </div>
        </transition>
      </main>
    </div>
    <AppFooter class="z-20 relative" />
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
  const roles = computed(() => authStore.userRoles || []) // userRoles: array of roles (e.g. ['admin', 'gardien'])

  // Définition des sections selon le rôle et la matrice d'accès
  // Ces deux sections sont accessibles si la personne a admin OU gardien (ou les deux)
  const sharedSections = [
    { key: 'admin-incidents', label: 'Incidents', icon: BanIcon },
    { key: 'admin-visiteurs', label: 'Gestion Visiteurs', icon: VisitIcon },
  ]
  const adminOnlySections = [
    { key: 'admin-residents', label: 'Gestion Résidents et Gardiens', icon: UserIcon },
    { key: 'admin-conversations', label: 'Gestion des conversations', icon: GroupIcon },
    { key: 'admin-logs', label: 'Logs', icon: FileIcon },
    { key: 'admin-settings', label: 'Paramètres', icon: CogIcon },
    { key: 'admin-stats', label: 'Statistiques Admin', icon: ChartIcon },
  ]
  const gardienOnlySections = [
    { key: 'gardien-stats', label: 'Statistiques Gardien', icon: ChartIcon },
  ]

  const sidebarSections = computed(() => {
    const sections = []
    // Les sections partagées sont accessibles si la personne a admin OU gardien
    if (roles.value.includes('admin') || roles.value.includes('gardien')) {
      sections.push(...sharedSections)
    }
    if (roles.value.includes('admin')) {
      sections.push(...adminOnlySections)
    }
    if (roles.value.includes('gardien')) {
      sections.push(...gardienOnlySections)
    }
    // Supprime les doublons éventuels (par key)
    const uniqueSections = []
    const seen = new Set()
    for (const s of sections) {
      if (s && s.key && !seen.has(s.key)) {
        uniqueSections.push(s)
        seen.add(s.key)
      }
    }
    return uniqueSections
  })

  const selectedSection = ref(sidebarSections.value[0]?.key || '')

  // Composants de contenu central (à personnaliser selon la section)
  const sectionComponent = computed(() => {
    // On autorise l'accès à toutes les sections présentes dans sidebarSections
    switch (selectedSection.value) {
      case 'admin-stats': return defineAsyncComponent(() => import('~/components/dashboard/AdminStats.vue'))
      case 'admin-residents': return defineAsyncComponent(() => import('~/components/dashboard/AdminResidents.vue'))
      case 'admin-visiteurs': return defineAsyncComponent(() => import('~/components/dashboard/GestionVisiteurs.vue'))
      case 'admin-incidents': return defineAsyncComponent(() => import('~/components/dashboard/AdminIncidents.vue'))
      case 'admin-logs': return defineAsyncComponent(() => import('~/components/dashboard/AdminLogs.vue'))
      case 'admin-conversations': return defineAsyncComponent(() => import('~/components/dashboard/AdminConversations.vue'))
      case 'admin-settings': return defineAsyncComponent(() => import('~/components/dashboard/AdminSettings.vue'))
      case 'gardien-stats': return defineAsyncComponent(() => import('~/components/dashboard/GardienStats.vue'))
      case 'gardien-visiteurs': return defineAsyncComponent(() => import('~/components/dashboard/GestionVisiteurs.vue'))
      default: return { template: '<div class="text-gray-500">Sélectionnez une section</div>' }
    }
  })
</script>

<style scoped>

  .dashboard-main-flat {
    background: linear-gradient(120deg, #fafdff 60%, #e6f6fa 100%);
    min-height: 70vh;
    margin: 0;
    border-radius: 0 2.5rem 2.5rem 0;
    box-shadow: 0 2px 16px 0 rgba(0,151,178,0.06);
    border: 1.5px solid #e0e7ef;
    border-left: none;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    transition: box-shadow 0.2s, background 0.2s;
  }

  .dashboard-sidebar-flat {
    min-width: 230px;
    max-width: 260px;
    padding-top: 2.5rem;
    padding-bottom: 2.5rem;
    margin: 0;
    border-radius: 0;
    background: linear-gradient(120deg, #fafdff 80%, #e6f6fa 100%);
    border-right: 1.5px solid #e0e7ef;
    box-shadow: 0 2px 16px 0 rgba(0,151,178,0.04);
    z-index: 20;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    height: 100%;
    transition: box-shadow 0.2s, background 0.2s;
  }

  .dashboard-sidebar {
    background: transparent !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    border-right: none !important;
    border: none !important;
    margin: 0 !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
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
    border-radius: 0.8rem;
    transition: background 0.18s, color 0.18s, box-shadow 0.18s;
    color: #222;
    display: flex;
    align-items: center;
    gap: 0.7rem;
    box-shadow: none;
  }
  .dashboard-sidebar button:hover, .dashboard-sidebar button:focus, .dashboard-sidebar button.active {
    background: linear-gradient(90deg, #0097b2 0%, #00b4d8 100%);
    color: #fff;
    box-shadow: 0 2px 8px 0 rgba(0,151,178,0.10);
  }
  .dashboard-sidebar .bg-gradient-to-r {
    background-clip: text;
  }

  @media (max-width: 1024px) {
    .dashboard-sidebar-flat {
      min-width: 54px;
      max-width: 80px;
      border-radius: 0;
      padding-left: 0.2rem;
      padding-right: 0.2rem;
    }
    .dashboard-main-flat {
      margin: 0;
      border-radius: 0;
    }
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

  @keyframes fadein {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .animate-fadein {
    animation: fadein 0.7s cubic-bezier(.4,0,.2,1);
  }
</style>

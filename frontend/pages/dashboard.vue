<template>
  <div class="min-h-screen flex flex-col bg-gradient-to-br from-slate-50 via-gray-50 to-white relative overflow-x-hidden">
    <header class="fixed top-0 left-0 w-full z-50 bg-white/80 backdrop-blur-md border-b border-gray-100 shadow-sm">
      <AppHeader :title="''" />
    </header>
    <div style="height:64px;"></div> <!-- Décalage pour le header sticky -->
    <div class="flex flex-1 z-10 relative flex-col lg:flex-row min-h-screen">
      <aside class="dashboard-sidebar-flat w-full lg:w-auto flex-shrink-0 fixed lg:relative top-16 lg:top-0 left-0 h-[calc(100vh-64px)] lg:h-auto overflow-y-auto lg:overflow-visible z-40 bg-white/95 backdrop-blur-sm border-r border-gray-100">
        <DashboardSidebar
          :sections="sidebarSections"
          :selectedSection="selectedSection"
          @select="selectedSection = $event"
        >
          <template #top>
            <div class="mb-8 lg:mb-12 flex flex-col items-center px-6 py-8">
              <div class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight mb-2">{{ t('dashboard.title') }}</div>
              <div class="text-sm text-gray-500 font-medium">Gestion moderne</div>
              <div class="h-0.5 w-12 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 mt-4"></div>
            </div>
          </template>
        </DashboardSidebar>
      </aside>
      <main class="dashboard-main-flat flex-1 px-4 sm:px-6 md:px-8 lg:px-12 py-8 md:py-10 lg:py-16 w-full min-w-0 lg:ml-0 pt-[calc(100vh-64px)] lg:pt-8 md:pt-10">
        <transition name="fade" mode="out-in">
          <div :key="selectedSection" class="relative z-10 max-w-7xl mx-auto">
            <component :is="sectionComponent" />
          </div>
        </transition>
      </main>
    </div>
    <AppFooter class="z-20 relative" />
  </div>
</template>

<script setup lang="ts">
  import { ref, computed, defineAsyncComponent, onMounted } from 'vue'
  import { useAuthStore } from '~/stores/auth'
  import { useI18n } from 'vue-i18n'
  import AppHeader from '~/components/AppHeader.vue'
  import AppFooter from '~/components/AppFooter.vue'
  import DashboardSidebar from '~/components/DashboardSidebar.vue'

  // Import proper icon components
  import UserIcon from '~/components/icons/UserIcon.vue'
  import GroupIcon from '~/components/icons/GroupIcon.vue'
  import HomeIcon from '~/components/icons/HomeIcon.vue'
  import FileIcon from '~/components/icons/FileIcon.vue'
  import ChartIcon from '~/components/icons/ChartIcon.vue'
  import CogIcon from '~/components/icons/CogIcon.vue'
  import BanIcon from '~/components/icons/BanIcon.vue'
  import VisitIcon from '~/components/icons/VisitIcon.vue'
  import IdentificationIcon from '~/components/icons/IdentificationIcon.vue'

  const authStore = useAuthStore()
  
  // Initialiser l'authentification au montage
  onMounted(async () => {
    console.log('Dashboard mounted - initializing auth...')
    if (process.client) {
      authStore.initAuth()
      
      // Vérifier que l'utilisateur est authentifié
      if (!authStore.isAuthenticated) {
        console.log('User not authenticated, redirecting to login...')
        await navigateTo('/login')
        return
      }
      
      console.log('User authenticated:', authStore.user)
      console.log('User token:', !!authStore.token)
    }
  })
  
  const roles = computed(() => authStore.userRoles || []) // userRoles: array of roles (e.g. ['admin', 'gardien'])

  // Définition des sections selon le rôle et la matrice d'accès
  // Ces deux sections sont accessibles si la personne a admin OU gardien (ou les deux)
  const { t } = useI18n()
  
  const sharedSections = [
    { key: 'admin-incidents', label: () => t('dashboard.sections.incidents'), icon: BanIcon },
    { key: 'admin-visiteurs', label: () => t('dashboard.sections.visitors'), icon: VisitIcon },
    { key: 'admin-badges', label: () => t('dashboard.sections.badges'), icon: IdentificationIcon },
  ]
  const adminOnlySections = [
    { key: 'admin-residents', label: () => t('dashboard.sections.residents'), icon: UserIcon },
    { key: 'admin-logs', label: () => t('dashboard.sections.logs'), icon: FileIcon },
    { key: 'admin-settings', label: () => t('dashboard.sections.settings'), icon: CogIcon },
    { key: 'admin-stats', label: () => t('dashboard.sections.stats'), icon: ChartIcon },
  ]
  const gardienOnlySections = [
    { key: 'gardien-stats', label: () => t('dashboard.sections.stats'), icon: ChartIcon },
  ]

  const sidebarSections = computed(() => {
    const sections = []
    // Les sections partagées sont accessibles si la personne a admin OU gardien
    if (roles.value.includes('admin') || roles.value.includes('gardien')) {
      sections.push(...sharedSections.map(s => ({ ...s, label: s.label() })))
    }
    if (roles.value.includes('admin')) {
      sections.push(...adminOnlySections.map(s => ({ ...s, label: s.label() })))
    }
    if (roles.value.includes('gardien')) {
      sections.push(...gardienOnlySections.map(s => ({ ...s, label: s.label() })))
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
      case 'admin-stats': return defineAsyncComponent(() => import('~/components/dashboard/AdminStatsAdvanced.vue'))
      case 'admin-residents': return defineAsyncComponent(() => import('~/components/dashboard/AdminResidents.vue'))
      case 'admin-visiteurs': return defineAsyncComponent(() => import('~/components/dashboard/GestionVisiteurs.vue'))
      case 'admin-badges': return defineAsyncComponent(() => import('~/components/dashboard/AdminBadges.vue'))
      case 'admin-incidents': return defineAsyncComponent(() => import('~/components/dashboard/AdminIncidents.vue'))
      case 'admin-logs': return defineAsyncComponent(() => import('~/components/dashboard/AdminLogs.vue'))
      case 'admin-settings': return defineAsyncComponent(() => import('~/components/dashboard/AdminSettings.vue'))
      case 'gardien-stats': return defineAsyncComponent(() => import('~/components/dashboard/GardienStats.vue'))
      case 'gardien-visiteurs': return defineAsyncComponent(() => import('~/components/dashboard/GestionVisiteurs.vue'))
      default: return { template: '<div class="text-gray-500">Sélectionnez une section</div>' }
    }
  })

  useHead({
    title: computed(() => t('dashboard.pageTitle'))
  })
</script>

<style scoped>

  .dashboard-main-flat {
    background: linear-gradient(120deg, #fafdff 60%, #e6f6fa 100%);
    min-height: calc(100vh - 64px);
    margin: 0;
    border-radius: 0 2.5rem 2.5rem 0;
    box-shadow: 0 2px 16px 0 rgba(0,151,178,0.06);
    border: 1.5px solid #e0e7ef;
    border-left: none;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    transition: box-shadow 0.2s, background 0.2s;
    min-width: 0;
    overflow-y: auto;
  }

  .dashboard-sidebar-flat {
    min-width: 230px;
    max-width: 260px;
    width: 260px;
    padding-top: 2.5rem;
    padding-bottom: 2.5rem;
    margin: 0;
    border-radius: 0;
    background: linear-gradient(120deg, #fafdff 80%, #e6f6fa 100%);
    border-right: 1.5px solid #e0e7ef;
    box-shadow: 0 2px 16px 0 rgba(0,151,178,0.04);
    z-index: 40;
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
      min-width: 100vw;
      max-width: 100vw;
      width: 100vw;
      height: auto;
      border-radius: 0;
      padding: 1rem;
      border-right: none;
      border-bottom: 1.5px solid #e0e7ef;
      box-shadow: 0 2px 8px 0 rgba(0,151,178,0.08);
      position: relative;
      top: 0;
    }
    .dashboard-main-flat {
      margin: 0;
      border-radius: 0;
      padding: 1rem;
      border: none;
      min-height: calc(100vh - 200px);
      padding-top: 1rem;
    }
    .flex-1.z-10.relative.flex-col.lg\:flex-row {
      flex-direction: column !important;
    }
  }
  @media (max-width: 640px) {
    .dashboard-sidebar-flat {
      min-width: 100vw;
      max-width: 100vw;
      width: 100vw;
      padding: 0.75rem;
    }
    .dashboard-main-flat {
      padding: 0.5rem;
      min-height: calc(100vh - 150px);
    }
  }

  .fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s cubic-bezier(.4,0,.2,1);
  }
  .fade-enter-from, .fade-leave-to {
    opacity: 0;
  }

  /* Styles modernes globaux */
  body {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  }

  /* Animations fluides */
  @keyframes fadeInUp {
    from { 
      opacity: 0; 
      transform: translateY(20px); 
    }
    to { 
      opacity: 1; 
      transform: translateY(0); 
    }
  }
  
  @keyframes slideInLeft {
    from { 
      opacity: 0; 
      transform: translateX(-20px); 
    }
    to { 
      opacity: 1; 
      transform: translateX(0); 
    }
  }

  .animate-fadeInUp {
    animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .animate-slideInLeft {
    animation: slideInLeft 0.5s cubic-bezier(0.4, 0, 0.2, 1);
  }

  /* Effet de profondeur pour les cards */
  .card-shadow {
    box-shadow: 
      0 1px 3px rgba(0, 0, 0, 0.05),
      0 10px 15px -3px rgba(0, 0, 0, 0.05),
      0 4px 6px -2px rgba(0, 0, 0, 0.02);
  }

  .card-shadow-hover {
    box-shadow: 
      0 4px 6px rgba(0, 0, 0, 0.07),
      0 20px 25px -5px rgba(0, 0, 0, 0.08),
      0 10px 10px -5px rgba(0, 0, 0, 0.04);
  }

  /* Gradients modernes */
  .gradient-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  }

  .gradient-success {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  }

  .gradient-warning {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
  }

  .gradient-danger {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  }
</style>


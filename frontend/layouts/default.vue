<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <!-- Navigation Header -->
    <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-40">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <!-- Logo -->
          <div class="flex items-center gap-3">
            <NuxtLink to="/" class="flex items-center gap-2">
              <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold">🏢</span>
              </div>
              <span class="text-xl font-bold text-gray-800 dark:text-white">
                Gestion Résidence
              </span>
            </NuxtLink>
          </div>

          <!-- Navigation -->
          <nav class="hidden md:flex items-center space-x-8">
            <NuxtLink 
              to="/planning" 
              class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors"
              active-class="text-blue-600 dark:text-blue-400"
            >
              📅 Planning
            </NuxtLink>
            <NuxtLink 
              to="/residents" 
              class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors"
              active-class="text-blue-600 dark:text-blue-400"
            >
              👥 Résidents
            </NuxtLink>
            <NuxtLink 
              to="/visitors" 
              class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors"
              active-class="text-blue-600 dark:text-blue-400"
            >
              🚶 Visiteurs
            </NuxtLink>
          </nav>

          <!-- Actions -->
          <div class="flex items-center gap-3">
            <!-- Theme Toggle -->
            <button
              @click="toggleTheme"
              class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
              :title="isDark ? 'Mode clair' : 'Mode sombre'"
            >
              <span class="text-lg">{{ isDark ? '☀️' : '🌙' }}</span>
            </button>

            <!-- Mobile menu button -->
            <button
              @click="toggleMobileMenu"
              class="md:hidden p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            >
              <span class="text-lg">☰</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Navigation -->
      <div v-if="showMobileMenu" class="md:hidden border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
        <div class="px-4 py-2 space-y-1">
          <NuxtLink 
            to="/planning"
            class="block px-3 py-2 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors"
            @click="closeMobileMenu"
          >
            📅 Planning
          </NuxtLink>
          <NuxtLink 
            to="/residents"
            class="block px-3 py-2 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors"
            @click="closeMobileMenu"
          >
            👥 Résidents
          </NuxtLink>
          <NuxtLink 
            to="/visitors"
            class="block px-3 py-2 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors"
            @click="closeMobileMenu"
          >
            🚶 Visiteurs
          </NuxtLink>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-auto">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="text-center text-sm text-gray-500 dark:text-gray-400">
          © {{ new Date().getFullYear() }} Gestion Résidence. Tous droits réservés.
        </div>
      </div>
    </footer>

    <!-- Notification System -->
    <NotificationToast ref="toastRef" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useNotifications } from '~/composables/useNotifications'
import { useTheme } from '~/composables/useTheme'

const { setToastInstance } = useNotifications()
const { isDark, toggleTheme, initTheme } = useTheme()
const toastRef = ref(null)

// Mobile menu
const showMobileMenu = ref(false)

function toggleMobileMenu() {
  showMobileMenu.value = !showMobileMenu.value
}

function closeMobileMenu() {
  showMobileMenu.value = false
}

onMounted(() => {
  initTheme()
  
  // Initialize notification system
  if (toastRef.value) {
    setToastInstance(toastRef.value)
  }
})
</script>

<style>
/* Global dark mode styles */
.dark {
  color-scheme: dark;
}

/* Smooth transitions for theme changes */
* {
  transition-property: background-color, border-color, color;
  transition-duration: 200ms;
  transition-timing-function: ease-in-out;
}
</style>

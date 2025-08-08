<template>
  <div v-if="isAdmin" class="admin-nav">
    <div class="admin-nav-trigger" @click="isOpen = !isOpen">
      <Icon name="mdi:cog" class="admin-icon" />
      <span class="admin-text">Admin</span>
      <Icon name="mdi:chevron-down" class="chevron" :class="{ 'rotate': isOpen }" />
    </div>
    
    <Transition name="dropdown">
      <div v-show="isOpen" class="admin-dropdown">
        <NuxtLink 
          to="/admin/settings" 
          class="admin-link"
          @click="isOpen = false"
        >
          <Icon name="mdi:palette" />
          <span>Personnalisation</span>
        </NuxtLink>
        
        <NuxtLink 
          to="/admin/users" 
          class="admin-link"
          @click="isOpen = false"
        >
          <Icon name="mdi:account-group" />
          <span>Utilisateurs</span>
        </NuxtLink>
        
        <NuxtLink 
          to="/admin/dashboard" 
          class="admin-link"
          @click="isOpen = false"
        >
          <Icon name="mdi:view-dashboard" />
          <span>Tableau de bord</span>
        </NuxtLink>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

const authStore = useAuthStore()
const isOpen = ref(false)

// Vérifier si l'utilisateur est administrateur
const isAdmin = computed(() => {
  return authStore.userRole === 'admin' || authStore.userRole === 'administrator'
})

// Fermer le menu quand on clique ailleurs
onMounted(() => {
  if (process.client) {
    document.addEventListener('click', (event) => {
      const target = event.target as HTMLElement
      if (!target.closest('.admin-nav')) {
        isOpen.value = false
      }
    })
  }
})
</script>

<style scoped>
.admin-nav {
  position: relative;
  display: inline-block;
}

.admin-nav-trigger {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: linear-gradient(135deg, var(--color-primary, #3B82F6), var(--color-secondary, #10B981));
  color: white;
  border-radius: 0.75rem;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.admin-nav-trigger:hover {
  transform: translateY(-1px);
  box-shadow: 0 8px 12px -1px rgba(0, 0, 0, 0.15);
}

.admin-icon {
  font-size: 1.25rem;
}

.admin-text {
  font-weight: 600;
  font-size: 0.875rem;
}

.chevron {
  font-size: 1rem;
  transition: transform 0.2s ease;
}

.chevron.rotate {
  transform: rotate(180deg);
}

.admin-dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  margin-top: 0.5rem;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  overflow: hidden;
  min-width: 200px;
  z-index: 50;
}

.admin-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  color: #374151;
  text-decoration: none;
  transition: all 0.2s ease;
  border-bottom: 1px solid #f3f4f6;
}

.admin-link:last-child {
  border-bottom: none;
}

.admin-link:hover {
  background: #f9fafb;
  color: var(--color-primary, #3B82F6);
}

.admin-link span {
  font-weight: 500;
  font-size: 0.875rem;
}

/* Transitions */
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from {
  opacity: 0;
  transform: translateY(-8px) scale(0.95);
}

.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-8px) scale(0.95);
}

@media (max-width: 768px) {
  .admin-nav-trigger {
    padding: 0.5rem;
  }
  
  .admin-text {
    display: none;
  }
  
  .admin-dropdown {
    right: auto;
    left: 0;
  }
}
</style>

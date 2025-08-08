<template>
  <div>
    <transition name="notif-btn-fade">
      <button
        v-show="!open"
        @click="toggleSidebar"
        class="notif-btn"
        :class="{ clicked: btnClicked }"
        @animationend="btnClicked = false"
      >
        🔔
      </button>
    </transition>

    <transition name="slide">
      <div v-if="open" class="sidebar">
        <div class="sidebar-header flex justify-between items-center px-4 py-2 border-b">
          <div class="flex space-x-4">
            <button @click="showTrash = false" :class="!showTrash ? 'font-bold underline' : 'text-gray-500'">
              Notifications
            </button>
            <button @click="showTrash = true" :class="showTrash ? 'font-bold underline' : 'text-gray-500'">
              Corbeille
            </button>
          </div>
          <button @click="toggleSidebar" class="close-btn text-xl" title="Fermer">✖</button>
        </div>

        <ul class="notif-list space-y-4 px-4">
          <li
            v-for="visite in filteredNotifications"
            :key="visite.id_visite"
            class="bg-white shadow rounded-lg p-4 border-l-4 transition-all"
            :class="{
              'border-green-500': visite.statut_visite === 'terminee',
              'border-yellow-500': visite.statut_visite === 'en_cours',
              'border-blue-500': visite.statut_visite === 'programmee',
              'border-red-500': visite.statut_visite === 'annulee',
              'border-gray-500': !['terminee', 'en_cours', 'programmee', 'annulee'].includes(visite.statut_visite)
            }"
          >
            <h3 class="font-semibold text-lg">{{ visite.motif_visite }}</h3>
            <p class="text-sm text-gray-500">
              Du {{ formatDate(visite.date_visite_start) }} au {{ formatDate(visite.date_visite_end) }}
            </p>
            <span
              class="inline-block mt-2 px-2 py-1 text-xs font-semibold rounded-full"
              :class="{
                'bg-green-100 text-green-700': visite.statut_visite === 'terminee',
                'bg-yellow-100 text-yellow-700': visite.statut_visite === 'en_cours',
                'bg-blue-100 text-blue-700': visite.statut_visite === 'programmee',
                'bg-red-100 text-red-700': visite.statut_visite === 'annulee',
                'bg-gray-200 text-gray-800': !['terminee', 'en_cours', 'programmee', 'annulee'].includes(visite.statut_visite)
              }"
            >
              {{ visite.statut_visite || 'non défini' }}
            </span>

            <div
              v-if="!['terminee', 'en_cours', 'annulee'].includes(visite.statut_visite)"
              class="mt-4 flex gap-2 flex-wrap"
            >
              <button
                @click="changerStatut(visite.id_visite, 'en_cours')"
                class="bg-green-100 text-green-800 px-3 py-1 rounded hover:bg-green-200 text-sm"
              >
                ✅ Accepter
              </button>
              <button
                @click="changerStatut(visite.id_visite, 'annulee')"
                class="bg-red-100 text-red-800 px-3 py-1 rounded hover:bg-red-200 text-sm"
              >
                ❌ Refuser
              </button>
              <button
                @click="changerStatut(visite.id_visite, 'banni')"
                class="bg-gray-200 text-gray-800 px-3 py-1 rounded hover:bg-gray-300 text-sm"
              >
                🚫 Bannir
              </button>
              <button
                @click="showReportMessage"
                class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded text-sm opacity-50 cursor-not-allowed"
                disabled
                :title="'Fonctionnalité non disponible'"
              >
                📅 Reporter
              </button>
            </div>
          </li>
          <li v-if="filteredNotifications.length === 0">
            {{ showTrash ? 'Aucune visite annulée.' : 'Aucune visite.' }}
          </li>
        </ul>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
const authStore = useAuthStore()
const config = useRuntimeConfig()

const open = ref(false)
const btnClicked = ref(false)
const showTrash = ref(false)
const notifications = ref([])
const emit = defineEmits(['update:open'])

const toggleSidebar = () => {
  btnClicked.value = true
  open.value = !open.value
  emit('update:open', open.value) // Ajout : on informe le parent
  if (open.value) fetchNotifications()
}

const fetchNotifications = async () => {
  try {
    const res = await fetch(`${config.public.apiBase}/visites`, {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json'
      }
    })
    const data = await res.json()
    if (res.ok && data.success) {
      notifications.value = data.visites
    } else {
      notifications.value = []
    }
  } catch (err) {
    notifications.value = []
  }
}

const changerStatut = async (id, statut) => {
  try {
    const res = await fetch(`${config.public.apiBase}/visites/${id}/statut`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ statut })
    })
    const data = await res.json()
    if (!res.ok || !data.success) throw new Error(data.message)
    await fetchNotifications()
  } catch (e) {
    console.error('Erreur lors du changement de statut:', e)
  }
}

const filteredNotifications = computed(() =>
  notifications.value.filter(v =>
    showTrash.value ? v.statut_visite === 'annulee' : v.statut_visite !== 'annulee'
  )
)

const formatDate = (str) => {
  const date = new Date(str)
  return date.toLocaleString('fr-FR', {
    day: '2-digit', month: '2-digit',
    hour: '2-digit', minute: '2-digit'
  })
}

// Fonction pour afficher un message d'information
const showReportMessage = () => {
  // Fonction temporaire - la fonctionnalité de report sera implémentée plus tard
}
</script>

<style scoped>
.notif-btn {
  position: absolute;
  top: 130px;
  right: 12px;
  z-index: 1001;
  background: linear-gradient(to right, #0097b2, #008699);
  color: #fff;
  border: none;
  width: 50px;
  height: 50px;
  border-radius: 9999px;
  font-size: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: transform 0.15s, background 0.3s;
  box-shadow: 0 10px 30px -10px rgba(0, 151, 178, 0.4);
}
.notif-btn:hover {
  background: linear-gradient(to right, #008699, #007a94);
  transform: scale(1.05);
}
.notif-btn.clicked {
  animation: notif-pop 0.15s;
}
@keyframes notif-pop {
  0% { transform: scale(1); }
  50% { transform: scale(1.15); }
  100% { transform: scale(1); }
}
.notif-btn-fade-enter-active, .notif-btn-fade-leave-active {
  transition: opacity 0.2s;
}
.notif-btn-fade-enter-from, .notif-btn-fade-leave-to {
  opacity: 0;
}
.sidebar {
  position: fixed;
  top: 0;
  right: 0;
  width: 360px;
  height: 100%;
  background: #fff;
  box-shadow: -2px 0 8px rgba(0, 0, 0, 0.15);
  z-index: 1000;
  display: flex;
  flex-direction: column;
}
.sidebar-header {
  background: #f5f5f5;
  border-bottom: 1px solid #eee;
}
.close-btn {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
}
.notif-list {
  list-style: none;
  padding: 1rem;
  margin: 0;
  flex: 1;
  overflow-y: auto;
}
.slide-enter-active, .slide-leave-active {
  transition: transform 0.3s;
}
.slide-enter-from, .slide-leave-to {
  transform: translateX(100%);
}
</style>

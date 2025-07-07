<template>
  <div class="min-h-screen flex flex-col bg-gradient-to-br from-teal-50 via-white to-cyan-50 relative overflow-hidden font-sans">
    <AppHeader />
    <!-- Bulles de fond décoratives -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-cyan-400 to-transparent rounded-full -translate-y-48 translate-x-48 opacity-60"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-gradient-to-tr from-cyan-400 to-transparent rounded-full translate-y-40 -translate-x-40 opacity-60"></div>

    <!-- Bandeau sticky -->
    <div class="flex justify-between items-center p-4 bg-white bg-opacity-80 backdrop-blur-sm border-b border-white border-opacity-50 sticky top-0 z-30 w-[90vw] mx-auto mt-4 rounded-2xl shadow-md">
      <button @click="prevWeek" class="px-4 py-2 bg-gradient-to-r from-[#0097b2] to-[#008699] text-white rounded-xl shadow hover:scale-105 transition-transform">
        ← Semaine précédente
      </button>
      <div class="font-semibold text-gray-700 text-lg">
        Semaine du {{ formatDate(weekDates[0]) }} au {{ formatDate(weekDates[6]) }}
      </div>
      <button @click="nextWeek" class="px-4 py-2 bg-gradient-to-r from-[#0097b2] to-[#008699] text-white rounded-xl shadow hover:scale-105 transition-transform">
        Semaine suivante →
      </button>
    </div>

    <!-- Tableau -->
    <div class="w-[90vw] max-w-full h-[80vh] mx-auto mt-6 bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl shadow-xl overflow-hidden relative z-10">
      <div class="relative overflow-auto h-full" ref="scrollDiv">
        <!-- Heure à gauche de la ligne -->
        <div
          class="absolute left-2 text-xs text-[#ff2e2e] font-semibold z-50 pointer-events-none select-none"
          :style="{ top: (currentTimePosition - 6) + 'px' }"
        >
          {{ currentTimeLabel }}
        </div>

        <!-- Ligne de l'heure actuelle -->
        <div
          class="absolute left-20 right-0 h-[2px] bg-[#ff2e2e] z-40 pointer-events-none rounded-full"
          :style="{ top: currentTimePosition + 'px' }"
        />

        <table class="min-w-full table-fixed border-separate border-spacing-0 text-sm text-gray-700">
          <thead>
            <tr class="sticky top-0 z-20 bg-white bg-opacity-80 backdrop-blur border-b border-white">
              <th class="w-20"></th>
              <th v-for="(date, idx) in weekDates" :key="idx" class="py-3 px-2 w-28 text-center border-r border-gray-200">
                <div :class="dateIsToday(date) ? 'text-[#0097b2] font-bold' : 'font-medium'">
                  {{ days[idx] }}
                </div>
                <div class="text-xs text-gray-500">
                  {{ date.getDate().toString().padStart(2, '0') }} / {{ (date.getMonth()+1).toString().padStart(2, '0') }}
                </div>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="i in 288"
              :key="i"
              :class="(i % 24 === 0) ? 'bg-white bg-opacity-30' : (Math.floor((i-1)/12) % 2 === 0 ? 'bg-white bg-opacity-20' : 'bg-transparent')"
            >
              <td
                v-if="(i-1) % 12 === 0"
                class="text-right pr-3 text-xs font-mono text-gray-400 w-20 align-top h-3"
                :rowspan="12"
              >
                {{ String(Math.floor((i-1)/12)).padStart(2, '0') }}:00
              </td>
              <td
                v-for="(date, idx) in weekDates"
                :key="idx"
                :class="[
                  'h-1 w-28 border-r border-gray-200 transition-all duration-100 hover:bg-cyan-100 cursor-pointer',
                  dateIsToday(date) ? 'bg-cyan-50' : '',
                  (i) % 12 === 0 ? 'border-b border-gray-300' : ''
                ]"
                @click="handleSlotClick(date, Math.floor((i-1)/12), ((i-1)%12)*5)"
              >
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Popups -->
    <Notif  />
    <div class="h-4 md:h-8"></div>
    <AppFooter />
  </div>
</template>




<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import axios from 'axios'
import AppHeader from '~/components/AppHeader.vue'
import AppFooter from '~/components/AppFooter.vue'
import VisitePopup from '@/components/VisitePopup.vue'
import Notif from '@/components/notif.vue'

const days = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim']
const today = new Date()
const weekOffset = ref(0)


//fonction du front (semaine, affichage, etc)
function getStartOfWeek(date) {
  const d = new Date(date)
  const day = d.getDay() || 7 // Lundi=1, Dimanche=7
  d.setHours(0, 0, 0, 0)
  d.setDate(d.getDate() - (day - 1))
  return d
}

const weekDates = computed(() => {
  const start = getStartOfWeek(new Date(today.getFullYear(), today.getMonth(), today.getDate()))
  start.setDate(start.getDate() + weekOffset.value * 7)
  return Array.from({ length: 7 }, (_, i) => {
    const d = new Date(start)
    d.setDate(start.getDate() + i)
    return d
  })
})

function dateIsToday(date) {
  const now = new Date()
  return (
    date.getDate() === now.getDate() &&
    date.getMonth() === now.getMonth() &&
    date.getFullYear() === now.getFullYear()
  )
}
function prevWeek() {
  weekOffset.value--
}
function nextWeek() {
  weekOffset.value++
}

function formatDate(date) {
  return `${date.getDate().toString().padStart(2, '0')}/${(date.getMonth()+1).toString().padStart(2, '0')}`
}

const scrollDiv = ref(null)

onMounted(async () => {
  //await fetchVisites() ////feeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee
  nextTick(() => {
    if (scrollDiv.value) {
      scrollDiv.value.scrollTop = scrollDiv.value.scrollHeight / 3.2
    }
  })
})

const currentTimeLabel = ref('')

function updateCurrentTimePosition() {
  const now = new Date()
  const minutesSinceMidnight = now.getHours() * 60 + now.getMinutes()
  const totalMinutesInDay = 24 * 60
  const tableHeight = 80 * 16

  currentTimePosition.value = (minutesSinceMidnight / totalMinutesInDay) * tableHeight

  const h = now.getHours().toString().padStart(2, '0')
  const m = now.getMinutes().toString().padStart(2, '0')
  currentTimeLabel.value = `${h}:${m}`
}
const currentTimePosition = ref(0)

onMounted(() => {
  updateCurrentTimePosition()
  setInterval(updateCurrentTimePosition, 60000) // update every minute
})

//backend

definePageMeta({
  middleware: 'auth'
})

const authStore = useAuthStore()
const config = useRuntimeConfig()
/*
interface Visite {
  id_visite: number                // Identifiant unique de la visite
  email_visiteur: string          // Email du visiteur
  id_invitation: number           // Identifiant de l'invitation liée
  motif_visite: string            // Motif ou sujet de la visite
  date_visite_start: string       // Date et heure de début (ISO string)
  date_visite_end: string         // Date et heure de fin (ISO string)
  statut_visite: 'terminee' | 'en_attente' | 'annulee' | 'programmee' // Statut de la visite
}
*/

/*

  async function fetchVisites() {
    try {
      const response = await axios.get('http://localhost:8000/api/visite')
      visites.value = response.data
    } catch (error) {
      console.error('Erreur API visites:', error)
    }
  }
  function hasVisite(date, hour, minute) {
    const slotStart = new Date(date)
    slotStart.setHours(hour, minute, 0, 0)
    return visites.value.some(visite => {
      const start = new Date(visite.date_visite_start)
      const end = new Date(visite.date_visite_end)
      return slotStart >= start && slotStart < end
    })
  }
  function isStartOfVisite(date, hour, minute) {
    const slotTime = new Date(date)
    slotTime.setHours(hour, minute, 0, 0)
    return visites.value.some(visite => {
      const start = new Date(visite.date_visite_start)
      return slotTime.getTime() === start.getTime()
    })
  }
  function getVisiteForSlot(date, hour, minute) {
    const slotStart = new Date(date)
    slotStart.setHours(hour, minute, 0, 0)
    return visites.value.find(visite => {
      const start = new Date(visite.date_visite_start)
      const end = new Date(visite.date_visite_end)
      return slotStart >= start && slotStart < end
    })
  }
  function getDureeVisite(visite) {
    if (!visite) return ''
    const start = new Date(visite.date_visite_start)
    const end = new Date(visite.date_visite_end)
    const diffMs = end - start
    const minutes = Math.floor(diffMs / 60000)
    const h = Math.floor(minutes / 60)
    const m = minutes % 60
    return `${h > 0 ? h + 'h' : ''}${m > 0 ? m + 'min' : ''}`
  }
  function handleSlotClick(date, hour, minute) {
    const visite = getVisiteForSlot(date, hour, minute)
    if (visite) {
      popupVisite.value = visite
      popupDate.value = new Date(date)
      popupDate.value.setHours(hour, minute, 0, 0)
      popupOpen.value = true
    }
  }
  function getSlotColor(date, hour, minute) {
    const slotStart = new Date(date)
    slotStart.setHours(hour, minute, 0, 0)
    const visite = visites.value.find(visite => {
      const start = new Date(visite.date_visite_start)
      const end = new Date(visite.date_visite_end)
      return slotStart >= start && slotStart < end
    })
    if (!visite) return ''
    switch (visite.statut_visite) {
      case 'programmee': return 'bg-blue-400'
      case 'terminee': return 'bg-green-500'
      case 'annulee': return 'bg-gray-400'
      case 'en_attente': return 'bg-yellow-400'
      default: return 'bg-green-500'
    }
  }
*/

</script>


<style>
/* Masquer la scrollbar pour tous les navigateurs */
.overflow-auto::-webkit-scrollbar {
  display: none;
}
.overflow-auto {
  -ms-overflow-style: none;  /* IE et Edge */
  scrollbar-width: none;     /* Firefox */
}
.no-border-right {
  border-right: none !important;
}
</style>

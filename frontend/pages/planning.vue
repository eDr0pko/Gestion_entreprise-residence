<template>
  <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100">
    <!-- Bandeau du haut sticky -->
    <div class="flex justify-between items-center p-4 border-b bg-white sticky top-0 z-30">
      <button @click="prevWeek" class="px-3 py-1 border bg-white text-gray-700 hover:bg-gray-100 transition">
        ← Semaine précédente
      </button>
      <div class="font-bold text-base text-gray-800">
        Semaine du {{ formatDate(weekDates[0]) }} au {{ formatDate(weekDates[6]) }}
      </div>
      <button @click="nextWeek" class="px-3 py-1 border bg-white text-gray-700 hover:bg-gray-100 transition">
        Semaine suivante →
      </button>
    </div>
    <div class="w-[80vw] max-w-full h-[80vh] mx-auto bg-gray-100 flex flex-col">
      <div class="overflow-auto flex-1" ref="scrollDiv">
        <table class="min-w-full border-collapse table-fixed">
          <thead>
            <tr class="sticky top-0 z-20 bg-white">
              <th class="w-20 min-w-20 max-w-20 bg-white border-r border-b"></th>
              <th
                v-for="(date, idx) in weekDates"
                :key="idx"
                class="py-2 px-2 border-b border-r text-center w-28 min-w-28 max-w-28"
                :class="[
                  idx === 5 || idx === 6 ? 'text-pink-600 font-semibold' : '',
                  dateIsToday(date) ? 'bg-blue-100 border-blue-400' : ''
                ]"
              >
                <div :class="dateIsToday(date) ? 'text-blue-700 font-bold' : 'text-gray-700 font-semibold'">
                  {{ days[idx] }}
                </div>
                <div :class="dateIsToday(date) ? 'text-blue-700 font-bold' : 'text-gray-500'">
                  {{ date.getDate().toString().padStart(2, '0') }} / {{ (date.getMonth()+1).toString().padStart(2, '0') }}
                </div>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="i in 288"
              :key="i"
              :class="i % 24 === 0 ? 'bg-gray-50' : (Math.floor((i-1)/12) % 2 === 0 ? 'bg-gray-50' : 'bg-white')"
            >
              <td
                v-if="(i-1) % 12 === 0"
                class="text-right pr-3 border-r border-b border-gray-600 bg-white text-xs font-mono text-gray-400 w-20 align-top h-3"
                :rowspan="12"
              >
                {{ String(Math.floor((i-1)/12)).padStart(2, '0') }}:00
              </td>
              <td
                v-for="(date, idx) in weekDates"
                :key="idx"
                :class="[
                  'border-r border-gray-200 h-1 w-28 min-w-28 max-w-28',
                  dateIsToday(date) ? 'bg-blue-100' : '',
                  (i) % 12 === 0 ? 'border-b border-gray-800' : '',
                  getSlotColor(date, Math.floor((i-1)/12), ((i-1)%12)*5),
                  hasVisite(date, Math.floor((i-1)/12), ((i-1)%12)*5) ? 'cursor-pointer text-white' : '',
                  (getCurrentSlotIndex().i === i && getCurrentSlotIndex().idx === idx)
                    ? 'border-t-2 border-red-600 font-bold no-border-right'
                    : ''
                ]"
                @click="handleSlotClick(date, Math.floor((i-1)/12), ((i-1)%12)*5)"
              >
                <template v-if="isStartOfVisite(date, Math.floor((i-1)/12), ((i-1)%12)*5)">
                  <div class="font-bold truncate">
                    {{ getVisiteForSlot(date, Math.floor((i-1)/12), ((i-1)%12)*5)?.motif_visite }}
                  </div>
                  <div>
                    {{ getDureeVisite(getVisiteForSlot(date, Math.floor((i-1)/12), ((i-1)%12)*5)) }}
                  </div>
                </template>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <Notif @refreshPlanning="fetchVisites" />
  <VisitePopup :show="popupOpen" :slotDate="popupDate" :visite="popupVisite" @close="popupOpen = false" />
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import axios from 'axios'
import VisitePopup from '@/components/VisitePopup.vue'
import Notif from '@/components/notif.vue'

const days = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim']
const today = new Date()
const weekOffset = ref(0)
const visites = ref([])

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
  await fetchVisites()
  nextTick(() => {
    if (scrollDiv.value) {
      scrollDiv.value.scrollTop = scrollDiv.value.scrollHeight / 3.2
    }
  })
})

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
  }) || null
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

const popupOpen = ref(false)
const popupDate = ref(null)
const popupVisite = ref(null)

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

function getCurrentSlotIndex() {
  const now = new Date()
  const todayIdx = weekDates.value.findIndex(date =>
    date.getDate() === now.getDate() &&
    date.getMonth() === now.getMonth() &&
    date.getFullYear() === now.getFullYear()
  )
  if (todayIdx === -1) return { i: -1, idx: -1 }
  const minutes = now.getHours() * 60 + now.getMinutes()
  const slot = Math.floor(minutes / 5) + 1
  return { i: slot, idx: todayIdx }
}
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

/* Ajoute ceci dans ton <style> */
.no-border-right {
  border-right: none !important;
}
</style>

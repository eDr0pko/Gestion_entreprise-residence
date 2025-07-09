<template>
  <div class="min-h-screen flex flex-col bg-gradient-to-br from-teal-50 via-white to-cyan-50 relative overflow-hidden font-sans">
    <AppHeader />
    <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-cyan-400 to-transparent rounded-full -translate-y-48 translate-x-48 opacity-60"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-gradient-to-tr from-cyan-400 to-transparent rounded-full translate-y-40 -translate-x-40 opacity-60"></div>

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

    <button
      class="btnAddVisite fixed right-6 top-[110px] z-50 bg-[#0097b2] text-white text-2xl rounded-full w-10 h-10 flex items-center justify-center shadow hover:scale-105 transition"
      @click="openCreationPopup()"
    >
      +
    </button>

    <div class="w-[90vw] max-w-full h-[80vh] mx-auto mt-6 bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl shadow-xl overflow-hidden relative z-10">
      <div class="relative overflow-auto h-full" ref="scrollDiv">
        <div
          class="absolute left-2 text-xs text-[#ff2e2e] font-semibold z-50 pointer-events-none select-none"
          :style="{ top: (currentTimePosition - 6) + 'px' }"
        >
          {{ currentTimeLabel }}
        </div>

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
                  'h-1 w-28 border-r border-gray-200 transition-all duration-100 relative',
                  dateIsToday(date) ? 'bg-cyan-50' : '',
                  (i) % 12 === 0 ? 'border-b border-gray-300' : ''
                ]"
                @click="handleCellClick(date, Math.floor((i-1)/12), ((i-1)%12)*5)"
              >
                <div
                  v-if="isStartOfVisite(date, Math.floor((i-1)/12), ((i-1)%12)*5)"
                  :style="{ height: getVisiteHeight(visiteForSlot(date, Math.floor((i-1)/12), ((i-1)%12)*5)) + 'rem', top: '0' }"
                  class="absolute left-0 right-0 rounded px-1 py-0.5 text-xs text-white z-10 cursor-pointer"
                  :class="getVisiteColor(visiteForSlot(date, Math.floor((i-1)/12), ((i-1)%12)*5))"
                  @click.stop="handleVisiteClick(visiteForSlot(date, Math.floor((i-1)/12), ((i-1)%12)*5))"
                >
                  {{ visiteForSlot(date, Math.floor((i-1)/12), ((i-1)%12)*5)?.motif_visite }}
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <Notif />
    <VisitePopup v-if="popupOpen" :visite="popupVisite" @close="popupOpen = false" />
    <CreateVisitePopup v-if="popupCreateOpen" :defaultStart="popupCreateStart" @close="popupCreateOpen = false" @refresh="fetchVisites" />
    <div class="h-4 md:h-8"></div>
    <AppFooter />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import AppHeader from '~/components/AppHeader.vue'
import AppFooter from '~/components/AppFooter.vue'
import Notif from '@/components/notif.vue'
import VisitePopup from '@/components/VisitePopup.vue'
import CreateVisitePopup from '@/components/CreateVisitePopup.vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const config = useRuntimeConfig()

const jours = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim']
const days = jours
const today = new Date()
const weekOffset = ref(0)

function getStartOfWeek(date) {
  const d = new Date(date)
  const day = d.getDay() || 7
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
  return date.getDate() === now.getDate() && date.getMonth() === now.getMonth() && date.getFullYear() === now.getFullYear()
}
function prevWeek() { weekOffset.value-- }
function nextWeek() { weekOffset.value++ }
function formatDate(date) {
  return `${date.getDate().toString().padStart(2, '0')}/${(date.getMonth()+1).toString().padStart(2, '0')}`
}

const scrollDiv = ref(null)

async function fetchVisites() {
  const res = await fetch(`${config.public.apiBase}/visites`, {
    headers: {
      'Authorization': `Bearer ${authStore.token}`,
      'Accept': 'application/json'
    }
  })
  const data = await res.json()
  if (res.ok && data.success) {
    visites.value = data.visites.filter(v => v.statut_visite === 'programmee')
  }
}

onMounted(async () => {
  await fetchVisites()
  nextTick(() => {
    if (scrollDiv.value) scrollDiv.value.scrollTop = scrollDiv.value.scrollHeight / 3.2
  })
})

const currentTimeLabel = ref('')
const currentTimePosition = ref(0)
function updateCurrentTimePosition() {
  const now = new Date()
  const minutesSinceMidnight = now.getHours() * 60 + now.getMinutes()
  const totalMinutesInDay = 24 * 60
  const tableHeight = 80 * 16
  currentTimePosition.value = (minutesSinceMidnight / totalMinutesInDay) * tableHeight
  currentTimeLabel.value = `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`
}
onMounted(() => {
  updateCurrentTimePosition()
  setInterval(updateCurrentTimePosition, 60000)
})

const visites = ref([])
function visiteForSlot(date, hour, minute) {
  const slotStart = new Date(date)
  slotStart.setHours(hour, minute, 0, 0)
  return visites.value.find(visite => {
    const start = new Date(visite.date_visite_start)
    return slotStart.getTime() === start.getTime()
  })
}
function isStartOfVisite(date, hour, minute) {
  return !!visiteForSlot(date, hour, minute)
}
function getVisiteHeight(visite) {
  if (!visite) return 1
  const start = new Date(visite.date_visite_start)
  const end = new Date(visite.date_visite_end)
  const diffMs = end - start
  const minutes = Math.floor(diffMs / 60000)
  return (minutes / 5) * 0.25
}
function getVisiteColor(visite) {
  if (!visite) return ''
  return 'bg-blue-500'
}

const popupOpen = ref(false)
const popupVisite = ref(null)
function handleVisiteClick(visite) {
  popupVisite.value = visite
  popupOpen.value = true
}

const popupCreateOpen = ref(false)
const popupCreateStart = ref('')
function openCreationPopup(startTime = '') {
  popupCreateStart.value = startTime
  popupCreateOpen.value = true
}
function handleCellClick(date, hour, minute) {
  const clickedDate = new Date(date)
  clickedDate.setHours(hour, minute, 0, 0)
  openCreationPopup(clickedDate.toISOString().slice(0, 16))
}


</script>

<style>
.overflow-auto::-webkit-scrollbar { display: none; }
.overflow-auto { -ms-overflow-style: none; scrollbar-width: none; }
.btnAddVisite {
  position: absolute;
  top: 200px;
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
</style>



<template>
  <div class="min-h-screen flex flex-col bg-gradient-to-br from-teal-50 via-white to-cyan-50 relative overflow-hidden font-sans">
    <AppHeader />
    <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-cyan-400 to-transparent rounded-full -translate-y-48 translate-x-48 opacity-60"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-gradient-to-tr from-cyan-400 to-transparent rounded-full translate-y-40 -translate-x-40 opacity-60"></div>

    <!-- Card semaine + boutons coins -->
    <div class="w-[90vw] max-w-full flex justify-center mx-auto mt-4 relative z-20">
      <div class="flex items-center w-full max-w-3xl mx-auto relative" style="min-width:340px;">
        <!-- Bouton création visite à gauche -->
        <div class="flex-shrink-0 mr-2 flex items-center" style="height: 100%;">
          <button
            v-if="!notifOpen"
            class="btnAddVisite bg-gradient-to-r from-[#0097b2] to-[#00b4d8] text-white text-2xl rounded-full flex items-center justify-center shadow-xl hover:scale-105 hover:shadow-2xl transition-all border-2 border-white/60 backdrop-blur"
            @click="openCreationPopup()"
            aria-label="Ajouter une visite"
            style="position: relative; left: 0;"
          >
            <span style="font-size: 2rem; line-height: 1;">+</span>
          </button>
        </div>
        <!-- Card semaine -->
        <div class="flex-1">
          <div class="flex justify-between items-center p-4 bg-white bg-opacity-80 backdrop-blur-sm border-b border-white border-opacity-50 rounded-2xl shadow-md week-card-nav">
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
        </div>
        <!-- Bouton notifications à droite -->
        <div class="flex-shrink-0 ml-2 flex items-center" style="height: 100%;">
          <Notif @update:open="notifOpen = $event" />
        </div>
      </div>
    </div>

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
                <template v-if="visitesForCell(date, Math.floor((i-1)/12), ((i-1)%12)*5).length">
                  <div
                    v-for="(visite, vIdx) in visitesForCell(date, Math.floor((i-1)/12), ((i-1)%12)*5)"
                    :key="visite.id_visite || vIdx"
                    :style="getVisiteStyle(visite, vIdx, visitesForCell(date, Math.floor((i-1)/12), ((i-1)%12)*5).length)"
                    class="visite-block absolute rounded px-2 py-1 text-xs font-semibold shadow-lg cursor-pointer border border-white/70 transition-all duration-200"
                    :class="getVisiteColor(visite)"
                    @click.stop="handleVisiteClick(visite)"
                  >
                    <span class="block truncate">{{ visite.motif_visite }}</span>
                    <span class="block text-[10px] opacity-80">{{ formatVisiteTime(visite) }}</span>
                  </div>
                </template>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Notif déplacé au-dessus du bouton +, ne pas dupliquer ici -->
    <VisitePopup v-if="popupOpen" :visite="popupVisite" @close="popupOpen = false" />
    <CreateVisitePopup v-if="popupCreateOpen" :defaultStart="popupCreateStart" @close="popupCreateOpen = false" @refresh="fetchVisites" />
    <div class="h-4 md:h-8"></div>
    <AppFooter />
  </div>
</template>

<script setup>


// Retourne toutes les visites qui commencent à ce créneau
function visitesForCell(date, hour, minute) {
  const slotStart = new Date(date)
  slotStart.setHours(hour, minute, 0, 0)
  return visites.value.filter(visite => {
    const start = new Date(visite.date_visite_start)
    return slotStart.getTime() === start.getTime()
  })
}

// Calcule le style pour chaque visite (gestion de la superposition)
function getVisiteStyle(visite, idx, total) {
  const height = getVisiteHeight(visite)
  const width = total > 1 ? `calc(${100 / total}% - 2px)` : '100%'
  const left = total > 1 ? `calc(${(100 / total) * idx}% + 1px)` : '0'
  return {
    height: height + 'rem',
    width,
    left,
    top: '0',
    zIndex: 10 + idx,
    boxShadow: '0 2px 8px 0 #0097b255',
    background: 'linear-gradient(90deg, #00b4d8 0%, #0097b2 100%)',
    color: '#fff',
    borderRadius: '0.6rem',
    border: '1.5px solid #fff',
    display: 'flex',
    flexDirection: 'column',
    justifyContent: 'center',
    alignItems: 'flex-start',
    padding: '0.2rem 0.5rem',
    marginRight: '2px',
    marginBottom: '2px',
    overflow: 'hidden',
  }
}

// Affichage heure début/fin
function formatVisiteTime(visite) {
  const start = new Date(visite.date_visite_start)
  const end = new Date(visite.date_visite_end)
  return `${start.getHours().toString().padStart(2, '0')}:${start.getMinutes().toString().padStart(2, '0')} - ${end.getHours().toString().padStart(2, '0')}:${end.getMinutes().toString().padStart(2, '0')}`
}
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
      // Si admin ou gardien, on affiche toutes les visites (hors annulées)
      const user = authStore.user || {}
      const isAdmin = user.role === 'admin' || user.niveau_acces === 'super_admin' || user.niveau_acces === 'admin_standard'
      const isGardien = user.role === 'gardien'
      if (isAdmin || isGardien) {
        visites.value = data.visites.filter(v => v.statut_visite !== 'annule')
      } else {
        // Sinon, filtrer selon l'utilisateur (exemple: visites où il est concerné)
        visites.value = data.visites.filter(v => v.statut_visite !== 'annule' && (v.id_personne === user.id_personne || v.id_invite === user.id_personne))
      }
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
    switch (visite.statut_visite) {
      case 'programmee':
        return 'bg-blue-500'
      case 'en_cours':
        return 'bg-green-500'
      case 'annule':
        return 'hidden' // ne devrait pas arriver car filtré, mais sécurité
      default:
        return 'bg-yellow-500' // autre statut = jaune
    }
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

  const notifOpen = ref(false)
</script>

<style>
  .visite-block {
    transition: box-shadow 0.2s, transform 0.15s;
    box-shadow: 0 2px 8px 0 #0097b255;
    border-radius: 0.6rem;
    border: 1.5px solid #fff;
    background: linear-gradient(90deg, #00b4d8 0%, #0097b2 100%);
    color: #fff;
    cursor: pointer;
    margin-bottom: 2px;
    margin-right: 2px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: center;
    overflow: hidden;
  }
  .visite-block:hover {
    box-shadow: 0 6px 24px 0 #00b4d8aa;
    transform: scale(1.04);
    z-index: 99;
  }
  .visite-block span {
    display: block;
    width: 100%;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
  }
  .overflow-auto::-webkit-scrollbar { display: none; }
  .overflow-auto { -ms-overflow-style: none; scrollbar-width: none; }
  .btnAddVisite {
    background: linear-gradient(90deg, #0097b2 0%, #00b4d8 100%);
    color: #fff;
    border: 2px solid rgba(255,255,255,0.6);
    width: 48px;
    height: 48px;
    border-radius: 50%;
    font-size: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 8px 24px -8px #0097b2a0;
    transition: transform 0.15s, box-shadow 0.3s, background 0.3s;
    backdrop-filter: blur(6px);
  }
  .btnAddVisite:hover {
    background: linear-gradient(90deg, #00b4d8 0%, #0097b2 100%);
    transform: scale(1.08);
    box-shadow: 0 12px 32px -8px #00b4d8a0;
  }
  /* Boutons coins card semaines */
  .week-card-btns-container {
    position: relative;
    display: inline-block;
    overflow: visible;
  }
  .week-card-btns-row {
    position: absolute;
    left: 0;
    top: 50%;
    width: 100%;
    transform: translateY(-50%);
    z-index: 41;
    pointer-events: none;
  }
  .week-card-nav {
    position: relative;
    z-index: 10;
  }
</style>



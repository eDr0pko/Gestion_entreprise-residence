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
                :class=" [
                  idx === 5 || idx === 6 ? 'text-pink-600 font-semibold' : '',
                  dateIsToday(date) ? 'bg-blue-100 border-blue-400' : ''
                ]"
              >
                <div :class="dateIsToday(date) ? 'text-blue-700 font-bold' : 'text-gray-700 font-semibold'">
                  {{ days[idx] }}
                </div>
                <div :class="dateIsToday(date) ? 'text-blue-700 font-bold' : 'text-gray-500'">
                  {{ date.getDate() }} / {{ (date.getMonth()+1).toString().padStart(2, '0') }}
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
                :class=" [
                  'border-r border-gray-200 h-1 cursor-pointer hover:bg-blue-50 w-28 min-w-28 max-w-28',
                  dateIsToday(date) ? 'bg-blue-100' : '',
                  (i) % 12 === 0 ? 'border-b border-gray-800' : ''
                ]"
              >
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <Notif />
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'

const days = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim']

const today = new Date()
const weekOffset = ref(0)

function getStartOfWeek(date) {
  const d = new Date(date)
  const day = d.getDay() || 7 // Lundi = 1, Dimanche = 7
  d.setHours(0,0,0,0)
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

onMounted(() => {
  nextTick(() => {
    if (scrollDiv.value) {
      // Pour scroller au milieu :
      scrollDiv.value.scrollTop = scrollDiv.value.scrollHeight / 3.2

      // Pour scroller tout en bas, utilise plutôt :
      // scrollDiv.value.scrollTop = scrollDiv.value.scrollHeight
    }
  })
})
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
</style>
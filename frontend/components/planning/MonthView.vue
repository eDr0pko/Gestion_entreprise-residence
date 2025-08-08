<template>
  <div class="month-view-container h-full flex flex-col">
    <div class="month-header p-4 border-b border-gray-200 dark:border-gray-700">
      <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
        {{ selectedDate.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' }) }}
      </h3>
    </div>
    
    <div class="month-content flex-1 p-4">
      <!-- Calendar Grid -->
      <div class="calendar-grid">
        <!-- Days of week header -->
        <div class="calendar-header grid grid-cols-7 gap-1 mb-2">
          <div
            v-for="day in daysOfWeek"
            :key="day"
            class="text-center text-sm font-medium text-gray-600 dark:text-gray-400 py-2"
          >
            {{ day }}
          </div>
        </div>
        
        <!-- Calendar days -->
        <div class="calendar-body grid grid-cols-7 gap-1">
          <div
            v-for="date in calendarDates"
            :key="date.toISOString()"
            class="calendar-cell min-h-[80px] p-2 bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
            :class="{
              'opacity-40': !isCurrentMonth(date),
              'ring-2 ring-blue-500': isToday(date),
              'bg-blue-50 dark:bg-blue-900/20': isSelected(date)
            }"
            @click="selectDate(date)"
          >
            <div class="text-sm font-medium text-gray-800 dark:text-white mb-1">
              {{ date.getDate() }}
            </div>
            
            <!-- Visites indicators -->
            <div class="space-y-1">
              <div
                v-for="visite in getVisitesForDate(date).slice(0, 3)"
                :key="visite.id_visite"
                class="text-xs px-2 py-1 rounded-full truncate cursor-pointer"
                :class="getVisiteIndicatorClass(visite)"
                @click.stop="$emit('visit-click', visite)"
              >
                {{ visite.motif_visite }}
              </div>
              
              <!-- More indicator -->
              <div
                v-if="getVisitesForDate(date).length > 3"
                class="text-xs text-gray-500 dark:text-gray-400 text-center"
              >
                +{{ getVisitesForDate(date).length - 3 }} autres
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { computed, ref } from 'vue'

  const props = defineProps({
    selectedDate: {
      type: Date,
      required: true
    },
    visites: {
      type: Array,
      default: () => []
    }
  })

  const emit = defineEmits(['visit-click', 'date-select', 'visit-edit'])

  const selectedCalendarDate = ref(new Date(props.selectedDate))

  const daysOfWeek = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim']

  const calendarDates = computed(() => {
    const year = props.selectedDate.getFullYear()
    const month = props.selectedDate.getMonth()
    
    // First day of the month
    const firstDay = new Date(year, month, 1)
    // Last day of the month
    const lastDay = new Date(year, month + 1, 0)
    
    // Start from Monday of the week containing the first day
    const startDate = new Date(firstDay)
    const dayOfWeek = firstDay.getDay()
    const mondayOffset = dayOfWeek === 0 ? -6 : 1 - dayOfWeek
    startDate.setDate(firstDay.getDate() + mondayOffset)
    
    // Generate 42 days (6 weeks)
    const dates = []
    for (let i = 0; i < 42; i++) {
      const date = new Date(startDate)
      date.setDate(startDate.getDate() + i)
      dates.push(date)
    }
    
    return dates
  })

  function isCurrentMonth(date) {
    return date.getMonth() === props.selectedDate.getMonth()
  }

  function isToday(date) {
    const today = new Date()
    return date.toDateString() === today.toDateString()
  }

  function isSelected(date) {
    return date.toDateString() === selectedCalendarDate.value.toDateString()
  }

  function selectDate(date) {
    selectedCalendarDate.value = date
    emit('date-select', date)
  }

  function getVisitesForDate(date) {
    return props.visites.filter(visite => {
      const visiteDate = new Date(visite.date_visite_start)
      return visiteDate.toDateString() === date.toDateString()
    }).sort((a, b) => new Date(a.date_visite_start) - new Date(b.date_visite_start))
  }

  function getVisiteIndicatorClass(visite) {
    const baseClass = 'inline-block'
    
    switch (visite.statut_visite) {
      case 'programmee':
        return `${baseClass} bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300`
      case 'en_cours':
        return `${baseClass} bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300`
      case 'terminee':
        return `${baseClass} bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300`
      case 'annulee':
        return `${baseClass} bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300`
      default:
        return `${baseClass} bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300`
    }
  }
</script>

<style scoped>
  .calendar-grid {
    max-height: calc(100vh - 200px);
    overflow: auto;
  }

  .calendar-cell {
    transition: all 0.2s ease;
  }

  .calendar-cell:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }

  .dark .calendar-cell:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
  }
</style>



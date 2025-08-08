<template>
  <div class="day-view-container h-full flex flex-col">
    <div class="day-header p-4 border-b border-gray-200 dark:border-gray-700">
      <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
        {{ selectedDate.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) }}
      </h3>
    </div>
    
    <div class="day-content flex-1 overflow-auto">
      <div class="grid grid-cols-1 gap-2 p-4">
        <div
          v-for="visite in dayVisites"
          :key="visite.id_visite"
          class="visite-card bg-white dark:bg-gray-700 rounded-lg p-4 shadow-md border-l-4"
          :class="getVisiteColorClass(visite)"
          @click="$emit('visit-click', visite)"
        >
          <div class="flex items-center justify-between mb-2">
            <h4 class="font-medium text-gray-800 dark:text-white">{{ visite.motif_visite }}</h4>
            <button
              @click.stop="$emit('visit-edit', visite)"
              class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
            >
              ✏️
            </button>
          </div>
          <p class="text-sm text-gray-600 dark:text-gray-300">
            {{ formatVisiteTime(visite) }}
          </p>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            {{ visite.email_visiteur }}
          </p>
        </div>
        
        <div v-if="dayVisites.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
          Aucune visite prévue pour cette journée
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { computed } from 'vue'

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

  const emit = defineEmits(['visit-click', 'cell-click', 'visit-edit'])

  const dayVisites = computed(() => {
    return props.visites.filter(visite => {
      const visiteDate = new Date(visite.date_visite_start)
      return visiteDate.toDateString() === props.selectedDate.toDateString()
    }).sort((a, b) => new Date(a.date_visite_start) - new Date(b.date_visite_start))
  })

  function getVisiteColorClass(visite) {
    switch (visite.statut_visite) {
      case 'programmee':
        return 'border-blue-500'
      case 'en_cours':
        return 'border-green-500'
      case 'terminee':
        return 'border-gray-500'
      case 'annulee':
        return 'border-red-500'
      default:
        return 'border-yellow-500'
    }
  }

  function formatVisiteTime(visite) {
    const start = new Date(visite.date_visite_start)
    const end = new Date(visite.date_visite_end)
    return `${start.getHours().toString().padStart(2, '0')}:${start.getMinutes().toString().padStart(2, '0')} - ${end.getHours().toString().padStart(2, '0')}:${end.getMinutes().toString().padStart(2, '0')}`
  }
</script>

<style scoped>
  .visite-card {
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .visite-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .dark .visite-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
  }
</style>



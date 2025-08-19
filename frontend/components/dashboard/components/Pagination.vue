<template>
  <div class="flex items-center justify-between">
    <div class="flex-1 flex justify-between sm:hidden">
      <!-- Mobile pagination -->
      <button
        @click="goToPrevious"
        :disabled="currentPage <= 1"
        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        {{ t('common.previous') }}
      </button>
      <button
        @click="goToNext"
        :disabled="currentPage >= totalPages"
        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        {{ t('common.next') }}
      </button>
    </div>
    
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700">
          {{ t('pagination.showing') }}
          <span class="font-medium">{{ from }}</span>
          {{ t('pagination.to') }}
          <span class="font-medium">{{ to }}</span>
          {{ t('pagination.of') }}
          <span class="font-medium">{{ totalItems }}</span>
          {{ t('pagination.results') }}
        </p>
      </div>
      
      <div>
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
          <!-- Previous button -->
          <button
            @click="goToPrevious"
            :disabled="currentPage <= 1"
            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <ChevronLeftIcon class="h-5 w-5" />
          </button>
          
          <!-- Page numbers -->
          <template v-for="page in visiblePages" :key="page">
            <button
              v-if="page !== '...'"
              @click="goToPage(page)"
              :class="pageButtonClasses(page)"
              class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
            >
              {{ page }}
            </button>
            <span
              v-else
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
            >
              ...
            </span>
          </template>
          
          <!-- Next button -->
          <button
            @click="goToNext"
            :disabled="currentPage >= totalPages"
            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <ChevronRightIcon class="h-5 w-5" />
          </button>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { computed } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'

  interface Props {
    currentPage: number
    totalPages: number
    totalItems: number
    perPage: number
  }

  const props = defineProps<Props>()
  const emit = defineEmits<{
    pageChanged: [page: number]
  }>()

  const { t } = useI18n()

  const from = computed(() => {
    return ((props.currentPage - 1) * props.perPage) + 1
  })

  const to = computed(() => {
    return Math.min(props.currentPage * props.perPage, props.totalItems)
  })

  const visiblePages = computed(() => {
    const pages: (number | string)[] = []
    const total = props.totalPages
    const current = props.currentPage
    
    if (total <= 7) {
      // Si 7 pages ou moins, afficher toutes les pages
      for (let i = 1; i <= total; i++) {
        pages.push(i)
      }
    } else {
      // Logique pour plus de 7 pages
      if (current <= 4) {
        // Début : 1, 2, 3, 4, 5, ..., total
        for (let i = 1; i <= 5; i++) {
          pages.push(i)
        }
        pages.push('...')
        pages.push(total)
      } else if (current >= total - 3) {
        // Fin : 1, ..., total-4, total-3, total-2, total-1, total
        pages.push(1)
        pages.push('...')
        for (let i = total - 4; i <= total; i++) {
          pages.push(i)
        }
      } else {
        // Milieu : 1, ..., current-1, current, current+1, ..., total
        pages.push(1)
        pages.push('...')
        for (let i = current - 1; i <= current + 1; i++) {
          pages.push(i)
        }
        pages.push('...')
        pages.push(total)
      }
    }
    
    return pages
  })

  const pageButtonClasses = (page: number | string) => {
    if (page === props.currentPage) {
      return 'z-10 bg-blue-50 border-blue-500 text-blue-600'
    }
    return 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
  }

  const goToPrevious = () => {
    if (props.currentPage > 1) {
      emit('pageChanged', props.currentPage - 1)
    }
  }

  const goToNext = () => {
    if (props.currentPage < props.totalPages) {
      emit('pageChanged', props.currentPage + 1)
    }
  }

  const goToPage = (page: number | string) => {
    if (typeof page === 'number' && page !== props.currentPage) {
      emit('pageChanged', page)
    }
  }
</script>



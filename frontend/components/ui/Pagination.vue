<template>
  <div class="flex items-center justify-between">
    <div class="text-sm text-gray-700">
      Affichage {{ startItem }}-{{ endItem }} sur {{ totalItems }} résultats
    </div>
    <div class="flex space-x-1">
      <button
        @click="$emit('pageChanged', currentPage - 1)"
        :disabled="currentPage <= 1"
        class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Précédent
      </button>
      
      <template v-for="page in visiblePages" :key="page">
        <button
          v-if="page !== '...'"
          @click="$emit('pageChanged', page)"
          :class="[
            'px-3 py-2 text-sm rounded-md',
            page === currentPage
              ? 'bg-blue-600 text-white'
              : 'text-gray-700 hover:bg-gray-100'
          ]"
        >
          {{ page }}
        </button>
        <span v-else class="px-3 py-2 text-sm text-gray-500">...</span>
      </template>
      
      <button
        @click="$emit('pageChanged', currentPage + 1)"
        :disabled="currentPage >= totalPages"
        class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Suivant
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { computed } from 'vue'

  interface Props {
    currentPage: number
    totalPages: number
    totalItems: number
    perPage: number
  }

  const props = defineProps<Props>()
  const emit = defineEmits(['pageChanged'])

  const startItem = computed(() => (props.currentPage - 1) * props.perPage + 1)
  const endItem = computed(() => Math.min(props.currentPage * props.perPage, props.totalItems))

  const visiblePages = computed(() => {
    const pages = []
    const maxVisible = 5
    
    if (props.totalPages <= maxVisible) {
      for (let i = 1; i <= props.totalPages; i++) {
        pages.push(i)
      }
    } else {
      // Logique de pagination intelligente
      pages.push(1)
      
      if (props.currentPage > 3) {
        pages.push('...')
      }
      
      const start = Math.max(2, props.currentPage - 1)
      const end = Math.min(props.totalPages - 1, props.currentPage + 1)
      
      for (let i = start; i <= end; i++) {
        if (i !== 1 && i !== props.totalPages) {
          pages.push(i)
        }
      }
      
      if (props.currentPage < props.totalPages - 2) {
        pages.push('...')
      }
      
      if (props.totalPages > 1) {
        pages.push(props.totalPages)
      }
    }
    
    return pages
  })
</script>



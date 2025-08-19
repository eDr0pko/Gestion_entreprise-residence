<template>
  <span :class="badgeClass">
    {{ status }}
  </span>
</template>

<script setup lang="ts">
  import { computed } from 'vue'

  interface Props {
    status: string
  }

  const props = defineProps<Props>()

  const badgeClass = computed(() => {
    const baseClass = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium'
    
    switch (props.status) {
      case 'actif':
        return `${baseClass} bg-green-100 text-green-800`
      case 'inactif':
        return `${baseClass} bg-red-100 text-red-800`
      case 'suspendu':
        return `${baseClass} bg-yellow-100 text-yellow-800`
      default:
        return `${baseClass} bg-gray-100 text-gray-800`
    }
  })
</script>



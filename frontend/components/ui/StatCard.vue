<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="text-sm font-medium text-gray-500">{{ title }}</h3>
        <div class="mt-2 flex items-baseline">
          <div v-if="loading" class="animate-pulse">
            <div class="h-8 w-16 bg-gray-200 rounded"></div>
          </div>
          <p v-else class="text-2xl font-semibold text-gray-900">{{ value }}</p>
        </div>
      </div>
      <div :class="iconClass">
        <Icon :name="iconName" class="w-6 h-6" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { computed } from 'vue'

  interface Props {
      title: string
      value: number | string
      loading?: boolean
      icon?: string
      color?: string
  }

  const props = withDefaults(defineProps<Props>(), {
      loading: false,
      icon: 'badge',
      color: 'blue'
  })

  const iconName = computed(() => {
      switch (props.icon) {
          case 'check-circle':
          return 'refresh'
          case 'x-circle':
          return 'trash'
          case 'pause-circle':
          return 'pause'
          case 'plus':
          return 'plus'
          case 'badge':
          default:
          return 'badge'
      }
      })

      const iconClass = computed(() => {
      const colorClasses: Record<string, string> = {
          blue: 'text-blue-600 bg-blue-100',
          green: 'text-green-600 bg-green-100',
          red: 'text-red-600 bg-red-100',
          yellow: 'text-yellow-600 bg-yellow-100'
      }
      const color = typeof props.color === 'string' && colorClasses[props.color] ? props.color : 'blue'
      return `flex-shrink-0 rounded-lg p-3 ${colorClasses[color]}`
  })
</script>



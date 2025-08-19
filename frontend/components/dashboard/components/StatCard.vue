<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
    <div class="flex items-center">
      <div class="flex-shrink-0">
        <div :class="iconClasses">
          <component :is="iconComponent" class="w-6 h-6" />
        </div>
      </div>
      <div class="ml-4 flex-1">
        <dt class="text-sm font-medium text-gray-500 truncate">
          {{ title }}
        </dt>
        <dd class="mt-1 text-2xl font-semibold" :class="valueClasses">
          <template v-if="loading">
            <div class="animate-pulse">
              <div class="h-8 bg-gray-200 rounded w-16"></div>
            </div>
          </template>
          <template v-else>
            {{ formattedValue }}
          </template>
        </dd>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { computed } from 'vue'
  import { 
    UserIcon,
    CheckCircleIcon,
    XCircleIcon,
    PauseCircleIcon,
    ExclamationTriangleIcon,
    ChartBarIcon
  } from '@heroicons/vue/24/outline'
  import BadgeIcon from '~/components/icons/BadgeIcon.vue'

  interface Props {
    title: string
    value: number | string
    loading?: boolean
    icon?: string
    color?: 'blue' | 'green' | 'red' | 'yellow' | 'gray'
    trend?: 'up' | 'down' | 'neutral'
  }

  const props = withDefaults(defineProps<Props>(), {
    loading: false,
    icon: 'chart',
    color: 'blue',
    trend: 'neutral'
  })

  const iconComponent = computed(() => {
    switch (props.icon) {
      case 'badge':
        return BadgeIcon
      case 'user':
        return UserIcon
      case 'check-circle':
        return CheckCircleIcon
      case 'x-circle':
        return XCircleIcon
      case 'pause-circle':
        return PauseCircleIcon
      case 'warning':
        return ExclamationTriangleIcon
      default:
        return ChartBarIcon
    }
  })

  const iconClasses = computed(() => {
    const baseClasses = 'flex items-center justify-center w-12 h-12 rounded-xl'
    
    switch (props.color) {
      case 'green':
        return `${baseClasses} bg-green-100 text-green-600`
      case 'red':
        return `${baseClasses} bg-red-100 text-red-600`
      case 'yellow':
        return `${baseClasses} bg-yellow-100 text-yellow-600`
      case 'gray':
        return `${baseClasses} bg-gray-100 text-gray-600`
      default:
        return `${baseClasses} bg-blue-100 text-blue-600`
    }
  })

  const valueClasses = computed(() => {
    switch (props.color) {
      case 'green':
        return 'text-green-900'
      case 'red':
        return 'text-red-900'
      case 'yellow':
        return 'text-yellow-900'
      case 'gray':
        return 'text-gray-900'
      default:
        return 'text-blue-900'
    }
  })

  const formattedValue = computed(() => {
    if (typeof props.value === 'number') {
      return props.value.toLocaleString()
    }
    return props.value
  })
</script>



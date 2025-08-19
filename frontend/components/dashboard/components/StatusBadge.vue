<template>
  <span 
    :class="badgeClasses"
    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
  >
    <span 
      :class="dotClasses"
      class="w-1.5 h-1.5 mr-1.5 rounded-full"
    ></span>
    {{ statusLabel }}
  </span>
</template>

<script setup lang="ts">
  import { computed } from 'vue'
  import { useI18n } from 'vue-i18n'

  interface Props {
    status: string
  }

  const props = defineProps<Props>()
  const { t } = useI18n()

  const statusConfig = computed(() => {
    switch (props.status) {
      case 'actif':
        return {
          label: t('badges.statuses.active'),
          bgClass: 'bg-green-100',
          textClass: 'text-green-800',
          dotClass: 'bg-green-400'
        }
      case 'inactif':
        return {
          label: t('badges.statuses.inactive'),
          bgClass: 'bg-red-100',
          textClass: 'text-red-800',
          dotClass: 'bg-red-400'
        }
      case 'suspendu':
        return {
          label: t('badges.statuses.suspended'),
          bgClass: 'bg-yellow-100',
          textClass: 'text-yellow-800',
          dotClass: 'bg-yellow-400'
        }
      case 'nouveau':
        return {
          label: t('badges.statuses.new'),
          bgClass: 'bg-blue-100',
          textClass: 'text-blue-800',
          dotClass: 'bg-blue-400'
        }
      default:
        return {
          label: props.status,
          bgClass: 'bg-gray-100',
          textClass: 'text-gray-800',
          dotClass: 'bg-gray-400'
        }
    }
  })

  const badgeClasses = computed(() => 
    `${statusConfig.value.bgClass} ${statusConfig.value.textClass}`
  )

  const dotClasses = computed(() => statusConfig.value.dotClass)

  const statusLabel = computed(() => statusConfig.value.label)
</script>



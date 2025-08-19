<template>
  <div class="flex justify-center items-center">
    <div 
      :class="sizeClasses"
      class="animate-spin rounded-full border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]"
      role="status"
    >
      <span class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]">
        {{ t('common.loading') }}...
      </span>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { computed } from 'vue'
  import { useI18n } from 'vue-i18n'

  interface Props {
    size?: 'sm' | 'md' | 'lg'
    color?: string
  }

  const props = withDefaults(defineProps<Props>(), {
    size: 'md',
    color: 'text-blue-600'
  })

  const { t } = useI18n()

  const sizeClasses = computed(() => {
    const colorClass = props.color
    
    switch (props.size) {
      case 'sm':
        return `h-4 w-4 border-2 ${colorClass}`
      case 'lg':
        return `h-12 w-12 border-4 ${colorClass}`
      default:
        return `h-8 w-8 border-3 ${colorClass}`
    }
  })
</script>



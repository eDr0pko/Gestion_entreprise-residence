<template>
  <button
    @click="toggleTheme"
    :aria-label="t('theme.toggle')"
    :class="[
      'flex items-center justify-center transition-all duration-200 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800',
      getVariantClasses()
    ]"
  >
    <Icon 
      :name="isDark ? 'sun' : 'moon'" 
      :class="getIconClasses()" 
    />
    
    <span 
      v-if="showText" 
      :class="getTextClasses()"
    >
      {{ isDark ? t('theme.light') : t('theme.dark') }}
    </span>
  </button>
</template>

<script setup lang="ts">
interface Props {
  variant?: 'default' | 'small' | 'large'
  showText?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'default',
  showText: true
})

const { t } = useI18n()
const { toggleTheme, isDark } = useTheme()

const getVariantClasses = () => {
  const baseClasses = 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'
  
  switch (props.variant) {
    case 'small':
      return `${baseClasses} p-1.5`
    case 'large':
      return `${baseClasses} px-6 py-3`
    default:
      return `${baseClasses} px-4 py-2`
  }
}

const getIconClasses = () => {
  const baseClasses = 'text-gray-600 dark:text-gray-300'
  
  switch (props.variant) {
    case 'small':
      return `${baseClasses} w-4 h-4`
    case 'large':
      return `${baseClasses} w-6 h-6`
    default:
      return `${baseClasses} w-5 h-5`
  }
}

const getTextClasses = () => {
  const baseClasses = 'text-gray-700 dark:text-gray-200 font-medium'
  
  switch (props.variant) {
    case 'small':
      return `${baseClasses} ml-1 text-xs`
    case 'large':
      return `${baseClasses} ml-3 text-base`
    default:
      return `${baseClasses} ml-2 text-sm`
  }
}
</script>

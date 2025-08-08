<template>
  <Teleport to="body">
    <transition-group
      name="toast"
      tag="div"
      class="fixed top-4 right-4 z-[9999] space-y-2"
    >
      <div
        v-for="notification in notifications"
        :key="notification.id"
        class="bg-white dark:bg-gray-800 border-l-4 rounded-lg shadow-lg p-4 min-w-80 max-w-sm animate-slideIn"
        :class="getBorderClass(notification.type)"
      >
        <div class="flex items-start gap-3">
          <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center">
            <span class="text-lg">{{ getIcon(notification.type) }}</span>
          </div>
          
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
              <h4 class="text-sm font-semibold text-gray-800 dark:text-white">
                {{ notification.title }}
              </h4>
              <button
                @click="removeNotification(notification.id)"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
              >
                <span class="sr-only">Fermer</span>
                <span class="text-lg">×</span>
              </button>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
              {{ notification.message }}
            </p>
          </div>
        </div>

        <!-- Progress bar for auto-dismiss -->
        <div v-if="notification.autoDismiss" class="mt-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1">
          <div
            class="h-1 rounded-full transition-all duration-100 ease-linear"
            :class="getProgressClass(notification.type)"
            :style="{ width: `${notification.progress}%` }"
          ></div>
        </div>
      </div>
    </transition-group>
  </Teleport>
</template>

<script setup>
  import { ref, onMounted, onUnmounted } from 'vue'

  const notifications = ref([])
  let notificationId = 0
  let intervals = new Map()

  const props = defineProps({
    duration: {
      type: Number,
      default: 5000
    }
  })

  function addNotification(notification) {
    const id = ++notificationId
    const newNotification = {
      id,
      progress: 100,
      autoDismiss: notification.autoDismiss !== false,
      ...notification
    }
    
    notifications.value.push(newNotification)
    
    if (newNotification.autoDismiss) {
      startProgressTimer(newNotification)
    }
    
    return id
  }

  function startProgressTimer(notification) {
    const startTime = Date.now()
    const duration = notification.duration || props.duration
    
    const interval = setInterval(() => {
      const elapsed = Date.now() - startTime
      const progress = Math.max(0, 100 - (elapsed / duration) * 100)
      
      notification.progress = progress
      
      if (progress <= 0) {
        removeNotification(notification.id)
      }
    }, 50)
    
    intervals.set(notification.id, interval)
  }

  function removeNotification(id) {
    const index = notifications.value.findIndex(n => n.id === id)
    if (index > -1) {
      notifications.value.splice(index, 1)
    }
    
    if (intervals.has(id)) {
      clearInterval(intervals.get(id))
      intervals.delete(id)
    }
  }

  function getBorderClass(type) {
    switch (type) {
      case 'success': return 'border-green-500'
      case 'error': return 'border-red-500'
      case 'warning': return 'border-yellow-500'
      case 'info': return 'border-blue-500'
      default: return 'border-gray-500'
    }
  }

  function getProgressClass(type) {
    switch (type) {
      case 'success': return 'bg-green-500'
      case 'error': return 'bg-red-500'
      case 'warning': return 'bg-yellow-500'
      case 'info': return 'bg-blue-500'
      default: return 'bg-gray-500'
    }
  }

  function getIcon(type) {
    switch (type) {
      case 'success': return '✅'
      case 'error': return '❌'
      case 'warning': return '⚠️'
      case 'info': return 'ℹ️'
      default: return '📢'
    }
  }

  // API exposée
  defineExpose({
    success(title, message, options = {}) {
      return addNotification({
        type: 'success',
        title,
        message,
        ...options
      })
    },
    
    error(title, message, options = {}) {
      return addNotification({
        type: 'error',
        title,
        message,
        autoDismiss: false, // Les erreurs ne se ferment pas automatiquement
        ...options
      })
    },
    
    warning(title, message, options = {}) {
      return addNotification({
        type: 'warning',
        title,
        message,
        ...options
      })
    },
    
    info(title, message, options = {}) {
      return addNotification({
        type: 'info',
        title,
        message,
        ...options
      })
    },
    
    custom(notification) {
      return addNotification(notification)
    },
    
    clear() {
      intervals.forEach(interval => clearInterval(interval))
      intervals.clear()
      notifications.value = []
    },
    
    remove: removeNotification
  })

  onUnmounted(() => {
    intervals.forEach(interval => clearInterval(interval))
    intervals.clear()
  })
</script>

<style scoped>
  @keyframes slideIn {
    from {
      opacity: 0;
      transform: translateX(100%);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  .animate-slideIn {
    animation: slideIn 0.3s ease-out;
  }

  .toast-enter-active {
    transition: all 0.3s ease-out;
  }

  .toast-leave-active {
    transition: all 0.3s ease-in;
  }

  .toast-enter-from {
    opacity: 0;
    transform: translateX(100%);
  }

  .toast-leave-to {
    opacity: 0;
    transform: translateX(100%);
  }

  .toast-move {
    transition: transform 0.3s ease;
  }
</style>



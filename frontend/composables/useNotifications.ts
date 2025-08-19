import { ref } from 'vue'

let toastInstance: any = null

export function useNotifications() {
  const setToastInstance = (instance: any) => {
    toastInstance = instance
  }
  
  const showNotification = (message: string, type: 'success' | 'error' | 'warning' | 'info' = 'info') => {
    if (toastInstance && typeof toastInstance.show === 'function') {
      toastInstance.show(message, type)
    } else {
      // Fallback to console or simple alert
      console.log(`[${type}] ${message}`)
    }
  }
  
  const showSuccess = (message: string) => showNotification(message, 'success')
  const showError = (message: string) => showNotification(message, 'error')
  const showWarning = (message: string) => showNotification(message, 'warning')
  const showInfo = (message: string) => showNotification(message, 'info')
  
  return {
    setToastInstance,
    showNotification,
    showSuccess,
    showError,
    showWarning,
    showInfo
  }
}

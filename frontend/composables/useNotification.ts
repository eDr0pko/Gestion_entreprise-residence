import { useNotifications } from './useNotifications'

export function useNotification() {
  const { showNotification: showToast } = useNotifications()
  
  const showNotification = (
    message: string, 
    type: 'success' | 'error' | 'warning' | 'info' = 'info'
  ) => {
    showToast(message, type)
  }
  
  return {
    showNotification
  }
}

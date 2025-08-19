import { useToast } from '~/composables/useToast'

export default defineNuxtPlugin(() => {
  const toast = useToast()

  return {
    provide: {
      toast: {
        success: toast.success,
        error: toast.error,
        warning: toast.warning,
        info: toast.info
      }
    }
  }
})




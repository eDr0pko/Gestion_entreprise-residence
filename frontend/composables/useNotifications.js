import { ref } from 'vue'

// Instance globale de notifications
const toastInstance = ref(null)

export const useNotifications = () => {
  const setToastInstance = (instance) => {
    toastInstance.value = instance
  }

  const notify = {
    success(title, message, options = {}) {
      if (toastInstance.value) {
        return toastInstance.value.success(title, message, options)
      }
      console.log(`✅ ${title}: ${message}`)
    },

    error(title, message, options = {}) {
      if (toastInstance.value) {
        return toastInstance.value.error(title, message, options)
      }
      console.error(`❌ ${title}: ${message}`)
    },

    warning(title, message, options = {}) {
      if (toastInstance.value) {
        return toastInstance.value.warning(title, message, options)
      }
      console.warn(`⚠️ ${title}: ${message}`)
    },

    info(title, message, options = {}) {
      if (toastInstance.value) {
        return toastInstance.value.info(title, message, options)
      }
      console.info(`ℹ️ ${title}: ${message}`)
    },

    custom(notification) {
      if (toastInstance.value) {
        return toastInstance.value.custom(notification)
      }
      console.log(`📢 ${notification.title}: ${notification.message}`)
    },

    clear() {
      if (toastInstance.value) {
        toastInstance.value.clear()
      }
    },

    remove(id) {
      if (toastInstance.value) {
        toastInstance.value.remove(id)
      }
    }
  }

  return {
    notify,
    setToastInstance
  }
}

// Helpers pour les messages courants
export const planningNotifications = {
  visitCreated(visiteur) {
    const { notify } = useNotifications()
    notify.success(
      'Visite créée',
      `La visite pour ${visiteur} a été programmée avec succès`
    )
  },

  visitUpdated(visiteur) {
    const { notify } = useNotifications()
    notify.success(
      'Visite modifiée',
      `La visite pour ${visiteur} a été mise à jour`
    )
  },

  visitDeleted() {
    const { notify } = useNotifications()
    notify.success(
      'Visite supprimée',
      'La visite a été supprimée avec succès'
    )
  },

  visitError(action, error) {
    const { notify } = useNotifications()
    notify.error(
      `Erreur lors de ${action}`,
      error?.message || 'Une erreur inattendue s\'est produite'
    )
  },

  loadingError() {
    const { notify } = useNotifications()
    notify.error(
      'Erreur de chargement',
      'Impossible de charger les données du planning'
    )
  },

  networkError() {
    const { notify } = useNotifications()
    notify.error(
      'Erreur réseau',
      'Vérifiez votre connexion internet',
      { autoDismiss: false }
    )
  },

  validationError(field) {
    const { notify } = useNotifications()
    notify.warning(
      'Données invalides',
      `Le champ "${field}" est requis ou invalide`
    )
  },

  syncSuccess() {
    const { notify } = useNotifications()
    notify.info(
      'Synchronisation',
      'Les données ont été synchronisées'
    )
  }
}

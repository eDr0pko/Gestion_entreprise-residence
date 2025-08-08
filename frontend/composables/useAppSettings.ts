import { ref } from 'vue'
import { useFetch } from '#app'

export function useAppSettings() {
	const settings = ref({
		appName: '',
		logoUrl: '',
	})
	const loading = ref(false)
	const error = ref('')


			type AppSettings = { appName: string; logoUrl: string }

			async function fetchSettings() {
				loading.value = true
				error.value = ''
				try {
					const { data, error: fetchError } = await useFetch<AppSettings>('/api/app-settings')
					if (fetchError.value) throw fetchError.value
					if (data.value) {
						settings.value = data.value as AppSettings
					}
				} catch (e: any) {
					error.value = e.message || 'Erreur lors du chargement des paramètres.'
				} finally {
					loading.value = false
				}
			}

			async function updateSettings(newSettings: AppSettings) {
				loading.value = true
				error.value = ''
				try {
					const { data, error: updateError } = await useFetch<AppSettings>('/api/app-settings', {
						method: 'POST',
						body: newSettings,
					})
					if (updateError.value) throw updateError.value
					if (data.value) {
						settings.value = data.value as AppSettings
					} else {
						settings.value = newSettings
					}
				} catch (e: any) {
					error.value = e.message || 'Erreur lors de la mise à jour.'
				} finally {
					loading.value = false
				}
			}

	return {
		settings,
		loading,
		error,
		fetchSettings,
		updateSettings,
	}
}

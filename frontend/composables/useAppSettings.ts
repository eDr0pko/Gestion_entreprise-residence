import { ref } from 'vue'

export function useAppSettings() {
	type AppSettings = {
		appName: string
		logoUrl: string | null
		primary_color?: string
		secondary_color?: string
		accent_color?: string
		background_color?: string
		app_name?: string
		company_name?: string
		app_logo?: string
		app_tagline?: string
		welcome_title?: string
		welcome_message?: string
		login_title?: string
		login_subtitle?: string
		register_title?: string
		register_subtitle?: string
		enable_registration?: boolean
		enable_dark_mode?: boolean
		show_footer?: boolean
		contact_email?: string
		contact_phone?: string
		contact_address?: string
		footer_text?: string
	}
	const settings = ref<AppSettings>({
		appName: '',
		logoUrl: null,
	})
	const loading = ref(false)
	const error = ref('')


			// type déjà défini plus haut

	async function fetchSettings() {
		loading.value = true
		error.value = ''
		try {
			const data = await $fetch('/api/settings', {
				baseURL: 'http://localhost:8000'
			})
			if (data) {
				const apiData = data as any
				settings.value.appName = apiData.app_name || 'Gestion Résidence'
				settings.value.logoUrl = apiData.logo_url || null
				settings.value.primary_color = apiData.primary_color
				settings.value.secondary_color = apiData.secondary_color
				settings.value.accent_color = apiData.accent_color
				settings.value.background_color = apiData.background_color
				settings.value.app_name = apiData.app_name
				settings.value.company_name = apiData.company_name
				settings.value.app_tagline = apiData.app_tagline
				settings.value.welcome_title = apiData.welcome_title
				settings.value.welcome_message = apiData.welcome_message
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
			const data = await $fetch('/api/settings', {
				method: 'POST',
				baseURL: 'http://localhost:8000',
				body: {
					app_name: newSettings.appName || newSettings.app_name,
					logo_url: newSettings.logoUrl,
					company_name: newSettings.company_name,
					app_tagline: newSettings.app_tagline,
					primary_color: newSettings.primary_color,
					secondary_color: newSettings.secondary_color,
					accent_color: newSettings.accent_color,
					background_color: newSettings.background_color,
					welcome_title: newSettings.welcome_title,
					welcome_message: newSettings.welcome_message
				},
			})
			if (data) {
				// Recharger les paramètres après mise à jour
				await fetchSettings()
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



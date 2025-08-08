import { ref } from 'vue'
import { useFetch } from '#app'

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
					const { data, error: fetchError } = await useFetch('/api/app-settings')
					if (fetchError.value) throw fetchError.value
							if (data.value) {
								const apiData = data.value as any
								settings.value.appName = apiData.app_name
								settings.value.logoUrl = apiData.logo_url
								settings.value.primary_color = apiData.primary_color
								settings.value.secondary_color = apiData.secondary_color
								settings.value.accent_color = apiData.accent_color
								settings.value.background_color = apiData.background_color
								settings.value.app_name = apiData.app_name
								settings.value.company_name = apiData.company_name
								settings.value.app_logo = apiData.app_logo
								settings.value.app_tagline = apiData.app_tagline
								settings.value.welcome_title = apiData.welcome_title
								settings.value.welcome_message = apiData.welcome_message
								settings.value.login_title = apiData.login_title
								settings.value.login_subtitle = apiData.login_subtitle
								settings.value.register_title = apiData.register_title
								settings.value.register_subtitle = apiData.register_subtitle
								settings.value.enable_registration = apiData.enable_registration
								settings.value.enable_dark_mode = apiData.enable_dark_mode
								settings.value.show_footer = apiData.show_footer
								settings.value.contact_email = apiData.contact_email
								settings.value.contact_phone = apiData.contact_phone
								settings.value.contact_address = apiData.contact_address
								settings.value.footer_text = apiData.footer_text
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
						const { data, error: updateError } = await useFetch('/api/app-settings', {
							method: 'PUT',
							body: {
								appName: newSettings.appName,
								logoUrl: newSettings.logoUrl,
							},
						})
						if (updateError.value) throw updateError.value
								if (data.value) {
									const apiData = data.value as any
									settings.value.appName = apiData.app_name
									settings.value.logoUrl = apiData.logo_url
									settings.value.primary_color = apiData.primary_color
									settings.value.secondary_color = apiData.secondary_color
									settings.value.accent_color = apiData.accent_color
									settings.value.background_color = apiData.background_color
									settings.value.app_name = apiData.app_name
									settings.value.company_name = apiData.company_name
									settings.value.app_logo = apiData.app_logo
									settings.value.app_tagline = apiData.app_tagline
									settings.value.welcome_title = apiData.welcome_title
									settings.value.welcome_message = apiData.welcome_message
									settings.value.login_title = apiData.login_title
									settings.value.login_subtitle = apiData.login_subtitle
									settings.value.register_title = apiData.register_title
									settings.value.register_subtitle = apiData.register_subtitle
									settings.value.enable_registration = apiData.enable_registration
									settings.value.enable_dark_mode = apiData.enable_dark_mode
									settings.value.show_footer = apiData.show_footer
									settings.value.contact_email = apiData.contact_email
									settings.value.contact_phone = apiData.contact_phone
									settings.value.contact_address = apiData.contact_address
									settings.value.footer_text = apiData.footer_text
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



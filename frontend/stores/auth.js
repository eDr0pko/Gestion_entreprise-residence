import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null,
    isAuthenticated: false,
    isLoading: false
  }),

  getters: {
    isLoggedIn: (state) => state.isAuthenticated && !!state.token,
    userRole: (state) => state.user?.role || null
  },

  actions: {
    // Initialiser depuis localStorage au démarrage
    initAuth() {
      if (process.client) {
        const token = localStorage.getItem('auth_token')
        const user = localStorage.getItem('user')
        
        if (token && user) {
          try {
            this.token = token
            this.user = JSON.parse(user)
            this.isAuthenticated = true
            console.log('Auth initialisé depuis localStorage:', { token: !!token, user: this.user })
          } catch (error) {
            console.error('Erreur lors de l\'initialisation auth:', error)
            this.clearAuth()
          }
        }
      }
    },

    // Connexion
    async login(email, password) {
      try {
        this.isLoading = true
        const config = useRuntimeConfig()
        
        console.log('Tentative de connexion:', { email, apiBase: config.public.apiBase })
        
        const response = await $fetch(`${config.public.apiBase}/login`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: {
            email,
            password
          }
        })

        console.log('Réponse de connexion:', response)

        if (response.success && response.access_token) {
          this.token = response.access_token
          this.user = response.user
          this.isAuthenticated = true
          
          // Sauvegarder dans localStorage
          if (process.client) {
            localStorage.setItem('auth_token', response.access_token)
            localStorage.setItem('user', JSON.stringify(response.user))
          }
          
          console.log('Connexion réussie:', { 
            token: !!this.token, 
            user: this.user,
            authenticated: this.isAuthenticated 
          })
          
          return { success: true, user: this.user }
        } else {
          throw new Error(response.message || 'Erreur de connexion')
        }
      } catch (error) {
        console.error('Erreur de connexion dans le store:', error)
        this.clearAuth()
        throw error
      } finally {
        this.isLoading = false
      }
    },

    // Déconnexion
    async logout() {
      try {
        if (this.token) {
          const config = useRuntimeConfig()
          await $fetch(`${config.public.apiBase}/logout`, {
            method: 'POST',
            headers: {
              'Authorization': `Bearer ${this.token}`,
              'Accept': 'application/json',
            }
          })
        }
      } catch (error) {
        console.error('Erreur lors de la déconnexion:', error)
      } finally {
        this.clearAuth()
        // Rediriger vers la page d'accueil après déconnexion
        if (process.client) {
          await navigateTo('/')
        }
      }
    },

    // Vérifier l'authentification
    async checkAuth() {
      if (!this.token) {
        this.initAuth()
      }
      
      if (!this.token) {
        return false
      }

      try {
        const config = useRuntimeConfig()
        const response = await $fetch(`${config.public.apiBase}/check`, {
          headers: {
            'Authorization': `Bearer ${this.token}`,
            'Accept': 'application/json',
          }
        })
        
        if (response.success) {
          this.isAuthenticated = true
          return true
        } else {
          this.clearAuth()
          return false
        }
      } catch (error) {
        console.error('Erreur lors de la vérification auth:', error)
        this.clearAuth()
        return false
      }
    },

    // Mettre à jour les informations utilisateur
    updateUser(userData) {
      this.user = { ...this.user, ...userData }
      
      if (process.client) {
        localStorage.setItem('user', JSON.stringify(this.user))
      }
    },

    // Définir l'authentification (pour inscription/connexion directe)
    setAuth(token, user) {
      this.token = token
      this.user = user
      this.isAuthenticated = true
      
      // Sauvegarder dans localStorage
      if (process.client) {
        localStorage.setItem('auth_token', token)
        localStorage.setItem('user', JSON.stringify(user))
      }
      
      console.log('Authentification définie:', { 
        token: !!this.token, 
        user: this.user,
        authenticated: this.isAuthenticated 
      })
    },

    // Nettoyer l'authentification
    clearAuth() {
      this.user = null
      this.token = null
      this.isAuthenticated = false
      
      if (process.client) {
        localStorage.removeItem('auth_token')
        localStorage.removeItem('user')
      }
    }
  }
})
// Types pour le système de messagerie
export interface Conversation {
  id_groupe_message: number
  nom_groupe: string
  date_creation: string
  derniere_activite?: string
  messages_non_lus: number
  dernier_contenu?: string
  dernier_auteur?: string
  nombre_membres?: number
}

export interface Message {
  id_message: number
  contenu_message?: string
  date_envoi: string
  email_auteur: string
  auteur_nom: string
  is_current_user: boolean
  statut_lecture?: string
  fichiers?: FichierMessage[]
  reactions?: Record<string, ReactionData>
  reply_to?: {
    id_message: number
    auteur_nom: string
    contenu_message?: string
  }
}

export interface ReactionData {
  count: number
  users: Array<{
    email: string
    nom: string
  }>
}

export interface FichierMessage {
  id_fichier: number
  nom_original: string
  type_fichier: string
  taille_fichier: number
}

export interface ApiResponse {
  success: boolean
  error?: string
  message?: string | Message // Peut être un string d'erreur ou un objet Message
  conversations?: Conversation[]
  messages?: Message[]
  conversation?: Conversation
}

// Types pour l'authentification
export interface User {
  id_personne: number
  email: string
  nom: string
  prenom: string
  numero_telephone: string
  photo_profil?: string
  role: string
}

// Types pour les membres des groupes
export interface Member {
  email: string
  nom: string
  prenom: string
  nom_complet: string
  role: string
  is_current_user: boolean
  date_adhesion: string
}

export interface AuthResponse {
  success: boolean
  message?: string
  token?: string
  user?: User
}

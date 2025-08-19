// Composable utilitaire pour normaliser la génération d'URL d'avatar
// Centralise la logique afin d'éviter les 404 dues à des chemins divergents
// Amélioré avec état réactif optionnel et gestion d'erreur

import { computed, ref } from 'vue'

/**
 * useAvatarUrl
 * - Fournit build(photo) pour générer une URL fiable
 * - url: ref calculé si vous lui passez une valeur initiale (set())
 * - markError() pour invalider l'affichage après une erreur <img @error>
 */
export function useAvatarUrl(initial?: string | null) {
  const config = useRuntimeConfig()
  const apiBase: string = config.public.apiBase.replace(/\/$/, '') // http://localhost:8000/api
  const rootBase = apiBase.replace(/\/api$/, '') // http://localhost:8000

  const source = ref<string | null | undefined>(initial)
  const errored = ref(false)

  function build(photo?: string | null): string | null {
    if (!photo) return null
    let value = photo.trim()
    if (!value) return null

    // Déjà une URL complète
    if (/^https?:\/\//i.test(value)) return value

    // Nettoyage des slashes initiaux
    value = value.replace(/^\/+/, '')

    // Normaliser certains préfixes historiques
    if (value.startsWith('public/avatars/')) value = value.replace('public/', '')

    // Nom de fichier simple sans dossier
    if (!value.includes('/') && !value.startsWith('avatars/')) {
      value = 'avatars/' + value
    }

    // Cas: storage/avatars/... => URL publique directe
    if (value.startsWith('storage/avatars/')) {
      return `${rootBase}/${value}`
    }

    // Cas: avatars/<file> => route API (pas besoin du symlink storage)
    if (value.startsWith('avatars/')) {
      const filename = value.split('/').pop() || value
      return `${apiBase}/avatars/${filename}`
    }

    // Fallback: route API /api/avatars/{filename}
    const filename = value.split('/').pop() || value
    return `${apiBase}/avatars/${filename}`
  }

  const url = computed(() => errored.value ? null : build(source.value || null))

  function set(photo?: string | null) {
    source.value = photo || null
    errored.value = false
  }

  function markError() { errored.value = true }

  return { build, url, set, markError, errored }
}

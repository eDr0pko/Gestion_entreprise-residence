/**
 * Composable pour gérer les URLs de logos et assets
 */
export const useAssets = () => {
  const BACKEND_URL = 'http://localhost:8000'
  
  /**
   * Construit l'URL complète pour un logo
   * @param logoUrl - URL du logo (peut être relative ou absolue)
   * @returns URL complète du logo
   */
  const getLogoUrl = (logoUrl: string | null): string => {
    if (!logoUrl) return ''
    
    // Si c'est déjà une URL complète ou commence par http, la retourner telle quelle
    if (logoUrl.startsWith('http') || logoUrl.startsWith('data:')) {
      return logoUrl
    }
    
    // Si c'est un chemin relatif, construire l'URL complète vers le backend
    if (logoUrl.startsWith('/')) {
      return `${BACKEND_URL}${logoUrl}`
    }
    
    // Sinon, ajouter le préfixe /logos/ si nécessaire
    return `${BACKEND_URL}/logos/${logoUrl}`
  }
  
  /**
   * Construit l'URL complète pour n'importe quel asset du backend
   * @param assetPath - Chemin de l'asset
   * @returns URL complète de l'asset
   */
  const getAssetUrl = (assetPath: string): string => {
    if (!assetPath) return ''
    
    if (assetPath.startsWith('http')) {
      return assetPath
    }
    
    if (assetPath.startsWith('/')) {
      return `${BACKEND_URL}${assetPath}`
    }
    
    return `${BACKEND_URL}/${assetPath}`
  }
  
  return {
    getLogoUrl,
    getAssetUrl,
    BACKEND_URL
  }
}

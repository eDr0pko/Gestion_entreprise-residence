<template>
    <!--
    =====================================================================
    | Template principal du footer
    |---------------------------------------------------------------------
    | Structure :
    | - Effet de glassmorphisme
    | - Copyright et lien
    =====================================================================
    -->
    <footer class="relative bg-gradient-to-r from-white via-gray-50 to-white border-t border-gray-200 py-6 mt-auto">
        
        <!-- Effet de glassmorphisme subtil -->
        <div class="absolute inset-0 bg-gradient-to-r from-[#0097b2]/5 via-transparent to-[#00b4d8]/5 pointer-events-none"></div>
        
        <div class="relative container mx-auto px-4 text-center">
            <p class="text-sm text-gray-600 leading-relaxed">
                Copyright © 2025 
                <a 
                    href="https://neostart.tech/" 
                    target="_blank" 
                    rel="noopener noreferrer"
                    class="text-[#0097b2] hover:text-[#00b4d8] transition-all duration-300 font-medium hover:underline decoration-2 underline-offset-2"
                >
                    Neo Start Technology
                </a> 
                - Tous droits réservés.
            </p>
        </div>
    </footer>
</template>

<script setup lang="ts">
    /*
    ========================================================================
    | Script principal du composant AppFooter
    |-----------------------------------------------------------------------
    | (Aucune logique JS, uniquement structure statique)
    ========================================================================
    */
</script>

<style scoped>
    /*
    ==========================================================================
    | Styles spécifiques au composant AppFooter
    |--------------------------------------------------------------------------
    | (Aucun style spécifique, utilisation des classes utilitaires Tailwind CSS)
    ==========================================================================
    */
</style>



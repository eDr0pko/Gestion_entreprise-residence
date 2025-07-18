<template>
  <div class="p-6 max-w-xl mx-auto">
    <h2 class="text-2xl font-bold mb-4">Paramètres de personnalisation</h2>
    <form class="space-y-6" @submit.prevent="saveSettings">
      <div>
        <label class="block font-semibold mb-1">Logo du site</label>
        <input type="file" accept="image/*" @change="handleLogo" />
        <div v-if="previewLogo" class="mt-2">
          <img :src="previewLogo" alt="Aperçu logo" class="h-16 rounded shadow" />
        </div>
      </div>
      <div>
        <label class="block font-semibold mb-1">Texte d'accueil</label>
        <input v-model="welcomeText" type="text" class="w-full border rounded px-3 py-2" placeholder="Bienvenue sur le site..." />
     </div>
      <div>
        <label class="block font-semibold mb-1">Couleur principale</label>
        <input v-model="mainColor" type="color" class="w-16 h-10 p-0 border rounded" />
      </div>
      <div class="flex gap-2 justify-end">
        <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-4 py-2 rounded shadow">Enregistrer</button>
      </div>
      <div v-if="success" class="text-green-600 text-sm">Paramètres enregistrés !</div>
      <div v-if="error" class="text-red-500 text-sm">{{ error }}</div>
    </form>
  </div>
</template>

<script setup lang="ts">
  import { ref } from 'vue'

  const welcomeText = ref('')
  const mainColor = ref('#ff6600')
  const logoFile = ref<File|null>(null)
  const previewLogo = ref<string|null>(null)
  const success = ref(false)
  const error = ref('')

  function handleLogo(e: Event) {
    const files = (e.target as HTMLInputElement).files
    if (files && files[0]) {
      logoFile.value = files[0]
      const reader = new FileReader()
      reader.onload = ev => {
        previewLogo.value = ev.target?.result as string
      }
      reader.readAsDataURL(files[0])
    }
  }

  async function saveSettings() {
    error.value = ''
    success.value = false
    try {
      // TODO: Appel API pour sauvegarder les paramètres (logo, texte, couleur)
      // Exemple d'envoi du logo :
      // if (logoFile.value) {
      //   const formData = new FormData()
      //   formData.append('logo', logoFile.value)
      //   await $fetch('/api/admin/settings/logo', { method: 'POST', body: formData })
      // }
      // await $fetch('/api/admin/settings', { method: 'POST', body: { welcomeText: welcomeText.value, mainColor: mainColor.value } })
      success.value = true
      setTimeout(() => { success.value = false }, 2000)
    } catch (e: any) {
      error.value = e?.data?.message || 'Erreur lors de la sauvegarde.'
    }
  }
</script>



<template>
  <div class="max-w-2xl mx-auto bg-white rounded-3xl shadow-2xl p-10 mt-10 animate-fadeInUp border border-gray-100">
    <h2 class="text-4xl font-extrabold mb-8 text-gray-900 tracking-tight">Personnalisation du site</h2>
    <form @submit.prevent="onSave" class="space-y-8">
      <div>
        <label class="block text-base font-semibold text-gray-700 mb-3">Nom du site</label>
        <input v-model="form.appName" type="text" class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white text-lg font-bold" placeholder="Nom du site" required />
      </div>
      <div>
        <label class="block text-base font-semibold text-gray-700 mb-3">Logo</label>
        <div class="relative flex flex-col items-center justify-center border-2 border-dashed border-blue-300 rounded-2xl bg-blue-50 py-8 px-4 cursor-pointer hover:bg-blue-100 transition-all duration-200" @dragover.prevent @drop.prevent="onDrop" @click="triggerFileInput">
          <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="onFileChange" />
          <div v-if="form.logoUrl" class="mb-4 flex flex-col items-center">
            <img :src="form.logoUrl.startsWith('/') ? form.logoUrl : '/logos/' + form.logoUrl" alt="Logo" class="h-20 w-20 rounded-xl object-contain border border-gray-200 shadow-lg" />
            <span class="text-gray-500 text-xs mt-2">Aperçu</span>
          </div>
          <div v-else class="flex flex-col items-center">
            <svg class="w-12 h-12 text-blue-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4a1 1 0 011-1h8a1 1 0 011 1v12m-4 4h-4a1 1 0 01-1-1v-4m5 5l5-5m-5 5l-5-5" />
            </svg>
            <span class="text-blue-500 font-semibold">Déposez votre logo ici ou cliquez pour choisir un fichier</span>
          </div>
        </div>
      </div>
      <div class="flex gap-4 justify-end mt-10">
        <button type="submit" class="px-8 py-3 rounded-2xl bg-blue-600 text-white font-bold shadow-xl hover:bg-blue-700 transition-all duration-200 text-lg">Enregistrer</button>
      </div>
      <div v-if="error" class="mt-6 text-red-500 text-base">{{ error }}</div>
      <div v-if="success" class="mt-6 text-green-600 text-base">Modifications enregistrées !</div>
    </form>
  </div>
</template>


<script setup lang="ts">
  import { ref, onMounted } from 'vue'
  import { useAppSettings } from '~/composables/useAppSettings'

  const { settings, loading, error, updateSettings, fetchSettings } = useAppSettings()
  const form = ref<{ appName: string; logoUrl: string | null }>({ appName: '', logoUrl: null })
  const success = ref(false)
  const fileInput = ref<HTMLInputElement | null>(null)

  onMounted(async () => {
    await fetchSettings()
    form.value.appName = settings.value.appName
    form.value.logoUrl = settings.value.logoUrl
  })

  function triggerFileInput() {
    fileInput.value?.click()
  }

  async function onFileChange(e: Event) {
    const files = (e.target as HTMLInputElement).files
    if (!files || !files[0]) return
    await uploadLogo(files[0])
  }

  async function onDrop(e: DragEvent) {
    const files = e.dataTransfer?.files
    if (!files || !files[0]) return
    await uploadLogo(files[0])
  }

  async function uploadLogo(file: File) {
    error.value = ''
    const formData = new FormData()
    formData.append('logo', file)
    try {
      const res = await fetch('/api/upload-logo', {
        method: 'POST',
        body: formData,
      })
      const data = await res.json()
      if (data.url) {
        form.value.logoUrl = data.url
      } else {
        error.value = data.error || 'Erreur lors de l\'upload du logo.'
      }
    } catch (e: any) {
      error.value = 'Erreur lors de l\'upload du logo.'
    }
  }

  async function onSave() {
    success.value = false
    await updateSettings({
      appName: form.value.appName,
      logoUrl: form.value.logoUrl,
    })
    if (!error.value) {
      success.value = true
      settings.value.appName = form.value.appName
      settings.value.logoUrl = form.value.logoUrl
    }
  }
</script>



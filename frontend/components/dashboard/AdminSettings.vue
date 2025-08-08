<template>
  <div class="max-w-xl mx-auto bg-white rounded-2xl shadow-lg p-8 mt-8 animate-fadeInUp">
    <h2 class="text-3xl font-extrabold mb-6 text-gray-900">Personnalisation du site</h2>
    <form @submit.prevent="onSave" class="space-y-6">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Nom du site</label>
        <input v-model="form.appName" type="text" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white" placeholder="Nom du site" required />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Logo (URL)</label>
        <input v-model="form.logoUrl" type="url" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white" placeholder="https://..." />
        <div v-if="form.logoUrl" class="mt-4 flex items-center gap-3">
          <img :src="form.logoUrl" alt="Logo" class="h-12 w-12 rounded-xl object-contain border border-gray-200 shadow" />
          <span class="text-gray-500 text-xs">Aperçu</span>
        </div>
      </div>
      <div class="flex gap-3 justify-end mt-8">
        <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 text-white font-bold shadow-lg hover:bg-blue-700 transition-all duration-200">Enregistrer</button>
      </div>
      <div v-if="error" class="mt-4 text-red-500 text-sm">{{ error }}</div>
      <div v-if="success" class="mt-4 text-green-600 text-sm">Modifications enregistrées !</div>
    </form>
  </div>
</template>


<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useAppSettings } from '~/composables/useAppSettings'

const { settings, loading, error, updateSettings, fetchSettings } = useAppSettings()
const form = ref({ appName: '', logoUrl: '' })
const success = ref(false)

onMounted(async () => {
  await fetchSettings()
  form.value.appName = settings.value.appName
  form.value.logoUrl = settings.value.logoUrl
})

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



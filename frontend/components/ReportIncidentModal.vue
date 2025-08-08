<template>
  <div class="fixed inset-0 z-[2000] flex items-center justify-center min-h-screen min-w-full bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 relative animate-fade-in flex flex-col items-center justify-center">
      <button @click="$emit('close')" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 text-xl font-bold">&times;</button>
      <h3 class="text-xl font-bold mb-2 text-orange-600 flex items-center gap-2">
        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 20H3a2 2 0 01-2-2V6a2 2 0 012-2h18a2 2 0 012 2v12a2 2 0 01-2 2z" />
        </svg>
        {{ t('components.reportIncidentModal.title') }}
      </h3>
      <form @submit.prevent="submitIncident" class="flex flex-col gap-4 mt-4" enctype="multipart/form-data">
        <div>
          <label class="block text-sm font-semibold mb-1">{{ t('components.reportIncidentModal.descriptionLabel') }} <span class="text-red-500">*</span></label>
          <textarea v-model="description" required rows="3" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400"></textarea>
        </div>
        <div>
          <label class="block text-sm font-semibold mb-1">{{ t('components.reportIncidentModal.statusLabel') }}</label>
          <select v-model="status" class="w-full border rounded-lg px-3 py-2">
            <option value="a_venir">{{ t('components.reportIncidentModal.statusUpcoming') }}</option>
            <option value="en_cours">{{ t('components.reportIncidentModal.statusInProgress') }}</option>
            <option value="resolu">{{ t('components.reportIncidentModal.statusResolved') }}</option>
          </select>
        </div>
        <div v-if="!user?.email">
          <label class="block text-sm font-semibold mb-1">{{ t('components.reportIncidentModal.emailLabel') }} <span class="text-red-500">*</span></label>
          <input v-model="emailSignaleur" type="email" required class="w-full border rounded-lg px-3 py-2" :placeholder="t('components.reportIncidentModal.emailPlaceholder')" />
        </div>
        <div>
          <label class="block text-sm font-semibold mb-1">{{ t('components.reportIncidentModal.attachmentsLabel') }}</label>
          <input type="file" multiple @change="handleFiles" class="w-full border rounded-lg px-3 py-2" />
          <div v-if="files.length" class="mt-1 text-xs text-gray-600">
            <div v-for="(file, idx) in files" :key="idx">{{ file.name }}</div>
          </div>
        </div>
        <div class="flex justify-end gap-2 mt-2">
          <button type="button" @click="$emit('close')" class="px-4 py-2 rounded-lg border border-gray-300 bg-gray-100 hover:bg-gray-200 text-gray-700">{{ t('components.reportIncidentModal.cancel') }}</button>
          <button type="submit" :disabled="loading" class="px-4 py-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-bold shadow disabled:opacity-60">{{ t('components.reportIncidentModal.send') }}</button>
        </div>
        <div v-if="error" class="text-red-500 text-sm mt-2">{{ t(error) }}</div>
        <div v-if="success" class="text-green-600 text-sm mt-2">{{ t('components.reportIncidentModal.successMessage') }}</div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { ref } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { useAuthStore } from '~/stores/auth'

  const { t } = useI18n()

  type User = {
    email?: string
    // ...other user fields as needed
  }

  const emit = defineEmits(['close'])
  const description = ref('')
  const status = ref('en_cours')
  const emailSignaleur = ref('')

  const loading = ref(false)
  const error = ref('')
  const success = ref(false)

  const files = ref<File[]>([])

  function handleFiles(e: Event) {
    const target = e.target as HTMLInputElement
    if (target.files) {
      files.value = Array.from(target.files)
    }
  }

  const authStore = useAuthStore()
  const user = authStore.user as unknown as User
  const apiBase = useRuntimeConfig().public.apiBase

  async function submitIncident() {
    error.value = ''
    success.value = false
    if (!description.value.trim()) {
      error.value = 'components.reportIncidentModal.errorDescriptionRequired'
      return
    }
    if (!user?.email && !emailSignaleur.value.trim()) {
      error.value = 'components.reportIncidentModal.errorEmailRequired'
      return
    }
    loading.value = true
    try {
      const formData = new FormData()
      // Compose details JSON for backend
      const details = {
        datetime: new Date().toISOString().slice(0, 19).replace('T', ' '),
        object: description.value,
        statut: status.value,
        email_signaleur: user.email || emailSignaleur.value,
        pieces_jointes: [] as string[],
      }
      // Upload files and collect their names (let backend handle storage)
      files.value.forEach(f => formData.append('pieces_jointes[]', f))
      // Add details as JSON string
      formData.append('details', JSON.stringify(details))
      const headers: Record<string, string> = {}
      if (authStore.token) headers['Authorization'] = `Bearer ${authStore.token}`
      await $fetch(`${apiBase}/admin/incidents`, {
        method: 'POST',
        body: formData,
        headers,
      })
      success.value = true
      description.value = ''
      status.value = 'en_cours'
      emailSignaleur.value = ''
      files.value = []
      // Reset file input (UX)
      const fileInput = document.querySelector<HTMLInputElement>('input[type="file"]')
      if (fileInput) fileInput.value = ''
      window.dispatchEvent(new CustomEvent('incident-reported'))
      setTimeout(() => {
        emit('close')
      }, 1200)
    } catch (e: any) {
      if (e && typeof e === 'object' && 'data' in e && e.data && typeof e.data === 'object' && 'message' in e.data) {
        error.value = (e.data as any).message
      } else if (e instanceof Error) {
        error.value = e.message
      } else {
        error.value = 'components.reportIncidentModal.errorSend'
      }
    } finally {
      loading.value = false
    }
  }
</script>

<style scoped>
  .animate-fade-in {
    animation: fadeIn 0.2s;
  }
  @keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
  }
</style>



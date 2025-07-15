import { ref } from 'vue'
import axios from 'axios'

export function usePersons() {
  const loading = ref(false)
  const error = ref<string|null>(null)
  const admins = ref([])
  const gardiens = ref([])
  const residents = ref([])

  const fetchPersons = async () => {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get('/api/admin/persons')
      if (data.success) {
        admins.value = data.admins
        gardiens.value = data.gardiens
        residents.value = data.residents
      } else {
        error.value = data.message || 'Erreur lors du chargement.'
      }
    } catch (e: any) {
      error.value = e?.response?.data?.message || e.message || 'Erreur inconnue.'
    } finally {
      loading.value = false
    }
  }

  const updatePerson = async (id: number, payload: any) => {
    try {
      await axios.put(`/api/admin/persons/${id}`, payload)
      await fetchPersons()
    } catch (e: any) {
      throw e
    }
  }

  const deletePerson = async (id: number) => {
    try {
      await axios.delete(`/api/admin/persons/${id}`)
      await fetchPersons()
    } catch (e: any) {
      throw e
    }
  }

  return { loading, error, admins, gardiens, residents, fetchPersons, updatePerson, deletePerson }
}

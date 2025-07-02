<template>
  <div>
    <h2>Liste des visites</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Email visiteur</th>
          <th>ID invitation</th>
          <th>Motif</th>
          <th>Date début</th>
          <th>Date fin</th>
          <th>Statut</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="visite in filteredVisites"
          :key="visite.id_visite"
        >
          <td>{{ visite.id_visite }}</td>
          <td>{{ visite.email_visiteur }}</td>
          <td>{{ visite.id_invitation }}</td>
          <td>{{ visite.motif_visite }}</td>
          <td>{{ visite.date_visite_start }}</td>
          <td>{{ visite.date_visite_end }}</td>
          <td>{{ visite.statut_visite }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const visites = ref([])

const filteredVisites = computed(() =>
  visites.value.filter(visite =>
    visite.statut_visite === 'programmee' || visite.statut_visite === 'terminee'
  )
)

onMounted(async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/visite')
    visites.value = response.data
  } catch (error) {
    console.error('Erreur API :', error)
  }
})
</script>

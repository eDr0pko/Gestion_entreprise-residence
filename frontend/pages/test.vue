<template>
  <div>
    <h2>Liste des invités</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nom</th>
          <th>Prénom</th>
          <!-- Ajoute d'autres colonnes selon ta table -->
        </tr>
      </thead>
      <tbody>
        <tr v-for="invite in invites" :key="invite.id">
          <td>{{ invite.id }}</td>
          <td>{{ invite.nom }}</td>
          <td>{{ invite.prenom }}</td>
          <!-- Ajoute d'autres champs ici -->
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  data() {
    return {
      invites: []
    }
  },
  async mounted() {
    const response = await fetch('http://localhost:8000/api/invite')
    this.invites = await response.json()
  }
}
</script>
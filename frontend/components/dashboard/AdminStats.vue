
<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Statistiques (Admin)</h2>
    <div v-if="loading" class="text-gray-500">Chargement...</div>
    <div v-else-if="error" class="text-red-500">{{ error }}</div>
    <div v-else-if="stats" class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- GRAPHIQUES PRINCIPAUX -->
      <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Répartition utilisateurs -->
        <div class="bg-white rounded shadow p-4 flex flex-col items-center">
          <h3 class="font-semibold mb-2">Répartition des utilisateurs</h3>
          <Pie v-if="userPieData && userPieData.datasets && userPieData.datasets[0] && userPieData.datasets[0].data.every(v => typeof v === 'number' && !isNaN(v))" :data="userPieData" :options="pieOptions" style="max-width:320px" />
        </div>
        <!-- Activité messages par jour (fictif si pas de stats) -->
        <div class="bg-white rounded shadow p-4 flex flex-col items-center">
          <h3 class="font-semibold mb-2">Messages par jour (7 derniers jours)</h3>
          <Bar v-if="messagesBarData" :data="messagesBarData" :options="barOptions" style="max-width:420px" />
        </div>
      </div>
      <!-- Utilisateurs -->
      <div class="bg-white rounded shadow p-4">
        <h3 class="font-semibold mb-2">Utilisateurs</h3>
        <ul>
          <li>Total: <b>{{ stats.users?.total ?? '?' }}</b></li>
          <li>Admins: <b>{{ stats.users?.admins ?? '?' }}</b></li>
          <li>Gardiens: <b>{{ stats.users?.gardiens ?? '?' }}</b></li>
          <li>Résidents: <b>{{ stats.users?.residents ?? '?' }}</b></li>
          <li>Invités: <b>{{ stats.users?.invites ?? '?' }}</b></li>
        </ul>
        <div class="mt-2">
          <span class="font-semibold">Nouveaux inscrits:</span>
          <ul class="text-xs">
            <li v-for="u in stats.users?.recent || []" :key="u.id_personne">{{ u.prenom }} {{ u.nom }} ({{ u.email }})</li>
          </ul>
        </div>
        <div class="mt-2 text-xs text-gray-600">
          <span class="font-semibold">Taux d'invités actifs:</span>
          <span v-if="stats.invites"> {{ stats.invites.active }}/{{ stats.invites.active + stats.invites.inactive }} ({{ inviteActiveRate }}%)</span>
        </div>
      </div>

      <!-- Groupes & Messages -->
      <div class="bg-white rounded shadow p-4">
        <h3 class="font-semibold mb-2">Groupes & Messages</h3>
        <ul>
          <li>Groupes: <b>{{ stats.groups?.total ?? '?' }}</b></li>
          <li>Messages: <b>{{ stats.messages?.total ?? '?' }}</b></li>
        </ul>
        <div class="mt-2">
          <span class="font-semibold">Groupes les plus actifs:</span>
          <ul class="text-xs">
            <li v-for="g in stats.groups?.most_active || []" :key="g.id_groupe_message">Groupe #{{ g.id_groupe_message }} ({{ g.count }} messages)</li>
          </ul>
        </div>
        <div class="mt-2">
          <span class="font-semibold">Utilisateurs les plus actifs:</span>
          <ul class="text-xs">
            <li v-for="u in stats.messages?.most_active_users || []" :key="u.id_auteur">Auteur #{{ u.id_auteur }} ({{ u.count }} messages)</li>
          </ul>
        </div>
        <div class="mt-2 text-xs text-gray-600">
          <span class="font-semibold">Part de messages avec fichiers:</span>
          <span v-if="stats.files && stats.messages"> {{ stats.files.total }}/{{ stats.messages.total }} ({{ fileRate }}%)</span>
        </div>
      </div>

      <!-- Réactions & Fichiers -->
      <div class="bg-white rounded shadow p-4">
        <h3 class="font-semibold mb-2">Réactions & Fichiers</h3>
        <ul>
          <li>Réactions: <b>{{ stats.reactions?.total ?? '?' }}</b></li>
          <li>Fichiers partagés: <b>{{ stats.files?.total ?? '?' }}</b></li>
        </ul>
        <div class="mt-2">
          <span class="font-semibold">Emojis les plus utilisés:</span>
          <ul class="text-xs">
            <li v-for="e in stats.reactions?.top_emojis || []" :key="e.emoji">{{ e.emoji }} ({{ e.count }})</li>
          </ul>
        </div>
        <div class="mt-2">
          <span class="font-semibold">Plus gros fichiers:</span>
          <ul class="text-xs">
            <li v-for="f in stats.files?.largest || []" :key="f.nom_fichier">{{ f.nom_fichier }} ({{ (f.taille_fichier/1024).toFixed(1) }} Ko)</li>
          </ul>
        </div>
      </div>

      <!-- Visites, Invités, Bans -->
      <div class="bg-white rounded shadow p-4">
        <h3 class="font-semibold mb-2">Visites, Invités, Bans</h3>
        <ul>
          <li>Visites par statut:
            <ul class="text-xs">
              <li v-for="v in stats.visits?.by_status || []" :key="v.statut_visite">{{ v.statut_visite }}: {{ v.count }}</li>
            </ul>
          </li>
          <li>Invités actifs: <b>{{ stats.invites?.active ?? '?' }}</b></li>
          <li>Invités inactifs: <b>{{ stats.invites?.inactive ?? '?' }}</b></li>
          <li>Bannissements: <b>{{ stats.bans?.total ?? '?' }}</b></li>
        </ul>
        <div class="mt-2">
          <span class="font-semibold">Motifs fréquents:</span>
          <ul class="text-xs">
            <li v-for="b in stats.bans?.motifs || []" :key="b.motif">{{ b.motif || 'Non renseigné' }} ({{ b.count }})</li>
          </ul>
        </div>
        <div class="mt-2 text-xs text-gray-600">
          <span class="font-semibold">Taux de bannissement:</span>
          <span v-if="stats.bans && stats.users"> {{ stats.bans.total }}/{{ stats.users.total }} ({{ banRate }}%)</span>
        </div>
      </div>

      <!-- Logs & Activité -->
      <div class="bg-white rounded shadow p-4 md:col-span-2">
        <h3 class="font-semibold mb-2">Logs & Activité</h3>
        <ul>
          <li>Total logs: <b>{{ stats.logs?.total ?? '?' }}</b></li>
        </ul>
        <div class="mt-2">
          <span class="font-semibold">Actions les plus fréquentes:</span>
          <ul class="text-xs">
            <li v-for="a in stats.logs?.top_actions || []" :key="a.action">{{ a.action }} ({{ a.count }})</li>
          </ul>
        </div>
        <div class="mt-2">
          <span class="font-semibold">Utilisateurs les plus actifs (logs):</span>
          <ul class="text-xs">
            <li v-for="u in stats.logs?.top_users || []" :key="u.user_id">User #{{ u.user_id }} ({{ u.count }} logs)</li>
          </ul>
        </div>
        <div class="mt-2">
          <span class="font-semibold">Dernières actions:</span>
          <ul class="text-xs">
            <li v-for="l in stats.logs?.recent || []" :key="l.id">[{{ l.created_at }}] {{ l.action }} - {{ l.message }}</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  </template>

<script setup lang="ts">
import { stats, loading, error } from '@/composables/useAdminStats';
import { computed } from 'vue';
import { Pie, Bar } from 'vue-chartjs';
import {
  Chart,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js';

Chart.register(Title, Tooltip, Legend, ArcElement, BarElement, CategoryScale, LinearScale);

// Répartition utilisateurs (pie)
const userPieData = computed(() => {
  if (!stats.value?.users) return null;
  // Sécurise les valeurs pour éviter undefined/null
  const admins = Number(stats.value.users.admins) || 0;
  const gardiens = Number(stats.value.users.gardiens) || 0;
  const residents = Number(stats.value.users.residents) || 0;
  const invites = Number(stats.value.users.invites) || 0;
  return {
    labels: ['Admins', 'Gardiens', 'Résidents', 'Invités'],
    datasets: [
      {
        data: [admins, gardiens, residents, invites],
        backgroundColor: ['#f87171', '#60a5fa', '#34d399', '#fbbf24'],
      }
    ]
  };
});
const pieOptions = { responsive: true, plugins: { legend: { position: 'bottom' as const } } };

// Messages par jour (bar) - fictif si pas de stats
const messagesBarData = computed(() => {
  // Si le backend fournit stats.messages.by_day, l'utiliser, sinon générer du fictif
  const days = Array.from({ length: 7 }, (_, i) => {
    const d = new Date();
    d.setDate(d.getDate() - (6 - i));
    return d.toLocaleDateString('fr-FR', { weekday: 'short', day: '2-digit', month: '2-digit' });
  });
  let values = stats.value?.messages?.by_day;
  if (!values) {
    // Générer du fictif à partir du total
    const total = stats.value?.messages?.total || 0;
    values = Array(7).fill(Math.floor(total / 7));
    if (total > 0) values[6] += total % 7;
  }
  return {
    labels: days,
    datasets: [
      {
        label: 'Messages',
        data: values,
        backgroundColor: '#60a5fa',
      }
    ]
  };
});
const barOptions = { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } };

// Taux d'invités actifs
const inviteActiveRate = computed(() => {
  if (!stats.value?.invites) return '?';
  const total = stats.value.invites.active + stats.value.invites.inactive;
  if (!total) return '0';
  return Math.round((stats.value.invites.active / total) * 100);
});
// Part de messages avec fichiers
const fileRate = computed(() => {
  if (!stats.value?.files || !stats.value?.messages) return '?';
  if (!stats.value.messages.total) return '0';
  return Math.round((stats.value.files.total / stats.value.messages.total) * 100);
});
// Taux de bannissement
const banRate = computed(() => {
  if (!stats.value?.bans || !stats.value?.users) return '?';
  if (!stats.value.users.total) return '0';
  return Math.round((stats.value.bans.total / stats.value.users.total) * 100);
});
</script>

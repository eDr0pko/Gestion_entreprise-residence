<template>
  <div class="p-6 bg-gradient-to-br from-[#f8fafc] to-[#e0e7ef] min-h-screen">
    <h2 class="text-3xl font-extrabold mb-8 text-[#1e293b] tracking-tight flex items-center gap-3">
      <span class="inline-block bg-blue-100 text-blue-700 rounded-full px-3 py-1 text-lg">📊</span>
      Statistiques &amp; Activité
    </h2>
    <div v-if="loading" class="text-gray-500 text-lg flex items-center gap-2 animate-pulse"><span class="animate-spin">⏳</span> Chargement...</div>
    <div v-else-if="error" class="text-red-500 text-lg font-semibold">{{ error }}</div>
    <div v-else-if="stats" class="grid grid-cols-1 xl:grid-cols-3 gap-8">
      <!-- KPIs principaux -->
      <div class="xl:col-span-3 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center border-t-4 border-blue-400">
          <div class="text-3xl mb-2">👥</div>
          <div class="text-2xl font-bold text-blue-700">{{ stats.users?.total ?? '?' }}</div>
          <div class="text-xs text-gray-500">Utilisateurs</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center border-t-4 border-green-400">
          <div class="text-3xl mb-2">💬</div>
          <div class="text-2xl font-bold text-green-700">{{ stats.messages?.total ?? '?' }}</div>
          <div class="text-xs text-gray-500">Messages</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center border-t-4 border-yellow-400">
          <div class="text-3xl mb-2">📁</div>
          <div class="text-2xl font-bold text-yellow-700">{{ stats.files?.total ?? '?' }}</div>
          <div class="text-xs text-gray-500">Fichiers partagés</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center border-t-4 border-pink-400">
          <div class="text-3xl mb-2">📝</div>
          <div class="text-2xl font-bold text-pink-700">{{ stats.logs?.total ?? '?' }}</div>
          <div class="text-xs text-gray-500">Logs</div>
        </div>
      </div>

      <!-- Graphiques principaux -->
      <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center xl:col-span-2">
        <h3 class="font-semibold mb-4 text-lg flex items-center gap-2"><span class="text-blue-400">●</span> Répartition des utilisateurs</h3>
        <Pie v-if="userPieData && userPieData.datasets && userPieData.datasets[0] && userPieData.datasets[0].data.every(v => typeof v === 'number' && !isNaN(v))" :data="userPieData" :options="pieOptions" style="max-width:340px" />
        <div class="flex gap-4 mt-6 w-full justify-center">
          <div v-for="(val, idx) in [stats.users?.admins, stats.users?.gardiens, stats.users?.residents, stats.users?.invites]" :key="idx" class="flex flex-col items-center">
            <div :class="['w-3 h-3 rounded-full mb-1', ['bg-red-300','bg-blue-300','bg-green-300','bg-yellow-300'][idx]]"></div>
            <div class="text-xs text-gray-500">{{ ['Admins','Gardiens','Résidents','Invités'][idx] }}</div>
            <div class="font-bold text-base">{{ val ?? '?' }}</div>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center">
        <h3 class="font-semibold mb-4 text-lg flex items-center gap-2"><span class="text-green-400">●</span> Messages par jour (7j)</h3>
        <Bar v-if="messagesBarData" :data="messagesBarData" :options="barOptions" style="max-width:420px" />
        <div class="w-full mt-6">
          <div class="flex justify-between text-xs text-gray-500 mb-1">
            <span>Total</span>
            <span>{{ stats.messages?.total ?? '?' }}</span>
          </div>
          <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
            <div class="h-2 bg-green-400" :style="{ width: ((stats.messages?.total || 0) / (stats.users?.total || 1) * 10) + '%' }"></div>
          </div>
        </div>
      </div>

      <!-- Utilisateurs récents -->
      <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col xl:col-span-3">
        <h3 class="font-semibold mb-4 text-lg flex items-center gap-2"><span class="text-indigo-400">●</span> Nouveaux inscrits</h3>
        <div v-if="stats.users?.recent?.length" class="flex flex-wrap gap-4">
          <div v-for="u in stats.users.recent" :key="u.id_personne" class="flex items-center gap-3 bg-indigo-50 rounded-xl px-4 py-2 shadow-sm">
            <div class="w-8 h-8 rounded-full bg-indigo-200 flex items-center justify-center text-indigo-700 font-bold text-lg">
              {{ u.prenom[0] }}{{ u.nom[0] }}
            </div>
            <div>
              <div class="font-semibold">{{ u.prenom }} {{ u.nom }}</div>
              <div class="text-xs text-gray-500">{{ u.email }}</div>
            </div>
          </div>
        </div>
        <div v-else class="text-gray-400 italic">Aucun nouvel inscrit récent</div>
      </div>

      <!-- Groupes & Messages avancés -->
      <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col">
        <h3 class="font-semibold mb-4 text-lg flex items-center gap-2"><span class="text-yellow-400">●</span> Groupes &amp; Activité</h3>
        <div class="flex flex-wrap gap-4 mb-4">
          <div class="flex flex-col items-center">
            <div class="text-2xl">👥</div>
            <div class="font-bold">{{ stats.groups?.total ?? '?' }}</div>
            <div class="text-xs text-gray-500">Groupes</div>
          </div>
          <div class="flex flex-col items-center">
            <div class="text-2xl">💬</div>
            <div class="font-bold">{{ stats.messages?.total ?? '?' }}</div>
            <div class="text-xs text-gray-500">Messages</div>
          </div>
        </div>
        <div class="mb-2">
          <span class="font-semibold">Groupes les plus actifs :</span>
          <ul class="text-xs mt-1">
            <li v-for="g in stats.groups?.most_active || []" :key="g.id_groupe_message">Groupe #{{ g.id_groupe_message }} <span class="text-gray-400">({{ g.count }} messages)</span></li>
          </ul>
        </div>
        <div class="mb-2">
          <span class="font-semibold">Utilisateurs les plus actifs :</span>
          <ul class="text-xs mt-1">
            <li v-for="u in stats.messages?.most_active_users || []" :key="u.id_auteur">Auteur #{{ u.id_auteur }} <span class="text-gray-400">({{ u.count }} messages)</span></li>
          </ul>
        </div>
        <div class="mb-2 text-xs text-gray-600">
          <span class="font-semibold">Part de messages avec fichiers :</span>
          <span v-if="stats.files && stats.messages"> {{ stats.files.total }}/{{ stats.messages.total }} ({{ fileRate }}%)</span>
        </div>
      </div>

      <!-- Réactions & Fichiers -->
      <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col">
        <h3 class="font-semibold mb-4 text-lg flex items-center gap-2"><span class="text-pink-400">●</span> Réactions &amp; Fichiers</h3>
        <div class="flex flex-wrap gap-4 mb-4">
          <div class="flex flex-col items-center">
            <div class="text-2xl">👍</div>
            <div class="font-bold">{{ stats.reactions?.total ?? '?' }}</div>
            <div class="text-xs text-gray-500">Réactions</div>
          </div>
          <div class="flex flex-col items-center">
            <div class="text-2xl">📁</div>
            <div class="font-bold">{{ stats.files?.total ?? '?' }}</div>
            <div class="text-xs text-gray-500">Fichiers</div>
          </div>
        </div>
        <div class="mb-2">
          <span class="font-semibold">Emojis les plus utilisés :</span>
          <ul class="text-xs mt-1 flex flex-wrap gap-2">
            <li v-for="e in stats.reactions?.top_emojis || []" :key="e.emoji" class="flex items-center gap-1 bg-pink-50 rounded px-2 py-1">
              <span class="text-lg">{{ e.emoji }}</span> <span class="text-gray-500">({{ e.count }})</span>
            </li>
          </ul>
        </div>
        <div class="mb-2">
          <span class="font-semibold">Plus gros fichiers :</span>
          <ul class="text-xs mt-1">
            <li v-for="f in stats.files?.largest || []" :key="f.nom_fichier">{{ f.nom_fichier }} <span class="text-gray-400">({{ (f.taille_fichier/1024).toFixed(1) }} Ko)</span></li>
          </ul>
        </div>
      </div>

      <!-- Visites, Invités, Bans -->
      <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col">
        <h3 class="font-semibold mb-4 text-lg flex items-center gap-2"><span class="text-blue-400">●</span> Visites, Invités, Bans</h3>
        <div class="flex flex-wrap gap-4 mb-4">
          <div class="flex flex-col items-center">
            <div class="text-2xl">🚪</div>
            <div class="font-bold">{{ totalVisits }}</div>
            <div class="text-xs text-gray-500">Visites</div>
          </div>
          <div class="flex flex-col items-center">
            <div class="text-2xl">🧑‍🤝‍🧑</div>
            <div class="font-bold">{{ stats.invites?.active ?? '?' }}</div>
            <div class="text-xs text-gray-500">Invités actifs</div>
          </div>
          <div class="flex flex-col items-center">
            <div class="text-2xl">⏳</div>
            <div class="font-bold">{{ stats.invites?.inactive ?? '?' }}</div>
            <div class="text-xs text-gray-500">Invités inactifs</div>
          </div>
          <div class="flex flex-col items-center">
            <div class="text-2xl">⛔</div>
            <div class="font-bold">{{ stats.bans?.total ?? '?' }}</div>
            <div class="text-xs text-gray-500">Bannissements</div>
          </div>
        </div>
        <div class="mb-2">
          <span class="font-semibold">Visites par statut :</span>
          <ul class="text-xs mt-1 flex flex-wrap gap-2">
            <li v-for="v in stats.visits?.by_status || []" :key="v.statut_visite" class="bg-blue-50 rounded px-2 py-1">{{ v.statut_visite }}: {{ v.count }}</li>
          </ul>
        </div>
        <div class="mb-2">
          <span class="font-semibold">Motifs fréquents de ban :</span>
          <ul class="text-xs mt-1 flex flex-wrap gap-2">
            <li v-for="b in stats.bans?.motifs || []" :key="b.motif" class="bg-red-50 rounded px-2 py-1">{{ b.motif || 'Non renseigné' }} ({{ b.count }})</li>
          </ul>
        </div>
        <div class="mb-2 text-xs text-gray-600">
          <span class="font-semibold">Taux d'invités actifs :</span>
          <span v-if="stats.invites"> {{ stats.invites.active }}/{{ stats.invites.active + stats.invites.inactive }} ({{ inviteActiveRate }}%)</span>
        </div>
        <div class="mb-2 text-xs text-gray-600">
          <span class="font-semibold">Taux de bannissement :</span>
          <span v-if="stats.bans && stats.users"> {{ stats.bans.total }}/{{ stats.users.total }} ({{ banRate }}%)</span>
        </div>
      </div>

      <!-- Logs & Activité -->
      <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col xl:col-span-3">
        <h3 class="font-semibold mb-4 text-lg flex items-center gap-2"><span class="text-gray-400">●</span> Logs &amp; Activité</h3>
        <div class="flex flex-wrap gap-4 mb-4">
          <div class="flex flex-col items-center">
            <div class="text-2xl">📝</div>
            <div class="font-bold">{{ stats.logs?.total ?? '?' }}</div>
            <div class="text-xs text-gray-500">Logs</div>
          </div>
        </div>
        <div class="mb-2">
          <span class="font-semibold">Actions les plus fréquentes :</span>
          <ul class="text-xs mt-1 flex flex-wrap gap-2">
            <li v-for="a in stats.logs?.top_actions || []" :key="a.action" class="bg-gray-100 rounded px-2 py-1">{{ a.action }} ({{ a.count }})</li>
          </ul>
        </div>
        <div class="mb-2">
          <span class="font-semibold">Utilisateurs les plus actifs (logs) :</span>
          <ul class="text-xs mt-1 flex flex-wrap gap-2">
            <li v-for="u in stats.logs?.top_users || []" :key="u.user_id" class="bg-gray-100 rounded px-2 py-1">User #{{ u.user_id }} ({{ u.count }} logs)</li>
          </ul>
        </div>
        <div class="mb-2">
          <span class="font-semibold">Dernières actions :</span>
          <ul class="text-xs mt-1">
            <li v-for="l in stats.logs?.recent || []" :key="l.id">[{{ l.created_at }}] {{ l.action }} - {{ l.message }}</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Calcul du total des visites pour affichage (évite les erreurs de typage dans le template)
const totalVisits = computed(() => {
  if (!stats.value?.visits?.by_status) return '?';
  return stats.value.visits.by_status.reduce((a: number, v: { count: number }) => a + (v.count || 0), 0);
});
  import { stats, loading, error, fetchStats } from '@/composables/useAdminStats';
  import { computed, onMounted } from 'vue';
  onMounted(fetchStats)
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

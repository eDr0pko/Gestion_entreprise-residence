<template>
  <div>
    <transition name="notif-btn-fade">
      <button
        v-if="!open"
        @click="toggleSidebar"
        class="notif-btn"
        :class="{ 'clicked': btnClicked }"
        @animationend="btnClicked = false"
      >
        🔔 Notifications
      </button>
    </transition>
    <transition name="slide">
      <div v-if="open" class="sidebar">
        <div class="sidebar-header flex justify-between items-center px-4 py-2 border-b">
          <div class="flex space-x-4">
            <button
              @click="showTrash = false"
              :class="!showTrash ? 'font-bold underline' : 'text-gray-500'"
            >
              Notifications
            </button>
            <button
              @click="showTrash = true"
              :class="showTrash ? 'font-bold underline' : 'text-gray-500'"
            >
              Corbeille
            </button>
          </div>
          <button @click="toggleSidebar" class="close-btn text-xl" title="Fermer">✖</button>
        </div>


        <ul class="notif-list">
          <li v-for="visite in filteredNotifications" :key="visite.id_visite" class="mb-4 border-b pb-2">
            <div><b>ID visite:</b> {{ visite.id_visite }}</div>
            <div><b>Email visiteur:</b> {{ visite.email_visiteur }}</div>
            <div><b>ID invitation:</b> {{ visite.id_invitation }}</div>
            <div><b>Motif:</b> {{ visite.motif_visite }}</div>
            <div><b>Date visite:</b> {{ visite.date_visite }}</div>
            <div>
              <b>Statut:</b>
              <select v-model="visite.statut_visite" @change="updateStatut(visite)">
                <option value="programmee">Programmee</option>
                <option value="terminee">Terminee</option>
                <option value="annulee">Annulee</option>
                <option value="en_attente">En attente</option>
              </select>
            </div>
          </li>
          <li v-if="filteredNotifications.length === 0">
            {{ showTrash ? 'Aucune visite annulée.' : 'Aucune visite.' }}
          </li>
        </ul>
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  data() {
    return {
      open: false,
      btnClicked: false,
      notifications: [],
      showTrash: false,
    };
  },
  computed: {
    filteredNotifications() {
      return this.notifications.filter(v =>
        this.showTrash ? v.statut_visite === 'annulee' : v.statut_visite !== 'annulee'
      );
    }
  },
  methods: {
    toggleSidebar() {
      if (!this.open) {
        this.btnClicked = true;
        setTimeout(() => {
          this.open = true;
          this.fetchNotifications();
        }, 150);
      } else {
        this.open = false;
      }
    },
    async fetchNotifications() {
      try {
        const response = await fetch('http://localhost:8000/api/visite');
        if (response.ok) {
          this.notifications = await response.json();
        } else {
          this.notifications = [];
        }
      } catch (e) {
        this.notifications = [];
      }
    },
    async updateStatut(visite) {
      try {
        await fetch(`http://localhost:8000/api/visite/${visite.id_visite}`, {
          method: 'PATCH',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ statut_visite: visite.statut_visite })
        });
        this.$emit('refreshPlanning'); // Ajoute cette ligne
      } catch (e) {
        alert("Erreur lors de la modification du statut");
      }
    }
  }
};
</script>

<style scoped>
.notif-btn {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 1001;
  background: #3498db;
  color: #fff;
  border: none;
  padding: 10px 18px;
  border-radius: 5px;
  cursor: pointer;
  transition: transform 0.15s;
}
.notif-btn.clicked {
  animation: notif-pop 0.15s;
}
@keyframes notif-pop {
  0% { transform: scale(1);}
  50% { transform: scale(1.15);}
  100% { transform: scale(1);}
}
.notif-btn-fade-enter-active, .notif-btn-fade-leave-active {
  transition: opacity 0.2s;
}
.notif-btn-fade-enter-from, .notif-btn-fade-leave-to {
  opacity: 0;
}
.sidebar {
  position: fixed;
  top: 0;
  right: 0;
  width: 320px;
  height: 100%;
  background: #fff;
  box-shadow: -2px 0 8px rgba(0,0,0,0.15);
  z-index: 1000;
  padding: 0;
  display: flex;
  flex-direction: column;
}
.sidebar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 18px;
  background: #f5f5f5;
  border-bottom: 1px solid #eee;
}
.close-btn {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
}
.notif-list {
  list-style: none;
  padding: 18px;
  margin: 0;
  flex: 1;
  overflow-y: auto;
}
.slide-enter-active, .slide-leave-active {
  transition: transform 0.3s;
}
.slide-enter-from, .slide-leave-to {
  transform: translateX(100%);
}
</style>
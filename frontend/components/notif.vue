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
        <div class="sidebar-header">
          <span>Notifications</span>
          <button @click="toggleSidebar" class="close-btn">✖</button>
        </div>
        <ul class="notif-list">
          <li v-for="invite in notifications" :key="invite.id_invitation">
            <strong>{{ invite.email_invite }}</strong><br>
            <small>Statut : {{ invite.statut_invitation }}</small><br>
            <small>Date : {{ invite.date_invitation }}</small>
          </li>
          <li v-if="notifications.length === 0">
            Aucune notification.
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
      notifications: []
    };
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
        const response = await fetch('http://localhost:8000/api/invites');
        if (response.ok) {
          this.notifications = await response.json();
        } else {
          this.notifications = [];
        }
      } catch (e) {
        this.notifications = [];
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
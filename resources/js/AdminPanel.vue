<template>
  <div id="admin-panel">
    <aside id="sidebar" :class="{ compact: isCompactSidebar }">
      <div class="logo">
        <div class="logo-mark">
          <i class="fas fa-compass"></i>
        </div>
        <div class="logo-copy">
          <strong>Autochtone</strong>
          <span>Administration</span>
        </div>
      </div>

      <div class="sidebar-section">
        <p class="sidebar-label">Navigation</p>
        <nav>
          <ul>
            <li
              v-for="item in visibleNavItems"
              :key="item.key"
              :class="{ active: currentSection === item.key }"
              @click="setSection(item.key)"
            >
              <i :class="item.icon"></i>
              <span>{{ item.label }}</span>
            </li>
          </ul>
        </nav>
      </div>

      <div class="sidebar-footer">
        <button class="logout-btn" @click="handleLogout">
          <i class="fas fa-sign-out-alt"></i>
          <span>Deconnecter</span>
        </button>
      </div>
    </aside>

    <main>
      <header class="admin-header">
        <div class="header-left">
          <button class="sidebar-toggle" @click="toggleSidebar">
            <i class="fas" :class="isCompactSidebar ? 'fa-bars' : 'fa-chevron-left'"></i>
          </button>
          <div class="header-copy">
            <p class="header-eyebrow">Back-office</p>
            <h2 id="current-title">{{ sectionTitles[currentSection] }}</h2>
          </div>
        </div>

        <div class="header-center">
          <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Rechercher dans l'administration...">
          </div>
        </div>

        <div class="header-right">
          <div class="header-stat">
            <span class="stat-label">Module</span>
            <strong>{{ sectionTitles[currentSection] }}</strong>
          </div>
          <div class="admin-profile-wrapper" ref="profileMenu">
            <button class="admin-profile" type="button" @click="toggleProfileMenu">
              <div class="admin-info">
                <span class="name">{{ adminName }}</span>
                <span class="role">Administrateur principal</span>
              </div>
              <img :src="adminPhotoUrl" alt="Profil administrateur principal">
              <i class="fas fa-chevron-down profile-chevron" :class="{ open: isProfileMenuOpen }"></i>
            </button>

            <div v-if="isProfileMenuOpen" class="admin-profile-dropdown">
              <div class="dropdown-profile-preview">
                <img :src="adminPhotoUrl" alt="Photo de profil administrateur">
                <div>
                  <strong>{{ adminName }}</strong>
                  <span>Administrateur principal</span>
                </div>
              </div>

              <button class="profile-shortcut" type="button" @click="openProfileSection">
                <i class="fas fa-user-circle"></i>
                <span>Afficher le profil</span>
              </button>
            </div>
          </div>
        </div>
      </header>

      <section id="main-section">
        <div class="section-shell">
          <div class="section-banner">
            <div>
              <h3>{{ sectionTitles[currentSection] }}</h3>
              <span>organisation des opérations.</span>
            </div>
            <div class="section-pills">
              <span class="section-pill">Vue active</span>
              <span class="section-pill muted">{{ currentDateLabel }}</span>
            </div>
          </div>

          <component :is="currentComponent" v-if="currentComponent !== 'Default'"></component>
          <div v-else class="card">
            <h3>Contenu pour {{ sectionTitles[currentSection] }}</h3>
            <p>Cette section est en cours de developpement.</p>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script>
import Dashboard from './components/Dashboard.vue';
import Reservation from './components/Reservation.vue';
import Circuit from './components/Circuit.vue';
import Client from './components/Client.vue';
import Partenariat from './components/Partenariat.vue';
import Paiement from './components/Paiement.vue';
import Marketing from './components/Marketing.vue';
import Profile from './components/Profile.vue';

export default {
  name: 'AdminPanel',
  components: {
    Dashboard,
    Reservation,
    Circuit,
    Client,
    Partenariat,
    Paiement,
    Marketing,
    Profile,
  },
  data() {
    return {
      currentSection: 'dashboard',
      isCompactSidebar: false,
      isProfileMenuOpen: false,
      adminName: 'Administrateur principal',
      adminPhoto: '',
      sectionTitles: {
        dashboard: 'Tableau de bord',
        reservation: 'Reservation',
        circuit: 'Gestion de circuit',
        client: 'Client',
        partenariat: 'Partenariat',
        paiement: 'Paiement',
        marketing: 'Marketing',
        profile: 'Mon Profil',
      },
      navItems: [
        { key: 'dashboard', label: 'Tableau de bord', icon: 'fas fa-chart-line' },
        { key: 'reservation', label: 'Reservation', icon: 'fas fa-calendar-check' },
        { key: 'circuit', label: 'Gestion de circuit', icon: 'fas fa-map-marked-alt' },
        { key: 'client', label: 'Client', icon: 'fas fa-users' },
        { key: 'partenariat', label: 'Partenariat', icon: 'fas fa-handshake' },
        { key: 'paiement', label: 'Paiement', icon: 'fas fa-credit-card' },
        { key: 'marketing', label: 'Marketing', icon: 'fas fa-bullhorn' },
        { key: 'profile', label: 'Mon Profil', icon: 'fas fa-user' },
      ],
    };
  },
  computed: {
    visibleNavItems() {
      return this.navItems.filter((item) => item.key !== 'profile');
    },
    currentComponent() {
      const componentName = this.currentSection.charAt(0).toUpperCase() + this.currentSection.slice(1);
      return this.$options.components[componentName] ? componentName : 'Default';
    },
    adminPhotoUrl() {
      if (this.adminPhoto) {
        return this.adminPhoto;
      }

      return '/img/Andranotora.jpg';
    },
    currentDateLabel() {
      return new Date().toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
      });
    },
  },
  methods: {
    setSection(section) {
      this.currentSection = section;
      this.isProfileMenuOpen = false;
    },
    toggleSidebar() {
      this.isCompactSidebar = !this.isCompactSidebar;
    },
    toggleProfileMenu() {
      this.isProfileMenuOpen = !this.isProfileMenuOpen;
    },
    openProfileSection() {
      this.currentSection = 'profile';
      this.isProfileMenuOpen = false;
    },
    handleDocumentClick(event) {
      if (!this.isProfileMenuOpen) {
        return;
      }

      if (this.$refs.profileMenu && !this.$refs.profileMenu.contains(event.target)) {
        this.isProfileMenuOpen = false;
      }
    },
    async handleLogout() {
      if (!confirm('Voulez-vous vraiment vous deconnecter ?')) {
        return;
      }

      const token = localStorage.getItem('access_token');
      if (token) {
        await fetch('/api/admin/logout', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
        }).catch(() => {});
      }

      localStorage.removeItem('access_token');
      window.location.href = '/admin/login';
    },

    async checkAdminAuthentication() {
      const token = localStorage.getItem('access_token');
      if (!token) {
        window.location.href = '/admin/login';
        return;
      }

      const res = await fetch('/api/admin/check', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      });

      const data = await res.json();
      if (!res.ok || !data.authenticated) {
        localStorage.removeItem('access_token');
        window.location.href = '/admin/login';
        return;
      }

      if (data.user && data.user.name) {
        this.adminName = data.user.name;
      }

      if (data.user) {
        this.adminPhoto = data.user.photo_url || data.user.avatar || '';
      }
    },

    syncSidebarWithViewport() {
      this.isCompactSidebar = window.innerWidth < 1100;
    },
  },
  mounted() {
    this.checkAdminAuthentication();
    this.syncSidebarWithViewport();
    window.addEventListener('resize', this.syncSidebarWithViewport);
    document.addEventListener('click', this.handleDocumentClick);
  },
  beforeUnmount() {
    window.removeEventListener('resize', this.syncSidebarWithViewport);
    document.removeEventListener('click', this.handleDocumentClick);
  },
};
</script>

<style scoped>
#admin-panel {
  display: flex;
  height: 100vh;
  width: 100vw;
  background-color: var(--bg-light);
  color: var(--text-main);
  overflow: hidden;
}
</style>

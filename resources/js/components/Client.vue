<template>
  <div class="client-crm-wrapper">
    <div class="crm-nav card">
      <div class="nav-left">
        <button class="crm-tab-btn" :class="{ active: activeTab === 'directory' }" @click="activeTab = 'directory'">
          <i class="fas fa-users-cog"></i> Gestion des Clients
        </button>
        <button class="crm-tab-btn" :class="{ active: activeTab === 'messages' }" @click="activeTab = 'messages'">
          <i class="fas fa-comment-alt"></i> Messagerie <span class="notif-badge">{{ unreadCount }}</span>
        </button>
      </div>
      <div class="nav-right">
        <div class="crm-search">
          <i class="fas fa-search"></i>
          <input type="text" v-model="searchQuery" placeholder="Recherche universelle...">
        </div>
      </div>
    </div>

    <div v-if="activeTab === 'directory'" class="crm-section active">
      <div class="crm-stats">
        <div class="stat-mini-card">
          <span class="label">Total Clients</span>
          <span class="value">{{ clients.length }}</span>
          <span class="trend up">Base active</span>
        </div>
        <div class="stat-mini-card">
          <span class="label">Taux Fidelite</span>
          <span class="value">{{ loyaltyRate }}%</span>
          <span class="trend">Selon niveau et statut</span>
        </div>
        <div class="stat-mini-card green-tint">
          <span class="label">Nouveaux (30 jours)</span>
          <span class="value">{{ recentClientsCount }}</span>
          <span class="trend up">Evolution recente</span>
        </div>
      </div>

      <div class="card crm-table-card">
        <div class="card-header-actions">
          <h3>Annuaire Clients</h3>
        </div>
        <table class="modern-table">
          <thead>
            <tr>
              <th>Identite</th>
              <th>Contact</th>
              <th>Inscription</th>
              <th>Fidelite</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="client in filteredClients" :key="client.id">
              <td>
                <div class="user-info">
                  <img :src="`https://ui-avatars.com/api/?name=${client.prenom}+${client.nom}&background=random`" class="avatar">
                  <span>{{ client.nom }} {{ client.prenom }}</span>
                </div>
              </td>
              <td><small>{{ client.email }}<br>{{ client.tel }}</small></td>
              <td>{{ client.reservation_date ? formatDate(client.reservation_date) : '-' }}</td>
              <td><span class="badge-vip">{{ client.niveau }}</span></td>
              <td><span class="status-pill" :class="`status-${client.reservation_status?.toLowerCase().replace('é', 'e')}`">{{ client.reservation_status || '-' }}</span></td>
              <td class="table-actions">
                <button class="act-btn del" @click="deleteClient(client.id)" title="Supprimer"><i class="fas fa-trash"></i></button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="activeTab === 'messages'" class="crm-section">
      <div class="messaging-container">
        <div class="msg-sidebar card">
          <div class="sidebar-filter">
            <select v-model="messageFilter">
              <option value="all">Toutes categories</option>
              <option value="Devis">Devis</option>
              <option value="Info">Information</option>
              <option value="Reclamation">Reclamation</option>
            </select>
          </div>
          <div class="msg-list">
            <div v-for="msg in filteredMessages" :key="msg.id" class="msg-item" :class="{ unread: msg.statut === 'non_lu' }" @click="selectMessage(msg)">
              <div class="msg-item-header">
                <strong>{{ msg.client }}</strong>
                <small>{{ msg.dateLabel }}</small>
              </div>
              <p class="msg-item-sub">{{ msg.sujet }}</p>
              <span class="cat-pill" :class="msg.cat.toLowerCase()">{{ msg.cat }}</span>
            </div>
          </div>
        </div>
        <div class="msg-content card">
          <div v-if="!selectedMessage" class="empty-state">
            <i class="fas fa-envelope-open-text"></i>
            <p>Selectionnez une conversation pour l'afficher</p>
          </div>
          <div v-else>
            <div class="msg-view-header">
              <div class="msg-title">
                <h2>{{ selectedMessage.sujet }}</h2>
                <span>De: {{ selectedMessage.client }} ({{ selectedMessage.email }})</span>
              </div>
              <div class="msg-header-actions">
                <button class="btn btn-delete" @click="deleteMessage"><i class="fas fa-trash"></i></button>
              </div>
            </div>
            <div class="msg-body">{{ selectedMessage.corps }}</div>
            <div class="msg-history">
              <div v-for="(reply, index) in selectedMessage.reponses" :key="index" class="history-item">
                <strong>Reponse Admin:</strong><p>{{ reply }}</p>
              </div>
            </div>
            <div class="msg-reply-box">
              <textarea v-model="replyContent" placeholder="Ecrire une reponse..."></textarea>
              <div class="reply-actions">
                <button class="btn btn-save" @click="submitReply">Envoyer <i class="fas fa-paper-plane"></i></button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Client',
  data() {
    return {
      activeTab: 'directory',
      searchQuery: '',
      messageFilter: 'all',
      selectedMessage: null,
      replyContent: '',
      clients: [],
      messages: [],
    };
  },
  computed: {
    filteredClients() {
      if (!this.searchQuery) return this.clients;
      const query = this.searchQuery.toLowerCase();
      return this.clients.filter((client) =>
        client.nom.toLowerCase().includes(query)
        || client.email.toLowerCase().includes(query)
        || client.prenom.toLowerCase().includes(query)
      );
    },
    filteredMessages() {
      if (this.messageFilter === 'all') return this.messages;
      return this.messages.filter((message) => message.cat === this.messageFilter);
    },
    unreadCount() {
      return this.messages.filter((message) => message.statut === 'non_lu').length;
    },
    recentClientsCount() {
      const limit = new Date();
      limit.setDate(limit.getDate() - 30);
      return this.clients.filter((client) => client.date && new Date(client.date) >= limit).length;
    },
    loyaltyRate() {
      if (!this.clients.length) return 0;
      const loyalClients = this.clients.filter((client) => ['VIP', 'Premium', 'Gold'].includes((client.niveau || '').trim()));
      return Math.round((loyalClients.length / this.clients.length) * 100);
    },
  },
  async mounted() {
    await this.loadClients();
    await this.loadMessages();
  },
  methods: {
    getAuthHeaders(json = false) {
      const token = localStorage.getItem('access_token');
      const headers = {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json',
      };

      if (json) {
        headers['Content-Type'] = 'application/json';
      }

      return headers;
    },
    formatDate(date) {
      if (!date) return '-';
      return new Date(date).toLocaleDateString('fr-FR');
    },
    async loadClients() {
      try {
        const res = await fetch('/api/clients', {
          headers: this.getAuthHeaders(),
        });
        if (!res.ok) throw new Error('Erreur chargement clients');
        const data = await res.json();
        this.clients = data;
      } catch (err) {
        console.error(err);
      }
    },
    async loadMessages() {
      try {
        const res = await fetch('/api/contacts', {
          headers: this.getAuthHeaders(),
        });
        if (!res.ok) throw new Error('Erreur chargement messages');
        const data = await res.json();
        this.messages = data.map((item) => ({
          id: item.id,
          client: item.name,
          email: item.email,
          sujet: item.subject || 'Message contact',
          date: item.created_at || item.updated_at || '',
          dateLabel: this.formatDate(item.created_at || item.updated_at || ''),
          statut: item.status || 'non_lu',
          cat: item.category || 'Info',
          corps: item.message,
          reponses: Array.isArray(item.admin_replies)
            ? item.admin_replies.map((reply) => typeof reply === 'string' ? reply : reply.message)
            : [],
        }));
      } catch (err) {
        console.error(err);
      }
    },
async deleteClient(id) {
      if (!confirm('Supprimer définitivement ce client ?')) return;
      try {
        const res = await fetch(`/api/clients/${id}`, {
          method: 'DELETE',
          headers: this.getAuthHeaders(),
        });
        if (!res.ok) throw new Error(`Erreur HTTP ${res.status}: Impossible de supprimer`);
        this.clients = this.clients.filter((client) => client.id !== id);
        alert('Client supprimé avec succès');
      } catch (err) {
        console.error(err);
        alert('Erreur lors de la suppression: ' + err.message);
      }
    },
    async selectMessage(msg) {
      this.selectedMessage = msg;

      if (msg.statut !== 'non_lu') {
        return;
      }

      msg.statut = 'lu';

      try {
        await fetch(`/api/contacts/${msg.id}/status`, {
          method: 'PATCH',
          headers: this.getAuthHeaders(true),
          body: JSON.stringify({ status: 'lu' }),
        });
      } catch (err) {
        console.error('Erreur statut message:', err);
      }
    },
    async deleteMessage() {
      if (!this.selectedMessage) return;
      if (!confirm('Supprimer ce message ?')) return;

      try {
        const res = await fetch(`/api/contacts/${this.selectedMessage.id}`, {
          method: 'DELETE',
          headers: this.getAuthHeaders(),
        });
        if (!res.ok) throw new Error('Impossible de supprimer le message');

        this.messages = this.messages.filter((message) => message.id !== this.selectedMessage.id);
        this.selectedMessage = null;
      } catch (err) {
        console.error(err);
        alert('Erreur lors de la suppression du message: ' + err.message);
      }
    },
    async submitReply() {
      if (!this.replyContent || !this.selectedMessage) return;

      try {
        const res = await fetch(`/api/contacts/${this.selectedMessage.id}/reply`, {
          method: 'POST',
          headers: this.getAuthHeaders(true),
          body: JSON.stringify({ reply: this.replyContent }),
        });

        if (!res.ok) throw new Error('Erreur envoi reponse');

        const result = await res.json();
        if (result.success) {
          this.selectedMessage.reponses.push(this.replyContent);
          this.selectedMessage.statut = 'repondu';
          this.replyContent = '';
          alert('Reponse envoyee avec succes !');
        } else {
          throw new Error(result.message || 'Erreur inconnue');
        }
      } catch (err) {
        console.error('Erreur envoi reponse:', err);
        alert('Erreur lors de l envoi de la reponse: ' + err.message);
      }
    },
  },
};
</script>

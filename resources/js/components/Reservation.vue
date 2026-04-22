<template>
  <div class="card">
    <div class="card-header">
      <h3>Liste des Reservations</h3>
      <button class="btn btn-primary" @click="fetchReservations" :disabled="loading">
        <i class="fas fa-sync"></i> Actualiser
      </button>
    </div>

    <div v-if="loading" class="loading-state">
      <i class="fas fa-spinner fa-spin"></i> Chargement des reservations...
    </div>

    <div v-else-if="error" class="error-state">
      <i class="fas fa-exclamation-triangle"></i> {{ error }}
    </div>

    <div v-else-if="reservations.length === 0" class="empty-state">
      <i class="fas fa-calendar-times"></i>
      <p>Aucune reservation trouvee</p>
    </div>

    <div v-else class="table-container">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Circuit</th>
            <th>Client</th>
            <th>Telephone</th>
            <th>Voyageurs</th>
            <th>Places</th>
            <th>Statut</th>
            <th>Date de creation</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="reservation in reservations"
            :key="reservation.id"
            :class="{ 'accepted-row': reservation.statut === 'acceptee' }"
            class="reservation-row"
            @click="viewReservation(reservation)"
          >
            <td>#{{ reservation.id }}</td>
            <td>{{ reservation.circuit?.destination || reservation.circuit_nom || reservation.circuit?.nom || 'Non renseigne' }}</td>
            <td>
              <strong>{{ reservation.nom }}</strong><br>
              <small>{{ reservation.email }}</small>
            </td>
            <td>{{ reservation.telephone }}</td>
            <td>{{ reservation.voyageurs }}</td>
            <td>{{ reservation.places || 'Non specifiees' }}</td>
            <td>
              <span class="status-badge" :class="statusClass(reservation.statut)">
                {{ statusLabel(reservation.statut) }}
              </span>
            </td>
            <td>{{ formatDate(reservation.created_at) }}</td>
            <td>
              <button
                class="icon-action icon-accept"
                title="Accepter"
                aria-label="Accepter"
                :disabled="reservation.statut === 'acceptee' || actionLoadingId === reservation.id"
                @click.stop="updateReservationStatus(reservation, 'acceptee')"
              >
                <i class="fas fa-check"></i>
              </button>
              <button class="icon-action icon-delete" title="Supprimer" aria-label="Supprimer" @click.stop="deleteReservation(reservation.id)">
                <i class="fas fa-trash"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Reservation',
  data() {
    return {
      reservations: [],
      loading: false,
      error: null,
      actionLoadingId: null,
    };
  },
  mounted() {
    this.fetchReservations();
  },
  methods: {
    getAuthHeaders() {
      const token = localStorage.getItem('access_token');
      return token ? {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      } : {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      };
    },

    async fetchReservations() {
      this.loading = true;
      this.error = null;

      try {
        const token = localStorage.getItem('access_token');
        if (!token) {
          throw new Error('Non autorise : token admin manquant. Veuillez vous connecter.');
        }

        const response = await fetch('/api/reservations', {
          headers: this.getAuthHeaders(),
        });

        if (!response.ok) {
          if (response.status === 401) {
            throw new Error('Non autorise : token invalide ou expire. Veuillez vous reconnecter.');
          }
          throw new Error('Erreur lors du chargement des reservations');
        }

        const data = await response.json();
        this.reservations = Array.isArray(data) ? data : (data.data || []);
      } catch (error) {
        console.error('Erreur:', error);
        this.error = error.message;
      } finally {
        this.loading = false;
      }
    },

    formatDate(dateString) {
      if (!dateString) return '-';
      return new Date(dateString).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
      });
    },

    viewReservation(reservation) {
      alert(
        `Details de la reservation #${reservation.id}:\n\n` +
        `Circuit: ${reservation.circuit_nom || reservation.circuit?.nom || 'Non renseigne'}\n` +
        `Destination: ${reservation.circuit?.destination || 'Non renseignee'}\n` +
        `Slug: ${reservation.circuit?.slug || 'Non renseigne'}\n` +
        `Statut: ${this.statusLabel(reservation.statut)}\n` +
        `Nom: ${reservation.nom}\n` +
        `Email: ${reservation.email}\n` +
        `Telephone: ${reservation.telephone}\n` +
        `Voyageurs: ${reservation.voyageurs}\n` +
        `Places: ${reservation.places || 'Non specifiees'}`
      );
    },

    statusLabel(status) {
      return {
        en_attente: 'En attente',
        acceptee: 'Acceptee',
        refusee: 'Refusee',
      }[status] || 'En attente';
    },

    statusClass(status) {
      return {
        pending: status === 'en_attente' || !status,
        accepted: status === 'acceptee',
        refused: status === 'refusee',
      };
    },

    async updateReservationStatus(reservation, status) {
      this.actionLoadingId = reservation.id;

      try {
        const token = localStorage.getItem('access_token');
        if (!token) {
          throw new Error('Non autorise : token admin manquant. Veuillez vous connecter.');
        }

        const response = await fetch(`/api/reservations/${reservation.id}`, {
          method: 'PUT',
          headers: {
            ...this.getAuthHeaders(),
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          },
          body: JSON.stringify({ statut: status }),
        });

        if (!response.ok) {
          const errorData = await response.json().catch(() => ({}));
          if (response.status === 401) {
            throw new Error('Non autorise : token invalide ou expire. Veuillez vous reconnecter.');
          }
          const errors = errorData.errors ? Object.values(errorData.errors).flat().join(', ') : '';
          throw new Error(errors || 'Mise a jour impossible.');
        }

        await this.fetchReservations();
      } catch (error) {
        alert(error.message);
      } finally {
        this.actionLoadingId = null;
      }
    },

    async deleteReservation(id) {
      if (!confirm('Etes-vous sur de vouloir supprimer cette reservation ?')) {
        return;
      }

      try {
        const token = localStorage.getItem('access_token');
        if (!token) {
          throw new Error('Non autorise : token admin manquant. Veuillez vous connecter.');
        }

        const response = await fetch(`/api/reservations/${id}`, {
          method: 'DELETE',
          headers: {
            ...this.getAuthHeaders(),
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          },
        });

        if (!response.ok) {
          if (response.status === 401) {
            throw new Error('Non autorise : token invalide ou expire. Veuillez vous reconnecter.');
          }
          throw new Error('Erreur lors de la suppression');
        }

        await this.fetchReservations();
        alert('Reservation supprimee avec succes');
      } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors de la suppression: ' + error.message);
      }
    },
  },
};
</script>

<style scoped>
.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.loading-state, .error-state, .empty-state {
  text-align: center;
  padding: 40px;
  color: #666;
}

.loading-state i, .error-state i, .empty-state i {
  font-size: 48px;
  margin-bottom: 20px;
  display: block;
}

.error-state {
  color: #dc3545;
}

.empty-state {
  color: #6c757d;
}

.table-container {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #dee2e6;
}

th {
  background-color: #f8f9fa;
  font-weight: 600;
}

.btn {
  padding: 6px 12px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin-right: 5px;
  font-size: 14px;
}

.icon-action {
  border: none;
  background: transparent;
  padding: 4px 6px;
  margin-right: 8px;
  font-size: 16px;
  cursor: pointer;
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.icon-action:hover:not(:disabled) {
  opacity: 0.75;
  transform: translateY(-1px);
}

.reservation-row {
  cursor: pointer;
}

.reservation-row:hover {
  background-color: rgba(0, 0, 0, 0.02);
}

.icon-accept {
  color: #198754;
}

.icon-delete {
  color: #dc3545;
}

.btn-primary {
  background-color: #28a745;
  color: white;
  padding: 8px 16px;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.fa-spin {
  animation: spin 1s linear infinite;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
}

.status-badge.pending {
  background: #fff3cd;
  color: #856404;
}

.status-badge.accepted {
  background: #dcfce7;
  color: #166534;
}

.status-badge.refused {
  background: #e7edf3;
  color: #445463;
}

.accepted-row {
  background-color: rgba(23, 50, 77, 0.08);
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>

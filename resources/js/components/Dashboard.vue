<template>
  <div class="dashboard-shell">
    <div class="dashboard-head">
      <div>
        <p class="dash-eyebrow">Vue generale</p>
        <h3>Tableau de bord administrateur</h3>
        <span class="dash-subtitle">Un apercu vivant de l'activite du site, des circuits et des reservations.</span>
      </div>

      <button class="refresh-btn" @click="loadDashboard" :disabled="loading">
        <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-rotate-right'"></i>
        <span>{{ loading ? 'Actualisation...' : 'Actualiser' }}</span>
      </button>
    </div>

    <div v-if="error" class="dash-alert error">
      <i class="fas fa-exclamation-triangle"></i>
      <span>{{ error }}</span>
    </div>

    <div class="kpi-grid">
      <article class="kpi-card primary">
        <span class="kpi-label">Reservations</span>
        <strong>{{ reservations.length }}</strong>
        <p>{{ pendingReservations }} en attente de traitement</p>
      </article>
      <article class="kpi-card success">
        <span class="kpi-label">Clients</span>
        <strong>{{ clients.length }}</strong>
        <p>{{ recentClientsCount }} nouveaux ces 30 derniers jours</p>
      </article>
      <article class="kpi-card amber">
        <span class="kpi-label">Circuits</span>
        <strong>{{ circuits.length }}</strong>
        <p>{{ publishedCircuits }} publies ou actifs</p>
      </article>
      <article class="kpi-card slate">
        <span class="kpi-label">Messages</span>
        <strong>{{ contacts.length }}</strong>
        <p>{{ contactsToday }} recus aujourd'hui</p>
      </article>
    </div>

    <div class="dashboard-grid">
      <section class="card chart-card">
        <div class="section-head">
          <div>
            <h4>Reservations par mois</h4>
            <span>Evolution sur les six derniers mois</span>
          </div>
        </div>

        <div v-if="loading" class="chart-state">Chargement...</div>
        <div v-else class="line-chart">
          <svg viewBox="0 0 420 220" preserveAspectRatio="none" class="chart-svg">
            <line
              v-for="(line, index) in 4"
              :key="index"
              x1="36"
              :y1="40 + (index * 40)"
              x2="392"
              :y2="40 + (index * 40)"
              class="grid-line"
            />
            <polyline
              :points="lineChartPoints"
              class="line-path"
            />
            <circle
              v-for="point in lineChartDots"
              :key="point.label"
              :cx="point.x"
              :cy="point.y"
              r="5"
              class="line-dot"
            />
            <text
              v-for="point in lineChartDots"
              :key="`${point.label}-text`"
              :x="point.x"
              y="208"
              text-anchor="middle"
              class="axis-label"
            >
              {{ point.label }}
            </text>
          </svg>
        </div>
      </section>

      <section class="card chart-card">
        <div class="section-head">
          <div>
            <h4>Statut des reservations</h4>
            <span>Repartition actuelle</span>
          </div>
        </div>

        <div class="status-chart">
          <div class="donut-wrap">
            <svg viewBox="0 0 120 120" class="donut-chart">
              <circle cx="60" cy="60" r="42" class="donut-base" />
              <circle cx="60" cy="60" r="42" class="donut-segment pending-segment" :stroke-dasharray="donutPending" stroke-dashoffset="0" />
              <circle cx="60" cy="60" r="42" class="donut-segment accepted-segment" :stroke-dasharray="donutAccepted" :stroke-dashoffset="donutAcceptedOffset" />
              <circle cx="60" cy="60" r="42" class="donut-segment refused-segment" :stroke-dasharray="donutRefused" :stroke-dashoffset="donutRefusedOffset" />
            </svg>
            <div class="donut-center">
              <strong>{{ reservations.length }}</strong>
              <span>Total</span>
            </div>
          </div>

          <div class="legend-list">
            <div class="legend-item">
              <span class="legend-dot pending"></span>
              <span>En attente</span>
              <strong>{{ pendingReservations }}</strong>
            </div>
            <div class="legend-item">
              <span class="legend-dot accepted"></span>
              <span>Acceptees</span>
              <strong>{{ acceptedReservations }}</strong>
            </div>
            <div class="legend-item">
              <span class="legend-dot refused"></span>
              <span>Refusees</span>
              <strong>{{ refusedReservations }}</strong>
            </div>
          </div>
        </div>
      </section>
    </div>

    <div class="dashboard-grid lower">
      <section class="card chart-card">
        <div class="section-head">
          <div>
            <h4>Circuits par type</h4>
            <span>Catalogue actuellement gere</span>
          </div>
        </div>

        <div class="bars-list">
          <div v-for="item in circuitTypeBreakdown" :key="item.label" class="bar-item">
            <div class="bar-copy">
              <strong>{{ item.label }}</strong>
              <span>{{ item.value }} circuit(s)</span>
            </div>
            <div class="bar-track">
              <div class="bar-fill" :style="{ width: `${item.percent}%` }"></div>
            </div>
          </div>
        </div>
      </section>

      <section class="card chart-card">
        <div class="section-head">
          <div>
            <h4>Activite recente</h4>
            <span>Dernieres tendances detectees</span>
          </div>
        </div>

        <div class="activity-list">
          <article class="activity-item">
            <i class="fas fa-calendar-check activity-icon"></i>
            <div>
              <strong>{{ latestReservationLabel }}</strong>
              <span>Derniere reservation enregistree</span>
            </div>
          </article>
          <article class="activity-item">
            <i class="fas fa-envelope activity-icon"></i>
            <div>
              <strong>{{ latestContactLabel }}</strong>
              <span>Dernier message recu</span>
            </div>
          </article>
          <article class="activity-item">
            <i class="fas fa-map-location-dot activity-icon"></i>
            <div>
              <strong>{{ topDestinationLabel }}</strong>
              <span>Destination la plus representee</span>
            </div>
          </article>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Dashboard',
  data() {
    return {
      reservations: [],
      clients: [],
      contacts: [],
      circuits: [],
      loading: false,
      error: '',
    };
  },
  computed: {
    pendingReservations() {
      return this.reservations.filter((item) => item.statut === 'en_attente' || !item.statut).length;
    },
    acceptedReservations() {
      return this.reservations.filter((item) => item.statut === 'acceptee').length;
    },
    refusedReservations() {
      return this.reservations.filter((item) => item.statut === 'refusee').length;
    },
    publishedCircuits() {
      return this.circuits.filter((item) => ['Publie', 'Actif'].includes(item.statut)).length;
    },
    recentClientsCount() {
      const limit = new Date();
      limit.setDate(limit.getDate() - 30);
      return this.clients.filter((item) => item.date && new Date(item.date) >= limit).length;
    },
    contactsToday() {
      const today = new Date().toLocaleDateString('fr-FR');
      return this.contacts.filter((item) => item.created_at && new Date(item.created_at).toLocaleDateString('fr-FR') === today).length;
    },
    reservationByMonth() {
      const formatter = new Intl.DateTimeFormat('fr-FR', { month: 'short' });
      const today = new Date();
      const months = [];

      for (let i = 5; i >= 0; i -= 1) {
        const date = new Date(today.getFullYear(), today.getMonth() - i, 1);
        const key = `${date.getFullYear()}-${date.getMonth()}`;
        months.push({
          key,
          label: formatter.format(date),
          value: 0,
        });
      }

      this.reservations.forEach((item) => {
        if (!item.created_at) return;
        const date = new Date(item.created_at);
        const key = `${date.getFullYear()}-${date.getMonth()}`;
        const found = months.find((month) => month.key === key);
        if (found) {
          found.value += 1;
        }
      });

      return months;
    },
    lineChartDots() {
      const max = Math.max(...this.reservationByMonth.map((item) => item.value), 1);

      return this.reservationByMonth.map((item, index) => {
        const x = 36 + ((356 / Math.max(this.reservationByMonth.length - 1, 1)) * index);
        const y = 180 - ((item.value / max) * 120);
        return { x, y, label: item.label };
      });
    },
    lineChartPoints() {
      return this.lineChartDots.map((point) => `${point.x},${point.y}`).join(' ');
    },
    donutCircumference() {
      return 2 * Math.PI * 42;
    },
    donutPending() {
      return `${(this.pendingReservations / Math.max(this.reservations.length, 1)) * this.donutCircumference} ${this.donutCircumference}`;
    },
    donutAccepted() {
      return `${(this.acceptedReservations / Math.max(this.reservations.length, 1)) * this.donutCircumference} ${this.donutCircumference}`;
    },
    donutRefused() {
      return `${(this.refusedReservations / Math.max(this.reservations.length, 1)) * this.donutCircumference} ${this.donutCircumference}`;
    },
    donutAcceptedOffset() {
      return -((this.pendingReservations / Math.max(this.reservations.length, 1)) * this.donutCircumference);
    },
    donutRefusedOffset() {
      return -(((this.pendingReservations + this.acceptedReservations) / Math.max(this.reservations.length, 1)) * this.donutCircumference);
    },
    circuitTypeBreakdown() {
      const counts = this.circuits.reduce((accumulator, item) => {
        const label = item.type || 'Sans type';
        accumulator[label] = (accumulator[label] || 0) + 1;
        return accumulator;
      }, {});

      const max = Math.max(...Object.values(counts), 1);

      return Object.entries(counts).map(([label, value]) => ({
        label,
        value,
        percent: (value / max) * 100,
      }));
    },
    latestReservationLabel() {
      const latest = this.reservations[0];
      if (!latest) return 'Aucune reservation';
      return `${latest.nom} - ${latest.circuit_nom || latest.circuit?.nom || 'Circuit'}`;
    },
    latestContactLabel() {
      const latest = this.contacts[0];
      if (!latest) return 'Aucun message';
      return `${latest.name} - ${latest.email}`;
    },
    topDestinationLabel() {
      const counts = {};
      this.circuits.forEach((item) => {
        const key = item.destination || 'Non renseignee';
        counts[key] = (counts[key] || 0) + 1;
      });
      const top = Object.entries(counts).sort((a, b) => b[1] - a[1])[0];
      return top ? top[0] : 'Aucune destination';
    },
  },
  mounted() {
    this.loadDashboard();
  },
  methods: {
    getAuthHeaders() {
      const token = localStorage.getItem('access_token');
      return {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json',
      };
    },
    async loadDashboard() {
      this.loading = true;
      this.error = '';

      try {
        const [reservationsRes, clientsRes, contactsRes, circuitsRes] = await Promise.all([
          fetch('/api/reservations', { headers: this.getAuthHeaders() }),
          fetch('/api/clients', { headers: this.getAuthHeaders() }),
          fetch('/api/contacts', { headers: this.getAuthHeaders() }),
          fetch('/api/circuits', { headers: this.getAuthHeaders() }),
        ]);

        if ([reservationsRes, clientsRes, contactsRes, circuitsRes].some((response) => !response.ok)) {
          throw new Error('Impossible de charger certaines donnees du tableau de bord.');
        }

        const [reservations, clients, contacts, circuits] = await Promise.all([
          reservationsRes.json(),
          clientsRes.json(),
          contactsRes.json(),
          circuitsRes.json(),
        ]);

        this.reservations = Array.isArray(reservations) ? reservations : (reservations.data || []);
        this.clients = Array.isArray(clients) ? clients : (clients.data || []);
        this.contacts = Array.isArray(contacts) ? contacts : (contacts.data || []);
        this.circuits = Array.isArray(circuits) ? circuits : (circuits.data || []);
      } catch (error) {
        console.error(error);
        this.error = error.message || 'Erreur lors du chargement du tableau de bord.';
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
.dashboard-shell {
  display: flex;
  flex-direction: column;
  gap: 22px;
}

.dashboard-head {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 18px;
}

.dash-eyebrow {
  margin: 0 0 8px;
  font-size: 0.72rem;
  text-transform: uppercase;
  letter-spacing: 0.18em;
  color: #6b7a86;
}

.dashboard-head h3 {
  margin: 0;
  font-size: 1.55rem;
  color: #17324d;
}

.dash-subtitle {
  display: block;
  margin-top: 8px;
  color: #667985;
}

.refresh-btn {
  border: none;
  border-radius: 14px;
  padding: 12px 18px;
  background: linear-gradient(135deg, #3f8f63, #57a97a);
  color: white;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  box-shadow: 0 14px 26px rgba(63, 143, 99, 0.18);
}

.refresh-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.dash-alert {
  padding: 14px 16px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.dash-alert.error {
  color: #9f2332;
  background: #fde8ea;
  border: 1px solid #f7c7cd;
}

.kpi-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 18px;
}

.kpi-card {
  padding: 22px;
  border-radius: 24px;
  color: white;
  box-shadow: 0 18px 36px rgba(23, 50, 77, 0.12);
}

.kpi-card strong {
  display: block;
  margin: 10px 0 8px;
  font-size: 2rem;
}

.kpi-card p,
.kpi-label {
  margin: 0;
  color: rgba(255, 255, 255, 0.86);
}

.kpi-card.primary {
  background: linear-gradient(135deg, #17324d, #29537a);
}

.kpi-card.success {
  background: linear-gradient(135deg, #3f8f63, #60ae81);
}

.kpi-card.amber {
  background: linear-gradient(135deg, #b9821c, #ddb056);
}

.kpi-card.slate {
  background: linear-gradient(135deg, #485866, #758696);
}

.dashboard-grid {
  display: grid;
  grid-template-columns: 1.3fr 1fr;
  gap: 20px;
}

.dashboard-grid.lower {
  grid-template-columns: 1fr 1fr;
}

.chart-card {
  padding: 22px;
}

.section-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  margin-bottom: 18px;
}

.section-head h4 {
  margin: 0;
  color: #17324d;
}

.section-head span {
  color: #718491;
  font-size: 0.9rem;
}

.chart-state {
  min-height: 220px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #718491;
}

.line-chart {
  height: 220px;
}

.chart-svg {
  width: 100%;
  height: 100%;
}

.grid-line {
  stroke: rgba(23, 50, 77, 0.1);
  stroke-width: 1;
}

.line-path {
  fill: none;
  stroke: #3f8f63;
  stroke-width: 4;
  stroke-linecap: round;
  stroke-linejoin: round;
}

.line-dot {
  fill: #17324d;
  stroke: white;
  stroke-width: 3;
}

.axis-label {
  fill: #718491;
  font-size: 12px;
}

.status-chart {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 22px;
  min-height: 220px;
}

.donut-wrap {
  position: relative;
  width: 170px;
  height: 170px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.donut-chart {
  width: 170px;
  height: 170px;
  transform: rotate(-90deg);
}

.donut-base,
.donut-segment {
  fill: none;
  stroke-width: 12;
}

.donut-base {
  stroke: rgba(23, 50, 77, 0.08);
}

.pending-segment {
  stroke: #e0a11a;
}

.accepted-segment {
  stroke: #3f8f63;
}

.refused-segment {
  stroke: #d65561;
}

.donut-center {
  position: absolute;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.donut-center strong {
  font-size: 1.8rem;
  color: #17324d;
}

.donut-center span {
  color: #718491;
}

.legend-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
  flex: 1;
}

.legend-item {
  display: grid;
  grid-template-columns: auto 1fr auto;
  align-items: center;
  gap: 12px;
  padding: 12px 14px;
  border-radius: 16px;
  background: #f7faf9;
}

.legend-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
}

.legend-dot.pending {
  background: #e0a11a;
}

.legend-dot.accepted {
  background: #3f8f63;
}

.legend-dot.refused {
  background: #d65561;
}

.bars-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.bar-item {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.bar-copy {
  display: flex;
  justify-content: space-between;
  gap: 12px;
}

.bar-copy strong {
  color: #17324d;
}

.bar-copy span {
  color: #718491;
}

.bar-track {
  width: 100%;
  height: 12px;
  border-radius: 999px;
  background: rgba(23, 50, 77, 0.08);
  overflow: hidden;
}

.bar-fill {
  height: 100%;
  border-radius: inherit;
  background: linear-gradient(135deg, #17324d, #3f8f63);
}

.activity-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.activity-item {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  padding: 16px;
  border-radius: 18px;
  background: #f7faf9;
  border: 1px solid rgba(23, 50, 77, 0.07);
}

.activity-item strong {
  display: block;
  color: #17324d;
  margin-bottom: 4px;
}

.activity-item span {
  color: #718491;
}

.activity-icon {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  background: rgba(63, 143, 99, 0.12);
  color: #3f8f63;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

@media (max-width: 1200px) {
  .kpi-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .dashboard-grid,
  .dashboard-grid.lower {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .dashboard-head {
    flex-direction: column;
  }

  .kpi-grid {
    grid-template-columns: 1fr;
  }

  .status-chart {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>

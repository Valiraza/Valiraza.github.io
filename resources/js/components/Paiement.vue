<template>
  <div class="paiement-container">
    <div class="paiement-header">
      <div class="tabs">
        <button class="tab-btn" :class="{ active: activeTab === 'list' }" @click="activeTab = 'list'">
          <i class="fas fa-list"></i> Historique des paiements
        </button>
        <button class="tab-btn" :class="{ active: activeTab === 'stats' }" @click="activeTab = 'stats'">
          <i class="fas fa-chart-bar"></i> Statistiques
        </button>
      </div>
      <div class="search-filter">
        <input type="text" v-model="searchQuery" placeholder="Filtrer par client...">
      </div>
    </div>

    <div v-if="activeTab === 'list'" class="card">
      <div class="table-responsive">
        <table id="tablePaiements">
          <thead>
            <tr>
              <th>ID Transaction</th>
              <th>Client</th>
              <th>Montant</th>
              <th>Méthode</th>
              <th>Date</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="paiement in filteredPaiements" :key="paiement.id">
              <td><strong>{{ paiement.transactionId }}</strong></td>
              <td>{{ paiement.client }}</td>
              <td class="montant">{{ paiement.montant }} €</td>
              <td><small>{{ paiement.methode }}</small></td>
              <td>{{ paiement.date }}</td>
              <td><span class="badge" :class="paiement.statut === 'Confirmé' ? 'bg-success' : 'bg-gray'">{{ paiement.statut }}</span></td>
              <td>
                <div class="action-btns">
                  <button class="icon-btn info" title="Reçu"><i class="fas fa-receipt"></i></button>
                  <button class="icon-btn edit" title="Voir détails"><i class="fas fa-eye"></i></button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="activeTab === 'stats'" class="card">
      <h3>Statistiques Financières</h3>
      <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 20px;">
        <div style="background: var(--primary-dark); color: white; padding: 20px; border-radius: 10px;">
          <h4>Total Encaissé</h4>
          <p style="font-size: 1.8rem; font-weight: bold;">{{ totalEncaisse }} €</p>
          <small>Tous les paiements confirmés</small>
        </div>
        <div style="background: var(--accent-green); color: white; padding: 20px; border-radius: 10px;">
          <h4>Paiements Mois</h4>
          <p style="font-size: 1.8rem; font-weight: bold;">{{ paiementsMois }}</p>
          <small>Mars 2026</small>
        </div>
        <div style="background: #3498db; color: white; padding: 20px; border-radius: 10px;">
          <h4>Montant Moyen</h4>
          <p style="font-size: 1.8rem; font-weight: bold;">{{ montantMoyen }} €</p>
          <small>Par transaction</small>
        </div>
      </div>

      <div style="margin-top: 30px; padding: 20px; background: #f8fafc; border-radius: 10px;">
        <h4>Summary Méthodes de Paiement</h4>
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-top: 15px;">
          <div style="border-left: 4px solid var(--primary-dark); padding: 15px;">
            <p style="color: #64748b; font-size: 0.9rem;">Carte Bancaire</p>
            <p style="font-size: 1.5rem; font-weight: bold;">45 transactions</p>
          </div>
          <div style="border-left: 4px solid var(--accent-green); padding: 15px;">
            <p style="color: #64748b; font-size: 0.9rem;">Virement</p>
            <p style="font-size: 1.5rem; font-weight: bold;">12 transactions</p>
          </div>
          <div style="border-left: 4px solid #3498db; padding: 15px;">
            <p style="color: #64748b; font-size: 0.9rem;">Mobile Money</p>
            <p style="font-size: 1.5rem; font-weight: bold;">8 transactions</p>
          </div>
          <div style="border-left: 4px solid #f39c12; padding: 15px;">
            <p style="color: #64748b; font-size: 0.9rem;">Espèces</p>
            <p style="font-size: 1.5rem; font-weight: bold;">5 transactions</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Paiement',
  data() {
    return {
      activeTab: 'list',
      searchQuery: '',
      paiements: [
        { id: 1, transactionId: "TRX20260320001", client: "Andry RAKOTO", montant: 2400, methode: "Carte Bancaire", date: "2026-03-20", statut: "Confirmé" },
        { id: 2, transactionId: "TRX20260319001", client: "Sophie MARTIN", montant: 850, methode: "Virement", date: "2026-03-19", statut: "Confirmé" },
        { id: 3, transactionId: "TRX20260318001", client: "Jean DUPONT", montant: 1200, methode: "Mobile Money", date: "2026-03-18", statut: "Confirmé" }
      ],
    };
  },
  computed: {
    filteredPaiements() {
      if (!this.searchQuery) return this.paiements;
      return this.paiements.filter(p => p.client.toLowerCase().includes(this.searchQuery.toLowerCase()));
    },
    totalEncaisse() {
      return this.paiements.reduce((sum, p) => sum + p.montant, 0);
    },
    paiementsMois() {
      return this.paiements.length;
    },
    montantMoyen() {
      const total = this.totalEncaisse;
      return (total / this.paiements.length).toFixed(2);
    },
  },
};
</script>

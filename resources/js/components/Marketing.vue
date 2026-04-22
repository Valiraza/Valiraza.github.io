<template>
  <div class="marketing-container">
    <div class="marketing-header">
      <div class="tabs">
        <button class="tab-btn" :class="{ active: activeTab === 'campaigns' }" @click="activeTab = 'campaigns'">
          <i class="fas fa-bullhorn"></i> Campagnes
        </button>
        <button class="tab-btn" :class="{ active: activeTab === 'newsletter' }" @click="activeTab = 'newsletter'">
          <i class="fas fa-envelope"></i> Newsletter
        </button>
        <button class="tab-btn" :class="{ active: activeTab === 'analytics' }" @click="activeTab = 'analytics'">
          <i class="fas fa-chart-line"></i> Analytique
        </button>
      </div>
      <div class="search-filter">
        <input type="text" v-model="searchQuery" placeholder="Filtrer...">
      </div>
    </div>

    <!-- CAMPAIGNS TAB -->
    <div v-if="activeTab === 'campaigns'" class="card">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3>Campagnes Marketing</h3>
        <button class="btn btn-save" @click="showCampaignForm = true"><i class="fas fa-plus"></i> Nouvelle</button>
      </div>
      <table class="modern-table">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Type</th>
            <th>Début</th>
            <th>Fin</th>
            <th>Audience</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="campaign in filteredCampaigns" :key="campaign.id">
            <td><strong>{{ campaign.nom }}</strong></td>
            <td>{{ campaign.type }}</td>
            <td><small>{{ campaign.debut }}</small></td>
            <td><small>{{ campaign.fin }}</small></td>
            <td>{{ campaign.audience }} personnes</td>
            <td><span class="badge" :class="campaign.statut === 'Actif' ? 'bg-success' : 'bg-gray'">{{ campaign.statut }}</span></td>
            <td>
              <button class="icon-btn info"><i class="fas fa-chart-line"></i></button>
              <button class="icon-btn edit"><i class="fas fa-edit"></i></button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- NEWSLETTER TAB -->
    <div v-if="activeTab === 'newsletter'" class="card">
      <h3>Gestion Newsletter</h3>
      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-top: 20px;">
        <div>
          <h4 style="margin-bottom: 15px;">Envoyer une newsletter</h4>
          <div class="form-group">
            <label>Sujet</label>
            <input type="text" v-model="newsletter.sujet" placeholder="Objet de l'email...">
          </div>
          <div class="form-group">
            <label>Contenu</label>
            <textarea v-model="newsletter.contenu" rows="6" placeholder="Contenu de la newsletter..."></textarea>
          </div>
          <div class="form-group">
            <label>Audience</label>
            <select v-model="newsletter.audience">
              <option value="all">Tous les clients</option>
              <option value="vip">Clients VIP</option>
              <option value="recent">Clients récents</option>
            </select>
          </div>
          <button class="btn btn-save" @click="sendNewsletter"><i class="fas fa-paper-plane"></i> Envoyer</button>
        </div>
        <div style="background: #f8fafc; padding: 20px; border-radius: 10px;">
          <h4>Statistiques</h4>
          <div style="margin-top: 15px;">
            <p style="color: #64748b; margin-bottom: 10px;">
              <strong style="color: var(--text-main);">{{ newsletters.length }}</strong> newsletters envoyées
            </p>
            <p style="color: #64748b; margin-bottom: 10px;">
              <strong style="color: var(--accent-green);">{{ totalAbonnes }}</strong> abonnés actifs
            </p>
            <p style="color: #64748b;">
              <strong style="color: #3498db;">{{ tauxOuverture }}%</strong> taux d'ouverture
            </p>
          </div>
        </div>
      </div>

      <div style="margin-top: 30px;">
        <h4>Historique</h4>
        <table class="modern-table" style="margin-top: 15px;">
          <thead>
            <tr>
              <th>Sujet</th>
              <th>Date</th>
              <th>Destinataires</th>
              <th>Ouvertures</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="nl in newsletters" :key="nl.id">
              <td><strong>{{ nl.sujet }}</strong></td>
              <td>{{ nl.date }}</td>
              <td>{{ nl.destinataires }}</td>
              <td>{{ nl.ouvertures }}%</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ANALYTICS TAB -->
    <div v-if="activeTab === 'analytics'" class="card">
      <h3>Analytique Marketing</h3>
      <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-top: 20px;">
        <div style="background: var(--primary-dark); color: white; padding: 20px; border-radius: 10px; text-align: center;">
          <h4>Visites Site</h4>
          <p style="font-size: 2rem; font-weight: bold;">12,540</p>
          <small>+8% vs mois dernier</small>
        </div>
        <div style="background: var(--accent-green); color: white; padding: 20px; border-radius: 10px; text-align: center;">
          <h4>Conversions</h4>
          <p style="font-size: 2rem; font-weight: bold;">384</p>
          <small>3.06% du trafic</small>
        </div>
        <div style="background: #3498db; color: white; padding: 20px; border-radius: 10px; text-align: center;">
          <h4>Leads</h4>
          <p style="font-size: 2rem; font-weight: bold;">156</p>
          <small>+12% vs mois dernier</small>
        </div>
        <div style="background: #f39c12; color: white; padding: 20px; border-radius: 10px; text-align: center;">
          <h4>ROI Estimé</h4>
          <p style="font-size: 2rem; font-weight: bold;">340%</p>
          <small>Rentabilité excellente</small>
        </div>
      </div>

      <div style="margin-top: 30px; padding: 20px; background: #f8fafc; border-radius: 10px;">
        <h4>Sources de Trafic</h4>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-top: 15px;">
          <div style="border-left: 4px solid var(--primary-dark); padding: 15px;">
            <p style="color: #64748b; font-size: 0.9rem;">Moteurs de recherche</p>
            <p style="font-size: 1.5rem; font-weight: bold;">5,240 (41.7%)</p>
          </div>
          <div style="border-left: 4px solid var(--accent-green); padding: 15px;">
            <p style="color: #64748b; font-size: 0.9rem;">Réseaux sociaux</p>
            <p style="font-size: 1.5rem; font-weight: bold;">3,480 (27.7%)</p>
          </div>
          <div style="border-left: 4px solid #3498db; padding: 15px;">
            <p style="color: #64748b; font-size: 0.9rem;">Trafic direct</p>
            <p style="font-size: 1.5rem; font-weight: bold;">3,820 (30.4%)</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Marketing',
  data() {
    return {
      activeTab: 'campaigns',
      searchQuery: '',
      showCampaignForm: false,
      campaigns: [
        { id: 1, nom: "Promo Été 2026", type: "Email", debut: "2026-03-01", fin: "2026-05-31", audience: 2500, statut: "Actif" },
        { id: 2, nom: "Circuit Sud", type: "Social Media", debut: "2026-03-15", fin: "2026-04-15", audience: 5000, statut: "Actif" },
        { id: 3, nom: "Spécial Groupe", type: "Email", debut: "2026-02-01", fin: "2026-02-28", audience: 1200, statut: "Terminé" }
      ],
      newsletter: {
        sujet: '',
        contenu: '',
        audience: 'all',
      },
      newsletters: [
        { id: 1, sujet: "Nouvelles destinations - Mars 2026", date: "2026-03-20", destinataires: 2500, ouvertures: 34 },
        { id: 2, sujet: "Offre exclusive circuits", date: "2026-03-10", destinataires: 2500, ouvertures: 28 },
        { id: 3, sujet: "Bienvenue chez nous!", date: "2026-03-01", destinataires: 650, ouvertures: 45 }
      ],
    };
  },
  computed: {
    filteredCampaigns() {
      if (!this.searchQuery) return this.campaigns;
      return this.campaigns.filter(c => c.nom.toLowerCase().includes(this.searchQuery.toLowerCase()));
    },
    totalAbonnes() {
      return 4850;
    },
    tauxOuverture() {
      return 36;
    },
  },
  methods: {
    sendNewsletter() {
      if (!this.newsletter.sujet || !this.newsletter.contenu) {
        alert("Veuillez remplir tous les champs");
        return;
      }
      alert(`✅ Newsletter "${this.newsletter.sujet}" sera envoyée à ${this.newsletter.audience}!`);
      this.newsletter = { sujet: '', contenu: '', audience: 'all' };
    },
  },
};
</script>

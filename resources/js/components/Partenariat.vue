<template>
  <div class="partenariat-container">
    <div class="partenariat-header">
      <div class="tabs">
        <button class="tab-btn" :class="{ active: activeTab === 'list' }" @click="activeTab = 'list'">
          <i class="fas fa-list"></i> Liste des partenaires
        </button>
        <button class="tab-btn" :class="{ active: activeTab === 'form' }" @click="activeTab = 'form'">
          <i class="fas fa-plus-circle"></i> Ajouter un partenaire
        </button>
      </div>
      <div class="search-filter">
        <input type="text" v-model="searchQuery" placeholder="Filtrer par nom...">
      </div>
    </div>

    <div v-if="activeTab === 'list'" class="card">
      <div class="table-responsive">
        <table id="tablePartenaires">
          <thead>
            <tr>
              <th>Nom</th>
              <th>Type</th>
              <th>Contact</th>
              <th>Email</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="partenaire in filteredPartenaires" :key="partenaire.id">
              <td><strong>{{ partenaire.nom }}</strong></td>
              <td>{{ partenaire.type }}</td>
              <td>{{ partenaire.contact }}</td>
              <td><small>{{ partenaire.email }}</small></td>
              <td><span class="badge" :class="partenaire.statut === 'Actif' ? 'bg-success' : 'bg-gray'">{{ partenaire.statut }}</span></td>
              <td>
                <div class="action-btns">
                  <button class="icon-btn info" title="Détails"><i class="fas fa-eye"></i></button>
                  <button class="icon-btn edit" title="Modifier"><i class="fas fa-edit"></i></button>
                  <button class="icon-btn delete" @click="deletePartenaire(partenaire.id)" title="Supprimer"><i class="fas fa-trash"></i></button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="activeTab === 'form'" class="card">
      <h3>Nouveau Partenaire</h3>
      <form @submit.prevent="handleSubmit">
        <div class="form-grid">
          <div class="form-group">
            <label>Nom de l'entreprise</label>
            <input type="text" v-model="newPartenaire.nom" required placeholder="Ex: Hotel Paradise">
          </div>
          <div class="form-group">
            <label>Type de partenaire</label>
            <input type="text" v-model="newPartenaire.type" required placeholder="Ex: Hôtel, Restaurant...">
          </div>
          <div class="form-group">
            <label>Personne de contact</label>
            <input type="text" v-model="newPartenaire.contact" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" v-model="newPartenaire.email" required>
          </div>
          <div class="form-group">
            <label>Téléphone</label>
            <input type="tel" v-model="newPartenaire.tel" required>
          </div>
          <div class="form-group">
            <label>Ville</label>
            <input type="text" v-model="newPartenaire.ville" required>
          </div>
          <div class="form-group full-width">
            <label>Adresse complète</label>
            <textarea v-model="newPartenaire.adresse" rows="2" placeholder="Adresse détaillée..."></textarea>
          </div>
        </div>
        <div class="form-actions">
          <button type="submit" class="btn btn-save">Enregistrer le partenaire</button>
          <button type="button" class="btn btn-edit" @click="activeTab = 'list'">Annuler</button>
        </div>
      </form>
      <div v-if="feedback.message" class="feedback-msg" :class="{ error: feedback.type === 'error', success: feedback.type === 'success' }">
        {{ feedback.message }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Partenariat',
  data() {
    return {
      activeTab: 'list',
      searchQuery: '',
      partenaires: [
        { id: 1, nom: "Hôtel Paradis", type: "Hôtel", contact: "Jean Dupont", email: "jean@hotelparadis.mg", tel: "0340000001", ville: "Antananarivo", statut: "Actif" },
        { id: 2, nom: "Restaurant Saveurs", type: "Restaurant", contact: "Marie Durand", email: "marie@saveurs.mg", tel: "0340000002", ville: "Toliara", statut: "Actif" }
      ],
      newPartenaire: {
        nom: '',
        type: '',
        contact: '',
        email: '',
        tel: '',
        ville: '',
        adresse: '',
      },
      feedback: { message: '', type: '' },
    };
  },
  computed: {
    filteredPartenaires() {
      if (!this.searchQuery) return this.partenaires;
      return this.partenaires.filter(p => p.nom.toLowerCase().includes(this.searchQuery.toLowerCase()));
    },
  },
  methods: {
    handleSubmit() {
      const partenaire = {
        id: this.partenaires.length + 1,
        ...this.newPartenaire,
        statut: "Actif"
      };

      this.partenaires.unshift(partenaire);
      this.feedback = { message: "✅ Partenaire enregistré avec succès !", type: 'success' };
      
      setTimeout(() => {
        this.activeTab = 'list';
        this.resetForm();
        this.feedback = { message: '', type: '' };
      }, 1500);
    },
    resetForm() {
      this.newPartenaire = {
        nom: '',
        type: '',
        contact: '',
        email: '',
        tel: '',
        ville: '',
        adresse: '',
      };
    },
    deletePartenaire(id) {
      if (confirm("Supprimer ce partenaire définitivement ?")) {
        this.partenaires = this.partenaires.filter(p => p.id !== id);
      }
    },
  },
};
</script>

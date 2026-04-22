<template>
  <div class="circuit-container">
    <div class="circuit-header">
      <div class="tabs">
        <button class="tab-btn" :class="{ active: activeTab === 'list' }" @click="activeTab = 'list'">
          <i class="fas fa-list"></i> Liste des circuits
        </button>
        <button class="tab-btn" :class="{ active: activeTab === 'form' }" @click="openCreateForm">
          <i class="fas fa-plus-circle"></i> Ajouter un circuit
        </button>
      </div>
      <div class="search-filter">
        <input v-model="searchQuery" type="text" placeholder="Filtrer par nom, destination ou slug...">
      </div>
    </div>

    <div v-if="feedback.message" class="feedback-msg" :class="{ error: feedback.type === 'error', success: feedback.type === 'success' }">
      {{ feedback.message }}
    </div>

    <div v-if="activeTab === 'list'" class="card pcb-card">
      <div class="pcb-card__glow"></div>
      <div class="pcb-card__trace pcb-card__trace--top"></div>
      <div class="pcb-card__trace pcb-card__trace--side"></div>
      <div class="card-header-actions">
        <div>
          <h3>Circuits enregistres</h3>
          <p>{{ filteredCircuits.length }} circuit(s) affiche(s)</p>
        </div>
        <button class="btn btn-save" @click="fetchCircuits" :disabled="loading">
          {{ loading ? 'Chargement...' : 'Actualiser' }}
        </button>
      </div>

      <div class="table-responsive">
        <table id="tableCircuits">
          <thead>
            <tr>
              <th>Nom</th>
              <th>Slug</th>
              <th>Destination</th>
              <th>Depart / Retour</th>
              <th>Prix</th>
              <th>Places</th>
              <th>Type</th>
              <th>Detail</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!loading && filteredCircuits.length === 0">
              <td colspan="10">Aucun circuit trouve.</td>
            </tr>
            <tr v-for="circuit in filteredCircuits" :key="circuit.id">
              <td><strong>{{ circuit.nom }}</strong></td>
              <td>{{ circuit.slug || '-' }}</td>
              <td>{{ circuit.destination }}</td>
              <td><small>{{ formatDate(circuit.depart) }} <br> {{ formatDate(circuit.retour) }}</small></td>
              <td>{{ formatPrice(circuit.prix) }}</td>
              <td>{{ circuit.places }}</td>
              <td>{{ circuit.type || '-' }}</td>
              <td>{{ detailSummary(circuit.detail) }}</td>
              <td><span class="badge" :class="statusClass(circuit.statut)">{{ circuit.statut }}</span></td>
              <td>
                <div class="action-btns">
                  <button class="icon-btn edit" title="Modifier" @click="editCircuit(circuit)">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="icon-btn info" :title="circuit.statut === 'Publie' ? 'Depublier' : 'Publier'" @click="togglePublish(circuit)">
                    <i :class="circuit.statut === 'Publie' ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                  </button>
                  <button class="icon-btn delete" title="Supprimer" @click="deleteCircuit(circuit.id)">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="activeTab === 'form'" class="card pcb-card pcb-card--form">
      <div class="pcb-card__glow"></div>
      <div class="pcb-card__trace pcb-card__trace--top"></div>
      <div class="pcb-card__trace pcb-card__trace--side"></div>
      <h3>{{ isEditing ? 'Modifier le circuit' : 'Nouveau circuit' }}</h3>

      <form @submit.prevent="handleSubmit">
        <section class="section-block">
          <div class="section-head">
            <h4>Informations generales</h4>
            <p>Base du circuit et informations visibles dans le catalogue.</p>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label>Nom du circuit</label>
              <input v-model="form.nom" type="text" required placeholder="Ex: Safari Sud">
            </div>
            <div class="form-group">
              <label>Slug public</label>
              <input v-model="form.slug" type="text" placeholder="Ex: incontournables-madagascar">
            </div>
            <div class="form-group">
              <label>Destination</label>
              <input v-model="form.destination" type="text" required placeholder="Ex: Tulear">
            </div>
            <div class="form-group">
              <label>Date de depart</label>
              <input v-model="form.depart" type="date" required>
            </div>
            <div class="form-group">
              <label>Date de retour</label>
              <input v-model="form.retour" type="date" required>
            </div>
            <div class="form-group">
              <label>Prix (Ar)</label>
              <input v-model.number="form.prix" type="number" min="1" required>
            </div>
            <div class="form-group">
              <label>Places disponibles</label>
              <input v-model.number="form.places" type="number" min="1" required>
            </div>
            <div class="form-group">
              <label>Type de circuit</label>
              <select v-model="form.type" required>
                <option disabled value="">Choisir un type</option>
                <option v-for="type in circuitTypes" :key="type" :value="type">{{ type }}</option>
              </select>
            </div>
            <div class="form-group">
              <label>Statut</label>
              <select v-model="form.statut">
                <option value="Brouillon">Brouillon</option>
                <option value="Publie">Publie</option>
              </select>
            </div>
            <div class="form-group full-width">
              <label>Description catalogue</label>
              <textarea v-model="form.description" rows="3" placeholder="Resume du voyage..."></textarea>
            </div>
            <div class="form-group full-width">
              <label>Image du circuit</label>
              <input type="file" accept="image/*" @change="handleImageChange">
              <small v-if="selectedImageName" class="text-muted">{{ selectedImageName }}</small>
              <img v-if="currentImageUrl" :src="currentImageUrl" alt="Apercu du circuit" class="preview-image">
            </div>
          </div>
        </section>

        <section class="section-block">
          <div class="section-head">
            <h4>Programme jour par jour</h4>
            <p>Correspond a la timeline du detail circuit.</p>
          </div>

          <div class="table-responsive">
            <table class="detail-table">
              <thead>
                <tr>
                  <th>Jour</th>
                  <th>Titre</th>
                  <th>Destination</th>
                  <th>Photo destination</th>
                  <th>Hebergement</th>
                  <th>Repas</th>
                  <th>Activites</th>
                  <th>Transport</th>
                  <th>Notes</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(step, index) in form.programme" :key="`programme-${index}`">
                  <td><input v-model.number="step.jour" type="number" min="1"></td>
                  <td><input v-model="step.titre" type="text" placeholder="Titre du jour"></td>
                  <td><input v-model="step.destination" type="text" placeholder="Lieu"></td>
                  <td>
                    <input type="file" accept="image/*" @change="handleProgrammeImageChange($event, index)">
                    <img v-if="step.image_preview" :src="step.image_preview" alt="Apercu jour" class="programme-preview-image">
                  </td>
                  <td><input v-model="step.hebergement" type="text" placeholder="Hotel / bivouac"></td>
                  <td><input v-model="step.repas" type="text" placeholder="Repas inclus"></td>
                  <td><input v-model="step.activites" type="text" placeholder="Activites"></td>
                  <td><input v-model="step.transport" type="text" placeholder="Transfert"></td>
                  <td><textarea v-model="step.notes" rows="2" placeholder="Description du jour"></textarea></td>
                  <td class="table-action-cell">
                    <button type="button" class="icon-btn delete" title="Retirer" @click="removeProgrammeStep(index)">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="inline-actions">
            <button type="button" class="btn btn-edit" @click="addProgrammeStep">
              Ajouter une journee
            </button>
          </div>
        </section>
        <section class="section-block">
          <div class="section-head">
            <h4>Detail du circuit</h4>
            <p>Tous les criteres issus de `detailcircuit.html` sont centralises ici.</p>
          </div>

          <div class="table-responsive">
            <table class="detail-table criteria-table">
              <thead>
                <tr>
                  <th>Critere</th>
                  <th>Valeur</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="field in detailMetaFields" :key="field.key">
                  <td>{{ field.label }}</td>
                  <td>
                    <textarea v-if="field.multiline" v-model="form.detail[field.key]" :rows="field.rows || 3" :placeholder="field.placeholder || ''"></textarea>
                    <input v-else v-model="form.detail[field.key]" type="text" :placeholder="field.placeholder || ''">
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="sub-section">
            <div class="sub-section-header">
              <h5>Image d'itineraire</h5>
              <p>Chargee comme fichier et stockee cote serveur.</p>
            </div>
            <div class="form-group full-width">
              <input type="file" accept="image/*" @change="handleDetailImageChange">
              <small v-if="selectedDetailImageName" class="text-muted">{{ selectedDetailImageName }}</small>
              <img v-if="currentDetailImageUrl" :src="currentDetailImageUrl" alt="Apercu itineraire" class="preview-image">
            </div>
          </div>

          <div class="sub-section">
            <div class="sub-section-header">
              <h5>Specifications</h5>
              <p>Bloc lateral du detail circuit.</p>
            </div>
            <div class="table-responsive">
              <table class="detail-table criteria-table">
                <thead>
                  <tr>
                    <th>Critere</th>
                    <th>Valeur</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="spec in specificationFields" :key="spec.key">
                    <td>{{ spec.label }}</td>
                    <td><input v-model="form.detail.specifications[spec.key]" type="text" :placeholder="spec.placeholder || ''"></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="sub-section">
            <div class="sub-section-header">
              <h5>Points forts</h5>
              <p>Liste a puces de la page detail.</p>
            </div>
            <div class="table-responsive">
              <table class="detail-table">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Texte</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in form.detail.points_forts" :key="`highlight-${index}`">
                    <td>{{ index + 1 }}</td>
                    <td><textarea v-model="form.detail.points_forts[index]" rows="2" placeholder="Point fort"></textarea></td>
                    <td class="table-action-cell">
                      <button type="button" class="icon-btn delete" @click="removeListItem(form.detail.points_forts, index)">
                        <i class="fas fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="inline-actions">
              <button type="button" class="btn btn-edit" @click="addListItem(form.detail.points_forts)">
                Ajouter un point fort
              </button>
            </div>
          </div>

          <div class="sub-section">
            <div class="sub-section-header">
              <h5>Prix compris / non compris</h5>
              <p>Colonnes de la section detail du prix.</p>
            </div>
            <div class="price-columns">
              <div>
                <h6>Le prix comprend</h6>
                <table class="detail-table">
                  <tbody>
                    <tr v-for="(item, index) in form.detail.prix_inclus" :key="`included-${index}`">
                      <td><textarea v-model="form.detail.prix_inclus[index]" rows="2" placeholder="Element inclus"></textarea></td>
                      <td class="table-action-cell">
                        <button type="button" class="icon-btn delete" @click="removeListItem(form.detail.prix_inclus, index)">
                          <i class="fas fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <div class="inline-actions">
                  <button type="button" class="btn btn-edit" @click="addListItem(form.detail.prix_inclus)">
                    Ajouter un element
                  </button>
                </div>
              </div>

              <div>
                <h6>Le prix ne comprend pas</h6>
                <table class="detail-table">
                  <tbody>
                    <tr v-for="(item, index) in form.detail.prix_exclus" :key="`excluded-${index}`">
                      <td><textarea v-model="form.detail.prix_exclus[index]" rows="2" placeholder="Element exclu"></textarea></td>
                      <td class="table-action-cell">
                        <button type="button" class="icon-btn delete" @click="removeListItem(form.detail.prix_exclus, index)">
                          <i class="fas fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <div class="inline-actions">
                  <button type="button" class="btn btn-edit" @click="addListItem(form.detail.prix_exclus)">
                    Ajouter un element
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="sub-section">
            <div class="sub-section-header">
              <h5>Fiche technique</h5>
              <p>Cartes et contenu du modal technique.</p>
            </div>
            <div class="table-responsive">
              <table class="detail-table">
                <thead>
                  <tr>
                    <th>Cle</th>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Elements</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in form.detail.fiche_technique" :key="`sheet-${index}`">
                    <td><input v-model="item.cle" type="text" placeholder="esprit"></td>
                    <td><input v-model="item.titre" type="text" placeholder="L'esprit du voyage"></td>
                    <td><textarea v-model="item.description" rows="3" placeholder="Description"></textarea></td>
                    <td><textarea v-model="item.elements_text" rows="4" placeholder="Un element par ligne"></textarea></td>
                    <td class="table-action-cell">
                      <button type="button" class="icon-btn delete" @click="removeTechnicalSheet(index)">
                        <i class="fas fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="inline-actions">
              <button type="button" class="btn btn-edit" @click="addTechnicalSheetItem">
                Ajouter une fiche
              </button>
            </div>
          </div>
        </section>

        <div class="form-actions">
          <button type="submit" class="btn btn-save" :disabled="saving">
            {{ saving ? 'Enregistrement...' : isEditing ? 'Mettre a jour' : 'Enregistrer le circuit' }}
          </button>
          <button type="button" class="btn btn-edit" @click="cancelForm">Annuler</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
const CIRCUIT_TYPES = [
  'Randonnee & Trek',
  'Classe Verte',
  'Team Building',
  'Circuit Sejour',
];

const DETAIL_META_FIELDS = [
  { key: 'breadcrumb', label: 'Breadcrumb', placeholder: 'Accueil > Madagascar > Circuit accompagne' },
  { key: 'titre', label: 'Titre detail', placeholder: 'Incontournables Madagascar' },
  { key: 'sous_titre', label: 'Sous-titre', placeholder: "L'ame de l'ile Rouge..." },
  { key: 'code', label: 'Code circuit', placeholder: 'MADA01' },
  { key: 'badge', label: 'Badge', placeholder: 'Coup de coeur' },
  { key: 'note', label: 'Note', placeholder: '4.8' },
  { key: 'nombre_avis', label: "Nombre d'avis", placeholder: '12 avis' },
  { key: 'description', label: 'Description detail', multiline: true, rows: 4 },
  { key: 'image_itineraire_legende', label: 'Legende image itineraire', placeholder: "Massif de l'Andringitra" },
  { key: 'prix_label', label: 'Label prix', placeholder: 'Prix par personne a partir de' },
  { key: 'prix_details', label: 'Details prix', placeholder: 'Vol compris au depart de Paris' },
  { key: 'bouton_prix', label: 'Texte bouton prix', placeholder: 'Voir les dates et prix' },
  { key: 'privatisation_titre', label: 'Titre privatisation', placeholder: 'Privatisez votre voyage' },
  { key: 'privatisation_description', label: 'Description privatisation', multiline: true, rows: 3 },
  { key: 'privatisation_bouton', label: 'Bouton privatisation', placeholder: 'Demander un devis' },
];

const SPECIFICATION_FIELDS = [
  { key: 'duree', label: 'Duree', placeholder: '15 jours' },
  { key: 'niveau', label: 'Niveau', placeholder: 'Tranquille' },
  { key: 'activite', label: 'Activite', placeholder: 'Randonnee' },
  { key: 'groupe', label: 'Groupe', placeholder: '5 a 12 pers.' },
];

function createProgrammeItem(overrides = {}) {
  return {
    jour: overrides.jour ?? null,
    titre: overrides.titre ?? '',
    destination: overrides.destination ?? '',
    image: overrides.image ?? '',
    image_path: overrides.image_path ?? overrides.image ?? '',
    image_preview: overrides.image_preview ?? '',
    hebergement: overrides.hebergement ?? '',
    repas: overrides.repas ?? '',
    activites: overrides.activites ?? '',
    transport: overrides.transport ?? '',
    notes: overrides.notes ?? '',
  };
}

function createTechnicalSheetItem(overrides = {}) {
  const elements = Array.isArray(overrides.elements) ? overrides.elements : [];

  return {
    cle: overrides.cle ?? '',
    titre: overrides.titre ?? '',
    description: overrides.description ?? '',
    elements: elements,
    elements_text: overrides.elements_text ?? elements.join('\n'),
  };
}
function createDetail(overrides = {}) {
  const specifications = overrides.specifications || {};

  return {
    breadcrumb: overrides.breadcrumb ?? 'Accueil > Madagascar > Circuit accompagne',
    titre: overrides.titre ?? 'Incontournables Madagascar',
    sous_titre: overrides.sous_titre ?? "L'ame de l'ile Rouge entre sommets et savanes",
    code: overrides.code ?? 'MADA01',
    badge: overrides.badge ?? 'Coup de coeur',
    note: overrides.note ?? '4.8',
    nombre_avis: overrides.nombre_avis ?? '12 avis',
    description:
      overrides.description ??
      "Une traversee exclusive des hautes terres jusqu'au Grand Sud. Ce voyage combine l'exploration des parcs nationaux et des rencontres immersives avec les communautes locales.",
    image_itineraire: overrides.image_itineraire ?? '',
    image_itineraire_legende: overrides.image_itineraire_legende ?? "Massif de l'Andringitra",
    prix_label: overrides.prix_label ?? 'Prix par personne a partir de',
    prix_details: overrides.prix_details ?? 'Vol compris au depart de Paris',
    bouton_prix: overrides.bouton_prix ?? 'Voir les dates et prix',
    privatisation_titre: overrides.privatisation_titre ?? 'Privatisez votre voyage',
    privatisation_description:
      overrides.privatisation_description ??
      'Envie de partir en famille ou avec vos amis aux dates que vous souhaitez ?',
    privatisation_bouton: overrides.privatisation_bouton ?? 'Demander un devis',
    points_forts: (overrides.points_forts || [
      "L'exclusivite des bivouacs au pied de l'Andringitra.",
      'La rencontre solidaire avec les paysans des Hautes Terres.',
      'Un itineraire equilibre entre randonnee et detente.',
    ]).slice(),
    specifications: {
      duree: specifications.duree ?? '15 jours',
      niveau: specifications.niveau ?? 'Tranquille',
      activite: specifications.activite ?? 'Randonnee',
      groupe: specifications.groupe ?? '5 a 12 pers.',
    },
    prix_inclus: (overrides.prix_inclus || [
      'Le transport aerien sur vols reguliers internationaux',
      "L'hebergement en chambre double",
      'La pension complete',
    ]).slice(),
    prix_exclus: (overrides.prix_exclus || [
      "Les frais de visa a regler a l'arrivee",
      'Les boissons et depenses personnelles',
      'Les assurances optionnelles',
    ]).slice(),
    fiche_technique: (overrides.fiche_technique || [
      {
        cle: 'esprit',
        titre: "L'esprit du voyage",
        description: 'Une immersion authentique au coeur des regions visitees.',
        elements: [
          'Rencontres avec les populations locales.',
          'Hebergements de charme et eco-responsables.',
          'Rythme adapte pour une decouverte approfondie.',
        ],
      },
      {
        cle: 'itineraire',
        titre: 'Itineraire detaille',
        description: 'Un parcours concu pour maximiser les experiences.',
        elements: ['Jour 1: Arrivee a Antananarivo', 'Jour 3: Parc Andasibe-Mantadia', 'Jour 5: Ile Sainte-Marie'],
      },
      {
        cle: 'formalites',
        titre: 'Formalites & sante',
        description: 'Les informations essentielles pour voyager sereinement.',
        elements: ['Passeport valide 6 mois apres le voyage', 'Assurance rapatriement conseillee'],
      },
      {
        cle: 'budget',
        titre: 'Budget & prix',
        description: 'Tous les details de tarification.',
        elements: ['Prix base', 'Supplement chambre simple', 'Assurance optionnelle'],
      },
      {
        cle: 'equipement',
        titre: 'Equipement conseille',
        description: 'Pour un voyage confortable et securise.',
        elements: ['Vetements legers et respirants', 'Chaussures de randonnee', 'Protection solaire'],
      },
      {
        cle: 'extensions',
        titre: 'Extensions possibles',
        description: 'Prolongez votre aventure malgache.',
        elements: ['Extension Sud', 'Extension Ouest', 'Pack combine'],
      },
    ]).map((item) => createTechnicalSheetItem(item)),
  };
}

function emptyForm() {
  return {
    id: null,
    nom: '',
    slug: '',
    destination: '',
    depart: '',
    retour: '',
    prix: null,
    places: null,
    description: '',
    type: '',
    statut: 'Brouillon',
    programme: [createProgrammeItem()],
    detail: createDetail(),
  };
}

export default {
  name: 'Circuit',
  data() {
    return {
      activeTab: 'list',
      searchQuery: '',
      circuits: [],
      loading: false,
      saving: false,
      circuitTypes: CIRCUIT_TYPES,
      detailMetaFields: DETAIL_META_FIELDS,
      specificationFields: SPECIFICATION_FIELDS,
      form: emptyForm(),
      selectedImageFile: null,
      selectedImageName: '',
      currentImageUrl: '',
      selectedDetailImageFile: null,
      selectedDetailImageName: '',
      currentDetailImageUrl: '',
      feedback: { message: '', type: '' },
    };
  },
  computed: {
    isEditing() {
      return Boolean(this.form.id);
    },
    filteredCircuits() {
      const query = this.searchQuery.trim().toLowerCase();
      if (!query) {
        return this.circuits;
      }

      return this.circuits.filter((circuit) =>
        [circuit.nom, circuit.slug, circuit.destination, circuit.type, circuit.statut, circuit.detail?.titre]
          .filter(Boolean)
          .some((value) => String(value).toLowerCase().includes(query))
      );
    },
  },
  methods: {
    async fetchCircuits() {
      this.loading = true;

      try {
        const token = localStorage.getItem('access_token');
        const headers = {
          'Accept': 'application/json'
        };
        
        if (token) {
          headers['Authorization'] = `Bearer ${token}`;
        }

        const response = await fetch('/api/circuits', {
          headers
        });
        
        if (!response.ok) {
          throw new Error('Impossible de charger les circuits.');
        }

        this.circuits = await response.json();
      } catch (error) {
        this.setFeedback(error.message, 'error');
      } finally {
        this.loading = false;
      }
    },
    async handleSubmit() {
      const dep = new Date(this.form.depart);
      const ret = new Date(this.form.retour);

      if (ret <= dep) {
        this.setFeedback('La date de retour doit etre apres le depart.', 'error');
        return;
      }

      this.saving = true;

      const url = this.isEditing ? `/api/circuits/${this.form.id}` : '/api/circuits';
      const formData = new FormData();
      const payload = this.buildPayload();

      this.appendFormData(formData, payload);

      if (this.selectedImageFile) {
        formData.append('image', this.selectedImageFile);
      }

      if (this.selectedDetailImageFile) {
        formData.append('detail_image_itineraire', this.selectedDetailImageFile);
      }

      if (this.isEditing) {
        formData.append('_method', 'PUT');
      }

      try {
        const token = localStorage.getItem('access_token');
        const headers = { Accept: 'application/json' };
        
        if (token) {
          headers['Authorization'] = `Bearer ${token}`;
        }

        const response = await fetch(url, {
          method: 'POST',
          headers,
          body: formData,
        });

        if (!response.ok) {
          const errorData = await response.json().catch(() => ({}));
          const firstError = errorData?.errors ? Object.values(errorData.errors)[0]?.[0] : null;
          throw new Error(firstError || 'Enregistrement impossible.');
        }

        await response.json();
        await this.fetchCircuits();

        this.setFeedback(this.isEditing ? 'Circuit mis a jour avec succes.' : 'Circuit enregistre avec succes.', 'success');
        this.cancelForm();
      } catch (error) {
        this.setFeedback(error.message, 'error');
      } finally {
        this.saving = false;
      }
    },
    buildPayload() {
      return {
        nom: this.form.nom,
        slug: this.form.slug || '',
        destination: this.form.destination,
        depart: this.form.depart,
        retour: this.form.retour,
        prix: this.form.prix,
        places: this.form.places,
        description: this.form.description,
        type: this.form.type,
        statut: this.form.statut,
        programme: this.form.programme
          .map((item) => ({
            jour: item.jour || null,
            titre: item.titre,
            destination: item.destination,
            image_path: item.image_path || '',
            image: item.image instanceof File ? item.image : null,
            hebergement: item.hebergement,
            repas: item.repas,
            activites: item.activites,
            transport: item.transport,
            notes: item.notes,
          }))
          .filter((item) => Object.values(item).some((value) => value !== '' && value !== null)),
        detail: {
          ...this.form.detail,
          image_itineraire: this.form.detail.image_itineraire || '',
          points_forts: this.form.detail.points_forts.filter((item) => item && item.trim()),
          prix_inclus: this.form.detail.prix_inclus.filter((item) => item && item.trim()),
          prix_exclus: this.form.detail.prix_exclus.filter((item) => item && item.trim()),
          fiche_technique: this.form.detail.fiche_technique.map((item) => ({
            cle: item.cle,
            titre: item.titre,
            description: item.description,
            elements: item.elements_text.split('\n').map((line) => line.trim()).filter(Boolean),
          })),
        },
      };
    },
    appendFormData(formData, value, parentKey = '') {
      if (value === null || value === undefined) {
        return;
      }

      if (Array.isArray(value)) {
        value.forEach((item, index) => {
          this.appendFormData(formData, item, `${parentKey}[${index}]`);
        });
        return;
      }

      if (typeof value === 'object' && !(value instanceof File)) {
        Object.entries(value).forEach(([key, nestedValue]) => {
          const fullKey = parentKey ? `${parentKey}[${key}]` : key;
          this.appendFormData(formData, nestedValue, fullKey);
        });
        return;
      }

      formData.append(parentKey, value);
    },
    editCircuit(circuit) {
      this.form = {
        id: circuit.id,
        nom: circuit.nom,
        slug: circuit.slug || '',
        destination: circuit.destination,
        depart: circuit.depart,
        retour: circuit.retour,
        prix: Number(circuit.prix),
        places: Number(circuit.places),
        description: circuit.description || '',
        type: circuit.type || '',
        statut: circuit.statut === 'Actif' ? 'Publie' : circuit.statut,
        programme: this.normalizeProgramme(circuit.programme),
        detail: createDetail(circuit.detail || {}),
      };

      this.selectedImageFile = null;
      this.selectedImageName = '';
      this.currentImageUrl = circuit.image_url || '';
      this.selectedDetailImageFile = null;
      this.selectedDetailImageName = '';
      this.currentDetailImageUrl = this.resolveStoredPath(this.form.detail.image_itineraire);
      this.activeTab = 'form';
    },
    normalizeProgramme(programme) {
      if (!Array.isArray(programme) || programme.length === 0) {
        return [createProgrammeItem()];
      }

      return programme.map((item) =>
        createProgrammeItem({
          ...item,
          image_path: item.image || '',
          image_preview: this.resolveStoredPath(item.image),
        })
      );
    },
    handleImageChange(event) {
      const [file] = event.target.files || [];
      this.selectedImageFile = file || null;
      this.selectedImageName = file ? file.name : '';

      if (file) {
        this.currentImageUrl = URL.createObjectURL(file);
      }
    },
    handleDetailImageChange(event) {
      const [file] = event.target.files || [];
      this.selectedDetailImageFile = file || null;
      this.selectedDetailImageName = file ? file.name : '';

      if (file) {
        this.currentDetailImageUrl = URL.createObjectURL(file);
      }
    },
    handleProgrammeImageChange(event, index) {
      const [file] = event.target.files || [];
      const step = this.form.programme[index];

      if (!step) {
        return;
      }

      step.image = file || '';
      step.image_preview = file ? URL.createObjectURL(file) : this.resolveStoredPath(step.image_path);
    },
    addProgrammeStep() {
      this.form.programme.push(createProgrammeItem({ jour: this.form.programme.length + 1 }));
    },
    removeProgrammeStep(index) {
      this.form.programme.splice(index, 1);
      if (this.form.programme.length === 0) {
        this.form.programme.push(createProgrammeItem());
      }
    },
    addListItem(target) {
      target.push('');
    },
    removeListItem(target, index) {
      target.splice(index, 1);
      if (target.length === 0) {
        target.push('');
      }
    },
    addTechnicalSheetItem() {
      this.form.detail.fiche_technique.push(createTechnicalSheetItem());
    },
    removeTechnicalSheet(index) {
      this.form.detail.fiche_technique.splice(index, 1);
      if (this.form.detail.fiche_technique.length === 0) {
        this.form.detail.fiche_technique.push(createTechnicalSheetItem());
      }
    },
    async togglePublish(circuit) {
      const nextStatus = circuit.statut === 'Publie' ? 'Brouillon' : 'Publie';

      try {
        const token = localStorage.getItem('access_token');
        const headers = { Accept: 'application/json' };
        
        if (token) {
          headers['Authorization'] = `Bearer ${token}`;
        }

        const response = await fetch(`/api/circuits/${circuit.id}`, {
          method: 'POST',
          headers,
          body: this.buildStatusFormData(nextStatus),
        });

        if (!response.ok) {
          throw new Error('Le changement de statut a echoue.');
        }

        await this.fetchCircuits();
        this.setFeedback(
          nextStatus === 'Publie' ? 'Le circuit est maintenant visible sur la page publique.' : 'Le circuit a ete retire de la page publique.',
          'success'
        );
      } catch (error) {
        this.setFeedback(error.message, 'error');
      }
    },
    buildStatusFormData(status) {
      const formData = new FormData();
      formData.append('_method', 'PUT');
      formData.append('statut', status);
      return formData;
    },
    async deleteCircuit(id) {
      if (!window.confirm('Supprimer ce circuit definitivement ?')) {
        return;
      }

      try {
        const token = localStorage.getItem('access_token');
        const headers = { Accept: 'application/json' };
        
        if (token) {
          headers['Authorization'] = `Bearer ${token}`;
        }

        const response = await fetch(`/api/circuits/${id}`, {
          method: 'DELETE',
          headers,
        });

        if (!response.ok) {
          throw new Error('Suppression impossible.');
        }

        await this.fetchCircuits();
        this.setFeedback('Circuit supprime avec succes.', 'success');
      } catch (error) {
        this.setFeedback(error.message, 'error');
      }
    },
    openCreateForm() {
      this.form = emptyForm();
      this.selectedImageFile = null;
      this.selectedImageName = '';
      this.currentImageUrl = '';
      this.selectedDetailImageFile = null;
      this.selectedDetailImageName = '';
      this.currentDetailImageUrl = '';
      this.activeTab = 'form';
    },
    cancelForm() {
      this.form = emptyForm();
      this.selectedImageFile = null;
      this.selectedImageName = '';
      this.currentImageUrl = '';
      this.selectedDetailImageFile = null;
      this.selectedDetailImageName = '';
      this.currentDetailImageUrl = '';
      this.activeTab = 'list';
    },
    resolveStoredPath(path) {
      if (!path) {
        return '';
      }

      if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/')) {
        return path;
      }

      return `/storage/${path.replace(/^\/+/, '')}`;
    },
    detailSummary(detail) {
      if (!detail) {
        return 'Non renseigne';
      }

      const title = detail.titre || detail.code;
      return title ? `${title}` : 'Partiel';
    },
    setFeedback(message, type) {
      this.feedback = { message, type };
    },
    formatDate(value) {
      if (!value) {
        return '-';
      }

      return new Date(value).toLocaleDateString('fr-FR');
    },
    formatPrice(value) {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'MGA',
        maximumFractionDigits: 0,
      }).format(Number(value || 0));
    },
    statusClass(status) {
      return status === 'Publie' || status === 'Actif' ? 'bg-success' : 'bg-gray';
    },
  },
  mounted() {
    this.fetchCircuits();
  },
};
</script>

<style scoped>
.pcb-card {
  position: relative;
  overflow: hidden;
  border: 1px solid rgba(39, 197, 181, 0.22);
  border-radius: 24px;
  background:
    radial-gradient(circle at top right, rgba(52, 211, 153, 0.08), transparent 26%),
    radial-gradient(circle at bottom left, rgba(56, 189, 248, 0.08), transparent 30%),
    rgba(255, 255, 255, 0.82);
  box-shadow:
    0 18px 45px rgba(3, 15, 18, 0.28),
    inset 0 1px 0 rgba(173, 255, 244, 0.08);
}

.pcb-card::before,
.pcb-card::after {
  content: '';
  position: absolute;
  pointer-events: none;
}

.pcb-card::before {
  inset: 14px;
  border: 1px solid rgba(94, 234, 212, 0.08);
  border-radius: 18px;
}

.pcb-card::after {
  top: 18px;
  right: 18px;
  width: 12px;
  height: 12px;
  border-radius: 999px;
  background: #7dd3fc;
  box-shadow:
    0 0 0 5px rgba(125, 211, 252, 0.12),
    0 0 18px rgba(125, 211, 252, 0.45);
}

.pcb-card :deep(h3),
.pcb-card :deep(h4),
.pcb-card :deep(h5),
.pcb-card :deep(h6),
.pcb-card :deep(th),
.pcb-card :deep(label) {
  color: inherit;
}

.pcb-card :deep(p),
.pcb-card :deep(td),
.pcb-card :deep(small),
.pcb-card :deep(.text-muted) {
  color: inherit;
}

.pcb-card__glow {
  position: absolute;
  inset: auto -80px -80px auto;
  width: 220px;
  height: 220px;
  background: radial-gradient(circle, rgba(45, 212, 191, 0.22), transparent 68%);
  pointer-events: none;
}

.pcb-card__trace {
  position: absolute;
  pointer-events: none;
  opacity: 0.9;
}

.pcb-card__trace--top {
  top: 46px;
  left: 28px;
  width: 220px;
  height: 44px;
  border-top: 2px solid rgba(45, 212, 191, 0.3);
  border-right: 2px solid rgba(45, 212, 191, 0.3);
  border-top-right-radius: 22px;
}

.pcb-card__trace--side {
  right: 42px;
  bottom: 34px;
  width: 120px;
  height: 120px;
  border-left: 2px solid rgba(125, 211, 252, 0.24);
  border-bottom: 2px solid rgba(125, 211, 252, 0.24);
  border-bottom-left-radius: 30px;
}

.pcb-card--form {
  background:
    radial-gradient(circle at top right, rgba(74, 222, 128, 0.08), transparent 24%),
    radial-gradient(circle at bottom left, rgba(34, 197, 94, 0.08), transparent 32%),
    rgba(255, 255, 255, 0.82);
}

.pcb-card :deep(.card-header-actions) {
  position: relative;
  z-index: 1;
}

.pcb-card :deep(.badge) {
  border: 1px solid rgba(167, 243, 208, 0.24);
  box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.03);
}

.pcb-card :deep(.btn-save),
.pcb-card :deep(.btn-edit) {
  border: 1px solid rgba(110, 231, 183, 0.22);
  box-shadow: 0 10px 18px rgba(0, 0, 0, 0.18);
}

.pcb-card :deep(input),
.pcb-card :deep(select),
.pcb-card :deep(textarea) {
  color: inherit;
  background: rgba(255, 255, 255, 0.92);
  border: 1px solid rgba(94, 234, 212, 0.16);
  border-radius: 14px;
  box-shadow: inset 0 0 0 1px rgba(15, 118, 110, 0.04);
}

.pcb-card :deep(input::placeholder),
.pcb-card :deep(textarea::placeholder) {
  color: rgba(80, 99, 111, 0.55);
}

.pcb-card :deep(input:focus),
.pcb-card :deep(select:focus),
.pcb-card :deep(textarea:focus) {
  outline: none;
  border-color: rgba(45, 212, 191, 0.52);
  box-shadow:
    0 0 0 4px rgba(45, 212, 191, 0.12),
    inset 0 0 0 1px rgba(45, 212, 191, 0.18);
}

.pcb-card :deep(.table-responsive),
.pcb-card :deep(.detail-table) {
  position: relative;
  z-index: 1;
}

.pcb-card :deep(.detail-table th) {
  background: rgba(226, 248, 244, 0.9);
}

.pcb-card :deep(.detail-table td) {
  background: rgba(255, 255, 255, 0.74);
}

.pcb-card :deep(.detail-table th),
.pcb-card :deep(.detail-table td) {
  border-color: rgba(94, 234, 212, 0.12);
}

.pcb-card :deep(#tableCircuits thead th) {
  background: rgba(226, 248, 244, 0.9);
}

.pcb-card :deep(#tableCircuits tbody td) {
  background: rgba(255, 255, 255, 0.74);
  border-color: rgba(94, 234, 212, 0.1);
}

.pcb-card :deep(#tableCircuits tbody tr:hover td) {
  background: rgba(236, 250, 247, 0.96);
}

.section-block {
  margin-top: 24px;
  padding-top: 24px;
  border-top: 1px solid #e5e7eb;
}

.section-block:first-of-type {
  margin-top: 0;
  padding-top: 0;
  border-top: none;
}

.section-head {
  margin-bottom: 18px;
}

.section-head h4,
.sub-section-header h5 {
  margin: 0 0 6px;
}

.section-head p,
.sub-section-header p {
  margin: 0;
  color: #6b7280;
  font-size: 0.92rem;
}

.detail-table {
  width: 100%;
  border-collapse: collapse;
}

.detail-table th,
.detail-table td {
  padding: 10px;
  border: 1px solid #e5e7eb;
  vertical-align: top;
}

.detail-table input,
.detail-table textarea {
  width: 100%;
}

.criteria-table td:first-child,
.criteria-table th:first-child {
  width: 220px;
}

.sub-section {
  margin-top: 24px;
}

.inline-actions {
  margin-top: 12px;
  display: flex;
  justify-content: flex-start;
}
.price-columns {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 20px;
}

.table-action-cell {
  width: 58px;
  text-align: center;
}

.preview-image {
  margin-top: 10px;
  width: 180px;
  height: 120px;
  object-fit: cover;
  border-radius: 12px;
}

.programme-preview-image {
  display: block;
  margin-top: 8px;
  width: 100%;
  max-width: 140px;
  height: 88px;
  object-fit: cover;
  border-radius: 10px;
}

@media (max-width: 900px) {
  .pcb-card__trace--top,
  .pcb-card__trace--side {
    display: none;
  }

  .price-columns {
    grid-template-columns: 1fr;
  }
}
</style>

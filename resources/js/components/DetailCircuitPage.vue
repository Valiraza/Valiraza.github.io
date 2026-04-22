<template>
  <div class="detail-circuit-page">
    <header class="main-header">
      <div class="nav-container">
        <div class="logo">
          <a href="/"><img :src="assetPath('img/logoautochtone-removebg-preview.png')" alt="Autochtone Tour Madagascar" class="logo-premium"></a>
        </div>
        <nav class="main-nav" :class="{ active: mobileNavOpen }">
          <ul>
            <li><a href="pageAT.vue" class="active">Accueil</a></li>
            <li><a href="#">A propos</a></li>
            <li><a href="#">Circuit Entreprise</a></li>
            <li><a href="#">Circuit Professionnel</a></li>
          </ul>
        </nav>
        <div class="nav-right">
          <button class="icon-btn" type="button"><i class="fa-solid fa-magnifying-glass"></i></button>
          <button class="icon-btn" type="button"><i class="fa-regular fa-user"></i></button>
          <button class="btn-cta" type="button" @click="openReservationModal">Reserver maintenant</button>
        </div>
        <div class="mobile-toggle" @click="mobileNavOpen = !mobileNavOpen">
          <i :class="mobileNavOpen ? 'fa-solid fa-xmark' : 'fa-solid fa-bars'"></i>
        </div>
      </div>
    </header>

    <div v-if="loading" class="container py-5"><p class="alert alert-info mb-0">Chargement du circuit...</p></div>
    <div v-else-if="error" class="container py-5"><p class="alert alert-warning mb-0">{{ error }}</p></div>

    <template v-else>
      <section class="hero-container-terdav">
        <div class="hero-slider-wrapper">
          <button class="slider-arrow prev" type="button" @click="moveSlide(-1)"><i class="fa-solid fa-chevron-left"></i></button>
          <div class="slider-main">
            <div v-for="(image, index) in heroImages" :key="`${image}-${index}`" class="slide" :class="{ active: index === currentSlide }" :style="{ backgroundImage: `url('${image}')` }"></div>
          </div>
          <button class="slider-arrow next" type="button" @click="moveSlide(1)"><i class="fa-solid fa-chevron-right"></i></button>
          <div class="slider-counter"><span>{{ currentSlide + 1 }}</span> / {{ heroImages.length }}</div>
        </div>
      </section>

      <section class="td-detail-section">
        <div class="td-container">
          <div class="td-main-content">
            <nav class="td-breadcrumb">{{ detailData.breadcrumb }}</nav>
            <h1 class="td-title">{{ detailData.titre }}</h1>
            <div class="td-header-meta">
              <div class="td-rating">
                <div class="td-stars"><i v-for="index in 5" :key="index" class="fa-solid fa-star"></i></div>
                <span class="td-reviews-count">{{ detailData.note }} ({{ detailData.nombre_avis }})</span>
              </div>
              <h2 class="td-subtitle">{{ detailData.sous_titre }}</h2>
              <div class="td-divider"></div>
            </div>
            <p class="td-description">{{ detailData.description }}</p>
            <div class="td-highlights">
              <h3 class="td-highlights-title">Les points forts</h3>
              <ul class="td-highlights-list">
                <li v-for="(point, index) in pointsForts" :key="`point-${index}`"><i class="fa-solid fa-check"></i><span>{{ point }}</span></li>
              </ul>
            </div>
          </div>

          <aside class="td-sidebar">
            <div class="td-floating-card">
              <div class="td-card-body">
                <div class="td-card-meta">
                  <span class="td-card-ref">Code : {{ detailData.code }}</span>
                  <span class="td-card-badge">{{ detailData.badge }}</span>
                </div>
                <h3 class="td-dest-name">{{ detailData.titre }}</h3>
                <div class="td-specs-grid">
                  <div class="td-spec-item" v-for="spec in specs" :key="spec.label">
                    <span class="td-spec-label">{{ spec.label }}</span>
                    <div class="td-spec-value-group">
                      <i :class="spec.icon"></i>
                      <span class="td-spec-value">{{ spec.value }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="td-card-footer">
                <div class="td-price-container">
                  <span class="td-price-label">{{ detailData.prix_label }}</span>
                  <div class="td-price-main"><span class="td-amount">{{ formatAr(circuit.prix) }}</span><span class="td-currency">Ar</span></div>
                  <span class="td-price-details">{{ detailData.prix_details }}</span>
                </div>
                <button class="td-btn-reserve" type="button" @click="openReservationModal"><span>{{ detailData.bouton_prix }}</span><i class="fa-solid fa-arrow-right"></i></button>
                <div class="td-card-links">
                  <a href="#" @click.prevent="openTechnicalModal(defaultTechnicalKey)"><i class="fa-solid fa-download"></i> Fiche technique</a>
                  <a href="#" @click.prevent="openReservationModal"><i class="fa-solid fa-envelope"></i> Demander un devis</a>
                </div>
              </div>
            </div>
          </aside>
        </div>
      </section>

      <section class="td-itinerary-section">
        <div class="td-itinerary-container">
          <aside class="td-sticky-column">
            <div class="td-sticky-wrapper">
              <h2 class="td-main-itinerary-title">Le programme jour par jour</h2>
              <div class="td-title-divider"></div>
              <div class="td-image-frame-modern">
                <img :src="itineraryImage" alt="Voyage Madagascar">
                <div class="td-photo-overlay"><span class="td-location-tag"><i class="fa-solid fa-location-dot"></i> {{ detailData.image_itineraire_legende }}</span></div>
              </div>
            </div>
          </aside>

          <div class="td-program-content">
            <div v-if="programme.length" class="td-modern-timeline">
              <article v-for="(day, index) in programme" :key="`day-${index}`" class="td-step-item">
                <div class="td-step-marker"><div class="td-circle-outline"></div></div>
                <div class="td-step-content">
                  <header class="td-step-header">
                    <span class="td-day-badge">Jour {{ day.jour || index + 1 }}</span>
                    <h3 class="td-step-title">{{ day.titre || day.destination || `Jour ${day.jour || index + 1}` }}</h3>
                  </header>
                  <div class="td-step-body">
                    <p v-if="day.notes" class="td-step-description">{{ day.notes }}</p>
                    <div v-if="day.image" class="td-step-visual"><img :src="resolveImage(day.image)" :alt="day.titre || `Jour ${index + 1}`"></div>
                    <div v-if="day.hebergement || day.repas || day.activites || day.transport" class="td-step-features">
                      <div v-for="feature in dayFeatures(day)" :key="feature.label" class="td-feature">
                        <i :class="feature.icon"></i>
                        <div class="td-feature-text"><span class="td-f-label">{{ feature.label }}</span><span class="td-f-val">{{ feature.value }}</span></div>
                      </div>
                    </div>
                  </div>
                </div>
              </article>
            </div>
            <p v-else class="alert alert-secondary">Aucun programme disponible pour ce circuit.</p>
          </div>
        </div>
      </section>

      <section class="td-price-details-section">
        <div class="td-price-container">
          <h2 class="td-main-itinerary-title">Prix et prestations</h2>
          <div class="td-title-divider"></div>
          <div class="td-price-grid">
            <div class="td-price-column">
              <h3 class="td-column-subtitle">Le prix comprend</h3>
              <ul class="td-price-list-fine">
                <li v-for="(item, index) in includedItems" :key="`in-${index}`"><i class="fa-solid fa-check td-icon-included"></i><p>{{ item }}</p></li>
              </ul>
            </div>
            <div class="td-vertical-separator"></div>
            <div class="td-price-column">
              <h3 class="td-column-subtitle">Le prix ne comprend pas</h3>
              <ul class="td-price-list-fine">
                <li v-for="(item, index) in excludedItems" :key="`out-${index}`"><i class="fa-solid fa-xmark td-icon-excluded"></i><p>{{ item }}</p></li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      <section class="td-privatisation-section">
        <div class="td-privatisation-card-green">
          <h2 class="td-main-itinerary-title-white">{{ detailData.privatisation_titre }}</h2>
          <div class="td-title-divider-white"></div>
          <div class="td-privatisation-content">
            <p class="td-privatisation-text-white">{{ detailData.privatisation_description }}</p>
            <a href="#" class="td-btn-quote-white" @click.prevent="openReservationModal">{{ detailData.privatisation_bouton }}</a>
          </div>
        </div>
      </section>

      <section class="td-section-wrapper">
        <div class="td-header">
          <h2 class="td-title">Fiche technique</h2>
          <div class="td-line"></div>
        </div>
        <div class="td-tech-grid">
          <div v-for="sheet in technicalSheets" :key="sheet.cle" class="td-tech-card" @click="openTechnicalModal(sheet.cle)">
            <i class="fa-solid fa-book-open td-card-icon"></i>
            <span class="td-card-text">{{ sheet.titre }}</span>
          </div>
        </div>
      </section>

      <div v-if="showTechnicalModal" class="td-modal-overlay" style="display: block;" @click.self="closeTechnicalModal">
        <div class="td-modal-container">
          <div class="td-modal-header">
            <div class="td-modal-top-bar">
              <h2 class="td-modal-main-title">Fiche technique</h2>
              <button class="td-modal-close" type="button" @click="closeTechnicalModal">&times;</button>
            </div>
            <div class="td-modal-tabs">
              <button v-for="sheet in technicalSheets" :key="sheet.cle" type="button" class="td-tab-btn" :class="{ active: activeTechnicalKey === sheet.cle }" @click="activeTechnicalKey = sheet.cle">
                <i class="fa-solid fa-book-open td-tab-icon"></i>
                <span class="td-tab-text">{{ sheet.titre }}</span>
              </button>
            </div>
          </div>
          <div class="td-modal-content">
            <div v-for="sheet in technicalSheets" :key="`pane-${sheet.cle}`" class="td-pane" :class="{ active: activeTechnicalKey === sheet.cle }">
              <h3 class="td-pane-title">{{ sheet.titre }}</h3>
              <p class="td-pane-desc">{{ sheet.description }}</p>
              <ul class="td-pane-list"><li v-for="(item, index) in sheet.elements" :key="`sheet-${sheet.cle}-${index}`">{{ item }}</li></ul>
            </div>
          </div>
        </div>
      </div>

      <div v-if="showReservationModal">
        <div class="modal-backdrop show"></div>
        <div class="modal fade show" style="display: block;" @click.self="closeReservationModal">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Reservation du circuit</h5>
                <button type="button" class="btn-close" @click="closeReservationModal"></button>
              </div>
              <div class="modal-body">
                <div class="step-bar">
                  <div class="step" :class="{ active: currentStep === 0 }">1</div>
                  <div class="step" :class="{ active: currentStep === 1 }">2</div>
                  <div class="step" :class="{ active: currentStep === 2 }">3</div>
                </div>
                <form @submit.prevent="submitReservation">
                  <div class="form-step" :class="{ active: currentStep === 0 }">
                    <h3>Informations personnelles</h3>
                    <input v-model="formData.fullname" type="text" class="form-control mb-3" placeholder="Nom complet">
                    <input v-model="formData.email" type="email" class="form-control mb-3" placeholder="Email">
                    <input v-model="formData.tel" type="tel" class="form-control mb-3" placeholder="Telephone">
                    <button type="button" class="btn btn-primary" @click="nextStep">Suivant</button>
                  </div>
                  <div class="form-step" :class="{ active: currentStep === 1 }">
                    <h3>Details du sejour</h3>
                    <div class="form-group mb-3">
                      <label>A combien voyagez-vous ?</label>
                      <div class="travellers-input">
                        <input type="text" :value="travellersSummary" readonly class="form-control" @click="showDropdown = !showDropdown">
                        <div v-if="showDropdown" class="dropdown">
                          <div v-for="(value, key) in formData.travellers" :key="key" class="dropdown-item">
                            <label>{{ travellerLabel(key) }}</label>
                            <input v-model.number="formData.travellers[key]" type="number" min="0" class="form-control">
                          </div>
                          <button type="button" class="btn btn-sm btn-success" @click="showDropdown = false">Valider</button>
                        </div>
                      </div>
                    </div>
                    <div class="form-group mb-3">
                      <label>Places</label>
                      <div class="travellers-input">
                        <input
                          type="text"
                          class="form-control"
                          readonly
                          :value="selectedSeats.length ? selectedSeats.join(', ') : 'Cliquez pour choisir vos places'"
                          @click="showBusPlan = !showBusPlan"
                        >
                        <div v-if="showBusPlan" class="bus-plan-overlay" @click.self="showBusPlan = false">
                        <div class="bus-plan-dropdown">
                          <div class="bus-plan">
                            <div class="bus-plan__title-wrap">
                              <h4 class="bus-plan__title">Plan du bus</h4>
                              <p class="bus-plan__subtitle">Choisissez vos places directement sur le plan.</p>
                            </div>
                            <div class="bus-plan__header">
                              <span class="bus-plan__legend"><span class="bus-plan__dot"></span> Disponible</span>
                              <span class="bus-plan__legend"><span class="bus-plan__dot bus-plan__dot--selected"></span> Selectionnee</span>
                              <span class="bus-plan__legend"><span class="bus-plan__dot bus-plan__dot--occupied"></span> Occupee</span>
                            </div>
                            <div class="bus-plan__body">
                              <div class="bus-plan__front">
                                <div class="bus-plan__driver">C</div>
                                <div class="bus-plan__front-seats">
                                  <button
                                    v-for="seat in frontSeats"
                                    :key="`front-${seat}`"
                                    type="button"
                                    class="seat bus-seat"
                                    :class="seatClass(seat)"
                                    :disabled="isSeatOccupied(seat)"
                                    @click="toggleSeat(seat)"
                                  >
                                    {{ seat }}
                                  </button>
                                </div>
                              </div>
                              <div v-for="(row, index) in busRows" :key="`row-${index}`" class="bus-plan__row">
                                <div class="bus-plan__side">
                                  <button
                                    v-for="seat in row.left"
                                    :key="`left-${seat}`"
                                    type="button"
                                    class="seat bus-seat"
                                    :class="seatClass(seat)"
                                    :disabled="isSeatOccupied(seat)"
                                    @click="toggleSeat(seat)"
                                  >
                                    {{ seat }}
                                  </button>
                                </div>
                                <div class="bus-plan__aisle"></div>
                                <div class="bus-plan__side">
                                  <button
                                    v-for="seat in row.right"
                                    :key="`right-${seat}`"
                                    type="button"
                                    class="seat bus-seat"
                                    :class="seatClass(seat)"
                                    :disabled="isSeatOccupied(seat)"
                                    @click="toggleSeat(seat)"
                                  >
                                    {{ seat }}
                                  </button>
                                </div>
                              </div>
                            </div>
                            <div class="bus-plan__actions">
                              <button type="button" class="btn btn-sm btn-success" @click="showBusPlan = false">Valider</button>
                            </div>
                          </div>
                        </div>
                        </div>
                      </div>
                    </div>
                    <button type="button" class="btn btn-secondary" @click="prevStep">Precedent</button>
                    <button type="button" class="btn btn-primary" @click="nextStep">Suivant</button>
                  </div>
                  <div class="form-step" :class="{ active: currentStep === 2 }">
                    <h3>Confirmation</h3>
                    <div class="alert alert-info">
                      <strong>Nom:</strong> {{ formData.fullname }}<br>
                      <strong>Email:</strong> {{ formData.email }}<br>
                      <strong>Telephone:</strong> {{ formData.tel }}<br>
                      <strong>Voyageurs:</strong> {{ travellersSummary }}<br>
                      <strong>Places:</strong> {{ selectedSeats.join(', ') || 'Aucune' }}
                    </div>
                    <button type="button" class="btn btn-secondary" @click="prevStep">Precedent</button>
                    <button type="submit" class="btn btn-success" :disabled="isSubmitting">{{ isSubmitting ? 'Envoi...' : 'Reserver' }}</button>
                  </div>
                  <div v-if="message.text" :class="['alert', `alert-${message.type}`]">{{ message.text }}</div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
const DEFAULT_CIRCUIT = {
  nom: 'Incontournables Madagascar',
  prix: 2450000,
  image_url: '/img/Andranotora.jpg',
  programme: [],
  detail: {
    breadcrumb: 'Accueil > Madagascar > Circuit accompagne',
    titre: 'Incontournables Madagascar',
    sous_titre: "L'ame de l'ile Rouge entre sommets et savanes",
    code: 'MADA01',
    badge: 'Coup de coeur',
    note: '4.8',
    nombre_avis: '12 avis',
    description: "Une traversee exclusive des hautes terres jusqu'au Grand Sud.",
    image_itineraire: '/img/road.jpg',
    image_itineraire_legende: "Massif de l'Andringitra",
    prix_label: 'Prix par personne a partir de',
    prix_details: 'Vol compris au depart de Paris',
    bouton_prix: 'Voir les dates et prix',
    privatisation_titre: 'Privatisez votre voyage',
    privatisation_description: 'Choisissez vos dates, votre rythme et vos envies pour vivre ce circuit en formule privee.',
    privatisation_bouton: 'Demander un devis',
    points_forts: ["L'exclusivite des bivouacs au pied de l'Andringitra.", 'La rencontre solidaire avec les paysans des Hautes Terres.', 'Un itineraire equilibre entre randonnee et detente.'],
    specifications: { duree: '15 jours', niveau: 'Tranquille', activite: 'Randonnee', groupe: '5 a 12 pers.' },
    prix_inclus: ['Les transferts et transports pendant le circuit.', "L'hebergement en chambre double.", 'La pension complete selon programme.'],
    prix_exclus: ["Les frais de visa a regler a l'arrivee.", 'Les boissons et depenses personnelles.', 'Les assurances optionnelles.'],
    fiche_technique: [{ cle: 'esprit', titre: "L'esprit du voyage", description: 'Une immersion authentique au coeur des regions visitees.', elements: ['Rencontres avec les populations locales.', 'Hebergements de charme et eco-responsables.', 'Rythme adapte pour une decouverte approfondie.'] }],
  },
};

export default {
  name: 'DetailCircuitPage',
  props: { initialCircuit: { type: Object, default: null } },
  data() {
    return {
      circuit: this.normalizeCircuit(this.initialCircuit || DEFAULT_CIRCUIT),
      loading: !this.initialCircuit,
      error: '',
      mobileNavOpen: false,
      currentSlide: 0,
      showTechnicalModal: false,
      activeTechnicalKey: 'esprit',
      showReservationModal: false,
      currentStep: 0,
      showDropdown: false,
      showBusPlan: false,
      isSubmitting: false,
      selectedSeats: [],
      occupiedSeats: [],
      message: { text: '', type: '' },
      formData: { fullname: '', email: '', tel: '', travellers: { adults: 1, children: 0, seniors: 0 } },
      occupiedSeatsRefreshInterval: null,
    };
  },
  computed: {
    slug() {
      const parts = window.location.pathname.split('/').filter(Boolean);
      return parts[parts.length - 1] || '';
    },
    detailData() {
      return this.circuit.detail || DEFAULT_CIRCUIT.detail;
    },
    programme() {
      return Array.isArray(this.circuit.programme) ? this.circuit.programme.filter((item) => item && (item.jour || item.titre || item.destination || item.notes || item.image)) : [];
    },
    heroImages() {
      const images = [this.resolveImage(this.circuit.image_url || this.circuit.image || DEFAULT_CIRCUIT.image_url), this.itineraryImage].filter(Boolean);
      return [...new Set(images)];
    },
    itineraryImage() {
      return this.resolveImage(this.detailData.image_itineraire || DEFAULT_CIRCUIT.detail.image_itineraire);
    },
    pointsForts() {
      return this.detailData.points_forts?.length ? this.detailData.points_forts : DEFAULT_CIRCUIT.detail.points_forts;
    },
    includedItems() {
      return this.detailData.prix_inclus?.length ? this.detailData.prix_inclus : DEFAULT_CIRCUIT.detail.prix_inclus;
    },
    excludedItems() {
      return this.detailData.prix_exclus?.length ? this.detailData.prix_exclus : DEFAULT_CIRCUIT.detail.prix_exclus;
    },
    technicalSheets() {
      return this.detailData.fiche_technique?.length ? this.detailData.fiche_technique : DEFAULT_CIRCUIT.detail.fiche_technique;
    },
    defaultTechnicalKey() {
      return this.technicalSheets[0]?.cle || 'esprit';
    },
    specs() {
      const spec = this.detailData.specifications || {};
      return [
        { label: 'DUREE', value: spec.duree, icon: 'fa-regular fa-calendar-days' },
        { label: 'NIVEAU', value: spec.niveau, icon: 'fa-solid fa-chart-simple' },
        { label: 'ACTIVITE', value: spec.activite, icon: 'fa-solid fa-person-hiking' },
        { label: 'GROUPE', value: spec.groupe, icon: 'fa-solid fa-user-group' },
      ];
    },
    travellersSummary() {
      const labels = [];
      const { adults, children, seniors } = this.formData.travellers;
      if (adults > 0) labels.push(`${adults} adulte(s)`);
      if (children > 0) labels.push(`${children} enfant(s)`);
      if (seniors > 0) labels.push(`${seniors} senior(s)`);
      return labels.length ? labels.join(', ') : 'Selectionnez...';
    },
    frontSeats() {
      return [1, 2];
    },
    busRows() {
      return [
        { left: [3, 4], right: [5, 6, 7] },
        { left: [8, 9], right: [10, 11, 12] },
        { left: [13, 14], right: [15, 16, 17] },
        { left: [18, 19], right: [20, 21, 22] },
        { left: [23, 24], right: [25, 26, 27] },
        { left: [28, 29], right: [30, 31, 32] },
      ];
    },
  },
  methods: {
    normalizeCircuit(source) {
      const circuit = source || {};
      return {
        ...DEFAULT_CIRCUIT,
        ...circuit,
        detail: {
          ...DEFAULT_CIRCUIT.detail,
          ...(circuit.detail || {}),
          specifications: { ...DEFAULT_CIRCUIT.detail.specifications, ...(circuit.detail?.specifications || {}) },
        },
      };
    },
    async loadCircuit() {
      if (this.initialCircuit || !this.slug || this.slug === 'detailcircuit') {
        this.loading = false;
        this.loadOccupiedSeats();
        return;
      }
      try {
        const response = await fetch(`/api/circuits/by-slug/${this.slug}`);
        if (!response.ok) throw new Error('Impossible de charger les details du circuit.');
        this.circuit = this.normalizeCircuit(await response.json());
        this.loadOccupiedSeats();
      } catch (error) {
        this.error = error.message;
      } finally {
        this.loading = false;
      }
    },
    assetPath(path) {
      return `/${path.replace(/^\/+/, '')}`;
    },
    resolveImage(path) {
      if (!path) return '';
      if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/')) return path;
      return `/storage/${path.replace(/^\/+/, '')}`;
    },
    formatAr(value) {
      return new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(Number(value || 0));
    },
    moveSlide(step) {
      if (!this.heroImages.length) return;
      this.currentSlide = (this.currentSlide + step + this.heroImages.length) % this.heroImages.length;
    },
    dayFeatures(day) {
      return [
        { label: 'Hebergement', value: day.hebergement, icon: 'fa-solid fa-bed' },
        { label: 'Repas', value: day.repas, icon: 'fa-solid fa-utensils' },
        { label: 'Activites', value: day.activites, icon: 'fa-solid fa-person-hiking' },
        { label: 'Transport', value: day.transport, icon: 'fa-solid fa-car-side' },
      ].filter((item) => item.value);
    },
    openTechnicalModal(key) {
      this.activeTechnicalKey = key || this.defaultTechnicalKey;
      this.showTechnicalModal = true;
      document.body.style.overflow = 'hidden';
    },
    closeTechnicalModal() {
      this.showTechnicalModal = false;
      document.body.style.overflow = this.showReservationModal ? 'hidden' : '';
    },
    openReservationModal() {
      this.showReservationModal = true;
      this.loadOccupiedSeats();
      this.startOccupiedSeatsRefresh();
      document.body.style.overflow = 'hidden';
    },
    closeReservationModal() {
      this.showReservationModal = false;
      this.stopOccupiedSeatsRefresh();
      this.message = { text: '', type: '' };
      this.showDropdown = false;
      this.showBusPlan = false;
      document.body.style.overflow = this.showTechnicalModal ? 'hidden' : '';
    },
    travellerLabel(key) {
      return { adults: 'Adults', children: 'Children', seniors: 'Seniors' }[key] || key;
    },
    seatClass(seatNumber) {
      return {
        selected: this.selectedSeats.includes(seatNumber),
        occupied: this.isSeatOccupied(seatNumber),
      };
    },
    isSeatOccupied(seatNumber) {
      return this.occupiedSeats.includes(seatNumber);
    },
    async loadOccupiedSeats() {
      if (!this.circuit?.id) {
        this.occupiedSeats = [];
        return;
      }

      try {
        const response = await fetch(`/api/reservations/occupied/${this.circuit.id}`);
        if (!response.ok) {
          throw new Error('Impossible de charger les places occupees.');
        }

        const data = await response.json();
        this.occupiedSeats = Array.isArray(data.occupied_seats) ? data.occupied_seats : [];
      } catch (error) {
        console.error(error);
        this.occupiedSeats = [];
      }
    },
    toggleSeat(seatNumber) {
      if (this.isSeatOccupied(seatNumber)) return;
      const index = this.selectedSeats.indexOf(seatNumber);
      if (index >= 0) this.selectedSeats.splice(index, 1);
      else this.selectedSeats.push(seatNumber);
      this.selectedSeats.sort((a, b) => a - b);
    },
    nextStep() {
      if (this.currentStep === 0) {
        if (!this.formData.fullname || !this.formData.email || !this.formData.tel) {
          this.message = { text: 'Veuillez remplir tous les champs obligatoires.', type: 'danger' };
          return;
        }
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(this.formData.email)) {
          this.message = { text: 'Veuillez entrer un email valide.', type: 'danger' };
          return;
        }
      }
      if (this.currentStep === 1) {
        const total = Object.values(this.formData.travellers).reduce((sum, value) => sum + Number(value || 0), 0);
        if (!total) {
          this.message = { text: 'Selectionnez au moins une personne.', type: 'danger' };
          return;
        }
      }
      this.message = { text: '', type: '' };
      if (this.currentStep < 2) this.currentStep += 1;
    },
    prevStep() {
      if (this.currentStep > 0) this.currentStep -= 1;
    },
    async submitReservation() {
      this.isSubmitting = true;
      const csrfToken = document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content') || '';
      try {
        const response = await fetch('/api/reservations', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
          body: JSON.stringify({
            circuit_id: this.circuit.id || null,
            circuit_nom: this.circuit.nom || this.detailData.titre,
            nom: this.formData.fullname,
            email: this.formData.email,
            telephone: this.formData.tel,
            voyageurs: this.travellersSummary,
            places: this.selectedSeats.join(','),
          }),
        });
        if (!response.ok) {
          const errorData = await response.json().catch(() => ({}));
          const errors = errorData.errors ? Object.values(errorData.errors).flat().join(', ') : '';
          throw new Error(errors || 'Reservation impossible.');
        }
        this.message = { text: 'Reservation creee avec succes.', type: 'success' };
        await this.loadOccupiedSeats();
        this.currentStep = 0;
        this.formData = { fullname: '', email: '', tel: '', travellers: { adults: 1, children: 0, seniors: 0 } };
        this.selectedSeats = [];
        setTimeout(() => this.closeReservationModal(), 1000);
      } catch (error) {
        this.message = { text: error.message, type: 'danger' };
      } finally {
        this.isSubmitting = false;
      }
    },
    handleEscape(event) {
      if (event.key !== 'Escape') return;
      if (this.showReservationModal) this.closeReservationModal();
      if (this.showTechnicalModal) this.closeTechnicalModal();
    },
    startOccupiedSeatsRefresh() {
      if (this.occupiedSeatsRefreshInterval) return;
      this.occupiedSeatsRefreshInterval = setInterval(() => {
        if (this.showReservationModal) {
          this.loadOccupiedSeats();
        }
      }, 5000);
    },
    stopOccupiedSeatsRefresh() {
      if (this.occupiedSeatsRefreshInterval) {
        clearInterval(this.occupiedSeatsRefreshInterval);
        this.occupiedSeatsRefreshInterval = null;
      }
    },
  },
  mounted() {
    this.activeTechnicalKey = this.defaultTechnicalKey;
    this.loadCircuit();
    window.addEventListener('keydown', this.handleEscape);
  },
  beforeUnmount() {
    this.stopOccupiedSeatsRefresh();
    window.removeEventListener('keydown', this.handleEscape);
    document.body.style.overflow = '';
  },
};
</script>

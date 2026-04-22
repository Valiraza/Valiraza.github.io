<template>
  <div>
    <header class="main-header" :style="{ height: headerHeight, background: headerBackground }">
      <div class="nav-container">
        <div class="logo"><a href="/"><img :src="logoSrc" alt="Autochtone Tour Madagascar" class="logo-premium"></a></div>
        <nav id="main-nav" class="main-nav" :class="{ active: menuActive }">
          <ul>
            <li><a href="/pageAT" class="active">Accueil</a></li>
            <li><a href="#catalog-intro">À propos</a></li>
            <li><a href="#catalog-types">Circuit Entreprise</a></li>
            <li><a href="#catalog-list">Circuit Professionnel</a></li>
          </ul>
        </nav>
        <div class="nav-right">
          <button class="icon-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
          <button class="icon-btn"><i class="fa-regular fa-user"></i></button>
          <a href="/contact" class="btn-cta">CONTACTEZ-NOUS</a>
        </div>
        <div id="mobile-toggle" class="mobile-toggle" @click="toggleMenu"><i class="fa-solid" :class="menuActive ? 'fa-xmark' : 'fa-bars'"></i></div>
      </div>
    </header>

    <main>
      <section class="hero-section" :style="{ backgroundImage: `url('${heroImageSrc}')` }">
        <div class="hero-image-overlay"></div>
        <div class="hero-content">
          <h1>{{ catalog.hero_title || catalog.title }}</h1>
          <p>{{ catalog.hero_subtitle }}</p>
        </div>
      </section>
    </main>

    <section id="catalog-intro" class="py-16 bg-white font-body text-slate-900">
      <div class="max-w-7xl mx-auto px-6">
        <div class="mb-12 grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          <div class="lg:col-span-8">
            <h1 class="td-main-title-custom">{{ catalog.heading || catalog.title }}</h1>
            <div class="flex items-center gap-3 mb-6">
              <div class="flex text-yellow-400 text-[10px] gap-0.5">
                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
              </div>
              <span class="text-[11px] font-bold text-slate-700 tracking-wide">4.8/5</span>
              <span class="text-[10px] text-slate-400 font-medium uppercase tracking-[0.15em] ml-2">Basé sur nos expériences terrain</span>
            </div>
            <p class="text-[15px] leading-relaxed font-light max-w-3xl text-[#3e2723]">
              {{ catalog.description }}
            </p>
          </div>
        </div>

        <div id="catalog-types" class="mb-10 flex flex-wrap gap-3">
          <a
            v-for="item in typeLinks"
            :key="item.slug"
            :href="item.href"
            class="catalog-type-link"
            :class="{ active: item.slug === catalogSlug }"
          >
            <span class="catalog-type-pill">
              <i class="fa-solid fa-compass"></i>
              {{ item.label }}
            </span>
          </a>
        </div>

        <div class="mb-8">
          <h2 class="td-main-title-custom">Destinations</h2>
        </div>

        <div class="flex gap-3 overflow-x-auto no-scrollbar pb-8">
          <div
            v-for="destination in destinations"
            :key="destination.name"
            class="vignette-item group cursor-pointer text-center"
          >
            <div class="relative h-36 overflow-hidden rounded-t-[1.25rem] mb-3 shadow-sm group-hover:shadow-lg transition-all duration-500">
              <img :src="destination.image" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" :alt="destination.name">
            </div>
            <h4 class="text-[10px] font-bold uppercase tracking-wider text-slate-500 group-hover:text-[#4a7c2c]">{{ destination.name }}</h4>
          </div>

          <div class="vignette-item group text-center">
            <a href="#catalog-list" class="relative h-36 flex flex-col items-center justify-center bg-slate-200 border border-slate-300 rounded-t-[1.25rem] group-hover:border-[#4a7c2c] group-hover:bg-white transition-all duration-300 shadow-inner">
              <div class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center mb-2 group-hover:bg-[#4a7c2c] transition-all shadow-sm">
                <i class="fa-solid fa-plus text-[12px] text-[#8B4513] group-hover:text-white transition-colors"></i>
              </div>
              <span class="text-[9px] font-bold uppercase tracking-[0.2em] text-slate-600 group-hover:text-[#4a7c2c]">Explorer</span>
            </a>
          </div>
        </div>
      </div>
    </section>

    <section id="catalog-list">
      <div v-if="formattedCircuits.length === 0" class="empty-state">
        <div class="rounded-[1.5rem] border border-stone-200 bg-white px-8 py-16 text-center shadow-sm">
          <h3 class="text-xl font-bold text-stone-900">Aucun circuit disponible pour le moment</h3>
          <p class="mt-3 text-sm text-stone-500">Les circuits de cette catégorie apparaîtront ici dès qu'ils seront publiés.</p>
        </div>
      </div>

      <div v-for="circuit in formattedCircuits" :key="circuit.id" class="td-wide-wrapper">
        <article class="td-card-soft">
          <div class="td-visual-zone">
            <div class="td-arrows">
              <button class="arrow-circ" type="button"><i class="fa-solid fa-chevron-left"></i></button>
              <button class="arrow-circ" type="button"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
            <img :src="circuit.image" :alt="circuit.title">
          </div>

          <div class="td-content-zone">
            <div class="td-main-body">
              <div class="td-header-flex">
                <h3 class="td-mini-title">{{ circuit.title }}</h3>
                <div class="td-price-tag-side">{{ circuit.price }}</div>
              </div>

              <div class="td-meta-row">
                <span class="m-loc">{{ circuit.destination }}</span>
                <span class="m-dot"></span>
                <span class="m-days">{{ circuit.duration }}</span>
                <span class="m-dot"></span>
                <div class="m-stars">
                  <i class="fas fa-star"></i>
                  <span class="m-count">{{ circuit.places }}</span>
                </div>
              </div>

              <div class="td-technical-stack">
                <span class="t-text">{{ circuit.level }}</span>
                <span class="t-text">Prochain départ : {{ circuit.departure }}</span>
              </div>

              <p class="td-description-text">{{ circuit.description }}</p>
            </div>

            <div class="td-divider-separator"></div>

            <div class="td-action-buttons">
              <a v-if="circuit.detailsAvailable" :href="`/circuit/${circuit.slug}`" class="btn-td btn-dark">Details</a>
              <span v-else class="btn-td btn-dark btn-td-disabled" title="détails du circuit indisponibles">
                détails du circuit indisponibles
              </span>
              <a href="/contact" class="btn-td btn-outline">Pour l'entreprise</a>
              <a href="/contact" class="btn-td btn-outline">Professionnel</a>
            </div>
          </div>
        </article>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
  catalog: { type: Object, default: () => ({}) },
  catalogSlug: { type: String, default: '' },
  catalogTypes: { type: Object, default: () => ({}) },
  circuits: { type: Array, default: () => [] },
  assetBase: { type: String, default: '/' },
});

const menuActive = ref(false);
const headerHeight = ref('80px');
const headerBackground = ref('');

function assetPath(path) {
  const base = props.assetBase.endsWith('/') ? props.assetBase : `${props.assetBase}/`;
  return `${base}${path.replace(/^\/+/, '')}`;
}

const logoSrc = assetPath('img/logoautochtone-removebg-preview.png');
const heroImageSrc = assetPath('img/Andranotora.jpg');

const typeLinks = computed(() => Object.entries(props.catalogTypes).map(([slug, config]) => ({
  slug,
  label: config.title,
  href: `/circuits/${slug}`,
})));

const formattedCircuits = computed(() => props.circuits.map((circuit) => ({
  id: circuit.id,
  slug: circuit.slug,
  title: circuit.nom,
  detailsAvailable: Boolean(circuit.depart && circuit.retour),
  destination: circuit.destination || 'Madagascar',
  duration: formatDuration(circuit.depart, circuit.retour),
  places: formatPlaces(circuit.places),
  departure: formatDate(circuit.depart),
  price: formatPrice(circuit.prix),
  level: inferLevel(circuit.type, circuit.description),
  description: circuit.description || `Découvrez ${circuit.nom} à travers ${circuit.destination || 'Madagascar'}.`,
  image: circuit.image_url || circuit.image || assetPath('img/49eme.jpg'),
})));

const destinations = computed(() => {
  const seen = new Set();

  return formattedCircuits.value
    .filter((circuit) => {
      if (!circuit.destination || seen.has(circuit.destination)) {
        return false;
      }

      seen.add(circuit.destination);
      return true;
    })
    .slice(0, 8)
    .map((circuit) => ({
      name: circuit.destination,
      image: circuit.image,
    }));
});

function toggleMenu() {
  menuActive.value = !menuActive.value;
}

function handleScroll() {
  const isCompact = window.scrollY > 50;
  headerHeight.value = isCompact ? '65px' : '80px';
  headerBackground.value = isCompact ? 'rgba(255, 255, 255, 0.98)' : '';
}

function formatPrice(value) {
  const amount = Number(value || 0);

  if (!amount) {
    return 'Sur demande';
  }

  return `Des ${new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(amount)} Ar`;
}

function formatPlaces(value) {
  const places = Number(value || 0);
  return places > 0 ? `${places} places` : 'Places limitées';
}

function formatDate(value) {
  if (!value) {
    return 'À confirmer';
  }

  const date = new Date(value);

  if (Number.isNaN(date.getTime())) {
    return 'À confirmer';
  }

  return new Intl.DateTimeFormat('fr-FR').format(date);
}

function formatDuration(start, end) {
  if (!start || !end) {
    return 'Durée sur mesure';
  }

  const startDate = new Date(start);
  const endDate = new Date(end);

  if (Number.isNaN(startDate.getTime()) || Number.isNaN(endDate.getTime())) {
    return 'Durée sur mesure';
  }

  const days = Math.max(1, Math.round((endDate - startDate) / 86400000) + 1);
  return `${days} jours`;
}

function inferLevel(type, description) {
  if (type === 'Randonnee & Trek') {
    const text = (description || '').toLowerCase();

    if (text.includes('niveau 3')) return 'Niveau 3';
    if (text.includes('niveau 2')) return 'Niveau 2';
    if (text.includes('niveau 1')) return 'Niveau 1';
    return 'Tous niveaux';
  }

  if (type === 'Classe Verte') return 'Programme pédagogique';
  if (type === 'Team Building') return 'Cohésion encadrée';
  if (type === 'Circuit Sejour') return 'Confort & découverte';
  return 'Expérience encadrée';
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
  handleScroll();
});

onBeforeUnmount(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

<style scoped>
.btn-td-disabled {
  opacity: 0.55;
  pointer-events: none;
  cursor: not-allowed;
  text-align: center;
}
</style>

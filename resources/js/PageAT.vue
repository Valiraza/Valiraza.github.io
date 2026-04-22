<template>
  <div>
    <header class="main-header" :style="{ height: headerHeight, background: headerBackground }">
      <div class="nav-container">
        <div class="logo"><a href="/"><img :src="logoSrc" alt="Autochtone Tour Madagascar" class="logo-premium"></a></div>
        <nav id="main-nav" class="main-nav" :class="{ active: menuActive }">
          <ul>
            <li><a href="/" class="active">Accueil</a></li>
            <li><a href="#engagement">À propos</a></li>
            <li><a href="#collection">Circuit Entreprise</a></li>
            <li><a href="#collection">Circuit Professionnel</a></li>
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
          <h1>Madagascar, l'aventure authentique</h1>
          <p>Découvrez nos treks et circuits exclusifs à travers l'île Rouge.</p>
        </div>
        <div class="search-wrapper">
          <form id="search-form" class="search-bar" @submit.prevent="handleSearch">
            <div class="search-group">
              <label>Destination</label>
              <select id="region" v-model="searchData.region">
                <option value="">Où souhaitez-vous aller ?</option>
                <option value="sud">Le Grand Sud (RN7)</option>
                <option value="nord">Le Nord Sauvage</option>
                <option value="est">L'Est &amp; Sainte-Marie</option>
                <option value="ouest">L'Ouest (Baobabs)</option>
              </select>
            </div>
            <div class="search-group">
              <label>Activité</label>
              <select id="activity" v-model="searchData.activity">
                <option value="">Type de voyage</option>
                <option value="trek">Trek &amp; Randonnée</option>
                <option value="nature">Nature &amp; Faune</option>
                <option value="culture">Rencontre &amp; Culture</option>
              </select>
            </div>
            <div class="search-group">
              <label>Départ</label>
              <input id="date-departure" v-model="searchData.date" type="month">
            </div>
            <button type="submit" class="btn-search">
              <template v-if="isSearching"><i class="fa-solid fa-spinner fa-spin"></i> RECHERCHE...</template>
              <template v-else>TROUVER MON VOYAGE <i class="fa-solid fa-chevron-right"></i></template>
            </button>
          </form>
        </div>
      </section>
    </main>

    <section id="engagement" class="relative pt-24 pb-20 bg-stone-50 overflow-hidden">
      <div class="absolute top-0 right-0 w-64 h-64 opacity-5 pointer-events-none"><svg viewBox="0 0 100 100" fill="none" stroke="currentColor" class="text-emerald-900"><path d="M0 20 L20 0 M20 40 L40 20 M40 60 L60 40 M60 80 L80 60 M80 100 L100 80" stroke-width="2"/></svg></div>
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-16">
          <span class="text-[#4a7c2c] font-bold uppercase tracking-widest text-sm">Notre Engagement</span>
          <h2 class="mt-3 text-4xl md:text-5xl font-serif text-stone-900">Une Vision Responsable de la Grande Île</h2>
          <div class="mt-4 w-24 h-1 bg-[#8b4513] mx-auto"></div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <div class="space-y-6">
            <p class="text-lg text-stone-600 leading-relaxed">Notre mission dépasse le simple voyage. Nous concevons des expériences qui honorent la terre des ancêtres tout en bâtissant l'avenir des générations futures. Notre approche repose sur un équilibre fragile mais essentiel entre la découverte et la préservation.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-8">
              <div class="flex items-start space-x-4">
                <div class="bg-emerald-100 p-3 rounded-lg"><svg class="w-6 h-6 text-[#4a7c2c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg></div>
                <div><h4 class="font-bold text-stone-900">Tourisme Durable</h4><p class="text-sm text-stone-500">Des itinéraires pensés pour minimiser l'empreinte carbone.</p></div>
              </div>
              <div class="flex items-start space-x-4">
                <div class="bg-amber-100 p-3 rounded-lg"><svg class="w-6 h-6 text-[#8b4513]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg></div>
                <div><h4 class="font-bold text-stone-900">Soutien Local</h4><p class="text-sm text-stone-500">80% de nos revenus sont réinjectés dans les communautés villageoises.</p></div>
              </div>
            </div>
          </div>
          <div class="relative">
            <div class="grid grid-cols-2 gap-4">
              <img :src="natureImageSrc" alt="Nature Madagascar" class="rounded-2xl shadow-lg transform translate-y-8 object-cover h-64 w-full">
              <img :src="cultureImageSrc" alt="Culture Malgache" class="rounded-2xl shadow-lg object-cover h-64 w-full">
            </div>
            <div class="absolute -bottom-4 left-1/2 transform -translate-x-1/2 bg-white px-8 py-4 rounded-full shadow-xl flex items-center space-x-3 border border-emerald-100"><span class="flex h-3 w-3 rounded-full bg-[#4a7c2c] animate-pulse"></span><span class="text-stone-800 font-medium">100% Engagement Éthique</span></div>
          </div>
        </div>
      </div>
    </section>

    <section class="relative py-24 bg-[#fcfaf7] overflow-hidden">
      <div class="absolute top-0 left-0 w-64 h-64 bg-[#4a7c2c]/5 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl"></div>
      <div class="absolute bottom-0 right-0 w-96 h-96 bg-[#8b4513]/5 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl"></div>
      <div class="absolute top-10 right-10 opacity-[0.03] pointer-events-none hidden lg:block"><svg width="200" height="400" viewBox="0 0 100 200" fill="none" stroke="#2d1a10" stroke-width="0.5"><path d="M50 0V200M30 20L70 20M35 40L65 40M20 180H80" /><circle cx="50" cy="70" r="15" /><rect x="35" y="100" width="30" height="40" /></svg></div>
      <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
        <div class="flex flex-col lg:flex-row lg:items-end justify-between mb-20 gap-8">
          <div class="max-w-3xl">
            <div class="flex items-center gap-3 mb-4"><span class="h-[2px] w-12 bg-[#4a7c2c]"></span><h2 class="text-[#4a7c2c] font-bold uppercase tracking-[0.2em] text-xs">Aventure Authentique</h2></div>
            <h3 class="text-4xl md:text-6xl font-serif text-[#2d1a10] leading-[1.1]">Explorez Madagascar <br><span class="relative inline-block"><span class="relative z-10 text-[#8b4513] italic">autrement</span><svg class="absolute -bottom-2 left-0 w-full h-3 text-[#4a7c2c]/20" viewBox="0 0 100 10" preserveAspectRatio="none"><path d="M0 5 Q 25 0 50 5 T 100 5" fill="none" stroke="currentColor" stroke-width="4"/></svg></span></h3>
          </div>
          <div class="lg:max-w-xs"><p class="text-[#5d4037] text-sm leading-relaxed italic border-l-2 border-[#4a7c2c] pl-6 py-2">"Ny rano tsy hiditra raha tsy misy mpitari-dalana" <br><span class="text-[10px] uppercase font-bold tracking-tighter not-italic text-stone-400">— Sagesse locale</span></p></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <article v-for="item in featuredCards" :key="item.title" class="group relative h-[520px] rounded-[2.5rem] overflow-hidden bg-[#2d1a10] shadow-xl transition-all duration-500 hover:shadow-[#4a7c2c]/20">
            <a :href="item.href" class="absolute inset-0 z-10" :aria-label="`Voir ${item.title}`"></a>
            <img :src="item.image" :alt="item.alt" class="absolute inset-0 h-full w-full object-cover opacity-80 transition-transform duration-1000 group-hover:scale-110" loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-t from-[#2d1a10] via-[#2d1a10]/40 to-transparent"></div>
            <div class="absolute inset-0 p-8 flex flex-col justify-end">
              <div class="mb-4 translate-y-8 group-hover:translate-y-0 transition-transform duration-500">
                <span :class="item.badgeClass" class="inline-block px-3 py-1 rounded-full text-[10px] font-bold text-white uppercase tracking-widest mb-4">{{ item.badge }}</span>
                <h4 class="text-3xl font-bold text-white font-serif mb-2">{{ item.title }}</h4>
                <p class="text-stone-200 text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100">{{ item.description }}</p>
              </div>
              <div class="flex items-center justify-between py-4 border-t border-white/10 opacity-0 group-hover:opacity-100 transition-all duration-500"><span class="text-white text-xs font-bold uppercase tracking-tighter">{{ item.linkLabel }}</span><div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[#2d1a10] -rotate-45 group-hover:rotate-0 transition-transform duration-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></div></div>
            </div>
          </article>
        </div>
      </div>
    </section>

    <section id="collection" class="collection-section py-24 bg-white font-exodus overflow-hidden">
      <div class="max-w-7xl mx-auto px-6 lg:px-8 relative">
        <div class="max-w-2xl mb-12">
          <h2 class="text-3xl md:text-5xl font-bold tracking-tight text-slate-900 mb-6 leading-tight">Gens du pays = <span class="text-[#4a7c2c]">Aventures extraordinaires.</span></h2>
          <p class="text-lg text-slate-600 font-light">Petits groupes. Meilleurs guides locaux. Itinéraires façonnés avec expertise.</p>
        </div>
        <div class="relative group">
          <button class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 z-20 hidden md:flex w-14 h-14 rounded-full bg-white border border-stone-200 items-center justify-center shadow-xl hover:bg-[#4a7c2c] hover:text-white transition-all duration-300" @click="scrollSlider('slider', -450)"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
          <button class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 z-20 hidden md:flex w-14 h-14 rounded-full bg-white border border-stone-200 items-center justify-center shadow-xl hover:bg-[#4a7c2c] hover:text-white transition-all duration-300" @click="scrollSlider('slider', 450)"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>
          <div id="slider" class="flex overflow-x-auto gap-6 no-scrollbar snap-x snap-mandatory pb-8 scroll-smooth">
            <div v-for="item in sliderCircuits" :key="item.id || item.title" class="public-circuit-card min-w-[85%] md:min-w-[32%] snap-start group/item cursor-pointer relative h-[550px] overflow-hidden rounded-3xl" :class="{ 'public-circuit-card--disabled': item.detailsAvailable === false }">
              <a v-if="item.detailsAvailable !== false" :href="item.href" class="absolute inset-0 z-10" :aria-label="`Voir ${item.title}`"></a>
              <div v-else class="absolute inset-0 z-10 cursor-not-allowed" aria-label="détails du circuit indisponibles"></div>
              <img :src="item.image" :alt="item.title" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover/item:scale-110">
              <div class="absolute inset-0 card-gradient"></div>
              <div class="public-circuit-card__content absolute inset-0 p-10 flex flex-col justify-end text-white">
                <span class="public-circuit-card__category text-[10px] font-bold uppercase tracking-[0.3em] mb-2">{{ item.category }}</span>
                <h4 class="public-circuit-card__title text-2xl font-bold mb-3">{{ item.title }}</h4>
                <p v-if="item.description" class="public-circuit-card__description text-sm text-stone-100">{{ item.description }}</p>
                <div class="public-circuit-card__meta mt-4 flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest border-b border-white w-fit pb-1">{{ item.meta }}</div>
                <div v-if="item.detailsAvailable === false" class="mt-4 inline-flex w-fit rounded-full bg-white/15 px-4 py-2 text-[10px] font-bold uppercase tracking-[0.2em] text-white">
                  détails du circuit indisponibles
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="collection-cta-wrap mt-16 flex justify-center"><a href="/circuits/randonnee-trek" class="collection-button px-10 py-4 bg-[#2d1a10] text-white text-[10px] font-bold uppercase tracking-[0.2em] rounded-full hover:bg-[#4a7c2c] transition-all shadow-xl">Consulter toute la collection</a></div>
      </div>
    </section>

    <section class="testimonials-section py-16 bg-white font-sans">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="testimonials-shell relative rounded-[2.5rem] overflow-hidden bg-cover bg-center py-20 px-4 md:px-12" :style="{ backgroundImage: `url('${landscapeImageSrc}')` }">
          <div class="absolute inset-0 bg-overlay-dark"></div>
          <div class="relative z-10">
            <div class="text-center mb-12"><h2 class="text-white font-bold uppercase tracking-[0.2em] text-[10px] mb-3">Témoignages</h2><h3 class="text-3xl md:text-4xl font-extrabold text-white leading-tight">Ce que disent nos <span class="italic font-serif font-normal text-white">voyageurs</span></h3></div>
            <div class="testimonials-track-wrap relative max-w-5xl mx-auto">
              <button class="absolute -left-6 md:-left-16 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-white flex items-center justify-center shadow-lg hover:bg-[#4a7c2c] hover:text-white transition-all" @click="scrollSlider('review-slider-compact', -320)"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
              <button class="absolute -right-6 md:-right-16 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-white flex items-center justify-center shadow-lg hover:bg-[#4a7c2c] hover:text-white transition-all" @click="scrollSlider('review-slider-compact', 320)"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>
              <div id="review-slider-compact" class="flex overflow-x-auto gap-5 no-scrollbar snap-x snap-mandatory scroll-smooth">
                <div v-for="review in reviews" :key="review.author" class="min-w-[280px] md:min-w-[300px] snap-start bg-white p-7 rounded-2xl shadow-xl flex flex-col h-[320px] justify-between transition-transform duration-300 hover:scale-[1.02]">
                  <div><div class="flex text-yellow-400 text-xs mb-3">★★★★★</div><h4 class="text-slate-900 font-bold text-lg mb-2">{{ review.title }}</h4><p class="text-stone-600 text-[13px] leading-relaxed line-clamp-6">"{{ review.text }}"</p></div>
                  <div class="pt-4 border-t border-stone-50"><p class="font-bold text-slate-900 text-[12px] uppercase">{{ review.author }}</p><p class="text-[#4a7c2c] text-[11px] font-semibold">{{ review.trip }}</p></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <footer class="footer-custom-bg pt-24 pb-12 text-stone-300 overflow-hidden font-jakarta">
      <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 mb-20">
          <div class="lg:col-span-5 space-y-10">
            <div class="space-y-6"><img :src="logoSrc" alt="Autochtone Tour Madagascar" class="h-24 md:h-28 w-auto object-contain brightness-110"><p class="text-stone-400 text-sm leading-relaxed max-w-sm font-light">Tour opérateur national basé à Antananarivo. Nous explorons Madagascar avec authenticité, façonnant des voyages qui respectent notre biodiversité et nos communautés locales.</p></div>
            <div class="relative w-full h-52 rounded-2xl overflow-hidden border border-white/5 shadow-2xl group">
              <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1887.164836148139!2d47.53616994457584!3d-18.916796436066956!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x21f07d8a6f036021%3A0x4b08b7b5a8a9306e!2sAutochtone%20Tour%20Madagascar!5e0!3m2!1sfr!2smg!4v1772621761856!5m2!1sfr!2smg" width="600" height="450" style="border: 0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
              <div class="absolute bottom-3 left-3 bg-[#0f0f0f]/80 backdrop-blur-md px-3 py-1 rounded-full text-[10px] text-white/60 border border-white/10 pointer-events-none">Siège social • Tana</div>
            </div>
          </div>
          <div class="lg:col-span-3">
            <h4 class="text-[11px] font-bold uppercase tracking-[0.3em] text-white mb-10 relative inline-block">
              Exploration
              <span class="absolute -bottom-3 left-0 w-8 h-0.5 bg-[#4a7c2c]"></span>
            </h4>
            <ul class="space-y-5 text-sm font-light text-stone-400">
              <li><a href="/circuits/randonnee-trek" class="footer-link hover:text-white flex items-center">Circuits Incontournables</a></li>
              <li><a href="#collection" class="footer-link hover:text-white flex items-center">Aventures &amp; Treks</a></li>
              <li><a href="#engagement" class="footer-link hover:text-white flex items-center">Écotourisme &amp; Engagement</a></li>
              <li><a href="/contact" class="footer-link hover:text-white flex items-center">Voyages Privés sur Mesure</a></li>
              <li><a href="#review-slider-compact" class="footer-link hover:text-white flex items-center">Témoignages Clients</a></li>
            </ul>
          </div>
          <div class="lg:col-span-4 space-y-12">
            <div>
              <h4 class="text-[11px] font-bold uppercase tracking-[0.3em] text-white mb-10 relative inline-block">Contact Direct<span class="absolute -bottom-3 left-0 w-8 h-0.5 bg-[#4a7c2c]"></span></h4>
              <div class="text-sm space-y-5 text-stone-400">
                <a href="https://maps.google.com" target="_blank" class="group flex items-start gap-4 hover:text-white transition-colors"><div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center group-hover:bg-[#4a7c2c]/20 transition-colors"><svg class="w-4 h-4 text-[#4a7c2c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div><span>Antananarivo 101, Madagascar</span></a>
                <a href="tel:+261340000000" class="group flex items-center gap-4 hover:text-white transition-colors"><div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center group-hover:bg-[#4a7c2c]/20 transition-colors"><svg class="w-4 h-4 text-[#4a7c2c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg></div><span>+261 34 00 000 00</span></a>
              </div>
            </div>
            <div class="flex gap-3">
              <a href="#" class="w-11 h-11 rounded-xl bg-white/5 flex items-center justify-center hover:bg-[#1877F2] hover:text-white hover:-translate-y-1 transition-all duration-300 border border-white/5"><i class="fab fa-facebook-f text-sm"></i></a>
              <a href="#" class="w-11 h-11 rounded-xl bg-white/5 flex items-center justify-center hover:bg-[#E4405F] hover:text-white hover:-translate-y-1 transition-all duration-300 border border-white/5"><i class="fab fa-instagram text-sm"></i></a>
              <a href="#" class="w-11 h-11 rounded-xl bg-white/5 flex items-center justify-center hover:bg-[#25D366] hover:text-white hover:-translate-y-1 transition-all duration-300 border border-white/5"><i class="fab fa-whatsapp text-sm"></i></a>
            </div>
          </div>
        </div>
        <div class="pt-10 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-8">
          <div class="flex items-center gap-8 opacity-40 hover:opacity-100 transition-opacity"><span class="text-[9px] font-bold uppercase tracking-widest border border-white/20 px-2 py-1 rounded">Licence N° XXXX-A</span><span class="text-[9px] font-bold uppercase tracking-widest">Membre ONTM</span></div>
          <div class="flex flex-col items-center md:items-end gap-2"><p class="text-stone-500 text-[9px] uppercase tracking-[0.3em]">© 2026 Autochtone Tour Madagascar.</p><p class="text-stone-600 text-[8px] uppercase tracking-[0.1em]">Design d'excellence pour aventuriers conscients</p></div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
const props = defineProps({ circuits: { type: Array, default: () => [] }, assetBase: { type: String, default: '/' } });
const searchData = ref({ region: '', activity: '', date: '' });
const menuActive = ref(false);
const isSearching = ref(false);
const headerHeight = ref('80px');
const headerBackground = ref('');
function assetPath(path) { const base = props.assetBase.endsWith('/') ? props.assetBase : `${props.assetBase}/`; return `${base}${path.replace(/^\/+/, '')}`; }
const logoSrc = assetPath('img/logoautochtone-removebg-preview.png');
const heroImageSrc = assetPath('img/Andranotora.jpg');
const natureImageSrc = assetPath('img/madagascar-4601287_1920.jpg');
const cultureImageSrc = assetPath('img/baobab.jpg');
const landscapeImageSrc = assetPath('img/paysage.jpg');
const featuredCards = [
  { badge: 'Nature', badgeClass: 'bg-[#4a7c2c]', title: 'Randonnée & Trek', description: "Des sommets de l'Andringitra aux sentiers ocre du Vakinankaratra.", image: assetPath('img/allee.jpg'), alt: 'Trek dans les massifs malgaches', href: '/circuits/randonnee-trek', linkLabel: "Découvrir l'itinéraire" },
  { badge: 'Éducation', badgeClass: 'bg-amber-600', title: 'Classe Verte', description: "Transmettre l'amour de la biodiversité aux générations futures.", image: assetPath('img/49eme.jpg'), alt: 'Classe verte éducation environnementale', href: '/circuits/classe-verte', linkLabel: 'Explorer les programmes' },
  { badge: 'Fivondronana', badgeClass: 'bg-blue-600', title: 'Team Building', description: "Forger l'esprit d'équipe au cœur de l'exceptionnel.", image: assetPath('img/baobab.jpg'), alt: "Cohésion d'équipe Madagascar", href: '/circuits/team-building', linkLabel: 'Offres entreprises' },
  { badge: 'Art de vivre', badgeClass: 'bg-[#8b4513]', title: 'Circuit Séjour', description: "L'harmonie entre confort et authenticité culturelle.", image: assetPath('img/lemu.jpg'), alt: 'Détente et culture malgache', href: '/circuits/circuit-sejour', linkLabel: "S'évader" },
];
const fallbackCircuits = [
  { id: 'fallback-1', category: 'Culture', title: 'La Route Royale', image: assetPath('img/madagascar-4601287_1920.jpg'), description: '', meta: 'Explorer', href: '/detailcircuit' },
  { id: 'fallback-2', category: 'Aventure', title: 'Allée des Baobabs', image: assetPath('img/baobab.jpg'), description: '', meta: 'Explorer', href: '/detailcircuit' },
  { id: 'fallback-3', category: 'Sérénité', title: "L'Émeraude du Nord", image: assetPath('img/sunset.jpg'), description: '', meta: 'Explorer', href: '/detailcircuit' },
  { id: 'fallback-4', category: 'Nature', title: 'Sainte-Marie', image: assetPath('img/sainte-marie.jpg'), description: '', meta: 'Explorer', href: '/detailcircuit' },
  { id: 'fallback-5', category: 'Immersion', title: 'Baie de Diego', image: assetPath('img/diego.jpg'), description: '', meta: 'Explorer', href: '/detailcircuit' },
];
const displayCircuits = computed(() => props.circuits.filter((circuit) => ['Publie', 'Actif'].includes(circuit.statut)).map((circuit) => ({ id: circuit.id, category: circuit.type || 'Circuit', title: circuit.nom, image: circuit.image_url || circuit.image || assetPath('img/baobab.jpg'), description: circuit.description || '', meta: circuit.destination || 'Explorer', href: `/circuit/${circuit.slug}`, detailsAvailable: Boolean(circuit.depart && circuit.retour) })));
const sliderCircuits = computed(() => displayCircuits.value.length > 0 ? displayCircuits.value : fallbackCircuits);
const reviews = [
  { title: 'Voyage de rêve', text: "L'organisation était impeccable. Notre guide connaissait chaque plante et chaque animal. Dormir près de l'Allée des Baobabs est un souvenir gravé à jamais.", author: 'Marc-Antoine L.', trip: 'Circuit Grand Sud' },
  { title: 'Authenticité', text: "On sent que c'est un tour opérateur local. Nous avons rencontré des communautés villageoises loin du tourisme de masse. Une expérience humaine incroyable.", author: 'Famille Desvaux', trip: 'Immersion Culturelle' },
  { title: 'Expertise locale', text: "Le trek dans le Makay était intense mais très bien encadré. On se sent en sécurité tout en vivant une vraie aventure sauvage. Bravo.", author: 'Lucie & Ben', trip: 'Expédition Trekking' },
  { title: 'Service au top', text: "Du premier appel à la fin du séjour, tout était parfait. Les conseils personnalisés ont fait toute la différence.", author: 'Sarah J.', trip: 'Détente & Nature' },
];
function toggleMenu() { menuActive.value = !menuActive.value; }
function handleSearch() { isSearching.value = true; window.setTimeout(() => { isSearching.value = false; if (!searchData.value.region && !searchData.value.activity) { window.alert('Veuillez sélectionner au moins une destination ou une activité.'); return; } window.alert(`Félicitations ! Nous préparons vos résultats pour : ${searchData.value.region || "Toute l'île"}`); }, 600); }
function handleScroll() { const isCompact = window.scrollY > 50; headerHeight.value = isCompact ? '65px' : '80px'; headerBackground.value = isCompact ? 'rgba(255, 255, 255, 0.98)' : ''; }
function scrollSlider(id, distance) { document.getElementById(id)?.scrollBy({ left: distance, behavior: 'smooth' }); }
onMounted(() => { window.addEventListener('scroll', handleScroll); handleScroll(); });
onBeforeUnmount(() => { window.removeEventListener('scroll', handleScroll); });
</script>

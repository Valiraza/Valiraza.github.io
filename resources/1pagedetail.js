// Données "Jour par jour"
const itinerary = [
  { day: 1, title: "Vol vers Antananarivo", summary: "Arrivée à Antanarivo, accueil par le guide et transfert à l’hôtel. Dîner libre.", photoUrl: "img/img1.jpg", meta: ["Hébergement : hôtel ***", "Transfert : véhicule ~0h30 / 15 km"] },
  { day: 2, title: "Antananarivo — Ambatolampy — Antsirabe", summary: "Prise en charge des VTT, nuit à Antsirabe.", photoUrl: "img/img2.jpg", meta: ["Hébergement : gîte", "Repas : petit-déjeuner, déjeuner, dîner", "Dénivelé + : 210 m", "Dénivelé − : 585 m", "Distance : 40 km"] },
  { day: 3, title: "Antsirabe — Ambositra — PN Ranomafana", summary: "Piste entre jardins et palmeraies; assistance électrique pour une journée accessible.", photoUrl: "img/bali.jpg", meta: ["Hébergement : gîte", "Repas : petit-déjeuner, déjeuner, dîner", "Dénivelé + : 485 m", "Dénivelé − : 740 m", "Distance : 82 km"] },
  { day: 4, title: "PN Ranomafana — Fianarantsoa — Andringitra", summary: "Visite PN ranomafana; hébergement au bord de la vallée...", photoUrl: "img/img3.jpg", meta: ["Hébergement : auberge", "Repas : petit-déjeuner, déjeuner, dîner", "Dénivelé + : 120 m", "Dénivelé − : 380 m", "Distance : 46 km"] },
  { day: 5, title: "Andringitra — Ranohira", summary: "Villages en terre battue, champs cultivés au cœur des ranohira, déjeuner en route, arrivée à ranohira.", photoUrl: "img/img2.jpg", meta: ["Hébergement : auberge", "Repas : petit-déjeuner, déjeuner, dîner", "Dénivelé + : 190 m", "Dénivelé − : 465 m", "Distance : 66 km"] },
  { day: 6, title: "Ranohira — tulear", summary: "Dernière étape entre champs et espaces sableux; paysages de dunes; déjeuner à l’ombre, puis arrivée à l’hébergement.", photoUrl: "img/bali.jpg", meta: ["Hébergement : auberge", "Repas : petit-déjeuner, déjeuner, dîner", "Dénivelé + : 160 m", "Dénivelé − : 360 m", "Distance : 42 km (VTT)"] },
  { day: 7, title: "Retour à ranohira", summary: "Retour en véhicule (~5 à 5h30) vers ranohira, arrivée l’après-midi. Dîner libre.", photoUrl: "img/img3.jpg", meta: ["Hébergement : hôtel ***", "Repas : petit-déjeuner, déjeuner", "Transfert : minibus ~5–5h30 / 200 km"] },
  { day: 8, title: "Ranohira — Antananarivo", summary: "Temps libre pour achats selon horaires de vol; transfert à l’aéroport et vol retour.", photoUrl: "img/img2.jpg", meta: ["Repas : petit-déjeuner", "Transfert : véhicule ~0h30 / 15 km"] }
];

// Rendu des cartes jour par jour
const daysEl = document.getElementById('days');

function getMetaIcon(metaItem) { 
  if (metaItem.startsWith("Hébergement")) return '<i class="icon fa fa-bed"></i>';  
  if (metaItem.startsWith("Repas")) return '<i class="icon fa fa-utensils"></i>';  
  if (metaItem.startsWith("Distance")) return '<i class="icon fa fa-road"></i>';  
  if (metaItem.startsWith("Transfert")) return '<i class="icon fa fa-bus"></i>';  
  if (metaItem.includes("Dénivelé +")) return '<i class="icon fas fa-arrow-up"></i>';
  if (metaItem.includes("Dénivelé −")) return '<i class="icon fas fa-arrow-down"></i>';
}

function createDayCard({ day, title, summary, meta, photoUrl }) {
  const card = document.createElement('div');
  card.className = 'day-card';

  const header = document.createElement('div');
  header.className = 'day-header';
  header.innerHTML = `
    <div class="badge">Jour ${day}</div>
    <div>
      <div class="title">${title}</div>
      <div class="distance">${meta.find(m => m.startsWith('Distance')) || ''}</div>
    </div>
    <div class="chevron">▼</div>
  `;

  const content = document.createElement('div');
  content.className = 'day-content';
  content.innerHTML = `
    <div class="section">
      <p>${summary}</p>
      ${photoUrl ? `<img src="${photoUrl}" alt="Photo jour ${day}" class="summary-photo">` : ''}
    </div>
    <div class="section">
      <div class="meta">
        ${meta.map(m => `<span class="chip">${getMetaIcon(m)} ${m}</span>`).join('')}
      </div>
    </div>
  `;

  header.addEventListener('click', () => card.classList.toggle('open'));

  card.appendChild(header);
  card.appendChild(content);
  return card;
}

itinerary.forEach(item => daysEl.appendChild(createDayCard(item)));

document.getElementById('openAll').addEventListener('click', () => {
  document.querySelectorAll('.day-card').forEach(c => c.classList.add('open'));
});
document.getElementById('closeAll').addEventListener('click', () => {
  document.querySelectorAll('.day-card').forEach(c => c.classList.remove('open'));
});

// Stepper reservation (FIXED & SAFE)
const form = document.getElementById("reservationForm");
const formsteps = document.querySelectorAll(".form-step");
const nextBtns = document.querySelectorAll(".next-btn");
const prevBtns = document.querySelectorAll(".prev-btn");
let currentStep = 0;

nextBtns.forEach(btn => btn.addEventListener("click", () => {
  if (currentStep < formsteps.length - 1) {
    formsteps[currentStep].classList.remove("active");
      currentStep++;
      formsteps[currentStep].classList.add("active");
  }
}));

prevBtns.forEach(btn => btn.addEventListener("click", () => {
  if (currentStep > 0) {
    formsteps[currentStep].classList.remove("active");
      currentStep--;
      formsteps[currentStep].classList.add("active");
  }
}));

// écoute UNIQUEMENT les boutons du form
form.addEventListener("click", (e) => {
  if (e.target.classList.contains("next-btn")) {
    e.preventDefault();
    if (currentStep < steps.length - 1) {
      currentStep++;
      showStep(currentStep);
    }
  }

  if (e.target.classList.contains("prev-btn")) {
    e.preventDefault();
    if (currentStep > 0) {
      currentStep--;
      showStep(currentStep);
    }
  }
});

// affichage initial garanti
showStep(0);

function fillRecap() {
  document.getElementById("recap-fullname").textContent =
    document.querySelector('input[placeholder="Nom complet"]').value;

  document.getElementById("recap-email").textContent =
    document.querySelector('input[placeholder="Email"]').value;

  document.getElementById("recap-tel").textContent =
    document.querySelector('input[placeholder="Téléphone"]').value;

  document.getElementById("recap-seats").textContent =
    app._data.selectedSeats.join(", ");
}

// Vue pour la sélection des seats
const occupiedSeats = [];
const app = Vue.createApp({
  data(){ return { selectedSeats: [], showBus:false, occupiedSeats:occupiedSeats }; },
  methods:{
    toggleSeat(id){
      if(this.occupiedSeats.includes(id)) return;
      const index = this.selectedSeats.indexOf(id);
      if(index>=0) this.selectedSeats.splice(index,1);
      else this.selectedSeats.push(id);
    },
    seatClass(id){
      if(id===0) return 'chauffeur';
      if(this.occupiedSeats.includes(id)) return 'occupied';
      if(this.selectedSeats.includes(id)) return 'selected';
      return '';
    },
    submitSeats(){ this.showBus = false; }
  }
});
app.mount('#app');

// Reservation form submit avec fetch POST
document.getElementById("reservationForm").addEventListener("submit", async (e) => {
  e.preventDefault();

  const fullname = document.querySelector('input[placeholder="Nom complet"]').value;
  const email = document.querySelector('input[placeholder="Email"]').value;
  const tel = document.querySelector('input[placeholder="Téléphone"]').value;
  const selectedSeats = app._data.selectedSeats;

  if(selectedSeats.length === 0){
    alert("Veuillez sélectionner au moins une place !");
    return;
  }

  const payload = {
    fullname,
    email,
    tel,
    seats: selectedSeats,
    voyage_id: 1 // à remplacer par dynamique si nécessaire
  };

  try {
    const response = await fetch("http://127.0.0.1:8000/reservations/", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    });

    if(!response.ok) throw new Error("Erreur serveur");

    const data = await response.json();
    alert("Réservation réussie ! ID : " + data.id);

    // Reset form & seats
    document.getElementById("reservationForm").reset();
    app._data.selectedSeats = [];
  } catch(err){
    console.error(err);
    alert("Erreur lors de la réservation !");
  }
});

// Dropdown travellers
const travellersField = document.getElementById("travellersField");
const travellersDropdown = document.getElementById("travellersDropdown");
const validateBtn = document.getElementById("validateTravellers");

travellersField.addEventListener("click", () => {
  travellersDropdown.style.display = "block";
});

validateBtn.addEventListener("click", () => {
  const adults = document.getElementById("adultCount").value;
  const teens = document.getElementById("teenCount").value;
  const children = document.getElementById("childCount").value;
  const babies = document.getElementById("babyCount").value;

  travellersField.value = `${adults} Adultes, ${teens} Ados, ${children} Enfants, ${babies} Bébés`;
  travellersDropdown.style.display = "none";
});

document.addEventListener("click", (e) => {
  if(!travellersField.contains(e.target) && !travellersDropdown.contains(e.target)){
    travellersDropdown.style.display = "none";
  }
});

// Form popup (si il y a)
document.getElementById("devisLink")?.addEventListener("click", function(e){
  e.preventDefault();
  const form = document.getElementById("privatisationForm");
  form.style.display = "block";
  form.scrollIntoView({ behavior: "smooth" });
});

document.getElementById("closeForm")?.addEventListener("click", function(){
  document.getElementById("privatisationForm").style.display = "none";
});

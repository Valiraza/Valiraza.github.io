const { createApp } = Vue;

const app = createApp({
  data() {
    return {
      // --- Identification ---
      tourId: null,

      // --- Navigation & UI ---
      currentStep: 0,
      showDropdown: false,
      showBus: false,
      activeDay: null, // Mitahiry ny jour misokatra ao amin'ny itinerary
      isSubmitting: false,

      // --- Data avy amin'ny API ---
      tour: {
        title: '',
        prices_included: [],
        prices_excluded: [],
        duration_days: 0,
        level: '',
        infos: { 
          formalites: [], 
          equipements: [], 
          accommodation_details: '',
          meals_details: '',
          staff_details: '',
          country_info: '',
          climate_info: '',
          culture_info: ''
        }
      },
      itineraries: [],
      priceDetails: [],
      occupiedSeats: {}, // Format: { tourId: [1, 2, 5] }

      // --- Formulaire ---
      formData: {
        fullname: '',
        email: '',
        tel: '',
        travellers: {
          adults: 0,
          teens: 0,
          children: 0,
          babies: 0
        }
      },
      selectedSeats: [],

      // --- Feedback ---
      message: { text: '', type: 'success' }
    };
  },

computed: {
    // --- PRIX ---
    inclusPrices() {
      return this.priceDetails.filter(p => p.type?.toLowerCase().trim() === 'inclus');
    },
    exclusPrices() {
      return this.priceDetails.filter(p => p.type?.toLowerCase().trim() === 'exclus');
    },

    // --- AUTOMATIQUE INFOS PRATIQUES ---
    displayAccommodation() {
      if (this.tour?.infos?.accommodation_details) return this.tour.infos.accommodation_details;
      if (this.itineraries.length > 0) {
        const hotels = this.itineraries.map(i => i.accommodation).filter(h => h && h !== '[]');
        const uniqueHotels = [...new Set(hotels.flat())]; 
        return uniqueHotels.length > 0 ? uniqueHotels.join(', ') : 'À préciser';
      }
      return 'À préciser';
    },

    displayMeals() {
      if (this.tour?.infos?.meals_details) return this.tour.infos.meals_details;
      if (this.itineraries.length > 0) {
        let count = { pdj: 0, dej: 0, din: 0 };
        this.itineraries.forEach(itin => {
          const m = itin.meals;
          if (Array.isArray(m)) {
            if (m.includes('PDJ')) count.pdj++;
            if (m.includes('DEJ')) count.dej++;
            if (m.includes('DIN')) count.din++;
          }
        });
        return count.pdj || count.dej || count.din ? `${count.pdj} P'tit déj, ${count.dej} Déj, ${count.din} Dîners` : 'À préciser';
      }
      return 'À préciser';
    },

    // --- UI SUMMARY ---
    travellersSummary() {
      const t = this.formData.travellers;
      const total = t.adults + t.teens + t.children + t.babies;
      if (total === 0) return "Sélectionnez...";
      return `${t.adults} Adultes, ${t.teens} Ados, ${t.children} Enfants, ${t.babies} Bébés`;
    }
  },
  methods: {
    // --- NAVIGATION STEPS ---
    nextStep() { if (this.currentStep < 2) this.currentStep++; },
    prevStep() { if (this.currentStep > 0) this.currentStep--; },

    // --- LOGIC BUS SEATS ---
    seatClass(n) {
      const isOccupied = this.occupiedSeats[this.tourId]?.includes(n);
      const isSelected = this.selectedSeats.includes(n);
      return {
        'occupied': isOccupied,
        'selected': isSelected,
        'available': !isOccupied && !isSelected
      };
    },
    toggleSeat(n) {
      if (this.occupiedSeats[this.tourId]?.includes(n)) return;
      const index = this.selectedSeats.indexOf(n);
      if (index > -1) this.selectedSeats.splice(index, 1);
      else this.selectedSeats.push(n);
    },

    // --- API FETCHING ---
    async fetchAllData() {
      try {
        // Mandefa ny requêtes rehetra miaraka mba ho haingana
        const [resTour, resItin, resSeats, resPrices] = await Promise.all([
          fetch(`http://127.0.0.1:8000/api/tours/${this.tourId}`),
          fetch(`http://127.0.0.1:8000/api/tours/${this.tourId}/itineraries`),
          fetch(`http://127.0.0.1:8000/api/reservations/occupied/${this.tourId}`),
          fetch(`http://127.0.0.1:8000/api/tours/${this.tourId}/price-details`)
        ]);

        if (resTour.ok) {
            const data = await resTour.json();
            this.tour = data;
            
            console.log("Tour data with infos:", this.tour);
        }
        
        if (resItin.ok) {
          const itinData = await resItin.json();
          this.itineraries = itinData.map(item => ({
            ...item,
            accommodation: this.safeParse(item.accommodation),
            meals: this.safeParse(item.meals),
            transfers: this.safeParse(item.transfers)
          }));
        }

        if (resSeats.ok) {
          this.occupiedSeats[this.tourId] = await resSeats.json();
        }

        if (resPrices.ok) {
          this.priceDetails = await resPrices.json();
        }

      } catch (error) {
        console.error("Erreur globale fetch:", error);
      }
    },

    safeParse(data) {
      if (!data) return [];
      if (typeof data === 'string') {
        try { return JSON.parse(data); } catch (e) { return [data]; }
      }
      return data;
    },

    // --- SUBMISSION ---
    async submitReservation() {
      if (this.selectedSeats.length === 0) {
        this.showMessage("❌ Choisissez au moins une place.", "danger");
        return;
      }

      this.isSubmitting = true;
      const payload = {
        ...this.formData,
        seats: this.selectedSeats.join(','),
        tours_id: this.tourId
      };

      try {
        const res = await fetch("http://127.0.0.1:8000/api/reservations", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload)
        });

        if (!res.ok) throw new Error("Erreur serveur");

        this.showMessage("✅ Réservation envoyée avec succès !", "success");
        this.resetForm();
      } catch (err) {
        this.showMessage("❌ Erreur : " + err.message, "danger");
      } finally {
        this.isSubmitting = false;
      }
    },

    showMessage(text, type) {
      this.message = { text, type };
      setTimeout(() => this.message.text = '', 5000);
    },

    resetForm() {
      this.formData = { 
        fullname: '', email: '', tel: '', 
        travellers: { adults: 0, teens: 0, children: 0, babies: 0 } 
      };
      this.selectedSeats = [];
      this.currentStep = 0;
    }
  },

  async mounted() {
    const params = new URLSearchParams(window.location.search);
    this.tourId = parseInt(params.get("id"), 10);

    if (this.tourId) {
      await this.fetchAllData();
    } else {
      console.error("ID du tour tsy hita");
    }
  }
});

const vueInstance = app.mount('#app');
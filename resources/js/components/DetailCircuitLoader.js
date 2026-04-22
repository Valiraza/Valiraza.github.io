/**
 * DetailCircuitLoader - Charge et affiche dynamiquement les données du circuit
 * basé sur le slug dans l'URL
 */

export default {
  data() {
    return {
      circuit: null,
      loading: true,
      error: null,
    };
  },
  computed: {
    slug() {
      // Extraire le slug de l'URL (dernière partie du path)
      const pathParts = window.location.pathname.split('/').filter(Boolean);
      return pathParts[pathParts.length - 1];
    },
    programme() {
      return this.circuit?.programme || [];
    },
    detail() {
      return this.circuit?.detail || {};
    },
  },
  methods: {
    async loadCircuit() {
      try {
        this.loading = true;
        this.error = null;

        const response = await fetch(`/api/circuits/by-slug/${this.slug}`);
        
        if (!response.ok) {
          throw new Error('Circuit non trouvé');
        }

        this.circuit = await response.json();
      } catch (err) {
        this.error = err.message;
        console.error('Erreur lors du chargement du circuit:', err);
      } finally {
        this.loading = false;
      }
    },
    resolveImagePath(path) {
      if (!path) return '';
      if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/')) {
        return path;
      }
      return `/storage/${path.replace(/^\/+/, '')}`;
    },
    formatDate(dateString) {
      if (!dateString) return '';
      return new Date(dateString).toLocaleDateString('fr-FR', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      });
    },
  },
  mounted() {
    this.loadCircuit();
  },
};

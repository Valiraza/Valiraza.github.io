<template>
  <div class="login-page">
    <div class="card shadow-sm">
      <h4 class="mb-3 text-center">Connexion administrateur</h4>
      <div v-if="errorMessage" id="errorMsg" class="text-danger mb-3">{{ errorMessage }}</div>

      <form @submit.prevent="handleLogin">
        <div class="mb-3">
          <label class="form-label">Nom d'utilisateur ou email</label>
          <input
            type="text"
            class="form-control"
            v-model.trim="username"
            placeholder="Entrer votre identifiant"
            autocomplete="username"
          />
        </div>

        <div class="mb-3">
          <label class="form-label">Mot de passe</label>
          <input
            type="password"
            class="form-control"
            v-model.trim="password"
            placeholder="Entrer votre mot de passe"
            autocomplete="current-password"
          />
        </div>

        <button class="btn btn-primary w-100" type="submit" :disabled="loading">
          <span v-if="loading">Connexion...</span>
          <span v-else>Se connecter</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LoginAdmin',
  data() {
    return {
      username: '',
      password: '',
      errorMessage: '',
      loading: false,
    };
  },
  methods: {
    async handleLogin() {
      this.errorMessage = '';

      if (!this.username || !this.password) {
        this.errorMessage = 'Veuillez remplir tous les champs.';
        return;
      }

      this.loading = true;

      try {
        const res = await fetch('/api/admin/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: JSON.stringify({ username: this.username, password: this.password }),
        });

        const data = await res.json();

        if (!res.ok) {
          this.errorMessage = data.message || 'Erreur de connexion';
          return;
        }

        localStorage.setItem('access_token', data.access_token);
        window.location.href = '/compteadmin';
      } catch (error) {
        this.errorMessage = error?.message || 'Erreur de connexion';
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: #f5f6fa;
}

.card {
  width: 100%;
  max-width: 380px;
  padding: 24px;
  border-radius: 18px;
  box-shadow: 0 20px 60px rgba(15, 23, 42, 0.12);
}

#admin-login-app .form-label {
  font-weight: 500;
}
</style>

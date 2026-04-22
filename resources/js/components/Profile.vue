<template>
  <div class="profile-section">
    <div class="card">
      <div class="card-header">
        <h3><i class="fas fa-user"></i> Mon Profil Administrateur</h3>
        <p>Gerer vos informations personnelles, votre photo et votre securite</p>
      </div>

      <div class="card-body">
        <div class="profile-hero">
          <div class="profile-photo-wrap">
            <button
              type="button"
              class="profile-photo-trigger"
              aria-label="Voir ou modifier la photo"
              @click="openPhotoModal"
            >
              <img :src="profilePhotoUrl" alt="Photo de profil administrateur" class="profile-photo">
              <span class="profile-photo-overlay">
                <i class="fas fa-camera"></i>
                <span>Voir et modifier</span>
              </span>
            </button>

            <button
              type="button"
              class="profile-photo-badge"
              aria-label="Changer la photo"
              @click="triggerPhotoPicker"
            >
              <i class="fas fa-camera"></i>
            </button>
          </div>

          <div class="profile-hero-text">
            <strong>{{ formData.name || 'Administrateur principal' }}</strong>
            <span>Photo de profil administrateur</span>
            <button type="button" class="profile-link-btn" @click="openPhotoModal">
              Afficher la photo
            </button>
          </div>
        </div>

        <form @submit.prevent="updateProfile" class="profile-form">
          <div class="form-group form-group-hidden">
            <label for="profile_photo">Photo de profil</label>
            <input
              id="profile_photo"
              ref="profilePhotoInput"
              type="file"
              class="form-control"
              accept=".jpg,.jpeg,.png,.webp"
              @change="handlePhotoChange"
            >
          </div>

          <div class="photo-shortcuts">
            <span class="field-help">Formats acceptes : JPG, PNG, WEBP. Taille maximale : 2 Mo.</span>
            <button type="button" class="btn btn-secondary btn-inline" @click="triggerPhotoPicker">
              Changer la photo
            </button>
            <button
              v-if="hasCustomPhoto || selectedPhoto"
              type="button"
              class="btn btn-secondary btn-inline"
              @click="removeCurrentPhoto"
            >
              Retirer la photo
            </button>
            <button
              v-if="selectedPhoto"
              type="button"
              class="btn btn-secondary btn-inline"
              @click="clearSelectedPhoto"
            >
              Annuler la nouvelle photo
            </button>
          </div>

          <div class="form-group">
            <label for="name">Nom complet</label>
            <input
              id="name"
              v-model="formData.name"
              type="text"
              class="form-control"
              required
            >
          </div>

          <div class="form-group">
            <label for="email">Adresse e-mail</label>
            <input
              id="email"
              v-model="formData.email"
              type="email"
              class="form-control"
              required
            >
          </div>

          <div class="password-section">
            <h4><i class="fas fa-lock"></i> Changer le mot de passe</h4>
            <p class="text-muted">Laissez vide si vous ne souhaitez pas changer le mot de passe.</p>

            <div class="form-group">
              <label for="current_password">Mot de passe actuel</label>
              <input
                id="current_password"
                v-model="formData.current_password"
                type="password"
                class="form-control"
                :required="formData.password.length > 0"
              >
            </div>

            <div class="form-group">
              <label for="password">Nouveau mot de passe</label>
              <input
                id="password"
                v-model="formData.password"
                type="password"
                class="form-control"
                minlength="8"
              >
            </div>

            <div class="form-group">
              <label for="password_confirmation">Confirmer le nouveau mot de passe</label>
              <input
                id="password_confirmation"
                v-model="formData.password_confirmation"
                type="password"
                class="form-control"
                :required="formData.password.length > 0"
              >
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn-primary" :disabled="loading">
              <i class="fas fa-save"></i>
              {{ loading ? 'Mise a jour...' : 'Mettre a jour le profil' }}
            </button>
          </div>
        </form>

        <div v-if="message.text" :class="['alert', message.type === 'success' ? 'alert-success' : 'alert-danger']">
          <i :class="message.type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle'"></i>
          {{ message.text }}
        </div>
      </div>
    </div>

    <div v-if="isPhotoModalOpen" class="photo-modal-backdrop" @click.self="closePhotoModal">
      <div class="photo-modal">
        <button type="button" class="photo-modal-close" aria-label="Fermer" @click="closePhotoModal">
          <i class="fas fa-times"></i>
        </button>

        <div class="photo-modal-header">
          <strong>{{ formData.name || 'Administrateur principal' }}</strong>
          <span>Photo de profil</span>
        </div>

        <div class="photo-modal-body">
          <img :src="profilePhotoUrl" alt="Apercu de la photo de profil" class="photo-modal-image">
        </div>

        <div class="photo-modal-actions">
          <button type="button" class="btn btn-secondary" @click="triggerPhotoPicker">
            <i class="fas fa-camera"></i>
            Modifier la photo
          </button>
          <button
            v-if="hasCustomPhoto || selectedPhoto"
            type="button"
            class="btn btn-secondary"
            @click="removeCurrentPhoto"
          >
            <i class="fas fa-trash-alt"></i>
            Supprimer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Profile',
  data() {
    return {
      profilePhoto: '',
      selectedPhoto: null,
      removePhoto: false,
      isPhotoModalOpen: false,
      formData: {
        name: '',
        email: '',
        current_password: '',
        password: '',
        password_confirmation: '',
      },
      loading: false,
      message: {
        text: '',
        type: '',
      },
    };
  },
  computed: {
    profilePhotoUrl() {
      return this.profilePhoto || '/img/Andranotora.jpg';
    },
    hasCustomPhoto() {
      return Boolean(this.profilePhoto) && this.profilePhoto !== '/img/Andranotora.jpg';
    },
  },
  mounted() {
    this.loadProfile();
  },
  methods: {
    openPhotoModal() {
      this.isPhotoModalOpen = true;
    },
    closePhotoModal() {
      this.isPhotoModalOpen = false;
    },
    triggerPhotoPicker() {
      if (this.$refs.profilePhotoInput) {
        this.$refs.profilePhotoInput.click();
      }
    },
    handlePhotoChange(event) {
      const [file] = event.target.files || [];

      if (!file) {
        this.selectedPhoto = null;
        return;
      }

      this.selectedPhoto = file;
      this.removePhoto = false;
      this.profilePhoto = URL.createObjectURL(file);
      this.isPhotoModalOpen = true;
    },
    clearSelectedPhoto() {
      this.selectedPhoto = null;
      this.removePhoto = false;
      if (this.$refs.profilePhotoInput) {
        this.$refs.profilePhotoInput.value = '';
      }
      this.loadProfile();
    },
    removeCurrentPhoto() {
      this.selectedPhoto = null;
      this.removePhoto = true;
      this.profilePhoto = '';
      if (this.$refs.profilePhotoInput) {
        this.$refs.profilePhotoInput.value = '';
      }
    },
    async loadProfile() {
      try {
        const token = localStorage.getItem('access_token');
        if (!token) {
          this.message = { text: 'Token manquant. Veuillez vous reconnecter.', type: 'error' };
          return;
        }

        const response = await fetch('/api/admin/check', {
          headers: {
            Authorization: `Bearer ${token}`,
            Accept: 'application/json',
            'Content-Type': 'application/json',
          },
        });

        const data = await response.json();
        if (!response.ok || !data.authenticated) {
          localStorage.removeItem('access_token');
          window.location.href = '/admin/login';
          return;
        }

        this.formData.name = data.user.name;
        this.formData.email = data.user.email;
        this.profilePhoto = data.user.photo_url || '';
        this.selectedPhoto = null;
        this.removePhoto = false;

        if (this.$refs.profilePhotoInput) {
          this.$refs.profilePhotoInput.value = '';
        }
      } catch (error) {
        console.error('Erreur:', error);
        this.message = { text: 'Erreur reseau.', type: 'error' };
      }
    },
    async updateProfile() {
      this.loading = true;
      this.message = { text: '', type: '' };

      try {
        const token = localStorage.getItem('access_token');
        if (!token) {
          this.message = { text: 'Token manquant. Veuillez vous reconnecter.', type: 'error' };
          return;
        }

        const payload = new FormData();
        payload.append('_method', 'PUT');
        payload.append('name', this.formData.name);
        payload.append('email', this.formData.email);
        payload.append('remove_profile_photo', this.removePhoto ? '1' : '0');

        if (this.formData.password) {
          payload.append('current_password', this.formData.current_password);
          payload.append('password', this.formData.password);
          payload.append('password_confirmation', this.formData.password_confirmation);
        }

        if (this.selectedPhoto) {
          payload.append('profile_photo', this.selectedPhoto);
        }

        const response = await fetch('/api/admin/profile', {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${token}`,
            Accept: 'application/json',
          },
          body: payload,
        });

        const contentType = response.headers.get('content-type') || '';
        const data = contentType.includes('application/json')
          ? await response.json()
          : { message: 'Le serveur a retourne une reponse inattendue.' };

        if (response.ok) {
          this.message = { text: data.message, type: 'success' };
          await this.loadProfile();
          this.formData.current_password = '';
          this.formData.password = '';
          this.formData.password_confirmation = '';
          this.selectedPhoto = null;
          this.removePhoto = false;
          this.isPhotoModalOpen = false;
        } else {
          const validationMessage = data.errors
            ? Object.values(data.errors).flat().join(' ')
            : null;

          this.message = {
            text: validationMessage || data.message || 'Erreur lors de la mise a jour.',
            type: 'error',
          };
        }
      } catch (error) {
        console.error('Erreur:', error);
        this.message = { text: 'Erreur reseau.', type: 'error' };
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
.profile-section {
  max-width: 600px;
  margin: 0 auto;
}

.card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.card-header {
  background: #f8f9fa;
  padding: 20px;
  border-bottom: 1px solid #e9ecef;
}

.card-header h3 {
  margin: 0 0 5px 0;
  color: #2d1a10;
  font-size: 1.5rem;
}

.card-header p {
  margin: 0;
  color: #6c757d;
}

.card-body {
  padding: 20px;
}

.profile-form {
  margin-bottom: 20px;
}

.profile-hero {
  display: flex;
  align-items: center;
  gap: 18px;
  margin-bottom: 24px;
  padding: 16px 18px;
  border-radius: 14px;
  background: #f8f9fa;
  border: 1px solid #e9ecef;
}

.profile-photo-wrap {
  position: relative;
  flex-shrink: 0;
}

.profile-photo-trigger {
  position: relative;
  display: block;
  width: 96px;
  height: 96px;
  padding: 0;
  border: none;
  border-radius: 28px;
  overflow: hidden;
  background: transparent;
  cursor: pointer;
}

.profile-photo {
  width: 100%;
  height: 100%;
  border-radius: 28px;
  object-fit: cover;
  display: block;
}

.profile-photo-overlay {
  position: absolute;
  inset: 0;
  background: rgba(18, 23, 28, 0.48);
  color: white;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 6px;
  font-size: 0.72rem;
  font-weight: 600;
  opacity: 0;
  transition: opacity 0.2s ease;
}

.profile-photo-trigger:hover .profile-photo-overlay {
  opacity: 1;
}

.profile-photo-badge {
  position: absolute;
  right: -6px;
  bottom: -6px;
  width: 34px;
  height: 34px;
  border: 3px solid white;
  border-radius: 50%;
  background: #4a7c2c;
  color: white;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 8px 18px rgba(74, 124, 44, 0.22);
}

.profile-hero-text {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.profile-hero-text strong {
  color: #2d1a10;
  font-size: 1rem;
}

.profile-hero-text span {
  color: #6c757d;
  font-size: 0.9rem;
}

.profile-link-btn {
  border: none;
  background: transparent;
  color: #4a7c2c;
  padding: 0;
  margin-top: 6px;
  text-align: left;
  font-weight: 700;
  cursor: pointer;
}

.form-group {
  margin-bottom: 20px;
}

.form-group-hidden {
  display: none;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: 600;
  color: #2d1a10;
}

.photo-shortcuts {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 10px;
  margin-bottom: 24px;
}

.field-help {
  display: block;
  color: #6c757d;
  font-size: 0.85rem;
  margin-right: auto;
}

.form-control {
  width: 100%;
  padding: 10px;
  border: 1px solid #ced4da;
  border-radius: 4px;
  font-size: 14px;
}

.form-control:focus {
  outline: none;
  border-color: #4a7c2c;
  box-shadow: 0 0 0 2px rgba(74, 124, 44, 0.25);
}

.password-section {
  background: #f8f9fa;
  padding: 20px;
  border-radius: 6px;
  margin-top: 30px;
  border: 1px solid #e9ecef;
}

.password-section h4 {
  margin: 0 0 10px 0;
  color: #2d1a10;
  font-size: 1.2rem;
}

.text-muted {
  color: #6c757d;
  font-size: 0.9rem;
  margin-bottom: 15px;
}

.form-actions {
  margin-top: 30px;
  text-align: center;
}

.btn {
  padding: 12px 24px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-primary {
  background: #4a7c2c;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #385d21;
  transform: translateY(-1px);
}

.btn-secondary {
  background: #e9ecef;
  color: #2d1a10;
}

.btn-inline {
  padding: 10px 18px;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.alert {
  padding: 15px;
  border-radius: 6px;
  margin-top: 20px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.alert-success {
  background: #d4edda;
  border: 1px solid #c3e6cb;
  color: #155724;
}

.alert-danger {
  background: #f8d7da;
  border: 1px solid #f5c6cb;
  color: #721c24;
}

.photo-modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 18, 24, 0.68);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  z-index: 2000;
}

.photo-modal {
  position: relative;
  width: min(92vw, 560px);
  background: white;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 30px 80px rgba(0, 0, 0, 0.28);
}

.photo-modal-close {
  position: absolute;
  top: 14px;
  right: 14px;
  width: 38px;
  height: 38px;
  border: none;
  border-radius: 50%;
  background: rgba(45, 26, 16, 0.08);
  color: #2d1a10;
  cursor: pointer;
  z-index: 2;
}

.photo-modal-header {
  padding: 22px 26px 10px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.photo-modal-header strong {
  font-size: 1.1rem;
  color: #2d1a10;
}

.photo-modal-header span {
  color: #6c757d;
  font-size: 0.92rem;
}

.photo-modal-body {
  padding: 12px 26px 18px;
}

.photo-modal-image {
  width: 100%;
  aspect-ratio: 1 / 1;
  object-fit: cover;
  border-radius: 22px;
  background: #f2f4f7;
}

.photo-modal-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  padding: 0 26px 26px;
}

@media (max-width: 640px) {
  .profile-hero {
    flex-direction: column;
    align-items: flex-start;
  }

  .photo-shortcuts {
    flex-direction: column;
    align-items: flex-start;
  }

  .field-help {
    margin-right: 0;
  }

  .photo-modal-actions {
    flex-direction: column;
  }
}
</style>

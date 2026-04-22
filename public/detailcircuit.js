// ============================================
// Slider Management
// ============================================
let currentSlide = 0;
let slides = [];
let counterSpan = null;

function initSlider() {
    slides = document.querySelectorAll('.slide');
    counterSpan = document.getElementById('current-index');
}

function showSlide(index) {
    if (slides.length === 0) return;
    
    slides.forEach(slide => slide.classList.remove('active'));
    currentSlide = (index + slides.length) % slides.length;
    slides[currentSlide].classList.add('active');
    
    if (counterSpan) {
        counterSpan.innerText = currentSlide + 1;
    }
}

function moveSlide(step) {
    showSlide(currentSlide + step);
}

// ============================================
// Modal Management
// ============================================
function openTerdavModal(slug) {
    const tdModal = document.getElementById('tdModal');
    if (!tdModal) {
        console.error('Modal element "tdModal" not found');
        return;
    }
    tdModal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    switchTerdavTab(slug);
}

function closeTerdavModal() {
    const tdModal = document.getElementById('tdModal');
    if (!tdModal) {
        console.error('Modal element "tdModal" not found');
        return;
    }
    tdModal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

function switchTerdavTab(slug) {
    // Désactiver tous les onglets
    document.querySelectorAll('.td-tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.td-pane').forEach(pane => pane.classList.remove('active'));

    // Activer l'onglet cliqué
    const activeBtn = document.getElementById('btn-' + slug);
    const activePane = document.getElementById('pane-' + slug);
    
    if (activeBtn) activeBtn.classList.add('active');
    if (activePane) activePane.classList.add('active');
}

// ============================================
// DOM Initialization
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    // Initialize slider
    initSlider();

    // -------- Formulaire de Réservation --------
    const reservationForm = document.getElementById('reservation-form');
    if (reservationForm) {
        reservationForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const nomInput = document.getElementById('nom');
            const emailInput = document.getElementById('email');
            const telInput = document.getElementById('telephone');

            if (!nomInput || !emailInput || !telInput) {
                console.error('Reservation form inputs not found');
                return;
            }

            const reservationData = {
                nom: nomInput.value,
                email: emailInput.value,
                telephone: telInput.value
            };

            console.log('Réservation reçue :', reservationData);

            const submitBtn = reservationForm.querySelector('button[type="submit"]');
            if (!submitBtn) return;

            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Envoi...';
            submitBtn.disabled = true;

            setTimeout(() => {
                alert(`Misaotra ${reservationData.nom}! Voaray ny famandrihana nataonao.`);
                
                const modalElement = document.getElementById('reservationFormModal');
                if (modalElement) {
                    try {
                        const modalInstance = bootstrap.Modal.getInstance(modalElement);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                    } catch (err) {
                        console.error('Error closing modal:', err);
                    }
                }

                reservationForm.reset();
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 1500);
        });
    }

    // -------- Formulaire de Recherche --------
    const searchForm = document.getElementById('search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const regionInput = document.getElementById('region');
            const activityInput = document.getElementById('activity');
            const dateInput = document.getElementById('date-departure');

            if (!regionInput || !activityInput || !dateInput) {
                console.warn('Search form inputs not found');
                return;
            }

            const data = {
                region: regionInput.value,
                activity: activityInput.value,
                date: dateInput.value
            };

            const btn = searchForm.querySelector('.btn-search');
            if (!btn) return;

            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> RECHERCHE...';

            console.log('Recherche en cours pour Madagascar :', data);

            setTimeout(() => {
                btn.innerHTML = originalText;
                if (!data.region && !data.activity) {
                    alert('Veuillez sélectionner au moins une destination ou une activité.');
                } else {
                    alert(`Félicitations ! Nous préparons vos résultats pour : ${data.region || 'Toute l\'île'}`);
                }
            }, 600);
        });
    }

    // -------- Menu Mobile --------
    const mobileToggle = document.querySelector('.mobile-toggle');
    const nav = document.querySelector('.main-nav');

    if (mobileToggle && nav) {
        mobileToggle.addEventListener('click', () => {
            nav.classList.toggle('active');
            const icon = mobileToggle.querySelector('i');
            
            if (icon) {
                if (nav.classList.contains('active')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-xmark');
                } else {
                    icon.classList.remove('fa-xmark');
                    icon.classList.add('fa-bars');
                }
            }
        });
    }

    // -------- Effet Header au scroll --------
    let scrollTimeout;
    window.addEventListener('scroll', () => {
        clearTimeout(scrollTimeout);
        const header = document.querySelector('.main-header');
        if (!header) return;

        if (window.scrollY > 50) {
            header.style.height = '65px';
            header.style.background = 'rgba(255, 255, 255, 0.98)';
        } else {
            header.style.height = '80px';
            header.style.background = '';
        }
    });
});




document.addEventListener('DOMContentLoaded', () => {
    const searchForm = document.getElementById('search-form');
    const mobileToggle = document.querySelector('.mobile-toggle');
    const nav = document.querySelector('.main-nav');

    // 1. Gestion de la recherche (Flexible et Rapide)
    searchForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        // Récupération instantanée des valeurs
        const data = {
            region: document.getElementById('region').value,
            activity: document.getElementById('activity').value,
            date: document.getElementById('date-departure').value
        };

        // Animation de chargement sur le bouton
        const btn = searchForm.querySelector('.btn-search');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> RECHERCHE...';

        console.log("Recherche en cours pour Madagascar :", data);

        // Simulation de rapidité (Redirection ou filtrage AJAX ici)
        setTimeout(() => {
            btn.innerHTML = originalText;
            if(!data.region && !data.activity) {
                alert("Veuillez sélectionner au moins une destination ou une activité.");
            } else {
                alert(`Félicitations ! Nous préparons vos résultats pour : ${data.region || 'Toute l\'île'}`);
            }
        }, 600);
    });

   // 2. Menu Mobile Simple
mobileToggle.addEventListener('click', () => {
    // On bascule la classe 'active' sur le menu
    nav.classList.toggle('active');

    // On récupère l'icône à l'intérieur du bouton
    const icon = mobileToggle.querySelector('i');

    // Si le menu est actif, on met la croix, sinon on remet les barres
    if (nav.classList.contains('active')) {
        icon.classList.remove('fa-bars');
        icon.classList.add('fa-xmark');
    } else {
        icon.classList.remove('fa-xmark');
        icon.classList.add('fa-bars');
    }
});

    // 3. Effet Header au scroll
    window.addEventListener('scroll', () => {
        const header = document.querySelector('.main-header');
        if (window.scrollY > 50) {
            header.style.height = '65px';
            header.style.background = 'rgba(255, 255, 255, 0.98)';
        } else {
            header.style.height = '80px';
        }
    });
});


/**
 * Gestion du Menu Mobile - Autochtone Tour Madagascar
 */
document.addEventListener('DOMContentLoaded', () => {
    const mobileToggle = document.getElementById('mobile-toggle');
    const mainNav = document.getElementById('main-nav');

    if (mobileToggle && mainNav) {
        mobileToggle.addEventListener('click', () => {
            // Basculer l'affichage du menu
            mainNav.classList.toggle('active');
            
            // Animation de l'icône (Bars <-> Xmark)
            const icon = mobileToggle.querySelector('i');
            if (mainNav.classList.contains('active')) {
                icon.classList.replace('fa-bars', 'fa-xmark');
            } else {
                icon.classList.replace('fa-xmark', 'fa-bars');
            }
        });
    }
});





// Script pour changer le texte du bouton
const toggleBtn = document.getElementById('toggleBtn'); 
const contenuPlus = document.getElementById('contenuPlus'); 
contenuPlus.addEventListener('shown.bs.collapse', () => { 
  toggleBtn.textContent = 'Voir moins'; });
   contenuPlus.addEventListener('hidden.bs.collapse', () => { 
    toggleBtn.textContent = 'Voir plus'; });



// Observer pour détecter quand la section est visible
const observer = new IntersectionObserver((entries, observer) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      const sectionCounters = entry.target.querySelectorAll('.nombre');
      sectionCounters.forEach(counter => startCounter(counter));
      observer.unobserve(entry.target); // évite de relancer plusieurs fois
    }
  });
}, { threshold: 0.5 }); // déclenche quand 50% de la section est visible

// Applique l’observer sur la section .timer
const timerSection = document.querySelector('.timer');
observer.observe(timerSection);


    //galery
const galleryGrid = document.querySelector('.gallery-grid');
const items = document.querySelectorAll('.gallery-item');
let index = 0;

function showSlide(i) {
  galleryGrid.style.transform = `translateX(-${i * 100}%)`;
}

document.querySelector('.gallery-btn.left').addEventListener('click', () => {
  index = (index > 0) ? index - 1 : items.length - 1;
  showSlide(index);
});

document.querySelector('.gallery-btn.right').addEventListener('click', () => {
  index = (index < items.length - 1) ? index + 1 : 0;
  showSlide(index);
});




    

    
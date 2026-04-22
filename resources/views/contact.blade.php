<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>contact-autochtone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('contact.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Playwrite+CZ:wght@100..400&family=Playwrite+NO:wght@100..400&display=swap" rel="stylesheet">

</head>
<body>
    <header class="site-header">
  <nav class="navbar navbar-expand-lg fixed-top">
  <div class="container">
    <a class="navbar-brand me-auto" href="#"><img src="{{ asset('img/logoauto.jpg') }}" width="100" height="60" alt="Village malgache" class="about-image"></a>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasNavbarLabel"><a class="navbar-brand me-auto" href="#"><img src="{{ asset('img/logoauto.jpg') }}" width="80" height="60" alt="Village malgache" class="about-image"></a></h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <ul class="navbar-nav justify-content-center flex-grow-1 pe-3">
          <li class="nav-item">
            <a class="nav-link mx-lg-2" href="accueil.html">Accueil</a>
          </li>
          <li class="nav-item">
            <a class="nav-link mx-lg-2" href="about.html">A propos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link mx-lg-2 active"  aria-current="page" href="#contact" >Contact</a>
          </li>
          <li class="nav-item">
            <a class="nav-link mx-lg-2" href="service.html" >Service</a>
          </li>
          <li class="nav-item">
            <a class="nav-link mx-lg-2" href="destination.html" >Circuits entreprise et corporate</a>
          </li>
          <li class="nav-item">
            <a class="nav-link mx-lg-2" href="prof.html" >Circuits professionnels</a>
          </li>
        </ul>
      </div>
    </div>
       <div class="social-icons">
        <a href="#"><i class="fab fa-facebook"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-linkedin"></i></a>
      </div>
      <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
  </div>
</nav>
    </header>
    
<section id="accueil" class="hero-section">
  <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="2000">
    <div class="carousel-inner">
      <!-- Slide 1 -->
      <div class="carousel-item active">
        <div class="hero-slide" style="background-image: url('{{ asset('img/road.jpg') }}');">
          <div class="hero-content">
            <h1>Contact</h1>
            <hr>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>



 <section class="contact-section">
  <div class="contact-container">
    
    <!-- Carte / Photo -->
    <div class="contact-map">
      <!-- Exemple avec Google Maps -->
      <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3774.337691668385!2d47.535064874486!3d-18.916441207547443!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x21f07d8a6f036021%3A0x4b08b7b5a8a9306e!2sAutochtone%20Tour%20Madagascar!5e0!3m2!1sfr!2smg!4v1769425837544!5m2!1sfr!2smg" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">

      </iframe>
    </div>

    <!-- Formulaire -->
    <div class="contact-form">
    <h2>Contactez-nous</h2>
    <div id="alertPlaceholder"></div>
    <form id="contactForm">
      @csrf
      <div class="form-group">
        <input type="text" name="name" id="name" required>
        <label>Votre nom</label>
      </div>
      <div class="form-group">
        <input type="email" name="email" id="email" required>
        <label>Votre email</label>
      </div>
      <div class="form-group">
        <textarea name="message" id="message" rows="5" required></textarea>
        <label>Votre message</label>
      </div>
      <button class="btn btn-primary btn-lg" type="submit">Envoyer</button>
    </form>
    </div>

  </div>
</section>

<section id="services" class="services-section">
    <div class="container">
        <div class="services-grid">
            <div class="service-item">
                <div class="service-icon-wrapper">
                    <i class="service-icon fas fa-phone"></i>
                </div>
                <h3 class="service-title">+261 32 60 773 53</h3>
            </div>

            <div class="service-item">
                <div class="service-icon-wrapper">
                    <i class="service-icon fas fa-house"></i>
                </div>
                <h3 class="service-title">Lot IIW 27E Ankorahotra</h3>
            </div>

            <div class="service-item">
                <div class="service-icon-wrapper">
                    <i class="service-icon fas fa-envelope"></i>
                </div>
                <h3 class="service-title">autochtonetourmada@gmail.com</h3>
            </div>
        </div>
    </div>
</section>



 
<footer class="footer">
  <div class="footer-container">
    <div class="footer-col brand-col">
      <h2 class="footer-logo">Autochtone <span>Tour</span></h2>
      <p class="brand-description">Explorez l'authenticité de Madagascar avec des experts passionnés. Des circuits sur mesure pour des souvenirs éternels.</p>
      <div class="social-icons">
        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
      </div>
    </div>

    <div class="footer-col">
      <h4>Navigation</h4>
      <ul>
        <li><a href="#accueil">Accueil</a></li>
        <li><a href="#destinations">Nos circuits</a></li>
        <li><a href="#about">À propos</a></li>
        <li><a href="#services">Nos services</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h4>Contactez-nous</h4>
      <ul class="contact-list">
        <li><i class="fas fa-envelope"></i> <a href="mailto:autochtonetourmada@gmail.com">autochtonetourmada@gmail.com</a></li>
        <li><i class="fas fa-phone"></i> +261 32 60 773 53</li>
        <li><i class="fas fa-map-marker-alt"></i> Lot IIW 27E Ankorahotra</li>
      </ul>
    </div>
  </div>

  <div class="footer-bottom">
    <div class="bottom-container">
      <p>&copy; 2026 Autochtone Tour Madagascar. Tous droits réservés.</p>
      <p class="credits">Conçu avec passion par <a href="mailto:abrahamrandrianarivo@gmail.com">Abraham</a></p>
    </div>
  </div>
</footer>
 <script src="{{ asset('contact.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script>
      const form = document.getElementById('contactForm');
      const alertPlaceholder = document.getElementById('alertPlaceholder');

      const showAlert = (message, type = 'success') => {
        alertPlaceholder.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">${message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`;
      };

      form.addEventListener('submit', async (evt) => {
        evt.preventDefault();
        const data = {
          name: form.name.value.trim(),
          email: form.email.value.trim(),
          message: form.message.value.trim(),
        };

        try {
          const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || document.querySelector('input[name="_token"]')?.value;
          const response = await fetch('/api/contacts', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-Token': csrfToken,
              'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(data),
          });

          const contentType = response.headers.get('content-type') || '';
          const json = contentType.includes('application/json') ? await response.json() : null;
          const text = !json ? await response.text() : null;

          if (!response.ok) {
            throw new Error(
              (json && (json.message || json.error)) ||
              (text && text.replace(/<[^>]+>/g, '').trim()) ||
              'Erreur lors de l\'envoi'
            );
          }

          showAlert('Merci ! Votre message a bien été envoyé. Nous vous recontacterons rapidement.', 'success');
          form.reset();
        } catch (err) {
          showAlert(err.message, 'danger');
          console.error(err);
        }
      });
    </script>
</body>
</html>

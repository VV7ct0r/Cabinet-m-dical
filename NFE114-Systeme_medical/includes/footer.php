    </div> <!-- Fin du container principal -->



    <!-- Footer amélioré -->
    <footer class="footer-enhanced mt-auto">
        <!-- Section principale du footer -->
        <div class="footer-main">
            <div class="container">
                <div class="row">
                    <!-- Informations du cabinet -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="footer-section">
                            <h5 class="footer-title">
                                <i class="bi bi-hospital"></i> Cabinet Médical
                            </h5>
                            <p class="footer-description">
                                Votre santé est notre priorité. Nous offrons des soins de qualité
                                avec une équipe de professionnels dévoués à Vierzon.
                            </p>
                        </div>
                    </div>

                    <!-- Liens rapides -->
                    <div class="col-lg-2 col-md-6 mb-4">
                        <div class="footer-section">
                            <h6 class="footer-subtitle">Liens rapides</h6>
                            <ul class="footer-links">
                                <li><a href="index.php"><i class="bi bi-house"></i> Accueil</a></li>
                                <?php if (isLoggedIn() && isPatient()): ?>
                                    <li><a href="rdv.php"><i class="bi bi-calendar-plus"></i> Prendre RDV</a></li>
                                    <li><a href="mes_rdv.php"><i class="bi bi-calendar-check"></i> Mes RDV</a></li>
                                    <li><a href="dashboard.php"><i class="bi bi-speedometer2"></i> Tableau de bord</a></li>
                                <?php elseif (isLoggedIn() && isAdmin()): ?>
                                    <li><a href="admin.php"><i class="bi bi-gear"></i> Administration</a></li>
                                <?php else: ?>
                                    <li><a href="login.php"><i class="bi bi-box-arrow-in-right"></i> Connexion</a></li>
                                    <li><a href="register.php"><i class="bi bi-person-plus"></i> Inscription</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>

                    <!-- Horaires -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="footer-section">
                            <h6 class="footer-subtitle">
                                <i class="bi bi-clock"></i> Horaires d'ouverture
                            </h6>
                            <div class="footer-hours">
                                <div class="hour-item">
                                    <span class="day">Lundi - Vendredi</span>
                                    <span class="time">8h00 - 18h00</span>
                                </div>
                                <div class="hour-item">
                                    <span class="day">Samedi</span>
                                    <span class="time">9h00 - 12h00</span>
                                </div>
                                <div class="hour-item">
                                    <span class="day">Dimanche</span>
                                    <span class="time closed">Fermé</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="footer-section">
                            <h6 class="footer-subtitle">
                                <i class="bi bi-telephone"></i> Contact
                            </h6>
                            <div class="footer-contact">
                                <div class="contact-item">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <div>
                                        <strong>18 Rue de l'Hôpital</strong><br>
                                        18100 Vierzon, France
                                    </div>
                                </div>
                                <div class="contact-item">
                                    <i class="bi bi-telephone-fill"></i>
                                    <div>
                                        <a href="tel:0123456789">01.23.45.67.89</a>
                                    </div>
                                </div>
                                <div class="contact-item">
                                    <i class="bi bi-envelope-fill"></i>
                                    <div>
                                        <a href="mailto:contact@cabinet-medical.fr">contact@cabinet-medical.fr</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Scripts personnalisés -->
    <script>
        // Confirmation de suppression
        function confirmDelete(message = 'Êtes-vous sûr de vouloir supprimer cet élément ?') {
            return confirm(message);
        }
        
        // Auto-hide des alertes après 5 secondes
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
        
        // Validation des formulaires
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        });

        // Gestion du logo
        document.addEventListener('DOMContentLoaded', function() {
            const logoImg = document.getElementById('logo-img');
            const logoText = document.querySelector('.logo-text');

            if (logoImg) {
                // Vérifier si le logo existe
                logoImg.onload = function() {
                    this.style.display = 'inline-block';
                    if (logoText) logoText.style.marginLeft = '10px';
                };
                logoImg.onerror = function() {
                    this.style.display = 'none';
                    if (logoText) logoText.style.marginLeft = '0';
                };

                // Animation au survol
                logoImg.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.05)';
                });
                logoImg.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            }

            // Effet parallaxe subtil pour le logo en filigrane
            let ticking = false;

            function updateParallax() {
                const scrolled = window.pageYOffset;
                const parallax = document.body;
                const speed = scrolled * 0.1;

                parallax.style.backgroundPosition = `center ${speed}px`;
                ticking = false;
            }

            function requestTick() {
                if (!ticking) {
                    requestAnimationFrame(updateParallax);
                    ticking = true;
                }
            }

            window.addEventListener('scroll', requestTick);
        });
    </script>
</body>
</html>

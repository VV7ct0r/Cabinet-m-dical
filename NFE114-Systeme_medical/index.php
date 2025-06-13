<?php
/**
 * Page d'accueil du système de cabinet médical
 * NFE114 - Système de cabinet médical
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

$pageTitle = 'Accueil';

// Récupérer les praticiens pour l'affichage
$praticiens = getAllPraticiens();
?>

<?php include 'includes/header.php'; ?>

<div class="row">
    <div class="col-lg-8">
        <!-- Hero Section -->
        <div class="card mb-4 fade-in-up">
            <div class="card-body text-center py-5">
                <h1 class="display-4 text-primary">
                    <i class="bi bi-hospital"></i> Cabinet Médical
                </h1>
                <p class="lead">Système de gestion des rendez-vous médicaux</p>
                <p class="text-muted">
                    Prenez rendez-vous facilement avec nos praticiens qualifiés
                </p>
                
                <?php if (!isLoggedIn()): ?>
                    <div class="mt-4">
                        <a href="register.php" class="btn btn-primary btn-lg me-3 btn-shine">
                            <i class="bi bi-person-plus"></i> S'inscrire
                        </a>
                        <a href="login.php" class="btn btn-outline-primary btn-lg btn-shine">
                            <i class="bi bi-box-arrow-in-right"></i> Se connecter
                        </a>
                    </div>
                <?php else: ?>
                    <div class="mt-4">
                        <?php if (isPatient()): ?>
                            <a href="rdv.php" class="btn btn-primary btn-lg me-3">
                                <i class="bi bi-calendar-plus"></i> Prendre RDV
                            </a>
                            <a href="mes_rdv.php" class="btn btn-outline-primary btn-lg">
                                <i class="bi bi-calendar-check"></i> Mes RDV
                            </a>
                        <?php elseif (isAdmin()): ?>
                            <a href="admin.php" class="btn btn-primary btn-lg">
                                <i class="bi bi-gear"></i> Administration
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Fonctionnalités -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100 card-hover">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar-plus display-4 text-primary mb-3 icon-bounce"></i>
                        <h5 class="card-title">Prise de RDV</h5>
                        <p class="card-text">
                            Prenez rendez-vous en ligne avec le praticien de votre choix
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card h-100 card-hover">
                    <div class="card-body text-center">
                        <i class="bi bi-people display-4 text-success mb-3 icon-bounce"></i>
                        <h5 class="card-title">Praticiens qualifiés</h5>
                        <p class="card-text">
                            Une équipe de professionnels de santé à votre service
                        </p>
                    </div>
                </div>
            </div>
            

        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Informations pratiques -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-info-circle"></i> Informations pratiques
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6><i class="bi bi-clock"></i> Horaires d'ouverture</h6>
                    <small class="text-muted">
                        Lundi - Vendredi : 8h00 - 18h00<br>
                        Samedi : 9h00 - 12h00<br>
                        Dimanche : Fermé
                    </small>
                </div>

                <div class="mb-3">
                    <h6><i class="bi bi-geo-alt"></i> Adresse</h6>
                    <small class="text-muted">
                        18 Rue de l'hôpital<br>
                        18100 Vierzon, France
                    </small>
                </div>

                <div>
                    <h6><i class="bi bi-telephone"></i> Contact</h6>
                    <small class="text-muted">
                        Tél : 01.23.45.67.89<br>
                        Email : contact@cabinet-medical.fr
                    </small>
                </div>
            </div>
        </div>

        <!-- Nos Praticiens -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-people"></i> Nos Praticiens
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($praticiens)): ?>
                    <?php foreach ($praticiens as $praticien): ?>
                        <div class="d-flex align-items-center mb-3 p-2 border rounded">
                            <div class="flex-shrink-0">
                                <i class="bi bi-person-circle display-6 text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">
                                    <?php echo htmlspecialchars($praticien['prenom'] . ' ' . $praticien['nom']); ?>
                                </h6>
                                <small class="text-muted">
                                    <i class="bi bi-briefcase"></i>
                                    <?php echo htmlspecialchars($praticien['specialite']); ?>
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if (isLoggedIn() && isPatient()): ?>
                        <div class="text-center mt-3">
                            <a href="rdv.php" class="btn btn-primary btn-sm">
                                <i class="bi bi-calendar-plus"></i> Prendre RDV
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-muted text-center">
                        <i class="bi bi-info-circle"></i>
                        Aucun praticien disponible pour le moment
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Espace avant le footer -->
<div class="mb-5"></div>

<?php include 'includes/footer.php'; ?>

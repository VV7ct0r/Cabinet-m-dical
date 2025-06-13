<?php
/**
 * Tableau de bord patient
 * NFE114 - Système de cabinet médical
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

$pageTitle = 'Tableau de bord';

// Vérifier que l'utilisateur est connecté et est un patient
if (!isLoggedIn() || !isPatient()) {
    redirect('login.php', 'Veuillez vous connecter pour accéder à cette page.', 'warning');
}

// Récupérer les informations du patient
$patientId = $_SESSION['user_id'];

// Récupérer les prochains rendez-vous
$prochainRdv = [];
$rdvPatient = getRendezVousPatient($patientId);

foreach ($rdvPatient as $rdv) {
    if ($rdv['statut'] === 'confirme' && strtotime($rdv['date_heure']) > time()) {
        $prochainRdv[] = $rdv;
    }
}

// Trier par date
usort($prochainRdv, function($a, $b) {
    return strtotime($a['date_heure']) - strtotime($b['date_heure']);
});

// Limiter aux 3 prochains
$prochainRdv = array_slice($prochainRdv, 0, 3);

// Statistiques
$totalRdv = count($rdvPatient);
$rdvConfirmes = count(array_filter($rdvPatient, function($rdv) {
    return $rdv['statut'] === 'confirme';
}));
$rdvAnnules = count(array_filter($rdvPatient, function($rdv) {
    return $rdv['statut'] === 'annule';
}));

// Récupérer les praticiens disponibles
$praticiens = getAllPraticiens();
?>

<?php include 'includes/header.php'; ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="bi bi-speedometer2"></i> 
                Bonjour <?php echo htmlspecialchars($_SESSION['user_prenom']); ?> !
            </h2>
            <div>
                <a href="rdv.php" class="btn btn-primary">
                    <i class="bi bi-calendar-plus"></i> Prendre RDV
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-calendar-check display-4 text-primary mb-2"></i>
                <h4 class="text-primary"><?php echo $rdvConfirmes; ?></h4>
                <p class="card-text">RDV Confirmés</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-calendar-x display-4 text-danger mb-2"></i>
                <h4 class="text-danger"><?php echo $rdvAnnules; ?></h4>
                <p class="card-text">RDV Annulés</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-calendar display-4 text-info mb-2"></i>
                <h4 class="text-info"><?php echo $totalRdv; ?></h4>
                <p class="card-text">Total RDV</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Prochains rendez-vous -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-event"></i> Prochains rendez-vous
                </h5>
                <a href="mes_rdv.php" class="btn btn-sm btn-outline-primary">
                    Voir tous mes RDV
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($prochainRdv)): ?>
                    <?php foreach ($prochainRdv as $rdv): ?>
                        <div class="d-flex align-items-center p-3 border rounded mb-3 rdv-card">
                            <div class="flex-shrink-0">
                                <i class="bi bi-person-circle display-6 text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">
                                    Dr. <?php echo htmlspecialchars($rdv['prenom'] . ' ' . $rdv['nom']); ?>
                                </h6>
                                <p class="mb-1 text-muted">
                                    <i class="bi bi-briefcase"></i> 
                                    <?php echo htmlspecialchars($rdv['specialite']); ?>
                                </p>
                                <p class="mb-0">
                                    <i class="bi bi-calendar"></i> 
                                    <strong><?php echo formatDate($rdv['date_heure']); ?></strong>
                                </p>
                                <?php if (!empty($rdv['motif'])): ?>
                                    <small class="text-muted">
                                        <i class="bi bi-chat-text"></i> 
                                        <?php echo htmlspecialchars($rdv['motif']); ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="badge bg-success">Confirmé</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-calendar-x display-4 text-muted mb-3"></i>
                        <h5 class="text-muted">Aucun rendez-vous à venir</h5>
                        <p class="text-muted">Prenez rendez-vous avec l'un de nos praticiens</p>
                        <a href="rdv.php" class="btn btn-primary">
                            <i class="bi bi-calendar-plus"></i> Prendre RDV
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Actions rapides et praticiens -->
    <div class="col-lg-4">
        <!-- Actions rapides -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-lightning"></i> Actions rapides
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="rdv.php" class="btn btn-primary">
                        <i class="bi bi-calendar-plus"></i> Prendre un rendez-vous
                    </a>
                    <a href="mes_rdv.php" class="btn btn-outline-primary">
                        <i class="bi bi-calendar-check"></i> Mes rendez-vous
                    </a>
                </div>
            </div>
        </div>

        <!-- Nos praticiens -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-people"></i> Nos praticiens
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($praticiens)): ?>
                    <?php foreach (array_slice($praticiens, 0, 4) as $praticien): ?>
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <i class="bi bi-person-circle text-primary fs-4"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">
                                    Dr. <?php echo htmlspecialchars($praticien['prenom'] . ' ' . $praticien['nom']); ?>
                                </h6>
                                <small class="text-muted">
                                    <?php echo htmlspecialchars($praticien['specialite']); ?>
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if (count($praticiens) > 4): ?>
                        <div class="text-center">
                            <small class="text-muted">
                                Et <?php echo count($praticiens) - 4; ?> autre(s) praticien(s)
                            </small>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-muted text-center">
                        Aucun praticien disponible
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Espace avant le footer -->
<div class="mb-5"></div>

<?php include 'includes/footer.php'; ?>

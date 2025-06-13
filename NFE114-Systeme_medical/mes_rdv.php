<?php
/**
 * Page de gestion des rendez-vous du patient
 * NFE114 - Système de cabinet médical
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

$pageTitle = 'Mes rendez-vous';

// Vérifier que l'utilisateur est connecté et est un patient
if (!isLoggedIn() || !isPatient()) {
    redirect('login.php', 'Veuillez vous connecter pour accéder à cette page.', 'warning');
}

$patientId = $_SESSION['user_id'];
$errors = [];

// Traitement de l'annulation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'annuler') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = "Erreur de sécurité. Veuillez réessayer.";
    } else {
        $rdvId = (int)($_POST['rdv_id'] ?? 0);
        
        if ($rdvId > 0) {
            if (annulerRendezVous($rdvId, $patientId)) {
                redirect('mes_rdv.php', 'Rendez-vous annulé avec succès.', 'success');
            } else {
                $errors[] = "Erreur lors de l'annulation du rendez-vous.";
            }
        } else {
            $errors[] = "Rendez-vous invalide.";
        }
    }
}

// Récupérer tous les rendez-vous du patient
$rdvPatient = getRendezVousPatient($patientId);

// Séparer les RDV par statut et date
$rdvFuturs = [];
$rdvPasses = [];
$rdvAnnules = [];

foreach ($rdvPatient as $rdv) {
    $dateRdv = strtotime($rdv['date_heure']);
    $maintenant = time();
    
    if ($rdv['statut'] === 'annule') {
        $rdvAnnules[] = $rdv;
    } elseif ($dateRdv > $maintenant) {
        $rdvFuturs[] = $rdv;
    } else {
        $rdvPasses[] = $rdv;
    }
}

// Trier par date
usort($rdvFuturs, function($a, $b) {
    return strtotime($a['date_heure']) - strtotime($b['date_heure']);
});

usort($rdvPasses, function($a, $b) {
    return strtotime($b['date_heure']) - strtotime($a['date_heure']);
});

usort($rdvAnnules, function($a, $b) {
    return strtotime($b['date_heure']) - strtotime($a['date_heure']);
});
?>

<?php include 'includes/header.php'; ?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-calendar-check"></i> Mes rendez-vous</h2>
            <a href="rdv.php" class="btn btn-primary">
                <i class="bi bi-calendar-plus"></i> Nouveau RDV
            </a>
        </div>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Statistiques rapides -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-primary"><?php echo count($rdvFuturs); ?></h4>
                <p class="card-text">À venir</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-success"><?php echo count($rdvPasses); ?></h4>
                <p class="card-text">Passés</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-danger"><?php echo count($rdvAnnules); ?></h4>
                <p class="card-text">Annulés</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4 class="text-info"><?php echo count($rdvPatient); ?></h4>
                <p class="card-text">Total</p>
            </div>
        </div>
    </div>
</div>

<!-- Onglets -->
<ul class="nav nav-tabs" id="rdvTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="futurs-tab" data-bs-toggle="tab" data-bs-target="#futurs" type="button" role="tab">
            <i class="bi bi-calendar-event"></i> À venir (<?php echo count($rdvFuturs); ?>)
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="passes-tab" data-bs-toggle="tab" data-bs-target="#passes" type="button" role="tab">
            <i class="bi bi-calendar-check"></i> Passés (<?php echo count($rdvPasses); ?>)
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="annules-tab" data-bs-toggle="tab" data-bs-target="#annules" type="button" role="tab">
            <i class="bi bi-calendar-x"></i> Annulés (<?php echo count($rdvAnnules); ?>)
        </button>
    </li>
</ul>

<div class="tab-content" id="rdvTabsContent">
    <!-- RDV à venir -->
    <div class="tab-pane fade show active" id="futurs" role="tabpanel">
        <div class="mt-4">
            <?php if (!empty($rdvFuturs)): ?>
                <?php foreach ($rdvFuturs as $rdv): ?>
                    <div class="card mb-3 rdv-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-2 text-center">
                                    <i class="bi bi-person-circle display-4 text-primary"></i>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="card-title mb-1">
                                        Dr. <?php echo htmlspecialchars($rdv['prenom'] . ' ' . $rdv['nom']); ?>
                                    </h5>
                                    <p class="text-muted mb-1">
                                        <i class="bi bi-briefcase"></i> 
                                        <?php echo htmlspecialchars($rdv['specialite']); ?>
                                    </p>
                                    <p class="mb-1">
                                        <i class="bi bi-calendar"></i> 
                                        <strong><?php echo formatDate($rdv['date_heure']); ?></strong>
                                    </p>
                                    <?php if (!empty($rdv['motif'])): ?>
                                        <p class="mb-0 text-muted">
                                            <i class="bi bi-chat-text"></i> 
                                            <?php echo htmlspecialchars($rdv['motif']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-2 text-center">
                                    <span class="badge bg-success fs-6">Confirmé</span>
                                </div>
                                <div class="col-md-2 text-center">
                                    <?php
                                    $heuresAvantRdv = (strtotime($rdv['date_heure']) - time()) / 3600;
                                    if ($heuresAvantRdv > 2): // Peut annuler si plus de 2h avant
                                    ?>
                                        <form method="POST" style="display: inline;" 
                                              onsubmit="return confirmDelete('Êtes-vous sûr de vouloir annuler ce rendez-vous ?')">
                                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                            <input type="hidden" name="action" value="annuler">
                                            <input type="hidden" name="rdv_id" value="<?php echo $rdv['id_rdv']; ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-x-circle"></i> Annuler
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle"></i><br>
                                            Trop tard pour annuler
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x display-1 text-muted mb-3"></i>
                    <h4 class="text-muted">Aucun rendez-vous à venir</h4>
                    <p class="text-muted">Prenez rendez-vous avec l'un de nos praticiens</p>
                    <a href="rdv.php" class="btn btn-primary">
                        <i class="bi bi-calendar-plus"></i> Prendre RDV
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- RDV passés -->
    <div class="tab-pane fade" id="passes" role="tabpanel">
        <div class="mt-4">
            <?php if (!empty($rdvPasses)): ?>
                <?php foreach ($rdvPasses as $rdv): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-2 text-center">
                                    <i class="bi bi-person-circle display-4 text-success"></i>
                                </div>
                                <div class="col-md-8">
                                    <h5 class="card-title mb-1">
                                        Dr. <?php echo htmlspecialchars($rdv['prenom'] . ' ' . $rdv['nom']); ?>
                                    </h5>
                                    <p class="text-muted mb-1">
                                        <i class="bi bi-briefcase"></i> 
                                        <?php echo htmlspecialchars($rdv['specialite']); ?>
                                    </p>
                                    <p class="mb-1">
                                        <i class="bi bi-calendar"></i> 
                                        <?php echo formatDate($rdv['date_heure']); ?>
                                    </p>
                                    <?php if (!empty($rdv['motif'])): ?>
                                        <p class="mb-0 text-muted">
                                            <i class="bi bi-chat-text"></i> 
                                            <?php echo htmlspecialchars($rdv['motif']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-2 text-center">
                                    <span class="badge bg-secondary">Terminé</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-calendar-check display-1 text-muted mb-3"></i>
                    <h4 class="text-muted">Aucun rendez-vous passé</h4>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- RDV annulés -->
    <div class="tab-pane fade" id="annules" role="tabpanel">
        <div class="mt-4">
            <?php if (!empty($rdvAnnules)): ?>
                <?php foreach ($rdvAnnules as $rdv): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-2 text-center">
                                    <i class="bi bi-person-circle display-4 text-danger"></i>
                                </div>
                                <div class="col-md-8">
                                    <h5 class="card-title mb-1">
                                        Dr. <?php echo htmlspecialchars($rdv['prenom'] . ' ' . $rdv['nom']); ?>
                                    </h5>
                                    <p class="text-muted mb-1">
                                        <i class="bi bi-briefcase"></i> 
                                        <?php echo htmlspecialchars($rdv['specialite']); ?>
                                    </p>
                                    <p class="mb-1">
                                        <i class="bi bi-calendar"></i> 
                                        <?php echo formatDate($rdv['date_heure']); ?>
                                    </p>
                                    <?php if (!empty($rdv['motif'])): ?>
                                        <p class="mb-0 text-muted">
                                            <i class="bi bi-chat-text"></i> 
                                            <?php echo htmlspecialchars($rdv['motif']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-2 text-center">
                                    <span class="badge bg-danger">Annulé</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x display-1 text-muted mb-3"></i>
                    <h4 class="text-muted">Aucun rendez-vous annulé</h4>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Espace avant le footer -->
<div class="mb-5"></div>

<?php include 'includes/footer.php'; ?>

<?php
/**
 * Interface d'administration
 * NFE114 - Système de cabinet médical
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

$pageTitle = 'Administration';

// Vérifier que l'utilisateur est connecté et est un admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php', 'Accès non autorisé.', 'error');
}

$errors = [];
$success = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = "Erreur de sécurité. Veuillez réessayer.";
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'ajouter_praticien') {
            // Ajouter un praticien
            $nom = cleanInput($_POST['nom'] ?? '');
            $prenom = cleanInput($_POST['prenom'] ?? '');
            $specialite = cleanInput($_POST['specialite'] ?? '');
            $email = cleanInput($_POST['email'] ?? '');
            $telephone = cleanInput($_POST['telephone'] ?? '');

            // Validation
            if (empty($nom) || empty($prenom) || empty($specialite) || empty($email)) {
                $errors[] = "Tous les champs obligatoires doivent être remplis.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email invalide.";
            } else {
                $praticienData = [
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'specialite' => $specialite,
                    'email' => $email,
                    'telephone' => $telephone
                ];

                if (ajouterPraticien($praticienData)) {
                    $success = "Praticien ajouté avec succès !";
                } else {
                    $errors[] = "Erreur lors de l'ajout du praticien. Email peut-être déjà utilisé.";
                }
            }
        } elseif ($action === 'modifier_praticien') {
            // Modifier un praticien
            $praticienId = (int)($_POST['praticien_id'] ?? 0);
            $nom = cleanInput($_POST['nom'] ?? '');
            $prenom = cleanInput($_POST['prenom'] ?? '');
            $specialite = cleanInput($_POST['specialite'] ?? '');
            $email = cleanInput($_POST['email'] ?? '');
            $telephone = cleanInput($_POST['telephone'] ?? '');

            // Validation
            if (empty($nom) || empty($prenom) || empty($specialite) || empty($email)) {
                $errors[] = "Tous les champs obligatoires doivent être remplis.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email invalide.";
            } elseif ($praticienId <= 0) {
                $errors[] = "Praticien invalide.";
            } else {
                $praticienData = [
                    'id_praticien' => $praticienId,
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'specialite' => $specialite,
                    'email' => $email,
                    'telephone' => $telephone
                ];

                if (modifierPraticien($praticienData)) {
                    $success = "Praticien modifié avec succès !";
                } else {
                    $errors[] = "Erreur lors de la modification du praticien. Email peut-être déjà utilisé.";
                }
            }
        } elseif ($action === 'supprimer_praticien') {
            // Supprimer un praticien
            $praticienId = (int)($_POST['praticien_id'] ?? 0);

            if ($praticienId > 0) {
                if (supprimerPraticien($praticienId)) {
                    $success = "Praticien supprimé avec succès !";
                } else {
                    $errors[] = "Erreur lors de la suppression du praticien.";
                }
            } else {
                $errors[] = "ID praticien invalide.";
            }
        }
    }
}

// Récupérer les données pour l'affichage
$praticiens = getAllPraticiens();

// Statistiques
$pdo = getDbConnection();

// Nombre de patients
$stmt = $pdo->query("SELECT COUNT(*) FROM Patient");
$nbPatients = $stmt->fetchColumn();

// Nombre de RDV ce mois
$stmt = $pdo->query("SELECT COUNT(*) FROM RendezVous WHERE MONTH(date_heure) = MONTH(CURDATE()) AND YEAR(date_heure) = YEAR(CURDATE())");
$rdvCeMois = $stmt->fetchColumn();

// Nombre de RDV aujourd'hui
$stmt = $pdo->query("SELECT COUNT(*) FROM RendezVous WHERE DATE(date_heure) = CURDATE() AND statut = 'confirme'");
$rdvAujourdhui = $stmt->fetchColumn();

// RDV récents
$stmt = $pdo->prepare("
    SELECT r.*, p.nom as patient_nom, p.prenom as patient_prenom, 
           pr.nom as praticien_nom, pr.prenom as praticien_prenom, pr.specialite
    FROM RendezVous r
    JOIN Patient p ON r.id_patient = p.id_patient
    JOIN Praticien pr ON r.id_praticien = pr.id_praticien
    ORDER BY r.date_creation DESC
    LIMIT 10
");
$stmt->execute();
$rdvRecents = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-gear"></i> Administration</h2>
        <p class="text-muted">Gestion du cabinet médical</p>
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

<?php if (!empty($success)): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<!-- Statistiques -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-people display-4 text-primary mb-2"></i>
                <h4 class="text-primary"><?php echo $nbPatients; ?></h4>
                <p class="card-text">Patients inscrits</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-person-badge display-4 text-success mb-2"></i>
                <h4 class="text-success"><?php echo count($praticiens); ?></h4>
                <p class="card-text">Praticiens</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-calendar-event display-4 text-info mb-2"></i>
                <h4 class="text-info"><?php echo $rdvCeMois; ?></h4>
                <p class="card-text">RDV ce mois</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-calendar-check display-4 text-warning mb-2"></i>
                <h4 class="text-warning"><?php echo $rdvAujourdhui; ?></h4>
                <p class="card-text">RDV aujourd'hui</p>
            </div>
        </div>
    </div>
</div>

<!-- Onglets -->
<ul class="nav nav-tabs" id="adminTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="praticiens-tab" data-bs-toggle="tab" data-bs-target="#praticiens" type="button" role="tab">
            <i class="bi bi-person-badge"></i> Praticiens
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="rdv-tab" data-bs-toggle="tab" data-bs-target="#rdv" type="button" role="tab">
            <i class="bi bi-calendar-event"></i> Rendez-vous récents
        </button>
    </li>
</ul>

<div class="tab-content" id="adminTabsContent">
    <!-- Gestion des praticiens -->
    <div class="tab-pane fade show active" id="praticiens" role="tabpanel">
        <div class="row mt-4">
            <div class="col-lg-8">
                <!-- Liste des praticiens -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Liste des praticiens</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($praticiens)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Spécialité</th>
                                            <th>Email</th>
                                            <th>Téléphone</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($praticiens as $praticien): ?>
                                            <tr>
                                                <td>
                                                    <strong>Dr. <?php echo htmlspecialchars($praticien['prenom'] . ' ' . $praticien['nom']); ?></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($praticien['specialite']); ?></td>
                                                <td><?php echo htmlspecialchars($praticien['email']); ?></td>
                                                <td><?php echo htmlspecialchars($praticien['telephone'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-outline-primary btn-sm me-2"
                                                            data-bs-toggle="modal" data-bs-target="#modifierPraticienModal"
                                                            onclick="remplirFormulaireModification(<?php echo htmlspecialchars(json_encode($praticien)); ?>)">
                                                        <i class="bi bi-pencil"></i> Modifier
                                                    </button>
                                                    <form method="POST" style="display: inline;"
                                                          onsubmit="return confirmDelete('Êtes-vous sûr de vouloir supprimer ce praticien ? Tous ses rendez-vous seront également supprimés.')">
                                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                        <input type="hidden" name="action" value="supprimer_praticien">
                                                        <input type="hidden" name="praticien_id" value="<?php echo $praticien['id_praticien']; ?>">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                                            <i class="bi bi-trash"></i> Supprimer
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-person-x display-4 text-muted mb-3"></i>
                                <h5 class="text-muted">Aucun praticien</h5>
                                <p class="text-muted">Ajoutez le premier praticien</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Formulaire d'ajout -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Ajouter un praticien</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            <input type="hidden" name="action" value="ajouter_praticien">
                            
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom *</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                                <div class="invalid-feedback">
                                    Veuillez saisir le nom.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="prenom" class="form-label">Prénom *</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" required>
                                <div class="invalid-feedback">
                                    Veuillez saisir le prénom.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="specialite" class="form-label">Spécialité *</label>
                                <input type="text" class="form-control" id="specialite" name="specialite" 
                                       placeholder="ex: Médecine générale" required>
                                <div class="invalid-feedback">
                                    Veuillez saisir la spécialité.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="invalid-feedback">
                                    Veuillez saisir un email valide.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="telephone" name="telephone">
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-person-plus"></i> Ajouter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rendez-vous récents -->
    <div class="tab-pane fade" id="rdv" role="tabpanel">
        <div class="mt-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Rendez-vous récents</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($rdvRecents)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th>Praticien</th>
                                        <th>Date RDV</th>
                                        <th>Statut</th>
                                        <th>Créé le</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rdvRecents as $rdv): ?>
                                        <tr>
                                            <td>
                                                <?php echo htmlspecialchars($rdv['patient_prenom'] . ' ' . $rdv['patient_nom']); ?>
                                            </td>
                                            <td>
                                                Dr. <?php echo htmlspecialchars($rdv['praticien_prenom'] . ' ' . $rdv['praticien_nom']); ?>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($rdv['specialite']); ?></small>
                                            </td>
                                            <td><?php echo formatDate($rdv['date_heure']); ?></td>
                                            <td>
                                                <?php
                                                $badgeClass = '';
                                                switch ($rdv['statut']) {
                                                    case 'confirme':
                                                        $badgeClass = 'bg-success';
                                                        break;
                                                    case 'annule':
                                                        $badgeClass = 'bg-danger';
                                                        break;
                                                    case 'termine':
                                                        $badgeClass = 'bg-secondary';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?php echo $badgeClass; ?>">
                                                    <?php echo ucfirst($rdv['statut']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo formatDate($rdv['date_creation']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-x display-4 text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun rendez-vous</h5>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de modification de praticien -->
<div class="modal fade" id="modifierPraticienModal" tabindex="-1" aria-labelledby="modifierPraticienModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modifierPraticienModalLabel">
                    <i class="bi bi-pencil"></i> Modifier le praticien
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" class="needs-validation" novalidate>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="modifier_praticien">
                    <input type="hidden" name="praticien_id" id="modifier_praticien_id">

                    <div class="mb-3">
                        <label for="modifier_nom" class="form-label">Nom *</label>
                        <input type="text" class="form-control" id="modifier_nom" name="nom" required>
                        <div class="invalid-feedback">
                            Veuillez saisir le nom.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="modifier_prenom" class="form-label">Prénom *</label>
                        <input type="text" class="form-control" id="modifier_prenom" name="prenom" required>
                        <div class="invalid-feedback">
                            Veuillez saisir le prénom.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="modifier_specialite" class="form-label">Spécialité *</label>
                        <input type="text" class="form-control" id="modifier_specialite" name="specialite"
                               placeholder="ex: Médecine générale" required>
                        <div class="invalid-feedback">
                            Veuillez saisir la spécialité.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="modifier_email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="modifier_email" name="email" required>
                        <div class="invalid-feedback">
                            Veuillez saisir un email valide.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="modifier_telephone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" id="modifier_telephone" name="telephone">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Espace avant le footer -->
<div class="mb-5"></div>

<script>
// Fonction pour remplir le formulaire de modification
function remplirFormulaireModification(praticien) {
    document.getElementById('modifier_praticien_id').value = praticien.id_praticien;
    document.getElementById('modifier_nom').value = praticien.nom;
    document.getElementById('modifier_prenom').value = praticien.prenom;
    document.getElementById('modifier_specialite').value = praticien.specialite;
    document.getElementById('modifier_email').value = praticien.email;
    document.getElementById('modifier_telephone').value = praticien.telephone || '';

    // Réinitialiser la validation
    const form = document.querySelector('#modifierPraticienModal form');
    form.classList.remove('was-validated');
    const inputs = form.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.classList.remove('is-invalid');
        input.classList.remove('is-valid');
    });
}
</script>

<?php include 'includes/footer.php'; ?>

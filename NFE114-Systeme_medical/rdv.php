<?php
/**
 * Page de prise de rendez-vous
 * NFE114 - Système de cabinet médical
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

$pageTitle = 'Prendre rendez-vous';

// Vérifier que l'utilisateur est connecté et est un patient
if (!isLoggedIn() || !isPatient()) {
    redirect('login.php', 'Veuillez vous connecter pour accéder à cette page.', 'warning');
}

$patientId = $_SESSION['user_id'];
$errors = [];
$success = false;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = "Erreur de sécurité. Veuillez réessayer.";
    } else {
        $praticienId = (int)($_POST['praticien_id'] ?? 0);
        $dateHeure = cleanInput($_POST['date_heure'] ?? '');
        $motif = cleanInput($_POST['motif'] ?? '');

        // Validation
        if ($praticienId <= 0) {
            $errors[] = "Veuillez sélectionner un praticien.";
        }
        
        if (empty($dateHeure)) {
            $errors[] = "Veuillez sélectionner une date et heure.";
        } else {
            // Vérifier que la date est dans le futur
            if (strtotime($dateHeure) <= time()) {
                $errors[] = "La date doit être dans le futur.";
            }
        }

        // Si pas d'erreurs, créer le rendez-vous
        if (empty($errors)) {
            if (prendreRendezVous($patientId, $praticienId, $dateHeure, $motif)) {
                redirect('mes_rdv.php', 'Rendez-vous pris avec succès !', 'success');
            } else {
                $errors[] = "Erreur lors de la prise de rendez-vous. Le créneau n'est peut-être plus disponible.";
            }
        }
    }
}

// Récupérer les praticiens et leurs disponibilités
$praticiens = getAllPraticiens();
$disponibilites = getAllDisponibilites();

// Organiser les disponibilités par praticien
$disponibilitesPraticien = [];
foreach ($disponibilites as $dispo) {
    $disponibilitesPraticien[$dispo['id_praticien']][] = $dispo;
}
?>

<?php include 'includes/header.php'; ?>

<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-calendar-plus"></i> Prendre rendez-vous</h2>
        <p class="text-muted">Sélectionnez un praticien et un créneau disponible</p>
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

<div class="row">
    <div class="col-lg-8">
        <!-- Formulaire de prise de RDV -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Nouveau rendez-vous</h5>
            </div>
            <div class="card-body">
                <form method="POST" class="needs-validation" novalidate id="rdvForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="mb-3">
                        <label for="praticien_id" class="form-label">Praticien *</label>
                        <select class="form-select" id="praticien_id" name="praticien_id" required>
                            <option value="">Sélectionnez un praticien</option>
                            <?php foreach ($praticiens as $praticien): ?>
                                <option value="<?php echo $praticien['id_praticien']; ?>"
                                        <?php echo (isset($_POST['praticien_id']) && $_POST['praticien_id'] == $praticien['id_praticien']) ? 'selected' : ''; ?>>
                                    Dr. <?php echo htmlspecialchars($praticien['prenom'] . ' ' . $praticien['nom']); ?> 
                                    - <?php echo htmlspecialchars($praticien['specialite']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Veuillez sélectionner un praticien.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="date_heure" class="form-label">Date et heure *</label>
                        <select class="form-select" id="date_heure" name="date_heure" required>
                            <option value="">Sélectionnez d'abord un praticien</option>
                        </select>
                        <div class="invalid-feedback">
                            Veuillez sélectionner une date et heure.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="motif" class="form-label">Motif de consultation</label>
                        <textarea class="form-control" id="motif" name="motif" rows="3" 
                                  placeholder="Décrivez brièvement le motif de votre consultation (optionnel)"><?php echo htmlspecialchars($_POST['motif'] ?? ''); ?></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-calendar-plus"></i> Confirmer le rendez-vous
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Informations -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-info-circle"></i> Informations
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6><i class="bi bi-clock"></i> Durée des consultations</h6>
                    <p class="text-muted mb-0">
                        Chaque consultation dure environ 30 minutes.
                    </p>
                </div>
                
                <div class="mb-3">
                    <h6><i class="bi bi-calendar-x"></i> Annulation</h6>
                    <p class="text-muted mb-0">
                        Vous pouvez annuler votre rendez-vous jusqu'à 2 heures avant.
                    </p>
                </div>
                
                <div>
                    <h6><i class="bi bi-telephone"></i> Contact</h6>
                    <p class="text-muted mb-0">
                        Pour toute urgence : 01.23.45.67.89
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Données des disponibilités par praticien
const disponibilitesPraticien = <?php echo json_encode($disponibilitesPraticien); ?>;

document.getElementById('praticien_id').addEventListener('change', function() {
    const praticienId = this.value;
    const dateHeureSelect = document.getElementById('date_heure');
    
    // Vider les options
    dateHeureSelect.innerHTML = '<option value="">Chargement...</option>';
    
    if (praticienId && disponibilitesPraticien[praticienId]) {
        const disponibilites = disponibilitesPraticien[praticienId];
        dateHeureSelect.innerHTML = '<option value="">Sélectionnez une date et heure</option>';
        
        disponibilites.forEach(function(dispo) {
            const option = document.createElement('option');
            option.value = dispo.date_heure;
            
            const date = new Date(dispo.date_heure);
            const dateStr = date.toLocaleDateString('fr-FR', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            option.textContent = dateStr;
            dateHeureSelect.appendChild(option);
        });
    } else {
        dateHeureSelect.innerHTML = '<option value="">Aucune disponibilité</option>';
    }
});
</script>

<!-- Espace avant le footer -->
<div class="mb-5"></div>

<?php include 'includes/footer.php'; ?>

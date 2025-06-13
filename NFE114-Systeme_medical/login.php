<?php
/**
 * Page de connexion (patients et administrateurs)
 * NFE114 - Système de cabinet médical
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

$pageTitle = 'Connexion';

// Rediriger si déjà connecté
if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('admin.php');
    } else {
        redirect('dashboard.php');
    }
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification du token CSRF
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = "Erreur de sécurité. Veuillez réessayer.";
    } else {
        $login = cleanInput($_POST['login'] ?? '');
        $password = $_POST['mot_de_passe'] ?? '';
        $userType = $_POST['user_type'] ?? 'patient';

        if (empty($login) || empty($password)) {
            $errors[] = "Veuillez saisir votre login et mot de passe.";
        } else {
            if ($userType === 'admin') {
                // Authentification admin
                $admin = authenticateAdmin($login, $password);
                if ($admin) {
                    $_SESSION['user_id'] = $admin['id_admin'];
                    $_SESSION['user_login'] = $admin['login'];
                    $_SESSION['user_type'] = 'admin';
                    $_SESSION['user_nom'] = $admin['nom'];
                    $_SESSION['user_prenom'] = $admin['prenom'];
                    
                    redirect('admin.php', 'Connexion réussie ! Bienvenue dans l\'espace administrateur.', 'success');
                } else {
                    $errors[] = "Login ou mot de passe incorrect pour l'administrateur.";
                }
            } else {
                // Authentification patient
                $patient = authenticatePatient($login, $password);
                if ($patient) {
                    $_SESSION['user_id'] = $patient['id_patient'];
                    $_SESSION['user_login'] = $patient['login'];
                    $_SESSION['user_type'] = 'patient';
                    $_SESSION['user_nom'] = $patient['nom'];
                    $_SESSION['user_prenom'] = $patient['prenom'];
                    $_SESSION['user_email'] = $patient['email'];
                    
                    redirect('dashboard.php', 'Connexion réussie ! Bienvenue ' . $patient['prenom'] . '.', 'success');
                } else {
                    $errors[] = "Login ou mot de passe incorrect.";
                }
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card">
            <div class="card-header text-center">
                <h4><i class="bi bi-box-arrow-in-right"></i> Connexion</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <!-- Type d'utilisateur -->
                    <div class="mb-3">
                        <label class="form-label">Type de compte</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="user_type" id="patient" value="patient" 
                                   <?php echo (!isset($_POST['user_type']) || $_POST['user_type'] === 'patient') ? 'checked' : ''; ?>>
                            <label class="btn btn-outline-primary" for="patient">
                                <i class="bi bi-person"></i> Patient
                            </label>

                            <input type="radio" class="btn-check" name="user_type" id="admin" value="admin"
                                   <?php echo (isset($_POST['user_type']) && $_POST['user_type'] === 'admin') ? 'checked' : ''; ?>>
                            <label class="btn btn-outline-secondary" for="admin">
                                <i class="bi bi-gear"></i> Administrateur
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="login" class="form-label">Login / Email</label>
                        <input type="text" class="form-control" id="login" name="login" 
                               value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>" 
                               required autocomplete="username">
                        <div class="invalid-feedback">
                            Veuillez saisir votre login ou email.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="mot_de_passe" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" 
                               required autocomplete="current-password">
                        <div class="invalid-feedback">
                            Veuillez saisir votre mot de passe.
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Se connecter
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <div class="patient-section">
                        <p class="mb-2">Nouveau patient ?</p>
                        <a href="register.php" class="btn btn-outline-primary">
                            <i class="bi bi-person-plus"></i> S'inscrire
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations de test -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Comptes de test</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Administrateur</h6>
                        <small class="text-muted">
                            Login: <code>admin</code><br>
                            Mot de passe: <code>admin123</code>
                        </small>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-success">Patient</h6>
                        <small class="text-muted">
                            Créez votre compte via<br>
                            <a href="register.php">l'inscription</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Afficher/masquer les sections selon le type d'utilisateur
document.addEventListener('DOMContentLoaded', function() {
    const patientRadio = document.getElementById('patient');
    const adminRadio = document.getElementById('admin');
    const patientSection = document.querySelector('.patient-section');
    
    function toggleSections() {
        if (adminRadio.checked) {
            patientSection.style.display = 'none';
        } else {
            patientSection.style.display = 'block';
        }
    }
    
    patientRadio.addEventListener('change', toggleSections);
    adminRadio.addEventListener('change', toggleSections);
    
    // Initialiser l'affichage
    toggleSections();
});
</script>

<!-- Espace avant le footer -->
<div class="mb-5"></div>

<?php include 'includes/footer.php'; ?>

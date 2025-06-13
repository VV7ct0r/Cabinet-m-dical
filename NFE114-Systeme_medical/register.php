<?php
/**
 * Page d'inscription des patients
 * NFE114 - Système de cabinet médical
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

$pageTitle = 'Inscription Patient';

// Rediriger si déjà connecté
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification du token CSRF
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = "Erreur de sécurité. Veuillez réessayer.";
    } else {
        // Validation des données
        $email = cleanInput($_POST['email'] ?? '');
        $login = cleanInput($_POST['login'] ?? '');
        $password = $_POST['mot_de_passe'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $numeroSecu = cleanInput($_POST['numero_secu'] ?? '');
        $nom = cleanInput($_POST['nom'] ?? '');
        $prenom = cleanInput($_POST['prenom'] ?? '');
        $telephone = cleanInput($_POST['telephone'] ?? '');
        $dateNaissance = cleanInput($_POST['date_naissance'] ?? '');
        $adresse = cleanInput($_POST['adresse'] ?? '');

        // Validation
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide.";
        }
        
        if (empty($login) || strlen($login) < 3) {
            $errors[] = "Le login doit contenir au moins 3 caractères.";
        }
        
        if (empty($password) || strlen($password) < 6) {
            $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }
        
        if (empty($numeroSecu) || !preg_match('/^[0-9]{13,15}$/', $numeroSecu)) {
            $errors[] = "Numéro de sécurité sociale invalide (13-15 chiffres).";
        }
        
        if (empty($nom) || empty($prenom)) {
            $errors[] = "Le nom et le prénom sont obligatoires.";
        }

        // Si pas d'erreurs, créer le patient
        if (empty($errors)) {
            $patientData = [
                'email' => $email,
                'login' => $login,
                'mot_de_passe' => $password,
                'numero_secu' => $numeroSecu,
                'nom' => $nom,
                'prenom' => $prenom,
                'telephone' => $telephone,
                'date_naissance' => $dateNaissance ?: null,
                'adresse' => $adresse
            ];

            if (createPatient($patientData)) {
                $success = true;
                redirect('login.php', 'Inscription réussie ! Vous pouvez maintenant vous connecter.', 'success');
            } else {
                $errors[] = "Erreur lors de l'inscription. Email, login ou numéro de sécurité sociale déjà utilisé.";
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header text-center">
                <h4><i class="bi bi-person-plus"></i> Inscription Patient</h4>
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
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">Nom *</label>
                            <input type="text" class="form-control" id="nom" name="nom" 
                                   value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>" required>
                            <div class="invalid-feedback">
                                Veuillez saisir votre nom.
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="prenom" class="form-label">Prénom *</label>
                            <input type="text" class="form-control" id="prenom" name="prenom" 
                                   value="<?php echo htmlspecialchars($_POST['prenom'] ?? ''); ?>" required>
                            <div class="invalid-feedback">
                                Veuillez saisir votre prénom.
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        <div class="invalid-feedback">
                            Veuillez saisir un email valide.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="login" class="form-label">Login *</label>
                        <input type="text" class="form-control" id="login" name="login" 
                               value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>" 
                               minlength="3" required>
                        <div class="form-text">Au moins 3 caractères</div>
                        <div class="invalid-feedback">
                            Le login doit contenir au moins 3 caractères.
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="mot_de_passe" class="form-label">Mot de passe *</label>
                            <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" 
                                   minlength="6" required>
                            <div class="form-text">Au moins 6 caractères</div>
                            <div class="invalid-feedback">
                                Le mot de passe doit contenir au moins 6 caractères.
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="confirm_password" class="form-label">Confirmer le mot de passe *</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                   minlength="6" required>
                            <div class="invalid-feedback">
                                Veuillez confirmer votre mot de passe.
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="numero_secu" class="form-label">Numéro de Sécurité Sociale *</label>
                        <input type="text" class="form-control" id="numero_secu" name="numero_secu" 
                               value="<?php echo htmlspecialchars($_POST['numero_secu'] ?? ''); ?>" 
                               pattern="[0-9]{13,15}" required>
                        <div class="form-text">13 à 15 chiffres</div>
                        <div class="invalid-feedback">
                            Numéro de sécurité sociale invalide (13-15 chiffres).
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control" id="telephone" name="telephone" 
                                   value="<?php echo htmlspecialchars($_POST['telephone'] ?? ''); ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="date_naissance" class="form-label">Date de naissance</label>
                            <input type="date" class="form-control" id="date_naissance" name="date_naissance" 
                                   value="<?php echo htmlspecialchars($_POST['date_naissance'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="adresse" class="form-label">Adresse</label>
                        <textarea class="form-control" id="adresse" name="adresse" rows="3"><?php echo htmlspecialchars($_POST['adresse'] ?? ''); ?></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-person-plus"></i> S'inscrire
                        </button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <p>Déjà inscrit ? <a href="login.php">Se connecter</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validation du mot de passe en temps réel
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('mot_de_passe').value;
    const confirmPassword = this.value;
    
    if (password !== confirmPassword) {
        this.setCustomValidity('Les mots de passe ne correspondent pas');
    } else {
        this.setCustomValidity('');
    }
});
</script>

<!-- Espace avant le footer -->
<div class="mb-5"></div>

<?php include 'includes/footer.php'; ?>

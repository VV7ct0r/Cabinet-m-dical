<?php
/**
 * Fonctions utilitaires pour le système de cabinet médical
 * NFE114 - Système de cabinet médical
 */

require_once 'config.php';

/**
 * Authentification d'un patient
 * @param string $login
 * @param string $password
 * @return array|false
 */
function authenticatePatient($login, $password) {
    $pdo = getDbConnection();
    
    $stmt = $pdo->prepare("SELECT * FROM Patient WHERE login = ? OR email = ?");
    $stmt->execute([$login, $login]);
    $patient = $stmt->fetch();
    
    if ($patient && password_verify($password, $patient['mot_de_passe'])) {
        return $patient;
    }
    
    return false;
}

/**
 * Authentification d'un administrateur
 * @param string $login
 * @param string $password
 * @return array|false
 */
function authenticateAdmin($login, $password) {
    $pdo = getDbConnection();
    
    $stmt = $pdo->prepare("SELECT * FROM Admin WHERE login = ?");
    $stmt->execute([$login]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['mot_de_passe'])) {
        return $admin;
    }
    
    return false;
}

/**
 * Créer un nouveau patient
 * @param array $data
 * @return bool
 */
function createPatient($data) {
    $pdo = getDbConnection();
    
    // Vérifier si l'email ou le login existe déjà
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Patient WHERE email = ? OR login = ? OR numero_secu = ?");
    $stmt->execute([$data['email'], $data['login'], $data['numero_secu']]);
    
    if ($stmt->fetchColumn() > 0) {
        return false; // Utilisateur déjà existant
    }
    
    // Hacher le mot de passe
    $hashedPassword = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("
        INSERT INTO Patient (email, login, mot_de_passe, numero_secu, nom, prenom, telephone, date_naissance, adresse) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    return $stmt->execute([
        $data['email'],
        $data['login'],
        $hashedPassword,
        $data['numero_secu'],
        $data['nom'],
        $data['prenom'],
        $data['telephone'] ?? null,
        $data['date_naissance'] ?? null,
        $data['adresse'] ?? null
    ]);
}

/**
 * Récupérer tous les praticiens
 * @return array
 */
function getAllPraticiens() {
    $pdo = getDbConnection();
    $stmt = $pdo->query("SELECT * FROM Praticien ORDER BY nom, prenom");
    return $stmt->fetchAll();
}

/**
 * Récupérer un praticien par ID
 * @param int $id
 * @return array|false
 */
function getPraticienById($id) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT * FROM Praticien WHERE id_praticien = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Récupérer les disponibilités d'un praticien
 * @param int $praticienId
 * @param string $dateDebut
 * @return array
 */
function getDisponibilitesPraticien($praticienId, $dateDebut = null) {
    $pdo = getDbConnection();
    
    if ($dateDebut === null) {
        $dateDebut = date('Y-m-d H:i:s');
    }
    
    $stmt = $pdo->prepare("
        SELECT d.*, p.nom, p.prenom, p.specialite 
        FROM Disponibilite d 
        JOIN Praticien p ON d.id_praticien = p.id_praticien 
        WHERE d.id_praticien = ? AND d.date_heure >= ? AND d.est_disponible = 1
        AND NOT EXISTS (
            SELECT 1 FROM RendezVous r 
            WHERE r.id_praticien = d.id_praticien 
            AND r.date_heure = d.date_heure 
            AND r.statut = 'confirme'
        )
        ORDER BY d.date_heure
    ");
    
    $stmt->execute([$praticienId, $dateDebut]);
    return $stmt->fetchAll();
}

/**
 * Récupérer toutes les disponibilités futures
 * @return array
 */
function getAllDisponibilites() {
    $pdo = getDbConnection();
    
    $stmt = $pdo->prepare("
        SELECT d.*, p.nom, p.prenom, p.specialite 
        FROM Disponibilite d 
        JOIN Praticien p ON d.id_praticien = p.id_praticien 
        WHERE d.date_heure >= NOW() AND d.est_disponible = 1
        AND NOT EXISTS (
            SELECT 1 FROM RendezVous r 
            WHERE r.id_praticien = d.id_praticien 
            AND r.date_heure = d.date_heure 
            AND r.statut = 'confirme'
        )
        ORDER BY d.date_heure, p.nom
    ");
    
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Prendre un rendez-vous
 * @param int $patientId
 * @param int $praticienId
 * @param string $dateHeure
 * @param string $motif
 * @return bool
 */
function prendreRendezVous($patientId, $praticienId, $dateHeure, $motif = '') {
    $pdo = getDbConnection();
    
    // Vérifier que le créneau est disponible
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM Disponibilite 
        WHERE id_praticien = ? AND date_heure = ? AND est_disponible = 1
    ");
    $stmt->execute([$praticienId, $dateHeure]);
    
    if ($stmt->fetchColumn() == 0) {
        return false; // Créneau non disponible
    }
    
    // Vérifier qu'il n'y a pas déjà un RDV
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM RendezVous 
        WHERE id_praticien = ? AND date_heure = ? AND statut = 'confirme'
    ");
    $stmt->execute([$praticienId, $dateHeure]);
    
    if ($stmt->fetchColumn() > 0) {
        return false; // Créneau déjà pris
    }
    
    // Créer le rendez-vous
    $stmt = $pdo->prepare("
        INSERT INTO RendezVous (id_patient, id_praticien, date_heure, motif) 
        VALUES (?, ?, ?, ?)
    ");
    
    return $stmt->execute([$patientId, $praticienId, $dateHeure, $motif]);
}

/**
 * Récupérer les rendez-vous d'un patient
 * @param int $patientId
 * @return array
 */
function getRendezVousPatient($patientId) {
    $pdo = getDbConnection();
    
    $stmt = $pdo->prepare("
        SELECT r.*, p.nom, p.prenom, p.specialite 
        FROM RendezVous r 
        JOIN Praticien p ON r.id_praticien = p.id_praticien 
        WHERE r.id_patient = ? 
        ORDER BY r.date_heure DESC
    ");
    
    $stmt->execute([$patientId]);
    return $stmt->fetchAll();
}

/**
 * Annuler un rendez-vous
 * @param int $rdvId
 * @param int $patientId
 * @return bool
 */
function annulerRendezVous($rdvId, $patientId) {
    $pdo = getDbConnection();
    
    $stmt = $pdo->prepare("
        UPDATE RendezVous 
        SET statut = 'annule' 
        WHERE id_rdv = ? AND id_patient = ? AND statut = 'confirme'
    ");
    
    return $stmt->execute([$rdvId, $patientId]);
}

/**
 * Ajouter un praticien (admin)
 * @param array $data
 * @return bool
 */
function ajouterPraticien($data) {
    $pdo = getDbConnection();
    
    $stmt = $pdo->prepare("
        INSERT INTO Praticien (nom, prenom, specialite, email, telephone) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    return $stmt->execute([
        $data['nom'],
        $data['prenom'],
        $data['specialite'],
        $data['email'],
        $data['telephone'] ?? null
    ]);
}

/**
 * Modifier un praticien (admin)
 * @param array $data
 * @return bool
 */
function modifierPraticien($data) {
    $pdo = getDbConnection();

    $stmt = $pdo->prepare("
        UPDATE Praticien
        SET nom = ?, prenom = ?, specialite = ?, email = ?, telephone = ?
        WHERE id_praticien = ?
    ");

    return $stmt->execute([
        $data['nom'],
        $data['prenom'],
        $data['specialite'],
        $data['email'],
        $data['telephone'],
        $data['id_praticien']
    ]);
}

/**
 * Supprimer un praticien (admin)
 * @param int $id
 * @return bool
 */
function supprimerPraticien($id) {
    $pdo = getDbConnection();

    $stmt = $pdo->prepare("DELETE FROM Praticien WHERE id_praticien = ?");
    return $stmt->execute([$id]);
}

/**
 * Formater une date pour l'affichage
 * @param string $date
 * @return string
 */
function formatDate($date) {
    return date('d/m/Y à H:i', strtotime($date));
}

/**
 * Formater une date courte
 * @param string $date
 * @return string
 */
function formatDateShort($date) {
    return date('d/m/Y', strtotime($date));
}
?>

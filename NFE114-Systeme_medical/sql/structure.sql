-- Base de données pour le système de cabinet médical
-- NFE114 - Système d'information

CREATE DATABASE IF NOT EXISTS cabinet_medical CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cabinet_medical;

-- Table des patients
CREATE TABLE Patient (
    id_patient INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(191) NOT NULL,
    login VARCHAR(50) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    numero_secu VARCHAR(15) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20),
    date_naissance DATE,
    adresse TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_email (email),
    UNIQUE KEY unique_login (login),
    UNIQUE KEY unique_numero_secu (numero_secu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des praticiens
CREATE TABLE Praticien (
    id_praticien INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    specialite VARCHAR(100) NOT NULL,
    email VARCHAR(191) NOT NULL,
    telephone VARCHAR(20),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_praticien_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des disponibilités
CREATE TABLE Disponibilite (
    id_disponibilite INT AUTO_INCREMENT PRIMARY KEY,
    id_praticien INT NOT NULL,
    date_heure DATETIME NOT NULL,
    duree_minutes INT DEFAULT 30,
    est_disponible BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_praticien) REFERENCES Praticien(id_praticien) ON DELETE CASCADE,
    UNIQUE KEY unique_praticien_date (id_praticien, date_heure)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des rendez-vous
CREATE TABLE RendezVous (
    id_rdv INT AUTO_INCREMENT PRIMARY KEY,
    id_patient INT NOT NULL,
    id_praticien INT NOT NULL,
    date_heure DATETIME NOT NULL,
    motif TEXT,
    statut ENUM('confirme', 'annule', 'termine') DEFAULT 'confirme',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_patient) REFERENCES Patient(id_patient) ON DELETE CASCADE,
    FOREIGN KEY (id_praticien) REFERENCES Praticien(id_praticien) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des administrateurs
CREATE TABLE Admin (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(191) NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_admin_login (login),
    UNIQUE KEY unique_admin_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion de données de test

-- Admin par défaut (mot de passe: admin123)
INSERT INTO Admin (login, mot_de_passe, nom, prenom, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrateur', 'Système', 'admin@cabinet.com');

-- Praticiens de test
INSERT INTO Praticien (nom, prenom, specialite, email, telephone) VALUES 
('Martin', 'Dr. Jean', 'Médecine générale', 'dr.martin@cabinet.com', '01.23.45.67.89'),
('Dubois', 'Dr. Marie', 'Cardiologie', 'dr.dubois@cabinet.com', '01.23.45.67.90'),
('Leroy', 'Dr. Pierre', 'Dermatologie', 'dr.leroy@cabinet.com', '01.23.45.67.91');

-- Disponibilités de test (pour les 7 prochains jours)
INSERT INTO Disponibilite (id_praticien, date_heure) VALUES 
-- Dr. Martin (ID 1)
(1, DATE_ADD(CURDATE(), INTERVAL 1 DAY) + INTERVAL 9 HOUR),
(1, DATE_ADD(CURDATE(), INTERVAL 1 DAY) + INTERVAL 10 HOUR),
(1, DATE_ADD(CURDATE(), INTERVAL 1 DAY) + INTERVAL 11 HOUR),
(1, DATE_ADD(CURDATE(), INTERVAL 2 DAY) + INTERVAL 9 HOUR),
(1, DATE_ADD(CURDATE(), INTERVAL 2 DAY) + INTERVAL 10 HOUR),
-- Dr. Dubois (ID 2)
(2, DATE_ADD(CURDATE(), INTERVAL 1 DAY) + INTERVAL 14 HOUR),
(2, DATE_ADD(CURDATE(), INTERVAL 1 DAY) + INTERVAL 15 HOUR),
(2, DATE_ADD(CURDATE(), INTERVAL 3 DAY) + INTERVAL 9 HOUR),
(2, DATE_ADD(CURDATE(), INTERVAL 3 DAY) + INTERVAL 10 HOUR),
-- Dr. Leroy (ID 3)
(3, DATE_ADD(CURDATE(), INTERVAL 2 DAY) + INTERVAL 14 HOUR),
(3, DATE_ADD(CURDATE(), INTERVAL 2 DAY) + INTERVAL 15 HOUR),
(3, DATE_ADD(CURDATE(), INTERVAL 4 DAY) + INTERVAL 9 HOUR);

# Système d'Information Cabinet Médical (NFE114)

## 📋 Description

Système de gestion complet pour un cabinet médical développé en PHP vanilla dans le cadre du module NFE114. Le système permet la gestion des patients, praticiens, rendez-vous avec une interface sécurisée pour les patients et les administrateurs.

## 🚀 Fonctionnalités

### Côté Patient
- ✅ Inscription sécurisée avec validation des données
- ✅ Connexion avec authentification par mot de passe haché
- ✅ Tableau de bord personnalisé
- ✅ Visualisation des praticiens et leurs spécialités
- ✅ Prise de rendez-vous en ligne
- ✅ Gestion des rendez-vous (consultation, annulation)
- ✅ Historique complet des consultations

### Côté Administrateur
- ✅ Interface d'administration sécurisée
- ✅ Gestion des praticiens (ajout, modification, suppression)
- ✅ Statistiques du cabinet
- ✅ Visualisation des rendez-vous récents
- ✅ Tableau de bord avec métriques

## 🛠️ Technologies Utilisées

- **Backend** : PHP 7.4+ (vanilla, sans framework)
- **Base de données** : MySQL/MariaDB avec PDO
- **Frontend** : HTML5, CSS3, Bootstrap 5.3
- **Sécurité** : 
  - Hachage des mots de passe avec `password_hash()`
  - Protection CSRF
  - Requêtes préparées (protection injection SQL)
  - Validation et nettoyage des données

## 📁 Structure du Projet

```
NFE114-Systeme_medical/
├── 📄 Pages principales
│   ├── index.php              # Page d'accueil
│   ├── login.php              # Connexion patient/admin
│   ├── register.php           # Inscription patient
│   ├── logout.php             # Déconnexion
│   ├── dashboard.php          # Tableau de bord patient
│   ├── rdv.php                # Prise de rendez-vous
│   ├── mes_rdv.php            # Gestion RDV patient
│   └── admin.php              # Interface administrateur
│
├── 📂 includes/               # Fichiers système
│   ├── config.php             # Configuration BDD
│   ├── functions.php          # Fonctions métier
│   ├── header.php             # En-tête avec styles
│   └── footer.php             # Pied de page
│
├── 📂 assets/                 # Ressources
│   ├── css/
│   │   └── animations.css     # Animations personnalisées
│   └── images/
│       ├── logo.svg           # Logo navbar
│       └── logo-ch-vierzon.png # Logo arrière-plan
│
├── 📂 sql/
│   └── structure.sql          # Structure base de données
│
└── 📄 README.md               # Documentation
```

## 🗄️ Base de Données

### Tables principales

1. **Patient** : Informations des patients inscrits
2. **Praticien** : Données des médecins du cabinet
3. **Disponibilite** : Créneaux disponibles des praticiens
4. **RendezVous** : Rendez-vous pris par les patients
5. **Admin** : Comptes administrateurs

### Relations
- Un patient peut avoir plusieurs rendez-vous
- Un praticien peut avoir plusieurs disponibilités et rendez-vous
- Contraintes d'intégrité référentielle avec CASCADE

## 🔧 Installation

### Prérequis
- Serveur web (Apache/Nginx)
- PHP 7.4 ou supérieur
- MySQL/MariaDB
- phpMyAdmin (recommandé)

### Étapes d'installation

1. **Cloner/télécharger le projet**
   ```bash
   git clone [url-du-projet]
   cd cabinet_medical
   ```

2. **Configurer la base de données**
   - Créer une base de données MySQL
   - Importer le fichier `sql/structure.sql`
   - Ou exécuter le script SQL dans phpMyAdmin

3. **Configurer la connexion**
   - Modifier `includes/config.php`
   - Ajuster les paramètres de connexion BDD :
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'cabinet_medical');
     define('DB_USER', 'votre_utilisateur');
     define('DB_PASS', 'votre_mot_de_passe');
     ```

4. **Déployer sur le serveur web**
   - Copier les fichiers dans le répertoire web
   - S'assurer que PHP a les permissions d'écriture

5. **Tester l'installation**
   - Accéder à `http://localhost/cabinet_medical`
   - Vérifier la connexion à la base de données

## 👤 Comptes de Test

### Administrateur
- **Login** : `admin`
- **Mot de passe** : `admin123`

### Patient
- Créer un compte via la page d'inscription
- Ou utiliser les données de test si disponibles

## 🔒 Sécurité Implémentée

1. **Authentification**
   - Mots de passe hachés avec `password_hash()`
   - Vérification avec `password_verify()`

2. **Protection CSRF**
   - Tokens CSRF sur tous les formulaires
   - Validation côté serveur

3. **Injection SQL**
   - Requêtes préparées PDO
   - Paramètres liés (bind parameters)

4. **Validation des données**
   - Nettoyage avec `htmlspecialchars()`
   - Validation côté client et serveur
   - Filtres de validation PHP

5. **Gestion des sessions**
   - Sessions sécurisées
   - Vérification des droits d'accès
   - Déconnexion automatique

## 📱 Interface Utilisateur

### Design Moderne
- **Thème personnalisé** : Dégradé vert/bleu (#20c997 → #17a2b8)
- **Logo Centre Hospitalier** : Vierzon en arrière-plan
- **Animations fluides** : Effets CSS personnalisés
- **Responsive Design** : Compatible mobile/tablette/desktop

### Fonctionnalités Interface
- **Bootstrap 5** : Interface moderne et intuitive
- **Icons Bootstrap** : Iconographie cohérente
- **Messages Flash** : Retours utilisateur clairs
- **Validation temps réel** : Formulaires interactifs
- **Dropdown optimisé** : Menu de déconnexion centré

## 🧪 Tests Recommandés

1. **Tests fonctionnels**
   - Inscription/connexion patient
   - Prise de rendez-vous
   - Annulation de rendez-vous
   - Gestion praticiens (admin)

2. **Tests de sécurité**
   - Tentatives d'injection SQL
   - Accès non autorisé aux pages
   - Validation des formulaires

3. **Tests d'interface**
   - Responsive design
   - Navigation
   - Messages d'erreur

## 🚀 Améliorations Possibles

- [ ] Système de notifications par email
- [ ] Calendrier interactif pour les RDV
- [ ] Gestion des disponibilités par les praticiens
- [ ] Historique médical des patients
- [ ] Système de rappels automatiques
- [ ] API REST pour applications mobiles
- [ ] Intégration avec des systèmes externes

## 📞 Support

Pour toute question ou problème :
- Consulter la documentation du code
- Vérifier les logs d'erreur PHP
- Tester la connexion à la base de données

## 📄 Licence

Projet étudiant - NFE114
Développé à des fins pédagogiques

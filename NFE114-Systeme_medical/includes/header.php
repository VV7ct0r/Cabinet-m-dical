<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Animations personnalisées -->
    <link href="assets/css/animations.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
            background-image:
                linear-gradient(rgba(248, 249, 250, 0.7), rgba(248, 249, 250, 0.7)),
                url('assets/images/logo-ch-vierzon.png');
            background-repeat: no-repeat;
            background-position: center center;
            background-attachment: fixed;
            background-size: 800px auto;
            min-height: 100vh;
            overflow-x: hidden;
        }



        /* Couleurs personnalisées */
        .text-primary {
            color: #20c997 !important;
        }

        .btn-outline-primary {
            color: #20c997;
            border-color: #20c997;
        }

        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%);
            border-color: #20c997;
            color: white;
        }
        .navbar-brand {
            font-weight: bold;
            display: flex;
            align-items: center;
        }

        #logo-img {
            transition: all 0.3s ease;
        }

        .logo-text {
            margin-left: 10px;
        }

        @media (max-width: 576px) {
            .logo-text {
                display: none;
            }
            #logo-img {
                height: 35px;
            }
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }
        .btn-primary {
            background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%);
            border-color: #20c997;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #1aa085 0%, #138496 100%);
            border-color: #1aa085;
        }
        /* Footer amélioré */
        .footer-enhanced {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 50%, #2c3e50 100%);
            color: white;
            margin-top: 60px;
            position: relative;
            overflow: hidden;
        }

        .footer-enhanced::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('assets/images/logo-watermark.svg') no-repeat center center;
            background-size: 300px 300px;
            opacity: 0.03;
            pointer-events: none;
        }

        .footer-main {
            padding: 50px 0 30px;
            position: relative;
            z-index: 2;
        }



        .footer-section {
            height: 100%;
        }

        .footer-title {
            color: #20c997;
            font-size: 1.5rem;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .footer-subtitle {
            color: #17a2b8;
            font-size: 1.1rem;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .footer-description {
            color: #bdc3c7;
            line-height: 1.6;
            margin-bottom: 20px;
        }



        /* Responsive pour mobile */
        @media (max-width: 767.98px) {


            /* Footer responsive */
            .footer-main {
                padding: 40px 0 20px;
            }

            .footer-section {
                margin-bottom: 30px;
            }



            body {
                background-size: 500px auto;
                background-image:
                    linear-gradient(rgba(248, 249, 250, 0.8), rgba(248, 249, 250, 0.8)),
                    url('assets/images/logo-ch-vierzon.png');
            }
        }





        /* Liens du footer */
        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: 8px;
        }

        .footer-links a {
            color: #bdc3c7;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-links a:hover {
            color: #20c997;
            padding-left: 5px;
        }

        /* Horaires */
        .footer-hours {
            color: #bdc3c7;
        }

        .hour-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .hour-item:last-child {
            border-bottom: none;
        }

        .day {
            font-weight: 500;
        }

        .time {
            color: #20c997;
            font-weight: 600;
        }

        .time.closed {
            color: #e74c3c;
        }

        /* Contact */
        .footer-contact {
            color: #bdc3c7;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 15px;
        }

        .contact-item i {
            color: #20c997;
            font-size: 1.1rem;
            margin-top: 2px;
        }

        .contact-item a {
            color: #bdc3c7;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .contact-item a:hover {
            color: #20c997;
        }



        .rdv-card {
            transition: transform 0.2s;
        }
        .rdv-card:hover {
            transform: translateY(-2px);
        }
        .status-confirme {
            color: #198754;
        }
        .status-annule {
            color: #dc3545;
        }
        .status-termine {
            color: #6c757d;
        }

        /* Correction du dropdown de déconnexion */
        .navbar-nav .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            left: auto;
            min-width: 160px;
            margin-top: 0.5rem;
            border: 1px solid rgba(0,0,0,0.15);
            border-radius: 0.375rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
            z-index: 1000;
        }

        .navbar-nav .dropdown-menu-end {
            --bs-position: end;
        }

        .navbar-nav .dropdown-item {
            padding: 0.5rem 1rem;
            color: #212529;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .navbar-nav .dropdown-item:hover {
            background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%);
            color: white;
        }

        .navbar-nav .dropdown-item i {
            margin-right: 0.5rem;
        }

        /* Éviter le décalage de la page */
        .navbar-nav .dropdown {
            position: static;
        }

        @media (min-width: 992px) {
            .navbar-nav .dropdown {
                position: relative;
            }
        }

        /* Amélioration du bouton dropdown */
        .navbar-nav .dropdown-toggle {
            transition: all 0.3s ease;
        }

        .navbar-nav .dropdown-toggle:hover {
            background-color: rgba(255,255,255,0.1);
            border-radius: 0.375rem;
        }

        .navbar-nav .dropdown-toggle::after {
            margin-left: 0.5rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%);"
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="assets/images/logo.svg" alt="Logo Cabinet Médical" height="40" class="me-2" id="logo-img">
                <span class="logo-text">
                    <i class="bi bi-hospital"></i> Cabinet Médical
                </span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-house"></i> Accueil
                        </a>
                    </li>
                    
                    <?php if (isLoggedIn()): ?>
                        <?php if (isPatient()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="dashboard.php">
                                    <i class="bi bi-speedometer2"></i> Tableau de bord
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="rdv.php">
                                    <i class="bi bi-calendar-plus"></i> Prendre RDV
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="mes_rdv.php">
                                    <i class="bi bi-calendar-check"></i> Mes RDV
                                </a>
                            </li>
                        <?php elseif (isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="admin.php">
                                    <i class="bi bi-gear"></i> Administration
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> 
                                <?php 
                                if (isPatient()) {
                                    echo htmlspecialchars($_SESSION['user_login']);
                                } else {
                                    echo 'Admin';
                                }
                                ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="bi bi-box-arrow-in-right"></i> Connexion
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">
                                <i class="bi bi-person-plus"></i> Inscription
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container mt-4">
        <?php displayFlashMessage(); ?>

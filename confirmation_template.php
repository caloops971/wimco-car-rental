<?php
// Activation des logs d'erreur mais suppression de l'affichage
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Vérifier si une session n'est pas déjà active avant de la démarrer
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Charger la configuration des partenaires
#require_once __DIR__ . '/partners/config.php';

// Récupérer le domaine du partenaire depuis l'URL
$partnerDomain = isset($_GET['partner']) ? $_GET['partner'] : null;
#$partner = getPartnerByDomain($partnerDomain);

// Définir le chemin de base pour les ressources
$basePath = '/';
// Si nous sommes sur un sous-domaine, ajuster le chemin
if (strpos($_SERVER['HTTP_HOST'], '.hertzstbarth.test') !== false || strpos($_SERVER['HTTP_HOST'], '.hertzstbarth.com') !== false) {
    $basePath = '/';
}

if (!$partner) {
    header('Location: ' . $basePath);
    exit;
}

#if (!isset($_SESSION['reservation_success']) || !isset($_SESSION['reservation_details'])) {
#    $protocol = strpos($_SERVER['HTTP_HOST'], '.com') !== false ? 'https' : 'http';
#    $domain = $partner['domain'] . '.hertzstbarth.' . (strpos($_SERVER['HTTP_HOST'], '.com') !== false ? 'com' : 'test');
#    $port = (strpos($_SERVER['HTTP_HOST'], '.test') !== false && strpos($_SERVER['HTTP_HOST'], ':') !== false) ? ':8000' : '';
#    header('Location: ' . $protocol . '://' . $domain . $port);
#    exit;
#}

$reservation = $_SESSION['reservation_details'];
unset($_SESSION['reservation_success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - <?php echo htmlspecialchars($partner['name']); ?></title>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-BYVTS0W0YV"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-BYVTS0W0YV');
    </script>
    <link rel="stylesheet" href="<?php echo $basePath; ?>styles.css">
    <style>
        :root {
            --primary-color: <?php echo $partner['colors']['primary']; ?>;
            --secondary-color: <?php echo $partner['colors']['secondary']; ?>;
        }
    </style>
</head>
<body>
    <div class="hero" style="height: 40vh;">
        <div class="logo-container">
            <?php if ($partner['logo']): ?>
            <img src="<?php echo $basePath; ?>images/logos/<?php echo htmlspecialchars($partner['logo']); ?>" alt="<?php echo htmlspecialchars($partner['name']); ?> Logo" class="partner-logo">
            <?php endif; ?>
        </div>
        <div class="logo-container hertz-logo-container">
            <img src="<?php echo $basePath; ?>images/Hertz-Logo.svg" alt="Hertz Logo" class="hertz-logo">
        </div>
    </div>

    <div class="container">
        <div class="confirmation-content">
            <div class="confirmation-header">
                <i class="fas fa-check-circle"></i>
                <h1>Booking Confirmed!</h1>
                <p class="reservation-number">Reservation number: <?php echo htmlspecialchars($reservation['number']); ?></p>
            </div>

            <div class="reservation-details">
                <div class="vehicle-summary">
                    <?php if (!empty($reservation['vehicleImage'])): ?>
                    <img src="<?php echo htmlspecialchars($reservation['vehicleImage']); ?>" alt="<?php echo htmlspecialchars($reservation['vehicleName']); ?>" class="vehicle-image">
                    <?php endif; ?>
                    <h2><?php echo htmlspecialchars($reservation['vehicleName']); ?></h2>
                </div>

                <div class="details-grid">
                    <div class="detail-item">
                        <i class="far fa-calendar-alt"></i>
                        <div>
                            <h3>Pick-up</h3>
                            <p><?php echo date('d/m/Y H:i', strtotime($reservation['pickupDateTime'])); ?></p>
                        </div>
                    </div>

                    <div class="detail-item">
                        <i class="far fa-calendar-alt"></i>
                        <div>
                            <h3>Return</h3>
                            <p><?php echo date('d/m/Y H:i', strtotime($reservation['dropoffDateTime'])); ?></p>
                        </div>
                    </div>

                    <div class="detail-item">
                        <i class="far fa-user"></i>
                        <div>
                            <h3>Customer</h3>
                            <p><?php echo htmlspecialchars($reservation['customerName']); ?></p>
                        </div>
                    </div>

                    <div class="detail-item">
                        <i class="far fa-envelope"></i>
                        <div>
                            <h3>Email</h3>
                            <p><?php echo htmlspecialchars($reservation['customerEmail']); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="confirmation-details">
                <p>A confirmation email has been sent to your email address with all the details of your reservation.</p>
                <p>Our team will contact you shortly to finalize the details of your rental.</p>
            </div>

            <div class="confirmation-actions">
                <a href="/" class="btn-return">Return to Homepage</a>
            </div>
        </div>
    </div>

    <!-- Ajout de Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</body>
</html> 

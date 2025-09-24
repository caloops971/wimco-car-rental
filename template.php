<?php
require_once __DIR__ . '/config.php';

// Get partner from domain
$domain = $_SERVER['HTTP_HOST'];
#$partner = getPartnerByDomain($domain);

if (!$partner) {
    die('Partner not found');
}

// Load the main configuration
#require_once __DIR__ . '/../config.php';

// Définir le chemin de base pour les ressources
// Pour le serveur de développement PHP, utiliser un chemin simple
if (php_sapi_name() === 'cli-server') {
    $basePath = '/';
} else {
    $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/';
}

error_log("Using base path: " . $basePath);

$vehiculesDisponibles = [];
$dateDebut = '';
$dateFin = '';
$promoCode = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier si les dates sont présentes dans le POST
    if (isset($_POST['date_debut']) && isset($_POST['date_fin'])) {
        $dateDebut = $_POST['date_debut'];
        $dateFin = $_POST['date_fin'];
        $promoCode = isset($_POST['promo_code']) ? $_POST['promo_code'] : '';
        
        // Préparation des données pour l'API
        $data = [
            "PickupLocationId" => 116,
            "DropoffLocationId" => 116,
            "PickupDateTime" => date("m/d/Y h:i:s A", strtotime($dateDebut)),
            "DropoffDateTime" => date("m/d/Y h:i:s A", strtotime($dateFin)),
            "RateCode" => "",
            "CompanyId" => $partner['id'],
            "PromoCode" => $promoCode,
            "AllVehicleTypes" => true
        ];

        // Configuration de la requête
        $options = [
            'http' => [
                'header'  => [
                    "Authorization: Bearer " . RENTCENTRIC_TOKEN,
                    "Content-Type: application/json"
                ],
                'method'  => 'POST',
                'content' => json_encode($data)
            ]
        ];

        try {
            $context = stream_context_create($options);
            $result = @file_get_contents(RENTCENTRIC_API_VEHICLES_URL, false, $context);

            if ($result !== false) {
                $response = json_decode($result, true);
                if ($response && isset($response['Status']) && $response['Status'] === 'OK') {
                    $vehiculesDisponibles = $response['Result'];
                    usort($vehiculesDisponibles, function($a, $b) {
                        return $a['TotalCharges'] <=> $b['TotalCharges'];
                    });
                }
            } else {
                error_log("API request failed: " . error_get_last()['message']);
            }
        } catch (Exception $e) {
            error_log("Error calling API: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Rental St Barth - <?php echo htmlspecialchars($partner['name']); ?> Partner</title>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-BYVTS0W0YV"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-BYVTS0W0YV');
    </script>
    <link rel="stylesheet" href="<?php echo $basePath; ?>styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        :root {
            --primary-color: <?php echo $partner['colors']['primary']; ?>;
            --secondary-color: <?php echo $partner['colors']['secondary']; ?>;
        }

        .modal .form-group {
            width: 100%;
            box-sizing: border-box;
            padding-right: 0;
        }

        .modal .form-group textarea,
        .modal .form-group input {
            width: 100%;
            box-sizing: border-box;
        }

        .form-group textarea {
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
            font-size: 1rem;
            line-height: 1.5;
            height: 100px;
            resize: vertical;
            transition: border-color 0.3s ease;
            background-color: white;
        }

        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.1);
        }

        .form-group textarea::placeholder {
            color: #999;
	}
.flatpickr-calendar {
    background: transparent;
    opacity: 0;
    display: none;
    text-align: center;
    visibility: hidden;
    padding: 0;
    -webkit-animation: none;
    animation: none;
    direction: ltr;
    border: 0;
    font-size: 14px;
    line-height: 24px;
    border-radius: 5px;
    position: absolute;
    width: 337.875px;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    background: #fff;
    -webkit-box-shadow: 1px 0 0 #e6e6e6,-1px 0 0 #e6e6e6,0 1px 0 #e6e6e6,0 -1px 0 #e6e6e6,0 3px 13px rgba(0,0,0,0.08);
    box-shadow: 1px 0 0 #e6e6e6,-1px 0 0 #e6e6e6,0 1px 0 #e6e6e6,0 -1px 0 #e6e6e6,0 3px 13px rgba(0,0,0,0.08)
}
    </style>
</head>
<body>
    <div class="hero">
        <div class="logo-container">
            <?php if ($partner['logo']): ?>
            <img src="<?php echo $basePath; ?>images/logos/<?php echo htmlspecialchars($partner['logo']); ?>" alt="<?php echo htmlspecialchars($partner['name']); ?> Logo" class="partner-logo">
            <?php endif; ?>
        </div>
        <div class="logo-container hertz-logo-container">
            <img src="<?php echo $basePath; ?>images/Hertz-Logo.svg" alt="Hertz Logo" class="hertz-logo">
        </div>
        <div class="hero-content">
            <h1>Premium Car Rental in St Barth</h1>
            <p>Prefered <?php echo htmlspecialchars($partner['name']); ?> Partner</p>
        </div>
    </div>

    <div class="container">
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="search-summary">
                <div class="search-details">
                    <span><i class="far fa-calendar-alt"></i> From <?php echo date('m/d/Y H:i', strtotime($dateDebut)); ?></span>
                    <span><i class="fas fa-arrow-right"></i></span>
                    <span><i class="far fa-calendar-alt"></i> To <?php echo date('m/d/Y H:i', strtotime($dateFin)); ?></span>
                    <?php if (!empty($promoCode)): ?>
                        <span class="promo-badge"><i class="fas fa-tag"></i> Promo code applied</span>
                    <?php endif; ?>
                </div>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn-modify">Modify</a>
            </div>
        <?php else: ?>
            <div class="search-form">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="date_debut">Start Date</label>
                        <input type="text" id="date_debut" name="date_debut" class="datepicker" required>
                        <i class="far fa-calendar-alt"></i>
                    </div>
                    <div class="form-group">
                        <label for="date_fin">End Date</label>
                        <input type="text" id="date_fin" name="date_fin" class="datepicker" required>
                        <i class="far fa-calendar-alt"></i>
                    </div>
                    <div class="form-group">
                        <label for="promo_code">Promo Code</label>
                        <input type="text" id="promo_code" name="promo_code" class="promo-input" placeholder="Optional">
                        <i class="fas fa-tag"></i>
                    </div>
                    <button type="submit" class="btn-search">
                        <i class="fas fa-search"></i>
                        <span>Search</span>
                    </button>
                </form>
            </div>

            <!-- Trust badges -->
            <div class="trust-badges">
                <div class="trust-item">
                    <i class="fas fa-check-circle"></i>
                    <div class="trust-content">
                        <h3>Official <?php echo htmlspecialchars($partner['name']); ?> Partner</h3>
                        <p>Premium service guaranteed</p>
                    </div>
                </div>
                <div class="trust-item">
                    <i class="fas fa-shield-alt"></i>
                    <div class="trust-content">
                        <h3>Secure Booking</h3>
                        <p>Safe and guaranteed payment</p>
                    </div>
                </div>
                <div class="trust-item">
                    <i class="fas fa-clock"></i>
                    <div class="trust-content">
                        <h3>Instant Confirmation</h3>
                        <p>Immediate booking</p>
                    </div>
                </div>
                <div class="trust-item">
                    <i class="fas fa-concierge-bell"></i>
                    <div class="trust-content">
                        <h3>Concierge Service</h3>
                        <p>24/7 assistance</p>
                    </div>
                </div>
            </div>

            <!-- Benefits section -->
            <section class="benefits">
                <h2>Why Choose Our Rental Service?</h2>
                <div class="benefits-container">
                    <div class="benefit-card">
                        <img src="<?php echo $basePath . 'images/' . rawurlencode('premium fleet.jpg'); ?>" alt="Premium Fleet" class="benefit-image">
                        <h3>Premium Fleet</h3>
                        <p>Recent and perfectly maintained vehicles for your comfort and safety.</p>
                    </div>
                    <div class="benefit-card">
                        <img src="<?php echo $basePath . 'images/' . rawurlencode('personalized services.jpg'); ?>" alt="Personalized Service" class="benefit-image">
                        <h3>Personalized Service</h3>
                        <p>A dedicated team at your service to meet all your needs.</p>
                    </div>
                    <div class="benefit-card">
                        <img src="<?php echo $basePath . 'images/' . rawurlencode('Delivery & Return.jpg'); ?>" alt="Delivery & Return" class="benefit-image">
                        <h3>Delivery & Return</h3>
                        <p>Vehicle delivery and pickup service at your villa or at the airport.</p>
                    </div>
                </div>
            </section>

            <!-- Testimonials -->
            <div class="testimonials-section">
                <h2>What Our Customers Say</h2>
                <div class="testimonials-grid">
                    <div class="testimonial-card">
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">"Exceptional service, impeccable vehicle. The delivery to our villa was perfect."</p>
                        <div class="testimonial-author">- John D., USA</div>
                    </div>
                    <div class="testimonial-card">
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">"Simple and quick booking. The team is very professional and attentive."</p>
                        <div class="testimonial-author">- Sarah M., United Kingdom</div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="vehicles-grid" id="vehicles-results">
            <?php if (!empty($vehiculesDisponibles)): ?>
                <?php foreach ($vehiculesDisponibles as $vehicle): ?>
                    <div class="vehicle-card">
                        <div class="vehicle-image-container">
                            <?php
                            $isElectric = strpos(strtolower($vehicle['VehicleTypeInfo']['Name']), 'wuling') !== false ||
                                         strpos(strtolower($vehicle['VehicleTypeInfo']['Name']), 'byd') !== false ||
                                         strpos(strtolower($vehicle['VehicleTypeInfo']['Name']), 'honda') !== false;
                            if ($isElectric):
                            ?>
                                <div class="electric-badge">
                                    <i class="fas fa-charging-station"></i>
                                </div>
                            <?php endif; ?>
                            <img src="<?php echo $vehicle['VehicleTypeInfo']['Image']; ?>" alt="<?php echo $vehicle['VehicleTypeInfo']['Name']; ?>" class="vehicle-image">
                        </div>
                        <div class="vehicle-info">
                            <div class="vehicle-name"><?php echo $vehicle['VehicleTypeInfo']['Name']; ?></div>
                            <div class="vehicle-features">
                                <span><i class="fas fa-users"></i> <?php echo $vehicle['VehicleTypeInfo']['NumberOfSeats']; ?> seats</span>
                                <span><i class="fas fa-suitcase"></i> <?php echo $vehicle['VehicleTypeInfo']['TotalNumberOfBags']; ?> bags</span>
                                <span><i class="fas fa-door-open"></i> <?php echo $vehicle['VehicleTypeInfo']['NumberOfDoors']; ?> doors</span>
                            </div>
                            <div class="vehicle-price">
                                <?php if (!empty($promoCode)): ?>
                                    <div class="price-original">
                                        <del><?php echo $vehicle['RentalRateCharges']; ?>€</del>
                                    </div>
                                <?php endif; ?>
                                <div class="price-total"><?php echo $vehicle['TotalCharges']; ?>€</div>
                                <div class="price-label">Total price for selected duration</div>
                                <div class="price-daily">
                                    <?php
                                    $nombreJours = ceil((strtotime($dateFin) - strtotime($dateDebut)) / (60 * 60 * 24));
                                    $prixJournalierReduit = $vehicle['TotalCharges'] / $nombreJours;
                                    echo '(' . number_format($prixJournalierReduit, 2, '.', ',') . '€ / day)';
                                    ?>
                                </div>
                            </div>
                            <button class="btn-reserve" onclick="openReservationModal('<?php 
                                echo htmlspecialchars(json_encode([
                                    'vehicleId' => $vehicle['VehicleTypeInfo']['VehicleTypeId'],
                                    'vehicleName' => $vehicle['VehicleTypeInfo']['Name'],
                                    'vehicleImage' => $vehicle['VehicleTypeInfo']['Image'],
                                    'rateId' => $vehicle['RateInfo']['RateId'],
                                    'locationId' => 116,
                                    'pickupDateTime' => date("m/d/Y h:i:s A", strtotime($dateDebut)),
                                    'dropoffDateTime' => date("m/d/Y h:i:s A", strtotime($dateFin)),
                                    'promoCode' => $promoCode
                                ])); 
                            ?>')">
                                Book Now
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
                <script>
                    // Défilement vers les résultats une fois qu'ils sont chargés
                    const resultsSection = document.getElementById('vehicles-results');
                    const headerOffset = 100;
                    const elementPosition = resultsSection.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                    
                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                </script>
            <?php else: ?>
                <div class="no-results">
                    <p>Please select your dates to see available vehicles.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Reservation Modal -->
    <div id="reservationModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Book Your Vehicle</h2>
            <p id="vehicleDetails"></p>
            <form id="reservationForm" onsubmit="submitReservation(event)">
                <div class="form-group">
                    <label for="firstName">First Name *</label>
                    <input type="text" id="firstName" name="firstName" required>
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name *</label>
                    <input type="text" id="lastName" name="lastName" required>
                </div>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required>
                    <div id="emailError" class="error-message" style="display: none; color: red; font-size: 0.9em; margin-top: 5px;">
                        Cette adresse email n'est pas autorisée pour les réservations.
                    </div>
                </div>
                <div class="form-group">
                    <label for="phone">Phone *</label>
                    <input type="tel" id="phone" name="phone" required pattern="[+]?[0-9]{8,}" title="Phone number must contain only digits and optional +, minimum 8 characters">
                    <div id="phoneError" class="error-message" style="display: none; color: red; font-size: 0.9em; margin-top: 5px;">
                        Le numéro de téléphone doit contenir uniquement des chiffres et un + optionnel (minimum 8 caractères).
                    </div>
                </div>
                <div class="form-group">
                    <label for="delivery_info">VILLA CODE/NAME - Delivery Request / Info *</label>
                    <textarea id="delivery_info" name="delivery_info" placeholder="Enter your villa code/name and delivery details (minimum 3 characters)" required minlength="3"></textarea>
                    <div id="deliveryError" class="error-message" style="display: none; color: red; font-size: 0.9em; margin-top: 5px;">
                        Ce champ doit contenir au moins 3 caractères.
                    </div>
                </div>
                <input type="hidden" id="reservationData" name="reservationData">
                <p class="form-note">* Required fields</p>
                <button type="submit" class="btn-submit">Confirm Booking</button>
            </form>
        </div>
    </div>

    <!-- Ajout de Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
    // Variables globales pour la modale
    let currentReservationData = null;
    let modal = null;
    let span = null;

    // Fonction globale pour ouvrir la modale
    function openReservationModal(reservationData) {
        try {
            currentReservationData = JSON.parse(reservationData);
            const vehicleDetails = document.getElementById('vehicleDetails');
            
            // Afficher les détails du véhicule
            vehicleDetails.textContent = `Réservation pour : ${currentReservationData.vehicleName}`;
            
            // Afficher la modale
            modal.style.display = 'block';
            
            // Stocker les données de réservation
            document.getElementById('reservationData').value = reservationData;
        } catch (error) {
            console.error('Error parsing reservation data:', error);
            alert('Une erreur est survenue lors de l\'ouverture du formulaire de réservation.');
        }
    }

    // Fonction globale pour soumettre la réservation
    async function submitReservation(event) {
        event.preventDefault();
        
        try {
            // Valider tous les champs avant de continuer
            if (!validateEmail()) {
                alert('Veuillez corriger l\'adresse email avant de continuer.');
                return;
            }
            
            if (!validatePhone()) {
                alert('Veuillez saisir un numéro de téléphone valide (chiffres et + optionnel, minimum 8 caractères).');
                return;
            }
            
            if (!validateDeliveryInfo()) {
                alert('Veuillez saisir au moins 3 caractères pour le code villa/informations de livraison.');
                return;
            }
            
            // Vérifier que nous avons les dates
            if (!currentReservationData || !currentReservationData.pickupDateTime || !currentReservationData.dropoffDateTime) {
                throw new Error('Les dates de réservation sont manquantes');
            }

            const formData = {
                CustomerInfo: {
                    FirstName: document.getElementById('firstName').value,
                    LastName: document.getElementById('lastName').value,
                    Email: document.getElementById('email').value,
                    Phone: document.getElementById('phone').value,
                    Address: "St Barth",
                    StateCode: "BL",
                    CountryCode: "FR",
                    Zip: "97133"
                },
                AdditionalDrivers: [],
                ReservationInfo: {
                    PickupLocationId: currentReservationData.locationId,
                    DropoffLocationId: currentReservationData.locationId,
                    PickupDateTime: currentReservationData.pickupDateTime,
                    DropoffDateTime: currentReservationData.dropoffDateTime,
                    VehicleTypeId: currentReservationData.vehicleId,
                    RateId: currentReservationData.rateId,
                    InsuranceIds: [],
                    TaxIds: [],
                    MiscChargeIds: [],
                    PromoCode: currentReservationData.promoCode || '',
                    CompanyId: <?php echo $partner['id']; ?>,
                    Comment: document.getElementById('delivery_info').value,
                    Airport: null,
                    Airline: null,
                    FlightNumber: null,
                    VehicleId: null,
                    OneWayFee: 0
                },
                Payment: {
                    PaymentMethod: "",
                    Amount: 0
                }
            };

            console.log('Sending reservation data:', formData);

            // Utiliser le chemin absolu complet
            const apiUrl = window.location.protocol + '//' + window.location.host + '/create_reservation.php';
            console.log('API URL:', apiUrl);

            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    formData: formData,
                    vehicleDetails: currentReservationData
                })
            });

            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);

            const result = await response.text();
            console.log('Raw response:', result);

            let jsonResult;
            try {
                jsonResult = JSON.parse(result);
                console.log('Parsed response:', jsonResult);
            } catch (parseError) {
                console.error('JSON parsing error:', parseError);
                console.error('Raw response that failed to parse:', result);
                throw new Error('Format de réponse invalide du serveur');
            }

            if (jsonResult.Status === 'OK') {
                // Utiliser le chemin absolu pour la redirection
                window.location.href = window.location.protocol + '//' + window.location.host + '/confirmation.php?partner=<?php echo $partner['domain']; ?>';
            } else {
                let errorMessage = jsonResult.ErrorMessage || 'Erreur inconnue';
                if (jsonResult.Debug) {
                    console.error('Debug info:', jsonResult.Debug);
                }
                throw new Error(errorMessage);
            }
        } catch (error) {
            console.error('Error during reservation:', error);
            alert('Erreur lors de la réservation : ' + error.message);
        }
    }

    // Fonction pour fermer la modale
    function closeModal() {
        modal.style.display = 'none';
        document.getElementById('reservationForm').reset();
    }

    // Fonction pour valider l'email
    function validateEmail() {
        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('emailError');
        
        if (!emailInput || !emailError) return true;
        
        const email = emailInput.value.toLowerCase();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const forbiddenDomains = ['@wimco', '@stbarth'];
        
        // Vérifier le format email
        if (!emailRegex.test(email)) {
            emailError.textContent = 'Veuillez saisir une adresse email valide.';
            emailError.style.display = 'block';
            emailInput.style.borderColor = 'red';
            return false;
        }
        
        // Vérifier les domaines interdits
        const isInvalid = forbiddenDomains.some(domain => email.includes(domain));
        
        if (isInvalid) {
            emailError.textContent = 'Cette adresse email n\'est pas autorisée pour les réservations.';
            emailError.style.display = 'block';
            emailInput.style.borderColor = 'red';
            return false;
        } else {
            emailError.style.display = 'none';
            emailInput.style.borderColor = '';
            return true;
        }
    }

    // Fonction pour valider le champ delivery_info
    function validateDeliveryInfo() {
        const deliveryInput = document.getElementById('delivery_info');
        const deliveryError = document.getElementById('deliveryError');
        
        if (!deliveryInput || !deliveryError) return true;
        
        const value = deliveryInput.value.trim();
        
        if (value.length < 3) {
            deliveryError.style.display = 'block';
            deliveryInput.style.borderColor = 'red';
            return false;
        } else {
            deliveryError.style.display = 'none';
            deliveryInput.style.borderColor = '';
            return true;
        }
    }

    // Fonction pour valider le téléphone
    function validatePhone() {
        const phoneInput = document.getElementById('phone');
        const phoneError = document.getElementById('phoneError');
        
        if (!phoneInput || !phoneError) return true;
        
        const phone = phoneInput.value.trim();
        // Regex pour valider : optionnel +, suivi de chiffres, minimum 8 caractères au total
        const phoneRegex = /^[+]?[0-9]{8,}$/;
        
        if (!phoneRegex.test(phone)) {
            phoneError.style.display = 'block';
            phoneInput.style.borderColor = 'red';
            return false;
        } else {
            phoneError.style.display = 'none';
            phoneInput.style.borderColor = '';
            return true;
        }
    }

    // Fonction pour valider tout le formulaire
    function validateForm() {
        const emailValid = validateEmail();
        const phoneValid = validatePhone();
        const deliveryValid = validateDeliveryInfo();
        const submitButton = document.querySelector('#reservationForm .btn-submit');
        
        if (!submitButton) return;
        
        if (emailValid && phoneValid && deliveryValid) {
            submitButton.disabled = false;
            submitButton.style.opacity = '1';
        } else {
            submitButton.disabled = true;
            submitButton.style.opacity = '0.5';
        }
    }

    // Initialisation au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des éléments de la modale
        modal = document.getElementById('reservationModal');
        span = document.getElementsByClassName('close')[0];

        // Configuration des datepickers
        const dateDebutElement = document.getElementById("date_debut");
        const dateFinElement = document.getElementById("date_fin");

        let dateDebutPicker = null;
        let dateFinPicker = null;

        if (dateDebutElement) {
            dateDebutPicker = flatpickr("#date_debut", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                minDate: "today",
                time_24hr: true,
                locale: "en",
                onChange: function(selectedDates, dateStr) {
                    if (dateFinPicker) {
                        dateFinPicker.set("minDate", dateStr);
                        if (dateFinPicker.selectedDates[0] && dateFinPicker.selectedDates[0] < selectedDates[0]) {
                            dateFinPicker.clear();
                        }
                    }
                }
            });
        }

        if (dateFinElement) {
            dateFinPicker = flatpickr("#date_fin", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                minDate: "today",
                time_24hr: true,
                locale: "en"
            });
        }

        function validateDates() {
            const submitBtn = document.querySelector('.btn-search');
            if (!submitBtn || !dateDebutPicker || !dateFinPicker) return;

            const dateDebut = dateDebutPicker.selectedDates[0];
            const dateFin = dateFinPicker.selectedDates[0];
            
            if (dateDebut && dateFin && dateFin > dateDebut) {
                submitBtn.disabled = false;
                submitBtn.style.opacity = "1";
            } else {
                submitBtn.disabled = true;
                submitBtn.style.opacity = "0.5";
            }
        }

        if (dateDebutPicker && dateFinPicker) {
            dateDebutPicker.config.onChange.push(validateDates);
            dateFinPicker.config.onChange.push(validateDates);
            validateDates();
        }

        // Configuration de la validation email
        const emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.addEventListener('input', validateForm);
            emailInput.addEventListener('blur', validateForm);
        }

        // Configuration de la validation phone
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', validateForm);
            phoneInput.addEventListener('blur', validateForm);
        }

        // Configuration de la validation delivery_info
        const deliveryInput = document.getElementById('delivery_info');
        if (deliveryInput) {
            deliveryInput.addEventListener('input', validateForm);
            deliveryInput.addEventListener('blur', validateForm);
        }

        // Configuration des événements de la modale
        if (span) {
            span.onclick = closeModal;
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        };
    });
    </script>
</body>
</html> 

<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once 'config.php';

$vehiculesDisponibles = [];
$dateDebut = '';
$dateFin = '';
$promoCode = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['date_debut']) && isset($_POST['date_fin'])) {
        $dateDebut = $_POST['date_debut'];
        $dateFin = $_POST['date_fin'];
        $promoCode = isset($_POST['promo_code']) ? $_POST['promo_code'] : '';
        
        // Préparation des données pour l'API
        $data = [
            'PickupLocationId' => 116,
            'DropoffLocationId' => 116,
            'PickupDateTime' => date('m/d/Y h:i:s A', strtotime($dateDebut)),
            'DropoffDateTime' => date('m/d/Y h:i:s A', strtotime($dateFin)),
            'RateCode' => '',
            'CompanyId' => $partner['id'],
            'PromoCode' => $promoCode,
            'AllVehicleTypes' => true
        ];

        // Configuration de la requête
        $options = [
            'http' => [
                'header'  => [
                    'Authorization: Bearer ' . RENTCENTRIC_TOKEN,
                    'Content-Type: application/json'
                ],
                'method'  => 'POST',
                'content' => json_encode($data)
            ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents(RENTCENTRIC_API_VEHICLES_URL, false, $context);

        if ($result !== false) {
            $response = json_decode($result, true);
            if ($response['Status'] === 'OK') {
                $vehiculesDisponibles = $response['Result'];
                usort($vehiculesDisponibles, function($a, $b) {
                    return $a['TotalCharges'] <=> $b['TotalCharges'];
                });
            }
        }
    }
}

include 'template.php';
?>
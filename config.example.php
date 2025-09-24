<?php
// Configuration de l'API - COPIEZ CE FICHIER VERS config.php ET REMPLISSEZ VOS VRAIES VALEURS
define('RENTCENTRIC_TOKEN', 'YOUR_RENTCENTRIC_API_TOKEN_HERE');
define('RENTCENTRIC_API_VEHICLES_URL', 'https://www8.rentcentric.com/RcOnlineAPI_Client6042/api/VehicleType/GetAvailableVehicleTypesAndRates');

// Configuration du partenaire
$partner = array (
  'id' => 108,
  'name' => 'Wimco',
  'domain' => 'wimco.hertzstbarth.com',
  'logo' => 'wimco properties.png',
  'colors' => 
  array (
    'primary' => '#002349',
    'secondary' => '#c4a052',
  ),
);

// Fonction pour formater le nom du partenaire
function formatPartnerName($name) {
    $name = strtolower($name);
    $words = explode(' ', $name);
    $words = array_map('ucfirst', $words);
    return implode(' ', $words);
}

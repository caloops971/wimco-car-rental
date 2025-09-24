<?php
// Configuration de l'API
define('RENTCENTRIC_TOKEN', 'uSrAECOA2Pb5BXVGVo3uVdWeTvrhjW3x9ZNa_-CsGSw8B7XGfHVyn9hBQqQwACyhkFgpYbAOJQnSXm4pd1124sqV-CnhuEDlYPD2vrkfs4knuw5RRr6Q0KwLo5jgNHXvUPSKh2MYhBLLhHnnAMkan8RzPme-ytWNEyZ02LMVV-y5bm8mpUZ9Jp8_wcFUx7AGgQ8zEFTxzEzzixmBg2XaxsEzuRv5cOYeK43Tydp2NySGF4fQC6BIaB9d8VlFTnLnn5dGbMH2gs3vOXZwzgL1YEFOr9zZDLINAxwbxsWYu3dVCHbsW70iv2NagSYp_GjZ');
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

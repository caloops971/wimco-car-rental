<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

require_once 'config.php';

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

if ($data === null) {
    http_response_code(400);
    echo json_encode([
        'Status' => 'Error',
        'ErrorMessage' => 'Invalid JSON data'
    ]);
    exit;
}

try {
    $formData = $data['formData'];
    $options = [
        'http' => [
            'header' => [
                'Authorization: Bearer ' . RENTCENTRIC_TOKEN,
                'Content-Type: application/json'
            ],
            'method' => 'POST',
            'content' => json_encode($formData)
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents('https://www8.rentcentric.com/RcOnlineAPI_Client6042/api/Reservation/CreateReservation', false, $context);
    
    $jsonResponse = json_decode($result, true);
    
    if ($jsonResponse['Status'] === 'OK') {
        $_SESSION['reservation_success'] = true;
        $_SESSION['reservation_details'] = [
            'number' => $jsonResponse['Result']['ReservationNumber'],
            'vehicleName' => $data['vehicleDetails']['vehicleName'],
            'vehicleImage' => $data['vehicleDetails']['vehicleImage'],
            'pickupDateTime' => $formData['ReservationInfo']['PickupDateTime'],
            'dropoffDateTime' => $formData['ReservationInfo']['DropoffDateTime'],
            'customerName' => $formData['CustomerInfo']['FirstName'] . ' ' . $formData['CustomerInfo']['LastName'],
            'customerEmail' => $formData['CustomerInfo']['Email']
        ];
    }
    
    echo json_encode($jsonResponse);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'Status' => 'Error',
        'ErrorMessage' => $e->getMessage()
    ]);
}
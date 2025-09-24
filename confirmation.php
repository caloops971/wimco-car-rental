<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

if (!isset($_SESSION['reservation_success']) || !isset($_SESSION['reservation_details'])) {
    header('Location: /');
    exit;
}

$reservation = $_SESSION['reservation_details'];
unset($_SESSION['reservation_success']);

include 'confirmation_template.php';
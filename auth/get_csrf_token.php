<?php
require_once '../includes/functions.php';
startSession();

header('Content-Type: application/json');

// Generate and return CSRF token
$token = generateCSRFToken();

echo json_encode([
    'success' => true,
    'token' => $token
]);

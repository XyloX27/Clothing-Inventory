<?php
require_once 'db_config.php';
session_start();

// Validate session and request method
if(!isset($_SESSION['loggedin']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized access']));
}

try {
    // Validate inputs
    $orderId = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
    $newStatus = filter_input(INPUT_POST, 'new_status', FILTER_SANITIZE_STRING);
    
    if(!$orderId || !in_array($newStatus, ['pending', 'processing', 'completed', 'canceled'])) {
        throw new Exception('Invalid input parameters');
    }

    // Update database
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $orderId]);
    
    // Return success response
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success', 
        'new_status' => $newStatus,
        'badge_class' => getBadgeClass($newStatus)
    ]);

} catch(Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error', 
        'message' => $e->getMessage()
    ]);
}

function getBadgeClass($status) {
    $statusMap = [
        'pending' => 'bg-secondary',
        'processing' => 'bg-primary',
        'completed' => 'bg-success',
        'canceled' => 'bg-danger'
    ];
    return $statusMap[strtolower($status)] ?? 'bg-secondary';
}
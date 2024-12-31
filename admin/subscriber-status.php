<?php
require_once('inc/config.php');

// Check if ID and status are provided
if(!isset($_REQUEST['id']) || !isset($_REQUEST['status'])) {
    header('location: subscriber.php');
    exit;
}

// Get and sanitize parameters
$id = intval($_REQUEST['id']);
$status = intval($_REQUEST['status']);

// Only allow 0 or 1 for status
if($status != 0 && $status != 1) {
    $_SESSION['error_message'] = 'Invalid status value.';
    header('location: subscriber.php');
    exit;
}

try {
    // Update subscriber status
    $statement = $pdo->prepare("UPDATE tbl_subscriber SET subs_active = ? WHERE subs_id = ?");
    $statement->execute([$status, $id]);

    // Set appropriate message
    if($status == 1) {
        $_SESSION['success_message'] = 'Subscriber has been activated successfully.';
    } else {
        $_SESSION['success_message'] = 'Subscriber has been deactivated successfully.';
    }
    
} catch(PDOException $e) {
    $_SESSION['error_message'] = 'Error updating subscriber status: ' . $e->getMessage();
}

// Redirect back to subscriber page
header('location: subscriber.php');
exit;
?> 
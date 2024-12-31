<?php
require_once('inc/config.php');

// Check if ID is provided
if(!isset($_REQUEST['id'])) {
	header('location: subscriber.php');
	exit;
}

// Get ID and sanitize it
$id = intval($_REQUEST['id']);

// Delete the subscriber
try {
	$statement = $pdo->prepare("DELETE FROM tbl_subscriber WHERE subs_id=?");
	$statement->execute(array($id));

	// Set success message and redirect
	$_SESSION['success_message'] = 'Subscriber has been deleted successfully.';
	header('location: subscriber.php');
	exit;
	
} catch(PDOException $e) {
	// Set error message and redirect
	$_SESSION['error_message'] = 'Error deleting subscriber: ' . $e->getMessage();
	header('location: subscriber.php');
	exit;
}
?>
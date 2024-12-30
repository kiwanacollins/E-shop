<?php require_once('header.php'); ?>

<?php
// Preventing the direct access of this page.
if(!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
} 

// Initialize arrays
$ecat_ids = array();
$p_ids = array();

// First get all end category IDs that belong to this top category through mid categories
$statement = $pdo->prepare("SELECT * 
							FROM tbl_top_category t1
							JOIN tbl_mid_category t2
							ON t1.tcat_id = t2.tcat_id
							JOIN tbl_end_category t3
							ON t2.mcat_id = t3.mcat_id
							WHERE t1.tcat_id=?");
$statement->execute(array($_REQUEST['id']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
foreach ($result as $row) {
	$ecat_ids[] = $row['ecat_id'];
}

// Then get all products that belong to these end categories
if(!empty($ecat_ids)) {
	foreach($ecat_ids as $ecat_id) {
		$statement = $pdo->prepare("SELECT * FROM tbl_product WHERE ecat_id=?");
		$statement->execute(array($ecat_id));
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		foreach ($result as $row) {
			$p_ids[] = $row['p_id'];
		}
	}
}

// Delete products and their related data
if(!empty($p_ids)) {
	foreach($p_ids as $p_id) {
		// Delete product photos
		$statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
		$statement->execute(array($p_id));
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		foreach ($result as $row) {
			if($row['p_featured_photo'] != '') {
				unlink('../assets/uploads/'.$row['p_featured_photo']);
			}
		}

		// Delete from tbl_product_photo
		$statement = $pdo->prepare("SELECT * FROM tbl_product_photo WHERE p_id=?");
		$statement->execute(array($p_id));
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		foreach ($result as $row) {
			if($row['photo'] != '') {
				unlink('../assets/uploads/product_photos/'.$row['photo']);
			}
		}
		$statement = $pdo->prepare("DELETE FROM tbl_product_photo WHERE p_id=?");
		$statement->execute(array($p_id));

		// Delete from other product-related tables
		$statement = $pdo->prepare("DELETE FROM tbl_product_size WHERE p_id=?");
		$statement->execute(array($p_id));

		$statement = $pdo->prepare("DELETE FROM tbl_product_color WHERE p_id=?");
		$statement->execute(array($p_id));

		$statement = $pdo->prepare("DELETE FROM tbl_product WHERE p_id=?");
		$statement->execute(array($p_id));
	}
}

// Delete end categories
if(!empty($ecat_ids)) {
	foreach($ecat_ids as $ecat_id) {
		$statement = $pdo->prepare("DELETE FROM tbl_end_category WHERE ecat_id=?");
		$statement->execute(array($ecat_id));
	}
}

// Delete mid categories
$statement = $pdo->prepare("DELETE FROM tbl_mid_category WHERE tcat_id=?");
$statement->execute(array($_REQUEST['id']));

// Finally delete the top category
$statement = $pdo->prepare("DELETE FROM tbl_top_category WHERE tcat_id=?");
$statement->execute(array($_REQUEST['id']));

header('location: top-category.php');
?>
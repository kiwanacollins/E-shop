<?php require_once('header.php'); ?>
<?php require_once('config/db.php'); // Adjust path if necessary ?>

<?php
// Sanitize GET parameters
$email = isset($_GET['email']) ? filter_var($_GET['email'], FILTER_SANITIZE_EMAIL) : '';
$key = isset($_GET['key']) ? filter_var($_GET['key'], FILTER_SANITIZE_STRING) : '';

if ($email && $key) {
    try {
        // Check if the subscriber exists and is inactive
        $statement = $pdo->prepare("SELECT * FROM tbl_subscriber WHERE subs_email = ? AND subs_hash = ? AND subs_active = 0");
        $statement->execute([$email, $key]);

        if ($statement->rowCount() > 0) {
            // Activate the subscriber
            $update = $pdo->prepare("UPDATE tbl_subscriber SET subs_active = 1 WHERE subs_email = ? AND subs_hash = ?");
            $update->execute([$email, $key]);

            $success_message = '<p style="color:green;">Your subscription has been confirmed successfully!</p>';
        } else {
            $error_message = '<p style="color:red;">Invalid verification link or subscription already confirmed.</p>';
        }
    } catch(PDOException $e) {
        $error_message = '<p style="color:red;">Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
} else {
    $error_message = '<p style="color:red;">Invalid verification link.</p>';
}
?>

<div class="page-banner" style="background-color:#444;">
    <div class="inner">
        <h1>Registration Successful</h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="user-content">
                    <?php 
                        echo $error_message;
                        echo $success_message;
                    ?>
                </div>                
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>
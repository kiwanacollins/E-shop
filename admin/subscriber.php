<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('inc/config.php');
require_once('header.php');

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// Display Session Messages if any
if(isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']);
}
if(isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
    unset($_SESSION['error_message']);
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Subscriber</h1>
	</div>
	<div class="content-header-right">
		<a href="subscriber-remove.php" class="btn btn-primary btn-sm">Remove Pending Subscribers</a>
		<a href="subscriber-csv.php" class="btn btn-primary btn-sm">Export as CSV</a>
	</div>
</section>


<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-info">        
        <div class="box-body table-responsive">
          <table id="example1" class="table table-bordered table-striped">
			<thead>
			    <tr>
			        <th>SL</th>
			        <th>Subscriber Email</th>
			        <th>Action</th>
			    </tr>
			</thead>
            <tbody>
            	<?php
            	$i=0;
            	$statement = $pdo->prepare("SELECT * FROM tbl_subscriber ORDER BY subs_date_time DESC");
            	$statement->execute();
            	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
            	foreach ($result as $row) {
            		$i++;
            		?>
					<tr>
	                    <td><?php echo $i; ?></td>
	                    <td><?php echo $row['subs_email']; ?></td>
	                    <td>
	                        <?php if($row['subs_active'] == 1): ?>
	                            <span class="badge badge-success">Active</span>
	                            <a href="subscriber-status.php?id=<?php echo $row['subs_id']; ?>&status=0" 
                                class="btn btn-warning btn-xs">Deactivate</a>
	                        <?php else: ?>
	                            <span class="badge badge-warning">Inactive</span>
	                            <a href="subscriber-status.php?id=<?php echo $row['subs_id']; ?>&status=1" 
                                class="btn btn-success btn-xs">Activate</a>
	                        <?php endif; ?>
	                        <button type="button" class="btn btn-danger btn-xs" 
	                            data-href="subscriber-delete.php?id=<?php echo $row['subs_id']; ?>" 
	                            data-toggle="modal" 
	                            data-target="#confirm-delete">
	                            Delete
	                        </button>
	                    </td>
	                </tr>
            		<?php
            	}
            	?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
        <div id="message-container">
            <!-- Messages will be displayed here -->
        </div>
    </div>
  </div>
</section>


<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this subscriber?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok">Delete</a>
            </div>
        </div>
    </div>
</div>

<!-- Make sure jQuery and Bootstrap JS are loaded -->
<script src="js/jquery-2.2.4.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    // For delete functionality
    $('#confirm-delete').on('show.bs.modal', function(e) {
        var href = $(e.relatedTarget).data('href');
        $(this).find('.btn-ok').attr('href', href);
    });

    // Add click handler for delete button
    $('.btn-ok').click(function(e) {
        e.preventDefault();
        var deleteUrl = $(this).attr('href');
        window.location.href = deleteUrl;
    });
});
</script>

<?php
if(isset($_POST['email_subscribe'])) {
    $error_message = '';
    $success_message = '';
    
    // Initialize variables
    $current_date = date("Y-m-d");
    $current_date_time = date("Y-m-d H:i:s");
    $key = bin2hex(random_bytes(16));

    // Sanitize and validate email
    $email = filter_var($_POST['email_subscribe'], FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        try {
            // Check if email already exists
            $check = $pdo->prepare("SELECT * FROM tbl_subscriber WHERE subs_email = ?");
            $check->execute([$email]);
            if($check->rowCount() > 0){
                $error_message = "Email is already subscribed.";
            } else {
                // Insert subscriber as active by default
                $statement = $pdo->prepare("INSERT INTO tbl_subscriber (subs_email, subs_date, subs_date_time, subs_hash, subs_active) VALUES (?, ?, ?, ?, ?)");
                $statement->execute([
                    $email,
                    $current_date,
                    $current_date_time,
                    $key,
                    1  // Set as active by default
                ]);

                // Send welcome email
                $mail = new PHPMailer(true);
                try {
                    $mail->isHTML(true);
                    $mail->Subject = 'Welcome to Our Newsletter';
                    $mail->Body = 'Thank you for subscribing to our newsletter! You have been automatically subscribed and will receive our latest updates.';

                    // Uncomment and configure these lines in production
                    // $mail->addAddress($email);
                    // $mail->send();
                    
                    $success_message = "Subscription successful! You are now subscribed to our newsletter.";
                    
                    // Redirect to refresh the subscriber list
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                } catch (Exception $e) {
                    $error_message = "Subscription successful, but welcome email could not be sent.";
                }
            }
        } catch(PDOException $e) {
            $error_message = "Database Error: " . htmlspecialchars($e->getMessage());
        }
    }

    // Display messages
    if ($error_message) {
        echo '<div class="alert alert-danger">' . $error_message . '</div>';
    }
    if ($success_message) {
        echo '<div class="alert alert-success">' . $success_message . '</div>';
    }
}
?>
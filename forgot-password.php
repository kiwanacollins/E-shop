<?php require_once('header.php'); ?>

<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(isset($_POST['form1'])) {
    $valid = 1;

    if(empty($_POST['cust_email'])) {
        $valid = 0;
        $error_message .= "Email address can not be empty<br>";
    } else {
        if (filter_var($_POST['cust_email'], FILTER_VALIDATE_EMAIL) === false) {
            $valid = 0;
            $error_message .= 'Email address must be valid<br>';
        } else {
            $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_email=?");
            $statement->execute(array($_POST['cust_email']));
            $total = $statement->rowCount();                            
            if(!$total) {
                $valid = 0;
                $error_message .= 'Email address does not exist<br>';
            }
        }
    }

    if($valid == 1) {
        $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_email=?");
        $statement->execute(array($_POST['cust_email']));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);                           
        foreach ($result as $row) {
            $cust_id = $row['cust_id'];
        }

        $token = md5(rand());

        // Update the customer with the token
        $statement = $pdo->prepare("UPDATE tbl_customer SET cust_token=? WHERE cust_id=?");
        $statement->execute(array($token,$cust_id));
        
        // Send reset link to the email
        $reset_link = BASE_URL . 'reset-password.php?email=' . $_POST['cust_email'] . '&token=' . $token;
        
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'collinzcalson@gmail.com'; // Your Gmail address
            $mail->Password = 'mdxx dwwu cqdx upst';    // Your provided app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            //Recipients
            $mail->setFrom('collinzcalson@gmail.com', 'E-Shop Password Reset');
            $mail->addAddress($_POST['cust_email']);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = '
            <p>To reset your password, please click the following link:</p>
            <p><a href="'.$reset_link.'">'.$reset_link.'</a></p>
            <p>This link will expire in 24 hours for security reasons.</p>
            <p>If you did not request this password reset, please ignore this email.</p>
            ';

            $mail->send();
            $success_message = 'A password reset link has been sent to your email address. Please check your inbox.';
        } catch (Exception $e) {
            $error_message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
?>

<div class="page-banner">
    <div class="inner">
        <h1>Forgot Password</h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="user-content">
                    <form action="" method="post">
                        <?php $csrf->echoInputField(); ?>
                        <div class="row">
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <?php
                                if($error_message != '') {
                                    echo "<div class='error' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>".$error_message."</div>";
                                }
                                if($success_message != '') {
                                    echo "<div class='success' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>".$success_message."</div>";
                                }
                                ?>
                                <div class="form-group">
                                    <label for="">Email Address *</label>
                                    <input type="email" class="form-control" name="cust_email">
                                </div>
                                <div class="form-group">
                                    <label for=""></label>
                                    <input type="submit" class="btn btn-primary" value="Submit" name="form1">
                                </div>
                                <a href="login.php" style="color:#e4144d;">Back to Login Page</a>
                            </div>
                        </div>                        
                    </form>
                </div>                
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?> 
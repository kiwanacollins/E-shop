<?php
require_once('header.php');
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendConfirmationEmail($customerEmail, $customerName) {
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'collinzcalson@gmail.com';
        $mail->Password = 'mdxx dwwu cqdx upst';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        //Recipients
        $mail->setFrom('collinzcalson@gmail.com', 'E-Shop');
        $mail->addAddress($customerEmail, $customerName);

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to E-Shop - Registration Confirmed';
        $mail->Body = '
        <h2>Welcome to E-Shop!</h2>
        <p>Dear ' . $customerName . ',</p>
        <p>Thank you for registering with E-Shop. Your account has been successfully created and is ready to use.</p>
        <p>You can now:</p>
        <ul>
            <li>Browse our products</li>
            <li>Add items to your cart</li>
            <li>Track your orders</li>
            <li>Update your profile</li>
        </ul>
        <p>If you have any questions or need assistance, please don\'t hesitate to contact us.</p>
        <p>Best regards,<br>E-Shop Team</p>
        ';

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}
?> 
<?php
require_once('header.php');
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $mail = new PHPMailer(true);

    //Enable debug mode
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->Debugoutput = 'html';
    
    //Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'collinzcalson@gmail.com';
    $mail->Password = 'mdxx dwwu cqdx upst';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    //Set timeout
    $mail->Timeout = 60;
    $mail->SMTPKeepAlive = true;

    //Recipients
    $mail->setFrom('collinzcalson@gmail.com', 'E-Shop Test');
    $mail->addAddress('collinzcalson@gmail.com', 'Test User'); // Send to yourself for testing

    //Content
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Subject = 'Test Email from E-Shop';
    $mail->Body = '
    <h2>This is a test email</h2>
    <p>If you receive this email, the SMTP configuration is working correctly.</p>
    ';

    echo '<pre>Attempting to send email...</pre>';
    $mail->send();
    echo '<pre>Email sent successfully!</pre>';
} catch (Exception $e) {
    echo '<pre>Message could not be sent. Mailer Error: ' . $mail->ErrorInfo . '</pre>';
}
?> 
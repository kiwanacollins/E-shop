

<?php

// Error Reporting Turn On
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1); // Ensure errors are displayed

// Setting up the time zone
date_default_timezone_set('Africa/Kampala');

// Host Name
$dbhost = 'localhost';

// Database Name
$dbname = 'fashiony_ogs';

// Database Username
$dbuser = 'root';

// Database Password
$dbpass = '';

// Defining base url
define("BASE_URL", "");
//http://fashionys.com/

// Getting Admin url
define("ADMIN_URL", BASE_URL . "admin" . "/");

try {
    // echo "Connecting to the database...<br>"; // Debugging message
    $pdo = new PDO("mysql:host={$dbhost};dbname={$dbname}", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connection successful!<br>"; // Debugging message
} catch (PDOException $exception) {
    echo "Connection error: " . $exception->getMessage();
}
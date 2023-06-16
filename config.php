<?php


ini_set('date.timezone','Africa/Nairobi');
date_default_timezone_set('Africa/Nairobi');

session_start();

require_once('initialize.php');
require_once('private/classes/dbconn.php');

$db = new DBConnection;
$conn = $db->conn;

function redirect_to_login() {
    $login_url = '/login';

    // Check if the current URL is already the login page
    if ($_SERVER['REQUEST_URI'] !== $login_url) {
        // Redirect to the login page
        header('Location: ' . $login_url);
        exit;
    }
}

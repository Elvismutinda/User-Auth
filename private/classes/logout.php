<?php

require_once("../../config.php");

class Logout
{
    public function logout()
    {
        // Clear session data
        session_start();
        session_unset();
        session_destroy();

        // Redirect to the login page
        header('Location: ../../index.php');
        exit;
    }
}


$logout = new Logout();
$logout->logout();

?>
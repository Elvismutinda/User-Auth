<?php

require_once("../../config.php");

// Verify the user's email based on the verification token
if(isset($_GET['token'])){
    $verificationToken = $_GET['token'];

    $conn = new DBConnection();
    $dbConnection = $conn->conn;

    // Check if the verification token exists in the database
    $stmt = $dbConnection->prepare("SELECT * FROM users WHERE verification_token = ?");
    $stmt->bind_param("s", $verificationToken);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $user = $result->fetch_assoc();
        $email = $user['email'];

        // Update the user's account as verified
        $stmt = $dbConnection->prepare("UPDATE users SET verified = 1 WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->close();

        // Delete the verification token from the database
        $stmt = $dbConnection->prepare("UPDATE users SET verification_token = NULL WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->close();

        // Display a success message to the user
        echo "<script>alert('Your account has been verified successfully. You can now login!');
        window.location='../..';</script>";
    }else{
        // Display an error message to the user
        echo "<script>alert('Invalid verification token!');
        window.location='../..';</script>";
    }
}

?>

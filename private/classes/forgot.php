<?php

require_once("../../config.php");
require_once("../../vendor/autoload.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class ForgotPassword
{
    private $email;
    private $dbConnection;

    public function __construct($email, $dbConnection)
    {
        $this->email = $email;
        $this->dbConnection = $dbConnection;
    }

    public function generateResetCode()
    {
        // Retrieve the DBConnection object from the class property
        $conn = $this->dbConnection->conn;

        // Sanitize and validate email
        $sanitizedEmail = filter_var($this->email, FILTER_SANITIZE_EMAIL);
        if (!filter_var($sanitizedEmail, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Generate a random reset code
        $resetCode = bin2hex(random_bytes(16));

        // Store the reset code in the database for the user
        $stmt = $conn->prepare("UPDATE users SET reset_code = ? WHERE email = ?");
        $stmt->bind_param("ss", $resetCode, $sanitizedEmail);
        $stmt->execute();
        $stmt->close();

        // Send the reset code to the user's email
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'your-smtp-host';  // Set your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@example.com';  // Set your email address
        $mail->Password = 'your-email-password';  // Set your email password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->setFrom('your-email@example.com', 'Your Name');  // Set the "from" email address and name
        $mail->addAddress($sanitizedEmail);  // Set the recipient email address
        $mail->Subject = 'Password Reset Code';
        $mail->Body = "Your password reset code is: $resetCode";

        if ($mail->send()) {
            return true;
        } else {
            return false;
        }
    }
}

// Usage example
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $dbConnection = new DBConnection(); // Create DBConnection object

    $forgotPassword = new ForgotPassword($email, $dbConnection);
    if ($forgotPassword->generateResetCode()) {
        echo "Reset code sent successfully!";
    } else {
        echo "Failed to send reset code.";
    }
}

?>

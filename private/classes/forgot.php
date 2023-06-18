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
        $resetCode = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store the reset code in the database for the user
        $stmt = $conn->prepare("UPDATE users SET reset_code = ? WHERE email = ?");
        $stmt->bind_param("ss", $resetCode, $sanitizedEmail);
        $stmt->execute();
        $stmt->close();

        // Send the reset code to the user's email
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Set your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'elvismutinda2@gmail.com';  // Set your email address
        $mail->Password = 'wkhpkegpcgnrtdep';  // Set your email password (app password that is)
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->setFrom('elvismutinda2@gmail.com', 'Elvis');  // Set the "from" email address and name
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

// forgot pass
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $dbConnection = new DBConnection();

    $forgotPassword = new ForgotPassword($email, $dbConnection);
    if ($forgotPassword->generateResetCode()) {
        echo "Reset code sent successfully!";
    } else {
        echo "Failed to send reset code.";
    }
}

?>

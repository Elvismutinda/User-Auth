<?php

require_once("../../config.php");
require_once("../../vendor/autoload.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class ForgotPassword
{
    private $email;
    private $dbConnection;
    private $recaptchaSecretKey;

    public function __construct($email, $dbConnection, $recaptchaSecretKey)
    {
        $this->email = $email;
        $this->dbConnection = $dbConnection;
        $this->recaptchaSecretKey = $recaptchaSecretKey;
    }

    public function generateResetCode()
    {
        // Retrieve the DBConnection object from the class property
        $conn = $this->dbConnection->conn;
        $timeSent = time();

        // Sanitize and validate email
        $sanitizedEmail = filter_var($this->email, FILTER_SANITIZE_EMAIL);
        if (!filter_var($sanitizedEmail, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Check if the email has exceeded the rate limit for password reset attempts
        if ($this->exceededRateLimit($sanitizedEmail)) {
            return false;
        }

        // Verify CAPTCHA
        if (!$this->verifyCaptcha()) {
            return false;
        }

        // Generate a random reset code
        $resetCode = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store the reset code and its expiration time in the database for the user
        $expirationTime = time() + 3600; // Set token expiration time to 1 hour
        $stmt = $conn->prepare("UPDATE users SET reset_code = ?, reset_code_expiration = ? WHERE email = ?");
        $stmt->bind_param("sis", $resetCode, $expirationTime, $sanitizedEmail);
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
            // Insert the reset attempt into the password_reset_attempts table
            $stmt = $conn->prepare("INSERT INTO password_reset_attempts (email, timestamp) VALUES (?, ?)");
            $stmt->bind_param("si", $sanitizedEmail, $timeSent);
            $stmt->execute();
            $stmt->close();

            return true;
        } else {
            return false;
        }
    }

    private function exceededRateLimit($emailAddress)
    {
        // Implement your rate limiting logic here
        // Example: Check if the email has exceeded the limit of 3 password reset attempts within the past 24 hours
        $conn = $this->dbConnection->conn;
        $currentTime = time();
        $limit = 3;
        $timePeriod = 24 * 60 * 60; // 24 hours
        $timeResult = $currentTime - $timePeriod;

        $stmt = $conn->prepare("SELECT COUNT(*) FROM password_reset_attempts WHERE email = ? AND timestamp >= ?");
        $stmt->bind_param("si", $emailAddress, $timeResult);
        $stmt->execute();
        $stmt->bind_result($attemptCount);
        $stmt->fetch();
        $stmt->close();

        return $attemptCount >= $limit;
    }

    private function verifyCaptcha()
    {
        if (isset($_POST['g-recaptcha-response'])) {
            $recaptchaResponse = $_POST['g-recaptcha-response'];
            $recaptchaSecretKey = $this->recaptchaSecretKey;

            // Send a POST request to the reCAPTCHA API to verify the user's response
            $recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';
            $recaptchaData = [
                'secret' => $recaptchaSecretKey,
                'response' => $recaptchaResponse
            ];

            $options = [
                'http' => [
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => http_build_query($recaptchaData)
                ]
            ];

            $context = stream_context_create($options);
            $recaptchaResult = file_get_contents($recaptchaUrl, false, $context);

            if ($recaptchaResult !== false) {
                $recaptchaResultData = json_decode($recaptchaResult, true);

                if (isset($recaptchaResultData['success']) && $recaptchaResultData['success']) {
                    return true;
                }
            }
        }

        return false;
    }
}

// forgot pass
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $recaptchaSecretKey = '6LeMF6smAAAAAO-VoNi-hky9Ii9m62UNjmcI1ez7'; // Replace with your reCAPTCHA secret key

    $dbConnection = new DBConnection();

    $forgotPassword = new ForgotPassword($email, $dbConnection, $recaptchaSecretKey);
    if ($forgotPassword->generateResetCode()) {
        echo "Reset code sent successfully!";
    } else {
        echo "Failed to send reset code.";
    }
}
?>

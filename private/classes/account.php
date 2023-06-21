<?php

require_once("../../config.php");
require_once("../../vendor/autoload.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Account
{
    private $name;
    private $email;
    private $password;
    private $dbConnection; // Store DBConnection object as a class property

    public function __construct($name, $email, $password, $dbConnection){
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->dbConnection = $dbConnection;
    }

    public function createAccount(){
        // Retrieve the DBConnection object from the class property
        $conn = $this->dbConnection->conn;

        // Generate a CSRF token and store it in the session
        $csrfToken = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $csrfToken;

        $sanitizedName = $conn->real_escape_string($this->name);
        // sanitize and validate email before using it
        $sanitizedEmail = filter_var($this->email, FILTER_SANITIZE_EMAIL);
        // hash the password
        $hashedPass = password_hash($this->password, PASSWORD_BCRYPT);

        // check if email is already taken
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $sanitizedEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        // check if a row was returned
        if($result->num_rows > 0){
            // email already exists
            $stmt->close();
            return false;
        }

        // Generate a unique verification token
        $verificationToken = bin2hex(random_bytes(32));

        // store details in database
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, verification_token) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $sanitizedName, $sanitizedEmail, $hashedPass, $verificationToken);
        $stmt->execute();
        // account creation successful
        $stmt->close();

        // Send verification email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'elvismutinda2@gmail.com';
        $mail->Password = 'wkhpkegpcgnrtdep';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->setFrom('elvismutinda2@gmail.com', 'Elvis');
        $mail->addAddress($this->email, $this->name);
        $mail->isHTML(true);
        $mail->Subject = 'Account Verification';
        $mail->Body = 'Please click the following link to verify your account: <a href="http://localhost/userAuth/private/classes/verify.php?token='.urlencode($verificationToken).'">Verify Account</a>';
        
        if($mail->send()){
            return true;
        }else{
            // If there is an error while sending the email, you can handle it here, e.g, display a message to the user
            return false;
        }

        // Generate a new CSRF token after creating the account
        $csrfToken = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $csrfToken;

        return true;
    }
}

// create account
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Check if the CSRF token matches the one stored in the session
    if(isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']){
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';

        // Validate user inputs
        if(empty($name) || empty($email) || empty($password)){
            echo "<script>alert('Failed to create account');
            window.location='../../index.php'</script>";
            exit;
        }

        // Validate email format
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            echo "<script>alert('Invalid email format');
            window.location='../../index.php'</script>";
            exit;
        }

        // Validate password strength
        if(strlen($password) < 8){
            echo "<script>alert('Password must be at least 8 characters');
            window.location='../../index.php'</script>";
            exit;
        }
        
        if(!preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[0-9]/", $password)){
            echo "<script>alert('Password must contain at least one uppercase letter, one lowercase letter, and one number');
            window.location='../../index.php'</script>";
            exit;
        }

        $dbConnection = new DBConnection(); // Create DBConnection object

        $account = new Account($name, $email, $password, $dbConnection);
        if($account->createAccount()){
            echo "<script>alert('Account created successfully. Please check your email for verification');
            window.location='../../index.php'</script>";
            exit;
        }else{
            echo "<script>alert('Failed to create account');
            window.location='../../index.php'</script>";
        }
    }else{
        echo "<script>alert('Attack detected');
        window.location='../../index.php'</script>"; // CSRF Validation Failed
    }
}

?>

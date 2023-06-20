<?php

require_once("../../config.php");

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

        // store details in database
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $sanitizedName, $sanitizedEmail, $hashedPass);
        $stmt->execute();
        // account creation successful
        $stmt->close();

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
            document.location='../../index.php'</script>";
            exit;
        }

        // Validate email format
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            echo "<script>alert('Invalid email format');
            document.location='../../index.php'</script>";
            exit;
        }

        // Validate password strength
        if(strlen($password) < 8){
            echo "<script>alert('Password must be at least 8 characters');
            document.location='../../index.php'</script>";
            exit;
        }
        
        if(!preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[0-9]/", $password)){
            echo "<script>alert('Password must contain at least one uppercase letter, one lowercase letter, and one number');
            document.location='../../index.php'</script>";
            exit;
        }

        $dbConnection = new DBConnection(); // Create DBConnection object

        $account = new Account($name, $email, $password, $dbConnection);
        if($account->createAccount()){
            header('Location: ../../index.php');
            exit;
        }else{
            echo "<script>alert('Failed to create account');
            document.location='../../index.php'</script>";
        }
    }else{
        echo "<script>alert('Attack detected');
        document.location='../../index.php'</script>"; // CSRF Validation Failed
    }
}

?>

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
        return true;
    }
}

// create account
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if(empty($name) || empty($email) || empty($password)){
        echo "Failed to create account";
        exit;
    }

    // Validate email format
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        echo "Invalid email format";
        exit;
    }

    // Validate password strength
    if(strlen($password) < 8){
        echo "Password must be at least 8 characters";
        exit;
    }
    
    if(!preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[0-9]/", $password)){
        echo "Password must contain at least one uppercase letter, one lowercase letter, and one number";
        exit;
    }

    $dbConnection = new DBConnection(); // Create DBConnection object

    $account = new Account($name, $email, $password, $dbConnection);
    if($account->createAccount()){
        // $dbConnection->conn->close(); // Close the DBConnection
        header('Location: ../../index.php');
        exit;
    }else{
        // $dbConnection->conn->close(); // Close the DBConnection
        echo "Failed to create account";
    }
}

?>

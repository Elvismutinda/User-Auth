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

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $dbConnection = new DBConnection(); // Create DBConnection object

    $account = new Account($name, $email, $password, $dbConnection);
    if($account->createAccount()){
        $dbConnection->conn->close(); // Close the DBConnection
        header('Location: ../../index.php');
        exit;
    }else{
        $dbConnection->conn->close(); // Close the DBConnection
        echo "Failed to create account";
    }
}

?>

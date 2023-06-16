<?php

require_once("../../config.php");

class Login
{
    private $email;
    private $password;
    private $dbConnection; // Store DBConnection object as a class property

    public function __construct($email, $password, $dbConnection){
        $this->email = $email;
        $this->password = $password;
        $this->dbConnection = $dbConnection;
    }

    public function authenticate(){
        // Retrieve the DBConnection object from the class property
        $conn = $this->dbConnection->conn;

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        if(!$stmt){
            $conn->close();
            return false;
        }

        // sanitize and validate email
        $sanitizedEmail = filter_var($this->email, FILTER_SANITIZE_EMAIL);

        $stmt->bind_param("s", $sanitizedEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows === 1){
            $row = $result->fetch_assoc();

            if(password_verify($this->password, $row['password'])){
                // authentication successful
                $stmt->close();
                return true;
            }
        }
        // authentication failed
        $stmt->close();
        return false;
    }
}

// login
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $dbConnection = new DBConnection(); // Create DBConnection object
    
    $login = new Login($email, $password, $dbConnection);
    if($login->authenticate()){
        $dbConnection->conn->close(); // Close the DBConnection
        header('Location: ../user/index.php');
        exit;
    }else{
        $dbConnection->conn->close(); // Close the DBConnection
        echo "Login failed";
    }
}

?>

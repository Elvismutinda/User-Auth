<?php

require_once("../../config.php");

class Login
{
    private $email;
    private $password;
    private $dbConnection;

    public function __construct($email, $password, $dbConnection)
    {
        $this->email = $email;
        $this->password = $password;
        $this->dbConnection = $dbConnection;
    }

    public function authenticate()
    {
        $conn = $this->dbConnection->conn;

        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        if (!$stmt) {
            $conn->close();
            return false;
        }

        $sanitizedEmail = filter_var($this->email, FILTER_SANITIZE_EMAIL);
        $stmt->bind_param("s", $sanitizedEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            if ($row['locked'] == 1) {
                // Account is locked
                $lockTimestamp = $row['last_failed_login'];

                if ($lockTimestamp !== null) {
                    $lockDuration = 5 * 60; // 5 minutes in seconds
                    $currentTime = time();
                    $lockExpiration = $lockTimestamp + $lockDuration;

                    if ($currentTime < $lockExpiration) {
                        // Account is still locked
                        $stmt->close();
                        echo "<script>alert('Account locked. Please try again later.');
                        document.location='../../index.php'</script>";
                        return false;
                    } else {
                        // Unlock the account since the lockout period has expired
                        $stmt = $conn->prepare("UPDATE users SET login_attempts = 0, locked = 0, last_failed_login = NULL WHERE id = ?");
                        $stmt->bind_param("i", $row['id']);
                        $stmt->execute(); 
                    }
                }
            }

            if ($row['login_attempts'] >= 5) {
                // Account locked due to too many failed attempts
                $stmt->close();
                return false;
            }

            if (password_verify($this->password, $row['password'])) {
                // Reset failed login attempts upon successful login
                $this->resetFailedLoginAttempts($row['id']);

                $stmt->close();
                return true;
            } else {
                // Increment failed login attempts
                $this->incrementFailedLoginAttempts($row['id']);
            }
        }
        
        // authentication failed
        $stmt->close();
        return false;
    }

    private function incrementFailedLoginAttempts($userId)
    {
        $conn = $this->dbConnection->conn;

        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("UPDATE users SET login_attempts = login_attempts + 1 WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        // Lock the account if login attempts exceed 5
        $this->checkAccountLockout($userId);

        $stmt->close();
    }

    private function resetFailedLoginAttempts($userId)
    {
        $conn = $this->dbConnection->conn;

        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("UPDATE users SET login_attempts = 0 WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();
    }

    private function checkAccountLockout($userId)
    {
        $conn = $this->dbConnection->conn;

        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT login_attempts FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            if ($row['login_attempts'] >= 5) {
                // Lock the account
                $stmt = $conn->prepare("UPDATE users SET locked = 1, last_failed_login = ? WHERE id = ?");
                $lockTimestamp = time();
                $stmt->bind_param("ii", $lockTimestamp, $userId);
                $stmt->execute();
            }
        }

        $stmt->close();
    }
}

// login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $dbConnection = new DBConnection();
    $login = new Login($email, $password, $dbConnection);
    
    if ($login->authenticate()) {
        // $dbConnection->conn->close();
        header('Location: ../user/index.php');
        exit;
    } else {
        echo "<script>alert('Login failed');
        document.location='../../index.php'</script>";
    }
}
?>

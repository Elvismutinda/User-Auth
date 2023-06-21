<?php

require_once("../../config.php");

class ResetPassword
{
    private $email;
    private $resetCode;
    private $newPassword;
    private $dbConnection;

    public function __construct($email, $resetCode, $newPassword, $dbConnection)
    {
        $this->email = $email;
        $this->resetCode = $resetCode;
        $this->newPassword = $newPassword;
        $this->dbConnection = $dbConnection;
    }

    public function reset()
    {
        $conn = $this->dbConnection->conn;

        // Sanitize and validate email
        $sanitizedEmail = filter_var($this->email, FILTER_SANITIZE_EMAIL);
        if (!filter_var($sanitizedEmail, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND reset_code = ? AND reset_code_expiration >= ?");
        if (!$stmt) {
            $conn->close();
            return false;
        }

        $currentTime = time();
        $stmt->bind_param("ssi", $sanitizedEmail, $this->resetCode, $currentTime);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $userId = $row['id'];
            $hashedPassword = password_hash($this->newPassword, PASSWORD_DEFAULT);

            // Update the user's password and reset the reset code and expiration time
            $stmt = $conn->prepare("UPDATE users SET password = ?, reset_code = NULL, reset_code_expiration = NULL WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $userId);
            $stmt->execute();
            $stmt->close();

            return true;
        }

        // Password reset failed
        $stmt->close();
        return false;
    }
}

// reset password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $resetCode = $_POST['reset_code'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate the new password
    if ($newPassword !== $confirmPassword) {
        echo "<script>alert('New passwords do not match!');
        window.location='../..'</script>";
        exit;
    }

    $dbConnection = new DBConnection();
    $resetPassword = new ResetPassword($email, $resetCode, $newPassword, $dbConnection);

    if ($resetPassword->reset()) {
        echo "<script>alert('Password reset successful!');
        window.location='../..'</script>";
    } else {
        echo "<script>alert('Failed to reset password!');
        window.location='../..'</script>";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
</head>
<body>
    <h2>Password Reset</h2>
    <form action="reset.php" method="POST">
        <input type="text" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>">
        <input type="text" name="reset_code" value="<?php echo htmlspecialchars($_GET['reset_code']); ?>">
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" id="new_password" required>
        <br>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" required>
        <br>
        <input type="submit" value="Reset Password">
    </form>
</body>
</html>

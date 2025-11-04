<?php
require_once '../includes/functions.php';
startSession();

// Get database connection
$conn = getDBConnection();

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    if (empty($email)) {
        $_SESSION["message"] = "<div class='error-msg'><i class='fas fa-exclamation-triangle'></i> Please enter your email address.</div>";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION["message"] = "<div class='error-msg'><i class='fas fa-envelope'></i> Invalid email format.</div>";
    }
    else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Email exists - in a real application, send a password reset email here
            $_SESSION["message"] = "<div class='success-msg'><i class='fas fa-check-circle'></i> If this email is registered, you will receive password reset instructions.</div>";

            // TODO: Implement email sending functionality
            // For now, just show a success message
        } else {
            // Don't reveal if email doesn't exist for security reasons
            $_SESSION["message"] = "<div class='success-msg'><i class='fas fa-check-circle'></i> If this email is registered, you will receive password reset instructions.</div>";
        }
        $stmt->close();
    }

    header("Location: forgot_password.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Petcare Pro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="../styles/auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-form">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fas fa-paw"></i>
                    <span>Petcare Pro</span>
                </div>
                <h2>Forgot Password?</h2>
                <p>Enter your email to reset your password</p>
            </div>

            <?php
                if (isset($_SESSION["message"])) {
                    echo '<div class="message">' . $_SESSION["message"] . '</div>';
                    unset($_SESSION["message"]);
                }
            ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" id="email" name="email"
                           placeholder="Enter your registered email" required>
                </div>

                <button type="submit" class="btn">
                    <i class="fas fa-paper-plane"></i> Send Reset Link
                </button>

                <div class="link-container">
                    <a href="login.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
                    <a href="register.php"><i class="fas fa-user-plus"></i> Create Account</a>
                </div>
            </form>

            <div class="divider">
                <span>OR</span>
            </div>

            <div class="back-home">
                <a href="../index.php">
                    <i class="fas fa-home"></i> Back to Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>

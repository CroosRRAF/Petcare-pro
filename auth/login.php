<?php
require_once '../includes/functions.php';
startSession();

// Redirect if already logged in
if (isLoggedIn()) {
    $redirect_url = isAdmin() ? '../admin/index.php' : '../user/dashboard.php';
    redirect($redirect_url);
}

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error_message = "Invalid security token. Please try again.";
    } else {
        $username_or_email = sanitize($_POST['username_or_email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username_or_email) || empty($password)) {
            $error_message = "Please fill in all fields.";
        } else {
            $conn = getDBConnection();

            // Check for user with username or email
            $stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username_or_email, $username_or_email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if ($user) {
                // Check if password is hashed or plain text (legacy support)
                if (password_verify($password, $user['password'])) {
                    $password_valid = true;
                } elseif ($password === $user['password']) {
                    // Legacy plain-text password - hash it now
                    $new_hash = hashPassword($password);
                    $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $update_stmt->bind_param("si", $new_hash, $user['id']);
                    $update_stmt->execute();
                    $update_stmt->close();
                    $password_valid = true;
                } else {
                    $password_valid = false;
                }

                if ($password_valid) {
                    // Set session data
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];

                    // Update cart count in session
                    $_SESSION['cart_count'] = getCartCount();

                    // Redirect based on role
                    if ($user['role'] === 'admin') {
                        redirect('../admin/dashboard.php');
                    } else {
                        redirect('../user/dashboard.php');
                    }
                } else {
                    $error_message = "Invalid email/username or password.";
                }
            } else {
                $error_message = "Invalid email/username or password.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Zyora PetCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="../styles/auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-form">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fas fa-paw"></i>
                    <span>Zyora PetCare</span>
                </div>
                <h2>Welcome Back!</h2>
                <p>Login to access your account</p>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="message">
                    <div class='error-msg'><i class='fas fa-exclamation-circle'></i> <?= sanitize($error_message) ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <?= getCSRFInput() ?>

                <div class="form-group">
                    <label for="username_or_email"><i class="fas fa-user"></i> Email or Username</label>
                    <input type="text" id="username_or_email" name="username_or_email"
                           placeholder="Enter your email or username"
                           value="<?= sanitize($_POST['username_or_email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password"
                           placeholder="Enter your password" required>
                </div>

                <button type="submit" class="btn">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>

                <div class="link-container">
                    <a href="forgot_password.php"><i class="fas fa-key"></i> Forgot Password?</a>
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

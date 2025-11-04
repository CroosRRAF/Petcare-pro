<?php
require_once '../includes/functions.php';
startSession();

// Redirect if already logged in
if (isLoggedIn()) {
    $redirect_url = isAdmin() ? '../admin/index.php' : '../user/dashboard.php';
    redirect($redirect_url);
}

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signup"])) {
    $username = sanitize($_POST["username"] ?? '');
    $email = sanitize($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';
    $confirm_password = $_POST["confirm_password"] ?? '';

    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
    }
    elseif (!validateEmail($email)) {
        $error_message = "Invalid email format.";
    }
    elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $error_message = "Username must be 3-20 characters and can only contain letters, numbers, and underscores.";
    }
    elseif (!validatePassword($password)) {
        $error_message = "Password must be at least 8 characters with uppercase, lowercase, and numbers.";
    }
    elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    }
    else {
        $conn = getDBConnection();

        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT username, email FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Username or Email is already registered.";
        } else {
            $hashed_password = hashPassword($password);

            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, 'user', NOW())");
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                setFlash('success', 'Registration successful! You can now login with your credentials.');
                redirect('login.php');
            } else {
                $error_message = "Error registering user. Please try again.";
                logError("Registration failed for user: $username, Email: $email");
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Petcare Pro</title>
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
                <h2>Create Account</h2>
                <p>Join our pet care community today!</p>
            </div>

            <?php displayFlashes(); ?>

            <?php if (!empty($error_message)): ?>
                <div class="message">
                    <div class='error-msg'><i class='fas fa-exclamation-circle'></i> <?= sanitize($error_message) ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Username</label>
                    <input type="text" id="username" name="username"
                           placeholder="Choose a username (3-20 characters)"
                           pattern="[a-zA-Z0-9_]{3,20}"
                           value="<?= sanitize($_POST['username'] ?? '') ?>"
                           title="Username must be 3-20 characters and can only contain letters, numbers, and underscores"
                           required>
                </div>

                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email"
                           placeholder="Enter your email address"
                           value="<?= sanitize($_POST['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password"
                           placeholder="Min 8 chars with uppercase, lowercase & numbers"
                           minlength="8" required>
                    <div class="password-strength" id="password-strength">
                        <div class="password-strength-bar" id="strength-bar"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password"
                           placeholder="Confirm your password" required>
                </div>

                <button type="submit" name="signup" class="btn">
                    <i class="fas fa-user-plus"></i> Register
                </button>

                <div class="link-container">
                    <a href="login.php"><i class="fas fa-sign-in-alt"></i> Already have an account?</a>
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

    <script>
        // Password strength indicator
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strength-bar');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;

            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            strengthBar.className = 'password-strength-bar';

            if (strength <= 2) {
                strengthBar.classList.add('strength-weak');
            } else if (strength <= 4) {
                strengthBar.classList.add('strength-medium');
            } else {
                strengthBar.classList.add('strength-strong');
            }
        });

        // Confirm password validation
        const confirmPasswordInput = document.getElementById('confirm_password');
        const form = document.querySelector('form');

        form.addEventListener('submit', function(e) {
            if (passwordInput.value !== confirmPasswordInput.value) {
                e.preventDefault();
                alert('Passwords do not match!');
                confirmPasswordInput.focus();
            }
        });
    </script>
</body>
</html>

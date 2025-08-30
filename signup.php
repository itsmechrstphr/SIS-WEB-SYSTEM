<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $role = 'student'; // Default role for signup

    if ($password !== $confirm_password) {
        header('Location: signup.php?error=Passwords do not match');
        exit();
    }

    // Check if username or email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
    $stmt->execute(['username' => $username, 'email' => $email]);
    if ($stmt->fetch()) {
        header('Location: signup.php?error=Username or email already exists');
        exit();
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, first_name, last_name) VALUES (:username, :email, :password, :role, :first_name, :last_name)");
    $stmt->execute([
        'username' => $username,
        'email' => $email,
        'password' => $password_hash,
        'role' => $role,
        'first_name' => $first_name,
        'last_name' => $last_name
    ]);

    header('Location: index.php?success=Account created successfully. Please login.');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Information System - Sign Up</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="signup-container">
            <div class="signUp-header">
                <div class="logo">
                <img src="assets/images/logo.png" alt="Edu.Konek Logo">
                <h1>Edu.Konek</h1>
                </div>
                <p>Create Account</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <form action="signup.php" method="POST" class="signup-form">
                <div class="form-group">
                    <input type="text" id="first_name" name="first_name" placeholder="First Name" required>
                </div>

                <div class="form-group">
                    <input type="text" id="last_name" name="last_name" placeholder="Last Name" required>
                </div>

                <div class="form-group">
                    <input type="text" id="username" name="username" placeholder="Username" required>
                </div>

                <div class="form-group">
                    <input type="email" id="email" name="email" placeholder="Email" required>
                </div>

                <div class="form-group">
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>

                <div class="form-group">
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                </div>

                <button type="submit" class="btn btn-primary">Sign Up</button>
            </form>

            <div class="login-link">
                <p>Already have an account? <a href="index.php">Login here</a></p>
            </div>
        </div>
    </div>
</body>
</html>

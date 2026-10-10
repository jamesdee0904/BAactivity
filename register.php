<?php
include 'db.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $contact = trim($_POST['contact_number'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name)) {
        $errors['name'] = "Please enter your full name.";
    } elseif (strlen($name) < 7 || strlen($name) > 30) {
        $errors['name'] = "Full name must be between 7 and 30 characters.";
    }

    if (empty($contact)) {
        $errors['contact'] = "Please enter your contact number.";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $contact)) {
        $errors['contact'] = "Contact number must contain only numbers and be 10-15 digits long.";
    }

    if (empty($email)) {
        $errors['email'] = "Please enter a email address";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address";
    }

    if (empty($password)) {
        $errors['password'] = "Please enter your password.";
    } elseif (strlen($password) < 9) {
        $errors['password'] = "Password must be at least 9 characters.";
    }

    if (empty($confirm_password)) {
        $errors['confirm'] = "Please confirm your password.";
    } elseif ($confirm_password !== $password) {
        $errors['confirm'] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, contact_number, email, password) VALUES (?, ?, ?, ?)");
        try {
            $stmt->execute([$name, $contact, $email, $hashed_password]);
            $success = "Account created successfully! <a href='login.php'>Login here</a>";
        } catch (PDOException $e) {
            $errors['db'] = "Registration failed: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="./css/register.css">
</head>
<body>
    <main class="auth-container">
        <section class="auth-card register-card">
            <div class="brand">
                <h1>REGISTER</h1>
            </div>
            <?php if (!empty($success)): ?>
                <p style="color: #00f0ff; text-align: center;"><?php echo $success; ?></p>
            <?php endif; ?>
            <form action="register.php" method="post">
                <div class="name-row">
                    <div class="form-group">
                        <label for="full-name">Full Name</label>
                        <input type="text" id="full-name" name="name" placeholder="Full Name" value="<?php echo htmlspecialchars($name ?? ''); ?>">
                        <span class="error-message"><?php echo $errors['name'] ?? ''; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="contact-number">Contact Number</label>
                        <input type="text" id="contact-number" name="contact_number" placeholder="Contact Number" value="<?php echo htmlspecialchars($contact ?? ''); ?>">
                        <span class="error-message"><?php echo $errors['contact'] ?? ''; ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="register-email">Email Address</label>
                    <input type="text" id="register-email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($email ?? ''); ?>">
                    <span class="error-message"><?php echo $errors['email'] ?? ''; ?></span>
                </div>
                <div class="form-group">
                    <label for="register-password">Password</label>
                    <input type="password" id="register-password" name="password" placeholder="Create a password">
                    <span class="error-message"><?php echo $errors['password'] ?? ''; ?></span>
                </div>
                <div class="form-group">
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm your password">
                    <span class="error-message"><?php echo $errors['confirm'] ?? ''; ?></span>
                </div>
                <button type="submit" class="btn">Create Account</button>
            </form>
            <p class="account-text" style="margin-top: 20px;">
                Already have an account? <a href="login.php">Login here</a>
            </p>
        </section>
    </main>
</body>
</html>
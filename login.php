<?php
session_start();

$validEmail = "user@gmail.com";
$validPassword = "user123";

if (!isset($_SESSION['remainingAttempts'])) {
    $_SESSION['remainingAttempts'] = 5;
}

$errors = [];
$errorMessage = "";
$email = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($_SESSION['remainingAttempts'] <= 0) {
        $errorMessage = "Account locked due to too many failed attempts.";
    } else {
        if (empty($email)) {
            $errors['email'] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Please enter a valid email address.";
        }

        if (empty($password)) {
            $errors['password'] = "Password is required.";
        }

        if (empty($errors)) {
            if ($email === $validEmail && $password === $validPassword) {
                $_SESSION['user'] = $email;
                unset($_SESSION['remainingAttempts']);

                header("Location: index.php");
                exit();
            } else {
                $_SESSION['remainingAttempts']--;

                if ($_SESSION['remainingAttempts'] > 0) {
                    $errorMessage = "Invalid credentials. Attempts remaining: "
                        . $_SESSION['remainingAttempts'];
                } else {
                    $errorMessage = "Account locked due to too many failed attempts.";
                }
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
    <title>Login Form</title>
    <link rel="stylesheet" href="./css/login.css">
</head>
<body>
    <div class="login-container">
        <h2>LOGIN</h2>

        <?php if (!empty($errorMessage)): ?>
            <div class="error-message" style="margin-bottom: 15px;">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="input-group">
                <label for="email">Email</label>
                <input
                    type="text"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    value="<?= htmlspecialchars($email) ?>"
                    <?= $_SESSION['remainingAttempts'] <= 0 ? 'disabled' : '' ?>
                >

                <?php if (!empty($errors['email'])): ?>
                    <span class="email-error" style="display:block; text-align:left;">
                        <?= htmlspecialchars($errors['email']) ?>
                    </span>
                <?php endif; ?>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    <?= $_SESSION['remainingAttempts'] <= 0 ? 'disabled' : '' ?>
                >

                <?php if (!empty($errors['password'])): ?>
                    <span class="password-error" style="display:block; text-align:left;">
                        <?= htmlspecialchars($errors['password']) ?>
                    </span>
                <?php endif; ?>
            </div>

            <button
                type="submit"
                class="login-btn"
                <?= $_SESSION['remainingAttempts'] <= 0 ? 'disabled' : '' ?>
            >
                Login
            </button>
        </form>

        <div class="signup">
            Don't have an account?
            <a href="register.php">Register</a>
        </div>
    </div>
</body>
</html>
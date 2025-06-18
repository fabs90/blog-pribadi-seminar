<?php
require_once 'config/connection.php';
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}


if (isset($_POST['login'])) {
    // Ambil data dari form login
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query 4
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username AND email = :email");
    $stmt->execute(['username' => $username, 'email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            // Password mismatch
            $messageError = "Invalid username, email or password!";
            echo $messageError;
        }
    } else {
        // Login failed
        $messageError = "Invalid username, email or password!";
        echo $messageError;
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
    <div class="container">
        <div class="login-form">
            <h1>Login</h1>
            <a href="index.php">Back to Index</a>

            <form action="login.php" method="post">
                <div>
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div>
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div>
                    <button type="submit" name="login" class="button-login">login</button>
                </div>
            </form>
        </div>

    </div>

</body>
<style>
.login-form {
    display: flex;
    flex-direction: column;
    align-items: center;
    max-width: 400px;
    margin: 0 auto;
    padding: 20px;
    border: 1px solid black;
    border-radius: 5px;
}

.register-form form div {
    margin-bottom: 15px;
}
</style>

</html>
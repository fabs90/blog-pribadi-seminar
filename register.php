<?php
require_once 'config/connection.php';
$messageSuccess = "";
$messageError = "";


if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $checkEmailStmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $checkEmailStmt->execute(['email' => $email]);
    $checkEmailStmt->fetch(PDO::FETCH_ASSOC);

    if ($checkEmailStmt->rowCount() > 0) {
        $messageError = "Email already exists!";
    } else {
        // Query
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT)
        ]);
        if ($stmt) {
            $messageSuccess = "Registration successful!";
        } else {
            $messageError = "Registration failed!";
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
</head>

<body>
    <div class="container">
        <div class="register-form">
            <h1>Register</h1>
            <a href="index.php">Back to Index</a>
            <?php
            if ($messageError != ""):
                ?>
                <h3 style="color: red;"><?= $messageError ?></h3>
                <?php
            elseif ($messageSuccess != ""):
                ?>
                <h3 style="color: green;"><?= $messageSuccess ?></h3>
                <?php
            endif;
            ?>
            <form action="" method="post">
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
                    <button type="submit" name="register" class="button-register">Register</button>
                </div>
            </form>
        </div>

    </div>

</body>
<style>
    .register-form {
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
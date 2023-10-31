<?php
    // Initialize session
    session_start();

    // Include database connection
    include('dbconfig.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Fetch hashed password from database
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            unset($user['password']);
            $_SESSION['user'] = $user;
            header('Location: index.php');
        } else {
            echo "Invalid credentials";
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - TTTExtreme</title>
    <link rel="stylesheet" type="text/css" href="./css/login.css">
</head>
<body>
    <div class="login-page">
        <div class="form">
            <div class="login">
                <div class="login-header">
                    <h2>LOGIN</h2>
                    <p>Please enter your credentials to login.</p>
                </div>
            </div>
            <form class="login-form" method="post" action="login.php">
                <input type="text" name="username" placeholder="Username" autocomplete="off" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
                <p class="message">Not registered? <a href="register.php">Create an account</a></p>
            </form>
        </div>
    </div>
</body>
</html>
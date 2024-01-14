<?php
    // Initialize session
    session_start();
    
    if (isset($_SESSION['message'])) {
        echo "<script type='text/javascript'>alert('" . $_SESSION['message'] . "');</script>";
        unset($_SESSION['message']);
    }

    if (isset($_SESSION['error'])) {
        echo "<script type='text/javascript'>alert('" . $_SESSION['error'] . "');</script>";
        unset($_SESSION['error']);
    }

    // Include database connection
    include('dbconfig.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Fetch user data from database
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            // Check if password is correct
            if (password_verify($password, $user['password'])) {
                // Check if email is verified
                if ($user['is_email_verified'] == 1) {
                    // Email is verified, proceed to log in
                    unset($user['password']); // It's a good practice to unset the password
                    $_SESSION['user'] = $user;
                    header('Location: index.php'); // Redirect to the home page or dashboard
                    exit;
                } else {
                    // Email is not verified
                    echo "<script type='text/javascript'>alert('Verify your email before logging in. Sometimes the verification link ends up in your spam!');</script>";
                }
            } else {
                // Incorrect password
                echo "Invalid credentials";
            }
        } else {
            // User does not exist
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
    <div class="join-discord">
        <a class="discord-btn" href="https://discord.gg/9QdzJqwW6A" target="_blank"><img class="discord-img" src="./images/Discord_icon_clyde_white_RGB.svg" alt="join-discord"></a>
    </div>
</body>
</html>
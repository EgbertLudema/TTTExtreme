<?php
    // Include database connection
    include('dbconfig.php');

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email']; // Retrieve the email from POST data

        // Check if the email already exists
        $emailCheckSql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($emailCheckSql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "Email already exists. Please use a different email.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Generate a verification token
            $token = bin2hex(random_bytes(50)); // Ensure your PHP version supports random_bytes()

            // Insert into database
            $sql = "INSERT INTO users (username, password, email, verification_token) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $username, $hashed_password, $email, $token);
            $stmt->execute();

            // Send verification email
            $verificationLink = "https://tttextreme.com/verify_email.php?token=$token";
            $subject = "Email Verification";
            $emailTemplate = <<<HTML
            <!DOCTYPE html>
            <html>
            <head>
                
            </head>
            <body>
                <div class="email-container">
                    <h1>Welcome to TTTExtreme!</h1>
                    <p>Thank you for registering. Please click the button below to verify your email address.</p>
                    <a href="{$verificationLink}" class="button">Verify Email</a>
                </div>
            </body>
            </html>
            HTML;
    
            // Set content-type header for sending HTML email 
            $headers = "MIME-Version: 1.0" . "\r\n"; 
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: TTTExtreme<noreply@tttextreme.com>' . "\r\n";
    
            if(mail($email, $subject, $emailTemplate, $headers)) {
                session_start();
                $_SESSION['message'] = "Verification email sent successfully. Check your email and press the verify link (Check your SPAM).";
            } else {
                session_start();
                $_SESSION['error'] = "Email sending failed";
            }

            header('Location: login.php');
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - TTTExtreme</title>
    <link rel="stylesheet" type="text/css" href="./css/register.css">
</head>

<body>
    <div class="register-page">
        <div class="form">
            <div class="register">
                <div class="register-header">
                    <h2>Register</h2>
                    <p>Please enter your credentials to register.</p>
                </div>
            </div>
            <form class="register-form" method="post" action="register.php">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="email" name="email" placeholder="Email" required> <!-- Email input field -->
                <button type="submit">Register</button>
                <p class="message">Already registered? <a href="login.php">Login</a></p>
            </form>
        </div>
    </div>
    <div class="join-discord">
        <a class="discord-btn" href="https://discord.gg/9QdzJqwW6A" target="_blank"><img class="discord-img" src="./images/Discord_icon_clyde_white_RGB.svg" alt="join-discord"></a>
    </div>
</body>
</html>
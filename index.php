<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "posdb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';
$success = '';

// Handle Login
if (isset($_POST['login'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username = '$username'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "User not found";
    }
}

// Handle Registration
if (isset($_POST['register'])) {
    $username = $conn->real_escape_string($_POST['new_username']);
    $password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $result = $conn->query("INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')");
        if ($result) {
            $success = "Registration successful. You can now log in.";
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gmall POS System - Login/Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 400px;
            width: 90%;
            margin: 0 auto;
        }
        .login-header {
            background-color: #4a69bd;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }
        .login-body {
            padding: 30px;
        }
        .nav-tabs {
            border-bottom: none;
            margin-bottom: 20px;
        }
        .nav-tabs .nav-link {
            border: none;
            color: #4a69bd;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 30px;
            transition: all 0.3s ease;
        }
        .nav-tabs .nav-link.active {
            background-color: #4a69bd;
            color: white;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 30px;
            padding: 12px 20px;
            border: 1px solid #ddd;
        }
        .btnSubmit {
            background-color: #4a69bd;
            color: white;
            border-radius: 30px;
            padding: 12px 30px;
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btnSubmit:hover {
            background-color: #3c5aa8;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .ForgetPwd {
            color: #4a69bd;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .ForgetPwd:hover {
            color: #3c5aa8;
            text-decoration: underline;
        }
        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background-color: #4a69bd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <div class="logo">
                    <i class="fas fa-cash-register"></i>
                </div>
                Gmall POS System
            </div>
            <div class="login-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="login-tab" data-toggle="tab" href="#login" role="tab" aria-controls="login" aria-selected="true">Login</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="register-tab" data-toggle="tab" href="#register" role="tab" aria-controls="register" aria-selected="false">Register</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                        <form method="post">
                            <div class="form-group">
                                <input type="text" class="form-control" name="username" placeholder="Your Username" required />
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" name="password" placeholder="Your Password" required />
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btnSubmit" name="login" value="Login" />
                            </div>
                            <div class="form-group text-center">
                                <a href="#" class="ForgetPwd">Forgot Password?</a>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                        <form method="post">
                            <div class="form-group">
                                <input type="text" class="form-control" name="new_username" placeholder="Choose Username" required />
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" name="new_password" placeholder="Your Password" required />
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required />
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btnSubmit" name="register" value="Register" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>
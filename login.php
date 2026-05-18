<?php
session_start();
include 'db.php';

if (isset($_POST['login'])) {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username = '$user' AND password = '$pass'");

    if ($result->num_rows > 0) {
        $_SESSION['auth_user'] = $user;
        header("Location: index.php");
        exit();
    } else {
        $error = "Incorrect Username or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    <title>FC Manager | Login</title>
    <style>
        body {
            display: flex; 
            justify-content: center; 
            align-items: flex-end; 
            height: 100vh;
            margin: 0;
            padding-bottom: 60px; 
            box-sizing: border-box;
            
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.7)), 
                        url('https://www.fcbarcelona.com/photo-resources/fcbarcelona/photo/2018/11/20/a6cbdb61-a122-462e-a610-b931889076f5/Camp-Nou.jpg?width=1920') no-repeat center center fixed;
            background-size: cover;
        }

        .login-box {
            width: 300px;
            text-align: center;
            padding: 25px;
            background: rgba(16, 22, 39, 0.85); 
            border: 1px solid rgba(255, 188, 0, 0.25); 
            border-radius: 12px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 12px;
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 6px;
            box-sizing: border-box;
            outline: none;
        }

        input:focus {
            border-color: rgba(255, 188, 0, 0.5); 
        }
    </style>
</head>
<body>

    <div class="login-box">
        <h2 style="color: var(--pitch-green); margin: 0 0 15px 0;">FC BARCELONA Squad Management Sytem</h2>
        <h2 style="color: var(--pitch-green); margin: 0 0 15px 0;">ADMIN LOGIN</h2>
        <?php if(isset($error)): ?>
            <p style="color: var(--danger); font-size: 0.8rem; margin-bottom: 15px; text-align: left;">⚠️ <?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required autocomplete="off">
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login" class="btn-primary" style="width: 100%; padding: 12px; margin-top: 5px;">ENTER SYSTEM</button>
        </form>
    </div>

</body>
</html>
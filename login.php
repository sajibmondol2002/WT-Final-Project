<?php
require_once 'config/database.php';
session_start();

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM users WHERE email = '$email' AND role = 'restaurant_manager'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            $res_sql = "SELECT id FROM restaurants WHERE manager_id = " . $user['id'];
            $res_result = mysqli_query($conn, $res_sql);
            $res_data = mysqli_fetch_assoc($res_result);
            $_SESSION['restaurant_id'] = $res_data['id'];

            header("Location: views/dashboard.php");
            exit();
        } else {
            $error = "Invalid Login Credentials!";
        }
    } else {
        $error = "Invalid Login Credentials!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manager Login</title>
    <style>
        body { font-family: Arial; background: #2c3e50; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 30px; border-radius: 8px; width: 320px; box-shadow: 0 0 20px rgba(0,0,0,0.3); }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #27ae60; color: white; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2 style="text-align: center;">Restaurant Manager</h2>
        <?php if(isset($error)) echo "<p style='color:red; text-align:center;'>$error</p>"; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="manager@example.com" required>
            <input type="password" name="password" placeholder="12345" required>
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>
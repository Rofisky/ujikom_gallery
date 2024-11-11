<?php
session_start();
include 'koneksi.php';

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email); 
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Corrected this line to use $row instead of $data
        $_SESSION['email'] = $row['email'];
        $_SESSION['UserID'] = $row['UserID'];

        if (password_verify($password, $row['password'])) {
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $row['role'];

            if ($row['role'] === 'admin') {
                header('Location: admin.php');
            } else {
                header('Location: gallery.php');
            }
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email tidak ditemukan!";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            background-color: #0e0e0e;
            color: #a3cfa3;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background-color: #1a1a1a;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .form-container h2 {
            color: #d1e7d1;
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #3a3d3a;
            background-color: #2c2c2c;
            color: #a3cfa3;
            outline: none;
        }
        input::placeholder {
            color: #6d7d6d;
        }
        button {
            background-color: #3b593b;
            border: none;
            padding: 12px;
            width: 100%;
            cursor: pointer;
            color: #d1e7d1;
            font-weight: bold;
            border-radius: 4px;
        }
        button:hover {
            background-color: #4a6a4a;
        }
        .password-container {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 13px;
            cursor: pointer;
            color: #6d7d6d;
        }
        .error-message {
            color: red;
            margin-bottom: 15px;
        }
        .register-link {
            margin-top: 15px;
            color: #a3cfa3;
        }
        .register-link a {
            color: #58c9c9;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePassword');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.textContent = '🙈';
            } else {
                passwordField.type = 'password';
                toggleIcon.textContent = '👁️';
            }
        }
    </script>
</head>
<body>
    <div class="form-container">
        <h2>Galeri Foto</h2>
        <?php if (!empty($error)): ?>
            <p class="error-message"><?= $error ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" required autofocus>
            </div>
            
            <div class="form-group password-container">
                <input type="password" name="password" id="password" placeholder="Password" required>
                <span id="togglePassword" class="toggle-password" onclick="togglePassword()">👁️</span>
            </div>
            
            <button type="submit">Masuk</button>
        </form>
        
        <p class="register-link">
            Belum punya akun? 
            <a href="register.php">Daftar di sini</a>
        </p>
    </div>
</body>
</html>


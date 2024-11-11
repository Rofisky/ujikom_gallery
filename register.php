<?php
// Koneksi ke database
include('koneksi.php'); // Ganti dengan skrip koneksi database Anda

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Mengamankan password
    $email = $_POST['email'];
    $namalengkap = $_POST['namalengkap'];
    $alamat = $_POST['alamat'];

    
    $role = 'user'; // Default role adalah user
    if ($username === 'admin' || $email === 'admin@example.com') { // Ganti dengan username atau email admin yang diinginkan
        $role = 'admin';
    }

    // Masukkan data pengguna ke dalam database
    $sql = "INSERT INTO users (username, password, email, namalengkap, alamat, role) 
            VALUES ('$username', '$password', '$email', '$namalengkap', '$alamat', '$role')";

    if (mysqli_query($conn, $sql)) {
        echo "Registrasi berhasil!";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
</head>
<body>
<div class="form-container">
    <h2>Galeri Foto</h2>
    <form action="register.php" method="POST">
        <label>Username:</label>
        <input type="text" name="username" required><br>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <label>Email:</label>
        <input type="email" name="email" required><br>
        <label>Nama Lengkap:</label>
        <input type="text" name="namalengkap" required><br>
        <label>Alamat:</label>
        <input type="text" name="alamat" required><br>
        <button type="submit">Daftar</button>
    </form>
    <p class="register-link">
        Sudah punya akun? 
        <a href="login.php">Masuk di sini</a>
    </p>
</div>

</body>
</html>

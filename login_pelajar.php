<?php
session_start();
if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'pelajar') {
    header("Location: dashboard_pelajar.php");
    exit();
}
include_once 'database.php';

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Handle Login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT * FROM tbl_users WHERE fld_username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($user && $user['fld_role'] === 'pelajar' && $password === $user['fld_password'])   {
            $_SESSION['user_id'] = $user['fld_user_id'];
            $_SESSION['username'] = $user['fld_username'];
            $_SESSION['full_name'] = $user['fld_name'];
            $_SESSION['user_role'] = $user['fld_role'];
            $_SESSION['user_matric'] = $user['fld_matric_no'];

            $_SESSION['user_email'] = $user['fld_email'];
            $_SESSION['user_phone'] = $user['fld_phone'];
        
            header("Location: dashboard_pelajar.php");
            exit();

        } else {
            $error = "Nama pengguna atau kata laluan tidak sah untuk pelajar.";
        }
        
    } catch (PDOException $e) {
        $error = "Ralat: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Masuk - Pelajar</title>
     <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Inter', sans-serif;
    }

    body {
      background: linear-gradient(to bottom right, #d3eafd, #a5d7f8);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }

    .login-container {
      background: #fff;
      padding: 40px 30px;
      border-radius: 20px;
      max-width: 420px;
      width: 100%;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
      text-align: center;
    }

    .login-container img {
      width: 60px;
      margin-bottom: 20px;
    }

    .login-container h2 {
      color:rgb(3, 33, 99);
      margin-bottom: 10px;
    }

    .login-container p {
      font-size: 14px;
      color: #555;
      margin-bottom: 30px;
    }

    .input-group {
      margin-bottom: 20px;
      text-align: left;
    }

    .input-group input {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 10px;
      font-size: 14px;
    }

    .form-options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 14px;
      margin-bottom: 25px;
    }

    .form-options a {
      color:rgb(3, 33, 99);
      text-decoration: none;
    }

    .form-options a:hover {
      text-decoration: underline;
    }

    .login-btn {
      width: 100%;
      padding: 14px;
      background-color:rgb(3, 33, 99);
      color: white;
      font-weight: 500;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background 0.3s ease, transform 0.2s ease;
    }

    .login-btn:hover {
      background-color:rgb(3, 33, 99);
      transform: scale(1.01);
    }

    .register-link {
      margin-top: 15px;
      font-size: 14px;
    }

    .register-link a {
      color:rgb(3, 33, 99);
      text-decoration: none;
      font-weight: 500;
    }

    @media (max-width: 480px) {
      .login-container {
        padding: 30px 20px;
      }
    }
    .back-button {
        position: absolute;
        top: 20px;
        left: 20px;
        text-decoration: none;
        font-weight: 500;
        font-size: 16px;
        background-color: transparent;
        border: none;
        color: #007bff;
        padding: 8px 12px;
        border-radius: 8px;
        transition: background-color 0.3s ease;
        }

        .back-button:hover {
        background-color: rgba(0, 123, 255, 0.1);
        text-decoration: underline;
        }

        .auth-link {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
            }

            .auth-link:hover {
            text-decoration: underline;
            color: #0056b3; /* Darker blue on hover */
            }

            .auth-link:active {
            color: #003d80; /* Even darker blue when clicked */
            }


  </style>
</head>
<body>

<a href="login.php" class="back-button">← Kembali</a>

  <div class="login-container">
    <img src="login.png" alt="FirstStepSiswa Logo" />
    <h2>Selamat Datang</h2>
    <p><em>Log masuk ke akaun First Step Siswa anda</em></p>

    <form method="POST" action="">
      <?php if (isset($error)): ?>
        <p style="color: red; margin-bottom: 10px;"><?php echo htmlspecialchars($error); ?></p>
      <?php endif; ?>

      <div class="input-group">
        <input type="text" name="username" placeholder="Nama Pengguna" required>
      </div>

      <div class="input-group">
        <input type="password" id="passwordInput" name="password" placeholder="Kata Laluan" required>
      </div>

       <div style="display: flex; justify-content: space-between; align-items: center; font-size: 14px; margin-bottom: 25px;">
        <label>
            <input type="checkbox" id="togglePassword" onclick="togglePasswordVisibility()">
            Tunjuk Kata Laluan
        </label>
        <a href="forgot_password.php" class="auth-link"><em>Lupa kata laluan?</em></a>
        </div>


      <button type="submit" name="login" class="login-btn">Log Masuk</button>

    <div class="register-link">
        atau <a href="register.php" class="auth-link">Daftar Akaun Baru</a>
        </div>

    </form>
  </div>

  <script>
    function togglePasswordVisibility() {
        const passwordField = document.getElementById("passwordInput");
        if (passwordField.type === "password") {
            passwordField.type = "text";
        } else {
            passwordField.type = "password";
        }
    }
    </script>

</body>
</html>

<?php
session_start();
include('database.php'); // Sambungan ke pangkalan data

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $matric_no = $_POST['matric_no'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = 'pelajar'; // Tetapkan peranan pelajar secara lalai

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Kata laluan tidak sepadan!";
    } else {
        // Semak jika nama pengguna telah wujud
        $check = $conn->prepare("SELECT COUNT(*) FROM tbl_users WHERE fld_username = :username");
        $check->bindParam(':username', $username);
        $check->execute();

        if ($check->fetchColumn() > 0) {
            $_SESSION['error'] = "Nama pengguna telah didaftarkan. Sila pilih yang lain.";
        } else {
            try {
                $stmt = $conn->prepare("INSERT INTO tbl_users (fld_name, fld_email, fld_phone, fld_username, fld_matric_no, fld_password, fld_role) 
                                        VALUES (:name, :email, :phone, :username, :matric_no, :password, :role)");
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':matric_no', $matric_no);
                $stmt->bindParam(':password', $password);
                $stmt->bindParam(':role', $role);

                $stmt->execute();
                $_SESSION['success'] = "Daftar Akaun Berjaya!";
                echo "<script>alert('Daftar Akaun Berjaya!'); window.location.href='login_pelajar.php';</script>";
                exit();
            } catch (PDOException $e) {
                $_SESSION['error'] = "Ralat Sebenar: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Daftar Akaun Baru</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: linear-gradient(to bottom right, #e6f0fc, #c3d9f7);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }

    .register-container {
      background: #fff;
      padding: 40px 30px;
      border-radius: 20px;
      max-width: 480px;
      width: 100%;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
      text-align: center;
    }

    .register-container h2 {
      color: rgb(3, 33, 99);
      margin-bottom: 25px;
    }

    .input-group {
      margin-bottom: 15px;
      text-align: left;
    }

    .input-group label {
      font-size: 14px;
      font-weight: 500;
      color: rgb(3, 33, 99);
      display: block;
      margin-bottom: 6px;
    }

    .input-group input {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 10px;
      font-size: 14px;
    }

    .register-btn {
      width: 100%;
      padding: 14px;
      background-color: rgb(3, 33, 99);
      color: white;
      font-weight: 600;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background 0.3s ease, transform 0.2s ease;
      margin-top: 10px;
    }

    .register-btn:hover {
      background-color: rgb(3, 33, 99);
      transform: scale(1.01);
    }

    .login-link {
      margin-top: 15px;
      font-size: 14px;
    }

    .login-link a {
      color: #007bff;
      text-decoration: none;
      font-weight: 500;
    }

    .login-link a:hover {
      text-decoration: underline;
    }

    @media (max-width: 480px) {
      .register-container {
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body>

  <div class="register-container">
    <h2>Daftar Akaun Baru</h2>

    <?php
    if (isset($_SESSION['error'])) {
      echo '<p style="color:red; font-size:14px; margin-bottom:10px;">' . $_SESSION['error'] . '</p>';
      unset($_SESSION['error']);
    }
    ?>

    <form method="POST" action="">
      <div class="input-group">
        <label>Nama</label>
        <input type="text" name="name" placeholder="contoh: Ain Binti Nizam" required />
      </div>

      <div class="input-group">
        <label>Nama Pengguna</label>
        <input type="text" name="username" placeholder="contoh: ainnizam" required />
      </div>

      <div class="input-group">
        <label>No Matrik</label>
        <input type="text" name="matric_no" placeholder="contoh: A123456" pattern="^A\d{6}$" required />
      </div>

      <div class="input-group">
        <label>Alamat E-mel</label>
        <input type="email" name="email" placeholder="contoh@siswa.ukm.edu.my" required />
      </div>

      <div class="input-group">
        <label>Nombor Telefon</label>
        <input type="text" name="phone" placeholder="contoh: 0112339562" required />
      </div>

      <div class="input-group">
        <label>Kata Laluan</label>
        <input type="password" name="password" placeholder="*******" required />
      </div>

      <div class="input-group">
        <label>Sahkan Kata Laluan</label>
        <input type="password" name="confirm_password" placeholder="*******" required />
      </div>

      <button type="submit" name="register" class="register-btn">Daftar</button>
    </form>

    <div class="login-link">
      atau <a href="login_pelajar.php">Log Masuk</a>
    </div>
  </div>

</body>
</html>

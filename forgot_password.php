<?php
include 'database.php';
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');

// Include PHPMailer
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emel = $_POST['emel'];

    // Validasi email hanya dari UKM
    if (!preg_match('/@(siswa\.ukm\.edu\.my|ukm\.edu\.my)$/', $emel)) {
        echo "<script>alert('Sila gunakan emel UKM sahaja!'); window.location.href='forgot_password.php';</script>";
        exit();
    }

    // Semak jika emel wujud dalam database
    $stmt = $conn->prepare("SELECT * FROM tbl_users WHERE fld_email = ?");
    $stmt->execute([$emel]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Cipta token unik
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime("+30 minutes")); // Token sah selama 30 minit

        // Simpan token dalam database
        $stmt = $conn->prepare("UPDATE tbl_users SET fld_reset_token=?, fld_reset_expiry=? WHERE fld_email=?");
        $stmt->execute([$token, $expiry, $emel]);

        // Pautan reset password
        $reset_link = "http://localhost/FirstStepSiswa/reset_password.php?token=" . $token;

        // Hantar email dengan PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'nabilaha645@gmail.com';
            $mail->Password = 'xcjm jowg hxmw nfhm'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Tetapan email
            $mail->setFrom('a193792@siswa.ukm.edu.my', 'First Step Siswa Support');
            $mail->addAddress($emel);
            $mail->Subject = "Reset Kata Laluan - First Step Siswa";
            $mail->Body = "Klik link berikut untuk reset kata laluan anda: " . $reset_link;

            $mail->send();
            echo "<script>alert('Sila semak emel anda untuk reset kata laluan!'); window.location.href='login.php';</script>";
            exit();
        } catch (Exception $e) {
            echo "<script>alert('Email gagal dihantar: " . $mail->ErrorInfo . "');</script>";
        }
    } else {
        echo "<script>alert('Emel tidak dijumpai dalam sistem!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Lupa Kata Laluan</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Inter', sans-serif;
    }

    body {
      background: linear-gradient(to bottom right, #e6f0fc, #c3d9f7);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }

    .forgot-container {
      background: #fff;
      padding: 40px 30px;
      border-radius: 20px;
      max-width: 420px;
      width: 100%;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
      text-align: center;
    }

    .forgot-container h2 {
      color: rgb(3, 33, 99);
      margin-bottom: 10px;
    }

    .forgot-container p {
      font-size: 14px;
      color: #555;
      margin-bottom: 25px;
    }

    .input-group {
      margin-bottom: 20px;
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

    .forgot-btn {
      width: 100%;
      padding: 14px;
      background-color: rgb(3, 33, 99);
      color: white;
      font-weight: 600;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background 0.3s ease, transform 0.2s ease;
    }

    .forgot-btn:hover {
      background-color: rgb(3, 33, 99);
      transform: scale(1.01);
    }

    .back-link {
      margin-top: 15px;
      display: block;
      font-size: 14px;
      color: #007bff;
      text-decoration: none;
    }

    .back-link:hover {
      text-decoration: underline;
    }

    @media (max-width: 480px) {
      .forgot-container {
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body>

  <div class="forgot-container">
    <h2>Lupa Kata Laluan</h2>
    <p>Masukkan emel UKM anda untuk menerima pautan set semula kata laluan.</p>
    <form method="post">
      <div class="input-group">
        <label for="emel">Emel Anda</label>
        <input type="email" id="emel" name="emel" placeholder="contoh@siswa.ukm.edu.my" required />
      </div>
      <button type="submit" class="forgot-btn">Hantar</button>
    </form>
    <a href="login.php" class="back-link">← Kembali ke Log Masuk</a>
  </div>

</body>
</html>

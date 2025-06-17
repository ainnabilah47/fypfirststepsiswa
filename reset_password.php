<?php
include 'database.php';
session_start();

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Semak token dalam database
    $stmt = $conn->prepare("SELECT * FROM tbl_users WHERE fld_reset_token = ? AND fld_reset_expiry > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "<script>alert('Token tidak sah atau telah tamat tempoh!'); window.location.href='login.php';</script>";
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_password = $_POST['fld_password']; // no hashing
    
        // Kemaskini kata laluan & padam token
        $stmt = $conn->prepare("UPDATE tbl_users SET fld_password = ?, fld_reset_token = NULL, fld_reset_expiry = NULL WHERE fld_reset_token = ?");
        $stmt->execute([$new_password, $token]);
    
        // Redirect ikut peranan
        $redirect = 'login.php'; // fallback default
        if ($user['fld_role'] === 'pelajar') {
            $redirect = 'login_pelajar.php';
        } elseif ($user['fld_role'] === 'penderma' || $user['fld_role'] === 'pentadbir') {
            $redirect = 'login_staff.php';
        }
    
        echo "<script>alert('Kata laluan berjaya dikemas kini! Sila log masuk.'); window.location.href='$redirect';</script>";
        exit();
    }
    
    
} else {
    echo "<script>alert('Token tidak sah!'); window.location.href='login.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reset Kata Laluan</title>
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

    .reset-container {
      background: #fff;
      padding: 40px 30px;
      border-radius: 20px;
      max-width: 420px;
      width: 100%;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
      text-align: center;
    }

    .reset-container h2 {
      color: rgb(3, 33, 99);
      margin-bottom: 10px;
    }

    .reset-container p {
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

    .reset-btn {
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

    .reset-btn:hover {
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
      .reset-container {
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body>

  <div class="reset-container">
    <h2>Reset Kata Laluan</h2>
    <p>Masukkan kata laluan baru anda.</p>
    <form method="post">
      <div class="input-group">
        <label for="katalaluan">Kata Laluan Baru</label>
        <input type="password" id="katalaluan" name="fld_password" placeholder="Masukkan kata laluan baru" required />
      </div>
      <button type="submit" class="reset-btn">Reset Kata Laluan</button>
    </form>
    <a href="login.php" class="back-link">← Kembali ke Log Masuk</a>
  </div>

</body>
</html>

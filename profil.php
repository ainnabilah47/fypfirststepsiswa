<?php
session_start();
include('database.php'); // Sambungan ke pangkalan data

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Ensure logged-in user can access this page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'] ?? '';

// Fetch user data
$stmt = $conn->prepare("SELECT fld_name, fld_email, fld_phone FROM tbl_users WHERE fld_user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Clear success message to avoid it appearing on the profile page
if (isset($_SESSION['success'])) {
    unset($_SESSION['success']);
}

if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = !empty($_POST['password']) ? $_POST['password'] : null;

    try {
        if ($password) {
            $stmt = $conn->prepare("UPDATE tbl_users SET fld_name = :name, fld_email = :email, fld_phone = :phone, fld_password = :password WHERE fld_user_id = :user_id");
            $stmt->bindParam(':password', $password);
        } else {
            $stmt = $conn->prepare("UPDATE tbl_users SET fld_name = :name, fld_email = :email, fld_phone = :phone WHERE fld_user_id = :user_id");
        }

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $_SESSION['success'] = "Kemaskini Berjaya";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Ralat: " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mengurus Profil Akaun</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Inter', sans-serif;
    }

    body {
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }

    .profile-container {
      background: #fff;
      padding: 40px 30px;
      border-radius: 20px;
      max-width: 500px;
      width: 100%;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
      text-align: center;
    }

    h3 {
      color: rgb(3, 33, 99);
      margin-bottom: 25px;
      font-weight: bold;
    }

    label {
      font-weight: 500;
      text-align: left;
      display: block;
      margin-bottom: 5px;
    }

    .form-control {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 10px;
      font-size: 14px;
      margin-bottom: 20px;
    }

    .btn-primary {
      background-color: rgb(3, 33, 99);
      border: none;
      width: 100%;
      padding: 14px;
      border-radius: 10px;
      font-weight: 500;
    }

    .btn-primary:hover {
      background-color: #002366;
    }

    .back-link {
      display: block;
      margin-top: 20px;
      text-align: center;
      font-size: 14px;
      color: rgb(3, 33, 99);
      font-weight: 500;
      text-decoration: none;
    }

    .back-link:hover {
      text-decoration: underline;
    }

    .alert {
      text-align: left;
    }
     body::before {
      content: "";
      position: fixed;
      top: 0;
      left: 0;
      height: 100%;
      width: 100%;
      background-image: url('background.jpg'); /* adjust path */
      background-size: cover;
      background-position: center;
      z-index: -1;
      opacity: 1; 
    }

    body::after {
    content: "";
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
    background-color: rgba(255, 255, 255, 0.5); /* adjust to add faded white overlay */
    z-index: -1;
  }
  </style>
</head>
<body>

<div class="profile-container">
  <h3>Mengurus Profil Akaun</h3>

  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
  <?php endif; ?>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <label>Nama</label>
    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['fld_name']) ?>" required>

    <label>Alamat e-mel</label>
    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['fld_email']) ?>" required>

    <label>Nombor Telefon</label>
    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['fld_phone']) ?>" required>

    <label>Kata Laluan (biarkan kosong jika tidak mahu ubah)</label>
    <input type="password" name="password" class="form-control" placeholder="*******">

    <button type="submit" name="update" class="btn btn-primary">Kemaskini</button>
  </form>

  <a href="<?php 
    switch ($user_role) {
      case 'pelajar': echo 'dashboard_pelajar.php'; break;
      case 'penderma': echo 'dashboard_penderma.php'; break;
      case 'pentadbir': echo 'dashboard_pentadbir.php'; break;
      default: echo 'login.php';
    }
  ?>" class="back-link">← Kembali ke Laman Utama</a>
</div>

</body>
</html>

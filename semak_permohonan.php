<?php

require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
include('database.php');
date_default_timezone_set('Asia/Kuala_Lumpur');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'penderma') {
    header("Location: login_staff.php");
    exit();
}

include 'nav_bar_nu.php';

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$request_id = $_GET['id'] ?? null;

if (!$request_id) {
    echo "ID permohonan tidak sah.";
    exit();
}

// Dapatkan maklumat permohonan
$stmt = $conn->prepare("SELECT * FROM tbl_requests WHERE fld_request_id = :id");
$stmt->bindParam(':id', $request_id);
$stmt->execute();
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    echo "Permohonan tidak dijumpai.";
    exit();
}

// Semak sama ada penderma telah menaja permohonan ini
$alreadySponsored = false;
$checkStmt = $conn->prepare("SELECT * FROM tbl_sponsorships WHERE fld_request_id = :request_id AND fld_sponsor_id = :sponsor_id");
$checkStmt->execute([':request_id' => $request_id, ':sponsor_id' => $_SESSION['user_id']]);
if ($checkStmt->rowCount() > 0) {
    $alreadySponsored = true;
}

if (isset($_POST['taja'])) {
  $sponsor_id = $_SESSION['user_id'];

  // Elakkan penajaan berganda
  $checkStmt = $conn->prepare("SELECT * FROM tbl_sponsorships WHERE fld_request_id = :request_id AND fld_sponsor_id = :sponsor_id");
  $checkStmt->execute([':request_id' => $request_id, ':sponsor_id' => $sponsor_id]);

  if ($checkStmt->rowCount() === 0) {
      // Insert into sponsorships table
      $insertStmt = $conn->prepare("INSERT INTO tbl_sponsorships (fld_request_id, fld_sponsor_id, fld_status) VALUES (:request_id, :sponsor_id, 'ditaja')");
      $insertStmt->execute([':request_id' => $request_id, ':sponsor_id' => $sponsor_id]);

      // Update status in requests table
      $updateStatus = $conn->prepare("UPDATE tbl_requests SET fld_status = 'berjaya' WHERE fld_request_id = :request_id");
      $updateStatus->execute([':request_id' => $request_id]);

      // Get student info for email
      $studentStmt = $conn->prepare("SELECT u.fld_name, u.fld_email 
        FROM tbl_requests r 
        JOIN tbl_users u ON r.fld_user_id = u.fld_user_id 
        WHERE r.fld_request_id = :request_id");
      $studentStmt->execute([':request_id' => $request_id]);
      $student = $studentStmt->fetch(PDO::FETCH_ASSOC);

      if ($student) {
          $student_name = $student['fld_name'];
          $student_email = $student['fld_email'];

          // Get donor name from DB
          $donor_id = $_SESSION['user_id'];
          $getDonor = $conn->prepare("SELECT fld_name FROM tbl_users WHERE fld_user_id = :id");
          $getDonor->execute([':id' => $donor_id]);
          $donor = $getDonor->fetch(PDO::FETCH_ASSOC);
          $donor_name = $donor['fld_name'] ?? 'Seorang Penderma';

          // Send email notification
          $mail = new PHPMailer(true);
          try {
              $mail->isSMTP();
              $mail->Host = 'smtp.gmail.com';
              $mail->SMTPAuth = true;
              $mail->Username = 'nabilaha645@gmail.com';
              $mail->Password = 'xcjm jowg hxmw nfhm';
              $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
              $mail->Port = 587;

              // Recipients
              $mail->setFrom('a193792@siswa.ukm.edu.my', 'First Step Siswa Support');
              $mail->addAddress($student_email, $student_name);

              $mail->isHTML(true);
              $mail->Subject = 'Permohonan Anda Telah Ditaja';
              $mail->Body = "
              <h3>Hi $student_name,</h3>
              <p>Tahniah! Permohonan bantuan anda telah <strong>berjaya</strong>.</p>
              <p><strong>$donor_name</strong> telah menaja permohonan anda bagi kategori <strong>{$request['fld_category']}</strong>.</p>
              <br>
              <p>Sila log masuk ke sistem untuk melihat maklumat lanjut bantuan anda.</p>
              <p>Terima kasih,<br><strong>First Step Siswa</strong></p>
          ";

              $mail->send();
          } catch (Exception $e) {
              // Optional: log error silently
          }
      }

      $_SESSION['sponsor_success'] = true;
  }

  header("Location: semak_permohonan.php?id=$request_id");
  exit();
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Perincian Permohonan Bantuan Pelajar</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background-image: url('background.jpg');
      background-size: cover;
      background-attachment: fixed;
      background-position: left;
      background-repeat: no-repeat;
      font-family: 'Inter', sans-serif;
      margin: 0;
      padding: 0;
    }

    h2 {
    font-weight: bold;
    margin-bottom: 30px;
    text-align: left;
    color: #333; /* This is the solid black color */
    }

    .form-container {
      background: #ffffff;
      margin: 60px auto;
      padding: 30px;
      border-radius: 10px;
      max-width: 750px;
      box-shadow: 0 6px 25px rgba(0,0,0,0.25);
      position: relative;
      z-index: 1;
    }
    .form-label {
      font-weight: 600;
      color: #333;
    }
    .section-title {
      color: #2c3e50;
      font-weight: 700;
      margin-top: 30px;
      margin-bottom: 15px;
    }
    .btn-taja {
      background-color: #28a745;
      color: white;
      border: none;
      padding: 10px 20px;
      font-weight: 500;
      border-radius: 5px;
    }
    .btn-taja:hover {
      background-color: #218838;
    }
    .btn-batal {
      background-color: #6c757d;
      color: white;
      border: none;
      padding: 10px 20px;
      font-weight: 500;
      border-radius: 5px;
    }
    .btn-batal:hover {
      background-color: #5a6268;
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
      opacity: 1; /* 👈 Adjust this for more or less transparency */
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

<div class="container">
  
  <?php if (isset($_SESSION['sponsor_success'])): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'PENAJAAN ANDA TERHADAP PELAJAR BERJAYA',
        html: 'Tekan <a href="dashboard_penderma.php"><u>di sini</u></a> untuk kembali ke laman utama.',
        showConfirmButton: false,
        timer: 5000
      });
    </script>
    <?php unset($_SESSION['sponsor_success']); ?>
  <?php endif; ?>

  <div class="form-container">
    <h2 style="font-weight: bold;">Perincian Permohonan Bantuan</h2>
    <p><i>Sila semak maklumat pelajar sebelum membuat penajaan.</i></p>

    <h3 class="text-primary mt-4">Maklumat Pelajar</h3>
    <form method="POST" id="tajaForm">
      <div class="form-group">
        <label class="form-label">Nama:</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($request['fld_name']) ?>" readonly>
      </div>

      <div class="form-group">
        <label class="form-label">No Matrik:</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($request['fld_matric_no']) ?>" readonly>
      </div>

      <div class="form-group">
        <label class="form-label">No Telefon:</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($request['fld_phone']) ?>" readonly>
      </div>

      <div class="form-group">
        <label class="form-label">Email:</label>
        <input type="email" class="form-control" value="<?= htmlspecialchars($request['fld_email']) ?>" readonly>
      </div>

      <h3 class="text-primary mt-4">Maklumat Bantuan</h3>
      <div class="form-group">
        <label class="form-label">Jenis Bantuan:</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($request['fld_category']) ?>" readonly>
      </div>

      <div class="form-group">
        <label class="form-label">Status Permohonan:</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars(ucfirst($request['fld_status'])) ?>" readonly>
      </div>

      <div class="form-group">
        <label class="form-label">Tarikh Permohonan:</label>
        <input type="text" class="form-control" value="<?= date('Y-m-d', strtotime($request['fld_request_date'])) ?>" readonly>
      </div>

      <div class="form-group d-flex justify-content-between mt-4">
        <?php if (!$alreadySponsored): ?>
          <button type="button" class="btn btn-taja" onclick="confirmTajaan()">Taja Permohonan</button>
          <input type="hidden" name="taja" value="1">
          <a href="saringan_permohonan.php" class="btn btn-batal">Batal Penajaan</a>
        <?php else: ?>
          <div class="d-flex justify-content-between w-100">
            <a href="senarai_ditaja.php" class="btn btn-default btn-back">← Kembali</a>
            <button type="button" class="btn btn-secondary" disabled>Telah Ditaja</button>
          </div>
        <?php endif; ?>

      </div>

    </form>
  </div>
</div>

<script>
function confirmTajaan() {
  Swal.fire({
    title: 'NOTIFIKASI',
    text: 'Adakah anda ingin sahkan penajaan?',
    icon: 'question',
    showCancelButton: true,
    showCloseButton: true, // ✅ Adds the "X" close button
    confirmButtonText: 'Sah',
    cancelButtonText: 'Batal',
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      setTimeout(() => {
        document.getElementById('tajaForm').submit();
      }, 1000);
    }
  });
}

</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>

</body>
</html>

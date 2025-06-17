<?php

require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include_once 'database.php';
date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'pentadbir') {
    header("Location: login_staff.php");
    exit();
}

$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$where = "WHERE fld_status IN ('sedang diproses', 'diluluskan', 'ditolak', 'berjaya')";

if ($search) {
    $safe = mysqli_real_escape_string($conn, $search);
    $where .= " AND (fld_name LIKE '%$safe%' OR fld_matric_no LIKE '%$safe%' OR fld_category LIKE '%$safe%')";
}

// Count total records
$totalSql = "SELECT COUNT(*) FROM tbl_requests $where";
$totalResult = mysqli_query($conn, $totalSql);
$totalRows = mysqli_fetch_row($totalResult)[0];
$totalPages = ceil($totalRows / $limit);

// Get data
$sql = "
  SELECT *, 
    DATEDIFF(CURDATE(), fld_request_date) AS days_pending
  FROM tbl_requests
  $where
  ORDER BY 
    CASE 
      WHEN fld_status = 'sedang diproses' AND DATEDIFF(CURDATE(), fld_request_date) >= 7 THEN 1
      WHEN fld_status = 'sedang diproses' AND DATEDIFF(CURDATE(), fld_request_date) >= 3 THEN 2
      ELSE 3
    END,
    fld_request_id DESC
  LIMIT $offset, $limit
";

$result = mysqli_query($conn, $sql);

$notificationMessages = [];

$adminEmail = "a193792@siswa.ukm.edu.my"; 

$emailBody = "<h3 style='font-family: Arial, sans-serif;'>Senarai Permohonan Belum Diproses</h3>";
$emailBody .= "
<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; font-family: Arial, sans-serif; width: 100%; text-align: center;'>
  <thead>
    <tr style='background-color: #f44336; color: white;'>
      <th style='width: 20%;'>Nama Pelajar</th>
      <th style='width: 12%;'>No Matrik</th>
      <th style='width: 25%;'>Jenis Bantuan</th>
      <th style='width: 13%;'>Hari Tertunggak</th>
      <th style='width: 15%;'>Tarikh Permohonan</th>
      <th style='width: 15%;'>Status</th>
    </tr>
  </thead>
  <tbody>";

$sendEmail = false;

$notifSql = "SELECT fld_name, fld_matric_no, fld_category, fld_request_date FROM tbl_requests WHERE fld_status = 'sedang diproses'";
$notifResult = mysqli_query($conn, $notifSql);

while ($rowNotif = mysqli_fetch_assoc($notifResult)) {
    $daysSince = floor((strtotime(date('Y-m-d')) - strtotime($rowNotif['fld_request_date'])) / (60 * 60 * 24));
    $statusLabel = '';

    if ($daysSince >= 7) {
        $statusLabel = "<span style='color:red;'>Lambat (7+ hari)</span>";
        $sendEmail = true;
    } elseif ($daysSince >= 3) {
        $statusLabel = "<span style='color:orange;'>Tertangguh (3+ hari)</span>";
        $sendEmail = true;
    }

    if (!empty($statusLabel)) {
        $emailBody .= "
        <tr>
          <td style='word-break: break-word; padding: 6px;'>" . htmlspecialchars($rowNotif['fld_name']) . "</td>
          <td>" . htmlspecialchars($rowNotif['fld_matric_no']) . "</td>
          <td style='word-break: break-word;'>" . htmlspecialchars($rowNotif['fld_category']) . "</td>
          <td>{$daysSince} hari</td>
          <td>" . date('Y-m-d', strtotime($rowNotif['fld_request_date'])) . "</td>
          <td>$statusLabel</td>
        </tr>";

        $notificationMessages[] = "<strong>Permohonan oleh {$rowNotif['fld_name']} masih belum diproses selama $daysSince hari.</strong>";
    }
}

$count = count($notificationMessages); // Jumlah permohonan tertunggak

$emailBody .= "</tbody></table>";

$emailBody .= "
<p style='margin-top:20px; font-family: Arial, sans-serif; text-align: center;'>
   Sila log masuk ke sistem untuk semak permohonan yang masih tertunggak.
</p>";

$lastSentFile = 'last_notification_sent.txt';
$today = date('Y-m-d');
$lastSent = file_exists($lastSentFile) ? file_get_contents($lastSentFile) : '';

if ($sendEmail && $lastSent !== $today) {

    // Simpan tarikh sebagai penanda dah dihantar
    file_put_contents($lastSentFile, $today);

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'nabilaha645@gmail.com';
        $mail->Password = 'xcjm jowg hxmw nfhm';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('a193792@siswa.ukm.edu.my', 'First Step Siswa Support');
        $mail->addAddress($adminEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Notifikasi Permohonan Belum Diproses';
        $mail->Body    = $emailBody;
        $mail->AltBody = strip_tags($emailBody); // versi teks

        $mail->send();
        // Log atau simpan ke fail jika perlu, tapi tiada popup
        
        // Jejak penghantaran emel
        file_put_contents('notification_log.txt',
            "[" . date('Y-m-d H:i:s') . "] Emel dihantar ke $adminEmail dengan $count permohonan tertunggak\n",
            FILE_APPEND
        );

    } catch (Exception $e) {
        file_put_contents('notification_log.txt',
        "[" . date('Y-m-d H:i:s') . "] ❌ GAGAL hantar emel ke $adminEmail. Ralat: {$mail->ErrorInfo}\n",
        FILE_APPEND
    );
    }
}

?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Memproses Permohonan Bantuan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: url('background.jpg') no-repeat center center fixed;
      background-size: cover;
      font-size: 14px; /* or 16px - set consistent base font size */
      line-height: 1.4;
    }

    h2 {
    font-weight: bold;
    margin-bottom: 10px;
    text-align: left;
    color: #333; /* This is the solid black color */
    }

    table {
      font-size: 14px; /* ensure table text matches body font size */
    }

    table th, table td {
      vertical-align: middle; /* aligns text nicely in table cells */
    }

    .container-box {
      background: #ffffff;
      padding: 30px;
      border-radius: 15px;
      margin-top: 60px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
    }

    nav.navbar {
      background-color: #ffffff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 9999;
    }

    table thead {
      background-color: #007BFF;
      color: white;
    }

    .btn-view {
      border: 1px solid #6c63ff;
      color: #6c63ff;
    }

    .btn-view:hover {
      background-color: #6c63ff;
      color: white;
    }

    .pagination {
      margin-top: 20px;
      justify-content: center;
    }

    .pagination li a {
      color: #007BFF;
    }

    .pagination .active a {
      background-color: #007BFF;
      color: white;
      border: 1px solid #007BFF;
    }

    .search-box {
      float: right;
      margin-bottom: 15px;
    }
        /* Highlight rows based on status age */
    .flag-yellow td {
      background-color: #fff8e1 !important;
    }

    .flag-red td {
      background-color: #ffe6e6 !important;
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

<?php include 'nav_bar_du.php'; ?>

<div class="container">

  <!-- Back Button -->
  <a href="dashboard_pentadbir.php" style="
    position: absolute;
    top: 20px;
    left: 20px;
    text-decoration: none;
    font-weight: 600;
    color: #337ab7;
    font-size: 16px;
    background-color: transparent;
    border: none;
    padding: 6px 12px;
    border-radius: 8px;
    transition: background-color 0.3s ease;
  " onmouseover="this.style.backgroundColor='#eef6ff'" onmouseout="this.style.backgroundColor='transparent'">
    ← Kembali
  </a>

  <div class="container-box">

    <?php if (!empty($notificationMessages)): ?>
      <div class="alert alert-danger" role="alert">
        <strong>Peringatan Penting:</strong>
        <ul>
          <?php foreach ($notificationMessages as $msg): ?>
            <li><?= $msg ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <h2 class="fw-bold">Memproses Permohonan Bantuan</h2>
    <h3 class="text-primary fw-semibold">Senarai Permohonan</h3>
    <p><i>Sila semak dan sahkan permohonan pelajar di bawah.</i></p>

    <!-- Search Box -->
    <form method="GET" class="form-inline text-right mb-3">
      <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Cari pelajar..." value="<?= htmlspecialchars($search); ?>">
        <span class="input-group-btn">
          <button class="btn btn-primary" type="submit"><i class="glyphicon glyphicon-search"></i></button>
        </span>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-bordered table-hover mt-3">
        <thead>
          <tr>
            <th>Nama Pelajar</th>
            <th>No Matrik</th>
            <th>No Telefon</th>
            <th>E-mel</th>
            <th>Jenis Bantuan</th>
            <th>Status Permohonan</th>
            <th>Tarikh Permohonan</th> 
            <th>Tindakan</th> 
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
              <?php
                $rowClass = '';
                if ($row['fld_status'] === 'sedang diproses') {
                    $requestDate = strtotime($row['fld_request_date']);
                    $today = strtotime(date('Y-m-d'));
                    $daysPending = floor(($today - $requestDate) / (60 * 60 * 24));

                    if ($daysPending >= 7) {
                        $rowClass = 'flag-red';
                    } elseif ($daysPending >= 3) {
                        $rowClass = 'flag-yellow';
                    }
                }
              ?>
              <tr class="<?= $rowClass ?>">
                <td><?= htmlspecialchars($row['fld_name']); ?></td>
                <td><?= htmlspecialchars($row['fld_matric_no']); ?></td>
                <td><?= htmlspecialchars($row['fld_phone']); ?></td>
                <td><?= htmlspecialchars($row['fld_email']); ?></td>
                <td><?= htmlspecialchars($row['fld_category']); ?></td>
                <td>
                    <?php
                      $status = $row['fld_status'];
                      $requestDate = strtotime($row['fld_request_date']);
                      $today = strtotime(date('Y-m-d'));
                      $daysPending = ($today - $requestDate) / (60 * 60 * 24);

                      if ($status === 'diluluskan') {
                          echo '<span class="label label-success">Diluluskan</span>';
                      } elseif ($status === 'ditolak') {
                          echo '<span class="label label-warning">Ditolak</span>';
                      } elseif ($status === 'berjaya') {
                          echo '<span class="label label-primary">Berjaya</span>';
                      } else {
                          if ($daysPending >= 7) {
                              echo '<span class="label label-danger">Sedang diproses (7+ hari)</span>';
                          } elseif ($daysPending >= 3) {
                              echo '<span class="label label-warning">Sedang diproses (3+ hari)</span>';
                          } else {
                              echo '<span class="label" style="background-color:#ffc107;">Sedang diproses</span>';
                          }
                      }
                    ?>
                  </td>

                <td><?= date('Y-m-d', strtotime($row['fld_request_date'])); ?></td> <!-- Display Tarikh Permohonan -->
                <td><a href="perincian_permohonan.php?id=<?= $row['fld_request_id']; ?>" class="btn btn-sm btn-view">Lihat</a></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="7" class="text-center">Tiada permohonan dijumpai.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
      <nav>
        <ul class="pagination">
          <?php if ($page > 1): ?>
            <li><a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">«</a></li>
          <?php endif; ?>
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="<?= ($i == $page) ? 'active' : '' ?>">
              <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>
          <?php if ($page < $totalPages): ?>
            <li><a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">»</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    <?php endif; ?>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
</body>
</html>

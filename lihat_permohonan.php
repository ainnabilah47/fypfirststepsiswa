<?php
include_once 'database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'pelajar') {
    header("Location: login_pelajar.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch latest request
$query = "SELECT * FROM tbl_requests WHERE fld_user_id = ? ORDER BY fld_request_id DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$request = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Lihat Permohonan</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <style>
    body {
      background-image: url('background.jpg');
      background-size: cover;
      background-attachment: fixed;
      background-position: center;
      font-family: 'Inter', sans-serif;
    }

    .form-container {
      background: #ffffff;
      margin: 30px auto;
      padding: 30px;
      border-radius: 10px;
      max-width: 750px;
      box-shadow: 0 6px 25px rgba(0,0,0,0.25);
    }

    h2 {
      font-weight: bold;
      color: #222;
      margin-bottom: 10px;
    }

    .status-badge {
      padding: 5px 10px;
      border-radius: 20px;
      font-weight: bold;
      display: inline-block;
      color: white;
    }

    .status-sedang {
      background-color: #f0ad4e;
    }

    .status-diluluskan {
      background-color: #5cb85c;
    }

    .status-ditolak {
      background-color: #d9534f;
    }

    .form-group label {
      font-weight: bold;

    }

    .back-button {
    width: 45px;
    height: 45px;
    font-size: 20px;
    font-weight: bold;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
    color: #0d6efd; /* Bootstrap primary color */
    border: 2px solid #0d6efd;
    background-color: transparent;
  }

  .back-button:hover {
    background-color: #0d6efd; /* Solid blue */
    color: white; /* Arrow turns white */
    border-color: #0d6efd;
  }

  .btn-back {
    margin-top: 10px;
  }


  .btn-back:hover {
    background-color: #e3f1ff;
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

<?php include 'nav_bar.php'; ?>

<div class="container">

  <div class="form-container">
    <h2>Lihat Permohonan</h2>
    <p>Berikut adalah maklumat permohonan anda.</p>

    <?php if (!$request): ?>
      <div class="alert alert-warning">Anda belum membuat sebarang permohonan.</div>
    <?php else: ?>
        <div class="form-group">
  <label>Status Permohonan:</label><br>
  <?php
    $status = strtolower($request['fld_status']); // just in case it’s in caps
    $badgeClass = 'status-sedang';
    $message = 'Status tidak diketahui.';

    switch ($status) {
      case 'diluluskan':
        $badgeClass = 'status-diluluskan';
        $message = "Tahniah, permohonan anda diluluskan dan sedang disaring oleh penderma";
        break;
      case 'sedang diproses':
        $badgeClass = 'status-sedang';
        $message = "Permohonan anda sedang diproses.";
        break;
      case 'ditolak':
        $badgeClass = 'status-ditolak';
        $message = "Maaf, permohonan anda ditolak.";
        break;
      case 'berjaya':
        $badgeClass = 'status-diluluskan';
        $message = "Tahniah, permohonan anda diluluskan";
        break;
    }
    
  ?>
  <span class="status-badge <?= $badgeClass ?>"><?= strtoupper($status) ?></span>
  <p class="mt-2"><strong><?= $message ?></strong></p>
</div>

      <div class="form-group">
        <label>Nama:</label>
        <input type="text" value="<?= htmlspecialchars($request['fld_name']) ?>" class="form-control" disabled>
      </div>

      <div class="form-group">
        <label>No Matrik:</label>
        <input type="text" value="<?= htmlspecialchars($request['fld_matric_no']) ?>" class="form-control" disabled>
      </div>

      <div class="form-group">
        <label>No Telefon:</label>
        <input type="text" value="<?= htmlspecialchars($request['fld_phone']) ?>" class="form-control" disabled>
      </div>

      <div class="form-group">
        <label>Email:</label>
        <input type="text" value="<?= htmlspecialchars($request['fld_email']) ?>" class="form-control" disabled>
      </div>

      <div class="form-group">
        <label>Jenis Bantuan:</label>
        <input type="text" value="<?= htmlspecialchars($request['fld_category']) ?>" class="form-control" disabled>
      </div>

      <div class="form-group">
        <label>Slip Pendapatan:</label><br>
        <?php if ($request['fld_income_slip']): ?>
          <a href="<?= $request['fld_income_slip'] ?>" target="_blank">Lihat Slip</a>
        <?php else: ?>
          <span class="text-muted">Tiada fail</span>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label>Dokumen Sokongan:</label><br>
        <?php if ($request['fld_supporting_doc']): ?>
          <a href="<?= $request['fld_supporting_doc'] ?>" target="_blank">Lihat Dokumen</a>
        <?php else: ?>
          <span class="text-muted">Tiada dokumen</span>
        <?php endif; ?>
      </div>
    <?php endif; ?>
    <div class="form-group mt-4">
  <button class="btn btn-default btn-back" onclick="window.location.href='borang_permohonan.php'">← Kembali</button>

  </div>
  
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>

</body>
</html>

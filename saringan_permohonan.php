<?php
include_once 'database.php';
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'penderma') {
    header("Location: login_staff.php");
    exit();
}

$search = $_GET['search'] ?? '';

$sql = "SELECT * FROM tbl_requests WHERE fld_status = 'diluluskan'";
if ($search) {
    $sql .= " AND fld_category LIKE '%" . mysqli_real_escape_string($conn, $search) . "%'";
}
$sql .= " ORDER BY fld_request_id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Saringan Permohonan Bantuan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: url('background.jpg') no-repeat center center fixed;
      background-size: cover;
    }

    h2 {
    font-weight: bold;
    margin-bottom: 30px;
    text-align: left;
    color: #333; /* This is the solid black color */
    }

    .container-box {
      background: #ffffff;
      padding: 30px;
      border-radius: 12px;
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

    .pagination > .active > a {
      background-color: #007BFF;
      color: white !important;
      border-color: #007BFF;
    }

    .pagination > li > a {
      color: #007BFF;
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

<?php include 'nav_bar_nu.php'; ?>

<div class="container">

  <!-- Back Button -->
  <a href="dashboard_penderma.php" style="
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
    <h2 class="fw-bold">Saringan Permohonan Bantuan</h2>
    <h4 class="text-primary fw-bold">Senarai Permohonan Diluluskan</h4>
    <p><i>Berikut adalah permohonan pelajar yang telah diluluskan dan sedia untuk ditaja.</i></p>

    <!-- Search Bar -->
    <form method="GET" class="form-inline text-right mb-3">
      <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Cari jenis bantuan..." value="<?= htmlspecialchars($search); ?>">
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
            <th>No Telefon</th>
            <th>E-mel</th>
            <th>Jenis Bantuan</th>
            <th>Tindakan</th>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td><?= htmlspecialchars($row['fld_name']); ?></td>
                <td><?= htmlspecialchars($row['fld_phone']); ?></td>
                <td><?= htmlspecialchars($row['fld_email']); ?></td>
                <td><?= htmlspecialchars($row['fld_category']); ?></td>
                <td><a href="semak_permohonan.php?id=<?= $row['fld_request_id']; ?>" class="btn btn-sm btn-view">Lihat</a></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="5" class="text-center">Tiada permohonan diluluskan buat masa ini.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>

</body>
</html>

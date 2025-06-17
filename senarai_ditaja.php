<?php
include_once 'database.php';
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'penderma') {
    header("Location: login_staff.php");
    exit();
}

$sponsor_id = $_SESSION['user_id'];
$search = $_GET['search'] ?? '';
$itemsPerPage = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $itemsPerPage;

$countSql = "SELECT COUNT(*) as total FROM tbl_requests r INNER JOIN tbl_sponsorships s ON r.fld_request_id = s.fld_request_id
    WHERE s.fld_sponsor_id = '$sponsor_id' AND r.fld_status = 'berjaya'";

if ($search) {
    $safe = mysqli_real_escape_string($conn, $search);
    $countSql .= " AND r.fld_category LIKE '%$safe%'";
}

$countResult = mysqli_query($conn, $countSql);
$totalRows = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRows / $itemsPerPage);

$sql = "SELECT r.*, s.fld_created_at FROM tbl_requests r INNER JOIN tbl_sponsorships s ON r.fld_request_id = s.fld_request_id
    WHERE s.fld_sponsor_id = '$sponsor_id' AND r.fld_status = 'berjaya'";

if ($search) {
    $safe = mysqli_real_escape_string($conn, $search);
    $sql .= " AND r.fld_category LIKE '%$safe%'";
}
$sql .= " ORDER BY s.fld_created_at DESC LIMIT $itemsPerPage OFFSET $offset";
$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Permohonan Telah Ditaja</title>
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

  <div class="container-box">
    <h2 class="fw-bold">Sejarah Permohonan Telah Ditaja</h2>
    <h4 class="text-primary fw-semibold">Senarai Permohonan yang Telah Anda Taja</h4>
    <p><i>Berikut adalah permohonan pelajar yang telah anda taja.</i></p>

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
            <th>Tarikh Taja</th>
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
                <td><?= date('Y-m-d', strtotime($row['fld_created_at'])); ?></td>
                <td><a href="semak_permohonan.php?id=<?= $row['fld_request_id']; ?>" class="btn btn-sm btn-view">Lihat</a></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="6" class="text-center">Tiada permohonan telah ditaja.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if ($totalPages > 1): ?>
      <nav aria-label="Page navigation">
        <ul class="pagination">
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="<?= ($i === $page) ? 'active' : '' ?>">
              <a href="?search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
    <?php endif; ?>

  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>

</body>
</html>

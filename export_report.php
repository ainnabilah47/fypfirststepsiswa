<?php
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');

include_once 'database.php';
require_once('dompdf/autoload.inc.php'); 
use Dompdf\Dompdf;
use Dompdf\Options;

// Ensure only admin can access the page
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'pentadbir') {
    header("Location: login_staff.php");
    exit();
}

// Fetch approved applications for the report
$query = "SELECT fld_name, fld_matric_no, fld_category, fld_status, fld_request_date FROM tbl_requests WHERE fld_status = 'berjaya' ORDER BY fld_request_date DESC";
$result = mysqli_query($conn, $query);

// Pagination settings
$limit = 10; // Bilangan rekod per halaman
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total rows
$count_query = "SELECT COUNT(*) as total FROM tbl_requests WHERE fld_status = 'berjaya'";
$count_result = mysqli_query($conn, $count_query);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $limit);

// Fetch data with LIMIT
$query = "SELECT fld_name, fld_matric_no, fld_category, fld_status, fld_request_date 
          FROM tbl_requests 
          WHERE fld_status = 'berjaya' 
          ORDER BY fld_request_date DESC 
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// CSV Export
if (isset($_POST['export_csv'])) {
    // Set CSV headers
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="laporan_permohonan_berjaya.csv"');

    $output = fopen('php://output', 'w');
    
    // Output column headers
    fputcsv($output, ['Nama Pelajar', 'No Matrik', 'Jenis Bantuan', 'Status Permohonan', 'Tarikh Permohonan']);
    
    // Output rows
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['fld_name'],
            $row['fld_matric_no'],
            $row['fld_category'],
            ucfirst($row['fld_status']),
            date('Y-m-d', strtotime($row['fld_request_date']))
        ]);
    }
    fclose($output);
    exit();
}

// PDF Export
if (isset($_POST['export_pdf'])) {
    // Reload data again in case it's consumed earlier
    $result = mysqli_query($conn, "SELECT fld_name, fld_matric_no, fld_category, fld_status, fld_request_date 
                                   FROM tbl_requests 
                                   WHERE fld_status = 'berjaya' 
                                   ORDER BY fld_request_date DESC 
                                   LIMIT $limit OFFSET $offset");

    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $dompdf = new Dompdf($options);

    $html = '
    <h2 style="text-align:center;">Laporan Permohonan Berjaya</h2>
    <p><i>Senarai permohonan pelajar yang telah berjaya diterima dan diluluskan oleh penderma.</i></p>
    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <thead>
            <tr style="background-color:#007BFF; color:white;">
                <th>Nama Pelajar</th>
                <th>No Matrik</th>
                <th>Jenis Bantuan</th>
                <th>Status Permohonan</th>
                <th>Tarikh Permohonan</th>
            </tr>
        </thead>
        <tbody>';

    while ($row = mysqli_fetch_assoc($result)) {
        $html .= '
            <tr>
                <td>' . htmlspecialchars($row['fld_name']) . '</td>
                <td>' . htmlspecialchars($row['fld_matric_no']) . '</td>
                <td>' . htmlspecialchars($row['fld_category']) . '</td>
                <td>' . ucfirst(htmlspecialchars($row['fld_status'])) . '</td>
                <td>' . date('Y-m-d', strtotime($row['fld_request_date'])) . '</td>
            </tr>';
    }

    $html .= '</tbody></table>';

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("laporan_permohonan_berjaya.pdf", ["Attachment" => true]);
    exit();
}


?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Laporan Permohonan Berjaya</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: url('background.jpg') no-repeat center center fixed;
      background-size: cover;
      margin: 0;
      padding: 0;
    }
    .container-box {
      background: #ffffff;
      padding: 30px;
      margin-top: 60px;
      border-radius: 15px;
      box-shadow: 0 6px 25px rgba(0,0,0,0.25);
      max-width: 1200px;
      margin: auto;
    }
    h2 {
      font-weight: bold;
      margin-bottom: 20px;
      text-align: left;
    }
    .table th {
      background-color: #007BFF;
      color: white;
    }

    /* Align the buttons horizontally */
    .button-container {
      display: flex;
      justify-content: flex-start; /* Align buttons to the left */
      gap: 170px; /* Adds space between buttons */
      margin-top: 20px;
    }

    .btn-export {
      background-color: #28a745;
      color: white;
      padding: 10px 20px;
      font-weight: 600;
      border-radius: 5px;
    }

    .btn-export:hover {
      background-color: #218838;
    }

    .btn-back {
      margin-right: 100px; /* Keep space between the back and export buttons */
    }
    .pagination li {
    display: inline;
    margin: 0 5px;
  }

  .pagination .active .page-link {
    background-color: #007BFF;
    color: white;
    border-color: #007BFF;
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
  <div class="container-box">
    <h2>Laporan Permohonan Berjaya</h2>
    <p><i>Senarai permohonan pelajar yang telah berjaya diterima dan diluluskan oleh penderma.</i></p>

    <table class="table table-bordered table-hover mt-3">
      <thead>
        <tr>
          <th>Nama Pelajar</th>
          <th>No Matrik</th>
          <th>Jenis Bantuan</th>
          <th>Status Permohonan</th>
          <th>Tarikh Permohonan</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= htmlspecialchars($row['fld_name']); ?></td>
            <td><?= htmlspecialchars($row['fld_matric_no']); ?></td>
            <td><?= htmlspecialchars($row['fld_category']); ?></td>
            <td><?= ucfirst(htmlspecialchars($row['fld_status'])); ?></td>
            <td><?= date('Y-m-d', strtotime($row['fld_request_date'])); ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
      </table>

<!-- Pagination -->
<nav aria-label="Page navigation example">
  <ul class="pagination justify-content-center">
    <?php if ($page > 1): ?>
      <li><a class="page-link" href="?page=<?= $page - 1 ?>">← Sebelum</a></li>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
      <li class="page-item <?= $i == $page ? 'active' : '' ?>">
        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
      <li><a class="page-link" href="?page=<?= $page + 1 ?>">Seterusnya →</a></li>
    <?php endif; ?>
  </ul>
</nav>

    </table>

    <!-- Buttons aligned horizontally without changing the design -->
    <div class="button-container">
      <button class="btn btn-default btn-back" onclick="window.location.href='report_berjaya.php'">← Kembali</button>

      <form method="POST">
        <button type="submit" name="export_csv" class="btn btn-export">Eksport CSV</button>
        <button type="submit" name="export_pdf" class="btn btn-export">Eksport PDF</button>
      </form>
    </div>

  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>

</body>
</html>

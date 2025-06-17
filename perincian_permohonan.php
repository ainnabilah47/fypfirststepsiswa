<?php
include_once 'database.php';
date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'pentadbir') {
    header("Location: login_staff.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: memproses_permohonan.php");
    exit();
}

$request_id = $_GET['id'];
$query = "SELECT * FROM tbl_requests WHERE fld_request_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();
$permohonan = $result->fetch_assoc();

if (!$permohonan) {
    echo "<script>alert('Permohonan tidak dijumpai!'); window.location.href='memproses_permohonan.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Perincian Permohonan</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
      box-shadow: 0 6px 20px rgba(0,0,0,0.25);
    }
    .btn-custom {
      border: 1px solid #6c63ff;
      color: #6c63ff;
    }
    .btn-custom:hover {
      background-color: #6c63ff;
      color: white;
    }
    .btn-back {
      margin-right: 10px;
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
    <h2 class="fw-bold">Perincian Permohonan Bantuan</h2>
    <table class="table">
  <tr><th>Nama</th><td><?= htmlspecialchars($permohonan['fld_name']); ?></td></tr>
  <tr><th>No Matrik</th><td><?= htmlspecialchars($permohonan['fld_matric_no']); ?></td></tr>
  <tr><th>No Telefon</th><td><?= htmlspecialchars($permohonan['fld_phone']); ?></td></tr>
  <tr><th>Email</th><td><?= htmlspecialchars($permohonan['fld_email']); ?></td></tr>
  <tr><th>Jenis Bantuan</th><td><?= htmlspecialchars($permohonan['fld_category']); ?></td></tr>
  <tr><th>Slip Pendapatan</th>
      <td><a href="<?= $permohonan['fld_income_slip']; ?>" target="_blank">Lihat Dokumen</a></td></tr>

  <tr>
    <th>Dokumen Sokongan</th>
    <td>
      <?php if ($permohonan['fld_supporting_doc']): ?>
        <a href="<?= $permohonan['fld_supporting_doc']; ?>" target="_blank">Lihat Dokumen</a>
      <?php else: ?>
        Tiada
      <?php endif; ?>
    </td>
  </tr>
  <tr>
    <th>Tarikh Permohonan</th>
    <td><?= date('Y-m-d', strtotime($permohonan['fld_request_date'])); ?></td>
  </tr>

  <tr>
    <th>Status Semasa</th>
    <td>
      <?php
        $status = $permohonan['fld_status'];
        if ($status === 'diluluskan') echo '<span class="label label-success">Diluluskan</span>';
        elseif ($status === 'ditolak') echo '<span class="label label-warning">Ditolak</span>';
        elseif ($status === 'berjaya') echo '<span class="label label-primary">Berjaya</span>';
        else echo '<span class="label label-info">Sedang diproses</span>';
      ?>
    </td>
  </tr>
</table>


    <div class="mt-4">
      <button class="btn btn-default btn-back" onclick="window.location.href='memproses_permohonan.php'">← Kembali</button>
      <?php if ($permohonan['fld_status'] === 'sedang diproses'): ?>
        <button class="btn btn-success btn-custom" onclick="sahkanPermohonan(<?= $permohonan['fld_request_id']; ?>)">Sahkan</button>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
function sahkanPermohonan(id) {
    Swal.fire({
        title: 'Adakah anda ingin meluluskan permohonan pelajar ini?',
        icon: 'question',
        showCancelButton: true,
        showCloseButton: true, // ✅ ADD THIS LINE
        confirmButtonText: 'Ya, Luluskan',
        cancelButtonText: 'Tidak, Tolak',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545'
    }).then((result) => {
        if (result.isConfirmed || result.dismiss === Swal.DismissReason.cancel) {
            let status = result.isConfirmed ? 'diluluskan' : 'ditolak';
            fetch(`proses_status_permohonan.php?id=${id}&status=${status}`)
                .then(response => response.text())
                .then(() => {
                  Swal.fire({
                    title: 'PERMOHONAN PELAJAR TELAH DIKEMASKINI',
                    html: `<a href='memproses_permohonan.php'>TEKAN DISINI UNTUK KEMBALI</a>`,
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                  }).then(() => {
                    window.location.href = 'memproses_permohonan.php';
                  });
                });
        }
    });
}

</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>

</body>
</html>

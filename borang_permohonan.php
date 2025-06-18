<?php
include_once 'database.php';
session_start();

// Semak login dan role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'pelajar') {
    header("Location: login_pelajar.php");
    exit();
}

$student_id = $_SESSION['user_id'];

$hasSubmitted = false;

// Check if the student has already submitted an application
$check = $conn->prepare("SELECT * FROM tbl_requests WHERE fld_user_id = ? LIMIT 1");
$check->bind_param("i", $student_id); // Correct usage for mysqli
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    $hasSubmitted = true;
}

// Using mysqli here
$stmt = $conn->prepare("SELECT fld_name, fld_matric_no, fld_phone, fld_email FROM tbl_users WHERE fld_user_id = ?");
$stmt->bind_param("i", $student_id); // Correct usage for mysqli
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$draft = $_SESSION['draft'] ?? [];

$name      = $draft['fld_name'] ?? ($user['fld_name'] ?? '');
$matric_no = $draft['fld_matric_no'] ?? ($user['fld_matric_no'] ?? '');
$phone     = $draft['fld_phone'] ?? ($user['fld_phone'] ?? '');
$email     = $draft['fld_email'] ?? ($user['fld_email'] ?? '');

$category  = $draft['fld_category'] ?? '';
$income_slip_path = $draft['fld_income_slip'] ?? '';
$support_doc_path = $draft['fld_supporting_doc'] ?? '';

?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Permohonan Bantuan</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <style>
    @keyframes bounce {
      0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
      }
      40% {
        transform: translateY(-10px);
      }
      60% {
        transform: translateY(-6px);
      }
    }

    body {
      background-color:rgb(255, 255, 255); /* optional fallback */
      background-size: cover;
      background-attachment: fixed;
      background-position: center;
      background-repeat: no-repeat;
      font-family: 'Inter', sans-serif;
      margin: 0;
      padding: 0;
    }
    nav.navbar {
      z-index: 9999;
      position: relative;
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
      background: white; /* keep the white card */
      min-height: 400px; /* Ensures the container has a minimum height */
      box-sizing: border-box; /* Ensure padding is included in the height calculation */
    }
    a[href*="panduan_permohonan.php"]:hover {
      background-color: #138496;
      box-shadow: 0 6px 16px rgba(0,0,0,0.4);
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
    <form action="proses_permohonan.php" method="POST" enctype="multipart/form-data">
      <!-- Notifikasi mesej berjaya atau ralat -->
      <?php if (isset($_SESSION['success_msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <?= $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
      <?php endif; ?>

      <?php if (isset($_SESSION['error_msg'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?= $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
      <?php endif; ?>

      <div class="mb-3" style="display: flex; align-items: center; justify-content: space-between;">
        <h2 class="mb-0" style="font-weight: bold;">Borang Permohonan Bantuan</h2>

        <?php if ($hasSubmitted): ?>
          <div style="display: flex; gap: 5px;">
            <a href="lihat_permohonan.php" class="btn btn-info btn-sm">Lihat</a>
            <a href="edit_permohonan.php" class="btn btn-warning btn-sm">Ubah</a>
          </div>
        <?php endif; ?>
      </div>

      <p><i>Sila lengkapkan maklumat berikut untuk memohon.</i></p>
      <h3 class="text-primary mt-4">Maklumat Pelajar</h3>

      <input type="hidden" name="fld_user_id" value="<?php echo $student_id; ?>">

      <div class="form-group">
        <label>Nama:</label>
        <input type="text" name="fld_name" class="form-control" required maxlength="300" pattern="^(\b\w+\b[\s\r\n]*){1,50}$" placeholder="Contoh: Nor Amalia Binti Nor Bani" value="<?= htmlspecialchars($name) ?>">
      </div>

      <div class="form-group">
        <label>No Matrik:</label>
        <input type="text" name="fld_matric_no" class="form-control" required pattern="^A\d{6}$" placeholder="Contoh: A123456" value="<?= htmlspecialchars($matric_no) ?>">
      </div>

      <div class="form-group">
        <label>No Telefon:</label>
        <input type="text" name="fld_phone" class="form-control" required pattern="^01\d{8,9}$" placeholder="Contoh: 01123345672" maxlength="11" inputmode="numeric" value="<?= htmlspecialchars($phone) ?>">
      </div>

      <div class="form-group">
        <label>Email:</label>
        <input type="email" name="fld_email" class="form-control" required pattern="^[a-zA-Z0-9._%+-]+@siswa\.ukm\.edu\.my$" placeholder="Contoh: nomatrik@siswa.ukm.edu.my" value="<?= htmlspecialchars($email) ?>">
      </div>

      <div class="form-group">
        <label>Jenis Bantuan:</label>
        <select name="fld_category" class="form-control" required>
          <option value="" disabled selected>-- Pilih Bantuan --</option>
          <option value="Peralatan Digital" <?php if($category == "Peralatan Digital") echo "selected"; ?>>Peralatan Digital</option>
          <option value="Bahan Pembelajaran" <?php if($category == "Bahan Pembelajaran") echo "selected"; ?>>Bahan Pembelajaran</option>
          <option value="Keperluan Diri" <?php if($category == "Keperluan Diri") echo "selected"; ?>>Keperluan Diri</option>
          <option value="Peralatan Digital + Bahan Pembelajaran" <?php if($category == "Peralatan Digital + Bahan Pembelajaran") echo "selected"; ?>>Peralatan Digital + Bahan Pembelajaran</option>
          <option value="Bahan Pembelajaran + Keperluan Diri" <?php if($category == "Bahan Pembelajaran + Keperluan Diri") echo "selected"; ?>>Bahan Pembelajaran + Keperluan Diri</option>
          <option value="Peralatan Digital + Keperluan Diri" <?php if($category == "Peralatan Digital + Keperluan Diri") echo "selected"; ?>>Peralatan Digital + Keperluan Diri</option>
          <option value="Peralatan Digital + Bahan Pembelajaran + Keperluan Diri" <?php if($category == "Peralatan Digital + Bahan Pembelajaran + Keperluan Diri") echo "selected"; ?>>Peralatan Digital + Bahan Pembelajaran + Keperluan Diri</option>
        </select>
      </div>

      <div class="form-group">
        <label>Slip Pendapatan (PDF sahaja):</label>
        <?php if (!empty($income_slip_path)): ?>
          <p>
            <a href="<?= $income_slip_path ?>" target="_blank">📎 Sudah dimuat naik (Lihat)</a>
            <a href="remove_draft_file.php?type=income" style="color:red; font-weight:bold; margin-left:10px;">❌ Buang</a>
          </p>
        <?php endif; ?>
        <input type="file" name="fld_income_slip" class="form-control-file" accept=".pdf" <?= empty($income_slip_path) ? 'required' : '' ?>>
      </div>

      <div class="form-group">
        <label>Dokumen Sokongan (PDF sahaja):</label>
        <?php if (!empty($support_doc_path)): ?>
          <p>
            <a href="<?= $support_doc_path ?>" target="_blank">📎 Sudah dimuat naik (Lihat)</a>
            <a href="remove_draft_file.php?type=support" style="color:red; font-weight:bold; margin-left:10px;">❌ Buang</a>
          </p>
        <?php endif; ?>
        <input type="file" name="fld_supporting_doc" class="form-control-file" accept=".pdf" <?= empty($support_doc_path) ? 'required' : '' ?>>
      </div>

      <div class="form-group d-flex justify-content-between align-items-center">
        <div>
          <button type="submit" name="save_draft" class="btn btn-primary">Simpan Draf</button>
          <button type="submit" name="submit" class="btn btn-success" <?= $hasSubmitted ? 'disabled' : '' ?>>Hantar Permohonan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
const MAX_FILE_SIZE_MB = 2;

function checkFileSize(file, maxMB) {
  return file.size <= maxMB * 1024 * 1024;
}

document.querySelector('form').addEventListener('submit', function(e) {
  const incomeSlip = document.querySelector('input[name="fld_income_slip"]');
  const supportDoc = document.querySelector('input[name="fld_supporting_doc"]');

  const isPDF = file => file && file.name.split('.').pop().toLowerCase() === 'pdf';

  if (incomeSlip.files.length) {
    if (!isPDF(incomeSlip.files[0])) {
      alert("Slip Pendapatan mesti dalam format PDF.");
      e.preventDefault();
      return;
    }
    if (!checkFileSize(incomeSlip.files[0], MAX_FILE_SIZE_MB)) {
      alert("Slip Pendapatan melebihi had 2MB.");
      e.preventDefault();
      return;
    }
  }

  if (supportDoc.files.length) {
    if (!isPDF(supportDoc.files[0])) {
      alert("Dokumen Sokongan mesti dalam format PDF.");
      e.preventDefault();
      return;
    }
    if (!checkFileSize(supportDoc.files[0], MAX_FILE_SIZE_MB)) {
      alert("Dokumen Sokongan melebihi had 2MB.");
      e.preventDefault();
      return;
    }
  }
});
</script>


<a href="panduan_permohonan.php" target="_blank"
   style="position: fixed; bottom: 25px; right: 25px; z-index: 9999;
          background-color: #17a2b8; color: white; padding: 12px 20px;
          text-decoration: none; border-radius: 30px; font-weight: bold;
          box-shadow: 0 4px 12px rgba(0,0,0,0.3); transition: all 0.3s ease;
          animation: bounce 2s infinite;">
  📘 Cara Mohon
</a>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>

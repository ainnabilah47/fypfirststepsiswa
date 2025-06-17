<?php
include_once 'database.php';
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'pelajar') {
    header("Location: login_pelajar.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch current request data
$query = "SELECT * FROM tbl_requests WHERE fld_user_id = ? ORDER BY fld_request_id DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$request = $result->fetch_assoc();

$status = $request['fld_status'] ?? '';
$edit_deadline = isset($request['fld_edit_dateline']) && $request['fld_edit_dateline']
    ? new DateTime($request['fld_edit_dateline'])
    : new DateTime(); // fallback to now

$now = new DateTime();

// ✅ Apply logic here: only allow edit if status = 'sedang diproses' and before deadline
$allow_edit = ($status === 'sedang diproses') && ($now <= $edit_deadline);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!$allow_edit) {
        die("Permohonan tidak boleh dikemaskini selepas tarikh akhir atau selepas diproses.");
    }

    // Backend file validation (optional but recommended)
    if ($_FILES['fld_income_slip']['name']) {
        if ($_FILES['fld_income_slip']['type'] !== 'application/pdf') {
            die("Slip Pendapatan mesti dalam format PDF.");
        }
        if ($_FILES['fld_income_slip']['size'] > 2 * 1024 * 1024) {
            die("Slip Pendapatan melebihi had 2MB.");
        }
    }

    if ($_FILES['fld_supporting_doc']['name']) {
        if ($_FILES['fld_supporting_doc']['type'] !== 'application/pdf') {
            die("Dokumen Sokongan mesti dalam format PDF.");
        }
        if ($_FILES['fld_supporting_doc']['size'] > 2 * 1024 * 1024) {
            die("Dokumen Sokongan melebihi had 2MB.");
        }
    }

    // Handle form submission to update request
    $request_id = $_POST['fld_request_id'];
    $name = $_POST['fld_name'];
    $matric_no = $_POST['fld_matric_no'];
    $phone = $_POST['fld_phone'];
    $email = $_POST['fld_email'];
    $category = $_POST['fld_category'];

    // Handle file uploads (if any)
    $income_slip = $_FILES['fld_income_slip']['name'] ? 'uploads/' . $_FILES['fld_income_slip']['name'] : $request['fld_income_slip'];
    $supporting_doc = $_FILES['fld_supporting_doc']['name'] ? 'uploads/' . $_FILES['fld_supporting_doc']['name'] : $request['fld_supporting_doc'];

    if ($_FILES['fld_income_slip']['name']) {
        move_uploaded_file($_FILES['fld_income_slip']['tmp_name'], $income_slip);
    }

    if ($_FILES['fld_supporting_doc']['name']) {
        move_uploaded_file($_FILES['fld_supporting_doc']['tmp_name'], $supporting_doc);
    }

    $update_query = "UPDATE tbl_requests SET 
                    fld_name = ?, 
                    fld_matric_no = ?, 
                    fld_phone = ?, 
                    fld_email = ?, 
                    fld_category = ?, 
                    fld_income_slip = ?, 
                    fld_supporting_doc = ? 
                    WHERE fld_request_id = ?";

    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("sssssssi", $name, $matric_no, $phone, $email, $category, $income_slip, $supporting_doc, $request_id);
    $update_stmt->execute();

    if ($update_stmt->error) {
        echo "SQL Error: " . $update_stmt->error;
        exit();
    }

    $_SESSION['success_msg'] = "Ubah suai maklumat permohonan berjaya!";

    header("Location: lihat_permohonan.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Ubah Suai Permohonan</title>
  <link rel="stylesheet" href="css/style.css">
  <title>Ubah Suai Permohonan</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <style>
/* Common Background */
body {
  background-image: url('background.jpg');
  background-size: cover;
  background-attachment: fixed;
  background-position: center;
  font-family: 'Inter', sans-serif;
}

/* Common Container */
.container {
  max-width: 960px; /* Adjust as per your design */
  margin: 0 auto;
  padding: 20px;
}

/* Form Container */
.form-container {
  background: #ffffff; /* Slight transparency */
  margin: 30px auto;
  padding: 30px;
  border-radius: 10px;
  max-width: 750px;
  box-shadow: 0 6px 25px rgba(0, 0, 0, 0.25);
}

h2 {
  font-weight: bold;
  color: #222;
  margin-bottom: 10px;
}

/* Status Badge Styling */
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

/* Button Styling */

.btn-back {
  margin: 10px;
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

<?php if (isset($_SESSION['success_msg'])): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
  </div>
<?php endif; ?>

  <div class="form-container">
    <h2>Ubah Suai Permohonan</h2>

    <?php if ($allow_edit): ?>
    <p>Ubah maklumat permohonan anda di bawah.</p>
    <p class="text-muted">
      Anda boleh menyunting permohonan ini sehingga: <strong><?= $edit_deadline->format('Y-m-d') ?></strong>
    </p>

    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="fld_request_id" value="<?= $request['fld_request_id'] ?>">

      <div class="form-group">
        <label>Nama:</label>
        <input type="text" name="fld_name" value="<?= htmlspecialchars($request['fld_name']) ?>" class="form-control" required>
      </div>

      <div class="form-group">
        <label>No Matrik:</label>
        <input type="text" name="fld_matric_no" value="<?= htmlspecialchars($request['fld_matric_no']) ?>" class="form-control" required>
      </div>

      <div class="form-group">
        <label>No Telefon:</label>
        <input type="text" name="fld_phone" value="<?= htmlspecialchars($request['fld_phone']) ?>" class="form-control" required>
      </div>

      <div class="form-group">
        <label>Email:</label>
        <input type="email" name="fld_email" value="<?= htmlspecialchars($request['fld_email']) ?>" class="form-control" required>
      </div>

      <div class="form-group">
        <label>Jenis Bantuan:</label>
        <select name="fld_category" class="form-control" required>
        <option value="" disabled selected>-- Pilih Bantuan --</option>
        <option value="Peralatan Digital" <?= $request['fld_category'] == 'Peralatan Digital' ? 'selected' : '' ?>>Peralatan Digital</option>
          <option value="Bahan Pembelajaran" <?= $request['fld_category'] == 'Bahan Pembelajaran' ? 'selected' : '' ?>>Bahan Pembelajaran</option>
          <option value="Keperluan Diri" <?= $request['fld_category'] == 'Keperluan Diri' ? 'selected' : '' ?>>Keperluan Diri</option>
          <option value="Peralatan Digital + Bahan Pembelajaran" <?= $request['fld_category'] == 'Peralatan Digital + Bahan Pembelajaran' ? 'selected' : '' ?>>Peralatan Digital + Bahan Pembelajaran</option>
          <option value="Bahan Pembelajaran + Keperluan Diri" <?= $request['fld_category'] == 'Bahan Pembelajaran + Keperluan Diri' ? 'selected' : '' ?>>Bahan Pembelajaran + Keperluan Diri</option>
          <option value="Peralatan Digital + Keperluan Diri" <?= $request['fld_category'] == 'Peralatan Digital + Keperluan Diri' ? 'selected' : '' ?>>Peralatan Digital + Keperluan Diri</option>
          <option value="Peralatan Digital + Bahan Pembelajaran + Keperluan Diri" <?= $request['fld_category'] == 'Peralatan Digital + Bahan Pembelajaran + Keperluan Diri' ? 'selected' : '' ?>>Peralatan Digital + Bahan Pembelajaran + Keperluan Diri</option>
        </select>
      </div>

      <div class="form-group">
        <label>Slip Pendapatan (PDF):</label>
        <input type="file" name="fld_income_slip" class="form-control">
        <?php if ($request['fld_income_slip']): ?>
          <p>Slip Semasa: <a href="<?= $request['fld_income_slip'] ?>" target="_blank">Lihat Slip</a></p>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label>Dokumen Sokongan (PDF):</label>
        <input type="file" name="fld_supporting_doc" class="form-control">
        <?php if ($request['fld_supporting_doc']): ?>
          <p>Dokumen Semasa: <a href="<?= $request['fld_supporting_doc'] ?>" target="_blank">Lihat Dokumen</a></p>
        <?php endif; ?>
      </div>

      <!-- Kembali button first, followed by Save -->
      <div class="form-group mt-4">
      <button class="btn btn-default btn-back" onclick="window.location.href='lihat_permohonan.php'">← Kembali</button>
        <button type="submit" class="btn btn-primary ml-2">Simpan Perubahan</button>
      </div>

    </form>

    <?php else: ?>
    <div class="alert alert-warning mt-4">
      <strong>Maaf!</strong> Anda tidak boleh lagi menyunting permohonan ini. Tarikh akhir suntingan ialah: <strong><?= $edit_deadline->format('Y-m-d') ?></strong>.
    </div>
  <button class="btn btn-default btn-back" onclick="window.location.href='borang_permohonan.php'">← Kembali</button>
  <?php endif; ?>
</div>
  </div>

</div>

<script>
const MAX_FILE_SIZE_MB = 2;

function isPDF(file) {
  return file && file.name.split('.').pop().toLowerCase() === 'pdf';
}

function checkFileSize(file, maxMB) {
  return file.size <= maxMB * 1024 * 1024;
}

document.querySelector('form').addEventListener('submit', function(e) {
  const incomeSlip = document.querySelector('input[name="fld_income_slip"]');
  const supportDoc = document.querySelector('input[name="fld_supporting_doc"]');

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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>

</body>
</html>

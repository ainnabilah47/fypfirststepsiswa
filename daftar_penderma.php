<?php
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');

include_once 'database.php'; // Database connection

// Ensure only admin can access the page
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'pentadbir') {
    header("Location: login_staff.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // Get the donations and quantities
    $bantuanList = $_POST['bantuan'];
    $kuantitiList = $_POST['kuantiti'];

    // Simple validation for password match
    if ($password !== $confirm) {
        echo "<script>alert('Kata laluan tidak sepadan!'); window.history.back();</script>";
        exit();
    }

    // Check if username already exists
    $checkUsername = $conn->prepare("SELECT COUNT(*) FROM tbl_users WHERE fld_username = ?");
    $checkUsername->bind_param("s", $username);
    $checkUsername->execute();
    $checkUsernameResult = $checkUsername->get_result();
    $row = $checkUsernameResult->fetch_row();

    if ($row[0] > 0) {
        // Display error message if username is taken
        $_SESSION['error'] = "Penderma telah didaftarkan";
        echo "<script>window.history.back();</script>";
        exit();
    }

    // Insert user into tbl_users if username is unique
    $insertUser = $conn->prepare("INSERT INTO tbl_users (fld_name, fld_username, fld_email, fld_phone, fld_password, fld_role) 
                                  VALUES (?, ?, ?, ?, ?, 'penderma')");
    $insertUser->bind_param("sssss", $name, $username, $email, $phone, $password);

    if ($insertUser->execute()) {
        $newUserId = $insertUser->insert_id;

        // Insert donations into tbl_donations
        $stmtDonation = $conn->prepare("INSERT INTO tbl_donations (fld_donor_id, fld_category, fld_quantity, fld_donation_date) VALUES (?, ?, ?, NOW())");

        // Loop through the donations
        for ($i = 0; $i < count($bantuanList); $i++) {
            $category = $bantuanList[$i];
            $quantity = (int)$kuantitiList[$i];
            $stmtDonation->bind_param("isi", $newUserId, $category, $quantity);
            $stmtDonation->execute();

            // Update inventory (add the donation quantity to inventory)
            $checkInventoryStmt = $conn->prepare("SELECT fld_quantity FROM tbl_inventory WHERE fld_category = ?");
            $checkInventoryStmt->bind_param("s", $category);
            $checkInventoryStmt->execute();
            $checkInventoryStmt->store_result();

            if ($checkInventoryStmt->num_rows > 0) {
                // If item exists in inventory, update the quantity
                $updateInventoryStmt = $conn->prepare("UPDATE tbl_inventory SET fld_quantity = fld_quantity + ? WHERE fld_category = ?");
                $updateInventoryStmt->bind_param("is", $quantity, $category);
                $updateInventoryStmt->execute();
            } else {
                // If item doesn't exist, insert a new entry in inventory
                $insertInventoryStmt = $conn->prepare("INSERT INTO tbl_inventory (fld_category, fld_quantity, fld_donor_id, fld_last_updated) VALUES (?, ?, ?, NOW())");
                $insertInventoryStmt->bind_param("sis", $category, $quantity, $newUserId);
                $insertInventoryStmt->execute();
            }
        }

        echo "<script>alert('Pendaftaran berjaya! Penajaan baru telah ditambah dan inventori dikemaskini.'); window.location.href='daftar_penderma.php';</script>";
    } else {
        echo "<script>alert('Ralat semasa mendaftar penderma!'); window.history.back();</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Daftar Akaun Penderma</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
  <style>
    body {
      background: url('background.jpg') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', sans-serif;
    }
    .form-container {
      background: rgba(255, 255, 255, 0.98);
      padding: 40px;
      margin: 60px auto;
      border-radius: 12px;
      max-width: 700px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.25);
    }
    h2 {
      font-weight: bold;
      margin-bottom: 30px;
      text-align: center;
      color: #333;
    }
    .form-label {
      font-weight: 600;
      margin-top: 10px;
    }
    .btn-add {
      margin-top: 10px;
    }
    .btn-submit {
      margin-top: 30px;
      width: 100%;
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
  <div class="form-container">
    <h2>Daftar Akaun Penderma</h2>
    
    <!-- Display error message if any -->
    <?php
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']); // Clear the error message after displaying it
    }
    ?>

    <form method="POST" action="proses_daftar_penderma.php">
      <div class="form-group">
          <label class="form-label">Nama Organisasi</label>
          <input type="text" name="name" class="form-control" required placeholder="Contoh: Yayasan XYZ">
      </div>
      <div class="form-group">
          <label class="form-label">Nama pengguna</label>
          <input type="text" name="username" class="form-control" required placeholder="Contoh: pengguna123">
      </div>
      <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required placeholder="Contoh: email@domain.com">
      </div>
      <div class="form-group">
          <label class="form-label">No Telefon</label>
          <input type="text" name="phone" class="form-control" required placeholder="Contoh: 01123456789">
      </div>
      <div class="form-group">
          <label class="form-label">Kata Laluan</label>
          <input type="password" name="password" class="form-control" required placeholder="Masukkan kata laluan">
      </div>
      <div class="form-group">
          <label class="form-label">Pengesahan Kata Laluan</label>
          <input type="password" name="confirm_password" class="form-control" required placeholder="Sahkan kata laluan">
      </div>

      <hr>
      <h4 style="font-weight:bold;">Jenis Bantuan & Kuantiti</h4>
      <div id="bantuan-wrapper">
          <div class="row bantuan-row">
              <div class="col-md-7">
                  <select name="bantuan[]" class="form-control" required>
                      <option value="">-- Pilih Jenis Bantuan --</option>
                      <option value="Peralatan Digital">Peralatan Digital</option>
                      <option value="Bahan Pembelajaran">Bahan Pembelajaran</option>
                      <option value="Keperluan Diri">Keperluan Diri</option>
                      <option value="Peralatan Digital + Bahan Pembelajaran">Peralatan Digital + Bahan Pembelajaran</option>
                      <option value="Bahan Pembelajaran + Keperluan Diri">Bahan Pembelajaran + Keperluan Diri</option>
                      <option value="Peralatan Digital + Keperluan Diri">Peralatan Digital + Keperluan Diri</option>
                      <option value="Peralatan Digital + Bahan Pembelajaran + Keperluan Diri">Peralatan Digital + Bahan Pembelajaran + Keperluan Diri</option>
                  </select>
              </div>
              <div class="col-md-4">
                  <input type="number" name="kuantiti[]" class="form-control" placeholder="Kuantiti" required>
              </div>
          </div>
      </div>
      <button type="button" class="btn btn-default btn-add" onclick="addBantuan()">+ Tambah Jenis Bantuan</button>

      <button type="submit" class="btn btn-primary btn-submit">Daftar</button>
    </form>
  </div>
</div>

<script>
    function addBantuan() {
        const wrapper = document.getElementById('bantuan-wrapper');
        const row = document.createElement('div');
        row.classList.add('row', 'bantuan-row');
        row.innerHTML = ` 
            <div class="col-md-7">
                <select name="bantuan[]" class="form-control" required>
                    <option value="">-- Pilih Jenis Bantuan --</option>
                    <option value="Peralatan Digital">Peralatan Digital</option>
                    <option value="Bahan Pembelajaran">Bahan Pembelajaran</option>
                    <option value="Keperluan Diri">Keperluan Diri</option>
                    <option value="Peralatan Digital + Bahan Pembelajaran">Peralatan Digital + Bahan Pembelajaran</option>
                    <option value="Bahan Pembelajaran + Keperluan Diri">Bahan Pembelajaran + Keperluan Diri</option>
                    <option value="Peralatan Digital + Keperluan Diri">Peralatan Digital + Keperluan Diri</option>
                    <option value="Peralatan Digital + Bahan Pembelajaran + Keperluan Diri">Peralatan Digital + Bahan Pembelajaran + Keperluan Diri</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="number" name="kuantiti[]" class="form-control" placeholder="Kuantiti" required>
            </div>
        `;
        wrapper.appendChild(row);
    }
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>

</body>
</html>

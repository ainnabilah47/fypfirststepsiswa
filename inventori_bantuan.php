<?php
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');
include_once 'database.php';

// Ensure only admin can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'pentadbir') {
    header("Location: login_staff.php");
    exit();
}

// Handle inventory updates (increase or decrease quantity)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $item_id = $_GET['id'];
    $action = $_GET['action'];

    // Check the current quantity in the inventory
    $checkQuery = "SELECT fld_quantity FROM tbl_inventory WHERE fld_item_id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("i", $item_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $inventory = $result->fetch_assoc();

    // Update the inventory based on the action
    if ($inventory) {
        $currentQuantity = $inventory['fld_quantity'];
        $newQuantity = ($action === 'tambah') ? $currentQuantity + 1 : ($currentQuantity > 0 ? $currentQuantity - 1 : $currentQuantity);
        
        // Update inventory
        $updateQuery = "UPDATE tbl_inventory SET fld_quantity = ? WHERE fld_item_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ii", $newQuantity, $item_id);
        $updateStmt->execute();

        echo json_encode(["status" => "success", "message" => "Stok telah dikemaskini!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Stok tidak wujud!"]);
    }

    exit(); // Stop further script execution
}

// Query to get inventory and donor info
$query = "SELECT i.*, u.fld_name AS donor_name 
          FROM tbl_inventory i
          JOIN tbl_users u ON i.fld_donor_id = u.fld_user_id
          ORDER BY i.fld_last_updated DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Inventori Bantuan Pelajar</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: url('background.jpg') no-repeat center center fixed;
      background-size: cover;
    }
    .container-box {
      background: #ffffff;
      padding: 30px;
      margin-top: 60px;
      border-radius: 15px;
      box-shadow: 0 6px 25px rgba(0,0,0,0.25);
    }
    h2 {
      font-weight: bold;
      margin-bottom: 10px;
    }
    .table th {
      background-color: #007BFF;
      color: white;
    }
    .btn-add {
      background-color: #28a745;
      color: white;
    }
    .btn-minus {
      background-color: #ffc107;
      color: white;
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
    <h2>Inventori Bantuan Pelajar</h2>
    <h3 class="text-primary fw-semibold">Senarai Inventori Bantuan</h3>
    <p><i>Klik untuk membuat tindakan pada bantuan.</i></p>

    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>ID Bantuan</th>
          <th>Jenis Bantuan</th>
          <th>Kuantiti Stok</th>
          <th>Penderma</th>
          <th>Tarikh Kemaskini</th>
          <th>Tindakan</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= 'B' . str_pad($row['fld_item_id'], 4, '0', STR_PAD_LEFT) ?></td>
            <td><?= htmlspecialchars($row['fld_category']) ?></td>
            <td><?= $row['fld_quantity'] ?></td>
            <td><?= htmlspecialchars($row['donor_name']) ?></td>
            <td><?= date('Y-m-d', strtotime($row['fld_last_updated'])) ?></td>
            <td>
              <button onclick="updateStock(<?= $row['fld_item_id'] ?>, 'tambah')" class="btn btn-sm btn-add">Tambah</button>
              <button onclick="updateStock(<?= $row['fld_item_id'] ?>, 'kurang')" class="btn btn-sm btn-minus">Kurang</button>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
function updateStock(id, action) {
  fetch(`inventori_bantuan.php?action=${action}&id=${id}`)
    .then(response => response.json())
    .then(data => {
      Swal.fire({
        title: 'NOTIFIKASI',
        text: data.message,
        icon: data.status, // success or error
        confirmButtonText: 'OK'
      }).then(() => location.reload());
    });
}
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>

</body>
</html>

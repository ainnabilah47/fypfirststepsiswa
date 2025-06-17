<?php
session_start();
include_once 'database.php';
date_default_timezone_set('Asia/Kuala_Lumpur');

// Ensure logged-in donor can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'penderma') {
    header("Location: login_staff.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Donor's user ID

// Fetch the donation history of the logged-in donor
$query = "SELECT fld_category, fld_quantity, fld_donation_date FROM tbl_donations WHERE fld_donor_id = ? ORDER BY fld_donation_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Insert donations into tbl_donations (when form is submitted)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bantuanList = $_POST['bantuan'];  // Array of selected donations
    $kuantitiList = $_POST['kuantiti'];  // Array of corresponding quantities

    // Insert donations into tbl_donations
    $stmtDonation = $conn->prepare("INSERT INTO tbl_donations (fld_donor_id, fld_category, fld_quantity, fld_donation_date) VALUES (?, ?, ?, NOW())");

    for ($i = 0; $i < count($bantuanList); $i++) {
        $category = $bantuanList[$i];
        $qty = (int)$kuantitiList[$i];
        
        // Insert into tbl_donations (donation history)
        $stmtDonation->bind_param("isi", $user_id, $category, $qty);
        $stmtDonation->execute();
        
        // Update the inventory for the donated items
        $checkInventoryStmt = $conn->prepare("SELECT fld_quantity FROM tbl_inventory WHERE fld_category = ?");
        $checkInventoryStmt->bind_param("s", $category);
        $checkInventoryStmt->execute();
        $checkInventoryStmt->store_result();
        
        if ($checkInventoryStmt->num_rows > 0) {
            // If item exists in inventory, update the quantity
            $updateInventoryStmt = $conn->prepare("UPDATE tbl_inventory SET fld_quantity = fld_quantity + ? WHERE fld_category = ?");
            $updateInventoryStmt->bind_param("is", $qty, $category);
            $updateInventoryStmt->execute();
        } else {
            // If item doesn't exist, insert a new entry in inventory
            $insertInventoryStmt = $conn->prepare("INSERT INTO tbl_inventory (fld_category, fld_quantity, fld_donor_id, fld_last_updated) VALUES (?, ?, ?, NOW())");
            $insertInventoryStmt->bind_param("sis", $category, $qty, $user_id);
            $insertInventoryStmt->execute();
        }
    }

    echo "<script>alert('Penajaan baru telah berjaya ditambah dan inventori telah dikemaskini!'); window.location.href='dashboard_penderma.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Tambah Penajaan Baru</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: url('background.jpg') no-repeat center center fixed;
            background-size: cover;
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

        h4 {
            font-weight: bold;
    
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

        .back-btn {
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
        }

        .back-btn:hover {
            background-color: #eef6ff;
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

    <div class="form-container">
        <h2 class="text-center">Tambah Bantuan Baru</h2>
        <form method="POST">
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

            <button type="submit" class="btn btn-primary btn-submit">Tambah</button>
        </form>

        <!-- Donation Summary Table -->
        <h4 class="text-center" style="margin-top: 40px;">Sejarah Senarai Bantuan yang Telah Diberi</h4>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Jenis Bantuan</th>
                    <th>Kuantiti</th>
                    <th>Tarikh Penajaan</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['fld_category']); ?></td>
                        <td><?= htmlspecialchars($row['fld_quantity']); ?></td>
                        <td><?= date('Y-m-d', strtotime($row['fld_donation_date'])); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
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

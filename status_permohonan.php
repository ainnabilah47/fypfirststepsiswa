<?php
session_start();
include_once 'database.php';

// Ensure logged-in student can access the page
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'pelajar') {
    header("Location: login_pelajar.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Student's user ID

// Query to get the most recent request and donor details (if any)
$query = "
    SELECT r.*, u.fld_name AS donor_name
    FROM tbl_requests r
    LEFT JOIN tbl_sponsorships s ON r.fld_request_id = s.fld_request_id
    LEFT JOIN tbl_users u ON s.fld_sponsor_id = u.fld_user_id
    WHERE r.fld_user_id = '$user_id'
    ORDER BY r.fld_request_id DESC
    LIMIT 1";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Status Permohonan Bantuan</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <style>
    body {
      background-size: cover;
      background-attachment: fixed;
      background-position: center;
      background-repeat: no-repeat;
      font-family: 'Inter', sans-serif;
      margin: 0;
      padding: 0;
    }

    .status-container {
      background: #ffffff;
      margin: 80px auto;
      padding: 40px;
      border-radius: 12px;
      max-width: 750px;
      box-shadow: 0 6px 25px rgba(0,0,0,0.25);
    }

    h2 {
      font-weight: bold;
      color: #222;
      margin-bottom: 10px;
    }

    .status-label {
      font-size: 16px;
      color: #666;
      margin-bottom: 5px;
    }

    .info-box {
      border: 1px solid #ccc;
      padding: 10px 15px;
      margin-bottom: 15px;
      background-color: #f9f9f9;
      font-weight: 500;
    }

    .status-message {
      font-size: 18px;
      font-weight: bold;
      color: #007bff;
      margin-bottom: 25px;
    }

    .alert {
      font-size: 16px;
    }

    .progress-bar-default {
    background-color: #e0e0e0;
    color: #555;
    }

    .timeline-container .badge {
      transition: all 0.3s ease-in-out;
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
  <div class="status-container">
    <h2>Status Permohonan Bantuan</h2>
    <p class="status-label"><i>Semak status permohonan bantuan anda di sini.</i></p>

    <?php if ($data): ?>
      <div class="status-message">
        <?php
          switch ($data['fld_status']) {
            case 'diluluskan':
              echo "Tahniah, permohonan anda diluluskan dan sedang disaring oleh penderma.";
              break;
            case 'ditolak':
              echo "Maaf, permohonan anda ditolak.";
              break;
            case 'berjaya':
              echo "Tahniah, permohonan anda diluluskan.";
              break;
            case 'sedang diproses':
            default:
              echo "Permohonan anda sedang diproses.";
              break;
          }
        ?>
      </div>

      <?php if ($data['fld_status'] === 'berjaya'): ?>
        <h4>Maklumat Permohonan</h4>
        <label>Nama</label>
        <div class="info-box"><?php echo htmlspecialchars($data['fld_name']); ?></div>

        <label>No Matrik</label>
        <div class="info-box"><?php echo htmlspecialchars($data['fld_matric_no']); ?></div>

        <label>Jenis Bantuan</label>
        <div class="info-box"><?php echo htmlspecialchars($data['fld_category']); ?></div>

        <!-- Display the donor name -->
        <?php if ($data['donor_name']): ?>
          <label>Penderma</label>
          <div class="info-box"><?php echo htmlspecialchars($data['donor_name']); ?></div>
        <?php endif; ?>
      <?php endif; ?>
    <?php else: ?>
      <div class="alert alert-warning mt-4">
        Tiada permohonan direkodkan untuk akaun anda.
      </div>
    <?php endif; ?>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>

</body>
</html>

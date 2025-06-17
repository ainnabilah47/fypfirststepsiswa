<?php
session_start();
include('database.php');

// Ensure only donor can access the page
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'penderma') {
    header("Location: login_staff.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$donor_name = $_SESSION['username'];

// Connect PDO
$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 1. Count the number of distinct students who have been sponsored and marked as 'berjaya'
$stmt = $conn->prepare("
    SELECT COUNT(DISTINCT r.fld_user_id) AS total 
    FROM tbl_sponsorships s
    JOIN tbl_requests r ON s.fld_request_id = r.fld_request_id
    WHERE s.fld_sponsor_id = ? AND r.fld_status = 'berjaya'
");
$stmt->execute([$user_id]);
$totalSponsored = $stmt->fetchColumn();

// Get unique categories sponsored by the donor
$catStmt = $conn->prepare("SELECT DISTINCT r.fld_category 
                           FROM tbl_requests r 
                           JOIN tbl_sponsorships s ON r.fld_request_id = s.fld_request_id 
                           WHERE s.fld_sponsor_id = ?");
$catStmt->execute([$user_id]);
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);

// 2. Get the count of successful sponsorships per category
$data = [];
foreach ($categories as $cat) {
    $query = $conn->prepare("
        SELECT COUNT(*) 
        FROM tbl_sponsorships s
        JOIN tbl_requests r ON s.fld_request_id = r.fld_request_id
        WHERE s.fld_sponsor_id = ? 
          AND r.fld_category = ? 
          AND r.fld_status = 'berjaya'
    ");
    $query->execute([$user_id, $cat]);
    $data[$cat] = $query->fetchColumn();
}

?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Statistik Penajaan</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      background: url('background.jpg') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Inter', sans-serif;
    }
    .container-box {
      background: #ffffff;
      padding: 30px;
      margin-top: 60px;
      border-radius: 15px;
      box-shadow: 0 6px 25px rgba(0,0,0,0.25);
    }
    h2, h4 {
      font-weight: bold;
    }
    canvas {
      background: #fff;
    }
   .card-stat {
      background: #f7f9fc;
      padding: 25px;
      border-left: 8px solid #007bff;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      margin-bottom: 40px; /* ✅ This adds space below the card */
    }
    .legend-container {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 15px;
      margin-top: 10px;
    }

    .legend-item {
      display: flex;
      align-items: center;
      font-size: 14px;
    }

    .legend-color-box {
      width: 15px;
      height: 15px;
      margin-right: 8px;
      border-radius: 3px;
    }
    .chart-wrapper {
      display: flex;
      justify-content: center;
    }
    form button {
     margin: 0 10px;
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
    <h2 class="text-primary mb-4">Statistik Penajaan Anda</h2>

    <!-- Card for Total Sponsored Students -->
    <div class="mb-3 card-stat">
      <h4>Jumlah Pelajar Ditaja: <span class="text-success"><?= $totalSponsored ?></span></h4>
    </div>

   <!-- Bar Chart and Custom Legend Centered -->
    <div class="chart-wrapper text-center">
      <div style="display: inline-block; max-width: 600px; width: 100%;">
      <div style="max-width:600px; margin:auto">
        <canvas id="barChart" width="400" height="200"></canvas>
      </div>
        <div id="customLegend" class="legend-container mt-3"></div>
      </div>
    </div>

<!-- Add margin-top and padding for spacing -->
<div class="text-center" style="margin-top: 40px; padding-top: 10px;">
  <form method="POST" action="export_statistik.php" class="d-inline-block">
    <button type="submit" name="export_csv" class="btn btn-success" style="margin-right: 20px;">Muat Turun CSV</button>
    <button type="submit" name="export_pdf" class="btn btn-primary">Muat Turun PDF</button>
  </form>
</div>


<script>
    var ctx = document.getElementById('barChart').getContext('2d');
    var labels = <?= json_encode(array_keys($data)) ?>;
    var values = <?= json_encode(array_values($data)) ?>;
    var colors = ['#FF5733', '#33FF57', '#3357FF', '#FF33A6', '#FFD733', '#33FFBD', '#7D33FF'];

    var barColors = [];
    for (var i = 0; i < labels.length; i++) {
        barColors.push(colors[i % colors.length]);
    }

    var barChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Jumlah Permohonan',
            data: values,
            backgroundColor: barColors,
            borderColor: '#0056b3',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false // We are using a custom legend
            },
            title: {
                display: true,
                text: 'Penajaan Anda Mengikut Kategori Bantuan',
                font: {
                    size: 18
                },
                padding: {
                    top: 10,
                    bottom: 20
                }
            },
            tooltip: {
                backgroundColor: '#fff',
                titleColor: '#000',
                bodyColor: '#000',
                borderColor: '#ccc',
                borderWidth: 1
            }
        },
        scales: {
            x: {
                display: false // ❌ Hide X-axis labels since legend replaces it
            },
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0, // ✅ Enforce whole numbers
                    callback: function(value) {
                        return Number.isInteger(value) ? value : '';
                    }
                },
                title: {
                    display: false // ❌ No label title needed for Y-axis
                }
            }
        }
    }
});

    const labelsData = <?= json_encode(array_keys($data)) ?>;
    const customLegend = document.getElementById('customLegend');

    customLegend.innerHTML = labelsData.map((label, i) => `
      <div class="legend-item">
        <div class="legend-color-box" style="background-color: ${barColors[i]};"></div>
        <span>${label}</span>
      </div>
    `).join('');
    

</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
</body>
</html>

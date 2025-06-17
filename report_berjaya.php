<?php
session_start();
include_once 'database.php';
date_default_timezone_set('Asia/Kuala_Lumpur');

// Ensure only admin can access the page
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'pentadbir') {
    header("Location: login_staff.php");
    exit();
}

// Query successful applications
$sql = "SELECT fld_category, COUNT(fld_request_id) AS total_success FROM tbl_requests WHERE fld_status = 'berjaya' GROUP BY fld_category";
$result = mysqli_query($conn, $sql);
$reportData = [];
while ($row = mysqli_fetch_assoc($result)) {
    $reportData[] = $row;
}

// Get total number of successful applications
$totalApplications = mysqli_query($conn, "SELECT COUNT(*) FROM tbl_requests WHERE fld_status = 'berjaya'");
$totalCount = mysqli_fetch_row($totalApplications)[0];

// Query donations by date (for monthly report)
$monthlyData = mysqli_query($conn, "SELECT MONTH(fld_request_date) AS month, COUNT(*) AS total FROM tbl_requests WHERE fld_status = 'berjaya' GROUP BY MONTH(fld_request_date)");
// Ensure month names are passed correctly in the PHP code
$monthlyReport = [];
$monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

while ($row = mysqli_fetch_assoc($monthlyData)) {
    $monthName = $monthNames[$row['month'] - 1];  // Convert month number to name
    $monthlyReport[] = ['month' => $monthName, 'total' => $row['total']];
}


?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Permohonan Berjaya</title>
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
        h2 {
            font-weight: bold;
            margin-bottom: 20px;

        }
        .report-btn {
            margin-top: 20px;
            text-align: center;
        }
        .btn-export {
            background-color: #28a745;
            color: white;
            margin-right: 20px;
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 5px;
        }
        .btn-export:hover {
            background-color: #218838;
        }
        .table th {
            background-color: #007BFF;
            color: white;
        }
        canvas {
        width: 100% !important;
        height: auto !important;
        }
        .chart-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }

        .chart-container {
            flex: 1 1 calc(50% - 20px);
            background: #fff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            min-width: 300px;
        }

        canvas {
            max-height: 280px !important;
            width: 100% !important;
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
    <h2 class="text-primary">Analisis Data Permohonan Berjaya</h2>
        <div class="mb-4 card-stat" style="background: #f7f9fc; padding: 20px; border-left: 8px solid #007bff; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h4>Jumlah Permohonan Berjaya: <span class="text-success"><?= $totalCount ?></span></h4>
        </div>

    <div class="chart-wrapper">
      <div class="chart-container">
        <h4 class="text-center">Permohonan Mengikut Kategori</h4>
        <canvas id="categoryChart" style="max-height: 300px;"></canvas>
        <div id="categoryLegend" class="legend-container mt-3 text-center"></div>
      </div>
      <div class="chart-container">
        <h4 class="text-center">Permohonan Mengikut Bulan</h4>
        <canvas id="monthlyChart"></canvas>
        <div id="monthlyLegend" class="legend-container mt-3 text-center"></div> <!-- ✅ ADD THIS -->
      </div>
    </div>

    <div class="report-btn">
      <a href="export_report.php" class="btn btn-success">Muat Turun Laporan (CSV/PDF)</a>
    </div>
  </div>
</div>


<script>
    // Category Distribution Chart
        var ctx = document.getElementById('categoryChart').getContext('2d');
        var categoryChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($reportData, 'fld_category')); ?>,
                datasets: [{
                    label: 'Jumlah Permohonan',
                    data: <?php echo json_encode(array_column($reportData, 'total_success')); ?>,
                    backgroundColor: [
                        '#FF5733', '#33FF57', '#3357FF', '#FF33A6', '#FFD733', // Customize your colors here
                        '#33FFBD', '#7D33FF', '#FF3333', '#33AFFF', '#33FF99'
                    ],
                    borderColor: '#0056b3',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false // Hide the legend
                    }
                },
                scales: {
                    x: {
                    display: false // ✅ Hides X-axis including labels and ticks
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1, // Ensures whole numbers on the Y-axis
                            callback: function(value) {
                                return Number.isInteger(value) ? value : ''; // Remove decimals
                            }
                        },
                        title: {
                            display: true,
                            text: 'Jumlah Permohonan Berjaya' // Label for the Y-Axis
                        }
                    }
                }
            }
        });

        const categoryLabels = <?php echo json_encode(array_column($reportData, 'fld_category')); ?>;
        const categoryColors = [
        '#FF5733', '#33FF57', '#3357FF', '#FF33A6', '#FFD733',
        '#33FFBD', '#7D33FF', '#FF3333', '#33AFFF', '#33FF99'
        ];

        const legendContainer = document.getElementById('categoryLegend');
        legendContainer.innerHTML = categoryLabels.map((label, i) => `
        <div class="legend-item">
            <div class="legend-color-box" style="background-color: ${categoryColors[i]};"></div>
            <span>${label}</span>
        </div>
        `).join('');


   // Monthly Applications Chart
var ctx2 = document.getElementById('monthlyChart').getContext('2d');
var monthlyChart = new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($monthlyReport, 'month')); ?>, // Month names
        datasets: [{
            label: 'Permohonan Berjaya Bulan',
            data: <?php echo json_encode(array_column($monthlyReport, 'total')); ?>,
            backgroundColor: [
                '#FF5733', '#33FF57', '#3357FF', '#FF33A6', '#FFD733',
                '#33FFBD', '#7D33FF', '#FF3333', '#33AFFF', '#33FF99',
                '#FFAA33', '#00CC99'
            ],
            borderColor: '#28a745',
            borderWidth: 2
        }]
    },
    options: {
  responsive: true,
  plugins: {
    legend: {
      display: false,
      position: 'bottom',
      labels: {
        boxWidth: 20,
        color: '#333',
        font: {
          size: 12,
          weight: 'bold'
        }
      }
    }
  },
  scales: {
    x: {
    display: false // ✅ Hides X-axis including labels and ticks
},
    y: {
      type: 'linear',
      beginAtZero: true,
      ticks: {
        stepSize: 1,
        callback: function(value) {
          return Number.isInteger(value) ? value : null;
        }
      },
      title: {
        display: true,
        text: 'Jumlah Permohonan Berjaya'
      }
    }
  }
}


    
});

const monthlyLabels = <?php echo json_encode(array_column($monthlyReport, 'month')); ?>;
const monthlyColors = [
  '#FF5733', '#33FF57', '#3357FF', '#FF33A6', '#FFD733',
  '#33FFBD', '#7D33FF', '#FF3333', '#33AFFF', '#33FF99',
  '#FFAA33', '#00CC99'
];

const monthlyLegendContainer = document.getElementById('monthlyLegend');
monthlyLegendContainer.innerHTML = monthlyLabels.map((label, i) => `
  <div class="legend-item">
    <div class="legend-color-box" style="background-color: ${monthlyColors[i]};"></div>
    <span>${label}</span>
  </div>
`).join('');


</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>

</body>
</html>

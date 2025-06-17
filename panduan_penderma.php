<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Panduan Penderma</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-size: cover;
    }
    .container-box {
      background: #fff;
      padding: 40px;
      border-radius: 15px;
      max-width: 900px;
      margin: 80px auto;
      box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
    }
    h2 {
      text-align: center;
      font-weight: bold;
      margin-bottom: 30px;
      color: #007bff;
    }
    .step-card {
      display: flex;
      align-items: flex-start;
      margin-bottom: 25px;
      padding: 20px;
      background-color: #f7f9fc;
      border-left: 6px solid #007bff;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }
    .step-icon {
      font-size: 30px;
      margin-right: 20px;
      color: #007bff;
      width: 40px;
      text-align: center;
    }
    .step-text h4 {
      margin-top: 0;
      margin-bottom: 8px;
      font-weight: bold;
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
    <h2>Panduan Penderma</h2>

    <div class="step-card">
    <div class="step-icon"><i class="fas fa-search"></i></div>
    <div class="step-text">
        <h4>1. Saringan Permohonan & Tajaan</h4>
        Klik menu <strong>"Saringan Permohonan"</strong> untuk melihat senarai pelajar yang telah diluluskan oleh pentadbir.<br><br>
        ✔ Klik butang <strong>"Lihat"</strong> untuk melihat maklumat pelajar seperti jenis bantuan yang dimohon dan butiran lain.<br>
        ✔ Jika bersetuju untuk menaja, klik butang <strong>"Taja Sekarang"</strong> di bahagian bawah.<br><br>
        Setelah tajaan berjaya, status permohonan pelajar akan dikemaskini kepada <strong>"Berjaya"</strong>, dan maklumat tersebut akan direkodkan dalam sistem.
    </div>
    </div>

    <div class="step-card">
      <div class="step-icon"><i class="fas fa-plus-circle"></i></div>
      <div class="step-text">
        <h4>2. Tambah Penajaan</h4>
        Klik <strong>"Tambah Penajaan"</strong> untuk menambah jenis dan kuantiti bantuan yang ingin disumbangkan.
      </div>
    </div>

    <div class="step-card">
      <div class="step-icon"><i class="fas fa-history"></i></div>
      <div class="step-text">
        <h4>3. Sejarah Penajaan</h4>
        Klik <strong>"Sejarah Penajaan"</strong> untuk menyemak semula pelajar-pelajar yang telah anda taja.
      </div>
    </div>

    <div class="step-card">
      <div class="step-icon"><i class="fas fa-chart-bar"></i></div>
      <div class="step-text">
        <h4>4. Statistik Penajaan</h4>
        Klik <strong>"Statistik Penajaan"</strong> untuk melihat carta statistik tajaan anda mengikut kategori.
      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
</body>
</html>

<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Panduan Permohonan Bantuan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-size: cover;
      color: #333;
      padding-top: 60px;
    }

    .container-box {
      background: #fff;
      border-radius: 12px;
      padding: 40px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
      margin-bottom: 60px;
      max-width: 900px;
      margin-left: auto;
      margin-right: auto;
    }

    h1 {
      font-size: 26px;
      font-weight: 700;
      color: #000;
      margin-bottom: 20px;
    }

    .lead {
      font-style: italic;
      font-size: 15px;
      color: #555;
      margin-bottom: 30px;
    }

    .card-section {
      background-color: #f7f9fc;
      border-left: 6px solid #337ab7;
      border-radius: 12px;
      padding: 20px 25px;
      margin-bottom: 25px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .card-section h4 {
      margin-top: 0;
      font-weight: 600;
      margin-bottom: 15px;
    }

    ul, ol {
      padding-left: 18px;
    }

    ul li, ol li {
      margin-bottom: 8px;
    }

    .btn-back {
      margin-top: 30px;
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

<div class="container">
  <div class="container-box">
    <h1>Panduan Permohonan Bantuan</h1>
    <p class="lead">Ikuti panduan ini untuk memastikan permohonan anda lengkap dan diproses dengan lancar.</p>

    <div class="card-section">
      <h4>✅ Syarat Kelayakan</h4>
      <ul>
        <li>Warganegara Malaysia</li>
        <li>Pelajar Tahun 1 di Fakulti Teknologi & Sains Maklumat, UKM</li>
        <li>Pendapatan isi rumah bawah RM4,000</li>
      </ul>
    </div>

    <div class="card-section">
      <h4>📑 Dokumen Diperlukan</h4>
      <ul>
        <li>Slip gaji terkini (1 bulan terakhir) / Penyata pendapatan ibu bapa / penjaga bersama pengesahan </li>
        <li>Dokumen sokongan, seperti:</li>
        <ul>
          <li>Surat tawaran belajar di UKM</li>
          <li>Salinan kad OKU / Surat sakit daripada hospital (jika berkaitan)</li>
          <li>Lain-lain dokumen yang menyokong permohonan anda. Sertakan dalam satu fail.</li>
        </ul>
        <li>Semua fail perlu dalam format <strong>PDF</strong></li>
      </ul>
    </div>

    <div class="card-section">
      <h4>📦 Bantuan yang Ditawarkan</h4>
      <p>Setiap pelajar yang layak akan menerima satu set bantuan lengkap yang merangkumi:</p>
      <ul>
        <li>📱 Peralatan Digital (laptop bersama charger)</li>
        <li>📚 Bahan Pembelajaran (buku rujukan, alat tulis)</li>
        <li>🧼 Keperluan Diri (barangan penjagaan diri, makanan asas)</li>
      </ul>
    </div>

    <div class="card-section">
      <h4>🔁 Peringatan Penting</h4>
      <p><strong>Setiap pelajar hanya dibenarkan membuat permohonan sekali sahaja.</strong><br>
      Sila pastikan semua maklumat dan dokumen adalah lengkap sebelum menghantar permohonan.</p>
    </div>

    <div class="card-section">
      <h4>📝 Langkah Permohonan</h4>
      <ol>
        <li>Log masuk ke sistem</li>
        <li>Pilih menu “Permohonan Bantuan”</li>
        <li>Isi borang permohonan dengan lengkap</li>
        <li>Muat naik dokumen sokongan</li>
        <li>Klik butang “Hantar Permohonan”</li>
      </ol>
    </div>

    <div class="text btn-back">
      <a href="borang_permohonan.php" class="btn btn-default">← Kembali</a>
    </div>

  </div>
</div>

<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
</body>
</html>

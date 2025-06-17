<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Soalan Lazim & Hubungi Kami</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
    font-family: 'Inter', sans-serif;
    background-size: cover;
    color: #000;
    margin: 0;
    padding: 0;
    }

    .container-box {
      background: white;
      border-radius: 12px;
      padding: 30px;
      max-width: 900px;
      margin: 100px auto;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    h1 {
      font-size: 28px;
      font-weight: bold;
      margin-bottom: 25px;
      text-align: center;
    }

    .panel-title a {
      display: block;
      text-decoration: none;
    }

    .panel-title a:hover {
      text-decoration: none;
    }

    .contact-section {
      margin-top: 40px;
      padding-top: 20px;
      border-top: 1px solid #ccc;
    }

    .btn-back {
      margin-bottom: 20px;
      color: white;
      border: none;
    }

    nav.navbar {
      background-color: #fff;
      border-bottom: 2px solid #ccc;
    }

    nav .navbar-brand {
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

<?php include 'nav_bar.php'; ?>

<!-- Content -->
<div class="container">
  <div class="container-box">
    <h1>Soalan Lazim (FAQ)</h1>

    <div class="panel-group" id="accordion">

      <!-- Permohonan -->
      <div class="panel panel-default">
        <div class="panel-heading">
          <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion" href="#permohonan">
              📌 Permohonan
            </a>
          </h4>
        </div>
        <div id="permohonan" class="panel-collapse collapse in">
          <div class="panel-body">
            <strong>S: Apakah dokumen yang diperlukan?</strong>
            <p>J: Slip gaji / penyata pendapatan dan surat sokongan (surat tawaran, dokumen sakit dan lain-lain).</p>
            <strong>S: Bagaimana saya mahu hantar permohonan?</strong>
            <p>J: Log Masuk > Klik Permohonan Bantuan > Isi borang > Muat naik dokumen > Hantar</p>
          </div>
        </div>
      </div>

     <!-- Status -->
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" data-parent="#accordion" href="#status">
            📄 Status Permohonan
          </a>
        </h4>
      </div>
      <div id="status" class="panel-collapse collapse">
        <div class="panel-body">
          <strong>S: Bilakah keputusan permohonan saya diketahui?</strong>
          <p>
            J: Keputusan permohonan akan dimaklumkan dalam tempoh <strong>10 hingga 14 hari bekerja</strong>
            selepas permohonan dihantar. Pelajar masih boleh <strong>kemas kini permohonan dalam masa 7 hari</strong>
            selepas penghantaran.
          </p>
          <strong>S: Bagaimana untuk semak status?</strong>
          <p>J: Log masuk dan klik “Status Permohonan”.</p>
        </div>
      </div>
    </div>

      <!-- Lain-lain -->
      <div class="panel panel-default">
        <div class="panel-heading">
          <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion" href="#lain">
              ❔ Lain-Lain
            </a>
          </h4>
        </div>
        <div id="lain" class="panel-collapse collapse">
          <div class="panel-body">
            <strong>S: Saya tiada slip gaji, bagaimana?</strong>
            <p>J: Sediakan surat pengesahan pendapatan daripada ketua kampung atau penghulu.</p>
            <strong>S: Format fail tidak dibenarkan?</strong>
            <p>J: Hanya PDF sahaja dibenarkan.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Hubungi Kami -->
    <div class="contact-section">
      <h4>📬 Hubungi Kami</h4>
      <p>Jika anda perlukan bantuan lanjut berkaitan permohonan, sila hubungi kami melalui:</p>
      <ul style="list-style-type: none; padding-left: 0;">
        <li><strong>📧 Emel:</strong> <a href="mailto:a193792@siswa.ukm.edu.my">support.firststep@ukm.edu.my</a></li>
        <li><strong>📞 Telefon:</strong> +60 11-2339 5682 (Isnin - Jumaat, 9:00 pagi - 5:00 petang)</li>
      </ul>
    </div>


<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
</body>
</html>

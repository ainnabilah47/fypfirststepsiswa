<?php
include_once 'database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'pentadbir') {
    header("Location: login_staff.php");
    exit();
}

$admin_name = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>First Step Siswa: Dashboard Pentadbir</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  
  <style>
    html, body {
      margin: 0;
      padding: 0;
      overflow-x: hidden;
      font-family: 'Poppins', sans-serif;
      color: #fff;
    }

    body {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      color: #fff;
      background-size: cover;
      background-position: center center;
    }

     nav.navbar {
      z-index: 9999;
      position: relative;
      margin-bottom: 0 !important;
      padding-top: 15px;
      padding-bottom: 15px;
      background-color: #fff;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .container {
      overflow-x: hidden;
      padding-left: 15px;
      padding-right: 15px;
    }

   #backgroundCarousel {
      margin-top: 0 !important;
      padding-top: 0 !important;
      height: 500px; /* Fixed height for the carousel */
      overflow: hidden; /* Ensure nothing spills out */
      transition: background-image 1s ease-in-out;
    }

    #backgroundCarousel .carousel-inner {
      height: 100%;
    }

    #backgroundCarousel .item,
    #backgroundCarousel .item img {
      height: 100%;  /* Set the image height to 100% */
      width: 100%;   /* Set the image width to 100% */
      object-fit: cover;  /* Ensure the image covers the entire space without distortion */
      display: block; /* Ensures the image is a block-level element */
    }

    #backgroundCarousel {
        position: relative;
        height: 500px;
        overflow: hidden;
      }

      #backgroundCarousel .item {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0;
        transition: opacity 1.5s ease-in-out;
        z-index: 1;
      }

      #backgroundCarousel .item.active {
        opacity: 1;
        z-index: 2;
      }

      #backgroundCarousel .item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }

    .carousel-caption {
      bottom: 30%;
      padding: 30px;
      background: rgba(0, 0, 0, 0.5);
      border-radius: 15px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
      color: #fff;
      text-align: center;
      text-shadow: 0 2px 8px rgba(0,0,0,0.6);
      max-width: 80%;
      margin: 0 auto;
    }

     /* Carousel Section */
    .carousel-caption h1 {
      font-size: 2.6rem;
      font-weight: 700;
      letter-spacing: 1px;
      text-transform: uppercase;
      margin-bottom: 10px;
    }

    .carousel-caption p {
      font-size: 1.2rem;
      font-weight: 400;
    }

    h2, h5 {
      font-weight: 700;
    }

    section.cards-section {
      background-color: #fff;
      padding: 60px 15px 80px;
      color: #222;
    }

     .row {
      display: flex;
      flex-wrap: wrap;
    }

    .col-md-3 {
      display: flex;
      justify-content: center;
      align-items: stretch;
    }

    .card {
      background-color: #fff;
      color: #222;
      border: none;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
      padding: 25px 20px;
      text-align: center;
      cursor: pointer;
      display: flex;
      flex-direction: column;
      min-height: 300px;
    }

    .card i {
      color: #888;
      margin-bottom: 15px;
      transition: color 0.3s ease;
    }

    .card:hover,
    .card:focus,
    .card:active {
      background-color: #0d2646;
      color: #fff;
      box-shadow: 0 8px 24px rgba(13,38,70,0.6);
      transform: translateY(-6px);
    }

    .card:hover i,
    .card:focus i,
    .card:active i {
      color: #f89c2d;
    }

    .card:hover {
      background-color: #0d2646;
      color: #fff;
      box-shadow: 0 8px 24px rgba(13,38,70,0.6);
      transform: translateY(-6px);
    }

    .card:hover i {
      color: #f89c2d;
    }

    .row {
      display: flex;
      flex-wrap: wrap;
    }

    .col-md-3 {
      display: flex;
      justify-content: center;
      align-items: stretch;
    }

    footer {
      background-color: #0d2646;
      color: #fff;
      text-align: center;
      padding: 15px 0;
      margin-top: 40px;
    }

    footer a {
      color: #ccc;
    }

    footer a:hover {
      color: white;
    }

     footer a {
      color: #ccc;
      text-decoration: none;
    }

    footer a:hover {
      color: white;
    }
    h2, h4 {
      font-weight: 700; /* Increased weight for boldness */
    }
    .tentang-description, .tentang-objectives {
      color: #333;  /* Dark gray color for text */
    }
    .cards-section h2 {
      margin-bottom: 30px;
    }
    .tentang-section {
      padding-top: 20px;   /* reduce space above */
      padding-bottom: 20px; /* reduce space below */
      margin-top: 0;
      margin-bottom: 0;
    }

    .tentang-section p {
      max-width: 900px;
      margin: 0 auto;
      font-size: 15px;
    }

    .card p,
    .tentang-section p {
      color: #000 !important;
    }

    section.cards-section {
      padding: 40px 15px 60px; /* was 60px 15px 80px */
    }
    .kecilkan-card .card {
      min-height: 240px;
      padding: 20px 15px;
    }
     /* Responsive */
    @media (max-width: 768px) {
      .carousel-caption h1 {
        font-size: 2.5rem;
      }
      .carousel-caption p {
        font-size: 1.2rem;
      }
    }
    .card:hover p,
    .card:focus p,
    .card:active p {
      color: #fff !important;
    }
    .cards-section h2 {
      margin-bottom: 30px;
    }

  </style>
</head>
<body>

<?php include_once 'nav_bar_du.php'; ?>

<!-- Background image slider -->
<div id="backgroundCarousel">
  <div class="carousel-inner">
    <div class="item active"><img src="bg_1.jpeg" alt="Background 1" /></div>
    <div class="item"><img src="bg_2.jpeg" alt="Background 2" /></div>
    <div class="item"><img src="bg_3.jpeg" alt="Background 3" /></div>
  </div>
  <div class="carousel-caption">
      <h1>Selamat Datang ke Sistem Web First Step Siswa!</h1>
    <p style="font-size: 1.4rem;"><em>Urus permohonan bantuan, inventori dan akaun penderma di sini.</em></p>
  </div>
</div>

<!-- Feature Cards -->
<section class="cards-section container">
  <h2 class="text-center fw-bold mb-3" style="color:#0d2646;">Apa Yang Anda Boleh Lakukan</h2>
  <div class="row">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card text-center p-4 shadow-sm" style="border-radius: 15px;">
        <i class="fas fa-file-alt fa-3x text-primary mb-3"></i>
        <h5>Urus Permohonan</h5>
        <p>Semak dan proses permohonan bantuan pelajar dengan mudah.</p>
      </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card text-center p-4 shadow-sm" style="border-radius: 15px;">
        <i class="fas fa-box-open fa-3x text-primary mb-3"></i>
        <h5>Inventori Bantuan</h5>
        <p>Kemaskini dan pantau stok bantuan daripada penderma.</p>
      </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card text-center p-4 shadow-sm" style="border-radius: 15px;">
        <i class="fas fa-chart-bar fa-3x text-primary mb-3"></i>
        <h5>Statistik Sistem</h5>
        <p>Lihat data dan laporan prestasi permohonan & penajaan.</p>
      </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card text-center p-4 shadow-sm" style="border-radius: 15px;">
        <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
        <h5>Daftar Penderma</h5>
        <p>Tambah akaun penderma baharu dan rekod jenis bantuan.</p>
      </div>
    </div>
  </div>
</section>

<!-- About -->
<section class="cards-section container kecilkan-card">
  <h2 class="text-center fw-bold mb-3" style="color:#0d2646;">Tentang First Step Siswa</h2>
  <p class="text-center" style="color: #333; max-width: 900px; margin: 0 auto;">First Step Siswa adalah platform yang dibangunkan untuk membantu pelajar UKM dalam memohon bantuan keperluan asas. Sistem ini direka untuk memudahkan pelajar mengakses bantuan dan sumber yang diperlukan untuk kejayaan mereka.</p>
</section>

<!-- Why Choose -->
<section class="cards-section container kecilkan-card">
  <h2 class="text-center fw-bold mb-3" style="color:#0d2646;">Kenapa Pilih First Step Siswa?</h2>
  <div class="row">
    <div class="col-md-4 col-sm-6 mb-4">
      <div class="card text-center p-4 shadow-sm" style="border-radius: 15px;">
        <i class="fas fa-clock fa-2x text-success mb-3"></i>
        <h5 class="font-weight-bold">Cepat & Efisien</h5>
        <p class="text-muted">Proses permohonan yang mudah dan cepat untuk pelajar memohon bantuan dengan pantas.</p>
      </div>
    </div>
    <div class="col-md-4 col-sm-6 mb-4">
      <div class="card text-center p-4 shadow-sm" style="border-radius: 15px;">
        <i class="fas fa-lock fa-2x text-info mb-3"></i>
        <h5 class="font-weight-bold">Selamat & Terjamin</h5>
        <p class="text-muted">Sistem kami menjamin keselamatan data dan identiti pelajar sepanjang proses permohonan.</p>
      </div>
    </div>
    <div class="col-md-4 col-sm-6 mb-4">
      <div class="card text-center p-4 shadow-sm" style="border-radius: 15px;">
        <i class="fas fa-users fa-2x text-warning mb-3"></i>
        <h5 class="font-weight-bold">Bina Komuniti Pelajar</h5>
        <p class="text-muted">Menghubungkan pelajar UKM dalam membantu antara satu sama lain dengan sistem bantuan kami.</p>
      </div>
    </div>
  </div>
</section>

<footer>
  <p>&copy; 2025 First Step Siswa. Hak Cipta Terpelihara.</p>
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>

<script>
  $(document).ready(function () {
    const items = $('#backgroundCarousel .item');
    let currentIndex = 0;

    function showNextImage() {
      items.eq(currentIndex).removeClass('active');
      currentIndex = (currentIndex + 1) % items.length;
      items.eq(currentIndex).addClass('active');
    }

    items.eq(currentIndex).addClass('active');
    setInterval(showNextImage, 5000);
  });
</script>
</body>
</html>

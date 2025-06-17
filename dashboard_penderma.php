<?php
include_once 'database.php';
session_start();

// Check if user is logged in as a donor
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'penderma') {
    header("Location: login_staff.php");
    exit();
}

$donor_name = $_SESSION['username'];

?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>First Step Siswa: Dashboard Penderma</title>

  <!-- Google Font + Bootstrap + FontAwesome -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    html, body {
      margin: 0;
      padding: 0;
      overflow-x: hidden;
      font-family: 'Poppins', sans-serif;
      color: #fff;
    }

    * {
      box-sizing: border-box; /* ✅ Prevents padding/margin overflow */
    }

    body {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      color: #fff;
      background-size: cover;  /* Makes sure the image covers the entire viewport */
      background-position: center center; /* Center the background */
    }

    .container {
      overflow-x: hidden; /* avoid any internal overflow */
      padding-left: 15px;
      padding-right: 15px;
    }

    .carousel-caption h1 {
      font-size: 3.5rem;
      font-weight: 700;
      letter-spacing: 1px;
      text-transform: uppercase;
      margin-bottom: 10px;
    }

    .carousel-caption p {
      font-size: 1.2rem;
      max-width: 500px;
      margin: 15px auto 0;
    }

    .carousel-fade .carousel-inner .item {
    transition: opacity 2s ease-in-out; /* Change 2s to any value */
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

    #backgroundCarousel {
      margin-top: 0 !important;
      padding-top: 0 !important;
      height: 500px; /* Adjust height to 80% of viewport height */
      overflow: hidden;
      transition: background-image 1s ease-in-out;
    }

    #backgroundCarousel .carousel-inner {
      height: 100%;
      width: 100%; /* Ensure carousel inner takes full width */
    }

    #backgroundCarousel .item,
    #backgroundCarousel .item img {
      height: 100%;  /* Set image height to 100% of the container */
      width: 100%;   /* Set image width to 100% of the container */
      object-fit: cover;  /* Ensure image covers space without distortion */
      display: block;
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
      background: rgba(0, 0, 0, 0.5); /* semi-transparent */
      border-radius: 15px;
      backdrop-filter: blur(10px); /* frosted effect */
      -webkit-backdrop-filter: blur(10px); /* for Safari */
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
      color: #fff;
      text-align: center;
      text-shadow: 0 2px 8px rgba(0,0,0,0.6);
      max-width: 90%;
      margin: 0 auto;
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
    /* Cards section below slider */
    section.cards-section {
      background-color: #fff;
      padding: 60px 15px 80px;
      color: #222;
    }

    .card {
      background-color: #fff;
      color: #222;
      border: none;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
      padding: 25px 20px;
      height: 100%;
      text-align: center;
      cursor: pointer;
      display: flex;
      flex-direction: column;  /* Ensures contents stack vertically */
      height: 100%;  /* Ensures all cards are the same height */
      min-height: 300px; /* Sets a minimum height to maintain consistency */
    }

    .row {
    display: flex;
    flex-wrap: wrap;
  }

  .col-md-3 {
    display: flex;
    justify-content: center;
  }

    .card i {
      color: #888;
      margin-bottom: 15px;
      transition: color 0.3s ease;
    }

    .card:hover,
    .card:focus,
    .card:active {
      background-color: #0d2646; /* Navy blue */
      color: #fff;
      box-shadow: 0 8px 24px rgba(13,38,70,0.6);
      transform: translateY(-6px);
    }

    .card:hover i,
    .card:focus i,
    .card:active i {
      color: #f89c2d; /* Accent orange on icon */
    }

    /* Footer */
    footer {
      background-color: #0d2646;
      color: #fff;
      text-align: center;
      padding: 15px 0;
      margin-top: 40px;
    }

    footer a {
      color: #ccc;
      text-decoration: none;
    }

    footer a:hover {
      color: white;
    }

    h2, h5 {
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

  </style>
</head>
<body>

  <?php include_once 'nav_bar_nu.php'; ?>

  <div id="backgroundCarousel">
    <div class="carousel-inner">
      <div class="item active">
        <img src="bg_1.jpeg" alt="Background 1" class="img-responsive" />
      </div>
      <div class="item">
        <img src="bg_2.jpeg" alt="Background 2" class="img-responsive" />
      </div>
      <div class="item">
        <img src="bg_3.jpeg" alt="Background 3" class="img-responsive" />
      </div>
    </div>

    <div class="carousel-caption">
      <h1>Selamat Datang ke Sistem Web First Step Siswa!</h1>
      <p style="font-size: 1.4rem;"><em>Terima kasih kerana menyokong pelajar UKM. Semak permohonan, taja bantuan, dan lihat statistik anda di sini.</em></p>
    </div>
  </div>
  
 <!-- Features section -->
  <section class="cards-section container">
    <h2 class="text-center fw-bold mb-3" style="color:#0d2646;">Apa Yang Anda Boleh Lakukan</h2>
    <div class="row">
      <div class="col-md-3 col-sm-6 mb-4">
        <div class="card text-center p-4 shadow-sm" style="border-radius: 15px;">
          <i class="fas fa-database fa-2x text-primary mb-3"></i>
          <h5 class="font-weight-bold">Saringan Permohonan</h5>
          <p class="text-muted">Lihat permohonan pelajar yang telah diluluskan dan pilih siapa yang ingin anda taja.</p>
        </div>
      </div>
      <div class="col-md-3 col-sm-6 mb-4">
        <div class="card text-center p-4 shadow-sm" style="border-radius: 15px;">
          <i class="fas fa-users fa-2x text-primary mb-3"></i>
          <h5 class="font-weight-bold">Senarai Ditaja</h5>
          <p class="text-muted">Semak senarai pelajar yang telah anda taja beserta maklumat bantuan mereka.</p>
        </div>
      </div>
      <div class="col-md-3 col-sm-6 mb-4">
        <div class="card text-center p-4 shadow-sm" style="border-radius: 15px;">
          <i class="fas fa-chart-bar fa-2x text-primary mb-3"></i>
          <h5 class="font-weight-bold">Statistik Penajaan</h5>
          <p class="text-muted">Lihat statistik penajaan anda mengikut kategori bantuan dan jumlah pelajar ditaja.</p>
        </div>
      </div>
      <div class="col-md-3 col-sm-6 mb-4">
        <div class="card text-center p-4 shadow-sm" style="border-radius: 15px;">
          <i class="fas fa-plus-circle fa-2x text-primary mb-3"></i>
          <h5 class="font-weight-bold">Tambah Bantuan</h5>
          <p class="text-muted">Tambah jenis bantuan dan kuantiti yang anda ingin sumbangkan kepada pelajar memerlukan.</p>
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

  <!-- Footer -->
  <footer>
    <p>&copy; 2025 First Step Siswa. Hak Cipta Terpelihara.</p>
  </footer>

  <!-- Bootstrap 3 JS and dependencies -->
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

      // Initial setup
      items.eq(currentIndex).addClass('active');

      // Change every 5 seconds
      setInterval(showNextImage, 5000);
    });
  </script>

</body>
</html>

<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Ensure session is started only once
}
?>

<nav class="navbar navbar-default" style="background-color: #ffffff; border-bottom: 2px solid #ddd; padding: 10px 20px;">
  <div class="container-fluid">
    <!-- Logo & Branding -->
    <div class="navbar-header">
      <a class="navbar-brand" href="dashboard_penderma.php" style="display: flex; align-items: center; font-family: 'Poppins', sans-serif; font-weight: bold; color: #222;">
        <img src="logo.png" alt="First Step Siswa Logo" style="height: 40px; margin-right: 10px;">
        First Step Siswa
      </a>
    </div>

    <!-- Navigation Links -->
    <div class="collapse navbar-collapse">
      <ul class="nav navbar-nav" style="gap: 15px;">
        <li>
          <a href="saringan_permohonan.php" style="color: #222; font-family: 'Poppins', sans-serif; font-weight: 500; padding: 15px 15px; text-decoration: none;"
             onmouseover="this.style.textDecoration='underline'" 
             onmouseout="this.style.textDecoration='none'">
            Saringan Permohonan
          </a>
        </li>
        <!-- Add 'Tambah Penajaan' Link here -->
        <li>
          <a href="tambah_penajaan.php" style="color: #222; font-family: 'Poppins', sans-serif; font-weight: 500; padding: 15px 15px; text-decoration: none;"
             onmouseover="this.style.textDecoration='underline'" 
             onmouseout="this.style.textDecoration='none'">
            Tambah Bantuan
          </a>
        </li>
      </ul>

      <ul class="nav navbar-nav" style="gap: 15px;">
        <li>
          <a href="senarai_ditaja.php" style="color: #222; font-family: 'Poppins', sans-serif; font-weight: 500; padding: 15px 15px; text-decoration: none;"
             onmouseover="this.style.textDecoration='underline'" 
             onmouseout="this.style.textDecoration='none'">
            Sejarah Tajaan
          </a>
        </li>
      </ul>

      <!-- Right Side User Profile -->
      <ul class="nav navbar-nav navbar-right" style="display: flex; align-items: center; gap: 15px;">
        <?php if (isset($_SESSION['full_name'])): ?>
          <li class="navbar-text" style="font-family: 'Poppins', sans-serif; font-weight: bold; color: #222; padding-right: 10px;">
            Selamat Datang, <?php echo htmlspecialchars(ucwords($_SESSION['full_name'])); ?>!
          </li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" style="color: #222; font-family: 'Poppins', sans-serif; font-weight: 500; padding: 10px; display: flex; align-items: center;">
              <img src="user-icon.png" alt="User" style="height: 30px; margin-right: 5px;"> <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" style="background-color: #fff; box-shadow: 0px 4px 6px rgba(0,0,0,0.1); border-radius: 5px;">
              <li><a href="profil.php" style="color: #222; font-family: 'Poppins', sans-serif;">Mengurus Profil</a></li>
              <!-- Add Statistik Penajaan below Mengurus Profil -->
              <li><a href="statistik_penajaan.php" style="color: #222; font-family: 'Poppins', sans-serif;">Statistik Penajaan</a></li>
              <li><a href="panduan_penderma.php" style="color: #222; font-family: 'Poppins', sans-serif;">Panduan</a></li>
              <li role="separator" class="divider"></li>
              <li><a href="logout.php" style="color: red; font-family: 'Poppins', sans-serif;">Log Keluar</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li><a href="login_staff.php" style="color: #222; font-family: 'Poppins', sans-serif; font-weight: bold; padding: 10px 15px;">Log Masuk</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Add Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;700&display=swap" rel="stylesheet">

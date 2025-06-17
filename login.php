<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Log Masuk - FirstStepSiswa</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    :root {
      --bg-light: linear-gradient(to bottom right, #e6f0fc, #c3d9f7);
      --bg-dark: linear-gradient(to bottom right, #1c1c2b, #2f2f4a);
      --text-light: #333;
      --text-dark: #f1f1f1;
      --card-bg-light: #ffffff;
      --card-bg-dark: #2b2b3d;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: linear-gradient(to bottom right, #d3eafd, #a5d7f8);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
      transition: background 0.5s ease;
      color: var(--text-light);
      color:rgb(3, 33, 99);

    }

    .dark-mode {
      background: var(--bg-dark);
      color: var(--text-dark);
    }

    .login-box {
      background-color: var(--card-bg-light);
      border-radius: 20px;
      padding: 40px 30px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      max-width: 420px;
      width: 100%;
      text-align: center;
      animation: fadeIn 0.8s ease;
      transition: background-color 0.4s ease, color 0.4s ease;
    }

    .dark-mode .login-box {
      background-color: var(--card-bg-dark);
    }

    .logo {
      width: 80px;
      margin-bottom: 15px;
    }

    h2 {
      font-weight: 600;
      margin-bottom: 25px;
    }

    .btn-choice {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      width: 100%;
      padding: 14px;
      font-weight: 600;
      font-size: 16px;
      border-radius: 10px;
      text-decoration: none;
      margin-bottom: 15px;
      transition: all 0.3s ease;
    }

    .btn-pelajar {
      background-color: #5ca8f8;
      color: white;
    }

    .btn-pelajar:hover {
      background-color: #3f92e0;
      transform: scale(1.03);
    }

    .btn-staff {
      background-color: #66d1cc;
      color: white;
    }

    .btn-staff:hover {
      background-color: #43b3ae;
      transform: scale(1.03);
    }

    .theme-toggle {
      position: absolute;
      top: 20px;
      right: 25px;
      background: none;
      border: none;
      font-size: 20px;
      cursor: pointer;
      color: inherit;
    }

    .animated-logo {
    animation: float 3.5s ease-in-out infinite;
    }

    @keyframes float {
    0%   { transform: translateY(0); }
    50%  { transform: translateY(-10px); }
    100% { transform: translateY(0); }
    }


    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 480px) {
      .login-box {
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body>

  <button class="theme-toggle" id="themeToggle" title="Tukar Mod Gelap / Cerah">
    <i class="fas fa-moon"></i>
  </button>

  <div class="login-box">
  <img src="logo.png" alt="FirstStepSiswa Logo" class="logo animated-logo" />
  <h2>First Step Siswa</h2>

    <a href="login_pelajar.php" class="btn-choice btn-pelajar">
      <i class="fas fa-user-graduate"></i> Log Masuk sebagai Pelajar
    </a>

    <a href="login_staff.php" class="btn-choice btn-staff">
      <i class="fas fa-user-tie"></i> Log Masuk sebagai Pentadbir atau Penderma
    </a>
  </div>

  <script>
    const toggleBtn = document.getElementById('themeToggle');
    const body = document.body;

    // Load preference
    if (localStorage.getItem('darkMode') === 'true') {
      body.classList.add('dark-mode');
      toggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
    }

    toggleBtn.addEventListener('click', () => {
      body.classList.toggle('dark-mode');
      const isDark = body.classList.contains('dark-mode');
      toggleBtn.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
      localStorage.setItem('darkMode', isDark);
    });
  </script>

</body>
</html>

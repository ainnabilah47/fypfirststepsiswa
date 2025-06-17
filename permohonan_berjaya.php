<?php
session_start();
$msg = $_SESSION['success_msg'] ?? 'Tiada mesej.';
unset($_SESSION['success_msg']);
?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Status Permohonan</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
Swal.fire({
  icon: 'success',
  title: 'Berjaya!',
  text: '<?php echo $msg; ?>',
  confirmButtonText: 'OK'
}).then(() => {
  window.location.href = 'dashboard_pelajar.php';
});
</script>

</body>
</html>

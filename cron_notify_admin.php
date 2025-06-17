<?php

require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include_once 'database.php';
date_default_timezone_set('Asia/Kuala_Lumpur');

// Get pending requests (status = 'sedang diproses')
$notifSql = "SELECT fld_name, fld_matric_no, fld_category, fld_request_date 
             FROM tbl_requests 
             WHERE fld_status = 'sedang diproses'";
$notifResult = mysqli_query($conn, $notifSql);

$emailBody = "<h3>Permohonan Belum Diproses</h3>
<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; font-family: Arial, sans-serif; width: 100%; text-align: center;'>
<thead>
<tr style='background-color: #f44336; color: white;'>
  <th>Nama Pelajar</th>
  <th>No Matrik</th>
  <th>Jenis Bantuan</th>
  <th>Hari Tertunggak</th>
  <th>Tarikh Permohonan</th>
  <th>Status</th>
</tr>
</thead><tbody>";

$sendEmail = false;
$count = 0;

while ($rowNotif = mysqli_fetch_assoc($notifResult)) {
    $daysSince = floor((strtotime(date('Y-m-d')) - strtotime($rowNotif['fld_request_date'])) / (60 * 60 * 24));
    $statusLabel = '';

    if ($daysSince >= 7) {
        $statusLabel = "<span style='color:red;'>Lambat (7+ hari)</span>";
        $sendEmail = true;
    } elseif ($daysSince >= 5) {
        $statusLabel = "<span style='color:orange;'>Tertangguh (5+ hari)</span>";
        $sendEmail = true;
    }

    if ($statusLabel) {
        $emailBody .= "<tr>
            <td>" . htmlspecialchars($rowNotif['fld_name']) . "</td>
            <td>" . htmlspecialchars($rowNotif['fld_matric_no']) . "</td>
            <td>" . htmlspecialchars($rowNotif['fld_category']) . "</td>
            <td>$daysSince hari</td>
            <td>" . htmlspecialchars($rowNotif['fld_request_date']) . "</td>
            <td>$statusLabel</td>
        </tr>";
        $count++;
    }
}
$emailBody .= "</tbody></table>";

// Add action button link for convenience
$emailBody .= "
<p style='margin-top:20px; font-family: Arial, sans-serif; text-align: center;'>
   <a href='http://localhost/firststepsiswa/memproses_permohonan.php' 
      style='display: inline-block; background-color: #4686FC; color: white; padding: 10px 20px; 
             text-decoration: none; border-radius: 6px; font-weight: bold;'>
      Semak Permohonan Sekarang
   </a>
</p>";

// Daily send guard using a file
$lastSentFile = 'last_notification_sent.txt';
$today = date('Y-m-d');
$lastSent = file_exists($lastSentFile) ? file_get_contents($lastSentFile) : '';

if ($sendEmail && $lastSent !== $today) {

    // Save current date to prevent multiple sends per day
    file_put_contents($lastSentFile, $today);

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'nabilaha645@gmail.com';    // YOUR SMTP USERNAME
        $mail->Password = 'xcjm jowg hxmw nfhm';      // YOUR SMTP PASSWORD or App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('a193792@siswa.ukm.edu.my', 'First Step Siswa Notifikasi');
        $mail->addAddress('a193792@siswa.ukm.edu.my');  // ADMIN EMAIL

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Notifikasi Harian: Permohonan Belum Diproses';
        $mail->Body    = $emailBody;
        $mail->AltBody = strip_tags($emailBody); // Plain text fallback

        $mail->send();

        // Log success
        file_put_contents('notification_log.txt',
            "[" . date('Y-m-d H:i:s') . "] Emel dihantar ke admin dengan $count permohonan tertunggak\n",
            FILE_APPEND
        );

    } catch (Exception $e) {
        // Log error
        file_put_contents('notification_log.txt',
            "[" . date('Y-m-d H:i:s') . "] Gagal hantar emel: {$mail->ErrorInfo}\n",
            FILE_APPEND
        );
    }
}

mysqli_close($conn);
?>

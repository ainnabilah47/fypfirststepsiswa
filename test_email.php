<?php
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Kuala_Lumpur');

// Test sender (your Gmail with app password)
$senderEmail = 'nabilaha645@gmail.com'; // Must be Gmail (with app password)
$senderPassword = 'xcjm jowg hxmw nfhm'; // Replace with your Gmail App Password

// Test recipient (UKM admin email)
$adminEmail = 'a193792@siswa.ukm.edu.my';

// ✅ VALIDATE BOTH EMAILS
if (!preg_match('/@(siswa\.ukm\.edu\.my|ukm\.edu\.my)$/', $adminEmail)) {
    echo "❌ Gagal: Emel penerima bukan dari domain UKM.";
    exit();
}

try {
    $mail = new PHPMailer(true);

    // SMTP Config
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $senderEmail;
    $mail->Password = $senderPassword;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Email setup
    $mail->setFrom($senderEmail, 'First Step Siswa');
    $mail->addAddress($adminEmail);
    $mail->Subject = '🔔 Ujian Penghantaran Emel - Sistem First Step Siswa';
    $mail->isHTML(true);
    $mail->Body = "<h3>✅ Ujian berjaya!</h3><p>Emel ini dihantar untuk menguji konfigurasi PHPMailer anda.</p>";

    // Send
    $mail->send();
    echo "✅ Emel berjaya dihantar ke $adminEmail";

} catch (Exception $e) {
    echo "❌ Emel gagal dihantar. Ralat: {$mail->ErrorInfo}";
}
?>

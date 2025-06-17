<?php
session_start();
require_once 'dompdf/autoload.inc.php'; // DOMPDF autoloader
use Dompdf\Dompdf;
use Dompdf\Options;
include('database.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'penderma') {
    header("Location: login_staff.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$donor_name = $_SESSION['username'];

// Reconnect using PDO
$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get sponsored categories and counts
$catStmt = $conn->prepare("
    SELECT r.fld_category, COUNT(*) AS jumlah
    FROM tbl_requests r
    JOIN tbl_sponsorships s ON r.fld_request_id = s.fld_request_id
    WHERE s.fld_sponsor_id = ? AND r.fld_status = 'berjaya'
    GROUP BY r.fld_category
");
$catStmt->execute([$user_id]);
$data = $catStmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['export_csv'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="statistik_penajaan.csv"');
    $output = fopen('php://output', 'w');

    fputcsv($output, ['Kategori Bantuan', 'Jumlah Permohonan']);
    foreach ($data as $row) {
        fputcsv($output, [$row['fld_category'], $row['jumlah']]);
    }
    fclose($output);
    exit();
}

if (isset($_POST['export_pdf'])) {
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('defaultFont', 'Helvetica');

    $dompdf = new Dompdf($options);

    $html = '<h2 style="text-align:center;">Statistik Penajaan oleh ' . htmlspecialchars($donor_name) . '</h2>';
    $html .= '<table border="1" cellpadding="6" cellspacing="0" width="100%">
                <thead>
                    <tr style="background-color: #007BFF; color: white;">
                        <th>Kategori Bantuan</th>
                        <th>Jumlah Permohonan</th>
                    </tr>
                </thead>
                <tbody>';

    foreach ($data as $row) {
        $html .= '<tr>
                    <td>' . htmlspecialchars($row['fld_category']) . '</td>
                    <td>' . $row['jumlah'] . '</td>
                  </tr>';
    }

    $html .= '</tbody></table>';

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("statistik_penajaan.pdf", ["Attachment" => true]);
    exit();
}

?>

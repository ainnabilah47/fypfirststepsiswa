<?php
session_start();

if (!isset($_SESSION['draft']) || !isset($_GET['type'])) {
    header("Location: borang_permohonan.php");
    exit();
}

$validTypes = ['income', 'support'];
$type = in_array($_GET['type'], $validTypes) ? $_GET['type'] : null;

if (!$type) {
    header("Location: borang_permohonan.php");
    exit();
}

switch ($type) {
    case 'income':
        if (!empty($_SESSION['draft']['fld_income_slip']) && file_exists($_SESSION['draft']['fld_income_slip'])) {
            unlink($_SESSION['draft']['fld_income_slip']);
        }
        unset($_SESSION['draft']['fld_income_slip']);
        break;

    case 'support':
        if (!empty($_SESSION['draft']['fld_supporting_doc']) && file_exists($_SESSION['draft']['fld_supporting_doc'])) {
            unlink($_SESSION['draft']['fld_supporting_doc']);
        }
        unset($_SESSION['draft']['fld_supporting_doc']);
        break;
}

$_SESSION['success_msg'] = "Fail berjaya dibuang daripada draf.";
header("Location: borang_permohonan.php");
exit();
?>

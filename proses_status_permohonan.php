<?php
include_once 'database.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];

    $stmt = $conn->prepare("UPDATE tbl_requests SET fld_status = ? WHERE fld_request_id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
}
?>

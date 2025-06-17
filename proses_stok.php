<?php
include_once 'database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'pentadbir') {
    header("Location: login_staff.php");
    exit();
}

$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

if (!$id || !$action) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request!']);
    exit();
}

if ($action === 'tambah') {
    // Increase the quantity
    $updateQuery = "UPDATE tbl_inventory SET fld_quantity = fld_quantity + 1 WHERE fld_item_id = ?";
} elseif ($action === 'kurang') {
    // Decrease the quantity
    $updateQuery = "UPDATE tbl_inventory SET fld_quantity = fld_quantity - 1 WHERE fld_item_id = ? AND fld_quantity > 0";
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action!']);
    exit();
}

$stmt = $conn->prepare($updateQuery);
$stmt->bind_param('i', $id);
$execute = $stmt->execute();

if ($execute) {
    // Success message
    echo json_encode(['status' => 'success', 'message' => 'Item quantity updated successfully.']);
} else {
    // Error message
    echo json_encode(['status' => 'error', 'message' => 'Failed to update item quantity.']);
}
?>

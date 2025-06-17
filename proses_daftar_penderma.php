<?php
include_once 'database.php';
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');

// Ensure only admin can access the page
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'pentadbir') {
    header("Location: login_staff.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // Get the donations and quantities
    $bantuanList = $_POST['bantuan'];
    $kuantitiList = $_POST['kuantiti'];

    // Validation: Ensure passwords match
    if ($password !== $confirm) {
        echo "<script>alert('Kata laluan tidak sepadan!'); window.history.back();</script>";
        exit();
    }

    // Check if username already exists
    $checkUsername = $conn->prepare("SELECT COUNT(*) FROM tbl_users WHERE fld_username = ?");
    $checkUsername->bind_param("s", $username);
    $checkUsername->execute();
    $checkUsernameResult = $checkUsername->get_result();
    $row = $checkUsernameResult->fetch_row();

    if ($row[0] > 0) {
        // Username is taken, display an error message
        $_SESSION['error'] = "Penderma telah didaftarkan";

        var_dump($_SESSION);  // This will print out the session content

        echo "<script>window.history.back();</script>";
        exit();
        unset($_SESSION['error']);

    }

    // Insert into tbl_users (donor account)
    $insertUser = $conn->prepare("INSERT INTO tbl_users (fld_name, fld_username, fld_email, fld_phone, fld_password, fld_role) 
                                  VALUES (?, ?, ?, ?, ?, 'penderma')");
    $insertUser->bind_param("sssss", $name, $username, $email, $phone, $password);
    
    if ($insertUser->execute()) {
        $newUserId = $insertUser->insert_id;

        // Insert donations into tbl_donations
        $stmtDonation = $conn->prepare("INSERT INTO tbl_donations (fld_donor_id, fld_category, fld_quantity, fld_created_at) 
                                        VALUES (?, ?, ?, NOW())");

        // Loop through the donations and insert them
        for ($i = 0; $i < count($bantuanList); $i++) {
            $category = $bantuanList[$i];
            $qty = (int)$kuantitiList[$i];

            // Sanitize inputs for donations
            $stmtDonation->bind_param("isi", $newUserId, $category, $qty);
            if (!$stmtDonation->execute()) {
                echo "<script>alert('Ralat semasa menambah bantuan!'); window.history.back();</script>";
                exit();
            }

            // Check if the donation category exists in the inventory
            $checkInventoryStmt = $conn->prepare("SELECT fld_quantity FROM tbl_inventory WHERE fld_category = ?");
            $checkInventoryStmt->bind_param("s", $category);
            $checkInventoryStmt->execute();
            $checkInventoryStmt->store_result();

            if ($checkInventoryStmt->num_rows > 0) {
                // If item exists, update the quantity in inventory
                $updateInventoryStmt = $conn->prepare("UPDATE tbl_inventory SET fld_quantity = fld_quantity + ? WHERE fld_category = ?");
                $updateInventoryStmt->bind_param("is", $qty, $category);
                $updateInventoryStmt->execute();
            } else {
                // If item doesn't exist, insert a new entry into inventory
                $insertInventoryStmt = $conn->prepare("INSERT INTO tbl_inventory (fld_category, fld_quantity, fld_donor_id, fld_last_updated) 
                                                      VALUES (?, ?, ?, NOW())");
                $insertInventoryStmt->bind_param("sis", $category, $qty, $newUserId);
                $insertInventoryStmt->execute();
            }
        }

        // Success message after donor registration and donation insertion
        echo "<script>alert('Pendaftaran berjaya! Penajaan baru telah ditambah dan inventori dikemaskini.'); window.location.href='daftar_penderma.php';</script>";
    } else {
        // Error message if user registration fails
        echo "<script>alert('Ralat semasa mendaftar penderma!'); window.history.back();</script>";
    }
}
?>

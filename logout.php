<?php
session_start();

$redirect = 'login.php';
if (isset($_SESSION['user_role'])) {
    switch ($_SESSION['user_role']) {
        case 'pelajar':
            $redirect = 'login_pelajar.php';
            break;
        case 'pentadbir':
        case 'penderma':
            $redirect = 'login_staff.php';
            break;
    }
}

session_unset();
session_destroy();
header("Location: $redirect");
exit();

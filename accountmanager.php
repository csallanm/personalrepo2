<?php

require_once "controllerUserData.php";

$email = $_SESSION['email'];
$password = $_SESSION['password'];

if ($email != false && $password != false) {
    $sql = "SELECT * FROM tblusers WHERE email = '$email'";
    $run_Sql = mysqli_query($con, $sql);
    if ($run_Sql) {
        $fetch_info = mysqli_fetch_assoc($run_Sql);
        $status = $fetch_info['status'];
        $code = $fetch_info['code'];
        $role_id = $fetch_info['role_id'];
        if ($status == "verified") {
            if ($code != 0) {
                header('Location: resetcode.php');
            }

            // Role-based access
            if($role_id != 1 && $role_id != 2){
                header('Location: homepage.php');
                exit();
            }
        } else {
            header('Location: userotp.php');
        }
    }
} else {
    header('Location: login.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Manager - Student Archiving System</title>
    <link rel="icon" type="image/x-icon" href="assets/euC.png">
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="customcolors.css" />
    <link rel="stylesheet" href="effects.css" />
    <link rel="stylesheet" href="appearance.css" />
    <link rel="stylesheet" href="new_login.css" />

    <nav class="navbar fixed-top navbar-expand navbar-dark shadow-lg p-3" style="background-color: #4e0000;">
        <a href="https://mseuf.edu.ph/candelaria" target="_blank"><img class="mx-3" src="assets/euC.png" alt="hugenerd"
                width="30" height="30"></a>
        <ul class="navbar-nav nav-pills mr-auto">
            <li class="nav-item">
                <a class="nav-link mx-2" href="homepage.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link mx-2" href="students.php">Students</a>
            </li>
        </ul>
        <div class="dropdown-pr4 ms-auto">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle mx-3"
                id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="assets/user-icon-2048x2048-ihoxz4vq.png" alt="hugenerd" width="30" height="30"
                    class="rounded-circle">
                <span class="d-none d-sm-inline mx-2" style="text-decoration: none; color: white;">
                    <?php echo $fetch_info['name'] ?>
                </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark text-small shadow"
                aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" href="EUC_SAS_MANUAL.pdf" target="_blank">User Manual</a></li>
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#aboutModal">About</a></li>
                <li><a class="dropdown-item" href="accountsettings.php">Account Settings</a></li>
                <?php if ($fetch_info['role_id'] == 1 || $fetch_info['role_id'] == 2): ?>
                    <li><a class="dropdown-item" href="accountmanager.php">Staff Account Manager</a></li>
                <?php endif; ?>

                <?php if ($fetch_info['role_id'] == 2): ?>
                    <li><a class="dropdown-item" href="adminmanager.php">Admin Account Manager</a></li>
                    <li><a class="dropdown-item" href="superadminmanager.php">Super Admin Account Manager</a></li>
                <?php endif; ?>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="logout.php">Log out</a></li>
            </ul>
        </div>
    </nav>
</head>

<body style="overflow-x: hidden;">

    <div class="container-fluid">
        <h1>Staff Account Manager <a href="#"><img align="right"
                    src="assets/add_1000dp_000_FILL0_wght400_GRAD0_opsz48.svg" style="width: 35px; display: inline;"
                    data-bs-toggle="modal" data-bs-target="#staticBackdrop" class="circular-hover-dark"></a></h1>

        <hr>

        <table class="table table-striped table-bordered table-danger" style="max-width: 800px; margin: 0 auto;">
            <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Role</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <th>Test</th>
                <th>Test</th>
                <th>Test</th>
                <th><button class="btn btn-primary btn-sm">Edit</button> <button class="btn btn-danger btn-sm">Delete</button></th>
            </tbody>
            
        </table>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</body>

</html>
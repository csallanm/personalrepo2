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
        if ($status == "verified") {
            if ($code != 0) {
                header('Location: resetcode.php');
            }
        } else {
            header('Location: userotp.php');
        }
    }
} else {
    header('Location: login.php');
}

// Initialize variables
$name = $fetch_info['name'];
$current_email = $fetch_info['email'];
$current_password = $fetch_info['password'];
$errors = [];
$modal_errors = []; // Modal only errors
$success_message = '';

// Handle form submission for updating account
if (isset($_POST['updateacc'])) {
    $name = $_POST['name'] ?: $fetch_info['name'];
    $new_email = $_POST['email'] ?: $fetch_info['email'];
    $new_password = $_POST['password'];
    $confirm_password = $_POST['cpassword'];

    // Validate email format
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "$new_email is not valid.";
    }
    // Check if the new passwords match
    elseif (!empty($new_password) && $new_password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    } elseif (!empty($new_password) && !preg_match('/^[A-Z]/', $new_password)) {
        $errors[] = "Password must start with an uppercase letter.";
    } elseif (!empty($new_password) && !preg_match('/[0-9!@#$%^&*(),.?":{}|<>]/', $new_password)) {
        $errors[] = "Password must contain at least one number or special character.";
    } else {
        // Check if the new email already exists, excluding the current user
        $check_email_sql = "SELECT email FROM tblusers WHERE email = '$new_email' AND email != '$email'";
        $check_email_result = mysqli_query($con, $check_email_sql);

        if (mysqli_num_rows($check_email_result) > 0) {
            $errors[] = "The email address is already in use.";
        } else {
            // If new password is provided, hash it
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            } else {
                $hashed_password = $current_password; // Retain current password if new one is empty
            }

            // Prepare update query
            $update_sql = "UPDATE tblusers SET name='$name', email='$new_email', password='$hashed_password' WHERE email='$email'";

            // Execute update query
            if (mysqli_query($con, $update_sql)) {
                // Update session with new email after successful update
                $_SESSION['email'] = $new_email;
                $_SESSION['success_message'] = "Account updated successfully."; // Set success message
                header("Location: accountsettings.php"); // Redirect to the same page to avoid resubmission
                exit();
            } else {
                $errors[] = "Error updating account: " . mysqli_error($con);
            }
        }
    }
}


// Handle account deletion with AJAX (DEPRECATED)
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - Student Archiving System</title>
    <link rel="icon" type="image/x-icon" href="assets/euC.png">
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="customcolors.css" />
    <link rel="stylesheet" href="effects.css" />
    <link rel="stylesheet" href="appearance.css" />
    <link rel="stylesheet" href="new_login.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />


    <nav class="navbar fixed-top navbar-expand navbar-dark shadow-lg p-3" style="background-color: #4e0000;">
        <a href="https://mseuf.edu.ph/candelaria" target="_blank"><img class="mx-3" src="assets/euC.png" alt="hugenerd" width="30" height="30"></a>
        <ul class="navbar-nav nav-pills mr-auto">
            <li class="nav-item">
                <a class="nav-link mx-2" href="homepage.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link mx-2" href="students.php">Students</a>
            </li>
        </ul>
        <div class="dropdown-pr4 ms-auto">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle mx-3" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="assets/user-icon-2048x2048-ihoxz4vq.png" alt="hugenerd" width="30" height="30" class="rounded-circle">
                <span class="d-none d-sm-inline mx-2" style="text-decoration: none; color: white;"><?php echo $fetch_info['name'] ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark text-small shadow" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" href="EUC_SAS_MANUAL.pdf" target="_blank">User Manual</a></li>
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#aboutModal">About</a></li>
                <li><a class="dropdown-item" href="accountsettings.php">Account Settings</a></li>
                <?php if ($fetch_info['role_id'] == 1 || $fetch_info['role_id'] == 2): ?>
                    <li><a class="dropdown-item" href="accountmanager.php">Staff Account Manager</a></li>
                <?php endif; ?>

                <?php if ($fetch_info['role_id'] == 2): ?>
                    <li><a class="dropdown-item" href="adminmanager.php">Admin Account Manager</a></li>
                    <li><a class="dropdown-item" href="headadminmanager.php">Head Admin Account Manager</a></li>
                <?php endif; ?>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="logout.php">Log out</a></li>
            </ul>
        </div>
    </nav>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<style>
    #login_shape1 {
        width: 80%;
        max-width: 400px;
    }
</style>

<body>
    <!-- ABOUT MODAL -->
    <div class="modal fade" id="aboutModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">About EUC Student Archiving System</h1>
                </div>
                <div class="modal-body">
                    <p><strong>EUC Student Archiving System</strong></p>
                    <p>© 2025 - CCS Department</p>
                    <p>This website is used for the registrar's office only. See user manual for guide.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-custom-color">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- ABOUT MODAL -->

    <div class="bg">
        <div class="container d-flex justify-content-center align-items-center" style="height: 85vh;">
            <div class="shadow-lg p-3 mb-4 rounded" id="login_shape1">
                <form action="accountsettings.php" method="POST" autocomplete="">
                    <h3>Account Settings</h3>
                    <p>Update your account.</p>

                    <!-- Display success message if set -->
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success">
                            <?php
                            echo $_SESSION['success_message'];
                            unset($_SESSION['success_message']); // Clear the message after displaying
                            ?>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="name">Full Name:</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="" value="<?php echo $name ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="example@domain.com" value="<?php echo $email ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="password">Password:</label>
                            <div class="input-group">
                                <input type="password" id="password" minlength="6" name="password" class="form-control" placeholder="">
                                <div class="input-group-append">
                                    <button type="button" id="togglePassword" class="btn btn-outline-secondary">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="cpassword">Confirm Password:</label>
                            <div class="input-group">
                                <input type="password" id="cpassword" minlength="6" name="cpassword" class="form-control" placeholder="">
                                <div class="input-group-append">
                                    <button type="button" id="toggleCPassword" class="btn btn-outline-secondary">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>

                    <?php
                    if (count($errors) == 1) {
                    ?>
                        <p class="text-danger">
                            <?php
                            foreach ($errors as $showerror) {
                                echo $showerror;
                            }
                            ?>
                        </p>
                    <?php
                    } elseif (count($errors) > 1) {
                    ?>
                        <p class="text-danger">
                            <?php
                            foreach ($errors as $showerror) {
                            ?>
                                <li><?php echo $showerror; ?></li>
                            <?php
                            }
                            ?>
                        </p>
                    <?php
                    }
                    ?>

                    <button class="btn btn-dark" name="updateacc" id="btn-custom-color">Update account</button>
                </form>
            </div>
        </div>


        <script src="js/bootstrap.bundle.min.js"></script>
        <script>
            // AJAX logic to handle account deletion (DEPRECATED)

            document.addEventListener('DOMContentLoaded', () => {
                const passwordInput = document.getElementById('password');
                const togglePasswordButton = document.getElementById('togglePassword');

                togglePasswordButton.addEventListener('click', () => {
                    const type = passwordInput.type === 'password' ? 'text' : 'password';
                    passwordInput.type = type;

                    togglePasswordButton.innerHTML = type === 'password' ?
                        '<i class="fa fa-eye" aria-hidden="true"></i>' :
                        '<i class="fa fa-eye-slash" aria-hidden="true"></i>';
                });

                const cpasswordInput = document.getElementById('cpassword');
                const toggleCPasswordButton = document.getElementById('toggleCPassword');

                toggleCPasswordButton.addEventListener('click', () => {
                    const type = cpasswordInput.type === 'password' ? 'text' : 'password';
                    cpasswordInput.type = type;

                    toggleCPasswordButton.innerHTML = type === 'password' ?
                        '<i class="fa fa-eye" aria-hidden="true"></i>' :
                        '<i class="fa fa-eye-slash" aria-hidden="true"></i>';
                });
            });

            document.addEventListener('DOMContentLoaded', () => {
                const deletePasswordInput = document.getElementById('deletePassword');
                const toggleDeletePasswordButton = document.getElementById('toggleDeletePassword');

                toggleDeletePasswordButton.addEventListener('click', () => {
                    const type = deletePasswordInput.type === 'password' ? 'text' : 'password';
                    deletePasswordInput.type = type;

                    toggleDeletePasswordButton.innerHTML = type === 'password' ?
                        '<i class="fa fa-eye" aria-hidden="true"></i>' :
                        '<i class="fa fa-eye-slash" aria-hidden="true"></i>';
                });
            });
        </script>
</body>

</html>
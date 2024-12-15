<?php require_once "controllerUserData.php"; ?>
<?php
$email = isset($_SESSION['email']) ? $_SESSION['email'] : false;

// Check if the user is logged in
if (!$email) {
    // User is not logged in, redirect to login
    header('Location: login.php');
    exit();
}

// Fetch user info only if they are logged in
$sql = "SELECT * FROM tblusers WHERE email = '$email'";
$run_Sql = mysqli_query($con, $sql);

if ($run_Sql) {
    $fetch_info = mysqli_fetch_assoc($run_Sql);
    $status = $fetch_info['status'];  // User verification status
    $code = $fetch_info['code'];      // Password reset code (if any)

    // If the user is verified
    if ($status == "verified") {
        // Check if the user has a reset code (indicating a reset OTP was entered)
        if ($code != 0) {
            // User has a reset code, allow access to changepassword.php
        } else {
            // No reset code, redirect to homepage.php
            if (basename($_SERVER['PHP_SELF']) != 'homepage.php') {
                header('Location: homepage.php');
                exit();
            }
        }
    } else {
        // If the user is not verified, redirect to userotp.php
        if (basename($_SERVER['PHP_SELF']) != 'userotp.php') {
            header('Location: userotp.php');
            exit();
        }
    }
}

// Reset OTP
if (isset($_GET['reset_otp'])) {
    // Reset the OTP in the database
    $update_sql = "UPDATE tblusers SET code = 0 WHERE email = '$email'";
    mysqli_query($con, $update_sql);
    // Optionally, you might want to unset the session variable for code (RESET OTP = 0)
    unset($_SESSION['otp']);
    session_unset();
    session_destroy();
    // Redirect to login after resetting OTP
    header('Location: login.php');
    exit();
}

// Restrict page when logged out/otp reset
if (basename($_SERVER['PHP_SELF']) == 'changepassword.php' && !$code) {
    // Redirect if the user is trying to access changepassword.php without a reset code
    header('Location: login.php'); // Or another appropriate page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Student Archiving System</title>
    <link rel="icon" type="image/x-icon" href="assets/euC.png">
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="new_login.css" />
    <link rel="stylesheet" href="effects.css" />
    <link rel="stylesheet" href="appearance.css" />
    <nav class="navbar fixed-top navbar-expand navbar-dark p-3" style="background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0));">
        <!-- Navbar content -->
        <a href="changepassword.php?reset_otp=true"><img align="left" src="assets/arrow_back_1000dp_FFFFFF_FILL0_wght400_GRAD0_opsz48.svg" style="width: 30px;" class="circular-hover"></a>
        <a href="https://mseuf.edu.ph/candelaria" target="_blank"><img class="mx-3" src="assets/euC.png" alt="hugenerd" width="30" height="30"></a>
        <ul class="navbar-nav nav-pills mr-auto"></ul>
    </nav>

    <script>
        // Detect browser's back button press
        window.addEventListener('popstate', function(event) {
            // Redirect to reset OTP by adding the reset_otp parameter
            window.location.href = 'resetcode.php?reset_otp=true';
        });

        // Push a new history state to help control back button behavior
        history.pushState(null, null, location.href);
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>

    <div class="bg">
        <!--code-->
        <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
            <div class="shadow-lg p-3 mb-4 rounded position-absolute " id="login_shape1">
                <h3>Change Password</h3>
                <p>Please enter new password.</p>
                <form action="changepassword.php" method="POST" autocomplete="off">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="password">Password:</label>
                            <div class="input-group">
                                <input type="password" id="password" minlength="6" name="password" class="form-control" placeholder="" required>
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
                                <input type="password" id="cpassword" minlength="6" name="cpassword" class="form-control" placeholder="" required>
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
                    if (isset($_SESSION['info'])) {
                    ?>
                        <p class="text-success">
                            <?php echo $_SESSION['info']; ?>
                        </p>
                    <?php
                    }
                    ?>
                    <?php
                    if (count($errors) > 0) {
                    ?>
                        <p class="text-danger">
                            <?php
                            foreach ($errors as $showerror) {
                                echo $showerror;
                            }
                            ?>
                        </p>
                    <?php
                    }
                    ?>

                    <input class="form-control button btn btn-secondary" type="submit" name="change-password" value="Change Password" id="btn-custom-color">
                </form>
            </div>
        </div>
    </div>

    <script>
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
    </script>

    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>
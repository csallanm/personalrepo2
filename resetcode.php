<?php require_once "controllerUserData.php"; ?>
<?php
$email = isset($_SESSION['email']) ? $_SESSION['email'] : false;

// If the user is not logged in, redirect to the login page
if (!$email) {
    header('Location: login.php');
    exit();
}

// The user is logged in; now check their reset code status
$sql = "SELECT * FROM tblusers WHERE email = '$email'";
$run_Sql = mysqli_query($con, $sql);

if ($run_Sql) {
    $fetch_info = mysqli_fetch_assoc($run_Sql);
    $status = $fetch_info['status'];
    $code = $fetch_info['code'];

    // Check if the user is verified
    if ($status == "verified") {
        // If the user has a reset code, they can access resetcode.php
        if ($code != 0) {
            // The user is allowed to stay on resetcode.php for OTP verification
            // Handle your OTP verification logic here
        } else {
            // If the user does not have a reset code, redirect to homepage
            header('Location: homepage.php');
            exit();
        }
    } else {
        // If the user is not verified, redirect to userotp.php
        header('Location: userotp.php');
        exit();
    }
} else {
    // If the SQL query fails for some reason, handle accordingly
    header('Location: login.php'); // Or handle as needed
    exit();
}

// Reset OTP logic (modify this section)
if (isset($_GET['reset_otp'])) {
    // Reset the OTP in the database
    $update_sql = "UPDATE tblusers SET code = 0 WHERE email = '$email'";
    mysqli_query($con, $update_sql);
    // Optionally, you might want to unset the session variable for code
    unset($_SESSION['otp']);
    session_unset();
    session_destroy();
    // Redirect to login or desired page after resetting the OTP
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code - Student Archiving System</title>
    <link rel="icon" type="image/x-icon" href="assets/euC.png">
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="new_login.css" />
    <link rel="stylesheet" href="effects.css" />
    <link rel="stylesheet" href="appearance.css" />

    <nav class="navbar fixed-top navbar-expand navbar-dark p-3" style="background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0));">
        <!-- Navbar content -->
        <a href="resetcode.php?reset_otp=true"><img align="left" src="assets/arrow_back_1000dp_FFFFFF_FILL0_wght400_GRAD0_opsz48.svg" style="width: 30px;" class="circular-hover"></a>
        <a href="https://mseuf.edu.ph/candelaria" target="_blank"><img class="mx-3" src="assets/euC.png" alt="hugenerd" width="30" height="30"></a>
        <ul class="navbar-nav nav-pills mr-auto"></ul>
    </nav>

    <script>
        // Push initial state to prevent immediate back navigation
        history.pushState(null, null, window.location.href);

        // Handle back button with popstate event
        window.addEventListener('popstate', function(event) {
            window.location.href = 'resetcode.php?reset_otp=true';
        });
    </script>

</head>

<body>

    <div class="bg">
        <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
            <div class="shadow-lg p-3 mb-4 rounded position-absolute" id="login_shape1">
                <h3>Code verification</h3>
                <p>Please enter code to reset your account's password.</p>
                <p>Code:</p>
                <form class="form-inline my-2 my-lg-0" action="resetcode.php" method="POST" autocomplete="off">
                    <input class="form-control w-75" name="otp" id="otp" type="number" placeholder="" required>
                    <br>
                    <?php
                    if (isset($_SESSION['info'])) {
                    ?>
                        <p class="text-success"><?php echo $_SESSION['info']; ?></p>
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
                            ?></p>
                    <?php
                    }
                    ?>
                    <div class="row">
                        <div class="col-md-6">
                            <input class="form-control button btn btn-secondary" type="submit" name="check-reset-otp" value="Submit" id="btn-custom-color">
                        </div>
                    </div>
            </div>
        </div>
        </form>
    </div>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>
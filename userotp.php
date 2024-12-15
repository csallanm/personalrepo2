<?php require_once "controllerUserData.php"; ?>

<?php
// Check if the user is already verified and avoid looping
// Check if the user is logged in
$email = isset($_SESSION['email']) ? $_SESSION['email'] : false;

if (!$email) {
    // User is not logged in, redirect to login page
    if (basename($_SERVER['PHP_SELF']) != 'login.php') {
        header('Location: login.php');
        exit();
    }
} else {
    // User is logged in, fetch their information
    $sql = "SELECT * FROM tblusers WHERE email = '$email'";
    $run_Sql = mysqli_query($con, $sql);
    
    if ($run_Sql) {
        $fetch_info = mysqli_fetch_assoc($run_Sql);
        
        // If the user is verified, redirect them to the homepage
        if ($fetch_info['status'] == "verified") {
            if (basename($_SERVER['PHP_SELF']) != 'homepage.php') {
                header('Location: homepage.php');
                exit();
            }
        }
        // If the user is not verified, they stay on userotp.php to enter their OTP
    }
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
    <!--<link rel="stylesheet" href="customcolors.css"/>-->
    <link rel="stylesheet" href="new_login.css" />
    <link rel="stylesheet" href="effects.css" />
    <link rel="stylesheet" href="appearance.css" />

    <nav class="navbar fixed-top navbar-expand navbar-dark p-3" style="background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0));;">
        <!-- Navbar content -->
        <a href="logout.php"><img align="left" src="assets/arrow_back_1000dp_FFFFFF_FILL0_wght400_GRAD0_opsz48.svg" style="width: 30px;" class="circular-hover"></a>
        <a href="https://mseuf.edu.ph/candelaria" target="_blank"><img class="mx-3" src="assets/euC.png" alt="hugenerd" width="30" height="30"></a>
    </nav>

    <script>
        // JavaScript to handle back button press
        window.addEventListener('popstate', function (event) {
            // Redirect to logout.php when back button is pressed
            window.location.href = 'logout.php';
        });
        
        // Push a new history entry to prevent automatic back navigation
        history.pushState(null, null, location.href);
    </script>

</head>

<body>

    <div class="bg">
        <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
            <div class="shadow-lg p-3 mb-4 rounded position-absolute" id="login_shape1">
                <h3>Code verification</h3>
                <p>Please enter code to verify your account.</p>
                <p>Code:</p>
                <form class="form-inline my-2 my-lg-0" action="userotp.php" method="POST" autocomplete="">
                    <input class="form-control w-75" name="otp" id="otp" type="number" placeholder="" required>
                    <br>
                    <!--<p class="text-danger" id="err"></p>-->
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
                            ?></p>
                    <?php
                    }
                    ?>
                    <div class="row">
                        <div class="col-md-6">
                            <input class="form-control button btn btn-secondary" type="submit" name="check" value="Submit" id="btn-custom-color">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>
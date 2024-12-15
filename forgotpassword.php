<?php require_once "controllerUserData.php"; 

// Middleware: Check if the user is already logged in
if (isset($_SESSION['email']) && !empty($_SESSION['password']) ) {
    header("Location: homepage.php");
  }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Student Archiving System</title>
    <link rel="icon" type="image/x-icon" href="assets/euC.png">
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <!--<link rel="stylesheet" href="customcolors.css"/>-->
    <link rel="stylesheet" href="new_login.css" />
    <link rel="stylesheet" href="effects.css" />
    <link rel="stylesheet" href="appearance.css" />

    <nav class="navbar fixed-top navbar-expand navbar-dark p-3" style="background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0));;">
        <!-- Navbar content -->
        <a href="login.php"><img align="left" src="assets/arrow_back_1000dp_FFFFFF_FILL0_wght400_GRAD0_opsz48.svg" style="width: 30px;" class="circular-hover"></a>
        <a href="https://mseuf.edu.ph/candelaria" target="_blank"><img class="mx-3" src="assets/euC.png" alt="hugenerd" width="30" height="30"></a>
        <ul class="navbar-nav nav-pills mr-auto">

        </ul>
    </nav>

</head>

<body>

    <div class="bg">
        <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
            <div class="shadow-lg p-3 mb-4 rounded position-absolute" id="login_shape1">
                <h3>Forgot password</h3>
                <p>Please enter email to get a recovery code and to change password.</p>
                <p>E-mail:</p>
                <form class="form-inline my-2 my-lg-0" action="forgotpassword.php" method="POST" autocomplete="">
                    <input class="form-control w-75" name="email" id="email" type="email" placeholder="example@domain.com" aria-label="email" required value="<?php echo $email ?>">

                    <br>
                    <!--<p class="text-danger" id="err"></p>-->
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
                            <input class="form-control button btn btn-secondary" type="submit" name="check-email" value="Get code" id="btn-custom-color">
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
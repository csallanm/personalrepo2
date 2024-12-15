<?php require_once "controllerUserData.php"; ?>
<?php
if($_SESSION['info'] == false){
    header('Location: login.php');  
}else{
    header('location: homepage.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="assets/euC.png">
    <title>Password changed - Student Archiving System</title>
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
        <!--code-->
        <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
            <div class="shadow-lg p-3 mb-4 rounded position-absolute " id="login_shape1">
                <h3>Password changed</h3>
                <!--<p>Please enter new password.</p>-->
                <?php
                if (isset($_SESSION['info'])) {
                ?>
                    <p class="text-success">
                        <?php echo $_SESSION['info']; ?>
                    </p>
                <?php
                }
                ?>
                <form action="login.php" method="POST">
                    <input class="form-control button btn btn-secondary" type="submit" name="login-now" value="Back to Login">
                </form>
            </div>
        </div>
    </div>



    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>
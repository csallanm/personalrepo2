<?php require_once "controllerUserData.php";

// Middleware: Check if the user is already logged in
if (isset($_SESSION['email']) && !empty($_SESSION['password'])) {
  header("Location: homepage.php");
}


?>
<!DOCTYPE html>
<html lang="en">



<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=devicen-width, initial-scale=1.0">
  <title>Login - Student Archiving System</title>
  <link rel="icon" type="image/x-icon" href="assets/euC.png">
  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <!--<link rel="stylesheet" href="customcolors.css"/>-->
  <link rel="stylesheet" href="new_login.css" />
  <link rel="stylesheet" href="effects.css" />
  <link rel="stylesheet" href="appearance.css" />

  <nav class="navbar fixed-top navbar-expand navbar-dark p-3" style="background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0));;">
    <!-- Navbar content -->
    <a href="https://mseuf.edu.ph/candelaria" target="_blank"><img class="mx-3" src="assets/euC.png" alt="hugenerd" width="30" height="30"></a>
    <ul class="navbar-nav nav-pills mr-auto">

    </ul>
  </nav>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


</head>

<body>

  <div class="bg">
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
      <div class="shadow-lg p-3 mb-4 rounded position-absolute" id="login_shape1">
        <h3>Login User</h3>
        <p>Enter your email and password to login.</p>
        <p>E-mail:</p>
        <form class="form-inline my-2 my-lg-0" action="login.php" method="POST" autocomplete="">
          <input class="form-control w-75" name="email" id="email" type="email" placeholder="example@domain.com" aria-label="email" required value="<?php echo $email ?>">
          <br>
          <p>Password:</p>
          <div class="input-group mb-3">
            <input class="form-control w-75" name="password" id="password" type="password" placeholder="" aria-label="password" required>
            <div class="input-group-append">
              <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                <i class="fa fa-eye" aria-hidden="true"></i>
              </button>
            </div>
          </div>

          <br>
          <!--p class="text-danger" id="err"></p>-->
          <?php
          if (count($errors) > 0) {
          ?>
            <p class="text-danger"><?php
                                    foreach ($errors as $showerror) {
                                      echo $showerror;
                                    }
                                    ?></p>
          <?php
          }
          ?>

          <?php
          if (isset($_SESSION['info'])) {
          ?>
            <p class="text-success">
              <?php echo $_SESSION['info']; ?>
            </p>
          <?php
          }
          ?>

          <div class="row">
            <div class="col-md-6">
              <input class="form-control button btn btn-secondary" type="submit" name="login" value="Login" id="btn-custom-color">
            </div>
            <div class="col-md-6">
              <button class="btn btn-secondary" name="btn_fpassword" id="btn-custom-color" onclick="window.location.href='forgotpassword.php'">Forgot Password</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const passwordInput = document.getElementById('password');
      const toggleButton = document.getElementById('togglePassword');

      toggleButton.addEventListener('click', () => {
        // Toggle the input type and the icon
        const type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = type;

        // Toggle the icon
        toggleButton.innerHTML = type === 'password' ?
          '<i class="fa fa-eye" aria-hidden="true"></i>' :
          '<i class="fa fa-eye-slash" aria-hidden="true"></i>';
      });
    });
  </script>
</body>

</html>
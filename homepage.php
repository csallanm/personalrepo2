<?php require_once "controllerUserData.php"; ?>
<?php
// Check if the user is logged in (email and password set)
$email = isset($_SESSION['email']) ? $_SESSION['email'] : false;
$password = isset($_SESSION['password']) ? $_SESSION['password'] : false;

if (!$email || !$password) {
  // User is not logged in, redirect to login
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
    $status = $fetch_info['status'];  // User verification status
    $code = $fetch_info['code'];      // Password reset code (if any)

    // If the user is verified
    if ($status == "verified") {
      // If the user has a reset code, redirect to resetcode.php
      if ($code != 0 && basename($_SERVER['PHP_SELF']) != 'resetcode.php') {
        header('Location: resetcode.php');
        exit();
      } else if (basename($_SERVER['PHP_SELF']) != 'homepage.php') {
        // If no reset code, send the user to homepage.php
        header('Location: homepage.php');
        exit();
      }
    } else {
      // If the user is not verified, redirect to userotp.php
      if (basename($_SERVER['PHP_SELF']) != 'userotp.php') {
        header('Location: userotp.php');
        exit();
      }
    }
  }
}

// count the students enrolled, total and for respective courses
$query = "
    SELECT 
      (SELECT COUNT(*) FROM tblstudents) AS total_population,
      (SELECT COUNT(*) FROM tblstudents WHERE course = 'BSA') AS bsa_population,
      (SELECT COUNT(*) FROM tblstudents WHERE course = 'BSHM') AS bshm_population,
      (SELECT COUNT(*) FROM tblstudents WHERE course = 'BSBA') AS bsba_population,
      (SELECT COUNT(*) FROM tblstudents WHERE course = 'BSN') AS bsn_population,
      (SELECT COUNT(*) FROM tblstudents WHERE course = 'BSCoE') AS bscoe_population,
      (SELECT COUNT(*) FROM tblstudents WHERE course = 'ABPsy') AS abpsy_population,
      (SELECT COUNT(*) FROM tblstudents WHERE course IN ('BSCS', 'ACS')) AS bscs_acs_population,
      (SELECT COUNT(*) FROM tblstudents WHERE course = 'BSTM') AS bstm_population,
      (SELECT COUNT(*) FROM tblstudents WHERE course IN ('BEEd', 'BSEd')) AS beed_bsed_population
";

$result = $con->query($query);
$data = $result->fetch_assoc();

$con->close();


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome - Student Archiving System</title>
  <link rel="icon" type="image/x-icon" href="assets/euC.png">
  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <link rel="stylesheet" href="customcolors.css" />
  <link rel="stylesheet" href="appearance.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <nav class="navbar fixed-top navbar-expand navbar-dark shadow-lg p-3" style="background-color: #4e0000;">
    <!-- Navbar content -->
    <a href="https://mseuf.edu.ph/candelaria" target="_blank"><img class="mx-3" src="assets/euC.png" alt="hugenerd" width="30" height="30"></a>
    <ul class="navbar-nav nav-pills mr-auto">
      <li class="nav-item">
        <a class="nav-link active mx-2" href="homepage.php">Home</a>
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


</head>

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

  <div class="container-fluid">
    <h3>Welcome to Student Archiving System<br><?php echo $fetch_info['name'] ?>!</h3>
  </div>

  <div class="container-fluid m-3">
    <p><img src="assets/help_sas.png" id="imgsize1">
      Need help using it? Click <a href="EUC_SAS_MANUAL.pdf" target="_blank" style="text-decoration: none; color: maroon;">HELP</a>!<br><br> For an offline copy: <a href="EUC_SAS_MANUAL.pdf" target="_blank" style="color: maroon; text-decoration: none;" download>Download Manual</a>

    </p>
  </div>

  </div>

  <div class="container" id="con1">
    <div class="shadow-lg p-3 mb-4 rounded position-absolute" id="shape1">
      <h4>Did you know?</h4>
      <!-- Spinner container -->
      <div id="loadingSpinner" class="spinner-border text-light" role="status" style="display:none;">
        <span class="visually-hidden">Loading...</span>
      </div>
      <!-- Fact paragraph -->
      <p id="fact">Loading... Please wait for a few moments.</p>
    </div>
  </div>


  <div class="container" id="con2">
    <div class="shadow-lg p-3 mb-4 rounded position-absolute" id="shape2">
      <h4>Population</h4>
      <div class="row">
        <div class="col">
          <h5><?php echo $data['total_population']; ?></h5>
          <p>College students enrolled</p>
        </div>
      </div>

      <h4>Course Population</h4>
      <div class="row">
        <div class="col">
          <h5><?php echo $data['bsa_population']; ?></h5>
          <p><b>BSA</b> students enrolled</p>
        </div>
        <div class="col">
          <h5><?php echo $data['bshm_population']; ?></h5>
          <p><b>BSHM</b> students enrolled</p>
        </div>
      </div>

      <div class="row">
        <div class="col">
          <h5><?php echo $data['bsba_population']; ?></h5>
          <p><b>BSBA</b> students enrolled</p>
        </div>
        <div class="col">
          <h5><?php echo $data['bsn_population']; ?></h5>
          <p><b>BSN</b> students enrolled</p>
        </div>
      </div>

      <div class="row">
        <div class="col">
          <h5><?php echo $data['bscoe_population']; ?></h5>
          <p><b>BSCoE</b> students enrolled</p>
        </div>
        <div class="col">
          <h5><?php echo $data['abpsy_population']; ?></h5>
          <p><b>ABPsy</b> students enrolled</p>
        </div>
      </div>

      <div class="row">
        <div class="col">
          <h5><?php echo $data['bscs_acs_population']; ?></h5>
          <p><b>BSCS & ACS</b> students enrolled</p>
        </div>
        <div class="col">
          <h5><?php echo $data['bstm_population']; ?></h5>
          <p><b>BSTM</b> students enrolled</p>
        </div>
      </div>

      <div class="row">
        <div class="col">
          <h5><?php echo $data['beed_bsed_population']; ?></h5>
          <p><b>BEEd & BSEd</b> students enrolled</p>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Fetch and display the random fact when the page loads
    window.onload = function() {
      getRandomFact();
    };

    function getRandomFact() {
      // Show the spinner
      document.getElementById('loadingSpinner').style.display = 'inline-block';
      document.getElementById('fact').style.display = 'none'; // Hide the fact text while loading

      fetch('http://localhost:5000/api/facts') // this is the server address to get the random fact from API

        .then(response => response.json())
        .then(data => {
          // Hide the spinner and show the fact
          document.getElementById('loadingSpinner').style.display = 'none';
          document.getElementById('fact').style.display = 'block';
          document.getElementById('fact').innerText = data.fact; // Access fact field
        })
        .catch(error => {
          console.error('Error:', error);
          // Hide the spinner and show an error message
          document.getElementById('loadingSpinner').style.display = 'none';
          document.getElementById('fact').style.display = 'block';
          document.getElementById('fact').innerText = 'Something went wrong. Please try again later.';
        });
    }
  </script>

  <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>
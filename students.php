<?php require_once "controllerUserData.php"; ?>
<?php
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

// Default course is set to BSA.
$program = isset($_GET['program']) ? $_GET['program'] : 'All';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students - Student Archiving System</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="icon" type="image/x-icon" href="assets/euC.png">
    <link rel="stylesheet" href="customcolors.css" />
    <link rel="stylesheet" href="effects.css" />
    <link rel="stylesheet" href="appearance.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />


    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <nav class="navbar fixed-top navbar-expand navbar-dark shadow-lg p-3" style="background-color: #4e0000;">
        <!-- Navbar content -->
        <a href="https://mseuf.edu.ph/candelaria" target="_blank"><img class="mx-3" src="assets/euC.png" alt="hugenerd" width="30" height="30"></a>
        <ul class="navbar-nav nav-pills mr-auto">
            <li class="nav-item">
                <a class="nav-link mx-2" href="homepage.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active mx-2" href="students.php">Students</a>
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

<style>
    .form-group {
        margin-bottom: 1rem;
    }

    .col-form-label {
        min-width: 145px;
        /* Set a minimum width to align labels consistently */
        text-align: left;
        /* Align text to the right to match input fields */
    }

    .form-control,
    .form-select {
        width: 100%;
        /* Ensure inputs and selects are uniformly wide */
    }

    .info-container {
        display: flex;
        flex-direction: column;
        gap: 8px;
        /* spacing between rows */
    }

    /* Row styling */
    .info-row {
        display: flex;
        align-items: center;
    }

    /* Label styling for consistent width */
    .info-row strong {
        min-width: 150px;
        /* adjust width for alignment */
        text-align: left;
        padding-right: 10px;
        /* space between label and value */
        font-weight: bold;
    }

    /* Value styling */
    .info-row span {
        flex: 1;
        /* occupies remaining space */
        text-align: left;
    }

    .custom-select-width {
        width: 205px;
        /* Set your desired width */
    }
</style>

<body style="overflow-x: hidden;">
    <!-- MODALS -->

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

    <!-- Insert Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Add Students</h1>
                </div>
                <div class="modal-body">
                    <form action="uploadimage.php" id="studentform" method="POST" enctype="multipart/form-data">
                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">First Name:</label>
                                </div>
                                <div class="col-auto">
                                    <input id="custom-textbox" class="form-control" type="text" name="fname" placeholder="" required>
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">Last Name:</label>
                                </div>
                                <div class="col-auto">
                                    <input id="custom-textbox" class="form-control" type="text" name="lname" placeholder="" required>
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center form-group">
                                <div class="col-auto">
                                    <!-- Label with Popover on Hover -->
                                    <label for="inputInline" class="col-form-label"
                                        data-bs-toggle="popover"
                                        data-bs-trigger="hover"
                                        data-bs-placement="right"
                                        data-bs-content="If the student doesn't have a middle name, you can leave it blank.">
                                        Middle Name <strong>(optional)</strong>:
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <input id="custom-textbox" class="form-control" type="text" name="midname" placeholder="">
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label"
                                        data-bs-toggle="popover"
                                        data-bs-trigger="hover"
                                        data-bs-placement="right"
                                        data-bs-content="If the student doesn't have a middle name, you can leave it blank.">Middle Initial <strong>(optional)</strong>:</label>
                                </div>
                                <div class="col-auto">
                                    <input id="custom-textbox" class="form-control" type="text" name="minit" placeholder="">
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">Suffix <strong>(optional)</strong>:</label>
                                </div>
                                <div class="col-auto">
                                    <input id="custom-textbox" class="form-control" type="text" name="suffix" placeholder="II, III, Jr., ...">
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center form-group">
                                <div class="col-auto">
                                    <label for="course" class="col-form-label">Course:</label>
                                </div>
                                <div class="col-auto">
                                    <select class="form-select" id="courseAdd" name="course">
                                        <option value="BSA">BSA</option>
                                        <option value="BSBA">BSBA</option>
                                        <option value="BSCoE">BSCoE</option>
                                        <option value="BSCS">BSCS</option>
                                        <option value="ACS">ACS</option>
                                        <option value="BEEd">BEEd</option>
                                        <option value="BSEd">BSEd</option>
                                        <option value="BSHM">BSHM</option>
                                        <option value="BSN">BSN</option>
                                        <option value="ABPsy">ABPsy</option>
                                        <option value="BSTM">BSTM</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">Section <strong>(optional)</strong>:</label>
                                </div>
                                <div class="col-auto">
                                    <input id="custom-textbox" class="form-control" type="text" name="section" placeholder="">
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center form-group">
                                <div class="col-auto">
                                    <label for="major" class="col-form-label">Major:</label>
                                </div>
                                <div class="col-auto">
                                    <select class="form-select custom-select-width" id="majorAdd" name="major">
                                        <option value="-">-</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">Year Level:</label>
                                </div>
                                <div class="col-auto">
                                    <select id="custom-textbox" class="form-select" id="ylvl" name="ylvl">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center form-group">
                                <div class="col-auto">
                                    <!-- Label with Popover on Hover -->
                                    <label for="inputInline" class="col-form-label"
                                        data-bs-toggle="popover"
                                        data-bs-trigger="hover"
                                        data-bs-placement="right"
                                        data-bs-content="Year of Batch Graduated, this is optional. (Value will set to 0 by default when left blank after adding)">
                                        Batch <strong>(optional)</strong>:
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <input id="custom-textbox" class="form-control" type="text" id="batch" name="batch" placeholder="2025">
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center form-group">
                                <div class="col-auto">
                                    <!-- Label with Popover on Hover -->
                                    <label for="inputInline" class="col-form-label"
                                        data-bs-toggle="popover"
                                        data-bs-trigger="hover"
                                        data-bs-placement="right"
                                        data-bs-content="The Student ID No. that you're going to assign is permanent. Double check Student ID No. before submission.">
                                        Student ID No.:
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <input id="custom-textbox" class="form-control" type="text" name="sid" placeholder="A00-1234" required>
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">Email:</label>
                                </div>
                                <div class="col-auto">
                                    <input id="custom-textbox" class="form-control" type="email" name="email" placeholder="example@domain.com" required>
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">Address:</label>
                                </div>
                                <div class="col-auto">
                                    <textarea class="form-control" name="address" rows="5" style="width: 100%;" placeholder="" required></textarea>
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">Phone number:</label>
                                </div>
                                <div class="col-auto">
                                    <input id="custom-textbox" class="form-control" type="number" name="phone" placeholder="" required>
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">Age:</label>
                                </div>
                                <div class="col-auto">
                                    <input id="addage" class="form-control" type="number" name="age" placeholder="" readonly>
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">Birthday:</label>
                                </div>
                                <div class="col-auto">
                                    <input id="addbday" class="form-control" type="date" name="bday" placeholder="" required>
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">Sex:</label>
                                </div>
                                <div class="col-auto">
                                    <select id="custom-textbox" class="form-select" id="sex" name="sex">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <hr>

                        <p class="md-2"></p>

                        <label for="">2x2 Photo:</label>
                        <input class="form-control" type="file" name="image" id="" accept="image/*" required>

                        <p class="md-2"></p>

                        <hr>

                        <p class="md-2"></p>

                        <label for="">PSA Document:</label>
                        <input class="form-control" type="file" name="file_psa" id="" accept=".pdf, .doc., .docx, .png, .jpeg, .jpg, .gif">

                        <p class="md-2"></p>

                        <label for="">Good Moral Document:</label>
                        <input class="form-control" type="file" name="file_goodmoral" id="" accept=".pdf, .doc., .docx, .png, .jpeg, .jpg, .gif">

                        <p class="md-2"></p>

                        <label for="">Transfer Credential Document:</label>
                        <input class="form-control" type="file" name="file_transcre" id="" accept=".pdf, .doc., .docx, .png, .jpeg, .jpg, .gif">

                        <p class="md-2"></p>

                        <label for="">Transcript of Records Document:</label>
                        <input class="form-control" type="file" name="file_tor" id="" accept=".pdf, .doc., .docx, .png, .jpeg, .jpg, .gif">

                        <p class="md-2"></p>

                        <label for="">Permanent Record Document:</label>
                        <input class="form-control" type="file" name="file_permrec" id="" accept=".pdf, .doc., .docx, .png, .jpeg, .jpg, .gif">

                        <p class="md-2"></p>

                        <label for="">Form 137 Document:</label>
                        <input class="form-control" type="file" name="file_f137" id="" accept=".pdf, .doc., .docx, .png, .jpeg, .jpg, .gif">

                        <p class="md-2"></p>

                        <label for="">Form 9 Document:</label>
                        <input class="form-control" type="file" name="file_f9" id="" accept=".pdf, .doc., .docx, .png, .jpeg, .jpg, .gif">

                        <p class="md-2"></p>

                        <label for="">Marriage Contract Document <strong>(if married)</strong>:</label>
                        <input class="form-control" type="file" name="file_marcon" id="" accept=".pdf, .doc., .docx, .png, .jpeg, .jpg, .gif">

                        <p class="md-2"></p>

                        <label for="">Other files <strong>(optional)</strong>:</label>
                        <input class="form-control" type="file" name="o_file[]" id="" accept=".pdf, .doc., .docx, .png, .jpeg, .jpg, .gif" multiple>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-custom-color">Close</button>
                    <button class="btn btn-dark" name="submit" id="btn-custom-color">Submit</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Insert Modal -->

    <!-- View Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Student info</h1>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-custom-color">Close</button>
                    <button type='button' class='btn btn-secondary edit_data' data-bs-toggle="modal" data-bs-target="#editdata" data-bs-dismiss='modal' id="btn-custom-color">Edit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- View Modal -->

    <!-- Edit Modal -->
    <div class="modal fade" id="editdata" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editdataLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editdataLabel">Edit Student</h1>
                </div>
                <div class="modal-body">
                    <form action="uploadimage.php" id="studentform" method="POST" enctype="multipart/form-data">
                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">First Name:</label>
                                </div>
                                <div class="col-auto">
                                    <input id="fname" class="form-control" type="text" name="fname" placeholder="" required>
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center form-group">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">Last Name:</label>
                                </div>
                                <div class="col-auto">
                                    <input id="lname" class="form-control" type="text" name="lname" placeholder="" required>
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label"
                                        data-bs-toggle="popover"
                                        data-bs-trigger="hover"
                                        data-bs-placement="right"
                                        data-bs-content="If the student doesn't have a middle name, you can leave it blank.">Middle Name <strong>(optional)</strong>:</label>
                                </div>
                                <div class="col-auto">
                                    <input id="midname" class="form-control" type="text" name="midname" placeholder="">
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label"
                                        data-bs-toggle="popover"
                                        data-bs-trigger="hover"
                                        data-bs-placement="right"
                                        data-bs-content="If the student doesn't have a middle name, you can leave it blank.">Middle Initial <strong>(optional)</strong>:</label>
                                </div>
                                <div class="col-auto">
                                    <input id="minit" class="form-control" type="text" name="minit" placeholder="">
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">Suffix <strong>(optional)</strong>:</label>
                                </div>
                                <div class="col-auto">
                                    <input id="suffix" class="form-control" type="text" name="suffix" placeholder="II, III, Jr., ...">
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center form-group">
                                <div class="col-auto">
                                    <label for="course" class="col-form-label">Course:</label>
                                </div>
                                <div class="col-auto">
                                    <select class="form-select" id="courseEdit" name="course">
                                        <option value="BSA">BSA</option>
                                        <option value="BSBA">BSBA</option>
                                        <option value="BSCoE">BSCoE</option>
                                        <option value="BSCS">BSCS</option>
                                        <option value="ACS">ACS</option>
                                        <option value="BEEd">BEEd</option>
                                        <option value="BSEd">BSEd</option>
                                        <option value="BSHM">BSHM</option>
                                        <option value="BSN">BSN</option>
                                        <option value="ABPsy">ABPsy</option>
                                        <option value="BSTM">BSTM</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">Section <strong>(optional)</strong>:</label>
                                </div>
                                <div class="col-auto">
                                    <input id="section" class="form-control" type="text" name="section" placeholder="">
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center form-group">
                                <div class="col-auto">
                                    <label for="major" class="col-form-label">Major:</label>
                                </div>
                                <div class="col-auto">
                                    <select class="form-select custom-select-width" id="majorEdit" name="major">
                                        <option value="-">-</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">Year Level:</label>
                                </div>
                                <div class="col-auto">
                                    <select id="ylvl" class="form-select" id="ylvl" name="ylvl">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center form-group">
                                <div class="col-auto">
                                    <!-- Label with Popover on Hover -->
                                    <label for="inputInline" class="col-form-label"
                                        data-bs-toggle="popover"
                                        data-bs-trigger="hover"
                                        data-bs-placement="right"
                                        data-bs-content="Year of Batch Graduated, this is optional. (0 is set by default when you leave it blank in adding students)">
                                        Batch <strong>(optional)</strong>:
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <input id="batch" class="form-control" type="text" name="batch" placeholder="2025">
                                </div>
                            </div>
                        </div>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <input id="sid" class="form-control" type="hidden" name="sid" placeholder="A00-1234" required>
                                </div>
                            </div>
                        </div>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">Email:</label>
                                </div>
                                <div class="col-auto">
                                    <input id="email" class="form-control" type="email" name="email" placeholder="example@domain.com" required>
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">Address:</label>
                                </div>
                                <div class="col-auto">
                                    <textarea id="address" class="form-control" name="address" rows="5" style="width: 100%;" placeholder="" required></textarea>
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">Phone number:</label>
                                </div>
                                <div class="col-auto">
                                    <input id="phone" class="form-control" type="number" name="phone" placeholder="" required>
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">Age:</label>
                                </div>
                                <div class="col-auto">
                                    <input id="editage" class="form-control" type="number" name="age" placeholder="" readonly>
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">Birthday:</label>
                                </div>
                                <div class="col-auto">
                                    <input id="editbday" class="form-control" type="date" name="bday" placeholder="">
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">Sex:</label>
                                </div>
                                <div class="col-auto">
                                    <select id="sex" class="form-select" id="sex" name="sex">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <hr>

                        <p class="md-2"></p>

                        <label for="">2x2 Photo:</label>
                        <input class="form-control" type="file" name="image" id="" accept="image/*">

                        <p class="md-2"></p>

                        <hr>

                        <p class="md-2"></p>

                        <label for="">PSA Document:</label>
                        <input class="form-control" type="file" name="file_psa" id="file_psa" accept=".pdf, .doc., .docx, .png, .jpeg, .jpg, .gif">

                        <p class="md-2"></p>

                        <label for="">Good Moral Document:</label>
                        <input class="form-control" type="file" name="file_goodmoral" id="file_goodmoral" accept=".pdf, .doc., .docx, .png, .jpeg, .jpg, .gif">

                        <p class="md-2"></p>

                        <label for="">Transfer Credential Document:</label>
                        <input class="form-control" type="file" name="file_transcre" id="file_transcre" accept=".pdf, .doc., .docx, .png, .jpeg, .jpg, .gif">

                        <p class="md-2"></p>

                        <label for="">Transcript of Records Document:</label>
                        <input class="form-control" type="file" name="file_tor" id="file_tor" accept=".pdf, .doc., .docx, .png, .jpeg, .jpg, .gif">

                        <p class="md-2"></p>

                        <label for="">Permanent Record Document:</label>
                        <input class="form-control" type="file" name="file_permrec" id="file_permrec" accept=".pdf, .doc., .docx, .png, .jpeg, .jpg, .gif">

                        <p class="md-2"></p>

                        <label for="">Form 137 Document:</label>
                        <input class="form-control" type="file" name="file_f137" id="file_f137" accept=".pdf, .doc., .docx, .png, .jpeg, .jpg, .gif">

                        <p class="md-2"></p>

                        <label for="">Form 9 Document:</label>
                        <input class="form-control" type="file" name="file_f9" id="file_f9" accept=".pdf, .doc., .docx, .png, .jpeg, .jpg, .gif">

                        <p class="md-2"></p>

                        <label for="">Marriage Contract Document <strong>(if married)</strong>:</label>
                        <input class="form-control" type="file" name="file_marcon" id="file_marcon" accept=".pdf, .doc., .docx, .png, .jpeg, .jpg, .gif">

                        <p class="md-2"></p>

                        <label for="">Other files <strong>(optional)</strong>:</label>
                        <input class="form-control" type="file" name="o_file[]" id="o_file" accept=".pdf, .doc., .docx, .png, .jpeg, .jpg, .gif" multiple>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-custom-color">Close</button>
                    <button class="btn btn-dark" name="update_data" id="btn-custom-color">Update</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Modal -->


    <div class="container-fluid">
        <h1>Students <a href="#"><img align="right" src="assets/add_1000dp_000_FILL0_wght400_GRAD0_opsz48.svg" style="width: 35px; display: inline;" data-bs-toggle="modal" data-bs-target="#staticBackdrop" class="circular-hover-dark"></a></h1>

        <?php
        if (isset($_SESSION['status']) && $_SESSION['status'] != '') {
        ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Notification:</strong> <?php echo $_SESSION['status']; // Show the notification
                                                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php
            unset($_SESSION['status']); // Clear the session status after displaying
        }
        ?>

    </div>

    <!-- Navigation -->
    <!-- Navigation -->
    <div class="container m-3">
        <ul class="nav nav-pills">
            <li class="nav-item px-3">
                <a class="nav-link <?php echo (empty($_GET['search']) && (!isset($_GET['program']) || $_GET['program'] === 'All')) ? 'active' : ''; ?>" href="?program=All" id="nav2">All</a>
            </li>
            <li class="nav-item px-3">
                <a class="nav-link <?php echo (empty($_GET['search']) && isset($_GET['program']) && $_GET['program'] === 'BSA') ? 'active' : ''; ?>" aria-current="page" href="?program=BSA" id="nav2">BSA</a>
            </li>
            <li class="nav-item px-3">
                <a class="nav-link <?php echo (empty($_GET['search']) && isset($_GET['program']) && $_GET['program'] === 'BSBA') ? 'active' : ''; ?>" href="?program=BSBA" id="nav2">BSBA</a>
            </li>
            <li class="nav-item px-3">
                <a class="nav-link <?php echo (empty($_GET['search']) && isset($_GET['program']) && $_GET['program'] === 'BSCS') ? 'active' : ''; ?>" href="?program=BSCS" id="nav2">BSCS</a>
            </li>
            <li class="nav-item px-3">
                <a class="nav-link <?php echo (empty($_GET['search']) && isset($_GET['program']) && $_GET['program'] === 'ACS') ? 'active' : ''; ?>" href="?program=ACS" id="nav2">ACS</a>
            </li>
            <li class="nav-item px-3">
                <a class="nav-link <?php echo (empty($_GET['search']) && isset($_GET['program']) && $_GET['program'] === 'BSCoE') ? 'active' : ''; ?>" href="?program=BSCoE" id="nav2">BSCoE</a>
            </li>
            <li class="nav-item px-3">
                <a class="nav-link <?php echo (empty($_GET['search']) && isset($_GET['program']) && $_GET['program'] === 'BEEd') ? 'active' : ''; ?>" href="?program=BEEd" id="nav2">BEEd</a>
            </li>
            <li class="nav-item px-3">
                <a class="nav-link <?php echo (empty($_GET['search']) && isset($_GET['program']) && $_GET['program'] === 'BSEd') ? 'active' : ''; ?>" href="?program=BSEd" id="nav2">BSEd</a>
            </li>
            <li class="nav-item px-3">
                <a class="nav-link <?php echo (empty($_GET['search']) && isset($_GET['program']) && $_GET['program'] === 'BSHM') ? 'active' : ''; ?>" href="?program=BSHM" id="nav2">BSHM</a>
            </li>
            <li class="nav-item px-3">
                <a class="nav-link <?php echo (empty($_GET['search']) && isset($_GET['program']) && $_GET['program'] === 'BSN') ? 'active' : ''; ?>" href="?program=BSN" id="nav2">BSN</a>
            </li>
            <li class="nav-item px-3">
                <a class="nav-link <?php echo (empty($_GET['search']) && isset($_GET['program']) && $_GET['program'] === 'ABPsy') ? 'active' : ''; ?>" href="?program=ABPsy" id="nav2">ABPsy</a>
            </li>
            <li class="nav-item px-3">
                <a class="nav-link <?php echo (empty($_GET['search']) && isset($_GET['program']) && $_GET['program'] === 'BSTM') ? 'active' : ''; ?>" href="?program=BSTM" id="nav2">BSTM</a>
            </li>
        </ul>
    </div>


    <hr class="p-2">

    <!-- Sorting and Search Box -->
    <div class="container">
        <div class="d-flex align-items-center mb-4">
            <ul class="navbar-nav mb-0 me-3">
                <li class="nav-item dropdown">
                    <button class="btn btn-dark dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        Sort Items
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="?sort=year-asc&program=<?php echo $program; ?>">Year Level (lower-higher year)</a></li>
                        <li><a class="dropdown-item" href="?sort=year-desc&program=<?php echo $program; ?>">Year Level (higher-lower year)</a></li>
                        <li><a class="dropdown-item" href="?sort=lastname-asc&program=<?php echo $program; ?>">Last Name A-Z</a></li>
                        <li><a class="dropdown-item" href="?sort=lastname-desc&program=<?php echo $program; ?>">Last Name Z-A</a></li>
                        <li><a class="dropdown-item" href="?sort=batch-asc&program=<?php echo $program; ?>">Batch (earliest-latest)</a></li>
                        <li><a class="dropdown-item" href="?sort=batch-desc&program=<?php echo $program; ?>">Batch (latest-earliest)</a></li>
                    </ul>
                </li>
            </ul>

            <div class="input-group mb-0">
                <form method="GET" action="" style="display: flex; width: 100%;">
                    <input type="text" class="form-control" name="search" placeholder="Search" aria-label="Search" aria-describedby="basic-addon2" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" style="background-color:rgb(255, 233, 233);">
                    <button class="btn btn-light" type="submit" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                        <img src="assets/search_1000dp_000000_FILL0_wght400_GRAD0_opsz48.svg" style="width: 20px; height: 20px;">
                    </button>
                </form>
            </div>
        </div>

        <!-- User List -->
        <div class="container">
            <div class="row d-flex justify-content-center align-items-center">
                <?php
                // Initialize variables for pagination and filtering
                // Initialize variables for pagination and filtering
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $recordsperpage = 40; // maximum number of records per page
                $offset = ($page - 1) * $recordsperpage;
                $sortOption = isset($_GET['sort']) ? $_GET['sort'] : 'year-asc';

                // Set default course/program to 'BSA' if not specified in the query string
                $program = isset($_GET['program']) ? $_GET['program'] : 'All';

                // Define the ORDER BY clause based on the selected sorting option
                switch ($sortOption) {
                    case 'year-asc':
                        $orderBy = 'ylvl ASC';
                        break;
                    case 'year-desc':
                        $orderBy = 'ylvl DESC';
                        break;
                    case 'lastname-asc':
                        $orderBy = 'lname ASC';
                        break;
                    case 'lastname-desc':
                        $orderBy = 'lname DESC';
                        break;
                    case 'batch-asc':
                        $orderBy = 'batch ASC';
                        break;
                    case 'batch-desc':
                        $orderBy = 'batch DESC';
                        break;
                    default:
                        $orderBy = 'ylvl ASC';
                        break;
                }

                // Get the search query if it exists
                $searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

                // Modify the SQL query to include search and program filter if present
                if (!empty($searchQuery)) {
                    // If a search is performed, ignore the program filter
                    $sql = "SELECT * FROM tblstudents WHERE (fname LIKE '%$searchQuery%' OR lname LIKE '%$searchQuery%' OR minit LIKE '%$searchQuery%')";
                    if ($sortOption == 'batch-asc' || $sortOption == 'batch-desc') {
                        $sql .= " AND batch != 0"; // Exclude students with batch = 0
                    }
                    $sql .= " ORDER BY $orderBy LIMIT $recordsperpage OFFSET $offset";
                } else {
                    // Default query without search
                    $sql = "SELECT * FROM tblstudents";
                    if ($program !== 'All') {
                        $sql .= " WHERE course='$program'";
                    }
                    // If sorting by batch, exclude students with batch = 0
                    if ($sortOption == 'batch-asc' || $sortOption == 'batch-desc') {
                        $sql .= ($program !== 'All' ? " AND" : " WHERE") . " batch != 0";
                    }
                    $sql .= " ORDER BY $orderBy LIMIT $recordsperpage OFFSET $offset";
                }

                $result = $con->query($sql);


                // Count the total number of records for pagination calculation
                // Define the SQL query to count records based on search and filters
                if (!empty($searchQuery)) {
                    // Count records for the search query
                    $totalRecordsQuery = "SELECT COUNT(*) FROM tblstudents WHERE (fname LIKE '%$searchQuery%' OR lname LIKE '%$searchQuery%' OR minit LIKE '%$searchQuery%')";
                    if ($program !== 'All') {
                        $totalRecordsQuery .= " AND course='$program'";
                    }
                    if ($sortOption == 'batch-asc' || $sortOption == 'batch-desc') {
                        $totalRecordsQuery .= " AND batch != 0";
                    }
                } else {
                    // Default count query with optional program filter
                    $totalRecordsQuery = "SELECT COUNT(*) FROM tblstudents";
                    if ($program !== 'All') {
                        $totalRecordsQuery .= " WHERE course='$program'";
                    }
                    if ($sortOption == 'batch-asc' || $sortOption == 'batch-desc') {
                        $totalRecordsQuery .= ($program !== 'All' ? " AND" : " WHERE") . " batch != 0";
                    }
                }

                $totalRecordsResult = $con->query($totalRecordsQuery);
                $totalRecords = $totalRecordsResult->fetch_array()[0];
                $totalPages = ceil($totalRecords / $recordsperpage); // Calculate pages based on filtered total



                $totalRecordsResult = $con->query($totalRecordsQuery);
                $totalRecords  = $totalRecordsResult->fetch_array()[0];
                $totalPages = ceil($totalRecords / $recordsperpage);

                // Display students if found
                if ($result->num_rows > 0) {
                    while ($row = mysqli_fetch_array($result)) {
                        $fname = $row["fname"];
                        $lname = $row["lname"];
                        $minit = $row["minit"];
                        $course = $row["course"];
                        $ylvl = $row["ylvl"];
                        $batch = $row["batch"];
                        $sid = $row["sid"];
                        $suffix = $row["suffix"];
                        $fileName = $row["filename"];

                        // Define student folder and construct the image URL
                        $studentFolder = "uploads/" . $sid . "/";
                        $imageUrl = $studentFolder . $fileName; // Adjusted to include student folder

                        echo "<div class='col-md-4 col-lg-3 mb-3 d-flex justify-content-center'>";
                        echo "<div class='shadow card' style='width: 19rem;' data-student-id='$sid' id='studshape'>";
                        echo "<img id='stud-img' src='$imageUrl' class='card-img-top' alt='...'>";
                        echo "<div class='card-body' style='height: 145px;'>"; // Set fixed height here
                        echo "<h6 class='card-title'>$fname <p class='d-inline'>$minit</p>. <p class='d-inline'>$lname</p> $suffix</h6>";
                        echo "<p class='card-text'>$course - $ylvl</p>";
                        echo "<p>" . (!empty($batch) && $batch != 0 ? $batch : '') . "</p>";
                        echo "<p class='student-id' style='display: none;'>Student No.: $sid</p>";
                        echo "<a href='#' class='stretched-link view_data' data-bs-toggle='modal' data-bs-target='#exampleModal'></a>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<div style='display: flex; align-items: center;'>";
                    echo "<img src='assets/norecords.png' width='150' style='margin-right: 10px;'>";
                    echo "<div>";
                    echo "<h2 style='margin: 0;'>No records found</h2>";
                    echo "<p style='margin: 0;'>Try adding students data or adjust the course filters.</p>";
                    echo "</div>";
                    echo "</div>";
                }
                ?>

                <!-- Pagination -->
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center">
                        <!-- Previous -->
                        <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&sort=<?php echo $sortOption; ?>&search=<?php echo $searchQuery; ?>&program=<?php echo $program; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only">Previous</span>
                            </a>
                        </li>

                        <!-- Page Numbers -->
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&sort=<?php echo $sortOption; ?>&search=<?php echo $searchQuery; ?>&program=<?php echo $program; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <!-- Next Button -->
                        <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&sort=<?php echo $sortOption; ?>&search=<?php echo $searchQuery; ?>&program=<?php echo $program; ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                                <span class="sr-only">Next</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <button id="backToTop" class="btn btn-secondary" style="display: none; position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
                Back to Top
            </button>
        </div>



        <script>
            // AJAX SCRIPT
            // VIEW STUDENTS
            // Wait until the document is fully loaded
            $(document).ready(function() {
                // Handle the click event on the view_data link
                $(document).on("click", ".view_data", function() {
                    // Get the student ID from the parent card's data attribute
                    var studentId = $(this).closest(".card").data("student-id");
                    sessionStorage.setItem("studentId", studentId);

                    // Optional: Show some loading indicator while fetching data
                    $("#exampleModal .modal-body").html("Loading...");

                    // Make an AJAX request to fetch student data based on studentId
                    $.ajax({
                        url: "fetch_student.php", // The PHP script that returns student details (fetch the student)
                        type: "POST",
                        data: {
                            id: studentId
                        },
                        success: function(response) {
                            // Update the modal body with the student info returned from PHP
                            $("#exampleModal .modal-body").html(response);
                        },
                        error: function(xhr, status, error) {
                            // Handle errors
                            console.error("Error fetching student data:", error);
                            $("#exampleModal .modal-body").html("Error loading data.");
                        }
                    });
                });
            });

            // EDIT STUDENTS (part 1)
            $(document).ready(function() {
                $(document).on("click", ".edit_data", function(e) {
                    var studentId = sessionStorage.getItem("studentId");
                    console.log(`Retrieved: ${studentId}`);

                    $.ajax({
                        type: "POST",
                        url: "uploadimage.php",
                        data: {
                            'click_edit_btn': true,
                            'sid': studentId,
                        },
                        dataType: 'json',
                        success: function(response) {
                            console.log(response);

                            if (response.error) {
                                alert(response.error);
                                return;
                            }

                            $.each(response, function(key, value) {
                                $('#fname').val(value['fname']);
                                $('#lname').val(value['lname']);
                                $('#midname').val(value['midname']);
                                $('#minit').val(value['minit']);
                                $('#suffix').val(value['suffix']);

                                // Populate the course dropdown in the edit modal
                                $('#courseEdit').val(value['course']);

                                // Populate the major dropdown based on course selection in the edit modal
                                const selectedCourse = value['course'];
                                const majorSelect = $('#majorEdit');
                                majorSelect.html('<option value="-">-</option>');

                                if (majors[selectedCourse]) {
                                    majors[selectedCourse].forEach(major => {
                                        const option = document.createElement("option");
                                        option.value = major;
                                        option.textContent = major;
                                        majorSelect.append(option);
                                    });
                                }

                                // Set the specific major from the response data in the edit modal
                                $('#majorEdit').val(value['major']);
                                $('#section').val(value['section']);
                                $('#ylvl').val(value['ylvl']);
                                $('#batch').val(value['batch']);
                                $('#sid').val(value['sid']);
                                $('#email').val(value['email']);
                                $('#address').val(value['address']);
                                $('#phone').val(value['phone']);
                                $('#editage').val(value['age']);
                                $('#editbday').val(value['bday']);
                                $('#sex').val(value['sex']);
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Error: ', error);
                            alert("An error occurred while processing the request.");
                        }
                    });
                });
            });

            // Popover Hover
            document.addEventListener('DOMContentLoaded', function() {
                var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
                var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
                    return new bootstrap.Popover(popoverTriggerEl);
                });
            });

            // Majors
            const majors = {
                BSBA: ["Financial Management", "Marketing Management", "Human Resource Development Management"],
                BSEd: ["Mathematics", "Science", "English", "Filipino", "Social Studies"],
                BEEd: ["General Education"],
                BSCoE: ["STEM", "Non-STEM"]
                // Add more course-major options as needed
            };

            // For Add Student modal
            document.getElementById("courseAdd").addEventListener("change", function() {
                const majorSelect = document.getElementById("majorAdd");
                majorSelect.innerHTML = '<option value="-">-</option>';
                const selectedCourse = this.value;
                const options = majors[selectedCourse];

                if (options) {
                    options.forEach(major => {
                        const option = document.createElement("option");
                        option.value = major;
                        option.textContent = major;
                        majorSelect.appendChild(option);
                    });
                }
            });

            // For Edit Student modal
            document.getElementById("courseEdit").addEventListener("change", function() {
                const majorSelect = document.getElementById("majorEdit");
                majorSelect.innerHTML = '<option value="-">-</option>';
                const selectedCourse = this.value;
                const options = majors[selectedCourse];

                if (options) {
                    options.forEach(major => {
                        const option = document.createElement("option");
                        option.value = major;
                        option.textContent = major;
                        majorSelect.appendChild(option);
                    });
                }
            });

            $(document).ready(function() {
                // Check scroll position on page load
                if ($(window).scrollTop() > 100) {
                    $('#backToTop').fadeIn();
                }

                // Show the button when the user scrolls down 100 pixels
                $(window).scroll(function() {
                    if ($(this).scrollTop() > 100) {
                        $('#backToTop').fadeIn();
                    } else {
                        $('#backToTop').fadeOut();
                    }
                });

                // Scroll to top instantly when the button is clicked
                $('#backToTop').click(function() {
                    $('html, body').scrollTop(0); // Set scroll position to 0 immediately
                    return false;
                });
            });

            // birthday autocompute for adding students
            document.addEventListener("DOMContentLoaded", function() {
                const ageInput = document.querySelector('#addage');
                const birthdayInput = document.querySelector('#addbday');

                // When the age field changes, calculate the approximate birthday
                ageInput.addEventListener("input", function() {
                    const age = parseInt(ageInput.value);
                    if (age >= 0) {
                        const today = new Date();
                        const birthYear = today.getFullYear() - age;
                        const birthDate = new Date(birthYear, today.getMonth(), today.getDate());
                        // Set the calculated birthday
                        birthdayInput.value = birthDate.toISOString().split('T')[0];
                    } else {
                        birthdayInput.value = ''; // Clear if invalid age
                    }
                });

                // When the birthday field changes, calculate the age
                birthdayInput.addEventListener("change", function() {
                    const birthday = new Date(birthdayInput.value);
                    const today = new Date();
                    let age = today.getFullYear() - birthday.getFullYear();
                    const monthDifference = today.getMonth() - birthday.getMonth();

                    // Adjust age if the birthday hasn't occurred yet this year
                    if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthday.getDate())) {
                        age--;
                    }

                    // Set the calculated age
                    ageInput.value = age >= 0 ? age : '';
                });
            });

            // birthday autocompute for editing students
            document.addEventListener("DOMContentLoaded", function() {
                const ageInput = document.querySelector('#editage');
                const birthdayInput = document.querySelector('#editbday');

                // When the age field changes, calculate the approximate birthday
                ageInput.addEventListener("input", function() {
                    const age = parseInt(ageInput.value);
                    if (age >= 0) {
                        const today = new Date();
                        const birthYear = today.getFullYear() - age;
                        const birthDate = new Date(birthYear, today.getMonth(), today.getDate());
                        // Set the calculated birthday
                        birthdayInput.value = birthDate.toISOString().split('T')[0];
                    } else {
                        birthdayInput.value = ''; // Clear if invalid age
                    }
                });

                // When the birthday field changes, calculate the age
                birthdayInput.addEventListener("change", function() {
                    const birthday = new Date(birthdayInput.value);
                    const today = new Date();
                    let age = today.getFullYear() - birthday.getFullYear();
                    const monthDifference = today.getMonth() - birthday.getMonth();

                    // Adjust age if the birthday hasn't occurred yet this year
                    if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthday.getDate())) {
                        age--;
                    }

                    // Set the calculated age
                    ageInput.value = age >= 0 ? age : '';
                });
            });
        </script>

        <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>
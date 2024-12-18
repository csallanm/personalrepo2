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

            if ($role_id != 2) {
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

// fetch super admin
$fetch_query = "SELECT id, name, email, tblroles.role_name FROM tblusers JOIN tblroles ON tblusers.role_id = tblroles.role_id WHERE tblusers.role_id = ?;";

$stmt = $con->prepare($fetch_query);
$super_admin_role_id = 2;
$stmt->bind_param("i", $super_admin_role_id);
$stmt->execute();
$result = $stmt->get_result();

// Define how many results per page
$resultsPerPage = 10;

// Get the current page from the URL, default to 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';

// Calculate the offset for the SQL query
$offset = ($page - 1) * $resultsPerPage;

// Modify your SQL query to search for super admins
$sql = "SELECT users.id, users.name, users.email, roles.role_name
        FROM tblusers users
        JOIN tblroles roles ON users.role_id = roles.role_id
        WHERE users.role_id = 2";

if ($search != '') {
    // Add search filter based on name only (remove email search)
    $sql .= " AND users.name LIKE '%$search%'";
}

// Add LIMIT and OFFSET for pagination
$sql .= " LIMIT $resultsPerPage OFFSET $offset";

$result = $con->query($sql);

// Check if the query was successful
if ($result === false) {
    die("Error: " . $con->error);
}

// Get the total number of rows for pagination calculation
$totalResultQuery = "SELECT COUNT(*) AS total FROM tblusers WHERE role_id = 2";
if ($search != '') {
    $totalResultQuery .= " AND name LIKE '%$search%'";
}
$totalResult = $con->query($totalResultQuery);
$totalRow = $totalResult->fetch_assoc();
$totalRows = $totalRow['total'];

// Calculate the total number of pages
$totalPages = ceil($totalRows / $resultsPerPage);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Manager - Student Archiving System</title>
    <link rel="icon" type="image/x-icon" href="assets/euC.png">
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="customcolors.css" />
    <link rel="stylesheet" href="effects.css" />
    <link rel="stylesheet" href="appearance.css" />
    <link rel="stylesheet" href="new_login.css" />

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

    <!-- Super Admin Add Modal -->
    <div class="modal fade" id="addSuperAdmin" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Add Super Admin</h1>
                </div>
                <div class="modal-body">
                    <form action="backend_superad.php" method="POST">
                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">Full Name:</label>
                                </div>
                                <div class="col-auto">
                                    <input id="custom-textbox" class="form-control" type="text" name="name" placeholder="" required>
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
                                    <input id="custom-textbox" class="form-control" type="email" name="email" placeholder="" required>
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">Password:</label>
                                </div>
                                <div class="col-auto">
                                    <div class="input-group">
                                        <input id="password-field" minlength="6" class="form-control" type="password" name="password" placeholder="" required>
                                        <button type="button" id="toggle-password" class="btn btn-outline-secondary">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="btn-custom-color" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="addsuperadmin" id="btn-custom-color">Submit</button>
                </div>
            </div>
            </form>
        </div>
    </div>

    <!-- Edit Super Admin Add Modal -->
    <div class="modal fade" id="editSuperAdmin" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Super Admin</h1>
                </div>
                <div class="modal-body">
                    <form action="backend_superad.php" method="POST">
                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">Full Name:</label>
                                </div>
                                <div class="col-auto">
                                    <input id="custom-textbox" class="form-control" type="text" name="name" placeholder="">
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="id" value="<?php echo $super_admin_role_id; ?>">

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">Email:</label>
                                </div>
                                <div class="col-auto">
                                    <input id="custom-textbox" class="form-control" type="email" name="email" placeholder="">
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="inputInline" class="col-form-label">New Password:</label>
                                </div>
                                <div class="col-auto">
                                    <div class="input-group">
                                        <input id="new-password-field" minlength="6" class="form-control" type="password" name="npassword" placeholder="">
                                        <button type="button" id="new-toggle-password" class="btn btn-outline-secondary">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="md-2"></p>

                        <?php if ($fetch_info['role_id'] == 2): ?>
                            <div class="container">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <label for="inputInline" class="col-form-label">Role:</label>
                                    </div>
                                    <div class="col-auto">
                                        <div class="input-group">
                                            <select class="form-select" name="role" aria-label="Default select example">
                                                <option value="0" <?php echo ($fetch_info['role_id'] == 0 ? 'selected' : ''); ?>>Staff</option>
                                                <option value="1" <?php echo ($fetch_info['role_id'] == 1 ? 'selected' : ''); ?>>Admin</option>
                                                <option value="2" <?php echo ($fetch_info['role_id'] == 2 ? 'selected' : ''); ?>>Super Admin</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="container">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="input-group">
                                            <select class="form-select" name="role" aria-label="Default select example" hidden>
                                                <option value="0" <?php echo ($fetch_info['role_id'] == 0 ? 'selected' : ''); ?>>Staff</option>
                                                <option value="1" <?php echo ($fetch_info['role_id'] == 1 ? 'selected' : ''); ?>>Admin</option>
                                                <option value="2" <?php echo ($fetch_info['role_id'] == 2 ? 'selected' : ''); ?>>Super Admin</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>


                        <p class="md-2"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="btn-custom-color" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="updatesuperadmin" id="btn-custom-color">Submit</button>
                </div>
            </div>
            </form>
        </div>
    </div>

    <div class="container-fluid">
        <h1>Super Admin Account Manager <a href="#"><img align="right" src="assets/add_1000dp_000_FILL0_wght400_GRAD0_opsz48.svg" style="width: 35px; display: inline;" data-bs-toggle="modal" data-bs-target="#addSuperAdmin" class="circular-hover-dark"></a></h1>
        <p>To edit or update your own Super Admin account, go to <a href="accountsettings.php" style="text-decoration: none; color: maroon;">Account Settings</a> to update your account.</p>

        <!-- Display Success/Errors if any -->
        <?php if (isset($_SESSION['success']) && $_SESSION['success'] != ''): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Success:</strong> <?php echo $_SESSION['success']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
            <?php foreach ($_SESSION['errors'] as $error): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>Error:</strong> <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endforeach; ?>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        <hr>

        <!-- Search Form -->
        <div class="input-group mb-0 justify-content-center">
            <form method="GET" action="" style="display: flex; width: 50%;">
                <input type="text" class="form-control" name="search" placeholder="Search Super Admin" aria-label="Search" aria-describedby="basic-addon2" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" style="background-color:rgb(255, 233, 233);">
                <button class="btn btn-light" type="submit" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                    <img src="assets/search_1000dp_000000_FILL0_wght400_GRAD0_opsz48.svg" style="width: 20px; height: 20px;">
                </button>
            </form>
        </div>

        <br>

        <!-- Table for Super Admins -->
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
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['role_name']); ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm" data-id="<?php echo $row['id']; ?>" id="btn-custom-color" data-bs-target="#editSuperAdmin" data-bs-toggle="modal"
                                    <?php if ($fetch_info['role_id'] == 2 && $row['email'] == $fetch_info['email']) {
                                        echo 'style="display:none;"';
                                    } ?>>
                                    Edit
                                </button>
                                <?php if (!($fetch_info['role_id'] == 2 && $row['email'] == $fetch_info['email'])): ?>
                                    <!-- Delete Button to Open Modal -->
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteSuperAdmin<?php echo $row['id']; ?>" id="btn-custom-color">Delete</button>
                                <?php endif; ?>
                            </td>

                            <!-- Modal for Each Super Admin -->
                            <div class="modal fade" id="deleteSuperAdmin<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="deleteSuperAdminLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteSuperAdminLabel">Delete Super Admin?</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to delete <strong><?php echo htmlspecialchars($row['name']); ?></strong>?
                                            <form action="backend_superad.php" method="POST">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" id="btn-custom-color" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" name="deletesuperadmin" id="btn-custom-color" class="btn btn-danger">Delete</button>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center"><strong>No Super Admin found.</strong> Try to add new one or check spelling when you search.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <br>

        <!-- Pagination -->
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                <!-- Previous -->
                <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo $search; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                        <span class="sr-only">Previous</span>
                    </a>
                </li>

                <!-- Page Numbers -->
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <!-- Next Button -->
                <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo $search; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">Next</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        document.getElementById('toggle-password').addEventListener('click', function() {
            const passwordField = document.getElementById('password-field');
            const toggleIcon = this.querySelector('i');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.add('fa-eye-slash');
                toggleIcon.classList.remove('fa-eye');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        })

        document.getElementById('new-toggle-password').addEventListener('click', function() {
            const passwordField = document.getElementById('new-password-field');
            const toggleIcon = this.querySelector('i');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.add('fa-eye-slash');
                toggleIcon.classList.remove('fa-eye');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        })

        // Data populate
        $(document).ready(function() {
            // Attach click event listener to Edit buttons
            $('.btn[data-id]').on('click', function() {
                const userId = $(this).data('id'); // Get user ID from the button's data-id attribute

                // Send AJAX request to fetch user details
                $.ajax({
                    url: 'backend_superad.php',
                    type: 'POST',
                    data: {
                        populate: true,
                        id: userId
                    },
                    dataType: 'json', // Expect JSON response
                    success: function(response) {
                        if (response.success) {
                            // Populate the modal fields with data from the response
                            $('#editSuperAdmin input[name="name"]').val(response.name);
                            $('#editSuperAdmin input[name="email"]').val(response.email);
                            $('#editSuperAdmin input[name="id"]').val(response.id);

                            // Set the role in the select dropdown using the role_id
                            $('#editSuperAdmin select[name="role"]').val(response.role_id); // Set the role_id directly here
                        } else {
                            alert('Failed to fetch user data. Please try again.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        alert('An error occurred while fetching user data. Please try again.');
                    }
                });
            });
        });
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</body>

</html>
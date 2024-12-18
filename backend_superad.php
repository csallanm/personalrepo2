<?php

session_start();
include "connection.php";

// Add Super Admin
if (isset($_POST['addsuperadmin'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $errors = []; // Initialize an array for errors

    // Password validation
    if (!preg_match('/^[A-Z]/', $password)) {
        $errors[] = "Password must start with an uppercase letter.";
    }
    if (!preg_match('/[0-9!@#$%^&*(),.?":{}|<>]/', $password)) {
        $errors[] = "Password must contain at least one number or special character.";
    }

    // Email uniqueness check
    $check_email = "SELECT * FROM tblusers WHERE email = ?";
    $stmt = $con->prepare($check_email);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errors[] = "The email already exists.";
    }

    // Validate email (if provided)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "$email is not valid.";
    }

    if (empty($errors)) {
        // If no errors, proceed with adding the super admin
        $add_superadmin = "INSERT INTO tblusers (name, email, password, code, role_id, status) VALUES (?, ?, ?, ?, ?, ?)";
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $autoverify = "verified";
        $code = 0;
        $role_id = 2;
        $addstmt = $con->prepare($add_superadmin);

        if ($addstmt) {
            $addstmt->bind_param("sssiis", $name, $email, $hashedPassword, $code, $role_id, $autoverify);
            if ($addstmt->execute()) {
                $_SESSION['success'] = "Head admin added successfully.";
                header("Location: headadminmanager.php");
                exit();
            } else {
                $_SESSION['errors'][] = "Failed to add super admin. Please try again.";
            }
            $addstmt->close();
        } else {
            $_SESSION['errors'][] = "Database error: Unable to prepare statement.";
        }
    } else {
        $_SESSION['errors'] = $errors; // Store validation errors in the session
    }
    header("Location: headadminmanager.php");
    exit();
}

// Edit Super Admin (populate data)
if (isset($_POST['populate']) && isset($_POST['id'])) {
    $id = intval($_POST['id']); // Sanitize input

    // Query to fetch user data along with the role name
    $query = "
        SELECT 
            u.id, u.name, u.email, u.role_id, r.role_name 
        FROM 
            tblusers u 
        INNER JOIN 
            tblroles r 
        ON 
            u.role_id = r.role_id 
        WHERE 
            u.id = ?";

    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            'success' => true,
            'id' => $row['id'],
            'name' => $row['name'],
            'email' => $row['email'],
            'role_id' => $row['role_id'], // Send role_id in the response
            'role_name' => $row['role_name'] // Send role_name in the response
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
    }

    exit;
}

// Update Super Admin Account
if (isset($_POST['updatesuperadmin'])) {
    // Ensure the super admin ID is passed in the request for the update
    if (!isset($_POST['id'])) {
        die("Error: Super admin ID not provided.");
    }

    $id = intval($_POST['id']); // Sanitize input
    $errors = []; // Initialize an array for errors

    // Debugging: Check the ID being passed
    // var_dump($id);

    // Get current data of the user before update
    $query = "SELECT * FROM tblusers WHERE id = ?";
    $stmt = $con->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $currentData = $result->fetch_assoc();
        $stmt->close();
    } else {
        $errors[] = "Database error: Unable to fetch user data.";
    }

    // If user data is not found
    if (!$currentData) {
        $errors[] = "User data not found.";
    }

    // If there are no errors, proceed with the update
    if (empty($errors)) {
        // Retain existing data if fields are blank
        $name = !empty($_POST['name']) ? mysqli_real_escape_string($con, $_POST['name']) : $currentData['name'];
        $email = !empty($_POST['email']) ? mysqli_real_escape_string($con, $_POST['email']) : $currentData['email'];
        $password = !empty($_POST['npassword']) ? mysqli_real_escape_string($con, $_POST['npassword']) : null;

        // Validate email if it's provided
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "$email is not valid.";
        }

        // Check if the email already exists for another user
        if (!empty($email)) {
            $checkEmailQuery = "SELECT id FROM tblusers WHERE email = ? AND id != ?";
            $stmt = $con->prepare($checkEmailQuery);
            if ($stmt) {
                $stmt->bind_param("si", $email, $id);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $errors[] = "The email $email is already taken by another user.";
                }
                $stmt->close();
            } else {
                $errors[] = "Database error: Unable to check email.";
            }
        }

        // Password validation (if a new password is provided)
        if (!empty($password)) {
            if (!preg_match('/^[A-Z]/', $password)) {
                $errors[] = "Password must start with an uppercase letter.";
            }
            if (!preg_match('/[0-9!@#$%^&*(),.?":{}|<>]/', $password)) {
                $errors[] = "Password must contain at least one number or special character.";
            }
        }

        // Handle role update
        $role_id = intval($_POST['role']); // Sanitize role input
        if (empty($role_id) && $role_id !== 0) {
            $errors[] = "Role is required.";
        } else {
            // Ensure the provided role ID exists in the database
            $roleQuery = "SELECT role_id FROM tblroles WHERE role_id = ?";
            $stmt = $con->prepare($roleQuery);
            if ($stmt) {
                $stmt->bind_param("i", $role_id);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows === 0) {
                    $errors[] = "Invalid role selected.";
                }
                $stmt->close();
            } else {
                $errors[] = "Database error: Unable to validate role.";
            }
        }

        // If no errors, proceed with updating the super admin data
        if (empty($errors)) {
            $update_query = "UPDATE tblusers SET name = ?, email = ?, password = ?, role_id = ? WHERE id = ?";
            $stmt = $con->prepare($update_query);

            if ($stmt) {
                // Hash password if it's provided
                $hashedPassword = !empty($password) ? password_hash($password, PASSWORD_BCRYPT) : $currentData['password'];

                $stmt->bind_param("sssii", $name, $email, $hashedPassword, $role_id, $id);
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Head admin updated successfully.";
                    header("Location: headadminmanager.php");
                    exit();
                } else {
                    $errors[] = "Failed to update super admin. Please try again.";
                }
                $stmt->close();
            } else {
                $errors[] = "Database error: Unable to prepare update statement.";
            }
        }
    }

    // Store validation errors in session
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
    }

    // Redirect back to the super admin manager
    header("Location: headadminmanager.php");
    exit();
}


// Delete Super Admin
if (isset($_POST['deletesuperadmin'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']); // Sanitize the input

    // Prepare the DELETE query
    $delete_superadmin = "DELETE FROM tblusers WHERE id = ?";
    $deletestmt = $con->prepare($delete_superadmin);

    if ($deletestmt) {
        $deletestmt->bind_param("i", $id); // Bind the id parameter
        if ($deletestmt->execute()) {
            $_SESSION['success'] = "Head admin account deleted successfully.";
        } else {
            $_SESSION['errors'][] = "Unable to delete the account. Please try again.";
        }
        $deletestmt->close();
    } else {
        $_SESSION['errors'][] = "Database error: Unable to prepare statement.";
    }
    header("Location: headadminmanager.php");
    exit();
}

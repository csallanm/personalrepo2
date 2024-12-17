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
                $_SESSION['success'] = "Super admin added successfully.";
                header("Location: superadminmanager.php");
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
    header("Location: superadminmanager.php");
    exit();
}

// Edit Super Admin (populate data)
if (isset($_POST['populate']) && isset($_POST['id'])) {
    $id = intval($_POST['id']); // Sanitize input
    $query = "SELECT name, email FROM tblusers WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'name' => $row['name'], 'email' => $row['email']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
    }

    exit;
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
            $_SESSION['success'] = "Super admin account deleted successfully.";
        } else {
            $_SESSION['errors'][] = "Unable to delete the account. Please try again.";
        }
        $deletestmt->close();
    } else {
        $_SESSION['errors'][] = "Database error: Unable to prepare statement.";
    }
    header("Location: superadminmanager.php");
    exit();
}

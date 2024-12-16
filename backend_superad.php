<?php

session_start();
include "connection.php";

// Add Super Admin
if (isset($_POST['addsuperadmin'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    // check if the password does not meet the requirements
    /*

    Password requirement:
    6   characters long, at least one symbol or number, capital letter first

    */

    if (!preg_match('/^[A-Z]/', $password)) {
        $errors['password'] = "Password must start with an uppercase letter.";
    } else if (!preg_match('/[0-9!@#$%^&*(),.?":{}|<>]/', $password)) {
        $errors['password'] = "Password must contain at least one number or special character.";
    }

    // Check if the email already exist
    $check_email = "SELECT * FROM tblusers WHERE email = ?";
    $stmt = $con->prepare($check_email);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errors['email'] = "The email already exists";
    }

    // If no errors found, proceed with adding super admin
    $add_superadmin = "INSERT INTO tblusers (name, email, password, code, role_id, status) VALUES (?, ?, ?, ?, ?, ?)";
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $autoverify = "verified";
    $code = 0;
    $role_id = 2;
    $addstmt = $con->prepare($add_superadmin);

    if ($addstmt) {
        $addstmt->bind_param("sssiis", $name, $email, $hashedPassword, $code, $role_id, $autoverify);

        if ($addstmt->execute()) {
            header("Location: superadminmanager.php");
            exit;
        }

        $addstmt->close();
    } else {
        $_SESSION['errors'] = ["Database error: Unable to prepare statement."];
        header("Location: superadminmanager.php");
        exit();
    }
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
            // Success: Redirect with a success message
            $_SESSION['success'] = "Super admin account deleted successfully.";
            header("Location: superadminmanager.php");
            exit();
        } else {
            // Query execution error
            $_SESSION['errors'] = ["Unable to delete the account. Please try again."];
            header("Location: superadminmanager.php");
            exit();
        }
        $deletestmt->close();
    } else {
        // Statement preparation error
        $_SESSION['errors'] = ["Database error: Unable to prepare statement."];
        header("Location: superadminmanager.php");
        exit();
    }
}

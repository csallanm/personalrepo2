<?php

session_start();
require "connection.php";
$email = "";
$name = "";
$errors = array();

//if user signup button (DEPRECATED)
/*if (isset($_POST['signup'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $cpassword = mysqli_real_escape_string($con, $_POST['cpassword']);

    // Check for password requirements
    if ($password !== $cpassword) {
        $errors['password'] = "Password and confirm password do not match.";
    } elseif (!preg_match('/^[A-Z]/', $password)) {
        $errors['password'] = "Password must start with an uppercase letter.";
    } elseif (!preg_match('/[0-9!@#$%^&*(),.?":{}|<>]/', $password)) {
        $errors['password'] = "Password must contain at least one number or special character.";
    }

    // Check if the email already exists
    $email_check = "SELECT * FROM tblusers WHERE email = '$email'";
    $res = mysqli_query($con, $email_check);
    if (mysqli_num_rows($res) > 0) {
        $errors['email'] = "Email already exists.";
    }

    if (count($errors) === 0) {
        $encpass = password_hash($password, PASSWORD_BCRYPT);
        $code = rand(999999, 111111);
        $status = "notverified";
        $insert_data = "INSERT INTO tblusers (name, email, password, code, status)
                        values('$name', '$email', '$encpass', '$code', '$status')";
        $data_check = mysqli_query($con, $insert_data);
        if ($data_check) {
            $subject = "EUC SAS - Verify your account";
            $message = "Hello $name! Your verification code is $code";
            $sender = "From: system@eucsas.ccsdepartment.com";
            if (mail($email, $subject, $message, $sender)) {
                $info = "Please check your email \"$email\" to verify your account.";
                $_SESSION['info'] = $info;
                $_SESSION['email'] = $email;
                $_SESSION['password'] = $password;
                header('location: userotp.php');
                exit();
            } else {
                $errors['otp-error'] = "An error occurred while sending code.";
            }
        } else {
            $errors['db-error'] = "An error occurred while inserting data into database!";
        }
    }
}*/

//if user click verification code submit button (DEPRECATED)
if (isset($_POST['check'])) {
    $_SESSION['info'] = "";
    $otp_code = mysqli_real_escape_string($con, $_POST['otp']);
    $check_code = "SELECT * FROM tblusers WHERE code = $otp_code";
    $code_res = mysqli_query($con, $check_code);
    if (mysqli_num_rows($code_res) > 0) {
        $fetch_data = mysqli_fetch_assoc($code_res);
        $fetch_code = $fetch_data['code'];
        $email = $fetch_data['email'];
        $code = 0;
        $status = 'verified';
        $update_otp = "UPDATE tblusers SET code = $code, status = '$status' WHERE code = $fetch_code";
        $update_res = mysqli_query($con, $update_otp);
        if ($update_res) {
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            session_unset();
            session_destroy();
            header('location: login.php');
            exit();
        } else {
            $errors['otp-error'] = "An error occurred while updating code.";
        }
    } else {
        $errors['otp-error'] = "Wrong code.";
    }
}

//if user click login button
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $check_email = "SELECT * FROM tblusers WHERE email = '$email'";
    $res = mysqli_query($con, $check_email);
    if (mysqli_num_rows($res) > 0) {
        $fetch = mysqli_fetch_assoc($res);
        $fetch_pass = $fetch['password'];
        if (password_verify($password, $fetch_pass)) {
            $_SESSION['email'] = $email;
            $status = $fetch['status'];
            if ($status == 'verified') {
                $_SESSION['email'] = $email;
                $_SESSION['password'] = $password;
                header('location: homepage.php');
            } else {
                $info = "Account \"$email\" is not verified. Please verify your account first.";
                $_SESSION['info'] = $info;
                header('location: userotp.php');
            }
        } else {
            $errors['email'] = "Wrong email or password.";
        }
    } else {
        $errors['email'] = "This account does not exist.";
    }
}

//if user click continue button in forgot password form
if (isset($_POST['check-email'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $check_email = "SELECT * FROM tblusers WHERE email='$email'";
    $run_sql = mysqli_query($con, $check_email);
    if (mysqli_num_rows($run_sql) > 0) {
        $code = rand(999999, 111111);
        $insert_code = "UPDATE tblusers SET code = $code WHERE email = '$email'";
        $run_query =  mysqli_query($con, $insert_code);
        if ($run_query) {
            $subject = "EUC SAS - Update Password";
            $message = "Hello! Your password reset code is $code";
            $sender = "From: system@eucsas.ccsdepartment.com";
            if (mail($email, $subject, $message, $sender)) {
                $info = "Email sent to \"$email\" for password reset.";
                $_SESSION['info'] = $info;
                $_SESSION['email'] = $email;
                header('location: resetcode.php');
                exit();
            } else {
                $errors['otp-error'] = "An error occurred while sending code.";
            }
        } else {
            $errors['db-error'] = "Something went wrong, please try again.";
        }
    } else {
        $errors['email'] = "This email address does not exist.";
    }
}

//if user click check reset otp button
if (isset($_POST['check-reset-otp'])) {
    $_SESSION['info'] = "";
    $otp_code = mysqli_real_escape_string($con, $_POST['otp']);
    $check_code = "SELECT * FROM tblusers WHERE code = $otp_code";
    $code_res = mysqli_query($con, $check_code);
    if (mysqli_num_rows($code_res) > 0) {
        $fetch_data = mysqli_fetch_assoc($code_res);
        $email = $fetch_data['email'];
        $_SESSION['email'] = $email;
        $info = "Change your password. Strong passwords are highly encouraged.";
        $_SESSION['info'] = $info;
        header('location: changepassword.php');
        exit();
    } else {
        $errors['otp-error'] = "Wrong code.";
    }
}

//if user click change password button
if (isset($_POST['change-password'])) {
    $_SESSION['info'] = "";
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $cpassword = mysqli_real_escape_string($con, $_POST['cpassword']);

    // Check for password requirements
    if ($password !== $cpassword) {
        $errors['password'] = "Password and confirm password do not match.";
    } elseif (!preg_match('/^[A-Z]/', $password)) {
        $errors['password'] = "Password must start with an uppercase letter.";
    } elseif (!preg_match('/[0-9!@#$%^&*(),.?":{}|<>]/', $password)) {
        $errors['password'] = "Password must contain at least one number or special character.";
    }

    if (count($errors) === 0) {
        $code = 0;
        $email = $_SESSION['email']; // Getting this email using session
        $encpass = password_hash($password, PASSWORD_BCRYPT);
        $update_pass = "UPDATE tblusers SET code = $code, password = '$encpass' WHERE email = '$email'";
        $run_query = mysqli_query($con, $update_pass);
        if ($run_query) {
            $info = "Password changed. You can now login.";
            $_SESSION['info'] = $info;
            header('Location: passwordchanged.php');
        } else {
            $errors['db-error'] = "Error while changing password.";
        }
    }
}


//if login now button click
if (isset($_POST['login-now'])) {
    header('Location: login.php');
}

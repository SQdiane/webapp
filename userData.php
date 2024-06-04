<?php 
session_start();
require_once 'db_connect.php';
require_once 'mailconf.php';
$errors = array(); // Initialize errors array

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$username = '';
$email = '';

//if user signup button
if( isset($_POST['signup']) ){
    // Get the posted form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    $encpass = password_hash($password, PASSWORD_BCRYPT);
    $code = rand(111111, 999999);
    // Validate input
    if (empty($username)) {
        $errors['username'] = "Username is required";
    }
    if (empty($email)) {
        $errors['email'] = "Email is required";
    }
    if (empty($password)) {
        $errors['password'] = "Password is required";
    }
    if ($password !== $cpassword) {
        $errors['cpassword'] = "Passwords do not match";
}
    if (checkUsernameExist($conn, $username)) {
        $errors['username'] = "Username that you have entered already exists!";
    }
    if (checkEmailExist($conn, $email)) {
        $errors['email'] = "Email that you have entered already exists!";
    }

    if(count($errors) === 0){
        // Use parameterized query to prevent SQL injection
        $query = "INSERT INTO profile (username, email, userpass, vericode) VALUES ($1, $2, $3, $4)";
        $result = pg_query_params($conn, $query, array($username, $email, $encpass, $code));

        if($result){
            if (sendVerificationEmail($email, $code)) {
                $info = "We've sent a verification code to your email - $email";
                $_SESSION['info'] = $info;
                $_SESSION['email'] = $email;
                $_SESSION['password'] = $password;
                header('Location: user-otp.php');
                exit();
            } else {
                echo 'Failed to send verification email.';
            }
        }else{
            $errors['db-error'] = "Failed while inserting data into database: " . pg_last_error($conn);
        }
    }
}

//if user click verification code submit button
if( isset($_POST['check']) ){
    $_SESSION['info'] = "";
    $otp_code = $_POST['otp'];
    $check_code_query = "SELECT * FROM profile WHERE vericode = $1";
    $check_code_result = pg_query_params($conn, $check_code_query, array($otp_code));

    if (pg_num_rows($check_code_result) > 0){
        $fetch_data = pg_fetch_assoc($check_code_result);
        $fetch_code = $fetch_data['vericode'];
        $email = $fetch_data['email'];
        $code = 0;
        $status = 'y';

        $update_otp_query = "UPDATE profile SET vericode = $1, status = $2 WHERE vericode = $3";
        $update_res = pg_query_params($conn, $update_otp_query, array($code, $status, $fetch_code));

        if($update_res){
            $_SESSION['email'] = $email;
            header('Location: index.php');
            exit();
        }else{
            $errors['otp-error'] = "Failed while updating code!";
        }
    }else{
        $errors['otp-error'] = "You've entered incorrect code!";
    }
}

//if user click login button
if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (checkEmailExist($conn, $email)) {
        $check_email_query = "SELECT * FROM profile WHERE email = $1";
        $check_email_result = pg_query_params($conn, $check_email_query, array($email));

        if(pg_num_rows($check_email_result) > 0) {
            $fetch = pg_fetch_assoc($check_email_result);
            $fetch_pass = $fetch['userpass'];
            if(password_verify($password, $fetch_pass)){
                $status = $fetch['status'];
                if($status == 'y'){
                    $_SESSION['email'] = $email;
                    header('Location: index.php');
                    exit();
                } else {
                    $query = "SELECT vericode FROM profile WHERE email = $1";
                    $res = pg_query_params($conn, $query, array($email));
                    if ($res) {
                        $row = pg_fetch_assoc($res); // 
                        $vericode = $row['vericode'];
                        if (sendVerificationEmail($email, $vericode)) {
                        $info = "Please verify your account. We've resend verification code to your email - $email";
                        $_SESSION['info'] = $info;
                        $_SESSION['email'] = $email;
                        $_SESSION['password'] = $password;
                        header('Location: user-otp.php');
                        exit();
                        } else {
                            echo 'Failed to send verification email.';
                            header('Location: user-otp.php');
                            exit();
                        }
                    }
                } 
            } else {
                    $errors['email'] = "Incorrect email or password!";
            }
        }
    } else {
        $errors['email'] = "It looks like you're not yet a member! Click on the bottom link to signup.";
    }
}

//if user click continue button in forgot password form
if(isset($_POST['check-email'])){
    $email = $_POST['email'];
    $check_email_query = "SELECT * FROM profile WHERE email = $1";
    $check_email_result = pg_query_params($conn, $check_email_query, array($email));

    if(pg_num_rows($check_email_result) > 0){
        $code = rand(111111, 999999);
        $insert_code_query = "UPDATE profile SET vericode = $1 WHERE email = $2";
        $run_query = pg_query_params($conn, $insert_code_query, array($code, $email));

        if($run_query){
            if (sendVerificationEmail($email, $code)) {
                $info = "We've sent a password reset OTP to your email - $email";
                $_SESSION['info'] = $info;
                $_SESSION['email'] = $email;
                header('Location: reset-code.php');
                exit();
            } else {
                $errors['otp-error'] = "Failed while sending code!";
            }
        }else{
            $errors['db-error'] = "Something went wrong!";
        }
    }else{
        $errors['email'] = "This email address does not exist!";
    }
}

//if user click check reset otp button
if(isset($_POST['check-reset-otp'])){
    $_SESSION['info'] = "";
    $otp_code = $_POST['otp'];
    $check_code_query = "SELECT * FROM profile WHERE vericode = $1";
    $code_res = pg_query_params($conn, $check_code_query, array($otp_code));

    if(pg_num_rows($code_res) > 0){
        $fetch_data = pg_fetch_assoc($code_res);
        $email = $fetch_data['email'];
        $_SESSION['email'] = $email;
        $info = "Please create a new password that you don't use on any other site.";
        $_SESSION['info'] = $info;
        header('Location: new-password.php');
        exit();
    }else{
        $errors['otp-error'] = "You've entered incorrect code!";
    }
}

//if user click change password button
if(isset($_POST['change-password'])){
    $_SESSION['info'] = "";
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    if($password !== $cpassword){
        $errors['password'] = "Confirm password not matched!";
    }else{
        $code = 0;
        $email = $_SESSION['email']; //getting this email using session
        $encpass = password_hash($password, PASSWORD_BCRYPT);

        $update_pass_query = "UPDATE profile SET vericode = $1, userpass = $2 WHERE email = $3";
        $run_query = pg_query_params($conn, $update_pass_query, array($code, $encpass, $email));

        if($run_query){
            $info = "Your password changed. Now you can login with your new password.";
            $_SESSION['info'] = $info;
            header('Location: password-changed.php');
            exit();
        }else{
            $errors['db-error'] = "Failed to change your password!";
        }
    }
}

?>

<?php

function checkUsernameExist($con, $username) {
    // Check if username already exists
    $query = "SELECT * FROM profile WHERE username = $1";
    $result = pg_query_params($con, $query, array($username));
    return pg_num_rows($result) > 0;
}

function checkEmailExist($con, $email) {
    // Check if email already exists
    $query = "SELECT * FROM profile WHERE email = $1";
    $result = pg_query_params($con, $query, array($email));
    return pg_num_rows($result) > 0;
}
?>

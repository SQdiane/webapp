<?php
session_start();

// Include the database connection file
require 'db_connect.php';

// Get the posted form data
$username = $_POST['username'];
$userpass = password_hash($_POST['userpass'], PASSWORD_BCRYPT);

// Check if username already exists
$query = "SELECT * FROM profile WHERE username = $1";
$result = pg_query_params($conn, $query, array($username));

if (pg_num_rows($result) > 0) {
    echo '<script>alert("Username already exists"); window.location.href="login.php?showRegister=true";</script>';
} else {
    // Insert new user
    $query = "INSERT INTO profile (username, userpass) VALUES ($1, $2)";
    $result = pg_query_params($conn, $query, array($username, $userpass));

    if ($result) {
        echo '<script>alert("User created successfully"); window.location.href="login.php";</script>';
    } else {
        echo 'An error occurred while creating the user.';
    }
}

pg_close($conn);
?>

<?php
// db_connect.php

$host = 'localhost';
$dbname = 'mydb';
$user = 'myuser';
$password = 'mypassword';

// Connect to PostgreSQL
$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");

if ($conn) {
    //echo "Successfully connected to the db! <br>";
} else {
    die('An error occurred while connecting to the database.');
}
?>

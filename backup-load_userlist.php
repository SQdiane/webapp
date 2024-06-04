<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "admin_control.php"; // Ensure this includes the database connection

$userNewCount = isset($_POST['userNewCount']) ? intval($_POST['userNewCount']) : 1;

$sql = "SELECT username FROM profile LIMIT $userNewCount";
$result = pg_query($conn, $sql);

if (pg_num_rows($result) > 0) {
    while ($row = pg_fetch_assoc($result)) {
        echo "<p>";
        echo $row['username'];
        echo "</p>";
    }
} else {
    echo "There is no user.";
}
?>

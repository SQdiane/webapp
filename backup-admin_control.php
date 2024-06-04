php<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start(); // Ensure session is started
require 'header.php';
require 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User List</title>
</head>
<body>
    <div id="userlist">
        <?php
            $sql = "SELECT username FROM profile LIMIT 1";
            $result = pg_query($conn, $sql);
            if (pg_num_rows($result) > 0) {
                while ($row = pg_fetch_assoc($result)) {
                    echo "<p>";
                    echo "username: " . $row['username'];
                    echo "</p>";
                }
            } else {
                echo "There is no user.";
            }
        ?>
    </div>
    <button id ="loadMore" >Show more users</button>

    <script>
        $(document).ready(function() {
            var userCount = 1;
            $("#loadMore").click(function() {
                userCount++;
                $.post("load_userlist.php", { userNewCount: userCount }, function(data) {
                    $("#userlist").append(data);
                });
            });
        });
    </script>
</body>
</html>

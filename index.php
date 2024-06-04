<?php require_once "userData.php"; ?>


<?php 

if (!isset($_SESSION['email']) || !isset($_SESSION['password'])) {
    header('Location: login.php');
    exit();
}
else {
    $email = $_SESSION['email'];
    $password = $_SESSION['password'];
}

if($email != false && $password != false){
    $sql = "SELECT * FROM profile WHERE email = $1";
    $run_Sql = pg_query_params($conn, $sql,array($email));
    if($run_Sql){
        $fetch_info = pg_fetch_assoc($run_Sql);
        $status = $fetch_info['status'];
        $code = $fetch_info['vericode'];
        if($status == "y"){
            if($code != 0){
                header('Location: reset-code.php');
            }
        }else{
            header('Location: user-otp.php');
        }
    }
}else{
    header('Location: login.php');
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $fetch_info['username']; ?> | Home</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="index.css"> <!-- Link to your custom CSS file -->
</head>
<body>
    <nav class="navbar">
    <a class="navbar-brand" href="#">Brand name</a>
    <button type="button" class="btn btn-light"><a href="logout.php">Logout</a></button>
    </nav>
    <h1>Welcome <?php echo $fetch_info['username'];  ?></h1>
    
</body>
</html>


<?php include 'footer.php'; ?>

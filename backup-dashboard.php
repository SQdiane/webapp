<?php
include 'user_access.php';  // Check if the user is logged in
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<body>

<div >
  <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
  <p>You are logged in.</p> 
</div>
  
<div class="container mt-5">
  <div class="row">
    <div class="col-sm-4">
    <p> This is the first contents!</p>
    <button id = "btn" > Click for more </button>
    </div>
    <div class="col-sm-4">
      <h3>Column 2</h3>
      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit...</p>
      <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris...</p>
    </div>
    <div class="col-sm-4">
      <h3>Column 3</h3>        
      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit...</p>
      <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris...</p>
    </div>
  </div>
</div>

</body>
</html>

<html>
    <script>
        $(document).ready(function(){
            $("#btn").click(function(){
                $("#test").load("newdata.txt");                
            });
        });
    </script>
</html>

<?php include 'footer.php'; ?>


<?php
include 'header.php';
?>

<div class="container">
    <div id="login-container">
        <h2>Login to Tuition Center</h2>
        <form action="login.php" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <a href="#" onclick="showRegister()">Create an account</a>
    </div>

    <div id="register-container" style="display:none;">
        <h2>Create User</h2>
        <form action="register.php" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>
        <a href="#" onclick="showLogin()">Back to login</a>
    </div>
</div>

<script>
    function showRegister() {
        document.getElementById('login-container').style.display = 'none';
        document.getElementById('register-container').style.display = 'block';
    }
    function showLogin() {
        document.getElementById('login-container').style.display = 'block';
        document.getElementById('register-container').style.display = 'none';
    }
    // Check for the 'showRegister' query parameter
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('showRegister')) {
        showRegister();
    }
</script>

<?php include 'footer.php'; ?>

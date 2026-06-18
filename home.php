<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class=" im">
    <img src="a1.jpg" >
    </div> 
    <div class="cont">
        <h3>SHOPKART</h3>
        <H4>All products at one place! </H4>
    </div>
   <form action="/login-endpoint" method="POST">
         <div class="form-group">
            <label for="email">Email id</label>
            <input type="text" id="email" name="email" placeholder="Enter email id" required>
        </div>
        
        <!-- Username/Email Field -->
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter username" required>
        </div>

        <!-- Password Field -->
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter password" required>
        </div>

        <!-- Submit Button -->
        <button type="submit">Log In</button>
        <p class="signup-link">
    Don't have an account?
    <a href="signup.php">Sign Up</a>
</p>
        
    </form>

    </form>
    
</body>
</html>
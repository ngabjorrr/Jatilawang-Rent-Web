<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0" />
     <title>Login Page</title>
     <link href="public/css/styles.css" rel="stylesheet" />
</head>

<body>
     <div class="container">
          <!-- Left Side (Login Form) -->
          <div class="form-section">
               <div class="form-container">
                    <img src="public/assets/Logo.png" alt="Company Logo" class="logo-img" />
                    <h2>Sign In</h2>
                    <p class="subtitle">Enter your credentials to access your account.</p>

                    <form action="proses_login.php" method="post">
                         <input type="email" name="email" placeholder="Enter your email address" required />
                         <input type="password" name="password" placeholder="Enter your password" required />
                         <button type="submit">Sign In</button>
                    </form>

                    <div class="divider">
                         <hr />
                         <span>Or continue with</span>
                         <hr />
                    </div>

                    <div class="social-buttons">
                         <button class="social google">
                              <img src="https://cdn1.iconfinder.com/data/icons/google-s-logo/150/Google_Icons-09-1024.png" alt="Google" />
                              Google
                         </button>
                         <button class="social apple">
                              <img src="https://www.freeiconspng.com/uploads/apple-icon-4.png" alt="Apple" />
                              Apple
                         </button>
                         </button>
                    </div>

                    <p class="signup">
                         Don’t have an account? <a href="register_page.php">Create an account</a>
                    </p>

                    <div class="footer-links">
                         <a href="#">Privacy & Terms</a>
                         <a href="#">Contact Us</a>
                    </div>
               </div>
          </div>

          <!-- Right Side (Image) -->
          <div class="image-section">
               <img src="public/assets/Right_Picture.png" alt="Climber" />
          </div>
     </div>
</body>

</html>
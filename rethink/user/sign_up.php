<?php
/*
 * File: sign_up.php
 * Author: Moa Burke
 * Date: 2024-10-30
 * Description: Handles user registration by collecting and validating input fields (first name, last name, username, email, password, and profile image).
 *
 * Functionality:
 * - Validates user input for required fields.
 * - Checks for unique username and email.
 * - Handles file uploads for user profile images (supports .jpg and .png).
 * - Saves user data to the database and redirects to the login page on successful registration.
 *
 * Dependencies:
 * - Requires 'connections.php' for database access and 'check_login.php' for login session validation.
 * - JavaScript file 'input_handler.js' to toggle visibility of password fields.
 *
 */

session_start();// Start the session

// Define a constant for the base path
define('BASE_PATH', '../server-side/shared/');

include(BASE_PATH . "connections.php"); // Include databade connection file
include(BASE_PATH . "check_login.php"); //Include the utiity functions
include(BASE_PATH . "timezone.php"); // Include the timezone configuration
include(BASE_PATH . "constants.php"); // Include the constants file
include(BASE_PATH . "header/common_head.php"); // Include the user header layout file

$errors = 0; // Initialize error count
$date = date("Y-m-d"); // Get current date

// Check if the form is submitted via post method
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Check if first name is empty: if so, store an error message
    if ($_POST['firstname'] == "") {
        $error['firstname_missing'] = "名前が入力されていません。"; // "First name is required"
    }
    // Check if last name is empty: if so, store an error message
    if ($_POST['lastname'] == "") {
        $error['lastname_missing'] = "ユーザー名が入力されていません。"; // "Last name is required"
    }
    // Check if username is empty: if so, store an error message 
    if ($_POST['username'] == "") {
        $error['username_missing'] = "苗字が入力されていません。"; // "Username is required"
    }
    // Check if email is empty: if so, store an error message 
    if ($_POST['email'] == "") {
        $error['email_missing'] = "メールアドレスが入力されていません。"; // "Email is required"
    }
    // Check if password is empty: if so, store an error message 
    if ($_POST['Password'] == "") {
        $error['password_missing'] = "パスワードが入力されていません。"; // "Password id required"
    }
    // Check if comfirmation password is empty: if so, store an error message 
    if ($_POST['password2'] == "") {
        $error['confirm_password_missing'] = "確認用のパスワードが入力されていません。"; // "Confirmation password is required"
    }

    // Validate image upload
    if (!empty($_FILES['image']['name'])) {
        $ext = substr($_FILES['image']['name'], -4); //Get the file extension
        // Check if the file extension is not .jpg or .png
        if ($ext != '.jpg' && $ext != '.png') {
            $error['image_upload'] = 'その画像は使用できません。使用可能拡張子：.img .jpg'; // "Invalid image. Allowed extentions: .jpg, .png"
        }
    }
        

    // If there are no errore, procees with registration
    if (empty($error)) {
        // Collect input data
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['Password'];
        $password2 = $_POST['password2'];

        // Check for existing username
        $sql_u = "SELECT * FROM users WHERE Username = '$username'";
        $res_u = mysqli_query($con, $sql_u);
        // Check for existing email
        $sql_u_mail = "SELECT * FROM users WHERE Email = '$email'";
        $res_u_mail = mysqli_query($con, $sql_u_mail);

        // If username already exists
        if (mysqli_num_rows($res_u) > 0) {
            $error['username_exists'] = "このユーザー名はすでに登録済みです。"; // "This username is already registered"
        }

        // If email already exists
        if (mysqli_num_rows($res_u_mail) > 0) {
            $error['email_exists'] = "このメールアドレスはすでに登録済みです。"; // "This email address is already registred"
        }

        // Check if passwords match
        if ($password != $password2){
            $error['passwords_mismatch'] = "パスワードと確認用のパスワードが一致していません。"; // "Passwords do not match"
        } else {
            // Check password length
            if (strlen($password) <= 5) {
                $error['password_too_short'] = 'パスワードは最低6文字必要です。'; // "Passwors must be at least 6 characters."
            }
        }

        // If there are stil no errors
        if (empty($error)) {
            // Handle image upload
            if (!empty($_FILES['image']['name'])) {
                $image = date('YmdHis') . $ext; // Create a unique file name
            } else {
                $image = 'user.jpg'; // Default image if no file uploaded
            }
    
            // Attempt to upload the image
            $upload = move_uploaded_file($_FILES['image']['tmp_name'], '../assets/user-img/' . $image);
            // Prepare SQL query to insert user data
            if ($upload) {      
                $query = 
                "INSERT INTO users (FirstName, LastName, Username, Email, profileImg, Password, Role, Created) 
                    VALUES('$firstname', '$lastname', '$username', '$email', '$image', '$password', '" . ROLE_USER ."', '$date')";
            } else {
                $query = 
                "INSERT INTO users (FirstName, LastName, Username, Email, profileImg, Password, Role, Created) 
                VALUES('$firstname', '$lastname', '$username', '$email', 'user.jpg', '$password', '" . ROLE_USER ."', '$date')";
            }

            // Execute the query
            mysqli_query($con, $query);
            header("Location: ./login.php"); // Redirect to login page
            die; // Stop further execution
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php 
        // Call the includeHeadAssets function to include the common head elements 
        // for the login page, ensuring consistent styling and functionality.
        includeHeadAssets(); 
        ?>
    </head>

    <body>
        <!-- Header Section -->
        <header class="header">
            <nav>
                <div class='header-left'>
                    <!-- Logo and website name -->
                    <a href='../index.php'>
                        <!-- Container for the logo and website name -->
                        <div class='logo-container'>
                            <div class='logo-heart-left'></div> <!-- Left side of the heart logo -->
                            <div class='logo-heart-right'></div> <!-- Right side of the heart logo -->

                            <!-- Displaying the website name with a logo -->
                            <div class='site-name'>
                                RE:<span>THINK</span>
                            </div> 
                        </div><!-- .heart -->
                    </a>
                </div><!-- .header-left -->

                <!-- Navigation link to login page -->
                <a href='./login.php' class="header-link">Login</a>
            </nav>
        </header>

        <div class="auth-container container-form">
            <div class="form-wrapper">
                <div class="form-header">
                    <h2>Sign Up</h2>
                </div>
                
                <form action="" method="POST" enctype="multipart/form-data">
                    <!-- First Name Input Section -->
                    <div class="input-container <?php if(isset($_POST['firstname'])){if($_POST['firstname'] != ''){echo 'focus';}} ?>">
                        <label for="">First Name</label>
                        <span>First Name</span>

                        <!-- Input field for the first name -->
                        <input type="text" name="firstname" class="input" 
                            value="<?php if(isset($_POST['firstname'])){if($_POST['firstname'] != ''){echo $_POST['firstname'];}} ?>">

                        <!-- Display any errors related to the first name input -->
                        <div class="input-error">
                            <p><?= $error['firstname_missing'] ?? '' ?></p>
                        </div> 
                    </div><!-- .input-container -->

                    <!-- Last Name Input Section -->
                    <div class="input-container <?php if(isset($_POST['lastname'])){if($_POST['lastname'] != ''){echo 'focus';}} ?>">
                        <label for="">Last Name</label>
                        <span>Last Name</span>

                        <!-- Input field for the last name -->
                        <input type="text" name="lastname" class="input" 
                            value="<?php if(isset($_POST['lastname'])){if($_POST['lastname'] != ''){echo $_POST['lastname'];}} ?>">

                        <!-- Display any errors related to the last name input -->
                        <div class="input-error">
                            <p><?= $error['lastname_missing'] ?? '' ?></p>
                        </div> 
                    </div><!-- .input-container -->

                    <!-- Username Input Section -->
                    <div class="input-container <?php if(isset($_POST['username'])){if($_POST['username'] != ''){echo 'focus';}} ?>">
                        <label for="">Username</label>
                        <span>Username</span>

                        <!-- Input field for the username -->
                        <input type="text" name="username" class="input" 
                            value="<?php if(isset($_POST['username'])){if($_POST['username'] != ''){echo $_POST['username'];}} ?>">

                        <!-- Display any errors related to the username input -->
                        <div class="input-error">
                            <p><?= $error['username_missing'] ?? '' ?><?= $error['username_exists'] ?? '' ?></p>
                        </div> 
                    </div><!-- .input-container -->

                    <!-- Email Input Section -->
                    <div class="input-container <?php if(isset($_POST['email'])){if($_POST['email'] != ''){echo 'focus';}} ?>">
                        <label for="">Email</label>
                        <span>Email</span>

                        <!-- Input field for the email -->
                        <input type="email" name="email" class="input" 
                            value="<?php if(isset($_POST['email'])){if($_POST['email'] != ''){echo $_POST['email'];}} ?>">

                        <!-- Display any errors related to the email input -->
                        <div class="input-error">
                            <p><?= $error['email_missing'] ?? '' ?><?= $error['email_exists'] ?? '' ?></p>
                        </div> 
                    </div><!-- .input-container -->

                    <!-- Password Input Section -->
                    <div class="input-container">
                        <label for="">Password</label>
                        <span>Password</span>

                        <!-- Input field for the password -->
                        <input type="password" name="Password" class="input" id="passwordInput">

                        <!-- Display any password-related errors -->
                        <div class="input-error">
                            <p><?= $error['password_missing'] ?? '' ?><?= $error['passwords_mismatch'] ?? '' ?><?= $error['password_too_short'] ?? '' ?></p>
                        </div> 

                        <!-- Icon to toggle the visibility of the password -->
                        <i id="passwordToggleIcon" class="eye fa-solid fa-eye" onclick="togglePasswordVisibility('passwordInput', 'passwordToggleIcon')"></i> 
                    </div><!-- .input-container -->

                    <!-- Re-enter Password Input Section -->
                    <div class="input-container">
                        <label for="">Re-enter Password</label>
                        <span>Re-enter Password</span>

                        <!-- Input field for re-entering the password -->
                        <input type="password" name="password2" class="input" id="reenterPasswordInput">

                        <!-- Display any errors related to re-entering the password -->
                        <div class="input-error">
                            <p><?= $error['confirm_password_missing'] ?? '' ?><?= $error['passwords_mismatch'] ?? '' ?></p>
                        </div> 

                        <!-- Icon to toggle the visibility of the re-entered password -->
                        <i id="reenterPasswordToggleIcon" class="eye fa-solid fa-eye" onclick="togglePasswordVisibility('reenterPasswordInput', 'reenterPasswordToggleIcon')"></i>
                    </div><!-- .input-container -->

                    <!-- Icon Upload -->
                    <div class="input-container">
                        <label for="">Icon</label>
                        <span>Icon</span>

                        <!-- Display any image upload errors -->
                        <div class="label">
                            <div class="input-error"><?= $error['image_upload'] ?? '' ?></div> 
                        </div><!-- .label -->

                        <!-- File input for user icon upload -->
                        <div>
                            <input type="file" name="image" class="input-img" size="35" value="test" accept="image/*" /> 
                        </div>
                    </div><!-- .input-container -->

                    <!-- Sign up button -->
                    <button type="submit" value="Signup" class="primary-btn">Sign up</button>

                    <!-- Link for users who already have an account -->
                    <div class="form-link">
                        <p>すでにアカウントをお持ちの場合はこちらから<a href="./login.php">ログイン</a></p> <!-- Link to login page -->
                    </div><!-- .form-link -->

                </form>

            </div><!-- form-wrapper -->
        </div><!-- .container container-form -->

        <!-- Footer Section -->
        <footer class="footer">  
            <div class="copyright">
                <small>&copy; 2024 RE:THINK</small>
            </div>
        </footer>

    </body>

</html>


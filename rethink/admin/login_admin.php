<?php
/*
 * File: admin_login.php
 * Author: Moa Burke
 * Date: 2024-10-30
 * Description: 
 *     This file manages the admin login process for the RE:THINK application. 
 *     It starts a session for user data management and includes essential files for:
 *     - Database connections
 *     - Admin authentication functions
 * 
 *     The script processes the login form submission, validating user input for 
 *     username and password, and checks credentials against the database for 
 *     admin users. If the login is successful, the user is redirected to the 
 *     admin dashboard. Error messages are displayed for invalid login attempts 
 *     or empty fields.
 * 
 * Sections Included:
 * - Session Management: Initializes a session to store admin user data
 * - Input Validation: Checks for empty username and password fields
 * - Database Query: Validates admin credentials against the database
 * - Error Handling: Displays messages for invalid login attempts or missing input
 * - Redirection: Directs admin users to the admin index page upon successful login
 * 
*/
session_start(); // Start the session

// Define a constant for the base path
define('BASE_PATH', '../server-side/');

include(BASE_PATH . "shared/connections.php"); // Include databade connection file
include(BASE_PATH . "admin/admin_check_login.php"); //Include additional functions
include(BASE_PATH . "shared/timezone.php"); // Include the timezone configuration
include(BASE_PATH . "shared/constants.php"); // Include the constants file
include(BASE_PATH . "shared/header/common_head.php"); // Include the header file

// Check if the request method is POST
if($_SERVER['REQUEST_METHOD'] == "POST"){

    //Ensure the POST data is not empty
    if (!empty($_POST)) { 

        //Validate the username field
        // Check if username is empty: if so, store an error message 
        if ($_POST['username'] == "") {
            $error['username_missing'] = "ユーザー名が入力されていません。"; // "Username is required"
        }

        //Validate the password field
        // Check if password is empty: if so, store an error message 
        if ($_POST['Password'] == "") {
            $error['password_missing'] = "パスワードが入力されていません。"; // "Password is required"
        }

        //Proceed if there are no validation errors
        if (empty($error)) {
            $username = $_POST['username']; // Store the username from POST data
            $password = $_POST['Password']; // Store the password from POST data

            // Check if the username and password are not empty and username is not numeric
            if (!empty($username) && !empty($password) && !is_numeric($username)) {
                // Query to select user from the database based on username and role
                $query = "SELECT * FROM users WHERE username = '$username' AND Role = '" . ROLE_ADMIN ."' limit 1";
                $result = mysqli_query($con, $query); // Execute the query

                // Check if the query was successful
                if ($result) {
                    // Check if the user exists
                    if ($result && mysqli_num_rows($result) > 0) {
                        $admin_data = mysqli_fetch_assoc($result); // Fetch the user data
                        
                        // Verify the password matches
                        if($admin_data['Password'] === $password) {
                            //Store user ID in session for future access
                            $_SESSION['UserID'] = $admin_data['UserID'];

                            //Redirect to index page upon successful login
                            header("Location: ./admin_dashboard.php");
                            die; // Stop further execution
                        }
                    }
                }

                // If the query failed, print the error
                if ( false===$result ) {
                    printf("error: %s\n", mysqli_error($con));
                }

                // Set error message for incorrect username or password
                $error['incorrect_credentials'] = "メールアドレスまたはパスワードに間違いがあります。"; // Incorrect username or password
            } 
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
                </div><!-- .header-left left-bg -->

                <!-- Navigation link to login page -->
                <a href='../user/sign_up.php' class="header-link">Sign Up</a>
            </nav>
        </header>

        <div class="auth-container container-form container-admin">
            <div class="form-wrapper">

                <div class="form-header">
                    <h2>Admin Login</h2>
                </div>

                <!-- Display general errors -->
                <div class="input-error error-index">
                    <p><?= $error['incorrect_credentials'] ?? '' ?></p>
                </div>

                <form action="" method="POST" enctype="multipart/form-data">
                    <!-- Username input container -->
                    <div class="input-container <?php if(!empty($_POST['username'])) echo 'focus'; ?>">
                        <label for="">Username</label>
                        <span>Username</span>

                        <!-- Username input field -->
                        <input type="text" name="username" class="input" value="<?php echo $_POST['username'] ?? ''; ?>">

                        <!-- Error message display for the username -->
                        <div class="input-error">
                            <p><?= $error['username_missing'] ?? '' ?></p>
                        </div>
                    </div><!-- .input-container -->

                    <!-- Password input container -->
                    <div class="input-container">
                        <label for="">Password</label>
                        <span>Password</span>

                        <!-- Password input field -->
                        <input type="password" name="Password" class="input" id="myInput">

                        <!-- Error message display for the password -->
                        <div class="input-error">
                            <p><?= $error['password_missing'] ?? '' ?></p>
                        </div>

                        <!-- Icon to toggle password visibility -->
                        <i id="myInputIcon" class="eye fa-solid fa-eye" onclick="togglePasswordVisibility('myInput', 'myInputIcon')"></i>
                    </div><!-- .input-container -->
                
                    <!-- Submit button for the login form -->
                    <button type="submit" value="Login" class="primary-btn"> Login </button>

                    <!-- Links for additional actions -->
                    <div class="form-link">
                        <!-- Link to the user login page -->
                        <p>
                            <a href="../user/login.php">ユーザーとしてログイン</a>
                        </p>  
                    </div><!-- .form-link -->
                </form>

            </div><!-- .form-wrapper -->
        </div><!-- .container container-form container-admin -->

        <!-- Footer Section -->
        <footer class="footer">  
            <div class="copyright">
                <small>&copy; 2024 RE:THINK</small>
            </div>
        </footer>

    </body>
</html>


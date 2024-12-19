<?php
/*
 * File: login.php
 * Author: Moa Burke
 * Date: 2024-10-30
 * Description: 
 *     This file handles user authentication for the RE:THINK application. 
 *     It initializes the session for user data management and includes essential files for:
 *     - Database connections
 *     - User authentication checks
 * 
 *     The script processes the login form submission, validating user input for 
 *     username and password, and checking credentials against the database. 
 *     Upon successful login, the user is redirected to the main index page, 
 *     and their login information is stored for session management. If there 
 *     are any errors, appropriate messages are displayed to the user.
 * 
 * Sections Included:
 * - Session Management: Starts a new session or resumes an existing session
 * - Input Validation: Checks for empty username and password fields
 * - Database Query: Validates user credentials against the database
 * - Error Handling: Displays messages for invalid login attempts or empty fields
 * - Redirection: Directs users to the index page upon successful login
 * 
 */

 session_start(); // Start the session

// Define a constant for the base path
define('BASE_PATH', '../server-side/shared/');

include(BASE_PATH . "connections.php"); // Include databade connection file
include(BASE_PATH . "check_login.php"); // Include login check script to verify user authentication before accessing this page.
include(BASE_PATH . "timezone.php"); // Include the timezone configuration
include(BASE_PATH . "constants.php"); // Include the constants file
include(BASE_PATH . "header/common_head.php"); // Include the admin header layout file

// Check if the request method is POST
if($_SERVER['REQUEST_METHOD'] == "POST") {

    //Ensure the POST data is not empty
    if (!empty($_POST)) { 

        //Validate the username field
        // Check if username is empty: if so, store an error message 
        if ($_POST['username'] == "") {
            $error['username_missing'] = "ユーザー名が入力されていません。"; // "Username is required"
        }

        //Validate the password field
        // Check if password is empty: if so, store an error message 
        if ($_POST['password'] == "") {
            $error['password_missing'] = "パスワードが入力されていません。"; // "Password is required"
        }

        //Proceed if there are no validation errors
        if (empty($error)) {
            $username = $_POST['username']; // Store the username from POST data
            $password = $_POST['password']; // Store the password from POST data

            // Check if the username and password are not empty and username is not numeric
            if (!empty($username) && !empty($password) && !is_numeric($username)) {
                // Query to select user from the database based on username and role
                $query = "SELECT * FROM users WHERE username = '$username' AND Role = '" . ROLE_USER ."' limit 1";
                $result = mysqli_query($con, $query); // Execute the query

                // Check if the query was successful
                if ($result) {
                    // Check if the user exists
                    if ($result && mysqli_num_rows($result) > 0) {
                        $user_data = mysqli_fetch_assoc($result); // Fetch the user data

                        // Verify the password matches
                        if ($user_data['Password'] === $password) {
                            //Store user ID in session for future access
                            $_SESSION['UserID'] = $user_data['UserID'];
                            $userID = $user_data['UserID']; // Store user ID for further use
                            $date = date("Y-m-d"); // Get current date
    
                            // Query to get user data based on user ID
                            $queryGetUserData = mysqli_query($con, "SELECT * FROM userdata WHERE UserID = $userID");
                            $rowsUserData = mysqli_num_rows($queryGetUserData); //Count rows returned

                            //If user data exists, update the login count and last login date
                            if ($rowsUserData > 0){
                                $resultUserData = mysqli_fetch_assoc($queryGetUserData); // Fetch user data
                                $LoginCount = $resultUserData['LoginCount']; // Get current login count
                                $NewLoginCount = $LoginCount + 1; // Increment login count

                                // Update user data with nre login count and last login date
                                $queryInsertUserData = "UPDATE userdata SET LastLogin = '$date', LoginCount = $NewLoginCount WHERE UserID = $userID";
                                mysqli_query($con, $queryInsertUserData); //Execute update query
                            }else{
                                // If no user data exists, insert new user
                                $queryInsert = "INSERT INTO userdata (UserID, LastLogin, LoginCount) VALUES ('$userID', '$date', '1')";
                                mysqli_query($con, $queryInsert); // Execute insert query
                            }
                            
                            //Redirect to index page upon successful login
                            header("Location: ./user_home.php");
                            die; // Stop further execution
                        }
                    }
                }

                // If the query failed, print the error
                if ( false===$result ) {
                    printf("error: %s\n", mysqli_error($con));
                }
                // Set error message for incorrect username or password
                $error['incorrect_credentials'] = "ユーザー名またはパスワードに間違いがあります。"; // Incorrect username or password
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
                </div><!-- .header-left  -->
                
                <!-- Navigation link to login page -->
                <a href='./sign_up.php' class="header-link">Sign Up</a>
            </nav>
        </header>

        <div class="auth-container container-form">
            <div class="form-wrapper">
                
                <div class="form-header">
                    <h2>Login</h2>
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
                        <input type="password" name="password" class="input" id="passwordInput">

                        <!-- Error message display for the password -->
                        <div class="input-error">
                            <p><?= $error['password_missing'] ?? '' ?></p>
                        </div> 

                        <!-- Icon to toggle password visibility -->
                        <i id="passwordToggleIcon" class="eye fa-solid fa-eye" onclick="togglePasswordVisibility('passwordInput', 'passwordToggleIcon')"></i>
                    </div><!-- .input-container -->

                    <!-- Submit button for the login form -->
                    <button type="submit" value="Login" class="primary-btn"> Login </button>

                    <!-- Links for additional actions -->
                    <div class="form-link">
                        <p>アカウントをお持ちでない場合はこちらから<a href="./sign_up.php">新規登録</a></p> <!-- Link to the registration page -->

                        <!-- Link to the admin login page -->
                        <p>
                            <a href="../admin/login_admin.php">管理者としてログイン</a>
                        </p> 
                    </div><!-- .form-link -->
                </form>

            </div><!-- .form-wrapper -->
        </div><!-- .auth-container -->

        <!-- Footer Section -->
        <footer class="footer">  
            <div class="copyright">
                <small>&copy; 2024 RE:THINK</small>
            </div>
        </footer>

    </body>
</html>


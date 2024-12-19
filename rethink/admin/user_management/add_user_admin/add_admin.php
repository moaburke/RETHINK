<?php
/**
 * File: add_admin.php
 * Author: Moa Burke
 * Date: 2024-11-04
 * Description: Provides a form for adding new admin users to the system.
 * 
 * This script generates an HTML form that allows the admin to input
 * new user information, including first name, last name, email, password,
 * and role. It also handles form submission and validation, ensuring that 
 * all required fields are filled and that the email format is valid.
 * 
 * Functionality:
 * - Displays input fields for first name, last name, email, password, and role.
 * - Validates the input and checks for existing email addresses to prevent 
 *   duplicates.
 * - Provides error messages for invalid inputs and confirmation messages
 *   upon successful user creation.
 * - Includes a button to submit the form for adding the new user.
*/
session_start(); // Start the session

// Define a constant for the base path
define('BASE_PATH', '../../../server-side/shared/');

include(BASE_PATH . "connections.php"); // Include databade connection file
include(BASE_PATH . "check_login.php"); // Include login check script to verify user authentication before accessing this page.
include(BASE_PATH . "timezone.php"); // Include the timezone configuration
include(BASE_PATH . "header/admin_layout.php"); // Include the admin header layout file

// Check if the admin is logged in 
$adminData = check_login($con);
// Retrieve the UserID of the logged-in admin
$adminID = $adminData['UserID']; 

// Check if the 'add-user' form has been submitted
if (isset($_POST['add-user'])) {
    // Validate required fields
    // Check if the username is provided
    if ($_POST['username'] == "") {
        $error['username_missing'] = "ユーザー名が入力されていません。"; // Translation: "Username is required"
    }

    // Check if the first name is provided
    if ($_POST['firstName'] == "") {
        $error['firstname_missing'] = "名前が入力されていません。"; // Translation: "First name is required"
    }

    // Check if the last name is provided
    if ($_POST['lastName'] == "") {
        $error['lastname_missing'] = "苗字が入力されていません。"; // Translation: "Last name is required"
    }

    // Check if the email address is provided
    if ($_POST['email'] == "") {
        $error['email_missing'] = "メールアドレスが入力されていません。"; // Translation: "Email address is required"
    }

    // Check if the password is provided
    if ($_POST['password'] == "") {
        $error['password_missing'] = "パスアが入力されていません。"; // Translation: "Password is required"
    }

    // Check if a role is selected
    if ($_POST['role'] == "") {
        $error['role_missing'] = "Roleが選択されていません。"; // Translation: "Role selection is required"
    }

    // Retrieve the submitted username
    $username = $_POST['username'];

    // Query the database to check if the username already exist
    $usernameCheckQuery =  mysqli_query($con, "SELECT Username FROM users WHERE Username = '$username';");
    $usernameCount = mysqli_num_rows($usernameCheckQuery);

    // If the username already exists, add an error message
    if ($usernameCount > 0) {
        $error['username_exists'] = "このユーザー名はすでに登録済みです。"; // Translation: "This username is already registered."
    }
    
    // If an email is provided, check for its existence in the database
    if(($_POST['email']) != ""){
        $email = $_POST['email'];

        // Query the database to check if the email already exists
        $emailCheckQuery =  mysqli_query($con, "SELECT Email FROM users WHERE Email = '$email';");
        $emailCount = mysqli_num_rows($emailCheckQuery);

        // If the email already exists, add an error message
        if ($emailCount > 0){
            $error['email_exists'] = "このメールアドレスはすでに登録済みです。"; // Translation: "This email address is already registered."
        }
    }

    // If there are no validation errors
    if (empty($error)) { 
        // Retrieve user input for database insertion
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        $img = "admin.jpg"; // Default profile image for the admin

        // Prepare SQL query to insert the new user into the database
        $query = "INSERT INTO users (FirstName, LastName, UserName, Email, Password, Role, Created, profileImg) 
            VALUES('$firstName', '$lastName', '$username', '$email', '$password', '$role', now(), '$img')";
        $queryRun = mysqli_query($con, $query); // Execute the query
        
        // Check if the query was successful
        if ($queryRun) {
            // Set a success message for successful addition of the admin
            $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>管理者が正常に追加されました。</p></div>";

        } else { 
            // Set a failure message for unsuccessful addition
            $_SESSION['feedbackMessage'] = "<div class='message-text fail-alert'><p>何か問題が発生しました。</p></div>";
        }

        // Redirect to user management page 
        header('Location: ../manage_users.php');
        exit(0); // Ensure script termination after redirection
    }
} 
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Include the necessary assets for the head section via the includeHeadAssets function -->
        <?php includeHeadAssets(); ?>
    </head>

    <body>
        <header class="sidebar-navigation manage-users-navigation">
            <!-- Render the sidebar navigation specifically for the admin section -->
            <?php renderAdminNavigation(); ?>
        </header>

        <!-- Render the header for the admin dashboard with logout functionality, using the admin's data -->
        <?php renderAdminHeaderWithLogout($adminData); ?>

        <div class="admin-main-wrapper">
            <!-- Header for the User/Admin section -->
            <h2>Users</h2>

            <div class="breadcrumbs breadcrumbs-admin">
                <!-- Link to navigate back to the Manage Users page -->
                <a href="../manage_users.php">
                    <p>Manage Users</p>
                </a> 

                <!-- Right arrow icon to indicate breadcrumb separation -->
                <i class="fa-solid fa-angle-right fa-sm"></i>

                <!-- Current active breadcrumb indicating the page the user is on -->
                <p class="bread-active">Add Admin</p>
            </div><!-- .breadcrumbs -->

            <div class="user-management-form-wrapper">
                <div class="user-management-header">
                    <!-- Title for the admin addition section in Japanese and English -->
                    <h3>
                        <span class="japanese-title">管理者の追加</span>
                        <span class="english-title">Add Admin</span>
                    </h3>
                </div><!-- .user-management-header -->

                <!-- Form for user input with POST method and file upload support -->
                <form action="" method="POST" enctype="multipart/form-data">

                    <!-- Container for the username input field -->
                    <div class="input-container 
                        <?php 
                        // Check if the username has been submitted and is not empty
                        if (isset($_POST['username'])) { 
                            if (($_POST['username']) != '') { 
                                echo 'focus';  // Add 'focus' class if username is filled
                            }
                        }?>">

                        <label for="">Username</label>
                        <span>Username</span> 
                        <!-- Input field for username -->
                        <input type="text" name="username" class="input form-control" value="<?= hsc($_POST['username'] ?? '') ?>"> 

                        <!-- Error message container for username input -->
                        <div class="input-error">
                            <p>
                                <?= $error['username_missing'] ?? '' ?> <!-- Display username error if present -->
                                <?= $error['username_exists'] ?? '' ?> <!-- Display error if username already exists -->
                            </p> 
                        </div>
                    </div><!-- .input-container -->

                    <!-- Container for the first name input field -->
                    <div class="input-container 
                        <?php 
                        // Check if the first name has been submitted and is not empty
                        if (isset($_POST['firstName'])) { 
                            if (($_POST['firstName']) != '') { 
                                echo 'focus'; // Add 'focus' class if the first name input is filled
                            }
                        }?>">

                        <label for="">First Name</label>
                        <span>First Name</span> 
                        <!-- Input field for first name -->
                        <input type="text" name="firstName" class="input form-control" value="<?= hsc($_POST['firstName'] ?? '') ?>">

                        <!-- Error message container for first name input -->
                        <div class="input-error"> 
                            <p><?= $error['firstname_missing'] ?? '' ?></p> <!-- Display error messages if any -->
                        </div>
                    </div><!-- .input-container -->

                    <!-- Container for the last name input field -->
                    <div class="input-container 
                        <?php 
                        // Check if the last name has been submitted and is not empty
                        if (isset($_POST['lastName'])) { 
                            if (($_POST['lastName']) != '') { 
                                echo 'focus'; // Add 'focus' class if the last name input is filled
                            }
                        }?>">

                        <label for="">Last Name</label>
                        <span>Last Name</span>
                        <!-- Input field for last name -->
                        <input type="text" name="lastName" class="input form-control" value="<?= hsc($_POST['lastName'] ?? '') ?>">

                        <!-- Error message container for last name input -->
                        <div class="input-error">
                            <p><?= $error['lastname_missing'] ?? '' ?></p> <!-- Display error messages if any -->
                        </div>
                    </div><!-- .input-container -->

                    <!-- Container for the email input field -->
                    <div class="input-container 
                        <?php 
                        // Check if the email has been submitted and is not empty
                        if (isset($_POST['email'])) { 
                            if (($_POST['email']) != '') { 
                                echo 'focus'; // Add 'focus' class if the email input is filled
                            }
                        }?>">
                        
                        <label for="">E-mail</label>
                        <span>E-mail</span>
                        <!-- Input field for email address -->
                        <input type="email" name="email" id="email" class="input" value="<?= hsc($_POST['email'] ?? '') ?>">

                        <!-- Error message container for the email input -->
                        <div class="input-error">
                            <p>
                                <?= $error['email_missing'] ?? '' ?> <!-- Display general email error if present -->
                                <?= $error['email_exists'] ?? '' ?> <!-- Display error if email already exists -->
                            </p> 
                        </div>
                    </div><!-- .input-container -->

                    <!-- Container for the password input field -->
                    <div class="input-container">
                        <label for="">Password</label>
                        <span>Password</span>

                        <!-- Input field for password -->
                        <input type="password" name="password" class="input" id="myInput">

                        <!-- Error message for password input -->
                        <div class="input-error">
                            <p><?= $error['password_missing'] ?? '' ?></p> <!-- Display error messages if any -->
                        </div>

                        <!-- Icon to toggle password visibility -->
                        <i id="myInputIcon" class="eye fa-solid fa-eye" onclick="togglePasswordVisibility('myInput', 'myInputIcon')"></i> <!-- Eye icon to show/hide password -->
                    </div><!-- .input-container -->

                    <!-- Container for the role selection input -->
                    <div class="input-container focus dropdown-container">
                        <label for="">Role</label>
                        <span>Role</span>

                        <!-- Dropdown for selecting user role, required field -->
                        <select name="role" required class="input form-control">
                            <option value=""></option> <!-- Empty option as a placeholder -->
                            <option value="1" selected>Admin</option> <!-- Option for Admin role, set as default -->
                            <option value="0">User</option> <!-- Option for User role -->
                        </select>

                        <!-- Error message container for role selection -->
                        <div class="input-error">
                            <p><?= $error['role_missing'] ?? '' ?></p> <!-- Display error messages for role if any -->
                        </div>
                    </div><!-- .input-container -->

                    <!-- Wrapper for form action buttons -->
                    <div class="form-actions">
                        <!-- Submit button to add a new admin, triggers form submission -->
                        <button type="submit" name="add-user" class="primary-btn">Add Admin</button>
                    </div><!-- .form-actions -->
                </form>

            </div><!-- .user-management-form-wrapper -->
        </div><!-- .admin-main-wrapper -->
    </body>
</html>
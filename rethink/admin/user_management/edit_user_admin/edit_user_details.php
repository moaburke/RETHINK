<?php
/**
 * File: edit_user_details.php
 * Author: Moa Burke
 * Date: 2024-11-04
 * Description: Generates an HTML form for updating user profile information.
 * 
 * This script retrieves and displays the current user's data, allowing updates 
 * to first name, last name, email, password, and role, while providing error 
 * messages for validation issues.
 * 
 * Functionality:
 * - Displays input fields for first name, last name, email, password, and role.
 * - Pre-fills input fields with existing user data.
 * - Highlights fields with existing data by adding a 'focus' class.
 * - Validates input and displays relevant error messages.
 * - Includes a button to submit the form for updating user information.
 * 
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

// Check if the 'update-user' form has been submitted
if(isset($_POST['update-user'])){
    // Retrieve the user ID from the submitted form
    $userID = $_POST['user_id'];

    // Validate required fields
    // Check if the username is provided
    if ($_POST['username'] == "") {
        // Store an error message indicating the username field is empty
        $error['username_missing'] = "ユーザー名が入力されていません。"; // Translation: "Username is required."
    }

    // Check if the first name is provided
    if ($_POST['firstName'] == "") {
        // Store an error message indicating the first name field is empty
        $error['firstname_missing'] = "名前が入力されていません。"; // Translation: "First name is required."
    }

    // Check if the last name is provided
    if ($_POST['lastName'] == "") {
        // Store an error message indicating the last name field is empty
        $error['lastname_missing'] = "苗字が入力されていません。"; // Translation: "Last name is required."
    }

    // Check if the email address is provided
    if ($_POST['email'] == "") {
        // Store an error message indicating the email field is empty
        $error['email_missing'] = "メールアドレスが入力されていません。"; // Translation: "Email address is required."
    }

    // Check if the password is provided
    if ($_POST['password'] == "") {
        // Store an error message indicating the password field is empty
        $error['password_missing'] = "パスアが入力されていません。"; // Translation: "Password is required."
    }

    // Check if a role is selected
    if ($_POST['role'] == "") {
        // Store an error message indicating the role selection is missing
        $error['role_missing'] = "Roleが選択されていません。"; // Translation: "Role selection is required."
    }

    // Retrieve the submitted username to check for uniqueness
    $username = $_POST['username'];

    // Query the database to check if the username already exists (excluding the current user)
    $usernameCheckQuery =  mysqli_query($con, "SELECT Username FROM users WHERE NOT UserID = '$userID' AND Username = '$username';");
    $usernameCount = mysqli_num_rows($usernameCheckQuery);

    // If the username already exists, add an error message
    if ($usernameCount > 0){
        $error['username_exists'] = "このユーザー名はすでに登録済みです。"; // Translation: "This username is already taken."
    }
    
    // If an email is provided, check for its existence in the database (excluding the current user)
    if (($_POST['email']) != "") {
        // Retrieve the submitted email for validation
        $email = $_POST['email'];

        // Query the database to check if the email already exists (excluding the current user)
        $emailCheckQuery =  mysqli_query($con, "SELECT Email FROM users WHERE NOT UserID = '$userID' AND Email = '$email';");
        $emailCount = mysqli_num_rows($emailCheckQuery);

        // If the email already exists, add an error message
        if ($emailCount > 0){
            $error['email_exists'] = "このメールアドレスはすでに登録済みです。"; // Translation: "This email address is already registered."
        }
    }

    // If there are no validation errors, proceed to update the user information
    if (empty($error)) { 
        // Retrieve user input for database update
        $userID = $_POST['user_id'];
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $submittedUsername = $_POST['username'];
        $submittedEmail = $_POST['email'];
        $role = $_POST['role'];

        // Check if a new password is provided
        if (($_POST['password']) == '') {
            // Update user information without changing the password
            $query = "UPDATE users SET 
                FirstName = '$firstName', 
                LastName = '$lastName', 
                Username = '$submittedUsername', 
                Role = '$role' 
                WHERE UserID = '$userID' "
            ;
            // Execute the update query
            $queryRun = mysqli_query($con, $query); 
        } else {
            // If a new password is provided, include it in the update
            $password = $_POST['password'];
            $query = "UPDATE users SET 
                FirstName = '$firstName', 
                LastName = '$lastName', 
                Username = '$submittedUsername', 
                Email = '$submittedEmail', 
                Password = '$password', 
                Role = '$role' 
                WHERE UserID = '$userID' "
            ;
            // Execute the update query
            $queryRun = mysqli_query($con, $query);
        }

        // Check if the query was successful
        if ($queryRun) {
            // Set a success message for successful update
            $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>ユーザーが正常に更新されました。</p></div>";
        } else {
            // Set a failure message for unsuccessful update
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

            <!-- Breadcrumb navigation for admin pages with a link back to Manage Users and an active indicator for the Edit User page -->
            <div class="breadcrumbs breadcrumbs-admin">
                <!-- Link to navigate back to the Manage Users page -->
                <a href="../manage_users.php">
                    <p>Manage Users</p>
                </a> 

                <!-- Right arrow icon to indicate breadcrumb separation -->
                <i class="fa-solid fa-angle-right fa-sm"></i>

                <!-- Current active breadcrumb indicating the page the user is on -->
                <p class="bread-active">Edit User</p>
            </div><!-- .breadcrumbs -->

            <div>
                <?php 
                // Check if a user ID is present in the URL parameters
                if (isset($_GET['id'])) {
                    // Retrieve the user ID from the URL and store it in $userID
                    $userID = $_GET['id'];
                    // Define a query to select all data for the specified user ID from the users table
                    $userQuery = "SELECT * FROM users WHERE UserID = $userID";
                    // Execute the query and store the result
                    $userResult = mysqli_query($con, $userQuery);

                    // Check if there are any rows returned by the query
                    if (mysqli_num_rows($userResult) > 0) {
                        // Loop through each user record returned by the query
                        foreach ($userResult as $userRecord) { ?>

                            <!-- Form wrapper for editing user details -->
                            <div class="user-management-form-wrapper">
                                <div class="user-management-header">
                                    <h3>
                                        <!-- Display titles in Japanese and English for editing the user -->
                                        <span class="japanese-title">ユーザーの編集</span>
                                        <span class="english-title">Edit User</span>
                                    </h3>
                                </div><!-- .user-management-header -->

                                <!-- Begin form for updating user information with POST method and file upload capability -->
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <!-- Hidden input to store the user ID, retrieved from $userRecord['UserID'], 
                                    so that it can be sent with the form submission without being visible to users -->
                                    <input type="hidden" name="user_id" value="<?=$userRecord['UserID'] ?>">

                                    <!-- Container for the username input field -->
                                    <div class="input-container 
                                        <?php 
                                        // Check if the 'Username' field in the user record is not empty
                                        if (($userRecord['Username']) != '') { 
                                            echo 'focus'; // If it's not empty, apply the 'focus' class to the input container
                                        }?>">

                                        <label for="">Username</label>
                                        <span>Username</span>

                                        <!-- Input field for the Username, pre-filled with the current value from the user record -->
                                        <input type="text" name="username" value="<?php echo $userRecord['Username'];?>" class=" input form-control" >

                                        <!-- Container to display any validation errors for the Username field -->
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
                                        // Check if FirstName is not empty; if true, add 'focus' class to highlight field
                                        if (($userRecord['FirstName']) != '') { 
                                            echo 'focus';
                                        }?>">

                                        <label for="">First Name</label>
                                        <span>First Name</span>

                                        <!-- Input field for first name, pre-filled with the user's current first name from the user record -->
                                        <input type="text" name="firstName" value="<?php echo $userRecord['FirstName'];?>" class="input form-control">

                                        <!-- Error message container for first name input -->
                                        <div class="input-error">
                                            <p><?= $error['firstname_missing'] ?? '' ?></p> <!-- Display error messages if any -->
                                        </div>
                                    </div><!-- .input-container -->

                                    <!-- Container for the last name input field -->
                                    <div class="input-container 
                                        <?php 
                                        // Check if LastName is not empty; if true, add 'focus' class to highlight field
                                        if (($userRecord['LastName']) != '') { 
                                            echo 'focus';
                                        }?>">

                                        <label for="">Last Name</label>
                                        <span>Last Name</span>

                                        <!-- Input field for last name, pre-filled with the user's current last name from the user record -->
                                        <input type="text" name="lastName" value="<?php echo $userRecord['LastName'];?>" class="input form-control">

                                        <!-- Error message container for last name input -->
                                        <div class="input-error">
                                            <p><?= $error['lastname_missing'] ?? '' ?></p> <!-- Display error messages if any -->
                                        </div>
                                    </div><!-- .input-container -->

                                    <!-- Container for the email input field -->
                                    <div class="input-container 
                                        <?php 
                                        // Check if Email field is not empty; if true, add 'focus' class to highlight field
                                        if (($userRecord['Email']) != '') { 
                                            echo 'focus';}
                                        ?>">

                                        <label for="">Email</label>
                                        <span>Email</span>

                                        <!-- Input field for email, pre-filled with the user's current email from the user record -->
                                        <input type="text" name="email" value="<?php echo $userRecord['Email'];?>" class="input form-control">

                                        <!-- Error message container for the email input -->
                                        <div class="input-error">
                                            <p>
                                                <?= $error['email_missing'] ?? '' ?> <!-- Display general email error if present -->
                                                <?= $error['email_exists'] ?? '' ?> <!-- Display error if email already exists -->
                                            </p> 
                                        </div>
                                    </div><!-- .input-container -->

                                    <!-- Container for the password input field -->
                                    <div class="input-container 
                                        <?php 
                                        // Check if Password field is not empty; if true, add 'focus' class to highlight field
                                        if (($userRecord['Password']) != '') { 
                                            echo 'focus';
                                        }?>">
                                        
                                        <label for="">Password</label>
                                        <span>Password</span>

                                        <!-- Input field for password, pre-filled with the user's current password from the user record -->
                                        <input type="password" name="password" value="<?php echo $userRecord['Password'];?>"class="input form-control">

                                        <!-- Error message for password input -->
                                        <div class="input-error">
                                            <p><?= $error['password_missing'] ?? '' ?></p> <!-- Display error messages if any -->
                                        </div>
                                    </div><!-- .input-container -->

                                    <!-- Container for the role selection input -->
                                    <div class="input-container focus select-area">
                                        <label for="">Role</label>
                                        <span>Role</span>

                                        <!-- Dropdown menu for selecting user role, required field -->
                                        <select name="role" required class="input form-control">
                                            <!-- Placeholder option for role selection, defaulting to empty -->
                                            <option value=""></option>

                                            <!-- Option for selecting 'Admin' role; if current user role is 'Admin', preselect this option -->
                                            <option value="1" <?= $userRecord['Role'] == '1' ? 'selected':'' ?> >Admin</option>

                                            <!-- Option for selecting 'User' role; if current user role is 'User', preselect this option -->
                                            <option value="0" <?= $userRecord['Role'] == '0' ? 'selected':'' ?> >User</option>
                                        </select>

                                        <!-- Error message container for role selection -->
                                        <div class="input-error">
                                            <p><?= $error['role_missing'] ?? '' ?></p> <!-- Display error messages for role if any -->
                                        </div>
                                    </div><!-- .input-container -->

                                    <!-- Container for the update button -->
                                    <div class="form-actions">
                                        <!-- Submit button to update user information -->
                                        <button type="submit" name="update-user" class="primary-btn">Update User</button>
                                    </div><!-- .form-actions -->
                                </form>

                            </div><!-- ./user-management-form-wrapper -->
                        <?php 
                        } // End of foreach loop to iterate through user query results

                    } else { ?>
                        <!-- Display message if no user record is found -->
                        <h4>No record found</h4>
                    <?php 
                    } // End of if statement checking for query results
                    
                } // End of outer if statement checking for 'id' parameter in the GET request
                ?>
            </div>
        </div><!-- .admin-main-wrapper -->
    </body>
</html>
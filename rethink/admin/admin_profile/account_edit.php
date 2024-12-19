<?php
/**
 * File: account_edit.php
 * Author: Moa Burke
 * Date: 2024-11-06
 * Description: Handles admin account editing, including form validation, database updates, and password changes.
 * 
 * This script provides functionality for admins to edit their account information, including username, first name, 
 * last name, email, and password. It handles form validation, updates to personal details in the database, 
 * password change functionality, and displays success or error messages. The page layout includes admin navigation 
 * and breadcrumb navigation for user orientation.
*/
session_start(); // Start the session

// Define a constant for the base path
define('BASE_PATH', '../../server-side/shared/');

include(BASE_PATH . "connections.php"); // Include databade connection file
include(BASE_PATH . "check_login.php"); // Include login check script to verify user authentication before accessing this page.
include(BASE_PATH . "timezone.php"); // Include the timezone configuration
include(BASE_PATH . "header/admin_layout.php"); // Include the admin header layout file

// Check if the admin is logged in 
$adminData = check_login($con);
// Retrieve the UserID of the logged-in admin
$adminID = $adminData['UserID']; 

// Get today's date in Y-m-d format
$date = date("Y-m-d");

// Check if the update information form was submitted
if(isset($_POST['updateInformationAdmin'])){

    // Validate the username field
    if ($_POST['username'] == "") {
        // If the username is empty, set an error message
        $error['username_missing'] = "ユーザー名が入力されていません。"; // Username is not provided
    } else {
        // If the username is provided, check if it already exists in the database
        $usernameInput = $_POST['username']; 
        $checkUsernameQuery =  mysqli_query($con, "SELECT Username FROM users WHERE NOT UserID = '$adminID' AND Username = '$usernameInput';");
        $existingUsernamesCount = mysqli_num_rows($checkUsernameQuery);

        // If the username already exists, set an error message
        if ($existingUsernamesCount > 0){
            $error['username_exists'] = "このユーザー名はすでに登録済みです。"; // This username is already registered
        }
    }

    // Validate the first name field
    if ($_POST['firstname'] == "") {
        // If the first name is empty, set an error message
        $error['firstname_missing'] = "名前が入力されていません。"; // First name is not provided
    }

    // Validate the last name field
    if ($_POST['lastname'] == "") {
        // If the last name is empty, set an error message
        $error['lastname_missing'] = "苗字が入力されていません。"; // Last name is not provided
    }

    // Validate the email field
    if ($_POST['email'] == "") {
        // If the email is empty, set an error message
        $error['email_missing'] = "メールアドレスが入力されていません。"; // Email is not provided
    } else {
        // If the email is provided, check if it already exists in the database
        $emailInput = $_POST['email'];
        $checkEmailQuery =  mysqli_query($con, "SELECT Email FROM users WHERE NOT UserID = '$adminID' AND Email = '$emailInput';");
        $existingEmailsCount = mysqli_num_rows($checkEmailQuery);

        // If the email already exists, set an error message
        if ($existingEmailsCount > 0) { 
            $error['email_exists'] = "このメールアドレスはすでに登録済みです。"; // This email is already registered
        }
    }

    // Check if there are no validation errors
    if (empty($error)) {
        // Retrieve user input from the form
        $usernameInput = $_POST['username']; // The username entered by the user
        $firstNameInput = $_POST['firstname']; // The first name entered by the user
        $lastNameInput = $_POST['lastname']; // The last name entered by the user
        $emailInput = $_POST['email']; // The email entered by the user

        // Prepare the SQL query to update user information in the database
        $updateQuery = "UPDATE users SET FirstName = '$firstNameInput', LastName = '$lastNameInput', Username = '$usernameInput', Email = '$emailInput'  WHERE UserID = '$adminID' ";
        $queryRun = mysqli_query($con, $updateQuery); // Execute the query
        
        // Check if the query was successful
        if ($queryRun) {
            // Set a success message in the session and redirect to the profile edit page
            $_SESSION['feedbackMessage'] = "<div class='message-text success-alert mypage-alert'><p>個人情報変更しました。</p></div>"; // Personal information updated successfully
            header('Location: account_edit.php'); // Redirect to the profile edit page
            exit(0); // Stop execution after the redirect
        }
    }

    // If there are validation errors, set an error message in the session
    if (!empty($error)) {
        $_SESSION['feedbackMessage'] = "<div class='message-text fail-alert'><p>個人情報が変更できませんでした。</p></div>"; // Failed to update personal information
    }
}

// Check if the update password form was submitted
if (isset($_POST['updatePassword'])) {
    // Validate the current password input
    if ($_POST['currentPassword'] == "") {
        $error['current_password_missing'] = "現在のパスワードが入力されていません。"; // Current password is not entered
    }

    // Validate the new password input
    if ($_POST['newPassword'] == "") {
        $error['new_password_missing'] = "新しいパスワードが入力されていません。"; // New password is not entered
    }

    // Validate the confirmation of the new password
    if ($_POST['newPasswordCheck'] == "") {
        $error['confirm_password_missing'] = "確認用のパスワードが入力されていません。"; // Confirmation password is not entered
    }

        // Check if there are no validation errors
    if (empty($error)) {
        $currentPasswordInput = $_POST['currentPassword']; // Current password entered by the user
        $newPassword = $_POST['newPassword']; // New password entered by the user
        $newPasswordConfirmation = $_POST['newPasswordCheck']; // Confirmation of the new password
    
        // Retrieve the current password from the database
        $queryUserPassword = mysqli_query($con, "SELECT Password FROM users WHERE UserID = '$adminID'");
        $userPasswordData = mysqli_fetch_assoc($queryUserPassword);
        $storedPassword =$userPasswordData['Password'];  // The password stored in the database

        // Verify if the entered current password matches the password stored in the database
        if ($storedPassword != $currentPasswordInput) {
            // If the current password does not match, add an error message indicating the input is incorrect
            $error['current_password_incorrect'] = "現在のパスワード入力に誤りがあります。"; // The current password entered is incorrect
        } else {

            // Proceed to validate the new password and its confirmation
            // Check if the new password matches the confirmation password
            if ($newPassword != $newPasswordConfirmation) {
                // If the passwords do not match, add an error message for mismatch
                $error['passwords_mismatch'] = "新しいパスワードと確認用のパスワードが一致していません。"; // New password and confirmation password do not match
            } elseif((strlen($newPassword) <= 5)) {  // Check if the new password meets the minimum length requirement
                $error['password_too_short'] = 'パスワードは最低6文字必要です。'; // Password must be at least 5 characters long
            } else {
                // If validation passes, prepare to update the password in the database
                $updatePasswordQuery = "UPDATE users SET Password = '$newPassword' WHERE UserID = '$adminID' ";
                // Execute the query to update the password
                mysqli_query($con, $updatePasswordQuery);

                // Set a success message indicating the password has been changed
                $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>パスワードが変更しました。</p></div>"; // Password has been changed successfully

                header('Location: account_edit.php'); // Redirect to the profile edit page
                exit(0); // Stop execution after the redirect
            }
        }
    }

    // If there are validation errors, set an error message in the session
    if (!empty($error)) {
        $_SESSION['feedbackMessage'] = "<div class='message-text fail-alert'><p>パスワードが変更できませんでした。</p></div>"; // Failed to change the password
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Load mutual head components and necessary scripts/styles -->
        <?php includeHeadAssets(); ?>

    </head>

    <body>
        <header class="sidebar-navigation">
            <!-- Include the mutual header for admin navigation -->
            <?php renderAdminNavigation(); ?>
        </header>

        <!-- Logout button and sticky header for logged-in admin -->
        <?php renderAdminHeaderWithLogout($adminData); ?>

    <div class="admin-main-wrapper">
        <!-- Heading for the 'Account Details' section -->
        <h2>Account Details</h2>

       <!-- Breadcrumb navigation to indicate the current page and allow easy navigation -->
       <div class="breadcrumbs breadcrumbs-admin">
            <!-- Link to the Admin Dashboard page -->
            <a href="../admin_dashboard.php">
                <p>Dashboard</p>
            </a>

            <!-- Right arrow icon to indicate breadcrumb separation -->
            <i class="fa-solid fa-angle-right fa-sm"></i>
            
            <!-- Link to the Account Details page -->
            <a href="./admin_profile.php"><p>Account Details</p></a>

            <!-- Right arrow icon to indicate breadcrumb separation -->
            <i class="fa-solid fa-angle-right fa-sm"></i>

            <!-- Current page indicator for Account Edit, highlighting the active page in the breadcrumb -->
            <p class="bread-active">Account Edit</p>
        </div><!-- .breadcrumbs -->

        <section class="account-edit-wrapper">
                <!-- Form for updating personal information. Allows the user to edit and submit details such as username, first name, last name, and email -->
                <form action="" method="post"  enctype="multipart/form-data">

                <!-- Include file for alert messages -->
                <div>
                    <?php include('../../server-side/shared/feedback_messages.php'); ?>
                </div>

                <div class="account-edit-table">
                    <!-- Section title displayed in both Japanese and English for accessibility and clarity -->
                    <h3>
                        <!-- Japanese title for "Change Personal Information" -->
                        <span class="japanese-title">個人情報変更</span>
                        <span class="english-title">Change Personal Information</span>
                    </h3>

                   <!-- Error message display area. Shows any errors related to the form fields (username, firstname, lastname, email) -->
                   <div class="input-error">
                        <p>
                            <?= $error['username_missing'] ?? '' ?>
                            <?=$error['username_exists'] ?? '' ?>
                            <?= $error['firstname_missing'] ?? '' ?>
                            <?= $error['lastname_missing'] ?? '' ?>
                            <?= $error['email_missing'] ?? '' ?>
                            <?= $error['email_exists'] ?? '' ?>
                        </p>
                    </div>

                    <table>
                        <!-- Username input row -->
                        <tr>
                            <!-- Label: "Username" in Japanese -->
                            <td>ユーザー名</td>
                            <!-- Input field pre-filled with current username if available -->
                            <td><input type="text" name="username" value="<?php if(!empty($adminData['Username'])){ echo $adminData['Username']; } ?>"></td>
                        </tr>

                        <!-- First Name input row -->
                        <tr>
                            <!-- Label: "First Name" in Japanese -->
                            <td>名前</td>
                            <!-- Input field pre-filled with current first name if available -->
                            <td><input type="text" name="firstname" value="<?php if(!empty($adminData['FirstName'])){ echo $adminData['FirstName']; } ?>"></td>
                        </tr>

                        <!-- Last Name input row -->
                        <tr>
                            <!-- Label: "Last Name" in Japanese -->
                            <td>苗字</td>
                            <!-- Input field pre-filled with current last name if available -->
                            <td><input type="text" name="lastname" value="<?php if(!empty($adminData['LastName'])){ echo $adminData['LastName']; } ?>"></td>
                        </tr>

                        <!-- Email input row -->
                        <tr>
                            <!-- Label: "Email Address" in Japanese -->
                            <td>メールアドレス</td>
                            <!-- Input field pre-filled with current email if available -->
                            <td><input type="text" name="email" value="<?php if(!empty($adminData['Email'])){ echo $adminData['Email']; } ?>"></td>
                        </tr>
                    </table>
                </div><!-- .account-edit-table -->

                <!-- Button wrapper for the submit action -->
                <div class="profile-edit-button">
                    <!-- Submit button for confirming updates to personal information -->
                    <input type="submit" value="Confirm" name="updateInformationAdmin" class="primary-btn"/>
                </div><!-- .profile-edit-button -->   
            </form>
    
            <!-- Form for updating password -->
            <form action="" method="post" enctype="multipart/form-data">
                <div class="account-edit-table">
                    <!-- Section header for changing the user's password -->
                    <h3>
                        <span class="japanese-title">パスワード変更</span>
                        <span class="english-title">Change Password</span>
                    </h3>

                    <!-- Display error messages related to password updates -->
                    <div class="input-error">
                        <p>
                            <?= $error['current_password_missing'] ?? '' ?>
                            <?= $error['new_password_missing'] ?? '' ?>
                            <?= $error['confirm_password_missing'] ?? '' ?>
                            <?= $error['current_password_incorrect'] ?? '' ?>
                            <?= $error['passwords_mismatch'] ?? '' ?>
                            <?= $error['password_too_short'] ?? '' ?>
                        </p>
                    </div>
                    
                    <table>
                        <tr>
                            <td>現在のパスワード</td>
                            <!-- Input field for current password with toggle visibility icon -->
                            <td>
                                <input type="password" name="currentPassword" id="currentPasswordInput">
                                <!-- Icon to toggle the visibility of the current password -->
                                <p><i id="currentPasswordToggle" class="fa-solid fa-eye" onclick="togglePasswordVisibility('currentPasswordInput', 'currentPasswordToggle')"></i>
                            </td>
                        </tr>
                        <tr>
                            <td>新しいパスワード</td>
                            <!-- Input field for new password with toggle visibility icon -->
                            <td>
                                <input type="password" name="newPassword" id="newPasswordInput">
                                <!-- Icon to toggle the visibility of the new password -->
                                <p><i id="newPasswordToggle" class="fa-solid fa-eye" onclick="togglePasswordVisibility('newPasswordInput', 'myInputnewPasswordToggleIcon')"></i>
                            </td>
                        </tr>
                        <tr>
                            <td>新しいパスワード(確認用)</td>
                            <!-- Input field for password confirmation with toggle visibility icon -->
                            <td>
                                <input type="password" name="newPasswordCheck" id="confirmPasswordInput">
                                <!-- Icon to toggle the visibility of the confirmed password -->
                                <p><i id="confirmPasswordToggle" class="fa-solid fa-eye" onclick="togglePasswordVisibility('confirmPasswordInput', 'confirmPasswordToggle')"></i>
                            </td>
                        </tr>
                    </table>
                </div><!-- .account-edit-table -->

                <div class="profile-edit-button">
                    <!-- Submit button for confirming the password update -->
                    <input type="submit" value="Confirm" name="updatePassword" class="primary-btn"/>
                </div><!-- .profile-edit-button -->   
            </form>

        </section><!-- .account-edit-wrapper -->
        
    </body>
</html>
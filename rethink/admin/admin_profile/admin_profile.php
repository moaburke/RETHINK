<?php
/**
 * File: admin_profile.php
 * Author: Moa Burke
 * Date: 2024-11-06
 * Description: Admin profile management page for displaying, viewing, and updating profile information and image.
 * 
 * This page handles the display and management of an admin's profile details, including
 * personal information and profile image. It allows admins to view and update their profile picture
 * and provides access to account details such as username, full name, email, and account creation date.
 * 
 * - Verifies admin login status and retrieves their details from the database.
 * - Displays the profile image, with an option to upload a new image.
 * - Provides a modal for image upload functionality, with validations for acceptable image types.
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

// Retrieve the user's profile image from the database
$profileImageQuery =  mysqli_query($con, "SELECT profileImg FROM users WHERE UserID = '$adminID' ");
// Check if a profile image was found for the given user ID
$profileImageCount = mysqli_num_rows($profileImageQuery);

// Check if the image upload form has been submitted
if (isset($_POST['uploadImg'])) {

    // Check if an image file has been uploaded
    if (!empty($_FILES['image']['name'])) {
        // Get the file extension of the uploaded image
        $fileExtension = substr($_FILES['image']['name'], -4);

        // Validate the file extension
        if ($fileExtension != '.jpg' && $fileExtension != '.png') {
            // Set an error message if the file type is not allowed
            $uploadErrors['image'] = 'その画像は使用できません。使用可能拡張子：.img .jpg';
        }
    }

    // Proceed if there are no upload errors
    if (empty($uploadErrors)) {
        // Temporarily store the uploaded image
        if (!empty($_FILES['image']['name'])) {
            $uploadedImageName = date('YmdHis') . $fileExtension;  // Generate a unique name for the image
        } else {
            $uploadedImageName = ''; // No image uploaded
        }

        // Attempt to move the uploaded file to the designated directory
        $isUploadSuccessful = move_uploaded_file($_FILES['image']['tmp_name'], '../../assets/user-img/' . $uploadedImageName);

        // If the file upload is successful
        if ($isUploadSuccessful) {    
            // Prepare the SQL query to update the user's profile image
            $updateProfileImageQuery = "UPDATE users SET profileImg = '$uploadedImageName' WHERE UserID = '$adminID' ";
            $isQueryExecuted = mysqli_query($con, $updateProfileImageQuery);
        }
    
        // Check if the query executed successfully
        if ($isQueryExecuted) {
            // Set a success message in the session
            $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>画像変更しました。</p></div>";

        } else {
            // Set an error message in the session if the upload fails
            $_SESSION['feedbackMessage'] = "<div class='message-text fail-alert'><p>何か問題が発生しました。</p></div>";

        }
    }
    header('Location: ./admin_profile.php'); // Redirect to the user's page
    exit(0); // Stop execution after the redirect
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

        <!-- Main container for admin profile page content -->
        <div class="admin-main-wrapper admin-profile-wrapper">
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

                <!-- Current page indicator for Account Details, highlighting the active page in the breadcrumb -->
                <p class="bread-active">Account Details</p>
            </div><!-- .breadcrumbs -->

            <!-- Include file for alert messages -->
            <div>
                <?php include('../../server-side/shared/feedback_messages.php'); ?>
            </div>

            
            <div class="profile-image" id="profile-image">
                <img src="../../assets/user-img/<?php 
                    // Check if there are profile images available
                    if ($profileImageCount > 0) {
                        // Fetch the user's profile image from the database
                        $profileImageData = mysqli_fetch_assoc($profileImageQuery); 
                        $profileImageFilename = $profileImageData['profileImg']; 
                        echo $profileImageFilename; // Output the profile image filename
                    } ?>" 
                alt="Admin Profile Image"> <!-- Image displayed as the user's profile picture -->

                <!-- Label for changing the image -->
                <p>画像変更</p>
                
            </div><!-- .profile-image -->

            <!-- Container for displaying user's full name and username -->
            <div class="user-name-container">

                <!-- Displays the user's full name, formatted based on the order of first and last name -->
                <div class="full-name">
                    <?php 
                    // Check if the first name contains only alphabetic characters
                    if (ctype_alpha($adminData['FirstName'])) { 
                        // If valid, display the first and last name in the order: First Name Last Name
                        echo "<p>" . htmlspecialchars($adminData['FirstName']) . "</p><p>" . htmlspecialchars($adminData['LastName']) . "</p>" ;
                    } else { 
                        // If not valid, swap the order to: Last Name First Name
                        echo "<p>" . htmlspecialchars($adminData['LastName']) . "</p><p>" . htmlspecialchars($adminData['FirstName']) . "</p>" ; 
                    }
                    ?>
                </div>

                <!-- Displays the username -->
                <div class="username">
                    <!-- Display the user's username with an "@" symbol -->
                    <p>@<?php echo htmlspecialchars($adminData['Username']) ?></p>
                </div>
            </div><!-- .user-name-container -->

            <!-- Main container for the user information -->
            <div class="user-profile-contents">
                <div class="account-info-table">

                    <!-- Container for account creation information -->
                    <div class="account-creation-info">
                        <p>アカウント作成：<?php echo $adminData['Created'] ?></p> <!-- Display the account creation date -->
                    </div>

                    <!-- Table to display user details -->
                    <table>
                        <!-- Row for username -->
                        <tr>
                            <td>ユーザー名</td> <!-- Column header for username -->
                            <td><?php echo htmlspecialchars($adminData['Username']) ?></td> <!-- Display the username -->
                        </tr>
                        <!-- Row for first name -->
                        <tr>
                            <td>名前</td> <!-- Column header for first name -->
                            <td><?php echo htmlspecialchars($adminData['FirstName']) ?></td> <!-- Display the first name -->
                        </tr>
                        <!-- Row for last name -->
                        <tr>
                            <td>苗字</td> <!-- Column header for last name -->
                            <td><?php echo htmlspecialchars($adminData['LastName']) ?></td> <!-- Display the last name -->
                        </tr>
                        <!-- Row for email address -->
                        <tr>
                            <td>メールアドレス</td> <!-- Column header for email address -->
                            <td><?php echo $adminData['Email'] ?></td> <!-- Display the email address -->
                        </tr>
                        <!-- Row for password (hidden) -->
                        <tr>
                            <td>パスワード</td> <!-- Column header for password -->
                            <td>********</td> <!-- Placeholder for password (not displayed for security) -->
                        </tr>
                    </table> <!-- End of user details table -->

                </div><!-- .account-info-table -->

                <!-- Container for action buttons -->
                <div class="profile-action-buttons">
                    <!-- Button to edit profile -->
                    <a href="account_edit.php"><button class="primary-btn">Edit</button></a>
                </div>

            </div><!-- .user-profile-contents -->

            <!-- Image Change Modal -->
            <div id="myModal" class="modal">
                <div class="modal-content profile-image-modal-wraper">
                    <!-- Main content area of the image change modal -->
                    <div class="modal-main image-change-modal">

                        <!-- Form for uploading a new profile image -->
                        <form action="./admin_profile.php" method="post" enctype="multipart/form-data">
                            <section class="image-change-form-table">
                                <!-- Modal header with title displayed in both Japanese and English for accessibility -->
                                <h3>
                                    <!-- Title in Japanese -->
                                    <span class="japanese-title">画像変更</span>
                                    <!-- Title in English -->
                                    <span class="english-title">Change Image</span>
                                </h3>

                                <!-- Table containing the form for selecting a new image file -->
                                <table>
                                    <tr>
                                        <!-- Label in Japanese for the file input field -->
                                        <td>画像</td>
                                        <!-- File input field allowing user to select an image file, accepts all image types -->
                                        <td>
                                            <input type="file" name="image" class="image-upload-input" size="35" value="test" accept="image/*" />
                                        </td>
                                    </tr>
                                </table>

                                <div class="modal-buttons">
                                    <!-- Cancel button to close the modal -->
                                    <div class="cancel-button">
                                        <p>Cancel</p>
                                    </div>

                                    <!-- Upload button to submit the new image -->
                                    <div class="upload-image-button">
                                        <input type="submit" name="uploadImg" value="Upload" class="submit-upload-button"/>
                                    </div>
                                </div><!-- .modal-buttons -->
                            </section>
                        </form>

                    </div><!-- .modal-main -->
                </div><!-- .modal-content -->
            </div><!-- .modal -->

        </div><!-- .admin-main-wrapper -->
    </body>

    <script>
        // This script handles the display of a modal for image uploading when the user profile image is clicked,
        // manages the display of comments associated with posts, and provides a confirmation prompt for deletion actions.

        // ------- Modal functionality -------
        var imageUploadModal = document.getElementById("myModal"); // Get the modal element by its ID
        var profileImageButton = document.getElementById("profile-image"); // Get the profile image button by its ID\
        var modalCloseButton = document.getElementsByClassName("cancel-button")[0];  // Get the close button element by its class name

        // Set up an event listener for the button click
        profileImageButton.onclick = function() {
            // Display the modal by changing its CSS display property to 'block'
            imageUploadModal.style.display = "block";
        }

        // Set up an event listener for the close button click
        modalCloseButton.onclick = function() {
            // Hide the modal by changing its CSS display property to 'none'
            imageUploadModal.style.display = "none";
        }
    </script>
</html>
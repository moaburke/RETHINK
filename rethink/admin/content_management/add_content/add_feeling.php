<?php
/**
 * File: add_feeling.php
 * Author: Moa Burke
 * Date: 2024-11-07
 * Description: Provides a form for admins to add new feelings to the system.
 * 
 * This script generates an HTML form that allows administrators to add new feelings 
 * to the system, including options to select a feeling's intensity (positive, neutral, 
 * or negative). These entries will be accessible to users when registering their 
 * daily mood, facilitating mood tracking in relation to the feelings they experience 
 * each day.
 * 
 * Functionality:
 * - Displays input fields for the feeling name, and feeling intensity selection.
 * - Validates input fields for feeling name and intensity, providing error messages 
 *   for any invalid entries.
 * - Inserts new feeling entries into the database upon successful validation.
 * - Displays feedback messages based on the success or failure of the data insertion.
 * - Includes a button to submit the form for adding new feeling data.
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

// Get today's date in Y-m-d format
$date = date("Y-m-d");

// Check if the 'add_feeling' form has been submitted
if (isset($_POST['add_feeling'])) {
    // Check if any data has been posted
    if (!empty($_POST)) { 
        // Validate that the feeling name is not empty
        if ($_POST['feelingName'] == "") {
            // If feelingName is empty, store an error message 
            $error['name_missing'] = "気持ち名が入力されていません"; // Translation: "Feeling name has not been entered"
        }

        // Validate that the loading option is selected
        if ($_POST['feelingLoading'] == "") {
            // If feelingLoading is empty, store an error message 
            $error['loading_missing'] = "Loadingが選択されていません"; // Translation: "Loading option has not been selected"
        }

        // Proceed with the insertion only if there are no validation errors
        if (empty($error)) {
            // Retrieve the feeling name and loading ID from POST data
            $feelingName = $_POST['feelingName'];
            $feelingLoading= $_POST['feelingLoading'];

            // Construct the SQL query to insert the new feeling into the database
            $queryFeeling = "INSERT INTO feelings (FeelingName, FeelingLoadingID) VALUES('$feelingName', '$feelingLoading')";
            $queryRunFeeling = mysqli_query($con, $queryFeeling);

            // Check if the insertion was successful
            if ($queryRunFeeling) {
                // If successful, set a success message in the session feedbackMessage
                $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>人々が正常に追加されました。</p></div>"; // Translation: "Company added successfully"
            } else { 
                // If the update failed, set a failure message in the session feedbackMessage
                $_SESSION['feedbackMessage'] = "<div class='message-text fail-alert'><p>何か問題が発生しました。</p></div>"; // Translation: "Something went wrong"
            }
    
            // Redirect the user to the manage_content.php page after handling the form submission
            header('Location: ../manage_content.php');
            // Ensure the script stops executing after the redirect to avoid any additional processing
            exit(0);
        }
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
        <header class="sidebar-navigation manage-contents-navigation">
            <!-- Render the sidebar navigation specifically for the admin section -->
            <?php renderAdminNavigation(); ?>
        </header>

        <!-- Render the header for the admin dashboard with logout functionality, using the admin's data -->
        <?php renderAdminHeaderWithLogout($adminData); ?>

        <!-- Main wrapper for the add content section -->
        <div class="admin-main-wrapper">
            <!-- Header for the Manage Content section -->
            <h2>Manage Content</h2>

            <div class="breadcrumbs breadcrumbs-admin">
                <!-- Link to navigate back to the Manage Users page -->
                <a href="../manage_content.php">
                    <p>Manage Content</p>
                </a> 

                <!-- Right arrow icon to indicate breadcrumb separation -->
                <i class="fa-solid fa-angle-right fa-sm"></i>

                <!-- Current active breadcrumb indicating the page the user is on -->
                <p class="bread-active">Add Feeling</p>
            </div><!-- End of .breadcrumbs -->

            <!-- Wrapper for the entire form section for adding feelings -->
            <div class="content-management-form-wrapper">
                <!-- Top section for the form title -->
                <div class="content-management-header">
                    <h3>
                        <!-- Japanese title for the form -->
                        <span class="japanese-title">気持ちの追加</span>
                        <!-- English title for the form -->
                        <span class="english-title">Add Feeling</span>
                    </h3>
                </div><!-- End of .content-management-header -->

                <!-- Form for submitting new feeling data -->
                <form action="" method="POST" enctype="multipart/form-data">
                    <!-- Input container for the feeling name -->
                    <div class="input-container input-name 
                        <?php 
                        // Check if the feeling name has been posted and is not empty
                        if (isset($_POST['feelingName'])) {
                            // Verify that the submitted feeling name is not empty
                            if ($_POST['feelingName'] != '') {
                                echo 'focus'; // Add 'focus' class to the container if the input is filled
                            }
                        } ?>">

                        <label for="">Name</label>
                        <span>Name</span>

                        <!-- Input field for the feeling name, retains value after form submission -->
                        <input type="text" name="feelingName" class="input" value="<?= hsc($_POST['feelingName'] ?? '') ?>">

                        <!-- Container for displaying any error messages related to the feeling name input -->
                        <div class="error">
                            <p><?= $error['name_missing'] ?? '' ?></p>
                        </div>
                    </div><!-- End of .input-container -->

                    <!-- Container for the input area where the user selects a feeling -->
                    <div class="input-container dropdown-container 
                        <?php 
                        // Check if the feelingLoading input has been posted and is not empty
                        if (isset($_POST['feelingLoading'])) {
                            if ($_POST['feelingLoading'] != '') {
                                echo 'focus'; // Add 'focus' class to the container if the input is filled
                            }
                        } ?>">

                        <label for="">Loading</label>
                        <span>Loading</span>

                        <!-- Dropdown for selecting a feeling with required validation -->
                        <select name="feelingLoading" required class="input">
                            <option value=""></option> <!-- Placeholder option -->

                            <option value="1" 
                                <?php 
                                // Check if the feelingLoading input is set and if it equals 1 to mark as selected
                                if (isset($_POST['feelingLoading'])) {
                                    if($_POST['feelingLoading'] == 1 ) {
                                        echo 'selected';
                                    }
                                } ?> 
                            >Positive</option>

                            <option value="2" 
                                <?php 
                                // Check if the feelingLoading input is set and if it equals 2 to mark as selected
                                if (isset($_POST['feelingLoading'])) {
                                    if ($_POST['feelingLoading'] == 2 ) {
                                        echo 'selected';
                                    }
                                } ?>
                            >Neutral</option>

                            <option value="3" 
                                <?php 
                                // Check if the feelingLoading input is set and if it equals 3 to mark as selected
                                if (isset($_POST['feelingLoading'])) {
                                    if ($_POST['feelingLoading'] == 3 ) {
                                        echo 'selected';
                                    }
                                } ?>
                            >Negative</option>
                        </select>

                        <div class="error-icon">
                            <!-- Display error message if there is an error with the feelingLoading input -->
                            <p><?= $error['loading_missing'] ?? '' ?></p>
                        </div>
                    </div><!-- End of .input-container -->  

                    <!-- Wrapper for the button to submit the feeling -->
                    <div class="button-wrapper">
                        <!-- Submit button for adding a new feeling -->
                        <button type="submit" name="add_feeling" class="primary-btn">Add Feeling</button>
                    </div><!-- End of .button-wrapper -->
                </form>
                
            </div><!-- End of .content-management-form-wrapper -->
        </div><!-- End of .admin-main-wrapper -->

    </body>
</html>
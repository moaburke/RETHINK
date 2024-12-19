<?php
/**
 * add_goal_category.php
 *
 * Author: Moa Burke
 * Date: 2024-11-05
 * Description: This script enables the admin user to add new goal categories to the system.
 *
 * The script generates an HTML form that allows administrators to input new goal 
 * categories, including names in both Japanese and English.
 *
 * Features:
 * - Provides input fields for entering the goal category names in Japanese and English.
 * - Validates the input fields to ensure both names are provided.
 * - Inserts new goal category entries into the database upon successful validation.
 * - Displays error messages for any missing or invalid input.
 * - Includes a submission button to add the new goal category to the system.
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

// Check if the 'add_goalcategory' form has been submitted
if (isset($_POST['add_goalcategory'])) {
    // Check if any data has been posted
    if (!empty($_POST)) { 
        // Validate that the Japanese goal category name is not empty
        if ($_POST['goalCategoryJp'] == "") {
            // Store an error message if the Japanese name is not provided
            $error['japanese_name_missing'] = "日本語名が入力されていません"; // Translation: "Japanese name has not been entered"
        }

        // Validate that the English goal category name is not empty
        if ($_POST['goalCategoryEn'] == "") {
            // Store an error message if the English name is not provided
            $error['english_name_missing'] = "英語語名が入力されていません"; // Translation: "English name has not been entered"
        }

        // Proceed with the insertion only if there are no validation errors
        if (empty($error)) {
            // Retrieve the Japanese and English goal category names from the POST data
            $weatherNameJp = $_POST['goalCategoryJp'];
            $weatherNameEn = $_POST['goalCategoryEn'];

            // Construct the SQL query to insert the new goal category into the database
            $queryWeather = "INSERT INTO goalcategories (GoalCategoryName, GoalCategoryNameJp) VALUES('$weatherNameJp', '$weatherNameEn')";
            // Execute the query
            $queryRunWeather = mysqli_query($con, $queryWeather);

            // Check if the query execution was successful
            if ($queryRunWeather) {
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
                <p class="bread-active">Add Goal Category</p>
            </div><!-- .breadcrumbs -->


            <!-- Wrapper for the entire form section for adding goal categories -->
            <div class="content-management-form-wrapper">
                <!-- Top section for the form title -->
                <div class="content-management-header">
                    <h3>
                        <!-- Japanese title for the form -->
                        <span class="japanese-title">目標カテゴリーの追加</span>
                        <!-- English title for the form -->
                        <span class="english-title">Add Goal Category</span>
                    </h3>
                </div><!-- .content-management-header -->

                 <!-- Form for submitting new goal category data -->
                <form action="" method="POST" enctype="multipart/form-data">
                    <!-- Input container for the goal category name in English -->
                    <div class="input-container input-name 
                        <?php 
                        // Check if the goal category name in English has been posted and is not empty
                        if (isset($_POST['goalCategoryEn'])) {
                            if($_POST['goalCategoryEn'] != '') {
                                echo 'focus'; // Add 'focus' class to the container if the input is filled
                            }
                        } ?>">

                        <label for="">Name English</label>
                        <span>Name English</span>

                        <!-- Input field for the goal category name in English, retains value after form submission -->
                        <input type="text" name="goalCategoryEn" class="input" value="<?= hsc($_POST['goalCategoryEn'] ?? '') ?>">

                        <!-- Container for displaying any error messages related to the goal category input -->
                        <div class="error">
                            <p><?= $error['english_name_missing'] ?? '' ?></p>
                        </div>
                    </div><!-- .input-container -->
                    
                    <div class="input-container input-name 
                        <?php 
                        // Check if the 'goalCategoryJp' input is set and not empty
                        if (isset($_POST['goalCategoryJp'])) {
                            if ($_POST['goalCategoryJp'] != '') {
                                echo 'focus'; // Add 'focus' class to the container if the input is filled
                            }
                        } ?>">

                        <label for="">Name Japanese</label>
                        <span>Name Japanese</span>

                        <!-- Input field for the goal category name -->
                        <input type="text" name="goalCategoryJp" class="input" value="<?= hsc($_POST['goalCategoryJp'] ?? '') ?>">

                        <!-- Container for displaying any error messages related to the weather name input -->
                        <div class="error">
                            <p><?= $error['japanese_name_missing'] ?? '' ?></p>
                        </div>
                    </div><!-- .input-container -->

                    <!-- Wrapper for the button that submits the form -->
                    <div class="button-wrapper">
                        <!-- Submit button for adding a new goal category -->
                        <button type="submit" name="add_goalcategory" class="primary-btn">Add Goal Category</button>
                    </div><!-- .button-wrapper -->
                </form>
            </div><!-- .content-management-form-wrapper -->
        </div><!-- .admin-main-wrapper -->
        
    </body>
</html>
<?php
/**
 * File: edit_goal_category.php
 * Author: Moa Burke
 * Date: 2024-11-07
 * Description: Provides a form for admins to edit goal category items in the system.
 * 
 * This page allows an admin to edit the details of a specific "goal category" record in the system.
 * Admins can update the Japanese and English names of the goal category.
 * The page checks for an active admin session, includes necessary configuration and layout files,
 * validates the form data (including required fields and proper formats), and updates the goal category in the database.
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

// Check if the 'update-goalCategory' form has been submitted
if (isset($_POST['update-goalCategory'])) {
    // Validate that the English category name field is not empty
    if ($_POST['GoalCategoryName'] == "") {
        // If empty, set an error message in the $error array with the key 'GoalCategoryName'
        $error['english_name_missing'] = "英語名が入力されていません"; // Translation: "English name has not been entered"
    }

    // Validate that the Japanese category name field is not empty
    if ($_POST['GoalCategoryNameJp'] == "") {
        // If empty, set an error message in the $error array with the key 'GoalCategoryNameJp'
        $error['japanese_name_missing'] = "日本語名が入力されていません"; // Translation: "Japanese name has not been entered"
    } 

    // Proceed with the update only if there are no validation errors
    if (empty($error)) {
        // Retrieve the goal category ID, English name, and Japanese name from the POST data
        $goalCategoryID = $_POST['GoalCategoriesID'];
        $goalCategoryName = $_POST['GoalCategoryName'];
        $goalCategoryNameJp = $_POST['GoalCategoryNameJp'];
        
        // Construct the SQL query to update the goal category in the database
        $query = "UPDATE goalcategories SET GoalCategoryName = '$goalCategoryName', GoalCategoryNameJp = '$goalCategoryNameJp' WHERE GoalCategoriesID = '$goalCategoryID' ";

        // Execute the query and store the result in $queryRun to check for success
        $queryRun = mysqli_query($con, $query);
        
        // Check if the query executed successfully
        if ($queryRun) {
            // If successful, set a success message in the session feedbackMessage
            $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>目標カテゴリーが正常に更新されました。</p></div>"; // Translation: "Goal category updated successfully"
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
                <p class="bread-active">Edit Goal Category</p>
            </div><!-- End of .breadcrumbs -->

            <!-- Wrapper for the entire form section for editing goal category items -->
            <div class="content-management-form-wrapper">
                <div class="content-management-header">
                    <h3>
                        <!-- Japanese title for the edit goal category section -->
                        <span class="japanese-title">目標カテゴリーの編集</span>
                        <!-- English title for the edit goal category section -->
                        <span class="english-title">Edit Goal Category</span>
                    </h3>
                </div><!-- End of .content-management-header -->

                <?php
                // Check if an 'id' is provided in the URL query parameters 
                if (isset($_GET['id'])) {
                    // Retrieve the goal category ID from the query parameter
                    $goalCategoryID = $_GET['id'];

                    // Query to select all columns from the 'goalcategories' table for the specified GoalCategoryID
                    $goalCategoryQuery = "SELECT * FROM goalcategories WHERE GoalCategoriesID = $goalCategoryID";
                    $goalCategoryResult = mysqli_query($con, $goalCategoryQuery);

                    // Check if there are results from the goalCategory query
                    if (mysqli_num_rows($goalCategoryResult) > 0) {
                        // Iterate through each goal category result and create a form for editing
                        foreach ($goalCategoryResult as $goalCategory){ ?>
                            <form action="" method="POST" enctype="multipart/form-data">
                                <!-- Hidden input to store the goal category ID -->
                                <input type="hidden" name="GoalCategoriesID" value="<?=$goalCategory['GoalCategoriesID'] ?>">

                                <!-- Container for input related to the English name of the goal category -->
                                <div class="input-container input-name focus">
                                    <label for="">Name English</label>
                                    <span>Name English</span>

                                    <!-- Text input field for entering or displaying the goal category's English name -->
                                    <input type="text" name="GoalCategoryName" value="<?php echo $goalCategory['GoalCategoryName'];?>" class="input">

                                    <!-- Display error message if there is an issue with the English name input -->
                                    <div class="error">
                                        <p><?= $error['english_name_missing'] ?? '' ?></p>
                                    </div>
                                </div><!-- End of .input-container -->

                                <!-- Container for input related to the Japanese name of the goal category -->
                                <div class="input-container input-name focus">
                                    <label for="">Name Japanese</label>
                                    <span>Name Japanese</span>

                                    <!-- Text input field for entering or displaying the goal category's Japanese name -->
                                    <input type="text" name="GoalCategoryNameJp" value="<?php echo $goalCategory['GoalCategoryNameJp'];?>" class="input">

                                    <!-- Display error message if there is an issue with the Japanese name input -->
                                    <div class="error">
                                        <p><?= $error['japanese_name_missing'] ?? '' ?></p>
                                    </div>
                                </div><!-- End of .input-container -->

                                <!-- Button wrapper for updating the goal category -->
                                <div class="button-wrapper">
                                    <button type="submit" name="update-goalCategory" class="primary-btn">Update Goal Category</button>
                                </div><!-- End of .button-wrapper -->
                            </form>

                        <?php } // End foreach loop for goal categories
                    } // End if statement checking for goal category results
                } // End if statement checking if 'id' parameter is set
                ?>
            </div><!-- End of ./content-management-form-wrapper -->
        </div><!-- End of .admin-main-wrapper -->
    </body>
</html>
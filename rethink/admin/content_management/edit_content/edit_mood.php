<?php
/**
 * File: edit_mood.php
 * Author: Moa Burke
 * Date: 2024-11-07
 * Description: Provides a form for admins to edit mood items in the system.
 * 
 * This page allows an admin to edit the details of a specific "mood" record in the system.
 * Admins can update the Japanese and English names of the mood.
 * The page checks for an active admin session, includes necessary configuration and layout files,
 * validates the form data (including required fields and proper formats), and updates the mood in the database.
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

// Check if the update mood form has been submitted
if (isset($_POST['update-mood'])) {

    // Check if the MoodName field is empty
    if ($_POST['MoodName'] == "") {
        // If MoodName is empty, store an error message in the $error array with the key 'MoodName'
        $error['english_name_missing'] = "気分が入力されていません"; // Translation: "Mood name has not been entered"
    }

    // Check if the JapaneseMoodName field is empty
    if ($_POST['JapaneseMoodName'] == "") {
        // If JapaneseMoodName is empty, store an error message in the $error array with the key 'JapaneseMoodName'
        $error['japanese_name_missing'] = "気分が入力されていません"; // Translation: "Mood name has not been entered"
    }

    // Proceed with the update only if there are no validation errors
    if (empty($error)) {
        // Retrieve the MoodID, MoodName, and JapaneseMoodName from the POST data
        // These values are assumed to be submitted by the form when the admin updates a mood
        $moodID = $_POST['MoodID'];
        $moodName = $_POST['MoodName'];
        $moodNameJapanese = $_POST['JapaneseMoodName'];

        // Construct an SQL query to update the mood record in the database
        // Update the MoodName and JapaneseMoodName fields where the MoodID matches the specified ID
        $query = "UPDATE moods SET MoodName = '$moodName', JapaneseMoodName = '$moodNameJapanese' WHERE MoodID = '$moodID' ";
        // Execute the query and store the result in $queryRun to check if the query was successful
        $queryRun = mysqli_query($con, $query);
    
        // Check if the query was executed successfully
        if($queryRun){
            // If the update was successful, set a success message in the session feedbackMessage
            $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>気分が正常に更新されました。</p></div>"; // Translation: "Mood updated successfully"
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
                <p class="bread-active">Edit Mood</p>
            </div><!-- End of .breadcrumbs -->

            <!-- Wrapper for the entire form section for editing mood -->
            <div class="content-management-form-wrapper">
                <div class="content-management-header">
                    <h3>
                        <!-- Japanese title for the edit mood section -->
                        <span class="japanese-title">気分の編集</span>
                        <!-- English title for the edit mood section -->
                        <span class="english-title">Edit Mood</span>
                    </h3>
                </div><!-- End of .content-management-header -->
                
                <?php 
                // Check if an 'id' is provided in the URL query parameters
                if (isset($_GET['id'])) {
                    // Retrieve the mood ID from the query parameter
                    $moodID = $_GET['id'];

                    // Query to select all columns from the 'mood' table for the specified MoodID
                    $moodQuery = "SELECT * FROM moods WHERE MoodID = $moodID";
                    $moodResult = mysqli_query($con, $moodQuery);

                    // Check if there are results from the mood query
                    if (mysqli_num_rows($moodResult) > 0) {
                        // Iterate through each mood result and create a form for editing
                        foreach ($moodResult as $mood){?>
                            <form action="" method="POST" enctype="multipart/form-data">
                                <!-- Hidden input to store the mood ID -->
                                <input type="hidden" name="MoodID" value="<?=$mood['MoodID'] ?>">

                                <!-- Input container for English mood name -->
                                <div class="input-container input-name 
                                    <?php 
                                    // Add 'focus' class if the mood name is not empty
                                    if (($mood['MoodName']) != '') { 
                                        echo 'focus';
                                    }?>">

                                    <label for="">Name English</label>
                                    <span>Name English</span>

                                    <!-- Input field for English mood name -->
                                    <input type="text" name="MoodName" value="<?php echo $mood['MoodName'];?>" class="input">

                                    <!-- Error message for English mood name -->
                                    <div class="error">
                                        <p><?= $error['english_name_missing'] ?? '' ?></p>
                                    </div>
                                </div><!-- End of .input-container -->

                                <!-- Input container for Japanese mood name -->
                                <div class="input-container input-name 
                                    <?php 
                                    // Add 'focus' class if the Japanese mood name is not empty
                                    if (($mood['JapaneseMoodName']) != '') { 
                                        echo 'focus';
                                    }?>">

                                    <label for="">Name Japanese</label>
                                    <span>Name Japanese</span>

                                    <!-- Input field for Japanese mood name -->
                                    <input type="text" name="JapaneseMoodName" value="<?php echo $mood['JapaneseMoodName'];?>" class="input">

                                    <!-- Error message for Japanese mood name -->
                                    <div class="error"><p><?= $error['japanese_name_missing'] ?? '' ?></p></div>
                                </div><!-- End of .input-container -->

                                <!-- Button wrapper for updating the mood -->
                                <div class="button-wrapper">
                                    <button type="submit" name="update-mood" class="primary-btn">Update Mood</button>
                                </div><!-- End of .button-wrapper -->
                            </form>

                        <?php } // End foreach loop for mood
                    } // End if statement checking for mood results
                } // End if statement checking if 'id' parameter is set
                ?>
            </div><!-- End of .content-management-form-wrapper -->
        </div><!-- End of .admin-main-wrapper -->
    </body>
</html>
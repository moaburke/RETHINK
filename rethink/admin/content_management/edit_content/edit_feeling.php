<?php
/**
 * File: edit_feeling.php
 * Author: Moa Burke
 * Date: 2024-11-07
 * Description: Provides a form for admins to edit feeling items in the system.
 * 
 * This page allows an admin to edit the details of a specific "feeling" record in the system.
 * Admins can update the name and loading type (e.g., Positive, Neutral, Negative) associated with each feeling.
 * The page checks for an active admin session, includes necessary configuration and layout files,
 * and validates the form data before updating the feeling in the database.
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

// Check if the 'update-feeling' form has been submitted by verifying if 'update-feeling' exists in the POST data
if (isset($_POST['update-feeling'])) {
    // Check if the FeelingName field is empty
    if ($_POST['FeelingName'] == "") {
        // If FeelingName is empty, store an error message 
        $error['name_missing'] = "気持ち名が入力されていません"; // Translation: "Feeling name has not been entered"
    }

    // Check if the FeelingLoadingID field is empty
    if ($_POST['FeelingLoadingID'] == "") {
        // If FeelingLoadingID is empty, store an error message 
        $error['loading_missing'] = "Loadingが選択されていません"; // Translation: "Loading has not been selected"
    }

    // Proceed with the update only if there are no validation errors
    if (empty($error)) {
        // Retrieve the FeelingID, FeelingName, and FeelingLoadingID from the POST data
        // These values are assumed to be submitted by the form when the admin updates a feeling
        $feelingID = $_POST['FeelingID'];
        $feelingName = $_POST['FeelingName'];
        $feelingLoading = $_POST['FeelingLoadingID'];

        // Construct an SQL query to update the feeling record in the database
        // Update the FeelingName and FeelingLoadingID fields where the FeelingID matches the specified ID
        $query = "UPDATE feelings SET FeelingName = '$feelingName', FeelingLoadingID = '$feelingLoading' WHERE FeelingID = '$feelingID' ";
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
                <p class="bread-active">Edit Feeling</p>
            </div><!-- End of .breadcrumbs -->

            <!-- Wrapper for the entire form section for editing feeling items -->
            <div class="content-management-form-wrapper">
                <div class="content-management-header">
                    <h3>
                        <!-- Japanese title for the edit feeling section -->
                        <span class="japanese-title">気持ちの編集</span>
                        <!-- English title for the edit feeling section -->
                        <span class="english-title">Edit Feeling</span>
                    </h3>
                </div><!-- End of .content-management-header -->

                <?php 
                // Check if an 'id' is provided in the URL query parameters
                if (isset($_GET['id'])) {
                    // Retrieve the feeling ID from the query parameter
                    $feelingID = $_GET['id'];

                    // Query to select all columns from the 'feelings' table for the specified FeelingID
                    $feelingQuery = "SELECT * FROM feelings WHERE FeelingID = $feelingID";
                    $feelingResult = mysqli_query($con, $feelingQuery);

                    // Check if there are results from the feeling query
                    if (mysqli_num_rows($feelingResult) > 0) {
                        // Iterate through each feeling result and create a form for editing
                        foreach ($feelingResult as $feeling){ ?>
                            <form action="" method="POST" enctype="multipart/form-data">
                                <!-- Hidden input to store the feeling ID -->
                                <input type="hidden" name="FeelingID" value="<?=$feeling['FeelingID'] ?>">

                                <!-- Container for the feeling name input field, with 'focus' class added if a feeling name already exists -->
                                <div class="input-container input-name 
                                    <?php 
                                    if (($feeling['FeelingName']) != '') { 
                                        echo 'focus';
                                    }?>">

                                    <label for="">Name</label>
                                    <span>Name</span>

                                    <!-- Input field for entering or displaying the name of the feeling -->
                                    <input type="text" name="FeelingName" value="<?php echo $feeling['FeelingName'];?>" class="input">

                                    <!-- Container for displaying any error messages related to the feeling name input -->
                                    <div class="error">
                                        <p><?= $error['name_missing'] ?? '' ?></p>
                                    </div>
                                </div><!-- End of .input-container -->

                                <!-- Container for the loading type selection dropdown -->
                                <div class="input-container focus dropdown-container">
                                    <label for="">Loading</label>
                                    <span>Loading</span>

                                    <!-- Dropdown for selecting the type of feeling loading (Positive, Neutral, Negative) -->
                                    <select name="FeelingLoadingID" required class="input">
                                        <option value="">--Select Loading--</option>
                                        <option value="1" <?= $feeling['FeelingLoadingID'] == '1' ? 'selected':'' ?> >Positive</option>
                                        <option value="2" <?= $feeling['FeelingLoadingID'] == '2' ? 'selected':'' ?> >Neutral</option>
                                        <option value="3" <?= $feeling['FeelingLoadingID'] == '3' ? 'selected':'' ?> >Negative</option>
                                    </select>

                                    <!-- Container for displaying any error messages related to the feeling loading selection -->
                                    <div class="error">
                                        <p><?= $error['loading_missing'] ?? '' ?></p>
                                    </div>
                                </div><!-- End of .input-container -->  

                                <!-- Button wrapper for updating the feeling -->
                                <div class="button-wrapper">
                                    <button type="submit" name="update-feeling" class="primary-btn">Update Feeling</button>
                                </div><!-- End of .button-wrapper -->
                            </form>

                        <?php } // End foreach loop for feeling
                    } // End if statement checking for feeling results
                } // End if statement checking if 'id' parameter is set
                ?>
            </div><!-- End of ./content-management-form-wrapper -->
        </div><!-- End of .admin-main-wrapper -->
    </body>
</html>
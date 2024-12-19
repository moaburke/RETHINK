<?php
/**
 * File: edit_location.php
 * Author: Moa Burke
 * Date: 2024-11-07
 * Description: Provides a form for admins to edit location items in the system.
 * 
 * This page allows an admin to edit the details of a specific "location" record in the system.
 * Admins can update the name and icon associated with each location item.
 * The page checks for an active admin session, includes necessary configuration and layout files,
 * and validates the form data before updating the location item in the database.
*/
session_start(); // Start the session

// Define a constant for the base path
define('BASE_PATH', '../../../server-side/shared/');

include(BASE_PATH . "connections.php"); // Include databade connection file
include(BASE_PATH . "check_login.php"); // Include login check script to verify user authentication before accessing this page.
include(BASE_PATH . "timezone.php"); // Include the timezone configuration
include(BASE_PATH . "header/admin_layout.php"); // Include the admin header layout file

// Define constants for location-related icons
define('ICON1', 'fa-solid fa-house-chimney');
define('ICON2', 'fa-solid fa-briefcase');
define('ICON3', 'fa-solid fa-graduation-cap');
define('ICON4', 'fa-solid fa-van-shuttle');
define('ICON5', 'fa-solid fa-bag-shopping');
define('ICON6', 'fa-solid fa-tree-city');
define('ICON7', 'fa-solid fa-utensils');
define('ICON8', 'fa-solid fa-dumbbell');

// Check if the admin is logged in 
$adminData = check_login($con);
// Retrieve the UserID of the logged-in admin
$adminID = $adminData['UserID']; 

// Get today's date in Y-m-d format
$date = date("Y-m-d");

// Check if the 'update-location' form has been submitted
if (isset($_POST['update-location'])) {
    // Check if the location name is provided
    if ($_POST['locationName'] == "") {
        // If location name is empty, set an error message with the key 'name'
        $error['name_missing'] = "場所名が入力されていません"; // Translation: "Location name has not been entered"
    }

    // Check if the selected icon field is selected
    if ($_POST['selectedIcon'] == "") { 
        // If selected icon is empty, set an error message with the key 'icon'
        $error['icon_missing'] = "アイコンが選択されていません"; // Translation: "Icon has not been selected"
    }

    // Proceed with the update only if there are no validation errors
    if (empty($error)) {
        // Retrieve the location ID, name, and icon from the POST data
        $locationID = $_POST['locationID'];
        $locationName = $_POST['locationName'];
        $locationIcon = $_POST['selectedIcon'];

        // Construct the SQL query based on whether an icon has been selected
        if (!empty($_POST['selectedIcon'])) {
            // If an icon is selected, update both the LocationName and LocationIcon fields
            $query = "UPDATE locations SET LocationName = '$locationName', LocationIcon = '$locationIcon' WHERE LocationID = '$locationID' ";
        } else {
            // If no icon is selected, only update the LocationName field
            $query = "UPDATE locations SET LocationName = '$locationName' WHERE LocationID = '$locationID' ";
        }

        // Execute the query and store the result in $queryRun to check for success
        $queryRun = mysqli_query($con, $query);

        if ($queryRun) {
            // If successful, set a success message in the session feedbackMessage
            $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>場所が正常に更新されました。</p></div>"; // Translation: "Location updated successfully"
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
                <p class="bread-active">Edit Location</p>
            </div><!-- End of .breadcrumbs -->

            <!-- Wrapper for the entire form section for editing location items -->
            <div class="content-management-form-wrapper">
                <div class="content-management-header">
                    <h3>
                        <!-- Japanese title for the edit location section -->
                        <span class="japanese-title">場所の編集</span>
                        <!-- English title for the edit location section -->
                        <span class="english-title">Edit Location</span>
                    </h3>
                </div><!-- End of .content-management-header -->

                <?php 
                // Check if an 'id' is provided in the URL query parameters
                if (isset($_GET['id'])) {
                    // Retrieve the location ID from the query parameter
                    $locationID = $_GET['id'];

                    // Query to select all columns from the 'location' table for the specified LocationID
                    $locationQuery = "SELECT * FROM locations WHERE LocationID = $locationID";
                    $locationResult = mysqli_query($con, $locationQuery);

                    // Check if there are results from the location query
                    if (mysqli_num_rows($locationResult) > 0) {
                        // Iterate through each location result and create a form for editing
                        foreach ($locationResult as $location){ ?>
                            <form action="" method="POST" enctype="multipart/form-data">
                                <!-- Hidden input to store the location ID -->
                                <input type="hidden" name="locationID" value="<?=$location['LocationID'] ?>">

                                <!-- Container for the location name input field -->
                                <div class="input-container input-name focus">
                                    <label for="">Name</label>
                                    <span>Name</span>

                                    <!-- Input field for the location name with pre-filled value -->
                                    <input type="text" name="locationName" value="<?php echo $location['LocationName'];?>" class="input">
                                    
                                    <!-- Container for displaying any error messages related to the location name input -->
                                    <div class="error">
                                        <p><?= $error['name_missing'] ?? '' ?></p>
                                    </div>
                                </div><!-- End of .input-container -->

                                <!-- Container for the input section with icon selection -->
                                <div class="input-container input-container-icons">
                                    <p>Select Icon</p>

                                    <!-- Wrapper for the icons -->
                                    <div class="icons-wrapper">
                                        <div class="icons">
                                            <!-- Radio button for selecting an icon, hidden by default and pre-checked -->
                                            <input type="radio" id="extra" name="selectedIcon" value='<?php echo $location['LocationIcon']; ?>' hidden checked>
                                            
                                            <!-- Icon option 1 -->
                                            <div id="show1">
                                                <label for="show1"><i class="<?= ICON1 ?>"></i></label>
                                                <input type="radio" id="show1" name="selectedIcon" value='<i class="<?= ICON1 ?>"></i>'>
                                            </div>

                                            <!-- Icon option 2 -->
                                            <div id="show2">
                                                <label for="show2"><i class="<?= ICON2 ?>"></i></label>
                                                <input type="radio" id="show2" name="selectedIcon" value='<i class="<?= ICON2 ?>"></i>' >
                                            </div>

                                            <!-- Icon option 3 -->
                                            <div id="show3">
                                                <label for="show3"><i class="<?= ICON3 ?>"></i></label>
                                                <input type="radio" id="show3" name="selectedIcon" value='<i class="<?= ICON3 ?>"></i>'>
                                            </div>

                                            <!-- Icon option 4 -->
                                            <div id="show4">
                                                <label for="show4"><i class="<?= ICON4 ?>"></i></label>
                                                <input type="radio" id="show4" name="selectedIcon" value='<i class="<?= ICON4 ?>"></i>'>
                                            </div>

                                            <!-- Icon option 5 -->
                                            <div id="show5">
                                                <label for="show5"><i class="<?= ICON5 ?>"></i></label>
                                                <input type="radio" id="show5" name="selectedIcon" value='<i class="<?= ICON5 ?>"></i>'>
                                            </div>

                                            <!-- Icon option 6 -->
                                            <div id="show6">
                                                <label for="show6"><i class="<?= ICON6 ?>"></i></label>
                                                <input type="radio" name="selectedIcon" id="show6" value='<i class="<?= ICON6 ?>"></i>'>
                                            </div>

                                            <!-- Icon option 7 -->
                                            <div id="show7">
                                                <label for="show7"><i class="<?= ICON7 ?>"></i></label>
                                                <input type="radio" name="selectedIcon" id="show7" value='<i class="<?= ICON7 ?>"></i>'>
                                            </div>         
                                            
                                            <!-- Icon option 8 -->
                                            <div id="show8">
                                                <label for="show8"><i class="<?= ICON8 ?>"></i></label>
                                                <input type="radio" name="selectedIcon" id="show8" value='<i class="<?= ICON8 ?>"></i>'>
                                            </div>

                                            <!-- Container for displaying error message related to icon selection -->
                                            <div class="error-icon">
                                                <p><?= $error['icon_missing'] ?? '' ?></p>
                                            </div>
                                        </div><!-- End of .icons -->
                                        
                                        <!-- Button to see all available icons -->
                                        <div id="view-all-icons-button" class="view-all-icons-button">
                                            <p>See All</p>
                                            <i class="fa-solid fa-chevron-right"></i> <!-- Icon indicating expansion or navigation to view more options -->
                                        </div>

                                        <!-- Container to display the location icon if it exists -->
                                        <div id="display-icon">
                                            <?php 
                                            if (!empty($location['LocationIcon'])) {
                                                echo $location['LocationIcon'];
                                            }
                                            ?>
                                        </div>
                                    </div><!-- End of .icons-wrapper -->
                                </div><!-- End of .input-container -->

                                <!-- Button wrapper for updating the location -->
                                <div class="button-wrapper">
                                    <button type="submit" name="update-location" class="primary-btn">Update Location</button>
                                </div><!-- End of .button-wrapper -->

                                <!-- Modal for selecting an icon -->
                                <div id="myModal" class="modal">
                                    <!-- Container for the modal content -->
                                    <div class="modal-content">

                                        <!-- Title section of the modal -->
                                        <div class="modal-title">
                                            <h3>
                                                <span class="japanese-title">アイコンの選択</span> <!-- Japanese title for the modal -->
                                                <span class="english-title">Select Icon</span> <!-- English title for the modal -->
                                            </h3>
                                        </div><!-- End of .modal-title -->
                                        
                                        <!-- Container for displaying icons in the modal -->    
                                        <div class="modal-icons">
                                            <div id="icon1">
                                                <input type="radio" id="iconSelectionButton1" name="selectedIcon" value='<i class="fa-solid fa-house-chimney"></i>'>
                                                <label for="iconSelectionButton1"><i class="fa-solid fa-house-chimney"></i></label>
                                            </div>
                                            <div id="icon2">
                                                <input type="radio" id="iconSelectionButton2" name="selectedIcon" value='<i class="fa-solid fa-briefcase"></i>' >
                                                <label for="iconSelectionButton2"><i class="fa-solid fa-briefcase"></i></label>
                                            </div>
                                            <div id="icon3">
                                                <input type="radio" id="iconSelectionButton3" name="selectedIcon" value='<i class="fa-solid fa-graduation-cap"></i>'>
                                                <label for="iconSelectionButton3"><i class="fa-solid fa-graduation-cap"></i></label>
                                            </div>
                                            <div id="icon4">
                                                <input type="radio" id="iconSelectionButton4" name="selectedIcon" value='<i class="fa-solid fa-van-shuttle"></i>'>
                                                <label for="iconSelectionButton4"><i class="fa-solid fa-van-shuttle"></i></label>
                                            </div>
                                            <div id="icon5">
                                                <input type="radio" id="iconSelectionButton5" name="selectedIcon" value='<i class="fa-solid fa-bag-shopping"></i>'>
                                                <label for="iconSelectionButton5"><i class="fa-solid fa-bag-shopping"></i></label>
                                            </div>
                                            <div id="icon6">
                                                <input type="radio" name="selectedIcon" id="iconSelectionButton6" value='<i class="fa-solid fa-tree-city"></i>'>
                                                <label for="iconSelectionButton6"><i class="fa-solid fa-tree-city"></i></label>
                                            </div>
                                            <div id="icon7">
                                                <input type="radio" name="selectedIcon" id="iconSelectionButton7" value='<i class="fa-solid fa-utensils"></i>'>
                                                <label for="iconSelectionButton7"><i class="fa-solid fa-utensils"></i></label>
                                            </div>                                    
                                            <div id="icon8">
                                                <input type="radio" name="selectedIcon" id="iconSelectionButton8" value='<i class="fa-solid fa-dumbbell"></i>'>
                                                <label for="iconSelectionButton8"><i class="fa-solid fa-dumbbell"></i></label>
                                            </div>
                                            <div id="icon9">
                                                <input type="radio" name="selectedIcon" id="iconSelectionButton9" value='<i class="fa-solid fa-tree"></i>'>
                                                <label for="iconSelectionButton9"><i class="fa-solid fa-tree"></i></label>
                                            </div>
                                            <div id="icon10">
                                                <input type="radio" name="selectedIcon" id="iconSelectionButton10" value='<i class="fa-solid fa-mug-saucer"></i>'>
                                                <label for="iconSelectionButton10"><i class="fa-solid fa-mug-saucer"></i></label>
                                            </div>
                                            <div id="icon11">
                                                <input type="radio" name="selectedIcon" id="iconSelectionButton11" value='<i class="fa-solid fa-umbrella-beach"></i>'>
                                                <label for="iconSelectionButton11"><i class="fa-solid fa-umbrella-beach"></i></label>
                                            </div>
                                            <div id="icon12">
                                                <input type="radio" name="selectedIcon" id="iconSelectionButton12" value='<i class="fa-solid fa-church"></i>'>
                                                <label for="iconSelectionButton12"><i class="fa-solid fa-church"></i></label>
                                            </div>
                                            <div id="icon13">
                                                <input type="radio" name="selectedIcon" id="iconSelectionButton13" value='<i class="fa-solid fa-globe"></i>'>
                                                <label for="iconSelectionButton13"><i class="fa-solid fa-globe"></i></label>
                                            </div>
                                            <div id="icon14">
                                                <input type="radio" name="selectedIcon" id="iconSelectionButton14" value='<i class="fa-solid fa-toilet-paper"></i>'>
                                                <label for="iconSelectionButton14"><i class="fa-solid fa-toilet-paper"></i></label>
                                            </div>
                                            <div id="icon15">
                                                <input type="radio" name="selectedIcon" id="iconSelectionButton15" value='<i class="fa-solid fa-warehouse"></i>'>
                                                <label for="iconSelectionButton15"><i class="fa-solid fa-warehouse"></i></label>
                                            </div>
                                            <div id="icon16">
                                                <input type="radio" name="selectedIcon" id="iconSelectionButton16" value='<i class="fa-solid fa-dungeon"></i>'>
                                                <label for="iconSelectionButton16"><i class="fa-solid fa-dungeon"></i></label>
                                            </div>
                                            <div id="icon17">
                                                <input type="radio" name="selectedIcon" id="iconSelectionButton17" value='<i class="fa-solid fa-couch"></i>'>
                                                <label for="iconSelectionButton17"><i class="fa-solid fa-couch"></i></label>
                                            </div>
                                            <div id="icon18">
                                                <input type="radio" name="selectedIcon" id="iconSelectionButton18" value='<i class="fa-solid fa-map-location-dot"></i>'>
                                                <label for="iconSelectionButton18"><i class="fa-solid fa-map-location-dot"></i></label>
                                            </div>
                                            <div id="icon19">
                                                <input type="radio" name="selectedIcon" id="iconSelectionButton19" value='<i class="fa-solid fa-music"></i>'>
                                                <label for="iconSelectionButton19"><i class="fa-solid fa-music"></i></label>
                                            </div>
                                            <div id="icon20">
                                                <input type="radio" name="selectedIcon" id="iconSelectionButton20" value='<i class="fa-solid fa-store"></i>'>
                                                <label for="iconSelectionButton20"><i class="fa-solid fa-store"></i></label>
                                            </div>
                                            <div id="icon21">
                                                <input type="radio" name="selectedIcon" id="iconSelectionButton21" value='<i class="fa-solid fa-vihara"></i>'>
                                                <label for="iconSelectionButton21"><i class="fa-solid fa-vihara"></i></label>
                                            </div>
                                            <div id="icon22">
                                                <input type="radio" name="selectedIcon" id="iconSelectionButton22" value='<i class="fa-solid fa-torii-gate"></i>'>
                                                <label for="iconSelectionButton22"><i class="fa-solid fa-torii-gate"></i></label>
                                            </div>
                                            <div id="icon23">
                                                <input type="radio" name="selectedIcon" id="iconSelectionButton23" value='<i class="fa-solid fa-signs-post"></i>'>
                                                <label for="iconSelectionButton23"><i class="fa-solid fa-signs-post"></i></label>
                                            </div>
                                            <div id="icon24">
                                                <input type="radio" name="selectedIcon" id="iconSelectionButton24" value='<i class="fa-solid fa-martini-glass-citrus"></i>'>
                                                <label for="iconSelectionButton24"><i class="fa-solid fa-martini-glass-citrus"></i></label>
                                            </div>
                                        </div><!-- End of .modal-icons -->

                                        <!-- Main container for the modal's actions -->
                                        <div class="modal-main">
                                            <!-- Button to cancel the action and close the modal -->
                                            <div class="cancel-button">
                                                Cancel
                                            </div>

                                            <!-- Button to confirm the selection and proceed -->
                                            <div class="select-button">
                                                Select
                                            </div> 
                                        </div>

                                    </div><!-- End of .modal-content -->
                                </div><!-- End of .modal -->

                            </form>

                        <?php } // End foreach loop for location
                    } // End if statement checking for location results
                } // End if statement checking if 'id' parameter is set
                ?>
            </div><!-- End of ./content-management-form-wrapper -->
        </div><!-- End of .admin-main-wrapper -->
    </body>
</html>
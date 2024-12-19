<?php
/**
 * File: edit_activity.php
 * Author: Moa Burke
 * Date: 2024-11-07
 * Description: Provides a form for admins to edit activity items in the system.
 * 
 * This page allows an admin to edit the details of a specific "activity" record in the system.
 * Admins can update the name and icon associated with each activity item.
 * The page checks for an active admin session, includes necessary configuration and layout files,
 * and validates the form data before updating the activity item in the database.
*/
session_start(); // Start the session

// Define a constant for the base path
define('BASE_PATH', '../../../server-side/shared/');

include(BASE_PATH . "connections.php"); // Include databade connection file
include(BASE_PATH . "check_login.php"); // Include login check script to verify user authentication before accessing this page.
include(BASE_PATH . "timezone.php"); // Include the timezone configuration
include(BASE_PATH . "header/admin_layout.php"); // Include the admin header layout file

// Define constants for activity-related icons
define('ICON1', 'fa-solid fa-graduation-cap');
define('ICON2', 'fa-solid fa-briefcase');
define('ICON3', 'fa-solid fa-broom');
define('ICON4', 'fa-solid fa-football');
define('ICON5', 'fa-solid fa-brush');
define('ICON6', 'fa-solid fa-camera-retro');
define('ICON7', 'fa-solid fa-kitchen-set');
define('ICON8', 'fa-solid fa-gamepad');

// Check if the admin is logged in 
$adminData = check_login($con);
// Retrieve the UserID of the logged-in admin
$adminID = $adminData['UserID']; 

// Get today's date in Y-m-d format
$date = date("Y-m-d");

// Check if the 'update-activity' form has been submitted
if (isset($_POST['update-activity'])) {
     // Check if the activity name is provided
    if ($_POST['activityName'] == "") {
        // If activity name is empty, set an error message with the key 'name'
        $error['name_missing'] = "アクティビティ名が入力されていません"; // Translation: "Activity name has not been entered"
    }

    // Check if the activity icon is selected
    if ($_POST['selectedIcon'] == "") {
        // If activity icon is empty, set an error message with the key 'icon'
        $error['icon_missing'] = "アイコンが選択されていません"; // Translation: "Icon has not been selected"
    }

    // Proceed with the update only if there are no validation errors
    if (empty($error)) {
        // Retrieve the activity ID, name, and icon from the POST dat
        $activityID = $_POST['activityID'];
        $activityName = $_POST['activityName'];
        $activityIcon = $_POST['selectedIcon'];
    

        // Construct the SQL query based on whether an icon has been selected
        if (!empty($_POST['selectedIcon'])) {
            // If an icon is selected, update both the ActivityName and ActivityIcon fields
            $query = "UPDATE activities SET ActivityName = '$activityName', ActivityIcon = '$activityIcon' WHERE ActivityID = '$activityID' ";
        } else {
            // If no icon is selected, only update the ActivityName field
            $query = "UPDATE activities SET ActivityName = '$activityName' WHERE ActivityID = '$activityID' ";
        }

        // Execute the query and store the result in $queryRun to check for success
        $queryRun = mysqli_query($con, $query);

        // Check if the query executed successfully
        if ($queryRun) {
            // If successful, set a success message in the session feedbackMessage
            $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>アクティビティがが正常に更新されました。</p></div>"; // Translation: "Activity updated successfully"
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
                <p class="bread-active">Edit Activity</p>
            </div><!-- End of .breadcrumbs -->

            <!-- Wrapper for the entire form section for editing activity items -->
            <div class="content-management-form-wrapper">
                <div class="content-management-header">
                    <h3>
                        <!-- Japanese title for the edit activity section -->
                        <span class="japanese-title">アクティビティの編集</span>
                        <!-- English title for the edit activity section -->
                        <span class="english-title">Edit Activity</span>
                    </h3>
                </div><!-- End of .content-management-header -->

                <?php 
                // Check if an 'id' is provided in the URL query parameters
                if (isset($_GET['id'])) {
                    // Retrieve the activity ID from the query parameter
                    $activityID = $_GET['id'];

                    // Query to select all columns from the 'activities' table for the specified ActivityID
                    $activityQuery = "SELECT * FROM activities WHERE ActivityID = $activityID";
                    $activityResult = mysqli_query($con, $activityQuery);

                    // Check if there are results from the activity query
                    if (mysqli_num_rows($activityResult) > 0) {
                        // Iterate through each activity result and create a form for editing
                        foreach ($activityResult as $activity){ ?>
                            <form action="" method="POST" enctype="multipart/form-data">
                                <!-- Hidden input to store the activity ID -->
                                <input type="hidden" name="activityID" value="<?=$activity['ActivityID'] ?>">

                                <!-- Container for the activity name input field -->
                                <div class="input-container input-name focus">
                                    <label for="">Name</label>
                                    <span>Name</span>

                                    <!-- Input field for the activity name with pre-filled value -->
                                    <input type="text" name="activityName" value="<?php echo $activity['ActivityName'];?>" class="input">
                                    
                                    <!-- Container for displaying any error messages related to the activity name input -->
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
                                            <input type="radio" id="extra" name="selectedIcon" value='<?php echo $activity['ActivityIcon']; ?>' hidden checked>

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

                                        <!-- Container to display the activity icon if it exists -->
                                        <div id="display-icon">
                                            <?php 
                                            if (!empty($activity['ActivityIcon'])) {
                                                echo $activity['ActivityIcon'];
                                            }
                                            ?>
                                        </div>
                                    </div><!-- End of .icon-wrapper -->
                                </div><!-- End of .input-container -->

                                <!-- Button wrapper for updating the activity -->
                                <div class="button-wrapper">
                                    <button type="submit" name="update-activity" class="primary-btn">Update Activity</button>
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
                                            <div id="activity1">
                                                <input type="radio" id="activityIcon1" name="selectedIcon" value='<i class="fa-solid fa-graduation-cap"></i>' >
                                                <label for="activityIcon1"><i class="fa-solid fa-graduation-cap"></i></label>
                                            </div>

                                            <div id="activity2">
                                                <input type="radio" id="activityIcon2" name="selectedIcon" value='<i class="fa-solid fa-briefcase"></i>' >
                                                <label for="activityIcon2"><i class="fa-solid fa-briefcase"></i></label>
                                            </div>

                                            <div id="activity3">
                                                <input type="radio" id="activityIcon3" name="selectedIcon" value='<i class="fa-solid fa-broom"></i>'>
                                                <label for="activityIcon3"><i class="fa-solid fa-broom"></i></label>
                                            </div>

                                            <div id="activity4">
                                                <input type="radio" id="activityIcon4" name="selectedIcon" value='<i class="fa-solid fa-football"></i>'>
                                                <label for="activityIcon4"><i class="fa-solid fa-football"></i></label>
                                            </div>

                                            <div id="activity5">
                                                <input type="radio" id="activityIcon5" name="selectedIcon" value='<i class="fa-solid fa-brush"></i>'>
                                                <label for="activityIcon5"><i class="fa-solid fa-brush"></i></label>
                                            </div>
                                                
                                            <div id="activity6">
                                                <input type="radio" name="selectedIcon" id="activityIcon6" value='<i class="fa-solid fa-camera-retro"></i>'>
                                                <label for="activityIcon6"><i class="fa-solid fa-camera-retro"></i></label>
                                            </div>
                                                
                                            <div id="activity7">
                                                <input type="radio" name="selectedIcon" id="activityIcon7" value='<i class="fa-solid fa-kitchen-set"></i>'>
                                                <label for="activityIcon7"><i class="fa-solid fa-kitchen-set"></i></label>
                                            </div>                                    
                                                
                                            <div id="activity8">
                                                <input type="radio" name="selectedIcon" id="activityIcon8" value='<i class="fa-solid fa-gamepad"></i>'>
                                                <label for="activityIcon8"><i class="fa-solid fa-gamepad"></i></label>
                                            </div>
                                                                                    
                                            <div id="activity9">
                                                <input type="radio" name="selectedIcon" id="activityIcon9" value='<i class="fa-solid fa-couch"></i>'>
                                                <label for="activityIcon9"><i class="fa-solid fa-couch"></i></label>
                                            </div>
                                                                                    
                                            <div id="activity10">
                                                <input type="radio" name="selectedIcon" id="activityIcon10" value='<i class="fa-solid fa-music"></i>'>
                                                <label for="activityIcon10"><i class="fa-solid fa-music"></i></label>
                                            </div>
                                                        
                                            <div id="activity11">
                                                <input type="radio" name="selectedIcon" id="activityIcon11" value='<i class="fa-solid fa-person-hiking"></i>'>
                                                <label for="activityIcon11"><i class="fa-solid fa-person-hiking"></i></label>
                                            </div>
                                                        
                                            <div id="activity12">
                                                <input type="radio" name="selectedIcon" id="activityIcon12" value='<i class="fa-solid fa-book"></i>'>
                                                <label for="activityIcon12"><i class="fa-solid fa-book"></i></label>
                                            </div>
                                                        
                                            <div id="activity13">
                                                <input type="radio" name="selectedIcon" id="activityIcon13" value='<i class="fa-solid fa-tv"></i>'>
                                                <label for="activityIcon13"><i class="fa-solid fa-tv"></i></label>
                                            </div>
                                                        
                                            <div id="activity14">
                                                <input type="radio" name="selectedIcon" id="activityIcon14" value='<i class="fa-solid fa-basket-shopping"></i>'>
                                                <label for="activityIcon14"><i class="fa-solid fa-basket-shopping"></i></label>
                                            </div>
                                                        
                                            <div id="activity15">
                                                <input type="radio" name="selectedIcon" id="activityIcon15" value='<i class="fa-solid fa-champagne-glasses"></i>'>
                                                <label for="activityIcon15"><i class="fa-solid fa-champagne-glasses"></i></label>
                                            </div>
                                                        
                                            <div id="activity16">
                                                <input type="radio" name="selectedIcon" id="activityIcon16" value='<i class="fa-solid fa-heart"></i>'>
                                                <label for="activityIcon16"><i class="fa-solid fa-heart"></i></label>
                                            </div>
                                                        
                                            <div id="activity17">
                                                <input type="radio" name="selectedIcon" id="activityIcon17" value='<i class="fa-solid fa-person-swimming"></i>'>
                                                <label for="activityIcon17"><i class="fa-solid fa-person-swimming"></i></label>
                                            </div>
                                                        
                                            <div id="activity18">
                                                <input type="radio" name="selectedIcon" id="activityIcon18" value='<i class="fa-solid fa-comments"></i>'>
                                                <label for="activityIcon18"><i class="fa-solid fa-comments"></i></label>
                                            </div>
                                                        
                                            <div id="activity19">
                                                <input type="radio" name="selectedIcon" id="activityIcon19" value='<i class="fa-solid fa-shop"></i>'>
                                                <label for="activityIcon19"><i class="fa-solid fa-shop"></i></label>
                                            </div>
                                                        
                                            <div id="activity20">
                                                <input type="radio" name="selectedIcon" id="activityIcon20" value='<i class="fa-solid fa-landmark"></i>'>
                                                <label for="activityIcon20"><i class="fa-solid fa-landmark"></i></label>
                                            </div>

                                            <div id="activity21">
                                                <input type="radio" name="selectedIcon" id="activityIcon21" value='<i class="fa-solid fa-rocket"></i>'>
                                                <label for="activityIcon21"><i class="fa-solid fa-rocket"></i></label>
                                            </div>
                                                        
                                            <div id="activity22">
                                                <input type="radio" name="selectedIcon" id="activityIcon22" value='<i class="fa-solid fa-earth-americas"></i>'>
                                                <label for="activityIcon22"><i class="fa-solid fa-earth-americas"></i></label>
                                            </div>
                                                        
                                            <div id="activity23">
                                                <input type="radio" name="selectedIcon" id="activityIcon23" value='<i class="fa-solid fa-place-of-worship"></i>'>
                                                <label for="activityIcon23"><i class="fa-solid fa-place-of-worship"></i></label>
                                            </div>
                                                        
                                            <div id="activity24">
                                                <input type="radio" name="selectedIcon" id="activityIcon24" value='<i class="fa-solid fa-hospital"></i>'>
                                                <label for="activityIcon24"><i class="fa-solid fa-hospital"></i></label>
                                            </div>
                                                        
                                            <div id="activity25">
                                                <input type="radio" name="selectedIcon" id="activityIcon25" value='<i class="fa-solid fa-church"></i>'>
                                                <label for="activityIcon25"><i class="fa-solid fa-church"></i></label>
                                            </div>
                                                        
                                            <div id="activity26">
                                                <input type="radio" name="selectedIcon" id="activityIcon26" value='<i class="fa-solid fa-hot-tub-person"></i>'>
                                                <label for="activityIcon26"><i class="fa-solid fa-hot-tub-person"></i></label>
                                            </div>
                                                
                                            <div id="activity27">
                                                <input type="radio" name="selectedIcon" id="activityIcon27" value='<i class="fa-solid fa-tree-city"></i>'>
                                                <label for="activityIcon27"><i class="fa-solid fa-tree-city"></i></label>
                                            </div>

                                            <div id="activity28">
                                                <input type="radio" name="selectedIcon" id="activityIcon28" value='<i class="fa-solid fa-torii-gate"></i>'>
                                                <label for="activityIcon28"><i class="fa-solid fa-torii-gate"></i></label>
                                            </div>
                                                        
                                            <div id="activity29">
                                                <input type="radio" name="selectedIcon" id="activityIcon29" value='<i class="fa-solid fa-tents"></i>'>
                                                <label for="activityIcon29"><i class="fa-solid fa-tents"></i></label>
                                            </div>
                                                        
                                            <div id="activity30">
                                                <input type="radio" name="selectedIcon" id="activityIcon30" value='<i class="fa-solid fa-mountain-city"></i>'>
                                                <label for="activityIcon30"><i class="fa-solid fa-mountain-city"></i></label>
                                            </div>
                                                        
                                            <div id="activity31">
                                                <input type="radio" name="selectedIcon" id="activityIcon31" value='<i class="fa-solid fa-campground"></i>'>
                                                <label for="activityIcon31"><i class="fa-solid fa-campground"></i></label>
                                            </div>
                                                        
                                            <div id="activity32">
                                                <input type="radio" name="selectedIcon" id="activityIcon32" value='<i class="fa-solid fa-calendar-days"></i>'>
                                                <label for="activityIcon32"><i class="fa-solid fa-calendar-days"></i></label>
                                            </div>
                                                        
                                            <div id="activity33">
                                                <input type="radio" name="selectedIcon" id="activityIcon33" value='<i class="fa-solid fa-pen"></i>'>
                                                <label for="activityIcon33"><i class="fa-solid fa-pen"></i></label>
                                            </div>
                                                        
                                            <div id="activity34">
                                                <input type="radio" name="selectedIcon" id="activityIcon34" value='<i class="fa-solid fa-calculator"></i>'>
                                                <label for="activityIcon34"><i class="fa-solid fa-calculator"></i></label>
                                            </div>
                                                        
                                            <div id="activity35">
                                                <input type="radio" name="selectedIcon" id="activityIcon35" value='<i class="fa-solid fa-glasses"></i>'>
                                                <label for="activityIcon35"><i class="fa-solid fa-glasses"></i></label>
                                            </div>
                                                        
                                            <div id="activity36">
                                                <input type="radio" name="selectedIcon" id="activityIcon36" value='<i class="fa-solid fa-cake-candles"></i>'>
                                                <label for="activityIcon36"><i class="fa-solid fa-cake-candles"></i></label>
                                            </div>
                                                        
                                            <div id="activity37">
                                                <input type="radio" name="selectedIcon" id="activityIcon37" value='<i class="fa-solid fa-fire"></i>'>
                                                <label for="activityIcon37"><i class="fa-solid fa-fire"></i></label>
                                            </div>
                                                
                                            <div id="activity38">
                                                <input type="radio" name="selectedIcon" id="activityIcon38" value='<i class="fa-solid fa-tree"></i>'>
                                                <label for="activityIcon38"><i class="fa-solid fa-tree"></i></label>
                                            </div>
                                                        
                                            <div id="activity39">
                                                <input type="radio" name="selectedIcon" id="activityIcon39" value='<i class="fa-solid fa-compass"></i>'>
                                                <label for="activityIcon39"><i class="fa-solid fa-compass"></i></label>
                                            </div>
                                                        
                                            <div id="activity40">
                                                <input type="radio" name="selectedIcon" id="activityIcon40" value='<i class="fa-solid fa-binoculars"></i>'>
                                                <label for="activityIcon40"><i class="fa-solid fa-binoculars"></i></label>
                                            </div>
                                                        
                                            <div id="activity41">
                                                <input type="radio" name="selectedIcon" id="activityIcon41" value='<i class="fa-solid fa-signs-post"></i>'>
                                                <label for="activityIcon41"><i class="fa-solid fa-signs-post"></i></label>
                                            </div>
                                                        
                                            <div id="activity42">
                                                <input type="radio" name="selectedIcon" id="activityIcon42" value='<i class="fa-solid fa-person-hiking"></i>'>
                                                <label for="activityIcon42"><i class="fa-solid fa-person-hiking"></i></label>
                                            </div>
                                                        
                                            <div id="activity43">
                                                <input type="radio" name="selectedIcon" id="activityIcon43" value='<i class="fa-solid fa-map-location-dot"></i>'>
                                                <label for="activityIcon43"><i class="fa-solid fa-map-location-dot"></i></label>
                                            </div>
                                                        
                                            <div id="activity44">
                                                <input type="radio" name="selectedIcon" id="activityIcon44" value='<i class="fa-solid fa-bottle-water"></i>'>
                                                <label for="activityIcon44"><i class="fa-solid fa-bottle-water"></i></label>
                                            </div>
                                                        
                                            <div id="activity45">
                                                <input type="radio" name="selectedIcon" id="activityIcon45" value='<i class="fa-solid fa-heart"></i>'>
                                                <label for="activityIcon45"><i class="fa-solid fa-heart"></i></label>
                                            </div>
                                                        
                                            <div id="activity46">
                                                <input type="radio" name="selectedIcon" id="activityIcon46" value='<i class="fa-solid fa-gift"></i>'>
                                                <label for="activityIcon46"><i class="fa-solid fa-gift"></i></label>
                                            </div>
                                                        
                                            <div id="activity47">
                                                <input type="radio" name="selectedIcon" id="activityIcon47" value='<i class="fa-solid fa-handshake"></i>'>
                                                <label for="activityIcon47"><i class="fa-solid fa-handshake"></i></label>
                                            </div>
                                                        
                                            <div id="activity48">
                                                <input type="radio" name="selectedIcon" id="activityIcon48" value='<i class="fa-solid fa-hand-holding-heart"></i>'>
                                                <label for="activityIcon48"><i class="fa-solid fa-hand-holding-heart"></i></label>
                                            </div>
                                                        
                                            <div id="activity49">
                                                <input type="radio" name="selectedIcon" id="activityIcon49" value='<i class="fa-solid fa-leaf"></i>'>
                                                <label for="activityIcon49"><i class="fa-solid fa-leaf"></i></label>
                                            </div>
                                                        
                                            <div id="activity50">
                                                <input type="radio" name="selectedIcon" id="activityIcon50" value='<i class="fa-solid fa-seedling"></i>'>
                                                <label for="activityIcon50"><i class="fa-solid fa-seedling"></i></label>
                                            </div>
                                                        
                                            <div id="activity51">
                                                <input type="radio" name="selectedIcon" id="activityIcon51" value='<i class="fa-solid fa-ribbon"></i>'>
                                                <label for="activityIcon51"><i class="fa-solid fa-ribbon"></i></label>
                                            </div>
                                                        
                                            <div id="activity52">
                                                <input type="radio" name="selectedIcon" id="activityIcon52" value='<i class="fa-solid fa-piggy-bank"></i>'>
                                                <label for="activityIcon53"><i class="fa-solid fa-piggy-bank"></i></label>
                                            </div>
                                                        
                                            <div id="activity53">
                                                <input type="radio" name="selectedIcon" id="activityIcon53" value='<i class="fa-solid fa-parachute-box"></i>'>
                                                <label for="activityIcon53"><i class="fa-solid fa-parachute-box"></i></label>
                                            </div>
                                                        
                                            <div id="activity54">
                                                <input type="radio" name="selectedIcon" id="activityIcon54" value='<i class="fa-solid fa-bath"></i>'>
                                                <label for="activityIcon54"><i class="fa-solid fa-bath"></i></label>
                                            </div>

                                            <div id="activity55">
                                                <input type="radio" name="selectedIcon" id="activityIcon55" value='<i class="fa-solid fa-gamepad"></i>'>
                                                <label for="activityIcon55"><i class="fa-solid fa-gamepad"></i></label>
                                            </div>
                                                        
                                            <div id="activity56">
                                                <input type="radio" name="selectedIcon" id="activityIcon56" value='<i class="fa-solid fa-robot"></i>'>
                                                <label for="activityIcon56"><i class="fa-solid fa-robot"></i></label>
                                            </div>
                                                        
                                            <div id="activity57">
                                                <input type="radio" name="selectedIcon" id="activityIcon57" value='<i class="fa-solid fa-puzzle-piece"></i>'>
                                                <label for="activityIcon57"><i class="fa-solid fa-puzzle-piece"></i></label>
                                            </div>
                                                        
                                            <div id="activity58">
                                                <input type="radio" name="selectedIcon" id="activityIcon58" value='<i class="fa-solid fa-cookie-bite"></i>'>
                                                <label for="activityIcon58"><i class="fa-solid fa-cookie-bite"></i></label>
                                            </div>
                                                        
                                            <div id="activity59">
                                                <input type="radio" name="selectedIcon" id="activityIcon59" value='<i class="fa-solid fa-snowman"></i>'>
                                                <label for="activityIcon59"><i class="fa-solid fa-snowman"></i></label>
                                            </div>
                                                        
                                            <div id="activity60">
                                                <input type="radio" name="selectedIcon" id="activityIcon60" value='<i class="fa-solid fa-baseball-bat-ball"></i>'>
                                                <label for="activityIcon60"><i class="fa-solid fa-baseball-bat-ball"></i></label>
                                            </div>
                                                        
                                            <div id="activity61">
                                                <input type="radio" name="selectedIcon" id="activityIcon61" value='<i class="fa-solid fa-hat-wizard"></i>'>
                                                <label for="activityIcon61"><i class="fa-solid fa-hat-wizard"></i></label>
                                            </div>
                                                        
                                            <div id="activity62">
                                                <input type="radio" name="selectedIcon" id="activityIcon62" value='<i class="fa-solid fa-hat-cowboy-side"></i>'>
                                                <label for="activityIcon62"><i class="fa-solid fa-hat-cowboy-side"></i></label>
                                            </div>
                                                        
                                            <div id="activity63">
                                                <input type="radio" name="selectedIcon" id="activityIcon63" value='<i class="fa-solid fa-keyboard"></i>'>
                                                <label for="activityIcon63"><i class="fa-solid fa-keyboard"></i></label>
                                            </div>
                                                        
                                            <div id="activity64">
                                                <input type="radio" name="selectedIcon" id="activityIcon64" value='<i class="fa-solid fa-poo"></i>'>
                                                <label for="activityIcon64"><i class="fa-solid fa-poo"></i></label>
                                            </div>
                                                        
                                            <div id="activity65">
                                                <input type="radio" name="selectedIcon" id="activityIcon65" value='<i class="fa-solid fa-comments"></i>'>
                                                <label for="activityIcon65"><i class="fa-solid fa-comments"></i></label>
                                            </div>
                                                        
                                            <div id="activity66">
                                                <input type="radio" name="selectedIcon" id="activityIcon66" value='<i class="fa-solid fa-paper-plane"></i>'>
                                                <label for="activityIcon66"><i class="fa-solid fa-paper-plane"></i></label>
                                            </div>
                                                        
                                            <div id="activity67">
                                                <input type="radio" name="selectedIcon" id="activityIcon67" value='<i class="fa-solid fa-microphone"></i>'>
                                                <label for="activityIcon67"><i class="fa-solid fa-microphone"></i></label>
                                            </div>
                                                        
                                            <div id="activity68">
                                                <input type="radio" name="selectedIcon" id="activityIcon68" value='<i class="fa-solid fa-mobile-screen-button"></i>'>
                                                <label for="activityIcon60"><i class="fa-solid fa-mobile-screen-button"></i></label>
                                            </div>
                                                        
                                            <div id="activity69">
                                                <input type="radio" name="selectedIcon" id="activityIcon69" value='<i class="fa-solid fa-baseball-bat-ball"></i>'>
                                                <label for="activityIcon69"><i class="fa-solid fa-baseball-bat-ball"></i></label>
                                            </div>
                                                        
                                            <div id="activity70">
                                                <input type="radio" name="selectedIcon" id="activityIcon70" value='<i class="fa-solid fa-ghost"></i>'>
                                                <label for="activityIcon70"><i class="fa-solid fa-ghost"></i></label>
                                            </div>
                                                        
                                            <div id="activity71">
                                                <input type="radio" name="selectedIcon" id="activityIcon71" value='<i class="fa-solid fa-brush"></i>'>
                                                <label for="activityIcon71"><i class="fa-solid fa-brush"></i></label>
                                            </div>
                                                        
                                            <div id="activity72">
                                                <input type="radio" name="selectedIcon" id="activityIcon72" value='<i class="fa-solid fa-paint-roller"></i>'>
                                                <label for="activityIcon72"><i class="fa-solid fa-paint-roller"></i></label>
                                            </div>
                                                
                                            <div id="activity73">
                                                <input type="radio" name="selectedIcon" id="activityIcon73" value='<i class="fa-solid fa-hammer"></i>'>
                                                <label for="activityIcon73"><i class="fa-solid fa-hammer"></i></label>
                                            </div>
                                                        
                                            <div id="activity74">
                                                <input type="radio" name="selectedIcon" id="activityIcon74" value='<i class="fa-solid fa-eye"></i>'>
                                                <label for="activityIcon74"><i class="fa-solid fa-eye"></i></label>
                                            </div>
                                                        
                                            <div id="activity75">
                                                <input type="radio" name="selectedIcon" id="activityIcon75" value='<i class="fa-solid fa-camera-retro"></i>'>
                                                <label for="activityIcon75"><i class="fa-solid fa-camera-retro"></i></label>
                                            </div>
                                                        
                                            <div id="activity76">
                                                <input type="radio" name="selectedIcon" id="activityIcon76" value='<i class="fa-solid fa-headphones"></i>'>
                                                <label for="activityIcon76"><i class="fa-solid fa-headphones"></i></label>
                                            </div>
                                                        
                                            <div id="activity77">
                                                <input type="radio" name="selectedIcon" id="activityIcon77" value='<i class="fa-solid fa-print"></i>'>
                                                <label for="activityIcon77"><i class="fa-solid fa-print"></i></label>
                                            </div>
                                                
                                            <div id="activity78">
                                                <input type="radio" name="selectedIcon" id="activityIcon78" value='<i class="fa-solid fa-computer-mouse"></i>'>
                                                <label for="activityIcon78"><i class="fa-solid fa-computer-mouse"></i></label>
                                            </div>
                                                        
                                            <div id="activity79">
                                                <input type="radio" name="selectedIcon" id="activityIcon79" value='<i class="fa-solid fa-tv"></i>'>
                                                <label for="activityIcon79"><i class="fa-solid fa-tv"></i></label>
                                            </div>
                                                        
                                            <div id="activity80">
                                                <input type="radio" name="selectedIcon" id="activityIcon80" value='<i class="fa-solid fa-graduation-cap"></i>'>
                                                <label for="activityIcon80"><i class="fa-solid fa-graduation-cap"></i></label>
                                            </div>
                                                        
                                            <div id="activity81">
                                                <input type="radio" name="selectedIcon" id="activityIcon81" value='<i class="fa-solid fa-user-graduate"></i>'>
                                                <label for="activityIcon81"><i class="fa-solid fa-user-graduate"></i></label>
                                            </div>
                                                
                                            <div id="activity82">
                                                <input type="radio" name="selectedIcon" id="activityIcon82" value='<i class="fa-brands fa-youtube"></i>'>
                                                <label for="activityIcon82"><i class="fa-brands fa-youtube"></i></label>
                                            </div>
                                                        
                                            <div id="activity83">
                                                <input type="radio" name="selectedIcon" id="activityIcon83" value='<i class="fa-solid fa-video"></i>'>
                                                <label for="activityIcon83"><i class="fa-solid fa-video"></i></label>
                                            </div>
                                                
                                            <div id="activity84">
                                                <input type="radio" name="selectedIcon" id="activityIcon84" value='<i class="fa-solid fa-mug-hot"></i>'>
                                                <label for="activityIcon84"><i class="fa-solid fa-mug-hot"></i></label>
                                            </div>
                                                        
                                            <div id="activity85">
                                                <input type="radio" name="selectedIcon" id="activityIcon85" value='<i class="fa-solid fa-martini-glass-citrus"></i>'>
                                                <label for="activityIcon85"><i class="fa-solid fa-martini-glass-citrus"></i></label>
                                            </div>
                                                
                                            <div id="activity86">
                                                <input type="radio" name="selectedIcon" id="activityIcon86" value='<i class="fa-solid fa-ice-cream"></i>'>
                                                <label for="activityIcon86"><i class="fa-solid fa-ice-cream"></i></label>
                                            </div>
                                                        
                                            <div id="activity87">
                                                <input type="radio" name="selectedIcon" id="activityIcon87" value='<i class="fa-solid fa-dice"></i>'>
                                                <label for="activityIcon87"><i class="fa-solid fa-dice"></i></label>
                                            </div>
                                                
                                            <div id="activity88">
                                                <input type="radio" name="selectedIcon" id="activityIcon88" value='<i class="fa-regular fa-chess-pawn"></i>'>
                                                <label for="activityIcon88"><i class="fa-regular fa-chess-pawn"></i></label>
                                            </div>
                                                
                                            <div id="activity89">
                                                <input type="radio" name="selectedIcon" id="activityIcon89" value='<i class="fa-solid fa-hand-peace"></i>'>
                                                <label for="activityIcon89"><i class="fa-solid fa-hand-peace"></i></label>
                                            </div>
                                                
                                            <div id="activity90">
                                                <input type="radio" name="selectedIcon" id="activityIcon90" value='<i class="fa-solid fa-utensils"></i>'>
                                                <label for="activityIcon90"><i class="fa-solid fa-utensils"></i></label>
                                            </div>

                                            <div id="activity92">
                                                <input type="radio" id="activityIcon92" name="selectedIcon" value='<i class="fa-solid fa-fish-fins"></i>' >
                                                <label for="activityIcon92"><i class="fa-solid fa-fish-fins"></i></label>
                                            </div>

                                            <div id="activity93">
                                                <input type="radio" id="activityIcon93" name="selectedIcon" value='<i class="fa-solid fa-dog"></i>'>
                                                <label for="activityIcon93"><i class="fa-solid fa-dog"></i></label>
                                            </div>

                                            <div id="activity94">
                                                <input type="radio" id="activityIcon94" name="selectedIcon" value='<i class="fa-solid fa-cat"></i>'>
                                                <label for="activityIcon94"><i class="fa-solid fa-cat"></i></label>
                                            </div>

                                            <div id="activity95">
                                                <input type="radio" id="activityIcon95" name="selectedIcon" value='<i class="fa-solid fa-spider"></i>'>
                                                <label for="activityIcon95"><i class="fa-solid fa-spider"></i></label>
                                            </div>
                                                
                                            <div id="activity96">
                                                <input type="radio" name="selectedIcon" id="activityIcon96" value='<i class="fa-solid fa-horse"></i>'>
                                                <label for="activityIcon96"><i class="fa-solid fa-horse"></i></label>
                                            </div>
                                                
                                            <div id="activity97">
                                                <input type="radio" name="selectedIcon" id="activityIcon97" value='<i class="fa-solid fa-globe"></i>'>
                                                <label for="activityIcon97"><i class="fa-solid fa-globe"></i></label>
                                            </div>                                    
                                                
                                            <div id="activity98">
                                                <input type="radio" name="selectedIcon" id="activityIcon98" value='<i class="fa-solid fa-moon"></i>'>
                                                <label for="activityIcon98"><i class="fa-solid fa-moon"></i></label>
                                            </div>
                                                                                    
                                            <div id="activity99">
                                                <input type="radio" name="selectedIcon" id="activityIcon99" value='<i class="fa-solid fa-user-astronaut"></i>'>
                                                <label for="activityIcon99"><i class="fa-solid fa-user-astronaut"></i></label>
                                            </div>
                                                                                    
                                            <div id="activity100">
                                                <input type="radio" name="selectedIcon" id="activityIcon100" value='<i class="fa-solid fa-shuttle-space"></i>'>
                                                <label for="activityIcon100"><i class="fa-solid fa-shuttle-space"></i></label>
                                            </div>
                                                        
                                            <div id="activity101">
                                                <input type="radio" name="selectedIcon" id="activityIcon101" value='<i class="fa-solid fa-meteor"></i>'>
                                                <label for="activityIcon101"><i class="fa-solid fa-meteor"></i></label>
                                            </div>
                                                        
                                            <div id="activity102">
                                                <input type="radio" name="selectedIcon" id="activityIcon102" value='<i class="fa-solid fa-motorcycle"></i>'>
                                                <label for="activityIcon102"><i class="fa-solid fa-motorcycle"></i></label>
                                            </div>
                                                        
                                            <div id="activity103">
                                                <input type="radio" name="selectedIcon" id="activityIcon103" value='<i class="fa-solid fa-truck-monster"></i>'>
                                                <label for="activityIcon103"><i class="fa-solid fa-truck-monster"></i></label>
                                            </div>
                                                        
                                            <div id="activity104">
                                                <input type="radio" name="selectedIcon" id="activityIcon104" value='<i class="fa-solid fa-caravan"></i>'>
                                                <label for="activityIcon104"><i class="fa-solid fa-caravan"></i></label>
                                            </div>
                                                        
                                            <div id="activity105">
                                                <input type="radio" name="selectedIcon" id="activityIcon105" value='<i class="fa-solid fa-car-side"></i>'>
                                                <label for="activityIcon105"><i class="fa-solid fa-car-side"></i></label>
                                            </div>
                                                        
                                            <div id="activity106">
                                                <input type="radio" name="selectedIcon" id="activityIcon106" value='<i class="fa-solid fa-meteor"></i>'>
                                                <label for="activityIcon106"><i class="fa-solid fa-truck-medical"></i></label>
                                            </div>
                                                        
                                            <div id="activity107">
                                                <input type="radio" name="selectedIcon" id="activityIcon107" value='<i class="fa-solid fa-house"></i>'>
                                                <label for="activityIcon107"><i class="fa-solid fa-house"></i></label>
                                            </div>
                                                        
                                            <div id="activity108">
                                                <input type="radio" name="selectedIcon" id="activityIcon108" value='<i class="fa-solid fa-city"></i>'>
                                                <label for="activityIcon108"><i class="fa-solid fa-city"></i></label>
                                            </div>
                                                        
                                            <div id="activity109">
                                                <input type="radio" name="selectedIcon" id="activityIcon109" value='<i class="fa-solid fa-shop"></i>'>
                                                <label for="activityIcon109"><i class="fa-solid fa-shop"></i></label>
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

                        <?php } // End foreach loop for activity
                    } // End if statement checking for activity results
                } // End if statement checking if 'id' parameter is set
                ?>
            </div><!-- End of .content-management-form-wrapperr -->
        </div><!-- End of .admin-main-wrapper -->
    </body>
</html>
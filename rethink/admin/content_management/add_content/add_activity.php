<?php
/**
 * File: add_activitye.php
 * Author: Moa Burke
 * Date: 2024-11-07
 * Description: Provides a form for admins to add new activity items to the system.
 * 
 * This script generates an HTML form that allows administrators to add new activity 
 * items to the system, including the ability to select an icon that symbolizes 
 * the activity. These entries will be accessible to users when registering their 
 * daily mood,  facilitating mood tracking in relation to the activities they engage in 
 * throughout the day.
 * 
 * Functionality:
 * - Displays input fields for the activity item name, and an icon selection.
 * - Validates input to ensure all fields are filled and data is formatted correctly.
 * - Provides error messages for invalid inputs and confirmation messages upon 
 *   successful creation of food entries.
 * - Includes a button to submit the form for adding new activity data.
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

// Check if the 'add_activity' form has been submitted
if (isset($_POST['add_activity'])) {
    // Check if any data has been posted
    if (!empty($_POST)) { 
        // Validate that the activity name is not empty
        if ($_POST['activityName'] == "") {
            // Store an error message if the activity name is not provided
            $error['name_missing'] = "アクティビティ名が入力されていません"; // Translation: "Activity name has not been entered"
        }

        // Validate that the icon is selected
        if (!isset($_POST['selectedIcon']) || $_POST['selectedIcon'] == "") {
            // Store an error message if the icon is not selected
            $error['icon_missing'] = "アイコンが選択されていません"; // Translation: "Icon has not been selected"
        }

        // Proceed with the insertion only if there are no validation errors
        if (empty($error)) {
            // Retrieve the activity name and icon from POST data
            $activityName = $_POST['activityName'];
            $activityIcon = $_POST["selectedIcon"];

            // Construct the SQL query to insert the new activity into the database
            $queryActivity= "INSERT INTO activities (ActivityName, ActivityIcon) VALUES('$activityName', '$activityIcon')";
            // Execute the query
            $queryRunActivity = mysqli_query($con, $queryActivity);

            // Check if the query execution was successful
            if ($queryRunActivity) {
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
                <p class="bread-active">Add Activity</p>
            </div><!-- End of .breadcrumbs -->

            <div class="content-management-form-wrapper">
                <!-- Top section for the form title -->
                <div class="content-management-header">
                    <h3>
                        <!-- Japanese title for the activity addition form -->
                        <span class="japanese-title">アクティビティの追加</span>
                        <!-- English title for the activity addition form -->
                        <span class="english-title">Add Activity</span>
                    </h3>
                </div><!-- End of .content-management-header -->

                <!-- Form for submitting new activity data -->
                <form action="" method="POST" enctype="multipart/form-data">

                    <!-- Input container for the activity name -->
                    <div class="input-container input-name
                        <?php 
                        // Check if the activity name has been posted and is not empty
                        if (isset($_POST['activityName'])) {
                            if($_POST['activityName'] != '') {
                                // Add 'focus' class to the container if the input is filled
                                echo 'focus';
                            }
                        } ?>">

                        <label for="">Name</label>
                        <span>Name</span>

                        <!-- Input field for the activity name, retains value after form submission -->
                        <input type="text" name="activityName" class="input" value="<?= hsc($_POST['activityName'] ?? '') ?>">

                        <!-- Container for displaying any error messages related to the name input -->
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
                                <!-- Hidden radio input to retain the selected icon value -->
                                <input type="radio" id="extra" name="selectedIcon" value='
                                    <?php 
                                    // Check if 'selectedIcon' has been submitted
                                    if (isset($_POST['selectedIcon'])) {
                                        // If it's not empty, set the radio button's value to the submitted value
                                        if($_POST['selectedIcon'] != '') {
                                            echo $_POST['selectedIcon'];
                                        }
                                    } ?>' hidden <?php 
                                    // Check if 'selectedIcon' has been submitted
                                    if (isset($_POST['selectedIcon'])) {
                                         // If it's not empty, mark this radio button as checked
                                        if($_POST['selectedIcon'] != '') {
                                            echo 'checked';
                                        }
                                    } ?>>
                                
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

                            <div id="display-icon">
                                <?php 
                                // Ensure the selected icon value is not empty before displaying it
                                if (isset($_POST['selectedIcon'])) {
                                    if ($_POST['selectedIcon'] != '') {
                                        echo $_POST['selectedIcon']; // Output the selected icon
                                    }
                                } 
                                ?>
                            </div>
                        </div><!-- End of .icons-wrapper -->
                    </div><!-- End of .input-container -->

                    <!-- Button wrapper for adding activity -->
                    <div class="button-wrapper">
                        <button type="submit" name="add_activity" class="primary-btn">Add Activity</button>
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
                                    <input type="radio" id="iconSelectionButton1" name="selectedIcon" value='<i class="fa-solid fa-graduation-cap"></i>' >
                                    <label for="iconSelectionButton1"><i class="fa-solid fa-graduation-cap"></i></label>
                                </div>

                                <div id="activity2">
                                    <input type="radio" id="iconSelectionButton2" name="selectedIcon" value='<i class="fa-solid fa-briefcase"></i>' >
                                    <label for="iconSelectionButton2"><i class="fa-solid fa-briefcase"></i></label>
                                </div>

                                <div id="activity3">
                                    <input type="radio" id="iconSelectionButton3" name="selectedIcon" value='<i class="fa-solid fa-broom"></i>'>
                                    <label for="iconSelectionButton3"><i class="fa-solid fa-broom"></i></label>
                                </div>

                                <div id="activity4">
                                    <input type="radio" id="iconSelectionButton4" name="selectedIcon" value='<i class="fa-solid fa-football"></i>'>
                                    <label for="iconSelectionButton4"><i class="fa-solid fa-football"></i></label>
                                </div>

                                <div id="activity5">
                                    <input type="radio" id="iconSelectionButton5" name="selectedIcon" value='<i class="fa-solid fa-brush"></i>'>
                                    <label for="iconSelectionButton5"><i class="fa-solid fa-brush"></i></label>
                                </div>
                                    
                                <div id="activity6">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton6" value='<i class="fa-solid fa-camera-retro"></i>'>
                                    <label for="iconSelectionButton6"><i class="fa-solid fa-camera-retro"></i></label>
                                </div>
                                    
                                <div id="activity7">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton7" value='<i class="fa-solid fa-kitchen-set"></i>'>
                                    <label for="iconSelectionButton7"><i class="fa-solid fa-kitchen-set"></i></label>
                                </div>                                    
                                    
                                <div id="activity8">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton8" value='<i class="fa-solid fa-gamepad"></i>'>
                                    <label for="iconSelectionButton8"><i class="fa-solid fa-gamepad"></i></label>
                                </div>
                                                                        
                                <div id="activity9">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton9" value='<i class="fa-solid fa-couch"></i>'>
                                    <label for="iconSelectionButton9"><i class="fa-solid fa-couch"></i></label>
                                </div>
                                                                        
                                <div id="activity10">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton10" value='<i class="fa-solid fa-music"></i>'>
                                    <label for="iconSelectionButton10"><i class="fa-solid fa-music"></i></label>
                                </div>
                                            
                                <div id="activity11">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton11" value='<i class="fa-solid fa-person-hiking"></i>'>
                                    <label for="iconSelectionButton11"><i class="fa-solid fa-person-hiking"></i></label>
                                </div>
                                            
                                <div id="activity12">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton12" value='<i class="fa-solid fa-book"></i>'>
                                    <label for="iconSelectionButton12"><i class="fa-solid fa-book"></i></label>
                                </div>
                                            
                                <div id="activity13">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton13" value='<i class="fa-solid fa-tv"></i>'>
                                    <label for="iconSelectionButton13"><i class="fa-solid fa-tv"></i></label>
                                </div>
                                            
                                <div id="activity14">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton14" value='<i class="fa-solid fa-basket-shopping"></i>'>
                                    <label for="iconSelectionButton14"><i class="fa-solid fa-basket-shopping"></i></label>
                                </div>
                                            
                                <div id="activity15">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton15" value='<i class="fa-solid fa-champagne-glasses"></i>'>
                                    <label for="iconSelectionButton15"><i class="fa-solid fa-champagne-glasses"></i></label>
                                </div>
                                            
                                <div id="activity16">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton16" value='<i class="fa-solid fa-heart"></i>'>
                                    <label for="iconSelectionButton16"><i class="fa-solid fa-heart"></i></label>
                                </div>
                                            
                                <div id="activity17">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton17" value='<i class="fa-solid fa-person-swimming"></i>'>
                                    <label for="iconSelectionButton17"><i class="fa-solid fa-person-swimming"></i></label>
                                </div>
                                            
                                <div id="activity18">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton18" value='<i class="fa-solid fa-comments"></i>'>
                                    <label for="iconSelectionButton18"><i class="fa-solid fa-comments"></i></label>
                                </div>
                                            
                                <div id="activity19">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton19" value='<i class="fa-solid fa-shop"></i>'>
                                    <label for="iconSelectionButton19"><i class="fa-solid fa-shop"></i></label>
                                </div>
                                            
                                <div id="activity20">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton20" value='<i class="fa-solid fa-landmark"></i>'>
                                    <label for="iconSelectionButton20"><i class="fa-solid fa-landmark"></i></label>
                                </div>

                                <div id="activity21">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton21" value='<i class="fa-solid fa-rocket"></i>'>
                                    <label for="iconSelectionButton21"><i class="fa-solid fa-rocket"></i></label>
                                </div>
                                            
                                <div id="activity22">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton22" value='<i class="fa-solid fa-earth-americas"></i>'>
                                    <label for="iconSelectionButton22"><i class="fa-solid fa-earth-americas"></i></label>
                                </div>
                                            
                                <div id="activity23">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton23" value='<i class="fa-solid fa-place-of-worship"></i>'>
                                    <label for="iconSelectionButton23"><i class="fa-solid fa-place-of-worship"></i></label>
                                </div>
                                            
                                <div id="activity24">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton24" value='<i class="fa-solid fa-hospital"></i>'>
                                    <label for="iconSelectionButton24"><i class="fa-solid fa-hospital"></i></label>
                                </div>
                                            
                                <div id="activity25">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton25" value='<i class="fa-solid fa-church"></i>'>
                                    <label for="iconSelectionButton25"><i class="fa-solid fa-church"></i></label>
                                </div>
                                            
                                <div id="activity26">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton26" value='<i class="fa-solid fa-hot-tub-person"></i>'>
                                    <label for="iconSelectionButton26"><i class="fa-solid fa-hot-tub-person"></i></label>
                                </div>
                                    
                                <div id="activity27">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton27" value='<i class="fa-solid fa-tree-city"></i>'>
                                    <label for="iconSelectionButton27"><i class="fa-solid fa-tree-city"></i></label>
                                </div>

                                <div id="activity28">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton28" value='<i class="fa-solid fa-torii-gate"></i>'>
                                    <label for="iconSelectionButton28"><i class="fa-solid fa-torii-gate"></i></label>
                                </div>
                                            
                                <div id="activity29">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton29" value='<i class="fa-solid fa-tents"></i>'>
                                    <label for="iconSelectionButton29"><i class="fa-solid fa-tents"></i></label>
                                </div>
                                            
                                <div id="activity30">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton30" value='<i class="fa-solid fa-mountain-city"></i>'>
                                    <label for="iconSelectionButton30"><i class="fa-solid fa-mountain-city"></i></label>
                                </div>
                                            
                                <div id="activity31">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton31" value='<i class="fa-solid fa-campground"></i>'>
                                    <label for="iconSelectionButton31"><i class="fa-solid fa-campground"></i></label>
                                </div>
                                            
                                <div id="activity32">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton32" value='<i class="fa-solid fa-calendar-days"></i>'>
                                    <label for="iconSelectionButton32"><i class="fa-solid fa-calendar-days"></i></label>
                                </div>
                                            
                                <div id="activity33">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton33" value='<i class="fa-solid fa-pen"></i>'>
                                    <label for="iconSelectionButton33"><i class="fa-solid fa-pen"></i></label>
                                </div>
                                            
                                <div id="activity34">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton34" value='<i class="fa-solid fa-calculator"></i>'>
                                    <label for="iconSelectionButton34"><i class="fa-solid fa-calculator"></i></label>
                                </div>
                                            
                                <div id="activity35">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton35" value='<i class="fa-solid fa-glasses"></i>'>
                                    <label for="iconSelectionButton35"><i class="fa-solid fa-glasses"></i></label>
                                </div>
                                            
                                <div id="activity36">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton36" value='<i class="fa-solid fa-cake-candles"></i>'>
                                    <label for="iconSelectionButton36"><i class="fa-solid fa-cake-candles"></i></label>
                                </div>
                                            
                                <div id="activity37">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton37" value='<i class="fa-solid fa-fire"></i>'>
                                    <label for="iconSelectionButton37"><i class="fa-solid fa-fire"></i></label>
                                </div>
                                    
                                            
                                <div id="activity38">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton38" value='<i class="fa-solid fa-tree"></i>'>
                                    <label for="iconSelectionButton38"><i class="fa-solid fa-tree"></i></label>
                                </div>
                                            
                                <div id="activity39">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton39" value='<i class="fa-solid fa-compass"></i>'>
                                    <label for="iconSelectionButton39"><i class="fa-solid fa-compass"></i></label>
                                </div>
                                            
                                <div id="activity40">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton40" value='<i class="fa-solid fa-binoculars"></i>'>
                                    <label for="iconSelectionButton40"><i class="fa-solid fa-binoculars"></i></label>
                                </div>
                                            
                                <div id="activity41">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton41" value='<i class="fa-solid fa-signs-post"></i>'>
                                    <label for="iconSelectionButton41"><i class="fa-solid fa-signs-post"></i></label>
                                </div>
                                            
                                <div id="activity42">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton42" value='<i class="fa-solid fa-person-hiking"></i>'>
                                    <label for="iconSelectionButton42"><i class="fa-solid fa-person-hiking"></i></label>
                                </div>
                                            
                                <div id="activity43">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton43" value='<i class="fa-solid fa-map-location-dot"></i>'>
                                    <label for="iconSelectionButton43"><i class="fa-solid fa-map-location-dot"></i></label>
                                </div>
                                            
                                <div id="activity44">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton44" value='<i class="fa-solid fa-bottle-water"></i>'>
                                    <label for="iconSelectionButton44"><i class="fa-solid fa-bottle-water"></i></label>
                                </div>
                                            
                                <div id="activity45">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton45" value='<i class="fa-solid fa-heart"></i>'>
                                    <label for="iconSelectionButton45"><i class="fa-solid fa-heart"></i></label>
                                </div>
                                    
                                <div id="activity46">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton46" value='<i class="fa-solid fa-gift"></i>'>
                                    <label for="iconSelectionButton46"><i class="fa-solid fa-gift"></i></label>
                                </div>
                                            
                                <div id="activity47">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton47" value='<i class="fa-solid fa-handshake"></i>'>
                                    <label for="iconSelectionButton47"><i class="fa-solid fa-handshake"></i></label>
                                </div>
                                            
                                <div id="activity48">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton48" value='<i class="fa-solid fa-hand-holding-heart"></i>'>
                                    <label for="iconSelectionButton48"><i class="fa-solid fa-hand-holding-heart"></i></label>
                                </div>
                                            
                                <div id="activity49">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton49" value='<i class="fa-solid fa-leaf"></i>'>
                                    <label for="iconSelectionButton49"><i class="fa-solid fa-leaf"></i></label>
                                </div>
                                            
                                <div id="activity50">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton50" value='<i class="fa-solid fa-seedling"></i>'>
                                    <label for="iconSelectionButton50"><i class="fa-solid fa-seedling"></i></label>
                                </div>
                                            
                                <div id="activity51">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton51" value='<i class="fa-solid fa-ribbon"></i>'>
                                    <label for="iconSelectionButton51"><i class="fa-solid fa-ribbon"></i></label>
                                </div>
                                            
                                <div id="activity52">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton52" value='<i class="fa-solid fa-piggy-bank"></i>'>
                                    <label for="iconSelectionButton53"><i class="fa-solid fa-piggy-bank"></i></label>
                                </div>
                                            
                                <div id="activity53">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton53" value='<i class="fa-solid fa-parachute-box"></i>'>
                                    <label for="iconSelectionButton53"><i class="fa-solid fa-parachute-box"></i></label>
                                </div>
                                            
                                <div id="activity54">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton54" value='<i class="fa-solid fa-bath"></i>'>
                                    <label for="iconSelectionButton54"><i class="fa-solid fa-bath"></i></label>
                                </div>

                                <div id="activity55">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton55" value='<i class="fa-solid fa-gamepad"></i>'>
                                    <label for="iconSelectionButton55"><i class="fa-solid fa-gamepad"></i></label>
                                </div>
                                            
                                <div id="activity56">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton56" value='<i class="fa-solid fa-robot"></i>'>
                                    <label for="iconSelectionButton56"><i class="fa-solid fa-robot"></i></label>
                                </div>
                                            
                                <div id="activity57">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton57" value='<i class="fa-solid fa-puzzle-piece"></i>'>
                                    <label for="iconSelectionButton57"><i class="fa-solid fa-puzzle-piece"></i></label>
                                </div>
                                            
                                <div id="activity58">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton58" value='<i class="fa-solid fa-cookie-bite"></i>'>
                                    <label for="iconSelectionButton58"><i class="fa-solid fa-cookie-bite"></i></label>
                                </div>
                                            
                                <div id="activity59">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton59" value='<i class="fa-solid fa-snowman"></i>'>
                                    <label for="iconSelectionButton59"><i class="fa-solid fa-snowman"></i></label>
                                </div>
                                            
                                <div id="activity60">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton60" value='<i class="fa-solid fa-baseball-bat-ball"></i>'>
                                    <label for="iconSelectionButton60"><i class="fa-solid fa-baseball-bat-ball"></i></label>
                                </div>
                                            
                                <div id="activity61">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton61" value='<i class="fa-solid fa-hat-wizard"></i>'>
                                    <label for="iconSelectionButton61"><i class="fa-solid fa-hat-wizard"></i></label>
                                </div>
                                            
                                <div id="activity62">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton62" value='<i class="fa-solid fa-hat-cowboy-side"></i>'>
                                    <label for="iconSelectionButton62"><i class="fa-solid fa-hat-cowboy-side"></i></label>
                                </div>
                                            
                                <div id="activity63">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton63" value='<i class="fa-solid fa-keyboard"></i>'>
                                    <label for="iconSelectionButton63"><i class="fa-solid fa-keyboard"></i></label>
                                </div>
                                            
                                <div id="activity64">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton64" value='<i class="fa-solid fa-poo"></i>'>
                                    <label for="iconSelectionButton64"><i class="fa-solid fa-poo"></i></label>
                                </div>
                                            
                                <div id="activity65">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton65" value='<i class="fa-solid fa-comments"></i>'>
                                    <label for="iconSelectionButton65"><i class="fa-solid fa-comments"></i></label>
                                </div>
                                            
                                <div id="activity66">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton66" value='<i class="fa-solid fa-paper-plane"></i>'>
                                    <label for="iconSelectionButton66"><i class="fa-solid fa-paper-plane"></i></label>
                                </div>
                                            
                                <div id="activity67">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton67" value='<i class="fa-solid fa-microphone"></i>'>
                                    <label for="iconSelectionButton67"><i class="fa-solid fa-microphone"></i></label>
                                </div>
                                            
                                <div id="activity68">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton68" value='<i class="fa-solid fa-mobile-screen-button"></i>'>
                                    <label for="iconSelectionButton60"><i class="fa-solid fa-mobile-screen-button"></i></label>
                                </div>
                                            
                                <div id="activity69">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton69" value='<i class="fa-solid fa-baseball-bat-ball"></i>'>
                                    <label for="iconSelectionButton69"><i class="fa-solid fa-baseball-bat-ball"></i></label>
                                </div>
                                            
                                <div id="activity70">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton70" value='<i class="fa-solid fa-ghost"></i>'>
                                    <label for="iconSelectionButton70"><i class="fa-solid fa-ghost"></i></label>
                                </div>
                                            
                                <div id="activity71">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton71" value='<i class="fa-solid fa-brush"></i>'>
                                    <label for="iconSelectionButton71"><i class="fa-solid fa-brush"></i></label>
                                </div>
                                            
                                <div id="activity72">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton72" value='<i class="fa-solid fa-paint-roller"></i>'>
                                    <label for="iconSelectionButton72"><i class="fa-solid fa-paint-roller"></i></label>
                                </div>
                                    
                                <div id="activity73">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton73" value='<i class="fa-solid fa-hammer"></i>'>
                                    <label for="iconSelectionButton73"><i class="fa-solid fa-hammer"></i></label>
                                </div>
                                            
                                <div id="activity74">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton74" value='<i class="fa-solid fa-eye"></i>'>
                                    <label for="iconSelectionButton74"><i class="fa-solid fa-eye"></i></label>
                                </div>
                                            
                                <div id="activity75">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton75" value='<i class="fa-solid fa-camera-retro"></i>'>
                                    <label for="iconSelectionButton75"><i class="fa-solid fa-camera-retro"></i></label>
                                </div>
                                            
                                <div id="activity76">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton76" value='<i class="fa-solid fa-headphones"></i>'>
                                    <label for="iconSelectionButton76"><i class="fa-solid fa-headphones"></i></label>
                                </div>
                                            
                                <div id="activity77">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton77" value='<i class="fa-solid fa-print"></i>'>
                                    <label for="iconSelectionButton77"><i class="fa-solid fa-print"></i></label>
                                </div>
                                    
                                <div id="activity78">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton78" value='<i class="fa-solid fa-computer-mouse"></i>'>
                                    <label for="iconSelectionButton78"><i class="fa-solid fa-computer-mouse"></i></label>
                                </div>
                                            
                                <div id="activity79">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton79" value='<i class="fa-solid fa-tv"></i>'>
                                    <label for="iconSelectionButton79"><i class="fa-solid fa-tv"></i></label>
                                </div>
                                            
                                <div id="activity80">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton80" value='<i class="fa-solid fa-graduation-cap"></i>'>
                                    <label for="iconSelectionButton80"><i class="fa-solid fa-graduation-cap"></i></label>
                                </div>
                                            
                                <div id="activity81">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton81" value='<i class="fa-solid fa-user-graduate"></i>'>
                                    <label for="iconSelectionButton81"><i class="fa-solid fa-user-graduate"></i></label>
                                </div>
                                    
                                <div id="activity82">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton82" value='<i class="fa-brands fa-youtube"></i>'>
                                    <label for="iconSelectionButton82"><i class="fa-brands fa-youtube"></i></label>
                                </div>
                                            
                                <div id="activity83">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton83" value='<i class="fa-solid fa-video"></i>'>
                                    <label for="iconSelectionButton83"><i class="fa-solid fa-video"></i></label>
                                </div>
                                            
                                <div id="activity84">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton84" value='<i class="fa-solid fa-mug-hot"></i>'>
                                    <label for="iconSelectionButton84"><i class="fa-solid fa-mug-hot"></i></label>
                                </div>
                                            
                                <div id="activity85">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton85" value='<i class="fa-solid fa-martini-glass-citrus"></i>'>
                                    <label for="iconSelectionButton85"><i class="fa-solid fa-martini-glass-citrus"></i></label>
                                </div>
                                    
                                <div id="activity86">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton86" value='<i class="fa-solid fa-ice-cream"></i>'>
                                    <label for="iconSelectionButton86"><i class="fa-solid fa-ice-cream"></i></label>
                                </div>
                                            
                                <div id="activity87">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton87" value='<i class="fa-solid fa-dice"></i>'>
                                    <label for="iconSelectionButton87"><i class="fa-solid fa-dice"></i></label>
                                </div>
                                    
                                <div id="activity88">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton88" value='<i class="fa-regular fa-chess-pawn"></i>'>
                                    <label for="iconSelectionButton88"><i class="fa-regular fa-chess-pawn"></i></label>
                                </div>
                                    
                                <div id="activity89">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton89" value='<i class="fa-solid fa-hand-peace"></i>'>
                                    <label for="iconSelectionButton89"><i class="fa-solid fa-hand-peace"></i></label>
                                </div>
                                    
                                <div id="activity90">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton90" value='<i class="fa-solid fa-utensils"></i>'>
                                    <label for="iconSelectionButton90"><i class="fa-solid fa-utensils"></i></label>
                                </div>

                                <div id="activity92">
                                    <input type="radio" id="iconSelectionButton92" name="selectedIcon" value='<i class="fa-solid fa-fish-fins"></i>' >
                                    <label for="iconSelectionButton92"><i class="fa-solid fa-fish-fins"></i></label>
                                </div>

                                <div id="activity93">
                                    <input type="radio" id="iconSelectionButton93" name="selectedIcon" value='<i class="fa-solid fa-dog"></i>'>
                                    <label for="iconSelectionButton93"><i class="fa-solid fa-dog"></i></label>
                                </div>

                                <div id="activity94">
                                    <input type="radio" id="iconSelectionButton94" name="selectedIcon" value='<i class="fa-solid fa-cat"></i>'>
                                    <label for="iconSelectionButton94"><i class="fa-solid fa-cat"></i></label>
                                </div>

                                <div id="activity95">
                                    <input type="radio" id="iconSelectionButton95" name="selectedIcon" value='<i class="fa-solid fa-spider"></i>'>
                                    <label for="iconSelectionButton95"><i class="fa-solid fa-spider"></i></label>
                                </div>
                                    
                                <div id="activity96">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton96" value='<i class="fa-solid fa-horse"></i>'>
                                    <label for="iconSelectionButton96"><i class="fa-solid fa-horse"></i></label>
                                </div>
                                    
                                <div id="activity97">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton97" value='<i class="fa-solid fa-globe"></i>'>
                                    <label for="iconSelectionButton97"><i class="fa-solid fa-globe"></i></label>
                                </div>                                    
                                    
                                <div id="activity98">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton98" value='<i class="fa-solid fa-moon"></i>'>
                                    <label for="iconSelectionButton98"><i class="fa-solid fa-moon"></i></label>
                                </div>
                                                                        
                                <div id="activity99">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton99" value='<i class="fa-solid fa-user-astronaut"></i>'>
                                    <label for="iconSelectionButton99"><i class="fa-solid fa-user-astronaut"></i></label>
                                </div>
                                                                        
                                <div id="activity100">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton100" value='<i class="fa-solid fa-shuttle-space"></i>'>
                                    <label for="iconSelectionButton100"><i class="fa-solid fa-shuttle-space"></i></label>
                                </div>
                                            
                                <div id="activity101">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton101" value='<i class="fa-solid fa-meteor"></i>'>
                                    <label for="iconSelectionButton101"><i class="fa-solid fa-meteor"></i></label>
                                </div>
                                            
                                <div id="activity102">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton102" value='<i class="fa-solid fa-motorcycle"></i>'>
                                    <label for="iconSelectionButton102"><i class="fa-solid fa-motorcycle"></i></label>
                                </div>
                                            
                                <div id="activity103">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton103" value='<i class="fa-solid fa-truck-monster"></i>'>
                                    <label for="iconSelectionButton103"><i class="fa-solid fa-truck-monster"></i></label>
                                </div>
                                            
                                <div id="activity104">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton104" value='<i class="fa-solid fa-caravan"></i>'>
                                    <label for="iconSelectionButton104"><i class="fa-solid fa-caravan"></i></label>
                                </div>
                                            
                                <div id="activity105">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton105" value='<i class="fa-solid fa-car-side"></i>'>
                                    <label for="iconSelectionButton105"><i class="fa-solid fa-car-side"></i></label>
                                </div>
                                            
                                <div id="activity106">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton106" value='<i class="fa-solid fa-truck-medical"></i>'>
                                    <label for="iconSelectionButton106"><i class="fa-solid fa-truck-medical"></i></label>
                                </div>
                                            
                                <div id="activity107">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton107" value='<i class="fa-solid fa-house"></i>'>
                                    <label for="iconSelectionButton107"><i class="fa-solid fa-house"></i></label>
                                </div>
                                            
                                <div id="activity108">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton108" value='<i class="fa-solid fa-city"></i>'>
                                    <label for="iconSelectionButton108"><i class="fa-solid fa-city"></i></label>
                                </div>
                                            
                                <div id="activity109">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton109" value='<i class="fa-solid fa-shop"></i>'>
                                    <label for="iconSelectionButton109"><i class="fa-solid fa-shop"></i></label>
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

            </div><!-- End of .content-management-form-wrapper -->
    </body>
</html>
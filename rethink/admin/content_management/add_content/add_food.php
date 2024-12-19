<?php
/**
 * File: add_food.php
 * Author: Moa Burke
 * Date: 2024-11-07
 * Description: Provides a form for admins to add new food items to the system.
 * 
 * This script generates an HTML form that allows administrators to add new food 
 * items to the system, including the ability to select an icon that symbolizes 
 * the food. These entries will be accessible to users when registering their 
 * daily mood, facilitating mood tracking in relation to dietary choices and 
 * their impact on overall well-being.
 * 
 * Functionality:
 * - Displays input fields for the food item name, and an icon selection.
 * - Validates input to ensure all fields are filled and data is formatted correctly.
 * - Provides error messages for invalid inputs and confirmation messages upon 
 *   successful creation of food entries.
 * - Includes a button to submit the form for adding new food data.
*/

session_start(); // Start the session

// Define a constant for the base path
define('BASE_PATH', '../../../server-side/shared/');

include(BASE_PATH . "connections.php"); // Include databade connection file
include(BASE_PATH . "check_login.php"); // Include login check script to verify user authentication before accessing this page.
include(BASE_PATH . "timezone.php"); // Include the timezone configuration
include(BASE_PATH . "header/admin_layout.php"); // Include the admin header layout file

// Define constants for food-related icons
define('ICON1', 'fa-solid fa-kitchen-set');
define('ICON2', 'fa-solid fa-bowl-food');
define('ICON3', 'fa-solid fa-utensils');
define('ICON4', 'fa-solid fa-burger');
define('ICON5', 'fa-solid fa-carrot');
define('ICON6', 'fa-solid fa-cookie-bite');
define('ICON7', 'fa-solid fa-drumstick-bite');
define('ICON8', 'fa-solid fa-egg');

// Check if the admin is logged in 
$adminData = check_login($con);
// Retrieve the UserID of the logged-in admin
$adminID = $adminData['UserID']; 

// Get today's date in Y-m-d format
$date = date("Y-m-d");
    
// Check if the 'add_food' form has been submitted
if(isset($_POST['add_food'])){
    // Check if any data has been posted
    if (!empty($_POST)) { 
        // Validate that the food name is not empty
        if ($_POST['foodName'] == "") {
            // Store an error message if the food name is not provided
            $error['name_missing'] = "食事名が入力されていません"; // Translation: "Food name has not been entered"
        } 

        // Validate that the icon is selected
        if (!isset($_POST['selectedIcon']) || $_POST['selectedIcon'] == "") {
            // Store an error message if the icon is not selected
            $error['icon_missing'] = "アイコンが選択されていません"; // Translation: "Icon has not been selected"
        }

        // Proceed with the insertion only if there are no validation errors
        if (empty($error)) {
            // Retrieve the food name and selected icon from the POST dat
            $foodName = $_POST['foodName'];
            $foodIcon= $_POST["selectedIcon"];

            // Construct the SQL query to insert the new food into the database
            $queryFood = "INSERT INTO foods (FoodName, FoodIcon) VALUES('$foodName', '$foodIcon')";
            // Execute the query
            $queryRunFood = mysqli_query($con, $queryFood);

            // Check if the query execution was successful
            if ($queryRunFood) {
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
                <p class="bread-active">Add Food</p>
            </div><!-- End of.breadcrumbs -->

            <!-- Wrapper for the entire form section for adding food -->
            <div class="content-management-form-wrapper">
                <!-- Top section for the form title -->
                <div class="content-management-header">
                    <h3>
                        <!-- Japanese title for the form -->
                        <span class="japanese-title">食事の追加</span>
                        <!-- English title for the form -->
                        <span class="english-title">Add Food</span>
                    </h3>
                </div><!-- End of.content-management-header -->

                <!-- Form for submitting new food data -->
                <form action="" method="POST" enctype="multipart/form-data">
                    <!-- Input container for the food name -->
                    <div class="input-container input-name 
                        <?php 
                        // Check if the food name has been posted and is not empty
                        if  (isset($_POST['foodName'])) {
                            if($_POST['foodName'] != '') {
                                echo 'focus'; // Add 'focus' class to the container if the input is filled
                            }
                        } ?>">

                        <label for="">Name</label>
                        <span>Name</span>

                        <!-- Input field for the food name, retains value after form submission -->
                        <input type="text" name="foodName" class="input" value="<?= hsc($_POST['foodName'] ?? '') ?>">

                        <!-- Container for displaying any error messages related to the food name input -->
                        <div class="error">
                            <p><?= $error['name_missing'] ?? '' ?></p>
                        </div>
                    </div><!-- End of.input-container -->

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
                            </div><!-- End of.icons -->

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
                        </div><!-- End of.icons-wrapper -->
                    </div><!-- End of.input-container -->

                    <!-- Button wrapper for adding food -->
                    <div class="button-wrapper">
                        <button type="submit" name="add_food" class="primary-btn">Add Food</button>
                    </div><!-- End of.button-wrapper -->

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
                            </div><!-- End of.modal-title -->
                             
                            <!-- Container for displaying icons in the modal -->    
                            <div class="modal-icons">
                                <div id="activity1">
                                    <input type="radio" id="iconSelectionButton1" name="selectedIcon" value='<i class="fa-solid fa-kitchen-set"></i>'>
                                    <label for="iconSelectionButton1"><i class="fa-solid fa-kitchen-set"></i></label>
                                </div>
                                <div id="activity2">
                                    <input type="radio" id="iconSelectionButton2" name="selectedIcon" value='<i class="fa-solid fa-bowl-food"></i>' >
                                    <label for="iconSelectionButton2"><i class="fa-solid fa-bowl-food"></i></label>
                                </div>
                                <div id="activity3">
                                    <input type="radio" id="iconSelectionButton3" name="selectedIcon" value='<i class="fa-solid fa-utensils"></i>'>
                                    <label for="iconSelectionButton3"><i class="fa-solid fa-utensils"></i></label>
                                </div>
                                <div id="activity4">
                                    <input type="radio" id="iconSelectionButton4" name="selectedIcon" value='<i class="fa-solid fa-burger"></i>'>
                                    <label for="iconSelectionButton4"><i class="fa-solid fa-burger"></i></label>
                                </div>
                                <div id="activity5">
                                    <input type="radio" id="iconSelectionButton5" name="selectedIcon" value='<i class="fa-solid fa-carrot"></i>'>
                                    <label for="iconSelectionButton5"><i class="fa-solid fa-carrot"></i></label>
                                </div>
                                <div id="activity6">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton6" value='<i class="fa-solid fa-cookie-bite"></i>'>
                                    <label for="iconSelectionButton6"><i class="fa-solid fa-cookie-bite"></i></label>
                                </div>
                                <div id="activity7">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton7" value='<i class="fa-solid fa-drumstick-bite"></i>'>
                                    <label for="iconSelectionButton7"><i class="fa-solid fa-drumstick-bite"></i></label>
                                </div>                                    
                                <div id="activity8">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton8" value='<i class="fa-solid fa-egg"></i>'>
                                    <label for="iconSelectionButton8"><i class="fa-solid fa-egg"></i></label>
                                </div>
                                <div id="activity9">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton9" value='<i class="fa-solid fa-bacon"></i>'>
                                    <label for="iconSelectionButton9"><i class="fa-solid fa-bacon"></i></label>
                                </div>
                                <div id="activity10">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton10" value='<i class="fa-solid fa-mug-hot"></i>'>
                                    <label for="iconSelectionButton10"><i class="fa-solid fa-mug-hot"></i></label>
                                </div>
                                <div id="activity11">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton11" value='<i class="fa-solid fa-lemon"></i>'>
                                    <label for="iconSelectionButton11"><i class="fa-solid fa-lemon"></i></label>
                                </div>
                                <div id="activity12">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton12" value='<i class="fa-solid fa-fish"></i>'>
                                    <label for="iconSelectionButton12"><i class="fa-solid fa-fish"></i></label>
                                </div>
                                <div id="activity13">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton13" value='<i class="fa-solid fa-wheat-awn"></i>'>
                                    <label for="iconSelectionButton13"><i class="fa-solid fa-wheat-awn"></i></label>
                                </div>
                                <div id="activity14">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton14" value='<i class="fa-solid fa-shrimp"></i>'>
                                    <label for="iconSelectionButton14"><i class="fa-solid fa-shrimp"></i></label>
                                </div>
                                <div id="activity15">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton15" value='<i class="fa-solid fa-pizza-slice"></i>'>
                                    <label for="iconSelectionButton15"><i class="fa-solid fa-pizza-slice"></i></label>
                                </div>
                                <div id="activity16">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton16" value='<i class="fa-solid fa-pepper-hot"></i>'>
                                    <label for="iconSelectionButton16"><i class="fa-solid fa-pepper-hot"></i></label>
                                </div>
                                <div id="activity17">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton17" value='<i class="fa-solid fa-ice-cream"></i>'>
                                    <label for="iconSelectionButton17"><i class="fa-solid fa-ice-cream"></i></label>
                                </div>
                                <div id="activity18">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton18" value='<i class="fa-solid fa-hotdog"></i>'>
                                    <label for="iconSelectionButton18"><i class="fa-solid fa-hotdog"></i></label>
                                </div>
                                <div id="activity19">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton19" value='<i class="fa-solid fa-cake-candles"></i>'>
                                    <label for="iconSelectionButton19"><i class="fa-solid fa-cake-candles"></i></label>
                                </div>
                                <div id="activity20">
                                    <input type="radio" name="selectedIcon" id="iconSelectionButton20" value='<i class="fa-solid fa-bread-slice"></i>'>
                                    <label for="iconSelectionButton20"><i class="fa-solid fa-bread-slice"></i></label>
                                </div>
                            </div><!-- End of.modal-icons -->

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

                        </div><!-- End of.modal-content -->
                    </div><!-- End of.modal -->

                </form>

            </div><!-- End of./content-management-form-wrapper -->
        </div><!-- End of.admin-main-wrapper -->
    </body>
</html>
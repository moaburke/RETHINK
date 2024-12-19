<?php
/**
 * File: edit_goal.php
 * Author: Moa Burke
 * Date: 2024-11-07
 * Description: Provides a form for admins to edit goal items in the system.
 * 
 * This page allows an admin to edit the details of a specific "goal" record in the system.
 * Admins can update the name, goal category type, and the icon associated with each goal.
 * The page checks for an active admin session, includes necessary configuration and layout files,
 * validates the form data (including required fields and proper formats), and updates the goal in the database.
*/
session_start(); // Start the session

// Define a constant for the base path
define('BASE_PATH', '../../../server-side/shared/');

include(BASE_PATH . "connections.php"); // Include databade connection file
include(BASE_PATH . "check_login.php"); // Include login check script to verify user authentication before accessing this page.
include(BASE_PATH . "timezone.php"); // Include the timezone configuration
include(BASE_PATH . "header/admin_layout.php"); // Include the admin header layout file

// Define constants for goal-related icons
define('ICON1', 'fa-solid fa-dumbbell');
define('ICON2', 'fa-solid fa-person-running');
define('ICON3', 'fa-solid fa-martini-glass-citrus');
define('ICON4', 'fa-solid fa-smoking');
define('ICON5', 'fa-solid fa-bed');
define('ICON6', 'fa-solid fa-cookie-bite');
define('ICON7', 'fa-solid fa-spa');
define('ICON8', 'fa-solid fa-mountain-city');

// Check if the admin is logged in 
$adminData = check_login($con);
// Retrieve the UserID of the logged-in admin
$adminID = $adminData['UserID']; 

// Get today's date in Y-m-d format
$date = date("Y-m-d");

// Check if the 'update-goal' form has been submitted
if (isset($_POST['update-goal'])) {
    // Validate that the GoalName field is not empty
    if ($_POST['GoalName'] == "") {
        // If empty, set an error message in the $error array with the key 'GoalName'
        $error['name_missing'] = "ゴール名が入力されていません"; // Translation: "Goal name has not been entered"
    }

    // Validate that the goal category field is not empty
    if ($_POST['goalCategory'] == "") {
        // If empty, set an error message in the $error array with the key 'goalCategory'
        $error['category_missing'] = "カテゴリーが入力されていません"; // Translation: "Category has not been entered"
    }

    // Validate that the selected icon field is not empty
    if ($_POST['selectedIcon'] == "") {
        // If empty, set an error message in the $error array with the key 'icon'
        $error['icon_missing'] = "アイコンが選択されていません"; // Translation: "Icon has not been selected"
    }

    // Proceed with the update only if there are no validation errors
    if (empty($error)) {
        // Retrieve the goal ID, name, category, and icon from the POST data
        $goalID = $_POST['GoalID'];
        $goalName = $_POST['GoalName'];
        $goalCategory = $_POST['goalCategory'];
        $goalIcon = $_POST['selectedIcon'];
        
        // Construct the SQL query based on whether an icon has been selected
        if (!empty($_POST['selectedIcon'])) {
            // If an icon is selected, update GoalName, GoalCategoriesID, and GoalIcon fields
            $query = "UPDATE goals SET GoalName = '$goalName', GoalCategoriesID = '$goalCategory', GoalIcon = '$goalIcon' WHERE GoalID = '$goalID' ";
        } else {
            // If no icon is selected, only update GoalName and GoalCategoriesID fields
            $query = "UPDATE goals SET GoalName = '$goalName', GoalCategoriesID = '$goalCategory' WHERE GoalID = '$goalID' ";
        }

        // Execute the query and store the result in $queryRun to check for success
        $queryRun = mysqli_query($con, $query);

        // Check if the query executed successfully
        if ($queryRun) {
            // If successful, set a success message in the session feedbackMessage
            $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>目標が正常に更新されました。</p></div>"; // Translation: "Goal updated successfully"

        }  else { 
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
                <p class="bread-active">Edit Goal</p>
            </div><!-- End of .breadcrumbs -->

            <!-- Wrapper for the entire form section for editing goal items -->
            <div class="content-management-form-wrapper">
                <div class="content-management-header">
                    <h3>
                        <!-- Japanese title for the edit goal section -->
                        <span class="japanese-title">目標の編集</span>
                        <!-- English title for the edit goal section -->
                        <span class="english-title">Edit Goal</span>
                    </h3>
                </div><!-- End of .content-management-header -->

                <?php 
                // Check if an 'id' is provided in the URL query parameters
                if (isset($_GET['id'])) {
                    // Retrieve the goal ID from the query parameter
                    $goalID = $_GET['id'];

                    // Query to select all columns from the 'goals' table for the specified GoalID
                    $goalQuery = "SELECT * FROM goals WHERE GoalID = $goalID";
                    $goalResult = mysqli_query($con, $goalQuery);

                    // Check if there are results from the goal query
                    if (mysqli_num_rows($goalResult) > 0) {
                        // Iterate through each goal result and create a form for editing
                        foreach ($goalResult as $goal){ ?>
                        <form action="" method="POST" enctype="multipart/form-data">
                            <!-- Hidden input to store the goal ID -->
                            <input type="hidden" name="GoalID" value="<?=$goal['GoalID'] ?>">

                            <!-- Container for the goal name input field -->
                            <div class="input-container input-name focus">
                                <label for="">Name</label>
                                <span>Name</span>

                                <!-- Input field for entering or displaying the name of the goal -->
                                <input type="text" name="GoalName" value="<?php echo $goal['GoalName'];?>" class="input">

                                <!-- Container for displaying any error messages related to the goal name input -->
                                <div class="error">
                                    <p><?= $error['name_missing'] ?? '' ?></p>
                                </div>
                            </div><!-- End of .input-container -->

                            <!-- Container for selecting the goal category -->
                            <div class="input-container dropdown-container focus">
                                <label for="">Goal Category</label>
                                <span>Goal Category</span>

                                <!-- Dropdown list to select a goal category -->
                                <select name="goalCategory" required class="input">
                                    <option value="">-- Select Category --</option>
                                        <?php 
                                        // Query to fetch all available goal categories from the database
                                        $queryGoalCategories = mysqli_query($con, "SELECT * FROM goalcategories");

                                        // Loop to display each goal category as an option in the dropdown
                                        while ($rowGoalCategories = mysqli_fetch_assoc($queryGoalCategories)) {
                                            // Store the current goal category's ID and Japanese name in variables for use in the dropdown options
                                            $goalCategoryID = $rowGoalCategories['GoalCategoriesID'];
                                            $goalCategoryName = $rowGoalCategories['GoalCategoryNameJp'];
                                            ?>

                                            <!-- Option for each goal category, marking as selected if it matches the current goal's category -->
                                            <option value="<?php echo $goalCategoryID;?>" <?php echo $goal['GoalCategoriesID'] == $goalCategoryID ? 'selected':'' ?> >
                                                <?php echo $goalCategoryName;?>

                                            </option>
                                        <?php } ?>
                                </select>

                                <!-- Container for displaying any error messages related to the goal category selection -->
                                <div class="error">
                                    <p><?= $error['category_missing'] ?? '' ?></p>
                                </div>
                            </div><!-- End of .input-container -->
                            
                            <!-- Container for the input section with icon selection -->
                            <div class="input-container input-container-icons">
                                <p>Select Icon</p>

                                <!-- Wrapper for the icons -->
                                <div class="icons-wrapper">
                                    <div class="icons">
                                        <!-- Radio button for selecting an icon, hidden by default and pre-checked -->
                                        <input type="radio" id="extra" name="selectedIcon" value='<?php echo $goal['GoalIcon']; ?>' hidden checked>
                                        
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

                                    <!-- Container to display the goal icon if it exists -->
                                    <div id="display-icon">
                                        <?php 
                                        if (!empty($goal['GoalIcon'])) {
                                            echo $goal['GoalIcon'];
                                        }
                                        ?>
                                    </div>
                                </div><!-- End of .icons-wrapper -->
                            </div><!-- End of .input-container -->

                            <!-- Button wrapper for updating the goal -->
                            <div class="button-wrapper">
                                <button type="submit" name="update-goal" class="primary-btn">Update Goal</button>
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
                                            <input type="radio" id="iconSelectionButton1" name="selectedIcon" value='<i class="fa-solid fa-dumbbell"></i>'>
                                            <label for="iconSelectionButton1"><i class="fa-solid fa-dumbbell"></i></label>
                                        </div>
                                        <div id="icon2">
                                            <input type="radio" id="iconSelectionButton2" name="selectedIcon" value='<i class="fa-solid fa-person-running"></i>' >
                                            <label for="iconSelectionButton2"><i class="fa-solid fa-person-running"></i></label>
                                        </div>
                                        <div id="icon3">
                                            <input type="radio" id="iconSelectionButton3" name="selectedIcon" value='<i class="fa-solid fa-martini-glass-citrus"></i>'>
                                            <label for="iconSelectionButton3"><i class="fa-solid fa-martini-glass-citrus"></i></label>
                                        </div>
                                        <div id="icon4">
                                            <input type="radio" id="iconSelectionButton4" name="selectedIcon" value='<i class="fa-solid fa-smoking"></i>'>
                                            <label for="iconSelectionButton4"><i class="fa-solid fa-smoking"></i></label>
                                        </div>
                                        <div id="icon5">
                                            <input type="radio" id="iconSelectionButton5" name="selectedIcon" value='<i class="fa-solid fa-bed"></i>'>
                                            <label for="iconSelectionButton5"><i class="fa-solid fa-bed"></i></label>
                                        </div>
                                        <div id="icon6">
                                            <input type="radio" name="selectedIcon" id="iconSelectionButton6" value='<i class="fa-solid fa-cookie-bite"></i>'>
                                            <label for="iconSelectionButton6"><i class="fa-solid fa-cookie-bite"></i></label>
                                        </div>
                                        <div id="icon7">
                                            <input type="radio" name="selectedIcon" id="iconSelectionButton7" value='<i class="fa-solid fa-spa"></i>'>
                                            <label for="iconSelectionButton7"><i class="fa-solid fa-spa"></i></label>
                                        </div>                                    
                                        <div id="icon8">
                                            <input type="radio" name="selectedIcon" id="iconSelectionButton8" value='<i class="fa-solid fa-mountain-city"></i>'>
                                            <label for="iconSelectionButton8"><i class="fa-solid fa-mountain-city"></i></label>
                                        </div>
                                        <div id="icon9">
                                            <input type="radio" name="selectedIcon" id="iconSelectionButton9" value='<i class="fa-regular fa-face-laugh-beam"></i>'>
                                            <label for="iconSelectionButton9"><i class="fa-regular fa-face-laugh-beam"></i></label>
                                        </div>
                                        <div id="icon10">
                                            <input type="radio" name="selectedIcon" id="iconSelectionButton10" value='<i class="fa-solid fa-heart"></i>'>
                                            <label for="iconSelectionButton10"><i class="fa-solid fa-heart"></i></label>
                                        </div>
                                        <div id="icon11">
                                            <input type="radio" name="selectedIcon" id="iconSelectionButton11" value='<i class="fa-solid fa-mug-saucer"></i>'>
                                            <label for="iconSelectionButton11"><i class="fa-solid fa-mug-saucer"></i></label>
                                        </div>
                                        <div id="icon12">
                                            <input type="radio" name="selectedIcon" id="iconSelectionButton12" value='<i class="fa-solid fa-lightbulb"></i>'>
                                            <label for="iconSelectionButton12"><i class="fa-solid fa-lightbulb"></i></label>
                                        </div>
                                        <div id="icon13">
                                            <input type="radio" name="selectedIcon" id="iconSelectionButton13" value='<i class="fa-solid fa-hands-praying"></i>'>
                                            <label for="iconSelectionButton13"><i class="fa-solid fa-hands-praying"></i></label>
                                        </div>
                                        <div id="icon14">
                                            <input type="radio" name="selectedIcon" id="iconSelectionButton14" value='<i class="fa-solid fa-book"></i>'>
                                            <label for="iconSelectionButton14"><i class="fa-solid fa-book"></i></label>
                                        </div>
                                        <div id="icon15">
                                            <input type="radio" name="selectedIcon" id="iconSelectionButton15" value='<i class="fa-solid fa-glass-water"></i>'>
                                            <label for="iconSelectionButton15"><i class="fa-solid fa-glass-water"></i></label>
                                        </div>
                                        <div id="icon16">
                                            <input type="radio" name="selectedIcon" id="iconSelectionButton16" value='<i class="fa-solid fa-apple-whole"></i>'>
                                            <label for="iconSelectionButton16"><i class="fa-solid fa-apple-whole"></i></label>
                                        </div>
                                        <div id="icon17">
                                            <input type="radio" name="selectedIcon" id="iconSelectionButton17" value='<i class="fa-solid fa-mobile"></i>'>
                                            <label for="iconSelectionButton17"><i class="fa-solid fa-mobile"></i></label>
                                        </div>
                                        <div id="icon18">
                                            <input type="radio" name="selectedIcon" id="iconSelectionButton18" value='<i class="fa-solid fa-tree"></i>'>
                                            <label for="iconSelectionButton18"><i class="fa-solid fa-tree"></i></label>
                                        </div>
                                        <div id="icon19">
                                            <input type="radio" name="selectedIcon" id="iconSelectionButton19" value='<i class="fa-solid fa-broom"></i>'>
                                            <label for="iconSelectionButton19"><i class="fa-solid fa-broom"></i></label>
                                        </div>
                                        <div id="icon20">
                                            <input type="radio" name="selectedIcon" id="iconSelectionButton20" value='<i class="fa-solid fa-couch"></i>'>
                                            <label for="iconSelectionButton20"><i class="fa-solid fa-couch"></i></label>
                                        </div>
                                        <div id="icon21">
                                            <input type="radio" name="selectedIcon" id="iconSelectionButton21" value='<i class="fa-solid fa-calendar"></i>'>
                                            <label for="iconSelectionButton21"><i class="fa-solid fa-calendar"></i></label>
                                        </div>
                                        <div id="icon22">
                                            <input type="radio" name="selectedIcon" id="iconSelectionButton22" value='<i class="fa-regular fa-credit-card"></i>'>
                                            <label for="iconSelectionButton22"><i class="fa-regular fa-credit-card"></i></label>
                                        </div>
                                        <div id="icon23">
                                            <input type="radio" name="selectedIcon" id="iconSelectionButton23" value='<i class="fa-solid fa-comments"></i>'>
                                            <label for="iconSelectionButton23"><i class="fa-solid fa-comments"></i></label>
                                        </div>
                                        <div id="icon24">
                                            <input type="radio" name="selectedIcon" id="iconSelectionButton24" value='<i class="fa-solid fa-guitar"></i>'>
                                            <label for="iconSelectionButton24"><i class="fa-solid fa-guitar"></i></label>
                                        </div>
                                        <div id="icon25">
                                            <input type="radio" name="selectedIcon" id="iconSelectionButton25" value='<i class="fa-solid fa-bicycle"></i>'>
                                            <label for="iconSelectionButton25"><i class="fa-solid fa-bicycle"></i></label>
                                        </div>
                                        <div id="icon26">
                                            <input type="radio" name="selectedIcon" id="iconSelectionButton26" value='<i class="fa-solid fa-person-walking"></i>'>
                                            <label for="iconSelectionButton26"><i class="fa-solid fa-person-walking"></i></label>
                                        </div>
                                        <div id="icon27">
                                            <input type="radio" name="selectedIcon" id="iconSelectionButton27" value='<i class="fa-solid fa-pizza-slice"></i>'>
                                            <label for="iconSelectionButton27"><i class="fa-solid fa-pizza-slice"></i></label>
                                        </div>
                                        <div id="icon28">
                                            <input type="radio" name="selectedIcon" id="iconSelectionButton28" value='<i class="fa-solid fa-moon"></i>'>
                                            <label for="iconSelectionButton28"><i class="fa-solid fa-moon"></i></label>
                                        </div>
                                        <div id="icon29">
                                            <input type="radio" name="selectedIcon" id="iconSelectionButton29" value='<i class="fa-solid fa-coins"></i>'>
                                            <label for="iconSelectionButton29"><i class="fa-solid fa-coins"></i></label>
                                        </div>
                                        <div id="icon30">
                                            <input type="radio" name="selectedIcon" id="iconSelectionButton30" value='<i class="fa-solid fa-rainbow"></i>'>
                                            <label for="iconSelectionButton30"><i class="fa-solid fa-rainbow"></i></label>
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

                        <?php } // End foreach loop for goal
                    } // End if statement checking for goal results
                } // End if statement checking if 'id' parameter is set
                ?>
            </div><!-- End of ./content-management-form-wrapper -->
        </div><!-- End of .admin-main-wrapper -->
    </body>
</html>
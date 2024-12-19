
<?php
/**
 * Page Name: Set Goals
 * Description: This page allows users to select up to three existing goals to focus on. 
 *      Users can update their selected goals at any time, providing a simple interface for managing personal objectives.
 * Author: Moa Burke
 * Date: 2024-10-29
 *
 * Notes:
 * - Utilizes PHP for server-side processing and MySQL for data retrieval.
 * - Ensures a straightforward user interface for goal selection and updating.
 *
 * Dependencies:
 * - Requires PHP for server-side processing and MySQL database connection.
 * - JavaScript is used for managing user interactions, including goal selection and visual feedback when goals are set or unset.
 * - FontAwesome is utilized for icon representation.
 *
*/
session_start();// Start the session

// Define a constant for the base path
define('BASE_PATH', '../../server-side/shared/');

include(BASE_PATH . "connections.php"); // Include databade connection file
include(BASE_PATH . "check_login.php"); //Include the utiity functions
include(BASE_PATH . "timezone.php"); // Include the timezone configuration
include(BASE_PATH . "header/user_layout.php"); // Include the user header layout file

// Check if the user is logged in and retrieve user data
$userData = check_login($con);
$userID = $userData['UserID']; // Retrieve the UserID FROM the session data 

// Get today's date
$date = date("Y-m-d");

// Retrieve the goals from the database
$queryFetchGoalDetailss = mysqli_query($con,"SELECT * FROM goals");

// Retrieve user goals from the database
$queryUserGoals = mysqli_query($con, "SELECT * FROM usergoals WHERE UserID = $userID"); // Execute the query to fetch goals for the specified user
$resultUserGoals = mysqli_num_rows($queryUserGoals); // Count the number of rows returned from the query

// Check if the request method is POST (i.e., the form has been submitted)
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Check if the 'goal' field is not empty
    if (!empty($_POST['goal-selection'])) {
        // Loop through each goal submitted in the form
        foreach ($_POST['goal-selection'] as $currentGoal) {
            // Prepare a query to check if the goal already exists for the user
            $queryCheckGoalExists = "SELECT * FROM userGoals WHERE UserID = $userID AND GoalID = $currentGoal";
            $resultCheckGoalExists = mysqli_query($con,$queryCheckGoalExists);

            // If the goal does not exist in the database, insert it
            if(mysqli_num_rows($resultCheckGoalExists) <= 0){
                // Prepare the insert query for the new goal
                $queryInsertGoal = "INSERT INTO usergoals (UserID, GoalID) VALUES('$userID', '$currentGoal')";
                mysqli_query($con, $queryInsertGoal); // Execute the insert query
            }          
        }
    }

    // Check if the 'goal' field is not empty
    if (!empty($_POST['goal-selection'])) {
        // Loop through each goal on the current page
        foreach ($_POST['goals_to_delete'] as $currentGoal) {

            // Check if the current goal is not in the submitted goals
            if (!in_array($currentGoal, $_POST['goal-selection'])) {

                // Prepare a query to check if the user has this goal
                $queryCheckUserGoals = "SELECT * FROM usergoals WHERE UserID = $userID AND GoalID = $currentGoal";
                $resultUserGoals = mysqli_query($con, $queryCheckUserGoals); 

                // Get the count of user goals returned by the query
                $userGoalsCount = mysqli_num_rows($resultUserGoals);

                // If the user has one or more goals
                if ($userGoalsCount > 0) {
                    // Fetch the user's goal data
                    $userGoal = mysqli_fetch_assoc($resultUserGoals);
                    // Get the UserGoalID of the fetched goal
                    $userGoalID = $userGoal['UserGoalID'];

                    // Prepare the delete query for tracked goals
                    $queryDeleteFromTrackedGoals = "DELETE FROM trackgoals WHERE UserGoalID = $userGoalID";
                    mysqli_query($con, $queryDeleteFromTrackedGoals); // Execute the delete query
                }

                // Prepare the delete query for the user goal
                $queryDeleteUserGoal = "DELETE FROM userGoals WHERE UserID = $userID AND GoalID = $currentGoal";
                mysqli_query($con, $queryDeleteUserGoal); // Execute the delete query
            }
        }
    }

    // Check if the 'goal' field is empty
    if (empty($_POST['goal-selection'])) {
        // Prepare a query to fetch all user goals for the given user
        $queryUsersGoals = "SELECT * FROM usergoals WHERE UserID = $userID";
        $resultUsersGoals = mysqli_query($con, $queryUsersGoals); 
        
        // Loop through each user goal retrieved
        while ($currentUserGoal = mysqli_fetch_array($resultUsersGoals)) {
            // Get the UserGoalID of the current goal
            $deleteEachUserGoal = $currentUserGoal['UserGoalID'];

            // Prepare the delete query for the user goal
            $deleteGoal = "DELETE FROM usergoals WHERE UserGoalID = $deleteEachUserGoal AND UserID = '$userID'";
            mysqli_query($con, $deleteGoal); // Execute the delete query
        }
    }

    // Redirect the user to the 'display_user_goals.php' page
    header("Location: display_user_goals.php");
    // Terminate the current script to ensure no further code is executed after the redirect
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Including shared head elements from a PHP function -->
        <?php includeHeadAssets(); ?>
    </head>

    <body>
        <!-- Header Section -->
        <header class="sidebar-navigation goals-navigation">
            <?php renderUserNavigation(); // Include the common user header ?>
        </header>
        <!-- Logout and sticky header for logged-in user -->
        <?php renderUserHeaderWithLogout($userData); ?>

        <div class="main-wrapper">
            <h2>Set Goals</h2>

            <!-- Breadcrumbs for navigation -->
            <div class="breadcrumbs">
                <a href="../user_home.php"><p>Top Page</p></a>
                <i class="fa-solid fa-angle-right fa-sm"></i>
                <a href="./display_user_goals.php"><p>Goals Overview</p></a>
                <i class="fa-solid fa-angle-right fa-sm"></i>
                <p class="bread-active">Set Goals</p>
            </div><!--. breadcrumbs -->

            <!-- Form to set user goals -->
            <form action="set_goals.php" method="post">
                <section class="goal-setting-form">
                    <h3>
                        <span class="japanese-title">目標の設定</span> <!-- Japanese title for setting goals -->
                        <span class="english-title">Set Goals</span> <!-- English title for setting goals -->
                    </h3>

                    <!-- Explanation about the maximum number of goals a user can set -->
                    <p class="goal-setting-description">最大３つの目標を設定することができます。</p>

                    <div class="goals-container">
                        <?php 
                        // Query to fetch goal categories from the database
                        $queryFetchGoalDetailsCategory = mysqli_query($con, "SELECT * FROM goalcategories");
                        
                        // Loop through each goal category retrieved from the database
                        while ($currentGoalCategory = mysqli_fetch_assoc($queryFetchGoalDetailsCategory)) {
                            // Get the ID of the current goal category
                            $currentGoalCategoryID = $currentGoalCategory['GoalCategoriesID'];

                            // Get the English name of the goal category
                            $goalCategoryNameEnglish = $currentGoalCategory['GoalCategoryName'];
                            // Get the Japanese name of the goal category
                            $categoryNameJapanese = $currentGoalCategory['GoalCategoryNameJp']; 
                            
                            // Query to fetch all goals from the database
                            $queryFetchAllGoals = mysqli_query($con,"SELECT * FROM goals");
                            // Query to fetch goals that belong to the current category

                            $queryFetchGoalsByCategory = mysqli_query($con,"SELECT * FROM goals WHERE GoalCategoriesID = '$currentGoalCategoryID'");
                            // Count the number of goals that exist in the current category
                            $goalsCountInCategory = mysqli_num_rows($queryFetchGoalsByCategory);
                            ?>

                            <!-- Div element to represent a goal category, using the English name as the class -->
                            <div class="<?php echo $goalCategoryNameEnglish;?>">
                                <?php 
                                // Check if there are goals in the current category
                                if ($goalsCountInCategory > 0) { ?>
                                    <!-- Heading displaying the Japanese name of the goal category -->
                                    <h4>
                                        <span><?php echo $categoryNameJapanese;?></span>
                                    </h4>
                                    
                                    <!-- Container for goals under this category -->
                                    <div class="goal-category">
                                        <?php 
                                        // Loop through each goal in the database
                                        while ($goalData = mysqli_fetch_assoc($queryFetchAllGoals)) {
                                            // Get the ID of the current goal
                                            $currentGoalID = $goalData['GoalID'];

                                            // Prepare a query to fetch details for the current goal
                                            $queryFetchGoalDetails = "SELECT * FROM goals WHERE GoalID = $currentGoalID";
                                            $goalDetailsResult = mysqli_query($con,$queryFetchGoalDetails);

                                            // Fetch the goal details
                                            $goalDetails = mysqli_fetch_assoc($goalDetailsResult);
                                            ?>

                                            <?php
                                            // Check if the current goal's category matches the selected goal category ID
                                            if ($goalDetails['GoalCategoriesID'] == $currentGoalCategoryID) { 
                                                // Prepare a query to check if the goal already exists for the user
                                                $queryCheckGoalExists = "SELECT * FROM userGoals WHERE UserID = $userID AND GoalID = $currentGoalID";
                                                $resultCheckGoalExists = mysqli_query($con,$queryCheckGoalExists); // Execute the query to check for the existence of the goal
                                                ?>

                                                <?php 
                                                // Check if the goal already exists for the user
                                                if (mysqli_num_rows($resultCheckGoalExists) > 0) { ?>
                                                    <div class="goal-item">
                                                        <label for="goal-set-<?php echo $goalDetails['GoalID'];?>">
                                                            <p class="goal-text" id="goal-icon<?php echo $goalDetails['GoalID'];?>"> 
                                                                <?php echo $goalDetails['GoalName'];?> <!-- Display the goal name -->
                                                                <?php echo $goalDetails['GoalIcon'];?> <!-- Display the goal icon -->

                                                                <!-- Checkmark icon indicating the goal is set -->
                                                                <div class="goal-circle goal-set" id="goal-check-circle-<?php echo $goalDetails['GoalID'];?>">
                                                                    <i class="fa-solid fa-check"></i>
                                                                </div>

                                                                <!-- Hidden checkbox for form submission -->
                                                                <input type="checkbox" name="goal-selection[]" value="<?php echo $goalDetails['GoalID'];?>" id="goal-set-<?php echo $goalDetails['GoalID'];?>" checked style="display:none;">
                                                        </label>
                                                    </div><!-- .goal-item -->

                                                <?php } else { ?>

                                                    <!-- If the goal does not exist, display the goal without the checkmark -->
                                                    <div class="goal-item-empty">
                                                        <label for="goal-set-<?php echo $goalDetails['GoalID'];?>">
                                                            <p class="goal-text" id="goal-icon<?php echo $goalDetails['GoalID'];?>"> 
                                                                <?php echo $goalDetails['GoalName'];?> <!-- Display the goal name -->
                                                                <?php echo $goalDetails['GoalIcon'];?> <!-- Display the goal icon -->

                                                                <!-- Empty circle indicating the goal is not set -->
                                                                <div class="goal-circle goal-not-set" id="goal-check-circle-<?php echo $goalDetails['GoalID'];?>"></div>

                                                                <!-- Hidden checkbox for form submission, indicating this goal is not set -->
                                                                <input type="checkbox" name="goal-selection[]" value="<?php echo $goalDetails['GoalID'];?>" id="goal-set-<?php echo $goalDetails['GoalID'];?>" style="display:none;">
                                                        </label>
                                                    </div><!-- .goal-item-empty -->

                                                <?php } ?> 

                                                <!-- Hidden input to track goals currently displayed on the page for deletion -->
                                                <input type="hidden" name="goals_to_delete[]" value="<?php echo $goalDetails['GoalID'];?>" checked>

                                            <?php } ?> <!-- End of if statement checking if the goal's category matches the selected category -->

                                        <?php } ?> <!-- End of the while loop for fetching goals -->
                                    </div><!-- .goal-category-->

                                <?php } ?> <!-- End of the if statement checking for goals in the current category -->
                            </div><!-- . goal categotry name -->
                            
                        <?php } ?> <!-- End of the while loop for fetching goal categories -->

                        <!-- Div container for the button to set goals -->
                        <div class="goals-button-wrapper">
                            <button type="submit" value="set-goals" class=" primary-btn">Set Goals</button>
                        </div><!-- .goals-button-wrapper -->     

                    </div><!-- .goals-container -->

                </section><!-- .goal-setting-form -->
            </form>
        </div><!-- .main-wrapper -->
    </body>

    <script>
        // Initialize the goal-set- variable with the number of user goals
        var totalUserGoals = <?php echo json_encode($resultUserGoals); 

        // Loop through each goal detail to add click event listeners
        while ($queryFetchGoalDetailsClick = mysqli_fetch_assoc($queryFetchGoalDetailss)) {
            $goalID = $queryFetchGoalDetailsClick['GoalID']; ?>     

            // Add click event listener for each goal icon
            document.getElementById("goal-icon<?php echo $goalID;?>").addEventListener("click", () => {
                // Check if the goal is currently marked as set (indicated by the presence of 'goal-check-circle-' class)
                if (document.getElementById("goal-check-circle-<?php echo $goalID;?>").classList.contains('goal-set')) {
                    // Enable the button
                    document.getElementById("goal-set-<?php echo $goalID;?>").disabled = false; // Replace "yourButtonId" with the actual button ID
                    
                    // The goal is currently set; remove the 'set' class to mark it as unset
                    document.getElementById("goal-check-circle-<?php echo $goalID;?>").classList.remove("goal-set");
                    document.getElementById("goal-check-circle-<?php echo $goalID;?>").classList.add("goal-not-set");

                    // Update styles to visually indicate the goal is now unset
                    document.getElementById("goal-icon<?php echo $goalID;?>").style.backgroundColor = "#cdcdcd"; // Gray background
                    document.getElementById("goal-icon<?php echo $goalID;?>").style.border = "2px solid #cdcdcd"; // Gray border
                    document.getElementById("goal-icon<?php echo $goalID;?>").style.color = "#fff"; // White text
                    document.getElementById("goal-check-circle-<?php echo $goalID;?>").innerHTML = ''; // Clear checkmark icon

                    // Decrement the count of total user goals, since one goal has been unset
                    totalUserGoals -= 1
                } else {
                    // If the goal is not set and the user has less than 3 goals set, add it
                    if (totalUserGoals < 3) {
                        // Enable the button
                        document.getElementById("goal-set-<?php echo $goalID;?>").disabled = false; // Replace "yourButtonId" with the actual button ID

                        // Increment the count of total user goals, since a new goal will be set
                        totalUserGoals += 1 
                        
                        // Update the goal icon to reflect it is now set
                        document.getElementById("goal-check-circle-<?php echo $goalID;?>").classList.add("goal-set");
                        document.getElementById("goal-check-circle-<?php echo $goalID;?>").classList.remove("goal-not-set");

                        // Change the visual appearance of the goal icon to indicate it is set
                        document.getElementById("goal-icon<?php echo $goalID;?>").style.backgroundColor = "#40929099"; // Set background color
                        document.getElementById("goal-icon<?php echo $goalID;?>").style.border = "2px solid rgba(64, 146, 144, 0.1)"; // Set border color
                        document.getElementById("goal-icon<?php echo $goalID;?>").style.color = "#fff"; // Set text color to white
                        document.getElementById("goal-check-circle-<?php echo $goalID; ?>").innerHTML = '<i class="fa-solid fa-check"></i>';  // Display a checkmark icon to indicate the goal is set
                    } else {
                        // Optionally, disable the button
                        document.getElementById("goal-set-<?php echo $goalID;?>").disabled = true; // Replace "yourButtonId" with the actual button ID
                    }
                }
            })
        <?php } ?>
    </script>

</html>
<?php
/**
 * Page Name: Goals Display
 * Author: Moa Burke
 * Date: 2024-10-29
 * Description: This page retrieves and displays the goals set by the logged-in user, including details such as goal names, categories, icons, and 
 *      the number of consecutive days each goal has been maintained. It checks if the user has set any goals and retrieves their details from the 
 *      database. If no goals are found, a message prompts the user to set goals to track their progress effectively.
 *
 * Notes:
 * - Utilizes PHP for server-side processing and MySQL for data retrieval.
 * - Features dynamic content that adapts based on the user's goals and their tracking history.
 *
 * Dependencies:
 * - Requires PHP for server-side processing and MySQL database connection.
 * - FontAwesome for icon representation of goals.
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
$userData = check_login($con); // Retrieve user data from the login check
$userID = $userData['UserID'];  // Get the UserID of the logged-in user

// Get today's date
$date = date("Y-m-d");

// Retrieve user goals from the database
$queryUserGoals = mysqli_query($con, "SELECT * FROM usergoals WHERE UserID = $userID"); // Execute the query to fetch goals for the specified user
$resultUserGoals = mysqli_num_rows($queryUserGoals); // Count the number of rows returned from the query

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
            <h2>Goals Overview</h2>

            <!-- Breadcrumbs for navigation -->
            <div class="breadcrumbs">
                <a href="../user_home.php"><p>Top Page</p></a>
                <i class="fa-solid fa-angle-right fa-sm"></i>
                <p class="bread-active">Goals Overview</p>
            </div><!--. breadcrumbs -->

            <section class="user-goals-list-wrapper goal-page">
                <h3>
                    <span class="japanese-title">あなたの目標</span> <!-- Japanese title: My Goals -->
                    <span class="english-title">Your Goals</span> <!-- English title -->
                </h3>

                <?php
                // Check if the user has set any goals
                if ($resultUserGoals > 0) { ?>
                    <div class="goal-card">
                        <?php 
                        // Loop through each user goal retrieved from the database
                        while ($userGoalData = mysqli_fetch_array($queryUserGoals)) {
                            $date = date("Y-m-d"); // Get today's date

                            // Extract goal-related information from the fetched data
                            $goalID = $userGoalData['GoalID']; // Unique identifier for the goal
                            $userGoalID = $userGoalData['UserGoalID']; // Unique identifier for the user's specific goal

                            // Retrieve goal data based on GoalID
                            $queryGoalData = mysqli_query($con, "SELECT * FROM goals WHERE GoalID = $goalID");
                            $resultGoalData = mysqli_fetch_array($queryGoalData);

                            // Extract category ID, icon, and name for the goal
                            $goalCategoryID = $resultGoalData['GoalCategoriesID']; // ID of the goal's category                          
                            $goalIcon = $resultGoalData['GoalIcon']; // Icon representing the goal
                            $goalName = $resultGoalData['GoalName']; // Name of the goal

                            // Query to retrieve goal category information based on the category ID
                            $queryGoalCategory = mysqli_query($con, "SELECT * FROM goalcategories WHERE GoalCategoriesID = $goalCategoryID");
                            $resultGoalCategory = mysqli_fetch_array($queryGoalCategory); 

                            // Query to retrieve all tracking dates for a specific goal up to today
                            $queryTrackingDates = 
                                "SELECT t.UserGoalID, t.Date, u.GoalID, u.UserID 
                                FROM usergoals u 
                                INNER JOIN trackgoals t ON u.UserGoalID = t.UserGoalID
                                WHERE GoalID = $goalID AND UserID = $userID AND Date <= '$date' 
                                ORDER BY Date DESC";

                            $resultTrackingDates = mysqli_query($con, $queryTrackingDates);
                        
                            // Initialize a counter for consecutive days
                            $consecutiveDays = 0;

                            // Loop through the results to count consecutive days
                            while ($dateData = mysqli_fetch_assoc($resultTrackingDates)) {  // Fetch each row of tracking dates
                                $trackingDate = $dateData['Date']; // Get the tracking date from the current row

                                // Check if the current date matches the tracking date
                                if ($date == $trackingDate) {
                                    $consecutiveDays += 1; // Increment the counter for consecutive days if they match
                                } else {
                                    break; // Exit the loop if the current date does not match the tracking date
                                }

                                // Update the date to the previous day for the next iteration
                                $date = date('Y-m-d', strtotime($date .' -1 day'));
                            } ?>

                            <!-- Wrapper for an individual goal that has been set -->
                            <div class="goal-card-content">
                                <h4>
                                    <!-- Display the goal category name, safely outputting it to prevent XSS -->
                                    <?php echo htmlspecialchars($resultGoalCategory['GoalCategoryName']); ?> 
                                </h4>
                                <h3>
                                    <!-- Display the specific goal name, safely outputting it to prevent XSS -->
                                    <?php echo htmlspecialchars($goalName); ?>
                                </h3>
                                <div class="goal-icon">
                                    <!-- Display the icon associated with the goal -->    
                                    <?php echo $goalIcon; ?>
                                </div>
                                <div class="consecutive-days-counter">
                                    <!-- Display the number of consecutive days the goal has been maintained -->
                                    <p><?php echo $consecutiveDays; ?>日の継続</p>
                                </div>
                            </div><!-- .goal-card-content -->

                        <?php } // End of the while loop iterating through tracking dates ?> 

                    </div><!-- .goal-card -->

                    <!-- Wrapper for the button to set goals -->
                    <div class="goals-button-wrapper">
                        <!-- Link to the 'set_goals.php' page, styled as a primary button -->
                        <a href="set_goals.php" class="primary-btn">Set Goals</a>
                    </div>

                <?php } else { // User has not set any goals ?>

                <!-- Wrapper for the message displayed when no goals have been set -->
                <div class="no-goals-message">
                        <!-- Message indicating that the user has not set any goals yet -->
                    <h5>目標はまだ設定していない</h5> <!-- Translation: "No goals have been set yet." -->

                    <!-- Instruction encouraging the user to set goals and track progress -->
                    <p>目標を設定し、達成に向けて努力しながら進捗状況を追跡しよう</p> <!-- Translation: "Set your goals and track your progress as you work towards achieving them." -->

                    <!-- Link to the 'set_goals.php' page for setting goals -->
                    <a href="set_goals.php">Set Goals</a>
                </div><!-- .no-goals-message -->

                <?php } // End of else block for when no goals are set ?>

            </section><!-- .user-goals-list-wrapper -->

        </div><!-- .main-wrapper -->
    </body>
</html>
<?php
/*
 * Page Name: Daily Tracker View
 * Author: Moa Burke
 * Date: 2024-10-28
 * Description: 
 *      This page retrieves and displays daily tracked items for a user, such as activities, companies, locations, food, weather, and completed goals.
 *
 * Notes:
 * - Retrieves data from the database to display various daily entries.
 * - Includes a delete confirmation modal to allow users to confirm deletion of specific tracking data.
 * - PHP variables are used to fetch and display information dynamically.
 *
 * Dependencies:
 * - Requires PHP for server-side processing and MySQL database connection.
 * - JavaScript for handling modal visibility for delete confirmation.
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
$userID = $userData['UserID']; // Retrieve the UserID from the session data 

// Get today's date and check for a specified date
$currentDate = date("Y-m-d");
$date = isset($_GET['date']) ? $_GET['date'] : $currentDate;
$previousDate = date('Y-m-d', strtotime($date . ' -1 day'));
$nextDate = date('Y-m-d', strtotime($date . ' +1 day'));

// Function to get the day of the week (0 for Sunday through 6 for Saturday)
function getWeekday($date) {
    return date('w', strtotime($date)); // Return the numeric representation of the day of the week
}
// Get the day of the week for the specified date
$dayofweek = getWeekday($date); 

// Retrieve tracking ID for the user's daily tracking
$queryGetDailyTracking = "SELECT * FROM dailytracking WHERE UserID = $userID AND DATE = '$date'";
$dailyTrackingResult = mysqli_query($con, $queryGetDailyTracking);
$dailyTrackingData = mysqli_fetch_assoc($dailyTrackingResult);
$dailyTrackingCount = mysqli_num_rows($dailyTrackingResult);

// Check if there are any daily tracking records for the user
if ($dailyTrackingCount > 0) {
    $trackingID = $dailyTrackingData['TrackingID']; // Store the TrackingID

    // Retrieve mood information based on the TrackingID 
    $queryRetrieveMoodByTrackingID = "SELECT * FROM trackmoods WHERE TrackingID = $trackingID";
    $resultMoodByTrackingID = mysqli_query($con, $queryRetrieveMoodByTrackingID);
    $moodDataByTrackingID = mysqli_fetch_assoc($resultMoodByTrackingID);

    if(!empty($moodDataByTrackingID['MoodID'])){
        $moodID = $moodDataByTrackingID['MoodID']; // Store the Mood ID

         // Query to retrieve mood details based on Mood ID
        $queryRetrieveMoodByID = "SELECT * FROM moods WHERE MoodID = $moodID";
        $resultMoodByID = mysqli_query($con, $queryRetrieveMoodByID);
        $moodData = mysqli_fetch_assoc($resultMoodByID);
        
        $mood = $moodData['JapaneseMoodName']; // Japanese name of the mood
        $moodEmoji = $moodData['moodEmojiColor']; // Emoji associated with the mood
        $moodname = $moodData['MoodName']; // Name of the mood
    }

    // Retrieve feelings associated with the daily tracking
    $queryFeelings = 
        "SELECT FeelingName 
        FROM feelings 
        WHERE FeelingID IN (
            SELECT FeelingID 
            FROM trackfeelings 
            WHERE TrackingID IN ( 
                SELECT TrackingID 
                FROM dailytracking 
                WHERE UserID = $userID 
                AND DATE = '$date'
            )
        )";
    $resultFeelings = mysqli_query($con, $queryFeelings);

    // Retrieve activities associated with the daily tracking
    $queryActivities = 
        "SELECT * 
        FROM activities 
        WHERE ActivityID IN (
            SELECT ActivityID 
            FROM trackactivities 
            WHERE TrackingID IN ( 
                SELECT TrackingID 
                FROM dailytracking 
                WHERE UserID = $userID 
                AND DATE = '$date'
            )
        )";
    $resultActivities = mysqli_query($con, $queryActivities);

    // Retrieve company information associated with the daily tracking
    $queryCompany = 
        "SELECT * 
        FROM company 
        WHERE CompanyID IN (
            SELECT CompanyID 
            FROM trackcompany 
            WHERE TrackingID IN ( 
                SELECT TrackingID 
                FROM dailytracking 
                WHERE UserID = $userID 
                AND DATE = '$date'
            )
        )";
    $resultCompany = mysqli_query($con, $queryCompany);

    // Retrieve locations associated with the daily tracking
    $queryLocations = 
        "SELECT * 
        FROM locations 
        WHERE LocationID IN (
            SELECT LocationID 
            FROM tracklocations 
            WHERE TrackingID IN ( 
                SELECT TrackingID 
                FROM dailytracking 
                WHERE UserID = $userID 
                AND DATE = '$date'
            )
        )";
    $resultLocations = mysqli_query($con, $queryLocations);
    
    // Retrieve foods associated with the daily tracking
    $queryFoods = 
        "SELECT * 
        FROM foods 
        WHERE FoodID IN (
            SELECT FoodID 
            FROM trackfoods 
            WHERE TrackingID IN ( 
                SELECT TrackingID 
                FROM dailytracking 
                WHERE UserID = $userID 
                AND DATE = '$date'
            )
        )";
    $resultFood = mysqli_query($con, $queryFoods);

    // Retrieve weather information associated with the daily tracking
    $queryWeather = 
        "SELECT * 
        FROM weather 
        WHERE WeatherID IN (
            SELECT WeatherID 
            FROM trackweather 
            WHERE TrackingID IN ( 
                SELECT TrackingID 
                FROM dailytracking 
                WHERE UserID = $userID 
                AND DATE = '$date'
            )
        )";
    $resultWeather = mysqli_query($con, $queryWeather);

    // Retrieve sleep time associated with the daily tracking
    $queryDeleteSleepTime = "SELECT * FROM tracksleeptime WHERE TrackingID = $trackingID";
    $resultSleeptime = mysqli_query($con, $queryDeleteSleepTime);
    $getSleeptime = mysqli_fetch_assoc($resultSleeptime);
    // Check if the retrieved sleep time is not empty
    if(!empty($getSleeptime['sleepTime'])){
        $sleepTime = ($getSleeptime['sleepTime']) / 60; // Convert sleep time FROM seconds to minutes
    }
    
    // Retrieve completed goals for the user on the specified date
    $queryCompletedGoals = 
        "SELECT * 
        FROM goals 
        WHERE GoalID IN (
            SELECT GoalID 
            FROM usergoals 
            WHERE UserID = $userID 
            AND UserGoalID IN (
                SELECT UserGoalID 
                FROM trackgoals WHERE DATE = '$date'
            )
        )";
    $resultCompletedGoals = mysqli_query($con, $queryCompletedGoals);
    $completedGoalsCount = mysqli_num_rows($resultCompletedGoals);

    // Retrieve memo associated with the daily tracking
    $queryMemo = "SELECT * FROM memos WHERE TrackingID = $trackingID";
    $resultMemo = mysqli_query($con, $queryMemo);
    $memo = mysqli_fetch_assoc($resultMemo);
}

// Check if a delete request has been made (e.g., from a form submission or URL parameter)
if (isset($_REQUEST['delete'])) {
    // Retrieve the tracking record for the logged-in user for today’s date
    $queryFetchUserTracking = "SELECT * FROM dailytracking WHERE UserID = $userID AND DATE = '$date'";
    $resultUserTracking = mysqli_query($con,$queryFetchUserTracking);
    $userTrackingRecord = mysqli_fetch_assoc($resultUserTracking);
    
    // Check if the user has an existing tracking record for today
    if(mysqli_num_rows($resultUserTracking) > 0){
        $TrackingID = $userTrackingRecord['TrackingID']; // Get the TrackingID for deletion
        
        // Delete associated mood entries from trackmoods table
        $queryDeleteMoods = "DELETE FROM trackmoods WHERE TrackingID = $TrackingID ";
        mysqli_query($con, $queryDeleteMoods);
        
        // Delete associated feelings entries from trackfeelings table
        $queryDeleteFeelings = "DELETE FROM trackfeelings WHERE TrackingID = $TrackingID ";
        mysqli_query($con, $queryDeleteFeelings);
        
        // Delete associated activities entries from trackactivities table
        $queryDeleteActivities = "DELETE FROM trackactivities WHERE TrackingID = $TrackingID ";
        mysqli_query($con, $queryDeleteActivities);
        
        // Delete associated company entries from trackcompany table
        $queryDeleteCompany = "DELETE FROM trackcompany WHERE TrackingID = $TrackingID ";
        mysqli_query($con, $queryDeleteCompany);
        
        // Delete associated location entries from tracklocations table
        $queryDeleteLocations = "DELETE FROM tracklocations WHERE TrackingID = $TrackingID ";
        mysqli_query($con, $queryDeleteLocations);
        
        // Delete associated food entries from trackfoods table
        $queryDeleteFoods = "DELETE FROM trackfoods WHERE TrackingID = $TrackingID ";
        mysqli_query($con, $queryDeleteFoods);
        
        // Delete associated weather entries from trackweather table
        $queryDeleteWeather = "DELETE FROM trackweather WHERE TrackingID = $TrackingID ";
        mysqli_query($con, $queryDeleteWeather);

        // Delete associated memo entries from memos table
        $queryDeleteMemos = "DELETE FROM memos WHERE TrackingID = $TrackingID ";
        mysqli_query($con, $queryDeleteMemos);

        // Delete associated sleep time entries from tracksleeptime table
        $queryDeleteSleepTime = "DELETE FROM tracksleeptime WHERE TrackingID = $TrackingID";
        mysqli_query($con, $queryDeleteSleepTime);

        // Retrieve user goals associated with the user ID
        $queryUsersGoals = "SELECT UserGoalID FROM usergoals WHERE UserID = $userID";
        $resultUserGoals = mysqli_query($con, $queryUsersGoals); 

        // Loop through each user goal and delete associated tracking entries
        while ($eachUserGoal = mysqli_fetch_array($resultUserGoals)){
            $userGoalID = $eachUserGoal['UserGoalID']; // Get the UserGoalID for deletion

            // Delete tracking entries for the specific user goal and date
            $queryDeleteGoalTracking = "DELETE FROM trackgoals WHERE UserGoalID = $userGoalID AND DATE = '$date'";
            mysqli_query($con, $queryDeleteGoalTracking); 
        }
        
        // Delete the user's daily tracking entry for today
        $queryDeleteUserTracking = "DELETE FROM DailyTracking WHERE UserID = $userID AND DATE = '$date'";
        $isQueryExecuted = mysqli_query($con, $queryDeleteUserTracking);

        // Check if the query executed successfully
        if ($isQueryExecuted) {
            // Set a success message in the session
            $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>正常に削除されました。</p></div>";

        } else {
            // Set an error message in the session if the deletion fails
            $_SESSION['feedbackMessage'] = "<div class='message-text fail-alert'><p>何か問題が発生しました。</p></div>";
        }
    } else {
        // Set an error message 
        $_SESSION['feedbackMessage'] = "<div class='message-text fail-alert'><p>何か問題が発生しました。</p></div>";
    }
    

    // Redirect to the daily_tracking_overview page after deletion
    header("Location: daily_tracking_overview.php?date=$date");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php includeHeadAssets(); ?> <!-- Include the mutual head content for this page -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> <!-- Include Font Awesome for icons -->
    </head>

    <body>
  
        <header class="sidebar-navigation insights-navigation">
            <?php renderUserNavigation(); ?> <!-- Include the mutual header for user navigation -->
        </header>
        <!-- Display logout button for the logged-in user -->
        <?php renderUserHeaderWithLogout($userData); ?> 

        <div class="main-wrapper">
            <h2>Insights</h2>

            <!-- Breadcrumb navigation -->
            <div class="breadcrumbs">
                <a href="../user_home.php"><p>Top Page</p></a>
                <i class="fa-solid fa-angle-right fa-sm"></i>
                <a href="./insights.php"><p>Insights</p></a>
                <i class="fa-solid fa-angle-right fa-sm"></i>
                <a href="./user_tracking_records.php"><p>Tracking Records</p></a>
                <i class="fa-solid fa-angle-right fa-sm"></i>
                <p class="bread-active">Daily Tracking Overview</p>
            </div><!--. breadcrumbs -->

            <!-- Section for displaying user tracking information -->
            <section class="tracking-records-wrapper">  

                <!-- Display feedback messages, included from a shared PHP file -->
                <div>
                    <?php include('../../server-side/shared/feedback_messages.php'); ?>
                </div>

                <!-- Check if there are any daily tracking records -->
                <?php if ($dailyTrackingCount > 0) {?>

                    <!-- Button to open the delete confirmation modal -->
                    <button id="delete-post-button" class="teritary-btn">Delete</button>

                    <!-- Modal dialog for delete confirmation -->
                    <div id="deleteConfirmationModal" class="modal">
                        <div class="modal-content">
                            <p class="japanese-prompt">本当に削除してよろしいですか?</p><!-- Confirmation prompt in Japanese asking if the user really wants to delete -->
                            <p class="english-prompt">Are you absolutely sure you want to delete this item? </p>
                            <!-- Main container for modal buttons -->
                            <div class="modal-main">
                                <!-- Button to close the modal without deleting -->
                                <div class="cancel-button"> 
                                    <p>Cancel</p>
                                </div><!-- .cancel-delete-btn -->

                                <!-- Container for the delete form -->
                                <div>
                                    <!-- Form to submit the delete request -->
                                    <form action="" method="POST">
                                        <input type="submit" value="Delete" name="delete" class="delete-confirmation-button"> <!-- Submit button to confirm deletion -->
                                    </form>
                                </div> 

                            </div><!-- .modal-main -->
                        </div><!-- .modal-content -->
                    </div><!-- .modal -->

                <?php } else { ?>  <!-- If no daily tracking records exist -->
                    <!-- Form to register a new mood -->
                    <form method="post" action="../mood/register_mood.php" class="mood-registration-form">
                        <input type="hidden" name="sendDateNext" value="<?php echo $date;?>"> <!-- Hidden input to send the selected date -->
                        <input type="submit" value="Register Mood"> <!-- Button to submit the mood registration form -->
                    </form>
                <?php } ?>

                <!-- Form to submit the selected date for viewing details -->
                <form action="" method="post">
                    <!-- Container for the entire view day section -->
                    <div class="day-wrapper">
                        <!-- Section to display the date and associated day of the week -->
                        <section class="date-info">
                            
                            <!-- Display the name of the day of the week -->
                            <div class="weekday-name">
                                <?php
                                // Determine the day of the week based on $dayofweek variable
                                switch($dayofweek) {
                                    case 0:
                                        print "日曜日"; // Sunday
                                        break;
                                    case 1:
                                        print "月曜日"; // Monday  
                                        break;
                                    case 2:
                                        print "火曜日"; // Tuesday
                                        break;
                                    case 3:
                                        print "水曜日"; // Wednesday 
                                        break;
                                    case 4:
                                        print "木曜日"; // Thursday
                                        break;
                                    case 5:
                                        print "金曜日"; // Friday
                                        break;
                                    case 6:
                                        print "土曜日"; // Saturday
                                        break;
                                    default :
                                    print "[曜日]エラー発生"; // Error message for unexpected day of the week
                                } ?>
                            </div><!-- .weekday-name -->

                            <!-- Container for date navigation -->
                            <div class="date-navigation">
                                <!-- Link to view the previous date -->
                                <a href="?date=<?=$previousDate;?>">
                                    <div id="previous-day-btn" class="previous-day-btn"><i class="fa-solid fa-angles-left"></i></div>
                                </a>

                                <!-- Display the current date -->
                                <?php echo $date ?>


                                <?php 
                                // Check if next date is the same as the current date; if so, set it to the current date
                                if ($nextDate == $currentDate) {
                                    $nextDate = $currentDate;
                                };
                                ?>

                                <!-- Link to view the next date with a class based on the current date state -->
                                <a href="?date=<?=$nextDate;?>" class="<?php if ($date == $currentDate) { echo "inactiveLink";} ?>">
                                    <div id="next-day-btn" class="next-day-btn"><i class="fa-solid fa-angles-right"></i></div>
                                </a>

                            </div><!-- .date-navigation -->
                        </section><!-- .date-info -->


                        <!-- Container for displaying the details of the selected day -->
                        <div class="day-details-container">
                            <?php 
                            // Check if there is daily tracking data available 
                            if($dailyTrackingCount > 0) { ?>
                                <!-- Left section displaying mood information -->
                                <section class="mood-details-container <?php echo $moodname; ?>-mood">
                                    <section class="mood-info-wrapper">

                                        <!-- Container for mood emoji -->
                                        <div class="mood-emoji-container">
                                            <p><?php echo $moodEmoji; ?></p>
                                        </div><!-- .mood-emoji-container -->

                                        <!-- Container for mood text -->
                                        <div class="mood-text-container">
                                            <?php 
                                            // Check if mood is not empty 
                                            if (!empty($mood)){?>
                                                <p><?php echo $mood;?></p> <!-- Display the mood text -->
                                            <?php } ?>
                                        </div><!-- .mood-text-container -->

                                    </section><!-- .mood-info-wrapper -->
                                </section><!-- .mood-details-container -->

                                <!-- Right section displaying additional details -->
                                <section class="additional-details-container <?php echo $moodname; ?>-mood-border">
                                    <!-- Left side content -->
                                    <div class="left-section">

                                        <!-- Feelings section -->
                                        <div class="feeling-container">
                                            <!-- Heading for feelings -->
                                            <h4>感情</h4>

                                            <!-- Container for listing feelings -->
                                            <div class="feeling-item">
                                                <?php 
                                                // Loop through each row of feelings fetched from the database
                                                while ($rowFeelings = mysqli_fetch_assoc($resultFeelings)) {
                                                    $feelingName = $rowFeelings['FeelingName']; // Get the name of the feeling from the current row ?>

                                                    <!-- Display the feeling -->
                                                    <span><?php echo $feelingName; ?></span>
                                                <?php } ?>
                                            </div><!-- .feeling-item -->
                                        </div><!-- .feeling-container -->

                                        <!-- Container for displaying sleep information -->
                                        <div class="sleep-container">
                                            <!-- Header for sleep duration -->
                                            <h4>睡眠時間</h4>

                                            <!-- Wrapper for the sleep time display -->
                                            <div class="sleep-duration-display">
                                                <?php 
                                                // Check if sleep time is not empty
                                                if (!empty($sleepTime)) { ?>
                                                    <!-- Display sleep time in hours -->
                                                    <p><?php echo $sleepTime; ?> 時間</p>
                                                <?php } ?>
                                            </div><!-- .sleep-duration-display -->
                                        </div><!-- .sleep-container -->

                                        <!-- Container for displaying the memo -->
                                        <div class="memo-container">
                                            <?php 
                                            // Check if there is a memo available
                                            if (!empty($memo['Memo'])) { ?>
                                                <!-- Header for the memo section -->
                                                <h4>日記</h4>

                                                <!-- Wrapper for the memo display -->
                                                <div class="memo-textarea <?php echo $moodname; ?>">
                                                    <?php 
                                                    // Check again if the memo is not empty
                                                    if (!empty($memo['Memo'])) {
                                                        echo $memo['Memo'];  // Display the memo content
                                                    } ?>
                                                </div><!-- .memo-textarea -->
                                            <?php } ?>
                                        </div><!-- .memo-container -->
                                    </div><!-- .left-section --> 

                                    <!-- Right side content -->
                                    <div class="right-section">

                                        <!-- Container for displaying activities -->
                                        <div class="activities-container">
                                            <!-- Header for the activities section -->
                                            <h4>アクティビティ</h4>

                                            <!-- Container for displaying activities, companies, locations, foods, and weather -->
                                            <div class="activity-detail-wrapper">

                                                    <?php 
                                                    // Fetch and display activities
                                                    while ($activitiesData = mysqli_fetch_assoc($resultActivities)) {
                                                        $activityName = $activitiesData['ActivityName']; // Retrieve the name of the activity
                                                        $activityIcon = $activitiesData['ActivityIcon']; // Retrieve the corresponding icon for the activity
                                                        ?> 

                                                        <!-- Wrapper for each activity -->
                                                        <div class="item-wrapper">
                                                            <!-- Display activity icon -->
                                                            <div class="item-icon <?php echo $moodname; ?>-mood"><?php echo $activityIcon; ?></div>
                                                            <!-- Display activity name -->
                                                            <?php echo $activityName; ?> 
                                                        </div>
                                                    <?php }

                                                    // Fetch and display companies
                                                    while ($companiesDate = mysqli_fetch_assoc($resultCompany)) {
                                                        $companyName = $companiesDate['CompanyName']; // Retrieve the name of the company
                                                        $companyIcon = $companiesDate['CompanyIcon']; // Retrieve the corresponding icon for the company
                                                        ?> 

                                                        <!-- Wrapper for each company -->
                                                        <div class="item-wrapper">
                                                            <!-- Display company icon -->
                                                            <div class="item-icon <?php echo $moodname; ?>-mood"><?php echo $companyIcon; ?></div>
                                                            <!-- Display company name -->
                                                            <?php echo $companyName; ?>
                                                        </div>
                                                    <?php }

                                                    // Fetch and display locations
                                                    while ($locationsData = mysqli_fetch_assoc($resultLocations)) {
                                                        $locationName = $locationsData['LocationName']; // Retrieve the name of the location
                                                        $locationIcon = $locationsData['LocationIcon']; // Retrieve the corresponding icon for the location
                                                        ?> 

                                                        <!-- Wrapper for each location -->
                                                        <div class="item-wrapper">
                                                            <!-- Display location icon -->
                                                            <div class="item-icon <?php echo $moodname; ?>-mood"><?php echo $locationIcon; ?></div>
                                                            <!-- Display location name -->
                                                            <?php echo $locationName; ?>
                                                        </div>
                                                    <?php }

                                                    // Fetch and display food
                                                    while ($foodData = mysqli_fetch_assoc($resultFood)) {
                                                        $foodName = $foodData['FoodName']; // Retrieve the name of the food
                                                        $foodIcon = $foodData['FoodIcon']; // Retrieve the corresponding icon for the location
                                                        ?> 

                                                        <!-- Wrapper for each food -->
                                                        <div class="item-wrapper">
                                                            <!-- Display food icon -->
                                                            <div class="item-icon <?php echo $moodname; ?>-mood"><?php echo $foodIcon; ?></div>
                                                            <!-- Display food name -->
                                                            <?php echo $foodName; ?></div>
                                                    <?php }

                                                    // Fetch and display weather
                                                    while($weatherData = mysqli_fetch_assoc($resultWeather)){
                                                        $weatherName = $weatherData['WeatherName']; // Retrieve the name of the weather
                                                        $weatherIcon = $weatherData['WeatherIcon']; // Retrieve the corresponding icon for the weather
                                                        ?> 

                                                        <!-- Wrapper for each weather -->
                                                        <div class="item-wrapper">
                                                            <!-- Display weather icon -->
                                                            <div class="item-icon <?php echo $moodname; ?>-mood"><?php echo $weatherIcon; ?></div>
                                                            <!-- Display weather name -->
                                                            <?php echo $weatherName; ?></div>
                                                        <?php  
                                                    } ?>

                                            </div><!-- .activity-detail-wrapper -->
                                        </div><!-- .activities-container -->


                                        <?php 
                                        // Check if there are completed goals
                                        if ($completedGoalsCount != 0) { ?>
                                            <!-- Container for displaying goals -->
                                            <div class="goals-container">
                                                <!-- Header for completed goals -->
                                                <h4>達成した目標</h4>

                                                <!-- Wrapper for the goals list -->
                                                <div class="completed-goals-wrapper">
                                                    <?php
                                                    // Loop through each completed goal
                                                    while ($rowCompletedGoals = mysqli_fetch_assoc($resultCompletedGoals)) {
                                                        // Retrieve the goal name and icon from the database
                                                        $goalName = $rowCompletedGoals['GoalName'];
                                                        $goalIcon = $rowCompletedGoals['GoalIcon'];
                                                        ?> 

                                                        <!-- Individual goal container -->
                                                        <div class="goal-item">
                                                            <div class="goal-icon"><?php echo $goalIcon; ?></div>
                                                            <p class="goal-name"><?php echo $goalName; ?></p>
                                                        </div><!-- .goal-item -->

                                                    <?php } ?>
                                                </div><!-- .completed-goals-wrapper -->
                                                
                                            </div><!-- .goals-container -->
                                        <?php } ?>

                                    </div><!-- .right-section --> 
                                </section><!-- .additional-details-container -->

                            <?php } else { ?> <!-- If there are no completed entries -->

                                <!-- Section for displaying a message when there are no entries -->
                                <section class="no-tracking-data">
                                    <p> <?php echo "未記入"; ?> </p> <!-- Display a message indicating no entries have been made -->
                                </section><!-- .no-tracking-data -->
                                
                            <?php } ?>

                        </div><!-- .day-details-container -->
                    </div><!-- .day-wrapper -->
                </form>
            </section><!-- .tracking-records-wrapper -->  
        </div><!-- .main-wrapper -->

        <script>
            // Script for handling the delete confirmation modal
            // This script manages the display and hiding of a modal dialog 
            // that prompts the user to confirm deletion of an item.

            // Get the modal element by its ID
            var modalElement = document.getElementById("deleteConfirmationModal");

            // Get the button element that opens the modal
            var openModalButton = document.getElementById("delete-post-button");

            // Get the close button element by class name
            var closeModalButton = document.getElementsByClassName("cancel-button")[0];

            // When the open modal button is clicked, display the modal
            openModalButton .onclick = function() {
                modalElement.style.display = "block";
            }

            // When the close button is clicked, hide the modal
            closeModalButton.onclick = function() {
                modalElement.style.display = "none";
            }

            // When clicking outside the modal, close it
            window.onclick = function(event) {
                if (event.target == modal) {
                    modalElement.style.display = "none";
                }
            }
        </script> 
    </body>
</html>
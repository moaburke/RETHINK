<?php
/**
 * 
 * Page Name: toggle_post_like_mypage.php
 * Author: Moa Burke
 * Date: 2024-10-30
 * Description: Daily Tracking Script for User Activities, Emotions, and Goals
 *      This script handles daily tracking for users, allowing them to log and manage their moods, 
 *      feelings, activities, companions, locations, food intake, weather, sleep time, and goals for a given date. 
 *      It first checks if there is an existing entry for the selected date. If so, it deletes existing records 
 *      associated with that date and creates new entries. If no entry exists, it creates new records directly. 
 *      Additionally, the script supports updating text memos related to the daily tracking.
 *
 * Main features:
 *  - User Authentication: Ensures only logged-in users can access this page.
 *  - Record Deletion: Deletes existing records for a specific date before inserting new entries.
 *  - Record Insertion: Inserts new tracking data into multiple database tables.
 *  - Date Handling: Allows users to specify or auto-select the date for tracking data.
 *
 * Dependencies:
 *  - connections.php: Handles database connection.
 *  - check_login.php: Verifies user authentication.
 *  - timezone.php: Configures timezone settings.
 *
 * After execution, the user is redirected to the user homepage.
*/
session_start();

// Define a constant for the base path
define('BASE_PATH', '../shared/');

include(BASE_PATH . "connections.php"); // Include databade connection file
include(BASE_PATH . "check_login.php"); // Include login check script to verify user authentication before accessing this page.
include(BASE_PATH . "timezone.php"); // Include the timezone configuration

// Get the logged-in user's data
$userData = check_login($con);

// If the form is submitted (POST request)
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Store the user ID from the logged-in user data
    $userID = $userData['UserID'];

    // Determine the date to be used for tracking
    if (isset($_POST['date-selector'])) {
        // Check if the selected date is empty
        if (($_POST['date-selector']) == "") {
            // If the selected date is empty, check if 'sendDateReg' is set
            if (isset($_POST['sendDateReg'])) {
                // Use the date provided in 'sendDateReg'
                $date = $_POST['sendDateReg'];
            } else {
                // If 'sendDateReg' is not set, default to today's date
                $date = date("Y-m-d");
            }
        } else {
            // If a valid date is selected, convert it to 'Y-m-d' format
            $date = date('Y-m-d', strtotime($_POST['date-selector']));
        }
    } else {
        // If no date selection is made, default to today's date
        $date = date("Y-m-d");
    }

    // Query to check if the user has already submitted tracking data for the selected date
    $queryUserTrackingDate = "SELECT * FROM dailytracking WHERE UserID = $userID AND Date = '$date'";
    $resultUserTrackingDate = mysqli_query($con,$queryUserTrackingDate);
    $userTrackingDate = mysqli_fetch_assoc($resultUserTrackingDate);
    
    // Check if the user has already posted for today in the DailyTracking table
    if (mysqli_num_rows($resultUserTrackingDate) > 0) {
        // Retrieve the TrackingID of the user's current entr
        $trackingID = $userTrackingDate['TrackingID'];

        // Delete associated moods from the trackmoods table
        $queryDeleteMood = "DELETE FROM trackmoods WHERE TrackingID = $trackingID ";
        mysqli_query($con, $queryDeleteMood);

        // Delete associated feelings from the trackfeelings table
        $queryDeleteFeelings = "DELETE FROM trackfeelings WHERE TrackingID = $trackingID ";
        mysqli_query($con, $queryDeleteFeelings);

        // Delete associated activities from the trackactivities table
        $queryDeleteActivities = "DELETE FROM trackactivities WHERE TrackingID = $trackingID ";
        mysqli_query($con, $queryDeleteActivities);
        
        // Delete associated company data from the trackcompany table
        $queryDeleteCompany = "DELETE FROM trackcompany WHERE TrackingID = $trackingID ";
        mysqli_query($con, $queryDeleteCompany);

        // Delete associated locations from the tracklocations table
        $queryDeleteLocations = "DELETE FROM tracklocations WHERE TrackingID = $trackingID ";
        mysqli_query($con, $queryDeleteLocations);

        // Delete associated food entries from the trackfoods table
        $queryDeleteFoods = "DELETE FROM trackfoods WHERE TrackingID = $trackingID ";
        mysqli_query($con, $queryDeleteFoods);

        // Delete associated weather data from the trackweather table
        $queryDeleteWeather = "DELETE FROM trackweather WHERE TrackingID = $trackingID ";
        mysqli_query($con, $queryDeleteWeather);

        // Delete associated memos from the memos table
        $queryDeleteWeather = "DELETE FROM memos WHERE TrackingID = $trackingID ";
        mysqli_query($con, $queryDeleteWeather);

        // Delete associated sleep time data from the tracksleeptime table
        $querysleepTime = "DELETE FROM tracksleeptime WHERE TrackingID = $trackingID";
        mysqli_query($con, $querysleepTime);

        // If there are goals set, check and delete goals that are not in the submitted list
        if (!empty($_POST['goalOnPage'])) {
            foreach ($_POST['goalOnPage'] as $goal) {
                if (!empty($_POST['goal'])) {
                    // If the goal is not in the submitted goals, delete it from the trackgoals table
                    if (!in_array($goal, $_POST['goal'])) {
                        $delete = "DELETE FROM trackgoals WHERE UserGoalID = $goal AND Date = '$date'";
                        mysqli_query($con, $delete); 
                    }
                }
            }
        }

        // If no goals are submitted, delete all user goals for today
        if (empty($_POST['goal'])) {
            // Retrieve all user goals from the usergoals table
            $queryGetUsersGoals = "SELECT UserGoalID FROM usergoals WHERE UserID = $userID";
            $getUsersGoals = mysqli_query($con, $queryGetUsersGoals);
            
            // Loop through each user goal and delete it from the trackgoals table for today
            while ($eachUserGoal = mysqli_fetch_array($getUsersGoals)){
                $deleteEachUserGoal = $eachUserGoal['UserGoalID'];
                $deleteGoal = "DELETE FROM trackgoals where UserGoalID = $deleteEachUserGoal and Date = '$date'";
                mysqli_query($con, $deleteGoal); 
            }
        }

        // Finally, delete the entry for today's date from the DailyTracking table
        $queryDeleteUserDate = "DELETE FROM DailyTracking WHERE UserID = $userID AND Date = '$date'";
        mysqli_query($con, $queryDeleteUserDate);
    }
    
    // Create a new entry in the DailyTracking table for the current user and date
    $insertDailyTracking = "INSERT INTO DailyTracking (UserID, Date) VALUES('$userID', '$date')";
    mysqli_query($con, $insertDailyTracking);

    // Retrieve the TrackingID of the newly created entry from the DailyTracking table
    $queryTrackingID = "SELECT * FROM DailyTracking WHERE UserID = $userID AND Date = '$date'";
    $resultTrackingID = mysqli_query($con,$queryTrackingID);
    $trackingID = mysqli_fetch_assoc($resultTrackingID);
    
    
    // Store the TrackingID as a string for further use
    $trackID = $trackingID['TrackingID'];


    // Add today's mood to the TrackMoods table if the mood is not empty
    if (!empty($_POST['selected-mood'])) {
        $todaysMood = $_POST['selected-mood'];
        // Insert the mood data linked to the current TrackingID
        $insertMood = "INSERT INTO trackmoods (TrackingID, MoodID) VALUES('$trackID', '$todaysMood')";
        mysqli_query($con, $insertMood);
    }

    // Add data to the TrackFeelings table
    if (!empty($_POST['feeling'])) {
        // Iterate through each submitted feeling
        foreach ($_POST['feeling'] as $feelingID) {
            // Insert each feeling associated with the current TrackingID into the TrackFeelings table
            $insertFeeling = "INSERT INTO trackfeelings (TrackingID, FeelingID) VALUES('$trackID', '$feelingID')";
            mysqli_query($con, $insertFeeling); 
        }
    }
    
    // Add data to the TrackActivities table
    if (!empty($_POST['activities'])) {
        // Iterate through each submitted activity
        foreach ($_POST['activities'] as $activityID) {
            // Insert each activity associated with the current TrackingID into the TrackActivities table
            $insertActivities = "INSERT INTO trackactivities (TrackingID, ActivityID) VALUES('$trackID', '$activityID')";
            mysqli_query($con, $insertActivities); 
        }
    }
    
    // Add data to the TrackCompany table
    if (!empty($_POST['company'])) {
        // Iterate through each submitted company
        foreach ($_POST['company'] as $comanyID) {
            // Insert each company associated with the current TrackingID into the TrackCompany table
            $insertCompany = "INSERT INTO trackcompany (TrackingID, CompanyID) VALUES('$trackID', '$comanyID')";
            mysqli_query($con, $insertCompany); 
        }
    }

    // Add data to the TrackLocations table
    if (!empty($_POST['location'])) {
        // Iterate through each submitted location
        foreach ($_POST['location'] as $locationID) {
            // Insert each location associated with the current TrackingID into the TrackLocations table
            $insertLocation = "INSERT INTO tracklocations (TrackingID, LocationID) VALUES('$trackID', '$locationID')";
            mysqli_query($con, $insertLocation); 
        }
    }
    
    // Add data to the TrackFoods table
    if (!empty($_POST['foods'])) {
        // Iterate through each submitted food item
        foreach ($_POST['foods'] as $foodID) {
            // Insert each food item associated with the current TrackingID into the TrackFoods table
            $insertFood = "INSERT INTO trackfoods (TrackingID, FoodID) VALUES('$trackID', '$foodID')";
            mysqli_query($con, $insertFood); 
        }
    }

    // Add data to the TrackWeather table
    if (!empty($_POST['weather'])) {
        // Iterate through each submitted weather condition
        foreach ($_POST['weather'] as $weatherID) {
            // Insert each weather condition associated with the current TrackingID into the TrackWeather table
            $insertWeather = "INSERT INTO trackweather (TrackingID, WeatherID) VALUES('$trackID', '$weatherID')";
            mysqli_query($con, $insertWeather); 
        }
    }

    // Check if there are any submitted goals
    if (!empty($_POST['goal'])) {
        // Iterate through each submitted goal ID
        foreach ($_POST['goal'] as $goalID) {
            // Query to check if the goal for today already exists for the user
            $queryTodaysGoal = "SELECT * FROM trackgoals WHERE UserGoalID = $goalID AND Date = '$date'";
            $resultTodaysGoal = mysqli_query($con,$queryTodaysGoal);
            $rowsTodaysGoalEach = mysqli_num_rows($resultTodaysGoal);

            // If no existing goal for today, insert the new goal
            if ($rowsTodaysGoalEach == 0 ) {
                $insertGoal = "INSERT INTO trackgoals (UserGoalID, Date) VALUES('$goalID', '$date')";
                mysqli_query($con, $insertGoal);  
            }
        }       
    }

    // Check if there is any submitted sleeping time
    if (!empty($_POST['sleepingTime'])) {
        // Get the submitted sleeping time
        $sleepingTime = $_POST['sleepingTime'];
        // Insert the sleeping time associated with the current TrackingID
        $insertSleepingTime = "INSERT INTO tracksleeptime (sleepTime, TrackingID) VALUES('$sleepingTime', '$trackID')";
        mysqli_query($con, $insertSleepingTime); 
    }

    // Check if there is any memo text submitted
    if (!empty($_POST['textarea'])) {
        // Get the submitted memo text
        $memo = $_POST['textarea'];

        // Ensure the memo is not the default prompt text
        if ($memo != "なにかを書く") {
            // Insert the memo associated with the current TrackingID
            $insertMemo = "INSERT INTO memos (TrackingID, Memo) VALUES('$trackID', '$memo')";
            mysqli_query($con, $insertMemo); 
        }
    }
}

// Redirect the user to the user home page
header("Location: ../../user/user_home.php");
exit; // Ensure that no further code is executed after the redirect
?>


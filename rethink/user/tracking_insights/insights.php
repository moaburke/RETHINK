<?php
/*
 * Page Name: Insights
 * Author: Moa Burke
 * Date: 2024-10-28
 * Description: This page retrieves and displays the user's tracked moods and associated feelings, showing them in both Japanese and English. 
 *      It features a doughnut chart visualization of mood distributions and navigation controls for browsing through daily records.
 *
 * Description of the Page:
 * - The Mood Tracker Display page allows users to visualize their mood data over time, providing insights into their emotional well-being.
 * - It retrieves mood records from the database for the logged-in user, displaying both the primary and additional feelings associated with each mood.
 * - A doughnut chart, rendered using Chart.js, illustrates the distribution of different moods, enabling users to quickly grasp their mood trends.
 * - Users can navigate through their mood records using the 'Previous' and 'Next' buttons, making it easy to review past moods.
 * - If no mood records are available, a user-friendly message is displayed, encouraging users to track their moods regularly.
 *
 * Notes:
 * - Displays mood names conditionally based on availability of tracking data.
 * - Includes functionality to count and show primary and additional feelings associated with each mood.
 * - Utilizes Chart.js to visualize the distribution of moods through a doughnut chart.
 * - Implements navigation buttons to allow users to cycle through mood records while managing button states for usability.
 * - Shows a message if no mood records exist.
 *
 * Dependencies:
 * - Requires PHP for server-side processing and MySQL database connection.
 * - JavaScript for handling the doughnut chart rendering and navigation functionality.
 * - Chart.js library for data visualization.
 *
*/

session_start();// Start the session

// Define a constant for the base path
define('BASE_PATH', '../../server-side/');

include(BASE_PATH . "shared/connections.php"); // Include databade connection file
include(BASE_PATH . "shared/check_login.php"); //Include the utiity functions
include(BASE_PATH . "shared/timezone.php"); // Include the timezone configuration
include(BASE_PATH . "shared/constants.php"); // Include the constants file
include(BASE_PATH . "shared/header/user_layout.php"); // Include the user header layout file
require_once BASE_PATH . "user/calendar.php"; // Include the calendar functionality

define('MINIMUM_TRACK_COUNT', 2);
define("MAX_DISPLAYED_ENTRIES", 9);

// Check if the user is logged in and retrieve user data
$userData = check_login($con);
$userID = $userData['UserID']; // Retrieve the UserID from the session data

// Get today's date
$date = date("Y-m-d");

// Query to retrieve the latest 7 daily tracking records for the user
$queryRecentTracking = "SELECT * FROM dailytracking WHERE UserID = $userID ORDER BY Date desc LIMIT 7";
$resultRecentTracking = mysqli_query($con, $queryRecentTracking); // Execute query for recent daily tracking records
$totalResultDays = mysqli_num_rows($resultRecentTracking); // Count the number of rows returne

// Query to retrieve all moods available
$queryAllMoods = "SELECT * FROM moods";
$resultAllMoods = mysqli_query($con, $queryAllMoods); // Execute query to get all moods
$resultMoodsChart = mysqli_query($con, $queryAllMoods); // Duplicate result for use in chart

// Query to retrieve each specific mood type based on MoodID
$queryMoodGreat = "SELECT * FROM moods WHERE MoodID = '" . MOOD_GREAT ."'";
$resultMoodGreat = mysqli_query($con, $queryMoodGreat); // Retrieve data for 'Great' mood

$queryMoodGood = "SELECT * FROM moods WHERE MoodID = '" . MOOD_GOOD ."'";
$resultMoodGood = mysqli_query($con, $queryMoodGood); // Retrieve data for 'Good' mood

$queryMoodOkay = "SELECT * FROM moods WHERE MoodID = '" . MOOD_OKAY ."'";
$resultMoodOkay = mysqli_query($con, $queryMoodOkay); // Retrieve data for 'Okay' mood

$queryMoodBad = "SELECT * FROM moods WHERE MoodID = '" . MOOD_BAD ."'";
$resultMoodBad = mysqli_query($con, $queryMoodBad); // Retrieve data for 'Bad' mood

$queryMoodAwful = "SELECT * FROM moods WHERE MoodID = '" . MOOD_AWFUL ."'";
$resultMoodAwful = mysqli_query($con, $queryMoodAwful); // Retrieve data for 'Awful' mood


// Retrieve data for 'Great' mood count by joining daily tracking and mood tracking tables
$queryGreatMoodCount = mysqli_query($con, 
    "SELECT t.MoodID, COUNT(MoodID) AS cntGreatMood 
    FROM dailytracking d 
    JOIN trackmoods t ON d.TrackingID = t.TrackingID 
    WHERE UserID = $userID 
    AND MoodID = '" . MOOD_GREAT ."' 
    GROUP BY t.MoodID");
$resultGreatMoodCount = mysqli_fetch_assoc($queryGreatMoodCount); // Fetch the result as an associative array

// Check if 'Great' mood data is available, otherwise set count to 0
if (!empty($resultGreatMoodCount)) {
    $greatMoodCount = $resultGreatMoodCount['cntGreatMood'];
} else {
    $greatMoodCount = 0;
}


// Retrieve data for 'Good' mood count by joining daily tracking and mood tracking tables
$queryGoodMoodCount = mysqli_query($con, 
    "SELECT t.MoodID, COUNT(MoodID) AS cntGoodMood 
    FROM dailytracking d 
    JOIN trackmoods t ON d.TrackingID = t.TrackingID 
    WHERE UserID = $userID 
    AND MoodID = '" . MOOD_GOOD ."' 
    GROUP BY t.MoodID");
$resultGoodMoodCount = mysqli_fetch_assoc($queryGoodMoodCount);  // Fetch the result as an associative array

// Check if 'Good' mood data is available, otherwise set count to 0
if (!empty($resultGoodMoodCount)) {
    $goodMoodCount = $resultGoodMoodCount['cntGoodMood'];
} else {
    $goodMoodCount = 0;
}


// Retrieve data for 'Okay' mood count by joining daily tracking and mood tracking tables
$queryOkayMoodCount = mysqli_query($con, 
    "SELECT t.MoodID, COUNT(MoodID) AS cntOkayMood 
    FROM dailytracking d 
    JOIN trackmoods t ON d.TrackingID = t.TrackingID 
    WHERE UserID = $userID 
    AND MoodID = '" . MOOD_OKAY ."' 
    GROUP BY t.MoodID"
);
$resultOkayMoodCount = mysqli_fetch_assoc($queryOkayMoodCount);  // Fetch the result as an associative array

// Check if 'Okay' mood data is available, otherwise set count to 0
if (!empty($resultOkayMoodCount)) {
    $okayMoodCount = $resultOkayMoodCount['cntOkayMood'];
} else {
    $okayMoodCount = 0;
}


// Retrieve data for 'Bad' mood count by joining daily tracking and mood tracking tables
$queryBadMoodCount = mysqli_query($con, 
    "SELECT t.MoodID, COUNT(MoodID) AS cntBadMood 
    FROM dailytracking d 
    JOIN trackmoods t ON d.TrackingID = t.TrackingID 
    WHERE UserID = $userID 
    AND MoodID = '" . MOOD_BAD ."' 
    GROUP BY t.MoodID"
);
$resultBadMoodCount = mysqli_fetch_assoc($queryBadMoodCount);  // Fetch the result as an associative array

// Check if 'Bad' mood data is available, otherwise set count to 0
if (!empty($resultBadMoodCount)) {
    $badMoodCount = $resultBadMoodCount['cntBadMood'];
} else {
    $badMoodCount = 0;
}

// Retrieve data for 'Awful' mood count by joining daily tracking and mood tracking tables
$queryAwfulMoodCount = mysqli_query($con, 
    "SELECT t.MoodID, COUNT(MoodID) AS cntAwfulMood 
    FROM dailytracking d 
    JOIN trackmoods t ON d.TrackingID = t.TrackingID 
    WHERE UserID = $userID 
    AND MoodID = '" . MOOD_AWFUL ."' 
    GROUP BY t.MoodID");
$resultAwfulMoodCount = mysqli_fetch_assoc($queryAwfulMoodCount);  // Fetch the result as an associative array

// Check if 'Awful' mood data is available, otherwise set count to 0
if (!empty($resultAwfulMoodCount)) {
    $awfulMoodCount = $resultAwfulMoodCount['cntAwfulMood'];
} else {
    $awfulMoodCount = 0;
}


$mostFrequentMood = ""; // Initialize variable to store the mood with the highest percentage


// Calculate total count of moods for percentage calculation
$totalMoodCount = $greatMoodCount + $goodMoodCount + $okayMoodCount + $badMoodCount + $awfulMoodCount;


// Check if there are any recorded mood entries to calculate percentages
if(!empty($totalMoodCount)){
    
    // Calculate percentage for 'Great' mood and update most frequent mood if applicable
    $greatMoodPercentage = ($greatMoodCount / $totalMoodCount) * 100;
    // Format the 'Great' mood percentage to a whole number
    $greatPercentage = number_format($greatMoodPercentage, 0); 

    // Check if the 'Great' mood percentage is greater than zero
    if ($greatPercentage > 0) {
        // Set 'mostFrequentMood' to 'emoji-status-great' if 'Great' mood has the highest percentage so far
        $mostFrequentMood = "emoji-status-great";
        $highestMoodPercentage = $greatPercentage;  // Update the highest mood percentage
    } else {
        // Set the highest mood percentage to zero if no 'Great' moods are recorded
        $highestMoodPercentage = 0;
    }

    
    // Calculate percentage for 'Good' mood and update most frequent mood if applicable
    $goodMoodPercentage = ($goodMoodCount / $totalMoodCount) * 100;
    // Format the 'Good' mood percentage to a whole number
    $goodPercentage = number_format($goodMoodPercentage, 0); 

    // Check if 'Good' mood has the highest percentage so far, and update the most frequent mood if applicable
    if($goodPercentage > $highestMoodPercentage ){
        $mostFrequentMood = "emoji-status-good"; // Set 'Good' mood as the most frequent mood
        $highestMoodPercentage = $goodPercentage;  // Update the highest mood percentage
    }

    // Calculate percentage for 'Okay' mood and update most frequent mood if applicable
    $okayMoodPercentage = ($okayMoodCount / $totalMoodCount) * 100;
    // Format the 'Okay' mood percentage to a whole number
    $okayPercentage = number_format($okayMoodPercentage, 0); 

    // Check if 'Okay' mood has the highest percentage so far, and update the most frequent mood if applicable
    if ($okayPercentage > $highestMoodPercentage ) {
        $mostFrequentMood = "emoji-status-okay"; // Set 'Okay' mood as the most frequent mood
        $highestMoodPercentage = $okayPercentage; // Update the highest mood percentage
    }

    // Calculate percentage for 'Bad' mood and update most frequent mood if applicable
    $badMoodPercentage = ($badMoodCount / $totalMoodCount) * 100;
    // Format the 'Bad' mood percentage to a whole number
    $badPercentage = number_format($badMoodPercentage, 0); 

    // Check if 'Bad' mood has the highest percentage so far, and update the most frequent mood if applicable
    if($badPercentage > $highestMoodPercentage ){
        $mostFrequentMood = "emoji-status-bad"; // Set 'Bad' mood as the most frequent mood
        $highestMoodPercentage = $badPercentage; // Update the highest mood percentage
    }
    
    // Calculate percentage for 'Awful' mood and update most frequent mood if applicable
    $awfulMoodPercentage = ($awfulMoodCount / $totalMoodCount) * 100;
    // Format the 'Awful' mood percentage to a whole number
    $awfulPercentage = number_format($awfulMoodPercentage, 0); 

    // Check if 'Awful' mood has the highest percentage so far
    if($awfulPercentage > $highestMoodPercentage ){
        $mostFrequentMood = "emoji-status-awful"; // Set 'Awful' mood as the most frequent mood
        $highestMoodPercentage = $awfulPercentage; // Update the highest mood percentage
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Load mutual head components and necessary scripts/styles -->
        <?php includeHeadAssets(); ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Load Chart.js for charting functionality -->
        <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script> <!-- jQuery library -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> <!-- Font Awesome for icons -->
        <script src="../../assets/javascript/tab_interactions.js" defer></script> <!-- This script handles the tab interactions in the user interface -->
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
                <p class="bread-active">Insights</p>
            </div><!--. breadcrumbs -->

            <!-- Insight navigation bar allowing users to switch between 'General' and 'Monthly' categories -->
            <div class="insight-navigation">
                <!-- General insights category, highlighted as the active selection -->
                <div class="general-category active-category">
                    <p>General</p>
                </div>

                <!-- Monthly insights category, with a link to navigate to the monthly insights page -->
                <div class="monthly-category">
                    <a href="./monthly_insights.php"><p>Montly</p></a>
                </div>
            </div><!-- .insight-navigation -->

            <!-- Wrapper for all insights -->
            <div class="insights-wrapper">
                
                <!-- Wrapper for the left section -->
                <section class="insights-left-section">
                    <section class="weekly-overview">

                        <!-- Header section for the continuation days display -->
                        <div class="weekly-overview-top">
                            <div class="continuation-days-header insights-header">
                                <h3>継続日数</h3> <!-- Title displaying "Continuation Days" in Japanese -->
                            </div><!-- .continuation-days-header -->
                        </div><!-- .weekly-overview-top -->
                
                        <!-- Main container for the continuation days content -->
                        <div class="user-progress-section">
                            <?php $date = date("Y-m-d"); // Get today's date ?>

                            <!-- Container for displaying past week's mood tracking -->
                            <div class="weekly-post-status">
                                <?php 
                                // Loop through the past 7 days to gather mood tracking data
                                for ($indexDays = 0; $indexDays < 7; $indexDays++) {
                                    // Adjust the date for each iteration
                                    if ($indexDays > 0) {
                                        $date = date('Y-m-d', strtotime($date .' -1 day'));  // Get the previous day's date
                                    }

                                    // Query the daily tracking data for the specific user and date
                                    $dailyTrackingQuery = "SELECT * FROM dailytracking WHERE UserID = $userID and DATE = '$date'";
                                    $trackingResult = mysqli_query($con, $dailyTrackingQuery);
                                    $trackingEntriesCount = mysqli_num_rows($trackingResult); // Get number of entries for that day

                                    // If mood data exists for the day, fetch the mood name
                                    if ($trackingEntriesCount != 0) {
                                        $trackingData = mysqli_fetch_assoc($trackingResult);
                                        $trackingID = $trackingData['TrackingID']; //Fetch TrackingID from the result

                                        // Query to fetch mood name based on the TrackingID
                                        $moodQuery = mysqli_query($con, "SELECT m.MoodName FROM trackmoods t LEFT JOIN moods m ON t.MoodID = m.MoodID
                                        WHERE TrackingID = $trackingID");
                                        $moodData = mysqli_fetch_assoc($moodQuery);
                                        $trackedMoodName = $moodData['MoodName']; // Store the mood name for the current day
                                    }?>

                                    <!-- Individual day mood display -->
                                    <div class="daily-post-status<?php if($indexDays == 0){
                                        // If it's the current day and no mood was tracked, add 'post-not-made' class
                                        if ($trackingEntriesCount == 0) echo ' post-not-made';
                                        } else {
                                            // If it's not the current day and no mood was tracked, add 'day-without-post ' class
                                            if ($trackingEntriesCount == 0) echo ' day-without-post';
                                            } ?> <?php 
                                            // If mood data exists, add the mood class to the element
                                            if ($trackingEntriesCount != 0) { 
                                                echo $trackedMoodName . "-mood"; 
                                            } ?>">
                                        <?php 
                                        // Display check or cross icon based on whether the mood was tracked
                                        if ($trackingEntriesCount != 0 ) { ?>
                                            <i class="fa-solid fa-check"></i> <!-- Check icon for tracked mood -->
                                        <?php } else { ?>
                                            <i class="fa-solid fa-xmark"></i> <!-- Cross icon for no mood tracked -->
                                        <?php } ?>

                                        <?php 
                                        // Determine the day of the week for the date
                                        $dayOfWeek = date('w', strtotime($date));?>
                                        <div class="day-of-week">
                                            <?php 
                                            // Switch statement to print the day of the week in Japanese
                                            switch($dayOfWeek) {
                                                case 0: // Sunday
                                                    print "日";
                                                    break;
                                                case 1: // Monday
                                                    print "月";  
                                                    break; 
                                                case 2: // Tuesday
                                                    print "火";
                                                    break;
                                                case 3: // Wednesday
                                                    print "水";    
                                                    break;
                                                case 4: // Thursday
                                                    print "木";  
                                                    break;
                                                case 5: // Friday
                                                    print "金";
                                                    break;
                                                case 6: // Saturday
                                                    print "土";
                                                    break;
                                                default :  // Error handling
                                                print "[曜日]エラー発生";
                                            }
                                            ?> 
                                        </div><!-- .day-of-week -->  
                                    </div><!-- .daily-post-status -->
                                <?php } ?>
                            </div><!-- .pastweek-content --> 

                            <!-- Container for displaying the number of consecutive tracking days -->
                            <div class="consecutive-days">
                                <?php 
                                // Get the current date 
                                $currentDate = date("Y-m-d");

                                // Query to retrieve daily tracking data for the user, ordered by date in descending order
                                $queryGetDates = "SELECT * FROM dailytracking WHERE UserID = $userID and Date <= '$currentDate' ORDER BY Date desc";
                                $resultGetDays = mysqli_query($con, $queryGetDates);
                            
                                // Initialize counter for consecutive tracked days
                                $consecutiveDaysCount = 0; 

                                // Loop through the retrieved tracking data
                                while ($rowGetDate = mysqli_fetch_assoc($resultGetDays)){
                                    $trackedDate = $rowGetDate['Date'];

                                    // Count consecutive days tracked starting from today
                                    if($currentDate == $trackedDate){
                                        $consecutiveDaysCount += 1; // Increment counter for the current day
                                    } else {
                                        break; // Exit the loop if a day is not consecutive
                                    }

                                    // Move to the previous day for the next iteration
                                    $currentDate = date('Y-m-d', strtotime($currentDate .' -1 day'));
                                }
                                ?>

                                <p class="<?php echo $mostFrequentMood;?>"> <!-- Class based on the most frequently recorded mood -->
                                    <span><?php echo $consecutiveDaysCount; ?>日</span> <!-- Count of consecutive days tracked -->
                                    <span>継続日数</span> <!-- Label for consecutive days -->
                                </p>

                            </div><!-- .daysInaRow -->
                        </div><!-- .user-progress-section -->
                    
                        <div class= "max-consecutive-days">
                            <?php 
                            // Get the current date
                            $currentDate = date("Y-m-d");

                            // Query to retrieve daily tracking data for the user, ordered by date in descending order
                            $queryGetDates = "SELECT * FROM dailytracking WHERE UserID = $userID and Date <= '$currentDate' ORDER BY Date desc";
                            $resultGetDays = mysqli_query($con, $queryGetDates);

                            // Initialize counters for the current streak and maximum streak
                            $maxStreak = 0;
                            $currentStreak = 0;

                            // Loop through the retrieved tracking data
                            while ($rowGetDate = mysqli_fetch_assoc($resultGetDays)) {
                                $trackedDate = $rowGetDate['Date'];

                                // Check if the tracked date matches the current date
                                if($currentDate == $trackedDate){
                                    $currentStreak += 1; // Increment the current streak count

                                    // Update the maximum streak if the current streak exceeds it
                                    if($currentStreak > $maxStreak){
                                        $maxStreak = $currentStreak; 
                                    }
                                }else{                        
                                    // If the date doesn't match, reset the current streak
                                    $currentDate = $trackedDate;
                                    $currentStreak = 1; // Reset current streak to 1 for the new date
                                }
                                // Move to the previous day for the next iteration
                                $currentDate = date('Y-m-d', strtotime($currentDate .' -1 day'));
                            } ?>

                            <p>
                               最長継続日数：<?php echo $maxStreak; ?>日<!-- Longest streak of consecutive days tracked -->
                            </p> 

                        </div><!-- .max-consecutive-days -->

                    </section><!-- .weekly-overview -->

                    <div class="mood-summary-wrapper">
                        <div class="mood-summary">
                            <div class="mood-summary-content">
                                <p>記録合計</p>  <!-- Total recorded moods -->
                                <div class="mood-days <?php echo $mostFrequentMood;?>">
                                    <span><?php echo $totalMoodCount; ?></span> <!-- Display the total count of moods -->
                                </div><!-- .mood-days -->
                            </div><!-- .mood-summary-content -->
                        </div><!-- .mood-summary -->
                    </div><!-- .mood-summary-wrapper -->

              
                    <!-- Wrapper for displaying mood count information -->
                    <section class="mood-count-wrapper">
                        <div class="mood-header-container">

                            <!-- Container for heading -->
                            <div class="mood-header insights-header">
                                <h3>気分の合計</h3>
                            </div>

                            <!-- Container for mood count and percentages -->
                            <div class="mood-stats-container">

                                <?php if (!empty($totalMoodCount)){ ?> <!-- Check if there is a total mood count -->
                                    <div class="mood-percentages-wrapper">
                                        <!-- Container for individual mood percentages -->
                                        <div class="mood-percentages">

                                            <!-- Check if the percentage for "great" mood is not empty -->
                                            <?php if(!empty($greatPercentage)){ ?>
                                                <div class="mood-percentage-bar great-percentage" style = "width:<?php echo $greatPercentage;?>%;">
                                                    <!-- Visual representation of the "great" mood percentage -->
                                                    <div class="mood-bar-graph"></div>
                                                    <!-- Container for the text displaying the percentage -->
                                                    <div class="mood-percentage-label">
                                                        <p><?php echo $greatPercentage;?>%</p> <!-- Display the percentage value -->
                                                    </div>
                                                </div><!-- .percentageGreat -->
                                            <?php } ?>

                                            <!-- Check if the percentage for "good" mood is not empty -->
                                            <?php if(!empty($goodPercentage)){ ?>
                                                <div class="mood-percentage-bar good-percentage" style = "width:<?php echo $goodPercentage;?>%;">
                                                    <!-- Visual representation of the "good" mood percentage -->
                                                    <div class="mood-bar-graph"></div>
                                                    <!-- Container for the text displaying the percentage -->
                                                    <div class="mood-percentage-label">
                                                        <p><?php echo $goodPercentage;?>%</p> <!-- Display the percentage value -->
                                                    </div>
                                                </div><!-- .percentageGood -->
                                            <?php } ?>
                                            
                                            <!-- Check if the percentage for "okay" mood is not empty -->
                                            <?php if(!empty($okayPercentage)){ ?>
                                                <div class="mood-percentage-bar okay-percentage" style = "width:<?php echo $okayPercentage;?>%;">
                                                    <!-- Visual representation of the "okay" mood percentage -->
                                                    <div class="mood-bar-graph"></div>
                                                    <!-- Container for the text displaying the percentage -->
                                                    <div class="mood-percentage-label">
                                                        <p><?php echo $okayPercentage;?>%</p> <!-- Display the percentage value -->
                                                    </div>
                                                </div><!-- .percentageOkay -->
                                            <?php } ?>

                                            <!-- Check if the percentage for "bad" mood is not empty -->
                                            <?php if(!empty($badPercentage)){ ?>
                                                <div class="mood-percentage-bar bad-percentage" style = "width:<?php echo $badPercentage;?>%;">
                                                    <!-- Visual representation of the "bad" mood percentage -->
                                                    <div class="mood-bar-graph"></div>
                                                    <!-- Container for the text displaying the percentage -->
                                                    <div class="mood-percentage-label">
                                                        <p><?php echo $badPercentage;?>%</p> <!-- Display the percentage value -->
                                                    </div>
                                                </div><!-- .percentageBad -->
                                            <?php } ?>
                                            
                                            <!-- Check if the percentage for "awful" mood is not empty -->
                                            <?php if(!empty($awfulPercentage)){ ?>
                                                <div class="mood-percentage-bar awful-percentage" style = "width:<?php echo $awfulPercentage;?>%;">
                                                    <!-- Visual representation of the "awful" mood percentage -->
                                                    <div class="mood-bar-graph"></div>
                                                    <!-- Container for the text displaying the percentage -->
                                                    <div class="mood-percentage-label">
                                                        <p><?php echo $awfulPercentage;?>%</p> <!-- Display the percentage value -->
                                                    </div>
                                                </div><!-- .percentageAwful -->
                                            <?php } ?>

                                        </div><!-- .mood-percentages -->
                                    </div><!-- .mood-percentages-wrapper -->

                                <?php } ?> <!-- End of condition checking if there is a total mood count -->

                                <div class="moods-summary">
                                    <?php 
                                    // Loop through each mood record retrieved from the database
                                    while ($currentMood = mysqli_fetch_assoc($resultAllMoods)) {
                                        $currentMoodID = $currentMood['MoodID']; // Get the current mood ID

                                        // Query to count the number of occurrences of the current mood for the specified user
                                        $countMoodQuery = "SELECT t.MoodID, COUNT(MoodID) as cntMood 
                                                FROM dailytracking d JOIN trackmoods t
                                                ON d.TrackingID = t.TrackingID 
                                                WHERE UserID = $userID and MoodID = $currentMoodID
                                                GROUP BY t.MoodID";

                                        // Execute the query and store the result
                                        $countMoodResult = mysqli_query($con,$countMoodQuery);
                                        // Fetch the count of the current mood
                                        $currentMoodCount = mysqli_fetch_assoc($countMoodResult); ?>

                                        <div class="mood-item">
                                            <p class="mood-emoji"> <?php echo $currentMood['moodEmoji']; ?> </p>
                                            <p class="mood-count <?php 
                                                // Assign CSS class based on mood ID
                                                if($currentMoodID == MOOD_GREAT) { 
                                                    echo ' mood-great';
                                                } elseif ($currentMoodID == MOOD_GOOD) { 
                                                    echo ' mood-good';
                                                } elseif($currentMoodID == MOOD_OKAY) {
                                                    echo ' mood-okay';
                                                } elseif($currentMoodID == MOOD_BAD) {
                                                    echo ' mood-bad';
                                                } elseif($currentMoodID == MOOD_AWFUL) {
                                                    echo ' mood-awful';
                                                } ?>"> 

                                                <?php 
                                                // Display the count of occurrences for the current mood or "0" if none
                                                if (!empty($currentMoodCount['cntMood'])) {
                                                    echo $currentMoodCount['cntMood'];
                                                } else {
                                                    echo "0";
                                                } 
                                                ?>
                                            </p>

                                        </div> <!-- .mood-item -->
                                    <?php } ?> <!-- End of while loop iterating through all mood records -->
                                </div><!-- .moods-per-month-summary -->

                            </div><!-- .mood-stats-container -->
                        </div><!-- .mood-header-container -->
                    </section><!-- .mood-count-wrapper -->
                </section><!-- .insights-left-section -->

                <!-- Wrapper for the middle section -->
                <section class="insights-middle-section">
                    <div class="mood-chart-container">

                        <!-- Container for heading -->
                        <div class="mood-chart-header insights-header">
                            <h3>気分の割合</h3>
                        </div>

                        <?php
                        // Check if there are any results for the total days
                        if ($totalResultDays > 0) { ?>

                            <p class="mood-section-intro">アカウントを作成してからの気分割合</p> <!-- Displaying the introduction text for mood distribution since account creation -->

                            <div class="mood-chart">
                                <canvas id="moodDistributionChart"></canvas> <!-- Canvas element for rendering the doughnut chart -->
                            </div><!-- .mood-chart -->

                            <div class="mood-chart-count-wrapper">
                                <div class="mood-data-list">
                                    <?php 
                                    // Loop through each mood record for the specified user
                                    while ($rowMoodsWeek = mysqli_fetch_assoc($resultMoodsChart)) {
                                        $currentMoodID = $rowMoodsWeek['MoodID']; // Get the current mood ID

                                        // Query to count occurrences of the current mood for the specified user and group by mood ID
                                        $queryGetCntMoodWeek = "SELECT t.MoodID, d.Date, COUNT(MoodID) as cntMood 
                                            FROM dailytracking d JOIN trackmoods t
                                            ON d.TrackingID = t.TrackingID 
                                            WHERE MoodID = $currentMoodID and UserID = $userID
                                            GROUP BY t.MoodID";

                                        // Execute the query and store the result
                                        $moodCountResult = mysqli_query($con,$queryGetCntMoodWeek);
                                        // Fetch the count of the current mood
                                        $cntMoodWeek = mysqli_fetch_assoc($moodCountResult); ?>

                                        <div class="mood-data-list-item">
                                            <p class="mood-label"> <?php echo htmlspecialchars($rowMoodsWeek['JapaneseMoodName']); ?> </p>
                                            <p class="mood-count"> <?php 
                                            
                                                // Display the total number of days recorded for the current mood; default to "0" if none
                                                if (!empty($cntMoodWeek['cntMood'])){
                                                    echo $cntMoodWeek['cntMood'];
                                                    } else {
                                                        echo "0";
                                                    } 
                                            ?>日</p>

                                        </div> <!-- .mood-data-list-item -->    
                                    <?php } ?><!-- End of while loop iterating through mood records for the user -->
                                    
                                </div><!-- .mood-data-list -->
                            </div><!-- .mood-chart-count-wrapper -->

                        <?php }else{ // If there are no recorded moods for the user ?>

                            <div class="no-mood-records-wrapper">
                                <div class="no-mood-message">
                                    <p>気分は何も記録していない。</p> <!-- Message indicating that no moods have been recorded -->
                                </div><!-- .no-mood-message -->
                            </div><!-- .no-mood-records-wrapper -->
                        <?php } // End of checking total days condition?>

                    </div><!-- .chart-wrapper -->
                </section><!-- .insights-middle-section -->

                <!-- Wrapper for the right section -->
                <section class="insights-right-section">

                    <!-- Container for heading -->
                    <div class="mood-associations-header insights-header">
                        <h3>気分の関連付け</h3> <!-- Heading for frequently tagged moods -->
                        <a href="mood_associations.php">
                            <p>See All <i class="fa-solid fa-angles-right"></i></p> <!-- Link to view all commonly tagged moods -->
                        </a>
                    </div> 

                    <!-- Wrapper for commonly tagged moods -->
                    <section class="mood-association-wrapper">
                        <div class="mood-association-tab-navigation">

                            <!-- Container for mood tab buttons -->
                            <div class="mood-tabs">
                                <ul> <!-- List of mood tabs -->
                                    <!-- Tab for great mood, active by default -->
                                    <li data-tab-target="#great-mood" class="active tab mood-great">
                                        <div class="navigation-content" id="active-navigation"><p>最高</p></div> <!-- Navigation content for great mood -->
                                    </li>
                                    <!-- Tab for good mood -->
                                    <li data-tab-target="#good-mood" class = "tab mood-good">
                                        <div class="navigation-content"><p>良い</p></div>  <!-- Navigation content for good mood -->
                                    </li>
                                    <!-- Tab for okay mood -->
                                    <li data-tab-target="#okay-mood" class="tab mood-okay">
                                        <div class="navigation-content"><p>普通</p></div> <!-- Navigation content for okay mood -->
                                    </li>
                                    <!-- Tab for bad mood -->
                                    <li data-tab-target="#bad-mood" class="tab mood-bad">
                                        <div class="navigation-content"><p>悪い</p></div> <!-- Navigation content for bad mood -->
                                    </li>
                                    <!-- Tab for awful mood -->
                                    <li data-tab-target="#awful-mood" class="tab mood-awful">
                                        <div class="navigation-content"><p>最悪</p></div> <!-- Tab for awful mood -->
                                    </li>
                                </ul>
                            </div><!-- .buttons -->
                        </div><!-- .mood-association-tab-navigation -->

                        <div class="mood-association-container">

                            <!-- Section for displaying content related to the 'great mood', active by default -->
                            <section id="great-mood" data-tab-content class="active">
                                <?php 
                                // Fetch mood data
                                $getMoods = mysqli_fetch_assoc($resultAllMoods); // Get all mood records
                                $getGreatMood = mysqli_fetch_assoc($resultMoodGreat); // Get data specific to the 'great' mood
                                $mood = MOOD_GREAT; // ID for 'great' mood 
                            
                                // Query to retrieve activities associated with the 'great' mood
                                $queryGetActivities = 
                                    "SELECT ActivityID, TrackingID, count(ActivityID) AS cntTrackAct 
                                    FROM trackactivities 
                                    WHERE TrackingID IN (
                                        SELECT TrackingID 
                                        FROM dailytracking 
                                        WHERE UserID = $userID 
                                        AND TrackingID IN (
                                            SELECT TrackingID 
                                            FROM trackmoods 
                                            WHERE MoodID = $mood
                                        )
                                    ) 
                                    GROUP BY ActivityID 
                                HAVING count(ActivityID) > '" . MINIMUM_TRACK_COUNT . "'";
                                $activitiesResult = mysqli_query($con, $queryGetActivities); // Execute activity query
                                $activitiesCount = mysqli_num_rows($activitiesResult); // Count number of activity records

                                // Query to retrieve companies associated with the 'great mood'
                                $queryGetCompany = 
                                    "SELECT CompanyID, TrackingID, count(CompanyID) AS cntTrackCom 
                                    FROM trackcompany 
                                    WHERE TrackingID IN (
                                        SELECT TrackingID 
                                        FROM dailytracking 
                                        WHERE UserID = $userID 
                                        AND TrackingID IN (
                                            SELECT TrackingID 
                                            FROM trackmoods 
                                            WHERE MoodID = $mood
                                        )
                                    ) 
                                    GROUP BY CompanyID 
                                    HAVING count(CompanyID) > '" . MINIMUM_TRACK_COUNT . "'";
                                $companiesResult = mysqli_query($con, $queryGetCompany); // Execute company query
                                $companiesCount = mysqli_num_rows($companiesResult); // Count number of company records

                                // Query to retrieve locations associated with the 'great mood'
                                $queryGetLocation = 
                                    "SELECT LocationID, TrackingID, count(LocationID) AS cntTrackLoc 
                                    FROM tracklocations 
                                    WHERE TrackingID IN (
                                        SELECT TrackingID 
                                        FROM dailytracking 
                                        WHERE UserID = $userID 
                                        AND TrackingID IN (
                                            SELECT TrackingID 
                                            FROM trackmoods 
                                            WHERE MoodID = $mood
                                        )
                                    ) 
                                    GROUP BY LocationID 
                                    HAVING count(LocationID) > '" . MINIMUM_TRACK_COUNT . "'";
                                $locationsResult = mysqli_query($con, $queryGetLocation); // Execute location query
                                $locationsCount = mysqli_num_rows($locationsResult); // Count number of location records

                                // Query to retrieve foods associated with the 'great mood'
                                $queryGetFood = 
                                    "SELECT FoodID, TrackingID, count(FoodID) AS cntTrackFood 
                                    FROM trackfoods 
                                    WHERE TrackingID IN (
                                        SELECT TrackingID 
                                        FROM dailytracking 
                                        WHERE UserID = $userID 
                                        AND TrackingID IN (
                                            SELECT TrackingID 
                                            FROM trackmoods 
                                            WHERE MoodID = $mood
                                        )
                                    ) 
                                    GROUP BY FoodID 
                                    HAVING count(FoodID) > '" . MINIMUM_TRACK_COUNT . "'";
                                $foodsResult = mysqli_query($con, $queryGetFood); // Execute food query
                                $foodsCount = mysqli_num_rows($foodsResult); // Count number of food records

                                // Query to retrieve weather data associated with the 'great mood'
                                $queryGetWeather = 
                                    "SELECT WeatherID, TrackingID, count(WeatherID) AS cntTrackWea 
                                    FROM trackweather 
                                    WHERE TrackingID IN (
                                        SELECT TrackingID 
                                        FROM dailytracking 
                                        WHERE UserID = $userID 
                                        AND TrackingID IN (
                                            SELECT TrackingID 
                                            FROM trackmoods 
                                            WHERE MoodID = $mood
                                        )
                                    ) 
                                    GROUP BY WeatherID 
                                    HAVING count(WeatherID) > '" . MINIMUM_TRACK_COUNT . "'";
                                $weatherResult= mysqli_query($con, $queryGetWeather); // Execute weather query
                                $weatherCount = mysqli_num_rows($weatherResult); // Count number of weather records

                                // Calculate the total number of tracked activities, companies, locations, foods, and weather entries
                                $totalRows = $activitiesCount + $companiesCount + $locationsCount + $foodsCount + $weatherCount;

                                // Check if there are any entries to display based on the total count across all categories
                                if ($totalRows > 0) { 
                                    // Initialize a counter for displaying entries (resets for each mood section)
                                    $totalEntries = 0; ?> 

                                    <!-- Wrapper for content related to commonly tracked activities associated with the specific mood -->
                                    <div class="mood-<?php echo $mood; ?>">

                                        <!-- Header displaying a message about frequently tagged activities for the current mood -->
                                        <div class="mood-content-wrapper">
                                            <!-- Display the mood name in Japanese within the message -->
                                            <p class="mood-content-intro">
                                                <span>気分が
                                                    <span class="great-mood"><?php echo htmlspecialchars($getGreatMood['JapaneseMoodName']); ?></span>時によくタグしていること
                                                </span>
                                            </p>

                                            <!-- Wrapper for the list of commonly tracked activities associated with the mood -->
                                            <div class="mood-associated-list">
                                                <?php
                                                // Check if there are any activities counted
                                                if ($activitiesCount > 0) {
                                                    // Loop through each activity result
                                                    while ($rowsAct = mysqli_fetch_assoc($activitiesResult)) {
                                                        $totalEntries++; // Increment the total activity count

                                                        // Limit the number of displayed activities to a maximum of 9
                                                        if ($totalEntries > MAX_DISPLAYED_ENTRIES) {
                                                            break; // Exit the loop if the limit is reached
                                                        } ?>

                                                        <div class="mood-associated-item">
                                                            <?php
                                                            $activityID = $rowsAct['ActivityID']; // Get the current activity ID
                                                            
                                                            // Query to get details for the specific activity
                                                            $queryGetActivity = "SELECT * FROM activities WHERE ActivityID = $activityID";
                                                            $resultGetActivity = mysqli_query($con, $queryGetActivity); // Execute the query
                                                            $activityDetails = mysqli_fetch_assoc($resultGetActivity); // Fetch activity details
                                                            ?> 

                                                            <!-- Display the activity icon -->
                                                            <div class="mood-item-icon"><?php echo $activityDetails['ActivityIcon']; ?> </div>
                                                            <!-- Display the activity name -->
                                                            <p class="mood-item-name"><?php echo htmlspecialchars($activityDetails['ActivityName']); ?></p>
                                                            <!-- Display the count of tracked activities -->
                                                            <div class="mood-item-count">
                                                                <?php echo $rowsAct['cntTrackAct'];?> 
                                                            </div>
                                                        </div>
                                                    <?php }
                                                }
                                                
                                                // Check if there are any companies counted
                                                if ($companiesCount > 0) {
                                                    // Loop through each company result
                                                    while ($companyRow = mysqli_fetch_assoc($companiesResult)) {  
                                                        $totalEntries++; // Increment the total company count

                                                        // Limit the number of displayed companies to a maximum of 9
                                                        if ($totalEntries > MAX_DISPLAYED_ENTRIES) {
                                                            break; // Exit the loop if the limit is reached
                                                        } ?>

                                                        <div class="mood-associated-item">
                                                            <?php
                                                            $companyID = $companyRow['CompanyID'];  // Get the current company ID
                                                            
                                                            // Query to get details for the specific company
                                                            $queryGetCompanyDetails = "SELECT * FROM company WHERE CompanyID = $companyID";
                                                            $companiesResultDetails = mysqli_query($con, $queryGetCompanyDetails); // Execute the query
                                                            $companyDetails = mysqli_fetch_assoc($companiesResultDetails); // Fetch company details
                                                            ?>

                                                            <!-- Display the company icon -->
                                                            <div class="mood-item-icon"><?php echo  $companyDetails['CompanyIcon']; ?> </div>
                                                            <!-- Display the company name -->
                                                            <p class="mood-item-name"><?php echo htmlspecialchars($companyDetails['CompanyName']); ?></p>
                                                            <!-- Display the count of tracked companies -->
                                                            <div class="mood-item-count">
                                                                <?php echo $companyRow['cntTrackCom'];?> 
                                                            </div>
                                                        </div>
                                                    <?php }
                                                }

                                                // Check if there are any locations counted
                                                if ($locationsCount > 0) {
                                                    // Loop through each location result
                                                    while ($locationRow = mysqli_fetch_assoc($locationsResult)) {
                                                        $totalEntries++; // Increment the total location count

                                                        // Limit the number of displayed locations to a maximum of 9
                                                        if ($totalEntries > MAX_DISPLAYED_ENTRIES) {
                                                            break; // Exit the loop if the limit is reached
                                                        } ?>

                                                        <div class="mood-associated-item">
                                                            <?php
                                                            $locationID = $locationRow['LocationID']; // Get the current location ID
                                                            
                                                            // Query to get details for the specific location
                                                            $queryGetLocationDetails = "SELECT * FROM locations WHERE LocationID = $locationID";
                                                            $locationsResultDetails = mysqli_query($con, $queryGetLocationDetails); // Execute the query
                                                            $locationDetails = mysqli_fetch_assoc($locationsResultDetails); // Fetch location details
                                                            ?>

                                                            <!-- Display the location icon -->
                                                            <div class="mood-item-icon"><?php echo $locationDetails['LocationIcon']; ?> </div>
                                                            <!-- Display the location name -->
                                                            <p class="mood-item-name"><?php echo htmlspecialchars($locationDetails['LocationName']);  ?></p>
                                                            <!-- Display the count of tracked locations -->
                                                            <div class="mood-item-count">
                                                                <?php echo $locationRow['cntTrackLoc']; ?> 
                                                            </div>
                                                        </div>
                                                    <?php }
                                                }
                                                
                                                // Check if there are any foods counted
                                                if ($foodsCount > 0) {
                                                    // Loop through each food result
                                                    while ($foodRow = mysqli_fetch_assoc($foodsResult)) {
                                                        $totalEntries++; // Increment the total food count

                                                        // Limit the number of displayed foods to a maximum of 9
                                                        if ($totalEntries > MAX_DISPLAYED_ENTRIES) {
                                                            break; // Exit the loop if the limit is reached
                                                        } ?>
                                                        <div class="mood-associated-item">
                                                            <?php
                                                            $foodID = $foodRow['FoodID']; // Get the current food ID
                                                            
                                                            // Query to get details for the specific food
                                                            $queryGetFoodDetails = "SELECT * FROM foods WHERE FoodID = $foodID";
                                                            $foodsResultDetails = mysqli_query($con, $queryGetFoodDetails); // Execute the query
                                                            $foodDetails = mysqli_fetch_assoc($foodsResultDetails); // Fetch food details
                                                            ?>

                                                            <!-- Display the food icon -->
                                                            <div class="mood-item-icon"><?php echo $foodDetails['FoodIcon']; ?> </div>
                                                            <!-- Display the food name -->
                                                            <p class="mood-item-name"><?php echo htmlspecialchars($foodDetails['FoodName']);  ?></p>
                                                            <!-- Display the count of tracked foods -->
                                                            <div class="mood-item-count">
                                                                <?php echo $foodRow['cntTrackFood']; ?> 
                                                            </div>
                                                        </div>
                                                    <?php }
                                                }


                                                // Check if there are any weather records counted
                                                if ($weatherCount > 0) {
                                                    // Loop through each weather result
                                                    while ($weatherRow = mysqli_fetch_assoc($weatherResult)) {
                                                        $totalEntries++; // Increment the total weather count

                                                        // Limit the number of displayed weather entries to a maximum of 9
                                                        if ($totalEntries > MAX_DISPLAYED_ENTRIES) {
                                                            break; // Exit the loop if the limit is reached
                                                        } ?>

                                                        <div class="mood-associated-item">
                                                            <?php
                                                            $weatherID = $weatherRow['WeatherID']; // Get the current weather ID
                                                            
                                                            // Query to get details for the specific weather
                                                            $queryGetWeatherDetails = "SELECT * FROM weather WHERE WeatherID = $weatherID";
                                                            $weatherResultDetails = mysqli_query($con, $queryGetWeatherDetails); // Execute the query
                                                            $weatherDetails = mysqli_fetch_assoc($weatherResultDetails); // Fetch weather details
                                                            ?> 

                                                            <!-- Display the weather icon -->
                                                            <div class="mood-item-icon"><?php echo $weatherDetails['WeatherIcon']; ?> </div>
                                                            <!-- Display the weather name -->
                                                            <p class="mood-item-name"><?php echo htmlspecialchars($weatherDetails['WeatherName']);  ?></p>
                                                            <!-- Display the count of tracked weather -->
                                                            <div class="mood-item-count">
                                                                <?php echo $weatherRow['cntTrackWea']; ?> 
                                                            </div>
                                                        </div><!-- .mood-associated-item -->
                                                    <?php }
                                                } ?>

                                            </div><!-- .mood-associated-list -->
                                        </div><!-- .mood-content-wrapper -->
                                    </div><!-- .mood-content-wrapper -->

                                <?php } else { ?>  
                                
                                <!-- If no records are found, display a message encouraging the user to add tags for better insights -->
                                <div class="no-mood-records-wrapper">
                                    <div class="no-mood-message">
                                        
                                        <!-- Informative text prompting the user to add tags when in the specified mood, to gain insights into activity patterns -->
                                        <p>気分が<b><?php echo htmlspecialchars($getGreatMood['JapaneseMoodName']); ?></b>ときに、よりタグを追加して自分の行動パターンについての洞察を得る。</p>

                                    </div><!-- .no-mood-message -->
                                </div><!-- .no-mood-records-wrapper -->
                                <?php } ?>
                            </section> <!-- #greatMood -->

                            <!-- Section for displaying content related to the 'good mood' -->
                            <section id="good-mood" data-tab-content>
                                <?php 
                                // Fetch mood data
                                $getMoods = mysqli_fetch_assoc($resultAllMoods); // Get all mood records
                                $getMoodsGood = mysqli_fetch_assoc($resultMoodGood); // Get data specific to the 'good' mood
                                $mood = MOOD_GOOD; // ID for 'good' mood
                              
                                // Query to retrieve activities associated with the 'great' mood
                                $queryGetActivities = 
                                    "SELECT ActivityID, TrackingID, count(ActivityID) AS cntTrackAct 
                                    FROM trackactivities 
                                    WHERE TrackingID IN (
                                        SELECT TrackingID 
                                        FROM dailytracking 
                                        WHERE UserID = $userID 
                                        AND TrackingID IN (
                                            SELECT TrackingID 
                                            FROM trackmoods 
                                            WHERE MoodID = $mood
                                        )
                                    ) 
                                    GROUP BY ActivityID 
                                    HAVING count(ActivityID) > '" . MINIMUM_TRACK_COUNT . "'";
                                $activitiesResult = mysqli_query($con, $queryGetActivities); // Execute activity query
                                $activitiesCount = mysqli_num_rows($activitiesResult); // Count number of activity records

                                // Query to retrieve companies associated with the 'great mood'
                                $queryGetCompany = 
                                    "SELECT CompanyID, TrackingID, count(CompanyID) AS cntTrackCom 
                                    FROM trackcompany 
                                    WHERE TrackingID IN (
                                        SELECT TrackingID 
                                        FROM dailytracking 
                                        WHERE UserID = $userID 
                                        AND TrackingID IN (
                                            SELECT TrackingID 
                                            FROM trackmoods 
                                            WHERE MoodID = $mood
                                        )
                                    ) 
                                    GROUP BY CompanyID 
                                    HAVING count(CompanyID) > '" . MINIMUM_TRACK_COUNT . "'";
                                $companiesResult = mysqli_query($con, $queryGetCompany); // Execute company query
                                $companiesCount = mysqli_num_rows($companiesResult); // Count number of company records

                                // Query to retrieve locations associated with the 'great mood'
                                $queryGetLocation = 
                                    "SELECT LocationID, TrackingID, count(LocationID) AS cntTrackLoc 
                                    FROM tracklocations 
                                    WHERE TrackingID IN (
                                        SELECT TrackingID 
                                        FROM dailytracking 
                                        WHERE UserID = $userID 
                                        AND TrackingID IN (
                                            SELECT TrackingID 
                                            FROM trackmoods 
                                            WHERE MoodID = $mood
                                        )
                                    ) 
                                    GROUP BY LocationID 
                                    HAVING count(LocationID) > '" . MINIMUM_TRACK_COUNT . "'";
                                $locationsResult = mysqli_query($con, $queryGetLocation); // Execute location query
                                $locationsCount = mysqli_num_rows($locationsResult); // Count number of location records

                                // Query to retrieve foods associated with the 'great mood'
                                $queryGetFood = 
                                    "SELECT FoodID, TrackingID, count(FoodID) AS cntTrackFood 
                                    FROM trackfoods 
                                    WHERE TrackingID IN (
                                        SELECT TrackingID 
                                        FROM dailytracking 
                                        WHERE UserID = $userID 
                                        AND TrackingID IN (
                                            SELECT TrackingID 
                                            FROM trackmoods 
                                            WHERE MoodID = $mood
                                        )
                                    ) 
                                    GROUP BY FoodID 
                                    HAVING count(FoodID) > '" . MINIMUM_TRACK_COUNT . "'";
                                $foodsResult = mysqli_query($con, $queryGetFood); // Execute food query
                                $foodsCount = mysqli_num_rows($foodsResult); // Count number of food records

                                // Query to retrieve weather data associated with the 'great mood'
                                $queryGetWeather = 
                                    "SELECT WeatherID, TrackingID, count(WeatherID) AS cntTrackWea 
                                    FROM trackweather 
                                    WHERE TrackingID IN (
                                        SELECT TrackingID 
                                        FROM dailytracking 
                                        WHERE UserID = $userID 
                                        AND TrackingID IN (
                                            SELECT TrackingID 
                                            FROM trackmoods WHERE MoodID = $mood
                                        )
                                    ) 
                                    GROUP BY WeatherID 
                                    HAVING count(WeatherID) > '" . MINIMUM_TRACK_COUNT . "'";
                                $weatherResult= mysqli_query($con, $queryGetWeather); // Execute weather query
                                $weatherCount = mysqli_num_rows($weatherResult); // Count number of weather records

                                // Calculate the total number of tracked activities, companies, locations, foods, and weather entries
                                $totalRows = $activitiesCount + $companiesCount + $locationsCount + $foodsCount + $weatherCount;

                                // Check if there are any entries to display based on the total count across all categories
                                if ($totalRows > 0) {  
                                    // Initialize a counter for displaying entries (resets for each mood section)
                                    $totalEntries = 0;?> 

                                    <!-- Wrapper for content related to commonly tracked activities associated with the specific mood -->
                                    <div class="mood-<?php echo $mood; ?>">

                                        <!-- Header displaying a message about frequently tagged activities for the current mood -->
                                        <div class="mood-content-wrapper">
                                            <!-- Display the mood name in Japanese within the message -->
                                            <p class="mood-content-intro">
                                                <span>気分が
                                                    <span class="good-mood"><?php echo htmlspecialchars($getMoodsGood['JapaneseMoodName']); ?></span>時によくタグしていること
                                                </span>
                                            </p>

                                            <div class="mood-associated-list">
                                                <?php
                                                // Check if there are any activities counted
                                                if ($activitiesCount > 0) {
                                                    // Loop through each activity result
                                                    while ($rowsAct = mysqli_fetch_assoc($activitiesResult)) {
                                                        $totalEntries++; // Increment the total activity count

                                                        // Limit the number of displayed activities to a maximum of 9
                                                        if ($totalEntries > MAX_DISPLAYED_ENTRIES) {
                                                            break; // Exit the loop if the limit is reached
                                                        } ?>

                                                        <div class="mood-associated-item">
                                                            <?php
                                                            $activityID = $rowsAct['ActivityID']; // Get the current activity ID
                                                            
                                                            // Query to get details for the specific activity
                                                            $queryGetActivity = "SELECT * FROM activities WHERE ActivityID = $activityID";
                                                            $resultGetActivity = mysqli_query($con, $queryGetActivity); // Execute the query
                                                            $activityDetails = mysqli_fetch_assoc($resultGetActivity); // Fetch activity details
                                                            ?> 

                                                            <!-- Display the activity icon -->
                                                            <div class="mood-item-icon"><?php echo $activityDetails['ActivityIcon']; ?> </div>
                                                            <!-- Display the activity name -->
                                                            <p class="mood-item-name"><?php echo htmlspecialchars($activityDetails['ActivityName']); ?></p>
                                                            <!-- Display the count of tracked activities -->
                                                            <div class="mood-item-count">
                                                                <?php echo $rowsAct['cntTrackAct'];?> 
                                                            </div>
                                                        </div>
                                                    <?php }
                                                }
                                                
                                                // Check if there are any companies counted
                                                if ($companiesCount > 0) {
                                                    // Loop through each company result
                                                    while ($companyRow = mysqli_fetch_assoc($companiesResult)) {  
                                                        $totalEntries++; // Increment the total company count

                                                        // Limit the number of displayed companies to a maximum of 9
                                                        if ($totalEntries > MAX_DISPLAYED_ENTRIES) {
                                                            break; // Exit the loop if the limit is reached
                                                        } ?>

                                                        <div class="mood-associated-item">
                                                            <?php
                                                            $companyID = $companyRow['CompanyID'];  // Get the current company ID
                                                            
                                                            // Query to get details for the specific company
                                                            $queryGetCompanyDetails = "SELECT * FROM company WHERE CompanyID = $companyID";
                                                            $companiesResultDetails = mysqli_query($con, $queryGetCompanyDetails); // Execute the query
                                                            $companyDetails = mysqli_fetch_assoc($companiesResultDetails); // Fetch company details
                                                            ?>

                                                            <!-- Display the company icon -->
                                                            <div class="mood-item-icon"><?php echo  $companyDetails['CompanyIcon']; ?> </div>
                                                            <!-- Display the company name -->
                                                            <p class="mood-item-name"><?php echo htmlspecialchars($companyDetails['CompanyName']); ?></p>
                                                            <!-- Display the count of tracked companies -->
                                                            <div class="mood-item-count">
                                                                <?php echo $companyRow['cntTrackCom'];?> 
                                                            </div>
                                                        </div>
                                                    <?php }
                                                }

                                                // Check if there are any locations counted
                                                if ($locationsCount > 0) {
                                                    // Loop through each location result
                                                    while ($locationRow = mysqli_fetch_assoc($locationsResult)) {
                                                        $totalEntries++; // Increment the total location count

                                                        // Limit the number of displayed locations to a maximum of 9
                                                        if ($totalEntries > MAX_DISPLAYED_ENTRIES) {
                                                            break; // Exit the loop if the limit is reached
                                                        } ?>

                                                        <div class="mood-associated-item">
                                                            <?php
                                                            $locationID = $locationRow['LocationID']; // Get the current location ID
                                                            
                                                            // Query to get details for the specific location
                                                            $queryGetLocationDetails = "SELECT * FROM locations WHERE LocationID = $locationID";
                                                            $locationsResultDetails = mysqli_query($con, $queryGetLocationDetails); // Execute the query
                                                            $locationDetails = mysqli_fetch_assoc($locationsResultDetails); // Fetch location details
                                                            ?>

                                                            <!-- Display the location icon -->
                                                            <div class="mood-item-icon"><?php echo $locationDetails['LocationIcon']; ?> </div>
                                                            <!-- Display the location name -->
                                                            <p class="mood-item-name"><?php echo htmlspecialchars($locationDetails['LocationName']);  ?></p>
                                                            <!-- Display the count of tracked locations -->
                                                            <div class="mood-item-count">
                                                                <?php echo $locationRow['cntTrackLoc']; ?> 
                                                            </div>
                                                        </div>
                                                    <?php }
                                                }
                                                
                                                // Check if there are any foods counted
                                                if ($foodsCount > 0) {
                                                    // Loop through each food result
                                                    while ($foodRow = mysqli_fetch_assoc($foodsResult)) {
                                                        $totalEntries++; // Increment the total food count

                                                        // Limit the number of displayed foods to a maximum of 9
                                                        if ($totalEntries > MAX_DISPLAYED_ENTRIES) {
                                                            break; // Exit the loop if the limit is reached
                                                        } ?>

                                                        <div class="mood-associated-item">
                                                            <?php
                                                            $foodID = $foodRow['FoodID']; // Get the current food ID
                                                            
                                                            // Query to get details for the specific food
                                                            $queryGetFoodDetails = "SELECT * FROM foods WHERE FoodID = $foodID";
                                                            $foodsResultDetails = mysqli_query($con, $queryGetFoodDetails); // Execute the query
                                                            $foodDetails = mysqli_fetch_assoc($foodsResultDetails); // Fetch food details
                                                            ?>

                                                            <!-- Display the food icon -->
                                                            <div class="mood-item-icon"><?php echo $foodDetails['FoodIcon']; ?> </div>
                                                            <!-- Display the food name -->
                                                            <p class="mood-item-name"><?php echo htmlspecialchars($foodDetails['FoodName']);  ?></p>
                                                            <!-- Display the count of tracked foods -->
                                                            <div class="mood-item-count">
                                                                <?php echo $foodRow['cntTrackFood']; ?> 
                                                            </div>
                                                        </div>
                                                    <?php }
                                                }


                                                // Check if there are any weather records counted
                                                if ($weatherCount > 0) {
                                                    // Loop through each weather result
                                                    while ($weatherRow = mysqli_fetch_assoc($weatherResult)) {
                                                        $totalEntries++; // Increment the total weather count

                                                        // Limit the number of displayed weather entries to a maximum of 9
                                                        if ($totalEntries > MAX_DISPLAYED_ENTRIES) {
                                                            break; // Exit the loop if the limit is reached
                                                        } ?>

                                                        <div class="mood-associated-item">
                                                            <?php
                                                            $weatherID = $weatherRow['WeatherID']; // Get the current weather ID
                                                            
                                                            // Query to get details for the specific weather
                                                            $queryGetWeatherDetails = "SELECT * FROM weather WHERE WeatherID = $weatherID";
                                                            $weatherResultDetails = mysqli_query($con, $queryGetWeatherDetails); // Execute the query
                                                            $weatherDetails = mysqli_fetch_assoc($weatherResultDetails); // Fetch weather details
                                                            ?> 

                                                            <!-- Display the weather icon -->
                                                            <div class="mood-item-icon"><?php echo $weatherDetails['WeatherIcon']; ?> </div>
                                                            <!-- Display the weather name -->
                                                            <p class="mood-item-name"><?php echo htmlspecialchars($weatherDetails['WeatherName']);  ?></p>
                                                            <!-- Display the count of tracked weather -->
                                                            <div class="mood-item-count">
                                                                <?php echo $weatherRow['cntTrackWea']; ?> 
                                                            </div>
                                                        </div><!-- .mood-associated-item -->
                                                    <?php }
                                                } ?>

                                            </div><!-- .mood-associated-list -->
                                        </div><!-- .mood-content-wrapper -->
                                    </div><!-- .mood-content-wrapper -->

                                <?php } else { ?> 

                                <!-- If no records are found, display a message encouraging the user to add tags for better insights -->
                                <div class="no-mood-records-wrapper">
                                    <div class="no-mood-message">

                                        <!-- Informative text prompting the user to add tags when in the specified mood, to gain insights into activity patterns -->
                                        <p>気分が<b><?php echo htmlspecialchars($getMoodsGood['JapaneseMoodName']); ?></b>ときに、よりタグを追加して自分の行動パターンについての洞察を得る。</p>

                                    </div><!-- .no-mood-message -->
                                </div><!-- .no-mood-records-wrapper -->
                                <?php } ?>
                            </section> <!-- #good-mood -->

                            <!-- Section for displaying content related to the 'okay mood', active by default -->
                            <section id="okay-mood" data-tab-content>
                                <?php 
                                // Fetch mood data
                                $getMoods = mysqli_fetch_assoc($resultAllMoods); // Get all mood records
                                $getMoodsOkay = mysqli_fetch_assoc($resultMoodOkay); // Get data specific to the 'okay' mood
                                $mood = MOOD_OKAY; // ID for 'okay' mood
                            
                                // Query to retrieve activities associated with the 'great' mood
                                $queryGetActivities = 
                                    "SELECT ActivityID, TrackingID, count(ActivityID) AS cntTrackAct 
                                    FROM trackactivities 
                                    WHERE TrackingID IN (
                                        SELECT TrackingID 
                                        FROM dailytracking 
                                        WHERE UserID = $userID 
                                        AND TrackingID IN (
                                            SELECT TrackingID 
                                            FROM trackmoods 
                                            WHERE MoodID = $mood
                                        )
                                    ) 
                                    GROUP BY ActivityID 
                                    HAVING count(ActivityID) > '" . MINIMUM_TRACK_COUNT . "'";
                                $activitiesResult = mysqli_query($con, $queryGetActivities); // Execute activity query
                                $activitiesCount = mysqli_num_rows($activitiesResult); // Count number of activity records

                                // Query to retrieve companies associated with the 'great mood'
                                $queryGetCompany = 
                                    "SELECT CompanyID, TrackingID, count(CompanyID) AS cntTrackCom 
                                    FROM trackcompany 
                                    WHERE TrackingID IN (
                                        SELECT TrackingID 
                                        FROM dailytracking 
                                        WHERE UserID = $userID 
                                        AND TrackingID IN (
                                            SELECT TrackingID 
                                            FROM trackmoods 
                                            WHERE MoodID = $mood
                                        )
                                    ) 
                                    GROUP BY CompanyID 
                                    HAVING count(CompanyID) > '" . MINIMUM_TRACK_COUNT . "'";
                                $companiesResult = mysqli_query($con, $queryGetCompany); // Execute company query
                                $companiesCount = mysqli_num_rows($companiesResult); // Count number of company records

                                // Query to retrieve locations associated with the 'great mood'
                                $queryGetLocation = 
                                    "SELECT LocationID, TrackingID, count(LocationID) AS cntTrackLoc 
                                    FROM tracklocations 
                                    WHERE TrackingID IN (
                                        SELECT TrackingID 
                                        FROM dailytracking 
                                        WHERE UserID = $userID 
                                        AND TrackingID IN (
                                            SELECT TrackingID 
                                            FROM trackmoods 
                                            WHERE MoodID = $mood
                                        )
                                    ) 
                                    GROUP BY LocationID 
                                    HAVING count(LocationID) > '" . MINIMUM_TRACK_COUNT . "'";
                                $locationsResult = mysqli_query($con, $queryGetLocation); // Execute location query
                                $locationsCount = mysqli_num_rows($locationsResult); // Count number of location records

                                // Query to retrieve foods associated with the 'great mood'
                                $queryGetFood = 
                                    "SELECT FoodID, TrackingID, count(FoodID) AS cntTrackFood 
                                    FROM trackfoods 
                                    WHERE TrackingID IN (
                                        SELECT TrackingID 
                                        FROM dailytracking 
                                        WHERE UserID = $userID 
                                        AND TrackingID IN (
                                            SELECT TrackingID 
                                            FROM trackmoods 
                                            WHERE MoodID = $mood
                                        )
                                    ) 
                                    GROUP BY FoodID 
                                    HAVING count(FoodID) > '" . MINIMUM_TRACK_COUNT . "'";
                                $foodsResult = mysqli_query($con, $queryGetFood); // Execute food query
                                $foodsCount = mysqli_num_rows($foodsResult); // Count number of food records

                                // Query to retrieve weather data associated with the 'great mood'
                                $queryGetWeather = 
                                    "SELECT WeatherID, TrackingID, count(WeatherID) AS cntTrackWea 
                                    FROM trackweather 
                                    WHERE TrackingID IN (
                                        SELECT TrackingID 
                                        FROM dailytracking 
                                        WHERE UserID = $userID 
                                        AND TrackingID IN (
                                            SELECT TrackingID 
                                            FROM trackmoods 
                                            WHERE MoodID = $mood
                                        )
                                    ) 
                                    GROUP BY WeatherID 
                                    HAVING count(WeatherID) > '" . MINIMUM_TRACK_COUNT . "'";
                                $weatherResult= mysqli_query($con, $queryGetWeather); // Execute weather query
                                $weatherCount = mysqli_num_rows($weatherResult); // Count number of weather records

                                // Calculate the total number of tracked activities, companies, locations, foods, and weather entries
                                $totalRows = $activitiesCount + $companiesCount + $locationsCount + $foodsCount + $weatherCount;

                                // Check if there are any entries to display based on the total count across all categories
                                if ($totalRows > 0) { 
                                    // Initialize a counter for displaying entries (resets for each mood section)
                                    $totalEntries = 0; ?> 
                                    
                                    <!-- Wrapper for content related to commonly tracked activities associated with the specific mood -->
                                    <div class="mood-<?php echo $mood; ?>">

                                        <!-- Header displaying a message about frequently tagged activities for the current mood -->
                                        <div class="mood-content-wrapper">
                                            <!-- Display the mood name in Japanese within the message -->
                                            <p class="mood-content-intro">
                                                <span>気分が
                                                    <span class="okay-mood"><?php echo htmlspecialchars($getMoodsOkay['JapaneseMoodName']); ?></span>時によくタグしていること
                                                </span>
                                            </p>
                                            
                                            <!-- Wrapper for the list of commonly tracked activities associated with the mood -->
                                            <div class="mood-associated-list">
                                                <?php
                                                // Check if there are any activities counted
                                                if ($activitiesCount > 0) {
                                                    // Loop through each activity result
                                                    while ($rowsAct = mysqli_fetch_assoc($activitiesResult)) {
                                                        $totalEntries++; // Increment the total activity count

                                                        // Limit the number of displayed activities to a maximum of 9
                                                        if ($totalEntries > MAX_DISPLAYED_ENTRIES) {
                                                            break; // Exit the loop if the limit is reached
                                                        } ?>

                                                        <div class="mood-associated-item">
                                                            <?php
                                                            $activityID = $rowsAct['ActivityID']; // Get the current activity ID
                                                            
                                                            // Query to get details for the specific activity
                                                            $queryGetActivity = "SELECT * FROM activities WHERE ActivityID = $activityID";
                                                            $resultGetActivity = mysqli_query($con, $queryGetActivity); // Execute the query
                                                            $activityDetails = mysqli_fetch_assoc($resultGetActivity); // Fetch activity details
                                                            ?> 

                                                            <!-- Display the activity icon -->
                                                            <div class="mood-item-icon"><?php echo $activityDetails['ActivityIcon']; ?> </div>
                                                            <!-- Display the activity name -->
                                                            <p class="mood-item-name"><?php echo htmlspecialchars($activityDetails['ActivityName']); ?></p>
                                                            <!-- Display the count of tracked activities -->
                                                            <div class="mood-item-count">
                                                                <?php echo $rowsAct['cntTrackAct'];?> 
                                                            </div>
                                                        </div>
                                                    <?php }
                                                }
                                                
                                                // Check if there are any companies counted
                                                if ($companiesCount > 0) {
                                                    // Loop through each company result
                                                    while ($companyRow = mysqli_fetch_assoc($companiesResult)) {  
                                                        $totalEntries++; // Increment the total company count

                                                        // Limit the number of displayed companies to a maximum of 9
                                                        if ($totalEntries > MAX_DISPLAYED_ENTRIES) {
                                                            break; // Exit the loop if the limit is reached
                                                        } ?>

                                                        <div class="mood-associated-item">
                                                            <?php
                                                            $companyID = $companyRow['CompanyID'];  // Get the current company ID
                                                            
                                                            // Query to get details for the specific company
                                                            $queryGetCompanyDetails = "SELECT * FROM company WHERE CompanyID = $companyID";
                                                            $companiesResultDetails = mysqli_query($con, $queryGetCompanyDetails); // Execute the query
                                                            $companyDetails = mysqli_fetch_assoc($companiesResultDetails); // Fetch company details
                                                            ?>

                                                            <!-- Display the company icon -->
                                                            <div class="mood-item-icon"><?php echo  $companyDetails['CompanyIcon']; ?> </div>
                                                            <!-- Display the company name -->
                                                            <p class="mood-item-name"><?php echo htmlspecialchars($companyDetails['CompanyName']); ?></p>
                                                            <!-- Display the count of tracked companies -->
                                                            <div class="mood-item-count">
                                                                <?php echo $companyRow['cntTrackCom'];?> 
                                                            </div>
                                                        </div>
                                                    <?php }
                                                }

                                                // Check if there are any locations counted
                                                if ($locationsCount > 0) {
                                                    // Loop through each location result
                                                    while ($locationRow = mysqli_fetch_assoc($locationsResult)) {
                                                        $totalEntries++; // Increment the total location count

                                                        // Limit the number of displayed locations to a maximum of 9
                                                        if ($totalEntries > MAX_DISPLAYED_ENTRIES) {
                                                            break; // Exit the loop if the limit is reached
                                                        } ?>

                                                        <div class="mood-associated-item">
                                                            <?php
                                                            $locationID = $locationRow['LocationID']; // Get the current location ID
                                                            
                                                            // Query to get details for the specific location
                                                            $queryGetLocationDetails = "SELECT * FROM locations WHERE LocationID = $locationID";
                                                            $locationsResultDetails = mysqli_query($con, $queryGetLocationDetails); // Execute the query
                                                            $locationDetails = mysqli_fetch_assoc($locationsResultDetails); // Fetch location details
                                                            ?>

                                                            <!-- Display the location icon -->
                                                            <div class="mood-item-icon"><?php echo $locationDetails['LocationIcon']; ?> </div>
                                                            <!-- Display the location name -->
                                                            <p class="mood-item-name"><?php echo htmlspecialchars($locationDetails['LocationName']);  ?></p>
                                                            <!-- Display the count of tracked locations -->
                                                            <div class="mood-item-count">
                                                                <?php echo $locationRow['cntTrackLoc']; ?> 
                                                            </div>
                                                        </div>
                                                    <?php }
                                                }
                                                
                                                // Check if there are any foods counted
                                                if ($foodsCount > 0) {
                                                    // Loop through each food result
                                                    while ($foodRow = mysqli_fetch_assoc($foodsResult)) {
                                                        $totalEntries++; // Increment the total food count

                                                        // Limit the number of displayed foods to a maximum of 9
                                                        if ($totalEntries > MAX_DISPLAYED_ENTRIES) {
                                                            break; // Exit the loop if the limit is reached
                                                        } ?>
                                                        <div class="mood-associated-item">
                                                            <?php
                                                            $foodID = $foodRow['FoodID']; // Get the current food ID
                                                            
                                                            // Query to get details for the specific food
                                                            $queryGetFoodDetails = "SELECT * FROM foods WHERE FoodID = $foodID";
                                                            $foodsResultDetails = mysqli_query($con, $queryGetFoodDetails); // Execute the query
                                                            $foodDetails = mysqli_fetch_assoc($foodsResultDetails); // Fetch food details
                                                            ?>

                                                            <!-- Display the food icon -->
                                                            <div class="mood-item-icon"><?php echo $foodDetails['FoodIcon']; ?> </div>
                                                            <!-- Display the food name -->
                                                            <p class="mood-item-name"><?php echo htmlspecialchars($foodDetails['FoodName']);  ?></p>
                                                            <!-- Display the count of tracked foods -->
                                                            <div class="mood-item-count">
                                                                <?php echo $foodRow['cntTrackFood']; ?> 
                                                            </div>
                                                        </div>
                                                    <?php }
                                                }


                                                // Check if there are any weather records counted
                                                if ($weatherCount > 0) {
                                                    // Loop through each weather result
                                                    while ($weatherRow = mysqli_fetch_assoc($weatherResult)) {
                                                        $totalEntries++; // Increment the total weather count

                                                        // Limit the number of displayed weather entries to a maximum of 9
                                                        if ($totalEntries > MAX_DISPLAYED_ENTRIES) {
                                                            break; // Exit the loop if the limit is reached
                                                        } ?>

                                                        <div class="mood-associated-item">
                                                            <?php
                                                            $weatherID = $weatherRow['WeatherID']; // Get the current weather ID
                                                            
                                                            // Query to get details for the specific weather
                                                            $queryGetWeatherDetails = "SELECT * FROM weather WHERE WeatherID = $weatherID";
                                                            $weatherResultDetails = mysqli_query($con, $queryGetWeatherDetails); // Execute the query
                                                            $weatherDetails = mysqli_fetch_assoc($weatherResultDetails); // Fetch weather details
                                                            ?> 

                                                            <!-- Display the weather icon -->
                                                            <div class="mood-item-icon"><?php echo $weatherDetails['WeatherIcon']; ?> </div>
                                                            <!-- Display the weather name -->
                                                            <p class="mood-item-name"><?php echo htmlspecialchars($weatherDetails['WeatherName']);  ?></p>
                                                            <!-- Display the count of tracked weather -->
                                                            <div class="mood-item-count">
                                                                <?php echo $weatherRow['cntTrackWea']; ?> 
                                                            </div>
                                                        </div><!-- .mood-associated-item -->
                                                    <?php }
                                                } ?>

                                            </div><!-- .mood-associated-list -->
                                        </div><!-- .mood-content-wrapper -->
                                    </div><!-- .mood-content-wrapper -->

                                <?php } else { ?> 

                                <!-- If no records are found, display a message encouraging the user to add tags for better insights -->
                                <div class="no-mood-records-wrapper">
                                    <div class="no-mood-message">

                                        <!-- Informative text prompting the user to add tags when in the specified mood, to gain insights into activity patterns -->
                                        <p>気分が<b><?php echo htmlspecialchars($getMoodsOkay['JapaneseMoodName']); ?></b>ときに、よりタグを追加して自分の行動パターンについての洞察を得る。</p>

                                    </div><!-- .no-mood-message -->
                                </div><!-- .no-mood-records-wrapper -->
                                <?php } ?>
                            </section> <!-- #okay-mood -->

                            <!-- Section for displaying content related to the 'bad mood', active by default -->
                            <section id="bad-mood" data-tab-content>
                                <?php 
                                // Fetch mood data
                                $getMoods = mysqli_fetch_assoc($resultAllMoods); // Get all mood records
                                $getMoodsBad = mysqli_fetch_assoc($resultMoodBad); // Get data specific to the 'bad' mood
                                $mood = MOOD_BAD; // ID for 'bad' mood
                            
                                // Query to retrieve activities associated with the 'great' mood
                                $queryGetActivities = 
                                    "SELECT ActivityID, TrackingID, count(ActivityID) AS cntTrackAct 
                                    FROM trackactivities 
                                    WHERE TrackingID IN (
                                        SELECT TrackingID 
                                        FROM dailytracking 
                                        WHERE UserID = $userID 
                                        AND TrackingID IN (
                                            SELECT TrackingID 
                                            FROM trackmoods 
                                            WHERE MoodID = $mood
                                        )
                                    ) 
                                    GROUP BY ActivityID 
                                    HAVING count(ActivityID) > '" . MINIMUM_TRACK_COUNT . "'";
                                $activitiesResult = mysqli_query($con, $queryGetActivities); // Execute activity query
                                $activitiesCount = mysqli_num_rows($activitiesResult); // Count number of activity records

                                // Query to retrieve companies associated with the 'great mood'
                                $queryGetCompany = 
                                    "SELECT CompanyID, TrackingID, count(CompanyID) AS cntTrackCom 
                                    FROM trackcompany 
                                    WHERE TrackingID IN (
                                        SELECT TrackingID 
                                        FROM dailytracking 
                                        WHERE UserID = $userID 
                                        AND TrackingID IN (
                                            SELECT TrackingID 
                                            FROM trackmoods 
                                            WHERE MoodID = $mood
                                        )
                                    ) 
                                    GROUP BY CompanyID 
                                    HAVING count(CompanyID) > '" . MINIMUM_TRACK_COUNT . "'";
                                $companiesResult = mysqli_query($con, $queryGetCompany); // Execute company query
                                $companiesCount = mysqli_num_rows($companiesResult); // Count number of company records

                                // Query to retrieve locations associated with the 'great mood'
                                $queryGetLocation = 
                                    "SELECT LocationID, TrackingID, count(LocationID) AS cntTrackLoc 
                                    FROM tracklocations 
                                    WHERE TrackingID IN (
                                        SELECT TrackingID 
                                        FROM dailytracking 
                                        WHERE UserID = $userID 
                                        AND TrackingID IN (
                                            SELECT TrackingID 
                                            FROM trackmoods 
                                            WHERE MoodID = $mood
                                        )
                                    ) 
                                    GROUP BY LocationID 
                                    HAVING count(LocationID) > '" . MINIMUM_TRACK_COUNT . "'";
                                $locationsResult = mysqli_query($con, $queryGetLocation); // Execute location query
                                $locationsCount = mysqli_num_rows($locationsResult); // Count number of location records

                                // Query to retrieve foods associated with the 'great mood'
                                $queryGetFood = 
                                    "SELECT FoodID, TrackingID, count(FoodID) AS cntTrackFood 
                                    FROM trackfoods 
                                    WHERE TrackingID IN (
                                        SELECT TrackingID 
                                        FROM dailytracking 
                                        WHERE UserID = $userID 
                                        AND TrackingID IN (
                                            SELECT TrackingID 
                                            FROM trackmoods 
                                            WHERE MoodID = $mood
                                        )
                                    ) 
                                    GROUP BY FoodID 
                                    HAVING count(FoodID) > '" . MINIMUM_TRACK_COUNT . "'";
                                $foodsResult = mysqli_query($con, $queryGetFood); // Execute food query
                                $foodsCount = mysqli_num_rows($foodsResult); // Count number of food records

                                // Query to retrieve weather data associated with the 'great mood'
                                $queryGetWeather = 
                                    "SELECT WeatherID, TrackingID, count(WeatherID) AS cntTrackWea 
                                    FROM trackweather 
                                    WHERE TrackingID IN (
                                        SELECT TrackingID 
                                        FROM dailytracking 
                                        WHERE UserID = $userID 
                                        AND TrackingID IN (
                                            SELECT TrackingID 
                                            FROM trackmoods 
                                            WHERE MoodID = $mood
                                        )
                                    ) 
                                    GROUP BY WeatherID 
                                    HAVING count(WeatherID) > '" . MINIMUM_TRACK_COUNT . "'";
                                $weatherResult= mysqli_query($con, $queryGetWeather); // Execute weather query
                                $weatherCount = mysqli_num_rows($weatherResult); // Count number of weather records

                                // Calculate the total number of tracked activities, companies, locations, foods, and weather entries
                                $totalRows = $activitiesCount + $companiesCount + $locationsCount + $foodsCount + $weatherCount;

                                // Check if there are any entries to display based on the total count across all categories
                                if ($totalRows > 0) { 
                                    // Initialize a counter for displaying entries (resets for each mood section)
                                    $totalEntries = 0 ?> 

                                    <!-- Wrapper for content related to commonly tracked activities associated with the specific mood -->
                                    <div class="mood-<?php echo $mood; ?>">

                                        <!-- Header displaying a message about frequently tagged activities for the current mood -->
                                        <div class="mood-content-wrapper">

                                            <!-- Display the mood name in Japanese within the message -->
                                            <p class="mood-content-intro">
                                                <span>気分が
                                                    <span class="bad-mood"><?php echo htmlspecialchars($getMoodsBad['JapaneseMoodName']); ?></span>時によくタグしていること
                                                </span>
                                            </p>

                                            <!-- Wrapper for the list of commonly tracked activities associated with the mood -->
                                            <div class="mood-associated-list">
                                                <?php
                                                // Check if there are any activities counted
                                                if ($activitiesCount > 0) {
                                                    // Loop through each activity result
                                                    while ($rowsAct = mysqli_fetch_assoc($activitiesResult)) {
                                                        $totalEntries++; // Increment the total activity count

                                                        // Limit the number of displayed activities to a maximum of 9
                                                        if ($totalEntries > MAX_DISPLAYED_ENTRIES) {
                                                            break; // Exit the loop if the limit is reached
                                                        } ?>

                                                        <div class="mood-associated-item">
                                                            <?php
                                                            $activityID = $rowsAct['ActivityID']; // Get the current activity ID
                                                            
                                                            // Query to get details for the specific activity
                                                            $queryGetActivity = "SELECT * FROM activities WHERE ActivityID = $activityID";
                                                            $resultGetActivity = mysqli_query($con, $queryGetActivity); // Execute the query
                                                            $activityDetails = mysqli_fetch_assoc($resultGetActivity); // Fetch activity details
                                                            ?> 

                                                            <!-- Display the activity icon -->
                                                            <div class="mood-item-icon"><?php echo $activityDetails['ActivityIcon']; ?> </div>
                                                            <!-- Display the activity name -->
                                                            <p class="mood-item-name"><?php echo htmlspecialchars($activityDetails['ActivityName']); ?></p>
                                                            <!-- Display the count of tracked activities -->
                                                            <div class="mood-item-count">
                                                                <?php echo $rowsAct['cntTrackAct'];?> 
                                                            </div>
                                                        </div>
                                                    <?php }
                                                }
                                                
                                                // Check if there are any companies counted
                                                if ($companiesCount > 0) {
                                                    // Loop through each company result
                                                    while ($companyRow = mysqli_fetch_assoc($companiesResult)) {  
                                                        $totalEntries++; // Increment the total company count

                                                        // Limit the number of displayed companies to a maximum of 9
                                                        if ($totalEntries > MAX_DISPLAYED_ENTRIES) {
                                                            break; // Exit the loop if the limit is reached
                                                        } ?>

                                                        <div class="mood-associated-item">
                                                            <?php
                                                            $companyID = $companyRow['CompanyID'];  // Get the current company ID
                                                            
                                                            // Query to get details for the specific company
                                                            $queryGetCompanyDetails = "SELECT * FROM company WHERE CompanyID = $companyID";
                                                            $companiesResultDetails = mysqli_query($con, $queryGetCompanyDetails); // Execute the query
                                                            $companyDetails = mysqli_fetch_assoc($companiesResultDetails); // Fetch company details
                                                            ?>

                                                            <!-- Display the company icon -->
                                                            <div class="mood-item-icon"><?php echo  $companyDetails['CompanyIcon']; ?> </div>
                                                            <!-- Display the company name -->
                                                            <p class="mood-item-name"><?php echo htmlspecialchars($companyDetails['CompanyName']); ?></p>
                                                            <!-- Display the count of tracked companies -->
                                                            <div class="mood-item-count">
                                                                <?php echo $companyRow['cntTrackCom'];?> 
                                                            </div>
                                                        </div>
                                                    <?php }
                                                }

                                                // Check if there are any locations counted
                                                if ($locationsCount > 0) {
                                                    // Loop through each location result
                                                    while ($locationRow = mysqli_fetch_assoc($locationsResult)) {
                                                        $totalEntries++; // Increment the total location count

                                                        // Limit the number of displayed locations to a maximum of 9
                                                        if ($totalEntries > MAX_DISPLAYED_ENTRIES) {
                                                            break; // Exit the loop if the limit is reached
                                                        } ?>

                                                        <div class="mood-associated-item">
                                                            <?php
                                                            $locationID = $locationRow['LocationID']; // Get the current location ID
                                                            
                                                            // Query to get details for the specific location
                                                            $queryGetLocationDetails = "SELECT * FROM locations WHERE LocationID = $locationID";
                                                            $locationsResultDetails = mysqli_query($con, $queryGetLocationDetails); // Execute the query
                                                            $locationDetails = mysqli_fetch_assoc($locationsResultDetails); // Fetch location details
                                                            ?>

                                                            <!-- Display the location icon -->
                                                            <div class="mood-item-icon"><?php echo $locationDetails['LocationIcon']; ?> </div>
                                                            <!-- Display the location name -->
                                                            <p class="mood-item-name"><?php echo htmlspecialchars($locationDetails['LocationName']);  ?></p>
                                                            <!-- Display the count of tracked locations -->
                                                            <div class="mood-item-count">
                                                                <?php echo $locationRow['cntTrackLoc']; ?> 
                                                            </div>
                                                        </div>
                                                    <?php }
                                                }
                                                
                                                // Check if there are any foods counted
                                                if ($foodsCount > 0) {
                                                    // Loop through each food result
                                                    while ($foodRow = mysqli_fetch_assoc($foodsResult)) {
                                                        $totalEntries++; // Increment the total food count

                                                        // Limit the number of displayed foods to a maximum of 9
                                                        if ($totalEntries > MAX_DISPLAYED_ENTRIES) {
                                                            break; // Exit the loop if the limit is reached
                                                        } ?>
                                                        <div class="mood-associated-item">
                                                            <?php
                                                            $foodID = $foodRow['FoodID']; // Get the current food ID
                                                            
                                                            // Query to get details for the specific food
                                                            $queryGetFoodDetails = "SELECT * FROM foods WHERE FoodID = $foodID";
                                                            $foodsResultDetails = mysqli_query($con, $queryGetFoodDetails); // Execute the query
                                                            $foodDetails = mysqli_fetch_assoc($foodsResultDetails); // Fetch food details
                                                            ?>

                                                            <!-- Display the food icon -->
                                                            <div class="mood-item-icon"><?php echo $foodDetails['FoodIcon']; ?> </div>
                                                            <!-- Display the food name -->
                                                            <p class="mood-item-name"><?php echo htmlspecialchars($foodDetails['FoodName']);  ?></p>
                                                            <!-- Display the count of tracked foods -->
                                                            <div class="mood-item-count">
                                                                <?php echo $foodRow['cntTrackFood']; ?> 
                                                            </div>
                                                        </div>
                                                    <?php }
                                                }


                                                // Check if there are any weather records counted
                                                if ($weatherCount > 0) {
                                                    // Loop through each weather result
                                                    while ($weatherRow = mysqli_fetch_assoc($weatherResult)) {
                                                        $totalEntries++; // Increment the total weather count

                                                        // Limit the number of displayed weather entries to a maximum of 9
                                                        if ($totalEntries > MAX_DISPLAYED_ENTRIES) {
                                                            break; // Exit the loop if the limit is reached
                                                        } ?>

                                                        <div class="mood-associated-item">
                                                            <?php
                                                            $weatherID = $weatherRow['WeatherID']; // Get the current weather ID
                                                            
                                                            // Query to get details for the specific weather
                                                            $queryGetWeatherDetails = "SELECT * FROM weather WHERE WeatherID = $weatherID";
                                                            $weatherResultDetails = mysqli_query($con, $queryGetWeatherDetails); // Execute the query
                                                            $weatherDetails = mysqli_fetch_assoc($weatherResultDetails); // Fetch weather details
                                                            ?> 

                                                            <!-- Display the weather icon -->
                                                            <div class="mood-item-icon"><?php echo $weatherDetails['WeatherIcon']; ?> </div>
                                                            <!-- Display the weather name -->
                                                            <p class="mood-item-name"><?php echo htmlspecialchars($weatherDetails['WeatherName']);  ?></p>
                                                            <!-- Display the count of tracked weather -->
                                                            <div class="mood-item-count">
                                                                <?php echo $weatherRow['cntTrackWea']; ?> 
                                                            </div>
                                                        </div><!-- .mood-associated-item -->
                                                    <?php }
                                                } ?>

                                            </div><!-- .mood-associated-list -->
                                        </div><!-- .mood-content-wrapper -->
                                    </div><!-- .mood-content-wrapper -->

                                <?php } else { ?> 

                                <!-- If no records are found, display a message encouraging the user to add tags for better insights -->
                                <div class="no-mood-records-wrapper">

                                    <!-- Informative text prompting the user to add tags when in the specified mood, to gain insights into activity patterns -->
                                    <p>気分が<b><?php echo htmlspecialchars($getMoodsBad['JapaneseMoodName']); ?></b>ときに、よりタグを追加して自分の行動パターンについての洞察を得る。</p>

                                </div><!-- .no-mood-records-wrapper -->
                                <?php } ?>
                            </section> <!-- #bad-mood -->

                            <!-- Section for displaying content related to the 'bad mood', active by default -->
                            <section id="awful-mood" data-tab-content>
                                <?php 
                                // Fetch mood data
                                $getMoods = mysqli_fetch_assoc($resultAllMoods); // Get all mood records
                                $getMoodsAwful = mysqli_fetch_assoc($resultMoodAwful); // Get data specific to the 'awful' mood
                                $mood = MOOD_AWFUL; // ID for 'awful' mood
                            
                                // Query to retrieve activities associated with the 'great' mood
                                $queryGetActivities = 
                                    "SELECT ActivityID, TrackingID, count(ActivityID) AS cntTrackAct 
                                    FROM trackactivities 
                                    WHERE TrackingID IN (
                                        SELECT TrackingID 
                                        FROM dailytracking 
                                        WHERE UserID = $userID 
                                        AND TrackingID IN (
                                            SELECT TrackingID 
                                            FROM trackmoods 
                                            WHERE MoodID = $mood
                                        )
                                    ) 
                                    GROUP BY ActivityID 
                                    HAVING count(ActivityID) > '" . MINIMUM_TRACK_COUNT . "'";
                                $activitiesResult = mysqli_query($con, $queryGetActivities); // Execute activity query
                                $activitiesCount = mysqli_num_rows($activitiesResult); // Count number of activity records

                                // Query to retrieve companies associated with the 'great mood'
                                $queryGetCompany = 
                                    "SELECT CompanyID, TrackingID, count(CompanyID) AS cntTrackCom 
                                    FROM trackcompany 
                                    WHERE TrackingID IN (
                                        SELECT TrackingID 
                                        FROM dailytracking 
                                        WHERE UserID = $userID 
                                        AND TrackingID IN (
                                            SELECT TrackingID 
                                            FROM trackmoods 
                                            WHERE MoodID = $mood
                                        )
                                    ) 
                                    GROUP BY CompanyID 
                                    HAVING count(CompanyID) > '" . MINIMUM_TRACK_COUNT . "'";
                                $companiesResult = mysqli_query($con, $queryGetCompany); // Execute company query
                                $companiesCount = mysqli_num_rows($companiesResult); // Count number of company records

                                // Query to retrieve locations associated with the 'great mood'
                                $queryGetLocation = 
                                    "SELECT LocationID, TrackingID, count(LocationID) AS cntTrackLoc 
                                    FROM tracklocations 
                                    WHERE TrackingID IN (
                                        SELECT TrackingID 
                                        FROM dailytracking 
                                        WHERE UserID = $userID 
                                        AND TrackingID IN (
                                            SELECT TrackingID 
                                            FROM trackmoods 
                                            WHERE MoodID = $mood
                                        )
                                    ) 
                                    GROUP BY LocationID 
                                    HAVING count(LocationID) > '" . MINIMUM_TRACK_COUNT . "'";
                                $locationsResult = mysqli_query($con, $queryGetLocation); // Execute location query
                                $locationsCount = mysqli_num_rows($locationsResult); // Count number of location records

                                // Query to retrieve foods associated with the 'great mood'
                                $queryGetFood = 
                                    "SELECT FoodID, TrackingID, count(FoodID) AS cntTrackFood 
                                    FROM trackfoods 
                                    WHERE TrackingID IN (
                                        SELECT TrackingID 
                                        FROM dailytracking 
                                        WHERE UserID = $userID 
                                        AND TrackingID IN (
                                            SELECT TrackingID 
                                            FROM trackmoods 
                                            WHERE MoodID = $mood
                                        )
                                    ) 
                                    GROUP BY FoodID 
                                    HAVING count(FoodID) > '" . MINIMUM_TRACK_COUNT . "'";
                                $foodsResult = mysqli_query($con, $queryGetFood); // Execute food query
                                $foodsCount = mysqli_num_rows($foodsResult); // Count number of food records

                                // Query to retrieve weather data associated with the 'great mood'
                                $queryGetWeather = 
                                    "SELECT WeatherID, TrackingID, count(WeatherID) AS cntTrackWea 
                                    FROM trackweather 
                                    WHERE TrackingID IN (
                                        SELECT TrackingID 
                                        FROM dailytracking 
                                        WHERE UserID = $userID 
                                        AND TrackingID IN (
                                            SELECT TrackingID 
                                            FROM trackmoods 
                                            WHERE MoodID = $mood
                                        )
                                    ) 
                                    GROUP BY WeatherID 
                                    HAVING count(WeatherID) > '" . MINIMUM_TRACK_COUNT . "'";
                                $weatherResult= mysqli_query($con, $queryGetWeather); // Execute weather query
                                $weatherCount = mysqli_num_rows($weatherResult); // Count number of weather records

                                // Calculate the total number of tracked activities, companies, locations, foods, and weather entries
                                $totalRows = $activitiesCount + $companiesCount + $locationsCount + $foodsCount + $weatherCount;

                                // Check if there are any entries to display based on the total count across all categories
                                if ($totalRows > 0) { 
                                    // Initialize a counter for displaying entries (resets for each mood section)
                                    $totalEntries = 0 ?> 

                                    <!-- Wrapper for content related to commonly tracked activities associated with the specific mood -->
                                    <div class="mood-<?php echo $mood; ?>">

                                        <!-- Header displaying a message about frequently tagged activities for the current mood -->
                                        <div class="mood-content-wrapper">

                                            <!-- Display the mood name in Japanese within the message -->
                                            <p class="mood-content-intro">
                                                <span>気分が
                                                    <span class="awful-mood"><?php echo $getMoodsAwful['JapaneseMoodName']; ?></span>時によくタグしていること
                                                </span>
                                            </p>
         

                                            <!-- Wrapper for the list of commonly tracked activities associated with the mood -->
                                            <div class="mood-associated-list">
                                                <?php
                                                // Check if there are any activities counted
                                                if ($activitiesCount > 0) {
                                                    // Loop through each activity result
                                                    while ($rowsAct = mysqli_fetch_assoc($activitiesResult)) {
                                                        $totalEntries++; // Increment the total activity count

                                                        // Limit the number of displayed activities to a maximum of 9
                                                        if ($totalEntries > MAX_DISPLAYED_ENTRIES) {
                                                            break; // Exit the loop if the limit is reached
                                                        } ?>

                                                        <div class="mood-associated-item">
                                                            <?php
                                                            $activityID = $rowsAct['ActivityID']; // Get the current activity ID
                                                            
                                                            // Query to get details for the specific activity
                                                            $queryGetActivity = "SELECT * FROM activities WHERE ActivityID = $activityID";
                                                            $resultGetActivity = mysqli_query($con, $queryGetActivity); // Execute the query
                                                            $activityDetails = mysqli_fetch_assoc($resultGetActivity); // Fetch activity details
                                                            ?> 

                                                            <!-- Display the activity icon -->
                                                            <div class="mood-item-icon"><?php echo $activityDetails['ActivityIcon']; ?> </div>
                                                            <!-- Display the activity name -->
                                                            <p class="mood-item-name"><?php echo htmlspecialchars($activityDetails['ActivityName']); ?></p>
                                                            <!-- Display the count of tracked activities -->
                                                            <div class="mood-item-count">
                                                                <?php echo $rowsAct['cntTrackAct'];?> 
                                                            </div>
                                                        </div>
                                                    <?php }
                                                }
                                                
                                                // Check if there are any companies counted
                                                if ($companiesCount > 0) {
                                                    // Loop through each company result
                                                    while ($companyRow = mysqli_fetch_assoc($companiesResult)) {  
                                                        $totalEntries++; // Increment the total company count

                                                        // Limit the number of displayed companies to a maximum of 9
                                                        if ($totalEntries > MAX_DISPLAYED_ENTRIES) {
                                                            break; // Exit the loop if the limit is reached
                                                        } ?>

                                                        <div class="mood-associated-item">
                                                            <?php
                                                            $companyID = $companyRow['CompanyID'];  // Get the current company ID
                                                            
                                                            // Query to get details for the specific company
                                                            $queryGetCompanyDetails = "SELECT * FROM company WHERE CompanyID = $companyID";
                                                            $companiesResultDetails = mysqli_query($con, $queryGetCompanyDetails); // Execute the query
                                                            $companyDetails = mysqli_fetch_assoc($companiesResultDetails); // Fetch company details
                                                            ?>

                                                            <!-- Display the company icon -->
                                                            <div class="mood-item-icon"><?php echo  $companyDetails['CompanyIcon']; ?> </div>
                                                            <!-- Display the company name -->
                                                            <p class="mood-item-name"><?php echo htmlspecialchars($companyDetails['CompanyName']); ?></p>
                                                            <!-- Display the count of tracked companies -->
                                                            <div class="mood-item-count">
                                                                <?php echo $companyRow['cntTrackCom'];?> 
                                                            </div>
                                                        </div>
                                                    <?php }
                                                }

                                                // Check if there are any locations counted
                                                if ($locationsCount > 0) {
                                                    // Loop through each location result
                                                    while ($locationRow = mysqli_fetch_assoc($locationsResult)) {
                                                        $totalEntries++; // Increment the total location count

                                                        // Limit the number of displayed locations to a maximum of 9
                                                        if ($totalEntries > MAX_DISPLAYED_ENTRIES) {
                                                            break; // Exit the loop if the limit is reached
                                                        } ?>

                                                        <div class="mood-associated-item">
                                                            <?php
                                                            $locationID = $locationRow['LocationID']; // Get the current location ID
                                                            
                                                            // Query to get details for the specific location
                                                            $queryGetLocationDetails = "SELECT * FROM locations WHERE LocationID = $locationID";
                                                            $locationsResultDetails = mysqli_query($con, $queryGetLocationDetails); // Execute the query
                                                            $locationDetails = mysqli_fetch_assoc($locationsResultDetails); // Fetch location details
                                                            ?>

                                                            <!-- Display the location icon -->
                                                            <div class="mood-item-icon"><?php echo $locationDetails['LocationIcon']; ?> </div>
                                                            <!-- Display the location name -->
                                                            <p class="mood-item-name"><?php echo htmlspecialchars($locationDetails['LocationName']);  ?></p>
                                                            <!-- Display the count of tracked locations -->
                                                            <div class="mood-item-count">
                                                                <?php echo $locationRow['cntTrackLoc']; ?> 
                                                            </div>
                                                        </div>
                                                    <?php }
                                                }
                                                
                                                // Check if there are any foods counted
                                                if ($foodsCount > 0) {
                                                    // Loop through each food result
                                                    while ($foodRow = mysqli_fetch_assoc($foodsResult)) {
                                                        $totalEntries++; // Increment the total food count

                                                        // Limit the number of displayed foods to a maximum of 9
                                                        if ($totalEntries > MAX_DISPLAYED_ENTRIES) {
                                                            break; // Exit the loop if the limit is reached
                                                        } ?>
                                                        <div class="mood-associated-item">
                                                            <?php
                                                            $foodID = $foodRow['FoodID']; // Get the current food ID
                                                            
                                                            // Query to get details for the specific food
                                                            $queryGetFoodDetails = "SELECT * FROM foods WHERE FoodID = $foodID";
                                                            $foodsResultDetails = mysqli_query($con, $queryGetFoodDetails); // Execute the query
                                                            $foodDetails = mysqli_fetch_assoc($foodsResultDetails); // Fetch food details
                                                            ?>

                                                            <!-- Display the food icon -->
                                                            <div class="mood-item-icon"><?php echo $foodDetails['FoodIcon']; ?> </div>
                                                            <!-- Display the food name -->
                                                            <p class="mood-item-name"><?php echo htmlspecialchars($foodDetails['FoodName']);  ?></p>
                                                            <!-- Display the count of tracked foods -->
                                                            <div class="mood-item-count">
                                                                <?php echo $foodRow['cntTrackFood']; ?> 
                                                            </div>
                                                        </div>
                                                    <?php }
                                                }


                                                // Check if there are any weather records counted
                                                if ($weatherCount > 0) {
                                                    // Loop through each weather result
                                                    while ($weatherRow = mysqli_fetch_assoc($weatherResult)) {
                                                        $totalEntries++; // Increment the total weather count

                                                        // Limit the number of displayed weather entries to a maximum of 9
                                                        if ($totalEntries > MAX_DISPLAYED_ENTRIES) {
                                                            break; // Exit the loop if the limit is reached
                                                        } ?>

                                                        <div class="mood-associated-item">
                                                            <?php
                                                            $weatherID = $weatherRow['WeatherID']; // Get the current weather ID
                                                            
                                                            // Query to get details for the specific weather
                                                            $queryGetWeatherDetails = "SELECT * FROM weather WHERE WeatherID = $weatherID";
                                                            $weatherResultDetails = mysqli_query($con, $queryGetWeatherDetails); // Execute the query
                                                            $weatherDetails = mysqli_fetch_assoc($weatherResultDetails); // Fetch weather details
                                                            ?> 

                                                            <!-- Display the weather icon -->
                                                            <div class="mood-item-icon"><?php echo $weatherDetails['WeatherIcon']; ?> </div>
                                                            <!-- Display the weather name -->
                                                            <p class="mood-item-name"><?php echo htmlspecialchars($weatherDetails['WeatherName']);  ?></p>
                                                            <!-- Display the count of tracked weather -->
                                                            <div class="mood-item-count">
                                                                <?php echo $weatherRow['cntTrackWea']; ?> 
                                                            </div>
                                                        </div><!-- .mood-associated-item -->
                                                    <?php }
                                                } ?>

                                            </div><!-- .mood-associated-list -->
                                        </div><!-- .mood-content-wrapper -->
                                    </div><!-- .mood-content-wrapper -->

                                <?php } else { ?> 

                                <!-- If no records are found, display a message encouraging the user to add tags for better insights -->
                                <div class="no-mood-records-wrapper">

                                    <!-- Informative text prompting the user to add tags when in the specified mood, to gain insights into activity patterns -->
                                    <p>気分が<b><?php echo htmlspecialchars($getMoodsAwful['JapaneseMoodName']); ?></b>ときに、よりタグを追加して自分の行動パターンについての洞察を得る。</p>

                                </div><!-- .no-mood-records-wrapper -->
                                <?php } ?>
                            </section> <!-- #awful-mood -->

                        </div><!-- .mood-association-container -->
                    </section><!-- .mood-association-wrapper -->
                </section><!-- .insights-right-section -->
            </div><!-- .insights-wrapper -->

            <section class="recent-posts-wrapper">

                <!-- Header Section: Title and Navigation Buttons -->
                <div class="recent-posts-header">

                    <!-- Section Title -->
                    <div class="header-title">
                        <div class="section-title">
                            <!-- Section Heading: Displayed in Japanese as 'Recent Moods' -->
                            <h3>最近の気分</h3>
                        </div><!-- .title -->

                        <!-- Navigation Buttons to Cycle Through Posts -->
                        <div class="navigation-buttons">
                            <!-- Button to navigate to the previous set of mood entries; disabled initially -->
                            <button id="previous-btn" class="nav-btn previous-btn disabled-btn" disabled>
                                <i class="fa-solid fa-angles-left"></i>
                            </button>

                            <!-- Button to navigate to the next set of mood entries; disabled if fewer than 5 entries are available -->
                            <button id="next-btn" class="nav-btn next-btn <?php if($totalResultDays <= 4){echo "disabled-btn";} ?>" 
                                >
                                <i class="fa-solid fa-angles-right"></i>
                            </button>
                        </div><!-- .navigation-buttons -->
                    </div><!-- .header-title -->

                    <!-- Link to View All Posts -->
                    <div class="view-all-link">
                        <a href="user_tracking_records.php">
                            <p>See All <i class="fa-solid fa-angles-right"></i></p>
                        </a>
                    </div><!-- .view-all-link -->

                </div><!-- .recent-posts-header -->

                <!-- Check if there are mood tracking records -->
                <?php if ($totalResultDays > 0) { ?>

                    <!-- Wrapper for Slideable Content -->
                    <div class="slide-wrapper">
                        <div class="slide-posts slide0">

                        <?php 
                        $date = date("Y-m-d"); // Store the current date

                        // Check if mood entries exist
                        if ($totalResultDays > 0) {
                            $resultRecentTracking = mysqli_query($con, $queryRecentTracking);

                            // Loop through each tracked day to fetch mood and feeling details
                            while ($dayRecord = mysqli_fetch_assoc($resultRecentTracking)) {
                                $currentTrackingID = $dayRecord['TrackingID']; // Current day's tracking ID

                                // Query mood data based on Tracking ID
                                $queryMoodDetails = 
                                    "SELECT * 
                                    FROM moods 
                                    WHERE MoodID IN (
                                        SELECT MoodID 
                                        FROM trackmoods 
                                        WHERE TrackingID = $currentTrackingID
                                    )";

                                // Execute the mood query and check if mood data exists
                                $moodResult = mysqli_query($con, $queryMoodDetails);
                                $moodData = mysqli_fetch_assoc($moodResult);
                                $moodExists = mysqli_num_rows($moodResult);
                                
                                // If mood data exists, extract mood details
                                if ($moodExists > 0 ) { 
                                    $moodName = $moodData['MoodName'];
                                    $japaneseMoodName = $moodData['JapaneseMoodName'];
                                    $moodID = $moodData['MoodID'];
                                    $moodIcon = $moodData['moodEmojiColor'];
                                }

                                // Query to retrieve associated feelings for the current day
                                $queryFeelingDetails = 
                                    "SELECT FeelingName 
                                    FROM feelings 
                                    WHERE FeelingID IN (
                                        SELECT FeelingID 
                                        FROM trackfeelings 
                                        WHERE TrackingID = $currentTrackingID
                                    )";

                                $feelingResult = mysqli_query($con, $queryFeelingDetails);
                                $feelingData = mysqli_fetch_assoc($feelingResult);

                                // If feeling data exists, extract details
                                if (!empty($feelingData)) { 
                                    $primaryFeeling = $feelingData['FeelingName'];
                                    $totalFeelings = mysqli_num_rows($feelingResult);
                                    $additionalFeelings = $totalFeelings - 1;
                                }
                                
                                // Store the recorded date of the current day's entry
                                $trackingDate = $dayRecord['Date']; ?>

                                <!-- Display Daily Tracking Information -->
                                <div class="tracking-day-container">
                                    <a href="./daily_tracking_overview.php?date=<?php echo $trackingDate; ?>">

                                        <!-- Left Indicator: Style based on Mood -->
                                        <div class="tracking-day-left-indicator <?php 
                                            if ($moodExists > 0) {
                                                if ($moodID == MOOD_GREAT) { 
                                                    echo ' mood-great';
                                                } elseif($moodID == MOOD_GOOD) { 
                                                    echo ' mood-good';
                                                } elseif($moodID == MOOD_OKAY) {
                                                    echo ' mood-okay';
                                                } elseif($moodID == MOOD_BAD) {
                                                    echo ' mood-bad';
                                                } elseif($moodID == MOOD_AWFUL) {
                                                    echo ' mood-awful';
                                                } 
                                            } else {
                                                echo ' mood-status-none';
                                            }
                                        ?>"></div><!-- .tracking-day-left-indicator -->

                                        <!-- Right Content with Date, Mood, and Feeling Details -->
                                        <div class="tracking-day-details">

                                            <!-- Date Display -->
                                            <p class="tracking-day-date"><?php echo $trackingDate; ?></p>

                                            <!-- Emoji Display based on Mood -->
                                            <p class="tracking-day-emoji <?php 
                                                if ($moodExists > 0) {
                                                    if ($moodID == MOOD_GREAT) { 
                                                        echo ' emoji-status-great';
                                                    } elseif ($moodID == MOOD_GOOD) { 
                                                        echo ' emoji-status-good';
                                                    } elseif ($moodID == MOOD_OKAY) {
                                                        echo ' emoji-status-okay';
                                                    } elseif ($moodID == MOOD_BAD) {
                                                        echo ' emoji-status-bad';
                                                    } elseif ($moodID == MOOD_AWFUL) {
                                                        echo ' emoji-status-awful';
                                                    } 
                                                } else {
                                                    echo ' mood-status-none';
                                                }
                                            ?>">   
                                            <?php echo $moodExists ? $moodIcon : '<i class="fa-solid fa-question"></i>'; ?></p>

                                            <!-- Mood Name in Japanese and English -->
                                            <p class="tracking-day-mood-name">
                                                <span class="mood-name-japanese"><?php echo $moodExists ? htmlspecialchars($japaneseMoodName) : ''; ?></span>
                                                <span><?php echo $moodExists ? htmlspecialchars($moodName) : ''; ?></span>
                                            </p>

                                            <!-- Display primary feeling and count of additional feelings if available -->
                                            <div class="feeling-wrapper">
                                                <p><?php 
                                                    if (!empty($feelingData)) {
                                                    echo htmlspecialchars($primaryFeeling); ?>
                                                    <span>
                                                        <?php if ($additionalFeelings > 0) { ?>
                                                            <?php echo "+ " . $additionalFeelings . " more"; // Display additional feeling count if there are more feelings ?>
                                                        <?php } ?>
                                                    </span>
                                                </p>
                                                <?php }?> 
                                            </div><!-- .feeling-wrapper -->

                                        </div><!-- .tracking-day-details -->
                                    </a>
                                </div><!-- .tracking-day-container -->
                            <?php }// End while loop for tracking days 
                        } ?>
                        </div><!-- .slide-posts -->

                        <!-- Display a message if no mood tracking records exist -->
                        <?php } else { ?>
                            <p class="no-records-message">気分は何も記録していない。</p>
                        <?php } ?>

                </div><!-- .slide-wrapper -->
            </section><!-- .recent-posts-wrappe -->

        </div><!-- .main-wrapper -->
    </body>

    <script>
        // Chart configuration
        // Convert PHP variables to JavaScript variables
        var greatPercentage = <?php echo json_encode($greatPercentage); ?>; // Percentage of 'great' mood
        var goodPercentage = <?php echo json_encode($goodPercentage); ?>; // Percentage of 'good' mood
        var okayPercentage = <?php echo json_encode($okayPercentage); ?>; // Percentage of 'okay' mood
        var badPercentage = <?php echo json_encode($badPercentage); ?>; // Percentage of 'bad' mood
        var awfulPercentage = <?php echo json_encode($awfulPercentage); ?>; // Percentage of 'awful' mood
        
        // Get the chart element from the DOM
        const chart = document.getElementById('moodDistributionChart');

        // Initialize the chart
        new Chart(chart, {
            type: 'doughnut', // Type of the chart
            data: {
            datasets: [{
                label: '%',  // Label for the dataset
                data: [
                    (greatPercentage), 
                    (goodPercentage), 
                    (okayPercentage), 
                    (badPercentage), 
                    (awfulPercentage)
                ],
                backgroundColor: [ // Set background colors for each segment
                    '#812061', // Color for 'great' mood
                    '#EB6694', // Color for 'good' mood
                    '#8BCCCA', // Color for 'okay' mood
                    '#9DC3E6', // Color for 'bad' mood
                    '#27627E', // Color for 'awful' mood
                    ],
                borderWidth: 1 // Width of the border around the segments
            }]
            },
        });

        // Navigation buttons and slide container
        const previous_btn = document.getElementById('previous-btn');
        const next_btn = document.getElementById('next-btn');
        const slide = document.querySelector('.slide-posts');

        const MAX_STEPS = 3; // Total number of slides
        let currentStep = 0; // Initialize current step

        // Add event listener for the Next button
        next_btn.addEventListener('click', () => {
            // Ensure not exceeding the maximum steps
            if (currentStep < MAX_STEPS) {
                // Remove disabled class from Previous button
                previous_btn.classList.remove('disabled-btn');
                
                currentStep++; // Increment the current step
                
                // Create the class names for current and previous slides
                const currentStepClass = 'slide' + currentStep;
                const prevStepClass = 'slide' + (currentStep - 1);
                
                // Add the current slide class and remove the previous slide class
                slide.classList.add(currentStepClass);
                slide.classList.remove(prevStepClass);
                
                // Enable Previous button if we move beyond the first step
                previous_btn.disabled = false;
                
                // Disable the Next button if we've reached the maximum steps
                if (currentStep === MAX_STEPS) {
                    next_btn.disabled = true;
                    next_btn.classList.add('disabled-btn');
                } 
                previous_btn.disabled = false;
            }
        })

        // Add event listener for the Previous button
        previous_btn.addEventListener('click', () => { 
            // Ensure not going below the first step
            if (currentStep > 0) {
                // Remove disabled class from Next button
                next_btn.classList.remove('disabled-btn');

                currentStep--; // Decrement the current step
                
                // Create the class names for current and next slides
                const currentStepClass = 'slide' + currentStep;
                const nextStepClass = 'slide' + (currentStep + 1);
                
                // Add the current slide class and remove the next slide class
                slide.classList.add(currentStepClass);
                slide.classList.remove(nextStepClass);
                
                // Disable Previous button if we're back at the first step
                if (currentStep === 0) {
                    previous_btn.disabled = true;
                    previous_btn.classList.add('disabled-btn');
                } 
                
                next_btn.disabled = false;
            }
        })
    </script>
    
</html>
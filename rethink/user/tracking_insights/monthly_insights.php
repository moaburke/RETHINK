<?php
session_start();// Start the session

// Define a constant for the base path
define('BASE_PATH', '../../server-side/shared/');

include(BASE_PATH . "connections.php"); // Include databade connection file
include(BASE_PATH . "check_login.php"); //Include the utiity functions
include(BASE_PATH . "timezone.php"); // Include the timezone configuration
include(BASE_PATH . "constants.php"); // Include the constants file
include(BASE_PATH . "header/user_layout.php"); // Include the user header layout file
require_once "../../server-side/user/calendar.php"; // Include the calendar functionality

define("TOP_LIMIT", 10);
define("MAX_DAYS_IN_MONTH", 31);
define("GOOD_SLEEP_THRESHOLD", 7);

define("GOOD_SLEEP_BG_COLOR", "rgba(129, 32, 97, 0.5)");
define("GOOD_SLEEP_BORDER_COLOR", "#812061");
define("INSUFFICIENT_SLEEP_BG_COLOR", "rgba(39, 98, 126, 0.5)");
define("INSUFFICIENT_SLEEP_BORDER_COLOR", "#27627E");

// Check if the user is logged in and retrieve user data
$userData = check_login($con); // Retrieve user data from the login check
$userID = $userData['UserID'];  // Get the UserID of the logged-in user

// Get today's date
$date = date("Y-m-d");

// Get the current year and month for display purposes
$currentYear = date("Y"); // Get the current year
$currentMonth = date("m"); // Get the current month

// Get the date from the GET request, or default to today
$selectedDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d'); // Use the selected date or today's date

// Calculate the previous and next months' dates for navigation
$previousMonth = date('Y-m-d', strtotime($selectedDate .' -1 month')); // Get the previous month's date
$nextMonth = date('Y-m-d', strtotime($selectedDate .' +1 month')); // Get the next month's date

// Function to extract the year from the selected date
function getYear($selectedDate) {
    return date('Y', strtotime($selectedDate)); // Return the year part of the date
}
$yearOfSelectedDate = getYear($selectedDate); // Get the year of the selected date

// Function to extract the month from the selected date
function getMonth($selectedDate) {
    return date('m', strtotime($selectedDate)); // Return the month part of the date
}
$monthOfSelectedDate = getMonth($selectedDate); // Get the month of the selected date

// Store the month number for the selected date
$selectedDate = $monthOfSelectedDate;

// Instantiate the calendar object for displaying the calendar
$calendar = new Calendar(new CurrentDate(), new CalendarDate());

$calendar->setMonth($selectedDate); // Set the calendar to the selected month
$calendar->create(); // Create and display the calendar

// Array of month names in English
$monthNamesEnglish = [
    'January', 'February', 'March', 'April', 'May', 'June', 
    'July', 'August', 'September', 'October', 'November', 'December'
];

// Array of month names in Japanese
$monthNamesJapanese = [
    '1月', '2月', '3月', '4月', '5月', '6月', 
    '7月', '8月', '9月', '10月', '11月', '12月'
];

// Get the number of days in the selected month of the specified year
$daysInSelectedMonth = cal_days_in_month(CAL_GREGORIAN, $selectedDate, $yearOfSelectedDate);

// SQL query to get all mood records
$queryMoods = "SELECT * FROM moods";
// Execute the query to get moods
$resultMoods = mysqli_query($con,$queryMoods);

// SQL query to retrieve the top 10 activities for the user in the selected month and year
$queryGetActivities = 
    "SELECT t.ActivityID, COUNT(ActivityID) AS cntAct 
    FROM dailytracking d 
    JOIN trackactivities t ON d.TrackingID = t.TrackingID 
    WHERE UserID = $userID 
    AND Month(Date) = $selectedDate 
    AND Year(Date) = $yearOfSelectedDate
    GROUP BY t.ActivityID
    ORDER BY cntAct DESC LIMIT " . TOP_LIMIT .""
;

// Execute the query to get activities
$resultActivities = mysqli_query($con,$queryGetActivities); 
// Get the number of rows returned for activities
$activitiesCount = mysqli_num_rows($resultActivities); 

// SQL query to retrieve the top 10 companies for the user in the selected month and year
$queryGetCompany = 
    "SELECT t.CompanyID, COUNT(CompanyID) AS cntCom 
    FROM dailytracking d
    JOIN trackcompany t ON d.TrackingID = t.TrackingID
    where UserID = $userID
    AND Month(Date) = $selectedDate
    AND Year(Date) = $yearOfSelectedDate
    GROUP BY t.CompanyID
    ORDER BY cntCom DESC LIMIT " . TOP_LIMIT .""
;

// Execute the query to get companies
$resultCompanies = mysqli_query($con,$queryGetCompany); 
// Get the number of rows returned for companies
$companiesCount = mysqli_num_rows($resultCompanies); 

// SQL query to retrieve the top 10 locations for the user in the selected month and year
$queryGetLocations = 
    "SELECT t.LocationID, COUNT(LocationID) AS cntLoc
    FROM dailytracking d
    JOIN tracklocations t ON d.TrackingID = t.TrackingID
    WHERE UserID = $userID
    AND Month(Date) = $selectedDate
    AND Year(Date) = $yearOfSelectedDate
    GROUP BY t.LocationID
    ORDER BY cntLoc DESC LIMIT " . TOP_LIMIT .""
;

// Execute the query to get locations
$resultLocations = mysqli_query($con,$queryGetLocations);
// Get the number of rows returned for locations
$locationsCount = mysqli_num_rows($resultLocations);

// SQL query to retrieve the top 10 food for the user in the selected month and year
$queryGetFood = 
    "SELECT t.FoodID, COUNT(FoodID) AS cntFood 
    FROM dailytracking d 
    JOIN trackfoods t ON d.TrackingID = t.TrackingID 
    WHERE UserID = $userID 
    AND Month(Date) = $selectedDate 
    AND Year(Date) = $yearOfSelectedDate
    GROUP BY t.FoodID
    ORDER BY cntFood DESC LIMIT " . TOP_LIMIT .""
;

// Execute the query to get food 
$resultFood = mysqli_query($con,$queryGetFood);
// Get the number of rows returned for food
$foodCount = mysqli_num_rows($resultFood);

// SQL query to retrieve the top 10 weather for the user in the selected month and year
$queryGetWeather = 
    "SELECT t.WeatherID, COUNT(WeatherID) AS cntWea 
    FROM dailytracking d 
    JOIN trackweather t ON d.TrackingID = t.TrackingID 
    WHERE UserID = $userID 
    AND Month(Date) = $selectedDate 
    AND Year(Date) = $yearOfSelectedDate
    GROUP BY t.WeatherID
    ORDER BY cntWea DESC LIMIT " . TOP_LIMIT .""
;

// Execute the query to get weather
$resultWeather = mysqli_query($con,$queryGetWeather);
// Get the number of rows returned for weather
$weatherCount = mysqli_num_rows($resultWeather);


// SQL query to count the occurrences of the "Great" mood for the user in the selected month and year
$queryGreatMood = mysqli_query($con, 
    "SELECT t.MoodID, COUNT(MoodID) AS cntGreatMood 
    FROM dailytracking d 
    JOIN trackmoods t ON d.TrackingID = t.TrackingID 
    WHERE UserID = $userID 
    AND Month(Date) = $selectedDate 
    AND Year(Date) = $yearOfSelectedDate 
    AND MoodID = " . MOOD_GREAT ." 
    GROUP BY t.MoodID"
);

// Fetch the result and set the count for great moods
$resultGreatMood = mysqli_fetch_assoc($queryGreatMood);
$greatMoodsCount = !empty($resultGreatMood) ? $resultGreatMood['cntGreatMood'] : 0; // Set to 0 if no records found

// SQL query to count the occurrences of the "Good" mood for the user in the selected month and year
$queryGoodMood = mysqli_query($con, 
    "SELECT t.MoodID, COUNT(MoodID) AS cntGoodMood 
    FROM dailytracking d 
    JOIN trackmoods t ON d.TrackingID = t.TrackingID 
    WHERE UserID = $userID 
    AND Month(Date) = $selectedDate 
    AND Year(Date) = $yearOfSelectedDate 
    AND MoodID = " . MOOD_GOOD ." 
    GROUP BY t.MoodID"
);

// Fetch the result and set the count for good moods
$resultGoodMood = mysqli_fetch_assoc($queryGoodMood);
$goodMoodsCount = !empty($resultGoodMood) ? $resultGoodMood['cntGoodMood'] : 0; // Set to 0 if no records found


// SQL query to count the occurrences of the "Okay" mood for the user in the selected month and year
$queryOkayMood = mysqli_query($con, 
    "SELECT t.MoodID, COUNT(MoodID) AS cntOkayMood 
    FROM dailytracking d 
    JOIN trackmoods t ON d.TrackingID = t.TrackingID 
    WHERE UserID = $userID 
    AND Month(Date) = $selectedDate 
    AND Year(Date) = $yearOfSelectedDate 
    AND MoodID =  " . MOOD_OKAY ." 
    GROUP BY t.MoodID"
);
                                            
// Fetch the result and set the count for okay moods
$resultOkayMood = mysqli_fetch_assoc($queryOkayMood);
$okayMoodsCount = !empty($resultOkayMood) ? $resultOkayMood['cntOkayMood'] : 0; // Set to 0 if no records found

// SQL query to count the occurrences of the "Bad" mood for the user in the selected month and year
$queryBadMood = mysqli_query($con, 
    "SELECT t.MoodID, COUNT(MoodID) AS cntBadMood 
    FROM dailytracking d 
    JOIN trackmoods t ON d.TrackingID = t.TrackingID
    WHERE UserID = $userID 
    AND Month(Date) = $selectedDate 
    AND Year(Date) = $yearOfSelectedDate 
    AND MoodID = " . MOOD_BAD ."  
    GROUP BY t.MoodID"
);
                                        
// Fetch the result and set the count for bad moods
$resultBadMood = mysqli_fetch_assoc($queryBadMood);
$badMoodsCount = !empty($resultBadMood) ? $resultBadMood['cntBadMood'] : 0; // Set to 0 if no records found

// SQL query to count the occurrences of the "Awful" mood for the user in the selected month and year
$queryAwfulMood = mysqli_query($con, 
    "SELECT t.MoodID, COUNT(MoodID) AS cntAwfulMood 
    FROM dailytracking d 
    JOIN trackmoods t ON d.TrackingID = t.TrackingID 
    WHERE UserID = $userID 
    AND Month(Date) = $selectedDate 
    AND Year(Date) = $yearOfSelectedDate 
    AND MoodID = " . MOOD_AWFUL ." 
    GROUP BY t.MoodID"
);

// Fetch the result and set the count for awful moods
$resultAwfulMood = mysqli_fetch_assoc($queryAwfulMood);
$awfulMoodsCount = !empty($resultAwfulMood) ? $resultAwfulMood['cntAwfulMood'] : 0; // Set to 0 if no records found


// Initialize variables for tracking the most prevalent mood and its percentage
$dominantMood = ""; // Variable to store the mood with the highest percentage
$highestMoodPercentage = 0; // Variable to track the highest percentage of moods

// Calculate the total number of moods recorded
$totalMoodsCount = $greatMoodsCount + $goodMoodsCount + $okayMoodsCount + $badMoodsCount + $awfulMoodsCount;

// Check if total moods count is not empty to avoid division by zero
if (!empty($totalMoodsCount)) {
    // Calculate the percentage of great moods relative to the total moods counted
    $greatMoodsPercentage = ($greatMoodsCount / $totalMoodsCount) * 100; // Calculate the percentage for great moods
    $greatPercentage = number_format($greatMoodsPercentage, 0);

    // Check if the percentage of great moods is greater than zero
    if ($greatPercentage > 0) {
        $dominantMood = "GreatDayEmoji"; // Assign the emoji representing a great mood
        $highestMoodPercentage = $greatPercentage;
    }

    // Calculate the percentage of good moods relative to the total moods counted
    $goodMoodsPercentage = ($goodMoodsCount / $totalMoodsCount) * 100; // Calculate the percentage for good moods
    $goodPercentage = number_format($goodMoodsPercentage, 0); // Format the percentage as a whole number

    // Check if the percentage of good moods is greater than zero
    if($goodPercentage > $highestMoodPercentage ){
        $dominantMood = "gGoodDayEmoji"; // Assign the emoji representing a good mood
        $highestMoodPercentage = $goodPercentage;
    }

    // Calculate the percentage of okay moods relative to the total moods counted
    $okayMoodsPercentage = ($okayMoodsCount / $totalMoodsCount) * 100; // Calculate the percentage for okay moods
    $okayPercentage = number_format($okayMoodsPercentage, 0); // Format the percentage as a whole number

    // Check if the percentage of okay moods is greater than zero
    if($okayPercentage > $highestMoodPercentage ){
        $dominantMood = "OkayDayEmoji"; // Assign the emoji representing an okay mood
        $highestMoodPercentage = $goodPercentage;
    }

    // Calculate the percentage of bad moods relative to the total moods counted
    $badMoodsPercentage = ($badMoodsCount / $totalMoodsCount) * 100; // Calculate the percentage for bad moods
    $badPercentage = number_format($badMoodsPercentage, 0); // Format the percentage as a whole number

    // Check if the percentage of bad moods is greater than zero
    if($badPercentage > $highestMoodPercentage ){
        $dominantMood = "BadDayEmoji"; // Assign the emoji representing a bad mood
        $highestMoodPercentage = $goodPercentage;
    }

    // Calculate the percentage of awful moods relative to the total moods counted
    $awfulMoodsPercentage = ($awfulMoodsCount / $totalMoodsCount) * 100; // Calculate the percentage for awful moods
    $awfulPercentage = number_format($awfulMoodsPercentage, 0); // Format the percentage as a whole number

    // Check if the percentage of awful moods is greater than zero
    if($awfulPercentage > $highestMoodPercentage ){
        $dominantMood = "AwfulDayEmoji"; // Assign the emoji representing an awful mood
    }
}

// Initialize arrays to store sleep times, background colors, and border colors
$sleepTimesInHours = array(); // Array to store sleep times for each day
$backgroundColor = array(); // Array to store background colors based on sleep quality
$borderColor = array(); // Array to store border colors based on sleep quality

// Loop through the days of the month (1 to 31) to retrieve sleep data
for ($day = 1; $day <= MAX_DAYS_IN_MONTH; $day++) {
    $querySleepTime = mysqli_query($con, 
        "SELECT d.Date,t.sleepTime, Day(Date) AS OnlyDay 
        FROM dailytracking d 
        JOIN tracksleeptime t ON d.TrackingID = t.TrackingID 
        WHERE UserID = $userID 
        AND Month(Date) = $selectedDate 
        AND Year(Date) = $yearOfSelectedDate 
        AND Day(Date) = $day
    ");
    
    // Check if any sleep data is returned 
    $sleepDataCount = mysqli_num_rows($querySleepTime);

    if ($sleepDataCount > 0) {
        // Fetch sleep time and convert it to hours
        $resultSleepTime = mysqli_fetch_assoc($querySleepTime); 
        $sleepTimeInMinutes = $resultSleepTime['sleepTime']; // Extract the sleep time in minutes from the fetched data
        $convertedSleepTime = $sleepTimeInMinutes / 60; // Convert the sleep time from minutes to hours for easier analysis
        $sleepTimesInHours[] = $convertedSleepTime; // Append the converted sleep time (in hours) to the array of sleep times
    } else {
        $convertedSleepTime = 0; // If no sleep data is found for the current day, set the converted sleep time to 0
        $sleepTimesInHours[] = $convertedSleepTime; // Store the default value (0 hours) in the array of sleep times
    }
}
        
// Loop through sleep times to determine background and border colors based on sleep quality
foreach ($sleepTimesInHours as $sleepTime) {
    if ($sleepTime >= GOOD_SLEEP_THRESHOLD) {
        // If sleep time is 7 hours or more, set colors for good sle
        $backgroundColor[] = GOOD_SLEEP_BG_COLOR; // Background color for good sleep
        $borderColor[] = GOOD_SLEEP_BORDER_COLOR; // Border color for good sleep
    } else {
        // If sleep time is less than 7 hours, set colors for insufficient sleep
        $backgroundColor[] = INSUFFICIENT_SLEEP_BG_COLOR; // Background color for insufficient sleep
        $borderColor[] = INSUFFICIENT_SLEEP_BORDER_COLOR; // Border color for insufficient sleep
    }
}
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Load mutual head components and necessary scripts/styles -->
        <?php includeHeadAssets(); ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Load Chart.js for rendering charts -->
        <!-- This script handles the tab interactions in the user interface -->
        <script src="../../assets/javascript/tab_interactions.js" defer></script> 
    </head>
   
    <body>
        <header class="sidebar-navigation insights-navigation">
            <!-- Include the mutual header for user navigation -->
            <?php renderUserNavigation(); ?>
        </header>
        <!-- Logout button and sticky header for logged-in user -->
        <?php renderUserHeaderWithLogout($userData); ?>

        <div class="main-wrapper">
            <h2>Monthly Insights</h2>

            <!-- Breadcrumb navigation -->
            <div class="breadcrumbs">
                <a href="../user_home.php"><p>Top Page</p></a>
                <i class="fa-solid fa-angle-right fa-sm"></i>
                <p class="bread-active">Insights</p>
            </div><!--. breadcrumbs -->

            <!-- Insight navigation bar allowing users to switch between 'General' and 'Monthly' categories -->
            <div class="insight-navigation">
                <!-- Link to general insights overview page -->
                <div class="general-category">
                    <a href="./insights.php"><p>General</p></a>
                </div>

                <!-- Display the current page as 'Monthly' insights with active styling applied -->
                <div class="monthly-category active-category"><p>Monthly</p></div>
            </div> <!-- .insight-navigation -->

            <!-- Container for the top section of the monthly insights view -->
            <div class="monthly-insights-top">

                <!-- Title section showing the month name in Japanese and English -->
                <div class="monthly-insights-title">
                    <h3>
                        <!-- Display the selected month name in Japanese, retrieved from PHP array -->
                        <span class="month-name-japanese"><?php echo $monthNamesJapanese[($selectedDate - 1)]; ?></span>
                            
                        <!-- Display the selected month name in English, retrieved from PHP array -->
                        <span class="month-name-english"><?php echo $monthNamesEnglish[($selectedDate - 1)]; ?></span>
                    </h3>

                    <!-- Navigation buttons to switch between months -->
                    <div class="month-navigation-buttons">   
                         <!-- Link to the previous month -->        
                        <a href="?date=<?=$previousMonth;?>">
                            <div id="previous-month" class="month-button previous-month">
                                <i class="fa-solid fa-angles-left"></i> <!-- Left arrow icon -->
                            </div>
                        </a>

                        <!-- Conditional check for disabling the next month button if at the current month -->
                        <?php if (($currentYear <= $yearOfSelectedDate && $currentMonth == $selectedDate) || ($currentYear < $yearOfSelectedDate)) { ?>  
                            
                            <!-- Next month button (disabled) -->
                            <a href="#">
                                <div id="next-month" <?php if(($currentYear <= $yearOfSelectedDate && $currentMonth == $selectedDate) or ($currentYear < $yearOfSelectedDate)){ echo "disabled";}?> 
                                    class="month-button next-month <?php if(($currentYear <= $yearOfSelectedDate && $currentMonth == $selectedDate) or ($currentYear < $yearOfSelectedDate)){ echo "disabled-btn";}?>">
                                    <i class="fa-solid fa-angles-right"></i> <!-- Right arrow icon (disabled) -->
                                </div>
                            </a> 
                            
                        <?php }else{ ?> 
                            
                            <!-- Next month button (enabled) -->
                            <a href="?date=<?=$nextMonth;?>">
                                <div id="next-month" <?php if(($currentYear <= $yearOfSelectedDate && $currentMonth == $selectedDate) or ($currentYear < $yearOfSelectedDate)){ echo "disabled";}?> 
                                    class="month-button next-month <?php if(($currentYear <= $yearOfSelectedDate && $currentMonth == $selectedDate) or ($currentYear < $yearOfSelectedDate)){ echo "disabled-btn";}?>">
                                    <i class="fa-solid fa-angles-right"></i> <!-- Right arrow icon -->
                                </div>
                            </a> 
                        <?php } ?>
                    </div><!-- .month-navigation-buttons -->

                </div><!-- .monthly-insights-title -->
            </div><!-- .monthly-insights-top -->

            <div class="insights-wrapper">

                <!-- Section for insights on the left side of the page -->
                <section class="insights-left-section">

                    <!-- Container for displaying the count of moods recorded in the selected month -->
                    <div class="montly-posted-days-wrapper">

                        <!-- Header section displaying the selected month in Japanese -->
                        <div class="montly-posted-days-header insights-header">
                            <h3><?php echo $monthNamesJapanese[($selectedDate - 1)]; ?>に記録した日数</h3>
                        </div>

                        <!-- Display the days the user posted moods in the selected month -->
                        <div class="montly-posted-days-content">

                                <!-- Days of the month with dominant mood styling -->
                                <div class="posted-days-stats <?php echo $dominantMood;?>">
                                    <!-- Display the total moods recorded out of total days in the selected month -->
                                    <span><?php echo $totalMoodsCount; ?></span>
                                    <span> / </span>
                                    <span> <?php echo $daysInSelectedMonth; ?></span>
                                </div><!-- .posted-days-stats -->

                        </div><!-- .montly-posted-days-content -->

                    </div><!-- . montly-posted-days-wrapper -->

                    <!-- Wrapper for displaying the monthly calendar -->
                    <div class="calendar-wrapper">

                        <div class="calendar-container mt-4">
                            <table class="table table-bordered mt-4">
                                
                                <!-- Table header row containing labels for each day of the week -->
                                <thread>
                                    <!-- Loop through each day label (e.g., Monday, Tuesday) generated by getDayLabels() function -->
                                    <?php foreach ($calendar->getDayLabels() as $dayLabel): ?>
                                        <!-- Table header cell for each day label -->
                                        <th>
                                            <?php echo $dayLabel; ?> <!-- Display the day label -->
                                        </th>
                                    <?php endforeach; ?>    
                                </thread>

                                <!-- Table body with calendar days organized into weeks -->
                                <tbody>
                                    <!-- Loop through each week in the calendar as provided by getWeeks() method -->
                                    <?php foreach($calendar->getWeeks() as $week): ?>
                                        <tr>
                                            <!-- Loop through each day within the current week -->
                                            <?php foreach ($week as $day): ?>
                                                <!-- Table cell for each day; style secondary text color for non-current month days -->
                                                <td <?php if(!$day['currentMonth']): ?>class="text-secondary" <?php endif; ?>>
                                                    
                                                    <!-- Highlight the current day if it matches the selected date -->
                                                    <span <?php if($calendar->isCurrentDate($day["dayNumber"])) : ?> class="text-primary" <?php endif; ?>>
                                                        
                                                        <?php 
                                                        // PHP query to fetch mood tracking data for each day
                                                        $queryTrackingData = "SELECT t.TrackingID, d.UserID, t.MoodID, m.moodEmoji, d.Date, Year(Date) AS OnlyYear, Month(Date) AS OnlyMonth, Day(Date) AS OnlyDay  
                                                                                FROM dailytracking d 
                                                                                JOIN trackmoods t ON d.TrackingID = t.TrackingID 
                                                                                JOIN moods m ON t.MoodID = m.MoodID 
                                                                                WHERE UserID =  $userID";
                                                        $resultTrackingData = mysqli_query($con,$queryTrackingData);; // Execute query
                                                        $trackingDataCount = mysqli_num_rows($resultTrackingData); // Count tracking records found

                                                        // Loop through each record in the result set
                                                        while ($trackingRecord = mysqli_fetch_assoc($resultTrackingData)) {
                                                            $recordedMonth = $trackingRecord['OnlyMonth']; // Month from the tracking record
                                                            $recordedDay = $trackingRecord['OnlyDay']; // Day from the tracking record
                                                            $recordedYear = $trackingRecord['OnlyYear']; // Year from the tracking record

                                                            // Check if the record date matches the currently selected date
                                                            if (( $selectedDate == $recordedMonth) && 
                                                                (($day["dayNumber"]) == $recordedDay) && 
                                                                ($yearOfSelectedDate == $recordedYear)) {
                                                                ?>

                                                                <!-- Display the mood emoji if the record date matches -->
                                                                <p class="day-emoji">
                                                                    <?php echo $trackingRecord['moodEmoji']; ?>
                                                                </p>
                                                            <?php } 
                                                        }?>

                                                        <!-- Display the day number -->
                                                        <p class="day-number"><?php echo $day['dayNumber']; ?></p>
                                                        <!-- Placeholder for styling -->
                                                        <p class="empty-cell"></p>

                                                    </span>
                                                </td>

                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>

                            </table>
                        </div><!-- .calendar-container -->
                    </div><!-- .calendar-wrapper -->

                </section><!-- .insights-left-section -->

                <!-- Middle wrapper section for displaying insights on mood counts and sleep data -->
                <section class="insights-middle-section">
                    <section class="mood-count-wrapper">

                        <div class="mood-header-container">

                            <!-- Heading for the total mood count in Japanese -->
                            <div class="mood-header insights-header">
                                <h3>気分の合計</h3>
                            </div><!-- . -->

                            <!-- Container for mood count percentage display -->
                            <div class="mood-stats-container">
                                
                                <?php 
                                // Only display percentages if there are mood entries
                                if (!empty($totalMoodsCount)) { ?>

                                    <!-- Wrapper for mood percentages bar -->
                                    <div class="mood-percentages-wrapper">

                                        <!-- Inner container to display mood percentages based on mood types -->
                                        <div class="mood-percentages">

                                            <!-- Display percentage bar and text for "Great" mood if data exists -->
                                            <?php if (!empty($greatPercentage)){ ?>
                                                <!-- Create a percentage bar for the 'great' mood category -->
                                                <div class="mood-percentage-bar great-percentage" style = "width:<?php echo $greatPercentage;?>%;">
                                                    <!-- Container for the graphical representation of the 'great' mood percentage -->
                                                    <div class="mood-bar-graph"></div>

                                                    <!-- Text displaying the percentage value of the 'great' mood -->
                                                    <div class="mood-percentage-label">
                                                        <p><?php echo $greatPercentage;?>%</p>
                                                    </div>
                                                </div><!-- .percentageGreat -->

                                            <?php } ?>

                                            <!-- Display percentage bar and text for "Good" mood if data exists -->
                                            <?php if (!empty($goodPercentage)){ ?>
                                                <!-- Create a percentage bar for the 'good' mood category -->
                                                <div class="mood-percentage-bar good-percentage" style = "width:<?php echo $goodPercentage;?>%;">
                                                    <!-- Container for the graphical representation of the 'good' mood percentage -->
                                                    <div class="mood-bar-graph"></div>

                                                    <!-- Text displaying the percentage value of the 'good' mood -->
                                                    <div class="mood-percentage-label">
                                                        <p><?php echo $goodPercentage;?>%</p>
                                                    </div>
                                                </div><!-- .percentageGood -->

                                            <?php } ?>

                                            <!-- Display percentage bar and text for "Okay" mood if data exists -->
                                            <?php if(!empty($okayPercentage)){ ?>
                                                <!-- Create a percentage bar for the 'okay' mood category -->
                                                <div class="mood-percentage-bar okay-percentage" style = "width:<?php echo $okayPercentage;?>%;">
                                                    <!-- Container for the graphical representation of the 'okay' mood percentage -->
                                                    <div class="mood-bar-graph"></div>

                                                    <!-- Text displaying the percentage value of the 'okay' mood -->
                                                    <div class="mood-percentage-label"><p><?php echo $okayPercentage;?>%</p></div>
                                                </div><!-- .percentageOkay -->

                                            <?php } ?>

                                            <!-- Display percentage bar and text for "Bad" mood if data exists -->
                                            <?php if(!empty($badPercentage)){ ?>
                                                <!-- Create a percentage bar for the 'bad' mood category -->
                                                <div class="mood-percentage-bar bad-percentage" style = "width:<?php echo $badPercentage;?>%;">
                                                    <!-- Container for the graphical representation of the 'bad' mood percentage -->
                                                    <div class="mood-bar-graph"></div>

                                                    <!-- Text displaying the percentage value of the 'bad' mood -->
                                                    <div class="mood-percentage-label"><p><?php echo $badPercentage;?>%</p></div>
                                                </div><!-- .percentageBad -->
                                                
                                            <?php } ?>

                                            <!-- Display percentage bar and text for "Awful" mood if data exists -->
                                            <?php if(!empty($awfulPercentage)){ ?>
                                                <!-- Create a percentage bar for the 'awful' mood category -->
                                                <div class="mood-percentage-bar awful-percentage" style = "width:<?php echo $awfulPercentage;?>%;">
                                                    <!-- Container for the graphical representation of the 'awful' mood percentage -->
                                                    <div class="mood-bar-graph"></div>

                                                    <!-- Text displaying the percentage value of the 'awful' mood -->
                                                    <div class="mood-percentage-label"><p><?php echo $awfulPercentage;?>%</p></div>
                                                </div><!-- .percentageAwful -->

                                            <?php } ?>

                                        </div><!-- .mood-percentages -->
                                    </div><!-- .mood-percentages-wrapper -->
                                <?php } // End of mood count check to display mood percentages ?>

                                <!-- Wrapper for the moods per month -->
                                <div class="moods-summary">
                                        <?php 
                                        // Loop through each mood record fetched from the database
                                        while ($moodData = mysqli_fetch_assoc($resultMoods)) {
                                            $moodID = $moodData['MoodID']; // Get the current MoodID from the data

                                            // Query to count occurrences of the specific mood for the user in the selected month and year
                                            $queryMoodCount = "SELECT t.MoodID, COUNT(MoodID) AS cntMood 
                                                                FROM dailytracking d 
                                                                JOIN trackmoods t ON d.TrackingID = t.TrackingID 
                                                                WHERE UserID = $userID and Month(Date) = $selectedDate 
                                                                AND Year(Date) = $yearOfSelectedDate 
                                                                AND MoodID = $moodID
                                                                GROUP BY t.MoodID";
                                            $moodCountResult = mysqli_query($con,$queryMoodCount); // Execute the mood count query
                                            $moodCountData = mysqli_fetch_assoc($moodCountResult); // Fetch the mood count data ?>

                                            <!-- Wrapper for mood count display -->
                                            <div class="mood-item">
                                                <!-- Display the emoji representing the mood -->
                                                <p class="mood-emoji"> 
                                                    <?php echo $moodData['moodEmoji']; ?> 
                                                </p>

                                                <!-- Display the count of days associated with the mood, with class based on mood ID -->
                                                <p class="mood-count 
                                                    <?php 
                                                    if ($moodID == MOOD_GREAT) { 
                                                        echo ' mood-great'; // Class for 'great' mood
                                                    } elseif ($moodID == MOOD_GOOD) { 
                                                        echo ' mood-good'; // Class for 'good' mood
                                                    } elseif ($moodID == MOOD_OKAY) {
                                                        echo ' mood-okay'; // Class for 'okay' mood
                                                    } elseif ($moodID == MOOD_BAD) {
                                                        echo ' mood-bad'; // Class for 'bad' mood
                                                    } elseif ($moodID == MOOD_AWFUL) {
                                                        echo ' mood-awful'; // Class for 'awful' mood
                                                    } 
                                                    ?>"> 
                                                    
                                                    <?php 
                                                    // Display the count of the mood occurrences or '0' if not available
                                                    if (!empty($moodCountData['cntMood'])) {
                                                        echo $moodCountData['cntMood']; // Output the count of the mood
                                                    } else {
                                                        echo "0"; // Default to '0' if no count available
                                                    } 
                                                    ?>
                                                </p>

                                            </div> <!-- .MoodCnt -->

                                        <?php } // End of mood data loop ?>
                                </div><!-- .moods-per-month-summary greatMood -->
                            </div><!-- .mood-stats-container -->

                        </div><!-- .mood-header-container -->
                    </section><!-- .moodCount-wrapper -->

                    <!-- Wrapper for the sleep chart section -->
                    <section class="sleep-chart-section">

                        <div class="sleep-chart-header insights-header">
                            <!-- Display the title "Sleep Duration" in Japanese -->
                            <h3>睡眠時間</h3>
                        </div><!-- .sleep-chart-header -->

                        <!-- Main container for the sleep chart -->
                        <div class="sleep-chart-container">
                            <canvas id="sleepChartCanvas"></canvas> <!-- Canvas element for rendering the sleep chart -->
                            <p class="sleep-chart-time-label">時間</p> <!-- Label for time -->
                            <p class="sleep-chart-day-label">日</p> <!-- Label for days -->
                        </div><!-- .sleep-chart-container -->

                         <!-- Section explaining the chart's meaning -->
                        <div class="sleep-chart-legend">
                            <p class="sleep-chart-more-than">7時間以上</p> <!-- Text indicating more than 7 hours -->
                            <p class="sleep-chart-less-than">7時間未満</p> <!-- Text indicating less than 7 hours -->
                        </div><!-- .sleep-chart-legend -->

                    </section><!-- .sleep-chart-section -->
                </section><!-- .insights-middle-section -->


                <!-- Navigation section for selecting data categories -->
                <section class="insights-right-section">

                    <div class="insights-top insights-header">
                        <h3>ランキング</h3> <!-- Display the title "Ranking" in Japanese -->
                    </div><!-- .insights-top -->

                    <!-- Wrapper for month navigation -->
                    <div class="ranking-navigation-wrapper">

                        <!-- Top section of the navigation with instructions -->
                        <div class="navigation-instructions">
                            <p>カテゴリーをタップして詳細を表示</p> <!-- Instruction to tap a category to view details -->
                        </div><!-- .navigation-instructions -->

                        <!-- Main container for the navigation buttons -->
                        <div class="ranking-navigation-container">
                            <div class="category-tabs">
                                <!-- Unordered list of navigation tabs -->
                                <ul id="tabs">
                                    <li data-tab-target="#activityData" id="activityDataID" class="active navigation-tab">
                                        <div class="navigation-content" id="active-navigation">
                                            <p id="activityDataP">Activity</p> <!-- Tab for Activity data -->
                                        </div> 
                                    </li>
                                    <li data-tab-target="#companyData" class = "navigation-tab" id="companyDataID">
                                        <div class="navigation-content">
                                            <p id="companyDataP">P</p> <!-- Tab for Company data -->
                                        </div> 
                                    </li>
                                    <li data-tab-target="#locationsData" class="navigation-tab" id="locationsDataID">
                                        <div class="navigation-content">
                                            <p id="locationsDataP">L</p> <!-- Tab for Locations data -->
                                        </div> 
                                    </li>
                                    <li data-tab-target="#foodData" class="navigation-tab" id="foodDataID">
                                        <div class="navigation-content">
                                            <p id="foodDataP">F</p> <!-- Tab for Food data -->
                                        </div> 
                                    </li>
                                    <li data-tab-target="#weatherData" class="navigation-tab" id="weatherDataID">
                                        <div class="navigation-content">
                                            <p id="weatherDataP">W</p> <!-- Tab for Weather data -->
                                        </div> 
                                    </li>
                                </ul>
                            </div><!-- .buttons -->
                        </div><!-- .ranking-navigation-container -->
                        
                    </div><!-- .ranking-navigation-wrapper -->


                    <!-- Ranking Contents Wrapper -->
                    <div class="ranking-contents-wrapper">

                        <!-- Activity Data Section -->
                        <div id="activityData" data-tab-content class="active">
                            <!-- Section header for activities -->
                            <h3><span>アクティビティ</span></h3>

                            <!-- Container for activity rankings -->
                            <div class="ranking-container">

                                <?php 
                                // Check if any activities have been recorded this month 
                                if ($activitiesCount > 0) { 
                                    $currentRank = 1; // Initialize the rank counter for activities

                                    // Loop through each activity record
                                    while ($activityRecord = mysqli_fetch_assoc($resultActivities)) {
                                        $activityID = $activityRecord['ActivityID']; // Get the current activity ID

                                        // Query to get details of the activity based on its ID
                                        $queryActivityDetails = "SELECT * FROM activities WHERE ActivityID = $activityID";
                                        $activityDetailsResult = mysqli_query($con,$queryActivityDetails);
                                        $activityDetails = mysqli_fetch_assoc($activityDetailsResult); ?>

                                        <!-- Individual entry for the ranked activity -->
                                        <div class="ranking-content">
                                            <p class="ranking"><?php echo $currentRank;?></p> <!-- Display current rank -->
                                            <p class="rank-icon"><?php echo $activityDetails['ActivityIcon']; ?></p> <!-- Display the icon for the activity -->
                                            <p class="rank-name"><?php echo htmlspecialchars($activityDetails['ActivityName']); ?></p> <!-- Display the name of the activity -->
                                            <p class="rank-count"><?php echo $activityRecord['cntAct']; ?>回</p> <!-- Display the count of occurrences for this activity -->
                                        </div><!-- .ranking-content -->

                                        <?php 
                                        $currentRank++; // Increment rank counter
                                    } 

                                } else { // If no activities were recorded this month ?>

                                    <!-- Container for the message when there are no recorded activities -->
                                    <div class="no-records-wrapper">
                                        <div class="no-records-message">
                                            <p>今月はアクティビティが何も記録していない。</p> <!-- Message indicating no activities recorded -->
                                        </div>
                                    </div><!-- .no-records-wrapper -->

                                <?php } ?>

                            </div><!-- .ranking-contents -->
                        </div> <!-- .activityData -->

                        <!-- Company Data Section -->
                        <div id="companyData" data-tab-content>
                            <!-- Section header for displaying companies -->
                            <h3><span>人々</span></h3>
                        
                            <!-- Container for company rankings -->
                            <div class="ranking-container">
                                <?php 
                                // Check if there are any companies recorded
                                if ($companiesCount > 0) {  
                                    $currentRank = 1; // Initialize rank counter for companies

                                    // Loop through each company record
                                    while ($companyRecord = mysqli_fetch_assoc($resultCompanies)) {
                                        $companyID = $companyRecord['CompanyID']; // Get the current company's ID

                                        // Query to retrieve details of the specific company using its ID
                                        $queryCompanyDetails = "SELECT * FROM company WHERE CompanyID = $companyID";
                                        $companyDetailsResult = mysqli_query($con,$queryCompanyDetails);
                                        $companyDetails = mysqli_fetch_assoc($companyDetailsResult); ?>

                                        <!-- Individual ranking entry for the company -->
                                        <div class="ranking-content">
                                            <p class="ranking"><?php echo $currentRank;?></p> <!-- Display the current rank of the company -->
                                            <p class="rank-icon"><?php echo $companyDetails['CompanyIcon']; ?></p> <!-- Display the company's icon -->
                                            <p class="rank-name"><?php echo htmlspecialchars($companyDetails['CompanyName']); ?></p> <!-- Display the company's name -->
                                            <p class="rank-count"><?php echo $companyRecord['cntCom']; ?>回</p> <!-- Display the count of occurrences for this company -->
                                        </div><!-- .ranking-content -->

                                        <?php 
                                        $currentRank++; // Increment the rank counter for the next company
                                    } 
                                } else { // If no companies were recorded ?>
                                    
                                    <!-- Container for the message when there are no recorded companies -->
                                    <div class="no-records-wrapper">
                                        <div class="no-records-message">
                                            <p>今月は人々が何も記録していない。</p> <!-- Message indicating no recorded companies -->
                                        </div><!-- .no-records-message -->
                                    </div><!-- .no-records-wrapper -->

                                <?php } ?>

                            </div><!-- .ranking-contents -->
                        </div><!-- .companyData -->

                        <!-- Locations Data Section -->
                        <div id="locationsData" data-tab-content>
                            <!-- Section header for displaying locations -->
                            <h3><span>場所</span></h3>

                            <!-- Container for location rankings -->
                            <div class="ranking-container" >
                                <?php 
                                // Check if there are any locations recorded
                                if ($locationsCount > 0) { 
                                    $currentRank = 1; // Initialize rank counter for locations

                                    // Loop through each location record
                                    while ($locationRecord = mysqli_fetch_assoc($resultLocations)) {
                                        $locID = $locationRecord['LocationID']; // Get the current location's ID

                                        // Query to retrieve details of the specific location using its ID
                                        $queryLocationDetails = "SELECT * FROM locations WHERE LocationID = $locID";
                                        $resultLocationsALL = mysqli_query($con,$queryLocationDetails);
                                        $locationDetails = mysqli_fetch_assoc($resultLocationsALL); ?>

                                        <!-- Individual ranking entry for the location -->
                                        <div class="ranking-content">
                                            <p class="ranking"><?php echo $currentRank;?></p> <!-- Display the current rank of the location -->
                                            <p class="rank-icon"><?php echo $locationDetails['LocationIcon']; ?></p> <!-- Display the location's icon -->
                                            <p class="rank-name"><?php echo htmlspecialchars($locationDetails['LocationName']); ?></p> <!-- Display the location's name -->
                                            <p class="rank-count"><?php echo $locationRecord['cntLoc']; ?>回</p> <!-- Display the count of occurrences for this location -->
                                        </div><!-- .ranking-content -->

                                    <?php 
                                    $currentRank++; // Increment the rank counter for the next location

                                    } 
                                } else { // If no locations were recorded  ?>

                                    <!-- Container for the message when there are no recorded locations -->
                                    <div class="no-records-wrapper">
                                        <div class="no-records-message">
                                            <p>今月は場所が何も記録していない。</p> <!-- Message indicating no recorded locations -->
                                        </div><!-- .no-records-message -->
                                    </div><!-- .no-records-wrapper -->

                                <?php } ?>

                            </div><!-- .ranking-contents -->
                        </div><!-- ./locationsData -->
                
                        <!-- Food Data Section -->
                        <div id="foodData" data-tab-content>
                            <!-- Section header for displaying food items -->
                            <h3><span>食事</span></h3>

                            <!-- Container for food rankings -->
                            <div class="ranking-container" >
                                <?php 
                                // Check if there are any food items recorded
                                if ($foodCount > 0) { 
                                    $currentRank = 1; // Initialize rank counter for food items

                                    // Loop through each food record
                                    while ($foodRecord = mysqli_fetch_assoc($resultFood)) {
                                        $foodID = $foodRecord['FoodID']; // Get the current food item's ID

                                        // Query to retrieve details of the specific food item using its ID
                                        $queryFoodDetails = "SELECT * FROM foods WHERE FoodID = $foodID";
                                        $resultFoodDetails = mysqli_query($con,$queryFoodDetails);
                                        $foodDetails = mysqli_fetch_assoc($resultFoodDetails); ?>

                                        <!-- Individual ranking entry for the food item -->
                                        <div class="ranking-content">
                                            <p class="ranking"><?php echo $currentRank;?></p> <!-- Display the current rank of the food item -->
                                            <p class="rank-icon"><?php echo $foodDetails['FoodIcon']; ?></p> <!-- Display the food item's icon -->
                                            <p class="rank-name"><?php echo htmlspecialchars($foodDetails['FoodName']); ?></p> <!-- Display the food item's name -->
                                            <p class="rank-count"><?php echo $foodRecord['cntFood']; ?>回</p> <!-- Display the count of occurrences for this food item -->
                                        </div><!-- .ranking-content --> 

                                    <?php 
                                    $currentRank++; // Increment the rank counter for the next food item

                                    } 
                                } else { // If no food items were recorded ?>

                                    <!-- Container for the message when there are no recorded food items -->
                                    <div class="no-records-wrapper">
                                        <div class="no-records-message">
                                            <p>今月は食事が何も記録していない。</p> <!-- Message indicating no recorded food items -->
                                        </div><!-- .no-records-message -->
                                    </div><!-- .no-records-wrapper -->
                                    
                                <?php } ?>

                            </div><!-- .ranking-contents -->
                        </div><!-- .foodData -->

                        <!-- Weather Data Section -->
                        <div id="weatherData" data-tab-content>
                            <!-- Section header for displaying weather information -->
                            <h3><span>天候</span></h3>
                        
                            <!-- Container for weather rankings -->
                            <div class="ranking-container">
                                <?php 
                                // Check if there are any weather records
                                if ($weatherCount > 0) { 
                                    $currentRank = 1; // Initialize rank counter for weather records

                                    // Loop through each weather record
                                    while ($weatherRecord = mysqli_fetch_assoc($resultWeather)) {
                                        $WeatherID = $weatherRecord['WeatherID']; // Get the current weather record's ID

                                        // Query to retrieve details of the specific weather record using its ID
                                        $queryGetWeatherDetails = "SELECT * FROM weather WHERE WeatherID = $WeatherID";
                                        $resultWeatherDetails = mysqli_query($con,$queryGetWeatherDetails);
                                        $weatherDetails = mysqli_fetch_assoc($resultWeatherDetails); ?>
                                        
                                        <!-- Individual ranking entry for the weather record -->
                                        <div class="ranking-content">
                                            <p class="ranking"><?php echo $currentRank;?></p> <!-- Display the current rank of the weather record -->
                                            <p class="rank-icon"><?php echo $weatherDetails['WeatherIcon']; ?></p> <!-- Display the weather record's icon -->
                                            <p class="rank-name"><?php echo htmlspecialchars($weatherDetails['WeatherName']); ?></p> <!-- Display the weather record's name -->
                                            <p class="rank-count"><?php echo $weatherRecord['cntWea']; ?>回</p> <!-- Display the count of occurrences for this weather record -->
                                        </div><!-- .ranking-content -->

                                        <?php 
                                        $currentRank++; // Increment the rank counter for the next weather record
                                    } 
                                } else { // If no weather records were recorded ?>

                                    <!-- Container for the message when there are no recorded weather records -->
                                    <div class="no-records-wrapper">
                                        <div class="no-records-message">
                                            <p>今月は天候が何も記録していない。</p> <!-- Message indicating no recorded weather records -->
                                        </div><!-- .no-records-message -->
                                    </div><!-- .no-records-wrapper -->

                                <?php } ?>
                            </div><!-- .ranking-contents -->
                        </div><!-- .weatherData -->

                    </div><!-- .ranking-contents-wrapper -->

                </section><!-- .insights-right-section -->
            </div><!-- .insights-wrapper -->
        </div><!--.main-wrapper -->

    </body>

    <script>
        // This code generates a bar chart using Chart.js to visualize sleep data for each day of the month. 
        // It constructs an array of day labels (1-31) and a dataset with sleep times, colors, and chart configurations. 
        // Finally, it creates and renders the chart on a canvas element with the ID 'sleepChartCanvas'.

        // Create an array of labels for the x-axis representing the days of the month (1 to 31)
        const labels = [
            <?php for($i = 1; $i <= 31; $i++){?>
                '<?php echo $i; ?>', // Add each day number as a string label
            <?php }
            ?>
        ];

        // Define the data object for the chart
        const data = {
            labels: labels, // Assign the labels to the data object
            datasets: [{
                label: '睡眠時間', // Set the label for the dataset (Sleep Time in Chinese)
                BarLength: 2, // Set the length of the bars (custom property, may need further handling)
                data: <?php echo json_encode($sleepTimesInHours) ?>, // Populate the dataset with sleep times in hours from PHP
                backgroundColor: <?php echo json_encode($backgroundColor) ?>, // Set the background color for the bars
                borderColor: <?php echo json_encode($borderColor) ?>, // Set the border color for the bars
                borderWidth: 1 // Set the border width for the bars
            }]
        };

        // Configuration object for the chart
        const config = {
            type: 'bar', // Specify the chart type as 'bar'
            data: data, // Assign the previously defined data object
            options: {
                scales: {
                    y: {
                        beginAtZero: true // Ensure the y-axis starts at zero
                    }
                },
                plugins: {
                    legend: {
                        display: false, // Hide the legend for this chart
                    }
                }
            }
        };

        // Create a new chart instance and render it on the canvas with the id 'sleepChartCanvas'
        var sleepChartCanvas = new Chart(
            document.getElementById('sleepChartCanvas'), // Reference the canvas element for rendering
            config // Pass the configuration object to the Chart constructor
        );
    </script>
</html>
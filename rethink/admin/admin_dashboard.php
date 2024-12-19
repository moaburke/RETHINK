<?php
session_start(); // Start the session

// Define a constant for the base path
define('BASE_PATH', '../server-side/shared/');

include(BASE_PATH . "connections.php"); // Include databade connection file
include(BASE_PATH . "check_login.php"); // Include login check script to verify user authentication before accessing this page.
include(BASE_PATH . "timezone.php"); // Include the timezone configuration
include(BASE_PATH . "constants.php"); // Include the constants file
include(BASE_PATH . "header/admin_layout.php"); // Include the admin header layout file

// Define constants for time measurements
define('DAYS_IN_A_WEEK', 7); // Number of days in a week
define('MONTHS_IN_A_YEAR', 12); // Number of months in a year
define('JANUARY', 1); // Constant representing the month of January

// Check if the admin is logged in 
$adminData = check_login($con);
// Retrieve the UserID of the logged-in admin
$adminID = $adminData['UserID']; 

// Get today's date in Y-m-d format
$date = date("Y-m-d");

// Function to extract month FROM date
function getMonth($date) {
    return date('m', strtotime($date)); // Return month as a two-digit number
}
$currentMonth = getMonth($date); // Get current month number

// Function to extract day FROM date
function getDay($date) {
    return date('d', strtotime($date)); // Return day as a two-digit number
}
$currentDay = getDay($date); // Get current day number

// Function to extract year FROM date
function getYear($date) {
    return date('Y', strtotime($date)); // Return year as a four-digit number
}
$currentYear = getYear($date); // Get current year number

// Get the total number of days in the last month
$totalDaysLastMonth = date("t", strtotime(date('Y-m-d')." -1 month"));

// Determine the active date based on the current day number
if ($currentDay <= DAYS_IN_A_WEEK) {
    // If the current day is in the first week
    $daysPastWeek = DAYS_IN_A_WEEK - $currentDay; // Calculate days left in the week
    $daysPastWeek = $totalDaysLastMonth - $daysPastWeek; // Adjust for last month
    $monthNumPast = $currentMonth - 1; // Get previous month number
    // Set active date to a date in the previous month
    $activeDate = date("$currentYear-$monthNumPast-$daysPastWeek");

} else {
    // If the current day is in the second week or later
    $daysPastWeek = $currentDay - DAYS_IN_A_WEEK; // Calculate days since last week
    // Set active date to a date in the current month
    $activeDate = date("$currentYear-$currentMonth-$daysPastWeek");
}

// Get the date FROM one month ago
$oneMonthAgoDate = new \DateTime('1 month ago');
$oneMonthBack = $oneMonthAgoDate->format('Y-m-d'); // Format it as Y-m-d

// Determine the last month's number and year for queries
if ($currentMonth == JANUARY) {
    // If current month is January
    $previousMonth = MONTHS_IN_A_YEAR; // Last month is December
    $lastYear = $currentYear - 1; // Decrease year by one
} else {
    // For other months
    $previousMonth = $currentMonth - 1; // Just decrease month by one
    $lastYear = $currentYear; // Keep the same year
}

// Query to get total number of user
$getTotalUsersQuery = mysqli_query($con, "SELECT * FROM users where Role = '" . ROLE_USER ."'");
$totalUsersCount = mysqli_num_rows($getTotalUsersQuery); // Count total users

// Query to get number of registered users this month
$getRegisteredThisMonthQuery = mysqli_query($con, "SELECT * FROM users where Year(Created) = $currentYear AND Month(Created) = $currentMonth AND Role = '" . ROLE_USER ."'");
$registeredThisMonthCount = mysqli_num_rows($getRegisteredThisMonthQuery); // Count registered this month

// Query to get number of registered users last month
$getRegisteredLastMonthQuery = mysqli_query($con, "SELECT * FROM users where Year(Created) = $lastYear AND Month(Created) = $previousMonth AND Role = '" . ROLE_USER ."'");
$registeredLastMonthCount = mysqli_num_rows($getRegisteredLastMonthQuery); // Count registered last month

// Query to get active users in the past week
$getActiveUsersQuery = mysqli_query($con, "SELECT * FROM `dailytracking` WHERE date BETWEEN '$activeDate' AND '$date' GROUP BY UserID;");
$activeUsersCount = mysqli_num_rows($getActiveUsersQuery); // Count active users

// Query to get updates in the past month
$getUpdatesMonthQuery = mysqli_query($con, "SELECT * FROM `dailytracking` WHERE date BETWEEN '$oneMonthBack' AND '$date';");
$updatesMonthCount = mysqli_num_rows($getUpdatesMonthQuery); // Count updates this month

// Query to get updates in the past week
$getUpdatesWeekQuery = mysqli_query($con, "SELECT * FROM `dailytracking` WHERE date BETWEEN '$activeDate' AND '$date';");
$updatesWeekCount = mysqli_num_rows($getUpdatesWeekQuery); // Count updates last week

// Queries to get counts of moods over the past month
$queryGreatMoodsMonth = mysqli_query($con, 
    "SELECT d.TrackingID, d.Date, m.MoodID 
    FROM dailytracking d 
    INNER JOIN trackmoods m ON d.TrackingID = m.TrackingID 
    WHERE Date BETWEEN '$oneMonthBack' AND '$date' 
    AND MoodID = '" . MOOD_GREAT . "';"
);
$countGreatMoodsMonth = mysqli_num_rows($queryGreatMoodsMonth); // Count of great moods

$queryGoodMoodsMonth = mysqli_query($con, 
    "SELECT d.TrackingID, d.Date, m.MoodID 
    FROM dailytracking d 
    INNER JOIN trackmoods m ON d.TrackingID = m.TrackingID 
    WHERE Date BETWEEN '$oneMonthBack' AND '$date' 
    AND MoodID = '" . MOOD_GOOD . "';"
);
$countGoodMoodsMonth = mysqli_num_rows($queryGoodMoodsMonth); // Count of good moods

$queryOkayMoodsMonth = mysqli_query($con, 
    "SELECT d.TrackingID, d.Date, m.MoodID 
    FROM dailytracking d 
    INNER JOIN trackmoods m ON d.TrackingID = m.TrackingID 
    WHERE Date BETWEEN '$oneMonthBack' AND '$date' 
    AND MoodID = '" . MOOD_OKAY . "';"
);
$countOkayMoodsMonth = mysqli_num_rows($queryOkayMoodsMonth); // Count of okay moods

$queryBadMoodsMonth = mysqli_query($con, 
    "SELECT d.TrackingID, d.Date, m.MoodID 
    FROM dailytracking d 
    INNER JOIN trackmoods m ON d.TrackingID = m.TrackingID 
    WHERE Date BETWEEN '$oneMonthBack' AND '$date' 
    AND MoodID = '" . MOOD_BAD . "';"
);
$countBadMoodsMonth = mysqli_num_rows($queryBadMoodsMonth); // Count of bad moods

$queryAwfulMoodsMonth = mysqli_query($con, 
    "SELECT d.TrackingID, d.Date, m.MoodID 
    FROM dailytracking d 
    INNER JOIN trackmoods m ON d.TrackingID = m.TrackingID 
    WHERE Date BETWEEN '$oneMonthBack' AND '$date' 
    AND MoodID = '" . MOOD_AWFUL . "';"
);
$countAwfulMoodsMonth = mysqli_num_rows($queryAwfulMoodsMonth); // Count of awful moods

// Calculate mood percentages if updates exist
if (!empty($updatesMonthCount)) {
    $percentageGreatMoodsMonth = ($countGreatMoodsMonth / $updatesMonthCount) * 100; // Calculate percentage of great moods
    $greatPercentage = number_format($percentageGreatMoodsMonth, 0); // Format as integer
    
    $percentageGoodMoodsMonth = ($countGoodMoodsMonth / $updatesMonthCount) * 100; // Calculate percentage of good moods
    $goodPercentage = number_format($percentageGoodMoodsMonth, 0); // Format as integer
    
    $percentageOkayMoodsMonth = ($countOkayMoodsMonth / $updatesMonthCount) * 100; // Calculate percentage of okay moods
    $okayPercentage = number_format($percentageOkayMoodsMonth, 0); // Format as integer
    
    $percentageBadMoodsMonth = ($countBadMoodsMonth / $updatesMonthCount) * 100; // Calculate percentage of bad moods
    $badPercentage = number_format($percentageBadMoodsMonth, 0); // Format as integer
    
    $percentageAwfulMoodsMonth = ($countAwfulMoodsMonth / $updatesMonthCount) * 100; // Calculate percentage of awful moods
    $awfulPercentage = number_format($percentageAwfulMoodsMonth, 0); // Format as integer
}

// Get great moods over the past week
$queryGreatMoodsWeek = mysqli_query($con, 
    "SELECT d.TrackingID, d.Date, m.MoodID 
    FROM dailytracking d 
    INNER JOIN trackmoods m ON d.TrackingID = m.TrackingID 
    WHERE Date BETWEEN '$activeDate' AND '$date' 
    AND MoodID = '" . MOOD_GREAT . "';"
);
$countGreatMoodsWeek = mysqli_num_rows($queryGreatMoodsWeek); // Count of great moods

// Get good moods over the past week
$queryGoodMoodsWeek = mysqli_query($con, 
    "SELECT d.TrackingID, d.Date, m.MoodID 
    FROM dailytracking d 
    INNER JOIN trackmoods m ON d.TrackingID = m.TrackingID 
    WHERE Date BETWEEN '$activeDate' AND '$date' 
    AND MoodID = '" . MOOD_GOOD . "';"
);
$countGoodMoodsWeek = mysqli_num_rows($queryGoodMoodsWeek); // Count of good moods

// Get okay moods over the past week
$queryOkayMoodsWeek = mysqli_query($con, 
    "SELECT d.TrackingID, d.Date, m.MoodID 
    FROM dailytracking d 
    INNER JOIN trackmoods m ON d.TrackingID = m.TrackingID 
    WHERE Date BETWEEN '$activeDate' AND '$date' 
    AND MoodID = '" . MOOD_OKAY . "';"
);
$countOkayMoodsWeek = mysqli_num_rows($queryOkayMoodsWeek); // Count of okay moods

// Get bad moods over the past week
$queryBadMoodsWeek = mysqli_query($con, 
    "SELECT d.TrackingID, d.Date, m.MoodID 
    FROM dailytracking d 
    INNER JOIN trackmoods m ON d.TrackingID = m.TrackingID 
    WHERE Date BETWEEN '$activeDate' AND '$date' 
    AND MoodID = '" . MOOD_BAD . "';"
);
$countBadMoodsWeek = mysqli_num_rows($queryBadMoodsWeek); // Count of bad moods

// Get awful moods over the past week
$queryAwfulMoodsWeek = mysqli_query($con, 
    "SELECT d.TrackingID, d.Date, m.MoodID 
    FROM dailytracking d INNER JOIN trackmoods m ON d.TrackingID = m.TrackingID 
    WHERE Date BETWEEN '$activeDate' AND '$date' 
    AND MoodID = '" . MOOD_AWFUL . "';"
);
$countAwfulMoodsWeek = mysqli_num_rows($queryAwfulMoodsWeek); // Count of awful moods

if(!empty($updatesWeekCount)){
    $percentageGreatMoodsWeek = ($countGreatMoodsWeek / $updatesWeekCount) * 100; // Calculate percentage of great moods
    $greatPercentageWeek = number_format($percentageGreatMoodsWeek, 0); // Format as integer
    
    $percentageGoodMoodsWeek = ($countGoodMoodsWeek / $updatesWeekCount) * 100; // Calculate percentage of good moods
    $goodPercentageWeek = number_format($percentageGoodMoodsWeek, 0); // Format as integer
        
    $percentageOkayMoodsWeek = ($countOkayMoodsWeek / $updatesWeekCount) * 100; // Calculate percentage of okay moods
    $okayPercentageWeek = number_format($percentageOkayMoodsWeek, 0); // Format as integer
    
    $percentageBadMoodsWeek = ($countBadMoodsWeek / $updatesWeekCount) * 100; // Calculate percentage of bad moods
    $badPercentageWeek = number_format($percentageBadMoodsWeek, 0); // Format as integer
    
    $percentageAwfulMoodsWeek = ($countAwfulMoodsWeek / $updatesWeekCount) * 100; // Calculate percentage of awful moods
    $awfulPercentageWeek = number_format($percentageAwfulMoodsWeek, 0); // Format as integer
}

// Fetch all mood records from the database
$queryGetAllMoods = "SELECT * FROM moods"; // SQL query to select all records from the 'moods' table
$resultGetAllMoods = mysqli_query($con, $queryGetAllMoods); // Execute the query and store the result

// Fetch all mood records for weekly tracking
$resultGetAllMoodsWeek = mysqli_query($con, $queryGetAllMoods); // Execute the same query for weekly mood data
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Include the necessary assets for the head section via the includeHeadAssets function -->
        <?php includeHeadAssets(); ?>

        <!-- Include the Chart.js library for rendering charts and graphs -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>

    <body>
        <header class="sidebar-navigation dashboard-navigation">
            <!-- Render the sidebar navigation specifically for the admin section -->
            <?php renderAdminNavigation(); ?>
        </header>

        <!-- Render the header for the admin dashboard with logout functionality, using the admin's data -->
        <?php renderAdminHeaderWithLogout($adminData); ?>
        
        <div class="admin-main-wrapper">
            <!-- Main heading for the dashboard -->
            <h2>Dashboard</h2>

            <!-- Top section of the dashboard, which contains a personalized welcome message for the admin -->
            <div class="admin-welcome-section">
                <h3>
                    <span>Welcome back, </span>
                    <!-- Display the admin's first name, applying HTML special character encoding for security -->
                    <!-- Check if the first name contains only alphabetic characters. If it does, display it as is. -->
                    <!-- If it contains non-alphabetic characters, append "さん" (a respectful suffix commonly used in Japanese) -->
                    <span>
                        <?php 
                        if (ctype_alpha($adminData['FirstName'])) { 
                            echo htmlspecialchars($adminData['FirstName']);
                        } else {
                            echo htmlspecialchars($adminData['FirstName'])."さん";
                        } 
                        ?>
                    </span>
                </h3>
            </div><!-- .admin-welcome-section" -->

            <div class="user-statistics-wrapper">

                <!-- Section displaying the total number of users -->
                <div class="user-statistic total-users-statistic">
                    <!-- Heading for total user count -->
                    <h4>ユーザーの合計</h4>

                    <!-- Display the total user count, followed by the kanji character "人" to indicate "people" -->
                    <p class="statistic-count"><?php echo $totalUsersCount; ?>人</p>
                </div><!-- .user-statistic total-users-statistic -->

                <!-- Section displaying the number of active users and their percentage of the total -->
                <div class="user-statistic active-users-statistic">
                    <!-- Heading for active user count -->
                    <h4>アクティブユーザー</h4>

                    <!-- Display the active user count with "人" to indicate "people" -->
                    <p class="statistic-count"><?php echo $activeUsersCount; ?>人</p>

                    <!-- Display the percentage of active users out of the total user count -->
                    <p class="percentage-change">
                        <?php 
                        // Calculate active users as a percentage of the total user count
                        $activeUsersPercentage = $activeUsersCount / $totalUsersCount * 100;
                        // Display the percentage, rounded to the nearest whole number
                        echo round($activeUsersPercentage) ."%"; ?>
                    </p>
                </div><!-- .user-statistic active-users-statistic -->

                <!-- Section displaying the number of users registered this month -->
                <div class="user-statistic new-registrations-statistic">
                    <!-- Heading for "Registrations This Month" -->
                    <h4>今月登録人数</h4>

                    <!-- Display the count of users registered this month with "人" to indicate "people" -->
                    <p class="statistic-count"><?php echo $registeredThisMonthCount; ?>人</p>

                    <p class="percentage-change">
                        <?php 
                        // Check if the number of registrations this month is higher than last month
                        if ($registeredThisMonthCount > $registeredLastMonthCount) { ?>
                            <!-- Display an icon and percentage increase if registrations have increased -->
                            <p class="percentage-increase">
                                <!-- Icon for upward trend -->
                                <i class="fa-solid fa-arrow-trend-up"></i>
                        
                                <?php 
                                // Handle the case where last month’s count is zero to avoid division by zero
                                if ($registeredLastMonthCount == 0) {
                                    $lastMonthCountForCalculation = 1; // Avoid zero by setting to 1
                                } else {
                                    $lastMonthCountForCalculation = $registeredLastMonthCount; // Use actual count if not zero
                                }

                                // Calculate the percentage increase in registrations from last month to this month
                                $percentageIncrease = ($registeredThisMonthCount / $lastMonthCountForCalculation) * 100;

                                // Display the percentage increase, rounded to the nearest whole number
                                echo round($percentageIncrease) ."%"; ?>
                            </p>
                        <?php }else{ ?>
                            <!-- Display an icon and percentage decrease if registrations have decreased -->
                            <p class="percentage-decrease">
                                <!-- Icon for downward trend -->
                                <i class="fa-solid fa-arrow-trend-down"></i>

                                <?php 
                                // Handle the case where last month’s count is zero
                                if ($registeredLastMonthCount == 0) {
                                    $registeredLastMonthCount = 1; // Set to 1 to avoid division by zero
                                }

                                // Calculate the percentage decrease in registrations from last month to this month
                                $percentageDecrease = (1 - $registeredThisMonthCount / $registeredLastMonthCount) * 100;
                                // Display the percentage decrease, rounded to the nearest whole number
                                echo round($percentageDecrease) ."%"; 
                                ?>
                            </p>
                        <?php }?>
                    </p>
                </div><!-- .user-statistic new-registrations-statistic -->
                
                <!-- Section displaying the number of users registered last month -->
                <div class="user-statistic registrations-last-month-statistic">
                    <!-- Heading for "Registrations Last Month" -->
                    <h4>前月登録人数</h4>
                    
                    <!-- Display the count of users registered last month with "人" to indicate "people" -->
                    <p class="statistic-count"><?php echo $registeredLastMonthCount; ?>人</p>
                </div><!-- .user-statistic registrations-last-month-statistic -->
            </div><!-- .user-statistics-wrapper -->

            <section class="mood-statistic-wrapper">
                
                <!-- Section for displaying the total number of updates -->
                <div class="update-records">
                    <div>
                        <!-- Container for the monthly user records -->
                        <div class="record-heading">
                            <!-- Heading for monthly user records -->
                            <p>一か月のユーザー記録</p>
                        </div><!-- .record-heading -->

                        <div class="record-content">
                            <!-- Display the count of updates for the month -->
                            <p><?php echo $updatesMonthCount; ?> 件</p>

                            <!-- Display the label for the monthly record count -->
                            <p class="record-label">一か月記録件数</p>
                        </div><!-- .record-content -->
                    </div>

                    <div>
                        <!-- Container for the weekly user records -->
                        <div class="record-heading">
                            <!-- Heading for weekly user records -->
                            <p>一週間のユーザー記録</p>
                        </div><!-- .record-heading -->
                        <div class="record-content">
                            <!-- Display the count of updates for the week -->
                            <p><?php echo $updatesWeekCount; ?> 件</p>

                            <!-- Display the label for the weekly record count -->
                            <p class="record-label">一週間記録件数</p>
                        </div><!-- .record-content -->
                    </div>
                </div><!-- .update-records -->

                <!-- Section for displaying mood data for the past month -->
                <div class="monthly-mood-data">
                    <!-- Section heading for user mood over the past month -->
                    <h3>一か月のユーザー気分</h3>
                    <!-- Introductory text describing user mood for the last month -->
                    <p class="mood-intro-text">ここ一か月、ユーザーはこんな気分でした。</p>

                    <div class="mood-chart">
                        <!-- Canvas element for rendering the monthly mood chart -->
                        <canvas id="myChartMonth"></canvas>
                    </div><!-- .mood-chart -->

                    <div class="mood-count">
                        <div class="mood-data-wrapper">
                            <?php 
                            // Iterate through each mood retrieved from the database to display mood data
                            while ($rowMoods = mysqli_fetch_assoc($resultGetAllMoods)) {
                                $moodID = $rowMoods['MoodID']; // Store the current mood ID for use in queries

                                // Query to get the count of each mood for the past month
                                $queryGetCntMood = 
                                    "SELECT t.MoodID, d.Date, COUNT(MoodID) AS cntMood 
                                    FROM dailytracking d 
                                    JOIN trackmoods t ON d.TrackingID = t.TrackingID 
                                    WHERE Date BETWEEN '$oneMonthBack' AND '$date' 
                                    AND MoodID = $moodID
                                    GROUP BY t.MoodID"
                                ;

                                // Execute the count query and fetch the result
                                $resultGetCntMood = mysqli_query($con,$queryGetCntMood);
                                $moodCount = mysqli_fetch_assoc($resultGetCntMood); ?>

                                <div class="mood-record">
                                    <div class="mood-bar 
                                        <?php 
                                        // Determine the CSS class to apply for visual styling based on the mood ID
                                        if ($moodID == MOOD_GREAT) { 
                                            echo ' great-mood';
                                        } elseif ($moodID == MOOD_GOOD) { 
                                            echo ' good-mood';
                                        } elseif ($moodID == MOOD_OKAY) {
                                            echo ' okay-mood';
                                        } elseif ($moodID == MOOD_BAD) {
                                            echo ' bad-mood';
                                        } elseif ($moodID == MOOD_AWFUL) {
                                            echo ' awful-mood';
                                        } 
                                        ?>"> 
                                    </div><!-- .mood-bar -->

                                    <!-- Display the mood name in Japanese, escaping HTML for security -->
                                    <p class="mood-name"> <?php echo  htmlspecialchars($rowMoods['JapaneseMoodName']); ?> </p>

                                    <!-- Display the total count of occurrences for each mood -->
                                    <p class="mood-total-count"> 
                                        <?php
                                        // Check if a count for the mood exists; if it does, display it, otherwise show "0" 
                                        echo !empty($moodCount['cntMood']) ? $moodCount['cntMood'] : "0"; 
                                        ?> 件
                                    </p>
                                </div> <!-- .mood-record -->
                            <?php } ?>
                        </div><!-- .mood-data-wrapper -->
                    </div><!-- .mood-count -->
                </div><!-- .monthly-mood-data -->

                <!-- Section for displaying mood data for the past week -->
                <div class="weekly-mood-data">
                    <!-- Section heading for displaying user mood over the past week -->
                    <h3>一週間のユーザー気分</h3>
                    <!-- Introductory text describing user mood for the last week -->
                    <p class="mood-intro-text">ここ一週間、ユーザーはこんな気分でした。</p>

                    <div class="mood-chart">
                        <!-- Canvas element for rendering the weekly mood chart -->
                        <canvas id="myChartWeek"></canvas>
                    </div><!-- .mood-chart -->

                    <div class="mood-count">
                        <div class="mood-data-wrapper">
                            <?php 
                            // Loop through each mood record fetched for the past week
                            while ($rowMoodsWeek = mysqli_fetch_assoc($resultGetAllMoodsWeek)) {
                                $moodIDWeek = $rowMoodsWeek['MoodID']; // Store the current mood ID for further queries

                                // Query to get the count of each mood for the past week
                                $queryGetCntMoodWeek = 
                                    "SELECT t.MoodID, d.Date, COUNT(MoodID) AS cntMood 
                                    FROM dailytracking d 
                                    JOIN trackmoods t ON d.TrackingID = t.TrackingID 
                                    WHERE Date BETWEEN '$activeDate' AND '$date' 
                                    AND MoodID = $moodIDWeek
                                    GROUP BY t.MoodID"
                                ;

                                // Execute the count query and fetch the result
                                $resultGetCntMoodWeek = mysqli_query($con,$queryGetCntMoodWeek);
                                $cntMoodWeek = mysqli_fetch_assoc($resultGetCntMoodWeek); ?>

                                <div class="mood-record">
                                    <div class="mood-bar 
                                        <?php 
                                        // Assign a CSS class based on the mood ID for visual representation
                                        if ($moodIDWeek == MOOD_GREAT) { 
                                            echo ' great-mood';
                                        } elseif ($moodIDWeek == MOOD_GOOD) { 
                                            echo ' good-mood';
                                        } elseif ($moodIDWeek == MOOD_OKAY) {
                                            echo ' okay-mood';
                                        } elseif ($moodIDWeek == MOOD_BAD) {
                                            echo ' bad-mood';
                                        } elseif ($moodIDWeek == MOOD_AWFUL) {
                                            echo ' awful-mood';
                                        } ?>">
                                    </div><!-- .mood-bar -->

                                    <!-- Display the mood name in Japanese, escaping HTML for security -->
                                    <p class="mood-name"> <?php echo  htmlspecialchars($rowMoodsWeek['JapaneseMoodName']); ?> </p>

                                    <!-- Display the total count of occurrences for each mood -->
                                    <p class="mood-total-count"> 
                                        <?php 
                                        // Check if a count for the mood exists; if it does, display it, otherwise show "0"
                                        echo !empty($cntMoodWeek['cntMood']) ? $cntMoodWeek['cntMood'] : "0"; 
                                        ?> 件
                                    </p>
                                </div> <!-- .mood-record -->    
                            <?php } ?>
                        </div><!-- .mood-data-wrapper -->
                    </div><!-- .mood-count -->
                </div><!-- .weekly-mood-data -->

            </section><!-- .mood-statistic-wrapper -->
        </div><!-- .admin-main-wrapper -->

        
        <script>
            // Constants for mood colors
            const MOOD_COLORS = {
                GREAT: '#f7a420', // Color for 'Great' mood
                GOOD: '#f1b14acc', // Color for 'Good' mood
                OKAY: '#8BCCCA', // Color for 'Okay' mood
                BAD: '#9DC3E6', // Color for 'Bad' mood
                AWFUL: '#27627E' // Color for 'Awful' mood
            };

            // MONTH CHART SETUP
            // Retrieve the percentage data for each mood for the past month
            var greatPercentage = <?php echo json_encode($greatPercentage); ?>; // Percentage of 'Great' mood
            var goodPercentage = <?php echo json_encode($goodPercentage); ?>; // Percentage of 'Good' mood
            var okayPercentage = <?php echo json_encode($okayPercentage); ?>; // Percentage of 'Okay' mood
            var badPercentage = <?php echo json_encode($badPercentage); ?>; // Percentage of 'Bad' mood
            var awfulPercentage = <?php echo json_encode($awfulPercentage); ?>; // Percentage of 'Awful' mood
            
            // Get the canvas element for the monthly mood chart
            const monthChart = document.getElementById('myChartMonth');

            // Create a new doughnut chart for the monthly mood data
            new Chart(monthChart, {
                type: 'doughnut', // Chart type
                data: {
                    datasets: [{
                        label: '%', // Label for the dataset
                        data: [
                            greatPercentage, 
                            goodPercentage, 
                            okayPercentage, 
                            badPercentage, 
                            awfulPercentage
                        ], 
                        backgroundColor: [  // Colors for each mood segment
                            MOOD_COLORS.GREAT,
                            MOOD_COLORS.GOOD,
                            MOOD_COLORS.OKAY,
                            MOOD_COLORS.BAD,
                            MOOD_COLORS.AWFUL,
                            ],
                        borderWidth: 1 // Border width for chart segments
                    }]
                },
            });
            
            // WEEK CHART SETUP
            // Retrieve the percentage data for each mood for the past week
            var greatPercentageWeek = <?php echo json_encode($greatPercentageWeek); ?>; // Percentage of 'Great' mood this week
            var goodPercentageWeek = <?php echo json_encode($goodPercentageWeek); ?>; // Percentage of 'Good' mood this week
            var okayPercentageWeek = <?php echo json_encode($okayPercentageWeek); ?>; // Percentage of 'Okay' mood this week
            var badPercentageWeek = <?php echo json_encode($badPercentageWeek); ?>; // Percentage of 'Bad' mood this week
            var awfulPercentageWeek = <?php echo json_encode($awfulPercentageWeek); ?>; // Percentage of 'Awful' mood this week

            // Get the canvas element for the weekly mood chart
            const weekChart = document.getElementById('myChartWeek');

            // Create a new doughnut chart for the weekly mood data
            new Chart(weekChart, {
                type: 'doughnut',  // Chart type
                data: {
                    datasets: [{
                        label: '%', // Label for the dataset
                        data: [
                            greatPercentageWeek, 
                            goodPercentageWeek, 
                            okayPercentageWeek, 
                            badPercentageWeek, 
                            awfulPercentageWeek
                        ], 
                        backgroundColor: [
                            MOOD_COLORS.GREAT,
                            MOOD_COLORS.GOOD,
                            MOOD_COLORS.OKAY,
                            MOOD_COLORS.BAD,
                            MOOD_COLORS.AWFUL,
                        ],
                        borderWidth: 1 // Border width for chart segments
                    }]
                },
            });
        </script>
    </body>
</html>
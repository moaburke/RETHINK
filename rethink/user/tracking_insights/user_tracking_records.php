<?php
/**
 * Page Name: Display All Posts
 * Description: This page displays the user's daily tracking records, including moods and feelings. 
 *      The user can navigate through their records and view details about each day's tracking. 
 *      This page requires the user to be logged in to access their personal data.
 * Author: Moa Burke
 * Date: 2024-12-17
 * 
 * Included Files:
 * - connections.php: Database connection settings.
 * - check_login.php: Functions to validate user login status.
 * - header.php: Common header for user navigation.
 *
 */
session_start();// Start the session

// Define a constant for the base path
define('BASE_PATH', '../../server-side/shared/');

include(BASE_PATH . "connections.php"); // Include databade connection file
include(BASE_PATH . "check_login.php"); //Include the utiity functions
include(BASE_PATH . "timezone.php"); // Include the timezone configuration
include(BASE_PATH . "constants.php"); // Include the constants file
include(BASE_PATH . "header/user_layout.php"); // Include the user header layout file


// Check if the user is logged in and retrieve user data
$userData = check_login($con);
$userID = $userData['UserID']; // Retrieve the UserID from the session data

// Get today's date
$date = date("Y-m-d");

// Set the number of posts to display per page
$numberOfPostsPerPage=12;

// Check if the 'page' parameter is set in the GET request
if (isset($_GET["page"])) {
    // If set, retrieve the page number
    $currentPage = $_GET["page"];
} else {
    // If not set, default to the first page
    $currentPage = 1;
}

// Calculate the starting point for the SQL query based on the current page
$offset = (($currentPage - 1) * $numberOfPostsPerPage);

// Prepare the SQL query to retrieve daily tracking data for the user, ordered by date
$dailyTrackingQuery = "SELECT * FROM dailytracking WHERE UserID = $userID ORDER BY DATE desc limit $offset,$numberOfPostsPerPage ";
// Execute the query to get the current set of results
$dailyTrackingResults = mysqli_query($con, $dailyTrackingQuery);
// Execute the query again to get the next set of results (for later use)
$nextDailyTrackingResults = mysqli_query($con, $dailyTrackingQuery);
// Get the number of rows returned by the first query
$numberOfDailyTrackingRows = mysqli_num_rows($dailyTrackingResults); 

// Check if the 'displayMoods' parameter is set in the POST request
if (isset($_POST["displayMoods"])) {
    // If set, retrieve the mood value
    $displayedMood = $_POST["displayMoods"];
} else {
     // If not set, default the mood to a wildcard character (*)
    $displayedMood = "*";
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Load mutual head components and necessary scripts/styles -->
        <?php includeHeadAssets(); ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> <!-- Font Awesome for icons -->
    </head>
    
    <body>
        <!-- Header Section -->
        <header class="sidebar-navigation insights-navigation">
            <?php renderUserNavigation(); // Include the common user header ?>
        </header>
        <!-- Logout and sticky header for logged-in user -->
        <?php renderUserHeaderWithLogout($userData); ?>

        <div class="main-wrapper">

            <h2>Insights</h2>

            <!-- Breadcrumb navigation -->
            <div class="breadcrumbs">
                <a href="../user_home.php"><p>Top Page</p></a>
                <i class="fa-solid fa-angle-right fa-sm"></i>
                <a href="./insights.php"><p>Insights</p></a>
                <i class="fa-solid fa-angle-right fa-sm"></i>
                <p class="bread-active">Tracking Records</p>
            </div><!--. breadcrumbs -->

            <!-- Container for user mood tracking details -->
            <section class="tracking-records-wrapper">
                <!-- Section heading for displaying all tracked records -->
                <h3>
                    <span class="japanese-title">すべての記録</span> <!-- Japanese title for "All Tracking Records" -->
                    <span class="english-title">Tracking Records</span> <!-- English title for "All Tracking Records" -->
                </h3>

                <div class="tracking-records-list">
                    <?php 
                    // Check if there are any daily tracking records
                    if ($numberOfDailyTrackingRows > 0) {
                        // Loop through each daily tracking record
                        while ($dailyTrackingRecord = mysqli_fetch_assoc($dailyTrackingResults)) {
                            $currentTrackingID = $dailyTrackingRecord['TrackingID']; // ID of the current tracking record

                            // Query to get mood names based on selected tracking ID
                            $moodQuery = "SELECT * FROM moods WHERE MoodID IN (SELECT MoodID FROM trackmoods WHERE TrackingID = $currentTrackingID)";

                            // Execute mood query and fetch results
                            $moodResult = mysqli_query($con, $moodQuery);
                            $moodData = mysqli_fetch_assoc($moodResult);
                            $numberOfMoodRecords = mysqli_num_rows($moodResult); // Count of mood records for the current day

                            // If there are mood records, extract mood details
                            if ($numberOfMoodRecords > 0 ) {
                                $currentMoodName = $moodData['MoodName']; // English name of the mood
                                $currentJapaneseMoodName = $moodData['JapaneseMoodName']; // Japanese name of the mood
                                $currentMoodID = $moodData['MoodID']; // ID of the mood
                                $currentMoodEmoji = $moodData['moodEmojiColor']; // Emoji representing the mood
                            }
        
                            // Query to get feeling names associated with the current tracking ID
                            $feelingQuery = 
                                "SELECT FeelingName 
                                FROM feelings 
                                WHERE FeelingID IN (
                                    SELECT FeelingID 
                                    FROM trackfeelings 
                                    WHERE TrackingID = $currentTrackingID
                                )";
                            $feelingResult = mysqli_query($con, $feelingQuery);
                            $feelingData = mysqli_fetch_assoc($feelingResult);

                            // If there are feeling records, extract feeling details
                            if (!empty($feelingData)) {
                                $currentFeelingName = $feelingData['FeelingName']; // Name of the primary feeling
                                $numberOfFeelingRecords = mysqli_num_rows($feelingResult); // Count of feeling records for the current day
                                $additionalFeelingsCount = $numberOfFeelingRecords - 1; // Count additional feelings
                            }
        
                            $trackingDate = $dailyTrackingRecord['Date']; // Date associated with the current tracking entry ?> 

                            <div class="tracking-day-container">
                                <!-- Link to the daily view for the specific tracking date -->
                                <a href="./daily_tracking_overview.php?date=<?php echo $trackingDate; ?>">
                                    <!-- Left section showing the mood color associated with the tracking entry -->
                                    <div class="tracking-day-left-indicator 
                                        <?php 
                                        // Apply emoji class based on mood ID
                                        if ($numberOfMoodRecords > 0) {
                                            if ($currentMoodID == MOOD_GREAT) { 
                                                echo ' mood-great'; // Class for a great mood
                                            } elseif($currentMoodID == MOOD_GOOD) { 
                                                echo ' mood-good'; // Class for a good mood
                                            } elseif($currentMoodID == MOOD_OKAY) {
                                                echo ' mood-okay'; // Class for an okay mood
                                            } elseif($currentMoodID == MOOD_BAD) {
                                                echo ' mood-bad'; // Class for a bad mood
                                            } elseif($currentMoodID == MOOD_AWFUL) {
                                                echo ' mood-awful'; // Class for an awful mood
                                            } 
                                        } else {
                                            echo ' mood-status-none'; // Class indicating no mood registered
                                        } ?>">
                                    </div><!-- .tracking-day-left-indicator -->

                                    <!-- Right section containing details about the tracking entry, including date and mood information -->
                                    <div class="tracking-day-details">
                                        <!-- Display the date associated with the current tracking entry -->
                                        <p class="tracking-day-date"><?php echo $trackingDate; ?></p>

                                        <!-- Display the mood emoji based on the current mood ID -->
                                        <p class="tracking-day-emoji <?php 
                                            if ($numberOfMoodRecords > 0) {
                                                if($currentMoodID == MOOD_GREAT) { 
                                                    echo ' emoji-status-great'; // Emoji class for a great mood
                                                } elseif($currentMoodID == MOOD_GOOD) { 
                                                    echo ' emoji-status-good'; // Emoji class for a good mood
                                                } elseif($currentMoodID == MOOD_OKAY) {
                                                    echo ' emoji-status-okay'; // Emoji class for an okay mood
                                                } elseif($currentMoodID == MOOD_BAD) {
                                                    echo ' emoji-status-bad'; // Emoji class for a bad mood
                                                } elseif($currentMoodID == MOOD_AWFUL) {
                                                    echo ' emoji-status-awful'; // Emoji class for an awful mood
                                                } 
                                            } else {
                                                echo ' mood-status-none'; // Class indicating no mood registered
                                            } ?>">
                                            
                                            <?php 
                                            // Display the current mood emoji or a placeholder if no mood is registered
                                            if ($numberOfMoodRecords > 0) {
                                                echo $currentMoodEmoji;
                                            } else { 
                                                echo '<i class="fa-solid fa-question"></i>'; // Placeholder icon for missing mood
                                            } ?>
                                        </p>

                                        <!-- Display the mood name in both Japanese and English for the current tracking entry -->
                                        <p class="tracking-day-mood-name">
                                            <span class="mood-name-japanese">
                                                <?php 
                                                // Check if there are any mood records for the current entry
                                                if ($numberOfMoodRecords > 0) {
                                                    // If there are mood records, output the Japanese mood name
                                                    echo htmlspecialchars($currentJapaneseMoodName); 
                                                }?>
                                            </span>
                                            <span>
                                                <?php 
                                                // Check again for mood records to display the English mood name
                                                if ($numberOfMoodRecords > 0) {
                                                    // If there are mood records, output the English mood name
                                                    echo htmlspecialchars($currentMoodName); 
                                                }?>
                                            </span>
                                        </p>

                                        <div class="feeling-wrapper">
                                            <p> <?php 
                                                // Check if there are any feelings associated with the current mood entry
                                                if (!empty($feelingData)) {
                                                    // If feelings data exists, output the primary feeling name
                                                    echo htmlspecialchars($currentFeelingName); ?>

                                                    <span>
                                                        <?php 
                                                        if ($additionalFeelingsCount > 0) { // Display the count of additional feelings, indicating that there are more feelings associated 
                                                            echo "+ " . $additionalFeelingsCount . " more"; // Show the count of additional feelings associated with the mood
                                                        } ?>
                                                    </span>
                                                <?php } // End of the feelings check ?> 
                                            </p>
                                        </div><!-- .feeling-wrapper -->

                                    </div><!-- .tracking-day-details -->

                                </a> <!-- End of link to the daily view for the specific tracking date -->
                                
                            </div><!-- .oneDay -->
                        <?php } // End of the daily tracking record loop ?>
                    <?php } // End of the check for daily tracking records ?>

                </div><!-- .tracking-records-list -->

                <?php 
                // Query to select all daily tracking records for the specified user
                $dailyTrackingQuery = "SELECT * FROM dailytracking WHERE UserID = $userID" ;
                $resultDays = mysqli_query($con, $dailyTrackingQuery);

                // Get the number of daily tracking records for pagination
                $numberOfDays = mysqli_num_rows($resultDays); 

                // Calculate the total number of pages needed for pagination
                $totalPages = ceil($numberOfDays/$numberOfPostsPerPage);

                // Determine which page is currently displayed
                if (!empty($_REQUEST['page'])) {
                    // If a page number is specified, use it as the current page
                    $currentPage = $_REQUEST['page'];
                } else {
                    // If no page number is specified, default to the first page
                    $currentPage = 1; // Set currentPage to 1 to display the first set of results
                }
                
                // Set the last page based on the total number of pages
                $lastPage = $totalPages; ?>

                <div class="pagination-wrapper">
                    <?php 
                    // Display pagination controls only if there is more than one page of results
                    if (!($totalPages <= 1)) { ?>
                        <?php 
                        // If the current page is not the first page, show links to navigate
                        if ($currentPage != 1){ ?>
                            <!-- Link to the first page -->
                            <a class="pagination-icon pagination-double-arrows" href="user_tracking_records.php?page=<?php echo 1; ?>"> 
                                <i class="fa-solid fa-angles-left"></i>
                            </a>
                            <!-- Link to the previous page -->
                            <a class="pagination-icon" href="user_tracking_records.php?page=<?php echo ($currentPage - 1); ?>">
                                <i class="fa-solid fa-angle-left"></i>
                            </a>
                        <?php 
                        } else { ?>
                            <!-- Display non-clickable icons for the first page, indicating no previous pages exist -->
                            <i class="fa-solid fa-angles-left pagination-disabled pagination-double-arrows"></i>
                            <i class="fa-solid fa-angle-left pagination-disabled"></i>
                        <?php } ?>

                        <div class="pagination-pages">
                            <?php
                            // Loop through each page number and create links
                            for ($pageNumber = 1; $pageNumber <=  $totalPages; $pageNumber++) {?>
                                <?php 
                                // Check if the current iteration corresponds to the active page
                                if ($pageNumber == $currentPage) {
                                    // Highlight the current page number
                                    echo '<a class="pagination-active-page">'.$pageNumber.'</a>';
                                } else { ?>
                                    <!-- Create a link for the other page numbers to navigate to that specific page -->
                                    <p class="pagination-page"><?php echo "<a href='user_tracking_records.php?page=".$pageNumber."'>".$pageNumber."</a>"; ?></p>
                                <?php } ?>
                            <?php } ?>
                        </div><!-- .pagination-pages -->

                        <?php 
                        // Check if the current page is not the last page
                        if ($currentPage != $lastPage) { ?>
                            <!-- Link to the next page -->
                            <a class="pagination-icon" href="user_tracking_records.php?page=<?php echo ($currentPage + 1); ?>"> 
                                <i class="fa-solid fa-angle-right"></i> <!-- Icon for next page navigation -->
                            </a>
                            <!-- Link to the last page -->
                            <a class="pagination-icon pagination-double-arrows" href="user_tracking_records.php?page=<?php echo $lastPage; ?>">
                                <i class="fa-solid fa-angles-right"></i> <!-- Icon for direct navigation to the last page -->
                            </a>
                        <?php 
                        } else { ?> <!-- If the current page is the last page, display non-clickable icons -->
                            <i class="fa-solid fa-angle-right pagination-disabled"></i> <!-- Non-clickable icon for next page -->
                            <i class="fa-solid fa-angles-right pagination-disabled pagination-double-arrows"></i> <!-- Non-clickable icon for the last page -->
                        <?php } ?>

                    <?php } ?> <!-- End of pagination control display logic -->
                </div><!-- .pagination-wrapper -->

            </section><!-- .tracking-records-wrapper -->  

        </div><!-- .main-wrapper -->

    </body>
</html>
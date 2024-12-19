<?php
/**
 * File: user_tracking_records.php
 * Author: Moa Burke
 * Date: 2024-11-04
 * Description: This script retrieves and displays the posts (mood tracking records) 
 *      for a specific user in the admin dashboard. Each post includes mood, feelings, 
 *      activities, companies, locations, food, weather, sleep time, and associated memos.
 *
 *      The user’s mood is displayed in Japanese, and the posts are ordered by date 
 *      in descending order. 
 * 
 * Functionality:
 *  - Fetches user-specific mood tracking data from the database.
 *  - Displays each post with detailed attributes for user-friendly insights.
 *  - Orders records by date to ensure the latest entries are easily accessible.
 *  - Handles edge cases such as no available tracking records for the user.
*/
session_start(); // Start the session

// Define a constant for the base path
define('BASE_PATH', '../../../server-side/shared/');

include(BASE_PATH . "connections.php"); // Include databade connection file
include(BASE_PATH . "check_login.php"); // Include login check script to verify user authentication before accessing this page.
include(BASE_PATH . "timezone.php"); // Include the timezone configuration
include(BASE_PATH . "header/admin_layout.php"); // Include the admin header layout file

define('MINUTES_TO_HOURS', 60); // Constant to convert minutes to hours

// Check if the admin is logged in 
$adminData = check_login($con);
// Retrieve the UserID of the logged-in admin
$adminID = $adminData['UserID']; 

// Check if a user ID has been passed as a GET parameter
if (isset($_GET['id'])) {
    // Store the user ID from the GET request
    $selectedUserID = $_GET['id'];

    // Query to retrieve all data for the selected user from the users table
    $queryUserData = "SELECT * FROM users WHERE UserID = $selectedUserID";
    $userDataResult = mysqli_query($con, $queryUserData);
    $userData = mysqli_fetch_assoc($userDataResult);

    // Query to get the total number of posts (tracking entries) by the selected user
    $getTotalPosts = mysqli_query($con, "SELECT * FROM dailytracking WHERE UserID = $selectedUserID");
    $rowsPost = mysqli_num_rows($getTotalPosts);

    // Query to retrieve all posts by the selected user, ordered by date in descending order
    $getPosts = mysqli_query($con, "SELECT * FROM dailytracking WHERE UserID = $selectedUserID ORDER BY Date desc");
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Include the necessary assets for the head section via the includeHeadAssets function -->
        <?php includeHeadAssets(); ?>
    </head>

    <body>
        <header class="sidebar-navigation manage-users-navigation">
            <!-- Render the sidebar navigation specifically for the admin section -->
            <?php renderAdminNavigation(); ?>
        </header>

        <div class="admin-main-wrapper">
            <!-- Render the header for the admin dashboard with logout functionality, using the admin's data -->
            <?php renderAdminHeaderWithLogout($adminData); ?>

            <h2>Users</h2>

            <!-- Breadcrumb navigation for easy access to user management and current user details -->
            <div class="breadcrumbs breadcrumbs-admin">
                <!-- Link to navigate back to the Manage Users page -->
                <a href="../manage_users.php">
                    <p>Manage Users</p>
                </a> 

                <!-- Right arrow icon to indicate breadcrumb separation -->
                <i class="fa-solid fa-angle-right fa-sm"></i>

                <!-- Link to the User Details page for the selected user -->
                <a href="./user_details.php?id=<?php echo $selectedUserID;?>">
                    <p>User Details</p>
                </a>

                <!-- Right arrow icon to indicate breadcrumb separation -->
                <i class="fa-solid fa-angle-right fa-sm"></i>
                
                <!-- Current page indicator, showing the user is on the User Posts page -->
                <p class="bread-active">User Posts</p> <!-- Current page indicator -->
            </div><!-- .breadcrumbs -->
            
            <!-- Section displaying a header for the user's posts -->
            <div class="user-tracking-records-header">
                <!-- Japanese and English titles for "User's Posts" -->
                <h3>
                    <span class="japanese-title">ユーザーの記録</span>
                    <span class="english-title">Users Posts</span>
                </h3>
            </div><!-- .user-tracking-records-header -->

            <div class="user-tracking-records-wrapper">

                <!-- Section containing the user's posts -->
                <section class="user-posts-wrapper">
                    
                    <!-- Wrapper for the latest post by the user -->
                    <div class="user-posts-list">
                        <!-- Header section for the latest post display -->
                        <div class="posts-header">
                            <!-- Displaying the user's first name and a message (in Japanese) for their mood tracking record -->
                            <h3>
                                <?php echo htmlspecialchars($userData['FirstName']); ?>
                                さんの気分記録
                            </h3>
                        </div><!-- .posts-header -->

                        <?php 
                        // Check if there are posts available for the user
                        if ($rowsPost > 0) {
                            // Loop through each post entry for the user
                            while ($userPostData = mysqli_fetch_assoc($getPosts)) {
                                // Retrieve the unique tracking ID and date for each post
                                $currentTrackingID = $userPostData['TrackingID'];
                                $postDate = $userPostData['Date'];

                                // Fetch the mood data for the current tracking entry
                                $moodQuery = mysqli_query($con, 
                                    "SELECT m.MoodName, m.JapaneseMoodName 
                                    FROM trackmoods t 
                                    JOIN moods m ON t.MoodID = m.MoodID 
                                    WHERE TrackingID = $currentTrackingID"
                                );
                                $moodData = mysqli_fetch_assoc($moodQuery);
                                // Store the Japanese version of the mood name in a variable for later display
                                $japaneseMoodName = $moodData['JapaneseMoodName'];

                                // Fetch the feeling data associated with the current tracking entry
                                $feelingsQuery = mysqli_query($con, 
                                    "SELECT f.FeelingName 
                                    FROM trackfeelings t 
                                    JOIN feelings f ON t.FeelingID = f.FeelingID 
                                    WHERE TrackingID = $currentTrackingID"
                                );
                                $totalFeelings = mysqli_num_rows($feelingsQuery);

                                // Fetch activities associated with the current tracking entry
                                $activityQuery = mysqli_query($con, 
                                    "SELECT a.ActivityIcon, a.ActivityName 
                                    FROM trackactivities t 
                                    JOIN activities a ON t.ActivityID = a.ActivityID 
                                    WHERE TrackingID = $currentTrackingID"
                                );
                                $totalActivities = mysqli_num_rows($activityQuery);

                                // Fetch company data associated with the current tracking entry
                                $companyQuery = mysqli_query($con, 
                                    "SELECT a.CompanyIcon, a.CompanyName 
                                    FROM trackcompany t 
                                    JOIN company a ON t.CompanyID = a.CompanyID 
                                    WHERE TrackingID = $currentTrackingID"
                                );
                                $totalCompanies = mysqli_num_rows($companyQuery);
                                
                                // Fetch location data associated with the current tracking entry
                                $locationQuery = mysqli_query($con, 
                                    "SELECT a.LocationIcon, a.LocationName 
                                    FROM tracklocations t 
                                    JOIN locations a ON t.LocationID = a.LocationID 
                                    WHERE TrackingID = $currentTrackingID"
                                );
                                $totalLocations = mysqli_num_rows($locationQuery);
                                
                                // Fetch food data associated with the current tracking entry
                                $foodQuery = mysqli_query($con, 
                                    "SELECT a.FoodIcon, a.FoodName 
                                    FROM trackfoods t 
                                    JOIN foods a ON t.FoodID = a.FoodID 
                                    WHERE TrackingID = $currentTrackingID"
                                );
                                $totalFoods = mysqli_num_rows($foodQuery);
                                
                                // Fetch weather data associated with the current tracking entry
                                $weatherQuery = mysqli_query($con, 
                                    "SELECT a.WeatherIcon, a.WeatherName 
                                    FROM trackweather t 
                                    JOIN weather a ON t.WeatherID = a.WeatherID 
                                    WHERE TrackingID = $currentTrackingID"
                                );
                                $totalWeatherEntries = mysqli_num_rows($weatherQuery);

                                // Query to retrieve sleep time associated with the current tracking entry
                                $sleepTimeQuery = "SELECT * FROM tracksleeptime WHERE TrackingID = $currentTrackingID";
                                $sleepTimeResult = mysqli_query($con, $sleepTimeQuery);
                                $sleepTimeData = mysqli_fetch_assoc($sleepTimeResult); // Retrieve the sleep time data as an associative array

                                // Check if sleep time data is available and convert the time from minutes to hours
                                if (!empty($sleepTimeData['sleepTime'])) {
                                    // Convert sleep time from minutes to hours and store in $sleepTime
                                    $sleepTime = ($sleepTimeData['sleepTime']) / MINUTES_TO_HOURS;
                                } else {
                                    // Set $sleepTime to 0 if no data is found for sleep time
                                    $sleepTime = 0; // Default to 0 if no sleep data is available
                                }
                                
                                // Query to check if there are any memos associated with the tracking entry
                                $memoQuery = mysqli_query($con, "SELECT * FROM memos WHERE TrackingID = $currentTrackingID");
                                $totalMemos = mysqli_num_rows($memoQuery);
                                ?>

                                <!-- Wrapper for each individual post -->
                                <div class="user-post">

                                    <!-- Display the date of the post -->
                                    <p class="post-date">
                                        <?php echo $postDate ; ?>
                                    </p>

                                    <!-- Display the mood of the post with a class based on mood level -->
                                    <p class="mood-label 
                                        <?php 
                                        // Check the mood in Japanese and assign a corresponding CSS class
                                        if ($japaneseMoodName == "最高") { 
                                            echo "great-mood";
                                        } elseif ($japaneseMoodName == "良い") { 
                                            echo "good-mood";
                                        } elseif ($japaneseMoodName == "普通") { 
                                            echo "okay-mood";
                                        } elseif ($japaneseMoodName == "悪い") { 
                                            echo "bad-mood";
                                        } elseif ($japaneseMoodName == "最悪") { 
                                            echo "awful-mood";
                                        }?>">
                                        
                                        <!-- Output the mood in Japanese -->
                                        <?php echo $japaneseMoodName ; ?>
                                    </p>
                   
                                    <!-- Display the feelings associated with the post, if any -->
                                    <p class="post-feelings">
                                        <?php 
                                         // Check if there are any feelings associated with this post
                                        if ($totalFeelings > 0) { ?>  
                                            <?php 
                                            // Loop through each feeling and display it
                                            while ($feelings = mysqli_fetch_assoc($feelingsQuery)) { ?>
                                                <span><?php echo htmlspecialchars($feelings['FeelingName']); ?> </span>
                                            <?php } ;?>
                                        <?php } ?>
                                    </p>
                        
                                    <!-- Wrapper for post contents -->
                                    <div class="post-main-content">
                                        <?php 
                                        // Check if there are any activities associated with the post
                                        if ($totalActivities > 0) {
                                            // Loop through each activity and display its icon and name
                                            while($activity = mysqli_fetch_assoc($activityQuery)) { ?>
                                                <p>
                                                    <!-- Display the activity icon -->
                                                    <span><?php echo $activity['ActivityIcon']; ?> </span> 

                                                    <!-- Display the activity name -->
                                                    <span><?php echo htmlspecialchars($activity['ActivityName']); ?> </span>
                                                </p>
                                            <?php } ;?>
                                        <?php } ?>

                                        <?php 
                                        // Check if there are any companies associated with the post
                                        if ($totalCompanies > 0) {
                                            // Loop through each company and display its icon and name
                                            while ($company = mysqli_fetch_assoc($companyQuery)) { ?>
                                                <p>
                                                    <!-- Display the company icon -->
                                                    <span><?php echo $company['CompanyIcon']; ?> </span>

                                                    <!-- Display the company name -->
                                                    <span><?php echo htmlspecialchars($company['CompanyName']); ?> </span>
                                                </p>
                                            <?php } ;?>
                                        <?php } ?>

                                        <?php 
                                        // Check if there are any locations associated with the post
                                        if ($totalLocations > 0) {
                                            // Loop through each location and display its icon and name
                                            while ($location = mysqli_fetch_assoc($locationQuery)) { ?>
                                                <p>
                                                    <!-- Display the location icon -->
                                                    <span><?php echo $location['LocationIcon']; ?> </span>

                                                    <!-- Display the location name -->
                                                    <span><?php echo htmlspecialchars($location['LocationName']); ?> </span>
                                                </p>
                                            <?php } ;?>
                                        <?php } ?>

                                        <?php 
                                        // Check if there are any foods associated with the post
                                        if ($totalFoods > 0) {
                                            // Loop through each food entry and display its icon and name
                                            while ($food = mysqli_fetch_assoc($foodQuery)) { ?>
                                                <p>
                                                    <!-- Display the food icon -->
                                                    <span><?php echo $food['FoodIcon']; ?> </span>

                                                    <!-- Display the food name -->
                                                    <span><?php echo htmlspecialchars($food['FoodName']); ?> </span>
                                                </p>
                                            <?php } ;?>
                                        <?php } ?>

                                        <?php 
                                        // Check if there are any weather entries associated with the post
                                        if ($totalWeatherEntries > 0) {
                                            // Loop through each weather entry and display its icon and name
                                            while ($weather = mysqli_fetch_assoc($weatherQuery)) { ?>
                                                <p>
                                                    <!-- Display the weather icon -->
                                                    <span><?php echo $weather['WeatherIcon']; ?> </span>

                                                    <!-- Display the weather name -->
                                                    <span><?php echo htmlspecialchars($weather['WeatherName']); ?> </span>
                                                </p>
                                            <?php } ;?>
                                        <?php } ?>
                                    </div><!-- .post-main-content -->

                                    <?php 
                                    // Check if sleep time is greater than 0
                                    if ($sleepTime > 0) { ?>
                                        <!-- Display the sleep time information -->
                                        <p class="post-sleep">
                                            <span>睡眠時間:</span> <!-- Label for sleep time -->
                                            <?php echo $sleepTime; ?>時間 <!-- Output the sleep time in hours -->
                                        </p>
                                    <?php } ?>

                                    <?php 
                                    // Check if there are any memos associated with the post
                                    if ($totalMemos > 0) {
                                        // Fetch the first memo from the query result
                                        $getMemo = mysqli_fetch_assoc($memoQuery);
                                        $memoContent = $getMemo['Memo']; ?>

                                        <!-- Display the memo content -->
                                        <p class="post-memo"><?php echo htmlspecialchars($memoContent); ?></p>
                                    <?php } ?>
                                </div><!-- .user-post -->

                            <?php } ?> <!-- End of the conditional block for displaying user posts -->

                        <?php }else { ?> <!-- If no posts are found, display the following message -->
                            <div class="no-mood-records">
                                <p>ユーザがまだ何も記録していない</p> <!-- Message indicating the user has not recorded anything yet -->
                            </div><!-- .no-mood-records -->
                        <?php } ?>  <!-- End of the else block -->

                    </div><!-- .user-posts-list -->
            </div><!-- .user-tracking-records-wrapper -->
            
        </div><!-- .admin-main-wrapper -->
    </body>
</html>
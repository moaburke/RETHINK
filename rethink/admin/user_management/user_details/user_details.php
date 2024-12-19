<?php
/**
 * File: user_details.php
 * Author: Moa Burke
 * Date: 2024-11-04
 * Description: This script retrieves and displays detailed information about a specific user 
 *      from the database. It checks for admin authentication, gathers user data including 
 *      tracking records, mood statistics, and recent posts. The page allows the admin 
 *      to show or hide user posts.
 *
 *      Additionally, mood percentages are retrieved from PHP variables and used 
 *      to create a doughnut chart visualizing the distribution of different moods.
 *      The chart is generated using the Chart.js library and includes custom 
 *      color coding for each mood category.
 * 
 * Functionality:
 *  - Validates admin user authentication to ensure authorized access to user details.
 *  - Gathers and displays user information, including tracking records and recent posts.
 *  - Provides functionality for admins to show or hide specific user posts.
 *  - Generates a doughnut chart using mood percentage data, visually representing 
 *      mood distributions with distinct color coding for clarity.
*/
session_start(); // Start the session

// Define a constant for the base path
define('BASE_PATH', '../../../server-side/shared/');

include(BASE_PATH . "connections.php"); // Include databade connection file
include(BASE_PATH . "check_login.php"); // Include login check script to verify user authentication before accessing this page.
include(BASE_PATH . "timezone.php"); // Include the timezone configuration
include(BASE_PATH . "constants.php"); // Include the constants file
include(BASE_PATH . "header/admin_layout.php"); // Include the admin header layout file

// Check if the admin is logged in 
$adminData = check_login($con);
// Retrieve the UserID of the logged-in admin
$adminID = $adminData['UserID']; 

// Get today's date in Y-m-d format
$date = date("Y-m-d");
// Get the current date and time in Y-m-d H:i:s format
$dateTime = date("Y-m-d H:i:s");

// Check if a user ID has been provided in the URL
if (isset($_GET['id'])) {
    // Retrieve the user ID from the URL
    $userID = $_GET['id'];

    // Query to get the user data based on UserID
    $userQuery = "SELECT * FROM users WHERE UserID = $userID";
    $resultUser = mysqli_query($con, $userQuery);
    $userData = mysqli_fetch_assoc($resultUser); // Fetch the user data 

    // Get all daily tracking records for the user
    $recentPostsQuery = mysqli_query($con, "SELECT * FROM dailytracking WHERE UserID = $userID");
    $recentPostsCount = mysqli_num_rows($recentPostsQuery); // Count total posts

    // Get the last 5 daily tracking records for the user
    $recentPostsQuery = mysqli_query($con, "SELECT * FROM dailytracking WHERE UserID = $userID ORDER BY Date desc LIMIT 5");
    $recentPostsCounts = mysqli_num_rows($recentPostsQuery); // Count recent posts
    
    // Query to retrieve all tracking records with a "great" mood for the specified user
    $greatMoodsQuery = mysqli_query($con, 
        "SELECT d.TrackingID, d.Date, m.MoodID 
        FROM dailytracking d 
        INNER JOIN trackmoods m ON d.TrackingID = m.TrackingID 
        WHERE UserID = '$userID' 
        AND MoodID = '" . MOOD_GREAT ."';"
    );
    $greatMoodsCount = mysqli_num_rows($greatMoodsQuery); // Count of great moods

    // Query to retrieve all tracking records with a "good" mood for the specified user
    $goodMoodsQuery = mysqli_query($con, 
        "SELECT d.TrackingID, d.Date, m.MoodID 
        FROM dailytracking d 
        INNER JOIN trackmoods m ON d.TrackingID = m.TrackingID 
        WHERE UserID = '$userID' 
        AND MoodID = '" . MOOD_GOOD ."';"
    );
    $goodMoodsCount = mysqli_num_rows($goodMoodsQuery); // Count of good moods

    // Query to retrieve all tracking records with an "okay" mood for the specified user
    $okayMoodsQuery = mysqli_query($con, 
        "SELECT d.TrackingID, d.Date, m.MoodID 
        FROM dailytracking d 
        INNER JOIN trackmoods m ON d.TrackingID = m.TrackingID 
        WHERE UserID = '$userID' 
        AND MoodID = '" . MOOD_OKAY ."';"
    );
    $okayMoodsCount = mysqli_num_rows($okayMoodsQuery); // Count of okay moods

    // Query to retrieve all tracking records with a "bad" mood for the specified user
    $badMoodsQuery = mysqli_query($con, 
        "SELECT d.TrackingID, d.Date, m.MoodID 
        FROM dailytracking d 
        INNER JOIN trackmoods m ON d.TrackingID = m.TrackingID 
        WHERE UserID = '$userID' 
        AND MoodID = '" . MOOD_BAD ."';"
    );
    $badMoodsCount = mysqli_num_rows($badMoodsQuery); // Count of bad moods

    // Query to retrieve all tracking records with an "awful" mood for the specified user
    $awfulMoodsQuery = mysqli_query($con, 
        "SELECT d.TrackingID, d.Date, m.MoodID 
        FROM dailytracking d 
        INNER JOIN trackmoods m ON d.TrackingID = m.TrackingID 
        WHERE UserID = '$userID' 
        AND MoodID = '" . MOOD_AWFUL ."';"
    );
    $awfulMoodsCount = mysqli_num_rows($awfulMoodsQuery); // Count of awful moods

    // Calculate the percentage of moods based on the total recent posts
    if (!empty($recentPostsCount)) {
        // Calculate the percentage of "great" moods based on recent posts
        $greatMoodsPercentage = ($greatMoodsCount / $recentPostsCount) * 100;
        // Format the "great" moods percentage to a whole number
        $greatPercentage = number_format($greatMoodsPercentage, 0);
        
        // Calculate the percentage of "good" moods based on recent posts
        $goodMoodsPercentage = ($goodMoodsCount / $recentPostsCount) * 100;
        // Format the "good" moods percentage to a whole number
        $goodPercentage = number_format($goodMoodsPercentage, 0);
        
        // Calculate the percentage of "okay" moods based on recent posts
        $okayMoodsPercentage = ($okayMoodsCount / $recentPostsCount) * 100;
        // Format the "okay" moods percentage to a whole number
        $okayPercentage = number_format($okayMoodsPercentage, 0);
        
        // Calculate the percentage of "bad" moods based on recent posts
        $badMoodsPercentage = ($badMoodsCount / $recentPostsCount) * 100;
        // Format the "bad" moods percentage to a whole numbers
        $badPercentage = number_format($badMoodsPercentage, 0);
        
        // Calculate the percentage of "awful" moods based on recent posts
        $awfulMoodsPercentage = ($awfulMoodsCount / $recentPostsCount) * 100;
        // Format the "awful" moods percentage to a whole number
        $awfulPercentage = number_format($awfulMoodsPercentage, 0);
    }

    // Query to get posts associated with the user
    $queryPosts = mysqli_query($con,
        "SELECT 
            u.UserID AS UserID, 
            p.PostID AS PostID, 
            p.PostedText AS PostedText, 
            DATE(Date) AS Date, 
            TIME(Date) AS Time, 
            l.likes, 
            c.comments, 
            m.JapaneseMoodName AS MoodName 
            FROM Posts p 
        LEFT JOIN users u ON p.UserID = u.UserID
        LEFT JOIN moods m ON p.MoodID = m.MoodID 
        LEFT OUTER JOIN (
            SELECT PostID, CommentID, COUNT(CommentID) AS comments 
            FROM comments 
            GROUP BY PostID
        ) 
        c ON p.PostID = c.PostID
        LEFT OUTER JOIN (
            SELECT PostID, LikeID, COUNT(LikeID) AS likes 
            FROM postlikes 
            GROUP BY PostID 
        ) 
        l ON p.PostID = l.PostID 
        WHERE u.UserID = '$userID' 
        GROUP BY p.PostID  
        ORDER BY Date DESC;
    ");
    $recentPostsCounts = mysqli_num_rows($queryPosts);  // Count total posts retrieved

    // Retrieve mood data
    $queryGetMoods = "SELECT * FROM moods";
    $resultGetMoods = mysqli_query($con, $queryGetMoods);

    // Unhide (show) a post if requested
    if (isset($_POST['show'])) {
        $postID = $_POST['show']; // Get the post ID to show
        $query = "UPDATE posts SET Hidden = '" . POST_VISIBLE . "'  WHERE PostID = $postID"; // Query to unhide the post
        $queryRun = mysqli_query($con, $query);
    
        // Redirect to user details
        header('Location: ./user_details.php?id='. $userID);
    }

    // Hide a post if requested
    if (isset($_POST['hide'])) {
        $postID = $_POST['hide']; // Get the post ID to hide
        $query = "UPDATE posts SET Hidden = '" . POST_HIDDEN . "' WHERE PostID = $postID"; // Query to hide the post
        $queryRun = mysqli_query($con, $query);

        // Redirect to user details
        header('Location: ./user_details.php?id='. $userID);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Include the necessary assets for the head section via the includeHeadAssets function -->
        <?php includeHeadAssets(); ?>

        <!-- Load the Chart.js library for creating charts -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>

    <body>
        <header class="sidebar-navigation manage-users-navigation">
            <!-- Render the sidebar navigation specifically for the admin section -->
            <?php renderAdminNavigation(); ?>
        </header>

        <!-- Render the header for the admin dashboard with logout functionality, using the admin's data -->
        <?php renderAdminHeaderWithLogout($adminData); ?>

        <div class="admin-main-wrapper">

            <h2>Users</h2>
            
            <!-- Breadcrumb navigation for easy access to user management and current user details -->
            <div class="breadcrumbs breadcrumbs-admin">
                    <!-- Link to navigate back to the Manage Users page -->
                    <a href="../manage_users.php">
                        <p>Manage Users</p>
                    </a> 

                    <!-- Right arrow icon to indicate breadcrumb separation -->
                    <i class="fa-solid fa-angle-right fa-sm"></i>

                    <!-- Current page indicator for User Details, highlighting the active page in the breadcrumb -->
                    <p class="bread-active">User Details</p>
            </div><!-- .breadcrumbs -->
            
            <!-- Section for displaying user details with titles in both Japanese and English -->
            <div class="user-details-header">
                <h3>
                    <span class="japanese-title">ユーザー詳細</span> <!-- Japanese title for user details -->
                    <span class="english-title">User Details</span> <!-- English title for user details -->
                    </h3>
                </h3>
            </div><!-- .user-details-header -->

            <div class="users-detail-wrapper">

                <section class="user-info-section left-column">
                    <!-- User profile image container -->
                    <div class="user-profile-img-container">
                        <!-- Display user's profile image -->
                        <img src="../../../assets/user-img/<?php echo $userData['profileImg']; ?>" alt="User's Profile Image">
                    </div><!-- .user-profile-img-container -->
                    
                     <!-- Table for displaying user details -->
                    <div class="user-details-table">
                        <table>
                            <!-- Row for user's first name -->
                            <tr>
                                <td>First Name</td>
                                <td><?php echo htmlspecialchars($userData['FirstName']); ?></td> <!-- Safely output user's first name -->
                            </tr>

                            <!-- Row for user's last name -->
                            <tr>
                                <td>Last Name</td>
                                <td><?php echo htmlspecialchars($userData['LastName']); ?></td> <!-- Safely output user's last name -->
                            </tr>

                            <!-- Row for user's username -->
                            <tr>
                                <td>Username</td>
                                <td><?php echo htmlspecialchars($userData['Username']); ?></td> <!-- Safely output user's username -->
                            </tr>

                            <!-- Row for user's email address -->
                            <tr>
                                <td>Email</td>
                                <td><?php echo htmlspecialchars($userData['Email']); ?></td> <!-- Safely output user's email -->
                            </tr>

                            <!-- Row for the account creation date -->
                            <tr>
                                <td>Account Created</td>
                                <td><?php echo $userData['Created']; ?></td> <!-- Display the date the account was created -->
                            </tr>
                        </table>
                    </div><!-- .user-details-table -->
                </section><!-- .user-info-section left-column -->

                <!-- Section for displaying the user's mood information -->
                <section class="mood-info-container middle-column">

                    <!-- Top section displaying the user's first name followed by "さんの気分" (meaning "user's mood") -->
                    <div class="mood-info-header">
                        <h3><?php echo htmlspecialchars($userData['FirstName']);?>さんの気分</h3>
                    </div><!-- .mood-info-header -->

                    <?php 
                    // Check if the user has any recent posts
                    if ($recentPostsCount > 0) { ?>
                        <!-- Main content area for user mood data -->
                        <div class="mood-content-section">

                            <!-- Chart area for displaying mood statistics -->
                            <div class="mood-chart-container">
                                <canvas id="myChart"></canvas> <!-- Canvas element for rendering mood chart -->
                            </div><!-- .mood-chart-container -->

                            <!-- -->
                            <div class="mood-count">
                                <div class="mood-data-wrapper">
                                    <?php 
                                    // Check if recent posts count is not empty
                                    if (!empty($recentPostsCount)) {
                                        // Loop through each mood retrieved from the database
                                        while ($moodData = mysqli_fetch_assoc($resultGetMoods)) {
                                            $currentMoodID = $moodData['MoodID'];

                                            // Query to count occurrences of each mood for the user
                                            $countMoodQuery = 
                                                "SELECT t.MoodID, COUNT(MoodID) AS cntMood 
                                                FROM dailytracking d 
                                                JOIN trackmoods t ON d.TrackingID = t.TrackingID 
                                                WHERE MoodID = $currentMoodID AND UserID = '$userID'
                                                GROUP BY t.MoodID"
                                            ;

                                            // Execute the mood count query
                                            $countMoodResult = mysqli_query($con,$countMoodQuery);
                                            $moodCount = mysqli_fetch_assoc($countMoodResult); ?>

                                            <!-- Mood data display -->
                                            <div class="mood-record">
                                                <div class="mood-bar 
                                                    <?php 
                                                    if ($currentMoodID == MOOD_GREAT) { 
                                                        echo ' great-mood';
                                                    } elseif ($currentMoodID == MOOD_GOOD) { 
                                                        echo ' good-mood';
                                                    } elseif ($currentMoodID == MOOD_OKAY) {
                                                        echo ' okay-mood';
                                                    } elseif ($currentMoodID == MOOD_BAD) {
                                                        echo 'bad-mood ';
                                                    } elseif ($currentMoodID == MOOD_AWFUL) {
                                                        echo ' awful-mood';
                                                    } 
                                                    ?>"> 
                                                </div><!-- .mood-bar -->
                                                    <p class="mood-name"> <?php echo htmlspecialchars($moodData['JapaneseMoodName']); ?> </p>
                                                    <p class="mood-total-count"> 
                                                        <?php 
                                                        // Check if a count for the mood exists; if it does, display it, otherwise show "0" 
                                                        echo !empty($moodCount['cntMood']) ? $moodCount['cntMood'] : "0"; 
                                                        ?> 件
                                                    </p>
                                            </div> <!-- .moodData -->
                                        <?php } 
                                    }?>
                            </div><!-- .count -->
                        </div><!-- .mood-content-section -->

                        <?php } else { // If no recent posts exist ?>
                            <!-- Message indicating that the user has not recorded any moods -->
                            <div class="no-mood-records">
                                <p>ユーザーはまだ何も記録していない。</p> <!-- Message indicating no records -->
                            </div><!-- .no-mood-records -->
                        <?php } // End of recent posts conditio ?>
                </section><!-- .mood-info-container middle-column -->

                <section class="mood-summary-column right-column">

                    <!-- Section displaying total updates and recent mood records -->
                    <div class="mood-record-summary">
                        <h3>気分記録</h3> <!-- Title: Mood Records -->
                        <p class="record-count"><?php echo $recentPostsCount; ?>件</p> <!-- Display the number of records -->
                        <p class="record-label">記録件数</p> <!-- Label for record count -->
                    </div><!-- .mood-record-summary -->
                    
                    <div class="recent-mood-wrapper">
                        <div class="recent-mood-header">
                            <h3>最近の気分記録</h3> <!-- Title: Recent Mood Records -->

                            <div class="view-all-btn">
                                <!-- Link to view all tracking records for the user -->
                                <a href="user_tracking_records.php?id=<?php echo $userID;?>">See All<i class="fa-solid fa-angle-right"></i></a>
                            </div>
                        </div><!-- .recent-mood-header -->

                        <?php 
                        if ($recentPostsCount > 0) {
                            while ($recentPost = mysqli_fetch_assoc($recentPostsQuery)) {
                                $currentTrackingID = $recentPost['TrackingID'];  // Get the tracking ID
                                $recordDate = $recentPost['Date']; // Get the date of the mood record

                                // Query to get mood information based on tracking ID
                                $moodQuery = mysqli_query($con, 
                                    "SELECT m.MoodName, m.JapaneseMoodName 
                                    FROM trackmoods t 
                                    JOIN moods m ON t.MoodID = m.MoodID 
                                    WHERE TrackingID = $currentTrackingID"
                                );
                                $moodResult = mysqli_fetch_assoc($moodQuery); // Fetch mood result
                                $japaneseMoodName = $moodResult['JapaneseMoodName']; // Get the Japanese mood name

                                // Query to get feelings associated with the mood record
                                $feelingQuery = mysqli_query($con, 
                                    "SELECT f.FeelingName 
                                    FROM trackfeelings t 
                                    JOIN feelings f ON t.FeelingID = f.FeelingID 
                                    WHERE TrackingID = $currentTrackingID"
                                );
                                $feelingCount = mysqli_num_rows($feelingQuery); // Count the number of feelings associated ?>

                                <div class="post">
                                    <!-- Display the date of the record -->
                                    <p class="record-date"><?php echo $recordDate ; ?></p>

                                    <p class="mood-label <?php 
                                        // Determine mood class based on the japanese mood name
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

                                        <!-- Display the mood -->
                                        <?php echo $japaneseMoodName ; ?>
                                    </p>

                                    <p class="mood-feeling">
                                        <?php 
                                        // Check if there are any associated feelings for the mood record
                                        if ($feelingCount > 0) {
                                            $feelings = mysqli_fetch_assoc($feelingQuery); // Fetch feeling data ?>

                                            <!-- Display the main feeling associated with the mood -->
                                            <span><?php echo htmlspecialchars($feelings['FeelingName']); ?> </span>

                                            <?php 
                                            // If there are additional feelings, display a summary of how many more exist
                                            if ($feelingCount > 1) {  ?>
                                                <!-- Display count of additional feelings -->
                                                <span class="additional-feelings"> <?php echo "+" . $feelingCount - 1 . " more"; ?></span> 
                                            <?php }?>

                                        <?php } ?>
                                    </p>
                                </div><!-- .post -->
                            <?php }  // End of the loop through recent post ?> 

                        <?php } else { // If there are no recent posts ?>
                            <div class="no-mood-records">
                                <p>ユーザーはまだ何も記録していない。</p> <!-- Message indicating no records have been made by the user -->
                            </div><!-- .no-mood-records -->
                        <?php } // End of the check for recent posts ?>

                    </div><!-- .recent-post-mood-wrapperper -->
                </section><!-- .mood-summary-column right-column -->    
            </div><!-- .user-detail-wrapper -->

            <div class="user-detalis-bottom-wrapper">
                <section class="user-posts-wrapper">

                    <!-- Wrapper for the latest posts by the user -->
                    <div class="user-posts-list">

                        <!-- Header for the posts section -->
                        <div class="posts-header">
                            <h3>フィードでの登録</h3> <!-- Title: Registered Posts in Feed -->
                        </div><!-- .posts-header -->

                        <?php 
                        // Check if there are recent posts
                        if ($recentPostsCounts > 0) {
                            // Loop through each post retrieved from the database
                            while ($postData = mysqli_fetch_assoc($queryPosts)) { 
                                $postID = $postData['PostID']; // Unique identifier for the post
                                $moodName = $postData['MoodName']; // Name of the mood associated with the post
                                $postContent = $postData['PostedText']; // Content of the post
                                $userPosted = $postData['UserID']; // ID of the user who posted
                                $creationDate = $postData['Date']; // Date the post was created
                                $creationTime = $postData['Time']; // Time the post was created
                                $numberOfLikes = $postData['likes']; // Number of likes on the post
                                $numberOfComments = $postData['comments'];  // Number of comments on the post
                                
                                // Query to check if the post is hidden
                                $checkHiddenQuery = "SELECT * FROM posts WHERE PostID = '$postID' AND Hidden = '" . POST_HIDDEN . "' ";
                                $hiddenCheckResult = mysqli_query($con, $checkHiddenQuery);
                                $isHidden = mysqli_num_rows($hiddenCheckResult); // Count of hidden posts
                                ?>

                                <div class="user-post <?php 
                                    // Check if the post is hidden and apply the 'post-hidden' class if it is
                                    if ($isHidden > 0) {
                                        echo "post-hidden"; // CSS class to visually hide the post
                                    }?>">

                                    <div class="post-timestamp">
                                        <!-- Display the date the post was created -->
                                        <p class="date"><?php echo $creationDate; ?></p>

                                        <!-- Display the time the post was created -->
                                        <p class="time"><?php echo $creationTime; ?></p>
                                    </div><!-- .post-timestamp -->

                                    <div class="post-main-content">
                                        <div class="post-mood-wrapper">
                                            <p class="post-mood-label <?php 
                                                // Determine the mood class based on the mood name for styling
                                                if ($moodName == "最高") { 
                                                    echo "great-mood";
                                                } elseif ($moodName == "良い") { 
                                                    echo "good-mood";
                                                } elseif ($moodName == "普通") { 
                                                    echo "okay-mood";
                                                } elseif ($moodName == "悪い") { 
                                                    echo "bad-mood";
                                                } elseif ($moodName == "最悪") { 
                                                    echo "awful-mood";
                                                }?>">
                                                
                                                <!-- Display the mood name -->
                                                <?php echo $moodName ; ?>
                                            </p>
                                        </div><!-- .post-mood-wrapper -->

                                        <div class="post-content">
                                            <!-- Display the post content, safely escaped for HTML -->
                                            <p><?php echo htmlspecialchars($postContent); ?></p>
                                        </div><!-- .post-content -->
                                    </div><!-- .post-main-content -->
                                    
                                    <div class="post-interactions">
                                        <!-- Container for user interactions with the post -->
                                        <div class="post-actions">
                                            <div class="post-likes">
                                                <!-- Container for user interactions with the post -->
                                                <i class="fa-regular fa-heart"></i>

                                                <!-- Display the number of likes; show "0" if no likes -->
                                                <p><?php echo $numberOfLikes > 0 ? $numberOfLikes : "0"; ?></p>
                                            </div><!-- .post-likes -->

                                            <div class="post-comments">
                                                <!-- Icon representing comments -->
                                                <i class="fa-regular fa-comment"></i>

                                                <!-- Display the number of comments. If there are no comments, display "0". -->
                                                <p><?php echo $numberOfComments > 0 ? $numberOfComments : "0"; ?></p>
                                            </div><!-- .post-comments -->
                                        </div><!-- .post-actions -->

                                        <!-- Container for the visibility toggle of the post -->
                                        <div class="post-visibility-toggle">
                                            <?php 
                                            // Check if the post is currently hidden
                                            if ($isHidden > 0) { ?>
                                                <div class="post-hidden-controls">
                                                    <!-- Icon indicating the post is hidden -->
                                                    <i class="fa-solid fa-eye-slash"></i>
                                                    <form action="" method="POST">
                                                        <!-- Button to show the hidden post -->
                                                        <button type="submit" name="show" value="<?=$postID;?>">Show</button>
                                                    </form>
                                                </div><!-- .post-hidden-controls -->

                                            <?php } else { // If post is not hidden ?>

                                                <div class="post-visible-controls">
                                                    <!-- Icon indicating the post is visible -->
                                                    <i class="fa-solid fa-eye"></i>
                                                    <form action="" method="POST">
                                                        <!-- Button to hide the visible post -->
                                                        <button type="submit" name="hide" value="<?=$postID;?>">Hide</button>
                                                    </form>
                                                </div><!-- .showing -->
                                            <?php } ?>
                                        </div><!-- .post-visibility-toggle -->

                                    </div><!-- .post-interactions -->
                                </div><!-- .post -->
                            <?php } // End of the loop through posts ?>

                        <?php }else { ?>
                            <!-- Display a message when the user has not posted any records -->
                            <div class="no-mood-records">
                                <p>ユーザーはまだ何も記録していない。</p> <!-- Message indicating the user has no records -->
                            </div><!-- .no-mood-records -->
                        <?php } // End of recent posts check ?>

                    </div><!-- .user-posts-list -->
                </section><!-- .user-posts-wrapper -->
            </div><!-- .user-detalis-bottom-wrapper -->
        </div><!-- .admin-main-wrapper -->
    </body>

    
    <script>
        // Constants for mood colors
        const MOOD_COLORS = {
            GREAT: '#f7a420', // Color for 'Great' mood
            GOOD: '#f1b14acc', // Color for 'Good' mood
            OKAY: '#8BCCCA', // Color for 'Okay' mood
            BAD: '#9DC3E6', // Color for 'Bad' mood
            AWFUL: '#27627E' // Color for 'Awful' mood
        };

        // Get all elements with the class 'displaycomments'
        var commentElements = document.getElementsByClassName("displaycomments");
        var index;
        var totalElements = commentElements.length;

        // Add click event listeners to each comment element
        for (index = 0; index < totalElements; index++) {
            commentElements[index].addEventListener('click', function(){
                // Toggle the 'commentSection-hide' class to show/hide comments
                this.classList.toggle('commentSection-hide');
            })
        }
            
        /* Retrieve PHP variables for mood percentages */
        var greatMoodPercentage = <?php echo json_encode($greatPercentage); ?>;
        var goodMoodPercentage = <?php echo json_encode($goodPercentage); ?>;
        var okayMoodPercentage = <?php echo json_encode($okayPercentage); ?>;
        var badMoodPercentage = <?php echo json_encode($badPercentage); ?>;
        var awfulMoodPercentage = <?php echo json_encode($awfulPercentage); ?>;
        
        // Get the canvas element for the chart
        const chartElement = document.getElementById('myChart');

        // Create a new doughnut chart using Chart.js
        new Chart(chartElement, {
            type: 'doughnut', // Label for the dataset
            data: {
                datasets: [{ 
                    label: '%', // Label for the dataset
                    data: [
                        greatMoodPercentage, 
                        goodMoodPercentage, 
                        okayMoodPercentage, 
                        badMoodPercentage, 
                        awfulMoodPercentage
                    ],
                    backgroundColor: [
                        MOOD_COLORS.GREAT,
                        MOOD_COLORS.GOOD,
                        MOOD_COLORS.OKAY,
                        MOOD_COLORS.BAD,
                        MOOD_COLORS.AWFUL,
                    ],
                    borderWidth: 1 // Width of the border around the doughnut segments
                }]
            },
        });
    </script>
</html>
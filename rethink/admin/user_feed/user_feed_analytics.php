<?php
/**
 * File: user_feed_analytics.php
 * Author: Moa Burke
 * Date: 2024-11-06
 * Description: Manages and displays user posts with mood ratings, interactions, and analytics, including post visibility, re-evaluation requests, and top contributors.
 * 
 * This script is responsible for displaying a list of posts from users with a specific focus 
 * on posts that have been hidden or are marked for re-evaluation requests. It includes 
 * functionality to hide/show posts based on user requests, manage mood data visualizations, 
 * and display the number of likes and comments for each post. Additionally, the script provides 
 * statistics on total posts, interactions, likes, and comments. It also identifies the top 
 * poster, top commenter, and top liker.
 *
 * Key Features:
 * - Displays posts with mood ratings and user interactions (likes, comments).
 * - Handles post visibility (show/hide) based on whether the post is marked as hidden.
 * - Allows for re-evaluation requests for posts.
 * - Provides a visual representation of mood data using a doughnut chart.
 * - Displays total posts, interactions (likes, comments), and other analytics.
 * - Identifies and displays the top poster (user with the most posts), top commenter 
 *   (user with the most comments), and top liker (user with the most likes).
*/
session_start(); // Start the session

// Define a constant for the base path
define('BASE_PATH', '../../server-side/shared/');

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

// Query to count the total number of posts
$totalPostsQuery = mysqli_query($con, "SELECT * FROM posts");
$totalPostCount = mysqli_num_rows($totalPostsQuery);

// Query to count the total number of post likes
$totalPostLikesQuery = mysqli_query($con, "SELECT * FROM postlikes");
$totalPostLikesCount = mysqli_num_rows($totalPostLikesQuery);

// Query to count the total number of comments on posts
$totalCommentsQuery = mysqli_query($con, "SELECT * FROM comments");
$totalCommentsCounts = mysqli_num_rows($totalCommentsQuery);

// Query to find the user with the most posts (top poster)
$topPosterQuery = mysqli_query($con, 
    "SELECT u.Username, p.UserID, COUNT(PostID) AS postCount 
    FROM posts p 
    LEFT JOIN users u ON p.UserID = u.UserID 
    GROUP BY UserID 
    ORDER BY postCount DESC
    LIMIT 1;" // limit to top result for clarity
);

// Retrieve and store details of the top poster
if ($topPosterQuery && mysqli_num_rows($topPosterQuery) > 0) {
    $topPosterData = mysqli_fetch_assoc($topPosterQuery); // Fetch the top poster's data
    $topPosterUsername = $topPosterData['Username']; // Store the username of the top poster
    $topPosterPostCount = $topPosterData['postCount']; // Store the post count of the top poster
    $topPosterUserID = $topPosterData['UserID']; // Store the user ID of the top poster
}


// Query to find the user with the most post likes (top liker)
$topLikerQuery = mysqli_query($con, 
    "SELECT u.Username, p.UserID, COUNT(PostID) AS likeCount 
    FROM postlikes p 
    LEFT JOIN users u ON p.UserID = u.UserID 
    GROUP BY UserID 
    ORDER BY likeCount DESC
    LIMIT 1;" // limit to top result for clarity
);

// Retrieve and store details of the top poster
if ($topLikerQuery && mysqli_num_rows($topLikerQuery) > 0) {
    $topLikerData = mysqli_fetch_assoc($topLikerQuery); // Fetch the top poster's data
    $topLikerUsername = $topLikerData['Username']; // Store the username of the top poster
    $topLikerLikeCount = $topLikerData['likeCount'];  // Store the post count of the top poster
    $topLikerUserID = $topLikerData['UserID'];  // Store the user ID of the top poster
}

// Query to find the user with the most comments (top commenter)
$getTopCommenter = mysqli_query($con, 
    "SELECT u.Username, p.UserID, COUNT(PostID) AS commentCount 
    FROM comments p 
    LEFT JOIN users u ON p.UserID = u.UserID 
    GROUP BY UserID 
    ORDER BY commentCount DESC
    LIMIT 1;" // Limit the result to the top user by comment count
);

// Retrieve and store details of the top commenter
if ($getTopCommenter && mysqli_num_rows($getTopCommenter) > 0) {
    $topCommenter = mysqli_fetch_assoc($getTopCommenter); // Fetch the top commenter’s data
    $topCommenterUsername = $topCommenter['Username']; // Store the username of the top commenter
    $topCommenterCnt = $topCommenter['commentCount']; // Store the comment count of the top commenter
    $topCommenterUserID = $topCommenter['UserID']; // Store the user ID of the top commenter
}

// Count the number of posts for each mood category using constants for MoodID values
$greatMoodsQuery = mysqli_query($con, "SELECT * FROM posts WHERE MoodID = '" . MOOD_GREAT . "';");
$greatMoodsCount = mysqli_num_rows($greatMoodsQuery);  // Store the count of posts with "Great" mood

$goodMoodsQuery = mysqli_query($con, "SELECT * FROM posts WHERE MoodID = '" . MOOD_GOOD . "';");
$goodMoodsCount = mysqli_num_rows($goodMoodsQuery); // Store the count of posts with "Good" mood

$okayMoodsQuery = mysqli_query($con, "SELECT * FROM posts WHERE MoodID = '" . MOOD_OKAY . "';");
$okayMoodsCount = mysqli_num_rows($okayMoodsQuery); // Store the count of posts with "Okay" mood

$badMoodsQuery = mysqli_query($con, "SELECT * FROM posts WHERE MoodID = '" . MOOD_BAD . "';");
$badMoodsCount = mysqli_num_rows($badMoodsQuery); // Store the count of posts with "Bad" mood

$awfulMoodsQuery = mysqli_query($con, "SELECT * FROM posts WHERE MoodID = '" . MOOD_AWFUL . "';");
$awfulMoodsCount = mysqli_num_rows($awfulMoodsQuery); // Store the count of posts with "Awful" mood

// Calculate and format percentages for each mood category if there are posts available
if(!empty($totalPostCount)){
    $greatMoodsPercentage = ($greatMoodsCount / $totalPostCount) * 100; // Calculate "Great" mood percentage
    $greatPercentage = number_format($greatMoodsPercentage, 0); // Format to integer percentage
    
    $goodMoodsPercentage = ($goodMoodsCount / $totalPostCount) * 100; // Calculate "Good" mood percentage
    $goodPercentage = number_format($goodMoodsPercentage, 0); // Format to integer percentage
    
    $okayMoodsPercentage = ($okayMoodsCount / $totalPostCount) * 100; // Calculate "Okay" mood percentage
    $okayPercentage = number_format($okayMoodsPercentage, 0); // Format to integer percentage
    
    $badMoodsPercentage = ($badMoodsCount / $totalPostCount) * 100; // Calculate "Bad" mood percentage
    $badPercentage = number_format($badMoodsPercentage, 0); // Format to integer percentage
    
    $awfulMoodsPercentage = ($awfulMoodsCount / $totalPostCount) * 100; // Calculate "Awful" mood percentage
    $awfulPercentage = number_format($awfulMoodsPercentage, 0); // Format to integer percentage
}

// Query to fetch all posts along with user details, comments, likes, and mood name
$queryAllPosts = mysqli_query($con,
    "SELECT 
        u.UserID AS UserID, 
        u.Username, 
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
        FROM postlikes GROUP BY PostID 
    ) 
    l ON p.PostID = l.PostID 
    GROUP BY p.PostID  
    ORDER BY Date DESC;"
);
// Count the total number of posts retrieved
$totalPostCount = mysqli_num_rows($queryAllPosts); 

// Query to fetch only visible posts (posts not hidden)
$queryVisiblePosts = mysqli_query($con,
    "SELECT 
        u.UserID AS UserID, 
        u.Username, 
        p.PostID AS PostID, 
        p.Hidden, 
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
    WHERE p.Hidden = '" . POST_VISIBLE . "' 
    GROUP BY p.PostID  
    ORDER BY Date DESC;"
);
// Count the number of visible posts
$visiblePostsCount = mysqli_num_rows($queryVisiblePosts); 

// Query to fetch only hidden posts
$queryHiddenPosts = mysqli_query($con,
    "SELECT 
        u.UserID AS UserID, 
        u.Username, 
        p.PostID AS PostID, 
        p.Hidden, 
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
    WHERE p.Hidden = '" . POST_HIDDEN . "'  
    GROUP BY p.PostID  
    ORDER BY Date DESC;"
);
// Count the number of hidden posts
$hiddenPostsCount = mysqli_num_rows($queryHiddenPosts); 

// Query to fetch posts with specific hidden status (hidden posts with a request)
$queryHiddenPostsRequest = mysqli_query($con,
    "SELECT 
        u.UserID AS UserID, 
        u.Username, 
        p.PostID AS PostID, 
        p.Hidden, 
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
    ) c ON p.PostID = c.PostID
    LEFT OUTER JOIN (
        SELECT PostID, LikeID, COUNT(LikeID) AS likes 
        FROM postlikes 
        GROUP BY PostID 
    ) 
    l ON p.PostID = l.PostID 
    WHERE p.Hidden = '" . POST_HIDDEN . "' 
    GROUP BY p.PostID  
    ORDER BY Date DESC;"
);
// Count the number of hidden posts with a request
$hiddenPostsRequestCount = mysqli_num_rows($queryHiddenPostsRequest); 

// Query to fetch all request check entries
$queryAllRequests = mysqli_query($con,"SELECT * FROM requestcheck");
$totalRequestCount = mysqli_num_rows($queryAllRequests);  // Count the total number of requests

// Query to get all available moods
$queryGetMoods = "SELECT * FROM moods";
$resultGetMoods = mysqli_query($con, $queryGetMoods); 
            
// Handle the 'show' action to display a post
if (isset($_POST['show'])) {
    $postID = $_POST['show']; // Get the post ID to show

    // Check if the post is already requested
    $queryCheckRequested = mysqli_query($con, "SELECT * FROM requestcheck WHERE PostID = '$postID'");
    $requestCount = mysqli_num_rows($queryCheckRequested); // Count the number of requests for this post

    // If the post is requested, delete the request entry
    if ($requestCount > 0) {
        $queryDeleteRequest = "DELETE FROM requestcheck WHERE PostID = $postID";
        mysqli_query($con, $queryDeleteRequest);
    }

    // Update the post to make it visible (not hidden)
    $queryShowPost = "UPDATE posts SET Hidden = '" . POST_VISIBLE . "' WHERE PostID = $postID";
    $isQueryExecuted = mysqli_query($con, $queryShowPost);

    // Check if the query executed successfully
    if ($isQueryExecuted) {
        // Set a success message in the session
        $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>投稿は表示されています。</p></div>";

    } else {
        // Set an error message in the session 
        $_SESSION['feedbackMessage'] = "<div class='message-text fail-alert'><p>何か問題が発生しました。</p></div>";

    }

    // Redirect to the user feed analytics page after the action
    header('Location: user_feed_analytics.php');
    exit(0);
}

// Handle the 'hide' action to hide a post
if (isset($_POST['hide'])) {
    $postID = $_POST['hide']; // Get the post ID to hide

    // Update the post to make it hidden
    $queryHidePost = "UPDATE posts SET Hidden = '" . POST_HIDDEN . "'  WHERE PostID = $postID";
    $isQueryExecuted = mysqli_query($con, $queryHidePost);

    // Check if the query executed successfully
    if ($isQueryExecuted) {
        // Set a success message in the session
        $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>投稿は非表示になりました。</p></div>";

    } else {
        // Set an error message in the session
        $_SESSION['feedbackMessage'] = "<div class='message-text fail-alert'><p>何か問題が発生しました。</p></div>";

    }

    // Redirect to the user feed analytics page after the action
    header('Location: user_feed_analytics.php');
    exit(0);
}

// Handle the 'notApproved' action to remove a post request
if (isset($_POST['disapprove-reevaluation'])) {
    $postID = $_POST['disapprove-reevaluation']; // Get the post ID for the request to be deleted

    // Delete the request entry from the requestcheck table
    $queryDeleteRequestEntry = "DELETE FROM requestcheck WHERE PostID = $postID";
    $isQueryExecuted = mysqli_query($con, $queryDeleteRequestEntry);

    // Check if the query executed successfully
    if ($isQueryExecuted) {
        // Set a success message in the session
        $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>投稿は承認されませんでした。</p></div>";

    } else {
        // Set an error message in the session
        $_SESSION['feedbackMessage'] = "<div class='message-text fail-alert'><p>何か問題が発生しました。</p></div>";

    }

    // Redirect to the user feed analytics page after the action
    header('Location: user_feed_analytics.php');
    exit(0);
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Include the necessary assets for the head section via the includeHeadAssets function -->
        <?php includeHeadAssets(); ?>
    </head>

    <body>
        <header class="sidebar-navigation user-feed-navigation">
            <!-- Render the sidebar navigation specifically for the admin section -->
            <?php renderAdminNavigation(); ?>

            <!-- Load the Chart.js library for creating charts -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

            <!-- Load the JavaScript file for tab interactions -->
            <script src="../../assets/javascript/tab_interactions.js" defer></script>
        </header>

        <!-- Render the header for the admin dashboard with logout functionality, using the admin's data -->
        <?php renderAdminHeaderWithLogout($adminData); ?>

        <div class="admin-main-wrapper">
            <!-- Heading for the Feed Analytics section -->
            <h2>Feed Analytics</h2>

            <div class="analytics-detail-wrapper">
                <!-- Left section of the feed analytics -->
                <section class="analytics-left-section">

                    <!-- Container for the top section of the feed analytics -->
                    <div class="analytics-summary-top">
                        <!-- Box displaying the total number of posts -->
                        <div class="summary-posts">
                            <h3>投稿合計</h3> <!-- Heading for total posts -->
                            <p><?php echo $totalPostCount;?>件</p> <!-- Displays the total post count -->
                        </div><!-- .summary-posts -->

                        <!-- Box displaying interaction counts -->
                        <div class="summary-interactions">
                            <!-- Top section inside the right box -->
                            <div class="summary-interactions-header">
                                <h3>インタラクション数</h3> <!-- Heading for interaction counts -->
                            </div><!-- .summary-interactions-header -->

                            <!-- Container for content inside the right box -->
                            <div class="summary-interactions-content">
                                <!-- Likes section -->
                                <div class="likes-section">
                                    <!-- Section for displaying the number of likes -->
                                    <div class="interaction-content">
                                        <i class="fa-regular fa-heart"></i> <!-- Heart icon for likes -->
                                        <p><?php echo $totalPostLikesCount; ?></p> <!-- Displays the total likes count -->
                                    </div>
                                    
                                    <!-- Explanation text for likes -->
                                    <div class="interaction-label">
                                        <p>いいね合計</p> <!-- Total likes explanation -->
                                    </div>
                                </div><!-- .likes-section -->

                                <!-- Comments section -->
                                <div class="comments-section">
                                    <!-- Section for displaying the number of comments -->
                                    <div class="interaction-content"> 
                                        <i class="fa-regular fa-comment"></i> <!-- Comment icon for comments -->
                                        <p><?php echo $totalCommentsCounts; ?></p>  <!-- Displays the total comments count -->
                                    </div>

                                    <!-- Explanation text for comments -->
                                    <div class="interaction-label">
                                        <p>コメント合計</p> <!-- Total comments explanation -->
                                    </div>
                                </div><!-- .comments-section -->

                            </div><!-- .summary-interactions-content -->
                        </div><!-- .summary-interactions -->
                    </div><!-- .analytics-summary-top -->

                    <!-- Wrapper for the bottom section of the feed -->
                    <div class="analytics-summary-bottom">
                        <!-- Container for the feed chart section -->
                        <div class="analytics-chart-section">

                            <!-- Chart area for displaying mood statistics -->
                            <div class="mood-chart-container">
                                <canvas id="myChart"></canvas> <!-- Canvas element for displaying the chart -->
                            </div><!-- .mood-chart-container -->

                            <!-- Container for mood data count -->
                            <div class="mood-count-section">
                                <div class="mood-count-wrapper">
                                    <?php 
                                    // Loop through each mood from the database
                                    while ($moodDataRow = mysqli_fetch_assoc($resultGetMoods)) {
                                        $currentMoodID = $moodDataRow['MoodID']; // Retrieve the MoodID for the current row of mood data

                                        // Query to get the count of posts for the current mood ID
                                        $queryGetMoodPostCount = "SELECT MoodID, COUNT(MoodID) AS cntMood FROM posts WHERE MoodID = '$currentMoodID' GROUP BY MoodID ";
                                        $moodPostCountResult = mysqli_query($con,$queryGetMoodPostCount);  // Query the count of posts for each mood
                                        $moodPostCount = mysqli_fetch_assoc($moodPostCountResult);  // Fetch the count of posts for the current mood
                                        ?> 

                                        <!-- Wrapper for each individual mood data -->
                                        <div class="mood-count-item">

                                            <!-- Mood bar that dynamically changes color based on the mood ID -->
                                            <div class="mood-bar 
                                                <?php 
                                                // Dynamically assign a class based on the mood ID to style the mood bars differently
                                                if ($currentMoodID == MOOD_GREAT) { 
                                                    echo ' great-mood'; // Style for 'great' mood
                                                } elseif ($currentMoodID == MOOD_GOOD) { 
                                                    echo ' good-mood'; // Style for 'good' mood
                                                } elseif ($currentMoodID == MOOD_OKAY) {
                                                    echo ' okay-mood'; // Style for 'okay' mood
                                                } elseif ($currentMoodID == MOOD_BAD) {
                                                    echo ' bad-mood'; // Style for 'bad' mood
                                                } elseif ($currentMoodID == MOOD_AWFUL) {
                                                    echo ' awful-mood'; // Style for 'awful' mood
                                                } 
                                                ?>"> 
                                            </div><!-- .moodBar -->

                                            <!-- Display the Japanese name of the mood -->
                                            <p class="mood-name"> 
                                                <?php echo htmlspecialchars($moodDataRow['JapaneseMoodName']); ?> 
                                            </p>

                                            <!-- Display the total count of posts associated with the current mood -->
                                            <p class="mood-total-count"> 
                                                <?php 
                                                // Display the count of posts for the current mood, or 0 if no posts exist
                                                echo !empty($moodPostCount['cntMood']) ? $moodPostCount['cntMood'] : "0";
                                                ?>
                                                件  <!-- Shows the total number of posts for the current mood -->
                                            </p>
                                        </div> <!-- .mood-count-item -->
                                        
                                    <?php } ?>
                                </div><!-- .mood-count-wrapper -->
                            </div><!-- .mood-count-section -->

                        </div><!-- .analytics-chart-section -->

                        <!-- Section for displaying top contributors (Top Poster, Top Commenter, and Top Liker) -->
                        <div class="top-contributors-section">
                            
                            <!-- Top Poster Section -->
                            <div class="top-poster-container">
                                <h4>Top Poster</h4>
                                <i class="fa-solid fa-pen"></i> <!-- Icon for top poster -->

                                <!-- Display the top poster's username with a link to their profile -->
                                <p class="contributor-username">
                                    <?php if(!empty($topPosterUserID)) { ?>
                                        <a href="../user_management/user_details/user_details.php?id=<?php echo $topPosterUserID;?>">
                                            <!-- Display the username if it's available, ensuring it's sanitized to prevent XSS -->
                                            @<?php echo htmlspecialchars($topPosterUsername); ?>
                                        </a>
                                    <?php } else { ?>
                                        <!-- Display a fallback message in Japanese if no posts exist  -->
                                        <span>投稿がありません</span>
                                    <?php } ?>
                                </p>

                                <!-- Display the total number of posts made by the top poster -->
                                <?php if (!empty($topPosterPostCount)) { ?>
                                    <p> <?php echo $topPosterPostCount;?>件</p>
                                <?php } ?>
                            </div><!-- .top-poster-container -->

                            <!-- Top Commenter Section -->
                            <div class="top-commenter-container">
                                <h4>Top Commenter</h4>
                                <i class="fa-solid fa-comment"></i> <!-- Icon for top commenter -->

                                <!-- Display the top commenter's username with a link to their profile -->
                                <p class="contributor-username">
                                    <!-- Check if a top commenter exists (by checking if the user ID is not empty) -->
                                    <?php if(!empty($topCommenterUserID)) { ?>
                                        <!-- If a top commenter exists, display their username with a link to their profile -->
                                        <a href="../user_management/user_details/user_details.php?id=<?php echo $topCommenterUserID;?>">
                                            <!-- Display the username if it's available, ensuring it's sanitized to prevent XSS -->
                                            @<?php echo htmlspecialchars($topCommenterUsername); ?>
                                        </a>
                                    <?php } else { ?>
                                        <!-- Fallback message if no top commenter exists  -->
                                        <span>コメントがありません</span>
                                    <?php } ?>            
                                </p>

                                <!-- Display the total number of comments made by the top commenter -->
                                <?php if (!empty($topCommenterCnt)) { ?>
                                    <p> <?php echo $topCommenterCnt;?>件</p>
                                <?php } ?>
                            </div><!-- .top-commenter-container -->

                            <!-- Top Liker Section -->
                            <div class="top-liker-container">
                                <h4>Top Liker</h4>
                                <i class="fa-solid fa-heart"></i> <!-- Icon for top liker -->

                                <!-- Display the top liker's username with a link to their profile -->
                                <p class="contributor-username">
                                    <!-- Check if a top liker exists (by checking if the user ID is not empty) -->
                                    <?php if(!empty($topLikerUserID)) { ?>
                                        <!-- If a top liker exists, display their username with a link to their profile -->
                                        <a href="../user_management/user_details/user_details.php?id=<?php echo $topLikerUserID;?>">
                                            <!-- Display the username if it's available, ensuring it's sanitized to prevent XSS -->
                                            @<?php if(!empty($topLikerUsername)){echo htmlspecialchars($topLikerUsername);} ?>
                                        </a>
                                    <?php } else { ?>
                                        <!-- Fallback message if no top liker exists  -->
                                        <span>「いいね」がありません</span>
                                    <?php } ?>  
                                </p>

                                <!-- Display the total number of likes given by the top liker -->
                                <?php if (!empty($topLikerLikeCount)) { ?>
                                    <p> <?php echo $topLikerLikeCount;?>件</p>
                                <?php } ?>
                            </div><!-- .top-liker-container -->

                        </div><!-- .top-contributors-section -->
                    </div><!-- .analytics-summary-bottom -->

                </section><!-- .analytics-left-section -->

                <!-- Wrapper for the right side feed, including the navigation -->    
                <section class="analytics-right-section">

                    <!-- Navigation menu for switching between different post views -->
                    <div class="posts-display-navigation">
                        <ul>
                            <!-- Tab for viewing all posts -->
                            <li data-tab-target="#allPosts" class="active tabs">
                                <div class="navigation-content" id="active-navigation">
                                    <p>すべての投稿</p> <!-- Display text for all posts -->
                                </div> 
                            </li>

                            <!-- Tab for viewing only the posts that are currently visible -->
                            <li data-tab-target="#showingPosts" class = "tabs">
                                <div class="navigation-content">
                                    <p>表示の投稿</p> <!-- Display text for showing posts -->
                                </div> 
                            </li>

                            <!-- Tab for viewing posts that are hidden -->
                            <li data-tab-target="#hiddenPosts" class="tabs">
                                <div class="navigation-content">
                                    <p>非表示の投稿</p> <!-- Display text for hidden posts -->
                                </div> 
                            </li>

                            <!-- Tab for viewing posts that are under review for re-evaluation -->
                            <li data-tab-target="#request" class="tabs">
                                <div class="navigation-content">
                                    <p>再評価の依頼</p> <!-- Display text for request for re-evaluation -->
                                </div> 
                            </li>
                        </ul>

                        <!-- If there are requests for re-evaluation, display a flag icon -->
                        <?php 
                        if ($totalRequestCount > 0 ){ ?>
                            <div class="requested">
                                <i class="fa-solid fa-flag"></i> <!-- Flag icon for re-evaluation requests -->
                            </div><!-- .under-request -->
                        <?php } ?>

                    </div><!-- ./posts-display-navigation -->

                    <!-- Section for displaying all posts -->
                    <section id="allPosts" data-tab-content class="active">

                        <div class="user-posts-wrapper">
                            <div class="user-posts-list">

                                <!-- Title for displaying all posts -->
                                <div class="posts-header">
                                    <h3>すべての投稿</h3> 
                                </div><!-- .posts-header -->

                                <!-- Include file for alert messages -->
                                <div>
                                    <?php include('../../server-side/shared/feedback_messages.php'); ?>
                                </div>

                                <?php 
                                // Check if there are any posts to display
                                if ($totalPostCount > 0) {
                                    // Loop through each post
                                    while ($post = mysqli_fetch_assoc($queryAllPosts)) { 
                                        // Retrieve post details from the database
                                        $postUserID = $post['UserID']; // User ID of the post author
                                        $postUsername = $post['Username'];  // Username of the post author
                                        $postID = $post['PostID']; // Unique identifier for the post
                                        $postMoodName = $post['MoodName']; // The mood associated with the post
                                        $postText = $post['PostedText']; // The text content of the post
                                        $postDate = $post['Date'];  // Date the post was made
                                        $postTime = $post['Time'];  // Time the post was made
                                        $postLikes = $post['likes']; // Number of likes for the post
                                        $postComments = $post['comments']; // Number of comments for the post
                                        
                                        // Check if the post is marked as hidden
                                        $checkHiddenQuery = "SELECT * FROM posts where PostID = '$postID' AND Hidden = '" . POST_HIDDEN . "' ";
                                        $hiddenResult = mysqli_query($con, $checkHiddenQuery);
                                        $isPostHidden = mysqli_num_rows($hiddenResult); // Check if the post is hidden based on the query
                                        ?>

                                        <div class="user-post 
                                            <?php 
                                            // Check if the post is hidden and apply the 'hidden-post' class if true
                                            if ($isPostHidden > 0) {
                                                echo "hidden-post"; // Mark the post as hidden
                                            }
                                            ?>">

                                            <div class="post-time">
                                                <!-- Display post details such as the username, date, and time of the post -->
                                                <a href="../user_management/user_details/user_details.php?id=<?php echo $postUserID;?>">
                                                    <!-- Display the username with @ symbol -->
                                                    <p class="username">@<?php echo htmlspecialchars($postUsername); ?></p>
                                                </a>
                                                
                                                <!-- Display the date the post was made -->
                                                <p class="date"><?php echo $postDate; ?></p>

                                                <!-- Display the time the post was made -->
                                                <p class="time"><?php echo $postTime; ?></p>
                                            </div><!-- .post-time -->

                                            <div class="main-post-content">
                                                <div class="post-mood-wrapper">

                                                    <!-- Display the mood associated with the post and apply a class based on the mood -->
                                                    <p class="post-mood-label 
                                                        <?php 
                                                        // Check the mood of the post and assign a corresponding class for styling
                                                        if ($postMoodName == "最高") { 
                                                            echo "great-mood"; 
                                                        } elseif ($postMoodName == "良い") { 
                                                            echo "good-mood";
                                                        } elseif ($postMoodName == "普通") { 
                                                            echo "okay-mood"; 
                                                        } elseif ($postMoodName == "悪い") { 
                                                            echo "bad-mood"; 
                                                        } elseif ($postMoodName == "最悪") { 
                                                            echo "awful-mood"; 
                                                        }
                                                        ?>">
                                                        
                                                        <!-- Display the mood name -->
                                                        <?php echo $postMoodName ; ?>
                                                    </p>
                                                </div><!-- .post-mood-wrapper -->

                                                <div class="post-content">
                                                    <!-- Display the content of the post (text), ensuring special characters are properly escaped for security -->
                                                    <p><?php echo htmlspecialchars($postText); ?></p> <!-- Prevent XSS attacks by escaping special characters in the post text -->
                                                </div><!-- .post-content -->
                                            </div><!-- .main -->
                                            
                                            <?php
                                            // Query to check if the post has any likes by checking the 'postlikes' table for the current post ID
                                            $queryCheckLikes = "SELECT * FROM postlikes WHERE PostID = '$postID'"; // Query to fetch all likes for the post
                                            $resultCheckLikes = mysqli_query($con, $queryCheckLikes); // Execute the query to get the result set
                                            ?>

                                            <div class="post-interactions">
                                                <div class="post-actions">

                                                    <!-- Likes section: Displays the number of likes, or "0" if there are no likes -->
                                                    <div class="post-likes">
                                                        <i class="fa-regular fa-heart"></i> <!-- Icon representing the like action -->
                                                        <p> <?php echo $postLikes > 0 ? $postLikes : "0"; ?> </p> <!-- Display number of likes or "0" -->
                                                    </div><!-- .post-likes -->

                                                    <!-- Comments section: Displays the number of comments, or "0" if there are no comments -->
                                                    <div class="post-comments">
                                                        <i class="fa-regular fa-comment"></i> <!-- Icon representing the comment action -->
                                                        <p><?php echo $postComments > 0 ? $postComments : "0"; ?></p> <!-- Display number of comments or "0" -->
                                                    </div><!-- .post-comments -->

                                                </div><!-- .post-actions -->

                                                <div class="post-visibility-toggle">
                                                    <?php 
                                                    // Check if the post is hidden (when $isPostHidden > 0)
                                                    // If the post is hidden, display the option to show the post
                                                    if ($isPostHidden > 0) { ?>
                                                        <div class="post-hidden-controls">
                                                            <!-- Icon indicating the post is hidden (eye with a slash) -->
                                                            <i class="fa-solid fa-eye-slash"></i>
                                                            
                                                            <!-- Form to submit a request to show the hidden post -->
                                                            <form action="" method="POST">
                                                                <!-- Button to trigger the 'show' action -->
                                                                <button type="submit" name="show" value="<?=$postID;?>">Show</button>
                                                            </form>
                                                        </div><!-- .post-hidden-controls -->

                                                    <?php }else{ ?>

                                                        <!-- If the post is not hidden, display the option to hide the post -->
                                                        <div class="post-visible-controls">
                                                            <!-- Icon indicating the post is visible (eye icon) -->
                                                            <i class="fa-solid fa-eye"></i>

                                                            <!-- Form to submit a request to hide the visible post -->
                                                            <form action="" method="POST">
                                                                <!-- Button to trigger the 'hide' action -->
                                                                <button type="submit" name="hide" value="<?=$postID;?>">Hide</button>
                                                            </form>
                                                        </div><!-- .post-visible-controls -->
                                                    <?php } ?>
                                                </div><!-- .post-visibility-toggle -->

                                            </div><!-- .post-interactions -->
                                        </div><!-- .post -->
                                    <?php } // End of PHP loop that fetches and displays posts ?>

                                <?php } else { // If no posts exist ?>
                                    <!-- Message to display when there are no posts -->
                                    <div class="no-posts">
                                        <p>投稿がありません</p> <!-- "No posts available" -->
                                    </div><!--.no-posts -->
                                <?php } // End of if statement for checking post count  ?>

                            </div><!-- .user-posts-list -->
                        </div><!-- .user-posts-wrapper -->
                    </section> <!-- #allPosts -->

                    <!-- Section for displaying all visable -->
                    <section id="showingPosts" data-tab-content>
                        <div class="user-posts-wrapper">
                            <div class="user-posts-list">

                                <!-- Header for the visible posts section -->
                                <div class="posts-header">
                                    <h3>表示の投稿</h3>
                                </div><!-- .posts-header -->

                                <?php 
                                // Check if there are any posts to display
                                if ($visiblePostsCount > 0) {
                                    // Loop through each visible post
                                    while ($post = mysqli_fetch_assoc($queryVisiblePosts)) { 
                                        // Retrieve post details from the database
                                        $postUserID = $post['UserID']; // User ID of the post author
                                        $postUsername = $post['Username'];  // Username of the post author
                                        $postID = $post['PostID']; // Unique identifier for the post
                                        $postMoodName = $post['MoodName']; // The mood associated with the post
                                        $postText = $post['PostedText']; // The text content of the post
                                        $postDate = $post['Date'];  // Date the post was made
                                        $postTime = $post['Time'];  // Time the post was made
                                        $postLikes = $post['likes']; // Number of likes for the post
                                        $postComments = $post['comments']; // Number of comments for the post
                                        
                                        // Check if the post is marked as hidden
                                        $checkHiddenQuery = "SELECT * FROM posts where PostID = '$postID' AND Hidden = '" . POST_HIDDEN . "' ";
                                        $hiddenResult = mysqli_query($con, $checkHiddenQuery);
                                        $isPostHidden = mysqli_num_rows($hiddenResult); // Check if the post is hidden based on the query
                                        ?>

                                        <div class="user-post">
                                            <div class="post-time">
                                                <!-- Display post details such as the username, date, and time of the post -->
                                                <a href="../user_management/user_details/user_details.php?id=<?php echo $postUserID;?>">
                                                    <!-- Display the username with @ symbol -->
                                                    <p class="username">@<?php echo htmlspecialchars($postUsername); ?></p>
                                                </a>
                                                
                                                <!-- Display the date the post was made -->
                                                <p class="date"><?php echo $postDate; ?></p>

                                                <!-- Display the time the post was made -->
                                                <p class="time"><?php echo $postTime; ?></p>
                                            </div><!-- .post-time -->

                                            <div class="main-post-content">
                                                <div class="post-mood-wrapper">

                                                    <!-- Display the mood associated with the post and apply a class based on the mood -->
                                                    <p class="post-mood-label 
                                                        <?php 
                                                        // Check the mood of the post and assign a corresponding class for styling
                                                        if ($postMoodName == "最高") { 
                                                            echo "great-mood"; 
                                                        } elseif ($postMoodName == "良い") { 
                                                            echo "good-mood"; 
                                                        } elseif ($postMoodName == "普通") { 
                                                            echo "okay-mood";
                                                        } elseif ($postMoodName == "悪い") { 
                                                            echo "bad-mood";
                                                        } elseif ($postMoodName == "最悪") { 
                                                            echo "awful-mood";
                                                        }
                                                        ?>">
                                                        
                                                        <!-- Display the mood name -->
                                                        <?php echo $postMoodName ; ?>
                                                    </p>
                                                </div><!-- .post-mood-wrapper -->

                                                <div class="post-content">
                                                    <!-- Display the content of the post (text), ensuring special characters are properly escaped for security -->
                                                    <p><?php echo htmlspecialchars($postText); ?></p> <!-- Prevent XSS attacks by escaping special characters in the post text -->
                                                </div><!-- .post-content -->
                                            </div><!-- .main -->
                                        
                                            <?php
                                            // Query to check if the post has any likes by checking the 'postlikes' table for the current post ID
                                            $queryCheckLikes = "SELECT * FROM postlikes where PostID = '$postID'"; // Query to fetch all likes for the post
                                            $resultCheckLikes = mysqli_query($con, $queryCheckLikes); // Execute the query to get the result set
                                            ?>

                                            <div class="post-interactions">

                                                <div class="post-actions">
                                                    <!-- Likes section: Displays the number of likes, or "0" if there are no likes -->
                                                    <div class="post-likes">
                                                        <i class="fa-regular fa-heart"></i> <!-- Icon representing the like action -->
                                                        <p> <?php echo $postLikes > 0 ? $postLikes : "0"; ?> </p> <!-- Display number of likes or "0" -->
                                                    </div><!-- .post-likes -->

                                                    <!-- Comments section: Displays the number of comments, or "0" if there are no comments -->
                                                    <div class="post-comments">
                                                        <i class="fa-regular fa-comment"></i> <!-- Icon representing the comment action -->
                                                        <p><?php echo $postComments > 0 ? $postComments : "0"; ?></p> <!-- Display number of comments or "0" -->
                                                    </div><!-- .post-comments -->
                                                </div><!-- .post-actions -->

                                                <div class="post-visibility-toggle">
                                                    <?php 
                                                    // Check if the post is hidden (when $isPostHidden > 0)
                                                    // If the post is hidden, display the option to show the post
                                                    if ($isPostHidden > 0) { ?>
                                                        <div class="post-hidden-controls">
                                                            <!-- Icon indicating the post is hidden (eye with a slash) -->
                                                            <i class="fa-solid fa-eye-slash"></i>
                                                            
                                                            <!-- Form to submit a request to show the hidden post -->
                                                            <form action="" method="POST">
                                                                <!-- Button to trigger the 'show' action -->
                                                                <button type="submit" name="show" value="<?=$postID;?>">Show</button>
                                                            </form>
                                                        </div><!-- .post-hidden-controls -->

                                                    <?php }else{ ?>

                                                        <!-- If the post is not hidden, display the option to hide the post -->
                                                        <div class="post-visible-controls">
                                                            <!-- Icon indicating the post is visible (eye icon) -->
                                                            <i class="fa-solid fa-eye"></i>

                                                            <!-- Form to submit a request to hide the visible post -->
                                                            <form action="" method="POST">
                                                                <!-- Button to trigger the 'hide' action -->
                                                                <button type="submit" name="hide" value="<?=$postID;?>">Hide</button>
                                                            </form>
                                                        </div><!-- .post-visible-controls -->
                                                    <?php } ?>
                                                </div><!-- .post-visibility-toggle -->

                                            </div><!-- .post-interactions -->
                                        </div><!-- .post -->
                                    <?php } // End of PHP loop that fetches and displays posts ?> 

                                <?php } else { // If no posts exist ?>
                                    <!-- Message to display when there are no posts -->
                                    <div class="no-posts">
                                        <p>投稿がありません</p> <!-- "No posts available" -->
                                    </div><!-- .no-posts -->
                                <?php } // End of if statement for checking post count ?>

                            </div><!-- .user-posts-list --> 
                        </div><!-- . user-posts-wrapper -->
                    </section><!-- #showingPosts -->

                    <!-- Section for displaying all hidden posts -->
                    <section id="hiddenPosts" data-tab-content>

                        <div class="user-posts-wrapper">
                            <div class="user-posts-list">

                                <!-- Header for the hidden posts section -->
                                <div class="posts-header">
                                    <h3>非表示の投稿</h3>
                                </div><!-- .posts-header -->

                                <?php 
                                // Check if there are any posts to display
                                if ($hiddenPostsCount > 0) {
                                    // Loop through each hidden post
                                    while ($post = mysqli_fetch_assoc($queryHiddenPosts)) { 
                                        // Retrieve post details from the database
                                        $postUserID = $post['UserID']; // User ID of the post author
                                        $postUsername = $post['Username'];  // Username of the post author
                                        $postID = $post['PostID']; // Unique identifier for the post
                                        $postMoodName = $post['MoodName']; // The mood associated with the post
                                        $postText = $post['PostedText']; // The text content of the post
                                        $postDate = $post['Date'];  // Date the post was made
                                        $postTime = $post['Time'];  // Time the post was made
                                        $postLikes = $post['likes']; // Number of likes for the post
                                        $postComments = $post['comments']; // Number of comments for the post
                                        
                                        // Check if the post is marked as hidden
                                        $checkHiddenQuery = "SELECT * FROM posts where PostID = '$postID' AND Hidden = '" . POST_HIDDEN . "' ";
                                        $hiddenResult = mysqli_query($con, $checkHiddenQuery);
                                        $isPostHidden = mysqli_num_rows($hiddenResult); // Check if the post is hidden based on the query
                                        ?>

                                        <div class="user-post 
                                            <?php 
                                            // Check if the post is hidden and apply the 'hidden-post' class if true
                                            if ($isPostHidden > 0) {
                                                echo "hidden-post"; // Mark the post as hidden
                                            }
                                            ?>">

                                            <div class="post-time">
                                                <!-- Display post details such as the username, date, and time of the post -->
                                                <a href="../user_management/user_details/user_details.php?id=<?php echo $postUserID;?>">
                                                    <!-- Display the username with @ symbol -->
                                                    <p class="username">@<?php echo htmlspecialchars($postUsername); ?></p>
                                                </a>
                                                
                                                <!-- Display the date the post was made -->
                                                <p class="date"><?php echo $postDate; ?></p>

                                                <!-- Display the time the post was made -->
                                                <p class="time"><?php echo $postTime; ?></p>
                                            </div><!-- .post-time -->

                                            <div class="main-post-content">
                                                <div class="post-mood-wrapper">

                                                    <!-- Display the mood associated with the post and apply a class based on the mood -->
                                                    <p class="post-mood-label 
                                                        <?php 
                                                        // Check the mood of the post and assign a corresponding class for styling
                                                        if ($postMoodName == "最高") { 
                                                            echo "great-mood";
                                                        } elseif ($postMoodName == "良い") { 
                                                            echo "good-mood";
                                                        } elseif ($postMoodName == "普通") { 
                                                            echo "okay-mood"; 
                                                        } elseif ($postMoodName == "悪い") { 
                                                            echo "bad-mood";
                                                        } elseif ($postMoodName == "最悪") { 
                                                            echo "awful-mood";
                                                        }
                                                        ?>">
                                                        
                                                        <!-- Display the mood name -->
                                                        <?php echo $postMoodName ; ?>
                                                    </p>
                                                </div><!-- .post-mood-wrapper -->

                                                <div class="post-content">
                                                    <!-- Display the content of the post (text), ensuring special characters are properly escaped for security -->
                                                    <p><?php echo htmlspecialchars($postText); ?></p> <!-- Prevent XSS attacks by escaping special characters in the post text -->
                                                </div><!-- .post-content -->
                                            </div><!-- .main -->
                                
                                            <?php
                                            // Query to check if the post has any likes by checking the 'postlikes' table for the current post ID
                                            $queryCheckLikes = "SELECT * FROM postlikes WHERE PostID = '$postID'"; // Query to fetch all likes for the post
                                            $resultCheckLikes = mysqli_query($con, $queryCheckLikes); // Execute the query to get the result set
                                            ?>

                                            <div class="post-interactions">
                                                
                                                <div class="post-actions">
                                                    <!-- Likes section: Displays the number of likes, or "0" if there are no likes -->
                                                    <div class="post-likes">
                                                        <i class="fa-regular fa-heart"></i> <!-- Icon representing the like action -->
                                                        <p> <?php echo $postLikes > 0 ? $postLikes : "0"; ?> </p> <!-- Display number of likes or "0" -->
                                                    </div><!-- .post-likes -->

                                                    <!-- Comments section: Displays the number of comments, or "0" if there are no comments -->
                                                    <div class="post-comments">
                                                        <i class="fa-regular fa-comment"></i> <!-- Icon representing the comment action -->
                                                        <p><?php echo $postComments > 0 ? $postComments : "0"; ?></p> <!-- Display number of comments or "0" -->
                                                    </div><!-- .post-comments -->
                                                </div><!-- .post-actions -->

                                                <div class="post-visibility-toggle">
                                                    <?php 
                                                    // Check if the post is hidden (when $isPostHidden > 0)
                                                    // If the post is hidden, display the option to show the post
                                                    if ($isPostHidden > 0) { ?>
                                                        <div class="post-hidden-controls">
                                                            <!-- Icon indicating the post is hidden (eye with a slash) -->
                                                            <i class="fa-solid fa-eye-slash"></i>
                                                            
                                                            <!-- Form to submit a request to show the hidden post -->
                                                            <form action="" method="POST">
                                                                <!-- Button to trigger the 'show' action -->
                                                                <button type="submit" name="show" value="<?=$postID;?>">Show</button>
                                                            </form>
                                                        </div><!-- .post-hidden-controls -->

                                                    <?php }else{ ?>

                                                        <!-- If the post is not hidden, display the option to hide the post -->
                                                        <div class="post-visible-controls">
                                                            <!-- Icon indicating the post is visible (eye icon) -->
                                                            <i class="fa-solid fa-eye"></i>

                                                            <!-- Form to submit a request to hide the visible post -->
                                                            <form action="" method="POST">
                                                                <!-- Button to trigger the 'hide' action -->
                                                                <button type="submit" name="hide" value="<?=$postID;?>">Hide</button>
                                                            </form>
                                                        </div><!-- .post-visible-controls -->
                                                    <?php } ?>
                                                </div><!-- .post-visibility-toggle -->

                                            </div><!-- .post-interactions -->
                                        </div><!-- .post -->
                                    <?php } // End of PHP loop that fetches and displays posts ?>  

                                <?php } else { // If no posts exist ?>
                                    <!-- Message to display when there are no posts -->
                                    <div class="no-posts">
                                        <p>非表示した投稿がありません</p> <!-- "No hidden posts available" -->
                                    </div><!-- .no-posts -->
                                <?php } // End of if statement for checking post count ?>

                            </div><!-- .user-posts-list -->
                        </div><!-- .user-posts-wrapper -->
                    </section><!-- #hiddenPosts -->

                    <!-- Section for displaying all hidden posts with requests for re-evaluation -->
                    <section id="request" data-tab-content>

                        <div class="user-posts-wrapper">
                            <div class="user-posts-list">

                                <!-- Header for the re-evaluation requests section -->
                                <div class="posts-header">
                                    <h3>再評価の依頼</h3>
                                </div><!-- .posts-header -->
                                
                                <?php 
                                // Check if there are any requests to display
                                if ($totalRequestCount > 0) {
                                    // Check if there are any hidden posts with requests for re-evaluation
                                    if ($hiddenPostsRequestCount > 0) {
                                        // Loop through each hidden post with a re-evaluation request
                                        while ($post = mysqli_fetch_assoc($queryHiddenPostsRequest)) { 
                                            // Retrieve post details from the database
                                            $postUserID = $post['UserID']; // User ID of the post author
                                            $postUsername = $post['Username'];  // Username of the post author
                                            $postID = $post['PostID']; // Unique identifier for the post
                                            $postMoodName = $post['MoodName']; // The mood associated with the post
                                            $postText = $post['PostedText']; // The text content of the post
                                            $postDate = $post['Date'];  // Date the post was made
                                            $postTime = $post['Time'];  // Time the post was made
                                            $postLikes = $post['likes']; // Number of likes for the post
                                            $postComments = $post['comments']; // Number of comments for the post
                                            
                                            // Check if the post is marked as hidden
                                            $checkHiddenQuery = "SELECT * FROM posts where PostID = '$postID' AND Hidden = '" . POST_HIDDEN . "' ";
                                            $hiddenResult = mysqli_query($con, $checkHiddenQuery);
                                            $isPostHidden = mysqli_num_rows($hiddenResult); // Check if the post is hidden based on the query

                                            // Query to check if there are any re-evaluation requests for the current post
                                            $queryCheckRequest = mysqli_query($con,"SELECT * FROM requestcheck WHERE PostID = '$postID'");
                                            // Count the number of re-evaluation requests for the current post
                                            $checkRequest = mysqli_num_rows($queryCheckRequest);

                                            // If there are any re-evaluation requests for the post, process the result
                                            if ($checkRequest > 0) { ?>

                                                <div class="user-post 
                                                    <?php 
                                                    // Check if the post is hidden and apply the 'hidden-post' class if true
                                                    if ($isPostHidden > 0) {
                                                        echo "hidden-post"; // Mark the post as hidden
                                                    }
                                                    ?>">

                                                    <div class="post-time">
                                                        <!-- Display post details such as the username, date, and time of the post -->
                                                        <a href="../user_management/user_details/user_details.php?id=<?php echo $postUserID;?>">
                                                            <!-- Display the username with @ symbol -->
                                                            <p class="username">@<?php echo htmlspecialchars($postUsername); ?></p>
                                                        </a>
                                                        
                                                        <!-- Display the date the post was made -->
                                                        <p class="date"><?php echo $postDate; ?></p>

                                                        <!-- Display the time the post was made -->
                                                        <p class="time"><?php echo $postTime; ?></p>
                                                    </div><!-- .post-time -->

                                                    <div class="main-post-content">
                                                        <div class="post-mood-wrapper">

                                                            <!-- Display the mood associated with the post and apply a class based on the mood -->
                                                            <p class="post-mood-label 
                                                                <?php 
                                                                // Check the mood of the post and assign a corresponding class for styling
                                                                if ($postMoodName == "最高") { 
                                                                    echo "great-mood"; 
                                                                } elseif ($postMoodName == "良い") { 
                                                                    echo "good-mood";
                                                                } elseif ($postMoodName == "普通") { 
                                                                    echo "okay-mood";
                                                                } elseif ($postMoodName == "悪い") { 
                                                                    echo "bad-mood";
                                                                } elseif ($postMoodName == "最悪") { 
                                                                    echo "awful-mood";
                                                                }
                                                                ?>">
                                                                
                                                                <!-- Display the mood name -->
                                                                <?php echo $postMoodName ; ?>
                                                            </p>
                                                        </div><!-- .post-mood-wrapper -->

                                                        <div class="post-content">
                                                            <!-- Display the content of the post (text), ensuring special characters are properly escaped for security -->
                                                            <p><?php echo htmlspecialchars($postText); ?></p> <!-- Prevent XSS attacks by escaping special characters in the post text -->
                                                        </div><!-- .post-content -->
                                                    </div><!-- .main -->
                                                    
                                                    <?php
                                                    // Query to check if the post has any likes by checking the 'postlikes' table for the current post ID
                                                    $queryCheckLikes = "SELECT * FROM postlikes WHERE PostID = '$postID'"; // Query to fetch all likes for the post
                                                    $resultCheckLikes = mysqli_query($con, $queryCheckLikes); // Execute the query to get the result set
                                                    ?>

                                                    <div class="post-interactions">
                                                        <div class="requested">
                                                            <i class="fa-solid fa-flag"></i>
                                                        </div><!-- .requested -->

                                                        <div class="post-actions">
                                                            <!-- Likes section: Displays the number of likes, or "0" if there are no likes -->
                                                            <div class="post-likes">
                                                                <i class="fa-regular fa-heart"></i> <!-- Icon representing the like action -->
                                                                <p> <?php echo $postLikes > 0 ? $postLikes : "0"; ?> </p> <!-- Display number of likes or "0" -->
                                                            </div><!-- .post-likes -->

                                                            <!-- Comments section: Displays the number of comments, or "0" if there are no comments -->
                                                            <div class="post-comments">
                                                                <i class="fa-regular fa-comment"></i> <!-- Icon representing the comment action -->
                                                                <p><?php echo $postComments > 0 ? $postComments : "0"; ?></p> <!-- Display number of comments or "0" -->
                                                            </div><!-- .post-comments -->
                                                        </div><!-- .post-actions -->

                                                        <div class="post-visibility-toggle">
                                                            <?php 
                                                            // Check if the post is hidden (when $isPostHidden > 0)
                                                            // If the post is hidden, display the option to show the post
                                                            if ($isPostHidden > 0) { ?>
                                                                <div class="post-hidden-controls">
                                                                    <!-- Icon indicating the post is hidden (eye with a slash) -->
                                                                    <i class="fa-solid fa-eye-slash"></i>
                                                                    
                                                                    <!-- Form to submit a request to show the hidden post -->
                                                                    <form action="" method="POST">
                                                                        <!-- Button to trigger the 'show' action -->
                                                                        <button type="submit" name="show" value="<?=$postID;?>">Approve</button>
                                                                    </form>

                                                                    <form action="" method="POST" class="dissaprove-button">
                                                                        <!-- Button to trigger the 'disapprove' action -->
                                                                        <button type="submit" name="disapprove-reevaluation" value="<?=$postID;?>">Disapprove</button>
                                                                    </form>
                                                                </div><!-- .post-hidden-controls -->

                                                            <?php }else{ ?>

                                                                <!-- If the post is not hidden, display the option to hide the post -->
                                                                <div class="post-visible-controls">
                                                                    <!-- Icon indicating the post is visible (eye icon) -->
                                                                    <i class="fa-solid fa-eye"></i>

                                                                    <!-- Form to submit a request to hide the visible post -->
                                                                    <form action="" method="POST">
                                                                        <!-- Button to trigger the 'hide' action -->
                                                                        <button type="submit" name="hide" value="<?=$postID;?>">Hide</button>
                                                                    </form>
                                                                </div><!-- .post-visible-controls -->
                                                            <?php } ?>
                                                        </div><!-- .post-visibility-toggle -->

                                                    </div><!-- .post-interactions -->
                                                </div><!-- .post -->
                                            <?php } // End of if statement for checking re-evaluation request ?>

                                        <?php } // End of while loop for each hidden post with re-evaluation request ?> 
                                    <?php } // End of PHP loop that fetches and displays posts ?> 

                                <?php } else { // If no posts exist ?>
                                    <!-- Message to display when there are no posts -->
                                    <div class="no-posts">
                                        <p>再評価の依頼がありません</p> <!-- "No request for re-evaluation" -->
                                    </div><!-- .no-posts -->
                                <?php } // End of if statement for checking post count ?>

                            </div><!-- .user-posts-list -->
                        </div><!-- .user-posts-wrapper -->
                    </section><!-- #request -->

                </section><!-- .analytics-right-section -->

            </div><!--.analytics-detail-wrapper -->
        </div><!--.admin-main-wrapper -->
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

        // CHART SETUP
        // Retrieve the percentage data for each mood 
        var greatPercentage = <?php echo json_encode($greatPercentage); ?>; // Percentage of 'Great' mood
        var goodPercentage = <?php echo json_encode($goodPercentage); ?>; // Percentage of 'Good' mood
        var okayPercentage = <?php echo json_encode($okayPercentage); ?>; // Percentage of 'Okay' mood
        var badPercentage = <?php echo json_encode($badPercentage); ?>; // Percentage of 'Bad' mood
        var awfulPercentage = <?php echo json_encode($awfulPercentage); ?>; // Percentage of 'Awful' mood
        
        // Get the canvas element for the mood chart
        const myChart = document.getElementById('myChart');

        // Create a new doughnut chart for the monthly mood data
        new Chart(myChart, {
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
    </script>

</html>
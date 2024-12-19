<?php
/**
 * Page Name: Edit Post
 * Author: Moa Burke
 * Date: 2024-10-30
 * Description: This PHP script allows users to edit and delete their posts on the platform. 
 *      It includes necessary session management, database connections, and utility functions 
 *      to handle user authentication and timezone settings.
 * 
 * Features:
 * - Starts a session to track user login status.
 * - Includes database connection and user login check.
 * - Allows users to retrieve and edit their existing posts.
 * - Provides functionality to delete a post.
 * - Displays a confirmation dialog before deleting a post.
 * - Redirects users back to their profile page with feedback messages upon successful updates or deletions.
 *
 *
 * Dependencies:
 * - PHP for server-side processing and MySQL for data management.
 * - FontAwesome for like/comment icons.
 * - JavaScript for delete confirmation.
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
// Get the UserID from the user data
$userID = $userData['UserID']; 

// Get the current date and time
$date = date("Y-m-d");
$dateTime = date("Y-m-d H:i:s");

// Check if the 'edit' button was submitted
if (isset($_POST['edit'])) {
    // Retrieve the post ID from the form submission
    $editedPostID = $_POST['edit'];
    // Get the updated text from the textarea
    $updatedText = $_POST['textarea'];

    // Prepare the SQL query to update the post text for the given post ID
    $updateQuery = "UPDATE posts SET PostedText = '$updatedText' WHERE PostID = '$editedPostID' ";
    $updateQueryRun = mysqli_query($con, $updateQuery); // Execute the SQL query
        
    // Check if the query was successful
    if ($updateQueryRun) {
        // Set a success message in the session if the update was successful
        $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>正常に更新されました。</p></div>";
        // Redirect to the user profile page
        header('Location: my_page.php');
        exit(0); // Stop further execution of the script
    }
}

// Check if the 'delete-post' button was submitted
if (isset($_POST['delete-post'])) {
    // Retrieve the post ID from the form submission
    $postToDeleteID = $_POST['PostID'];

    // Prepare the SQL query to delete the post with the given post ID
    $deleteQuery = "DELETE FROM posts WHERE PostID = '$postToDeleteID' ";
    $deleteQueryRun = mysqli_query($con, $deleteQuery); // Execute the SQL query

    // Check if the query was successful
    if ($deleteQueryRun) {
        // Set a success message in the session if the deletion was successful
        $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>正常に更新されました。</p></div>";
        // Redirect to the user profile page
        header('Location: my_page.php');
        exit(0); // Stop further execution of the script
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Load mutual head components and necessary scripts/styles -->
        <?php includeHeadAssets(); ?>
    </head>

    <body>
        <header class="sidebar-navigation">
            <!-- Include the mutual header for user navigation -->
            <?php renderUserNavigation(); ?>
        </header>
        <!-- Logout button and sticky header for logged-in user -->
        <?php renderUserHeaderWithLogout($userData); ?>

        <div class="main-wrapper">
            <!-- Breadcrumb navigation -->
            <div class="breadcrumbs">
                <a href="../user_home.php"><p>Top Page</p></a>
                <i class="fa-solid fa-angle-right fa-sm"></i>
                <a href="./my_page.php"><p>My Page</p></a>
                <i class="fa-solid fa-angle-right fa-sm"></i>
                <p class="bread-active">Edit Post</p>
            </div>

            <h2>Edit Post</h2>

            <section class="mypage-content-wrapper">
                <div class="edit-post-wrapper">
                    <div class="posts-feed-wrapper">

                        <!-- Title for editing a post, with Japanese and English translations -->
                        <h3>
                            <span class="japanese-title">投稿の編集</span> <!-- Japanese for "Edit Post" -->
                            <span class="english-title">Edit Your Post</span> <!-- English title -->
                        </h3>

                        <!-- Main content area for displaying the post editing form or related content -->
                        <div class="posts-feed">

                            <!-- Form for editing a post -->
                            <form action="" method="post" enctype="multipart/form-data">
                                <?php  
                                // Check if the 'edit-post' button was submitted
                                if (isset($_POST['edit-post'])) {
                                    // Retrieve the post ID from the form submission
                                    $currentPostID = $_POST['edit-post'];
                                    
                                    // Query to get the post details along with the user's information, mood, likes, and comments
                                    $queryPosts = mysqli_query($con,
                                        "SELECT 
                                            u.Username AS Username, 
                                            u.profileImg AS profileImg, 
                                            u.UserID AS UserID, 
                                            p.PostID AS PostID, 
                                            p.PostedText AS PostedText, 
                                            p.Date AS Date, 
                                            l.likes, 
                                            c.comments, 
                                            m.moodEmojiColor AS emoji, 
                                            m.MoodName AS MoodName 
                                        FROM Posts p 
                                        LEFT JOIN users u ON p.UserID = u.UserID
                                        LEFT JOIN moods m ON p.MoodID = m.MoodID 
                                        LEFT OUTER JOIN (
                                            SELECT PostID, CommentID, COUNT(CommentID) AS comments 
                                            FROM comments 
                                            GROUP BY PostID) c 
                                            ON p.PostID = c.PostID
                                            LEFT OUTER JOIN (
                                                SELECT PostID, LikeID, COUNT(LikeID) AS likes 
                                                FROM postlikes 
                                                GROUP BY PostID 
                                            ) 
                                            l ON p.PostID = l.PostID 
                                            WHERE p.PostID = '$currentPostID';"
                                        );
                                    
                                    // Fetch the post data associated with the given post ID
                                    $postData = mysqli_fetch_assoc($queryPosts); 
                            
                                    // Store user-related data from the fetched post row
                                    $authorUsername = $postData['Username']; // Username of the user who posted
                                    $authorProfileImage = $postData['profileImg']; // Path to the user's profile image
                                    $postID = $postData['PostID']; // Unique identifier for the current post
                                    $moodEmoji = $postData['emoji']; // Mood emoji associated with the post
                                    $moodName = $postData['MoodName']; // Name of the mood associated with the post
                                    $postText = $postData['PostedText']; // The text content of the post
                                    $postDate = $postData['Date']; // Date when the post was created
                                    $likeCount = $postData['likes']; // Number of likes for the post
                                    $commentCount = $postData['comments']; // Number of comments for the post

                                    // Query to check if the current post is marked as hidden
                                    $queryCheckHidden = "SELECT * FROM posts WHERE PostID = '$postID' AND Hidden = '" . POST_HIDDEN . "'";
                                    // Execute the query to check if the post is hidden
                                    $resultCheckedHidden = mysqli_query($con, $queryCheckHidden);
                                    // Get the number of rows returned by the query
                                    $checkedHidden = mysqli_num_rows($resultCheckedHidden); ?>

                                            
                                    <div class="post-item <?php echo $moodName; ?> 
                                        <?php 
                                        if ($checkedHidden > 0) { 
                                            echo "hidden-post";
                                        } ?>">

                                        <div class="post-header">

                                            <!-- Container for the posted user's information -->
                                            <div class="post-author">

                                                <!-- User's profile image -->
                                                <div class="author-profile-img">
                                                    <!-- Display the user's profile image -->
                                                    <img src="../../assets/user-img/<?php echo $authorProfileImage ?>" alt="profile img">
                                                </div>

                                                <div class="author-info">

                                                    <!-- User information container -->
                                                    <p class="author-username">
                                                        <!-- Display the username with "@" prefix -->
                                                        <?php echo "@" . htmlspecialchars($authorUsername); ?>
                                                    </p>

                                                    <!-- Display the time difference between the post creation date and the current date -->
                                                    <p class="post-time-elapsed">
                                                        <?php
                                                        // Convert the date strings to timestamps
                                                        $currentTimestamp = strtotime($dateTime); // Current date and time
                                                        $postCreationTimestamp = strtotime($postDate); // Post creation date and time

                                                        // Calculate the difference in minutes
                                                        $timeDifferenceMinutes = round(abs($currentTimestamp - $postCreationTimestamp) / 60,2);

                                                        // Determine and display the time difference in a human-readable format
                                                        if ($timeDifferenceMinutes >= 60) { // Check if the difference is at least one hour
                                                            $timeDifferenceHours = round($timeDifferenceMinutes / 60); // Convert minutes to hours

                                                            // Check if the difference is at least one day
                                                            if ($timeDifferenceHours >= 24) {
                                                                $timeDifferenceDays = round($timeDifferenceHours / 24); // Convert hours to days

                                                                // Check if the difference is at least one year
                                                                if ($timeDifferenceDays >= 365) {
                                                                    $timeDifferenceYears = round($timeDifferenceDays / 365); // Convert days to years
                                                                    echo $timeDifferenceYears . "年"; // Output the difference in years
                                                                } else {
                                                                    echo $timeDifferenceDays . "日"; // Output the difference in days
                                                                }
                                                            } else {
                                                                echo $timeDifferenceHours . "時間"; // Output the difference in hours
                                                            }
                                                        } else {
                                                            echo round($timeDifferenceMinutes) ."分"; // Output the difference in minutes
                                                        } ?>
                                                    </p>

                                                </div><!-- .user-info -->
                                            </div><!-- .post-author -->

                                            <!-- Display the post emoji associated with the post -->
                                            <div class="post-emoji">
                                                <?php echo $moodEmoji; ?>
                                            </div><!-- .post-emoji -->

                                        </div><!-- .post-top -->

                                        <div class="post-content-wrapper">
                                            <div class="post-text-content">
                                                <textarea id="textarea" name="textarea" required><?php echo $postText; ?></textarea>
                                            </div><!-- .post-box -->
                                        </div><!-- ."post-main-content -->
        
                                        <?php
                                        // Prepare a query to check if the current user has liked the post
                                        $queryCheckLiked = "SELECT * FROM postlikes WHERE UserID = $userID AND PostID = '$postID'";
                                        // Execute the query and store the result
                                        $resultCheckLiked = mysqli_query($con, $queryCheckLiked);
                                        // Count the number of rows returned to determine if the post is liked by the user
                                        $checkLiked = mysqli_num_rows($resultCheckLiked); 
                                        ?>

                                        <!-- Post bottom div for likes and comments -->
                                        <div class="post-interactions">
                                            <div class="post-likes">
                                                <?php 
                                                // Display a filled heart icon if the post is liked, otherwise display an outline heart icon
                                                if ($checkLiked > 0) {  
                                                    echo '<i class="fa-solid fa-heart"></i>'; // Filled heart icon for liked post
                                                } else { 
                                                    echo '<i class="fa-regular fa-heart"></i>'; // Outline heart icon for unliked post
                                                } ?>

                                                <!-- Display the count of likes -->
                                                <p><?php echo $likeCount; ?></p>
                                            </div><!-- .likes -->

                                            <div class="post-comments">
                                                <!-- Display a comment icon -->
                                                <i class="fa-regular fa-comment"></i>

                                                <!-- Display the count of comments -->
                                                <p><?php echo $commentCount; ?></p>
                                            </div><!-- .comments -->

                                        </div><!-- .post-bottom -->
                                    </div><!-- ./user-post -->

                                    <div class="edit-post-buttons">
                                        <!-- Form for deleting the post -->
                                        <form action="" method="post">
                                            <!-- Hidden input to store the PostID for the delete action -->
                                            <input type="hidden" name="PostID" value="<?php echo $postID; ?>">

                                            <!-- Button to submit the delete request for the current post -->
                                            <button type="submit" class="secondary-btn" name="delete-post" value="<?php $postID;?>" onclick="return checkDelete()">Delete Post</button>
                                        </form>

                                        <!-- Button to submit the edit form for the current post -->
                                        <button type="submit" class="primary-btn" name="edit" value="<?=$postID;?>">Edit</button>

                                          
                                        </button>
                                    </div><!-- .edit-post-buttons -->

                                <?php } // End of the if block checking if the 'edit-post' button was submitted ?>
                            </form>

                        </div><!-- .posts-main -->
                    </div><!-- .posts-feed-wrapper -->
                </div><!-- .edit-post-wrapper -->
            </section><!-- .mypage-content-wrapper -->
        </div><!-- .main-wrapper -->

        <script>
            // Function to confirm deletion action
            function checkDelete() {
                return confirm("本当に削除しますか?"); // Ask the user for confirmation before deleting
            }
        </script>

    </body>
</html>
<?php
/**
 * Page Name: My Page - User Profile
 * Author: Moa Burke
 * Date: 2024-10-30
 * Description: This page serves as the user's profile dashboard, allowing the logged-in user to view and interact with their posts, likes, and comments. 
 *      Users can view personal information, update their profile image, and see detailed post information with comments and likes. The page also provides 
 *      tools for users to manage their comments and likes on posts, ensuring easy interaction with their content.
 *
 * Notes:
 * - JavaScript is used to manage modal functionality for uploading a new profile image, toggle the display of comments, and provide confirmation for deletions.
 *
 * Dependencies:
 * - PHP for server-side processing and MySQL for data management.
 * - FontAwesome for like/comment icons.
 * - JavaScript for modal, comment display toggling, and delete confirmation.
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

define("MINUTES_IN_HOUR", 60);
define("HOURS_IN_DAY", 24);
define("DAYS_IN_YEAR", 365);

// Check if the user is logged in and retrieve user data
$userData = check_login($con);

// Get the UserID from the user data
$userID = $userData['UserID']; 

// Get the current date and time
$date = date("Y-m-d");
$dateTime = date("Y-m-d H:i:s");

// Get the user's profile image from the databASe
$userProfileImageQuery =  mysqli_query($con, "SELECT profileImg FROM users WHERE UserID = '$userID' ");
$profileImageCount = mysqli_num_rows($userProfileImageQuery);

// Retrieve posts and ASsociated data for the user
$userPostsQuery = mysqli_query($con,
    "SELECT 
        u.Username AS Username, 
        u.profileImg AS profileImg, 
        u.UserID AS UserID, 
        p.PostID AS PostID, 
        p.PostedText AS PostedText, 
        p.Date AS Date, l.likes, 
        c.comments, 
        m.moodEmojiColor AS emoji, 
        m.MoodName AS MoodName 
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
    ) l ON p.PostID = l.PostID 
    WHERE u.UserID = '$userID' 
    GROUP BY p.PostID  
    ORDER BY Date DESC;"
);
    
// Get the number of posts returned for the user's query
$totalUserPosts = mysqli_num_rows($userPostsQuery); 


// Check if the image upload form has been submitted
if (isset($_POST['uploadImg'])) {

    // Check if an image file has been uploaded
    if (!empty($_FILES['image']['name'])) {
        // Get the file extension of the uploaded image
        $fileExtension = substr($_FILES['image']['name'], -4);

        // Validate the file extension
        if ($fileExtension != '.jpg' && $fileExtension != '.png') {
            // Set an error message if the file type is not allowed
            $uploadErrors['image'] = 'その画像は使用できません。使用可能拡張子：.img .jpg';
        }
    }

    // Proceed if there are no upload errors
    if (empty($uploadErrors)) {
        // Temporarily store the uploaded image
        if (!empty($_FILES['image']['name'])) {
            $uploadedImageName = date('YmdHis') . $fileExtension;  // Generate a unique name for the image
        } else {
            $uploadedImageName = ''; // No image uploaded
        }

        // Attempt to move the uploaded file to the designated directory
        $isUploadSuccessful = move_uploaded_file($_FILES['image']['tmp_name'], '../../assets/user-img/' . $uploadedImageName);

        // If the file upload is successful
        if ($isUploadSuccessful) {    
            // Prepare the SQL query to update the user's profile image
            $updateProfileImageQuery = "UPDATE users SET profileImg = '$uploadedImageName' WHERE UserID = '$userID' ";
            $isQueryExecuted = mysqli_query($con, $updateProfileImageQuery);
        }
    
        // Check if the query executed successfully
        if ($isQueryExecuted) {
            // Set a success message in the session
            $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>画像変更しました。</p></div>";
            header('Location: ./my_page.php'); // Redirect to the user's page
            exit(0);// Stop execution after the redirect
        } else {
            // Set an error message in the session if the upload fails
            $_SESSION['feedbackMessage'] = "<div class='message-text fail-alert'><p>画像のアップロードが失敗しました。</p></div>";
            header('Location: ./my_page.php'); // Redirect to the user's page
            exit(0); // Stop execution after the redirect
        }
    }
}

// Check if the comment form has been submitted
if(isset($_POST['comment'])){

    // Check if the comment area is empty
    if ($_POST['commentarea'] == "") {
        // Set an error message if the comment area is empty
        $formErrors['commentarea'] = "コメントを入力してください"; // Please enter a comment
    }

    // Proceed if there are no form errors
    if (empty($formErrors)) {
        // Get the Post ID from the submitted data
        $currentPostID = $_POST['PostID'];

        // Get the user's comment from the submitted data
        $userComment = $_POST['commentarea'];

        // Prepare the SQL query to insert the comment into the database
        $insertCommentQuery = "INSERT INTO comments (Comment, UserID, PostID) VALUES('$userComment', '$userID', '$currentPostID')";

        // Execute the query and check if the insertion was successful
        $isInsertSuccessful = mysqli_query($con, $insertCommentQuery);
           
        // If the insertion was successful
        if ($isInsertSuccessful) {
            // Redirect to the user's page
            header('Location: my_page.php');
             exit(0); // Stop execution after the redirect
        }

    }
}

// Check if the delete-post form has been submitted
if (isset($_POST['delete-post'])) {
    // Get the Post ID to delete from the submitted data
    $postIDToDelete = $_POST['PostID'];

    // Check if the post exists for the current user
    $checkPostQuery = mysqli_query($con, "SELECT * FROM posts WHERE UserID = '$userID' AND PostID = '$postIDToDelete'");
    $postCount = mysqli_num_rows($checkPostQuery);

    // If the post exists
    if($postCount > 0){
        // Delete any requests related to the post
        $deleteRequestQuery = "DELETE FROM requestcheck WHERE PostID = '$postIDToDelete'";
        mysqli_query($con, $deleteRequestQuery); 

        // Delete comments associated with the post
        $deleteCommentsQuery = "DELETE FROM comments WHERE PostID = '$postIDToDelete'";
        mysqli_query($con, $deleteCommentsQuery); 

        // Delete likes associated with the post
        $deletePostLikesQuery = "DELETE FROM postlikes WHERE PostID = '$postIDToDelete'";
        mysqli_query($con, $deletePostLikesQuery); 
        
        // Delete the actual post
        $deletePostQuery = "DELETE FROM posts WHERE PostID = '$postIDToDelete'";
        $isQueryExecuted = mysqli_query($con, $deletePostQuery); 

        // Check if the query executed successfully
        if ($isQueryExecuted) {
            // Set a success message in the session
            $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>正常に削除されました。</p></div>";

        } else {
            // Set an error message in the session if the deletion fails
            $_SESSION['feedbackMessage'] = "<div class='message-text fail-alert'><p>何か問題が発生しました。</p></div>";
        }

        // If the comments were successfully deleted
        if ($deleteCommentsQuery) {
            // Redirect to the user's page
            header('Location: my_page.php');
            exit(0); // Stop execution after the redirect
        }
    }
}

// Check if the request form has been submitted
if(isset($_POST['request'])){
    // Get the Post ID for the request from the submitted data
    $requestedPostID = $_POST['request'];

    // Prepare the SQL query to insert the request into the database
    $insertRequestQuery = "INSERT INTO requestcheck (PostID) VALUES('$requestedPostID')";

    // Execute the query and check if the insertion was successful
    $isInsertSuccessful = mysqli_query($con, $insertRequestQuery);

    // If the insertion was successful
    if ($isInsertSuccessful) {
        // Set a success message in the session
        $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>再評価を依頼しました。</p></div>";
        
        // Redirect to the user's page
        header('Location: ./my_page.php');
        exit(0); // Stop execution after the redirect
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Load mutual head components and necessary scripts/styles -->
        <?php includeHeadAssets(); ?>
        <!-- JavaScript for handling alert notifications and user interactions -->
        <script src="../../assets/javascript/alert_handler.js" defer></script>
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
                <p class="bread-active">My Page</p>
            </div><!-- .breadcrumbs -->

            <!-- Wrapper for the user's My Page section -->
            <article class="mypage-content-wrapper">

                <!-- Left section of the My Page layout -->
                <section class="mypage-left-section">

                    <!-- Include file for alert messages -->
                    <?php include('../../server-side/shared/feedback_messages.php'); ?>

                    <!-- Profile image container -->
                    <div class="profile-image" id="profile-image">
                        <img src="../../assets/user-img/<?php 
                            // Check if there are profile images available
                            if ($profileImageCount > 0) {
                                // Fetch the user's profile image from the database
                                $profileImageData = mysqli_fetch_assoc($userProfileImageQuery); 
                                $profileImageFilename = $profileImageData['profileImg']; 
                                echo $profileImageFilename; // Output the profile image filename
                            } ?>" 
                        alt="User Profile Image"> <!-- Image displayed as the user's profile picture -->

                        <!-- Label for changing the image -->
                        <p>画像変更</p>
                    </div><!-- .profile-image -->

                    <h2>My page</h2>

                    <!-- Container for user information -->
                    <div class="user-name-container">
                        <!-- Container for displaying the user's full name -->
                        <div class="full-name">
                            <?php 
                            // Check if the first name contains only alphabetic characters
                            if (ctype_alpha($userData['FirstName'])) { 
                                // If true, display the first name and last name
                                echo "<p>" . htmlspecialchars($userData['FirstName']) . "</p><p>" . $userData['LastName'] . "</p>" ;
                            } else { 
                                echo "<p>" . $userData['LastName'] . "</p><p>" . htmlspecialchars($userData['FirstName']) . "</p>" ; 
                            }
                            ?>
                        </div>

                        <!-- Container for displaying the username -->
                        <div class="username">
                            <p>@<?php echo htmlspecialchars($userData['Username']) ?></p> <!-- Display the username with '@' symbol -->
                        </div>
                    </div><!-- .user-name-container -->

                    <!-- Main container for the user information -->
                    <div class="user-profile-contents">
                        <div class="account-info-table">

                            <!-- Container for account creation information -->
                            <div class="account-creation-info">
                                <p>アカウント作成：<?php echo $userData['Created'] ?></p> <!-- Display the account creation date -->
                            </div>

                            <!-- Table to display user details -->
                            <table>
                                <!-- Row for username -->
                                <tr>
                                    <td>ユーザー名</td> <!-- Column header for username -->
                                    <td><?php echo htmlspecialchars($userData['Username']) ?></td> <!-- Display the username -->
                                </tr>
                                <!-- Row for first name -->
                                <tr>
                                    <td>名前</td> <!-- Column header for first name -->
                                    <td><?php echo htmlspecialchars($userData['FirstName']) ?></td> <!-- Display the first name -->
                                </tr>
                                <!-- Row for last name -->
                                <tr>
                                    <td>苗字</td> <!-- Column header for last name -->
                                    <td><?php echo htmlspecialchars($userData['LastName']) ?></td> <!-- Display the last name -->
                                </tr>
                                <!-- Row for email address -->
                                <tr>
                                    <td>メールアドレス</td> <!-- Column header for email address -->
                                    <td><?php echo $userData['Email'] ?></td> <!-- Display the email address -->
                                </tr>
                                <!-- Row for password (hidden) -->
                                <tr>
                                    <td>パスワード</td> <!-- Column header for password -->
                                    <td>********</td> <!-- Placeholder for password (not displayed for security) -->
                                </tr>
                            </table> <!-- End of user details table -->

                        </div><!-- .account-info-table -->

                        <!-- Container for action buttons -->
                        <div class="profile-action-buttons">
                            <!-- Button to edit profile -->
                            <a href="account_edit.php"><button class="primary-btn">Edit</button></a>
                        </div>

                    </div><!-- .user-profile-contents -->

                </section><!-- .mypage-left-section -->

                <!-- Container for the right side of the My Page, holding user posts -->
                <section class="mypage-right-section">

                    <!-- Wrapper for the posts feed section -->
                    <div class="feed-container">

                        <!-- Top section of the posts feed -->
                        <div class="post-section">
                            <h3>
                                <span class="japanese-title">あなたのフィード投稿</span> <!-- Japanese title for user's feed posts -->
                                <span class="english-title">Your posts on the feed</span> <!-- English title for user's feed posts -->
                            </h3>
                        </div><!-- .post-section -->

                        <!-- Main section displaying individual user posts -->
                        <div class="posts-feed">
                            <?php 
                            // Check if the user has any posted any posts
                            if ($totalUserPosts > 0) {
                                // Loop through each user post
                                while ($postData = mysqli_fetch_assoc($userPostsQuery)) { 
                                    // Assign variables for post details
                                    $postAuthorName = $postData['Username']; // User's name who made the post
                                    $postAuthorProfileImg = $postData['profileImg']; // Profile image URL of the user
                                    $postID = $postData['PostID']; // Unique identifier for the post
                                    $postEmoji = $postData['emoji'];  // Emoji associated with the post
                                    $postMoodName = $postData['MoodName']; // Name of the mood associated with the post
                                    $postTextContent = $postData['PostedText']; // Text content of the post
                                    $postDateCreated = $postData['Date']; // Date the post was created
                                    $totalLikes = $postData['likes']; // Number of likes on the post
                                    $totalComments = $postData['comments']; // Number of comments on the post

                                    // Query to check if the post is marked as hidden
                                    $checkHiddenPostQuery = "SELECT * FROM posts WHERE PostID = '$postID' AND Hidden = '" . POST_HIDDEN . "'";
                                    $hiddenPostCheckResult = mysqli_query($con, $checkHiddenPostQuery);
                                    $checkedHidden = mysqli_num_rows($hiddenPostCheckResult); // Count if the post is hidden
                                    ?>

                                    <!-- User post container with dynamic mood and visibility -->
                                    <div class="post-item <?php echo $postMoodName; ?> <?php if($checkedHidden > 0){ echo "hidden-post";}?>"> 
                                        <?php 
                                        // Check if the post is hidden
                                        if ($checkedHidden > 0) { 
                                            // Query to check if a request exists for the current post
                                            $queryRequestExist = mysqli_query($con, "select * from requestcheck where PostID = '$postID'");
                                            $requestExistCount = mysqli_num_rows($queryRequestExist); 
                                            
                                            // Query to retrieve blocked words from the database
                                            $getBlockedWordsQuery = mysqli_query($con, "select * from blockedwords");
                                            $blockedWordsCount = mysqli_num_rows($getBlockedWordsQuery); 
                                            
                                            // Check if there are blocked words to evaluate
                                            if ($blockedWordsCount > 0) {
                                                $foundBlockedWords = []; // Array to store any blocked words found in the post content

                                                // Loop through each blocked word
                                                while ($blockedWordData = mysqli_fetch_assoc($getBlockedWordsQuery)) {
                                                    $blockedWord = $blockedWordData['blockedWord']; 
                                                    $postText = $postTextContent; // The text content of the post

                                                    // Check if the post text contains any blocked words
                                                    if (preg_match("/{$blockedWord}/i", $postText)) { 
                                                        $foundBlockedWords[] = $blockedWord; // Add the blocked word to the array
                                                    }
                                                }
                                            } ?>

                                            <!-- Message displayed when a post is hidden due to guideline violations -->
                                            <div class="post-hidden-guideline-violation">
                                                <!-- Explanation for the user about why the post is hidden -->
                                                <p>この投稿がガイドラインに違反しているので、別のユーザーに表示されていません。誤りがあると思われる場合、あなたは申し立てをすることができ、その場合は再評価を致します。</p>
                                                
                                                <?php 
                                                // Check if there are any blocked words found in the post content
                                                if (count($foundBlockedWords) > 0) { ?>
                                                    <!-- Check if any blocked words were found in the post content -->
                                                    <!-- If there are blocked words, display a list of these words to the user -->
                                                    <p>禁止用語：
                                                        <ul>
                                                            <?php
                                                            // Loop through each blocked word found in the post
                                                            foreach ($foundBlockedWords as $word) { ?>
                                                                <li><?php echo htmlspecialchars($word);?></li> <!-- Output each blocked word in a list -->
                                                            <?php } ?>
                                                        </ul>
                                                    </p>
                                                <?php } ?>
                                                
                                            </div><!-- .post-hidden-guideline-violation"-->

                                            <div class="reevaluation-request">
                                                <?php 
                                                if ($requestExistCount > 0) { ?>
                                                    <!-- If there is an existing request for reevaluation, display the requested status -->
                                                    <div class="reevaluation-requested">
                                                        <i class="fa-solid fa-flag"></i> <!-- Icon indicating a request -->
                                                        <p>再評価の依頼中</p> <!-- Message indicating that a reevaluation request is currently pending -->
                                                    </div><!-- .request -->

                                                <?php }else{ ?>

                                                    <!-- If no reevaluation request exists, display the form to submit a new request -->
                                                    <form action="" method="POST">
                                                        <!-- Button to submit reevaluation request with post ID -->
                                                        <button type="submit" name="request" value="<?=$postID;?>">再評価の依頼</i></button>
                                                    </form>

                                                <?php } ?>
                                            </div><!-- .reevaluation-request -->

                                        <?php } ?> <!-- End of the if statement checking if the post is hidden -->

                                        <div class="post-header">
                                            <div class="edit-button-wrapper">
                                                <!-- Form for editing the post -->
                                                <form action="post_edit.php" method="POST">
                                                    <!-- Button to submit the edit request with the post ID -->
                                                    <button type="submit" name="edit-post" value="<?=$postID;?>">
                                                        <i class="fa-solid fa-pen-to-square"></i> <!-- Icon indicating editing -->
                                                    </button>
                                                </form>
                                            </div><!-- .edit-button-wrapper -->

                                            <?php 
                                            // Query to check if the current user has a post with the specified PostID
                                            $postCheckQuery = mysqli_query($con, "select * from posts where UserID = '$userID' AND PostID = '$postID'");

                                            // Count the number of posts returned by the query
                                            $numberOfPosts = mysqli_num_rows($postCheckQuery);

                                            // Check if any posts were found for the user with the given PostID
                                            if ($numberOfPosts > 0) { 
                                                $buttonDisplayStatus = ""; // If a post exists, set display to empty (visible)
                                            } else { 
                                                $buttonDisplayStatus = "hidden-btn"; // If no post exists, set display to hidden (invisible)
                                            } ?>

                                            <!-- Container for the delete post button, with a dynamic class for visibility -->
                                            <div class="delete-button-wrapper <?php echo $buttonDisplayStatus;?>">
                                                <!-- Button to open the delete confirmation modal -->
                                                <button id="delete-post-button-<?php echo $postID; ?>" class="teritary-btn delete-post-button" data-postid="<?php echo $postID; ?>"><i class="fa-solid fa-trash"></i></button>
                                            </div><!-- .delete-post -->

                                            <!-- Modal dialog for delete confirmation -->
                                            <div id="deleteConfirmationModal-<?php echo $postID; ?>" class="modal">
                                                <div class="modal-content">
                                                    <p class="japanese-prompt">本当に削除してよろしいですか?</p><!-- Confirmation prompt in Japanese asking if the user really wants to delete -->
                                                    <p class="english-prompt">Are you absolutely sure you want to delete this item? </p>
                                                    <!-- Main container for modal buttons -->
                                                    <div class="modal-main">
                                                        <!-- Button to close the modal without deleting -->
                                                        <div class="cancel-delete-button"> 
                                                            <p>Cancel</p>
                                                        </div><!-- .cancel-delete-btn -->

                                                        <!-- Container for the delete form -->
                                                        <div>
                                                            <!-- Form to submit the delete request -->
                                                            <form action="" method="post">
                                                                <!-- Hidden input to store the post ID for deletion -->
                                                                <input type="hidden" name="PostID" value="<?php echo $postID; ?>">

                                                                <!-- Wrapper for the delete button -->
                                                                <div class="delete-button-container">
                                                                    <!-- Submit button for deletion, hidden until label is clicked -->
                                                                    <input type="submit" id="delete_btn<?php echo $postID; ?>" value="Delete" name="delete-post" class="delete-confirmation-button" />
                                                                </div><!-- .delete-button-container -->

                                                            </form>
                                                        </div> 

                                                    </div><!-- .modal-main -->
                                                </div><!-- .modal-content -->
                                            </div><!-- .modal -->

                                            <!-- Container for the posted user's information -->
                                            <div class="post-author">
                                                <!-- User's profile image -->
                                                <div class="author-profile-img">
                                                    <img src="../../assets/user-img/<?php echo $postAuthorProfileImg ?>" alt="Author Profile Image"> <!-- Display the user's profile image -->
                                                </div>

                                                <!-- User information container -->
                                                <div class="author-info">
                                                    <!-- Display the username with "@" prefix -->
                                                    <p class="author-username"><?php echo "@" . htmlspecialchars($postAuthorName); ?></p>
                                                    
                                                    <!-- Display the time difference between the post creation date and the current date -->
                                                    <p class="post-time-elapsed">
                                                        <?php
                                                        // Convert the date strings to timestamps
                                                        $currentTimestamp = strtotime($dateTime);  // Current date and time
                                                        $postCreationTimestamp = strtotime($postDateCreated); // Post creation date and time

                                                        // Calculate the difference in minutes
                                                        $timeDifferenceMinutes = round(abs($currentTimestamp - $postCreationTimestamp) / MINUTES_IN_HOUR,2);

                                                        // Determine and display the time difference in a human-readable format
                                                        if ($timeDifferenceMinutes >= MINUTES_IN_HOUR) {  // Check if the difference is at least one hour
                                                            $timeDifferenceHours = round($timeDifferenceMinutes / MINUTES_IN_HOUR); // Convert minutes to hours

                                                            // Check if the difference is at least one day
                                                            if ($timeDifferenceHours >= HOURS_IN_DAY) {
                                                                $timeDifferenceDays = round($timeDifferenceHours / HOURS_IN_DAY); // Convert hours to days

                                                                // Check if the difference is at least one year
                                                                if ($timeDifferenceDays >= DAYS_IN_YEAR) {
                                                                    $timeDifferenceYears = round($timeDifferenceDays / DAYS_IN_YEAR); // Convert days to years
                                                                    echo $timeDifferenceYears . "年"; // Output the difference in years
                                                                } else {
                                                                    echo $timeDifferenceDays . "日"; // Output the difference in days
                                                                }
                                                            } else {
                                                                echo $timeDifferenceHours . "時間"; // Output the difference in hours
                                                            }
                                                        } else { 
                                                            echo round($timeDifferenceMinutes) ."分"; // Output the difference in minutes
                                                        } 
                                                        ?>
                                                    </p>

                                                </div><!-- .author-info -->
                                            </div><!-- .post-author -->
                            
                                            <!-- Display the post emoji associated with the post -->
                                            <div class="post-emoji">
                                                <?php echo $postEmoji; ?>
                                            </div><!-- .post-emoji -->
                                        </div><!-- .post-header -->
                                
                                        <!-- Main content of the post -->
                                        <div class="post-content-wrapper">
                                            <!-- Display the text content of the post, ensuring HTML special characters are escaped for security -->
                                            <div class="post-text-content"><?php echo htmlspecialchars($postTextContent); ?></div><!-- .post-emoji -->
                                        </div><!-- ."post-content-wrapper -->
        
                                        <?php
                                        // Query to check if the current user has liked the post
                                        $checkUserLikedPostQuery = "SELECT * FROM postlikes WHERE UserID = $userID AND PostID = '$postID'";
                                        // Execute the query and store the result
                                        $resultCheckLiked = mysqli_query($con, $checkUserLikedPostQuery);
                                        // Count the number of likes by this user for the post
                                        $userLikedCount = mysqli_num_rows($resultCheckLiked); ?>

                                        <!-- Bottom section of the post, containing likes and comments information -->
                                        <div class="post-interactions">

                                            <!-- Likes section -->
                                            <div class="post-likes">
                                                <!-- Link to toggle the like status of the post -->
                                                <a href="../../server-side/user/toggle_post_like_mypage.php?=article&id=<?php echo $postID; ?>">   
                                                    <?php 
                                                    // Display filled heart icon if liked, otherwise display outlined heart icon 
                                                    if ($userLikedCount > 0) { 
                                                        echo '<i class="fa-solid fa-heart"></i>'; // Filled heart for liked post
                                                    } else { 
                                                        echo '<i class="fa-regular fa-heart"></i>'; // Outlined heart for unliked post
                                                    } ?>
                                                </a>

                                                <!-- Display the number of likes for the post -->
                                                <p><?php echo $totalLikes; ?> </p>
                                            </div><!-- .post-likes -->

                                            <!-- Comments section -->
                                            <div class="post-comments">
                                                <i class="fa-regular fa-comment"></i> <!-- Comment icon -->

                                                <!-- Display the number of comments for the post, escaping HTML special characters for safety -->
                                                <p><?php echo htmlspecialchars($totalComments); ?></p>
                                            </div><!-- .post-comments -->
                                        </div><!-- .post-interactions -->

                                        <!-- Form for adding a comment to the post -->
                                        <form action="" method="post">
                                            <!-- Hidden input to store the PostID -->
                                            <input type="hidden" name="PostID" value="<?= $postID; ?>">

                                            <div class="comment-section">
                                                <?php 
                                                // Check if the post is not hidden (checkedHidden equals 0)
                                                if ($checkedHidden == 0) { ?>
                                                    <div class="comment-main">
                                                        <!-- Section displaying the user's profile image -->
                                                        <div class="comment-author">
                                                            <div class="author-profile-img">
                                                                <img src="../../assets/user-img/<?php echo $postAuthorProfileImg ?>" alt="profile img"> <!-- User's profile image -->
                                                            </div>
                                                        </div><!-- .post-author -->

                                                        <!-- Comment input area -->
                                                        <div class="comment-input">
                                                            <textarea id="commentarea" name="commentarea" placeholder="comment" required></textarea> <!-- Textarea for entering comments -->
                                                        </div><!-- .comment-input -->

                                                        <!-- Submit button wrapper -->
                                                        <div class="comment-submit">
                                                            <input type="submit" value="" name="comment" class="button"/> <!-- Submit button for the comment -->
                                                            <i class="fa-solid fa-chevron-right"></i> <!-- Icon indicating submission -->
                                                        </div>  
                                                    </div><!-- .comment-main -->
                                                <?php } ?>
                                            </div><!-- .comment-section -->
                                        </form>

                                        <!-- Container to display comments; initially hidden with 'comments-list-hide' class -->
                                        <div class="comments-display comments-hidden">
                                            <?php 
                                            // Display the comments arrow and icon only if there are comments 
                                            if ($totalComments > 0) {
                                                echo '<p class="arrow">Comments<i class="fa-solid fa-angle-down"></i></p>';
                                            } ?>

                                            <!-- Section containing the actual comments -->
                                            <div class="comments-list">
                                                <div class="comments-container">
                                                    <?php 
                                                    // Query to retrieve comments for the current post, along with each comment's user info
                                                    $commentsQuery = mysqli_query($con, 
                                                        "SELECT c.CommentID, c.Comment, c.Date, u.Username, u.profileImg 
                                                        FROM comments c
                                                        LEFT JOIN users u ON c.UserID = u.UserID 
                                                        WHERE PostID = '$postID' 
                                                        ORDER BY Date DESC; "
                                                    );

                                                    // Count the number of retrieved comments
                                                    $commentCount = mysqli_num_rows($commentsQuery); 
                                                    
                                                    // Check if there are any comments to display
                                                    if ($commentCount > 0) {
                                                        // Loop through each comment retrieved by the query
                                                        while ($commentData = mysqli_fetch_assoc($commentsQuery)) {
                                                            // Extract each comment's ID, content, author name, author profile image, and date
                                                            $commentContent = $commentData['Comment'];
                                                            $commentAuthorName = $commentData['Username'];
                                                            $commentAuthorProfileImg  = $commentData['profileImg'];
                                                            $commentTimestamp = $commentData['Date']; ?>

                                                            <div class="comment-item">
                                                                <!-- Display the user's profile image and username -->
                                                                <div class="comment-author">  

                                                                    <!-- User's profile image -->
                                                                    <div class="author-profile-img">
                                                                        <img src="../../assets/user-img/<?php echo $commentAuthorProfileImg ?>" alt="profile img"> <!-- Display the user's profile image -->
                                                                    </div><!-- .author-profile-img -->

                                                                    <!-- User information container -->
                                                                    <div class="author-info">

                                                                        <!-- Display the comment author's username -->
                                                                        <p class="author-username"><?php echo "@" . $commentAuthorName ; ?></p>

                                                                        <!-- Display the time difference between the post creation date and the current date -->
                                                                        <p class="post-time-elapsed">
                                                                            <?php
                                                                            // Convert the date strings to timestamps
                                                                            $currentTimestamp = strtotime($dateTime); // Current date and time
                                                                            $postedTimestamp = strtotime($commentTimestamp); // Post creation date and time

                                                                            // Calculate the difference in minutes
                                                                            $timeDifferenceMinutes = round(abs($currentTimestamp - $postedTimestamp) / MINUTES_IN_HOUR,2);

                                                                            // Determine whether to display the time difference in minutes, hours, days, or years
                                                                            if ($timeDifferenceMinutes >= MINUTES_IN_HOUR) { // Check if the difference is at least one hour
                                                                                $timeDifferenceHours = round($timeDifferenceMinutes / MINUTES_IN_HOUR); // Convert minutes to hours

                                                                                // Check if the difference is at least one day
                                                                                if ($timeDifferenceHours >= HOURS_IN_DAY) {
                                                                                    $timeDifferenceDays = round($timeDifferenceHours / HOURS_IN_DAY); // Convert hours to days

                                                                                    // Check if the difference is at least one year
                                                                                    if ($timeDifferenceDays >= DAYS_IN_YEAR) {
                                                                                        $timeDifferenceYears = round($timeDifferenceDays / DAYS_IN_YEAR); // Convert days to years
                                                                                        echo $timeDifferenceYears . "年"; // Output the difference in years
                                                                                    } else {
                                                                                        echo $timeDifferenceDays . "日"; // Output the difference in days
                                                                                    }
                                                                                } else {
                                                                                    echo $timeDifferenceHours . "時間"; // Output the difference in hours
                                                                                }
                                                                            } else {
                                                                                echo round($timeDifferenceMinutes) ."分"; // Output the difference in minutes
                                                                            }
                                                                            ?>
                                                                    </div><!-- .author-info -->
                                                                </div><!-- .comment-author -->

                                                                <div class="post-content-wrapper">
                                                                    <!-- Display the main content of the post, which includes the comment text -->
                                                                    <div class="comment-text-content">
                                                                        <?php echo htmlspecialchars($commentContent); // Display the comment content with HTML special characters encoded ?>
                                                                    </div>
                                                                </div><!-- ."post-content-wrapper -->

                                                            </div><!-- .one_comment -->
                                                        <?php 
                                                        } // End of loop through each comment
                                                    } // End of check for comments to display 
                                                    ?> 

                                                </div><!-- .ccomments-container -->
                                            </div><!-- .comments-list -->
                                        </div><!-- .displaycomments -->

                                    </div><!-- .post-item -->
                                <?php } ?> <!-- End of loop through each user post -->

                            <?php } else { // If the user has no posts ?>
                                <p class="empty">あなたはフィードに何も投稿していない。</p> <!-- Message indicating no posts in the feed -->
                            <?php } ?>  <!-- End of check if user has any posts -->

                        </div><!-- .posts-feed -->
                    </div><!-- .feed-container -->
                </section><!-- .mypage-right-section -->
            </article><!-- .mypage-content-wrapper -->
        </div><!-- .main-wrapper -->

        <!-- Image Change Modal -->
        <div id="myModal" class="modal">
            <div class="modal-content profile-image-modal-wraper">
                <!-- Main content area of the image change modal -->
                <div class="modal-main image-change-modal">

                    <!-- Form for uploading a new profile image -->
                    <form action="./my_page.php" method="post" enctype="multipart/form-data">
                        <section class="image-change-form-table">
                            <!-- Modal header with title displayed in both Japanese and English for accessibility -->
                            <h3>
                                <!-- Title in Japanese -->
                                <span class="japanese-title">画像変更</span>
                                <!-- Title in English -->
                                <span class="english-title">Change Image</span>
                            </h3>

                            <!-- Table containing the form for selecting a new image file -->
                            <table>
                                <tr>
                                     <!-- Label in Japanese for the file input field -->
                                    <td>画像</td>
                                    <!-- File input field allowing user to select an image file, accepts all image types -->
                                    <td>
                                        <input type="file" name="image" class="image-upload-input" size="35" value="test" accept="image/*" />
                                    </td>
                                </tr>
                            </table>

                            <div class="modal-buttons">
                                <!-- Cancel button to close the modal -->
                                <div class="cancel-button">
                                    <p>Cancel</p>
                                </div>

                                <!-- Upload button to submit the new image -->
                                <div class="upload-image-button">
                                    <input type="submit" name="uploadImg" value="Upload" class="submit-upload-button"/>
                                </div>
                            </div><!-- .modal-buttons -->
                        </section>
                    </form>
                </div><!-- .modal-main -->
            </div><!-- .modal-content -->
        </div><!-- .modal -->

        <script>
            // Script for handling the delete confirmation modal
            // This script manages the display and hiding of a modal dialog 
            // that prompts the user to confirm deletion of an item.

            // Loop through each delete button and set up individual handlers for each modal
            document.querySelectorAll('.delete-post-button').forEach(button => {
                button.addEventListener('click', function() {
                    // Get the post ID from the button's data attribute
                    const postID = this.getAttribute('data-postid');
                    
                    // Get the specific modal element by its unique ID based on postID
                    const modalElement = document.getElementById('deleteConfirmationModal-' + postID);

                    // Display the modal
                    modalElement.style.display = 'block';
                });
            });

            // Loop through each cancel button to close the corresponding modal
            document.querySelectorAll('.cancel-delete-button').forEach(button => {
                button.addEventListener('click', function() {
                    // Get the modal container by finding its closest ancestor
                    const modal = this.closest('.modal');
                    
                    // Hide the modal when cancel button is clicked
                    modal.style.display = 'none';
                });
            });

            // Optional: Close the modal if the user clicks outside of it
            window.onclick = function(event) {
                // Loop through all modals and close the one clicked outside of
                document.querySelectorAll('.modal').forEach(modal => {
                    if (event.target === modal) {
                        modal.style.display = 'none';
                    }
                });
            };

            // This script handles the display of a modal for image uploading when the user profile image is clicked,
            // manages the display of comments associated with posts, and provides a confirmation prompt for deletion actions.

            // ------- Modal functionality -------
            var imageUploadModal = document.getElementById("myModal"); // Get the modal element by its ID
            var profileImageButton = document.getElementById("profile-image"); // Get the profile image button by its ID\
            var modalCloseButton = document.getElementsByClassName("cancel-button")[0];  // Get the close button element by its class name

            // Set up an event listener for the button click
            profileImageButton.onclick = function() {
                // Display the modal by changing its CSS display property to 'block'
                imageUploadModal.style.display = "block";
            }

            // Set up an event listener for the close button click
            modalCloseButton.onclick = function() {
                // Hide the modal by changing its CSS display property to 'none'
                imageUploadModal.style.display = "none";
            }


            // ------- Confirmation functionality -------
            // Function to prompt the user for confirmation before deleting
            function confirmDeleteAction() {
                // Display a confirmation dialog asking the user if they really want to delete
                return confirm("本当に削除しますか?");
            }


            // ------- Comment display functionality -------
            var commentElements = document.getElementsByClassName("comments-display"); // Get all elements with the class name 'comments-display'
            var totalCommentElements = commentElements.length; // Store the number of comments found

            // Loop through each element and add a click event listener
            for (var i = 0; i < totalCommentElements; i++){
                // Add a click event listener to each 'comments-display' element
                commentElements[i].addEventListener('click', function(){
                    // Toggle the 'comments-list-hide' class on click to show or hide comments
                    this.classList.toggle('comments-hidden');
                })
            }

        </script>
    </body>
</html>
<?php
session_start();// Start the session

// Define a constant for the base path
define('BASE_PATH', '../server-side/shared/');

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

// Retrieve the user data from the database
$userQueryResult = mysqli_query($con, "SELECT * FROM users WHERE UserID = '$userID'");
$userDataFromDB = mysqli_fetch_assoc($userQueryResult);
$userProfileImage = $userDataFromDB['profileImg']; // Get the user's profile image
// $user_username = $userDataFromDB['Username']; // Get the user's username

// Check if the user has posted today my querying the daily tracking tabke
$userPostTodayQuery =  mysqli_query($con, "SELECT * FROM dailytracking WHERE UserID = '$userID' AND Date = '$date'");
$hasUserPostedToday = mysqli_num_rows($userPostTodayQuery); // Check if there are any rows for today


// Retrieve all mood options from the database
$moodsQuery = mysqli_query($con,"SELECT * FROM Moods");
$totalAvailableMoods = mysqli_num_rows($moodsQuery); // Count the number of mood options availible

// Retrieve posts with additional data such as likes, comments, and mood
$postsWithDetailsQuery = mysqli_query($con,
    "SELECT 
        u.Username AS Username, 
        u.profileImg AS profileImg, 
        u.UserID AS UserID, 
        p.PostID AS PostID, 
        p.Hidden, 
        p.PostedText AS PostedText, 
        p.Date AS Date, 
        l.likes, 
        c.comments, 
        m.moodEmojiColor AS emoji, 
        m.MoodName as MoodName 
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
    WHERE Hidden = '" . POST_VISIBLE . "' 
    GROUP BY p.PostID 
    ORDER BY Date DESC;"
    );
$postsCount = mysqli_num_rows($postsWithDetailsQuery); // Count the number of post retrieved

// // Retrieve goal data from the gials table
// $queryGoals = mysqli_query($con,"SELECT * FROM goals");
// $rowsGoals = mysqli_num_rows($queryGoals); // Count the number of goals

// Retrieve the goals specific to the logged-in user
$userGoalsQuery = mysqli_query($con, "SELECT * FROM usergoals WHERE UserID = $userID");
$userGoalsCount = mysqli_num_rows($userGoalsQuery); // Count the numebr of user-specific goals


$postStatus = "";

// Handle a post submission
if(isset($_POST['post'])){
    // Check if a mood is selected
    if ($_POST['mood'] == "") { // No mood is selected
        $error['mood_missing'] = "気分を選択してください";
    }
    // Check if the area has input
    if ($_POST['textarea'] == "") { // No text input
        $error['textarea_empty'] = "何かを入力してください";
    }
    // If there are no errors, procees with the post
    if (empty($error)) {
        $mood = $_POST['mood'];
        $text = $_POST['textarea'];

        // Retrieve blocked words from the database
        $blockedWordsQuery = mysqli_query($con,"SELECT * FROM blockedWords");
        $blockedWordsCount = mysqli_num_rows($blockedWordsQuery);

        // Check if there are any blocked words to evaluate
        if ($blockedWordsCount > 0){ 
            // Loop through each blocked word retrieved from the database
            while ($rowBlockedWords =  mysqli_fetch_assoc($blockedWordsQuery)){
                $blockedWord = $rowBlockedWords['blockedWord']; // Store the blocked word
                $postText = $text; // Assign the user's post text to a variable
                $searchToSend = $blockedWord; // Set the blocked word for comparison 

                //Check if the user's post contains any blocked words (case insensitive)
                if (preg_match("/{$searchToSend}/i", $postText)) { 
                    // If a blocked word is found, set the post to hidden (not visible on the feed)
                    $insertPostQuery = "INSERT INTO posts (MoodID, PostedText, UserID, Hidden) VALUES('$mood', '$text', '$userID', '" . POST_HIDDEN . "')";
                    $postStatus = "display-error"; // Mark the post as containing restricted content
                    break; // Exit the loop once a blocked word is found
                } else {
                    // If no blocked words are found, allow the post to be visible
                    $insertPostQuery = "INSERT INTO posts (MoodID, PostedText, UserID, Hidden) VALUES('$mood', '$text', '$userID', '" . POST_VISIBLE . "')";
                }
            }
            
            // Run the query to insert the post into the database
            $runInsertPostQuery = mysqli_query($con, $insertPostQuery);

            // If the post does not contain restricted content, redirect to the main page
            if (empty($postStatus)) { 
                header('Location: user_home.php');
                exit(0); // Stop further script execution after the redirect
            }
        }
    }
}

// Check if the user has submitted a comment
if(isset($_POST['comment'])){

    // Check if the comment area is empty
    if ($_POST['commentarea'] == "") { 
        // If no input is provided, set an error message
        $error['commentarea_empty'] = "コメントを入力してください"; // "Please enter a comment"
    }

    // Proceed if there are no errors
    if (empty($error)) { 
        // Rertieve the post ID and the comment from the post
        $postID = $_POST['PostID'];
        $userComment = $_POST['commentarea'];

        // Insert the comment into the 'comments' table
        $insertCommentQuery = "INSERT INTO comments (Comment, UserID, PostID) VALUES('$userComment', '$userID', '$postID')";

        // Execute the query to insert the comment
        $executeInsertComment = mysqli_query($con, $insertCommentQuery);

        // If the comment was successfully inserted, redirect to the user_home page
        if($executeInsertComment){
            header('Location: user_home.php');
            exit(0); // Stop further script execution after the redirect
        }
    }
}

// Check if the delete-post action has been triggered by the user
if(isset($_POST['delete-post'])){
    // Get the post ID from the form submission
    $postID = $_POST['PostID'];

    // Query to check if the post exists and belongs to the logged-in user
    $queryPost = mysqli_query($con, "SELECT * FROM posts WHERE UserID = '$userID' AND PostID = '$postID'");
    $rowsPost = mysqli_num_rows($queryPost); // Get the number of rows (posts found)

    // If the post exists and belongs to the user
    if($rowsPost > 0){
        // Delete any review requests related to the post
        $queryDeleteRequest = "DELETE FROM requestcheck where PostID = '$postID'";
        mysqli_query($con, $queryDeleteRequest); 

        // Delete any comments associated with the post
        $queryDeleteComments = "DELETE FROM comments where PostID = '$postID'";
        mysqli_query($con, $queryDeleteComments); 
        
        // Delete any likes associated with the post
        $queryDeletePostLikes = "DELETE FROM postlikes where PostID = '$postID'";
        mysqli_query($con, $queryDeletePostLikes); 

        // Delete the post itself
        $queryDeletePost = "DELETE FROM posts where PostID = '$postID'";
        mysqli_query($con, $queryDeletePost); 

        // If comment was successfully deleted, redirect tp the user_home page
        if($queryDeleteComments){
            header('Location: user_home.php');
            exit(0); // Stop further script execution after the redirect
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php includeHeadAssets(); //Include the common head section (meta tags, title, etc.)?> 
    </head>

    <body>
        <!-- Header Section -->
        <header class="sidebar-navigation home-navigation">
            <?php renderUserNavigation(); // Include the common user header ?>
        </header>

        <!-- Logout and sticky header for logged-in user -->
        <?php renderUserHeaderWithLogout($userData); // Include the sticky logout header for users ?>

        <div class="main-wrapper">

            <h2>Home</h2>

            <div class="dashboard-greeting">
                 <!-- Display a welcome message with the user's first name (or さん if non-alphabetical characters are used) -->
                <h3>
                    <span>Welcome back, </span>
                    <span>
                        <?php 
                        if (ctype_alpha($userData['FirstName'])) { 
                            echo htmlspecialchars($userData['FirstName']);
                        } else {
                            echo htmlspecialchars($userData['FirstName'])."さん";
                        } ?>
                    </span>
                </h3>
            </div><!-- .dashboard-greeting -->
            
            <article class="user-dashboard-wrapper">

                <section class="dashboard-left-section">

                    <section class="weekly-overview">
                        <!-- If the user has not posted today -->
                        <?php if($hasUserPostedToday == 0){ ?>

                            <div class="mood-registration-container">
                                <h3>気分はどうですか？</h3>

                                <!-- Form to register the user's mood -->
                                <form method="post" action="./mood/register_mood.php">
                                    <div class="mood-options">
                                        <?php 
                                            // Loop through all available mood options from the 'moods' table
                                            for ($moodIndex = 1; $moodIndex <= $totalAvailableMoods; $moodIndex++) {
                                                // Fetch mood details based on MoodID
                                                $moodQuery = "SELECT * FROM moods where MoodID = $moodIndex";
                                                $moodData = mysqli_query($con,$moodQuery);
                                                $moodData = mysqli_fetch_assoc($moodData);  ?>

                                                <div class="mood-item" id="thismood<?php echo   $moodData['MoodID'];?>">
                                                    <!-- Display mood emoji and mood text -->
                                                    <label for="thischeckmood<?php echo $moodData['MoodName'];?>">
                                                        <p class="mood-emoji" id="thismood-emoji<?php echo $moodData['MoodID'];?>">
                                                            <?php echo $moodData['moodEmoji']; ?>
                                                        </p>
                                                        <p class="mood-text" id="mood-text<?php echo $moodData['MoodID'];?>">
                                                            <span class="japaneseMoodName"><?php echo htmlspecialchars($moodData['JapaneseMoodName']);?></span>
                                                        </p>
                                                    </label>

                                                    <!-- Hidden input to submit mood selection -->
                                                    <input type="submit" name="thismood" id="thischeckmood<?php echo htmlspecialchars($moodData['MoodName']);?>" value="<?php echo $moodData['MoodID'];?>">
                                                </div><!-- .mood-item -->

                                        <?php } ?>
                                    </div><!-- .Moods -->
                                </form>

                            </div><!-- .mood-registration-container -->

                        <?php }else{ ?>

                       <!-- Section to display user progress when they have already posted today -->
                        <div class="user-progress-section">
                            <?php $date = date("Y-m-d"); // Get the current date ?>

                            <div class="weekly-post-status">
                                <?php 
                                // Loop through the past 7 days (including today) to check if the user posted
                                for ($dayIndex = 0; $dayIndex < 7; $dayIndex++){
                                    // If not the first iteration, decrement the date by 1 day
                                    if ($dayIndex > 0) {
                                        $date = date('Y-m-d', strtotime($date .' -1 day'));
                                    }

                                        // Query to check if the user has a post for this specific date
                                    $getPostByDateQuery = "SELECT * FROM dailytracking WHERE UserID = $userID and Date = '$date'";
                                    $postResult = mysqli_query($con, $getPostByDateQuery);
                                    $postCount = mysqli_num_rows($postResult);
                                    
                                    // If the user has a post for this date, retrieve the mood
                                    if($postCount != 0){
                                        $trackingData = mysqli_fetch_assoc($postResult);
                                        $currentTrackingID = $trackingData['TrackingID']; // Get tracking ID for the day

                                        // Query to retrieve the mood name for the day
                                        $moodQuery = mysqli_query($con, "SELECT m.MoodName from trackmoods t LEFT JOIN moods m ON t.MoodID = m.MoodID
                                        WHERE TrackingID = $currentTrackingID");
                                        $moodData = mysqli_fetch_assoc($moodQuery);
                                        $moodForDay = $moodData['MoodName']; // Store the mood name
                                    }?>

                                    <div class="daily-post-status
                                        <?php 
                                        if ($dayIndex == 0) {
                                            if ($postCount == 0) 
                                                echo 'post-not-made';
                                        } else {
                                            if ($postCount == 0) 
                                            echo ' day-without-post';
                                        } ?> 
                                        <?php 
                                        if ($postCount != 0){ 
                                            echo $moodForDay . "-mood"; 
                                        } ?>">

                                        <!-- Display a checkmark if a post exists, otherwise an 'X' -->    
                                        <?php if( $postCount != 0 ){ ?>
                                            <i class="fa-solid fa-check"></i>
                                        <?php } else { ?>
                                            <i class="fa-solid fa-xmark"></i>
                                        <?php } ?>
                                            
                                        <!-- Display the day of the week (Japanese characters for Sun-Sat) -->
                                        <?php $dayofweek = date('w', strtotime($date));?>

                                        <div class="day-of-week">
                                            <?php switch($dayofweek)
                                                {
                                                    case 0:
                                                        print "日"; // Sunday
                                                        break;
                                                    case 1:
                                                        print "月"; // Monday
                                                        break;
                                                    case 2:
                                                        print "火"; // Tuesday
                                                        break;
                                                    case 3:
                                                        print "水"; //Wednesday
                                                        break;
                                                    case 4:
                                                        print "木"; //Thursday
                                                        break;
                                                    case 5:
                                                        print "金"; //Friday
                                                        break;
                                                    case 6:
                                                        print "土"; // Saturday
                                                        break;
                                                    default :
                                                    print "[曜日]エラー発生"; // Error handling
                                                } ?> 
                                        </div><!-- .day-of-week -->  
                                        
                                    </div><!-- .daily-post-status -->
                                <?php } ?>
                            </div><!-- .weekly-post-status --> 
                            
                            <!-- Display the number of consecutive days the user has posted -->
                            <div class="consecutive-days">
                                <?php 
                                    $date = date("Y-m-d"); // Reset date to today

                                    // Query to get posts from the user ordered by date (from newest to oldest)
                                    $datesQuery = "SELECT * FROM dailytracking WHERE UserID = $userID and Date <= '$date' ORDER BY Date desc";
                                    $datesResult = mysqli_query($con, $datesQuery); // Execute the query against the database
                                    $datesCount = mysqli_num_rows($datesResult); // Get the number of rows returned by the query to determine how many tracking records exist
                                
                                    // Initialize variable to count consecutive days
                                    $consecutiveDays = 0;

                                    // Loop through each post retrieved from the database
                                    while ($dateRow = mysqli_fetch_assoc($datesResult)) {
                                        // Get the date of the current post
                                        $daysDatee = $dateRow['Date'];

                                        // Check if the current post date matches the expected consecutive day
                                        if ($date == $daysDatee) {
                                            $consecutiveDays += 1; // Increment the counter for consecutive days of posting
                                        } else {
                                            break; // Exit the loop if there is a gap in posting (non-consecutive days)
                                        }
                                        
                                        // Update the expected date to the previous day for the next iteration
                                        $date = date('Y-m-d', strtotime($date .' -1 day'));
                                    } 
                                ?>
                                <p><span><?php echo $consecutiveDays; ?>日</span><span>継続日数</span></p>
                            </div><!-- .consecutive-days -->
                        </div><!-- .user-progress-section -->
            
                        <!-- Display the user's longest streak of consecutive days posting -->
                        <div class= "max-consecutive-days">
                            <?php 
                                $date = date("Y-m-d"); // Reset date to today

                                // Query to get posts from the user ordered by date (from newest to oldest)
                                $datesQuery = "SELECT * FROM dailytracking WHERE UserID = $userID and Date <= '$date' ORDER BY Date desc";
                                $datesResult = mysqli_query($con, $datesQuery); // Execute the query against the database
                                $datesCount = mysqli_num_rows($datesResult); // Get the number of rows returned by the query to determine how many tracking records exist

                                // Initialize variables to track the maximum streak
                                $longestStreak = 0;
                                $currentStrea = 0;

                                // Loop through each post retrieved from the database
                                while ($dateRow = mysqli_fetch_assoc($datesResult)) {
                                    // Get the date of the current post
                                    $daysDatee = $dateRow['Date'];  
                                    
                                    // Check if the current post date matches the previous one (continuing streak)
                                    if ($date == $daysDatee) {
                                        $currentStrea += 1; // Increment the current streak

                                         // If the current streak is the longest so far, update the longest streak
                                        if ($currentStrea > $longestStreak) {
                                            $longestStreak = $currentStrea; 
                                        }
                                    } else {   
                                        // If there is a gap between the current post date and the previous one, reset the streak                  
                                        $date = $daysDatee; // Reset the date to the current post's date
                                        $currentStrea = 1; // Start a new streak with this post
                                    }
                                    
                                    // Move to the next day in the iteration (one day before the current post date)
                                    $date = date('Y-m-d', strtotime($date .' -1 day'));
                                } 
                            ?>
                            <p>最長継続日数：<?php echo $longestStreak; ?>日</p>
                        </div><!-- .max-consecutive-days -->
                        <?php } ?>
                    </section><!-- .weekly-overview -->

                    <section class="today-goals-section">
                    <?php if ($userGoalsCount > 0) {  // Check if the user has any goals for today ?>
                        <div class="goals-header">
                            <div class="goals-title">
                                <!-- Display today's goals title -->
                                <h3>今日の目標</h3> 
                            </div><!-- .title -->
                            <div class="link">
                                <!-- Link to view all goals -->
                                <a href="./goals/display_user_goals.php"><p>See All <i class="fa-solid fa-angles-right"></i></p></a>
                            </div><!-- .link -->
                        </div><!-- .goals-header -->
                        
                        <section class="user-goals-list-wrapper">
                            <div class="goal-card">
                                <?php while($goalRow = mysqli_fetch_array($userGoalsQuery)){
                                    
                                    $date = date("Y-m-d"); // Get today's date

                                    $goalID = $goalRow['GoalID']; // ID of the goal
                                    $userGoalID = $goalRow['UserGoalID']; // User-specific goal ID

                                    // Fetch data for the current goal
                                    $queryGetGoalData = mysqli_query($con, "SELECT * FROM goals where GoalID = $goalID");
                                    $goalData = mysqli_fetch_array($queryGetGoalData);

                                    $goalCategoryID = $goalData['GoalCategoriesID']; // ID of the goal category                          
                                    $goalIcon = $goalData['GoalIcon']; // Icon associated with the goal
                                    $goalName = $goalData['GoalName']; // Name of the goal

                                    // Fetch data for the goal category
                                    $queryGetGoalcategories = mysqli_query($con, "SELECT * FROM goalcategories where GoalCategoriesID = $goalCategoryID");
                                    $goalCategoryData = mysqli_fetch_array($queryGetGoalcategories);

                                    // Fetch updates for today's goals
                                    $queryTodayUpdates = mysqli_query($con, "SELECT * FROM trackgoals where UserGoalID = $userGoalID and Date = '$date'");
                                    $rowTodayUpdates = mysqli_num_rows($queryTodayUpdates); // Count of today's updates

                                    // Fetch all tracking dates for the goal
                                    $datesQuery = "SELECT t.UserGoalID, t.Date, u.GoalID, u.UserID 
                                        FROM usergoals u INNER JOIN trackgoals t
                                        ON u.UserGoalID = t.UserGoalID
                                        where GoalID = $goalID and UserID = $userID and Date <= '$date' ORDER BY Date desc";
                                    $datesResult = mysqli_query($con, $datesQuery);
                                    $datesCount = mysqli_num_rows($datesResult); // Count of total tracked dates
                                
                                    // Initialize the consecutive days counter
                                    $consecutiveDays = 0;
                                
                                    // Loop through the result set of tracked goal dates
                                    while ($dateRow = mysqli_fetch_assoc($datesResult)){
                                        $daysDatee = $dateRow['Date']; // Get the tracked date from the result set

                                        // Check if the tracked date matches the current date
                                        if($date == $daysDatee){
                                            $consecutiveDays += 1; // Increment the consecutive days counter
                                        } else {
                                            break; // Exit the loop if there's a gap in the tracked dates
                                        }

                                        // Move to the previous day for the next iteration
                                        $date = date('Y-m-d', strtotime($date .' -1 day'));
                                } ?>

                                    <div class="goal-card-content">
                                        <!-- Display the goal category -->
                                        <h4><?php echo htmlspecialchars($goalCategoryData['GoalCategoryName']); ?> </h4> 
                                        
                                        <!-- Display the goal name -->
                                        <h3><?php echo htmlspecialchars($goalName); ?></h3> 

                                        <!-- Display the goal icon -->
                                        <div class="goal-icon">
                                            <?php echo $goalIcon; ?>
                                        </div> 

                                        <!-- Display consecutive days -->
                                        <div class="consecutive-days-counter">
                                            <p><?php echo $consecutiveDays; ?>日の継続</p>
                                        </div> 
                                        
                                        <div class="goal-status-circle<?php if(!empty($rowTodayUpdates)){ echo "-completed";};?>">
                                            <i class="fa-solid fa-check"></i> <!-- Check icon if there are today's updates -->
                                        </div><!-- .goal-status-circle -->
                                    </div><!-- .goal-card-content -->

                                <?php } ?>
                            </div><!-- .goal-card -->

                        <?php } else { // If no goals have been set ?>

                                <div class="goals-header">
                                    <div class="title">
                                        <h3><span class="japanese"></span>今日の目標</h3>  <!-- Display today's goals title -->
                                    </div><!-- .title -->
                                </div><!-- .goals-header -->

                                <div class="no-goals-message">
                                    <h5>目標はまだ設定していない</h5> <!-- Message for no goals set -->
                                    <p>目標を設定し、達成に向けて努力しながら進捗状況を追跡しよう</p> <!-- Encouragement to set goals -->
                                    <a href="./goals/set_goals.php">Set Goal</a> <!-- Link to set goals -->
                                </div><!-- .goalsBeenSet -->

                            <?php } ?>
                        </section><!-- .user-goals-list-wrapper -->
                    </section><!-- .today-goals-section -->
                </section><!-- .dashboard-left-section -->

                <section class="dashboard-right-section">
                    <div class="feed-container">
                        <div class="post-section">
                            <h3>みんなのトーク</h3> <!-- Display the title for the posts section -->

                            <div class="post-actions">
                                <!-- Form to submit a new post -->
                                <form action="user_home.php" method="post">
                                    <div class="mood-selector">
                                        <?php 
                                            // Loop through all available moods and display them as radio buttons
                                            for($moodIndex = 1; $moodIndex <= $totalAvailableMoods; $moodIndex++) {
                                                $moodQuery = "SELECT * FROM moods where MoodID = $moodIndex"; // Query to get mood details
                                                $moodData = mysqli_query($con,$moodQuery); // Execute the query
                                                $currentMood = mysqli_fetch_assoc($moodData); // Fetch the mood data ?>

                                                <div class="mood-item" id="mood<?php echo $currentMood['MoodID'];?>">
                                                    <label for="checkmood<?php echo $currentMood['MoodName'];?>">
                                                        <p class="mood-emoji" id="mood-emoji<?php echo $currentMood['MoodID'];?>"><?php echo $currentMood['moodEmoji']; ?></p>
                                                    </label>
                                                    <!-- Radio button for selecting mood -->
                                                    <input type="radio" name="mood" id="checkmood<?php echo htmlspecialchars($currentMood['MoodName']);?>" value="<?php echo $currentMood['MoodID'];?>" required>
                                                </div><!-- .mood-item -->
                                            <?php } 
                                        ?>
                                    </div><!-- .mood-selector -->

                                    <!-- Wrapper for the post input section -->
                                    <div class="post-input-wrapper">
                                        <!-- Textarea for user input -->
                                        <textarea id="textarea" name="textarea" placeholder="今どんな気持ち？" required></textarea>
                                    </div><!-- .post-input-wrapper -->

                                    <!-- Wrapper for the submission button -->
                                    <div class="post-submit-wrapper">
                                        <!-- Submit button for the post -->
                                        <input type="submit" value="Post" name="post" class="button"/>
                                    </div><!-- .post-submit-wrapper -->

                                </form>
                            </div><!-- .post-actions -->
                        </div><!-- .post-section -->
                      
                        <!-- Container for the error message that will display if the post contains blocked words -->
                        <div class="post-error-message 
                            <?php 
                            if ($postStatus == "display-error") {
                                echo $postStatus;
                            }?>
                        ">
                            <div class="error-message-content">
                                <div class="error-message-title">
                                    <h4>投稿が非表示</h4> <!-- Title indicating the post is hidden -->
                                </div><!-- .error-message-title -->

                                <!-- Message explaining that the post violates guidelines and providing a link for re-evaluation -->
                                <div class="error-message-details">
                                    <p>投稿がガイドラインに違反しているので、別のユーザーに表示されていません。誤りがあると思われる場合、<a href="./user_profile/my_page.php">マイページ</a>で再評価を依頼することができます。</p>
                                </div><!-- .error-message-details -->

                            </div><!-- .error-message-content -->
                        </div><!-- .post-error-message -->

                        <div class="posts-feed">
                            <?php if ($postsCount > 0) { // Check if there are posts to display
                                while($postDetails = mysqli_fetch_assoc($postsWithDetailsQuery)) { 
                                    // Retrieve post details from the current row
                                    $username = $postDetails['Username'];
                                    $profileImg = $postDetails['profileImg'];
                                    $postID = $postDetails['PostID'];
                                    $emoji = $postDetails['emoji'];
                                    $moodName = $postDetails['MoodName'];
                                    $text = $postDetails['PostedText'];
                                    $userPosted = $postDetails['UserID'];
                                    $postedDate = $postDetails['Date'];
                                    $totalLikes = $postDetails['likes'];
                                    $totalComments = $postDetails['comments']; ?>

                                    <!-- Wrapper for individual user post -->
                                    <div class="post-item <?php echo htmlspecialchars($moodName); ?>"> 
                                        <div class="post-header">
                                        <?php 
                                            // Query to check if the current user is the post owner
                                            $postQuery = mysqli_query($con, "SELECT * FROM posts WHERE UserID = '$userID' AND PostID = '$postID'");
                                            $rowsPostGet = mysqli_num_rows($postQuery);
                                            
                                            // Determine visibility of edit and delete buttons
                                            if ($rowsPostGet > 0) { 
                                                $display = ""; // Show buttons if the user is the post owner
                                            } else { 
                                                $display = "hidden-button"; // Hide buttons if not
                                            } ?>
                                        
                                            <!-- Section for editing the post -->
                                            <div class="edit-button-wrapper <?php echo $display;?>"> 
                                                <form action="./user_profile/post_edit.php" method="POST">
                                                    <!-- Submit button to edit the post -->
                                                    <button type="submit" name="edit-post" value="<?=$postID;?>"><i class="fa-solid fa-pen-to-square"></i></button>
                                                </form>
                                            </div><!-- .edit-button-wrapper -->

                                            <!-- Section for deleting the post -->
                                            <div class="delete-button-wrapper <?php echo $display;?>"> 
                                                <!-- Button to open the delete confirmation modal -->
                                                <button id="delete-post-button-<?php echo $postID; ?>" class="teritary-btn delete-post-button" data-postid="<?php echo $postID; ?>"><i class="fa-solid fa-trash"></i></button>
                                            </div><!-- .delete-button-wrapper -->

                                            <!-- Modal dialog for delete confirmation -->
                                            <div id="deleteConfirmationModal-<?php echo $postID; ?>" class="modal">
                                                <div class="modal-content">
                                                    <p class="japanese-prompt">本当に削除してよろしいですか?</p><!-- Confirmation prompt in Japanese asking if the user really wants to delete -->
                                                    <p class="english-prompt">Are you absolutely sure you want to delete this item? </p>
                                                    <!-- Main container for modal buttons -->
                                                    <div class="modal-main">
                                                        <!-- Button to close the modal without deleting -->
                                                        <div class="cancel-button"> 
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
                                        
                                            <!-- Section displaying the user who posted the content -->
                                            <div class="post-author">
                                                <!-- User's profile image -->
                                                <div class="author-profile-img">
                                                    <img src="../assets/user-img/<?php echo $profileImg ?>" alt="Author's Profile Imagemg">
                                                </div>

                                                <!-- User information display -->
                                                <div class="author-info">
                                                    <!-- Displaying the username -->
                                                    <p class="author-username"><?php echo "@" . htmlspecialchars($username); ?></p> 

                                                    <p class="post-time-elapsed"><?php
                                                        // Calculate the time difference between the post date and the current date/time
                                                        $currentTimestamp = strtotime($dateTime); // Current date/time in Unix timestamp
                                                        $postedTimestamp = strtotime($postedDate); // Posted date in Unix timestamp
                                                        $timeDifferenceMinutes = round(abs($currentTimestamp - $postedTimestamp) / MINUTES_IN_HOUR,2); // Time difference in minutes

                                                        // Determine the appropriate time unit to display based on the difference in minutes
                                                        if ($timeDifferenceMinutes >= MINUTES_IN_HOUR) {
                                                            // If the difference is 60 minutes or more, convert the time difference from minutes to hours
                                                            $timeDifferenceHours = round($timeDifferenceMinutes / MINUTES_IN_HOUR); 

                                                            if ($timeDifferenceHours >= HOURS_IN_DAY) {
                                                                // If the difference is 24 hours or more, convert the time difference from hours to days
                                                                $timeDifferenceDays = round($timeDifferenceHours / HOURS_IN_DAY);

                                                                if($timeDifferenceDays >= DAYS_IN_YEAR) {
                                                                    // If the difference is 365 days or more, convert the time difference from days to years
                                                                    $timeDifferenceYears = round($timeDifferenceDays / DAYS_IN_YEAR); // Convert to years
                                                                    echo $timeDifferenceYears . "年"; // Display the difference in years
                                                                } else {
                                                                    echo $timeDifferenceDays . "日"; // Display the difference in days
                                                                }
                                                            }else{
                                                                echo $timeDifferenceHours . "時間"; ; // Display the difference in hours
                                                            }
                                                        }else{
                                                            // If the difference is less than 60 minutes, display the difference in minutes
                                                            echo round($timeDifferenceMinutes) ."分"; // Display the difference in minutes
                                                        } ?>
                                                    </p>
                                                </div><!-- .author-info -->
                                            </div><!-- .post-author -->

                                            <!-- Section displaying the emoji associated with the post -->
                                            <div class="post-emoji">
                                                <?php echo $emoji; ?>
                                            </div><!-- .post-emoji -->

                                        </div><!-- .post-header -->
                                    
                                        <div class="post-content-wrapper">
                                            <div class="post-text-content">
                                                <?php echo htmlspecialchars($text); ?>
                                            </div>
                                        </div><!-- ."post-content-wrapper -->
        
                                        <?php
                                            // Query to check if the user has already liked the post
                                            $queryCheckLiked = "SELECT * FROM postlikes WHERE UserID = $userID and PostID = '$postID'";
                                            $resultCheckLiked = mysqli_query($con, $queryCheckLiked);

                                            // Count the number of rows to see if the post has been liked by the user
                                            $hasUserLikedPost = mysqli_num_rows($resultCheckLiked); 
                                        ?>

                                        <div class="post-interactions">
                                            <div class="post-likes">
                                                <!-- Check if the user has liked the post, display a filled heart icon if true, otherwise an empty heart -->
                                                <a href="../server-side/user/toggle_post_like.php?=article&id=<?php echo $postID; ?>">
                                                    <?php if($hasUserLikedPost > 0){ echo '<i class="fa-solid fa-heart"></i>';
                                                    } else { 
                                                        echo '<i class="fa-regular fa-heart"></i>';
                                                    } ?>
                                                </a>

                                                <!-- Display the total number of likes for the post -->
                                                <p><?php echo $totalLikes; ?> </p>
                                            </div><!-- .post-likes -->

                                            <div class="post-comments">
                                                <!-- Display a comment icon and the total number of comments -->
                                                <i class="fa-regular fa-comment"></i>
                                                <p><?php echo htmlspecialchars($totalComments); ?></p>
                                            </div><!-- .post-comments -->
                                        </div><!-- .post-interactions -->

                                        <!-- Comment form for adding a new comment -->
                                        <form action="" method="post">
                                            <!-- Hidden input to store the PostID for the comment -->
                                            <input type="hidden" name="PostID" value="<?= $postID; ?>">

                                            <div class="comment-section">
                                                <div class="comment-main">
                                                    <div class="comment-author">
                                                        <!-- Display the user's profile image -->
                                                        <div class="author-profile-img">
                                                            <img src="../assets/user-img/<?php echo $userProfileImage ?>" alt="profile img">
                                                        </div>
                                                    </div><!-- .comment-author -->

                                                    <div class="comment-input">
                                                        <!-- Textarea for the user to write their comment -->
                                                        <textarea id="commentarea" name="commentarea" placeholder="コメントを書く" required></textarea>
                                                    </div><!-- .comment-input -->

                                                    <div class="comment-submit">
                                                        <!-- Submit button for posting the comment -->
                                                        <input type="submit" value="" name="comment" class="button"/>
                                                        <i class="fa-solid fa-chevron-right"></i>
                                                    </div><!-- .comment-submit -->
                                                </div><!-- .comment-main -->
                                            </div><!-- .comment-section -->
                                        </form>

                                        <div class="comments-display comments-hidden">
                                            <!-- Display the "Comments" heading with an arrow if there are any comments -->
                                            <?php if($totalComments > 0) {
                                                echo '<p class="arrow">Comments<i class="fa-solid fa-angle-down"></i></p>';
                                            } ?>
                                                
                                            <div class="comments-list">
                                                <div class="comments-container">
                                                    <?php 
                                                    // Query to retrieve comments along with user information for the current post
                                                    $queryComments = mysqli_query($con, "
                                                        select c.CommentID, c.Comment, c.Date, u.Username, u.profileImg 
                                                        from comments c
                                                        LEFT JOIN users u ON c.UserID = u.UserID 
                                                        where PostID = '$postID' 
                                                        ORDER BY Date DESC; "
                                                    );

                                                    // Get the number of rows (comments) returned by the query
                                                    $totalFetchedComments = mysqli_num_rows($queryComments); 
                                                    
                                                    // If there are comments, loop through and display them
                                                    if ($totalFetchedComments > 0) {
                                                        while ($commentData = mysqli_fetch_assoc($queryComments)) {
                                                            // Extract relevant data for each comment
                                                            $commentID = $commentData['CommentID'];
                                                            $commentText = $commentData['Comment'];
                                                            $commenterUsername = $commentData['Username'];
                                                            $commenterProfileImg = $commentData['profileImg'];
                                                            $commentTimestamp = $commentData['Date']; ?>

                                                            <div class="comment-item">
                                                                <div class="comment-author">  
                                                                    <!-- Display the user's profile image who posted the comment -->
                                                                    <div class="author-profile-img">
                                                                        <img src="../assets/user-img/<?php echo $commenterProfileImg ?>" alt="profile img">
                                                                    </div>

                                                                    <div class="author-info">
                                                                        <!-- Display the username of the commenter -->
                                                                        <p class="author-username"><?php echo "@" . htmlspecialchars($commenterUsername); ?></p>

                                                                        <!-- Display the time difference between the comment time and the current time -->
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
                                                                        </p>
                                                                    </div><!-- .author-info -->
                                                                </div><!-- .comment-author -->

                                                                <!-- Display the comment text -->
                                                                <div class="comment-content-wrapper">
                                                                    <div class="comment-text-content"><?php echo htmlspecialchars($commentText); ?></div>
                                                                </div><!-- ."post-content-wrapper -->

                                                            </div><!-- .comment-item -->

                                                        <?php } //End of while loop
                                                    } //End of if loop ?>

                                                </div><!-- .comments-container -->

                                            </div><!-- .comments-list -->
                                        
                                        </div><!-- .displaycomments -->

                                    </div><!-- .post-item -->

                                <?php } // End of while loop
                            } // End of if loop ?>
                        </div><!-- .posts-feed -->

                    </div><!-- .feed-container -->
                </section><!-- .dashboard-right-section -->

            </article><!-- .user-dashboard-wrapper -->

        </div><!-- .main-wrapper -->

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
            document.querySelectorAll('.cancel-button').forEach(button => {
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


            /**
             * Adds a click event listener to all elements with the class 'comments-display',
             * which toggles the visibility of the comment section by adding/removing a class.
             */
            const elements = document.getElementsByClassName("comments-display");

            // Convert HTMLCollection to array and add click event listeners to toggle comment visibility
            Array.from(elements).forEach(element => {
                element.addEventListener('click', function() {
                    this.classList.toggle('comments-hidden'); // Toggle 'comments-hidden' class on click
                });
            });
            

            /**
             * Confirm delete action
             * Displays a confirmation dialog to the user and redirects to the specified page if confirmed.
             */ 
            function checkDelete() {
                // Display a confirmation dialog to the user
                const isConfirmed = confirm("本当に削除しますか?");

                // If the user confirms, redirect to user_home.php
                if (isConfirmed) {
                    window.location.href = 'user_home.php'; 
                }

                // Return the result of the confirmation dialog (true if confirmed, false otherwise)
                return isConfirmed;
            }

            // Function to update mood emojis
            function updateMoodEmojis(prefix, selectedIndex) {

                // Arrays of classes for regular and selected emoji icons
                const emojis = [
                    'fa-regular fa-face-laugh-beam',
                    'fa-regular fa-face-smile-beam',
                    'fa-regular fa-face-meh',
                    'fa-regular fa-face-frown',
                    'fa-regular fa-face-tired'
                ];

                const selectedEmojis = [
                    'fa-solid fa-face-laugh-beam',
                    'fa-solid fa-face-smile-beam',
                    'fa-solid fa-face-meh',
                    'fa-solid fa-face-frown',
                    'fa-solid fa-face-tired'
                ];

                // Loop through the emoji elements and update the icon based on the selected index
                for (let i = 1; i <= emojis.length; i++) {
                    document.getElementById(`${prefix}-emoji${i}`).innerHTML = `<i class="${i === selectedIndex ? selectedEmojis[i - 1] : emojis[i - 1]}"></i>`;
                }
            }

            // Function to assign event listeners to mood buttons
            function assignMoodListeners(prefix) {
                // Loop through the mood buttons and assign a click event listener to each
                for (let i = 1; i <= 5; i++) {
                    document.getElementById(`${prefix}${i}`).addEventListener("click", () => {
                        updateMoodEmojis(prefix, i); // Update emojis when a button is clicked
                    });
                }
            }

            // Assign event listeners to both 'mood' and 'thismood' buttons
            assignMoodListeners("mood");
            assignMoodListeners("thismood");

   

        </script>
    </body>

</html>

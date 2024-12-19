<?php
/**
 * Page Name: admin_data_cleanup.php
 * Author: Moa Burke
 * Date: 2024-10-30
 * Description: This script handles the deletion of various types of data in the content management system, including:
 *      - Food items and their associated tracking records
 *      - Weather records and their associated tracking data
 *      - Goals, their tracked activities, and user-specific goal records
 *      - Goal categories, with validation to prevent deletion if associated goals exist
 *      - Blocked words from the prohibited words list
 * 
 *      Each section checks if a delete request has been submitted for a specific item type (e.g., food, weather, goal, etc.),
 *      retrieves the corresponding ID, and then deletes associated tracking data before removing the main record. 
 *      If deletion is successful, a success message is stored in a session variable, otherwise, a failure message is set.
 *      Finally, the script redirects the user to the appropriate management page.
 * 
*/

session_start(); // Start the session

// Define a constant for the base path
define('BASE_PATH', '../../server-side/shared/');

include(BASE_PATH . "connections.php"); // Include databade connection file
include(BASE_PATH . "check_login.php"); // Include login check script to verify user authentication before accessing this page.
include(BASE_PATH . "timezone.php"); // Include the timezone configuration
include(BASE_PATH . "header/admin_layout.php"); // Include the admin header layout file

/*************** Delete Admin/User ***************/    
// Check if the form for deleting a user has been submitted
if (isset($_POST['user-delete'])) {
    // Get the user ID from the POST reques
    $userID = $_POST['user-delete'];

    // Delete comments associated with the user
    $queryDeleteComments = "DELETE FROM comments WHERE UserID = $userID";
    mysqli_query($con, $queryDeleteComments);

    // Delete post likes associated with the user
    $queryDeleteLikes = "DELETE FROM postlikes WHERE UserID = $userID";
    mysqli_query($con, $queryDeleteLikes);

    // Retrieve posts created by the user
    $queryGetPosts = mysqli_query($con, "SELECT * FROM posts where UserID = $userID");
    // Loop through each post to delete associated likes and comments
    while ($postData= mysqli_fetch_assoc($queryGetPosts)) {
        // Get the post ID
        $postID = $postData['PostID'];

        // Check if there are any requests associated with the post and delete them
        $queryDeletePostRequests = "DELETE FROM requestcheck WHERE PostID = '$postID'";
        mysqli_query($con, $queryDeletePostRequests);

        // Delete likes associated with the post
        $queryDeletePostLikes = "DELETE FROM postlikes WHERE PostID = $postID";
        mysqli_query($con, $queryDeletePostLikes);

        // Delete comments associated with the post
        $queryDeletePostComments = "DELETE FROM comments WHERE PostID = $postID";
        mysqli_query($con, $queryDeletePostComments);
    }

    // Delete all posts created by the user
    $queryDeletePosts = "DELETE FROM posts WHERE UserID = $userID";
    mysqli_query($con, $queryDeletePosts);

    // Retrieve user goals associated with the user
    $queryGetUserGoals = mysqli_query($con, "SELECT * FROM usergoals where UserID = $userID");
    // Loop through each goal to delete associated tracking records
    while ($userGoalData = mysqli_fetch_assoc($queryGetUserGoals)) {
        // Get the user goal ID
        $userGoalID = $userGoalData['UserGoalID'];
        
        // Delete tracking records associated with the user goal
        $queryDeleteGoalTracking = "DELETE FROM trackgoals WHERE UserGoalID = '$userGoalID'";
        mysqli_query($con, $queryDeleteGoalTracking);
    }

    // Delete the user's goals
    $queryDeleteUserGoals = "DELETE FROM usergoals WHERE UserID = '$userID'";
    mysqli_query($con, $queryDeleteUserGoals);
    
    // Retrieve daily tracking records associated with the user
    $queryGetDailyTracking = mysqli_query($con, "SELECT * FROM dailytracking where UserID = $userID");
    // Loop through each tracking record to delete associated activities and data
    while ($trackingData = mysqli_fetch_assoc($queryGetDailyTracking)){
        $trackingID = $trackingData['TrackingID'];

        // Delete all associated tracking activities
        $queryDeleteActivities = "DELETE FROM trackactivities WHERE TrackingID = '$trackingID'";
        mysqli_query($con, $queryDeleteActivities);

        // Delete associated company data
        $queryDeleteCompany = "DELETE FROM trackcompany WHERE TrackingID = '$trackingID'";
        mysqli_query($con, $queryDeleteCompany);

        // Delete associated feelings data
        $queryDeleteFeelings = "DELETE FROM trackfeelings WHERE TrackingID = '$trackingID'";
        mysqli_query($con, $queryDeleteFeelings);

        // Delete associated food tracking data
        $queryDeleteFood = "DELETE FROM trackfoods WHERE TrackingID = '$trackingID'";
        mysqli_query($con, $queryDeleteFood);

        // Delete associated locations data
        $queryDeleteLocations = "DELETE FROM tracklocations WHERE TrackingID = '$trackingID'";
        mysqli_query($con, $queryDeleteLocations);

        // Delete associated moods data
        $queryDeleteMoods = "DELETE FROM trackmoods WHERE TrackingID = '$trackingID'";
        mysqli_query($con, $queryDeleteMoods);

        // Delete associated sleep time data
        $queryDeleteSleepTime = "DELETE FROM tracksleeptime WHERE TrackingID = '$trackingID'";
        mysqli_query($con, $queryDeleteSleepTime );

        // Delete associated weather data
        $queryDeleteWeather = "DELETE FROM trackweather WHERE TrackingID = '$trackingID'";
        mysqli_query($con, $queryDeleteWeather);

        // Delete associated memo data
        $queryDeleteMemo = "DELETE FROM memos WHERE TrackingID = '$trackingID'";
        mysqli_query($con, $queryDeleteMemo);
    }
    
    // Delete the daily tracking records for the user
    $queryDeleteDailyTracking = "DELETE FROM dailytracking WHERE UserID = '$userID'";
    mysqli_query($con, $queryDeleteDailyTracking);
    
    // Finally, delete the user data
    $queryDeleteUserData = "DELETE FROM userdata WHERE UserID = '$userID'";
    mysqli_query($con, $queryDeleteUserData);
    
    // Delete the user record from the users table
    $queryDeleteUser = "DELETE FROM users WHERE UserID = '$userID'";
    $deleteUserResult = mysqli_query($con, $queryDeleteUser);

    // Check if the user deletion was successful
    if ($deleteUserResult) {
        // Set a success message if the deletion was successful
        $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>正常に削除されました。</p></div>"; // Translation: "Successfully deleted."
    } else {
        // Set a failure message if the deletion failed
        $_SESSION['feedbackMessage'] = "<div class='message-text fail-alert'><p>何か問題が発生しました。</p></div>";  // Translation: "Something went wrong.
    }

    // Redirect to the user management page
    header('Location: ../../admin/user_management/manage_users.php');
    exit(0);
}

/*************** Delete Feeling ***************/
// Check if the form to delete a feeling has been submitted    
if (isset($_POST['feeling-delete'])) {
    // Get the Feeling ID from the POST request
    $feelingIDToDelete = $_POST['feeling-delete'];

    // Prepare the SQL query to delete tracking records associated with the feeling
    $queryDeleteTracking = "DELETE FROM trackfeelings WHERE FeelingID = '$feelingIDToDelete' ";
    // Execute the query to delete tracking records
    mysqli_query($con, $queryDeleteTracking);

    // Prepare the SQL query to delete the feeling from the feelings table
    $queryDeleteFeeling = "DELETE FROM feelings WHERE FeelingID = '$feelingIDToDelete' ";
    // Execute the query to delete the feeling
    $queryRunFeeling = mysqli_query($con, $queryDeleteFeeling);

    // Check if the deletion of the feeling was successful
    if ($queryRunFeeling) {
        // Set a success message if the deletion was successful
        $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>気持ちが正常に削除されました。</p></div>"; // "Feeling deleted successfully."
    } else {
        // Set a failure message if the deletion failed
        $_SESSION['feedbackMessage'] = "<div class='message-text fail-alert'><p>何か問題が発生しました。</p></div>"; // "Something went wrong."
    }

    // Redirect to the content management page
    header('Location: ../../admin/content_management/manage_content.php');
    exit(0);
}

/*************** Delete Activity ***************/    
// Check if the form to delete an activity has been submitted
if (isset($_POST['activity-delete'])) {
    // Retrieve the Activity ID from the POST request
    $activityID = $_POST['activity-delete'];

    // Prepare the SQL query to delete tracking records associated with the activity
    $queryDeleteTracking = "DELETE FROM trackactivities WHERE ActivityID = '$activityID' ";
    // Execute the query to delete tracking records
    mysqli_query($con, $queryDeleteTracking);

    // Prepare the SQL query to delete the activity from the activities table
    $queryDeleteActivity = "DELETE FROM activities WHERE ActivityID = '$activityID' ";
    // Execute the query to delete the activity
    $queryRunActivity = mysqli_query($con, $queryDeleteActivity);

    // Check if the deletion of the activity was successful
    if ($queryRunActivity) {
        // Set a success message if the deletion was successful
        $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>アクティビティが正常に削除されました。</p></div>"; // "Activity deleted successfully."
    } else {
        // Set a failure message if the deletion failed
        $_SESSION['feedbackMessage'] = "<div class='message-text fail-alert'><p>何か問題が発生しました。</p></div>"; // "Something went wrong."
 
    }

    // Redirect to the content management page
    header('Location: ../../admin/content_management/manage_content.php');
    exit(0);
}

/*************** Delete Company ***************/
// Check if the form to delete a company has been submitted
if (isset($_POST['company-delete'])) {
    // Retrieve the Company ID from the POST request
    $companyID = $_POST['company-delete'];

    // Prepare the SQL query to delete tracking records associated with the company
    $queryDeleteTracking = "DELETE FROM trackcompany WHERE CompanyID = '$companyID' ";
    // Execute the query to delete tracking records
    mysqli_query($con, $queryDeleteTracking);

    // Prepare the SQL query to delete the company from the company table
    $queryDeleteCompany = "DELETE FROM company WHERE CompanyID = '$companyID' ";
    // Execute the query to delete the company
    $queryRunCompany = mysqli_query($con, $queryDeleteCompany);

    // Check if the deletion of the company was successful
    if ($queryRunCompany) {
        // Set a success message if the deletion was successful
        $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>人々が正常に削除されました。</p></div>"; // "Company deleted successfully."
    } else {
       // Set a failure message if the deletion failed
       $_SESSION['feedbackMessage'] = "<div class='message-text fail-alert'><p>何か問題が発生しました。</p></div>"; // "Something went wrong."
    }

    // Redirect to the content management page
    header('Location: ../../admin/content_management/manage_content.php');
    exit(0);
}

/*************** Delete Location ***************/     
// Check if the form to delete a location has been submitted
if (isset($_POST['location-delete'])) {
    // Retrieve the Location ID from the POST request
    $locationID = $_POST['location-delete'];

    // Prepare the SQL query to delete tracking records associated with the location
    $queryDeleteTracking = "DELETE FROM tracklocations WHERE LocationID = '$locationID' ";
    // Execute the query to delete tracking records
    mysqli_query($con, $queryDeleteTracking);

    // Prepare the SQL query to delete the location from the locations table
    $queryDeleteLocation = "DELETE FROM locations WHERE LocationID = '$locationID' ";
    // Execute the query to delete the location
    $queryRunLocation = mysqli_query($con, $queryDeleteLocation);

    // Check if the deletion of the location was successful
    if ($queryRunLocation) {
        // Set a success message if the deletion was successful
        $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>場所が正常に削除されました。</p></div>"; // "Location deleted successfully."
    } else {
        // Set a failure message if the deletion failed
        $_SESSION['feedbackMessage'] = "<div class='message-text fail-alert'><p>何か問題が発生しました。</p></div>"; // "Something went wrong."
    }

    // Redirect to the content management page
    header('Location: ../../admin/content_management/manage_content.php');
    exit(0);
}

/*************** Delete Food ***************/
// Check if the form to delete a food item has been submitted
if (isset($_POST['food-delete'])) {
    // Retrieve the Food ID from the POST request
    $foodID = $_POST['food-delete'];

    // Prepare the SQL query to delete tracking records associated with the food
    $queryDeleteTracking = "DELETE FROM trackfoods WHERE FoodID = '$foodID' ";
    // Execute the query to delete tracking records
    mysqli_query($con, $queryDeleteTracking);

    // Prepare the SQL query to delete the food item from the foods table
    $queryDeleteFood = "DELETE FROM foods WHERE FoodID = '$foodID' ";
    // Execute the query to delete the food
    $queryRunFood = mysqli_query($con, $queryDeleteFood);

    // Check if the deletion of the food item was successful
    if ($queryRunFood) {
        // Set a success message if the deletion was successful
        $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>食事が正常に削除されました。</p></div>"; // "Food deleted successfully."
    } else {
        // Set a failure message if the deletion failed
        $_SESSION['feedbackMessage'] = "<div class='message-text fail-alert'><p>何か問題が発生しました。</p></div>"; // "Something went wrong."
    }

    // Redirect to the content management page
    header('Location: ../../admin/content_management/manage_content.php');
    exit(0);
}


/*************** Delete Weather ***************/
// Check if the form to delete a weather record has been submitted
if (isset($_POST['weather-delete'])) {
    // Retrieve the Weather ID from the POST request
    $weatherID = $_POST['weather-delete'];

    // Prepare the SQL query to delete tracking records associated with the weather
    $queryDeleteTracking = "DELETE FROM trackweather WHERE WeatherID = '$weatherID' ";
    // Execute the query to delete tracking records
    mysqli_query($con, $queryDeleteTracking);

    // Prepare the SQL query to delete the weather record from the weather table
    $queryDeleteWeather = "DELETE FROM weather WHERE WeatherID = '$weatherID' ";
    // Execute the query to delete the weather
    $queryRunWeather = mysqli_query($con, $queryDeleteWeather);

    // Check if the deletion of the weather record was successful
    if ($queryRunWeather) {
        // Set a success message if the deletion was successful
        $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>天候が正常に削除されました。</p></div>"; // "Weather deleted successfully."
    } else {
        // Set a failure message if the deletion failed
        $_SESSION['feedbackMessage'] = "<div class='message-text fail-alert'><p>何か問題が発生しました。</p></div>"; // "Something went wrong."
    }

    // Redirect to the content management page
    header('Location: ../../admin/content_management/manage_content.php');
    exit(0);
}


/*************** Delete Goal ***************/
// Check if the form to delete a goal has been submitted
if (isset($_POST['goal-delete'])) {
    // Retrieve the Goal ID from the POST request
    $goalID = $_POST['goal-delete'];

    // Get the UserGoalID associated with the GoalID
    $userGoalsQuery = mysqli_query($con, "SELECT * FROM usergoals WHERE GoalID = '$goalID'");

    // Loop through each tracking record to delete associated activities and data
    while ($userGoalRecord = mysqli_fetch_assoc($userGoalsQuery)) {
        // Retrieve UserGoalID for the current record
        $userGoalID = $userGoalRecord['UserGoalID'];

        // Prepare a query to delete all tracked activities associated with this UserGoalID
        $queryDeleteTrackedGoal = "DELETE FROM trackgoals WHERE UserGoalID = '$userGoalID' ";

        // Execute the query to delete tracked activities for the current UserGoalID
        mysqli_query($con, $queryDeleteTrackedGoal);
    }

    // Prepare and execute a query to delete the user goal entry from the usergoals table
    $deleteUserGoalsQuery = "DELETE FROM usergoals WHERE GoalID = '$goalID' ";
    mysqli_query($con, $deleteUserGoalsQuery);

    // Prepare and execute a query to delete the goal from the goals table
    $deleteGoalQuery = "DELETE FROM goals WHERE GoalID = '$goalID' ";
    mysqli_query($con, $deleteGoalQuery);

    // Check if the goal deletion was successful
    if ($deleteGoalQuery) {
        // Set a success message if the deletion was successful
        $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>目標が正常に削除されました。</p></div>"; // "Goal deleted successfully."
    } else {
        // Set a failure message if the deletion failed
        $_SESSION['feedbackMessage'] = "<div class='message-text fail-alert'><p>何か問題が発生しました。</p></div>"; // "Something went wrong."
    }

    // Redirect to the content management page
    header('Location: ../../admin/content_management/manage_content.php');
    exit(0);
}


/*************** Delete Goal Category ***************/
// Check if the form to delete a goal category has been submitted
if (isset($_POST['goalCategory-delete'])) {
    // Retrieve the Goal Category ID from the POST request
    $goalCategoryID = $_POST['goalCategory-delete'];

    // Get the goal associated with the GoalCategoryID
    $checkGoalAssociationQuery = mysqli_query($con, "SELECT * FROM goals WHERE GoalCategoriesID = '$goalCategoryID'");
    
    // Check if any goals are associated with the specified Goal Category ID
    $associatedGoalsCount = mysqli_num_rows($checkGoalAssociationQuery); 

    //If any goals associated with the specified Goal Category ID exist
    if ($associatedGoalsCount <= 0) {
        // Prepare the SQL query to delete the goal category from the database
        $queryDeleteGoalCategory = "DELETE FROM goalcategories WHERE GoalCategoriesID = '$goalCategoryID' ";
        // Execute the query to delete the goal category
        $queryRunGoalCategory = mysqli_query($con, $queryDeleteGoalCategory);

        // Check if the deletion of the goal category was successful
        if ($queryRunGoalCategory) {
            // Set a success message if the deletion was successful
            $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>目標カテゴリーが正常に削除されました。</p></div>"; // "Goal Category deleted successfully."
        } else {
            // Set a failure message if the deletion failed
            $_SESSION['feedbackMessage'] = "<div class='message-text fail-alert'><p>何か問題が発生しました。</p></div>"; // "Something went wrong."
        }
    } else {
        // Set a failure message if there are any goals associated with the GoalCategoryID
        $_SESSION['feedbackMessage'] = "
            <div class='message-text fail-alert'>
                <p>この目標カテゴリーには関連する目標が存在するため、削除できません。<br>関連する目標を先に削除してください。</p>
            </div>"; // "This goal category cannot be deleted because there are associated goals. Please delete the associated goals first to delete the goal category."
    }

    // Redirect to the content management page
    header('Location: ../../admin/content_management/manage_content.php');
    exit(0);
}

/* blocked-words*/
if(isset($_POST['word-delete'])){
    $wordID = $_POST['word-delete'];

    $queryDeleteWord = "DELETE FROM blockedWords WHERE blockedWordID = '$wordID' ";
    // Execute the query to delete the blocked word
    $runDeleteWord = mysqli_query($con, $queryDeleteWord);
    
    if ($runDeleteWord) {
        // Set a success message if the deletion was successful
        $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>禁止用語が正常に削除されました。</p></div>";
    } else {
        // Set a failure message if the deletion failed
        $_SESSION['feedbackMessage'] = "<div class='message-text fail-alert'><p>何か問題が発生しました。</p></div>";

    }
    header('Location: ../../admin/blocked_words/manage_blocked_words.php');
    exit(0);
}

?>




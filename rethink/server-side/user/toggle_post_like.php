<?php 
/*
 * Page Name: toggle_post_like.php
 * Author: Moa Burke
 * Date: 2024-10-30
 * Description: This script handles the functionality for liking and unliking posts. 
 *      It checks if a user is logged in, retrieves the user's ID, and 
 *      determines if the user has already liked a particular post based on the 
 *      post ID passed via a GET request. 
 * 
 *      - If the user has already liked the post, the script removes the like.
 *      - If the user has not liked the post, it adds a new like to the database.
 * 
 *      Finally, it redirects the user to the user home page.
*/
session_start(); // Start the session

// Define a constant for the base path
define('BASE_PATH', '../shared/');

include(BASE_PATH . "connections.php"); // Include databade connection file
include(BASE_PATH . "check_login.php"); // Include login check script to verify user authentication before accessing this page.
include(BASE_PATH . "timezone.php"); // Include the timezone configuration

// Check if the user is logged in and retrieve user data
$userData = check_login($con);
// Get the UserID from the user data
$userID = $userData['UserID']; 

// Check if a 'post ID' has been passed via a GET request, meaning the user clicked to like/unlike a specific post
if(isset($_GET['id'])){
    $postID = $_GET['id']; // Store the post ID for the like/unlike action
    
    // Check if the user has already liked the post
    $queryExists = mysqli_query($con, "select * from PostLikes where UserID = '$userID' AND PostID = '$postID'");
    $getExists = mysqli_num_rows($queryExists); // Count records found

    if ($getExists > 0 ) {
        // If a like record exists, the user has already liked the post; prepare to unlike it
        // Define the query to delete the existing like, removing the record from the PostLikes table
        $queryDelete = "DELETE FROM PostLikes WHERE UserID = '$userID' AND PostID = '$postID'";
        $getExists = mysqli_query($con, $queryDelete); // Execute delete query

    } else {
        // If no like record exists, the user has not liked the post yet; prepare to like it
        // Define the query to insert a new record in the PostLikes table with the user's ID and post 
        $query = "INSERT INTO PostLikes (PostID, UserID) VALUES('$postID', '$userID')";
        $queryRun = mysqli_query($con, $query); // Execute delete query
    }

}

// Redirect the user back to the homepage, ensuring they see the updated like/unlike status of the post
header('Location: ../../user/user_home.php');
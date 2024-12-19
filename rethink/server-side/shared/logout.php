<?php
/*
 * Page Name: logout.php
 * Author: Moa Burke
 * Date: 2024-10-30
 * Description: This script handles the logout process for users and administrators.
 *      It does so by starting the session, unsetting the session variables 
 *      for 'UserID' and 'AdminID' if they are set, effectively logging 
 *      out the user. After clearing the session data, the script redirects 
 *      the user to the 'index.php' page. This ensures that no further code 
 *      is executed after the redirect.
 * 
 */
session_start(); // Start the session

# Check if the 'UserID' session variable is set
if(isset($_SESSION['UserID'])){
    // Unset the 'UserID' variable
    unset($_SESSION['UserID']);
}

// Check if the 'AdminID' session variable is set
if(isset($_SESSION['AdminID'])){
    // Unset the 'AdminID' variable
    unset($_SESSION['AdminID']);
}

// Redirect the user to 'index.php' after logging out
header("Location: ../../index.php"); // Terminate the script to ensure no further code is executed
die;
?>
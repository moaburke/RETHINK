<?php
/**
 * Page Name: check_login.php
 * Author: Moa Burke
 * Date: 2024-10-30
 * Description: This function is responsible for verifying whether a user is logged in by checking if a valid session exist
 * 		(specially for checking 'UserID' for the session). If the session exists, the function queries the database
 * 		to retrieve the user's information. If the user is authenticated (i.e., their UserID is found in the database),
 * 		it returns the user's data. Otherwise, it redirects to the index page.
 * 
 * 		Flow":
 * 		1. Check if 'UserID' is set in the session.
 * 		2. If 'UserID' exists, retrieve it and run a query on the 'users' table to find a match.
 * 		3. If a matching user is found, return their data (as an associative array).
 * 		4. If no matching user is found or it the session is not set, rederict the user to the index page.
 * 		5. The function terminated the script with `die` after redirecting to ensure no further code is executed.
 * 
 */ 
// --------------- Adjust according to your server setup ---------------
define('BASE_URL', '/rethink/'); 


function check_login($con) {
	// Check if the session vasiable 'UserId' is set
	if(isset($_SESSION['UserID']))
	{
		// Retrieve the 'UserID' from the session
		$id = $_SESSION['UserID'];
		// Query to select the user data from the 'users' table where UserID matches the session 'UserID'
		$query = "SELECT * FROM users WHERE UserID = '$id' limit 1";

		// Execute the query
		$result = mysqli_query($con,$query);

		// Check if the query returned a result and if there's at leat one row
		if ($result && mysqli_num_rows($result) > 0) {
			// Fetch the user data as an associative array 
			$user_data = mysqli_fetch_assoc($result);
			// Return the user data to be used elsewhere
			return $user_data;
		}
	}

	// If the session is not set or the query fails, redirect the user to application's top page 'index.php'
	header("Location:". BASE_URL . "index.php");
	// Stop the script execution after the redirection
	die;
}
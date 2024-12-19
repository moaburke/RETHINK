<?php
/**
 * Page Name: admin_user_add.php
 * Author: Moa Burke
 * Date: 2024-10-30
 * Description: This function is responsible for verifying whether an admin is logged in by checking if a valid session exist
 * 		(specially for checking 'UserID' for the session). If the session exists, the function queries the database
 * 		to retrieve the admin's information. If the admin is authenticated (i.e., their UserID is found in the database),
 * 		it returns the admin's data. Otherwise, it redirects to the index page.
 * 
 * 		Flow":
 * 		1. Check if 'UserID' is set in the session.
 * 		2. If 'UserID' exists, retrieve it and run a query on the 'users' table to find a match.
 * 		3. If a matching admin is found, return their data (as an associative array).
 * 		4. If no matching admin is found or it the session is not set, rederict the index page.
 * 		5. The function terminated the script with `die` after redirecting to ensure no further code is executed.
 * 
 */ 
// --------------- Adjust according to your server setup ---------------
define('BASE_URL', '/rethink/'); 

function check_login($con) {
	// Check if the session vasiable 'UserId' is set
	if(isset($_SESSION['UserID'])){

		// Retrieve the 'UserID' from the session
		$id = $_SESSION['UserID'];
		// Query to select the user data from the 'users' table where UserID matches the session 'UserID' and the role is 1 (admin)
		$query = "select * from users where UserID = '$id' and Role = 1 limit 1";
		// Execute the query
		$resultAdmin = mysqli_query($con,$query);

		// Check if the query returned a result and if there's at leat one row
		if($resultAdmin && mysqli_num_rows($resultAdmin) > 0) {
			// Fetch the user data as an associative array 
			$admin_data = mysqli_fetch_assoc($resultAdmin);
			// Return the admin data to be used elsewhere
			return $admin_data;
		}
	}

	// If the session is not set or the query fails, redirect the user to application's top page 'index.php'
	header("Location:". BASE_URL . "index.php");
	// Stop the script execution after the redirection
	die;
}
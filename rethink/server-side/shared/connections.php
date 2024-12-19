<?php
/*
 * Page Name: connection.php
 * Author: Moa Burke
 * Date: 2024-10-30
 * Description: This script establishes a connection to the MySQL database and checks 
 *      if the connection was successful. 
 * 
 *  - Database: 'rethink' on host 'localhost' with user 'guest' and no password.
 *  - If the connection fails, the script terminates and displays an error message.
 * 
 */
// Establish a connection to the MySQL dtabase with host 'localhost', user 'guest, no password, and database 'rethink'
$con = mysqli_connect('localhost', 'guest', '', 'rethink');

// Check if the connection was successful
if ($con === false){
    // If the connection failes, terminate the script and display an error message
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
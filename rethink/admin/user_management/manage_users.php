<?php
/**
 * File: admin_user_management.php
 * Author: Moa Burke
 * Date: 2024-12-17
 * Description: This script manages the admin dashboard for user management, allowing 
 *      the administrator to view, edit, and delete user and admin accounts. It retrieves 
 *      information from the database, displays it in a structured format, and provides 
 *      functionalities for adding new users and administrators. Additionally, it includes feedback 
 *      messages for user interactions and error handling.
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

// Query to retrieve all regular users from the database
$queryRegularUsers =  mysqli_query($con, "SELECT * FROM users WHERE Role = '" . ROLE_USER ."'");
// Get the count of regular users
$countRegularUsers = mysqli_num_rows($queryRegularUsers);

// Query to retrieve all administrators from the database
$queryAdmins =  mysqli_query($con, "SELECT * FROM users WHERE Role = '" . ROLE_ADMIN ."'");
// Get the count of administrators
$countAdmins = mysqli_num_rows($queryAdmins);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Include the necessary assets for the head section via the includeHeadAssets function -->
        <?php includeHeadAssets(); ?>
        <script src="../../assets/javascript/tab_interactions.js" defer></script>
    </head>

    <body>
        <header class="sidebar-navigation manage-users-navigation">
            <!-- Render the sidebar navigation specifically for the admin section -->
            <?php renderAdminNavigation(); ?>
            
        </header>

        <!-- Render the header for the admin dashboard with logout functionality, using the admin's data -->
        <?php renderAdminHeaderWithLogout($adminData); ?>

        <!-- Main wrapper for the admin user management section -->
        <div class="admin-main-wrapper">
            <!-- Header for the User/Admin section -->
            <h2>User/Admin</h2>

            <!-- Include feedback messages for user interactions -->
            <div>
                <?php include('../../server-side/shared/feedback_messages.php'); ?>
            </div>

            <!-- Navigation for editing different content categories -->
            <div class="user-management-navigation ">
                <ul>
                    <!-- Tab for managing 'Moods' content, initially active -->
                    <li data-tab-target="#admin-tab" class="active tabs">
                        <div class="navigation-content" id="active-navigation">
                            <p>
                                <!-- Japanese label for 'Moods' -->
                                <span class="japanese-label">ユーザー</span>
                                <!-- English label for 'Moods' -->
                                <span class="english-label">Admin</span>
                            </p>
                        </div> 
                    </li>

                    <!-- Tab for managing 'Feelings' content -->
                    <li data-tab-target="#users-tab" class = "tabs">
                        <div class="navigation-content">
                            <p>
                                <!-- Japanese label for 'Feelings' -->
                                <span class="japanese-label">ユーザー</span>
                                <!-- English label for 'Feelings' -->
                                <span class="english-label">Users</span>
                            </p>
                        </div> 
                    </li>

            </div><!-- End of .user-management-navigation -->

            <div class="user-management-content-tabs">

                <!-- Table for the user information -->
                <div class="edit-users user-management-table" id="users-tab" data-tab-content>
                
                    <!-- Section for editing user information with title and action button -->
                    <div class="user-management-header">
                        <!-- Title displaying both Japanese and English for clarity -->
                        <h3>
                            <span class="japanese-title">ユーザー</span> <!-- Title in Japanese: "Users" -->
                            <span class="english-title">Users</span> <!-- Title in English -->
                        </h3>

                        <!-- Link to add a new user -->
                        <a href="./add_user_admin/add_user.php" class="primary-btn">
                            <i class="fa-solid fa-plus"></i> <!-- Icon for adding a new user -->
                            Add New <!-- Text for the button -->
                        </a>
                    </div><!-- .user-management-header -->

                    <div class="total-users">
                        <p>Total: <?php if ($countRegularUsers != null) { echo $countRegularUsers;} else { echo "0";} ?></p>
                    </div>

                    <!-- Table displaying user information -->
                    <table>
                        <!-- Table headers for user data -->
                        <tr>
                            <th>No</th>  <!-- User index -->
                            <th>Username</th>
                            <th>First name</th>
                            <th>Last name</th>
                            <th>Email</th>
                            <th>Account Created</th> <!-- Date when the account was created -->
                            <th>Role</th> <!-- User's role (Admin/Regular) -->
                            <th>Last Login</th> <!-- Timestamp of the user's last login -->
                            <th>Login Count</th> <!-- Total number of times the user has logged in -->
                            <th>Action</th> <!-- Actions that can be performed on the user -->
                        </tr>

                        <?php 
                        /// Initialize the index for user listing
                        $userIndex = 1;    

                        // Check if there are regular users to display
                        if ($countRegularUsers > 0) {
                            // Loop through each user record
                            while ($userData = mysqli_fetch_assoc($queryRegularUsers)) { 
                                $userID = $userData['UserID']; // Get the UserID for the current user

                                // Query to get additional user data
                                $queryGetUserData = mysqli_query($con, "SELECT * FROM userdata WHERE UserID = $userID");
                                $additionalUserData = mysqli_fetch_assoc($queryGetUserData); // Fetch the user data

                                $userRoleID = $userData['Role']; // Get the user's role

                                // Determine the role description based on the role ID
                                $userRoleDescription = ($userRoleID == ROLE_USER) ? "User" : "Admin";
                                ?>
                                
                                <tr> 
                                    <!-- Display the user index -->
                                    <td>
                                        <?php echo $userIndex; ?>
                                    </td>

                                    <!-- Display the username -->
                                    <td>
                                        <?php echo  htmlspecialchars($userData['Username']); ?>
                                    </td>

                                    <!-- Display the first name -->
                                    <td>
                                        <?php echo  htmlspecialchars($userData['FirstName']); ?>
                                    </td>

                                    <!-- Display the last name -->
                                    <td>
                                        <?php echo  htmlspecialchars($userData['LastName']); ?>
                                    </td>

                                    <!-- Display the email -->
                                    <td>
                                        <?php echo  htmlspecialchars($userData['Email']); ?>
                                    </td>

                                    <!-- Display the account creation date -->
                                    <td>
                                        <?php echo $userData['Created']; ?>
                                    </td>

                                    <!-- Display the user's role -->
                                    <td>
                                        <?php echo $userRoleDescription ?>
                                    </td>

                                    <!-- Display the Last Login -->
                                    <td>
                                        <?php 
                                            // Check if user data exists and display the last login or a placeholder
                                            echo !empty($additionalUserData) ? $additionalUserData['LastLogin'] : "---"; // Display last login time or "---"
                                        ?>
                                    </td>

                                    <!-- Display the Login Count -->
                                    <td>
                                        <?php 
                                            // Check if user data exists and display the login count or "0" if not
                                            echo !empty($additionalUserData) ? $additionalUserData['LoginCount'] : "0"; // Display login count or "0"
                                        ?>
                                    </td> 

                                    <!-- Action buttons for editing and deleting users -->
                                    <td>
                                        <div class="action-buttons-wrapper">
                                            <div class="edit-button">
                                                <!-- Link to edit user details with the user's ID in the query string -->
                                                <a href="./edit_user_admin/edit_user_details.php?id=<?php echo $userData['UserID']; ?>">
                                                    <i class="fa-solid fa-pen-to-square"></i> <!-- Edit icon -->
                                                </a>
                                            </div><!-- .edit-button-wrapper -->

                                            <div class="delete-button">
                                                <!-- Link to delete user, with a confirmation prompt -->
                                                <a href="deletelink" onclick="return checkDelete()">
                                                    <form action="../../server-side/admin/admin_data_cleanup.php" method="POST">
                                                        <button type="submit" name="user-delete" value="<?=$userData['UserID'];?>" >
                                                            <i class="fa-solid fa-trash"></i> <!-- Delete icon -->
                                                        </button>
                                                    </form>
                                                </a>
                                            </div><!-- .delete-button -->

                                            <div class="view-user-details-button">
                                                <!-- Link to view detailed information about the user -->
                                                <a href="./user_details/user_details.php?id=<?php echo $userData['UserID'];?>">
                                                    <i class="fa-solid fa-angle-right"></i> <!-- View details icon -->
                                                </a>
                                            </div>
                                        </div><!-- .action-buttons-wrapper -->
                                    </td> 
                                </tr>

                                <?php 
                                // Increment the user counter for the next user
                                $userIndex++;

                            } // End of while loop through each user record

                        } // End of if statement checking for regular users
                        ?>
                    </table>
                </div><!-- .edit-users -->

                <!-- Container for the admin management table -->
                <div class="edit-admin user-management-table active" id="admin-tab" data-tab-content>
            
                    <!-- Header section for the admin management area -->
                    <div class="user-management-header">
                        <h3>
                            <span class="japanese-title">管理者</span> <!-- Title in Japanese: "Administrator" -->
                            <span class="english-title">Admins</span> <!-- Title in English -->
                        </h3>

                        <!-- Link to add a new admin -->
                        <a href="./add_user_admin/add_admin.php" class="primary-btn">
                            <i class="fa-solid fa-plus"></i> <!-- Icon for adding a new admin -->
                            Add New <!-- Text for the button -->
                        </a> 
                    </div><!-- .user-management-header -->

                    <div class="total-users">
                        <p>Total: <?php if ($countAdmins != null) { echo $countAdmins;} else { echo "0";}?></p>
                    </div>

                    <!-- Table displaying admin information -->
                    <table>
                        <!-- Table headers for admin data -->
                        <tr>
                            <th>No</th> <!-- Admin index -->
                            <th>Username</th>
                            <th>First name</th>
                            <th>Last name</th>
                            <th>Email</th>
                            <th>Account Created</th> <!-- Date when the account was created -->
                            <th>Role</th> <!-- User's role (Admin/Regular) -->
                            <th>Action</th> <!-- Actions that can be performed on the admin -->
                        </tr>
                    
                        <?php 
                        // Initialize the index for admin user listing
                        $userIndex = 1;

                        // Check if there are admin users to display
                        if ($countAdmins > 0) {
                            // Loop through each user record
                            while ($userData = mysqli_fetch_assoc($queryAdmins)) { 
                                $userID = $userData['UserID']; // Get the UserID for the current user

                                // Query to get additional user data
                                $queryAdminsData = mysqli_query($con, "SELECT * FROM userdata where UserID = $userID");
                                $additionalUserData = mysqli_fetch_assoc($queryAdminsData); // Fetch the user data

                                // Determine the role description based on the role ID
                                $userRoleDescription = ($userRoleID == ROLE_ADMIN) ? "User" : "Admin";
                                ?>

                                <tr> 
                                    <!-- Display the user index -->
                                    <td>
                                        <?php echo $userIndex; ?>
                                    </td>

                                    <!-- Display the username -->
                                    <td>
                                        <?php echo htmlspecialchars($userData['Username']); ?>
                                    </td>

                                    <!-- Display the first name -->
                                    <td>
                                        <?php echo htmlspecialchars($userData['FirstName']); ?>
                                    </td>

                                    <!-- Display the last name -->
                                    <td>
                                        <?php echo htmlspecialchars($userData['LastName']); ?>
                                    </td>

                                    <!-- Display the email -->
                                    <td>
                                        <?php echo htmlspecialchars($userData['Email']); ?>
                                    </td>

                                    <!-- Display the account creation date -->
                                    <td>
                                        <?php echo $userData['Created']; ?>
                                    </td>

                                    <!-- Display the admin's role -->
                                    <td>
                                        <?php echo $userRoleDescription ?>
                                    </td>

                                    <!-- Action buttons for editing and deleting users -->
                                    <td>
                                        <div class="action-buttons-wrapper">
                                            <div class="edit-button">
                                                <!-- Link to edit user details with the user's ID in the query string -->
                                                <a href="./edit_user_admin/edit_admin_details.php?id=<?php echo $userData['UserID']; ?>">
                                                    <i class="fa-solid fa-pen-to-square"></i> <!-- Edit icon -->
                                                </a>
                                            </div><!-- .edit-button -->

                                            <div class="delete-button 
                                                <?php 
                                                    // Check if the username is "MainAdmin"
                                                    if (($userData['Username']) == "MainAdmin") {
                                                        // Disable the functionality for MainAdmin
                                                        echo "disabled";
                                                    }?>
                                                ">
                                                <!-- Link to delete user, with a confirmation prompt -->
                                                <a href="deletelink" onclick="return checkDelete()">
                                                    <form action="../../server-side/admin/admin_data_cleanup.php" method="POST">
                                                        <button type="submit" name="user-delete" value="<?=$userData['UserID'];?>" 
                                                            <?php 
                                                            // Check if the username is "MainAdmin"
                                                            if (($userData['Username']) == "MainAdmin") {
                                                                // Disable the functionality for MainAdmin
                                                                echo "disabled";
                                                            }?>>
                                                            <i class="fa-solid fa-trash"></i> <!-- Delete icon -->
                                                        </button>
                                                    </form> 
                                                </a>

                                            </div><!-- .btn-detele -->
                                            
                                        </div><!-- .action-buttons-wrapper -->
                                    </td>
                                </tr>

                                <?php 
                                // Increment the admin counter for the next user
                                $userIndex++;

                            } // End of while loop through each user record

                        } // End of if statement checking for regular users 
                        ?>
                    </table>
                </div><!-- .edit-admin -->
                    
            </div><!-- ./user-management-content-tabs -->
            
        </div><!-- .admin-main-wrapper -->
    </body>
</html>
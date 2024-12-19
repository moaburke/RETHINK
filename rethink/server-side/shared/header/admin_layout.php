<?php
/**
 * Page Name: admin_layout.php
 * Author: Moa Burke
 * Date: 2024-12-17
 * Description: This PHP file provides essential functions and constants for rendering consistent and functional
 *      admin-side HTML components. It includes:
 *
 *      - A base URL constant (`BASE_URL`) for uniform link generation throughout the admin interface.
 *      - `hsc($hsc)`: A utility function to safely escape HTML special characters for security.
 *      - `includeHeadAssets()`: Outputs the <head> section, including meta tags, stylesheets, and scripts specific to admin pages.
 *      - `renderAdminNavigation()`: Generates the navigation bar for admin pages, including links to sections like Dashboard, 
 *                                   User Management, Content Management, Feed Analytics, and Blocked Words.
 *      - `renderAdminHeaderWithLogout($admin_data)`: Displays a top header with admin profile information and a sticky logout button.
 *
 * Purpose: These functions ensure consistency, maintainability, and reusability of common HTML components 
 *          across admin pages while adhering to best practices for security and design.
 */

// Check if the constant is already defined before defining it
if (!defined('BASE_URL')) {
    define('BASE_URL', '/rethink/'); // Adjust according to your server setup
}

// Utility function to shorten the usage of htmlspecialchars
// Special characters in a string are converted to HTML entitites
function hsc($hsc)
{
    // Convert special characters to HTML entities (like <, >, &, etc.) and handle both single and double quotes
	return htmlspecialchars($hsc, ENT_QUOTES);
}

// Function to include the <head> section for admin pages, similar to the mutual_head function, but wiht additional scripts for admin-specific funtionality
function includeHeadAssets(){
    echo '
        <!-- Set the character encoding for the document -->
        <meta charset="utf-8">

        <!-- Set the title of the page -->
        <title>RE:THINK</title>
        
        <!-- Include FontAwesome icons for access to various icons -->
        <script src="https://kit.fontawesome.com/4f1988a159.js" crossorigin="anonymous"></script>
        
        <!-- Preconnect to Google Fonts server to optimize font loading -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <!-- Load the Gudea font from Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Gudea&display=swap" rel="stylesheet">
        
        <!-- Link to the custom CSS file for site styling -->
        <link rel="stylesheet" href="' . BASE_URL . './assets/css/shared.css">
        <link rel="stylesheet" href="' . BASE_URL . './assets/css/admin.css">
        

        <!-- Include JavaScript files for input handling and messages -->
        <script src="'. BASE_URL . 'assets/javascript/input_handler.js" defer></script>
        <script src="'. BASE_URL . 'assets/javascript/alert_handler.js" defer></script>
        <script src="'. BASE_URL . 'assets/javascript/icon_selection_modal.js" defer></script>

        <!-- JavaScript function to confirm deletion (Asks the user before performing a deletion action) -->
        <script language="JavaScript" type="text/javascript">
            function checkDelete(){
                return confirm("本当に削除しますか？"); // "Are you sure you want to delete"? in Japanese
            }
        </script>
    ';
}


// Function to output the navigation heder for admin pages
function renderAdminNavigation(){
    echo "
        <nav>
            <!-- Link to the admin homepage (admin_dashboard.php) -->
            <a href='". BASE_URL . "admin/admin_dashboard.php'>
                <!-- Decorative heart shape created using two parts (left and right) -->
                <div class='heart-header'>
                    <!-- Decorative heart shape -->
                    <div class='heart'>
                        <div class='heart-left'></div> <!-- Left part of the heart -->
                        <div class='heart-right'></div> <!-- Right part of the heart -->
                    </div>
                    <!-- Website name -->
                    <div class='name-header'>RE:<span>THINK</span></div>  
                </div>
            </a>

            <!-- Text indicating the admin section of the site -->
            <div class='header-admin-text'>
                <p>ADMIN</p>
            </div>

            <!-- List of navigation links for admin functionality -->
            <ul>
                <!-- Dashboard link -->
                <a href='". BASE_URL . "admin/admin_dashboard.php' class='dashboard-active'>
                    <li class='hover-underline-animation'>Dashboard</li>
                </a>

                <!-- Manage Users link -->
                <a href='". BASE_URL . "admin/user_management/manage_users.php' class='manage-users-active'>
                    <li class='hover-underline-animation'>Manage Users</li>
                </a>

                <!-- Manage Content link -->
                <a href='". BASE_URL . "admin/content_management/manage_content.php' class='manage-contents-active'>
                    <li class='hover-underline-animation'>Manage Content</li>
                </a>

                <!-- Feed Analytics link -->
                <a href='". BASE_URL . "admin/user_feed/user_feed_analytics.php' class='user-feed-active'>
                    <li class='hover-underline-animation'>Feed Analytics</li>
                </a>

                <!-- Blocked Words Management link -->
                <a href='". BASE_URL . "admin/blocked_words/manage_blocked_words.php' class='blocked-words-active'>
                    <li class='hover-underline-animation'>Blocked Words</li>
                </a>
            </ul>
        </nav>
    ";
}

//Function to display the header and sticky logout button for admin users, including their profile images and name
function renderAdminHeaderWithLogout($admin_data){
    echo "
    <div class='top-header'>
        <!-- Link to the admin's profile page (my_page.php) -->
        <a href='" . BASE_URL . "admin/admin_profile/admin_profile.php'>
            <div class='header-display'>
                <!-- Container for the profile image -->
                <div class='header-icon'>
                    <!-- Display the admins's profile image -->
                    <img src='" . BASE_URL . "assets/user-img/" .hsc($admin_data['profileImg'])."' alt='Admin Profile Image'>
                </div><!-- .header-icon -->

                <!-- Wrapper for the user's name and title -->
                <div class='header-name-wrapper'>
                    <div class='header-name'>
                        <!-- Display the admin's first and last name -->
                        <p class='header-fname'>" .hsc($admin_data['FirstName'])."</p>
                        <p class='header-lname'>" .hsc($admin_data['LastName'])."</p>
                    </div>
                    <div class='header-title'>
                        <!-- Static Admin displayed for all admin users -->
                        <p>Admin</p>
                    </div>
                </div><!-- .header-name-wrapper -->
            </div><!-- .header-display -->
        </a>
        
        <!-- Sticky logout button -->
        <a href='". BASE_URL . "server-side/shared/logout.php'>
            <div class='logout-sticky'>Log Out</div>
        </a>
    </div><!-- .top-header -->
    ";
}
?>
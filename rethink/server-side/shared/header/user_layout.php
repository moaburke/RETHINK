<?php
/**
 * Page Name: user_layout.php
 * Author: Moa Burke
 * Date: 2024-11-03
 * Description: This PHP file defines utility functions for common HTML components on the user interface, such as navigation, 
 *      header, and asset inclusion. It includes:
 *
 *      - A base URL constant (`BASE_URL`) for consistent URL usage across the site.
 *      - `hsc()`: A utility function to safely escape HTML special characters.
 *      - `includeHeadAssets()`: Outputs the <head> section with meta tags, stylesheets, and scripts used throughout the user pages.
 *      - `renderUserNavigation()`: Generates the navigation menu for user pages, with links to main sections like Home, Register Mood, Insights, and Your Goals.
 *      - `renderUserHeaderWithLogout($user_data)`: Renders the header section with user profile information and a sticky logout button.
 * 
 *      Each function outputs necessary HTML for consistent design and functionality across the site, following best practices for security and maintainability.
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

// Function to include the <head> section for user pages, including common assets like stylesheets and scripts
function includeHeadAssets() {
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
        <link rel="stylesheet" href="' . BASE_URL . './assets/css/user.css">
  
        ';
}


// Function to output the navigation header for admin pages
function renderUserNavigation() {
    echo "
        <nav>
            <!-- Link to the homepage (user_home.php) -->
            <a href='" . BASE_URL . "user/user_home.php'>
                <!-- Container for the logo section, which includes a decorative heart and the website title -->
                <div class='heart-header'>
                    <!-- Decorative heart shape created using two parts (left and right) -->
                    <div class='heart'>
                        <div class='heart-left'></div> <!-- Left part of the heart -->
                        <div class='heart-right'></div> <!-- Right part of the heart -->
                    </div>
                    <!-- Website name -->
                    <div class='name-header'>RE:<span>THINK</span></div>  
                </div><!-- ./heart-header -->
            </a>

            <!-- User navigation menu with links to main sections -->
            <ul class='navigation-user'>
                <!-- Link to the Home page -->
                <a href='" . BASE_URL . "user/user_home.php' class='index-active'>
                    <li class='hover-underline-animation'>Home</li>
                </a>

                <!-- Link to the Register Mood page -->
                <a href='" . BASE_URL . "user/mood/register_mood.php' class='mood-register-active'>
                    <li class='hover-underline-animation'>Register Mood</li>
                </a>

                <!-- Link to the Insights page -->
                <a href='" . BASE_URL . "user/tracking_insights/insights.php' class='insights-active'>
                    <li class='hover-underline-animation'>Insights</li>
                </a>

                <!-- Link to the Your Goals page -->
                <a href='" . BASE_URL . "user/goals/display_user_goals.php' class='goals-active'>
                    <li class='hover-underline-animation'>Your Goals</li>
                </a>
            </ul>
        </nav>
    ";
}


// Function to display the header and sticky logout button for users, including their profile images and name
function renderUserHeaderWithLogout($user_data){
    echo "
        <div class='top-header'>

            <!-- Link to the user's profile page (my_page.php) -->
            <a href='" . BASE_URL . "user/user_profile/my_page.php'>
                <div class='header-display'>
                    <!-- Container for the profile image -->
                    <div class='header-icon'>
                        <!-- Display the user's profile image -->
                        <img src='" . BASE_URL . "assets/user-img/" .hsc($user_data['profileImg'])."' alt='User Profile Image'>
                    </div><!-- .header-icon -->

                    <!-- Wrapper for the user's name and title -->
                    <div class='header-name-wrapper'>
                        <div class='header-name'>
                            <!-- Display the user's first and last name -->
                            <p class='first-name'>" .hsc($user_data['FirstName'])."</p>
                            <p class='last-name'>" .hsc($user_data['LastName'])."</p>
                        </div>

                        <div class='header-title'>
                            <!-- Display the user's username -->
                            <p>@" .hsc($user_data['Username'])."</p>
                        </div>
                    </div><!-- .header-name-wrapper -->
                </div><!-- .header-display -->
            </a>
            
            <!-- Sticky logout button that logs the user out when clicked -->
            <a href='". BASE_URL . "server-side/shared/logout.php'>
                <div class='logout-sticky'>Log Out</div>
            </a>
        </div><!-- .top-header -->
    ";
}

?>